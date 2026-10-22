-- e-Saraban: ระบบขออนุมัติและขออนุญาต
-- สำรองฐานข้อมูลก่อนรัน และเลือกฐานข้อมูล skjacth_saraban
SET NAMES utf8mb4;
START TRANSACTION;

CREATE TABLE IF NOT EXISTS approval_types (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  code VARCHAR(50) NOT NULL,
  name VARCHAR(180) NOT NULL,
  category ENUM('approval','permission') NOT NULL DEFAULT 'approval',
  description TEXT NULL,
  requires_memo TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=ต้องบันทึกข้อความก่อน, 0=กรอกแบบฟอร์มได้ทันที',
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_approval_type_code (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS approval_type_steps (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  approval_type_id INT UNSIGNED NOT NULL,
  step_order INT UNSIGNED NOT NULL,
  step_name VARCHAR(120) NOT NULL,
  approver_user_id INT UNSIGNED NOT NULL,
  created_at DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_approval_type_step (approval_type_id, step_order),
  KEY idx_approval_type_step_user (approver_user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS approval_requests (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  document_id INT UNSIGNED NULL COMMENT 'อ้างอิง documents.id เมื่อประเภทนั้นกำหนดให้มีบันทึกข้อความ',
  approval_type_id INT UNSIGNED NOT NULL,
  requester_id INT UNSIGNED NOT NULL,
  title VARCHAR(255) NOT NULL,
  details LONGTEXT NULL,
  purpose TEXT NULL,
  location VARCHAR(255) NULL,
  start_date DATE NULL,
  end_date DATE NULL,
  amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  extra_data LONGTEXT NULL,
  status ENUM('pending','approved','rejected','returned','cancelled') NOT NULL DEFAULT 'pending',
  current_step INT UNSIGNED NOT NULL DEFAULT 1,
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  PRIMARY KEY (id),
  KEY idx_approval_requester_status (requester_id,status),
  KEY idx_approval_request_document (document_id),
  KEY idx_approval_request_type (approval_type_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS approval_request_steps (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  request_id BIGINT UNSIGNED NOT NULL,
  step_order INT UNSIGNED NOT NULL,
  step_name VARCHAR(120) NOT NULL,
  approver_user_id INT UNSIGNED NOT NULL,
  status ENUM('pending','approved','rejected','returned','skipped') NOT NULL DEFAULT 'pending',
  comment TEXT NULL,
  action_at DATETIME NULL,
  created_at DATETIME NULL,
  PRIMARY KEY (id),
  KEY idx_approval_request_step_order (request_id,step_order),
  KEY idx_approval_step_user_status (approver_user_id,status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO approval_types (code,name,category,description,requires_memo,is_active,created_at,updated_at) VALUES
('project','ขออนุมัติดำเนินโครงการ/กิจกรรม','approval','รายละเอียดโครงการ กำหนดการ และผลที่คาดว่าจะได้รับ',1,1,NOW(),NOW()),
('budget','ขออนุมัติใช้จ่ายงบประมาณ','approval','แหล่งงบประมาณ รายการค่าใช้จ่าย และวงเงิน',1,1,NOW(),NOW()),
('procurement','ขออนุมัติจัดซื้อจัดจ้าง','approval','รายการพัสดุ เหตุผลความจำเป็น และวงเงิน',1,1,NOW(),NOW()),
('official_travel','ขออนุมัติเดินทางไปราชการ','approval','สถานที่ วันเดินทาง ผู้ร่วมเดินทาง และค่าใช้จ่าย',1,1,NOW(),NOW()),
('overtime','ขออนุมัติปฏิบัติงานนอกเวลาราชการ','approval','วันเวลา ภารกิจ และผู้ปฏิบัติงาน',1,1,NOW(),NOW()),
('leave_area','ขออนุญาตออกนอกบริเวณโรงเรียน','permission','เหตุผล วันเวลาออกและกลับ',0,1,NOW(),NOW()),
('use_venue','ขออนุญาตใช้สถานที่/ห้องประชุม','permission','สถานที่ วันเวลา และวัตถุประสงค์',0,1,NOW(),NOW()),
('use_vehicle','ขออนุญาตใช้รถยนต์ราชการ','permission','เส้นทาง วันเวลา ผู้เดินทาง และภารกิจ',0,1,NOW(),NOW()),
('student_trip','ขออนุญาตนำนักเรียนออกนอกสถานศึกษา','permission','สถานที่ กำหนดการ รายชื่อนักเรียน และผู้ควบคุม',0,1,NOW(),NOW()),
('school_activity','ขออนุญาตจัดกิจกรรม','permission','รายละเอียดกิจกรรม กลุ่มเป้าหมาย และการดูแลความปลอดภัย',0,1,NOW(),NOW())
ON DUPLICATE KEY UPDATE name=VALUES(name), category=VALUES(category), description=VALUES(description), requires_memo=VALUES(requires_memo), is_active=1, updated_at=NOW();

COMMIT;
