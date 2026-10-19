-- Schema for School e-Saraban & e-Signature System
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- 1. Departments (กลุ่มสาระ / ฝ่ายงาน)
CREATE TABLE IF NOT EXISTS `departments` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(150) NOT NULL,
  `code` VARCHAR(50) DEFAULT NULL,
  `type` ENUM('management', 'department') DEFAULT 'department' COMMENT 'ฝ่ายบริหาร หรือ กลุ่มสาระฯ',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Positions (ตำแหน่ง)
CREATE TABLE IF NOT EXISTS `positions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `level` INT NOT NULL DEFAULT 4 COMMENT '1=ผอ., 2=รอง ผอ., 3=หัวหน้ากลุ่ม, 4=ครู/เจ้าหน้าที่',
  `can_sign_final` TINYINT(1) DEFAULT 0 COMMENT 'อนุมัติ/สั่งการขั้นสุดท้ายได้หรือไม่',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Users (ผู้ใช้งานในระบบ)
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `prefix` VARCHAR(20) DEFAULT 'นาย',
  `first_name` VARCHAR(100) NOT NULL,
  `last_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) DEFAULT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `department_id` INT DEFAULT NULL,
  `position_id` INT DEFAULT NULL,
  `role` ENUM('admin', 'saraban', 'executive', 'teacher') DEFAULT 'teacher',
  `signature_img` VARCHAR(255) DEFAULT NULL COMMENT 'Path to default signature file',
  `signature_pin` VARCHAR(60) DEFAULT NULL COMMENT 'PIN code for 1-click signing',
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`department_id`) REFERENCES `departments`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`position_id`) REFERENCES `positions`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Documents (ทะเบียนหนังสือราชการ)
CREATE TABLE IF NOT EXISTS `documents` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `doc_type` ENUM('in', 'out', 'internal', 'order', 'circular') NOT NULL DEFAULT 'in' COMMENT 'in=หนังสือรับ, out=หนังสือส่ง, internal=บันทึกข้อความ, order=คำสั่ง, circular=หนังสือเวียน',
  `doc_no` VARCHAR(100) NOT NULL COMMENT 'เลขที่หนังสือ (เช่น สพม.จบ/1234 หรือ ศธ 04xxx/...)',
  `internal_reg_no` VARCHAR(50) DEFAULT NULL COMMENT 'เลขทะเบียนรับ/ส่งภายใน รร. (เช่น 012/2569)',
  `year` INT NOT NULL DEFAULT 2569 COMMENT 'ปี พ.ศ.',
  `title` VARCHAR(255) NOT NULL COMMENT 'เรื่อง',
  `urgency` ENUM('normal', 'urgent', 'very_urgent', 'most_urgent') DEFAULT 'normal' COMMENT 'ความเร่งด่วน',
  `secret_level` ENUM('normal', 'confidential', 'top_secret') DEFAULT 'normal' COMMENT 'ชั้นความลับ',
  `from_org` VARCHAR(200) NOT NULL COMMENT 'จาก',
  `to_org` VARCHAR(200) NOT NULL COMMENT 'ถึง',
  `doc_date` DATE NOT NULL COMMENT 'ลงวันที่ในหนังสือ',
  `receive_date` DATE DEFAULT NULL COMMENT 'วันที่รับเอกสาร',
  `content` LONGTEXT DEFAULT NULL COMMENT 'เนื้อหาบันทึกข้อความแบบร่าง/พิมพ์เสร็จ',
  `memo_department` VARCHAR(255) DEFAULT NULL COMMENT 'ส่วนราชการเจ้าของเรื่อง',
  `memo_contact` VARCHAR(100) DEFAULT NULL COMMENT 'เบอร์โทร/ต่อ',
  `file_path` VARCHAR(255) DEFAULT NULL COMMENT 'ไฟล์ PDF แนบ (ถ้ามี)',
  `status` ENUM('draft', 'routing', 'completed', 'returned', 'canceled') DEFAULT 'routing',
  `current_step` INT DEFAULT 1 COMMENT 'ขั้นตอนปัจจุบัน',
  `created_by` INT NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Document Routes (เส้นทางการเกษียนและลงนาม / Workflow Step)
CREATE TABLE IF NOT EXISTS `document_routes` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `document_id` INT NOT NULL,
  `step_order` INT NOT NULL DEFAULT 1,
  `user_id` INT NOT NULL COMMENT 'ผู้มีหน้าที่เกษียน/ลงนามในขั้นตอนนี้',
  `status` ENUM('pending', 'signed', 'returned', 'bypassed') DEFAULT 'pending',
  `comment_preset` VARCHAR(100) DEFAULT NULL COMMENT 'ข้อความสำเร็จรูป เช่น เพื่อโปรดพิจารณา, เห็นควรอนุมัติ',
  `comment_text` TEXT DEFAULT NULL COMMENT 'ข้อความเกษียน/บันทึกสั่งการ',
  `signature_img` VARCHAR(255) DEFAULT NULL COMMENT 'รูปลายเซ็นที่ใช้',
  `signature_type` ENUM('draw', 'preset') DEFAULT 'draw',
  `signed_at` DATETIME DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `verify_hash` VARCHAR(64) DEFAULT NULL COMMENT 'SHA256 hash สำหรับตรวจสอบความถูกต้องของลายเซ็น',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`document_id`) REFERENCES `documents`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Document Logs (Audit Trail)
CREATE TABLE IF NOT EXISTS `document_logs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `document_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `action` VARCHAR(50) NOT NULL COMMENT 'CREATE, ROUTE, VIEW, SIGN, RETURN, COMPLETE',
  `detail` TEXT DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`document_id`) REFERENCES `documents`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed Data (ข้อมูลเริ่มต้น)
INSERT INTO `departments` (`id`, `name`, `code`, `type`) VALUES
(1, 'ฝ่ายบริหารงานทั่วไป (งานสารบรรณ)', 'GEN', 'management'),
(2, 'ฝ่ายบริหารงานบุคคล', 'HR', 'management'),
(3, 'ฝ่ายบริหารงบประมาณและแผนงาน', 'PLAN', 'management'),
(4, 'ฝ่ายบริหารงานวิชาการ', 'ACAD', 'management'),
(5, 'กลุ่มสาระการเรียนรู้วิทยาศาสตร์และเทคโนโลยี', 'SCI', 'department'),
(6, 'กลุ่มสาระการเรียนรู้คณิตศาสตร์', 'MATH', 'department'),
(7, 'กลุ่มสาระการเรียนรู้ภาษาไทย', 'THAI', 'department'),
(8, 'กลุ่มสาระการเรียนรู้ภาษาต่างประเทศ', 'ENG', 'department')
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

INSERT INTO `positions` (`id`, `name`, `level`, `can_sign_final`) VALUES
(1, 'ผู้อำนวยการโรงเรียน', 1, 1),
(2, 'รองผู้อำนวยการฝ่ายบริหารงานทั่วไป', 2, 0),
(3, 'รองผู้อำนวยการฝ่ายวิชาการ', 2, 0),
(4, 'หัวหน้ากลุ่มสาระการเรียนรู้', 3, 0),
(5, 'เจ้าหน้าที่งานสารบรรณ', 4, 0),
(6, 'ครูผู้สอน', 4, 0),
(7, 'ผู้ดูแลระบบ (System Administrator)', 4, 0)
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

-- Default Users (Password: 123456)
-- Hash for '123456' using password_hash('123456', PASSWORD_BCRYPT) is:
-- $2y$10$w0B0h3X7/5g1oWvhx8n52ez3bH/Q6cT6t3G2j1r.9Y6lqW8JmUuUe or standard bcrypt
INSERT INTO `users` (`id`, `username`, `password`, `prefix`, `first_name`, `last_name`, `email`, `department_id`, `position_id`, `role`) VALUES
(1, 'director', '$2y$10$3YmG.568Yl0QpP.kF5yM/uX0h4y6x0Z3r0Yv7r7C/3F8w2c1B.p6W', 'ดร.', 'สมศักดิ์', 'รักการเรียน (ผอ.)', 'director@school.ac.th', 1, 1, 'executive'),
(2, 'deputy_gen', '$2y$10$3YmG.568Yl0QpP.kF5yM/uX0h4y6x0Z3r0Yv7r7C/3F8w2c1B.p6W', 'นาย', 'วิชัย', 'ปัญญาดี (รอง ผอ. ทั่วไป)', 'deputy.gen@school.ac.th', 1, 2, 'executive'),
(3, 'deputy_acad', '$2y$10$3YmG.568Yl0QpP.kF5yM/uX0h4y6x0Z3r0Yv7r7C/3F8w2c1B.p6W', 'นาง', 'พรทิพย์', 'วิชาการเลิศ (รอง ผอ. วิชาการ)', 'deputy.acad@school.ac.th', 4, 3, 'executive'),
(4, 'head_sci', '$2y$10$3YmG.568Yl0QpP.kF5yM/uX0h4y6x0Z3r0Yv7r7C/3F8w2c1B.p6W', 'นาย', 'อนันต์', 'ทดลองเก่ง (หัวหน้ากลุ่มสาระฯ วิทย์)', 'head.sci@school.ac.th', 5, 4, 'teacher'),
(5, 'saraban', '$2y$10$3YmG.568Yl0QpP.kF5yM/uX0h4y6x0Z3r0Yv7r7C/3F8w2c1B.p6W', 'นางสาว', 'สุดา', 'เอกสารดี (เจ้าหน้าที่สารบรรณ)', 'saraban@school.ac.th', 1, 5, 'saraban'),
(6, 'teacher1', '$2y$10$3YmG.568Yl0QpP.kF5yM/uX0h4y6x0Z3r0Yv7r7C/3F8w2c1B.p6W', 'นาย', 'เอกชัย', 'ขยันสอน (ครูผู้สอน)', 'teacher1@school.ac.th', 5, 6, 'teacher'),
(7, 'admin', '$2y$10$8HSeZN.F6qeKa5TL71ROGuQeKp/Q5TLiV3TohOUpvrLfWiVw1UAsW', 'นาย', 'แอดมิน', 'ผู้ดูแลระบบ', 'admin@school.ac.th', 1, 7, 'admin')
ON DUPLICATE KEY UPDATE `username`=VALUES(`username`);

SET FOREIGN_KEY_CHECKS = 1;
