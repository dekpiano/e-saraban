<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMemoRequirementToApprovalModule extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('requires_memo', 'approval_types')) {
            $this->forge->addColumn('approval_types', [
                'requires_memo' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1, 'after' => 'description'],
            ]);
        }
        if ($this->db->fieldExists('document_id', 'approval_requests')) {
            $fields = $this->db->getFieldData('approval_requests');
            foreach ($fields as $field) {
                if ($field->name === 'document_id' && empty($field->nullable)) {
                    $this->db->query('ALTER TABLE approval_requests MODIFY document_id INT UNSIGNED NULL');
                    break;
                }
            }
        }
        $this->db->query("UPDATE approval_types SET requires_memo = CASE code
            WHEN 'leave_area' THEN 0
            WHEN 'use_venue' THEN 0
            ELSE 1 END");
    }

    public function down()
    {
        // Keep data and compatibility intact; this migration is intentionally non-destructive.
    }
}
