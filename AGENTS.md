# Core AI Development Guidelines & Tri-Master Skills (e-Office School System)

เอกสารนี้รวบรวมและกำหนดมาตรฐานสำหรับ AI ในการพัฒนา ปรับปรุง และออกแบบระบบ e-Office สารบรรณและติดตามงานราชการ:

> [!IMPORTANT]
> **กฎเหล็กการเรียกใช้ Skill อัตโนมัติ (Automatic Contextual Skill Invocation):**  
> AI ต้องตรวจสอบบริบทของทุกคำขอ และ**ดึง Skill ที่เกี่ยวข้องมาปฏิบัติงานโดยอัตโนมัติทันที** ตามแนวทางของ `using-superpowers` โดยไม่ต้องรอให้ผู้ใช้สั่งซ้ำ:
> - **ฟีเจอร์ใหม่ / วางระบบใหม่:** เรียกใช้ `brainstorming` และ `writing-plans` ก่อนเริ่มเขียนโค้ดเสมอ
> - **พบข้อผิดพลาด / บั๊ก / Error:** เรียกใช้ `systematic-debugging` เพื่อสืบค้นต้นตอจาก Log และหลักฐานเชิงประจักษ์
> - **ออกแบบ UI / CSS / ปรับหน้าจอ:** เรียกใช้ `uiux-master-designer` บังคับ Contrast ตัวหนังสือ และ Google Fonts
> - **แก้ไขโครงสร้างฐานข้อมูล / Query:** เรียกใช้ `database-architect`
> - **เขียนโค้ด / API / Logic สารบรรณ:** เรียกใช้ `expert-programmer`
> - **ก่อนส่งมอบงานทุกครั้ง:** เรียกใช้ `verification-before-completion` ตรวจสอบความถูกต้องและทดสอบ 100%

---

## 🧙‍♂️ 1. มาตรฐานนักโปรแกรมเมอร์ขั้นเทพ (Master Full-Stack Programmer)
- **Framework & Runtime:** CodeIgniter 4 + PHP 8+ + Docker Environment (`saraban_app`)
- **Clean Architecture:** ยึดหลัก MVC อย่างเคร่งครัด แยก Business Logic ออกจาก View เสมอ
- **ความปลอดภัยระดับสูงสุด:**
  - ป้องกัน CSRF ในทุกแบบฟอร์ม POST (`<?= csrf_field() ?>`)
  - ป้องกัน XSS ทุกจุดที่ Render ตัวแปรสู่หน้าจอ (`esc(...)`)
  - ตรวจสอบสิทธิ์และ Session อย่างรัดกุมใน Controller ก่อนแก้ไขหรือลงนาม
  - จัดการ Database Transaction ทุกครั้งที่มีการบันทึกหรืออัปเดตหลายตารางพร้อมกัน
- **กฎเหล็กการตั้งชื่อไฟล์วิว (File Naming with Folder Prefix):**
  - **"เวลาตั้งชื่อไฟล์ ให้มีชื่อโฟลเดอร์นำหน้าเสมอ"** เช่น ภายในโฟลเดอร์ `orders/` ให้ตั้งชื่อไฟล์เป็น `orders_index.php`, `orders_create.php`, `orders_edit.php`, `orders_view.php`, `orders_print.php` เพื่อป้องกันความสับสนของชื่อไฟล์และแท็บบน IDE และระบุบริบทของไฟล์ได้ชัดเจนทันที

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

## 📜 4. มาตรฐานสากลสำหรับแบบฟอร์มเอกสารราชการทุกระบบ (Universal Thai Official Document Standards)
ยึดถือมาตรฐานนี้เป็น **กฎเหล็กถาวร** ในการออกแบบ พัฒนา และปรับปรุงเอกสารราชการทุกประเภทในทุกโมดูลทั้งปัจจุบันและอนาคต (เช่น บันทึกข้อความ, ขอไปราชการ, รายงานผลการปฏิบัติงาน, คำสั่ง, ประกาศ, หนังสือภายนอก ฯลฯ):
- **ขอบกระดาษ (Page Margins):**
  - ขอบบน: 1.5 ซม. (`1.5cm`)
  - ขอบซ้าย: 3 ซม. (`3cm`)
  - ขอบขวา: 2 ซม. (`2cm`)
  - ขอบล่าง: 2 ซม. (`2cm`)
  - กำหนดเป็น `padding: 1.5cm 2cm 2cm 3cm;` ทั้งบนหน้าจอ (`.saraban-paper`, `.page`) และใน `@media print` (`@page { size: A4 portrait; margin: 0; }`)
- **Typography & ขนาดตัวอักษร (ใช้หน่วย pt เพื่อให้เทียบเท่าสเกลของ Microsoft Word 1:1 ทั้งหน้าจอและหน้าพิมพ์ตามไฟล์ แบบฟอร์ม_หนังสือภายใน (1).docx):**
  - ฟอนต์: `TH SarabunIT๙`, `TH Sarabun IT๙`, `THSarabunIT9`, `TH Sarabun New`, `TH Sarabun PSK`, `Sarabun`, sans-serif (ฝังแบบ Base64 แสดงตัวเลขอารบิกเป็นเลขไทยอัตโนมัติตามแบบฟอร์ม Word)
  - หัวเรื่อง "บันทึกข้อความ": `29pt` ตัวหนา กึ่งกลาง ระยะห่างบรรทัดค่าแน่นอน `35pt` (`line-height: 35pt; padding-top: 10px;` / OpenXML `w:sz="58"`, `w:line="700" w:lineRule="exact"`, `w:vAlign="bottom"` ขยับลงมาให้ชิดด้านล่างของระดับครุฑอย่างสง่างาม)
  - ฉลากหัวเรื่อง "ส่วนราชการ", "ที่", "วันที่", "เรื่อง": `20pt` ตัวหนา (`font-size: 20pt; font-weight: bold;` / OpenXML `w:sz="40"`)
  - ฉลาก "เรียน": `16pt` ตัวปกติ (ห้ามล็อกความกว้าง ให้ใช้ `width: auto; margin-right: 8px;`, OpenXML `before=120` หรือ `margin-top: 6pt;`)
  - เนื้อหาข้อความ / ย่อหน้า / ข้อความกรอก: `16pt` ตัวปกติ (`font-size: 16pt;` / OpenXML `w:sz="32"`)
  - ครุฑ: ใช้ไฟล์ `assets/img/krut_thai.png` ลายเส้นขาวดำทางการ คมชัด มีมิติโปร่ง ไม่ทึบดำเป็นปื้น (สูง 1.5 ซม. กว้าง 1.37 ซม. สำหรับบันทึกข้อความ วางมุมบนซ้าย)
- **ระยะห่างบรรทัดและย่อหน้า (Line Spacing & Paragraphs - มาตรฐาน 1:1 กับ Microsoft Word):**
  - **ระยะห่างบรรทัด (Line Spacing):** Single Spacing แท้จริงตาม Microsoft Word กำหนดเป็น `line-height: 1.15;` (OpenXML `w:line="240" w:lineRule="auto"`) เพื่อให้สระบน-ล่างและวรรณยุกต์ไทยโปร่งสวยงาม บรรทัดกระชับพอดี ไม่บวมหรือห่างเกินจริง
  - **ย่อหน้าข้อความ:** `text-indent: 2.5cm;` (สำหรับทุกย่อหน้าของ `<p>`, OpenXML `w:firstLine="1418"`)
  - **ระยะห่างเมื่อเคาะขึ้นย่อหน้าใหม่ (Paragraph Spacing / Enter):** กำหนดเป็น `10pt` (`--paragraph-spacing: 10pt;` / `margin-bottom: var(--paragraph-spacing);`) เพื่อให้เมื่อกด Enter ขึ้นย่อหน้าใหม่ จะเว้นระยะบรรทัดโปร่งสบายตา ชัดเจน ไม่เบียดติดกันเกินไป และย่อหน้า 2.5 ซม. ทันทีอย่างสวยงาม
  - ส่วนลงนาม (Signature): เว้นระยะ 3-4 บรรทัด (3-4 Enter / `margin-top: 36pt` ถึง `48pt;` ทั้งบนหน้าจอและในการพิมพ์) กำหนด `padding-left: 7.5cm;` และ `.signature-box { transform: none; width: fit-content; align-items: center; text-align: center; }` โดยให้เครื่องหมายวงเล็บเปิด `(` ของชื่อ-นามสกุล อยู่ตรงแนวดิ่งกับคำว่า "ข้อความ" (ตัว 'ข') ของหัวเรื่อง "บันทึกข้อความ" พอดีเป๊ะตามความต้องการของผู้ใช้งานและระเบียบงานสารบรรณ และจัดตำแหน่งลายเซ็น (Signature Image) และตำแหน่ง (Position) ให้อยู่กึ่งกลางชื่อเสมอ โดยไม่มีข้อความกลุ่มสาระการเรียนรู้
  - **ส่วนลงนามหนังสือคำสั่งโรงเรียน (School Orders Signature Layout):**
    - ครุฑ: กึ่งกลางหน้ากระดาษ ขนาดสูง 3.0 ซม. กว้าง 2.73 ซม.
    - "สั่ง ณ วันที่ ...": กึ่งกลางหน้ากระดาษ (`text-align: center;`) สอดคล้องกับระเบียบและไฟล์ต้นแบบ `แบบฟอร์ม_คำสั่ง.docx` ("สั่ง ณ วันที่ ... ปรับบรรทัดให้ตรงกับคำสั่งด้านบน")
    - บล็อกลงนามผู้อำนวยการ/ผู้บริหาร: ให้จัดอยู่กึ่งกลางหน้ากระดาษ (`margin-left: auto; margin-right: auto; width: fit-content; max-width: 15cm; text-align: center;`) เพื่อให้ลายเซ็น ชื่อ `( นายพงษ์ศักดิ์ เงินสันเทียะ )` และตำแหน่ง `ผู้อำนวยการโรงเรียน...` กึ่งกลางหน้ากระดาษและกึ่งกลางซึ่งกันและกันอย่างสมบูรณ์แบบ
    - **แหล่งข้อมูลผู้ลงนาม (Executive Signer Data Source):** สำหรับเอกสารที่มีตำแหน่ง ผอ. หรือ รอง ผอ. ต้องดึงชื่อ-สกุล และตำแหน่งจริงตามฐานข้อมูล `skjacth_personnel.tb_personnel` (ตำแหน่ง `posi_001` และ `posi_002`) เสมอ พร้อมมีตัวเลือกให้เจ้าหน้าที่เลือกสลับผู้ลงนามได้สะดวก
- **ข้อยกเว้นส่วนหัวกระดาษ (Header Block Exception):**
  - แถวข้อมูลหัวหนังสือ (ส่วนราชการ, ที่, วันที่, เรื่อง) ต้องคงระยะห่างกระชับแบบฟอร์มเดิม (`margin-bottom: 4px;`) ไม่ขยายกว้างตามเนื้อหา
  - เส้นประจุดไข่ปลา (`.editable-field`): กำหนด `line-height: 1.1; min-height: 20px; padding: 0 4px 0px 4px; border-bottom: 1px dotted #000;` (ดึงเส้นประชิดติดฐานตัวอักษร กระชับสวยงาม ไม่ทับสระล่าง)
- **ความสะอาดของแบบฟอร์มราชการ (Form Cleanliness):**
  - **ห้ามใส่ "ชั้นความลับ (ถ้ามี)"** ทั้งด้านบนและด้านล่างของเอกสาร
  - **ห้ามใส่ลายเซ็นจำลอง** (เช่น ข้อความสีน้ำเงิน หรือรูปลายเซ็นปลอม) ให้เว้นเป็นช่องว่างสำหรับลงนามจริง ตามด้วยวงเล็บชื่อเต็ม `( นาย/นาง/นางสาว... )` และตำแหน่ง (จัดตำแหน่งให้อยู่กึ่งกลางชื่อ และไม่ใส่กลุ่มสาระการเรียนรู้)
- **การตัดคำและการจัดระเบียบภาษาไทย (Thai Typography & Word Wrap Standards):**
  - กำหนดเนื้อหาให้ยื้อขอบซ้าย-ขวาเท่ากันเสมอแบบกระจายราชการ (Thai Distributed): `text-align: justify; text-justify: inter-character; -webkit-text-justify: inter-character; text-align-last: left; word-break: normal; -webkit-hyphens: none;`
  - ล็อกกลุ่มคำสำคัญห้ามแตกแถว (Non-breaking): ชื่อ-นามสกุล, วันที่-เดือน-ปี, และตัวเลข+หน่วยนับ โดยใช้ `\u00A0` หรือ `white-space: nowrap;`
  - จัดการตัดคำไทยตามพจนานุกรมด้วย `Intl.Segmenter` และแทรก Zero-Width Space (`\u200B`) ที่รอยต่อคำ เพื่อป้องกันเบราว์เซอร์ตัดผ่ากลางคำศัพท์
  - มีปุ่ม "จัดคำไทยสวย" (`formatThaiTypography()`) บนแถบเครื่องมือ และทำงานอัตโนมัติก่อนบันทึกฟอร์มเสมอ
  - **แถบเครื่องมือจัดตำแหน่งข้อความ (Text Alignment Controls):** มีปุ่มจัดตำแหน่งครบถ้วน: ชิดซ้าย (`bx-align-left`), กึ่งกลาง (`bx-align-middle`), ชิดขวา (`bx-align-right`), และซ้าย-ขวาเท่ากันแบบราชการ (`bx-align-justify`) โดยใช้ `onmousedown="event.preventDefault();"` เพื่อไม่ให้หลุด Cursor จากกล่องพิมพ์ข้อความ
  - **การจัดตำแหน่งเฉพาะจุด (Pinpoint Targeting on Cursor & Selection - กฎเหล็ก):**
    - ปุ่มจัดตำแหน่งทุกปุ่มต้องกระทำ **เฉพาะแถว/ย่อหน้าที่เคอร์เซอร์อยู่ หรือเฉพาะข้อความที่คลุมดำเท่านั้น 100%**
    - หากมีการคลุมดำเฉพาะบางข้อความ/บางท่อนในย่อหน้า ระบบต้องแยก (Isolate) ข้อความที่คลุมดำออกมาเป็นบล็อกอิสระเพื่อจัดตำแหน่งเฉพาะส่วนนั้น โดยคงข้อความก่อนหน้า/หลังจากนั้นไว้ตามเดิม
    - ตรวจสอบขอบเขตให้อยู่ภายใน Editor เสมอ ห้ามปีนออกไปจัดตำแหน่งพาเรนต์ภายนอก (`.order-page-wrapper` หรือ `.saraban-paper`) เด็ดขาด
  - **มาตรฐานการเคาะบรรทัด Shift+Enter และ Enter (Microsoft Word Thai Distributed Standard):**
    - **เมื่อกด `Shift + Enter` (ขึ้นบรรทัดใหม่แบบกระจายเต็มแถว):**
      - บรรทัดเดิม (ก่อนจุดตัด) จะถูกบังคับให้ **กระจายเต็มแถวทันที (Full Justify: `text-align: justify; text-align-last: justify; text-justify: inter-character;`)** พร้อมแทรก Zero-Width Space (`\u200B`) ตามพจนานุกรมคำไทย
      - **ไม่ต้องเว้นระยะห่างบรรทัด (`margin-bottom: 0 !important; margin-top: 0 !important;`)** เพื่อให้ระยะชิดเป็นบรรทัดเดียวกันตามปกติของ Single Line Spacing (`line-height: 1.15`) โดยไม่มีช่องว่างย่อหน้า
      - บรรทัดถัดไปเป็นบรรทัดต่อเนื่อง ชิดขอบซ้ายปกติ (`text-indent: 0 !important;`) พร้อมให้พิมพ์ต่อได้ทันที
    - **เมื่อกด `Enter` ปกติ (ขึ้นย่อหน้าใหม่):**
      - เป็นการขึ้นย่อหน้าใหม่ตามระเบียบงานสารบรรณ บรรทัดแรกเยื้องย่อหน้า **๒.๕ ซม.** อัตโนมัติ (`text-indent: 2.5cm`)
      - เว้นระยะห่างระหว่างย่อหน้าโปร่งสบายตา `10pt` (`margin-bottom: var(--paragraph-spacing);`)
      - บรรทัดท้ายของย่อหน้าเดิมจะชิดซ้ายปกติ (`text-align-last: left;`)

---

## 📑 5. มาตรฐานระบบแบ่งหน้ากระดาษ A4 จริง (Multi-Sheet Physical A4 Pagination Standard)
เอกสารราชการทุกประเภทในทุกโมดูล (คำสั่งโรงเรียน, บันทึกข้อความ, ขอไปราชการ ฯลฯ) เมื่อมีเนื้อหามากกว่า 1 หน้า A4 ต้องใช้มาตรฐานระบบแบ่งหน้ากระดาษจริง (Multi-Sheet Pagination Engine) เดียวกันนี้เสมอ:
- **โครงสร้างแผ่นกระดาษ A4 แยกจริง (Physical A4 Sheets):**
  - ทุกแผ่นต้องกำหนดขนาดเท่า A4 จริง: ความกว้าง `21cm`, ความสูง `29.7cm` (`min-height: 29.7cm; max-height: 29.7cm; box-sizing: border-box; overflow: hidden;`)
  - ขอบกระดาษทุกแผ่นต้องเป๊ะตามระเบียบ: บน `1.5cm`, ซ้าย `3.0cm`, ขวา `2.0cm`, ล่าง `2.0cm` (`padding: 1.5cm 2.0cm 2.0cm 3.0cm;`)
  - มีแถบขอบแบ่งแผ่นบนหน้าจอ (`order-sheet-gap`) แสดงป้ายสิ้นสุดแผ่นที่ / ขึ้นแผ่นที่ พร้อมซ่อนเมื่อสั่งพิมพ์ (`@media print { .no-print { display: none !important; } }`)
- **หัวกระดาษหน้าถัดไป (Continued Header):**
  - แผ่นที่ ๒ เป็นต้นไป ต้องมีเลขหน้ากึ่งกลางด้านบนเสมอ เช่น `- ๒ -`, `- ๓ -` (`.order-page-header-number { text-align: center; font-size: 16pt; margin-bottom: 6pt; }`)
- **การเกลี่ยและรักษาสมดุลเนื้อหา (Intelligent Page Balancing - กฎเหล็กงานสารบรรณ):**
  - **ห้ามปล่อยให้ส่วนลงนามอยู่โดดเดี่ยวบนหน้าใหม่โดยไม่มีเนื้อหาเด็ดขาด:** หากส่วนลงนามล้นไปหน้าใหม่ ระบบต้องตัดแบ่งเนื้อหาท่อนล่างของหน้าก่อนหน้า (อย่างน้อย 30-40% หรือ 6-10 บรรทัด) ข้ามมาอยู่เป็นเนื้อหานำในหน้าลงนามเสมอ
  - **ย่อหน้าที่ถูกตัดข้ามหน้า (Continued Paragraph):** บรรทัดแรกของข้อความส่วนที่ต่อบนหน้าใหม่ต้อง **ไม่มีการย่อหน้า** (`.order-paragraph-continued { text-indent: 0 !important; margin-top: 0 !important; }`)
  - **ระยะขอบล่างปลอดภัย (Bottom Margin Buffer):** เผื่อพื้นที่ระยะปลอดภัยอย่างน้อย `2.0 - 2.5cm` ที่ก้นกระดาษทุกแผ่น เพื่อรับประกันว่าไม่มีตัวหนังสือตกขอบหรือหลุดกระดาษเด็ดขาด
- **รองรับโหมดแก้ไขและการพิมพ์สมบูรณ์ (Full Edit & Print Interactivity):**
  - ในหน้าแก้ไข (`edit.php`) และสร้าง (`create.php`): กล่องเนื้อหาทุกแผ่นต้องแก้ไขได้ (`contenteditable="true"`), การจัดแบ่งหน้าใหม่ต้องรักษาสถานะค่าและตัวเลือกของฟอร์มครบ 100% (`cloneWithInputs`), และเมื่อกดบันทึก ต้องเชื่อมต่อย่อหน้าที่ถูกตัดข้ามหน้ากลับเป็น HTML ปกติก่อนส่งลงฐานข้อมูล (`getCombinedContent()`)
  - **⚠️ กฎเหล็กการแก้ไขคู่ขนาน (Dual Synchronization Rule):** ต้องอัปเดตไฟล์ทั้งฝั่งสร้าง/แก้ไข (`create.php`, `edit.php`), ฝั่งพรีวิว/พิมพ์ (`view.php`, `print.php`) และไฟล์ CSS ส่วนกลาง (`orders_saraban.css`, `saraban-memo.css`) ให้ใช้เอนจินและสไตล์เดียวกันตรงกัน 100% เสมอ

---

## 💻 6. มาตรฐานถาวร แถบเครื่องมือและแม่แบบฟอร์มเอกสารสไตล์ Word (Universal Word-Ribbon Document Standard)
ยึดถือมาตรฐานนี้เป็น **กฎเหล็กถาวร** สำหรับทุกระบบเอกสารราชการในระบบ e-Saraban / e-Office ทั้งหมด (คำสั่งโรงเรียน, บันทึกข้อความ, ขอไปราชการ, รายงานผลการปฏิบัติงาน, ประกาศ ฯลฯ) เพื่อให้มีลักษณะการทำงาน หน้าตา ประสบการณ์ผู้ใช้ และคีย์ลัดเหมือนกับ Microsoft Word 100%:
- **โครงสร้างด้านบนแบบ 2 ชั้น ตรึงติดหน้าจอ (Two-Tier Sticky Top Architecture):**
  1. **แถบการกระทำหลัก (Top Action Bar - Sticky `top: 0; z-index: 1000;`):**
     - ฝั่งซ้าย: ปุ่มย้อนกลับ, ป้าย Badge ชนิดเอกสาร / ปี พ.ศ.
     - ฝั่งขวา: ป้ายระบุจำนวนแผ่น A4 (`badge bg-dark text-white fw-bold`), ปุ่มดูตัวอย่าง A4 หรือพิมพ์/PDF, ปุ่มบันทึกร่าง, และปุ่มส่งเรื่อง/ออกคำสั่ง
  2. **แถบเครื่องมือจัดรูปแบบสไตล์ Word (Word-Style Formatting Ribbon Toolbar - Sticky `top: 52px; z-index: 995;`):**
     - **Group 1 (History):** ปุ่มเลิกทำ (Undo - `Ctrl+Z`), ทำซ้ำ (Redo - `Ctrl+Y`)
     - **Group 2 (Font & Size):** ป้ายฟอนต์มาตรฐาน "สารบรรณ", เมนู Dropdown เลือกขนาดตัวอักษรแบบราชการ (`14pt` ถึง `29pt`), ปุ่ม $A^+$ (`Ctrl+]`), ปุ่ม $A^-$ (`Ctrl+[`)
     - **Group 3 (Font Style):** ตัวหนา `Ctrl+B`, ตัวเอียง `Ctrl+I`, ขีดเส้นใต้ `Ctrl+U`, ขีดฆ่า, และปุ่มล้างการจัดรูปแบบ (Clear Formatting)
     - **Group 4 (Alignment):** ชิดซ้าย `Ctrl+L`, กึ่งกลาง `Ctrl+E`, ชิดขวา `Ctrl+R`, จัดกระจายซ้าย-ขวาเท่ากันแบบราชการ (Thai Distributed - `Ctrl+J`)
     - **Group 5 (Lists & Indentation):** รายการลำดับเลข (1. 2. 3.), สัญลักษณ์หัวข้อย่อย (Bullet), ลดระยะเยื้อง (`bx-left-indent`), เพิ่มระยะเยื้อง ๒.๕ ซม. (`bx-right-indent`), สลับย่อหน้าราชการ (`bx-paragraph`)
     - **Group 6 (Saraban Utilities):** แทรกเส้นคั่นแบ่งส่วน (Divider Line), แปลงเลขไทย (๑๒๓), จัดคำไทยสวย (`formatThaiTypography`), จัดแบ่งหน้า A4 / ลายเซ็นออนไลน์
     - **Group 7 (Right-side Document Meta Controls - `ms-auto`):** จัดวางเมนูข้อมูลเมตาชิดขวาของแท็บ Ribbon เสมอ (เช่น หมวดหมู่งาน, สถานะคำสั่ง, ระดับความเร่งด่วน, หรือชั้นความลับ) เพื่อลดพื้นที่แนวตั้ง และให้กระดาษ A4 วางต่อจาก Ribbon ทันทีโดยไม่มีการ์ดลอยเกะกะ
- **การคงเคอร์เซอร์และการเลือกข้อความ (Zero Focus Loss):** ทุกปุ่มบน Ribbon Toolbar ต้องใช้ `onmousedown="event.preventDefault();"` เพื่อป้องกันการหลุด Focus หรือสูญเสียข้อความที่คลุมดำ
- **การซิงค์สถานะปุ่มแบบ Realtime (Active State Sync):** ตรวจจับอีเวนต์ `selectionchange` เพื่อปรับสถานะคลาส `.active` (ไฮไลต์พื้นหลังสีฟ้า) บนปุ่ม Bold, Italic, Underline, Strike, Lists, และ Paragraph Indent รวมทั้งปรับค่าใน Dropdown ขนาดตัวอักษรให้ตรงกับข้อความที่เคอร์เซอร์อยู่เสมอ
- **มาตรฐานการเคาะบรรทัด Shift+Enter และ Enter:**
  - `Shift + Enter`: บังคับแถวเดิมกระจายเต็มแถว (Full Justify) พร้อมแทรก `\u200B` โดย **ไม่มีระยะห่างบรรทัด** (`margin-bottom: 0`)
  - `Enter` ปกติ: ขึ้นย่อหน้าใหม่ เยื้อง **๒.๕ ซม.** อัตโนมัติ พร้อมเว้นระยะย่อหน้าโปร่งสบายตา `10pt` (`margin-bottom: var(--paragraph-spacing)`)

---

## 📅 6. กฎเหล็กระบบปฏิทินและวันที่ พ.ศ. ทั้งระบบ (Universal Thai Buddhist Era Calendar Standard - พ.ศ. ทั้งหมด 100%)
ยึดถือมาตรฐานนี้เป็น **กฎเหล็กถาวร** สำหรับทุกหน้าจอ ฟอร์ม ค้นหา และการแสดงผลวันที่ทุกโมดูลในระบบ e-Office:
- **ปฏิทินต้องใช้ พ.ศ. ทั้งหมด 100%:**
  - ตัวเลือกปฏิทิน (Datepicker / Calendar) และการแสดงผลวันที่บนหน้าจอผู้ใช้งานทุกจุด **ต้องแสดงเป็นปีพุทธศักราช (พ.ศ.) เท่านั้น**
  - **ห้ามแสดงหรือใช้ปฏิทินปีคริสต์ศักราช (ค.ศ.) บนหน้าจอผู้ใช้งานโดยเด็ดขาด**
  - ห้ามใช้ `<input type="date">` ของเบราว์เซอร์โดยตรงโดยไม่มีตัวแปลง เพราะเบราว์เซอร์จะแสดงปี ค.ศ.
- **การติดตั้งและเรียกใช้งาน Datepicker:**
  - ทุกแบบฟอร์มที่มีการเลือกวันที่ ให้ใช้คลาส `.flatpickr-thai-input` ร่วมกับไลบรารี `flatpickr-thai-buddhist.js` เสมอ
  - ในส่วนหัวของปฏิทินและ Dropdown เดือน/ปี ต้องแปลงปี ค.ศ. เป็นปี พ.ศ. (A.D. + 543) เช่น ๒๕๖๙ อย่างถูกต้อง ไหลลื่น ไม่มีกระตุกหรือเพี้ยนเป็นปี 3112+
  - หากมีไอคอนปฏิทินใน `.input-group` ให้ผูกการคลิกที่ไอคอนเพื่อเปิดปฏิทินได้ทันที
- **การจัดเก็บข้อมูลหลังบ้าน (Database Storage Standard):**
  - การจัดเก็บลงฐานข้อมูลยังคงเป็นรูปแบบสากล `YYYY-MM-DD` (ค.ศ.) ตามมาตรฐาน Database Normalization
  - การส่งค่าจาก Form Submit จะส่งเป็น `Y-m-d` (ค.ศ.) ผ่าน Hidden/AltInput เพื่อความปลอดภัยและความถูกต้องของข้อมูล และแปลงเป็น พ.ศ. อัตโนมัติเมื่อแสดงผลด้วย `thai_date(...)`

---

*อ้างอิง Skills เฉพาะทางเพิ่มเติมใน `.agents/skills/`:*
- **Tri-Master Skills (ทักษะหลักของระบบ):**
  - `expert-programmer`: ขั้นตอนและ Best Practices การเขียนโค้ดและแก้ไขข้อผิดพลาด (PHP/CI4/Docker)
  - `database-architect`: รูปแบบ Schema, Query Optimization, และการจัดการข้อมูล
  - `uiux-master-designer`: แนวทางการออกแบบ Layout, Component, และ Color System
- **Superpowers Engineering Framework (Jesse Vincent - `obra/superpowers`):**
  - `using-superpowers`: กรอบคิดและมาตรฐานการเรียกใช้สกิลอัตโนมัติก่อนเริ่มเขียนโค้ด
  - `brainstorming`: การวิเคราะห์ความต้องการและออกแบบสเปกร่วมกับผู้ใช้ก่อนลงมือ
  - `writing-plans` & `executing-plans`: วางแผนงานย่อยและปฏิบัติตามแผนทีละขั้น
  - `subagent-driven-development` & `dispatching-parallel-agents`: บริหารจัดการตัวแทน AI คู่ขนาน
  - `systematic-debugging`: การตรวจสอบหาสาเหตุของบั๊กอย่างเป็นวิทยาศาสตร์ ไม่เดาสุ่ม
  - `test-driven-development`: วงจร TDD ทดสอบก่อนเขียนโค้ดจริง
  - `verification-before-completion`: ตรวจสอบความถูกต้องและทดสอบระบบให้พร้อม 100% ก่อนส่งมอบงาน
  - `requesting-code-review` & `receiving-code-review`: ทบทวนคุณภาพโค้ดอย่างมืออาชีพ
  - `using-git-worktrees` & `finishing-a-development-branch`: บริหารจัดการกิ่ง Git อย่างปลอดภัย

