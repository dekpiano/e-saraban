<?php

namespace App\Controllers;

use App\Models\TravelRequestModel;
use App\Models\TravelLogModel;
use App\Models\UserModel;
use App\Models\DepartmentModel;

class TravelReportController extends BaseController
{
    protected $travelModel;
    protected $logModel;
    protected $userModel;
    protected $deptModel;

    public function __construct()
    {
        $this->travelModel = new TravelRequestModel();
        $this->logModel    = new TravelLogModel();
        $this->userModel   = new UserModel();
        $this->deptModel   = new DepartmentModel();
        helper(['thai_date', 'form', 'url']);
    }

    /**
     * ทะเบียนรายงานผลการไปราชการ
     * - หาก Login แล้ว: แสดงเฉพาะของตัวเองเท่านั้น (user_id = $userId)
     * - หากไม่ได้ Login: แสดงเฉพาะรายงานที่ส่งผลแล้ว (status = reported) ในโหมดดูได้อย่างเดียว จัดการไม่ได้
     */
    public function index()
    {
        $isLoggedIn  = (bool)session()->get('logged_in');
        $userId      = session()->get('user_id');
        $sessionRole = session()->get('role');
        $isOfficer   = in_array($sessionRole, ['super_admin', 'admin', 'saraban', 'executive'], true);

        // ถ้า Login เข้ามา: เห็นเฉพาะของตัวเองเป็นหลัก (หากเป็น Officer และเลือกดูของทุกคน scope=all จึงจะเห็นทั้งหมด)
        $scope      = $this->request->getGet('scope');
        $showMyOnly = $isLoggedIn && !($isOfficer && $scope === 'all');

        $tab    = $this->request->getGet('tab') ?: 'all'; // all, reported, pending_report
        $search = trim($this->request->getGet('search') ?: '');

        $query = $this->travelModel->select('travel_requests.*, users.prefix, users.first_name, users.last_name, positions.name as position_name, departments.name as department_name, p.pers_prefix, p.pers_firstname, p.pers_lastname, pos.posi_name as personnel_posi_name, l.lear_namethai as personnel_dept_name')
                                   ->join('users', 'users.id = travel_requests.user_id', 'left')
                                   ->join('positions', 'positions.id = users.position_id', 'left')
                                   ->join('departments', 'departments.id = users.department_id', 'left')
                                   ->join('skjacth_personnel.tb_personnel p', "p.pers_id = users.pers_id OR p.pers_username = users.email OR p.pers_username = users.username", 'left')
                                   ->join('skjacth_skj.tb_position pos', 'pos.posi_id = p.pers_position', 'left')
                                   ->join('skjacth_skj.tb_learning l', 'l.lear_id = p.pers_learning', 'left');

        if ($isLoggedIn) {
            if ($showMyOnly) {
                // เห็นเฉพาะของตัวเองเท่านั้น
                $query->where('travel_requests.user_id', $userId)
                      ->whereIn('travel_requests.status', ['approved', 'reported']);
            } else {
                $query->whereIn('travel_requests.status', ['approved', 'reported']);
            }

            if ($tab === 'reported') {
                $query->where('travel_requests.status', 'reported');
            } elseif ($tab === 'pending_report') {
                $query->where('travel_requests.status', 'approved');
            }
        } else {
            // ถ้าไม่ได้ Login: แค่เห็นอย่างเดียว จัดการอะไรไม่ได้ (แสดงเฉพาะรายการที่รายงานผลแล้ว)
            $query->where('travel_requests.status', 'reported');
        }

        if (!empty($search)) {
            $query->groupStart()
                  ->like('travel_requests.doc_no', $search)
                  ->orLike('travel_requests.title', $search)
                  ->orLike('travel_requests.destination', $search)
                  ->orLike('travel_requests.province', $search)
                  ->orLike('users.first_name', $search)
                  ->orLike('users.last_name', $search)
                  ->orLike('p.pers_firstname', $search)
                  ->orLike('p.pers_lastname', $search)
                  ->groupEnd();
        }

        $reports = $query->orderBy('travel_requests.updated_at', 'DESC')->findAll();

        foreach ($reports as &$r) {
            if (!empty($r['pers_firstname'])) {
                $r['prefix']          = $r['pers_prefix'];
                $r['first_name']      = $r['pers_firstname'];
                $r['last_name']       = $r['pers_lastname'];
                $r['position_name']   = !empty($r['personnel_posi_name']) ? $r['personnel_posi_name'] : ($r['position_name'] ?: 'บุคลากร');
                $r['department_name'] = !empty($r['personnel_dept_name']) ? ('กลุ่มสาระฯ ' . $r['personnel_dept_name']) : ($r['department_name'] ?: 'สำนักงาน');
            }
        }
        unset($r);

        // คำนวณสถิติ
        if ($isLoggedIn) {
            $statsQuery = $this->travelModel->whereIn('status', ['approved', 'reported']);
            if ($showMyOnly) {
                $statsQuery->where('user_id', $userId);
            }
            $allStats   = $statsQuery->findAll();
            $totalApproved = count($allStats);
            $totalReported = count(array_filter($allStats, fn($item) => $item['status'] === 'reported'));
            $totalPending  = $totalApproved - $totalReported;
            $percentReported = $totalApproved > 0 ? round(($totalReported / $totalApproved) * 100, 1) : 0;
        } else {
            $publicStats = $this->travelModel->where('status', 'reported')->findAll();
            $totalReported = count($publicStats);
            $totalApproved = $totalReported;
            $totalPending  = 0;
            $percentReported = 100;
        }

        return view('travel_reports/index', [
            'reports'            => $reports,
            'currentTab'         => $tab,
            'search'             => $search,
            'totalApproved'      => $totalApproved,
            'totalReported'      => $totalReported,
            'totalPending'       => $totalPending,
            'percentReported'    => $percentReported,
            'isLoggedIn'         => $isLoggedIn,
            'isOfficer'          => $isOfficer,
            'showMyOnly'         => $showMyOnly,
            'scope'              => $scope,
        ]);
    }

    /**
     * รายงานผลการไปราชการเฉพาะของฉัน
     */
    public function my()
    {
        $userId = session()->get('user_id');
        $tab    = $this->request->getGet('tab') ?: 'all';
        $search = trim($this->request->getGet('search') ?: '');

        $query = $this->travelModel->select('travel_requests.*, users.prefix, users.first_name, users.last_name, positions.name as position_name, departments.name as department_name, p.pers_prefix, p.pers_firstname, p.pers_lastname, pos.posi_name as personnel_posi_name, l.lear_namethai as personnel_dept_name')
                                   ->join('users', 'users.id = travel_requests.user_id', 'left')
                                   ->join('positions', 'positions.id = users.position_id', 'left')
                                   ->join('departments', 'departments.id = users.department_id', 'left')
                                   ->join('skjacth_personnel.tb_personnel p', "p.pers_id = users.pers_id OR p.pers_username = users.email OR p.pers_username = users.username", 'left')
                                   ->join('skjacth_skj.tb_position pos', 'pos.posi_id = p.pers_position', 'left')
                                   ->join('skjacth_skj.tb_learning l', 'l.lear_id = p.pers_learning', 'left')
                                   ->where('travel_requests.user_id', $userId)
                                   ->whereIn('travel_requests.status', ['approved', 'reported']);

        if ($tab === 'reported') {
            $query->where('travel_requests.status', 'reported');
        } elseif ($tab === 'pending_report') {
            $query->where('travel_requests.status', 'approved');
        }

        if (!empty($search)) {
            $query->groupStart()
                  ->like('travel_requests.doc_no', $search)
                  ->orLike('travel_requests.title', $search)
                  ->orLike('travel_requests.destination', $search)
                  ->orLike('travel_requests.province', $search)
                  ->groupEnd();
        }

        $reports = $query->orderBy('travel_requests.updated_at', 'DESC')->findAll();

        foreach ($reports as &$r) {
            if (!empty($r['pers_firstname'])) {
                $r['prefix']          = $r['pers_prefix'];
                $r['first_name']      = $r['pers_firstname'];
                $r['last_name']       = $r['pers_lastname'];
                $r['position_name']   = !empty($r['personnel_posi_name']) ? $r['personnel_posi_name'] : ($r['position_name'] ?: 'บุคลากร');
                $r['department_name'] = !empty($r['personnel_dept_name']) ? ('กลุ่มสาระฯ ' . $r['personnel_dept_name']) : ($r['department_name'] ?: 'สำนักงาน');
            }
        }
        unset($r);

        $myStats = $this->travelModel->where('user_id', $userId)->whereIn('status', ['approved', 'reported'])->findAll();
        $totalApproved = count($myStats);
        $totalReported = count(array_filter($myStats, fn($item) => $item['status'] === 'reported'));
        $totalPending  = $totalApproved - $totalReported;
        $percentReported = $totalApproved > 0 ? round(($totalReported / $totalApproved) * 100, 1) : 0;

        return view('travel_reports/index', [
            'reports'            => $reports,
            'currentTab'         => $tab,
            'search'             => $search,
            'totalApproved'      => $totalApproved,
            'totalReported'      => $totalReported,
            'totalPending'       => $totalPending,
            'percentReported'    => $percentReported,
            'isOfficer'          => false,
            'isMyOnly'           => true,
        ]);
    }

    /**
     * หน้าเขียน/ยื่นรายงานผลการไปราชการ
     */
    public function create()
    {
        $userId      = session()->get('user_id');
        $sessionRole = session()->get('role');
        $isOfficer   = in_array($sessionRole, ['super_admin', 'admin', 'saraban', 'executive'], true);

        $travelId = (int)$this->request->getGet('travel_id');
        $memoId = (int)$this->request->getGet('memo_id');
        $memo = null;
        if ($memoId > 0) {
            $memo = (new \App\Models\DocumentModel())->find($memoId);
            if (!$memo || ($memo['doc_type'] ?? '') !== 'internal' || (!$isOfficer && (int)$memo['created_by'] !== (int)$userId)) {
                return redirect()->to('/admin/travel-reports/create' . ($travelId > 0 ? '?travel_id=' . $travelId : ''))->with('error', 'ไม่พบบันทึกข้อความรายงานผลที่ถูกต้อง หรือคุณไม่มีสิทธิ์ใช้เอกสารนี้');
            }
        }
        $selectedTravel = null;

        // ดึงรายการคำขอที่อนุมัติแล้ว และรอรายงานผล
        $query = $this->travelModel->where('status', 'approved');
        if (!$isOfficer) {
            $query->where('user_id', $userId);
        }
        $pendingRequests = $query->orderBy('start_date', 'DESC')->findAll();

        if ($travelId > 0) {
            $selectedTravel = $this->travelModel->getRequestWithDetails($travelId);
            if (!$selectedTravel) {
                return redirect()->to('/admin/travel-reports/create')->with('error', 'ไม่พบข้อมูลคำขอไปราชการที่ระบุ');
            }
            if (!$isOfficer && (int)$selectedTravel['user_id'] !== (int)$userId) {
                return redirect()->to('/admin/travel-reports')->with('error', 'คุณไม่มีสิทธิ์รายงานผลคำขอนี้');
            }
            if (!in_array($selectedTravel['status'], ['approved', 'reported'], true)) {
                return redirect()->to('/admin/travel-reports')->with('error', 'คำขอนี้ยังไม่ได้รับอนุมัติ จึงยังไม่สามารถส่งรายงานผลได้');
            }

            // หากคำขอที่เลือกไม่อยู่ใน $pendingRequests (เช่น มีสถานะ reported อยู่แล้ว แต่อยากส่งหรือแก้)
            $found = false;
            foreach ($pendingRequests as $p) {
                if ((int)$p['id'] === $travelId) { $found = true; break; }
            }
            if (!$found) {
                $pendingRequests[] = $selectedTravel;
            }
        }

        if ($travelId > 0 && $memoId <= 0) {
            return redirect()->to('/admin/memos/create?purpose=travel_report&travel_id=' . $travelId)->with('warning', 'ก่อนบันทึกรายงานผล กรุณาสร้างบันทึกข้อความรายงานผลก่อน');
        }

        return view('travel_reports/create', [
            'pendingRequests' => $pendingRequests,
            'isOfficer'       => $isOfficer,
            'memoId'          => $memoId,
            'memo'            => $memo,
        ] + ['selectedTravel' => $selectedTravel]);
    }

    /**
     * API AJAX สำหรับดึงรายละเอียดคำขอเมื่อผู้ใช้เลือกจาก Dropdown
     */
    public function getTravelInfo($id)
    {
        $travel = $this->travelModel->getRequestWithDetails($id);
        if (!$travel) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่พบข้อมูล'])->setStatusCode(404);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => [
                'id'          => $travel['id'],
                'doc_no'      => $travel['doc_no'],
                'title'       => $travel['title'],
                'destination' => $travel['destination'],
                'province'    => $travel['province'],
                'start_date'  => thai_date($travel['start_date'], 'short'),
                'end_date'    => thai_date($travel['end_date'], 'short'),
                'total_days'  => $travel['total_days'],
                'requester'   => $travel['prefix'] . $travel['first_name'] . ' ' . $travel['last_name'],
                'position'    => $travel['position_name'],
                'companions'  => $travel['companions'] ?: '',
                'budget_type' => $travel['budget_type'],
                'budget_amount' => number_format((float)$travel['budget_amount'], 2),
                'file_url'    => !empty($travel['file_path']) ? document_url($travel['file_path']) : null,
            ]
        ]);
    }

    /**
     * บันทึกรายงานผลการไปราชการ
     */
    public function store()
    {
        $userId      = session()->get('user_id');
        $sessionRole = session()->get('role');
        $isOfficer   = in_array($sessionRole, ['super_admin', 'admin', 'saraban', 'executive'], true);

        $travelId = (int)$this->request->getPost('travel_id');
        if ($travelId <= 0) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'กรุณาเลือกคำขอไปราชการที่ต้องการส่งรายงานผล'])->setStatusCode(400);
            }
            return redirect()->back()->withInput()->with('error', 'กรุณาเลือกคำขอไปราชการที่ต้องการส่งรายงานผล');
        }

        $travel = $this->travelModel->find($travelId);
        if (!$travel) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่พบข้อมูลคำขอไปราชการ'])->setStatusCode(404);
            }
            return redirect()->back()->withInput()->with('error', 'ไม่พบข้อมูลคำขอไปราชการ');
        }

        if (!$isOfficer && (int)$travel['user_id'] !== (int)$userId) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'คุณไม่มีสิทธิ์รายงานผลสำหรับคำขอนี้'])->setStatusCode(403);
            }
            return redirect()->to('/admin/travel-reports')->with('error', 'คุณไม่มีสิทธิ์รายงานผลสำหรับคำขอนี้');
        }

        if (!in_array($travel['status'], ['approved', 'reported'], true)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'คำขอนี้ยังไม่ได้รับอนุมัติ จึงยังไม่สามารถส่งรายงานผลได้'])->setStatusCode(400);
            }
            return redirect()->back()->withInput()->with('error', 'คำขอนี้ยังไม่ได้รับอนุมัติ จึงยังไม่สามารถส่งรายงานผลได้');
        }

        $memoId = (int)$this->request->getPost('memo_id');
        $memo = $memoId > 0 ? (new \App\Models\DocumentModel())->find($memoId) : null;
        if (!$memo || ($memo['doc_type'] ?? '') !== 'internal' || (!$isOfficer && (int)$memo['created_by'] !== (int)$userId)) {
            $message = 'กรุณาจัดทำและบันทึกข้อความรายงานผลก่อนบันทึกรายงานผล';
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => $message, 'redirect' => base_url('admin/memos/create?purpose=travel_report&travel_id=' . $travelId)])->setStatusCode(400);
            }
            return redirect()->to('/admin/memos/create?purpose=travel_report&travel_id=' . $travelId)->with('error', $message);
        }
        $dbForMemo = \Config\Database::connect();
        if (!$dbForMemo->tableExists('document_links')) {
            return redirect()->back()->withInput()->with('error', 'ยังไม่ได้ติดตั้งตารางเชื่อมโยงเอกสาร กรุณาติดตั้ง document_links ก่อน');
        }
        $reportMemoLink = (new \App\Models\DocumentLinkModel())->where('source_type', 'document')
            ->where('source_id', (int)$memoId)
            ->where('target_type', 'travel_request')
            ->where('target_id', (int)$travelId)
            ->where('relation_type', 'report_memo')
            ->first();
        if (!$reportMemoLink) {
            $message = 'บันทึกข้อความฉบับนี้ไม่ได้จัดทำสำหรับรายงานผลของคำขอนี้ กรุณาสร้างบันทึกข้อความรายงานผลก่อน';
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => $message, 'redirect' => base_url('admin/memos/create?purpose=travel_report&travel_id=' . $travelId)])->setStatusCode(400);
            }
            return redirect()->to('/admin/memos/create?purpose=travel_report&travel_id=' . $travelId)->with('error', $message);
        }

        $reportDetail = trim($this->request->getPost('report_detail') ?: '');
        if (empty($reportDetail)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'กรุณาระบุรายละเอียดหรือสรุปผลการปฏิบัติราชการ'])->setStatusCode(400);
            }
            return redirect()->back()->withInput()->with('error', 'กรุณาระบุรายละเอียดหรือสรุปผลการปฏิบัติราชการ');
        }

        $file = $this->request->getFile('report_file');
        $reportFilePath = $travel['report_file'] ?? '';

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $ext = strtolower($file->getClientExtension());
            if ($ext !== 'pdf') {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['status' => 'error', 'message' => 'ระบบรองรับเฉพาะไฟล์เอกสารรายงานหรือเกียรติบัตรประเภท PDF (.pdf) เท่านั้น'])->setStatusCode(400);
                }
                return redirect()->back()->withInput()->with('error', 'ระบบรองรับเฉพาะไฟล์เอกสารรายงานหรือเกียรติบัตรประเภท PDF (.pdf) เท่านั้น');
            }

            $uploadResult = $this->_uploadFileToServer($file, 'saraban/travel_reports', $file->getClientName());

            if ($uploadResult['status'] === 'success') {
                if (!empty($travel['report_file']) && str_starts_with($travel['report_file'], 'saraban/')) {
                    $this->_deleteFileFromServer($travel['report_file']);
                }
                $reportFilePath = 'saraban/travel_reports/' . $uploadResult['filename'];
            } else {
                $newName = $file->getRandomName();
                $file->move(FCPATH . 'uploads/documents', $newName);
                $reportFilePath = 'uploads/documents/' . $newName;
            }
        }

        $isFirstReport = empty($travel['reported_at']) || $travel['status'] !== 'reported';

        $linkModel = new \App\Models\DocumentLinkModel();
        $existingReportLink = $linkModel->where('source_type', 'document')
            ->where('source_id', (int)$memoId)
            ->where('target_type', 'travel_request')
            ->where('target_id', (int)$travelId)
            ->where('relation_type', 'report_memo')
            ->first();
        if (!$existingReportLink) {
            $linkInserted = $linkModel->insert([
                'source_type'   => 'document',
                'source_id'     => (int)$memoId,
                'target_type'   => 'travel_request',
                'target_id'     => (int)$travelId,
                'relation_type' => 'report_memo',
                'created_by'    => (int)$userId,
                'created_at'    => date('Y-m-d H:i:s'),
            ]);
            if (!$linkInserted) {
                return redirect()->back()->withInput()->with('error', 'ไม่สามารถเชื่อมบันทึกข้อความรายงานผลได้ จึงยังไม่ได้บันทึกรายงานผล');
            }
        }

        $this->travelModel->update($travelId, [
            'status'        => 'reported',
            'report_detail' => $reportDetail,
            'report_file'   => $reportFilePath,
            'reported_at'   => $travel['reported_at'] ?: date('Y-m-d H:i:s'),
        ]);

        $logMsg = $isFirstReport
            ? 'ส่งรายงานผลการไปราชการเรียบร้อยแล้ว (อ้างถึงหนังสือขออนุมัติเลขที่ ' . $travel['doc_no'] . ')'
            : 'แก้ไขปรับปรุงข้อมูลรายงานผลการไปราชการ (อ้างถึงหนังสือขออนุมัติเลขที่ ' . $travel['doc_no'] . ')';

        $this->logModel->log($travelId, $userId, 'REPORT', $logMsg);

        $successMsg = 'บันทึกรายงานผลการไปราชการเรียบร้อยแล้ว (อ้างถึงคำขอเลขที่ ' . $travel['doc_no'] . ')';

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status'   => 'success',
                'message'  => $successMsg,
                'redirect' => base_url('admin/travel-reports/' . $travelId),
            ]);
        }

        return redirect()->to('/admin/travel-reports/' . $travelId)->with('success', $successMsg);
    }

    /**
     * หน้ารายละเอียดรายงานผลการไปราชการ
     */
    public function view($id)
    {
        $isLoggedIn  = (bool)session()->get('logged_in');
        $userId      = session()->get('user_id');
        $sessionRole = session()->get('role');
        $isOfficer   = in_array($sessionRole, ['super_admin', 'admin', 'saraban', 'executive'], true);

        $travel = $this->travelModel->getRequestWithDetails($id);
        if (!$travel) {
            return redirect()->to('/admin/travel-reports')->with('error', 'ไม่พบข้อมูลรายงานผลการไปราชการ');
        }

        // ถ้าไม่ได้ login: ให้ดูได้เฉพาะรายงานที่ส่งแล้วเท่านั้น (status = reported) และเป็นแบบ Read-Only จัดการอะไรไม่ได้
        if (!$isLoggedIn && $travel['status'] !== 'reported') {
            return redirect()->to('/login')->with('warning', 'กรุณาเข้าสู่ระบบเพื่อเข้าถึงข้อมูลคำขอนี้');
        }

        // จัดการไม่ได้ถ้าไม่ได้ login; ถ้า login แล้ว: แก้ไขได้เฉพาะของตัวเอง หรือเป็นเจ้าหน้าที่
        $canEdit = $isLoggedIn && ($isOfficer || ((int)$travel['user_id'] === (int)$userId));

        $logs = $isLoggedIn ? $this->logModel->getLogsForTravel($id) : [];
        $linkedMemos = [];
        $db = \Config\Database::connect();
        if ($db->tableExists('document_links')) {
            $linkedMemos = (new \App\Models\DocumentLinkModel())->getMemosForTravel($id);
        }

        return view('travel_reports/view', [
            'travel'     => $travel,
            'logs'       => $logs,
            'canEdit'    => $canEdit,
            'isLoggedIn' => $isLoggedIn,
            'isOfficer'  => $isOfficer,
            'linkedMemos' => $linkedMemos,
        ]);
    }

    /**
     * หน้าแก้ไขรายงานผล
     */
    public function edit($id)
    {
        $userId      = session()->get('user_id');
        $sessionRole = session()->get('role');
        $isOfficer   = in_array($sessionRole, ['super_admin', 'admin', 'saraban', 'executive'], true);

        $travel = $this->travelModel->getRequestWithDetails($id);
        if (!$travel) {
            return redirect()->to('/admin/travel-reports')->with('error', 'ไม่พบข้อมูลรายงานผลการไปราชการ');
        }

        if (!$isOfficer && (int)$travel['user_id'] !== (int)$userId) {
            return redirect()->to('/admin/travel-reports/' . $id)->with('error', 'คุณไม่มีสิทธิ์แก้ไขรายงานผลนี้');
        }

        $db = \Config\Database::connect();
        $hasReportMemo = false;
        if ($db->tableExists('document_links')) {
            foreach ((new \App\Models\DocumentLinkModel())->getMemosForTravel($id) as $linkedMemo) {
                if (($linkedMemo['relation_type'] ?? '') === 'report_memo') {
                    $hasReportMemo = true;
                    break;
                }
            }
        }
        if (!$hasReportMemo) {
            return redirect()->to('/admin/memos/create?purpose=travel_report&travel_id=' . (int)$id)->with('warning', 'ก่อนแก้ไขรายงานผล กรุณาสร้างบันทึกข้อความรายงานผลก่อน');
        }

        return view('travel_reports/edit', [
            'travel'    => $travel,
            'isOfficer' => $isOfficer,
        ]);
    }

    /**
     * บันทึกการแก้ไขรายงานผล
     */
    public function update($id)
    {
        $userId      = session()->get('user_id');
        $sessionRole = session()->get('role');
        $isOfficer   = in_array($sessionRole, ['super_admin', 'admin', 'saraban', 'executive'], true);

        $travel = $this->travelModel->find($id);
        if (!$travel) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่พบข้อมูลคำขอไปราชการ'])->setStatusCode(404);
            }
            return redirect()->to('/admin/travel-reports')->with('error', 'ไม่พบข้อมูลคำขอไปราชการ');
        }

        if (!$isOfficer && (int)$travel['user_id'] !== (int)$userId) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'คุณไม่มีสิทธิ์แก้ไขรายงานผลนี้'])->setStatusCode(403);
            }
            return redirect()->to('/admin/travel-reports/' . $id)->with('error', 'คุณไม่มีสิทธิ์แก้ไขรายงานผลนี้');
        }

        $reportDetail = trim($this->request->getPost('report_detail') ?: '');
        if (empty($reportDetail)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'กรุณาระบุรายละเอียดหรือสรุปผลการปฏิบัติราชการ'])->setStatusCode(400);
            }
            return redirect()->back()->withInput()->with('error', 'กรุณาระบุรายละเอียดหรือสรุปผลการปฏิบัติราชการ');
        }

        $file = $this->request->getFile('report_file');
        $reportFilePath = $travel['report_file'] ?? '';

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $ext = strtolower($file->getClientExtension());
            if ($ext !== 'pdf') {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['status' => 'error', 'message' => 'ระบบรองรับเฉพาะไฟล์เอกสารรายงานหรือเกียรติบัตรประเภท PDF (.pdf) เท่านั้น'])->setStatusCode(400);
                }
                return redirect()->back()->withInput()->with('error', 'ระบบรองรับเฉพาะไฟล์เอกสารรายงานหรือเกียรติบัตรประเภท PDF (.pdf) เท่านั้น');
            }

            $uploadResult = $this->_uploadFileToServer($file, 'saraban/travel_reports', $file->getClientName());

            if ($uploadResult['status'] === 'success') {
                if (!empty($travel['report_file']) && str_starts_with($travel['report_file'], 'saraban/')) {
                    $this->_deleteFileFromServer($travel['report_file']);
                }
                $reportFilePath = 'saraban/travel_reports/' . $uploadResult['filename'];
            } else {
                $newName = $file->getRandomName();
                $file->move(FCPATH . 'uploads/documents', $newName);
                $reportFilePath = 'uploads/documents/' . $newName;
            }
        }

        $this->travelModel->update($id, [
            'report_detail' => $reportDetail,
            'report_file'   => $reportFilePath,
            'reported_at'   => $travel['reported_at'] ?: date('Y-m-d H:i:s'),
        ]);

        $this->logModel->log($id, $userId, 'REPORT', 'แก้ไขปรับปรุงข้อมูลรายงานผลการไปราชการ (อ้างถึงหนังสือขออนุมัติเลขที่ ' . $travel['doc_no'] . ')');

        $successMsg = 'บันทึกการแก้ไขรายงานผลเรียบร้อยแล้ว';

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status'   => 'success',
                'message'  => $successMsg,
                'redirect' => base_url('admin/travel-reports/' . $id),
            ]);
        }

        return redirect()->to('/admin/travel-reports/' . $id)->with('success', $successMsg);
    }

    /**
     * ลบ/ยกเลิกรายงานผล (ปรับสถานะกลับเป็น approved เพื่อรอรายงานใหม่)
     */
    public function delete($id)
    {
        $userId      = session()->get('user_id');
        $sessionRole = session()->get('role');
        $isOfficer   = in_array($sessionRole, ['super_admin', 'admin', 'saraban', 'executive'], true);

        $travel = $this->travelModel->find($id);
        if (!$travel) {
            return redirect()->to('/admin/travel-reports')->with('error', 'ไม่พบข้อมูลคำขอไปราชการ');
        }

        if (!$isOfficer && (int)$travel['user_id'] !== (int)$userId) {
            return redirect()->to('/admin/travel-reports/' . $id)->with('error', 'คุณไม่มีสิทธิ์ลบรายงานผลนี้');
        }

        // ลบไฟล์รายงานผลบนเซิร์ฟเวอร์
        if (!empty($travel['report_file'])) {
            if (str_starts_with($travel['report_file'], 'saraban/')) {
                $this->_deleteFileFromServer($travel['report_file']);
            } else {
                $localPath = FCPATH . $travel['report_file'];
                if (file_exists($localPath) && is_file($localPath)) {
                    @unlink($localPath);
                }
            }
        }

        $this->travelModel->update($id, [
            'status'        => 'approved',
            'report_detail' => null,
            'report_file'   => null,
            'reported_at'   => null,
        ]);

        $this->logModel->log($id, $userId, 'REPORT_CANCEL', 'ยกเลิกรายงานผลการไปราชการ (ปรับสถานะกลับเป็นรอส่งรายงานผล)');

        return redirect()->to('/admin/travel-reports')->with('success', 'ยกเลิกรายงานผลเรียบร้อยแล้ว คำขอถูกปรับสถานะเป็นรอส่งรายงานผล');
    }

    /**
     * Helper ฟังก์ชันอัปโหลดไฟล์ไปยัง Remote Server พร้อมรองรับ Chunked Upload
     */
    private function _uploadFileToServer($file, $remotePath, $originalName)
    {
        $uploadUrl = env('upload.server.url') ?: 'https://skj.nsnpao.go.th/upload.php';
        $token     = (string) env('upload.server.token');

        $filePath   = $file->getTempName();
        $fileSize   = filesize($filePath);
        $chunkLimit = 750 * 1024;

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

                $errorMessage = $body['message'] ?? ("Server Error (Status: {$statusCode})");
                log_message('error', "Report file upload failed: {$errorMessage}");
                return ['status' => 'error', 'message' => $errorMessage];
            } catch (\Exception $e) {
                log_message('error', 'cURL Error during report upload: ' . $e->getMessage());
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
            log_message('error', 'cURL Chunk Upload Error in TravelReportController: ' . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Helper ฟังก์ชันลบไฟล์บนเซิร์ฟเวอร์หลัก
     */
    private function _deleteFileFromServer($remoteFilePath)
    {
        $deleteUrl = env('upload.server.delete.url') ?: 'https://skj.nsnpao.go.th/delete.php';
        $token     = (string) env('upload.server.token');

        try {
            $client = \Config\Services::curlrequest();
            $client->post($deleteUrl, [
                'form_params' => [
                    'path' => $remoteFilePath
                ],
                'headers' => [
                    'X-Auth-Token' => $token
                ],
                'http_errors' => false,
                'timeout'     => 15
            ]);
            return true;
        } catch (\Exception $e) {
            log_message('error', 'Error deleting file from server: ' . $e->getMessage());
            return false;
        }
    }
}
