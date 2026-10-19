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
        $query = $this->documentModel->select('documents.*, users.first_name, users.last_name, users.prefix')
                                     ->join('users', 'users.id = documents.created_by', 'left');

        if (!empty($type)) {
            $query->where('documents.doc_type', $type);
        }

        $documents = $query->orderBy('documents.created_at', 'DESC')->findAll();

        return view('documents/index', [
            'documents' => $documents,
            'currentType' => $type
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
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/documents', $newName);
            $filePath = 'uploads/documents/' . $newName;
        } else {
            // Default sample file if none uploaded
            $filePath = 'uploads/documents/sample_document.pdf';
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

        foreach ($routes as $route) {
            if ($route['step_order'] == $document['current_step'] && $route['user_id'] == $userId && $route['status'] === 'pending' && $document['status'] === 'routing') {
                $canSign = true;
                $currentRoute = $route;
                break;
            }
        }

        return view('documents/view', [
            'document'     => $document,
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

        // Find route for this user at this step
        $route = $this->routeModel->where('document_id', $id)
                                  ->where('step_order', $document['current_step'])
                                  ->where('user_id', $userId)
                                  ->where('status', 'pending')
                                  ->first();

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
            $filename = 'sign_' . $id . '_step_' . $route['step_order'] . '_' . time() . '.png';
            $path = FCPATH . 'uploads/signatures/' . $filename;
            file_put_contents($path, $img);
            $signaturePath = 'uploads/signatures/' . $filename;
        } else {
            // Preset profile signature
            $user = $this->userModel->find($userId);
            $signaturePath = $user['signature_img'] ?? '';
        }

        // Create verification hash
        $verifyString = $id . '|' . $userId . '|' . date('Y-m-d H:i:s') . '|' . $this->request->getIPAddress();
        $verifyHash   = hash('sha256', $verifyString);

        // Update current route step
        $this->routeModel->update($route['id'], [
            'status'         => 'signed',
            'comment_preset' => $commentPreset,
            'comment_text'   => $commentText,
            'signature_img'  => $signaturePath,
            'signature_type' => $signatureType,
            'signed_at'      => date('Y-m-d H:i:s'),
            'ip_address'     => $this->request->getIPAddress(),
            'verify_hash'    => $verifyHash,
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
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/documents', $newName);
            $filePath = 'uploads/documents/' . $newName;

            $this->documentModel->update($id, [
                'file_path' => $filePath
            ]);

            $this->logModel->log($id, $userId, 'UPLOAD', 'อัปโหลด/เปลี่ยนไฟล์แนบเอกสาร (' . $file->getClientName() . ')');

            return redirect()->to('/admin/documents/' . $id)->with('success', 'อัปโหลดไฟล์เอกสารเรียบร้อยแล้ว');
        }

        return redirect()->to('/admin/documents/' . $id)->with('error', 'ไม่สามารถอัปโหลดไฟล์ได้ กรุณาเลือกไฟล์ที่ถูกต้อง');
    }
}
