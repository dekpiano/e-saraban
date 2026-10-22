-- e-Saraban: แยกขั้นตอนส่งสารบรรณตรวจสอบก่อนออกเลขบันทึกข้อความ
-- สำรองฐานข้อมูลก่อนรัน และรันกับฐานข้อมูลที่ระบบ e-Saraban ใช้งานจริง
ALTER TABLE documents
    MODIFY COLUMN doc_no VARCHAR(100) NULL COMMENT 'เลขที่หนังสือฉบับจริง ออกโดยเจ้าหน้าที่สารบรรณ',
    MODIFY COLUMN status ENUM('draft','pending_saraban','routing','completed','returned','canceled')
        NOT NULL DEFAULT 'draft';
