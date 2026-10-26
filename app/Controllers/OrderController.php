<?php

namespace App\Controllers;

use App\Models\DocumentModel;
use App\Models\DocumentLogModel;
use App\Models\UserModel;
use App\Models\DepartmentModel;

class OrderController extends BaseController
{
    protected $documentModel;
    protected $logModel;
    protected $userModel;
    protected $deptModel;

    public function __construct()
    {
        $this->documentModel = new DocumentModel();
        $this->logModel      = new DocumentLogModel();
        $this->userModel     = new UserModel();
        $this->deptModel     = new DepartmentModel();
        helper(['thai_date', 'form', 'url']);
    }

    /**
     * ทะเบียนคำสั่งโรงเรียนทั้งหมด (Orders Registry)
     */
    public function index()
    {
        $currentYear = (int)date('Y') + 543;
        $selectedYear = $this->request->getGet('year') ? (int)$this->request->getGet('year') : $currentYear;
        $status = $this->request->getGet('status');
        $category = $this->request->getGet('category');
        $search = trim((string)$this->request->getGet('search'));

        $builder = $this->documentModel->select('documents.*, users.first_name, users.last_name, users.prefix')
                                       ->join('users', 'users.id = documents.created_by', 'left')
                                       ->where('documents.doc_type', 'order');

        if (!empty($selectedYear)) {
            $builder->where('documents.year', $selectedYear);
        }

        if (!empty($status) && in_array($status, ['published', 'draft', 'canceled'], true)) {
            $builder->where('documents.status', $status);
        }

        if (!empty($category)) {
            $builder->where('documents.order_category', $category);
        }

        if (!empty($search)) {
            $builder->groupStart()
                    ->like('documents.doc_no', $search)
                    ->orLike('documents.title', $search)
                    ->orLike('documents.signer_name', $search)
                    ->orLike('users.first_name', $search)
                    ->orLike('users.last_name', $search)
                    ->groupEnd();
        }

        $orders = $builder->orderBy('documents.doc_date', 'DESC')
                          ->orderBy('documents.id', 'DESC')
                          ->findAll();

        // คำนวณสถิติคำสั่งประจำปี
        $allOrdersYear = $this->documentModel->where('doc_type', 'order')
                                             ->where('year', $selectedYear)
                                             ->findAll();
        $stats = [
            'total'     => count($allOrdersYear),
            'published' => count(array_filter($allOrdersYear, fn($o) => ($o['status'] ?? 'published') === 'published')),
            'draft'     => count(array_filter($allOrdersYear, fn($o) => ($o['status'] ?? '') === 'draft')),
            'with_file' => count(array_filter($allOrdersYear, fn($o) => !empty($o['file_path']))),
        ];

        // ดึงรายการปี พ.ศ. ที่มีคำสั่งในระบบ
        $distinctYears = $this->documentModel->where('doc_type', 'order')
                                             ->select('year')
                                             ->distinct()
                                             ->orderBy('year', 'DESC')
                                             ->findAll();
        $yearsList = array_unique(array_filter(array_merge([$currentYear], array_column($distinctYears, 'year'))));
        rsort($yearsList);

        return view('orders/orders_index', [
            'orders'       => $orders,
            'stats'        => $stats,
            'currentYear'  => $currentYear,
            'selectedYear' => $selectedYear,
            'yearsList'    => $yearsList,
            'status'       => $status,
            'category'     => $category,
            'search'       => $search,
        ]);
    }

    /**
     * หน้าสร้างคำสั่งโรงเรียนใหม่ (Interactive WYSIWYG Paper Form)
     */
    public function create()
    {
        $currentYear = (int)date('Y') + 543;
        $nextOrderNo = $this->documentModel->getNextOrderNumber($currentYear);

        // ดึงข้อมูลผู้บริหารจริง (ผอ. และ รอง ผอ.) จากฐานข้อมูล tb_personnel
        $executives = $this->userModel->getExecutivePersonnelList();
        $defaultDirector = $this->userModel->getDefaultDirector();

        $defaultSignerName = $defaultDirector['full_name'];
        $defaultSignerPos  = $defaultDirector['position_name'];

        return view('orders/orders_create', [
            'currentYear'        => $currentYear,
            'suggestedOrderNo'   => $nextOrderNo,
            'defaultSchoolTitle' => 'คำสั่งโรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์',
            'defaultSignerName'  => $defaultSignerName,
            'defaultSignerPos'   => $defaultSignerPos,
            'executives'         => $executives,
            'todayDate'          => date('Y-m-d'),
        ]);
    }

    /**
     * บันทึกคำสั่งโรงเรียน
     */
    public function store()
    {
        $userId = session()->get('user_id');
        $title  = trim((string)$this->request->getPost('title'));
        $docNo  = trim((string)$this->request->getPost('doc_no'));
        $year   = (int)$this->request->getPost('year') ?: ((int)date('Y') + 543);
        $date   = $this->request->getPost('doc_date') ?: date('Y-m-d');

        if (empty($title)) {
            return redirect()->back()->withInput()->with('error', 'กรุณาระบุเรื่องคำสั่งโรงเรียน');
        }

        // จัดการอัปโหลดไฟล์คำสั่ง PDF ถ้ามี
        $filePath = null;
        $file = $this->request->getFile('order_file');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $ext = strtolower($file->getExtension());
            if ($ext !== 'pdf') {
                return redirect()->back()->withInput()->with('error', 'เอกสารแนบคำสั่งต้องเป็นไฟล์ PDF เท่านั้น');
            }
            $newName = 'order_' . $year . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $uploadDir = FCPATH . 'uploads/orders/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $file->move($uploadDir, $newName);
            $filePath = 'uploads/orders/' . $newName;
        }

        $status = $this->request->getPost('status') ?: 'published';

        $data = [
            'doc_type'             => 'order',
            'doc_no'               => $docNo ?: $this->documentModel->getNextOrderNumber($year),
            'year'                 => $year,
            'title'                => $title,
            'content'              => (string)$this->request->getPost('content'),
            'order_effective_text' => (string)$this->request->getPost('order_effective_text'),
            'signer_name'          => (string)$this->request->getPost('signer_name'),
            'signer_position'      => (string)$this->request->getPost('signer_position'),
            'order_category'       => (string)$this->request->getPost('order_category') ?: 'บริหารทั่วไป',
            'from_org'             => 'โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์',
            'to_org'               => 'บุคลากรโรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์',
            'doc_date'             => $date,
            'file_path'            => $filePath,
            'status'               => $status,
            'created_by'           => $userId,
            'created_at'           => date('Y-m-d H:i:s'),
        ];

        $orderId = $this->documentModel->insert($data);

        // บันทึก Audit Log
        $this->logModel->log($orderId, $userId, 'CREATE_ORDER', 'สร้างคำสั่งโรงเรียน: ' . $data['doc_no'] . ' เรื่อง ' . $title);

        return redirect()->to('/admin/orders/' . $orderId)->with('success', 'บันทึกคำสั่งโรงเรียนเลขที่ ' . $data['doc_no'] . ' เรียบร้อยแล้ว');
    }

    /**
     * หน้ารายละเอียดและพรีวิวคำสั่งโรงเรียน (View / Dashboard)
     */
    public function view($id)
    {
        $order = $this->documentModel->select('documents.*, users.first_name, users.last_name, users.prefix, users.signature_img as user_sig')
                                     ->join('users', 'users.id = documents.created_by', 'left')
                                     ->where('documents.id', $id)
                                     ->where('documents.doc_type', 'order')
                                     ->first();

        if (!$order) {
            return redirect()->to('/admin/orders')->with('error', 'ไม่พบข้อมูลคำสั่งโรงเรียนที่ระบุ');
        }

        // หากยังไม่มีชื่อผู้ลงนาม หรือตกหล่น ให้ดึงข้อมูล ผอ. คนปัจจุบันจาก tb_personnel
        $defaultDirector = $this->userModel->getDefaultDirector();
        if (empty($order['signer_name']) || str_contains($order['signer_name'], 'ชาญชัย')) {
            $order['signer_name'] = $defaultDirector['full_name'];
            $order['signer_position'] = $defaultDirector['position_name'];
        }

        $userId = session()->get('user_id');
        $currentUser = $this->userModel->getUserWithDetails($userId);
        $logs = $this->logModel->getLogsForDocument($id);

        return view('orders/orders_view', [
            'order'       => $order,
            'logs'        => $logs,
            'currentUser' => $currentUser,
        ]);
    }

    /**
     * หน้าแก้ไขคำสั่งโรงเรียน
     */
    public function edit($id)
    {
        $order = $this->documentModel->where('id', $id)
                                     ->where('doc_type', 'order')
                                     ->first();

        if (!$order) {
            return redirect()->to('/admin/orders')->with('error', 'ไม่พบข้อมูลคำสั่งโรงเรียนที่ระบุ');
        }

        // ดึงข้อมูลผู้บริหารจริง (ผอ. และ รอง ผอ.) จากฐานข้อมูล tb_personnel
        $executives = $this->userModel->getExecutivePersonnelList();
        $defaultDirector = $this->userModel->getDefaultDirector();

        if (empty($order['signer_name']) || str_contains($order['signer_name'], 'ชาญชัย')) {
            $order['signer_name'] = $defaultDirector['full_name'];
            $order['signer_position'] = $defaultDirector['position_name'];
        }

        return view('orders/orders_edit', [
            'order'      => $order,
            'executives' => $executives,
        ]);
    }

    /**
     * อัปเดตข้อมูลคำสั่งโรงเรียน
     */
    public function update($id)
    {
        $userId = session()->get('user_id');
        $order = $this->documentModel->where('id', $id)->where('doc_type', 'order')->first();
        if (!$order) {
            return redirect()->to('/admin/orders')->with('error', 'ไม่พบข้อมูลคำสั่งโรงเรียน');
        }

        $title = trim((string)$this->request->getPost('title'));
        if (empty($title)) {
            return redirect()->back()->withInput()->with('error', 'กรุณาระบุเรื่องคำสั่งโรงเรียน');
        }

        $updateData = [
            'doc_no'               => (string)$this->request->getPost('doc_no') ?: $order['doc_no'],
            'year'                 => (int)$this->request->getPost('year') ?: $order['year'],
            'title'                => $title,
            'content'              => (string)$this->request->getPost('content'),
            'order_effective_text' => (string)$this->request->getPost('order_effective_text'),
            'signer_name'          => (string)$this->request->getPost('signer_name'),
            'signer_position'      => (string)$this->request->getPost('signer_position'),
            'order_category'       => (string)$this->request->getPost('order_category'),
            'doc_date'             => $this->request->getPost('doc_date') ?: $order['doc_date'],
            'status'               => $this->request->getPost('status') ?: $order['status'],
        ];

        $this->documentModel->update($id, $updateData);
        $this->logModel->log($id, $userId, 'UPDATE_ORDER', 'แก้ไขข้อมูลคำสั่งโรงเรียน: ' . $updateData['doc_no']);

        return redirect()->to('/admin/orders/' . $id)->with('success', 'แก้ไขคำสั่งโรงเรียนเรียบร้อยแล้ว');
    }

    /**
     * พิมพ์คำสั่งโรงเรียน A4 ทางการ 1:1
     */
    public function printDoc($id)
    {
        $order = $this->documentModel->where('id', $id)->where('doc_type', 'order')->first();
        if (!$order) {
            return redirect()->to('/admin/orders')->with('error', 'ไม่พบข้อมูลคำสั่งโรงเรียน');
        }

        $defaultDirector = $this->userModel->getDefaultDirector();
        if (empty($order['signer_name']) || str_contains($order['signer_name'], 'ชาญชัย')) {
            $order['signer_name'] = $defaultDirector['full_name'];
            $order['signer_position'] = $defaultDirector['position_name'];
        }

        return view('orders/orders_print', [
            'order' => $order,
        ]);
    }

    /**
     * อัปโหลดไฟล์สแกนคำสั่ง PDF ฉบับจริง
     */
    public function uploadFile($id)
    {
        $userId = session()->get('user_id');
        $order = $this->documentModel->where('id', $id)->where('doc_type', 'order')->first();
        if (!$order) {
            return redirect()->to('/admin/orders')->with('error', 'ไม่พบข้อมูลคำสั่ง');
        }

        $file = $this->request->getFile('order_file');
        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'กรุณาเลือกไฟล์ PDF คำสั่ง');
        }

        $ext = strtolower($file->getExtension());
        if ($ext !== 'pdf') {
            return redirect()->back()->with('error', 'รองรับเฉพาะไฟล์ PDF เท่านั้น');
        }

        $uploadDir = FCPATH . 'uploads/orders/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // ลบไฟล์เดิมถ้ามี
        if (!empty($order['file_path']) && file_exists(FCPATH . $order['file_path'])) {
            @unlink(FCPATH . $order['file_path']);
        }

        $newName = 'order_' . $order['year'] . '_' . $id . '_' . time() . '.pdf';
        $file->move($uploadDir, $newName);
        $filePath = 'uploads/orders/' . $newName;

        $this->documentModel->update($id, ['file_path' => $filePath]);
        $this->logModel->log($id, $userId, 'UPLOAD_FILE', 'อัปโหลดไฟล์คำสั่งฉบับจริง (PDF)');

        return redirect()->to('/admin/orders/' . $id)->with('success', 'อัปโหลดไฟล์คำสั่ง (PDF) เรียบร้อยแล้ว');
    }

    /**
     * ลงนาม 1-Click สำหรับผู้อำนวยการ/ผู้มีอำนาจ
     */
    public function sign($id)
    {
        $userId = session()->get('user_id');
        $user = $this->userModel->getUserWithDetails($userId);

        if (empty($user['can_sign_final'])) {
            return redirect()->back()->with('error', 'ท่านไม่มีสิทธิ์ในการลงนามคำสั่งโรงเรียนขั้นสุดท้าย');
        }

        if (empty($user['signature_img'])) {
            return redirect()->back()->with('error', 'กรุณาตั้งค่าลายเซ็นดิจิทัลในเมนูโปรไฟล์ก่อนลงนาม');
        }

        // ดึงชื่อเต็มและตำแหน่งจริงของผู้ลงนามที่กำลัง Login อยู่
        $fullName = trim(($user['prefix'] ?? '') . ($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
        if (empty($fullName)) {
            $fullName = session()->get('full_name') ?: $user['username'];
        }

        // ตำแหน่ง: ใช้ position_name จาก session หรือข้อมูลผู้ใช้งาน
        $position = session()->get('position_name') ?: ($user['position_name'] ?? 'ผู้อำนวยการสถานศึกษา');

        $this->documentModel->update($id, [
            'creator_signature' => $user['signature_img'],
            'signer_name'       => $fullName,
            'signer_position'   => $position,
            'status'            => 'published',
        ]);

        $this->logModel->log($id, $userId, 'SIGN_ORDER', 'ลงนามคำสั่งโรงเรียนด้วยลายเซ็นดิจิทัล 1-Click โดย ' . $fullName . ' (' . $position . ')');

        return redirect()->to('/admin/orders/' . $id)->with('success', 'ลงนามดิจิทัลในคำสั่งโรงเรียนเรียบร้อยแล้ว');
    }

    /**
     * ลบคำสั่งโรงเรียน
     */
    public function delete($id)
    {
        $userId = session()->get('user_id');
        $order = $this->documentModel->where('id', $id)->where('doc_type', 'order')->first();
        if (!$order) {
            return redirect()->to('/admin/orders')->with('error', 'ไม่พบข้อมูลคำสั่ง');
        }

        // ลบไฟล์แนบถ้ามี
        if (!empty($order['file_path']) && file_exists(FCPATH . $order['file_path'])) {
            @unlink(FCPATH . $order['file_path']);
        }

        $this->documentModel->delete($id);
        $this->logModel->log($id, $userId, 'DELETE_ORDER', 'ลบคำสั่งโรงเรียนเลขที่ ' . $order['doc_no']);

        return redirect()->to('/admin/orders')->with('success', 'ลบคำสั่งโรงเรียนเรียบร้อยแล้ว');
    }
}
