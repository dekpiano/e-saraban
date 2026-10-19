<?php

namespace App\Controllers;

use App\Models\DocumentModel;
use App\Models\DocumentRouteModel;
use App\Models\DocumentLogModel;
use App\Models\UserModel;
use App\Models\DepartmentModel;

class InternalMemoController extends BaseController
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
     * ทะเบียนบันทึกข้อความภายในโรงเรียนทั้งหมด
     */
    public function index()
    {
        $status = $this->request->getGet('status');
        $search = trim($this->request->getGet('search') ?? '');

        $builder = $this->documentModel->select('documents.*, users.first_name, users.last_name, users.prefix, departments.name as creator_department')
                                       ->join('users', 'users.id = documents.created_by', 'left')
                                       ->join('departments', 'departments.id = users.department_id', 'left')
                                       ->where('documents.doc_type', 'internal');

        if (!empty($status) && in_array($status, ['routing', 'completed', 'returned', 'draft'], true)) {
            $builder->where('documents.status', $status);
        }

        if (!empty($search)) {
            $builder->groupStart()
                    ->like('documents.doc_no', $search)
                    ->orLike('documents.internal_reg_no', $search)
                    ->orLike('documents.title', $search)
                    ->orLike('documents.from_org', $search)
                    ->orLike('users.first_name', $search)
                    ->orLike('users.last_name', $search)
                    ->groupEnd();
        }

        $memos = $builder->orderBy('documents.created_at', 'DESC')->findAll();

        // สถิติบันทึกข้อความ
        $stats = [
            'total'     => $this->documentModel->where('doc_type', 'internal')->countAllResults(),
            'routing'   => $this->documentModel->where('doc_type', 'internal')->where('status', 'routing')->countAllResults(),
            'completed' => $this->documentModel->where('doc_type', 'internal')->where('status', 'completed')->countAllResults(),
            'returned'  => $this->documentModel->where('doc_type', 'internal')->where('status', 'returned')->countAllResults(),
        ];

        return view('memos/index', [
            'memos'         => $memos,
            'currentStatus' => $status,
            'search'        => $search,
            'stats'         => $stats,
        ]);
    }

    /**
     * ฟอร์มเขียนบันทึกข้อความ (Word-like A4 Editor)
     */
    public function create()
    {
        $userId = session()->get('user_id');
        $user   = $this->userModel->getUserWithDetails($userId);

        $year = (int)date('Y') + 543;
        $nextRegNo = $this->documentModel->getNextInternalNumber('internal', $year);

        // แนะนำเลขที่บันทึกข้อความ เช่น ศธ 04225/บข.0001
        $count = $this->documentModel->where('doc_type', 'internal')->where('year', $year)->countAllResults();
        $runningNo = str_pad($count + 1, 4, '0', STR_PAD_LEFT);
        $suggestedDocNo = 'ศธ 04225/' . $runningNo;

        $allUsers    = $this->userModel->getAllActiveUsers();
        $departments = $this->deptModel->findAll();

        // แผนกและเบอร์โทรเริ่มต้น
        $defaultDept = $user['department_name'] ?? 'กลุ่มสาระการเรียนรู้วิทยาศาสตร์และเทคโนโลยี';
        $defaultContact = !empty($user['phone']) ? str_replace('โทร.', '', $user['phone']) : '056-221133';
        $defaultContact = trim($defaultContact);

        return view('memos/create', [
            'user'           => $user,
            'nextRegNo'      => $nextRegNo,
            'suggestedDocNo' => $suggestedDocNo,
            'allUsers'       => $allUsers,
            'departments'    => $departments,
            'defaultDept'    => $defaultDept,
            'defaultContact' => $defaultContact,
            'currentYear'    => $year,
        ]);
    }

    /**
     * บันทึกข้อมูลบันทึกข้อความและสร้างเส้นทางเดินเรื่อง
     */
    public function store()
    {
        $userId = session()->get('user_id');

        // Validation Rules
        $rules = [
            'title'     => 'required|min_length[3]|max_length[255]',
            'doc_no'    => 'required|max_length[100]',
            'from_org'  => 'required|max_length[200]',
            'to_org'    => 'required|max_length[200]',
            'doc_date'  => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'กรุณากรอกข้อมูลบันทึกข้อความให้ครบถ้วน');
        }

        $docNo       = trim($this->request->getPost('doc_no'));
        $regNo       = trim($this->request->getPost('internal_reg_no'));
        $docDate     = $this->request->getPost('doc_date') ?: date('Y-m-d');
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $docDate, $m) && (int)$m[1] > 2400) {
            $docDate = ((int)$m[1] - 543) . '-' . $m[2] . '-' . $m[3];
        }

        $docYear     = (int)date('Y', strtotime($docDate)) + 543;
        $title       = trim($this->request->getPost('title'));
        $urgency     = $this->request->getPost('urgency') ?: 'normal';
        $secretLevel = $this->request->getPost('secret_level') ?: 'normal';
        $fromOrg     = trim($this->request->getPost('from_org'));
        $toOrg       = trim($this->request->getPost('to_org'));
        $memoContact = trim($this->request->getPost('memo_contact') ?: '');
        $content     = $this->request->getPost('content'); // HTML from Word-like editor

        // การส่งเรื่อง (Draft หรือ Route ทันที)
        $action = $this->request->getPost('submit_action') ?: 'route';
        $status = ($action === 'draft') ? 'draft' : 'routing';

        // จัดการไฟล์แนบเพิ่มเติม (ถ้ามี)
        $file = $this->request->getFile('doc_file');
        $filePath = null;
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/documents', $newName);
            $filePath = 'uploads/documents/' . $newName;
        }

        // จัดการลายเซ็นออนไลน์ของผู้เขียน (ถ้าเลือกใช้งาน)
        $creatorSignature = null;
        $sigData = $this->request->getPost('signature_data');
        if (!empty($sigData)) {
            if (strpos($sigData, 'data:image/') === 0) {
                $data = substr($sigData, strpos($sigData, ',') + 1);
                $decoded = base64_decode($data);
                if ($decoded !== false) {
                    $sigName = 'memo_sig_' . uniqid() . '_' . time() . '.png';
                    $sigDir  = FCPATH . 'uploads/signatures/';
                    if (!is_dir($sigDir)) {
                        mkdir($sigDir, 0777, true);
                    }
                    file_put_contents($sigDir . $sigName, $decoded);
                    $creatorSignature = 'uploads/signatures/' . $sigName;
                }
            } else {
                $creatorSignature = trim($sigData);
            }
        }

        // Database Transaction ตามมาตรฐาน Expert Programmer
        $db = \Config\Database::connect();
        $db->transStart();

        $docData = [
            'doc_type'          => 'internal',
            'doc_no'            => $docNo,
            'internal_reg_no'   => $regNo,
            'year'              => $docYear,
            'title'             => $title,
            'urgency'           => $urgency,
            'secret_level'      => $secretLevel,
            'from_org'          => $fromOrg,
            'to_org'            => $toOrg,
            'memo_department'   => $fromOrg,
            'memo_contact'      => $memoContact,
            'doc_date'          => $docDate,
            'receive_date'      => date('Y-m-d'),
            'content'           => $content,
            'file_path'         => $filePath,
            'creator_signature' => $creatorSignature,
            'status'            => $status,
            'current_step'      => 1,
            'created_by'        => $userId,
        ];

        $docId = $this->documentModel->insert($docData);

        // กำหนดเส้นทางเดินเรื่อง (Routing Steps)
        $routeUsers = $this->request->getPost('route_users');
        if (!empty($routeUsers) && is_array($routeUsers)) {
            $step = 1;
            foreach ($routeUsers as $rUserId) {
                if (!empty($rUserId)) {
                    $this->routeModel->insert([
                        'document_id' => $docId,
                        'step_order'  => $step,
                        'user_id'     => (int)$rUserId,
                        'status'      => 'pending',
                    ]);
                    $step++;
                }
            }
        }

        // Audit Trail Log
        $this->logModel->log($docId, $userId, 'CREATE', 'เขียนบันทึกข้อความภายในโรงเรียนและส่งเสนอขออนุมัติ');

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'เกิดข้อผิดพลาดในการบันทึกข้อมูล กรุณาลองใหม่อีกครั้ง');
        }

        $message = ($status === 'draft') 
            ? 'บันทึกฉบับร่างบันทึกข้อความเรียบร้อยแล้ว' 
            : 'บันทึกและส่งเสนอเกษียนบันทึกข้อความเรียบร้อยแล้ว (เลขที่: ' . $docNo . ')';

        return redirect()->to('/admin/memos/' . $docId)->with('success', $message);
    }

    /**
     * ดูรายละเอียดบันทึกข้อความ (เอกสาร A4 + ลายเซ็นต์เกษียน + เส้นทางเดินเรื่อง)
     */
    public function view($id)
    {
        $userId   = session()->get('user_id');
        $document = $this->documentModel->getDocumentDetails($id);

        if (!$document || $document['doc_type'] !== 'internal') {
            return redirect()->to('/admin/memos')->with('error', 'ไม่พบบันทึกข้อความที่ต้องการ หรือเอกสารไม่ใช่ประเภทบันทึกข้อความ');
        }

        $routes = $this->routeModel->getRoutesForDocument($id);
        $logs   = $this->logModel->getLogsForDocument($id);

        // ตรวจสอบว่าผู้ใช้งานปัจจุบันคือผู้ที่ต้องลงนามในขั้นตอนนี้หรือไม่
        $canSign = false;
        $currentRoute = null;

        foreach ($routes as $route) {
            if ($route['step_order'] == $document['current_step'] 
                && $route['user_id'] == $userId 
                && $route['status'] === 'pending' 
                && $document['status'] === 'routing') {
                $canSign = true;
                $currentRoute = $route;
                break;
            }
        }

        // ดึงข้อมูลผู้สร้างบันทึกข้อความ
        $creator = $this->userModel->getUserWithDetails($document['created_by']);

        return view('memos/view', [
            'document'     => $document,
            'creator'      => $creator,
            'routes'       => $routes,
            'logs'         => $logs,
            'canSign'      => $canSign,
            'currentRoute' => $currentRoute,
        ]);
    }

    /**
     * หน้าพร้อมพิมพ์บันทึกข้อความขนาด A4 ตามมาตรฐานระเบียบสารบรรณ
     */
    public function printView($id)
    {
        $document = $this->documentModel->getDocumentDetails($id);

        if (!$document) {
            return redirect()->to('/admin/memos')->with('error', 'ไม่พบบันทึกข้อความ');
        }

        $routes  = $this->routeModel->getRoutesForDocument($id);
        $creator = $this->userModel->getUserWithDetails($document['created_by']);

        return view('memos/print', [
            'document' => $document,
            'creator'  => $creator,
            'routes'   => $routes,
        ]);
    }

    /**
     * ฟอร์มแก้ไขบันทึกข้อความ (กรณีร่างหรือถูกตีกลับ)
     */
    public function edit($id)
    {
        $userId   = session()->get('user_id');
        $document = $this->documentModel->getDocumentDetails($id);

        if (!$document || $document['doc_type'] !== 'internal') {
            return redirect()->to('/admin/memos')->with('error', 'ไม่พบบันทึกข้อความ');
        }

        // อนุญาตให้แก้ไขได้เฉพาะเจ้าของเรื่องและสถานะ draft หรือ returned
        if ($document['created_by'] != $userId && session()->get('role') !== 'super_admin') {
            return redirect()->to('/admin/memos/' . $id)->with('error', 'คุณไม่มีสิทธิ์แก้ไขบันทึกข้อความนี้');
        }

        $user        = $this->userModel->getUserWithDetails($userId);
        $allUsers    = $this->userModel->getAllActiveUsers();
        $departments = $this->deptModel->findAll();
        $routes      = $this->routeModel->getRoutesForDocument($id);

        return view('memos/edit', [
            'document'    => $document,
            'user'        => $user,
            'allUsers'    => $allUsers,
            'departments' => $departments,
            'routes'      => $routes,
        ]);
    }

    /**
     * อัปเดตบันทึกข้อความ
     */
    public function update($id)
    {
        $userId   = session()->get('user_id');
        $document = $this->documentModel->find($id);

        if (!$document || $document['doc_type'] !== 'internal') {
            return redirect()->to('/admin/memos')->with('error', 'ไม่พบเอกสาร');
        }

        $rules = [
            'title'    => 'required|min_length[3]|max_length[255]',
            'doc_no'   => 'required|max_length[100]',
            'from_org' => 'required|max_length[200]',
            'to_org'   => 'required|max_length[200]',
            'doc_date' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'กรุณากรอกข้อมูลให้ครบถ้วน');
        }

        $docDate = $this->request->getPost('doc_date') ?: date('Y-m-d');
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $docDate, $m) && (int)$m[1] > 2400) {
            $docDate = ((int)$m[1] - 543) . '-' . $m[2] . '-' . $m[3];
        }

        $action = $this->request->getPost('submit_action') ?: 'route';
        $newStatus = ($action === 'draft') ? 'draft' : 'routing';

        $db = \Config\Database::connect();
        $db->transStart();

        $updateData = [
            'doc_no'          => trim($this->request->getPost('doc_no')),
            'internal_reg_no' => trim($this->request->getPost('internal_reg_no')),
            'title'           => trim($this->request->getPost('title')),
            'urgency'         => $this->request->getPost('urgency') ?: 'normal',
            'secret_level'    => $this->request->getPost('secret_level') ?: 'normal',
            'from_org'        => trim($this->request->getPost('from_org')),
            'to_org'          => trim($this->request->getPost('to_org')),
            'memo_department' => trim($this->request->getPost('from_org')),
            'memo_contact'    => trim($this->request->getPost('memo_contact') ?: ''),
            'doc_date'        => $docDate,
            'content'         => $this->request->getPost('content'),
            'status'          => $newStatus,
        ];

        // จัดการปรับปรุงหรือลบลายเซ็น
        $removeSig = $this->request->getPost('remove_signature');
        $sigData   = $this->request->getPost('signature_data');
        if ($removeSig === '1') {
            $updateData['creator_signature'] = null;
        } elseif (!empty($sigData)) {
            if (strpos($sigData, 'data:image/') === 0) {
                $data = substr($sigData, strpos($sigData, ',') + 1);
                $decoded = base64_decode($data);
                if ($decoded !== false) {
                    $sigName = 'memo_sig_' . uniqid() . '_' . time() . '.png';
                    $sigDir  = FCPATH . 'uploads/signatures/';
                    if (!is_dir($sigDir)) {
                        mkdir($sigDir, 0777, true);
                    }
                    file_put_contents($sigDir . $sigName, $decoded);
                    $updateData['creator_signature'] = 'uploads/signatures/' . $sigName;
                }
            } else {
                $updateData['creator_signature'] = trim($sigData);
            }
        }

        // หากถูกส่งคืนมา เมื่ออัปเดตและส่งใหม่ ให้รีเซ็ตขั้นตอนกลับไป step 1
        if ($document['status'] === 'returned' && $newStatus === 'routing') {
            $updateData['current_step'] = 1;
            // รีเซ็ตสถานะ routes ที่ค้างอยู่ให้กลับเป็น pending
            $this->routeModel->where('document_id', $id)->set(['status' => 'pending'])->update();
        }

        $this->documentModel->update($id, $updateData);
        $this->logModel->log($id, $userId, 'EDIT', 'แก้ไขเนื้อหาบันทึกข้อความ');

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'เกิดข้อผิดพลาดในการบันทึกข้อมูล');
        }

        return redirect()->to('/admin/memos/' . $id)->with('success', 'บันทึกการแก้ไขเรียบร้อยแล้ว');
    }

    /**
     * ลงนาม / เกษียนเรื่องบันทึกข้อความ
     */
    public function sign($id)
    {
        $userId   = session()->get('user_id');
        $document = $this->documentModel->find($id);

        if (!$document || $document['doc_type'] !== 'internal') {
            return redirect()->to('/admin/memos')->with('error', 'ไม่พบบันทึกข้อความ');
        }

        $route = $this->routeModel->where('document_id', $id)
                                  ->where('step_order', $document['current_step'])
                                  ->where('user_id', $userId)
                                  ->where('status', 'pending')
                                  ->first();

        if (!$route) {
            return redirect()->back()->with('error', 'คุณไม่มีสิทธิ์ลงนามในขั้นตอนนี้');
        }

        $actionType     = $this->request->getPost('action_type'); // 'approve' or 'return'
        $commentPreset  = $this->request->getPost('comment_preset');
        $commentText    = $this->request->getPost('comment_text');
        $signatureType  = $this->request->getPost('signature_type') ?: 'draw';
        $signatureData  = $this->request->getPost('signature_data');

        $db = \Config\Database::connect();
        $db->transStart();

        if ($actionType === 'return') {
            // ตีกลับเอกสาร
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

            $this->logModel->log($id, $userId, 'RETURN', 'ส่งคืนบันทึกข้อความให้แก้ไข: ' . $commentText);
            $db->transComplete();

            return redirect()->to('/admin/memos/' . $id)->with('warning', 'ส่งคืนบันทึกข้อความให้แก้ไขเรียบร้อยแล้ว');
        }

        // บันทึกลายเซ็นต์
        $signaturePath = '';
        if ($signatureType === 'draw' && !empty($signatureData)) {
            $data = explode(',', $signatureData);
            $img = base64_decode(end($data));
            $filename = 'sign_memo_' . $id . '_step_' . $route['step_order'] . '_' . time() . '.png';
            $path = FCPATH . 'uploads/signatures/' . $filename;
            if (!is_dir(FCPATH . 'uploads/signatures')) {
                mkdir(FCPATH . 'uploads/signatures', 0777, true);
            }
            file_put_contents($path, $img);
            $signaturePath = 'uploads/signatures/' . $filename;
        } else {
            $user = $this->userModel->getUserWithDetails($userId);
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
            'ip_address'     => $this->request->getIPAddress(),
            'verify_hash'    => $verifyHash,
        ]);

        // ตรวจสอบขั้นตอนถัดไป
        $nextStep = $this->routeModel->where('document_id', $id)
                                     ->where('step_order', $document['current_step'] + 1)
                                     ->first();

        $userProfile = $this->userModel->getUserWithDetails($userId);
        $isFinalSigner = (!empty($userProfile['can_sign_final']) && $userProfile['can_sign_final'] == 1);

        if ($nextStep && !$isFinalSigner) {
            $this->documentModel->update($id, [
                'current_step' => $document['current_step'] + 1
            ]);
            $this->logModel->log($id, $userId, 'SIGN', "เกษียนบันทึกข้อความ (ขั้นที่ {$route['step_order']}) ส่งต่อขั้นถัดไป");
            $msg = 'เกษียน/ลงนามบันทึกข้อความเรียบร้อยแล้ว และส่งต่อไปยังลำดับถัดไป';
        } else {
            $this->documentModel->update($id, [
                'status' => 'completed'
            ]);
            $this->logModel->log($id, $userId, 'COMPLETE', "ลงนามอนุมัติ/สั่งการบันทึกข้อความเสร็จสิ้นสมบูรณ์");
            $msg = 'ลงนามอนุมัติบันทึกข้อความเรียบร้อยแล้ว เอกสารเสร็จสิ้นสมบูรณ์';
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'เกิดข้อผิดพลาดในการลงนาม');
        }

        return redirect()->to('/admin/memos/' . $id)->with('success', $msg);
    }

    /**
     * ลบบันทึกข้อความ (เฉพาะฉบับร่าง)
     */
    public function delete($id)
    {
        $userId   = session()->get('user_id');
        $document = $this->documentModel->find($id);

        if (!$document || $document['doc_type'] !== 'internal') {
            return redirect()->to('/admin/memos')->with('error', 'ไม่พบเอกสาร');
        }

        if ($document['created_by'] != $userId && session()->get('role') !== 'super_admin') {
            return redirect()->to('/admin/memos')->with('error', 'คุณไม่มีสิทธิ์ลบบันทึกข้อความนี้');
        }

        if ($document['status'] !== 'draft' && session()->get('role') !== 'super_admin') {
            return redirect()->to('/admin/memos')->with('error', 'สามารถลบได้เฉพาะบันทึกข้อความที่อยู่ในสถานะแบบร่างเท่านั้น');
        }

        $this->documentModel->delete($id);
        return redirect()->to('/admin/memos')->with('success', 'ลบบันทึกข้อความเรียบร้อยแล้ว');
    }
}
