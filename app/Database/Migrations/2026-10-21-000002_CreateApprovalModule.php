<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateApprovalModule extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'code' => ['type' => 'VARCHAR', 'constraint' => 50],
            'name' => ['type' => 'VARCHAR', 'constraint' => 180],
            'category' => ['type' => 'ENUM', 'constraint' => ['approval', 'permission'], 'default' => 'approval'],
            'description' => ['type' => 'TEXT', 'null' => true],
            'requires_memo' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'is_active' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('code');
        $this->forge->createTable('approval_types', true);

        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'approval_type_id' => ['type' => 'INT', 'unsigned' => true],
            'step_order' => ['type' => 'INT', 'unsigned' => true],
            'step_name' => ['type' => 'VARCHAR', 'constraint' => 120],
            'approver_user_id' => ['type' => 'INT', 'unsigned' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['approval_type_id', 'step_order'], 'uq_approval_type_step');
        $this->forge->addKey('approver_user_id');
        $this->forge->createTable('approval_type_steps', true);

        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'document_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'approval_type_id' => ['type' => 'INT', 'unsigned' => true],
            'requester_id' => ['type' => 'INT', 'unsigned' => true],
            'title' => ['type' => 'VARCHAR', 'constraint' => 255],
            'details' => ['type' => 'LONGTEXT', 'null' => true],
            'purpose' => ['type' => 'TEXT', 'null' => true],
            'location' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'start_date' => ['type' => 'DATE', 'null' => true],
            'end_date' => ['type' => 'DATE', 'null' => true],
            'amount' => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0],
            'extra_data' => ['type' => 'LONGTEXT', 'null' => true],
            'status' => ['type' => 'ENUM', 'constraint' => ['pending', 'approved', 'rejected', 'returned', 'cancelled'], 'default' => 'pending'],
            'current_step' => ['type' => 'INT', 'unsigned' => true, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['requester_id', 'status'], false, false, 'idx_approval_requester_status');
        $this->forge->addKey(['document_id']);
        $this->forge->addKey(['approval_type_id']);
        $this->forge->createTable('approval_requests', true);

        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'request_id' => ['type' => 'BIGINT', 'unsigned' => true],
            'step_order' => ['type' => 'INT', 'unsigned' => true],
            'step_name' => ['type' => 'VARCHAR', 'constraint' => 120],
            'approver_user_id' => ['type' => 'INT', 'unsigned' => true],
            'status' => ['type' => 'ENUM', 'constraint' => ['pending', 'approved', 'rejected', 'returned', 'skipped'], 'default' => 'pending'],
            'comment' => ['type' => 'TEXT', 'null' => true],
            'action_at' => ['type' => 'DATETIME', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['request_id', 'step_order']);
        $this->forge->addKey(['approver_user_id', 'status']);
        $this->forge->createTable('approval_request_steps', true);

        $types = [
            ['project', 'ขออนุมัติดำเนินโครงการ/กิจกรรม', 'approval', 'รายละเอียดโครงการ กำหนดการ และผลที่คาดว่าจะได้รับ', 1],
            ['budget', 'ขออนุมัติใช้จ่ายงบประมาณ', 'approval', 'แหล่งงบประมาณ รายการค่าใช้จ่าย และวงเงิน', 1],
            ['procurement', 'ขออนุมัติจัดซื้อจัดจ้าง', 'approval', 'รายการพัสดุ เหตุผลความจำเป็น และวงเงิน', 1],
            ['official_travel', 'ขออนุมัติเดินทางไปราชการ', 'approval', 'สถานที่ วันเดินทาง ผู้ร่วมเดินทาง และค่าใช้จ่าย', 1],
            ['overtime', 'ขออนุมัติปฏิบัติงานนอกเวลาราชการ', 'approval', 'วันเวลา ภารกิจ และผู้ปฏิบัติงาน', 1],
            ['leave_area', 'ขออนุญาตออกนอกบริเวณโรงเรียน', 'permission', 'เหตุผล วันเวลาออกและกลับ', 0],
            ['use_venue', 'ขออนุญาตใช้สถานที่/ห้องประชุม', 'permission', 'สถานที่ วันเวลา และวัตถุประสงค์', 0],
            ['use_vehicle', 'ขออนุญาตใช้รถยนต์ราชการ', 'permission', 'เส้นทาง วันเวลา ผู้เดินทาง และภารกิจ', 1],
            ['student_trip', 'ขออนุญาตนำนักเรียนออกนอกสถานศึกษา', 'permission', 'สถานที่ กำหนดการ รายชื่อนักเรียน และผู้ควบคุม', 1],
            ['school_activity', 'ขออนุญาตจัดกิจกรรม', 'permission', 'รายละเอียดกิจกรรม กลุ่มเป้าหมาย และการดูแลความปลอดภัย', 1],
        ];
        foreach ($types as $type) {
            $this->db->table('approval_types')->ignore(true)->insert([
                'code' => $type[0], 'name' => $type[1], 'category' => $type[2],
                'description' => $type[3], 'requires_memo' => $type[4] ?? 1, 'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    public function down()
    {
        $this->forge->dropTable('approval_request_steps', true);
        $this->forge->dropTable('approval_requests', true);
        $this->forge->dropTable('approval_type_steps', true);
        $this->forge->dropTable('approval_types', true);
    }
}
