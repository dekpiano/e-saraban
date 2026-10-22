<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddApprovalMemoRequirement extends Migration
{
    public function up()
    {
        // Column may already be present from the initial approval-module migration.
        if (!$this->db->fieldExists('requires_memo', 'approval_types')) {
            $this->db->query('ALTER TABLE approval_types ADD COLUMN requires_memo TINYINT(1) NOT NULL DEFAULT 1 AFTER description');
        }

        // Default behavior: approval forms require a memo; permission forms can be submitted directly.
        $this->db->query("UPDATE approval_types SET requires_memo = CASE WHEN category = 'approval' THEN 1 ELSE 0 END");

        if ($this->db->fieldExists('document_id', 'approval_requests')) {
            $this->forge->modifyColumn('approval_requests', [
                'document_id' => [
                    'type' => 'INT',
                    'unsigned' => true,
                    'null' => true,
                ],
            ]);
        }
    }

    public function down()
    {
        // Keep request data safe; do not make document_id required again.
        if ($this->db->fieldExists('requires_memo', 'approval_types')) {
            $this->forge->dropColumn('approval_types', 'requires_memo');
        }
    }
}
