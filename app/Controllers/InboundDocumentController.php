<?php

namespace App\Controllers;

use App\Models\DocumentModel;
use App\Models\DocumentRouteModel;
use App\Models\DocumentLogModel;
use App\Models\UserModel;
use App\Models\DepartmentModel;

class InboundDocumentController extends BaseController
{
    protected $documentModel;
    protected $routeModel;
    protected $logModel;
    protected $userModel;
    protected $deptModel;

    public function __construct()
    {
        $this->documentModel = new DocumentModel();
        $this->routeModel    = new DocumentRouteModel();
        $this->logModel      = new DocumentLogModel();
        $this->userModel     = new UserModel();
        $this->deptModel     = new DepartmentModel();
        helper(['thai_date', 'form', 'url']);
    }

    /**
     * ทะเบียนหนังสือรับภายนอก (Inbound Document Register)
     */
    public function index()
    {
        $search    = trim((string)$this->request->getGet('search'));
        $urgency   = $this->request->getGet('urgency');
        $status    = $this->request->getGet('status');
        $startDate = $this->request->getGet('start_date');
        $endDate   = $this->request->getGet('end_date');

        $query = $this->documentModel->select('documents.*, 
                    creator.first_name as creator_first, creator.last_name as creator_last,
                    assigned.first_name as assigned_first, assigned.last_name as assigned_last, assigned.prefix as assigned_prefix')
                ->join('users as creator', 'creator.id = documents.created_by', 'left')
                ->join('users as assigned', 'assigned.id = documents.assigned_to_user_id', 'left')
                ->where('documents.doc_type', 'in');

        if (!empty($search)) {
            $query->groupStart()
                  ->like('documents.doc_no', $search)
                  ->orLike('documents.internal_reg_no', $search)
                  ->orLike('documents.title', $search)
                  ->orLike('documents.from_org', $search)
                  ->orLike('documents.to_org', $search)
                  ->orLike('documents.executive_instruction', $search)
                  ->groupEnd();
        }

        if (!empty($urgency)) {
            $query->where('documents.urgency', $urgency);
        }

        if (!empty($status)) {
            $query->where('documents.status', $status);
        }

        if (!empty($startDate)) {
            $query->where('documents.receive_date >=', $startDate);
        }
        if (!empty($endDate)) {
            $query->where('documents.receive_date <=', $endDate);
        }

        $documents = $query->orderBy('documents.receive_date', 'DESC')
                           ->orderBy('documents.id', 'DESC')
                           ->findAll();

        return view('documents/inbound_index', [
            'documents' => $documents,
            'search'    => $search,
            'urgency'   => $urgency,
            'status'    => $status,
            'startDate' => $startDate,
            'endDate'   => $endDate,
        ]);
    }

    /**
     * หน้าฟอร์มลงทะเบียนรับหนังสือเข้าใหม่
     */
    public function create()
    {
        $userRole = (string)(session()->get('role') ?? 'teacher');
        if (!in_array($userRole, ['super_admin', 'admin', 'saraban'], true)) {
            return redirect()->to(base_url('admin/inbound'))->with('error', 'เฉพาะเจ้าหน้าที่สารบรรณหรือผู้ดูแลระบบเท่านั้นที่มีสิทธิ์ลงทะเบียนรับหนังสือเข้า');
        }

        $suggestedRegNo  = $this->documentModel->getNextInternalNumber('in');
        $defaultDirector = $this->userModel->getDefaultDirector();
        $personnelList   = $this->userModel->getPersonnelForSelection();
        $departments     = $this->deptModel->findAll();

        $userId = session()->get('user_id');
        $currentUser = $this->userModel->getUserWithDetails($userId);
        $currentReceiverName = !empty($currentUser)
            ? trim(($currentUser['prefix'] ?? '') . ($currentUser['first_name'] ?? '') . ' ' . ($currentUser['last_name'] ?? ''))
            : 'เจ้าหน้าที่สารบรรณ';

        return view('documents/inbound_create', [
            'suggestedRegNo'      => $suggestedRegNo,
            'defaultDirector'     => $defaultDirector,
            'personnelList'       => $personnelList,
            'departments'         => $departments,
            'currentUser'         => $currentUser,
            'currentReceiverName' => $currentReceiverName,
        ]);
    }

    /**
     * บันทึกการลงทะเบียนหนังสือรับเข้า
     */
    public function store()
    {
        $userRole = (string)(session()->get('role') ?? 'teacher');
        if (!in_array($userRole, ['super_admin', 'admin', 'saraban'], true)) {
            return redirect()->to(base_url('admin/inbound'))->with('error', 'คุณไม่มีสิทธิ์ลงทะเบียนหนังสือรับเข้า');
        }

        $userId = (int)session()->get('user_id');

        $rules = [
            'doc_no'   => 'required|max_length[100]',
            'title'    => 'required|min_length[3]|max_length[255]',
            'from_org' => 'required|max_length[200]',
            'to_org'   => 'required|max_length[200]',
            'doc_date' => 'required',
        ];

        if (!$this->validate($rules)) {
            $errors = implode(', ', $this->validator->getErrors());
            return redirect()->back()->withInput()->with('error', 'กรุณากรอกข้อมูลให้ครบถ้วน: ' . $errors);
        }

        $docNo         = trim($this->request->getPost('doc_no'));
        $internalRegNo = trim($this->request->getPost('internal_reg_no'));
        if (empty($internalRegNo)) {
            $internalRegNo = $this->documentModel->getNextInternalNumber('in');
        }

        $docDate = $this->request->getPost('doc_date') ?: date('Y-m-d');
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $docDate, $m) && (int)$m[1] > 2400) {
            $docDate = ((int)$m[1] - 543) . '-' . $m[2] . '-' . $m[3];
        }

        $receiveDate = $this->request->getPost('receive_date') ?: date('Y-m-d');
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $receiveDate, $m) && (int)$m[1] > 2400) {
            $receiveDate = ((int)$m[1] - 543) . '-' . $m[2] . '-' . $m[3];
        }

        $docYear     = (int)date('Y', strtotime($receiveDate)) + 543;
        $title       = trim($this->request->getPost('title'));
        $urgency     = $this->request->getPost('urgency') ?: 'normal';
        $secretLevel = $this->request->getPost('secret_level') ?: 'normal';
        $fromOrg     = trim($this->request->getPost('from_org'));
        $toOrg       = trim($this->request->getPost('to_org'));
        $receiveTime = trim($this->request->getPost('receive_stamp_time')) ?: (date('H:i') . ' น.');

        // จัดการอัปโหลดไฟล์สแกน PDF หรือรูปภาพ
        $file = $this->request->getFile('doc_file');
        $filePath = null;

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $ext = strtolower($file->getExtension());
            if (!in_array($ext, ['pdf', 'png', 'jpg', 'jpeg', 'webp'], true)) {
                return redirect()->back()->withInput()->with('error', 'รองรับเฉพาะไฟล์เอกสาร PDF หรือรูปภาพ (JPG, PNG, WEBP) เท่านั้น');
            }
            $uniqueId = uniqid() . '_' . time();
            $newName = 'inbound_' . $uniqueId . '.' . $ext;
            $rawName = 'raw_inbound_' . $uniqueId . '.' . $ext;
            $uploadDir = FCPATH . 'uploads/documents';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $file->move($uploadDir, $newName);
            $filePath = 'uploads/documents/' . $newName;

            // สำรองไฟล์ต้นฉบับสะอาดไว้ (สำหรับให้ จนท. ขยับหรือสแตมป์ใหม่ได้ก่อน ผอ. เกษียน)
            $fullDiskPath = $uploadDir . DIRECTORY_SEPARATOR . $newName;
            $fullRawPath  = $uploadDir . DIRECTORY_SEPARATOR . $rawName;
            @copy($fullDiskPath, $fullRawPath);
            $rawFilePath = 'uploads/documents/' . $rawName;

            // ตรวจสอบว่าเปิดโหมด "มีตรายางรับ" หรือไม่
            $hasStamp = $this->request->getPost('has_receive_stamp') !== null ? (int)$this->request->getPost('has_receive_stamp') : 1;
            if ($hasStamp === 1) {
                // ประทับตราเลขรับกลืนลงไปในเนื้อไฟล์จริงถาวร (Permanent Stamp Imprint)
                $stampData = [
                    'reg_no'        => to_thai_number($internalRegNo),
                    'doc_date'      => to_thai_number(thai_date($receiveDate, 'medium')),
                    'doc_time'      => to_thai_number($receiveTime),
                    'receiver_name' => session()->get('name') ?: 'เจ้าหน้าที่สารบรรณ',
                ];
                $coords = [
                    'x'     => $this->request->getPost('receive_stamp_x') ?: '72.2%',
                    'y'     => $this->request->getPost('receive_stamp_y') ?: '3.5%',
                    'scale' => (float)($this->request->getPost('receive_stamp_scale') ?: 1.0),
                    'w_pct' => $this->request->getPost('receive_stamp_w_pct') ?: '23.8%',
                ];

                $stampService = new \App\Services\DocumentStampService();
                $imprinted = $stampService->imprintReceiveStamp($fullDiskPath, $stampData, $coords);
                if ($imprinted && file_exists($imprinted)) {
                    $relPath = str_replace(rtrim(FCPATH, '/\\'), '', $imprinted);
                    $filePath = ltrim(str_replace('\\', '/', $relPath), '/');
                    log_message('info', 'Document receive stamp permanently imprinted on file: ' . $imprinted);
                }
            }
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $docData = [
            'doc_type'            => 'in',
            'doc_no'              => $docNo,
            'internal_reg_no'     => $internalRegNo,
            'year'                => $docYear,
            'title'               => $title,
            'urgency'             => $urgency,
            'secret_level'        => $secretLevel,
            'from_org'            => $fromOrg,
            'to_org'              => $toOrg,
            'doc_date'            => $docDate,
            'receive_date'        => $receiveDate,
            'file_path'           => $filePath,
            'raw_file_path'       => $rawFilePath ?? null,
            'status'              => 'routing',
            'current_step'        => 1,
            'has_receive_stamp'   => $this->request->getPost('has_receive_stamp') !== null ? (int)$this->request->getPost('has_receive_stamp') : 1,
            'receive_stamp_x'     => $this->request->getPost('receive_stamp_x') ?: '72.2%',
            'receive_stamp_y'     => $this->request->getPost('receive_stamp_y') ?: '3.5%',
            'receive_stamp_scale' => (float)($this->request->getPost('receive_stamp_scale') ?: 1.0),
            'receive_stamp_time'  => $receiveTime,
            'received_by'         => $userId,
            'received_at'         => date('Y-m-d H:i:s'),
            'created_by'          => $userId,
        ];

        $docId = $this->documentModel->insert($docData);

        // จัดการสายการเดินแฟ้มเสนอเกษียน (รอง ผอ. กลั่นกรอง -> ผอ. สั่งการ)
        $routePersonnel = $this->request->getPost('route_personnel');
        if (!empty($routePersonnel) && is_array($routePersonnel)) {
            $step = 1;
            foreach ($routePersonnel as $persId) {
                if (empty($persId)) continue;
                $user = $this->userModel->where('pers_id', $persId)->first();
                $personnelDetails = $this->userModel->getPersonnelDetails($persId);

                $approverName = !empty($personnelDetails['full_name']) 
                    ? $personnelDetails['full_name'] 
                    : (!empty($personnelDetails['pers_firstname']) 
                        ? trim(($personnelDetails['pers_prefix'] ?? '') . $personnelDetails['pers_firstname'] . ' ' . ($personnelDetails['pers_lastname'] ?? ''))
                        : ($user ? trim(($user['prefix'] ?? '') . ($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) : 'ผู้บริหาร'));

                $posName = !empty($personnelDetails['position_name']) ? $personnelDetails['position_name'] : ($user['position_name'] ?? 'รองผู้อำนวยการ/ผู้อำนวยการ');
                $userIdVal = !empty($personnelDetails['user_id']) ? (int)$personnelDetails['user_id'] : ($user['id'] ?? null);

                $this->routeModel->insert([
                    'document_id'        => (int)$docId,
                    'step_order'         => $step++,
                    'user_id'            => $userIdVal,
                    'pers_id'            => $persId,
                    'approver_name'      => $approverName,
                    'personnel_position' => $posName,
                    'status'             => 'pending',
                ]);
            }
        } else {
            // เส้นทางมาตรฐาน: ส่งตรงถึง ผู้อำนวยการโรงเรียน (pers_060)
            $defaultDirector = $this->userModel->getDefaultDirector();
            $directorUser    = $this->userModel->where('pers_id', $defaultDirector['pers_id'])->first();

            $this->routeModel->insert([
                'document_id'        => (int)$docId,
                'step_order'         => 1,
                'user_id'            => $directorUser['id'] ?? null,
                'pers_id'            => $defaultDirector['pers_id'],
                'approver_name'      => $defaultDirector['full_name'],
                'personnel_position' => $defaultDirector['position_name'],
                'status'             => 'pending',
            ]);
        }

        // Audit Trail Log
        $this->logModel->log($docId, $userId, 'RECEIVE_INBOUND', "ลงทะเบียนหนังสือรับเข้า เลขทะเบียน {$internalRegNo} (จาก: {$fromOrg}) และส่งเข้าสายเกษียน");

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'เกิดข้อผิดพลาดในการลงทะเบียนหนังสือรับ');
        }

        return redirect()->to(base_url('admin/inbound/' . $docId))->with('success', "ลงทะเบียนหนังสือรับเข้าเลขที่ {$internalRegNo} เรียบร้อยแล้ว");
    }

    /**
     * หน้ารายละเอียดหนังสือรับเข้า & ตรวจดูเอกสาร & เกษียนสั่งการ
     */
    public function view($id)
    {
        $userId   = session()->get('user_id');
        $document = $this->documentModel->select('documents.*, 
                        creator.first_name as creator_first, creator.last_name as creator_last,
                        assigned.first_name as assigned_first, assigned.last_name as assigned_last, assigned.prefix as assigned_prefix,
                        assigned_dept.name as assigned_dept_name')
                    ->join('users as creator', 'creator.id = documents.created_by', 'left')
                    ->join('users as assigned', 'assigned.id = documents.assigned_to_user_id', 'left')
                    ->join('departments as assigned_dept', 'assigned_dept.id = assigned.department_id', 'left')
                    ->where('documents.id', $id)
                    ->where('documents.doc_type', 'in')
                    ->first();

        if (!$document) {
            return redirect()->to(base_url('admin/inbound'))->with('error', 'ไม่พบข้อมูลหนังสือรับเข้า');
        }

        $routes = $this->routeModel->getRoutesForDocument($id);
        $logs   = $this->logModel->getLogsForDocument($id);

        // ตรวจสอบสิทธิ์การเกษียน/ลงนามในขั้นตอนนี้
        // เจ้าหน้าที่สารบรรณ, ครู, และผู้ใช้ทั่วไป จะอยู่ในโหมด "ดูได้อย่างเดียว" (View Only)
        // เฉพาะผู้บริหารที่ระบุในเส้นทาง หรือผู้มีบทบาท executive / pers_060 เท่านั้นที่ลงนามได้
        $canSign = false;
        $currentRoute = null;
        $persId = (string)(session()->get('pers_id') ?? '');
        $role   = (string)(session()->get('role') ?? '');
        $isExecutive = ($role === 'executive') || ($persId === 'pers_060');

        foreach ($routes as $route) {
            $isMatch = (!empty($route['user_id']) && (int)$route['user_id'] === (int)$userId)
                || (!empty($persId) && (string)($route['pers_id'] ?? '') === $persId)
                || ($isExecutive && (
                    ($route['pers_id'] ?? '') === 'pers_060'
                    || str_contains($route['personnel_position'] ?? '', 'ผู้อำนวยการ')
                ));

            if ($route['step_order'] == $document['current_step'] 
                && $isMatch
                && $route['status'] === 'pending' 
                && $document['status'] === 'routing') {
                $canSign = true;
                $currentRoute = $route;
                break;
            }
        }

        // รายชื่อบุคลากรและกลุ่มสาระสำหรับ ผอ. เลือกมอบหมายงาน
        $personnelList = $this->userModel->getPersonnelForSelection();
        $departments   = $this->deptModel->findAll();
        $currentUser   = $this->userModel->getUserWithDetails($userId);

        return view('documents/inbound_view', [
            'document'      => $document,
            'routes'        => $routes,
            'logs'          => $logs,
            'canSign'       => $canSign,
            'currentRoute'  => $currentRoute,
            'personnelList' => $personnelList,
            'departments'   => $departments,
            'currentUser'   => $currentUser,
        ]);
    }

    /**
     * ผู้บริหาร (รอง ผอ. / ผอ.) เกษียนสั่งการ และเลือกมอบหมายงาน
     */
    public function endorse($id)
    {
        $userId   = (int)session()->get('user_id');
        $document = $this->documentModel->where('doc_type', 'in')->find($id);

        if (!$document) {
            return redirect()->to(base_url('admin/inbound'))->with('error', 'ไม่พบเอกสาร');
        }

        if ($document['status'] !== 'routing') {
            return redirect()->back()->with('error', 'เอกสารนี้ไม่ได้อยู่ในสถานะรอเกษียนสั่งการ');
        }

        $persId = (string)(session()->get('pers_id') ?? '');
        $role   = (string)(session()->get('role') ?? '');
        $isExecutive = ($role === 'executive') || ($persId === 'pers_060');

        $routeBuilder = $this->routeModel->where('document_id', $id)
                                         ->where('step_order', $document['current_step'])
                                         ->where('status', 'pending');
        $routeBuilder->groupStart();
        if (!empty($userId)) {
            $routeBuilder->where('user_id', $userId);
        }
        if (!empty($persId)) {
            $routeBuilder->orWhere('pers_id', $persId);
        }
        if ($isExecutive) {
            $routeBuilder->orWhere('pers_id', 'pers_060')
                         ->orLike('personnel_position', 'ผู้อำนวยการ');
        }
        $routeBuilder->groupEnd();

        $route = $routeBuilder->first();

        if (!$route) {
            return redirect()->back()->with('error', 'คุณไม่มีสิทธิ์ลงนามในขั้นตอนนี้ หรือเอกสารไม่ได้อยู่ในสถานะรอลงนาม');
        }

        $commentPreset = $this->request->getPost('comment_preset');
        $commentText   = trim((string)$this->request->getPost('comment_text'));
        $signatureType = $this->request->getPost('signature_type') ?: 'preset';
        $signatureData = $this->request->getPost('signature_data');

        // ฟิลด์การมอบหมายงาน (สำหรับการสั่งการของ ผอ.)
        $assignedUserId = $this->request->getPost('assigned_to_user_id') ? (int)$this->request->getPost('assigned_to_user_id') : null;
        $assignedDept   = trim((string)$this->request->getPost('assigned_department'));

        $db = \Config\Database::connect();
        $db->transStart();

        // บันทึกลายเซ็นต์
        $signaturePath = '';
        if ($signatureType === 'draw' && !empty($signatureData)) {
            $data = explode(',', $signatureData);
            $img = base64_decode(end($data));
            $filename = 'sign_inbound_' . $id . '_step_' . $route['step_order'] . '_' . time() . '.png';
            $path = FCPATH . 'uploads/signatures/' . $filename;
            if (!is_dir(FCPATH . 'uploads/signatures')) {
                mkdir(FCPATH . 'uploads/signatures', 0777, true);
            }
            file_put_contents($path, $img);
            $signaturePath = 'uploads/signatures/' . $filename;
        } else {
            $user = $this->userModel->getUserWithDetails($userId);
            $signaturePath = $user['signature_img'] ?? '';
            if (empty($signaturePath)) {
                $defaultDirector = $this->userModel->getDefaultDirector();
                $signaturePath = $defaultDirector['signature_img'] ?? '';
            }
        }

        $verifyString = $id . '|' . $userId . '|' . date('Y-m-d H:i:s') . '|' . $this->request->getIPAddress();
        $verifyHash   = hash('sha256', $verifyString);

        $this->routeModel->update($route['id'], [
            'user_id'        => $userId,
            'status'         => 'signed',
            'comment_preset' => $commentPreset,
            'comment_text'   => $commentText,
            'signature_img'  => $signaturePath,
            'signature_type' => $signatureType,
            'signed_at'      => date('Y-m-d H:i:s'),
            'ip_address'     => $this->request->getIPAddress(),
            'verify_hash'    => $verifyHash,
        ]);

        // ตรวจสอบขั้นตอนถัดไป
        $nextStep = $this->routeModel->where('document_id', $id)
                                     ->where('step_order', $document['current_step'] + 1)
                                     ->first();

        $userProfile   = $this->userModel->getUserWithDetails($userId);
        $isFinalSigner = (!empty($userProfile['can_sign_final']) && $userProfile['can_sign_final'] == 1)
                         || str_contains($route['personnel_position'] ?? '', 'ผู้อำนวยการ');

        $docUpdate = [];

        if (!empty($assignedUserId)) {
            $docUpdate['assigned_to_user_id'] = $assignedUserId;
        }
        if (!empty($assignedDept)) {
            $docUpdate['assigned_department'] = $assignedDept;
        }
        if (!empty($commentText) || !empty($commentPreset)) {
            $instruction = trim($commentPreset . ($commentText ? ' : ' . $commentText : ''));
            $docUpdate['executive_instruction'] = $instruction;
        }

        if ($nextStep && !$isFinalSigner) {
            $docUpdate['current_step'] = $document['current_step'] + 1;
            $this->documentModel->update($id, $docUpdate);
            $this->logModel->log($id, $userId, 'ENDORSE_STEP', "เกษียนกลั่นกรองหนังสือรับเข้า (ขั้นที่ {$route['step_order']}) และส่งต่อ ผอ. สั่งการ");
            $msg = 'เกษียนกลั่นกรองหนังสือรับเข้าเรียบร้อยแล้ว ส่งต่อเสนอ ผู้อำนวยการสั่งการ';
        } else {
            $docUpdate['status'] = 'completed';
            $docUpdate['dispatch_status'] = !empty($assignedUserId) || !empty($assignedDept) ? 'dispatched' : 'pending';

            // ลบไฟล์สำรอง (raw_file_path) ออก เพื่อประหยัดพื้นที่จัดเก็บบนดิสก์ เมื่อ ผอ. เกษียนอนุมัติเสร็จสิ้นแล้ว
            if (!empty($document['raw_file_path'])) {
                $rawDiskPath = FCPATH . ltrim($document['raw_file_path'], '/\\');
                if (file_exists($rawDiskPath)) {
                    @unlink($rawDiskPath);
                }
                $docUpdate['raw_file_path'] = null;
            }

            $this->documentModel->update($id, $docUpdate);

            $logDetail = "ผู้อำนวยการเกษียนสั่งการสำเร็จสมบูรณ์ (ลบไฟล์สำรองต้นฉบับเพื่อคืนพื้นที่จัดเก็บ)";
            if (!empty($assignedDept)) $logDetail .= " (มอบหมาย: {$assignedDept})";
            if (!empty($assignedUserId)) {
                $assignedUser = $this->userModel->find($assignedUserId);
                if ($assignedUser) $logDetail .= " (มอบหมายครู: {$assignedUser['first_name']} {$assignedUser['last_name']})";
            }

            $this->logModel->log($id, $userId, 'DIRECTOR_INSTRUCT', $logDetail);
            $msg = 'ผู้อำนวยการลงนามเกษียนสั่งการเรียบร้อยแล้ว ระบบได้กระจายเรื่องไปยังผู้รับผิดชอบเรียบร้อยแล้ว';
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'เกิดข้อผิดพลาดในการลงนามเกษียน');
        }

        return redirect()->to(base_url('admin/inbound/' . $id))->with('success', $msg);
    }

    /**
     * อัปเดตตำแหน่ง / ขนาดย่อ-ขยาย ของตรายางรับหนังสือ และประทับตราลงในเอกสารใหม่ (AJAX)
     */
    public function updateStamp($id)
    {
        $userId = session()->get('user_id');
        $role   = session()->get('role');
        if (!in_array($role, ['saraban', 'admin', 'super_admin'], true)) {
            return $this->response->setJSON(['success' => false, 'message' => 'คุณไม่มีสิทธิ์แก้ไขตรายางรับหนังสือ']);
        }

        $document = $this->documentModel->where('doc_type', 'in')->find($id);
        if (!$document) {
            return $this->response->setJSON(['success' => false, 'message' => 'ไม่พบข้อมูลเอกสาร']);
        }

        // หากผู้บริหารได้ลงนามเกษียนไปแล้ว หรือเอกสารเสร็จสมบูรณ์แล้ว ห้ามแก้ไขตรายาง
        $signedRoutesCount = $this->routeModel->where('document_id', $id)->where('status', 'signed')->countAllResults();
        if ($signedRoutesCount > 0 || $document['status'] === 'completed') {
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'เอกสารได้รับการเกษียนสั่งการจากผู้บริหารแล้ว จึงไม่สามารถแก้ไขตำแหน่งตรายางได้'
            ]);
        }

        // ตรวจสอบไฟล์สำรองดิบ (Clean Raw File)
        $rawFilePath = $document['raw_file_path'] ?? '';
        $rawDiskPath = !empty($rawFilePath) ? FCPATH . ltrim($rawFilePath, '/\\') : '';
        if (empty($rawDiskPath) || !file_exists($rawDiskPath)) {
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'ไม่พบไฟล์ต้นฉบับสำรอง (อาจถูกลบไปแล้วหลัง ผอ. สั่งการ) ไม่สามารถประทับตราใหม่ได้'
            ]);
        }

        $targetFilePath = $document['file_path'] ?? '';
        $targetDiskPath = !empty($targetFilePath) ? FCPATH . ltrim($targetFilePath, '/\\') : '';
        if (empty($targetDiskPath)) {
            return $this->response->setJSON(['success' => false, 'message' => 'ไม่พบไฟล์เอกสารปลายทาง']);
        }

        // คืนค่าไฟล์เป้าหมายจากไฟล์ต้นฉบับสะอาดก่อนสแตมป์ใหม่
        if (!@copy($rawDiskPath, $targetDiskPath)) {
            return $this->response->setJSON(['success' => false, 'message' => 'ไม่สามารถเตรียมไฟล์ต้นฉบับสำหรับการประทับตราใหม่ได้']);
        }

        $x        = $this->request->getPost('receive_stamp_x') ?: ($document['receive_stamp_x'] ?: '72.2%');
        $y        = $this->request->getPost('receive_stamp_y') ?: ($document['receive_stamp_y'] ?: '3.5%');
        $scale    = (float)($this->request->getPost('receive_stamp_scale') ?: ($document['receive_stamp_scale'] ?: 1.0));
        $wPct     = $this->request->getPost('receive_stamp_w_pct') ?: '23.8%';
        $hasStamp = $this->request->getPost('has_receive_stamp') !== null ? (int)$this->request->getPost('has_receive_stamp') : 1;

        if ($hasStamp === 1) {
            $stampData = [
                'reg_no'        => to_thai_number($document['internal_reg_no'] ?: '-'),
                'doc_date'      => to_thai_number(thai_date($document['receive_date'] ?? date('Y-m-d'), 'medium')),
                'doc_time'      => to_thai_number($document['receive_stamp_time'] ?: (date('H:i') . ' น.')),
                'receiver_name' => session()->get('name') ?: 'เจ้าหน้าที่สารบรรณ',
            ];
            $coords = [
                'x'     => $x,
                'y'     => $y,
                'scale' => $scale,
                'w_pct' => $wPct,
            ];

            $stampService = new \App\Services\DocumentStampService();
            $imprinted = $stampService->imprintReceiveStamp($targetDiskPath, $stampData, $coords);
            if ($imprinted && file_exists($imprinted)) {
                $relPath = str_replace(rtrim(FCPATH, '/\\'), '', $imprinted);
                $targetFilePath = ltrim(str_replace('\\', '/', $relPath), '/');
            }
        }

        $updateData = [
            'file_path'           => $targetFilePath,
            'receive_stamp_x'     => $x,
            'receive_stamp_y'     => $y,
            'receive_stamp_scale' => $scale,
            'has_receive_stamp'   => $hasStamp,
        ];

        $this->documentModel->update($id, $updateData);
        $this->logModel->log($id, $userId, 'UPDATE_STAMP_POS', "ปรับตำแหน่ง/ขนาดตรายางรับหนังสือ และประทับตราลงในไฟล์ใหม่สำเร็จ (X: {$x}, Y: {$y}, Scale: {$scale})");

        return $this->response->setJSON([
            'success'  => true,
            'message'  => 'ปรับตำแหน่งตรายางและประทับตราลงในเอกสารเรียบร้อยแล้ว',
            'file_url' => base_url($targetFilePath) . '?t=' . time(),
        ]);
    }

    /**
     * ลบหนังสือรับเข้า พร้อมลบไฟล์แนบและไฟล์สำรองทั้งหมด (ก่อน ผอ. เกษียน)
     */
    public function delete($id)
    {
        $userId = session()->get('user_id');
        $role   = session()->get('role');
        if (!in_array($role, ['saraban', 'admin', 'super_admin'], true)) {
            return redirect()->back()->with('error', 'คุณไม่มีสิทธิ์ลบหนังสือรับเข้า');
        }

        $document = $this->documentModel->where('doc_type', 'in')->find($id);
        if (!$document) {
            return redirect()->to(base_url('admin/inbound'))->with('error', 'ไม่พบข้อมูลเอกสาร');
        }

        // หากผู้บริหารได้ลงนามแล้ว ห้ามลบ
        $signedRoutesCount = $this->routeModel->where('document_id', $id)->where('status', 'signed')->countAllResults();
        if ($signedRoutesCount > 0 || $document['status'] === 'completed') {
            return redirect()->back()->with('error', 'หนังสือรับเข้าผ่านการเกษียนสั่งการแล้ว ไม่สามารถลบได้');
        }

        // ลบไฟล์เอกสารจริง และไฟล์สำรองดิบ
        if (!empty($document['file_path']) && file_exists(FCPATH . $document['file_path'])) {
            @unlink(FCPATH . $document['file_path']);
        }
        if (!empty($document['raw_file_path']) && file_exists(FCPATH . $document['raw_file_path'])) {
            @unlink(FCPATH . $document['raw_file_path']);
        }

        $this->routeModel->where('document_id', $id)->delete();
        $this->logModel->where('document_id', $id)->delete();
        $this->documentModel->delete($id);

        return redirect()->to(base_url('admin/inbound'))->with('success', "ลบหนังสือรับเข้าเลขทะเบียน {$document['internal_reg_no']} เรียบร้อยแล้ว");
    }

    /**
     * พิมพ์ใบสรุปการรับหนังสือ & เส้นทางเกษียนสั่งการ (Routing Slip)
     */
    public function printSlip($id)
    {
        $document = $this->documentModel->select('documents.*, 
                        creator.first_name as creator_first, creator.last_name as creator_last,
                        assigned.first_name as assigned_first, assigned.last_name as assigned_last, assigned.prefix as assigned_prefix,
                        assigned_dept.name as assigned_dept_name')
                    ->join('users as creator', 'creator.id = documents.created_by', 'left')
                    ->join('users as assigned', 'assigned.id = documents.assigned_to_user_id', 'left')
                    ->join('departments as assigned_dept', 'assigned_dept.id = assigned.department_id', 'left')
                    ->where('documents.id', $id)
                    ->where('documents.doc_type', 'in')
                    ->first();

        if (!$document) {
            return redirect()->to(base_url('admin/inbound'))->with('error', 'ไม่พบข้อมูลหนังสือรับเข้า');
        }

        $routes = $this->routeModel->getRoutesForDocument($id);
        $logs   = $this->logModel->getLogsForDocument($id);

        return view('documents/inbound_print', [
            'document' => $document,
            'routes'   => $routes,
            'logs'     => $logs,
        ]);
    }
}
