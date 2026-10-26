<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddInboundAssignmentToDocuments extends Migration
{
    public function up()
    {
        $fields = [
            'assigned_to_user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'after'      => 'received_at',
            ],
            'assigned_department' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
                'null'       => true,
                'after'      => 'assigned_to_user_id',
            ],
            'dispatch_status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'dispatched'],
                'default'    => 'pending',
                'null'       => false,
                'after'      => 'assigned_department',
            ],
            'executive_instruction' => [
                'type'       => 'TEXT',
                'null'       => true,
                'after'      => 'dispatch_status',
            ],
        ];

        foreach ($fields as $fieldName => $fieldConfig) {
            if (!$this->db->fieldExists($fieldName, 'documents')) {
                $this->forge->addColumn('documents', [$fieldName => $fieldConfig]);
            }
        }
    }

    public function down()
    {
        $fields = ['assigned_to_user_id', 'assigned_department', 'dispatch_status', 'executive_instruction'];
        foreach ($fields as $fieldName) {
            if ($this->db->fieldExists($fieldName, 'documents')) {
                $this->forge->dropColumn('documents', $fieldName);
            }
        }
    }
}
