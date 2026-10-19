<?php

namespace App\Controllers;

use App\Models\TravelRequestModel;
use App\Models\TravelRouteModel;
use App\Models\TravelLogModel;
use App\Models\UserModel;
use App\Models\DepartmentModel;

class TravelController extends BaseController
{
    protected $travelModel;
    protected $routeModel;
    protected $logModel;
    protected $userModel;
    protected $deptModel;

    public function __construct()
    {
        $this->travelModel = new TravelRequestModel();
        $this->routeModel  = new TravelRouteModel();
        $this->logModel    = new TravelLogModel();
        $this->userModel   = new UserModel();
        $this->deptModel   = new DepartmentModel();
        helper(['thai_date', 'form', 'url']);
    }

    // ติดตามคำขอไปราชการทั้งหมด
    public function index()
    {
        $status = $this->request->getGet('status');
        $userId = session()->get('user_id');

        $query = $this->travelModel->select('travel_requests.*, users.prefix, users.first_name, users.last_name, departments.name as department_name, p.pers_prefix, p.pers_firstname, p.pers_lastname, pos.posi_name as personnel_posi_name, l.lear_namethai as personnel_dept_name')
                                   ->join('users', 'users.id = travel_requests.user_id', 'left')
                                   ->join('departments', 'departments.id = users.department_id', 'left')
                                   ->join('skjacth_personnel.tb_personnel p', "p.pers_id = users.pers_id OR p.pers_username = users.email OR p.pers_username = users.username", 'left')
                                   ->join('skjacth_skj.tb_position pos', 'pos.posi_id = p.pers_position', 'left')
                                   ->join('skjacth_skj.tb_learning l', 'l.lear_id = p.pers_learning', 'left');

        if (!empty($status)) {
            $query->where('travel_requests.status', $status);
        }

        $requests = $query->orderBy('travel_requests.created_at', 'DESC')->findAll();

        foreach ($requests as &$req) {
            if (!empty($req['pers_firstname'])) {
                $req['prefix'] = $req['pers_prefix'];
                $req['first_name'] = $req['pers_firstname'];
                $req['last_name'] = $req['pers_lastname'];
                $req['department_name'] = !empty($req['personnel_dept_name']) ? ('กลุ่มสาระฯ ' . $req['personnel_dept_name']) : ($req['department_name'] ?: 'สำนักงาน');
            }
        }
        unset($req);

        $pendingMe = $this->travelModel->getRequestsForUser($userId, 'pending_me');

        return view('travel/index', [
            'requests'       => $requests,
            'currentStatus'  => $status,
            'pendingMeCount' => count($pendingMe),
        ]);
    }

    // รายการของฉัน & ติดตามสถานะ
    public function my()
    {
        $userId   = session()->get('user_id');
        $requests = $this->travelModel->getRequestsForUser($userId, 'my');

        foreach ($requests as &$req) {
            $req['routes'] = $this->routeModel->getRoutesForTravel($req['id']);
            $req['latest_log'] = $this->logModel->where('travel_id', $req['id'])
                                                ->orderBy('id', 'DESC')
                                                ->first();
        }
        unset($req);

        return view('travel/my', [
            'requests' => $requests
        ]);
    }

    // รายการรอฉันพิจารณา / ลงนาม
    public function inbox()
    {
        $userId   = session()->get('user_id');
        $requests = $this->travelModel->getRequestsForUser($userId, 'pending_me');

        return view('travel/inbox', [
            'requests' => $requests
        ]);
    }

    // ฟอร์มยื่นขออนุมัติไปราชการใหม่
    public function create()
    {
        $userId        = session()->get('user_id');
        $user          = $this->userModel->getUserWithDetails($userId);
        $allUsers      = $this->userModel->getAllActiveUsers();
        $personnelList = $this->userModel->getPersonnelForSelection();
        $nextDocNo     = $this->travelModel->getNextDocNumber();

        return view('travel/create', [
            'user'          => $user,
            'allUsers'      => $allUsers,
            'personnelList' => $personnelList,
            'nextDocNo'     => $nextDocNo,
        ]);
    }

    // บันทึกคำขอไปราชการ
    public function store()
    {
        $sessionUserId = session()->get('user_id');
        $sessionRole   = session()->get('role');
        $isOfficer     = in_array($sessionRole, ['super_admin', 'admin', 'saraban', 'executive'], true);

        // ตรวจสอบว่าเป็นการบันทึกแทนครู/บุคลากรหรือไม่
        $effectiveUserId = $sessionUserId;

        if ($isOfficer) {
            $applicantParam = trim($this->request->getPost('applicant_user_id') ?: '');
            if (!empty($applicantParam) && $applicantParam != $sessionUserId) {
                // หากเลือกเป็น pers_id เช่น "pers_004"
                if (strpos($applicantParam, 'pers_') === 0) {
                    $person = $this->userModel->findPersonnel($applicantParam);
                    if ($person) {
                        $synced = $this->userModel->syncPersonnelToUser($person);
                        $effectiveUserId = (int)$synced['id'];
                    }
                } elseif (is_numeric($applicantParam)) {
                    $existingUser = $this->userModel->find($applicantParam);
                    if ($existingUser) {
                        $effectiveUserId = (int)$applicantParam;
                    } else {
                        $person = $this->userModel->findPersonnel($applicantParam);
                        if ($person) {
                            $synced = $this->userModel->syncPersonnelToUser($person);
                            $effectiveUserId = (int)$synced['id'];
                        }
                    }
                } else {
                    $person = $this->userModel->findPersonnel($applicantParam);
                    if ($person) {
                        $synced = $this->userModel->syncPersonnelToUser($person);
                        $effectiveUserId = (int)$synced['id'];
                    }
                }
            }
        }

        $docNo           = trim($this->request->getPost('doc_no') ?: $this->travelModel->getNextDocNumber());
        $title           = trim($this->request->getPost('title') ?: '');

        // 1. ตรวจสอบเลขที่หนังสือขอไปราชการ (doc_no) ซ้ำ
        if (empty($docNo)) {
            return redirect()->back()->withInput()->with('error', 'กรุณาระบุเลขที่หนังสือขอไปราชการ');
        }
        $existingDoc = $this->travelModel->where('doc_no', $docNo)->first();
        if ($existingDoc) {
            return redirect()->back()->withInput()->with('error', "เลขที่หนังสือ '{$docNo}' มีอยู่ในระบบแล้ว กรุณาตรวจสอบหรือเปลี่ยนเลขที่หนังสือ");
        }

        // 2. ตรวจสอบเรื่อง (title) ซ้ำ
        if (empty($title)) {
            return redirect()->back()->withInput()->with('error', 'กรุณาระบุเรื่อง / วัตถุประสงค์การไปราชการ');
        }
        $existingTitle = $this->travelModel->where('title', $title)->first();
        if ($existingTitle) {
            return redirect()->back()->withInput()->with('error', "เรื่อง '{$title}' มีบันทึกอยู่ในระบบแล้ว (เลขที่หนังสือ: {$existingTitle['doc_no']}) กรุณาตรวจสอบเพื่อป้องกันการยื่นเรื่องซ้ำซ้อน");
        }

        $destination     = trim((string)$this->request->getPost('destination'));
        $province        = trim((string)$this->request->getPost('province')) ?: 'กรุงเทพมหานคร';
        $startDate       = $this->request->getPost('start_date');
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $startDate, $m) && (int)$m[1] > 2400) {
            $startDate = ((int)$m[1] - 543) . '-' . $m[2] . '-' . $m[3];
        }

        $endDate         = $this->request->getPost('end_date');
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $endDate, $m) && (int)$m[1] > 2400) {
            $endDate = ((int)$m[1] - 543) . '-' . $m[2] . '-' . $m[3];
        }
        $transportType   = 'public_transport';
        $transportDetail = null;
        $budgetType      = 'no_budget';
        $budgetAmount    = 0.00;
        $companions      = null;
        $teachingAssign  = null;

        // Calculate total days
        $start = strtotime($startDate);
        $end   = strtotime($endDate);
        $totalDays = max(1, round(($end - $start) / (60 * 60 * 24)) + 1);

        // Handle attachment (Optional in Step 1: Applicant submission & document checklist verification)
        $file = $this->request->getFile('doc_file');
        $filePath = null;
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $ext = strtolower($file->getClientExtension());
            if ($ext !== 'pdf') {
                $errMsg = 'ระบบรองรับเฉพาะไฟล์เอกสารรูปแบบ PDF (.pdf) เท่านั้น';
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['status' => 'error', 'message' => $errMsg])->setStatusCode(400);
                }
                return redirect()->back()->withInput()->with('error', $errMsg);
            }

            // อัปโหลดไปยัง Remote Upload Server (skj.nsnpao.go.th)
            $uploadResult = $this->_uploadFileToServer($file, 'saraban/travel', $file->getClientName());
            if ($uploadResult['status'] === 'success') {
                $filePath = 'saraban/travel/' . $uploadResult['filename'];
            } else {
                // Fallback กรณีเชื่อมต่อเซิร์ฟเวอร์หลักขัดข้อง
                $newName = $file->getRandomName();
                $file->move(FCPATH . 'uploads/documents', $newName);
                $filePath = 'uploads/documents/' . $newName;
            }
        }

        // Attached documents checklist
        $attachedDocs     = $this->request->getPost('attached_docs') ?: [];
        $otherDocDetail   = $this->request->getPost('other_doc_detail') ?: null;
        $attachedDocsJson = !empty($attachedDocs) ? json_encode($attachedDocs, JSON_UNESCAPED_UNICODE) : null;

        // ดึงข้อมูลชื่อจริงของผู้บันทึก (คนที่กดบันทึก) จาก user_id ใน session
        $recorderUser = $this->userModel->getUserWithDetails($sessionUserId);
        $recorderName = $recorderUser ? trim($recorderUser['prefix'] . $recorderUser['first_name'] . ' ' . $recorderUser['last_name']) : 'ผู้บันทึก ID: ' . $sessionUserId;
        $stepReceivers = [
            1 => [
                'user_id'    => $sessionUserId,
                'name'       => $recorderName,
                'updated_at' => date('Y-m-d H:i:s'),
                'note'       => 'ยื่นขออนุมัติในระบบเรียบร้อย (ตรวจสอบรายการเอกสารที่ส่งมาด้วย)',
            ],
        ];

        $travelData = [
            'doc_no'           => $docNo,
            'user_id'          => $effectiveUserId,
            'title'            => $title,
            'destination'      => $destination,
            'province'         => $province,
            'start_date'       => $startDate,
            'end_date'         => $endDate,
            'total_days'       => $totalDays,
            'transport_type'   => $transportType,
            'transport_detail' => $transportDetail,
            'budget_type'      => $budgetType,
            'budget_amount'    => $budgetAmount,
            'companions'       => $companions,
            'teaching_assign'  => $teachingAssign,
            'file_path'        => $filePath,
            'attached_docs'    => $attachedDocsJson,
            'other_doc_detail' => $otherDocDetail,
            'status'           => 'pending',
            'current_step'     => 1,
            'current_receiver' => $recorderName,
            'step_receivers'   => json_encode($stepReceivers, JSON_UNESCAPED_UNICODE),
        ];

        $travelId = $this->travelModel->insert($travelData);

        // Optional School Routing Steps (Only inserted if valid users provided)
        $routeUsers = $this->request->getPost('route_users');
        if (!empty($routeUsers) && is_array($routeUsers)) {
            $step = 1;
            foreach ($routeUsers as $targetUserId) {
                if (!empty($targetUserId)) {
                    $userExists = $this->userModel->find($targetUserId);
                    if ($userExists) {
                        $this->routeModel->insert([
                            'travel_id'  => $travelId,
                            'step_order' => $step,
                            'user_id'    => $targetUserId,
                            'status'     => 'pending',
                        ]);
                        $step++;
                    }
                }
            }
        }

        // Audit log
        if ($effectiveUserId != $sessionUserId) {
            $targetUser = $this->userModel->getUserWithDetails($effectiveUserId);
            $targetName = $targetUser ? ($targetUser['prefix'] . $targetUser['first_name'] . ' ' . $targetUser['last_name']) : "ID: {$effectiveUserId}";
            $logDetail = "เจ้าหน้าที่บันทึกยื่นคำขอไปราชการแทน: {$targetName} (ขั้นตอนที่ 1: บันทึกยื่นขออนุมัติ/ตรวจสอบเอกสารที่ส่งมาด้วย)";
        } else {
            $logDetail = 'ขั้นตอนที่ 1: บันทึกยื่นขออนุมัติในระบบเรียบร้อย (ตรวจสอบรายการเอกสารที่ส่งมาด้วย) เตรียมนำส่งแฟ้มให้เจ้าหน้าที่ธุรการจัดทำเรื่องในขั้นตอนที่ 2';
        }
        $successMsg = 'บันทึกคำขอไปราชการเรียบร้อยแล้ว';
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status'   => 'success',
                'message'  => $successMsg,
                'redirect' => base_url('admin/travel/' . $travelId),
            ]);
        }

        return redirect()->to('/admin/travel/' . $travelId)->with('success', $successMsg);
    }

    // ดูรายละเอียด & ติดตามความคืบหน้าคำขอ
    public function view($id)
    {
        $userId  = session()->get('user_id');
        $travel  = $this->travelModel->getRequestWithDetails($id);

        if (!$travel) {
            return redirect()->to('/admin/travel')->with('error', 'ไม่พบข้อมูลคำขอไปราชการ');
        }

        $routes = $this->routeModel->getRoutesForTravel($id);
        $logs   = $this->logModel->getLogsForTravel($id);

        // Check if it is current user's turn to approve
        $canSign = false;
        $currentRoute = null;

        foreach ($routes as $route) {
            if ($route['step_order'] == $travel['current_step'] && $route['user_id'] == $userId && $route['status'] === 'pending' && $travel['status'] === 'pending') {
                $canSign = true;
                $currentRoute = $route;
                break;
            }
        }

        return view('travel/view', [
            'travel'       => $travel,
            'routes'       => $routes,
            'logs'         => $logs,
            'canSign'      => $canSign,
            'currentRoute' => $currentRoute,
        ]);
    }

    // ลงนามพิจารณา / อนุมัติการไปราชการ
    public function sign($id)
    {
        $userId = session()->get('user_id');
        $travel = $this->travelModel->find($id);

        if (!$travel) {
            return redirect()->to('/admin/travel')->with('error', 'ไม่พบข้อมูลคำขอ');
        }

        $route = $this->routeModel->where('travel_id', $id)
                                  ->where('step_order', $travel['current_step'])
                                  ->where('user_id', $userId)
                                  ->where('status', 'pending')
                                  ->first();

        if (!$route) {
            return redirect()->back()->with('error', 'คุณไม่มีสิทธิ์ลงนามในขั้นตอนนี้');
        }

        $actionType    = $this->request->getPost('action_type');
        $commentPreset = $this->request->getPost('comment_preset');
        $commentText   = $this->request->getPost('comment_text');
        $signatureType = $this->request->getPost('signature_type') ?: 'draw';
        $signatureData = $this->request->getPost('signature_data');

        if ($actionType === 'return') {
            $this->routeModel->update($route['id'], [
                'status'         => 'returned',
                'comment_preset' => 'ส่งคืนแก้ไข/ไม่อนุมัติ',
                'comment_text'   => $commentText,
                'signed_at'      => date('Y-m-d H:i:s'),
            ]);

            $this->travelModel->update($id, [
                'status' => 'returned'
            ]);

            $this->logModel->log($id, $userId, 'RETURN', 'ส่งคืนคำขอไปราชการให้แก้ไข: ' . $commentText);

            return redirect()->to('/admin/travel/' . $id)->with('warning', 'ส่งคืนคำขอไปราชการให้ผู้ขอแก้ไขเรียบร้อยแล้ว');
        }

        // Handle signature image
        $signaturePath = '';
        if ($signatureType === 'draw' && !empty($signatureData)) {
            $data = explode(',', $signatureData);
            $img  = base64_decode(end($data));
            $filename = 'sig_travel_' . $id . '_step_' . $route['step_order'] . '_' . time() . '.png';
            $path = FCPATH . 'uploads/signatures/' . $filename;
            file_put_contents($path, $img);
            $signaturePath = 'uploads/signatures/' . $filename;
        } else {
            $user = $this->userModel->find($userId);
            $signaturePath = $user['signature_img'] ?? '';
        }

        $verifyString = $id . '|' . $userId . '|' . date('Y-m-d H:i:s') . '|' . $this->request->getIPAddress();
        $verifyHash   = hash('sha256', $verifyString);

        $this->routeModel->update($route['id'], [
            'status'         => 'signed',
            'comment_preset' => $commentPreset,
            'comment_text'   => $commentText,
            'signature_img'  => $signaturePath,
            'signature_type' => $signatureType,
            'signed_at'      => date('Y-m-d H:i:s'),
            'verify_hash'    => $verifyHash,
        ]);

        $nextStep = $this->routeModel->where('travel_id', $id)
                                     ->where('step_order', $travel['current_step'] + 1)
                                     ->first();

        $userProfile = $this->userModel->getUserWithDetails($userId);
        $isFinal = $userProfile['can_sign_final'] == 1;

        if ($nextStep && !$isFinal) {
            $this->travelModel->update($id, [
                'current_step' => $travel['current_step'] + 1
            ]);
            $this->logModel->log($id, $userId, 'SIGN', "พิจารณาให้ความเห็นชอบ (ขั้นที่ {$route['step_order']}) และส่งต่อ");
            $msg = 'ลงนามพิจารณาเรียบร้อยแล้ว และส่งต่อไปยังลำดับถัดไป';
        } else {
            $this->travelModel->update($id, [
                'status' => 'approved'
            ]);
            $this->logModel->log($id, $userId, 'APPROVE', "ผู้อำนวยการอนุมัติการเดินทางไปราชการเรียบร้อยแล้ว");
            $msg = 'อนุมัติการเดินทางไปราชการเสร็จสมบูรณ์เรียบร้อยแล้ว';
        }

        return redirect()->to('/admin/travel/' . $id)->with('success', $msg);
    }

    // สำหรับเจ้าหน้าที่ธุรการ / ผู้ดูแล บันทึกติดตามขั้นตอนเอกสาร
    public function updateStep($id)
    {
        $userId   = session()->get('user_id');
        $userRole = session()->get('role');

        // ตรวจสอบสิทธิ์: เฉพาะเจ้าหน้าที่สารบรรณ/ธุรการ ผู้ดูแลระบบ หรือผู้บริหารเท่านั้น
        if (!in_array($userRole, ['admin', 'saraban', 'executive'])) {
            return redirect()->back()->with('error', 'ท่านไม่มีสิทธิ์ในการอัปเดตขั้นตอนติดตามเอกสาร (เฉพาะเจ้าหน้าที่สารบรรณ/ธุรการ หรือผู้ดูแลระบบเท่านั้น)');
        }

        $travel = $this->travelModel->find($id);
        if (!$travel) {
            return redirect()->to('/admin/travel')->with('error', 'ไม่พบข้อมูลคำขอไปราชการ');
        }

        $trackingMode = $this->request->getPost('tracking_mode') ?: 'outbound';
        $db = \Config\Database::connect();
        $db->transStart();

        // -------------------------------------------------------------
        // โหมดที่ 1: บันทึกการส่งมอบขากลับ (Return Tracking)
        // -------------------------------------------------------------
        if ($trackingMode === 'return') {
            $returnStep   = (int)$this->request->getPost('return_step');
            $returnNote   = trim($this->request->getPost('return_note') ?: '');

            // คำนวณจุดที่ขาไปสิ้นสุด (approved step)
            $curStep = (int)$travel['current_step'];
            $isApprovedByClerk = (!empty($travel['approver_title']) && str_contains($travel['approver_title'], 'ปลัด'));
            $approvedAtStep = $isApprovedByClerk ? 5 : max(1, $curStep);

            $allNodeTitles = [
                1 => 'ส่งมอบหนังสืออนุมัติตัวจริงให้ผู้ขอไปราชการเรียบร้อยแล้ว (เสร็จสิ้นสมบูรณ์)',
                2 => 'งานสารบรรณ/ธุรการโรงเรียน ลงรับหนังสือตัวจริงเรียบร้อยแล้ว',
                3 => 'กองการศึกษา ศาสนา และวัฒนธรรม ส่งกลับโรงเรียน',
                4 => 'กองการเจ้าหน้าที่ ส่งต่อกองการศึกษา',
                5 => 'สำนักปลัด อบจ. ส่งเอกสารตามสายงาน',
                6 => 'นายก อบจ. ส่งหนังสืออนุมัติตัวจริง',
            ];

            // สร้างรายการขากลับตามจุดที่ขาไปสิ้นสุด
            $returnTitles = [];
            $rIndex = 1;
            for ($s = $approvedAtStep; $s >= 1; $s--) {
                $returnTitles[$rIndex] = $allNodeTitles[$s];
                $rIndex++;
            }
            $totalReturnSteps = count($returnTitles);
            $curReturnStep = (int)($travel['return_step'] ?? 0);

            if (!isset($returnTitles[$returnStep])) {
                return redirect()->back()->with('error', 'ขั้นตอนขากลับไม่ถูกต้องตามเส้นทางที่ได้รับอนุมัติ');
            }

            // ป้องกันการข้ามขั้นตอนขากลับ: เลือกได้เฉพาะขั้นตอนเดิม หรือขั้นตอนถัดไป (+1)
            if ($returnStep > $curReturnStep + 1) {
                return redirect()->back()->with('error', "ไม่สามารถข้ามขั้นตอนขากลับได้! ขณะนี้อยู่ที่ขั้นที่ {$curReturnStep} สามารถบันทึกได้เฉพาะขั้นถัดไป (ขั้นที่ " . ($curReturnStep + 1) . ") เท่านั้น");
            }

            // ดึงชื่อจริงของเจ้าหน้าที่ที่กดบันทึก (session user)
            $recorderUser = $this->userModel->getUserWithDetails($userId);
            $receiverName = $recorderUser ? trim($recorderUser['prefix'] . $recorderUser['first_name'] . ' ' . $recorderUser['last_name']) : 'ผู้บันทึก ID: ' . $userId;

            $stepReceivers = json_decode($travel['step_receivers'] ?: '{}', true) ?: [];
            $stepReceivers["return_{$returnStep}"] = [
                'user_id'    => $userId,
                'name'       => $receiverName,
                'updated_at' => date('Y-m-d H:i:s'),
                'note'       => $returnNote,
            ];

            $updateData = [
                'return_step'      => $returnStep,
                'return_note'      => $returnNote,
                'current_receiver' => $receiverName,
                'step_receivers'   => json_encode($stepReceivers, JSON_UNESCAPED_UNICODE),
            ];
            if ($returnStep === $totalReturnSteps) {
                $updateData['returned_to_user_at'] = date('Y-m-d H:i:s');
            }

            $this->travelModel->update($id, $updateData);

            $logDetail = "บันทึกการส่งมอบขากลับ: " . $returnTitles[$returnStep] . " [ผู้รับเอกสาร: {$receiverName}]" . (!empty($returnNote) ? " (หมายเหตุ: {$returnNote})" : "");
            $this->logModel->log($id, $userId, 'RETURN_TRACK', $logDetail);

            $db->transComplete();
            if ($db->transStatus() === false) {
                return redirect()->back()->with('error', 'เกิดข้อผิดพลาดในการบันทึกข้อมูลขากลับ');
            }

            return redirect()->to('/admin/travel/' . $id)->with('success', 'บันทึกการส่งมอบขากลับ: ' . $returnTitles[$returnStep] . ' เรียบร้อยแล้ว');
        }

        // -------------------------------------------------------------
        // โหมดที่ 2: เสนอขั้นตอนขาไป & การอนุมัติ (Outbound & Approval)
        // -------------------------------------------------------------
        $newStep      = (int)$this->request->getPost('step');
        $status       = $this->request->getPost('status') ?: 'pending';
        $note         = trim($this->request->getPost('tracking_note') ?: '');
        // ไม่ใช้ receiver_name จาก form อีกต่อไป — ใช้ชื่อจริงของ session user แทน
        $approvalType = $this->request->getPost('approval_type'); // 'clerk' (ปลัด), 'president' (นายก)

        $stepTitles = [
            1 => 'ยื่นขออนุมัติ (ผู้ขอยื่นคำขอในระบบ / ตรวจสอบเอกสารที่ส่งมาด้วย)',
            2 => 'โรงเรียน (จัดทำเรื่อง / ตรวจเอกสารฉบับจริง / แนบไฟล์คำสั่ง)',
            3 => 'กองการศึกษา (กองการศึกษา ศาสนา และวัฒนธรรม)',
            4 => 'กองการเจ้าหน้าที่ (ตรวจสอบคุณสมบัติและระเบียบ)',
            5 => 'สำนักปลัด (กลั่นกรองเสนอผู้บริหาร อบจ.)',
            6 => 'นายก อบจ. (พิจารณาลงนามอนุมัติ)',
            7 => 'อนุมัติเรียบร้อยแล้ว (เสร็จสิ้นการพิจารณา)',
        ];

        $curStep = (int)$travel['current_step'];
        $isApp   = ($travel['status'] === 'approved');

        // กรณีเลือกอนุมัติโดย ปลัด อบจ. (ปฏิบัติราชการแทนนายก อบจ. ที่สำนักปลัด ขั้นที่ 5)
        if ($approvalType === 'clerk') {
            $status   = 'approved';
            $stepText = 'ปลัด อบจ. ลงนามอนุมัติแล้ว (ปฏิบัติราชการแทน)';

            // ดึงชื่อจริงของผู้บันทึก (session user)
            $recorderUser = $this->userModel->getUserWithDetails($userId);
            $receiverName = $recorderUser ? trim($recorderUser['prefix'] . $recorderUser['first_name'] . ' ' . $recorderUser['last_name']) : 'ผู้บันทึก ID: ' . $userId;

            $stepReceivers = json_decode($travel['step_receivers'] ?: '{}', true) ?: [];
            $stepReceivers[5] = [
                'user_id'    => $userId,
                'name'       => $receiverName,
                'updated_at' => date('Y-m-d H:i:s'),
                'note'       => 'ปลัด อบจ. ลงนามอนุมัติ (ปฏิบัติราชการแทนนายก อบจ.)' . (!empty($note) ? " - {$note}" : ""),
            ];

            $updateData = [
                'current_step'     => 5,
                'status'           => 'approved',
                'approver_title'   => 'ปลัด อบจ. (ปฏิบัติราชการแทนนายก อบจ.)',
                'current_receiver' => $receiverName,
                'step_receivers'   => json_encode($stepReceivers, JSON_UNESCAPED_UNICODE),
            ];
            $this->travelModel->update($id, $updateData);

            // อัปเดต route ถึงขั้นที่ 5
            $routes = $this->routeModel->where('travel_id', $id)->orderBy('step_order', 'ASC')->findAll();
            foreach ($routes as $r) {
                if ($r['step_order'] <= 5 && $r['status'] !== 'signed') {
                    $this->routeModel->update($r['id'], [
                        'status'         => 'signed',
                        'comment_preset' => 'ปลัด อบจ. ลงนามอนุมัติ (ปฏิบัติราชการแทนนายก อบจ.)',
                        'signed_at'      => $r['signed_at'] ?: date('Y-m-d H:i:s'),
                    ]);
                }
            }

            $logDetail = "ปลัด อบจ. ลงนามอนุมัติการเดินทางไปราชการเรียบร้อยแล้ว (ปฏิบัติราชการแทนนายก อบจ.) [ผู้รับ/ลงนาม: {$receiverName}]" . (!empty($note) ? " (หมายเหตุ: {$note})" : "");
            $this->logModel->log($id, $userId, 'APPROVE', $logDetail);

            $db->transComplete();
            if ($db->transStatus() === false) {
                return redirect()->back()->with('error', 'เกิดข้อผิดพลาดในการบันทึกข้อมูล');
            }

            return redirect()->to('/admin/travel/' . $id)->with('success', 'บันทึก: ปลัด อบจ. ลงนามอนุมัติเรียบร้อยแล้ว (ปฏิบัติราชการแทน)');
        }

        // กรณีเลือกอนุมัติโดย นายก อบจ. (ขั้นที่ 6 หรือ 7)
        if ($approvalType === 'president' || $newStep >= 7 || ($newStep == 6 && $status === 'approved')) {
            $status   = 'approved';
            $stepText = 'นายก อบจ. ลงนามอนุมัติเรียบร้อยแล้ว';

            // ดึงชื่อจริงของผู้บันทึก (session user)
            $recorderUser = $this->userModel->getUserWithDetails($userId);
            $receiverName = $recorderUser ? trim($recorderUser['prefix'] . $recorderUser['first_name'] . ' ' . $recorderUser['last_name']) : 'ผู้บันทึก ID: ' . $userId;

            $stepReceivers = json_decode($travel['step_receivers'] ?: '{}', true) ?: [];
            $stepReceivers[6] = [
                'user_id'    => $userId,
                'name'       => $receiverName,
                'updated_at' => date('Y-m-d H:i:s'),
                'note'       => 'นายก อบจ. ลงนามอนุมัติเรียบร้อยแล้ว' . (!empty($note) ? " - {$note}" : ""),
            ];

            $updateData = [
                'current_step'     => 6,
                'status'           => 'approved',
                'approver_title'   => 'นายก อบจ.',
                'current_receiver' => $receiverName,
                'step_receivers'   => json_encode($stepReceivers, JSON_UNESCAPED_UNICODE),
            ];
            $this->travelModel->update($id, $updateData);

            // อัปเดตทุก route ให้ signed
            $routes = $this->routeModel->where('travel_id', $id)->orderBy('step_order', 'ASC')->findAll();
            foreach ($routes as $r) {
                if ($r['status'] !== 'signed') {
                    $this->routeModel->update($r['id'], [
                        'status'         => 'signed',
                        'comment_preset' => 'นายก อบจ. ลงนามอนุมัติเรียบร้อยแล้ว',
                        'signed_at'      => $r['signed_at'] ?: date('Y-m-d H:i:s'),
                    ]);
                }
            }

            $logDetail = "นายก อบจ. ลงนามอนุมัติการเดินทางไปราชการเรียบร้อยแล้ว [ผู้รับ/ลงนาม: {$receiverName}]" . (!empty($note) ? " (หมายเหตุ: {$note})" : "");
            $this->logModel->log($id, $userId, 'APPROVE', $logDetail);

            $db->transComplete();
            if ($db->transStatus() === false) {
                return redirect()->back()->with('error', 'เกิดข้อผิดพลาดในการบันทึกข้อมูล');
            }

            return redirect()->to('/admin/travel/' . $id)->with('success', 'บันทึก: นายก อบจ. ลงนามอนุมัติเรียบร้อยแล้ว');
        }

        // ตรวจสอบเงื่อนไข: ในขั้นตอนที่ 2 โรงเรียนต้องแนบไฟล์เอกสารคำสั่ง/หนังสือเชิญก่อน
        // หากยังไม่ได้อัปโหลดไฟล์ จะไม่สามารถกดบันทึกก้าวข้ามไปยังขั้นตอนที่ 3 หรือขั้นตอนถัดไปได้
        if (empty($travel['file_path']) && ($newStep > 2 || in_array($approvalType, ['clerk', 'president'], true) || $status === 'approved')) {
            return redirect()->back()->with('error', 'ไม่สามารถก้าวไปขั้นตอนต่อไปได้! กรุณาอัปโหลดไฟล์เอกสารคำสั่ง / หนังสือเชิญ (PDF) ในขั้นตอนที่ 2 ให้เรียบร้อยก่อนส่งเรื่องต่อไปยังกองการศึกษา');
        }

        // กรณีการเดินเอกสารขั้นตอนปกติ (ยังไม่อนุมัติ)
        // ป้องกันการข้ามขั้นตอน: เลือกได้เฉพาะขั้นตอนถัดไป (+1) หรือขั้นตอนเดิม
        if (!$isApp && $newStep > $curStep + 1) {
            return redirect()->back()->with('error', "ไม่สามารถข้ามขั้นตอนได้! ขณะนี้เอกสารอยู่ที่ขั้นที่ {$curStep} สามารถเลือกได้เฉพาะขั้นตอนถัดไป (ขั้นที่ " . ($curStep + 1) . ") เท่านั้น");
        }
        if (!$isApp && $newStep < $curStep && $status !== 'returned') {
            return redirect()->back()->with('error', "ไม่สามารถย้อนขั้นตอนเอกสารได้ หากต้องการส่งกลับ กรุณาเลือกสถานะ 'ส่งคืนเอกสารให้แก้ไข'");
        }

        $stepText = $stepTitles[$newStep] ?? "ขั้นตอนที่ {$newStep}";

        // ดึงชื่อจริงของเจ้าหน้าที่ที่กดบันทึก (session user)
        $recorderUser = $this->userModel->getUserWithDetails($userId);
        $receiverName = $recorderUser ? trim($recorderUser['prefix'] . $recorderUser['first_name'] . ' ' . $recorderUser['last_name']) : 'ผู้บันทึก ID: ' . $userId;

        $stepReceivers = json_decode($travel['step_receivers'] ?: '{}', true) ?: [];
        $stepReceivers[$newStep] = [
            'user_id'    => $userId,
            'name'       => $receiverName,
            'updated_at' => date('Y-m-d H:i:s'),
            'note'       => $note,
        ];

        $updateData = [
            'current_step'     => min(6, max(1, $newStep)),
            'status'           => $status,
            'current_receiver' => $receiverName,
            'step_receivers'   => json_encode($stepReceivers, JSON_UNESCAPED_UNICODE),
        ];
        if ($status === 'returned') {
            $updateData['status'] = 'returned';
        }

        $this->travelModel->update($id, $updateData);

        // Update travel_routes status
        $routes = $this->routeModel->where('travel_id', $id)->orderBy('step_order', 'ASC')->findAll();
        foreach ($routes as $r) {
            if ($r['step_order'] < $newStep) {
                if ($r['status'] !== 'signed') {
                    $this->routeModel->update($r['id'], [
                        'status'         => 'signed',
                        'comment_preset' => 'ผ่านการพิจารณาเรียบร้อยแล้ว',
                        'signed_at'      => $r['signed_at'] ?: date('Y-m-d H:i:s'),
                    ]);
                }
            } elseif ($r['step_order'] == $newStep) {
                if ($status === 'returned') {
                    $this->routeModel->update($r['id'], [
                        'status'       => 'returned',
                        'comment_text' => $note,
                        'signed_at'    => date('Y-m-d H:i:s'),
                    ]);
                } else {
                    $this->routeModel->update($r['id'], [
                        'status' => 'pending',
                    ]);
                }
            } else {
                $this->routeModel->update($r['id'], [
                    'status' => 'pending',
                ]);
            }
        }

        // Log audit trail
        $actionName = ($status === 'returned') ? 'RETURN' : 'TRACK';
        $logDetail = "เจ้าหน้าที่อัปเดตติดตามเอกสาร: {$stepText} [ผู้รับเอกสาร: {$receiverName}]" . (!empty($note) ? " (หมายเหตุ: {$note})" : "");
        $this->logModel->log($id, $userId, $actionName, $logDetail);

        $db->transComplete();
        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'เกิดข้อผิดพลาดในการบันทึกข้อมูล');
        }

        return redirect()->to('/admin/travel/' . $id)->with('success', 'บันทึกอัปเดตขั้นตอนเอกสาร: ' . $stepText . ' เรียบร้อยแล้ว');
    }

    // รายงานผลหลังกลับจากราชการ (อ้างอิงจากคำขออนุมัติไปราชการต้นเรื่อง)
    public function report($id)
    {
        $userId      = session()->get('user_id');
        $sessionRole = session()->get('role');
        $isOfficer   = in_array($sessionRole, ['super_admin', 'admin', 'saraban', 'executive'], true);

        $travel = $this->travelModel->find($id);

        if (!$travel) {
            return redirect()->to('/admin/travel')->with('error', 'ไม่พบข้อมูลคำขอไปราชการ');
        }

        // อนุญาตให้เฉพาะผู้ยื่นคำขอ หรือเจ้าหน้าที่สารบรรณ/ธุรการเป็นผู้รายงานผล
        if (!$isOfficer && (int)$travel['user_id'] !== (int)$userId) {
            return redirect()->to('/admin/travel/' . $id)->with('error', 'คุณไม่มีสิทธิ์รายงานผลสำหรับคำขอนี้');
        }

        // ต้องได้รับอนุมัติแล้วเท่านั้นจึงจะรายงานผลได้
        if (!in_array($travel['status'], ['approved', 'reported'], true)) {
            return redirect()->to('/admin/travel/' . $id)->with('error', 'คำขอไปราชการนี้ยังไม่ได้รับการอนุมัติ จึงยังไม่สามารถส่งรายงานผลได้');
        }

        $reportDetail = trim($this->request->getPost('report_detail') ?: '');
        if (empty($reportDetail)) {
            return redirect()->to('/admin/travel/' . $id . '#reportSection')->with('error', 'กรุณาระบุรายละเอียดหรือสรุปผลการปฏิบัติราชการ');
        }

        $file = $this->request->getFile('report_file');
        $reportFilePath = $travel['report_file'] ?? '';

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $ext = strtolower($file->getClientExtension());
            if ($ext !== 'pdf') {
                return redirect()->to('/admin/travel/' . $id . '#reportSection')->with('error', 'ระบบรองรับเฉพาะไฟล์เอกสารรายงานหรือเกียรติบัตรประเภท PDF (.pdf) เท่านั้น');
            }

            // ส่งไฟล์ไปยังเซิร์ฟเวอร์หลัก (Remote Upload Server) หรือ Fallback ลง Local
            $uploadResult = $this->_uploadFileToServer($file, 'saraban/travel_reports', $file->getClientName());

            if ($uploadResult['status'] === 'success') {
                // ลบไฟล์เก่าบน Remote Server หากเคยมี
                if (!empty($travel['report_file']) && str_starts_with($travel['report_file'], 'saraban/')) {
                    $this->_deleteFileFromServer($travel['report_file']);
                }
                $reportFilePath = 'saraban/travel_reports/' . $uploadResult['filename'];
            } else {
                // Fallback กรณีเซิร์ฟเวอร์หลักไม่ตอบสนอง จัดเก็บลงโฟลเดอร์ Local
                $newName = $file->getRandomName();
                $file->move(FCPATH . 'uploads/documents', $newName);
                $reportFilePath = 'uploads/documents/' . $newName;
            }
        }

        $isFirstReport = empty($travel['reported_at']) || $travel['status'] !== 'reported';

        $this->travelModel->update($id, [
            'status'        => 'reported',
            'report_detail' => $reportDetail,
            'report_file'   => $reportFilePath,
            'reported_at'   => $travel['reported_at'] ?: date('Y-m-d H:i:s'),
        ]);

        $logMsg = $isFirstReport
            ? 'ส่งรายงานผลการไปราชการเรียบร้อยแล้ว (อ้างถึงหนังสือขออนุมัติเลขที่ ' . $travel['doc_no'] . ')'
            : 'แก้ไขปรับปรุงข้อมูลรายงานผลการไปราชการ (อ้างถึงหนังสือขออนุมัติเลขที่ ' . $travel['doc_no'] . ')';

        $this->logModel->log($id, $userId, 'REPORT', $logMsg);

        $successMsg = 'บันทึกรายงานผลการไปราชการเรียบร้อยแล้ว (อ้างถึงคำขอเลขที่ ' . $travel['doc_no'] . ')';
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status'   => 'success',
                'message'  => $successMsg,
                'redirect' => base_url('admin/travel/' . $id . '#reportSection'),
            ]);
        }

        return redirect()->to('/admin/travel/' . $id . '#reportSection')->with('success', $successMsg);
    }

    // อัปโหลดหรือเปลี่ยนไฟล์แนบในหน้ารายละเอียดคำขอ (ส่งไปยัง Remote Upload Server แบบ teacher2025)
    public function uploadFile($id)
    {
        $userId      = session()->get('user_id');
        $sessionRole = session()->get('role');
        $isOfficer   = in_array($sessionRole, ['super_admin', 'admin', 'saraban', 'executive'], true);

        if (!$isOfficer) {
            $msg = 'คุณไม่มีสิทธิ์ในการแนบหรือแก้ไขไฟล์เอกสารคำสั่ง (สงวนสิทธิ์เฉพาะเจ้าหน้าที่เท่านั้น)';
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => $msg])->setStatusCode(403);
            }
            return redirect()->to('/admin/travel/' . $id)->with('error', $msg);
        }

        $travel = $this->travelModel->find($id);

        if (!$travel) {
            $msg = 'ไม่พบข้อมูลคำขอ';
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => $msg])->setStatusCode(404);
            }
            return redirect()->to('/admin/travel')->with('error', $msg);
        }

        $file = $this->request->getFile('doc_file');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $ext = strtolower($file->getClientExtension());
            if ($ext !== 'pdf') {
                $err = 'ระบบรองรับเฉพาะไฟล์เอกสารรูปแบบ PDF (.pdf) เท่านั้น';
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['status' => 'error', 'message' => $err])->setStatusCode(400);
                }
                return redirect()->to('/admin/travel/' . $id)->with('error', $err);
            }

            $uploadResult = $this->_uploadFileToServer($file, 'saraban/travel', $file->getClientName());

            if ($uploadResult['status'] === 'success') {
                $filePath = 'saraban/travel/' . $uploadResult['filename'];

                // ลบไฟล์เก่าบน remote server หากมี
                if (!empty($travel['file_path']) && str_starts_with($travel['file_path'], 'saraban/')) {
                    $this->_deleteFileFromServer($travel['file_path']);
                }

                $this->travelModel->update($id, [
                    'file_path' => $filePath
                ]);

                $this->logModel->log($id, $userId, 'UPLOAD', 'อัปโหลด/เปลี่ยนไฟล์แนบเอกสารคำสั่งไปยังเซิร์ฟเวอร์หลัก (' . $file->getClientName() . ')');

                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'status'   => 'success',
                        'message'  => 'อัปโหลดไฟล์แนบเอกสารคำสั่ง (PDF) เรียบร้อยแล้ว',
                        'filePath' => $filePath,
                        'fileUrl'  => document_url($filePath),
                    ]);
                }

                return redirect()->to('/admin/travel/' . $id)->with('success', 'อัปโหลดไฟล์แนบเอกสารคำสั่ง (PDF) เรียบร้อยแล้ว');
            } else {
                $errMsg = 'เกิดข้อผิดพลาดในการอัปโหลดไฟล์: ' . ($uploadResult['message'] ?? 'ไม่ทราบสาเหตุ');
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['status' => 'error', 'message' => $errMsg])->setStatusCode(400);
                }
                return redirect()->to('/admin/travel/' . $id)->with('error', $errMsg);
            }
        }

        $uploadError = 'ไม่พบไฟล์ที่เลือก หรือไฟล์มีขนาดเกินกำหนด';
        if ($file && !$file->isValid()) {
            $uploadError = $file->getErrorString() . ' (' . $file->getError() . ')';
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่สามารถอัปโหลดไฟล์ได้: ' . $uploadError])->setStatusCode(400);
        }
        return redirect()->to('/admin/travel/' . $id)->with('error', 'ไม่สามารถอัปโหลดไฟล์ได้: ' . $uploadError);
    }

    /**
     * Helper function to upload a file to the remote server via upload.php (รองรับ Chunked Upload อัตโนมัติ)
     */
    private function _uploadFileToServer($file, $remotePath, $originalName)
    {
        $uploadUrl = env('upload.server.url') ?: 'https://skj.nsnpao.go.th/upload.php';
        $token     = env('upload.server.token') ?: 'Dekpiano2025!!';

        $filePath   = $file->getTempName();
        $fileSize   = filesize($filePath);
        $chunkLimit = 750 * 1024; // 750 KB ต่อชิ้น (ปลอดภัย ไม่เกิน 1 MB Nginx limit แน่นอน)

        // กรณีไฟล์ขนาดเล็กกว่า 750 KB ส่งแบบปกติได้ทันที
        if ($fileSize <= $chunkLimit) {
            try {
                $client = \Config\Services::curlrequest();
                $postData = [
                    'path' => $remotePath,
                    'file' => new \CURLFile($filePath, $file->getMimeType(), $originalName)
                ];
                $headers = ['X-Auth-Token' => $token];

                $response = $client->post($uploadUrl, [
                    'multipart'   => $postData,
                    'headers'     => $headers,
                    'http_errors' => false,
                    'timeout'     => 60,
                ]);

                $statusCode = $response->getStatusCode();
                $rawBody    = $response->getBody();
                $body       = json_decode($rawBody, true);

                if ($statusCode === 200 && isset($body['status']) && $body['status'] === 'success') {
                    return [
                        'status'   => 'success',
                        'filename' => $body['filename']
                    ];
                }

                $errorMessage = $body['message'] ?? ("Server Error (Status: {$statusCode}). " . substr(strip_tags($rawBody), 0, 150));
                log_message('error', "File upload failed: {$errorMessage} (Status: {$statusCode})");
                return ['status' => 'error', 'message' => $errorMessage];
            } catch (\Exception $e) {
                log_message('error', 'cURL Error during upload: ' . $e->getMessage());
                return ['status' => 'error', 'message' => $e->getMessage()];
            }
        }

        // กรณีไฟล์ใหญ่กว่า 750 KB: ทำการหั่นไฟล์ (Chunking) แล้วส่งทีละชิ้นไปประกอบร่างที่ server ปลายทาง
        $totalChunks = (int)ceil($fileSize / $chunkLimit);
        $fileHandle  = fopen($filePath, 'rb');
        if (!$fileHandle) {
            return ['status' => 'error', 'message' => 'ไม่สามารถเปิดอ่านไฟล์เพื่อทำการแบ่งส่วนข้อมูลได้'];
        }

        $uniqueDesiredFilename = pathinfo($originalName, PATHINFO_FILENAME) . '_' . uniqid() . '.' . pathinfo($originalName, PATHINFO_EXTENSION);
        $tempChunkPath = WRITEPATH . 'uploads/temp_chunk_' . uniqid() . '.tmp';

        try {
            $client = \Config\Services::curlrequest();
            $headers = ['X-Auth-Token' => $token];

            for ($chunkIndex = 0; $chunkIndex < $totalChunks; $chunkIndex++) {
                $chunkData = fread($fileHandle, $chunkLimit);
                file_put_contents($tempChunkPath, $chunkData);

                $postData = [
                    'path'             => $remotePath,
                    'chunk_index'      => (string)$chunkIndex,
                    'total_chunks'     => (string)$totalChunks,
                    'filename'         => $uniqueDesiredFilename,
                    'desired_filename' => $uniqueDesiredFilename,
                    'file'             => new \CURLFile($tempChunkPath, 'application/octet-stream', $uniqueDesiredFilename)
                ];

                $response = $client->post($uploadUrl, [
                    'multipart'   => $postData,
                    'headers'     => $headers,
                    'http_errors' => false,
                    'timeout'     => 60,
                ]);

                if (file_exists($tempChunkPath)) {
                    @unlink($tempChunkPath);
                }

                $statusCode = $response->getStatusCode();
                $rawBody    = $response->getBody();
                $body       = json_decode($rawBody, true);

                if ($statusCode !== 200) {
                    fclose($fileHandle);
                    $errMsg = $body['message'] ?? "การส่งส่วนที่ " . ($chunkIndex + 1) . "/{$totalChunks} ล้มเหลว (Status: {$statusCode})";
                    return ['status' => 'error', 'message' => $errMsg];
                }

                // Chunk สุดท้ายจะรวมไฟล์เสร็จและได้ status = success
                if ($chunkIndex === $totalChunks - 1) {
                    fclose($fileHandle);
                    if (isset($body['status']) && $body['status'] === 'success') {
                        return [
                            'status'   => 'success',
                            'filename' => $body['filename'] ?? $uniqueDesiredFilename
                        ];
                    }
                    return ['status' => 'error', 'message' => $body['message'] ?? 'รวมไฟล์ชิ้นส่วนที่เซิร์ฟเวอร์ปลายทางไม่สำเร็จ'];
                }
            }

            fclose($fileHandle);
            return ['status' => 'error', 'message' => 'เกิดข้อผิดพลาดในการประมวลผล Chunked Upload'];
        } catch (\Exception $e) {
            if (is_resource($fileHandle)) {
                fclose($fileHandle);
            }
            if (file_exists($tempChunkPath)) {
                @unlink($tempChunkPath);
            }
            log_message('error', 'cURL Chunk Upload Error: ' . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Helper function to delete a file from the remote server via delete.php
     */
    private function _deleteFileFromServer($remoteFilePath)
    {
        $deleteUrl = env('upload.server.delete.url') ?: 'https://skj.nsnpao.go.th/delete.php';
        $token     = env('upload.server.token') ?: 'Dekpiano2025!!';

        try {
            $client = \Config\Services::curlrequest();
            $pathParts = pathinfo($remoteFilePath);
            $remotePath = $pathParts['dirname'] ?? '';
            $filename   = $pathParts['basename'] ?? '';

            $postData = [
                'path'     => $remotePath,
                'filename' => $filename
            ];

            $headers = [
                'X-Auth-Token' => $token
            ];

            $client->post($deleteUrl, [
                'form_params' => $postData,
                'headers'     => $headers,
                'http_errors' => false,
                'timeout'     => 15,
            ]);
            return true;
        } catch (\Exception $e) {
            log_message('error', 'cURL Error during delete: ' . $e->getMessage());
            return false;
        }
    }

    // ตรวจสอบความซ้ำซ้อนของเลขที่หนังสือ (doc_no) และเรื่อง (title) ผ่าน AJAX
    public function checkDuplicate()
    {
        $docNo     = trim($this->request->getGet('doc_no') ?: '');
        $title     = trim($this->request->getGet('title') ?: '');
        $excludeId = (int)($this->request->getGet('exclude_id') ?: 0);

        $duplicateDocNo = false;
        $docNoMsg       = '';
        if (!empty($docNo)) {
            $builder = $this->travelModel->where('doc_no', $docNo);
            if ($excludeId > 0) {
                $builder->where('id !=', $excludeId);
            }
            $found = $builder->first();
            if ($found) {
                $duplicateDocNo = true;
                $docNoMsg       = "เลขที่หนังสือ '{$docNo}' มีอยู่ในระบบแล้ว (เรื่อง: " . esc($found['title']) . ")";
            }
        }

        $duplicateTitle = false;
        $titleMsg       = '';
        if (!empty($title)) {
            $builder = $this->travelModel->where('title', $title);
            if ($excludeId > 0) {
                $builder->where('id !=', $excludeId);
            }
            $found = $builder->first();
            if ($found) {
                $duplicateTitle = true;
                $titleMsg       = "เรื่องนี้มีบันทึกในระบบแล้ว (เลขที่: " . esc($found['doc_no']) . ")";
            }
        }

        $nextDocNo = $this->travelModel->getNextDocNumber();

        return $this->response->setJSON([
            'duplicate_doc_no' => $duplicateDocNo,
            'doc_no_msg'       => $docNoMsg,
            'duplicate_title'  => $duplicateTitle,
            'title_msg'        => $titleMsg,
            'next_doc_no'      => $nextDocNo,
        ]);
    }

    // ลบคำขอไปราชการ พร้อมลบไฟล์เอกสารคำสั่ง และรายงานผล ทั้งบนเซิร์ฟเวอร์และในเครื่อง
    public function delete($id)
    {
        $userId   = session()->get('user_id');
        $userRole = session()->get('role');

        $travel = $this->travelModel->find($id);
        if (!$travel) {
            return redirect()->to('/admin/travel')->with('error', 'ไม่พบข้อมูลคำขอไปราชการที่ต้องการลบ');
        }

        // ตรวจสอบสิทธิ์: Super Admin, Admin, Saraban หรือเจ้าของคำขอ (เฉพาะเมื่อยังไม่อนุมัติ)
        $isOfficer = in_array($userRole, ['super_admin', 'admin', 'saraban', 'executive'], true);
        $isOwner   = ((int)$travel['user_id'] === (int)$userId);

        if (!$isOfficer && !$isOwner) {
            return redirect()->to('/admin/travel/' . $id)->with('error', 'ท่านไม่มีสิทธิ์ในการลบคำขอนี้');
        }

        // หากเป็นเจ้าของเรื่องแต่ไม่ได้เป็นเจ้าหน้าที่ อนุญาตให้ลบได้เฉพาะสถานะ pending/returned เท่านั้น
        if (!$isOfficer && $travel['status'] === 'approved') {
            return redirect()->to('/admin/travel/' . $id)->with('error', 'คำขอที่ผ่านการอนุมัติเรียบร้อยแล้ว ไม่สามารถลบได้');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // 1. ลบไฟล์เอกสารคำสั่ง (file_path)
            if (!empty($travel['file_path'])) {
                // ลบบน Remote Server (skj.nsnpao.go.th)
                $this->_deleteFileFromServer($travel['file_path']);

                // ลบในเครื่อง (Local Storage เผื่อมีบันทึกสำรอง)
                $localPath = WRITEPATH . 'uploads/' . $travel['file_path'];
                if (file_exists($localPath) && is_file($localPath)) {
                    @unlink($localPath);
                }
            }

            // 2. ลบไฟล์รายงานผล (report_file) ถ้ามี
            if (!empty($travel['report_file'])) {
                $this->_deleteFileFromServer($travel['report_file']);
                $localReportPath = WRITEPATH . 'uploads/' . $travel['report_file'];
                if (file_exists($localReportPath) && is_file($localReportPath)) {
                    @unlink($localReportPath);
                }
            }

            // 3. ลบข้อมูลที่เกี่ยวข้องในฐานข้อมูล (Logs, Routes, และ Travel Request)
            $this->logModel->where('travel_id', $id)->delete();
            $this->routeModel->where('travel_id', $id)->delete();
            $this->travelModel->delete($id);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->to('/admin/travel/' . $id)->with('error', 'เกิดข้อผิดพลาดในการลบข้อมูลคำขอจากฐานข้อมูล');
            }

            // นำทางกลับตามสิทธิ์
            $redirectUrl = $isOfficer ? '/admin/travel' : '/admin/travel/my';
            return redirect()->to($redirectUrl)->with('success', 'ลบคำขอไปราชการ เลขที่ ' . esc($travel['doc_no']) . ' และไฟล์แนบทั้งหมดเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Error deleting travel request ' . $id . ': ' . $e->getMessage());
            return redirect()->to('/admin/travel/' . $id)->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }
}

