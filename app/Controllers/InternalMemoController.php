<?php

namespace App\Controllers;

use App\Models\DocumentModel;
use App\Models\DocumentRouteModel;
use App\Models\DocumentLogModel;
use App\Models\UserModel;
use App\Models\DepartmentModel;
use App\Models\TravelRequestModel;
use App\Models\DocumentLinkModel;

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

        if (!empty($status) && in_array($status, ['routing', 'completed', 'returned', 'draft', 'pending_saraban'], true)) {
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
            'pending_saraban' => $this->documentModel->where('doc_type', 'internal')->where('status', 'pending_saraban')->countAllResults(),
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

    /** รายการบันทึกข้อความที่รอเจ้าหน้าที่สารบรรณตรวจสอบและออกเลข */
    public function sarabanInbox()
    {
        if (!in_array(session()->get('role'), ['saraban', 'admin', 'super_admin'], true)) {
            return redirect()->to('/admin/memos')->with('error', 'เฉพาะเจ้าหน้าที่สารบรรณหรือผู้ดูแลระบบเท่านั้นที่เข้าถึงรายการนี้ได้');
        }

        $memos = $this->documentModel
            ->select('documents.*, users.first_name, users.last_name, users.prefix, departments.name as creator_department')
            ->join('users', 'users.id = documents.created_by', 'left')
            ->join('departments', 'departments.id = users.department_id', 'left')
            ->where('documents.doc_type', 'internal')
            ->where('documents.status', 'pending_saraban')
            ->orderBy('documents.created_at', 'ASC')
            ->findAll();

        $defaultDirector = $this->userModel->getDefaultDirector();
        $directorUser = $this->userModel->where('pers_id', $defaultDirector['pers_id'])->first();

        $routeMap = [];
        foreach ($memos as $memo) {
            $routes = $this->routeModel->getRoutesForDocument((int)$memo['id']);
            // ตรวจสอบว่าเคยมีการกดบันทึกปรับเส้นทางเองหรือไม่
            $hasManualUpdate = $this->logModel
                ->where('document_id', (int)$memo['id'])
                ->where('action', 'UPDATE_ROUTE')
                ->countAllResults();

            // ค่าเริ่มต้น: แสดงและกำหนดเฉพาะ ผอ. คนเดียวก่อนเสมอ
            if (empty($routes) || ($hasManualUpdate === 0 && count($routes) !== 1)) {
                $this->routeModel->where('document_id', (int)$memo['id'])->delete();
                $this->routeModel->insert([
                    'document_id'        => (int)$memo['id'],
                    'step_order'         => 1,
                    'user_id'            => $directorUser['id'] ?? null,
                    'pers_id'            => $defaultDirector['pers_id'],
                    'approver_name'      => $defaultDirector['full_name'],
                    'personnel_position' => $defaultDirector['position_name'],
                    'status'             => 'pending',
                ]);
                $routes = $this->routeModel->getRoutesForDocument((int)$memo['id']);
            }
            $routeMap[(int)$memo['id']] = $routes;
        }

        return view('memos/memos_saraban_inbox', [
            'memos'           => $memos,
            'routeMap'        => $routeMap,
            'defaultDirector' => $defaultDirector,
            'allUsers'        => $this->userModel->getAllActiveUsers(),
            'allPersonnel'    => $this->userModel->getPersonnelForSelection(),
        ]);
    }

    /** เจ้าหน้าที่สารบรรณปรับเส้นทางเกษียนที่ระบบแนะนำ ก่อนออกเลข */
    public function updateRoute($id)
    {
        if (!in_array(session()->get('role'), ['saraban', 'admin', 'super_admin'], true)) {
            return redirect()->to('/admin/memos')->with('error', 'คุณไม่มีสิทธิ์ปรับเส้นทางเกษียน');
        }

        $document = $this->documentModel->where('doc_type', 'internal')->find($id);
        if (!$document || $document['status'] !== 'pending_saraban') {
            return redirect()->to('/admin/memos/saraban-inbox')->with('error', 'ปรับเส้นทางได้เฉพาะบันทึกข้อความที่รอสารบรรณตรวจสอบ');
        }

        $submitted = $this->request->getPost('route_users');
        $submitted = is_array($submitted) ? array_values(array_filter(array_map('trim', $submitted), static fn($value) => $value !== '')) : [];
        if (empty($submitted) || count($submitted) !== count(array_unique($submitted))) {
            return redirect()->to('/admin/memos/saraban-inbox')->with('error', 'กรุณาเลือกผู้เกษียนให้ครบทุกลำดับและห้ามเลือกชื่อซ้ำ');
        }

        $personnelById = [];
        foreach ($this->userModel->getPersonnelForSelection() as $person) {
            $personnelById[(string)$person['pers_id']] = $person;
        }
        foreach ($submitted as $personnelId) {
            if (!isset($personnelById[$personnelId])) {
                return redirect()->to('/admin/memos/saraban-inbox')->with('error', 'ไม่พบข้อมูลบุคลากรที่เลือก กรุณาเลือกใหม่');
            }
        }

        $db = \Config\Database::connect();
        $db->transBegin();
        $this->routeModel->where('document_id', $id)->delete();
        foreach ($submitted as $index => $personnelId) {
            $person = $personnelById[$personnelId];
            $this->routeModel->insert([
                'document_id' => (int)$id,
                'step_order' => $index + 1,
                'user_id' => !empty($person['user_id']) ? (int)$person['user_id'] : null,
                'pers_id' => $person['pers_id'],
                'approver_name' => $person['full_name'],
                'personnel_position' => $person['position_name'] ?? null,
                'personnel_learning' => $person['pers_learning'] ?? null,
                'status' => 'pending',
            ]);
        }

        $this->logModel->log((int)$id, (int)session()->get('user_id'), 'UPDATE_ROUTE', 'เจ้าหน้าที่สารบรรณปรับเส้นทางเกษียนก่อนออกเลข');
        if ($db->transStatus() === false) {
            $db->transRollback();
            return redirect()->to('/admin/memos/saraban-inbox')->with('error', 'บันทึกเส้นทางเกษียนไม่สำเร็จ');
        }
        $db->transCommit();

        return redirect()->to('/admin/memos/saraban-inbox')->with('success', 'ปรับเส้นทางเกษียนเรียบร้อยแล้ว');
    }

    /** เจ้าหน้าที่สารบรรณตรวจสอบและออกเลข ก่อนเปิดเส้นทางเกษียน */
    public function issueNumber($id)
    {
        if (!in_array(session()->get('role'), ['saraban', 'admin', 'super_admin'], true)) {
            return redirect()->to('/admin/memos')->with('error', 'คุณไม่มีสิทธิ์ออกเลขบันทึกข้อความ');
        }

        $document = $this->documentModel->where('doc_type', 'internal')->find($id);
        if (!$document || $document['status'] !== 'pending_saraban') {
            return redirect()->to('/admin/memos/saraban-inbox')->with('error', 'ไม่พบรายการที่รอออกเลข หรือรายการนี้ดำเนินการไปแล้ว');
        }

        $routes = $this->routeModel->where('document_id', $id)->orderBy('step_order', 'ASC')->findAll();
        if (empty($routes)) {
            $defaultDirector = $this->userModel->getDefaultDirector();
            $directorUser = $this->userModel->where('pers_id', $defaultDirector['pers_id'])->first();
            $this->routeModel->insert([
                'document_id'        => (int)$id,
                'step_order'         => 1,
                'user_id'            => $directorUser['id'] ?? null,
                'pers_id'            => $defaultDirector['pers_id'],
                'approver_name'      => $defaultDirector['full_name'],
                'personnel_position' => $defaultDirector['position_name'],
                'status'             => 'pending',
            ]);
            $routes = $this->routeModel->where('document_id', $id)->orderBy('step_order', 'ASC')->findAll();
        }

        $year = (int)($document['year'] ?? ((int)date('Y') + 543));
        $db = \Config\Database::connect();
        $db->transBegin();

        // นับเฉพาะรายการที่ออกเลขแล้ว ไม่รวมฉบับร่าง/รายการที่รอตรวจสอบ
        $issuedCount = $this->documentModel
            ->where('doc_type', 'internal')
            ->where('year', $year)
            ->where('doc_no IS NOT NULL', null, false)
            ->where('doc_no !=', '')
            ->countAllResults();

        $runningNo = str_pad($issuedCount + 1, 4, '0', STR_PAD_LEFT);
        $docNo = 'นว 51008.2/' . $runningNo;
        $regNo = $runningNo . '/' . $year;

        // รับค่าที่ผู้ใช้อาจระบุหรือปรับแก้จากหน้าจอ
        $customDocNo = trim((string)$this->request->getPost('doc_no'));
        if (!empty($customDocNo)) {
            $docNo = $customDocNo;
        }

        $customRegNo = trim((string)$this->request->getPost('internal_reg_no'));
        if (!empty($customRegNo)) {
            $regNo = $customRegNo;
        }

        $userId = (int)session()->get('user_id');
        $currentTime = date('H:i') . ' น.';
        $receiveTime = trim((string)$this->request->getPost('receive_stamp_time')) ?: $currentTime;
        $receiveStampX = trim((string)$this->request->getPost('receive_stamp_x')) ?: '65%';
        $receiveStampY = trim((string)$this->request->getPost('receive_stamp_y')) ?: '1.2cm';
        $receiveStampScale = (float)($this->request->getPost('receive_stamp_scale') ?: 1.0);
        $docDate = $this->request->getPost('doc_date') ?: date('Y-m-d');
        $hasReceiveStamp = ($this->request->getPost('has_receive_stamp') !== null) ? (int)$this->request->getPost('has_receive_stamp') : 1;

        $updated = $this->documentModel->update($id, [
            'doc_no'              => $docNo,
            'internal_reg_no'     => $regNo,
            'doc_date'            => $docDate,
            'receive_date'        => date('Y-m-d'),
            'status'              => 'routing',
            'current_step'        => 1,
            'has_receive_stamp'   => $hasReceiveStamp,
            'receive_stamp_x'     => $receiveStampX,
            'receive_stamp_y'     => $receiveStampY,
            'receive_stamp_scale' => $receiveStampScale,
            'receive_stamp_time'  => $receiveTime,
            'received_by'         => $userId,
            'received_at'         => date('Y-m-d H:i:s'),
        ]);

        if (!$updated || $db->transStatus() === false) {
            $db->transRollback();
            return redirect()->to('/admin/memos/' . $id)->with('error', 'ออกเลขไม่สำเร็จ กรุณาตรวจสอบฐานข้อมูลและลองใหม่');
        }

        $this->logModel->log($id, $userId, 'ISSUE_NUMBER', 'เจ้าหน้าที่สารบรรณประทับตราและออกเลขบันทึกข้อความ ' . $docNo . ' (ทะเบียนรับ ' . $regNo . ') และส่งเข้าสู่เส้นทางเกษียน');
        $db->transCommit();

        $redirectTo = '/admin/memos/' . $id;
        return redirect()->to($redirectTo)->with('success', 'ประทับตราและออกเลขที่หนังสือ ' . $docNo . ' (เลขทะเบียนรับ ' . $regNo . ') เรียบร้อยแล้ว');
    }

    /** เจ้าหน้าที่สารบรรณแก้ไขการออกเลข หรือปรับตำแหน่งตรายางรับหนังสือ */
    public function updateIssue($id)
    {
        if (!in_array(session()->get('role'), ['saraban', 'admin', 'super_admin'], true)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'คุณไม่มีสิทธิ์แก้ไขการออกเลข']);
            }
            return redirect()->to('/admin/memos/' . $id)->with('error', 'คุณไม่มีสิทธิ์แก้ไขการออกเลข');
        }

        $document = $this->documentModel->where('doc_type', 'internal')->find($id);
        if (!$document) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่พบเอกสาร']);
            }
            return redirect()->to('/admin/memos')->with('error', 'ไม่พบเอกสาร');
        }

        // หากผู้บริหารลงนามแล้ว จะไม่สามารถแก้ไขข้อมูลการออกเลขหรือตรายางได้อีกเด็ดขาด
        $hasSigned = $this->routeModel->where('document_id', $id)->where('status', 'signed')->countAllResults() > 0;
        if ($document['status'] === 'completed' || $hasSigned) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'เอกสารมีผู้บริหารลงนามแล้ว ล็อกการแก้ไขตามระเบียบงานสารบรรณ']);
            }
            return redirect()->to('/admin/memos/' . $id)->with('error', 'เอกสารมีผู้บริหารลงนามแล้ว ล็อกการแก้ไขตามระเบียบงานสารบรรณ');
        }

        $userId = (int)session()->get('user_id');
        $updateData = [];

        // เลขที่หนังสือ
        $customDocNo = trim((string)$this->request->getPost('doc_no'));
        if ($customDocNo !== '') {
            $updateData['doc_no'] = $customDocNo;
        }

        // เลขทะเบียนรับ
        $customRegNo = trim((string)$this->request->getPost('internal_reg_no'));
        if ($customRegNo !== '') {
            $updateData['internal_reg_no'] = $customRegNo;
        }

        // วันที่
        $docDate = $this->request->getPost('doc_date');
        if (!empty($docDate)) {
            if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $docDate, $m) && (int)$m[1] > 2400) {
                $docDate = ((int)$m[1] - 543) . '-' . $m[2] . '-' . $m[3];
            }
            $updateData['doc_date'] = $docDate;
        }

        // เวลา
        $receiveTime = trim((string)$this->request->getPost('receive_stamp_time'));
        if (!empty($receiveTime)) {
            $updateData['receive_stamp_time'] = $receiveTime;
        }

        // ตำแหน่งและสเกลตรายาง
        $receiveStampX = trim((string)$this->request->getPost('receive_stamp_x'));
        if (!empty($receiveStampX)) {
            $updateData['receive_stamp_x'] = $receiveStampX;
        }

        $receiveStampY = trim((string)$this->request->getPost('receive_stamp_y'));
        if (!empty($receiveStampY)) {
            $updateData['receive_stamp_y'] = $receiveStampY;
        }

        $scale = $this->request->getPost('receive_stamp_scale');
        if ($scale !== null && $scale !== '') {
            $updateData['receive_stamp_scale'] = (float)$scale;
        }

        if ($this->request->getPost('has_receive_stamp') !== null) {
            $updateData['has_receive_stamp'] = (int)$this->request->getPost('has_receive_stamp');
        }

        if (empty($updateData)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'info', 'message' => 'ไม่มีการเปลี่ยนแปลง']);
            }
            return redirect()->to('/admin/memos/' . $id)->with('info', 'ไม่มีข้อมูลที่เปลี่ยนแปลง');
        }

        $this->documentModel->update($id, $updateData);
        $this->logModel->log($id, $userId, 'EDIT_ISSUE', 'เจ้าหน้าที่สารบรรณแก้ไขข้อมูลการออกเลข/ปรับตำแหน่งตรายางรับหนังสือ');

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'อัปเดตการออกเลขและตำแหน่งตรายางเรียบร้อยแล้ว',
                'data'    => $updateData
            ]);
        }

        return redirect()->to('/admin/memos/' . $id)->with('success', 'แก้ไขข้อมูลการออกเลขและปรับตำแหน่งตรายางเรียบร้อยแล้ว');
    }

    /** เจ้าหน้าที่สารบรรณยกเลิกการออกเลข และดึงกลับมาเป็นรอออกเลขใหม่ */
    public function cancelIssue($id)
    {
        if (!in_array(session()->get('role'), ['saraban', 'admin', 'super_admin'], true)) {
            return redirect()->to('/admin/memos/' . $id)->with('error', 'คุณไม่มีสิทธิ์ยกเลิกการออกเลข');
        }

        $document = $this->documentModel->where('doc_type', 'internal')->find($id);
        if (!$document || in_array($document['status'], ['pending_saraban', 'draft'], true)) {
            return redirect()->to('/admin/memos/' . $id)->with('warning', 'เอกสารนี้ยังไม่ได้ออกเลขสารบรรณ');
        }

        // หากผู้บริหารลงนามแล้ว จะไม่สามารถยกเลิกการออกเลขได้อีกเด็ดขาด
        $hasSigned = $this->routeModel->where('document_id', $id)->where('status', 'signed')->countAllResults() > 0;
        if ($document['status'] === 'completed' || $hasSigned) {
            return redirect()->to('/admin/memos/' . $id)->with('error', 'เอกสารมีผู้บริหารลงนามแล้ว ไม่สามารถยกเลิกการออกเลขได้');
        }

        $userId = (int)session()->get('user_id');
        $db = \Config\Database::connect();
        $db->transBegin();

        // รีเซ็ตสถานะ routes ที่เคยลงนามให้กลับมาเป็น pending (หากประสงค์ให้เริ่มขั้นตอนใหม่)
        $this->routeModel->where('document_id', $id)->set([
            'status'         => 'pending',
            'signed_at'      => null,
            'signature_img'  => null,
            'comment_preset' => null,
            'comment_text'   => null,
            'verify_hash'    => null,
        ])->update();

        // รีเซ็ตเอกสารกลับเป็น pending_saraban
        $this->documentModel->update($id, [
            'status'          => 'pending_saraban',
            'current_step'    => 1,
            'doc_no'          => null,
            'internal_reg_no' => null,
            'received_by'     => null,
            'received_at'     => null,
        ]);

        $this->logModel->log($id, $userId, 'CANCEL_ISSUE', 'เจ้าหน้าที่สารบรรณยกเลิกการออกเลข ดึงบันทึกข้อความกลับสู่สถานะรอออกเลขใหม่');

        if ($db->transStatus() === false) {
            $db->transRollback();
            return redirect()->to('/admin/memos/' . $id)->with('error', 'ยกเลิกการออกเลขไม่สำเร็จ');
        }

        $db->transCommit();
        return redirect()->to('/admin/memos/' . $id)->with('success', 'ยกเลิกการออกเลขเรียบร้อยแล้ว บันทึกข้อความกลับสู่สถานะรอออกเลข');
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

        // แนะนำเลขที่บันทึกข้อความ เช่น นว 51008.2/0001
        $count = $this->documentModel->where('doc_type', 'internal')->where('year', $year)->countAllResults();
        $runningNo = str_pad($count + 1, 4, '0', STR_PAD_LEFT);
        $suggestedDocNo = 'นว 51008.2/' . $runningNo;

        $allUsers = $this->userModel->getAllActiveUsers();
        $allPersonnel = $this->userModel->getPersonnelForSelection();
        $departments = $this->deptModel->findAll();

        // เส้นทางเกษียนเริ่มต้น: กำหนดเฉพาะ ผู้อำนวยการโรงเรียน (ผอ.) คนเดียวก่อนเสมอตามมาตรฐานโรงเรียน รายชื่ออื่นสามารถเพิ่มได้ในภายหลัง
        $defaultDirector = $this->userModel->getDefaultDirector();
        $suggestedRoutePersIds = !empty($defaultDirector['pers_id']) ? [(string)$defaultDirector['pers_id']] : [];

        // Context for workflows that require an official memo before saving the business record.
        $purpose = (string)($this->request->getGet('purpose') ?? '');
        $contextTravelId = (int)($this->request->getGet('travel_id') ?? 0);
        $linkedTravel = null;
        $travelRequestId = (int)($this->request->getGet('travel_request_id') ?? 0);
        if ($contextTravelId > 0) {
            $linkedTravel = (new TravelRequestModel())->find($contextTravelId);
            if (!$linkedTravel) {
                return redirect()->to('/admin/travel')->with('error', 'ไม่พบคำขอไปราชการที่ใช้อ้างอิง');
            }
            $isOfficer = in_array(session()->get('role'), ['super_admin', 'admin', 'saraban', 'executive'], true);
            if (!$isOfficer && (int)$linkedTravel['user_id'] !== (int)$userId) {
                return redirect()->to('/admin/travel/' . $contextTravelId)->with('error', 'คุณไม่มีสิทธิ์สร้างบันทึกข้อความสำหรับรายการนี้');
            }
        }
        if ($travelRequestId > 0) {
            $linkedTravel = (new TravelRequestModel())->find($travelRequestId);
            if ($linkedTravel) {
                $isOfficer = in_array(session()->get('role'), ['super_admin', 'admin', 'saraban', 'executive'], true);
                if (!$isOfficer && (int)$linkedTravel['user_id'] !== (int)$userId) {
                    return redirect()->to('/admin/travel/' . $travelRequestId)->with('error', 'คุณไม่มีสิทธิ์สร้างบันทึกข้อความจากคำขอนี้');
                }
            }
        }

        $defaultTitle = '';
        $defaultContent = '';
        if ($linkedTravel && $purpose === 'travel_report') {
            $defaultTitle = 'รายงานผลการไปราชการ: ' . ($linkedTravel['title'] ?? '');
            $defaultContent = '<p>ตามที่ได้รับอนุมัติให้ไปราชการ เรื่อง ' . esc($linkedTravel['title'] ?? '-') . '</p>'
                . '<p>สถานที่: ' . esc($linkedTravel['destination'] ?? '-') . ' จังหวัด' . esc($linkedTravel['province'] ?? '-') . '</p>'
                . '<p>ระหว่างวันที่ ' . esc($linkedTravel['start_date'] ?? '-') . ' ถึงวันที่ ' . esc($linkedTravel['end_date'] ?? '-') . '</p>'
                . '<p>บัดนี้ ได้ดำเนินการเสร็จสิ้นแล้ว จึงขอรายงานผลการปฏิบัติราชการดังรายละเอียดต่อไปนี้</p>'
                . '<p>จึงเรียนมาเพื่อโปรดทราบ</p>';
        } elseif ($linkedTravel) {
            $defaultTitle = 'ขออนุมัติไปราชการ: ' . ($linkedTravel['title'] ?? '');
            $defaultContent = '<p>ด้วย ' . esc($user['prefix'] ?? '') . esc($user['first_name'] ?? '') . ' ' . esc($user['last_name'] ?? '') . ' มีความประสงค์ขออนุมัติไปราชการ เรื่อง ' . esc($linkedTravel['title'] ?? '') . '</p>'
                . '<p>สถานที่เดินทาง: ' . esc($linkedTravel['destination'] ?? '-') . ' จังหวัด' . esc($linkedTravel['province'] ?? '-') . '</p>'
                . '<p>ระหว่างวันที่ ' . esc($linkedTravel['start_date'] ?? '-') . ' ถึงวันที่ ' . esc($linkedTravel['end_date'] ?? '-') . ' รวม ' . esc($linkedTravel['total_days'] ?? '-') . ' วัน</p>'
                . '<p>จึงเรียนมาเพื่อโปรดพิจารณา</p>';
        } elseif ($purpose === 'travel_request') {
            $defaultTitle = 'ขออนุมัติไปราชการ';
            $defaultContent = '<p>ด้วยข้าพเจ้ามีความประสงค์ขออนุมัติเดินทางไปราชการ เพื่อ ' . '........................................................</p><p>สถานที่และกำหนดการเดินทางจะระบุในคำขอไปราชการที่เชื่อมโยงกับบันทึกข้อความฉบับนี้</p><p>จึงเรียนมาเพื่อโปรดพิจารณาอนุมัติ</p>';
        }

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
            'suggestedRoutePersIds' => $suggestedRoutePersIds,
            'allPersonnel' => $allPersonnel,
            'defaultDept'    => $defaultDept,
            'defaultContact' => $defaultContact,
            'currentYear'    => $year,
            'linkedTravel'   => $linkedTravel,
            'travelRequestId'=> $travelRequestId,
            'workflowPurpose' => $purpose,
            'contextTravelId' => $contextTravelId,
            'defaultTitle'   => $defaultTitle,
            'defaultContent' => $defaultContent,
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
            'from_org'  => 'required|max_length[200]',
            'to_org'    => 'required|max_length[200]',
            'doc_date'  => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'กรุณากรอกข้อมูลบันทึกข้อความให้ครบถ้วน');
        }

        // เลขที่หนังสือฉบับจริงต้องออกโดยเจ้าหน้าที่สารบรรณหลังตรวจสอบแล้ว
        $docNo       = null;
        $regNo       = null;
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

        $linkedTravelId = (int)($this->request->getPost('travel_request_id') ?? 0);
        $workflowPurpose = (string)($this->request->getPost('workflow_purpose') ?? '');
        $contextTravelId = (int)($this->request->getPost('context_travel_id') ?? 0);
        if ($contextTravelId > 0) {
            $contextTravel = (new TravelRequestModel())->find($contextTravelId);
            $isOfficer = in_array(session()->get('role'), ['super_admin', 'admin', 'saraban', 'executive'], true);
            if (!$contextTravel || (!$isOfficer && (int)$contextTravel['user_id'] !== (int)$userId)) {
                return redirect()->back()->withInput()->with('error', 'ไม่พบรายการอ้างอิงหรือคุณไม่มีสิทธิ์สร้างบันทึกข้อความสำหรับรายการนี้');
            }
            if (!in_array($workflowPurpose, ['travel_request', 'travel_report'], true)) {
                return redirect()->back()->withInput()->with('error', 'ประเภทการดำเนินงานไม่ถูกต้อง');
            }
        }
        if (in_array($workflowPurpose, ['travel_request', 'travel_report'], true) && !\Config\Database::connect()->tableExists('document_links')) {
            return redirect()->back()->withInput()->with('error', 'ยังไม่ได้ติดตั้งตารางเชื่อมโยงเอกสาร กรุณาติดตั้ง document_links ก่อน');
        }
        if ($linkedTravelId > 0) {
            $dbCheck = \Config\Database::connect();
            if (!$dbCheck->tableExists('document_links')) {
                return redirect()->back()->withInput()->with('error', 'ยังไม่ได้ติดตั้งตารางเชื่อมโยงเอกสาร กรุณารัน SQL docker/document_links.sql ก่อน');
            }
            $travelRequest = (new TravelRequestModel())->find($linkedTravelId);
            if (!$travelRequest) {
                return redirect()->back()->withInput()->with('error', 'ไม่พบคำขอไปราชการต้นทาง');
            }
            $isOfficer = in_array(session()->get('role'), ['super_admin', 'admin', 'saraban', 'executive'], true);
            if (!$isOfficer && (int)$travelRequest['user_id'] !== (int)$userId) {
                return redirect()->back()->withInput()->with('error', 'คุณไม่มีสิทธิ์เชื่อมบันทึกข้อความกับคำขอไปราชการนี้');
            }
        }

        // การส่งเรื่อง (Draft หรือ ส่งให้สารบรรณ pending_saraban)
        $action = $this->request->getPost('submit_action') ?: 'route';
        if (!in_array($action, ['draft', 'route'], true)) {
            return redirect()->back()->withInput()->with('error', 'รูปแบบการบันทึกไม่ถูกต้อง');
        }
        $status = ($action === 'draft') ? 'draft' : 'pending_saraban';

        // เส้นทางเกษียน: ผู้จัดทำไม่จำเป็นต้องเลือก เจ้าหน้าที่สารบรรณจะเป็นผู้กำหนด/ปรับเส้นทางในขั้นตอนตรวจรับ
        $submittedRouteUsers = $this->request->getPost('route_users');
        $submittedRouteUsers = is_array($submittedRouteUsers) ? array_values(array_filter(array_map('trim', $submittedRouteUsers), static fn($value) => $value !== '')) : [];
        if (!empty($submittedRouteUsers)) {
            if (count($submittedRouteUsers) !== count(array_unique($submittedRouteUsers))) {
                return redirect()->back()->withInput()->with('error', 'ไม่สามารถกำหนดผู้รับเรื่องคนเดิมซ้ำหลายลำดับได้');
            }
            $personnelIds = array_column($this->userModel->getPersonnelForSelection(), 'pers_id');
            foreach ($submittedRouteUsers as $personnelId) {
                if (!in_array($personnelId, $personnelIds, true)) {
                    return redirect()->back()->withInput()->with('error', 'พบข้อมูลบุคลากรที่เลือกไม่ถูกต้อง กรุณาเลือกผู้รับเรื่องใหม่');
                }
            }
        }

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

        // Do not create route/log rows unless the main document insert succeeded.
        if (!$docId) {
            $db->transRollback();
            log_message('error', 'Internal memo insert failed: ' . json_encode($this->documentModel->errors(), JSON_UNESCAPED_UNICODE));
            return redirect()->back()->withInput()->with('error', 'บันทึกบันทึกข้อความไม่สำเร็จ กรุณาตรวจสอบข้อมูลและลองใหม่');
        }

        if ($docId && $linkedTravelId > 0) {
            (new DocumentLinkModel())->insert([
                'source_type'  => 'travel_request',
                'source_id'    => $linkedTravelId,
                'target_type'  => 'document',
                'target_id'    => (int)$docId,
                'relation_type'=> 'created_from',
                'created_by'   => (int)$userId,
                'created_at'   => date('Y-m-d H:i:s'),
            ]);
        }

        if ($docId && $workflowPurpose === 'travel_request') {
            $pendingLinkId = (new DocumentLinkModel())->insert([
                'source_type'   => 'document',
                'source_id'     => (int)$docId,
                'target_type'   => 'travel_request',
                'target_id'     => 0,
                'relation_type' => 'request_memo',
                'created_by'    => (int)$userId,
                'created_at'    => date('Y-m-d H:i:s'),
            ]);
            if (!$pendingLinkId) {
                $db->transRollback();
                return redirect()->back()->withInput()->with('error', 'ไม่สามารถเตรียมการเชื่อมบันทึกข้อความกับคำขอได้');
            }
        } elseif ($docId && $workflowPurpose === 'travel_report' && $contextTravelId > 0) {
            $reportLinkId = (new DocumentLinkModel())->insert([
                'source_type'   => 'document',
                'source_id'     => (int)$docId,
                'target_type'   => 'travel_request',
                'target_id'     => (int)$contextTravelId,
                'relation_type' => 'report_memo',
                'created_by'    => (int)$userId,
                'created_at'    => date('Y-m-d H:i:s'),
            ]);
            if (!$reportLinkId) {
                $db->transRollback();
                return redirect()->back()->withInput()->with('error', 'ไม่สามารถเชื่อมบันทึกข้อความรายงานผลกับคำขอต้นเรื่องได้');
            }
        }

        // กำหนดเส้นทางเดินเรื่องจากรหัสบุคลากรใน tb_personnel
        $routePersonnelIds = $this->request->getPost('route_users');
        if (is_array($routePersonnelIds)) {
            $personnelById = [];
            foreach ($this->userModel->getPersonnelForSelection() as $person) {
                $personnelById[(string)$person['pers_id']] = $person;
            }

            $routePersonnelIds = array_values(array_filter(array_map('trim', $routePersonnelIds), static fn($id) => $id !== ''));
            if (count($routePersonnelIds) !== count(array_unique($routePersonnelIds))) {
                $db->transRollback();
                return redirect()->back()->withInput()->with('error', 'ผู้เกษียนแต่ละลำดับต้องไม่ซ้ำกัน');
            }

            $step = 1;
            foreach ($routePersonnelIds as $personnelId) {
                if (!isset($personnelById[$personnelId])) {
                    $db->transRollback();
                    return redirect()->back()->withInput()->with('error', 'ไม่พบข้อมูลบุคลากรที่เลือก กรุณารีเฟรชหน้าแล้วเลือกใหม่');
                }
                $person = $personnelById[$personnelId];
                $this->routeModel->insert([
                    'document_id' => $docId,
                    'step_order' => $step++,
                    'user_id' => !empty($person['user_id']) ? (int)$person['user_id'] : null,
                    'pers_id' => $person['pers_id'],
                    'approver_name' => $person['full_name'],
                    'personnel_position' => $person['position_name'] ?? null,
                    'personnel_learning' => $person['pers_learning'] ?? null,
                    'status' => 'pending',
                ]);
            }
        }

        // หากยังไม่มีเส้นทางเกษียน ให้กำหนดผู้อำนวยการโรงเรียน (ผอ.) เป็นผู้ลงนามเริ่มต้นเสมอ
        $hasRoute = $this->routeModel->where('document_id', $docId)->countAllResults();
        if ($hasRoute === 0) {
            $defaultDirector = $this->userModel->getDefaultDirector();
            $directorUser = $this->userModel->where('pers_id', $defaultDirector['pers_id'])->first();
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
        $this->logModel->log($docId, $userId, 'CREATE', 'เขียนบันทึกข้อความภายในโรงเรียนและส่งเสนอขออนุมัติ');

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'เกิดข้อผิดพลาดในการบันทึกข้อมูล กรุณาลองใหม่อีกครั้ง');
        }

        $message = ($status === 'draft')
            ? 'บันทึกฉบับร่างบันทึกข้อความเรียบร้อยแล้ว'
            : 'ส่งบันทึกข้อความให้เจ้าหน้าที่สารบรรณตรวจสอบแล้ว รอออกเลขหนังสือ';

        if ($workflowPurpose === 'travel_request') {
            return redirect()->to('/admin/travel/create?memo_id=' . (int)$docId)->with('success', 'บันทึกข้อความแล้ว กรุณากรอกข้อมูลคำขอไปราชการต่อ');
        }
        if ($workflowPurpose === 'travel_report' && $contextTravelId > 0) {
            return redirect()->to('/admin/travel-reports/create?travel_id=' . (int)$contextTravelId . '&memo_id=' . (int)$docId)->with('success', 'บันทึกข้อความรายงานผลแล้ว กรุณากรอกรายละเอียดรายงานผลต่อ');
        }

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

            if ($route['step_order'] == $document['current_step'] 
                && $isMatch
                && $route['status'] === 'pending' 
                && $document['status'] === 'routing') {
                $canSign = true;
                $currentRoute = $route;
                break;
            }
        }

        // ดึงข้อมูลผู้สร้างบันทึกข้อความ
        $creator = $this->userModel->getUserWithDetails($document['created_by']);
        $linkedTravel = null;
        $db = \Config\Database::connect();
        if ($db->tableExists('document_links')) {
            $linkedTravel = (new DocumentLinkModel())->getTravelForMemo($id);
        }

        // ข้อมูลผู้ใช้งานปัจจุบันและผู้รับหนังสือ
        $currentUser = $userId ? $this->userModel->getUserWithDetails($userId) : null;
        $receiverUser = !empty($document['received_by']) ? $this->userModel->getUserWithDetails($document['received_by']) : null;

        // คำนวณเลขที่และเลขทะเบียนรับที่ระบบแนะนำ
        $year = (int)($document['year'] ?? ((int)date('Y') + 543));
        $issuedCount = $this->documentModel
            ->where('doc_type', 'internal')
            ->where('year', $year)
            ->where('doc_no IS NOT NULL', null, false)
            ->where('doc_no !=', '')
            ->countAllResults();

        $runningNo = str_pad($issuedCount + 1, 4, '0', STR_PAD_LEFT);
        $suggestedDocNo = 'นว 51008.2/' . $runningNo;
        $suggestedRegNo = $runningNo . '/' . $year;

        return view('memos/view', [
            'document'       => $document,
            'creator'        => $creator,
            'routes'         => $routes,
            'logs'           => $logs,
            'canSign'        => $canSign,
            'currentRoute'   => $currentRoute,
            'linkedTravel'   => $linkedTravel,
            'currentUser'    => $currentUser,
            'receiverUser'   => $receiverUser,
            'suggestedDocNo' => $suggestedDocNo,
            'suggestedRegNo' => $suggestedRegNo,
        ]);
    }

    /**
     * หน้าพร้อมพิมพ์บันทึกข้อความขนาด A4 ตามมาตรฐานระเบียบสารบรรณ
     */
    public function printView($id)
    {
        $document = $this->documentModel->getDocumentDetails($id);

        if (!$document || $document['doc_type'] !== 'internal') {
            return redirect()->to('/admin/memos')->with('error', 'ไม่พบบันทึกข้อความ');
        }

        $routes  = $this->routeModel->getRoutesForDocument($id);
        $creator = $this->userModel->getUserWithDetails($document['created_by']);
        $receiverUser = !empty($document['received_by']) ? $this->userModel->getUserWithDetails($document['received_by']) : null;

        return view('memos/print', [
            'document'     => $document,
            'creator'      => $creator,
            'routes'       => $routes,
            'receiverUser' => $receiverUser,
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

        // อนุญาตให้แก้ไขได้เฉพาะเจ้าของเรื่อง (หรือผู้ดูแลระบบ) และเฉพาะฉบับร่าง/ถูกส่งคืน/รอสารบรรณตรวจสอบออกเลข
        $isPrivileged = in_array((string) session()->get('role'), ['super_admin', 'admin', 'saraban'], true);
        if ((int)$document['created_by'] !== (int)$userId && !$isPrivileged) {
            return redirect()->to('/admin/memos')->with('error', 'คุณไม่มีสิทธิ์แก้ไขบันทึกข้อความนี้');
        }
        if (!in_array($document['status'], ['draft', 'returned', 'pending_saraban'], true)) {
            return redirect()->to('/admin/memos/' . $id)->with('error', 'เอกสารที่ออกเลขหนังสือและส่งเข้าสู่การเกษียนแล้ว ไม่สามารถแก้ไขได้');
        }
        $hasSigned = $this->routeModel->where('document_id', $id)->where('status', 'signed')->countAllResults() > 0;
        if ($document['status'] === 'completed' || $hasSigned) {
            return redirect()->to('/admin/memos/' . $id)->with('error', 'เอกสารมีผู้บริหารลงนามแล้ว ไม่สามารถแก้ไขได้');
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

        // Enforce permissions on the POST endpoint too; hiding the edit link is not authorization.
        $isPrivileged = in_array((string) session()->get('role'), ['super_admin', 'admin', 'saraban'], true);
        if ((int)$document['created_by'] !== (int)$userId && !$isPrivileged) {
            return redirect()->to('/admin/memos')->with('error', 'คุณไม่มีสิทธิ์แก้ไขบันทึกข้อความนี้');
        }
        if (!in_array($document['status'], ['draft', 'returned', 'pending_saraban'], true)) {
            return redirect()->to('/admin/memos/' . $id)->with('error', 'เอกสารที่ออกเลขหนังสือและส่งเข้าสู่การเกษียนแล้ว ไม่สามารถแก้ไขได้');
        }
        $hasSigned = $this->routeModel->where('document_id', $id)->where('status', 'signed')->countAllResults() > 0;
        if ($document['status'] === 'completed' || $hasSigned) {
            return redirect()->to('/admin/memos/' . $id)->with('error', 'เอกสารมีผู้บริหารลงนามแล้ว ไม่สามารถแก้ไขได้');
        }

        $rules = [
            'title'    => 'required|min_length[3]|max_length[255]',
            'doc_no'   => 'permit_empty|max_length[100]',
            'from_org' => 'required|max_length[200]',
            'to_org'   => 'required|max_length[200]',
            'doc_date' => 'required',
        ];

        if (!$this->validate($rules)) {
            $errors = implode(', ', $this->validator->getErrors());
            return redirect()->back()->withInput()->with('error', 'กรุณากรอกข้อมูลให้ครบถ้วน: ' . $errors);
        }

        $docDate = $this->request->getPost('doc_date') ?: date('Y-m-d');
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $docDate, $m) && (int)$m[1] > 2400) {
            $docDate = ((int)$m[1] - 543) . '-' . $m[2] . '-' . $m[3];
        }

        $action = $this->request->getPost('submit_action') ?: 'route';
        // ถ้าเป็น pending_saraban และกดบันทึกส่งใหม่ ให้คงเป็น pending_saraban รอสารบรรณออกเลข
        if ($action === 'draft') {
            $newStatus = 'draft';
        } else {
            $newStatus = ($document['status'] === 'returned') ? 'routing' : 'pending_saraban';
        }

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

        $persId = (string)(session()->get('pers_id') ?? '');
        $role   = (string)(session()->get('role') ?? '');
        $isExecutiveOrAdmin = in_array($role, ['executive', 'super_admin'], true);

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
