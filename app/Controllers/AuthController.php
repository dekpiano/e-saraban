<?php

namespace App\Controllers;

use App\Models\UserModel;

class AuthController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        helper(['form', 'url']);
    }

    public function login()
    {
        if (session()->get('logged_in')) {
            return redirect()->to('/admin/dashboard');
        }

        $allUsers = $this->userModel->getAllActiveUsers();
        $personnelList = $this->userModel->getActivePersonnelList();

        return view('auth/login', [
            'users'         => $allUsers,
            'personnelList' => $personnelList,
        ]);
    }

    public function attemptLogin()
    {
        $inputEmail = trim((string)$this->request->getPost('email'));
        if (empty($inputEmail)) {
            $inputEmail = trim((string)$this->request->getPost('username'));
        }

        if (empty($inputEmail)) {
            return redirect()->back()->withInput()->with('error', 'กรุณาระบุหรือเลือกอีเมลโรงเรียน (@skj.ac.th)');
        }

        $email = (strpos($inputEmail, '@') === false) ? $inputEmail . '@skj.ac.th' : $inputEmail;
        return $this->processLoginByEmail($email);
    }

    /**
     * เปลี่ยนเส้นทางไปยังหน้าลงชื่อเข้าใช้ Google (Google OAuth 2.0)
     */
    public function googleRedirect()
    {
        $clientId = env('google.clientID');
        $redirectUri = env('google.redirectURI') ?: base_url('login/google/callback');

        if (empty($clientId)) {
            return redirect()->to('/login')->with('error', '⚠️ ยังไม่ได้ระบุ Google Client ID ในไฟล์ .env (กรุณาตั้งค่า google.clientID และ google.clientSecret จาก Google Cloud Console)');
        }

        $state = bin2hex(random_bytes(16));
        session()->set('google_oauth_state', $state);

        $params = [
            'client_id'             => $clientId,
            'redirect_uri'          => $redirectUri,
            'response_type'         => 'code',
            'scope'                 => 'openid email profile',
            'state'                 => $state,
            'access_type'           => 'online',
            'hd'                    => 'skj.ac.th', // กำหนดโดเมนโรงเรียน @skj.ac.th
            'include_granted_scopes'=> 'true',
        ];

        $authUrl = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
        return redirect()->to($authUrl);
    }

    /**
     * รับข้อมูล Callback หลังยืนยันตัวตนสำเร็จจาก Google
     */
    public function googleCallback()
    {
        $error = $this->request->getGet('error');
        if (!empty($error)) {
            return redirect()->to('/login')->with('error', 'การเข้าสู่ระบบด้วย Google ถูกยกเลิก หรือเกิดข้อผิดพลาด: ' . esc($error));
        }

        $state = (string)$this->request->getGet('state');
        $savedState = (string)session()->get('google_oauth_state');
        session()->remove('google_oauth_state');

        if (empty($state) || $state !== $savedState) {
            return redirect()->to('/login')->with('error', 'การตรวจสอบความปลอดภัยของ Google (OAuth State) ไม่ถูกต้อง กรุณาลองใหม่อีกครั้ง');
        }

        $code = (string)$this->request->getGet('code');
        if (empty($code)) {
            return redirect()->to('/login')->with('error', 'ไม่พบรหัสการยืนยันจาก Google (Authorization Code)');
        }

        $clientId = env('google.clientID');
        $clientSecret = env('google.clientSecret');
        $redirectUri = env('google.redirectURI') ?: base_url('login/google/callback');

        // แลกเปลี่ยน Authorization Code เป็น Access Token
        $client = \Config\Services::curlrequest();
        try {
            $tokenResponse = $client->post('https://oauth2.googleapis.com/token', [
                'form_params' => [
                    'code'          => $code,
                    'client_id'     => $clientId,
                    'client_secret' => $clientSecret,
                    'redirect_uri'  => $redirectUri,
                    'grant_type'    => 'authorization_code',
                ],
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'http_errors' => false,
            ]);

            $tokenData = json_decode($tokenResponse->getBody(), true);
            if (empty($tokenData['access_token'])) {
                $errDesc = $tokenData['error_description'] ?? ($tokenData['error'] ?? 'ไม่สามารถดึง Access Token ได้');
                return redirect()->to('/login')->with('error', 'Google Token Error: ' . esc($errDesc));
            }

            // ดึงข้อมูลบัญชี Google ของผู้ใช้งาน (Email, Profile)
            $userResponse = $client->get('https://www.googleapis.com/oauth2/v3/userinfo', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $tokenData['access_token'],
                    'Accept'        => 'application/json',
                ],
                'http_errors' => false,
            ]);

            $googleUser = json_decode($userResponse->getBody(), true);
            if (empty($googleUser['email'])) {
                return redirect()->to('/login')->with('error', 'ไม่สามารถอ่านข้อมูลอีเมลจากบัญชี Google ได้');
            }

            $email = trim(strtolower($googleUser['email']));

            // ตรวจสอบว่าเป็นอีเมลโรงเรียน @skj.ac.th หรือไม่
            if (!str_ends_with($email, '@skj.ac.th')) {
                return redirect()->to('/login')->with('error', 'ระบบอนุญาตเฉพาะบัญชี Google Workspace ของโรงเรียน (@skj.ac.th) เท่านั้น (' . esc($email) . ')');
            }

            // ประมวลผลและตรวจสอบสิทธิ์ตามอีเมล Google ที่ได้รับ
            return $this->processLoginByEmail($email, 'เข้าสู่ระบบด้วย Google สำเร็จ');
        } catch (\Throwable $e) {
            log_message('error', 'Google OAuth Exception: ' . $e->getMessage());
            return redirect()->to('/login')->with('error', 'เกิดข้อผิดพลาดในการเชื่อมต่อกับ Google: ' . esc($e->getMessage()));
        }
    }

    /**
     * รับข้อมูล Google Credential (JWT ID Token) จาก Google Identity Services (GIS)
     * รองรับทั้ง One Tap และ Modern Sign in with Google Button
     */
    public function googleVerify()
    {
        $credential = $this->request->getPost('credential');
        if (empty($credential)) {
            $json = $this->request->getJSON(true);
            $credential = $json['credential'] ?? null;
        }

        if (empty($credential)) {
            return redirect()->to('/login')->with('error', 'ไม่พบข้อมูล Credential จาก Google Sign-In');
        }

        // ตรวจสอบความถูกต้องของ Google ID Token ผ่าน TokenInfo Endpoint
        $client = \Config\Services::curlrequest(['verify' => false]);
        try {
            $response = $client->get('https://oauth2.googleapis.com/tokeninfo', [
                'query'       => ['id_token' => $credential],
                'http_errors' => false,
            ]);

            $payload = json_decode($response->getBody(), true);
            if (empty($payload['email'])) {
                $err = $payload['error_description'] ?? ($payload['error'] ?? 'Google ID Token ไม่ถูกต้อง');
                return redirect()->to('/login')->with('error', 'การยืนยันตัวตน Google ไม่สำเร็จ: ' . esc($err));
            }

            // ตรวจสอบ Client ID เพื่อป้องกัน Token จากระบบอื่น
            $clientId = env('google.clientID');
            if (!empty($clientId) && !empty($payload['aud']) && $payload['aud'] !== $clientId) {
                return redirect()->to('/login')->with('error', 'Google Client ID ไม่ถูกต้อง');
            }

            $email = trim(strtolower($payload['email']));

            // ตรวจสอบโดเมนโรงเรียน @skj.ac.th
            if (!str_ends_with($email, '@skj.ac.th')) {
                return redirect()->to('/login')->with('error', 'ระบบอนุญาตเฉพาะบัญชี Google Workspace ของโรงเรียน (@skj.ac.th) เท่านั้น (' . esc($email) . ')');
            }

            // เข้าสู่ระบบและตรวจสอบสิทธิ์
            return $this->processLoginByEmail($email, 'เข้าสู่ระบบด้วย Google สำเร็จ');
        } catch (\Throwable $e) {
            log_message('error', 'Google GIS Verify Error: ' . $e->getMessage());
            return redirect()->to('/login')->with('error', 'เกิดข้อผิดพลาดในการตรวจสอบ Google Token: ' . esc($e->getMessage()));
        }
    }

    /**
     * ตรวจสอบสิทธิ์และนำผู้ใช้เข้าสู่ระบบด้วยอีเมล
     */
    private function processLoginByEmail(string $email, string $successMessagePrefix = 'เข้าสู่ระบบสำเร็จ')
    {
        // 1. ตรวจสอบข้อมูลในตาราง users ของสารบรรณ (เช่น Supper Admin / Admin)
        $user = $this->userModel->groupStart()
                                    ->where('email', $email)
                                    ->orWhere('username', $email)
                                ->groupEnd()
                                ->where('is_active', 1)
                                ->first();

        // 2. ตรวจสอบข้อมูลบุคลากรจริงจาก skjacth_personnel.tb_personnel
        $person = $this->userModel->findPersonnel($email);

        if (!$user && !$person) {
            return redirect()->to('/login')->with('error', 'ไม่พบข้อมูลบัญชีอีเมล "' . esc($email) . '" ในระบบสารบรรณและฐานข้อมูลบุคลากรโรงเรียน');
        }

        // จัดการและตรวจสอบสิทธิ์การใช้งานตามอีเมล
        if (!$user) {
            // สร้างบัญชีผู้ใช้งานทั่วไปใหม่อัตโนมัติสำหรับทุกคนที่มีอีเมลโรงเรียน @skj.ac.th
            if ($person) {
                $userWithDetails = $this->userModel->syncPersonnelToUser($person);
                $userId = $userWithDetails['id'];
                $role = in_array($userWithDetails['role'], ['super_admin', 'admin'], true) ? $userWithDetails['role'] : 'teacher';
            } else {
                $newUserId = $this->userModel->insert([
                    'pers_id'   => null,
                    'username'  => $email,
                    'email'     => $email,
                    'role'      => 'teacher',
                    'is_active' => 1,
                ]);
                $userId = $newUserId;
                $role = 'teacher';
            }
        } else {
            $userId = $user['id'];
            if ($user['role'] === 'super_admin') {
                $role = 'super_admin';
            } elseif ($user['role'] === 'admin') {
                $role = 'admin';
            } else {
                $role = $user['role'] ?: 'teacher';
            }
        }

        // ดึงข้อมูลบุคลากรแบบเรียลไทม์จาก tb_personnel
        $userWithDetails = $this->userModel->getUserWithDetails($userId);

        // ตรวจสอบเงื่อนไขในฐานข้อมูลว่ามีมากกว่า 1 สิทธิ์หรือไม่ (เช่น เป็นทั้ง Admin/Supper Admin และ ครู ในคราวเดียวกัน)
        $hasDualRoles = false;
        $availableRoles = [];
        $adminRole = null;
        $staffRole = 'teacher';

        if ($user && in_array($user['role'], ['super_admin', 'admin'], true)) {
            $hasDualRoles = true;
            $adminRole = $user['role'];
            $staffRole = ($person && in_array($person['pers_position'], ['posi_001', 'posi_002'], true)) ? 'executive' : 'teacher';

            $availableRoles[$adminRole] = [
                'key'         => $adminRole,
                'title'       => ($adminRole === 'super_admin') ? 'Supper Admin' : 'Admin',
                'subtitle'    => ($adminRole === 'super_admin') ? 'ผู้ดูแลระบบสูงสุด' : 'เจ้าหน้าที่งานสารบรรณ',
                'description' => ($adminRole === 'super_admin')
                    ? 'ควบคุมและบริหารระบบทั้งหมด จัดการแต่งตั้ง/แก้ไขสิทธิ์เจ้าหน้าที่ และตรวจสอบ Audit Log'
                    : 'ลงรับ-ส่งหนังสือราชการ ออกเลขทะเบียน คุมเล่มทะเบียน และกำหนดเส้นทางเดินแฟ้ม',
                'icon'        => ($adminRole === 'super_admin') ? 'bx-crown' : 'bx-folder-open',
                'badge_class' => ($adminRole === 'super_admin') ? 'bg-primary' : 'bg-info',
                'border_class'=> ($adminRole === 'super_admin') ? 'border-primary' : 'border-info',
            ];

            $availableRoles[$staffRole] = [
                'key'         => $staffRole,
                'title'       => 'ครู / ผู้ใช้งานทั่วไป',
                'subtitle'    => !empty($userWithDetails['position_name']) ? $userWithDetails['position_name'] : 'บุคลากรโรงเรียน',
                'description' => 'ยื่นขออนุมัติไปราชการ สร้างบันทึกข้อความภายใน เสนอคำสั่ง และลงนาม/เกษียนตามสายงาน',
                'icon'        => 'bx-user-check',
                'badge_class' => 'bg-secondary',
                'border_class'=> 'border-secondary',
            ];

            $role = $adminRole; // ค่าเริ่มต้น
        }

        $sessionData = [
            'user_id'            => $userWithDetails['id'],
            'pers_id'            => $userWithDetails['pers_id'] ?? null,
            'username'           => $userWithDetails['username'],
            'full_name'          => trim($userWithDetails['prefix'] . $userWithDetails['first_name'] . ' ' . $userWithDetails['last_name']) ?: $email,
            'first_name'         => $userWithDetails['first_name'] ?: 'ผู้ใช้งาน',
            'last_name'          => $userWithDetails['last_name'] ?: '',
            'email'              => $userWithDetails['email'],
            'role'               => $role,
            'has_dual_roles'     => $hasDualRoles,
            'available_roles'    => $availableRoles,
            'primary_admin_role' => $adminRole,
            'staff_role'         => $staffRole,
            'position_name'      => $userWithDetails['position_name'],
            'department_name'    => $userWithDetails['department_name'],
            'signature_img'      => $userWithDetails['signature_img'] ?? null,
            'can_sign_final'     => ($role === 'super_admin' || $role === 'executive'),
            'logged_in'          => true,
        ];

        session()->set($sessionData);

        // หากพบบัญชีมีหลายสิทธิ์ ให้แสดง Modal เลือกสิทธิ์ที่ต้องการเข้าใช้งาน
        if ($hasDualRoles) {
            return redirect()->to('/select-role');
        }

        return redirect()->to('/admin/dashboard')->with('success', $successMessagePrefix . ' ยินดีต้อนรับ ' . $sessionData['full_name']);
    }

    /**
     * หน้าแสดง Modal เลือกสิทธิ์การเข้าใช้งาน (เมื่อมีสิทธิ์ Admin และ ครู พร้อมกัน)
     */
    public function selectRole()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        if (!session()->get('has_dual_roles')) {
            return redirect()->to('/admin/dashboard');
        }

        return view('auth/select_role', [
            'availableRoles' => session()->get('available_roles') ?? [],
            'currentRole'    => session()->get('role'),
            'fullName'       => session()->get('full_name'),
            'email'          => session()->get('email'),
            'positionName'   => session()->get('position_name'),
            'departmentName' => session()->get('department_name'),
        ]);
    }

    /**
     * บันทึกการเลือกสิทธิ์จาก Modal
     */
    public function processSelectRole()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $selectedRole = (string)$this->request->getPost('role');
        $availableRoles = session()->get('available_roles') ?? [];

        if (!array_key_exists($selectedRole, $availableRoles)) {
            return redirect()->to('/select-role')->with('error', 'สิทธิ์ที่เลือกไม่ถูกต้อง');
        }

        session()->set([
            'role'           => $selectedRole,
            'can_sign_final' => ($selectedRole === 'super_admin' || $selectedRole === 'executive'),
        ]);

        $roleTitle = $availableRoles[$selectedRole]['title'] ?? $selectedRole;
        return redirect()->to('/admin/dashboard')->with('success', 'เข้าสู่ระบบในฐานะ: ' . $roleTitle . ' เรียบร้อยแล้ว');
    }

    /**
     * สลับสิทธิ์ระหว่าง Admin กับ ครู ในขณะใช้งานระบบ
     */
    public function switchRole($role)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $availableRoles = session()->get('available_roles') ?? [];
        if (!session()->get('has_dual_roles') || !array_key_exists($role, $availableRoles)) {
            return redirect()->back()->with('error', 'ไม่สามารถสลับไปยังสิทธิ์นี้ได้');
        }

        session()->set([
            'role'           => $role,
            'can_sign_final' => ($role === 'super_admin' || $role === 'executive'),
        ]);

        $roleTitle = $availableRoles[$role]['title'] ?? $role;
        return redirect()->to('/admin/dashboard')->with('success', 'สลับสิทธิ์การใช้งานเป็น: ' . $roleTitle . ' เรียบร้อยแล้ว');
    }

    public function switchUser($id)
    {
        // ปิดโหมดสลับผู้ใช้งานทดสอบแล้ว เพื่อความปลอดภัยในการนำขึ้น Production
        return redirect()->to('/admin/dashboard')->with('error', 'ระบบปิดโหมดสลับผู้ใช้งานทดสอบแล้วเพื่อความปลอดภัย');
    }

    public function profile()
    {
        $userId = session()->get('user_id');
        $user = $this->userModel->getUserWithDetails($userId);

        return view('auth/profile', [
            'user' => $user
        ]);
    }

    public function saveSignature()
    {
        $userId = session()->get('user_id');
        $signatureData = $this->request->getPost('signature_data');

        if ($signatureData) {
            // Save base64 image
            $data = explode(',', $signatureData);
            $img = base64_decode(end($data));

            $dir = FCPATH . 'uploads/signatures/';
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }

            $filename = 'sig_user_' . $userId . '_' . time() . '.png';
            $path = $dir . $filename;
            file_put_contents($path, $img);

            $relativeSigPath = 'uploads/signatures/' . $filename;

            // 1. อัปเดตในตาราง users ของระบบสารบรรณ
            $this->userModel->update($userId, [
                'signature_img' => $relativeSigPath
            ]);

            // 2. อัปเดตเก็บไว้ในฐานข้อมูลบุคลากร skjacth_personnel.tb_personnel โดยตรง
            $userRecord = $this->userModel->find($userId);
            if ($userRecord) {
                $db = \Config\Database::connect();
                $personnelBuilder = $db->table('skjacth_personnel.tb_personnel');
                
                $personnelBuilder->groupStart()
                                 ->where('pers_username', $userRecord['email'])
                                 ->orWhere('pers_username', $userRecord['username']);
                if (!empty($userRecord['pers_id'])) {
                    $personnelBuilder->orWhere('pers_id', $userRecord['pers_id']);
                }
                $personnelBuilder->groupEnd();

                $personnelBuilder->update([
                    'pers_signature'  => $relativeSigPath,
                    'pers_dataUpdate' => date('Y-m-d H:i:s'),
                ]);
            }

            session()->set('signature_img', $relativeSigPath);

            return redirect()->to('/admin/profile')->with('success', 'บันทึกลายเซ็นเข้าสู่ฐานข้อมูลบุคลากรเรียบร้อยแล้ว');
        }

        return redirect()->to('/admin/profile')->with('error', 'ไม่พบข้อมูลลายเซ็น');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'ออกจากระบบเรียบร้อยแล้ว');
    }
}
