<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddHasReceiveStampToDocuments extends Migration
{
    public function up()
    {
        $fields = [
            'has_receive_stamp' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'null'       => false,
                'after'      => 'receive_stamp_scale',
            ],
        ];

        if (!$this->db->fieldExists('has_receive_stamp', 'documents')) {
            $this->forge->addColumn('documents', $fields);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('has_receive_stamp', 'documents')) {
            $this->forge->dropColumn('documents', 'has_receive_stamp');
        }
    }
}
