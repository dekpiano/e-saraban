<?php

namespace App\Models;

use CodeIgniter\Model;

class DocumentRouteModel extends Model
{
    protected $table            = 'document_routes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'document_id',
        'step_order',
        'user_id',
        'status',
        'comment_preset',
        'comment_text',
        'signature_img',
        'signature_type',
        'signed_at',
        'ip_address',
        'verify_hash',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getRoutesForDocument($documentId)
    {
        return $this->select('document_routes.*, users.prefix, users.first_name, users.last_name, positions.name as position_name, departments.name as department_name, positions.level as position_level')
                    ->join('users', 'users.id = document_routes.user_id', 'left')
                    ->join('positions', 'positions.id = users.position_id', 'left')
                    ->join('departments', 'departments.id = users.department_id', 'left')
                    ->where('document_routes.document_id', $documentId)
                    ->orderBy('document_routes.step_order', 'ASC')
                    ->findAll();
    }
}
