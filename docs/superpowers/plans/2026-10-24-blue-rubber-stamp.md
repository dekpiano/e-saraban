# Realistic Blue Rubber Stamp & Free Draggable Endorsements Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** เปลี่ยนรูปแบบการเกษียนหนังสือราชการจากกล่องข้อความธรรมดา ให้กลายเป็น **"ตราปั๊มตรายางเกษียนหนังสือหมึกน้ำเงินเสมือนจริง (Realistic Blue Rubber Stamp)"** พร้อมฟังก์ชันให้ผู้ลงนามสามารถ **ลากและวาง (Drag & Drop)** ตราปั๊มไปไว้ที่ตำแหน่งใดก็ได้บนหน้ากระดาษ A4 อย่างอิสระ โดยจดจำพิกัดและแสดงผลตรงกัน 100% ทั้งในหน้าตรวจสอบเอกสาร (`admin/documents/:id`), หน้าพิมพ์ A4, และหน้าติดตามงานสาธารณะ (`tracking/:id`)

**Architecture:** 
1. เพิ่มคอลัมน์พิกัด `stamp_x`, `stamp_y`, `stamp_page` ในตาราง `document_routes`
2. อัปเดต Controller `DocumentController::sign` รับและบันทึกพิกัดของตราปั๊ม
3. สร้าง CSS `.saraban-blue-stamp` จำลองรอยประทับตรายางหมึกน้ำเงิน กรอบขอบคู่ เอียง -1.5 องศา ฟอนต์สารบรรณสีน้ำเงินหมึกตรายาง
4. เพิ่ม JavaScript Draggable System (Touch & Mouse Pointer) ใน `documents/view.php` ให้ผู้เซ็นลากวางตราปั๊มบนกระดาษ A4 ได้แบบ Real-Time และซิงค์ข้อความความเห็นสด
5. แสดงตราปั๊มตามพิกัดที่บันทึกไว้ใน `documents/view.php`, `public/track.php`, และหน้าสั่งพิมพ์

---

### Task 1: Database Schema & Model Update

**Files:**
- Database: `document_routes` table
- Modify: `app/Models/DocumentRouteModel.php`

- [x] **Step 1: Check and add `stamp_x`, `stamp_y`, `stamp_page` to `document_routes` table**
  - `stamp_x`: VARCHAR(20) DEFAULT NULL (เก็บค่าเป็น % เช่น `15.5%`)
  - `stamp_y`: VARCHAR(20) DEFAULT NULL (เก็บค่าเป็น % เช่น `72.0%`)
  - `stamp_page`: INT DEFAULT 1
- [x] **Step 2: Update `allowedFields` in `DocumentRouteModel.php`**
  Add `'stamp_x'`, `'stamp_y'`, `'stamp_page'` to `$allowedFields`
- [x] **Step 3: Commit database updates**

---

### Task 2: Controller & Backend Update

**Files:**
- Modify: `app/Controllers/DocumentController.php`

- [x] **Step 1: Update `sign($id)` method in `DocumentController.php`**
  Receive `stamp_x`, `stamp_y`, `stamp_page` from `POST` inputs and pass to `documentRouteModel->update($currentRoute['id'], ...)`
- [x] **Step 2: Provide fallback smart defaults if coordinates are omitted**
  If `stamp_x` or `stamp_y` is empty, set default coordinates (e.g., `stamp_x = '8%'`, `stamp_y = '75%'`)

---

### Task 3: Realistic Blue Rubber Stamp Styling (CSS)

**Files:**
- Modify: `public/assets/css/saraban-memo.css`

- [x] **Step 1: Create `.saraban-blue-stamp` CSS class**
  - Dimensions: Width ~7.6cm, height ~4.2cm
  - Border: Double line border in authentic blue ink (`#1d4ed8` / `#1e40af`, `3px double #1d4ed8`)
  - Rounded corners: `4px`
  - Subtle rotation: `transform: rotate(-1.5deg);`
  - Color: Royal Blue Ink (`#1e3a8a` / `#1d4ed8`)
  - Typography: TH Sarabun 14pt-15pt bold header, 13pt content, 12pt signer
  - Rubber stamp effect: Subtle ink texture, slight opacity, crisp borders
- [x] **Step 2: Add `@media print` rules**
  Ensure `.saraban-blue-stamp` stays in its exact percentage position on A4 printouts

---

### Task 4: Interactive Draggable UI in `documents/view.php`

**Files:**
- Modify: `app/Views/documents/view.php`

- [x] **Step 1: Render Draggable Preview Stamp on Paper Canvas when `$canSign` is active**
  - Display `.saraban-blue-stamp.draggable-stamp` on top of `.saraban-paper-display`
  - Bind drag events (Pointer/Touch/Mouse) with boundary constraints (stays within the paper)
  - Calculate percentage `(left / paperWidth * 100)` and `(top / paperHeight * 100)`
  - Update hidden inputs `stamp_x` and `stamp_y` in `#signForm`
  - Live sync preview text when user changes `comment_preset` or `comment_text`
- [x] **Step 2: Render Signed Blue Stamps for Completed Routes**
  - For each signed route, position its `.saraban-blue-stamp` at its `stamp_x, stamp_y`
  - Include verification badge link
- [x] **Step 3: Test on document 4 (`/admin/documents/4`)**

---

### Task 5: Synchronization to Public Tracking & Memo Views

**Files:**
- Modify: `app/Views/public/track.php`

- [x] **Step 1: Render Blue Rubber Stamps on `public/track.php`**
  Display stamps at their exact coordinates on the A4 paper
- [x] **Step 2: Verify in browser subagent and capture visual proof**
