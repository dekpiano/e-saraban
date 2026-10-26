# Remote File Upload Service Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** เปลี่ยนแปลงระบบจัดเก็บไฟล์แนบทั้งหมดของระบบ e-saraban ไปยังเซิร์ฟเวอร์ภายนอก (Remote Upload Server: skj.nsnpao.go.th) ผ่าน `RemoteUploadService` กลาง

**Architecture:** สร้าง `App\Services\RemoteUploadService` เพื่อทำหน้าที่เชื่อมต่อกับ HTTP Upload/Delete API (`upload.php` / `delete.php`) พร้อมระบบ Chunked Upload สำหรับไฟล์ขนาดใหญ่ ปรับจูน `InboundDocumentController` ให้ประทับตราบน Temp File ก่อนส่งขึ้น Cloud, ปรับ `OrderController`, `DocumentController`, `InternalMemoController` และ Refactor โมดูลไปราชการให้ใช้ Service เดียวกันทั้งหมด พร้อมตรวจสอบ View ให้แสดงผลผ่าน `document_url()` 100%

**Tech Stack:** PHP 8+, CodeIgniter 4 cURLRequest, Multipart POST, FPDI / TCPDF (Document Stamp), Docker (`saraban_app`)

**Spec:** `docs/superpowers/specs/2026-10-26-remote-file-upload-design.md`

## Global Constraints
- ต้องใช้ Configuration จาก `.env`: `upload.server.url`, `upload.server.delete.url`, `upload.server.token`, `upload.server.baseurl`
- ต้องรองรับ Chunked Upload (ไม่เกิน 750 KB ต่อ chunk) เมื่อขนาดไฟล์เกิน 750 KB ป้องกัน Nginx 413
- ไฟล์ที่ประทับตราดิจิทัล (Permanent Stamp Imprint) ต้องสแตมป์ให้เสร็จสมบูรณ์ก่อนอัปโหลดขึ้น Remote Server
- การแสดงผลและลิงก์ใน View ทั้งหมดต้องใช้ Helper `document_url($filePath)` เท่านั้น ห้าม Hardcode `base_url()` สำหรับไฟล์ที่อัปโหลด

## Review Focus
1. **ไฟล์ PDF ขนาดใหญ่ (> 10MB):** ต้องแบ่ง Chunk และอัปโหลดสำเร็จ ไม่เกิด Timeout หรือหน่วยความจำเกิน
2. **การประทับตราหนังสือรับเข้า:** ไฟล์ที่ถูกส่งขึ้นเซิร์ฟเวอร์ปลายทางต้องมีภาพตรายางประทับติดในเนื้อหา PDF จริง
3. **การลบหรือแทนที่ไฟล์เดิม:** เมื่อผู้ใช้เปลี่ยนไฟล์แนบในคำสั่งหรือเอกสาร ระบบต้องสั่งลบไฟล์เก่าบน Remote Server ผ่าน `delete.php`
4. **ความเข้ากันได้ย้อนหลัง (Backward Compatibility):** ไฟล์เก่าที่เคยเซฟเป็น `uploads/documents/...` บนเครื่อง ต้องยังสามารถเปิดดูได้ผ่าน `document_url()`
5. **ความปลอดภัย:** ส่ง `X-Auth-Token` ที่ถูกต้องในทุก Request ไปยัง Remote Upload Server

---

### Task 1: สร้าง `App\Services\RemoteUploadService`

**Files:**
- Create: `app/Services/RemoteUploadService.php`

**Interfaces:**
- Produces:
  - `uploadUploadedFile(\CodeIgniter\HTTP\Files\UploadedFile $file, string $remoteSubPath, ?string $desiredFilename = null): array`
  - `uploadLocalFile(string $localFullPath, string $remoteSubPath, string $desiredFilename, ?string $mimeType = null): array`
  - `deleteFile(string $remoteFilePath): bool`
  - `pingServer(): bool`

- [ ] **Step 1: เขียนโค้ด `RemoteUploadService.php`**
  สร้างคลาสที่อ่าน `.env`, รองรับ `uploadUploadedFile` (direct upload และ chunked upload), `uploadLocalFile` (อ่านไฟล์จากเครื่องแบ่ง chunk ส่งไปประกอบที่ server ปลายทาง), `deleteFile` และ `pingServer`.
- [ ] **Step 2: ทดสอบการเชื่อมต่อ API (`pingServer`) ผ่าน Docker CLI**
  ทดสอบยิง Ping ไปยัง `upload.php?ping=1` หรือตรวจสอบ Token ผ่าน Docker container `saraban_app`.
- [ ] **Step 3: Commit**

---

### Task 2: ตรวจสอบและปรับปรุง `document_url()` ใน `thai_date_helper.php`

**Files:**
- Modify: `app/Helpers/thai_date_helper.php:222-240`

**Interfaces:**
- Consumes: `env('upload.server.baseurl')`
- Produces: `document_url(?string $filePath): string`

- [ ] **Step 1: ปรับแต่ง `document_url()` ให้ครอบคลุมทุก Subpath**
  รองรับทั้ง `saraban/...`, `travel/...`, URL เต็ม (`http/https`), และ Fallback สำหรับไฟล์ Local (`uploads/...`).
- [ ] **Step 2: ทดสอบการแปลง URL ด้วยชุดทดสอบ Input ต่างๆ**
- [ ] **Step 3: Commit**

---

### Task 3: เชื่อมต่อระบบหนังสือรับ (`InboundDocumentController`) กับ `RemoteUploadService`

**Files:**
- Modify: `app/Controllers/InboundDocumentController.php`
- Modify: `app/Views/documents/inbound_view.php`

**Interfaces:**
- Consumes: `App\Services\RemoteUploadService`, `App\Services\DocumentStampService`
- Produces: ค่า `file_path` และ `raw_file_path` ในรูปแบบ `saraban/inbound/<filename>`

- [ ] **Step 1: ปรับ Logic อัปโหลดใน `InboundDocumentController::store`**
  - เซฟไฟล์ลงในโฟลเดอร์ Temp ชั่วคราวบนเครื่อง
  - หากเลือกให้มีตรายาง (`hasStamp === 1`) ให้ประทับตราบน Temp File ด้วย `DocumentStampService`
  - ส่งทั้งไฟล์ประทับตรา และไฟล์ Raw ขึ้น Remote Server ที่โฟลเดอร์ `saraban/inbound`
  - ลบ Temp File ในเครื่องออก
  - บันทึกพาธสัมพัทธ์ `saraban/inbound/...` ลงใน Database
- [ ] **Step 2: ปรับฟังก์ชัน Restamp / Edit Stamp ใน `InboundDocumentController` ให้รองรับไฟล์จาก Remote Server**
- [ ] **Step 3: ปรับแต่ง `inbound_view.php` ให้ใช้ `document_url()` สำหรับพรีวิวและดาวน์โหลด**
- [ ] **Step 4: ทดสอบกระบวนการลงทะเบียนหนังสือรับเข้าและตรวจสอบไฟล์บน Remote Server**
- [ ] **Step 5: Commit**

---

### Task 4: เชื่อมต่อระบบคำสั่งโรงเรียน (`OrderController`) กับ `RemoteUploadService`

**Files:**
- Modify: `app/Controllers/OrderController.php`
- Modify: `app/Views/orders/orders_index.php`
- Modify: `app/Views/orders/orders_view.php`

**Interfaces:**
- Consumes: `App\Services\RemoteUploadService`
- Produces: บันทึก `file_path` เป็น `saraban/orders/<filename>`

- [ ] **Step 1: ปรับ Logic อัปโหลดใน `OrderController::store` และ `OrderController::updateFile`**
  - อัปโหลดไฟล์คำสั่งขึ้น Remote Server ไปยัง `saraban/orders`
  - ลบไฟล์เดิมบน Remote Server หากเป็นการเปลี่ยนไฟล์แนบใหม่
- [ ] **Step 2: ตรวจสอบและเปลี่ยน `base_url($o['file_path'])` ใน `orders_index.php` และ `orders_view.php` เป็น `document_url(...)`**
- [ ] **Step 3: ทดสอบอัปโหลดและเปิดดูไฟล์คำสั่ง**
- [ ] **Step 4: Commit**

---

### Task 5: เชื่อมต่อระบบเอกสารทั่วไปและบันทึกข้อความ (`DocumentController`, `InternalMemoController`)

**Files:**
- Modify: `app/Controllers/DocumentController.php`
- Modify: `app/Controllers/InternalMemoController.php`
- Modify: `app/Views/documents/view.php`
- Modify: `app/Views/memos/view.php`

**Interfaces:**
- Consumes: `App\Services\RemoteUploadService`
- Produces: บันทึก `file_path` เป็น `saraban/documents/...` และ `saraban/memos/...`

- [ ] **Step 1: ปรับ `DocumentController` ให้ใช้อัปโหลดขึ้น `saraban/documents`**
- [ ] **Step 2: ปรับ `InternalMemoController` ให้ใช้อัปโหลดขึ้น `saraban/memos`**
- [ ] **Step 3: ปรับ Views ทั้งหมดให้เปิดดูผ่าน `document_url(...)`**
- [ ] **Step 4: ทดสอบการสร้างและเปิดดูเอกสาร**
- [ ] **Step 5: Commit**

---

### Task 6: Refactor โมดูลไปราชการ (`TravelController`, `TravelReportController`) ให้ใช้ Service กลาง

**Files:**
- Modify: `app/Controllers/TravelController.php`
- Modify: `app/Controllers/TravelReportController.php`

**Interfaces:**
- Consumes: `App\Services\RemoteUploadService`

- [ ] **Step 1: แทนที่ Private Methods `_uploadFileToServer` และ `_deleteFileFromServer` ใน `TravelController` ด้วย `RemoteUploadService`**
- [ ] **Step 2: แทนที่ Private Methods ใน `TravelReportController` ด้วย `RemoteUploadService`**
- [ ] **Step 3: ตรวจสอบการทำงานของระบบไปราชการและรายงานผล**
- [ ] **Step 4: Commit**

---

### Task 7: ตรวจสอบทั้งระบบ (Full Verification)

- [ ] **Step 1: ค้นหาคำสั่ง `base_url` ที่ใช้งานกับ `$document['file_path']` หรือไฟล์แนบตกค้างในทั้งโปรเจกต์**
- [ ] **Step 2: ทดสอบรัน PHP Syntax Check ทุกไฟล์ที่แก้ไข**
- [ ] **Step 3: ทดสอบการทำงาน End-to-End**
