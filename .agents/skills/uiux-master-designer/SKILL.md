---
name: uiux-master-designer
description: >-
  Use this skill whenever designing or implementing user interfaces, web layouts, components, responsive views, color palettes, typography, or interactive UX elements. Ensures stunning, modern, harmonious, and highly usable web design for e-Office and administrative systems.
---

# Master UI/UX & Web Designer Skill (นักดีไซน์เว็บไซต์ขั้นเทพ)

สกิลนี้กำกับการออกแบบอินเทอร์เฟซผู้ใช้และประสบการณ์การใช้งาน (UI/UX) ให้ดูพรีเมียม สวยสะกดตา ทันสมัย และสอดรับกับสัดส่วนการแสดงผลข้อมูลอย่างถูกต้องที่สุด

---

## 1. ปรัชญาการออกแบบ (Rich Aesthetics & Design Principles)
- **Visual Hierarchy ที่ชัดเจน:** ผู้ใช้ต้องรู้ได้ทันทีว่าสิ่งใดสำคัญที่สุด เช่น สถานะของเอกสาร, ขั้นตอนที่กำลังค้างอยู่, และปุ่ม Action หลัก
- **Typography & ฟอนต์ภาษาไทย:**
  - ใช้ฟอนต์ Google Fonts `K2D` สำหรับหัวข้อ, ปุ่ม, เมนู และป้ายสถานะ เพื่อความทันสมัยและดูเป็นมืออาชีพ
  - ใช้ `Sarabun` สำหรับเนื้อหาเอกสารราชการทางการ
  - กำหนด Line-height และ Font-size ให้สมส่วน อ่านง่าย สบายตา
- **Color Palette & Harmonies:**
  - หลีกเลี่ยงสีฉูดฉาดแบบ Default ใช้ธีมสี Soft Modern Palette:
    - Primary: `#696cff` (Indigo Blue - ความทันสมัย มั่นคง)
    - Success: `#71dd37` (Soft Mint Green - อนุมัติ/เสร็จสิ้น)
    - Warning: `#ffab00` (Amber Gold - รอลงนาม/กำลังพิจารณา)
    - Danger: `#ff3e1d` (Coral Red - ส่งคืนแก้ไข/ด่วนที่สุด)
    - Neutral/Slate: `#f5f5f9` สำหรับพื้นหลัง และ `#566a7f` สำหรับข้อความรอง
  - ใช้ `bg-label-*` (Light pastel badges with vibrant text) เพื่อความสบายตา

- **⚠️ กฎเหล็กความชัดเจนของข้อความ (Color Contrast & Readability - สำคัญสูงสุด):**
  - **สีข้อความกับพื้นหลังจะต้องเป็นคนละสีกันเสมอ (High Contrast):** ห้ามใช้สีเดียวกันหรือเฉดสีใกล้เคียงกันโดยเด็ดขาด เพราะจะทำให้มองไม่เห็นตัวหนังสือ
  - **ห้ามใช้พื้นหลังสีขาวโปร่งแสงกับตัวหนังสือสีขาวโดยเด็ดขาด** (เช่น `bg-white bg-opacity-20 text-white`)
  - **บนพื้นหลังสีเข้ม / Banner Gradient:**
    - ป้าย Tag / Pill ให้ใช้: `bg-dark text-white fw-bold shadow-sm` หรือ `style="background-color: rgba(15, 23, 42, 0.75) !important;"`
    - ปุ่มกด Action ให้ใช้: `btn btn-light text-primary fw-bold shadow-sm` หรือ `btn btn-warning text-dark fw-bold`
    - ป้าย Badge สังกัด/ตำแหน่ง ให้ใช้: `badge bg-white text-dark fw-bold px-2 py-1 shadow-sm`
  - **พื้นหลังสีสว่าง (Light / White / Pastel):** ข้อความต้องเป็นสีเข้มชัดเจนเสมอ เช่น สีเทาเข้ม (`#2b3445`), สีกรมท่า (`#1e293b`), หรือสีดำ ห้ามใช้สีขาวหรือสีเทาอ่อนบนพื้นขาว ตัวเลขสถิติใช้สีทึบเข้ม เช่น `#b45309`, `#15803d`, `#0369a1`, `#4338ca`
  - **ระบบ Badge ทั้งหมด:** บังคับใช้ Solid Badges พร้อมตัวหนังสือตัดกันเสมอ:
    - `bg-danger text-white fw-bold`
    - `bg-warning text-dark fw-bold`
    - `bg-success text-white fw-bold`
    - `bg-info text-white fw-bold`
    - `bg-primary text-white fw-bold`
    - `bg-secondary text-white fw-semibold`
  - **ตรวจสอบทุกจุด:** ไม่ว่าจะเป็น Badge, Alert Box, Header, Card, Button, Input Placeholder, และแถบสถานะ ตัวหนังสือต้องคมชัดและอ่านออกได้ทันทีในทุกสภาพแสง

---

## 2. Interactive Components & Micro-Animations
- **Visual Workflow Stepper:**
  - การแสดงสถานะขั้นตอนต้องใช้ Stepper แบบวงกลมเชื่อมต่อกัน พร้อม Pulse animation สำหรับขั้นตอนที่กำลังรอพิจารณา (Active Step)
- **Glassmorphism & Card Shadows:**
  - ใช้เงาที่นุ่มนวล `box-shadow: 0 4px 20px rgba(0,0,0,0.05)` และขอบมน `border-radius: 0.75rem` - `1rem`
  - การ์ดสถิติ (Stat Widgets) มี Hover Effect ยกตัวขึ้นเล็กน้อย (`transform: translateY(-3px)`)
- **Responsive & Mobile First:**
  - รองรับหน้าจอคอมพิวเตอร์ แท็บเล็ต และสมาร์ตโฟน
  - ตารางต้องห่อด้วย `.table-responsive` เสมอ เพื่อให้เลื่อนดูได้อย่างราบรื่นบนมือถือ

---

## 4. มาตรฐานหนังสือราชการและบันทึกข้อความ (Thai Official Memo Standards)
- **ขอบกระดาษ (Margins):**
  - ขอบบน: 3 ซม. (`3cm`), ขอบซ้าย: 3 ซม. (`3cm`), ขอบขวา: 2 ซม. (`2cm`), ขอบล่าง: 2 ซม. (`2cm`)
  - กำหนดเป็น `padding: 3cm 2cm 2cm 3cm;` ทั้งบนหน้าจอ (`.saraban-paper`, `.page`) และใน `@media print`
- **ขนาดตัวอักษร (Typography Units in px):**
  - หัวเรื่อง "บันทึกข้อความ": `29px` ตัวหนา กึ่งกลาง
  - ฉลาก "ส่วนราชการ", "ที่", "วันที่", "เรื่อง": `17px` ตัวหนา
  - ฉลาก "เรียน": `16px` ตัวหนา (ไม่ล็อกความกว้าง ให้ใช้ `width: auto; margin-right: 8px;`)
  - เนื้อหาข้อความ ย่อหน้า และช่องกรอก: `16px`
  - ครุฑ: ใช้ไฟล์ `assets/img/krut_thai.png` ลายเส้นขาวดำทางการ คมชัด มีมิติโปร่ง ไม่ทึบดำเป็นปื้น (สูง 1.5 ซม. สำหรับบันทึกข้อความ วางมุมบนซ้าย)
- **ระยะห่างบรรทัดและย่อหน้า (Line Spacing):**
  - ข้อความเนื้อหา: `line-height: 1.50;` ย่อหน้า `text-indent: 2.5cm;` และ `margin-bottom: 15px;`
  - ส่วนลงนาม: เว้นระยะ 3-4 บรรทัด (`margin-top: 65px;` บนจอ และ `2cm` ในการพิมพ์) ชิดขวา (`width: 60%; margin-left: auto;`)
- **ข้อยกเว้นแถวหัวกระดาษ:**
  - แถวหัวกระดาษคงระยะกระชับแบบฟอร์มเดิม (`margin-bottom: 4px;`)
  - เส้นประจุดไข่ปลา (`.editable-field`): กำหนด `line-height: 1.50; min-height: 18px; padding: 0 4px 0px 4px; border-bottom: 1px dotted #000;`
- **ความสะอาดของแบบฟอร์ม:**
  - ไม่แสดงข้อความ "ชั้นความลับ (ถ้ามี)"
  - ไม่ใส่ลายเซ็นจำลอง ให้เว้นช่องว่างสำหรับลงนามจริง ตามด้วยวงเล็บชื่อเต็มและตำแหน่ง
- **การแก้ไขคู่ขนาน (Dual Synchronization):**
  - เวลาแก้ไขแบบฟอร์มราชการ ต้องอัปเดตทั้งหน้าสร้าง/แก้ไข (`create.php`, `edit.php`) และหน้าพรีวิว/พิมพ์ (`view.php`, `print.php`) ควบคู่กับ CSS ส่วนกลาง (`public/assets/css/saraban-memo.css`) เสมอ

