<?php

namespace App\Models;

use CodeIgniter\Model;

class DocumentLogModel extends Model
{
    protected $table            = 'document_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'document_id',
        'user_id',
        'action',
        'detail',
    ];

    protected $useTimestamps = false;

    public function log($documentId, $userId, $action, $detail = null)
    {
        return $this->insert([
            'document_id' => $documentId,
            'user_id'     => $userId,
            'action'      => $action,
            'detail'      => $detail,
            'created_at'  => date('Y-m-d H:i:s'),
        ]);
    }

    public function getLogsForDocument($documentId)
    {
        return $this->select('document_logs.*, users.prefix, users.first_name, users.last_name, positions.name as position_name')
                    ->join('users', 'users.id = document_logs.user_id', 'left')
                    ->join('positions', 'positions.id = users.position_id', 'left')
                    ->where('document_logs.document_id', $documentId)
                    ->orderBy('document_logs.id', 'ASC')
                    ->findAll();
    }
}
