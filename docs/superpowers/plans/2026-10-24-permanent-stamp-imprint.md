# Permanent Stamp Imprint on Inbound Documents Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development or superpowers:executing-plans to implement this plan task-by-task.

**Goal:** เมื่อลงทะเบียนหนังสือรับเข้า (Inbound Document) และเลือกประทับตรายางรับหนังสือ ระบบจะประทับตรายางรับหนังสือ (Receive Stamp) กลืนลงไปเป็นเนื้อเดียวกับไฟล์เอกสารจริงอย่างถาวร (Permanent Imprint) ทั้งไฟล์รูปภาพ (JPG, PNG, WEBP) และไฟล์ PDF พร้อมเก็บไฟล์ประทับตรานี้ไว้ใช้ในระบบตลอดไป

**Architecture:**
1. สร้าง Service `app/Services/DocumentStampService.php` สำหรับประทับตรายางลงบนไฟล์จริง:
   - **กรณีไฟล์รูปภาพ (JPG, PNG, WEBP):** ใช้ PHP GD (`imagecreatefrom...`, `imagettftext` ฟอนต์ `THSarabunIT๙.ttf`, `imageline`, `imagerectangle`) วาดกรอบตรายางคู่สีน้ำเงินเข้ม พิมพ์ข้อความภาษาไทยคมชัดสวยงามตามพิกัดและขนาดเปอร์เซ็นต์ที่ผู้ใช้จัดวาง
   - **กรณีไฟล์ PDF:** ใช้ `setasign/fpdi` และ `setasign/fpdf` นำเข้าหน้า PDF เดิม แล้วแปลงตรายางรับเป็นภาพ PNG โปร่งใสคมชัดสูง (GD) ประทับลงบนหน้าแรกตามพิกัดจริง แล้วเซฟเป็น PDF ใหม่
2. ใน `app/Controllers/InboundDocumentController.php` เมธอด `store()`:
   - เมื่ออัปโหลดไฟล์เสร็จ ตรวจสอบว่า `$hasReceiveStamp == 1` หรือไม่
   - หากมีตรายาง ให้ส่งต่อไปยัง `DocumentStampService::stampInboundDocument(...)`
   - เซฟทับหรือสร้างเป็นไฟล์ประทับตราถาวร (เช่น `inbound_..._stamped.pdf`) และบันทึกพาธไฟล์นี้ลงในฐานข้อมูล `documents.file_path`
3. ใน `inbound_view.php` และส่วนแสดงผล:
   - เนื่องจากตัวไฟล์มีตรายางฝังในเนื้อเอกสารถาวรแล้ว ให้ปิดหรือปรับการแสดง CSS Stamp ทับซ้อน เพื่อไม่ให้เห็นตรายางเบิ้ลซ้ำซ้อน

---

### Task 1: สร้าง `DocumentStampService.php`
- [ ] สร้างคลาส `App\Services\DocumentStampService`
- [ ] ฟังก์ชันคำนวณตำแหน่งพิกัด X, Y, W, H จาก % ของกระดาษ A4
- [ ] เมธอด `stampImage($filePath, $stampData, $coords)` สำหรับวาดตรายางลงรูปภาพ
- [ ] เมธอด `stampPdf($filePath, $stampData, $coords)` สำหรับประทับตรายางลงหน้าแรกของ PDF
- [ ] ทดสอบสร้างไฟล์ประทับตรายางผ่าน CLI / PHP script

### Task 2: เชื่อมโยงใน `InboundDocumentController::store`
- [ ] เรียกใช้ `DocumentStampService` เมื่ออัปโหลดไฟล์และผู้ใช้เลือกโหมด "มีตรายางรับ"
- [ ] ตรวจสอบ error handling กรณีไฟล์เสียหายหรือไม่สามารถประทับได้ ให้ fallback อย่างปลอดภัย
- [ ] บันทึกชื่อไฟล์ใหม่ที่ถูกประทับตราลงฐานข้อมูล

### Task 3: ปรับปรุงหน้าแสดงผลและทดสอบความถูกต้อง
- [ ] ปรับ `inbound_view.php` ไม่ให้แสดง CSS Stamp ซ้ำซ้อนเมื่อไฟล์มีตราประทับถาวรแล้ว
- [ ] ตรวจสอบและทดสอบอัปโหลดทั้งรูปภาพและ PDF บนหน้าเว็บจริง
