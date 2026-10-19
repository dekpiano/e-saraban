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
        'creator_signature',
        'status',
        'current_step',
        'created_by',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getDocumentDetails($id)
    {
        return $this->select('documents.*, users.first_name, users.last_name, users.prefix, departments.name as creator_department')
                    ->join('users', 'users.id = documents.created_by', 'left')
                    ->join('departments', 'departments.id = users.department_id', 'left')
                    ->where('documents.id', $id)
                    ->first();
    }

    public function getDocumentsForUser($userId, $type = 'inbox')
    {
        if ($type === 'inbox') {
            // Documents currently waiting for this user to sign
            return $this->select('documents.*, document_routes.id as route_id, document_routes.step_order, document_routes.status as route_status, users.first_name as creator_first, users.last_name as creator_last')
                        ->join('document_routes', 'document_routes.document_id = documents.id')
                        ->join('users', 'users.id = documents.created_by', 'left')
                        ->where('document_routes.user_id', $userId)
                        ->where('document_routes.status', 'pending')
                        ->where('documents.current_step = document_routes.step_order')
                        ->where('documents.status', 'routing')
                        ->orderBy('documents.urgency', 'DESC')
                        ->orderBy('documents.created_at', 'DESC')
                        ->findAll();
        } elseif ($type === 'signed') {
            // Documents this user already signed
            return $this->select('documents.*, document_routes.signed_at, document_routes.comment_preset, users.first_name as creator_first, users.last_name as creator_last')
                        ->join('document_routes', 'document_routes.document_id = documents.id')
                        ->join('users', 'users.id = documents.created_by', 'left')
                        ->where('document_routes.user_id', $userId)
                        ->where('document_routes.status', 'signed')
                        ->orderBy('document_routes.signed_at', 'DESC')
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
