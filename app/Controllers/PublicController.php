<?php

namespace App\Controllers;

use App\Models\DocumentModel;
use App\Models\DocumentRouteModel;
use App\Models\DocumentLogModel;
use App\Models\TravelRequestModel;
use App\Models\TravelRouteModel;
use App\Models\TravelLogModel;

class PublicController extends BaseController
{
    protected $documentModel;
    protected $routeModel;
    protected $logModel;
    protected $travelModel;
    protected $travelRouteModel;
    protected $travelLogModel;

    public function __construct()
    {
        $this->documentModel    = new DocumentModel();
        $this->routeModel       = new DocumentRouteModel();
        $this->logModel         = new DocumentLogModel();
        $this->travelModel      = new TravelRequestModel();
        $this->travelRouteModel = new TravelRouteModel();
        $this->travelLogModel   = new TravelLogModel();
        helper(['thai_date', 'form', 'url']);
    }

    /**
     * หน้าแรกสาธารณะ: ติดตามเอกสารและหนังสือราชการ (Read-Only สำหรับทุกคน)
     */
    public function index()
    {
        $search = trim($this->request->getGet('search') ?? '');
        $type   = $this->request->getGet('type');
        $status = $this->request->getGet('status');

        // สถิติภาพรวมสำหรับแสดง Dashboard ย่อย (ไม่นับสถานะร่างเอกสาร)
        $totalDocsCount     = (new DocumentModel())->where('status !=', 'draft')->countAllResults();
        $totalTravelCount   = (new TravelRequestModel())->countAllResults();
        $totalCount         = $totalDocsCount + $totalTravelCount;

        $routingDocsCount   = (new DocumentModel())->where('status', 'routing')->countAllResults();
        $routingTravelCount = (new TravelRequestModel())->where('status', 'pending')->countAllResults();
        $routingCount       = $routingDocsCount + $routingTravelCount;

        $completedDocsCount   = (new DocumentModel())->where('status', 'completed')->countAllResults();
        $completedTravelCount = (new TravelRequestModel())->where('status', 'approved')->countAllResults();
        $completedCount       = $completedDocsCount + $completedTravelCount;

        $returnedDocsCount   = (new DocumentModel())->where('status', 'returned')->countAllResults();
        $returnedTravelCount = (new TravelRequestModel())->where('status', 'returned')->countAllResults();
        $returnedCount       = $returnedDocsCount + $returnedTravelCount;

        // 1. ดึงข้อมูลคำขอไปราชการ (ถ้าเป็นเมนู "ทั้งหมด" หรือเลือกประเภท "ไปราชการ")
        $includeTravel = empty($type) || $type === 'travel';
        $travelDocs = [];
        if ($includeTravel) {
            $travelQuery = (new TravelRequestModel())->select('travel_requests.*, users.prefix, users.first_name, users.last_name, departments.name as department_name')
                                                     ->join('users', 'users.id = travel_requests.user_id', 'left')
                                                     ->join('departments', 'departments.id = users.department_id', 'left');

            if (!empty($search)) {
                $travelQuery->groupStart()
                            ->like('travel_requests.doc_no', $search)
                            ->orLike('travel_requests.title', $search)
                            ->orLike('travel_requests.destination', $search)
                            ->orLike('travel_requests.province', $search)
                            ->orLike('users.first_name', $search)
                            ->orLike('users.last_name', $search)
                            ->groupEnd();
            }

            if (!empty($status)) {
                $statusMap = ['routing' => 'pending', 'completed' => 'approved', 'returned' => 'returned'];
                $mappedStatus = $statusMap[$status] ?? $status;
                $travelQuery->where('travel_requests.status', $mappedStatus);
            }

            $travels = $travelQuery->orderBy('travel_requests.created_at', 'DESC')->findAll(100);

            $stepNames = [
                1 => '1. โรงเรียน (ตรวจเอกสาร)',
                2 => '2. กองการศึกษา (ตรวจสอบ)',
                3 => '3. กองการเจ้าหน้าที่ (ตรวจสอบคุณสมบัติ)',
                4 => '4. สำนักปลัด (กลั่นกรอง)',
                5 => '5. นายก อบจ. (รอลงนามอนุมัติ)'
            ];

            foreach ($travels as $tr) {
                $curStep = (int)$tr['current_step'];
                $travelDocs[] = [
                    'id'              => $tr['id'],
                    'doc_type'        => 'travel',
                    'doc_no'          => $tr['doc_no'],
                    'internal_reg_no' => 'ไปราชการ',
                    'title'           => $tr['title'] . ' (ณ ' . $tr['destination'] . ' ' . $tr['province'] . ')',
                    'from_org'        => ($tr['prefix'] ?? '') . $tr['first_name'] . ' ' . $tr['last_name'] . ' (' . ($tr['department_name'] ?? 'รร.') . ')',
                    'to_org'          => $this->getTravelDestination($tr),
                    'doc_date'        => $tr['start_date'],
                    'urgency'         => 'normal',
                    'status'          => ($tr['status'] === 'approved') ? 'completed' : (($tr['status'] === 'returned') ? 'returned' : 'routing'),
                    'current_step'    => $curStep,
                    'total_steps'     => 5,
                    'is_travel'       => true,
                    'created_at'      => $tr['created_at'],
                    'current_handler' => [
                        'position_name' => $stepNames[$curStep] ?? 'กำลังพิจารณา',
                        'prefix'        => '',
                        'first_name'    => 'หน่วยงานขั้นตอนที่ ' . $curStep,
                        'last_name'     => ''
                    ]
                ];
            }
        }

        // 2. ดึงข้อมูลหนังสือราชการทั่วไป (ถ้าเป็นเมนู "ทั้งหมด" หรือเลือกประเภทเอกสารที่ไม่ใช่ "ไปราชการ")
        $includeRegularDocs = empty($type) || $type !== 'travel';
        $regularDocs = [];
        if ($includeRegularDocs) {
            $docQuery = (new DocumentModel())->select('documents.*, users.prefix, users.first_name, users.last_name, departments.name as department_name')
                                             ->join('users', 'users.id = documents.created_by', 'left')
                                             ->join('departments', 'departments.id = users.department_id', 'left')
                                             ->where('documents.status !=', 'draft');

            // กรองคำค้นหา
            if (!empty($search)) {
                $docQuery->groupStart()
                         ->like('documents.doc_no', $search)
                         ->orLike('documents.internal_reg_no', $search)
                         ->orLike('documents.title', $search)
                         ->orLike('documents.from_org', $search)
                         ->orLike('documents.to_org', $search)
                         ->groupEnd();
            }

            // กรองประเภทเอกสาร (กรณีระบุประเภทเจาะจงที่ไม่ใช่ travel)
            if (!empty($type)) {
                $docQuery->where('documents.doc_type', $type);
            }

            // กรองสถานะเอกสาร
            if (!empty($status)) {
                if ($status === 'draft') {
                    $docQuery->where('1 = 0');
                } else {
                    $docQuery->where('documents.status', $status);
                }
            }

            $docs = $docQuery->orderBy('documents.created_at', 'DESC')->findAll(100);

            // ดึงข้อมูล current handler สำหรับแต่ละเอกสาร
            foreach ($docs as $doc) {
                $doc['is_travel'] = false;
                // หาจำนวนขั้นตอนทั้งหมด
                $routes = $this->routeModel->where('document_id', $doc['id'])->orderBy('step_order', 'ASC')->findAll();
                $doc['total_steps'] = count($routes);

                // หาผู้รับผิดชอบขั้นตอนปัจจุบัน
                $currentRoute = $this->routeModel->select('document_routes.*, users.prefix, users.first_name, users.last_name, positions.name as position_name')
                                                 ->join('users', 'users.id = document_routes.user_id', 'left')
                                                 ->join('positions', 'positions.id = users.position_id', 'left')
                                                 ->where('document_routes.document_id', $doc['id'])
                                                 ->where('document_routes.step_order', $doc['current_step'])
                                                 ->first();

                $doc['current_handler'] = $currentRoute ?: null;
                $regularDocs[] = $doc;
            }
        }

        // 3. รวมและจัดเรียงตามวันที่สร้างล่าสุด (DESC)
        $documents = array_merge($regularDocs, $travelDocs);
        if (empty($type)) {
            usort($documents, function ($a, $b) {
                return strcmp($b['created_at'] ?? '', $a['created_at'] ?? '');
            });
        }

        return view('public/index', [
            'documents'      => $documents,
            'search'         => $search,
            'currentType'    => $type,
            'currentStatus'  => $status,
            'totalCount'     => $totalCount,
            'routingCount'   => $routingCount,
            'completedCount' => $completedCount,
            'returnedCount'  => $returnedCount,
            'isLoggedIn'     => session()->get('logged_in') ?? false,
            'userFullName'   => session()->get('full_name') ?? '',
            'userRole'       => session()->get('position_name') ?? '',
        ]);
    }

    /**
     * หน้ารายละเอียดการติดตามขั้นตอนของเอกสาร (Read-Only)
     */
    public function track($id)
    {
        $document = $this->documentModel->getDocumentDetails($id);

        if (!$document || $document['status'] === 'draft') {
            // ลองค้นหาในคำขอไปราชการ
            $travel = $this->travelModel->select('travel_requests.*, users.prefix, users.first_name, users.last_name, departments.name as department_name')
                                        ->join('users', 'users.id = travel_requests.user_id', 'left')
                                        ->join('departments', 'departments.id = users.department_id', 'left')
                                        ->where('travel_requests.id', $id)
                                        ->first();
            if ($travel) {
                return redirect()->to('/travel/' . $id);
            }
            return redirect()->to('/')->with('error', 'ไม่พบเอกสารที่ระบุในระบบ');
        }

        $routes = $this->routeModel->getRoutesForDocument($id);
        $logs   = $this->logModel->getLogsForDocument($id);

        return view('public/track', [
            'document'   => $document,
            'routes'     => $routes,
            'logs'       => $logs,
            'isLoggedIn' => session()->get('logged_in') ?? false,
        ]);
    }

    /**
     * Ajax API สำหรับดึงข้อมูลขั้นตอนและไทม์ไลน์ของเอกสารมาแสดงใน Modal
     */
    public function trackDocJson($id)
    {
        $type = $this->request->getGet('type');

        $document = null;
        if ($type !== 'travel') {
            $document = $this->documentModel->getDocumentDetails($id);
            if ($document && $document['status'] === 'draft') {
                $document = null; // ป้องกันการเรียกดูข้อมูลฉบับร่างผ่าน API สาธารณะ
            }
        }

        // หากเป็นคำขอไปราชการ
        if (!$document) {
            $travel = $this->travelModel->select('travel_requests.*, users.prefix, users.first_name, users.last_name, departments.name as department_name')
                                        ->join('users', 'users.id = travel_requests.user_id', 'left')
                                        ->join('departments', 'departments.id = users.department_id', 'left')
                                        ->where('travel_requests.id', $id)
                                        ->first();

            if (!$travel) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'ไม่พบข้อมูลเอกสารในระบบ'
                ])->setStatusCode(404);
            }

            $routes = $this->travelRouteModel->getRoutesForTravel($id);
            $logs   = $this->travelLogModel->getLogsForTravel($id);

            $formattedRoutes = [];
            if (!empty($routes)) {
                foreach ($routes as $r) {
                    $formattedRoutes[] = [
                        'id'             => $r['id'],
                        'step_order'     => $r['step_order'],
                        'user_name'      => ($r['prefix'] ?? '') . ($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? ''),
                        'position_name'  => $r['step_name'] ?? ($r['position_name'] ?? 'ผู้พิจารณา'),
                        'department'     => $r['department_name'] ?? '',
                        'status'         => $r['status'],
                        'comment_preset' => $r['comment_preset'],
                        'comment_text'   => $r['comment_text'],
                        'signed_at_th'   => $r['signed_at'] ? thai_date($r['signed_at'], 'short', true) : null,
                        'is_current'     => ($r['step_order'] == $travel['current_step'] && $travel['status'] === 'pending'),
                    ];
                }
            } else {
                // หากไม่ได้กำหนด travel_routes แยก ให้สร้างจาก 6 ขั้นตอนมาตรฐานของไปราชการ
                $trackingStages = [
                    1 => ['name' => '1. ยื่นขออนุมัติ', 'role' => 'ผู้ขอยื่นคำขอ / รร. ตรวจสอบ'],
                    2 => ['name' => '2. โรงเรียน', 'role' => 'จัดทำเรื่อง / ตรวจเอกสารจริง'],
                    3 => ['name' => '3. กองการศึกษา', 'role' => 'กองการศึกษา ศาสนา และวัฒนธรรม'],
                    4 => ['name' => '4. กองการเจ้าหน้าที่', 'role' => 'ตรวจสอบคุณสมบัติและระเบียบ'],
                    5 => ['name' => '5. สำนักปลัด', 'role' => 'กลั่นกรองเสนอผู้บริหาร'],
                    6 => ['name' => '6. นายก อบจ.', 'role' => 'พิจารณาลงนามอนุมัติคำขอ'],
                ];

                $stepReceivers = json_decode($travel['step_receivers'] ?? '{}', true) ?: [];
                $curStep = (int)$travel['current_step'];
                $isApproved = ($travel['status'] === 'approved');
                $isReturned = ($travel['status'] === 'returned');
                $isApprovedByClerk = ($isApproved && !empty($travel['approver_title']) && str_contains($travel['approver_title'], 'ปลัด'));

                foreach ($trackingStages as $stepNum => $stg) {
                    $rec = $stepReceivers[$stepNum] ?? null;
                    $isDone = false;
                    $isCur = false;
                    $isRet = false;

                    if ($isApproved) {
                        if ($isApprovedByClerk && $stepNum === 6) {
                            $status = 'skipped';
                        } else {
                            $isDone = ($stepNum <= 5 || !$isApprovedByClerk) && (!empty($rec['name']) || $stepNum <= $curStep);
                            $status = $isDone ? 'signed' : 'pending';
                        }
                    } elseif ($isReturned && $stepNum == $curStep) {
                        $isRet = true;
                        $status = 'returned';
                    } elseif ($stepNum < $curStep) {
                        $isDone = true;
                        $status = 'signed';
                    } elseif ($stepNum == $curStep) {
                        $isCur = true;
                        $status = 'pending';
                    } else {
                        $status = 'pending';
                    }

                    $userName = !empty($rec['name']) ? $rec['name'] : ($isCur ? ($travel['current_receiver'] ?? '-') : '-');
                    $signedAt = !empty($rec['updated_at']) ? thai_date($rec['updated_at'], 'short', true) : null;
                    $commentNote = !empty($rec['note']) ? $rec['note'] : null;

                    $formattedRoutes[] = [
                        'id'             => $stepNum,
                        'step_order'     => $stepNum,
                        'user_name'      => $userName,
                        'position_name'  => $stg['name'] . ' (' . $stg['role'] . ')',
                        'department'     => '',
                        'status'         => $status,
                        'comment_preset' => $isApproved && $status === 'signed' ? ($travel['approver_title'] ? 'อนุมัติโดย ' . $travel['approver_title'] : 'อนุมัติเรียบร้อย') : null,
                        'comment_text'   => $commentNote,
                        'signed_at_th'   => $signedAt,
                        'is_current'     => $isCur,
                    ];
                }
            }

            // คำนวณเส้นทางขากลับ (Return Routes) สำหรับคำขอไปราชการ
            $curStep        = (int)$travel['current_step'];
            $curRetStep     = (int)$travel['return_step'];
            $isApproved     = ($travel['status'] === 'approved');
            $isApprovedByClerk = ($isApproved && !empty($travel['approver_title']) && str_contains($travel['approver_title'], 'ปลัด'));

            $approvedAtStep = $curStep;
            if ($isApprovedByClerk) {
                $approvedAtStep = 5;
            } elseif ($isApproved && $curStep < 5) {
                $approvedAtStep = max(1, $curStep);
            } elseif ($isApproved && empty($travel['approver_title'])) {
                $approvedAtStep = max(1, $curStep);
            }

            $allNodeStages = [
                1 => ['name' => 'ผู้ขอยื่นเรื่อง', 'desc' => 'ผู้ขอรับหนังสือตัวจริงเรียบร้อย (เสร็จสิ้น)'],
                2 => ['name' => 'โรงเรียน', 'desc' => 'งานสารบรรณ/ธุรการ รร. ลงรับแฟ้มตัวจริง'],
                3 => ['name' => 'กองการศึกษา', 'desc' => 'กองการศึกษาฯ ส่งกลับโรงเรียน'],
                4 => ['name' => 'กองการเจ้าหน้าที่', 'desc' => 'กองการเจ้าหน้าที่ ส่งต่อกองการศึกษา'],
                5 => ['name' => 'สำนักปลัด', 'desc' => 'สำนักปลัด อบจ. ส่งเอกสารตามสายงาน'],
                6 => ['name' => 'นายก อบจ.', 'desc' => 'นายก อบจ. ออกเลขส่งคืนตัวจริง'],
            ];

            $formattedReturnRoutes = [];
            $retIndex = 1;
            for ($s = $approvedAtStep; $s >= 1; $s--) {
                $node = $allNodeStages[$s];
                $isRetDone = ($curRetStep >= $retIndex);
                $isRetCur  = ($curRetStep == $retIndex && $isApproved && $curRetStep < $approvedAtStep);
                $retRec    = $stepReceivers['return_' . $retIndex] ?? null;

                $retStatus = 'pending';
                if (!$isApproved) {
                    $retStatus = 'pending';
                } elseif ($isRetDone) {
                    $retStatus = 'signed';
                } elseif ($isRetCur) {
                    $retStatus = 'active';
                }

                $formattedReturnRoutes[] = [
                    'id'             => 'ret_' . $retIndex,
                    'step_order'     => $retIndex,
                    'orig_step'      => $s,
                    'user_name'      => !empty($retRec['name']) ? $retRec['name'] : ($isRetCur ? ($travel['current_receiver'] ?? '-') : '-'),
                    'position_name'  => $retIndex . '. ' . $node['name'] . ' (' . ($s === $approvedAtStep ? 'ผู้อนุมัติส่งคืนตัวจริง' : ($s === 1 ? 'ผู้ขอรับหนังสือตัวจริง' : $node['desc'])) . ')',
                    'department'     => '',
                    'status'         => $retStatus,
                    'comment_preset' => $isRetDone ? ($retIndex == $approvedAtStep ? 'ส่งมอบตัวจริงครบถ้วนสมบูรณ์' : 'ส่งต่อแฟ้มตัวจริงเรียบร้อย') : null,
                    'comment_text'   => !empty($retRec['note']) ? $retRec['note'] : null,
                    'signed_at_th'   => !empty($retRec['updated_at']) ? thai_date($retRec['updated_at'], 'short', true) : null,
                    'is_current'     => $isRetCur,
                ];
                $retIndex++;
            }

            $formattedLogs = [];
            foreach ($logs as $l) {
                $formattedLogs[] = [
                    'action'        => $l['action'],
                    'detail'        => $l['detail'],
                    'user_name'     => ($l['prefix'] ?? '') . ($l['first_name'] ?? '') . ' ' . ($l['last_name'] ?? ''),
                    'position_name' => $l['position_name'] ?? '',
                    'created_at_th' => !empty($l['created_at']) ? thai_date($l['created_at'], 'short', true) : '-',
                ];
            }

            return $this->response->setJSON([
                'success'  => true,
                'document' => [
                    'id'              => $travel['id'],
                    'doc_no'          => $travel['doc_no'],
                    'internal_reg_no' => 'ไปราชการ',
                    'title'           => $travel['title'] . ' (ณ ' . $travel['destination'] . ')',
                    'doc_type'        => 'travel',
                    'doc_type_badge'  => doc_type_badge('travel'),
                    'urgency'         => 'normal',
                    'urgency_badge'   => urgency_badge('normal'),
                    'status'          => ($travel['status'] === 'approved') ? 'completed' : (($travel['status'] === 'returned') ? 'returned' : 'routing'),
                    'status_badge'    => travel_status_badge($travel['status']),
                    'current_step'    => $travel['current_step'],
                    'approver_title'  => $travel['approver_title'],
                    'return_step'     => (int)$travel['return_step'],
                    'return_badge'    => travel_return_badge($travel['return_step']),
                    'return_note'     => $travel['return_note'],
                    'from_org'        => ($travel['prefix'] ?? '') . $travel['first_name'] . ' ' . $travel['last_name'] . ' (' . ($travel['department_name'] ?? '') . ')',
                    'to_org'          => $this->getTravelDestination($travel),
                    'doc_date_th'     => thai_date($travel['start_date'], 'short') . ' - ' . thai_date($travel['end_date'], 'short'),
                    'file_path'       => $travel['file_path'],
                ],
                'routes'        => $formattedRoutes,
                'return_routes' => $formattedReturnRoutes,
                'logs'          => $formattedLogs,
            ]);
        }

        $routes = $this->routeModel->getRoutesForDocument($id);
        $logs   = $this->logModel->getLogsForDocument($id);

        // ปรับแต่งข้อมูล route ให้แสดงผลพร้อมวันที่ไทย
        $formattedRoutes = [];
        foreach ($routes as $r) {
            $formattedRoutes[] = [
                'id'             => $r['id'],
                'step_order'     => $r['step_order'],
                'user_name'      => ($r['prefix'] ?? '') . ($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? ''),
                'position_name'  => $r['position_name'] ?? 'ไม่ระบุตำแหน่ง',
                'department'     => $r['department_name'] ?? '',
                'status'         => $r['status'],
                'comment_preset' => $r['comment_preset'],
                'comment_text'   => $r['comment_text'],
                'signed_at_th'   => $r['signed_at'] ? thai_date($r['signed_at'], 'short', true) : null,
                'is_current'     => ($r['step_order'] == $document['current_step'] && $document['status'] === 'routing'),
            ];
        }

        $formattedLogs = [];
        foreach ($logs as $l) {
            $formattedLogs[] = [
                'action'        => $l['action'],
                'detail'        => $l['detail'],
                'user_name'     => ($l['prefix'] ?? '') . ($l['first_name'] ?? '') . ' ' . ($l['last_name'] ?? ''),
                'position_name' => $l['position_name'] ?? '',
                'created_at_th' => !empty($l['created_at']) ? thai_date($l['created_at'], 'short', true) : '-',
            ];
        }

        return $this->response->setJSON([
            'success'  => true,
            'document' => [
                'id'              => $document['id'],
                'doc_no'          => $document['doc_no'],
                'internal_reg_no' => $document['internal_reg_no'],
                'title'           => $document['title'],
                'doc_type'        => $document['doc_type'],
                'doc_type_badge'  => doc_type_badge($document['doc_type']),
                'urgency'         => $document['urgency'],
                'urgency_badge'   => urgency_badge($document['urgency']),
                'status'          => $document['status'],
                'status_badge'    => doc_status_badge($document['status']),
                'current_step'    => $document['current_step'],
                'from_org'        => $document['from_org'],
                'to_org'          => $document['to_org'],
                'doc_date_th'     => thai_date($document['doc_date'], 'short'),
                'file_path'       => $document['file_path'],
            ],
            'routes'   => $formattedRoutes,
            'logs'     => $formattedLogs,
        ]);
    }

    /**
     * คำนวณหน่วยงาน/ขั้นตอนล่าสุดที่เอกสารไปถึง สำหรับคำขอไปราชการ
     */
    protected function getTravelDestination(array $travel): string
    {
        $status        = $travel['status'] ?? 'pending';
        $curStep       = (int)($travel['current_step'] ?? 1);
        $approverTitle = trim($travel['approver_title'] ?? '');

        // 1. กรณีอนุมัติแล้ว: แสดงขั้นตอน/หน่วยงานสูงสุดที่ลงนามอนุมัติ
        if ($status === 'approved') {
            if (!empty($approverTitle)) {
                if (str_contains($approverTitle, 'ปลัด')) {
                    return 'สำนักปลัด';
                }
                if (str_contains($approverTitle, 'นายก')) {
                    return 'นายก อบจ.';
                }
                return $approverTitle;
            }
            if ($curStep == 5) {
                return 'สำนักปลัด';
            }
            return 'นายก อบจ.';
        }

        // 2. แผนผังขั้นตอนขาไป
        $stepNames = [
            1 => 'โรงเรียน',
            2 => 'โรงเรียน',
            3 => 'กองการศึกษา',
            4 => 'กองการเจ้าหน้าที่',
            5 => 'สำนักปลัด',
            6 => 'นายก อบจ.',
        ];

        $targetName = $stepNames[$curStep] ?? ('ขั้นตอนที่ ' . $curStep);

        // กรณีส่งคืนแก้ไข
        if ($status === 'returned') {
            return $targetName . ' (ส่งคืนแก้ไข)';
        }

        return $targetName;
    }
}
