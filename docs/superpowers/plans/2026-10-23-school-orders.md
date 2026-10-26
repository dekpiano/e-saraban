# School Orders Management System Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** สร้างระบบคำสั่งโรงเรียน (School Orders System) สำหรับจัดทำ ออกเลข คุมทะเบียน พิมพ์ และจัดเก็บไฟล์คำสั่งโรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์ ถอดแบบจาก `แบบฟอร์ม_คำสั่ง.docx` 1:1

**Architecture:** สถาปัตยกรรม MVC บน CodeIgniter 4 โดยใช้ Controller เฉพาะทาง `OrderController` จัดการ Business Logic บันทึกข้อมูลลงตาราง `documents` (`doc_type = 'order'`) พร้อม Audit Log และสร้างหน้าจอ Interactive Paper Form รองรับ Thai Typography และ Print Sheet 1:1

**Tech Stack:** PHP 8+, CodeIgniter 4, MySQL (MariaDB), Bootstrap 5 (Sneat Theme), Boxicons, Google Fonts (K2D & Sarabun), Intl.Segmenter สำหรับตัดคำภาษาไทย

**Spec:** `docs/superpowers/specs/2026-10-23-school-orders-design.md`

## Global Constraints
- **Universal Thai Official Document Standard:** Margins top 1.5cm, left 3cm, right 2cm, bottom 2cm (`padding: 1.5cm 2cm 2cm 3cm;`) ทั้งบนจอและ `@media print`
- **Garuda Center Standard:** ครุฑสูง 3.0 ซม. กว้าง 2.73 ซม. (`assets/img/krut_thai.png`) วางกึ่งกลางหน้ากระดาษด้านบน
- **Typography Standard:** ฟอนต์ TH Sarabun ขนาด 16pt, ย่อหน้า 2.5 ซม. (`text-indent: 2.5cm`), Single line-height 1.25, Thai Distribute กระจายสม่ำเสมอ
- **Color Contrast Standard:** หัวข้อสีเข้ม `#0f172a`, ป้าย Solid Badges ตัดกับพื้นหลัง 100% ห้ามใช้สีขาวบนขาวเด็ดขาด
- **Dual Synchronization Rule:** เมื่อปรับปรุงหรือพัฒนาแบบฟอร์ม ต้องอัปเดตทั้งหน้ากรอกข้อมูล (`create.php`, `edit.php`) หน้าพรีวิว (`view.php`) และหน้าพิมพ์ (`print.php`) ให้ตรงกันเสมอ

## Review Focus
1. การคำนวณและรันเลขคำสั่งอัตโนมัติประจำปี พ.ศ. (เช่น `ที่ 1/2569`, `ที่ 2/2569`) ไม่ชนกันเมื่อมีคำสั่งหลายฉบับ
2. การตัดคำภาษาไทย (`Intl.Segmenter`) ในเนื้อหาคำสั่งและย่อหน้า `ทั้งนี้ ตั้งแต่...` ไม่ถูกเบราว์เซอร์ตัดผ่ากลางคำ
3. หน้าพิมพ์ (`print.php`) ต้องแสดงผล 1:1 ตาม `แบบฟอร์ม_คำสั่ง.docx` และไม่มีแถบเมนู ส่วนหัว หรือส่วนท้ายของเว็บหลุดไปในหน้าพิมพ์
4. การอัปโหลดไฟล์แนบคำสั่ง PDF ฉบับจริง และการเปิดอ่าน/ดาวน์โหลดเพื่อนำไปแนบในการขอไปราชการ
5. การลงนาม 1-Click สำหรับผู้อำนวยการโรงเรียน บันทึกรูปลายเซ็นและประวัติการลงนามถูกต้อง

---

### Task 1: Database Migration & Route Setup

**Files:**
- Modify: `app/Config/Routes.php:40-60`
- Create / Run Migration: เพิ่มคอลัมน์เฉพาะสำหรับคำสั่งในตาราง `documents`

**Interfaces:**
- Produces: เส้นทาง URL สำหรับระบบคำสั่งในกลุ่ม `admin`
  - `admin/orders` $\rightarrow$ `OrderController::index`
  - `admin/orders/create` $\rightarrow$ `OrderController::create`
  - `admin/orders/store` $\rightarrow$ `OrderController::store`
  - `admin/orders/view/(:num)` $\rightarrow$ `OrderController::view/$1`
  - `admin/orders/edit/(:num)` $\rightarrow$ `OrderController::edit/$1`
  - `admin/orders/update/(:num)` $\rightarrow$ `OrderController::update/$1`
  - `admin/orders/delete/(:num)` $\rightarrow$ `OrderController::delete/$1`
  - `admin/orders/print/(:num)` $\rightarrow$ `OrderController::printDoc/$1`
  - `admin/orders/upload/(:num)` $\rightarrow$ `OrderController::uploadFile/$1`
  - `admin/orders/sign/(:num)` $\rightarrow$ `OrderController::sign/$1`

- [ ] **Step 1: Check and add order-specific columns to `documents` table**
  Add columns if not existing: `order_effective_text` (VARCHAR 255), `signer_name` (VARCHAR 255), `signer_position` (VARCHAR 255), `order_category` (VARCHAR 100)
- [ ] **Step 2: Add routes in `app/Config/Routes.php` under `admin` group**
  Add full CRUD & action routes for `OrderController`
- [ ] **Step 3: Verify routes with `php spark routes`**
  Ensure all `admin/orders/*` routes are registered cleanly
- [ ] **Step 4: Commit changes**

---

### Task 2: Controller & Business Logic Implementation

**Files:**
- Create: `app/Controllers/OrderController.php`
- Modify: `app/Models/DocumentModel.php` (เพิ่มฟังก์ชัน `getNextOrderNumber($year)`)

**Interfaces:**
- Consumes: `DocumentModel`, `DocumentLogModel`, `UserModel`
- Produces:
  - `getNextOrderNumber(int $year): string` $\rightarrow$ คืนค่าเลขคำสั่งถัดไป เช่น `"1/2569"`
  - `OrderController` methods: `index()`, `create()`, `store()`, `view($id)`, `edit($id)`, `update($id)`, `delete($id)`, `printDoc($id)`, `uploadFile($id)`, `sign($id)`

- [ ] **Step 1: Implement `getNextOrderNumber` in `DocumentModel`**
  Query max order number for `doc_type = 'order'` in the given year and increment
- [ ] **Step 2: Create `OrderController.php` with all required methods**
  Handle authentication, role checking, input validation, CSRF, and audit logging
- [ ] **Step 3: Test controller execution via CLI / PHPUnit or spark**
  Verify `OrderController` loads without syntax errors
- [ ] **Step 4: Commit changes**

---

### Task 3: Universal CSS & Styling for Official School Orders

**Files:**
- Create: `public/assets/css/orders_saraban.css`

**Interfaces:**
- Produces: CSS classes for screen paper `.saraban-order-paper` and print media `@media print`
- Specifications:
  - Page size: A4 portrait (`210mm x 297mm`)
  - Margins: 1.5cm top, 3.0cm left, 2.0cm right, 2.0cm bottom
  - Garuda: width 2.73cm, height 3.0cm, centered
  - Header: 16pt bold, centered
  - Paragraphs: 16pt, text-indent 2.5cm, line-height 1.25, Thai distribute
  - Date & Signature alignment: centered with order title

- [ ] **Step 1: Write `orders_saraban.css` with screen and print styles**
- [ ] **Step 2: Verify font embeddings (TH Sarabun) and print rules**
- [ ] **Step 3: Commit changes**

---

### Task 4: Interactive Order Views (With Folder Prefix)

**Files:**
- Create: `app/Views/orders/orders_index.php` (ทะเบียนคำสั่งโรงเรียน รายการ ค้นหา กรองปี สถิติ)
- Create: `app/Views/orders/orders_create.php` (หน้าสร้างคำสั่ง กระดาษ A4 เสมือนจริง WYSIWYG)
- Create: `app/Views/orders/orders_edit.php` (หน้าแก้ไขคำสั่ง)
- Create: `app/Views/orders/orders_view.php` (หน้ารายละเอียดคำสั่ง แสดงพรีวิว อัปโหลดไฟล์ แนบเอกสาร)
- Create: `app/Views/orders/orders_print.php` (หน้าพิมพ์ทางการ 1:1 ปริ้นต์ได้ทันที)

**Interfaces:**
- Consumes: Data passed from `OrderController`
- Produces: Fully interactive web interfaces matching `AGENTS.md` and `uiux-master-designer`

- [ ] **Step 1: Create `app/Views/orders/orders_index.php`**
  List orders with status badges, year filter, search, quick download and view buttons
- [ ] **Step 2: Create `app/Views/orders/orders_create.php` and `orders_edit.php`**
  Paper form 1:1, auto-running number preview, Thai segmenter button, form submit loading
- [ ] **Step 3: Create `app/Views/orders/orders_view.php`**
  Order preview, PDF viewer modal, upload form, 1-Click sign button for executive
- [ ] **Step 4: Create `app/Views/orders/orders_print.php`**
  Clean A4 layout matching `แบบฟอร์ม_คำสั่ง.docx` 1:1
- [ ] **Step 5: Commit changes**

---

### Task 5: Sidebar Navigation & System Integration

**Files:**
- Modify: `app/Views/layouts/main.php` (เชื่อมโยงเมนู "4. หนังสือสั่งการ -> ทะเบียนคำสั่งโรงเรียน")

**Interfaces:**
- Produces: Active menu state and badge counter for orders

- [ ] **Step 1: Update sidebar in `main.php` to link to `admin/orders`**
- [ ] **Step 2: Add order count badge in sidebar**
- [ ] **Step 3: Commit changes**

---

### Task 6: Verification & Browser Subagent Testing

**Files:**
- Verification via browser and test scripts

- [ ] **Step 1: Verify order creation flow (Create Draft Order with number 1/2569)**
- [ ] **Step 2: Verify print view in browser subagent (Capture screenshot and check 1:1 match with docx)**
- [ ] **Step 3: Verify PDF upload and download**
- [ ] **Step 4: Verify mobile responsiveness and high contrast rules**
