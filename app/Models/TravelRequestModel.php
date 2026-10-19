<?php

namespace App\Models;

use CodeIgniter\Model;

class TravelRequestModel extends Model
{
    protected $table            = 'travel_requests';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'doc_no',
        'user_id',
        'title',
        'destination',
        'province',
        'start_date',
        'end_date',
        'total_days',
        'transport_type',
        'transport_detail',
        'budget_type',
        'budget_amount',
        'companions',
        'teaching_assign',
        'file_path',
        'attached_docs',
        'other_doc_detail',
        'status',
        'current_step',
        'current_receiver',
        'step_receivers',
        'approver_title',
        'return_step',
        'return_note',
        'returned_to_user_at',
        'report_detail',
        'report_file',
        'reported_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getRequestWithDetails($id)
    {
        $req = $this->select('travel_requests.*, users.pers_id, users.email, users.username, users.prefix, users.first_name, users.last_name, positions.name as position_name, departments.name as department_name, p.pers_prefix, p.pers_firstname, p.pers_lastname, pos.posi_name as personnel_posi_name, l.lear_namethai as personnel_dept_name')
                    ->join('users', 'users.id = travel_requests.user_id', 'left')
                    ->join('positions', 'positions.id = users.position_id', 'left')
                    ->join('departments', 'departments.id = users.department_id', 'left')
                    ->join('skjacth_personnel.tb_personnel p', "p.pers_id = users.pers_id OR p.pers_username = users.email OR p.pers_username = users.username", 'left')
                    ->join('skjacth_skj.tb_position pos', 'pos.posi_id = p.pers_position', 'left')
                    ->join('skjacth_skj.tb_learning l', 'l.lear_id = p.pers_learning', 'left')
                    ->where('travel_requests.id', $id)
                    ->first();

        if ($req && !empty($req['pers_firstname'])) {
            $req['prefix'] = $req['pers_prefix'];
            $req['first_name'] = $req['pers_firstname'];
            $req['last_name'] = $req['pers_lastname'];
            $req['position_name'] = !empty($req['personnel_posi_name']) ? $req['personnel_posi_name'] : ($req['position_name'] ?: 'บุคลากร');
            $req['department_name'] = !empty($req['personnel_dept_name']) ? ('กลุ่มสาระฯ ' . $req['personnel_dept_name']) : ($req['department_name'] ?: 'สำนักงาน');
        }

        return $req;
    }

    public function getRequestsForUser($userId, $type = 'my')
    {
        if ($type === 'my') {
            $list = $this->select('travel_requests.*, users.prefix, users.first_name, users.last_name, p.pers_prefix, p.pers_firstname, p.pers_lastname')
                        ->join('users', 'users.id = travel_requests.user_id', 'left')
                        ->join('skjacth_personnel.tb_personnel p', "p.pers_id = users.pers_id OR p.pers_username = users.email OR p.pers_username = users.username", 'left')
                        ->where('travel_requests.user_id', $userId)
                        ->orderBy('travel_requests.created_at', 'DESC')
                        ->findAll();

            foreach ($list as &$item) {
                if (!empty($item['pers_firstname'])) {
                    $item['prefix'] = $item['pers_prefix'];
                    $item['first_name'] = $item['pers_firstname'];
                    $item['last_name'] = $item['pers_lastname'];
                }
            }
            unset($item);
            return $list;
        } elseif ($type === 'pending_me') {
            $list = $this->select('travel_requests.*, travel_routes.id as route_id, travel_routes.step_order, users.prefix, users.first_name, users.last_name, positions.name as position_name, p.pers_prefix, p.pers_firstname, p.pers_lastname, pos.posi_name as personnel_posi_name')
                        ->join('travel_routes', 'travel_routes.travel_id = travel_requests.id')
                        ->join('users', 'users.id = travel_requests.user_id', 'left')
                        ->join('positions', 'positions.id = users.position_id', 'left')
                        ->join('skjacth_personnel.tb_personnel p', "p.pers_id = users.pers_id OR p.pers_username = users.email OR p.pers_username = users.username", 'left')
                        ->join('skjacth_skj.tb_position pos', 'pos.posi_id = p.pers_position', 'left')
                        ->where('travel_routes.user_id', $userId)
                        ->where('travel_routes.status', 'pending')
                        ->where('travel_requests.current_step = travel_routes.step_order')
                        ->where('travel_requests.status', 'pending')
                        ->orderBy('travel_requests.start_date', 'ASC')
                        ->findAll();

            foreach ($list as &$item) {
                if (!empty($item['pers_firstname'])) {
                    $item['prefix'] = $item['pers_prefix'];
                    $item['first_name'] = $item['pers_firstname'];
                    $item['last_name'] = $item['pers_lastname'];
                    $item['position_name'] = !empty($item['personnel_posi_name']) ? $item['personnel_posi_name'] : ($item['position_name'] ?: 'บุคลากร');
                }
            }
            unset($item);
            return $list;
        }

        $list = $this->select('travel_requests.*, users.prefix, users.first_name, users.last_name, departments.name as department_name, p.pers_prefix, p.pers_firstname, p.pers_lastname, pos.posi_name as personnel_posi_name, l.lear_namethai as personnel_dept_name')
                    ->join('users', 'users.id = travel_requests.user_id', 'left')
                    ->join('departments', 'departments.id = users.department_id', 'left')
                    ->join('skjacth_personnel.tb_personnel p', "p.pers_id = users.pers_id OR p.pers_username = users.email OR p.pers_username = users.username", 'left')
                    ->join('skjacth_skj.tb_position pos', 'pos.posi_id = p.pers_position', 'left')
                    ->join('skjacth_skj.tb_learning l', 'l.lear_id = p.pers_learning', 'left')
                    ->orderBy('travel_requests.created_at', 'DESC')
                    ->findAll();

        foreach ($list as &$item) {
            if (!empty($item['pers_firstname'])) {
                $item['prefix'] = $item['pers_prefix'];
                $item['first_name'] = $item['pers_firstname'];
                $item['last_name'] = $item['pers_lastname'];
                $item['department_name'] = !empty($item['personnel_dept_name']) ? ('กลุ่มสาระฯ ' . $item['personnel_dept_name']) : ($item['department_name'] ?: 'สำนักงาน');
            }
        }
        unset($item);
        return $list;
    }

    public function getNextDocNumber($year = null)
    {
        // ค้นหาเลขที่หนังสือทั้งหมดในระบบเพื่อหาลำดับตัวเลขสูงสุด
        $rows = $this->select('doc_no')->findAll();

        $maxNum = 0;
        foreach ($rows as $r) {
            $docNo = trim($r['doc_no'] ?? '');
            // จับตัวเลขท้ายสุด เช่น 'ที่ ศธ 04225/ไปร002' หรือ 'ป.0001/2569' หรือ '.../005'
            if (preg_match('/(\d+)(?:\/\d+)?$/u', $docNo, $matches)) {
                $num = (int)$matches[1];
                if ($num > $maxNum) {
                    $maxNum = $num;
                }
            } elseif (preg_match('/(\d+)/u', $docNo, $matches)) {
                $num = (int)$matches[1];
                if ($num > $maxNum) {
                    $maxNum = $num;
                }
            }
        }

        // วนลูปหาเลขถัดไปที่ไม่ซ้ำแน่นอน
        do {
            $maxNum++;
            $nextFormatted = str_pad($maxNum, 3, '0', STR_PAD_LEFT);
            $candidate = "ที่ ศธ 04225/ไปร{$nextFormatted}";
            $exists = $this->where('doc_no', $candidate)->first();
        } while ($exists);

        return $candidate;
    }
}
