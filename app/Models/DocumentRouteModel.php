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
        'pers_id',
        'approver_name',
        'personnel_position',
        'personnel_learning',
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
        return $this->select("document_routes.*, CASE WHEN NULLIF(users.first_name, '') IS NULL AND document_routes.approver_name IS NOT NULL THEN '' ELSE COALESCE(users.prefix, '') END as prefix, COALESCE(NULLIF(users.first_name, ''), document_routes.approver_name, '') as first_name, COALESCE(users.last_name, '') as last_name, COALESCE(document_routes.personnel_position, positions.name, '') as position_name, departments.name as department_name, positions.level as position_level")
                    ->join('users', 'users.id = document_routes.user_id', 'left')
                    ->join('positions', 'positions.id = users.position_id', 'left')
                    ->join('departments', 'departments.id = users.department_id', 'left')
                    ->where('document_routes.document_id', $documentId)
                    ->orderBy('document_routes.step_order', 'ASC')
                    ->findAll();
    }
}
