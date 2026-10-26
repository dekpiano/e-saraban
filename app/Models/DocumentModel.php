<?php

namespace App\Models;

use CodeIgniter\Model;

class DocumentModel extends Model
{
    protected $table            = 'documents';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'doc_type',
        'doc_no',
        'internal_reg_no',
        'year',
        'title',
        'urgency',
        'secret_level',
        'from_org',
        'to_org',
        'doc_date',
        'receive_date',
        'content',
        'memo_department',
        'memo_contact',
        'file_path',
        'raw_file_path',
        'creator_signature',
        'order_effective_text',
        'signer_name',
        'signer_position',
        'order_category',
        'status',
        'current_step',
        'created_by',
        'receive_stamp_x',
        'receive_stamp_y',
        'receive_stamp_scale',
        'has_receive_stamp',
        'receive_stamp_time',
        'received_by',
        'received_at',
        'assigned_to_user_id',
        'assigned_department',
        'dispatch_status',
        'executive_instruction',
    ];

    /**
     * คำนวณเลขที่คำสั่งโรงเรียนลำดับถัดไปประจำปี พ.ศ. (เช่น 1/2569)
     */
    public function getNextOrderNumber($year = null)
    {
        if (!$year) {
            $year = (int)date('Y') + 543;
        }

        $orders = $this->where('doc_type', 'order')
                       ->where('year', $year)
                       ->select('doc_no')
                       ->findAll();

        $maxNum = 0;
        foreach ($orders as $o) {
            if (!empty($o['doc_no'])) {
                if (preg_match('/(\d+)\s*\/\s*' . $year . '/u', $o['doc_no'], $matches)) {
                    $num = (int)$matches[1];
                    if ($num > $maxNum) {
                        $maxNum = $num;
                    }
                }
            }
        }

        $nextNum = $maxNum + 1;
        return $nextNum . '/' . $year;
    }

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getDocumentDetails($id)
    {
        return $this->select('documents.*, users.first_name, users.last_name, users.prefix, users.signature_img as user_signature, departments.name as creator_department, positions.name as creator_position')
                    ->join('users', 'users.id = documents.created_by', 'left')
                    ->join('departments', 'departments.id = users.department_id', 'left')
                    ->join('positions', 'positions.id = users.position_id', 'left')
                    ->where('documents.id', $id)
                    ->first();
    }

    public function getDocumentsForUser($userId, $type = 'inbox')
    {
        $persId = (string)(session()->get('pers_id') ?? '');
        $role   = (string)(session()->get('role') ?? '');
        $isExecutiveOrAdmin = in_array($role, ['executive', 'super_admin'], true);

        if ($type === 'inbox') {
            // Documents currently waiting for this user to sign
            $builder = $this->select('documents.*, document_routes.id as route_id, document_routes.step_order, document_routes.status as route_status, users.first_name as creator_first, users.last_name as creator_last')
                        ->join('document_routes', 'document_routes.document_id = documents.id')
                        ->join('users', 'users.id = documents.created_by', 'left')
                        ->where('document_routes.status', 'pending')
                        ->where('documents.current_step = document_routes.step_order')
                        ->where('documents.status', 'routing');

            $builder->groupStart()
                    ->where('document_routes.user_id', $userId);
            if (!empty($persId)) {
                $builder->orWhere('document_routes.pers_id', $persId);
            }
            if ($isExecutiveOrAdmin) {
                $builder->orWhere('document_routes.pers_id', 'pers_060')
                        ->orLike('document_routes.personnel_position', 'ผู้อำนวยการ')
                        ->orWhere('document_routes.user_id IS NULL', null, false);
            }
            $builder->groupEnd();

            return $builder->orderBy('documents.urgency', 'DESC')
                           ->orderBy('documents.created_at', 'DESC')
                           ->findAll();
        } elseif ($type === 'signed') {
            // Documents this user already signed
            $builder = $this->select('documents.*, document_routes.signed_at, document_routes.comment_preset, users.first_name as creator_first, users.last_name as creator_last')
                        ->join('document_routes', 'document_routes.document_id = documents.id')
                        ->join('users', 'users.id = documents.created_by', 'left')
                        ->where('document_routes.status', 'signed');

            $builder->groupStart()
                    ->where('document_routes.user_id', $userId);
            if (!empty($persId)) {
                $builder->orWhere('document_routes.pers_id', $persId);
            }
            if ($isExecutiveOrAdmin) {
                $builder->orWhere('document_routes.pers_id', 'pers_060')
                        ->orLike('document_routes.personnel_position', 'ผู้อำนวยการ');
            }
            $builder->groupEnd();

            return $builder->orderBy('document_routes.signed_at', 'DESC')
                           ->findAll();
        } elseif ($type === 'my_created') {
            // Documents created by this user
            return $this->select('documents.*')
                        ->where('documents.created_by', $userId)
                        ->orderBy('documents.created_at', 'DESC')
                        ->findAll();
        }

        return $this->findAll();
    }

    public function getNextInternalNumber($type = 'in', $year = null)
    {
        if (!$year) {
            $year = (int)date('Y') + 543;
        }

        $last = $this->where('doc_type', $type)
                     ->where('year', $year)
                     ->orderBy('id', 'DESC')
                     ->first();

        $count = $this->where('doc_type', $type)
                      ->where('year', $year)
                      ->countAllResults();

        $nextNum = str_pad($count + 1, 4, '0', STR_PAD_LEFT);
        return $nextNum . '/' . $year;
    }
}
