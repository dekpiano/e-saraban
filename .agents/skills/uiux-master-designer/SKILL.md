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

## 4. มาตรฐานสากลสำหรับแบบฟอร์มเอกสารราชการทุกระบบ (Universal Thai Official Document Standards)
ยึดถือมาตรฐานนี้เป็น **กฎเหล็กถาวร** สำหรับทุกเอกสารราชการในระบบ (บันทึกข้อความ, ขอไปราชการ, รายงานผลการปฏิบัติงาน, หนังสือคำสั่ง, ประกาศ ฯลฯ):
- **ขอบกระดาษ (Page Margins):**
  - ขอบบน: 1.5 ซม. (`1.5cm`), ขอบซ้าย: 3 ซม. (`3cm`), ขอบขวา: 2 ซม. (`2cm`), ขอบล่าง: 2 ซม. (`2cm`)
  - กำหนดเป็น `padding: 1.5cm 2cm 2cm 3cm;` ทั้งบนหน้าจอ (`.saraban-paper`, `.page`) และใน `@media print` (`@page { size: A4 portrait; margin: 0; }`)
- **Typography & ขนาดตัวอักษร (ใช้หน่วย pt เพื่อให้เทียบเท่าสเกลของ Microsoft Word 1:1 ทั้งหน้าจอและหน้าพิมพ์ตามไฟล์ แบบฟอร์ม_หนังสือภายใน (1).docx):**
  - ฟอนต์: `TH SarabunIT๙`, `TH Sarabun IT๙`, `THSarabunIT9`, `TH Sarabun New`, `TH Sarabun PSK`, `Sarabun`, sans-serif (ฝังแบบ Base64 แสดงตัวเลขอารบิกเป็นเลขไทยอัตโนมัติตามแบบฟอร์ม Word)
  - หัวเรื่อง "บันทึกข้อความ": `29pt` ตัวหนา กึ่งกลาง ระยะห่างบรรทัดค่าแน่นอน `35pt` (`line-height: 35pt; padding-top: 10px;` ขยับลงมาให้ชิดด้านล่างของระดับครุฑอย่างสง่างาม)
  - ฉลากหัวเรื่อง "ส่วนราชการ", "ที่", "วันที่", "เรื่อง": `20pt` ตัวหนา (`font-size: 20pt; font-weight: bold;`)
  - ฉลาก "เรียน": `16pt` ตัวปกติ (ไม่ล็อกความกว้าง ให้ใช้ `width: auto; margin-right: 8px;`, `margin-top: 6pt;`)
  - เนื้อหาข้อความ ย่อหน้า และช่องกรอก: `16pt` ตัวปกติ (`font-size: 16pt;`)
  - ครุฑ: ใช้ไฟล์ `assets/img/krut_thai.png` ลายเส้นขาวดำทางการ คมชัด มีมิติโปร่ง ไม่ทึบดำเป็นปื้น (สูง 1.5 ซม. กว้าง 1.37 ซม. สำหรับบันทึกข้อความ วางมุมบนซ้าย)
- **ระยะห่างบรรทัดและย่อหน้า (Line Spacing & Paragraphs):**
  - เนื้อหาทั่วไป: Single Spacing ตาม Microsoft Word `line-height: 1.25;` สระและวรรณยุกต์ไทยโปร่ง อ่านง่าย บรรทัดไม่บวม
  - ย่อหน้าข้อความ: `text-indent: 2.5cm;` (สำหรับทุกย่อหน้าของ `<p>`)
  - ระยะห่างระหว่างย่อหน้าและก่อนขึ้นย่อหน้า (Enter Spacing): กำหนดเป็น `10pt` (`--paragraph-spacing: 10pt;` / `margin-bottom: 10pt;`, `margin-top: 10pt;`)
  - ส่วนลงนาม (Signature): เว้นระยะ 4 บรรทัด (4 Enter / `margin-top: 48pt;` ทั้งบนหน้าจอและในการพิมพ์) กำหนด `padding-left: 9.0cm;` และ `.signature-box { transform: translateX(-50%); }` ให้แนวกึ่งกลางของชื่อ-ตำแหน่ง อยู่ตรงแนวดิ่งกับคำว่า "ข้อความ" ของหัวเรื่อง "บันทึกข้อความ" พอดีเป๊ะตามความต้องการของผู้ใช้งาน และจัดตำแหน่ง (Position) ให้อยู่กึ่งกลางชื่อเสมอ โดยไม่มีข้อความกลุ่มสาระการเรียนรู้
- **ข้อยกเว้นแถวหัวกระดาษ (Header Block Exception):**
  - แถวหัวกระดาษคงระยะกระชับแบบฟอร์มเดิม (`margin-bottom: 4px;`)
  - เส้นประจุดไข่ปลา (`.editable-field`): กำหนด `line-height: 1.1; min-height: 20px; padding: 0 4px 0px 4px; border-bottom: 1px dotted #000;` (ดึงเส้นประชิดติดฐานตัวอักษร กระชับสวยงาม ไม่ทับสระล่าง)
- **ความสะอาดของแบบฟอร์มราชการ (Form Cleanliness):**
  - ไม่แสดงข้อความ "ชั้นความลับ (ถ้ามี)" ทั้งด้านบนและด้านล่างของเอกสาร
  - ไม่ใส่ลายเซ็นจำลอง ให้เว้นช่องว่างสำหรับลงนามจริง ตามด้วยวงเล็บชื่อเต็ม `( นาย/นาง/นางสาว... )` และตำแหน่ง (จัดตำแหน่งให้อยู่กึ่งกลางชื่อ และไม่ใส่กลุ่มสาระการเรียนรู้)
- **การตัดคำและการจัดระเบียบภาษาไทย (Thai Typography & Word Wrap Standards):**
  - กำหนดเนื้อหาให้ยื้อขอบซ้าย-ขวาเท่ากันเสมอแบบกระจายราชการ (Thai Distributed): `text-align: justify; text-justify: inter-character; text-align-last: left;`
  - ล็อกกลุ่มคำสำคัญห้ามแตกแถว (Non-breaking): ชื่อ-นามสกุล, วันที่-เดือน-ปี, และตัวเลข+หน่วยนับ
  - จัดการตัดคำไทยด้วย `Intl.Segmenter` และแทรก Zero-Width Space (`\u200B`) ที่รอยต่อคำ
- **การแก้ไขคู่ขนาน (Dual Synchronization Rule):**
  - **"เวลา แก้ไข ต้อง แก้ไข ทั้งหน้าพิมพ์ และ หน้าใส่ข้อมูล"** เมื่อปรับปรุงหรือพัฒนาแบบฟอร์มราชการ ต้องอัปเดตไฟล์ทั้งฝั่งสร้าง/แก้ไข (`create.php`, `edit.php`), ฝั่งพรีวิว/พิมพ์ (`view.php`, `print.php`) และไฟล์ CSS ส่วนกลาง (`public/assets/css/saraban-memo.css`) ให้สอดคล้องตรงกันเสมอ

