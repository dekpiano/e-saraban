---
name: expert-programmer
description: >-
  Use this skill whenever developing, refactoring, debugging, or reviewing backend code, business logic, API endpoints, or architecture in PHP, CodeIgniter 4, and Docker environments. Ensures clean code, high security, and high performance.
---

# Master Full-Stack Programmer & Backend Architect Skill (นักโปรแกรมเมอร์ขั้นเทพ)

สกิลนี้กำกับการพัฒนาโค้ดระดับ Senior / Lead Software Engineer สำหรับระบบ e-Office สารบรรณอิเล็กทรอนิกส์และคำขอไปราชการ (CodeIgniter 4 + PHP 8 + Docker)

---

## 1. ปรัชญาการพัฒนา (Core Programming Principles)
- **SOLID & Clean Code:** แยกความรับผิดชอบของโค้ดให้ชัดเจน Controller ทำหน้าที่รับ-ส่ง Request/Response และประสานงาน, Model จัดการข้อมูลและการสืบค้น, Views แสดงผลเท่านั้น
- **Strict Security By Default:**
  - **CSRF Protection:** ทุก form แบบ POST ต้องใส่ `<?= csrf_field() ?>` เสมอ
  - **XSS Prevention:** ทุกข้อความที่มาจาก user หรือ database ที่ส่งออกไปยังหน้า view ต้องห่อด้วย `esc(...)` ทุกครั้ง
  - **SQL Injection Prevention:** ใช้ Query Builder หรือ Prepared Statements เสมอ ห้ามนำ input มาต่อ string ใน raw query โดยเด็ดขาด
  - **Role & Auth Validation:** ตรวจสอบ session และสิทธิ์ของผู้ใช้งานใน Controller ก่อนอนุญาตให้ดำเนินการ เช่น ตรวจสอบว่าผู้เซ็นคือผู้ที่อยู่ในขั้นตอน `current_step` นั้นจริง
  - **File Upload Security:** ตรวจสอบ mime-type, นามสกุลไฟล์, ขนาดไฟล์ และสุ่มชื่อไฟล์ (`getRandomName()`) เสมอ ป้องกันการอัปโหลด executable script

---

## 2. มาตรฐาน CodeIgniter 4 Best Practices
1. **Controllers:**
   - ใช้ Helper อย่างเหมาะสม เช่น `helper(['thai_date', 'form', 'url'])`
   - ตอบกลับข้อความแจ้งเตือนผ่าน Flashdata: `->with('success', '...')` หรือ `->with('error', '...')`
   - รักษารูปแบบ Route ให้สอดคล้องกับ RESTful conventions
2. **Models:**
   - กำหนด `$allowedFields` อย่างรัดกุม ป้องกัน Mass Assignment Vulnerability
   - เปิดใช้งาน `$useTimestamps = true` สำหรับตารางที่มี `created_at` และ `updated_at`
   - เขียน Query method เฉพาะใน Model เพื่อ Reusable และหลีกเลี่ยง Logic ซ้ำซ้อนใน Controller
3. **Database Transactions:**
   - เมื่อมีการบันทึกข้อมูลหลายตารางที่เกี่ยวข้องกัน (เช่น อัปเดตสถานะเอกสาร + บันทึกลายเซ็นใน route + บันทึก log) ต้องใช้ Transaction เสมอ:
   ```php
   $db = \Config\Database::connect();
   $db->transStart();
   // ทำการ Insert / Update หลายตาราง
   $db->transComplete();
   if ($db->transStatus() === false) {
       return redirect()->back()->with('error', 'เกิดข้อผิดพลาดในการประมวลผล');
   }
   ```

---

## 3. การทำงานร่วมกับ Docker & Environment
- โครงสร้างโปรเจกต์รันบน Docker Container `saraban_app`
- ซิงค์โค้ดแบบ Realtime ผ่าน Volume Mount `.:/var/www/html`
- จัดการคอนฟิกต่าง ๆ ผ่านไฟล์ `.env` เสมอ ไม่ฮาร์ดโค้ดข้อมูลสำคัญลงในไฟล์โค้ด
- ตรวจสอบ Error Logs ใน `writable/logs/` เมื่อเกิดปัญหาในการทำงาน
