<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDocumentLinks extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'source_type' => ['type' => 'VARCHAR', 'constraint' => 50],
            'source_id' => ['type' => 'BIGINT', 'unsigned' => true],
            'target_type' => ['type' => 'VARCHAR', 'constraint' => 50],
            'target_id' => ['type' => 'BIGINT', 'unsigned' => true],
            'relation_type' => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'related'],
            'created_by' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['source_type', 'source_id']);
        $this->forge->addKey(['target_type', 'target_id']);
        $this->forge->addUniqueKey(['source_type', 'source_id', 'target_type', 'target_id', 'relation_type'], 'uq_document_link');
        $this->forge->createTable('document_links', true);
    }

    public function down()
    {
        $this->forge->dropTable('document_links', true);
    }
}
