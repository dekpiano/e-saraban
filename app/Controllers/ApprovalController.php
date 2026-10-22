<?php

namespace App\Controllers;

use CodeIgniter\Database\BaseBuilder;

class ApprovalController extends BaseController
{
    private function db() { return \Config\Database::connect(); }
    private function userId(): int { return (int) session()->get('user_id'); }
    private function isAdmin(): bool { return in_array((string) session()->get('role'), ['admin', 'super_admin'], true); }

    public function index()
    {
        $db = $this->db();
        $uid = $this->userId();
        $requests = $db->query(
            "SELECT ar.*, at.name AS type_name, d.doc_no, d.title AS memo_title, u.first_name, u.last_name
             FROM approval_requests ar
             INNER JOIN approval_types at ON at.id = ar.approval_type_id
             LEFT JOIN documents d ON d.id = ar.document_id
             INNER JOIN users u ON u.id = ar.requester_id
             WHERE ar.requester_id = ?
                OR EXISTS (SELECT 1 FROM approval_request_steps s WHERE s.request_id = ar.id AND s.approver_user_id = ?)
             ORDER BY ar.created_at DESC",
            [$uid, $uid]
        )->getResultArray();
        return view('approvals/index', ['requests' => $requests]);
    }

    public function create()
    {
        $db = $this->db();
        $uid = $this->userId();
        $memos = $db->table('documents')->select('id, doc_no, title, created_at')
            ->where('doc_type', 'internal')->whereIn('status', ['routing', 'completed'])
            ->groupStart()->where('created_by', $uid);
        if ($this->isAdmin() || in_array((string) session()->get('role'), ['saraban', 'executive'], true)) {
            $memos->orWhere('id >', 0);
        }
        $memos->groupEnd()->orderBy('created_at', 'DESC');
        $selectedType = (string) $this->request->getGet('type');
        return view('approvals/create', [
            'types' => $db->table('approval_types')->where('is_active', 1)->orderBy('category')->orderBy('name')->get()->getResultArray(),
            'memos' => $memos->get()->getResultArray(),
            'selectedType' => $selectedType,
        ]);
    }

    public function store()
    {
        $db = $this->db();
        $uid = $this->userId();
        $postedDocumentId = trim((string) $this->request->getPost('document_id'));
        $documentId = $postedDocumentId !== '' ? (int) $postedDocumentId : null;
        $typeId = (int) $this->request->getPost('approval_type_id');
        $title = trim((string) $this->request->getPost('title'));
        $type = $db->table('approval_types')->where('id', $typeId)->where('is_active', 1)->get()->getRowArray();
        if (!$type || mb_strlen($title) < 3) return redirect()->back()->withInput()->with('error', 'กรุณาเลือกประเภทและกรอกเรื่องให้ครบถ้วน');

        $memo = null;
        if ((int) ($type['requires_memo'] ?? 1) === 1) {
            if (!$documentId) return redirect()->back()->withInput()->with('error', 'เอกสารประเภทนี้ต้องบันทึกข้อความและส่งเสนอให้เรียบร้อยก่อน');
            $memo = $db->table('documents')->where('id', $documentId)->where('doc_type', 'internal')->get()->getRowArray();
            if (!$memo) return redirect()->back()->withInput()->with('error', 'ไม่พบบันทึกข้อความภายในที่เลือก');
            if (!$this->isAdmin() && !in_array((string) session()->get('role'), ['saraban', 'executive'], true) && (int)$memo['created_by'] !== $uid) {
                return redirect()->back()->withInput()->with('error', 'เลือกได้เฉพาะบันทึกข้อความที่คุณเป็นผู้สร้าง');
            }
            if (in_array($memo['status'], ['draft', 'returned', 'canceled'], true)) {
                return redirect()->back()->withInput()->with('error', 'บันทึกข้อความต้องส่งเสนอหรือดำเนินการแล้วก่อนเปิดแบบฟอร์มนี้');
            }
        } else {
            $documentId = null;
        }
        $steps = $db->table('approval_type_steps ats')->select('ats.*, u.is_active')
            ->join('users u', 'u.id=ats.approver_user_id')->where('ats.approval_type_id', $typeId)
            ->where('u.is_active', 1)->orderBy('ats.step_order')->get()->getResultArray();
        if (!$steps) return redirect()->back()->withInput()->with('error', 'ประเภทเอกสารนี้ยังไม่ได้ตั้งค่าผู้พิจารณา กรุณาติดต่อผู้ดูแลระบบ');
        $db->transStart();
        $db->table('approval_requests')->insert([
            'document_id' => $documentId > 0 ? $documentId : null, 'approval_type_id' => $typeId, 'requester_id' => $uid,
            'title' => $title, 'details' => trim((string)$this->request->getPost('details')),
            'purpose' => trim((string)$this->request->getPost('purpose')),
            'location' => trim((string)$this->request->getPost('location')) ?: null,
            'start_date' => $this->request->getPost('start_date') ?: null,
            'end_date' => $this->request->getPost('end_date') ?: null,
            'amount' => is_numeric($this->request->getPost('amount')) ? (float)$this->request->getPost('amount') : 0,
            'extra_data' => json_encode(['reference_no' => trim((string)$this->request->getPost('reference_no'))], JSON_UNESCAPED_UNICODE),
            'status' => 'pending', 'current_step' => 1,
            'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $requestId = (int)$db->insertID();
        foreach ($steps as $step) {
            $db->table('approval_request_steps')->insert([
                'request_id' => $requestId, 'step_order' => $step['step_order'],
                'step_name' => $step['step_name'], 'approver_user_id' => $step['approver_user_id'],
                'status' => 'pending', 'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
        if ($documentId !== null) {
            $db->table('document_links')->ignore(true)->insert([
                'source_type' => 'document', 'source_id' => $documentId,
                'target_type' => 'approval_request', 'target_id' => $requestId,
                'relation_type' => 'approval_form', 'created_by' => $uid,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
        $db->transComplete();
        if (!$db->transStatus()) return redirect()->back()->withInput()->with('error', 'บันทึกคำขอไม่สำเร็จ กรุณาลองใหม่');
        return redirect()->to('/admin/approvals/' . $requestId)->with('success', 'บันทึกคำขอและส่งตามลำดับผู้พิจารณาแล้ว');
    }

    public function view(int $id)
    {
        $db = $this->db(); $uid = $this->userId();
        $request = $db->table('approval_requests ar')->select('ar.*, at.name AS type_name, at.category, d.doc_no, d.title AS memo_title, d.id AS memo_id, u.first_name, u.last_name')
            ->join('approval_types at', 'at.id=ar.approval_type_id')->join('documents d', 'd.id=ar.document_id', 'left')
            ->join('users u', 'u.id=ar.requester_id')->where('ar.id', $id)->get()->getRowArray();
        if (!$request) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        $steps = $db->table('approval_request_steps s')->select('s.*, u.prefix, u.first_name, u.last_name, p.name AS position_name')
            ->join('users u', 'u.id=s.approver_user_id')->join('positions p', 'p.id=u.position_id', 'left')
            ->where('s.request_id', $id)->orderBy('s.step_order')->get()->getResultArray();
        $canAct = false;
        foreach ($steps as $step) if ((int)$step['approver_user_id'] === $uid && $step['status'] === 'pending' && (int)$step['step_order'] === (int)$request['current_step']) $canAct = true;
        if ((int)$request['requester_id'] !== $uid && !$this->isAdmin() && !$canAct && !in_array($uid, array_map('intval', array_column($steps, 'approver_user_id')), true)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        return view('approvals/view', ['request' => $request, 'steps' => $steps, 'canAct' => $canAct]);
    }


    public function printBundle(int $id)
    {
        $db = $this->db();
        $uid = $this->userId();
        $request = $db->table('approval_requests ar')
            ->select('ar.*, ar.title AS request_title, at.name AS type_name, at.category,
                d.id AS memo_id, d.doc_no, d.internal_reg_no, d.year, d.title AS memo_title,
                d.from_org, d.to_org, d.doc_date, d.content AS memo_content, d.memo_department,
                d.memo_contact, d.creator_signature, d.status AS memo_status, d.created_by,
                u.prefix AS requester_prefix, u.first_name AS requester_first_name,
                u.last_name AS requester_last_name, u2.first_name AS memo_creator_first_name,
                u2.last_name AS memo_creator_last_name')
            ->join('approval_types at', 'at.id=ar.approval_type_id')
            ->join('documents d', 'd.id=ar.document_id', 'left')
            ->join('users u', 'u.id=ar.requester_id')
            ->join('users u2', 'u2.id=d.created_by', 'left')
            ->where('ar.id', $id)->get()->getRowArray();
        if (!$request) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        $isApprover = $db->table('approval_request_steps')->where('request_id', $id)->where('approver_user_id', $uid)->countAllResults() > 0;
        if ((int)$request['requester_id'] !== $uid && (int)$request['created_by'] !== $uid && !$isApprover && !$this->isAdmin()) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        $steps = $db->table('approval_request_steps s')
            ->select('s.*, u.prefix, u.first_name, u.last_name, p.name AS position_name')
            ->join('users u', 'u.id=s.approver_user_id')
            ->join('positions p', 'p.id=u.position_id', 'left')
            ->where('s.request_id', $id)->orderBy('s.step_order')->get()->getResultArray();
        return view('approvals/print_bundle', ['request' => $request, 'steps' => $steps]);
    }

    public function decide(int $id)
    {
        $db = $this->db(); $uid = $this->userId();
        $action = (string)$this->request->getPost('action');
        if (!in_array($action, ['approve', 'return', 'reject'], true)) return redirect()->back()->with('error', 'คำสั่งไม่ถูกต้อง');
        $db->transStart();
        $req = $db->table('approval_requests')->where('id', $id)->get()->getRowArray();
        $step = $db->table('approval_request_steps')->where('request_id', $id)->where('step_order', (int)($req['current_step'] ?? 0))->where('approver_user_id', $uid)->where('status', 'pending')->get()->getRowArray();
        if (!$req || !$step || $req['status'] !== 'pending') {
            $db->transRollback();
            return redirect()->back()->with('error', 'รายการนี้ไม่ได้อยู่ในขั้นตอนที่คุณมีสิทธิ์พิจารณา');
        }
        $comment = trim((string)$this->request->getPost('comment'));
        $stepStatus = ['approve'=>'approved', 'return'=>'returned', 'reject'=>'rejected'][$action];
        $db->table('approval_request_steps')->where('id', $step['id'])->update(['status'=>$stepStatus, 'comment'=>$comment, 'action_at'=>date('Y-m-d H:i:s')]);
        if ($action === 'approve') {
            $next = $db->table('approval_request_steps')->where('request_id', $id)->where('status', 'pending')->where('step_order >', (int)$step['step_order'])->orderBy('step_order')->get()->getRowArray();
            if ($next) $db->table('approval_requests')->where('id', $id)->update(['current_step'=>$next['step_order'], 'updated_at'=>date('Y-m-d H:i:s')]);
            else $db->table('approval_requests')->where('id', $id)->update(['status'=>'approved', 'updated_at'=>date('Y-m-d H:i:s')]);
        } else {
            $db->table('approval_requests')->where('id', $id)->update(['status'=>$action === 'return' ? 'returned' : 'rejected', 'updated_at'=>date('Y-m-d H:i:s')]);
        }
        $db->transComplete();
        return redirect()->to('/admin/approvals/' . $id)->with('success', 'บันทึกผลการพิจารณาแล้ว');
    }

    public function settings()
    {
        if (!$this->isAdmin()) return redirect()->to('/admin')->with('error', 'เฉพาะผู้ดูแลระบบ');
        $db = $this->db();
        $types = $db->table('approval_types')->orderBy('category')->orderBy('name')->get()->getResultArray();
        $users = $db->table('users')->select('users.id, users.prefix, users.first_name, users.last_name, positions.name AS position_name')
            ->join('positions', 'positions.id=users.position_id', 'left')->where('users.is_active', 1)->orderBy('positions.level')->orderBy('users.first_name')->get()->getResultArray();
        $stepRows = $db->table('approval_type_steps')->orderBy('approval_type_id')->orderBy('step_order')->get()->getResultArray();
        $steps = [];
        foreach ($stepRows as $step) $steps[$step['approval_type_id']][$step['step_order']] = $step;
        return view('approvals/settings', ['types'=>$types, 'users'=>$users, 'steps'=>$steps]);
    }

    public function saveSettings()
    {
        if (!$this->isAdmin()) return redirect()->to('/admin')->with('error', 'เฉพาะผู้ดูแลระบบ');
        $db = $this->db();
        $submitted = $this->request->getPost('approvers') ?? [];
        $memoRequirements = $this->request->getPost('requires_memo') ?? [];
        $db->transStart();
        foreach ($db->table('approval_types')->select('id')->get()->getResultArray() as $type) {
            $typeId = (int)$type['id'];
            $db->table('approval_types')->where('id', $typeId)->update([
                'requires_memo' => isset($memoRequirements[$typeId]) ? 1 : 0,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $db->table('approval_type_steps')->where('approval_type_id', $typeId)->delete();
            $names = [1 => 'หัวหน้างาน/หัวหน้ากลุ่มงาน', 2 => 'รองผู้อำนวยการ/ผู้รับผิดชอบ', 3 => 'ผู้อนุมัติ'];
            foreach ([1,2,3] as $order) {
                $approver = (int)($submitted[$typeId][$order] ?? 0);
                if ($approver > 0 && $db->table('users')->where('id', $approver)->where('is_active', 1)->countAllResults() > 0) {
                    $db->table('approval_type_steps')->insert([
                        'approval_type_id'=>$typeId, 'step_order'=>$order, 'step_name'=>$names[$order],
                        'approver_user_id'=>$approver, 'created_at'=>date('Y-m-d H:i:s'),
                    ]);
                }
            }
        }
        $db->transComplete();
        if (!$db->transStatus()) return redirect()->back()->with('error', 'บันทึกการตั้งค่าไม่สำเร็จ');
        return redirect()->to('/admin/approvals/settings')->with('success', 'บันทึกผู้พิจารณาแต่ละประเภทเอกสารแล้ว');
    }
}
