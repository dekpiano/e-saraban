<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'pers_id',
        'username',
        'password',
        'prefix',
        'first_name',
        'last_name',
        'email',
        'phone',
        'department_id',
        'position_id',
        'role',
        'signature_img',
        'signature_pin',
        'is_active',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getUserWithDetails($id)
    {
        $user = $this->where('id', $id)->first();

        if (!$user) {
            return null;
        }

        // เชื่อมโยงข้อมูลบุคลากรจริงจาก tb_personnel โดยตรงผ่านอีเมลโรงเรียน
        $db = \Config\Database::connect();
        $person = $db->table('skjacth_personnel.tb_personnel p')
                     ->select('p.pers_id, p.pers_prefix, p.pers_firstname, p.pers_lastname, p.pers_phone, p.pers_img, p.pers_signature, pos.posi_name, l.lear_namethai')
                     ->join('skjacth_skj.tb_position pos', 'pos.posi_id = p.pers_position', 'left')
                     ->join('skjacth_skj.tb_learning l', 'l.lear_id = p.pers_learning', 'left')
                     ->groupStart()
                         ->where('p.pers_username', $user['email'])
                         ->orWhere('p.pers_username', $user['username'])
                         ->orWhere('p.pers_id', $user['pers_id'])
                     ->groupEnd()
                     ->get()
                     ->getRowArray();

        if ($person) {
            $user['prefix']          = $person['pers_prefix'];
            $user['first_name']      = $person['pers_firstname'];
            $user['last_name']       = $person['pers_lastname'];
            $user['phone']           = $person['pers_phone'];
            $user['img']             = $person['pers_img'] ?? null;
            $user['position_name']   = !empty($person['posi_name']) ? $person['posi_name'] : 'บุคลากร';
            $user['department_name'] = !empty($person['lear_namethai']) ? $person['lear_namethai'] : 'สำนักงาน';
            if (!empty($person['pers_signature'])) {
                $user['signature_img'] = $person['pers_signature'];
            }
        } else {
            $user['prefix']          = '';
            $user['first_name']      = ($user['role'] === 'super_admin') ? 'Supper Admin' : 'Admin';
            $user['last_name']       = '';
            $user['phone']           = '';
            $user['img']             = null;
            $user['position_name']   = ($user['role'] === 'super_admin') ? 'ผู้ดูแลระบบสูงสุด' : 'เจ้าหน้าที่สารบรรณ';
            $user['department_name'] = 'งานสารบรรณ';
        }

        // ดึงสิทธิ์ can_sign_final จาก positions หรือ role ของระบบ
        $canSignFinal = false;
        if (!empty($user['position_id'])) {
            $pos = $db->table('positions')->where('id', $user['position_id'])->get()->getRowArray();
            if ($pos && !empty($pos['can_sign_final'])) {
                $canSignFinal = true;
            }
        }
        if (in_array($user['role'] ?? '', ['super_admin', 'executive'], true)) {
            $canSignFinal = true;
        }
        $user['can_sign_final'] = $canSignFinal ? 1 : 0;

        return $user;
    }

    public function getAllActiveUsers()
    {
        $users = $this->select('users.id, users.email, users.username, users.role, users.is_active, p.pers_prefix as prefix, p.pers_firstname as first_name, p.pers_lastname as last_name, pos.posi_name as position_name, l.lear_namethai as department_name')
                      ->join('skjacth_personnel.tb_personnel p', "p.pers_username = users.email OR p.pers_username = users.username OR p.pers_id = users.pers_id", 'left')
                      ->join('skjacth_skj.tb_position pos', 'pos.posi_id = p.pers_position', 'left')
                      ->join('skjacth_skj.tb_learning l', 'l.lear_id = p.pers_learning', 'left')
                      ->where('users.is_active', 1)
                      ->orderBy('users.id', 'ASC')
                      ->findAll();

        foreach ($users as &$u) {
            if (empty($u['first_name'])) {
                $u['first_name'] = ($u['role'] === 'super_admin') ? 'Supper Admin' : ($u['role'] === 'admin' ? 'Admin' : $u['email']);
                $u['prefix'] = '';
                $u['last_name'] = '';
                $u['position_name'] = ($u['role'] === 'super_admin') ? 'ผู้ดูแลระบบสูงสุด' : ($u['role'] === 'admin' ? 'เจ้าหน้าที่สารบรรณ' : 'ผู้ใช้งาน');
                $u['department_name'] = 'งานสารบรรณ';
            }
        }
        unset($u);

        return $users;
    }

    /**
     * ดึงบัญชีผู้ใช้งานในระบบ skjacth_saraban.users ทั้งหมด พร้อมข้อมูลบุคลากรจริงจาก tb_personnel
     */
    public function getAdminStaffUsers(array $filters = [])
    {
        $builder = $this->select('users.id, users.email, users.username, users.role, users.is_active, users.created_at, users.pers_id, p.pers_prefix as prefix, p.pers_firstname as first_name, p.pers_lastname as last_name, pos.posi_name as position_name, l.lear_namethai as department_name')
                        ->join('skjacth_personnel.tb_personnel p', "p.pers_username = users.email OR p.pers_username = users.username OR p.pers_id = users.pers_id", 'left')
                        ->join('skjacth_skj.tb_position pos', 'pos.posi_id = p.pers_position', 'left')
                        ->join('skjacth_skj.tb_learning l', 'l.lear_id = p.pers_learning', 'left');

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $builder->groupStart()
                    ->like('users.username', $search)
                    ->orLike('users.email', $search)
                    ->orLike('p.pers_firstname', $search)
                    ->orLike('p.pers_lastname', $search)
                    ->orLike('pos.posi_name', $search)
                    ->groupEnd();
        }

        if (!empty($filters['role'])) {
            if ($filters['role'] === 'general') {
                $builder->whereNotIn('users.role', ['super_admin', 'admin']);
            } else {
                $builder->where('users.role', $filters['role']);
            }
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $builder->where('users.is_active', (int)$filters['is_active']);
        }

        $users = $builder->orderBy('users.id', 'ASC')->findAll();

        foreach ($users as &$u) {
            if (empty($u['first_name'])) {
                $u['first_name'] = $u['email'] ?: $u['username'];
                $u['prefix'] = '';
                $u['last_name'] = '';
                $u['position_name'] = self::formatRoleName($u['role']);
                $u['department_name'] = 'งานสารบรรณ';
            }
        }
        unset($u);

        return $users;
    }

    public static function formatRoleName($role)
    {
        switch ($role) {
            case 'super_admin':
                return 'Supper Admin (ผู้ดูแลระบบสูงสุด)';
            case 'admin':
                return 'Admin (เจ้าหน้าที่สารบรรณ)';
            default:
                return 'ผู้ใช้งานทั่วไป (อีเมลโรงเรียน)';
        }
    }

    /**
     * สถิติผู้ใช้งานทั้งหมดใน skjacth_saraban.users
     */
    public function getAdminStaffStats(): array
    {
        $rows = $this->select('role, is_active, COUNT(*) as total')
                     ->groupBy(['role', 'is_active'])
                     ->findAll();

        $stats = [
            'total'       => 0,
            'active'      => 0,
            'inactive'    => 0,
            'super_admin' => 0,
            'admin'       => 0,
            'general'     => 0,
        ];

        foreach ($rows as $row) {
            $count = (int)$row['total'];
            $stats['total'] += $count;
            if ((int)$row['is_active'] === 1) {
                $stats['active'] += $count;
            } else {
                $stats['inactive'] += $count;
            }

            $r = (string)$row['role'];
            if ($r === 'super_admin') {
                $stats['super_admin'] += $count;
            } elseif ($r === 'admin') {
                $stats['admin'] += $count;
            } else {
                $stats['general'] += $count;
            }
        }

        return $stats;
    }

    /**
     * ค้นหาบุคลากรใน skjacth_personnel.tb_personnel
     */
    public function findPersonnel($username)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('skjacth_personnel.tb_personnel p');
        return $builder->select('p.*, pos.posi_name, l.lear_namethai')
                       ->join('skjacth_skj.tb_position pos', 'pos.posi_id = p.pers_position', 'left')
                       ->join('skjacth_skj.tb_learning l', 'l.lear_id = p.pers_learning', 'left')
                       ->groupStart()
                           ->where('p.pers_username', $username)
                           ->orWhere('p.pers_username', $username . '@skj.ac.th')
                           ->orWhere("SUBSTRING_INDEX(p.pers_username, '@', 1) =", $username)
                           ->orWhere('p.pers_id', $username)
                       ->groupEnd()
                       ->where('p.pers_status', 'กำลังใช้งาน')
                       ->get()
                       ->getRowArray();
    }

    /**
     * ซิงค์ข้อมูลบุคลากรเข้าสู่ตาราง users ในระบบสารบรรณ
     */
    public function syncPersonnelToUser(array $person)
    {
        $persId = $person['pers_id'];
        $existing = $this->where('pers_id', $persId)->first();

        // คำนวณ Role และ Position ในระบบสารบรรณ
        // ผอ: posi_001 -> executive (สั่งการขั้นสุดท้าย)
        // รอง ผอ: posi_002 -> executive
        // ครู: posi_003..006 -> teacher
        $posCode = $person['pers_position'] ?? '';
        $role = in_array($posCode, ['posi_001', 'posi_002'], true) ? 'executive' : 'teacher';
        $positionId = ($posCode === 'posi_001') ? 1 : (($posCode === 'posi_002') ? 2 : 6);

        // แผนก/กลุ่มสาระ
        $learId = $person['pers_learning'] ?? '';
        $learMap = [
            'lear_001' => 7,  // ภาษาไทย
            'lear_002' => 6,  // คณิตศาสตร์
            'lear_003' => 5,  // วิทยาศาสตร์ฯ
            'lear_004' => 9,  // สังคมศึกษาฯ
            'lear_005' => 10, // สุขศึกษาฯ
            'lear_006' => 11, // ศิลปะ
            'lear_007' => 12, // การงานอาชีพ
            'lear_008' => 8,  // ภาษาต่างประเทศ
            'lear_009' => 13, // แนะแนว
        ];
        $departmentId = in_array($posCode, ['posi_001', 'posi_002'], true) ? 1 : ($learMap[$learId] ?? 1);

        $emailUsername = !empty($person['pers_username']) 
            ? $person['pers_username'] 
            : $person['pers_id'] . '@skj.ac.th';

        // บันทึกข้อมูลบุคลากรลงตาราง users ในระบบสารบรรณ
        $userData = [
            'pers_id'       => $persId,
            'username'      => $emailUsername,
            'email'         => $emailUsername,
            'prefix'        => $person['pers_prefix'] ?? '',
            'first_name'    => $person['pers_firstname'] ?? '',
            'last_name'     => $person['pers_lastname'] ?? '',
            'phone'         => $person['pers_phone'] ?? '',
            'department_id' => $departmentId,
            'position_id'   => $positionId,
            'role'          => $role,
            'is_active'     => 1,
        ];

        if ($existing) {
            $this->update($existing['id'], $userData);
            return $this->getUserWithDetails($existing['id']);
        }

        $userData['password'] = password_hash('123456', PASSWORD_BCRYPT);
        $newId = $this->insert($userData);
        return $this->getUserWithDetails($newId);
    }

    /**
     * ดึงรายชื่อบุคลากรทั้งหมดที่มีอีเมลโรงเรียน (@skj.ac.th)
     */
    public function getActivePersonnelList(): array
    {
        $db = \Config\Database::connect();
        $rows = $db->table('skjacth_personnel.tb_personnel p')
                   ->select('p.pers_id, p.pers_username, p.pers_prefix, p.pers_firstname, p.pers_lastname, p.pers_phone, p.pers_position, p.pers_learning, p.pers_img, pos.posi_name, l.lear_namethai')
                   ->join('skjacth_skj.tb_position pos', 'pos.posi_id = p.pers_position', 'left')
                   ->join('skjacth_skj.tb_learning l', 'l.lear_id = p.pers_learning', 'left')
                   ->where('p.pers_status', 'กำลังใช้งาน')
                   ->where("p.pers_username LIKE '%@skj.ac.th'")
                   ->orderBy('p.pers_firstname', 'ASC')
                   ->get()
                   ->getResultArray();

        $learMap = [
            'lear_001' => 7,  // ภาษาไทย
            'lear_002' => 6,  // คณิตศาสตร์
            'lear_003' => 5,  // วิทยาศาสตร์ฯ
            'lear_004' => 9,  // สังคมศึกษาฯ
            'lear_005' => 10, // สุขศึกษาฯ
            'lear_006' => 11, // ศิลปะ
            'lear_007' => 12, // การงานอาชีพ
            'lear_008' => 8,  // ภาษาต่างประเทศ
            'lear_009' => 13, // แนะแนว
        ];

        foreach ($rows as &$r) {
            $r['department_id'] = in_array($r['pers_position'], ['posi_001', 'posi_002'], true) ? 1 : ($learMap[$r['pers_learning']] ?? 1);
        }
        unset($r);

        return $rows;
    }

    /**
     * ดึงรายชื่อครูและบุคลากรทั้งหมดจาก tb_personnel สำหรับให้เจ้าหน้าที่เลือกทำเรื่องแทน
     */
    public function getPersonnelForSelection(): array
    {
        $db = \Config\Database::connect();
        $personnel = $db->table('skjacth_personnel.tb_personnel p')
                        ->select('p.pers_id, p.pers_username, p.pers_prefix, p.pers_firstname, p.pers_lastname, p.pers_phone, p.pers_position, p.pers_learning, p.pers_img, pos.posi_name, l.lear_id, l.lear_namethai')
                        ->join('skjacth_skj.tb_position pos', 'pos.posi_id = p.pers_position', 'left')
                        ->join('skjacth_skj.tb_learning l', 'l.lear_id = p.pers_learning', 'left')
                        ->where('p.pers_status', 'กำลังใช้งาน')
                        ->orderBy("CASE 
                            WHEN p.pers_position IN ('posi_001', 'posi_002') THEN 1 
                            WHEN l.lear_id IS NOT NULL AND l.lear_id != '' THEN 2 
                            ELSE 3 
                        END", 'ASC', false)
                        ->orderBy('l.lear_id', 'ASC')
                        ->orderBy('p.pers_firstname', 'ASC')
                        ->get()
                        ->getResultArray();

        // ดึง users ที่มีอยู่แล้วเพื่อ map user_id ถ้ามี
        $existingUsers = $this->select('id, pers_id, email, username')->findAll();
        $userPersMap = [];
        $userEmailMap = [];
        foreach ($existingUsers as $eu) {
            if (!empty($eu['pers_id'])) {
                $userPersMap[$eu['pers_id']] = (int)$eu['id'];
            }
            if (!empty($eu['email'])) {
                $userEmailMap[strtolower(trim($eu['email']))] = (int)$eu['id'];
            }
            if (!empty($eu['username'])) {
                $userEmailMap[strtolower(trim($eu['username']))] = (int)$eu['id'];
            }
        }

        foreach ($personnel as &$p) {
            $p['full_name'] = trim(($p['pers_prefix'] ?? '') . ($p['pers_firstname'] ?? '') . ' ' . ($p['pers_lastname'] ?? ''));
            $p['position_name'] = !empty($p['posi_name']) ? $p['posi_name'] : 'ครู/บุคลากร';

            // จัดกลุ่มและชื่อฝ่าย/กลุ่มสาระฯ
            if (in_array($p['pers_position'], ['posi_001', 'posi_002'], true)) {
                $p['group_name'] = 'ฝ่ายบริหารสถานศึกษา';
                $p['department_name'] = 'ฝ่ายบริหารสถานศึกษา';
            } elseif (!empty($p['lear_namethai'])) {
                $p['group_name'] = 'กลุ่มสาระฯ ' . $p['lear_namethai'];
                $p['department_name'] = 'กลุ่มสาระฯ ' . $p['lear_namethai'];
            } else {
                $p['group_name'] = 'ฝ่ายงาน/บุคลากรทางการศึกษา';
                $p['department_name'] = 'ฝ่ายงาน/สำนักงาน';
            }

            $email = strtolower(trim($p['pers_username'] ?? ''));
            $p['user_id'] = $userPersMap[$p['pers_id']] ?? ($userEmailMap[$email] ?? null);
        }
        unset($p);

        return $personnel;
    }
}

