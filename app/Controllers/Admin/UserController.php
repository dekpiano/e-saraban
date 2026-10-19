<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\DepartmentModel;
use App\Models\PositionModel;
use App\Models\DocumentModel;
use App\Models\DocumentRouteModel;
use App\Models\TravelRequestModel;

class UserController extends BaseController
{
    protected $userModel;
    protected $deptModel;
    protected $positionModel;

    public function __construct()
    {
        $this->userModel     = new UserModel();
        $this->deptModel     = new DepartmentModel();
        $this->positionModel = new PositionModel();
        helper(['form', 'url', 'thai_date']);
    }

    public function index()
    {
        $filters = [
            'search'    => $this->request->getGet('search'),
            'role'      => $this->request->getGet('role'),
            'is_active' => $this->request->getGet('is_active'),
        ];

        // ดึงเฉพาะบัญชี Supper Admin และ Admin
        $users = $this->userModel->getAdminStaffUsers($filters);
        $stats = $this->userModel->getAdminStaffStats();

        return view('admin/users/index', [
            'users'   => $users,
            'stats'   => $stats,
            'filters' => $filters,
        ]);
    }

    public function create()
    {
        $personnelList = $this->userModel->getActivePersonnelList();

        return view('admin/users/create', [
            'personnelList' => $personnelList,
        ]);
    }

    public function store()
    {
        $rawEmail = trim((string)$this->request->getPost('email'));
        if (empty($rawEmail)) {
            $rawEmail = trim((string)$this->request->getPost('username'));
        }
        $email = (strpos($rawEmail, '@') === false) ? $rawEmail . '@skj.ac.th' : $rawEmail;

        // ตรวจสอบว่ามีอีเมลนี้แล้วหรือไม่
        $existing = $this->userModel->groupStart()
                                        ->where('email', $email)
                                        ->orWhere('username', $email)
                                    ->groupEnd()
                                    ->first();
        if ($existing) {
            return redirect()->back()->withInput()->with('errors', ['email' => 'อีเมลโรงเรียนนี้ (' . esc($email) . ') มีอยู่ในระบบแล้ว']);
        }

        $rules = [
            'role' => 'required|in_list[super_admin,admin,executive,saraban,teacher]',
        ];

        $messages = [
            'role' => ['required' => 'กรุณากำหนดสิทธิ์การใช้งาน'],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $persId = trim((string)$this->request->getPost('pers_id')) ?: null;

        if (!$persId) {
            $person = $this->userModel->findPersonnel($email);
            if ($person) {
                $persId = $person['pers_id'];
            }
        }

        $data = [
            'pers_id'   => $persId,
            'username'  => $email,
            'email'     => $email,
            'role'      => (string)$this->request->getPost('role'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
        ];

        $this->userModel->insert($data);

        return redirect()->to('/admin/users')->with('success', 'บันทึกสิทธิ์การใช้งานสำหรับ "' . esc($email) . '" เรียบร้อยแล้ว');
    }

    public function edit($id)
    {
        $user = $this->userModel->getUserWithDetails($id);
        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'ไม่พบข้อมูลผู้ใช้งานที่ต้องการแก้ไข');
        }

        $personnelList = $this->userModel->getActivePersonnelList();

        $docModel    = new DocumentModel();
        $routeModel  = new DocumentRouteModel();
        $travelModel = new TravelRequestModel();

        $stats = [
            'created_docs' => $docModel->where('created_by', $id)->countAllResults(),
            'signed_docs'  => $routeModel->where('user_id', $id)->where('status', 'signed')->countAllResults(),
            'travel_docs'  => $travelModel->where('user_id', $id)->countAllResults(),
        ];

        return view('admin/users/edit', [
            'user'          => $user,
            'personnelList' => $personnelList,
            'stats'         => $stats,
        ]);
    }

    public function update($id)
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'ไม่พบข้อมูลผู้ใช้งาน');
        }

        $rawEmail = trim((string)$this->request->getPost('email'));
        if (empty($rawEmail)) {
            $rawEmail = trim((string)$this->request->getPost('username'));
        }
        $email = (strpos($rawEmail, '@') === false) ? $rawEmail . '@skj.ac.th' : $rawEmail;

        // ตรวจสอบว่าชื่อผู้ใช้ซ้ำกับคนอื่นหรือไม่
        $existing = $this->userModel->groupStart()
                                        ->where('username', $email)
                                        ->orWhere('email', $email)
                                    ->groupEnd()
                                    ->where('id !=', $id)
                                    ->first();
        if ($existing) {
            return redirect()->back()->withInput()->with('errors', ['email' => 'อีเมลโรงเรียนนี้ (' . esc($email) . ') ถูกใช้งานโดยบัญชีอื่นแล้ว']);
        }

        $rules = [
            'role' => 'required|in_list[super_admin,admin,executive,saraban,teacher]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $currentUserId = session()->get('user_id');

        // ป้องกันไม่ให้แอดมินปิดสถานะตนเอง
        $newRole = (string)$this->request->getPost('role');
        $newIsActive = $this->request->getPost('is_active') ? 1 : 0;
        if ((int)$id === (int)$currentUserId && $newIsActive !== 1) {
            return redirect()->back()->withInput()->with('error', 'ไม่อนุญาตให้ปิดการใช้งานบัญชีของตนเอง');
        }

        $persId = trim((string)$this->request->getPost('pers_id')) ?: null;
        if (!$persId) {
            $person = $this->userModel->findPersonnel($email);
            if ($person) {
                $persId = $person['pers_id'];
            }
        }

        $data = [
            'pers_id'   => $persId ?: $user['pers_id'],
            'username'  => $email,
            'email'     => $email,
            'role'      => $newRole,
            'is_active' => $newIsActive,
        ];

        $this->userModel->update($id, $data);

        // หากกำลังแก้ไขบัญชีตนเอง ให้อัปเดต Session
        if ((int)$id === (int)$currentUserId) {
            $updatedUser = $this->userModel->getUserWithDetails($id);
            session()->set([
                'username' => $updatedUser['username'],
                'email'    => $updatedUser['email'],
                'role'     => $updatedUser['role'],
            ]);
        }

        return redirect()->to('/admin/users')->with('success', 'บันทึกการแก้ไขสิทธิ์สำหรับ "' . esc($email) . '" เรียบร้อยแล้ว');
    }

    public function toggleStatus($id)
    {
        $currentUserId = session()->get('user_id');
        if ((int)$id === (int)$currentUserId) {
            return redirect()->back()->with('error', 'ไม่อนุญาตให้ปิดการใช้งานบัญชีที่กำลังเข้าสู่ระบบอยู่');
        }

        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->back()->with('error', 'ไม่พบข้อมูลผู้ใช้');
        }

        $newStatus = ($user['is_active'] == 1) ? 0 : 1;
        $this->userModel->update($id, ['is_active' => $newStatus]);

        $statusText = $newStatus == 1 ? 'เปิดใช้งาน' : 'ระงับการใช้งาน';
        return redirect()->back()->with('success', "เปลี่ยนสถานะผู้ใช้ {$user['first_name']} เป็น {$statusText} เรียบร้อยแล้ว");
    }

    public function resetPassword($id)
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->back()->with('error', 'ไม่พบข้อมูลผู้ใช้');
        }

        $customPassword = (string)$this->request->getPost('new_password');
        $newPassword = !empty($customPassword) ? $customPassword : '123456';

        $this->userModel->update($id, [
            'password' => password_hash($newPassword, PASSWORD_BCRYPT)
        ]);

        return redirect()->back()->with('success', "รีเซตรหัสผ่านสำหรับผู้ใช้ {$user['first_name']} สำเร็จ รหัสผ่านใหม่คือ: {$newPassword}");
    }

    public function delete($id)
    {
        $currentUserId = session()->get('user_id');
        if ((int)$id === (int)$currentUserId) {
            return redirect()->back()->with('error', 'ไม่อนุญาตให้ลบบัญชีของตนเอง');
        }

        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->back()->with('error', 'ไม่พบข้อมูลผู้ใช้');
        }

        $docModel = new DocumentModel();
        $routeModel = new DocumentRouteModel();
        $travelModel = new TravelRequestModel();

        $hasCreatedDocs = $docModel->where('created_by', $id)->countAllResults();
        $hasSignedDocs  = $routeModel->where('user_id', $id)->where('status', 'signed')->countAllResults();
        $hasTravels     = $travelModel->where('user_id', $id)->countAllResults();

        if ($hasCreatedDocs > 0 || $hasSignedDocs > 0 || $hasTravels > 0) {
            return redirect()->back()->with('error', "ไม่สามารถลบเจ้าหน้าที่ \"{$user['first_name']} {$user['last_name']}\" ได้ เนื่องจากมีประวัติเอกสารหรือการลงนามในระบบ แนะนำให้ใช้การ \"ระงับการใช้งาน (ปิดสถานะ)\" แทน");
        }

        $this->userModel->delete($id);
        return redirect()->to('/admin/users')->with('success', "ลบข้อมูลเจ้าหน้าที่ {$user['first_name']} {$user['last_name']} เรียบร้อยแล้ว");
    }

    public function roles()
    {
        $stats = $this->userModel->getAdminStaffStats();
        return view('admin/users/roles', [
            'stats' => $stats,
        ]);
    }
}
