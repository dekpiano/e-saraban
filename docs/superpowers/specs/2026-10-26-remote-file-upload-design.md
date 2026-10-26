# Remote File Upload Service (Design Specification)

## 1. Overview & Intent
ระบบงานสารบรรณอิเล็กทรอนิกส์ (e-saraban) มีความจำเป็นต้องเปลี่ยนกลไกการจัดเก็บไฟล์แนบทั้งหมดของทุกระบบ (หนังสือรับ, หนังสือส่ง/เอกสารทั่วไป, คำสั่งโรงเรียน, บันทึกข้อความ, ขอไปราชการ และรายงานผลการปฏิบัติงาน) จากเดิมที่บันทึกลง Local Disk ของเซิร์ฟเวอร์เว็บโดยตรง ไปเก็บไว้ที่เซิร์ฟเวอร์จัดเก็บไฟล์ส่วนกลาง (`https://skj.nsnpao.go.th/`) เพื่อลดภาระพื้นที่ฮาร์ดดิสก์ของเว็บเซิร์ฟเวอร์หลัก เพิ่มความปลอดภัย และใช้มาตรฐานเดียวกับระบบงานครู (`D:\SkjSystem\teacher2025`)

## 2. Remote Server API Specification
- **Upload Endpoint:** `env('upload.server.url')` -> `https://skj.nsnpao.go.th/upload.php`
- **Delete Endpoint:** `env('upload.server.delete.url')` -> `https://skj.nsnpao.go.th/delete.php`
- **Auth Header:** `X-Auth-Token: <token>` (ค่าจาก `env('upload.server.token') ?: 'Dekpiano2025!!'`)
- **Public Base URL:** `env('upload.server.baseurl')` -> `https://skj.nsnpao.go.th/uploads/`
- **Direct Upload Mode:** ส่งไฟล์เต็มในครั้งเดียวผ่าน cURLRequest Multipart (เซิร์ฟเวอร์ปลายทางปรับเพิ่ม Max Upload Size เกิน 1 MB แล้ว ไม่จำเป็นต้องหั่นไฟล์/chunking)

## 3. Remote Path Directory Structure
| โมดูล | โฟลเดอร์ปลายทางบน Server | ตัวอย่างค่าที่บันทึกลง Database |
| :--- | :--- | :--- |
| **หนังสือรับเข้า (Inbound)** | `saraban/inbound` | `saraban/inbound/inbound_671cb_1729928192.pdf` |
| **หนังสือส่ง / เอกสารทั่วไป (Outbound/General)** | `saraban/documents` | `saraban/documents/doc_671cb_1729928192.pdf` |
| **คำสั่งโรงเรียน (Orders)** | `saraban/orders` | `saraban/orders/order_671cb_1729928192.pdf` |
| **บันทึกข้อความ (Memos)** | `saraban/memos` | `saraban/memos/memo_671cb_1729928192.pdf` |
| **ขอไปราชการ & รายงานผล (Travel)** | `travel/documents` | `travel/documents/travel_671cb_1729928192.pdf` |

## 4. Architecture: `RemoteUploadService`
สร้างคลาส `App\Services\RemoteUploadService` ให้เป็น Singleton/Service กลาง มีฟังก์ชันสำคัญดังนี้:
1. `uploadUploadedFile(\CodeIgniter\HTTP\Files\UploadedFile $file, string $remoteSubPath, ?string $desiredFilename = null): array`
   - ตรวจสอบความถูกต้องของไฟล์
   - จัดการส่งแบบ Direct Upload หากไฟล์ <= 750 KB หรือแบบ Chunked Upload หากไฟล์ > 750 KB
   - คืนค่า `['status' => 'success', 'filePath' => 'saraban/inbound/...', 'filename' => '...']`
2. `uploadLocalFile(string $localFullPath, string $remoteSubPath, string $desiredFilename, string $mimeType = 'application/pdf'): array`
   - สำหรับกรณีที่ไฟล์ถูกสร้าง/ประมวลผลบนเซิร์ฟเวอร์แล้ว (เช่น ประทับตรายางดิจิทัลเสร็จแล้ว) ก่อนส่งขึ้น Remote Server
3. `deleteFile(string $remoteFilePath): bool`
   - ส่งคำขอไปยัง `delete.php` เพื่อลบไฟล์เก่า

## 5. Module Implementations & Special Cases
1. **ระบบหนังสือรับ (`InboundDocumentController`):**
   - การประทับตรายาง (Stamp Imprint): ดำเนินการบน Local Temp File ให้เสร็จสิ้น
   - ส่งไฟล์ฉบับประทับตรา (`filePath`) และไฟล์ต้นฉบับสำรอง (`rawFilePath`) ขึ้น `saraban/inbound/`
   - ลบ Local Temp File หลังจากส่งเสร็จเรียบร้อย
2. **ระบบคำสั่ง (`OrderController`):**
   - แนบไฟล์คำสั่งใหม่ / อัปเดตไฟล์คำสั่งเดิม → ส่งขึ้น `saraban/orders/`
   - หากมีการเปลี่ยนไฟล์ ให้เรียก `deleteFile` ลบไฟล์เก่า
3. **ระบบเอกสารทั่วไป (`DocumentController`):**
   - อัปโหลดไฟล์เอกสารแนบ → ส่งขึ้น `saraban/documents/`
4. **ระบบบันทึกข้อความ (`InternalMemoController`):**
   - อัปโหลดไฟล์แนบ → ส่งขึ้น `saraban/memos/`
5. **ระบบไปราชการ (`TravelController`, `TravelReportController`):**
   - Refactor ให้ใช้ `RemoteUploadService` แทน Private Methods เดิม
6. **URL Helper & Views:**
   - ตรวจสอบฟังก์ชัน `document_url($filePath)` ใน `app/Helpers/thai_date_helper.php` ให้รองรับการเชื่อมต่อกับ `env('upload.server.baseurl')` อย่างสมบูรณ์
   - ตรวจสอบและเปลี่ยนการเรียกใช้ `base_url($filePath)` ใน Views ทุกจุดเป็น `document_url($filePath)`
