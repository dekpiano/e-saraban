<?php

namespace App\Controllers;

use App\Models\DocumentModel;
use App\Models\DocumentRouteModel;
use App\Models\DocumentLogModel;
use App\Models\UserModel;
use App\Models\DepartmentModel;

class DocumentController extends BaseController
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

    // ทะเบียนหนังสือทั้งหมด
    public function index()
    {
        $type = $this->request->getGet('type');
        $search = trim((string)$this->request->getGet('search'));
        $query = $this->documentModel->select('documents.*, users.first_name, users.last_name, users.prefix')
                                     ->join('users', 'users.id = documents.created_by', 'left');

        if (!empty($type)) {
            $query->where('documents.doc_type', $type);
        }

        if (!empty($search)) {
            $query->groupStart()
                  ->like('documents.doc_no', $search)
                  ->orLike('documents.internal_reg_no', $search)
                  ->orLike('documents.title', $search)
                  ->orLike('documents.from_org', $search)
                  ->orLike('documents.to_org', $search)
                  ->groupEnd();
        }

        $documents = $query->orderBy('documents.created_at', 'DESC')->findAll();

        return view('documents/documents_index', [
            'documents'   => $documents,
            'currentType' => $type,
            'search'      => $search
        ]);
    }

    // หนังสือรอฉันลงนาม
    public function inbox()
    {
        $userId = session()->get('user_id');
        $documents = $this->documentModel->getDocumentsForUser($userId, 'inbox');

        return view('documents/inbox', [
            'documents' => $documents
        ]);
    }

    // หนังสือที่ฉันลงนามแล้ว
    public function signed()
    {
        $userId = session()->get('user_id');
        $documents = $this->documentModel->getDocumentsForUser($userId, 'signed');

        return view('documents/signed', [
            'documents' => $documents
        ]);
    }

    // หนังสือที่ฉันเป็นผู้สร้าง
    public function myDocuments()
    {
        $userId = session()->get('user_id');
        $documents = $this->documentModel->getDocumentsForUser($userId, 'my_created');

        return view('documents/my_documents', [
            'documents' => $documents
        ]);
    }

    // ฟอร์มลงทะเบียนหนังสือใหม่
    public function create()
    {
        $userRole = (string)(session()->get('role') ?? 'teacher');
        if (!in_array($userRole, ['super_admin', 'admin', 'saraban'], true)) {
            return redirect()->to(base_url('admin/documents'))->with('error', 'เฉพาะเจ้าหน้าที่สารบรรณหรือผู้ดูแลระบบเท่านั้นที่มีสิทธิ์ลงทะเบียนรับ-ส่งหนังสือ');
        }

        $defaultType = $this->request->getGet('type') ?? 'in';
        $nextRegNo   = $this->documentModel->getNextInternalNumber($defaultType);
        $users       = $this->userModel->getAllActiveUsers();
        $departments = $this->deptModel->findAll();

        return view('documents/create', [
            'defaultType' => $defaultType,
            'nextRegNo'   => $nextRegNo,
            'users'       => $users,
            'departments' => $departments,
        ]);
    }

    // บันทึกการสร้างหนังสือ
    public function store()
    {
        $userId = session()->get('user_id');

        $docType       = $this->request->getPost('doc_type');
        $docNo         = $this->request->getPost('doc_no');
        $internalRegNo = $this->request->getPost('internal_reg_no');
        $docDate       = $this->request->getPost('doc_date') ?: date('Y-m-d');
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $docDate, $m) && (int)$m[1] > 2400) {
            $docDate = ((int)$m[1] - 543) . '-' . $m[2] . '-' . $m[3];
        }

        $receiveDate   = $this->request->getPost('receive_date') ?: date('Y-m-d');
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $receiveDate, $m) && (int)$m[1] > 2400) {
            $receiveDate = ((int)$m[1] - 543) . '-' . $m[2] . '-' . $m[3];
        }

        $docYear       = (int)date('Y', strtotime($docDate)) + 543;
        $year          = (int)$this->request->getPost('year') ?: $docYear;
        $title         = $this->request->getPost('title');
        $urgency       = $this->request->getPost('urgency') ?: 'normal';
        $secretLevel   = $this->request->getPost('secret_level') ?: 'normal';
        $fromOrg       = $this->request->getPost('from_org');
        $toOrg         = $this->request->getPost('to_org');

        // File upload
        $file = $this->request->getFile('doc_file');
        $filePath = '';

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $ext = strtolower($file->getExtension());
            $newName = 'doc_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $remoteUploadService = new \App\Services\RemoteUploadService();
            $uploadResult = $remoteUploadService->uploadUploadedFile($file, 'saraban/documents', $newName);
            if ($uploadResult['status'] === 'success') {
                $filePath = $uploadResult['filePath'];
            }
        }

        $docData = [
            'doc_type'        => $docType,
            'doc_no'          => $docNo,
            'internal_reg_no' => $internalRegNo,
            'year'            => $year,
            'title'           => $title,
            'urgency'         => $urgency,
            'secret_level'    => $secretLevel,
            'from_org'        => $fromOrg,
            'to_org'          => $toOrg,
            'doc_date'        => $docDate,
            'receive_date'    => $receiveDate,
            'file_path'       => $filePath,
            'status'          => 'routing',
            'current_step'    => 1,
            'created_by'      => $userId,
        ];

        $docId = $this->documentModel->insert($docData);

        // Routing Steps
        $routeUsers = $this->request->getPost('route_users'); // array of user IDs
        if (!empty($routeUsers) && is_array($routeUsers)) {
            $step = 1;
            foreach ($routeUsers as $targetUserId) {
                if (!empty($targetUserId)) {
                    $this->routeModel->insert([
                        'document_id' => $docId,
                        'step_order'  => $step,
                        'user_id'     => $targetUserId,
                        'status'      => 'pending',
                    ]);
                    $step++;
                }
            }
        }

        // Log audit
        $this->logModel->log($docId, $userId, 'CREATE', 'ลงทะเบียนหนังสือและส่งเข้าสู่ขั้นตอนเสนอเกษียน');

        return redirect()->to('/admin/documents/' . $docId)->with('success', 'ลงทะเบียนและส่งเสนอหนังสือเรียบร้อยแล้ว');
    }

    // ดูรายละเอียดเอกสารและประวัติการเกษียน
    public function view($id)
    {
        $userId   = session()->get('user_id');
        $document = $this->documentModel->getDocumentDetails($id);

        if (!$document) {
            return redirect()->to('/admin/documents')->with('error', 'ไม่พบเอกสารที่ระบุ');
        }

        $routes = $this->routeModel->getRoutesForDocument($id);
        $logs   = $this->logModel->getLogsForDocument($id);

        // Check if current user is the one assigned to current_step
        $canSign = false;
        $currentRoute = null;
        $persId = (string)(session()->get('pers_id') ?? '');
        $role   = (string)(session()->get('role') ?? '');
        $isExecutiveOrAdmin = in_array($role, ['executive', 'super_admin'], true);

        foreach ($routes as $route) {
            $isMatch = ($route['user_id'] == $userId)
                || (!empty($persId) && (string)($route['pers_id'] ?? '') === $persId)
                || ($isExecutiveOrAdmin && (
                    ($route['pers_id'] ?? '') === 'pers_060'
                    || str_contains($route['personnel_position'] ?? '', 'ผู้อำนวยการ')
                    || str_contains($route['position_name'] ?? '', 'ผู้อำนวยการ')
                    || empty($route['user_id'])
                ));

            if ($route['step_order'] == $document['current_step'] && $isMatch && $route['status'] === 'pending' && $document['status'] === 'routing') {
                $canSign = true;
                $currentRoute = $route;
                break;
            }
        }

        $creator     = !empty($document['created_by']) ? $this->userModel->getUserWithDetails($document['created_by']) : null;
        $currentUser = !empty($userId) ? $this->userModel->getUserWithDetails($userId) : null;

        return view('documents/view', [
            'document'     => $document,
            'creator'      => $creator,
            'currentUser'  => $currentUser,
            'routes'       => $routes,
            'logs'         => $logs,
            'canSign'      => $canSign,
            'currentRoute' => $currentRoute,
        ]);
    }

    // ทำการเซ็นเกษียน / ลงนาม
    public function sign($id)
    {
        $userId   = session()->get('user_id');
        $document = $this->documentModel->find($id);

        if (!$document) {
            return redirect()->to('/admin/documents')->with('error', 'ไม่พบเอกสาร');
        }

        $persId = (string)(session()->get('pers_id') ?? '');
        $role   = (string)(session()->get('role') ?? '');
        $isExecutiveOrAdmin = in_array($role, ['executive', 'super_admin'], true);

        // Find route for this user at this step
        $routeBuilder = $this->routeModel->where('document_id', $id)
                                         ->where('step_order', $document['current_step'])
                                         ->where('status', 'pending');

        $routeBuilder->groupStart()
                     ->where('user_id', $userId);
        if (!empty($persId)) {
            $routeBuilder->orWhere('pers_id', $persId);
        }
        if ($isExecutiveOrAdmin) {
            $routeBuilder->orWhere('pers_id', 'pers_060')
                         ->orLike('personnel_position', 'ผู้อำนวยการ')
                         ->orWhere('user_id IS NULL', null, false);
        }
        $routeBuilder->groupEnd();

        $route = $routeBuilder->first();

        if (!$route) {
            return redirect()->back()->with('error', 'คุณไม่มีสิทธิ์ลงนามในเอกสารขั้นตอนนี้');
        }

        $actionType     = $this->request->getPost('action_type'); // 'approve' or 'return'
        $commentPreset  = $this->request->getPost('comment_preset');
        $commentText    = $this->request->getPost('comment_text');
        $signatureType  = $this->request->getPost('signature_type') ?: 'draw';
        $signatureData  = $this->request->getPost('signature_data');

        if ($actionType === 'return') {
            // Return document for revision
            $this->routeModel->update($route['id'], [
                'status'         => 'returned',
                'comment_preset' => 'ส่งคืนเรื่องให้แก้ไข',
                'comment_text'   => $commentText,
                'signed_at'      => date('Y-m-d H:i:s'),
                'ip_address'     => $this->request->getIPAddress(),
            ]);

            $this->documentModel->update($id, [
                'status' => 'returned'
            ]);

            $this->logModel->log($id, $userId, 'RETURN', 'ส่งคืนหนังสือให้แก้ไขเนื่องจาก: ' . $commentText);

            return redirect()->to('/admin/documents/' . $id)->with('warning', 'ส่งคืนหนังสือให้ผู้เสนอเรื่องแก้ไขเรียบร้อยแล้ว');
        }

        // Handle signature image
        $signaturePath = '';
        if ($signatureType === 'draw' && !empty($signatureData)) {
            $data = explode(',', $signatureData);
            $img = base64_decode(end($data));
            $dir = FCPATH . 'uploads/signatures/';
            if (!is_dir($dir)) {
                @mkdir($dir, 0777, true);
            }
            $filename = 'sign_' . $id . '_step_' . $route['step_order'] . '_' . time() . '.png';
            $path = $dir . $filename;
            file_put_contents($path, $img);
            $signaturePath = 'uploads/signatures/' . $filename;
        } else {
            // Preset profile signature (support both users and tb_personnel)
            $user = $this->userModel->getUserWithDetails($userId);
            $signaturePath = $user['signature_img'] ?? session()->get('signature_img') ?? '';
        }

        // Create verification hash
        $verifyString = $id . '|' . $userId . '|' . date('Y-m-d H:i:s') . '|' . $this->request->getIPAddress();
        $verifyHash   = hash('sha256', $verifyString);

        // Capture stamp coordinates, scale and dimensions
        $stampX     = trim((string)$this->request->getPost('stamp_x'));
        $stampY     = trim((string)$this->request->getPost('stamp_y'));
        $stampScale = trim((string)$this->request->getPost('stamp_scale'));
        $stampWidth = trim((string)$this->request->getPost('stamp_width'));
        $stampPage  = (int)($this->request->getPost('stamp_page') ?? 1);

        if (empty($stampX)) {
            $stampX = '8%';
        }
        if (empty($stampY)) {
            $stampY = '70%';
        }
        if (empty($stampScale)) {
            $stampScale = '1.0';
        }

        // Auto-check table columns
        $db = \Config\Database::connect();
        if (!$db->fieldExists('stamp_scale', 'document_routes')) {
            $db->query("ALTER TABLE document_routes ADD COLUMN stamp_scale VARCHAR(20) DEFAULT '1.0' AFTER stamp_y");
        }
        if (!$db->fieldExists('stamp_width', 'document_routes')) {
            $db->query("ALTER TABLE document_routes ADD COLUMN stamp_width VARCHAR(20) DEFAULT NULL AFTER stamp_scale");
        }

        // Update current route step
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
            'stamp_x'        => $stampX,
            'stamp_y'        => $stampY,
            'stamp_scale'    => $stampScale,
            'stamp_width'    => !empty($stampWidth) ? $stampWidth : null,
            'stamp_page'     => $stampPage,
        ]);

        // Check if there is a next step
        $nextStep = $this->routeModel->where('document_id', $id)
                                     ->where('step_order', $document['current_step'] + 1)
                                     ->first();

        $userProfile = $this->userModel->getUserWithDetails($userId);
        $isFinalSigner = $userProfile['can_sign_final'] == 1;

        if ($nextStep && !$isFinalSigner) {
            // Move to next step
            $this->documentModel->update($id, [
                'current_step' => $document['current_step'] + 1
            ]);
            $this->logModel->log($id, $userId, 'SIGN', "ลงนามเกษียนหนังสือ (ขั้นที่ {$route['step_order']}) ส่งต่อขั้นถัดไป");
            $msg = 'ลงนามเกษียนหนังสือเรียบร้อยแล้ว และส่งต่อไปยังลำดับถัดไป';
        } else {
            // Document completed
            $this->documentModel->update($id, [
                'status' => 'completed'
            ]);
            $this->logModel->log($id, $userId, 'COMPLETE', "ลงนามอนุมัติ/สั่งการเรียบร้อย เอกสารเสร็จสิ้นสมบูรณ์");
            $msg = 'ลงนามอนุมัติ/สั่งการเสร็จสมบูรณ์เรียบร้อยแล้ว เอกสารมีผลบังคับใช้';
        }

        return redirect()->to('/admin/documents/' . $id)->with('success', $msg);
    }

    // ตรวจสอบความถูกต้องของลายเซ็นดิจิทัล (Public Verification)
    public function verify($hash)
    {
        $route = $this->routeModel->select('document_routes.*, documents.doc_no, documents.title, documents.doc_type, users.prefix, users.first_name, users.last_name, positions.name as position_name, departments.name as department_name')
                                  ->join('documents', 'documents.id = document_routes.document_id')
                                  ->join('users', 'users.id = document_routes.user_id')
                                  ->join('positions', 'positions.id = users.position_id', 'left')
                                  ->join('departments', 'departments.id = users.department_id', 'left')
                                  ->where('document_routes.verify_hash', $hash)
                                  ->first();

        return view('documents/verify', [
            'route' => $route,
            'hash'  => $hash
        ]);
    }

    // อัปโหลดหรือเปลี่ยนไฟล์แนบในหน้ารายละเอียดหนังสือ
    public function uploadFile($id)
    {
        $userId   = session()->get('user_id');
        $document = $this->documentModel->find($id);

        if (!$document) {
            return redirect()->to('/admin/documents')->with('error', 'ไม่พบเอกสาร');
        }

        $file = $this->request->getFile('doc_file');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $ext = strtolower($file->getExtension());
            $newName = 'doc_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $remoteUploadService = new \App\Services\RemoteUploadService();

            // ลบไฟล์เดิมถ้ามี
            if (!empty($document['file_path'])) {
                $remoteUploadService->deleteFile($document['file_path']);
                if (file_exists(FCPATH . ltrim($document['file_path'], '/\\'))) {
                    @unlink(FCPATH . ltrim($document['file_path'], '/\\'));
                }
            }

            $uploadResult = $remoteUploadService->uploadUploadedFile($file, 'saraban/documents', $newName);
            if ($uploadResult['status'] !== 'success') {
                return redirect()->to('/admin/documents/' . $id)->with('error', $uploadResult['message']);
            }
            $filePath = $uploadResult['filePath'];

            $this->documentModel->update($id, [
                'file_path' => $filePath
            ]);

            $this->logModel->log($id, $userId, 'UPLOAD', 'อัปโหลด/เปลี่ยนไฟล์แนบเอกสาร (' . $file->getClientName() . ')');

            return redirect()->to('/admin/documents/' . $id)->with('success', 'อัปโหลดไฟล์เอกสารเรียบร้อยแล้ว');
        }

        return redirect()->to('/admin/documents/' . $id)->with('error', 'ไม่สามารถอัปโหลดไฟล์ได้ กรุณาเลือกไฟล์ที่ถูกต้อง');
    }
}
