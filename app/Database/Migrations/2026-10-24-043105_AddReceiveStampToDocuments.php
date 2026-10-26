<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddReceiveStampToDocuments extends Migration
{
    public function up()
    {
        $fields = [
            'receive_stamp_x' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => '60%',
                'null'       => true,
            ],
            'receive_stamp_y' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => '1.8cm',
                'null'       => true,
            ],
            'receive_stamp_scale' => [
                'type'       => 'DECIMAL',
                'constraint' => '3,2',
                'default'    => 1.00,
                'null'       => true,
            ],
            'receive_stamp_time' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'received_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'received_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ];

        $this->forge->addColumn('documents', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('documents', [
            'receive_stamp_x',
            'receive_stamp_y',
            'receive_stamp_scale',
            'receive_stamp_time',
            'received_by',
            'received_at',
        ]);
    }
}
