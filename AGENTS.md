# Core AI Development Guidelines & Tri-Master Skills (e-Office School System)

เอกสารนี้รวบรวมและกำหนดมาตรฐานสูงสุด 3 ด้านสำหรับ AI ในการพัฒนา ปรับปรุง และออกแบบระบบ e-Office สารบรรณและติดตามงานราชการ:

---

## 🧙‍♂️ 1. มาตรฐานนักโปรแกรมเมอร์ขั้นเทพ (Master Full-Stack Programmer)
- **Framework & Runtime:** CodeIgniter 4 + PHP 8+ + Docker Environment (`saraban_app`)
- **Clean Architecture:** ยึดหลัก MVC อย่างเคร่งครัด แยก Business Logic ออกจาก View เสมอ
- **ความปลอดภัยระดับสูงสุด:**
  - ป้องกัน CSRF ในทุกแบบฟอร์ม POST (`<?= csrf_field() ?>`)
  - ป้องกัน XSS ทุกจุดที่ Render ตัวแปรสู่หน้าจอ (`esc(...)`)
  - ตรวจสอบสิทธิ์และ Session อย่างรัดกุมใน Controller ก่อนแก้ไขหรือลงนาม
  - จัดการ Database Transaction ทุกครั้งที่มีการบันทึกหรืออัปเดตหลายตารางพร้อมกัน

---

## 🏛️ 2. มาตรฐานการออกแบบฐานข้อมูลขั้นเทพ (Master Database Architect)
- **Normalization & Integrity:** ออกแบบตารางตามมาตรฐาน 3NF ลดความซ้ำซ้อน กำหนด Foreign Keys และ Constraints อย่างแม่นยำ
- **Naming Conventions:** ตารางใช้พหูพจน์ (`documents`, `document_routes`, `document_logs`), Primary Key ใช้ `id`, Foreign Key ใช้ `<singular>_id`
- **Indexing:** เพิ่ม Index ในคอลัมน์ที่ถูก Filter, Sort หรือ Join บ่อย ๆ เช่น `step_order`, `status`, `created_at`
- **Audit & Workflow History:** ทุกกิจกรรมการเปลี่ยนแปลงต้องมี Log บันทึกประวัติและ Digital Verification Hash เพื่อความโปร่งใส

---

## 🎨 3. มาตรฐานนักดีไซน์เว็บไซต์ขั้นเทพ (Master UI/UX & Web Designer)
- **Rich Aesthetics & Typography:**
  - ใช้ Google Fonts `K2D` สำหรับ UI, หัวข้อ, และปุ่ม และ `Sarabun` สำหรับเอกสารทางการ
  - ใช้ Sneat Theme Palette ที่คัดสรรอย่างกลมกลืน (Indigo Primary, Mint Green Success, Amber Warning, Coral Danger)
- **Interactive Visual Feedback:**
  - แสดงสถานะขั้นตอนด้วย Visual Stepper พร้อม Pulse Animation สำหรับ Active Step
  - ใช้ Card Shadow ที่นุ่มนวล มนสวยงาม (Border radius 0.75-1rem)
- **⚠️ กฎเหล็กความชัดเจนของสี (Color Contrast & Readability - สำคัญสูงสุด):**
  - **สีข้อความกับพื้นหลังจะต้องเป็นคนละสีกันเสมอ:** ห้ามใช้สีเดียวกันหรือเฉดสีใกล้เคียงกันโดยเด็ดขาด เพราะจะทำให้มองไม่เห็นตัวหนังสือ
  - **ห้ามใช้พื้นหลังสีขาวโปร่งแสงกับตัวหนังสือสีขาวโดยเด็ดขาด** (เช่น `bg-white bg-opacity-20 text-white` หรือ `quick-action-pill` สีขาวบนขาว)
  - **บนพื้นหลังสีเข้ม / Banner Gradient:**
    - ป้าย Tag / Pill ให้ใช้: `bg-dark text-white shadow-sm` หรือ `style="background-color: rgba(15, 23, 42, 0.75) !important;"` คู่กับ `text-white fw-bold`
    - ปุ่มกด Action ให้ใช้: `btn btn-light text-primary fw-bold shadow-sm` หรือ `btn btn-warning text-dark fw-bold`
    - ป้าย Badge สังกัด/ตำแหน่ง ให้ใช้: `badge bg-white text-dark fw-bold px-2 py-1 shadow-sm`
  - **พื้นหลังสีสว่าง / การ์ดสีขาว:** ข้อความต้องเป็นสีเข้มชัดเจนเสมอ (`#2b3445`, `#1e293b`, `#0f172a`, `#334155`) ตัวเลขสถิติใช้สีทึบเข้ม (เช่น `#b45309`, `#15803d`, `#0369a1`, `#4338ca`)
  - **ระบบ Badge ทั้งหมด:** บังคับใช้ Solid Badges พร้อมตัวหนังสือตัดกันเสมอ เช่น `bg-danger text-white fw-bold`, `bg-warning text-dark fw-bold`, `bg-success text-white fw-bold`, `bg-info text-white fw-bold`, `bg-primary text-white fw-bold`
- **Proportional Data Layout & Usability:**
  - จัดสัดส่วนข้อมูลให้สมดุล แบ่งคอลัมน์อย่างเหมาะสม ไม่อัดแน่นเกินไป
  - แสดง Empty States อย่างสวยงามเมื่อไม่มีข้อมูล
  - รองรับ Responsive เต็มรูปแบบทั้ง Desktop, Tablet และ Mobile

---

## 📜 4. มาตรฐานการจัดรูปแบบหนังสือราชการ / บันทึกข้อความ (Thai Official Memo Standards)
ยึดถือมาตรฐานนี้ในการออกแบบ พัฒนา และปรับปรุงหนังสือราชการ/บันทึกข้อความทุกฉบับในระบบอย่างเคร่งครัด:
- **ขอบกระดาษ (Page Margins):**
  - ขอบบน: 3 ซม. (`3cm`)
  - ขอบซ้าย: 3 ซม. (`3cm`)
  - ขอบขวา: 2 ซม. (`2cm`)
  - ขอบล่าง: 2 ซม. (`2cm`)
  - กำหนดเป็น `padding: 3cm 2cm 2cm 3cm;` ทั้งบนหน้าจอ (`.saraban-paper`, `.page`) และใน `@media print` (`@page { size: A4 portrait; margin: 0; }`)
- **Typography & ขนาดตัวอักษร (ใช้หน่วย px เพื่อความแม่นยำและการตัดบรรทัดที่ตรงกัน 100% ทั้งหน้าจอและหน้าพิมพ์):**
  - ฟอนต์: `Sarabun`, `TH Sarabun PSK`, `TH Sarabun New`, sans-serif
  - หัวเรื่อง "บันทึกข้อความ": `29px` ตัวหนา กึ่งกลาง
  - ฉลากหัวเรื่อง "ส่วนราชการ", "ที่", "วันที่", "เรื่อง": `17px` ตัวหนา
  - ฉลาก "เรียน": `16px` ตัวหนา (ห้ามล็อกความกว้าง ให้ใช้ `width: auto; margin-right: 8px;`)
  - เนื้อหาข้อความ / ย่อหน้า / ข้อความกรอก: `16px`
  - ครุฑ: ใช้ไฟล์ `assets/img/krut_thai.png` ลายเส้นขาวดำทางการ คมชัด มีมิติโปร่ง ไม่ทึบดำเป็นปื้น (สูง 1.5 ซม. สำหรับบันทึกข้อความ วางมุมบนซ้าย)
- **ระยะห่างบรรทัดและย่อหน้า (Line Spacing & Paragraphs):**
  - เนื้อหาทั่วไป: `line-height: 1.50;` สระและวรรณยุกต์ไทยโปร่ง อ่านง่าย ไม่ซ้อนทับกัน
  - ย่อหน้าข้อความ: `text-indent: 2.5cm;` (สำหรับทุกย่อหน้าของ `<p>`)
  - ระยะห่างระหว่างย่อหน้า: `margin-bottom: 15px;`
  - ส่วนลงนาม (Signature): เว้นระยะ 3-4 บรรทัด (`margin-top: 65px;` บนหน้าจอ และ `2cm` ในการพิมพ์) ชิดขวา (`margin-left: auto; width: 60%;`)
- **ข้อยกเว้นส่วนหัวกระดาษ (Header Block Exception):**
  - แถวข้อมูลหัวหนังสือ (ส่วนราชการ, ที่, วันที่, เรื่อง) ต้องคงระยะห่างกระชับแบบฟอร์มเดิม (`margin-bottom: 4px;`) ไม่ขยายกว้างตามเนื้อหา
  - เส้นประจุดไข่ปลา (`.editable-field`): กำหนด `line-height: 1.50; min-height: 18px; padding: 0 4px 0px 4px; border-bottom: 1px dotted #000;`
- **ความสะอาดของแบบฟอร์มราชการ (Form Cleanliness):**
  - **ห้ามใส่ "ชั้นความลับ (ถ้ามี)"** ทั้งด้านบนและด้านล่างของเอกสาร
  - **ห้ามใส่ลายเซ็นจำลอง** (เช่น ข้อความสีน้ำเงิน หรือรูปลายเซ็นปลอม) ให้เว้นเป็นช่องว่างสำหรับลงนามจริง ตามด้วยวงเล็บชื่อเต็ม `( นาย/นาง/นางสาว... )` และตำแหน่ง
- **การตัดคำและการจัดระเบียบภาษาไทย (Thai Typography & Word Wrap Standards):**
  - กำหนดเนื้อหาให้ยื้อขอบซ้าย-ขวาเท่ากันเสมอแบบกระจายราชการ (Thai Distributed): `text-align: justify; text-justify: inter-character; -webkit-text-justify: inter-character; text-align-last: left; word-break: normal; -webkit-hyphens: none;`
  - ล็อกกลุ่มคำสำคัญห้ามแตกแถว (Non-breaking): ชื่อ-นามสกุล, วันที่-เดือน-ปี, และตัวเลข+หน่วยนับ โดยใช้ `\u00A0` หรือ `white-space: nowrap;`
  - จัดการตัดคำไทยตามพจนานุกรมด้วย `Intl.Segmenter` และแทรก Zero-Width Space (`\u200B`) ที่รอยต่อคำ เพื่อป้องกันเบราว์เซอร์ตัดผ่ากลางคำศัพท์
  - มีปุ่ม "จัดคำไทยสวย" (`formatThaiTypography()`) บนแถบเครื่องมือ และทำงานอัตโนมัติก่อนบันทึกฟอร์มเสมอ

- **⚠️ กฎเหล็กการแก้ไขคู่ขนาน (Dual Synchronization Rule):**
  - **"เวลา แก้ไข ต้อง แก้ไข ทั้งหน้าพิมพ์ และ หน้าใส่ข้อมูล"** เมื่อปรับปรุงหรือพัฒนาแบบฟอร์มราชการ ต้องอัปเดตไฟล์ทั้งฝั่งสร้าง/แก้ไข (`create.php`, `edit.php`), ฝั่งพรีวิว/พิมพ์ (`view.php`, `print.php`) และไฟล์ CSS ส่วนกลาง (`public/assets/css/saraban-memo.css`) ให้สอดคล้องตรงกันเสมอ

---

*อ้างอิง Skills เฉพาะทางเพิ่มเติมใน `.agents/skills/`:*
- `expert-programmer`: ขั้นตอนและ Best Practices การเขียนโค้ดและแก้ไขข้อผิดพลาด
- `database-architect`: รูปแบบ Schema, Query Optimization, และการจัดการข้อมูล
- `uiux-master-designer`: แนวทางการออกแบบ Layout, Component, และ Color System

