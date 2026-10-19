---
name: database-architect
description: >-
  Use this skill whenever designing tables, defining schemas, creating migrations, writing complex queries, adding indexes, or structuring relationships for MySQL and relational databases in the e-Office system. Ensures normalized, high-performance, and scalable data architecture.
---

# Master Database Architect Skill (นักออกแบบฐานข้อมูลขั้นเทพ)

สกิลนี้กำกับการออกแบบและจัดการฐานข้อมูลเชิงสัมพันธ์ (Relational Database) สำหรับระบบ e-Office ให้ถูกต้อง สมส่วน ได้มาตรฐานระดับสากล

---

## 1. กฎการออกแบบโครงสร้างฐานข้อมูล (Database Normalization & Standards)
- **Normal Form (3NF):** ตารางต้องอยู่ในรูปแบบที่ลดความซ้ำซ้อน ไม่เก็บข้อมูลที่สามารถคำนวณหรือ join ได้ซ้ำหลายที่
- **Naming Conventions:**
  - ชื่อตาราง: ใช้พหูพจน์ตัวพิมพ์เล็ก คั่นด้วย underscore เช่น `documents`, `document_routes`, `document_logs`, `travel_requests`
  - คอลัมน์ Primary Key: ใช้ `id` แบบ AUTO_INCREMENT (BIGINT หรือ INT UNSIGNED)
  - Foreign Key: ใช้ `<table_singular>_id` เช่น `user_id`, `document_id`, `department_id`
  - สถานะ: ใช้ ENUM หรือ VARCHAR ขนาดสั้น เช่น `status` ('pending', 'approved', 'returned', 'routing', 'completed')
  - เวลา: ใช้ `created_at`, `updated_at`, `deleted_at` (DATETIME หรือ TIMESTAMP)

---

## 2. การจัดการความสัมพันธ์และ Integrity (Relationships & Constraints)
- **One-to-Many & Workflow Routing:**
  - `documents` (1) ── (N) `document_routes`: เก็บลำดับขั้นตอนการเกษียนเอกสาร โดยมี `step_order` เรียงจาก 1, 2, 3...
  - `documents` (1) ── (N) `document_logs`: บันทึก Audit Trail ทุกการกระทำ (สร้าง, อัปโหลด, เซ็น, ส่งคืน, ปิดเรื่อง)
  - `users` (1) ── (N) `positions` / `departments`: จัดการโครงสร้างองค์กรโรงเรียน
- **Cascade & Foreign Key Integrity:**
  - กำหนด Foreign Keys ชัดเจน หรือจัดการ soft/hard cascade อย่างระมัดระวัง เพื่อป้องกัน Orphan Records

---

## 3. ดัชนีและการเพิ่มประสิทธิภาพ (Indexing & Performance Optimization)
- **Composite Indexes สำหรับ Query ที่พบบ่อย:**
  - คอลัมน์ที่ถูกค้นหาใน `WHERE` บ่อยๆ เช่น `document_routes(document_id, step_order, status)`
  - คอลัมน์ที่ถูกใช้ในตัวกรองหน้าแรก เช่น `documents(status, doc_type, created_at)`
  - คอลัมน์สำหรับ Authentication: `users(username, is_active)`
- **Fulltext Search:**
  - สำหรับฟิลด์ข้อความยาวหรือค้นหาหัวข้อเรื่อง เช่น `documents(title, doc_no)`

---

## 4. Audit Trail & Digital Verification
- ทุกเอกสารต้องมีตาราง Audit Log บันทึก `user_id`, `action`, `detail`, `created_at` และ `ip_address`
- การลงนามดิจิทัลต้องเก็บ `verify_hash` (เช่น SHA-256) เพื่อให้สามารถสแกน QR Code ตรวจสอบความถูกต้องของเอกสารได้ตลอดเวลา
