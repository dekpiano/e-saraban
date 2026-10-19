<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
ตารางกำหนดสิทธิ์การใช้งานระบบ (Role & Permission Matrix)
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-xxl flex-grow-1 container-p-y">

    <!-- Header Section -->
    <div class="d-md-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark" style="font-family: 'K2D', sans-serif;">
                <i class="bx bx-shield-quarter text-primary me-2"></i>ตารางกำหนดสิทธิ์การใช้งาน (Role & Permission Matrix)
            </h4>
            <p class="text-secondary mb-0 small">
                โครงสร้างสิทธิ์ของระบบ e-Saraban แบ่งออกเป็น 2 สิทธิ์หลักสำหรับบริหารจัดการ และสิทธิ์ผู้ใช้งานทั่วไปสำหรับบุคลากรทุกคนที่มีอีเมลโรงเรียน
            </p>
        </div>
        <div class="mt-3 mt-md-0 d-flex gap-2">
            <a href="<?= base_url('admin/users') ?>" class="btn btn-outline-secondary btn-sm fw-bold">
                <i class="bx bx-arrow-back me-1"></i> หน้ารายชื่อผู้ใช้
            </a>
            <a href="<?= base_url('admin/users/create') ?>" class="btn btn-primary btn-sm fw-bold shadow-sm">
                <i class="bx bx-user-plus me-1"></i> เพิ่มเจ้าหน้าที่ระบบ
            </a>
        </div>
    </div>

    <!-- Summary Overview Cards -->
    <div class="row g-3 mb-4">
        <!-- Card 1: Supper Admin -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 0.75rem; border-top: 4px solid #696cff !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar avatar-sm me-2 flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-primary text-primary fw-bold">
                                <i class="bx bx-crown fs-5"></i>
                            </span>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold text-dark" style="font-family: 'K2D', sans-serif;">1. Supper Admin</h6>
                            <span class="badge bg-primary text-white py-0 px-2 fw-semibold" style="font-size: 0.72rem;">สิทธิ์หลัก: ผู้ดูแลระบบสูงสุด</span>
                        </div>
                    </div>
                    <p class="text-secondary small mb-2" style="font-size: 0.83rem; line-height: 1.45;">
                        ควบคุมและดูแลความปลอดภัยทั้งระบบ จัดการแต่งตั้ง/แก้ไข/ระงับสิทธิ์เจ้าหน้าที่ ตรวจสอบกิจกรรม Audit Log และจัดการทะเบียนได้ทุกส่วนงาน
                    </p>
                    <div class="d-flex align-items-center text-primary small fw-semibold">
                        <i class="bx bx-check-double me-1"></i> สิทธิ์สูงสุด (Full Access Control)
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Admin -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 0.75rem; border-top: 4px solid #03c3ec !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar avatar-sm me-2 flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-info text-info fw-bold">
                                <i class="bx bx-folder-open fs-5"></i>
                            </span>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold text-dark" style="font-family: 'K2D', sans-serif;">2. Admin</h6>
                            <span class="badge bg-info text-white py-0 px-2 fw-semibold" style="font-size: 0.72rem;">สิทธิ์หลัก: เจ้าหน้าที่สารบรรณ</span>
                        </div>
                    </div>
                    <p class="text-secondary small mb-2" style="font-size: 0.83rem; line-height: 1.45;">
                        เจ้าหน้าที่งานสารบรรณโรงเรียน ลงรับหนังสือภายนอก ออกเลขทะเบียนส่ง คุมเล่มทะเบียน และกำหนดเส้นทางเดินแฟ้มเสนอเซ็น/เกษียน
                    </p>
                    <div class="d-flex align-items-center text-info small fw-semibold">
                        <i class="bx bx-check-circle me-1"></i> ควบคุมงานสารบรรณกลางทั้งหมด
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3: General Users -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 0.75rem; border-top: 4px solid #8592a3 !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar avatar-sm me-2 flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-secondary text-secondary fw-bold">
                                <i class="bx bx-user-check fs-5"></i>
                            </span>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold text-dark" style="font-family: 'K2D', sans-serif;">ผู้ใช้งานทั่วไป</h6>
                            <span class="badge bg-secondary text-white py-0 px-2 fw-semibold" style="font-size: 0.72rem;">อีเมลโรงเรียน @skj.ac.th</span>
                        </div>
                    </div>
                    <p class="text-secondary small mb-2" style="font-size: 0.83rem; line-height: 1.45;">
                        ครู บุคลากร และผู้บริหารทุกคนที่มีอีเมลโรงเรียน เข้าสู่ระบบด้วย Google Workspace ได้ทันทีเพื่อสร้างเอกสาร ยื่นไปราชการ และลงนามตามลำดับ
                    </p>
                    <div class="d-flex align-items-center text-secondary small fw-semibold">
                        <i class="bx bx-log-in-circle me-1"></i> ล็อกอิน Google อัตโนมัติ ไม่ต้องเพิ่มล่วงหน้า
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Matrix Card -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 0.75rem;">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h5 class="card-title mb-0 fw-bold text-dark" style="font-family: 'K2D', sans-serif;">
                <i class="bx bx-table text-primary me-2"></i>เมทริกซ์การดำเนินการตามบทบาท (Role Privileges Matrix)
            </h5>
            <span class="badge bg-label-primary px-3 py-1">2 สิทธิ์หลัก + ผู้ใช้อีเมลโรงเรียนทั่วไป</span>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="min-width: 280px;" class="text-dark fw-bold">ฟังก์ชัน / ขอบเขตงานระบบ</th>
                        <th class="text-center" style="width: 220px; background-color: #f5f6ff;">
                            <span class="badge bg-primary text-white fw-bold px-3 py-1 fs-7">
                                <i class="bx bx-crown me-1"></i> Supper Admin
                            </span>
                            <div class="small text-secondary mt-1 fw-semibold">ผู้ดูแลระบบสูงสุด</div>
                        </th>
                        <th class="text-center" style="width: 220px; background-color: #f1fbfe;">
                            <span class="badge bg-info text-white fw-bold px-3 py-1 fs-7">
                                <i class="bx bx-folder-open me-1"></i> Admin
                            </span>
                            <div class="small text-secondary mt-1 fw-semibold">เจ้าหน้าที่สารบรรณ</div>
                        </th>
                        <th class="text-center" style="width: 260px; background-color: #fafafa;">
                            <span class="badge bg-secondary text-white fw-bold px-3 py-1 fs-7">
                                <i class="bx bx-user me-1"></i> ผู้ใช้งานทั่วไป
                            </span>
                            <div class="small text-secondary mt-1 fw-semibold">ครู / บุคลากร / ผู้บริหาร (@skj.ac.th)</div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <!-- หมวดที่ 1: งานสารบรรณและทะเบียนหนังสือ -->
                    <tr class="table-secondary">
                        <td colspan="4" class="fw-bold text-dark py-2">
                            <i class="bx bx-folder me-1 text-primary"></i> 1. งานสารบรรณและทะเบียนหนังสือราชการ
                        </td>
                    </tr>
                    <tr>
                        <td class="text-dark fw-semibold">ดูทะเบียนหนังสือทั้งหมด (All Documents)</td>
                        <td class="text-center text-success"><i class="bx bx-check-circle fs-4"></i></td>
                        <td class="text-center text-success"><i class="bx bx-check-circle fs-4"></i></td>
                        <td class="text-center text-success"><i class="bx bx-check-circle fs-4"></i> <small class="text-muted d-block">(ดูเฉพาะที่เกี่ยวข้อง/เผยแพร่)</small></td>
                    </tr>
                    <tr>
                        <td class="text-dark fw-semibold">ลงทะเบียนรับหนังสือภายนอก (หนังสือรับ)</td>
                        <td class="text-center text-success"><i class="bx bx-check-circle fs-4"></i></td>
                        <td class="text-center text-success"><i class="bx bx-check-circle fs-4"></i></td>
                        <td class="text-center text-muted"><i class="bx bx-minus fs-4"></i></td>
                    </tr>
                    <tr>
                        <td class="text-dark fw-semibold">ออกเลขทะเบียนส่งภายนอก (หนังสือส่ง)</td>
                        <td class="text-center text-success"><i class="bx bx-check-circle fs-4"></i></td>
                        <td class="text-center text-success"><i class="bx bx-check-circle fs-4"></i></td>
                        <td class="text-center text-muted"><i class="bx bx-minus fs-4"></i></td>
                    </tr>
                    <tr>
                        <td class="text-dark fw-semibold">สร้างบันทึกข้อความภายใน / คำสั่งโรงเรียน</td>
                        <td class="text-center text-success"><i class="bx bx-check-circle fs-4"></i></td>
                        <td class="text-center text-success"><i class="bx bx-check-circle fs-4"></i></td>
                        <td class="text-center text-success"><i class="bx bx-check-circle fs-4"></i></td>
                    </tr>
                    <tr>
                        <td class="text-dark fw-semibold">กำหนดและเรียงลำดับเส้นทางเดินแฟ้ม (Workflow Routing)</td>
                        <td class="text-center text-success"><i class="bx bx-check-circle fs-4"></i></td>
                        <td class="text-center text-success"><i class="bx bx-check-circle fs-4"></i></td>
                        <td class="text-center text-muted"><i class="bx bx-minus fs-4"></i></td>
                    </tr>
                    <tr>
                        <td class="text-dark fw-semibold">ลงนาม / เกษียนหนังสือดิจิทัล (Digital e-Signature)</td>
                        <td class="text-center text-success"><i class="bx bx-check-circle fs-4"></i></td>
                        <td class="text-center text-success"><i class="bx bx-check-circle fs-4"></i></td>
                        <td class="text-center text-success"><i class="bx bx-check-circle fs-4"></i> <small class="text-muted d-block">(ตามเส้นทางเสนอเรื่อง)</small></td>
                    </tr>
                    <tr>
                        <td class="text-dark fw-semibold">ส่งคืนเรื่องให้แก้ไข (Return for Revision)</td>
                        <td class="text-center text-success"><i class="bx bx-check-circle fs-4"></i></td>
                        <td class="text-center text-success"><i class="bx bx-check-circle fs-4"></i></td>
                        <td class="text-center text-success"><i class="bx bx-check-circle fs-4"></i> <small class="text-muted d-block">(ตามขั้นตอนของตนเอง)</small></td>
                    </tr>

                    <!-- หมวดที่ 2: งานไปราชการ -->
                    <tr class="table-secondary">
                        <td colspan="4" class="fw-bold text-dark py-2">
                            <i class="bx bx-briefcase-alt-2 me-1 text-primary"></i> 2. การขออนุมัติไปราชการและติดตามงาน
                        </td>
                    </tr>
                    <tr>
                        <td class="text-dark fw-semibold">ยื่นขออนุมัติไปราชการออนไลน์</td>
                        <td class="text-center text-success"><i class="bx bx-check-circle fs-4"></i></td>
                        <td class="text-center text-success"><i class="bx bx-check-circle fs-4"></i></td>
                        <td class="text-center text-success"><i class="bx bx-check-circle fs-4"></i></td>
                    </tr>
                    <tr>
                        <td class="text-dark fw-semibold">ตรวจสอบการสอนแทน / เห็นชอบ (หัวหน้ากลุ่ม)</td>
                        <td class="text-center text-success"><i class="bx bx-check-circle fs-4"></i></td>
                        <td class="text-center text-success"><i class="bx bx-check-circle fs-4"></i></td>
                        <td class="text-center text-success"><i class="bx bx-check-circle fs-4"></i> <small class="text-muted d-block">(ตามบทบาทในสายงาน)</small></td>
                    </tr>
                    <tr>
                        <td class="text-dark fw-semibold">กลั่นกรองงบประมาณ / ฝ่ายบริหาร (รอง ผอ.)</td>
                        <td class="text-center text-success"><i class="bx bx-check-circle fs-4"></i></td>
                        <td class="text-center text-success"><i class="bx bx-check-circle fs-4"></i></td>
                        <td class="text-center text-success"><i class="bx bx-check-circle fs-4"></i> <small class="text-muted d-block">(รอง ผอ. ประจำกลุ่มงาน)</small></td>
                    </tr>
                    <tr>
                        <td class="text-dark fw-semibold">อนุมัติการเดินทางไปราชการ (ผอ. โรงเรียน)</td>
                        <td class="text-center text-success"><i class="bx bx-check-circle fs-4"></i></td>
                        <td class="text-center text-success"><i class="bx bx-check-circle fs-4"></i></td>
                        <td class="text-center text-success"><i class="bx bx-check-circle fs-4"></i> <small class="text-muted d-block">(เฉพาะผู้อำนวยการโรงเรียน)</small></td>
                    </tr>
                    <tr>
                        <td class="text-dark fw-semibold">บันทึกรายงานผลการไปราชการ & แนบเกียรติบัตร</td>
                        <td class="text-center text-success"><i class="bx bx-check-circle fs-4"></i></td>
                        <td class="text-center text-success"><i class="bx bx-check-circle fs-4"></i></td>
                        <td class="text-center text-success"><i class="bx bx-check-circle fs-4"></i></td>
                    </tr>

                    <!-- หมวดที่ 3: การจัดการระบบและความปลอดภัย -->
                    <tr class="table-secondary">
                        <td colspan="4" class="fw-bold text-dark py-2">
                            <i class="bx bx-cog me-1 text-primary"></i> 3. การจัดการระบบและความปลอดภัย (System Administration)
                        </td>
                    </tr>
                    <tr>
                        <td class="text-dark fw-semibold">ตั้งค่าลายเซ็นดิจิทัลส่วนตัว (Saved e-Signature)</td>
                        <td class="text-center text-success"><i class="bx bx-check-circle fs-4"></i></td>
                        <td class="text-center text-success"><i class="bx bx-check-circle fs-4"></i></td>
                        <td class="text-center text-success"><i class="bx bx-check-circle fs-4"></i></td>
                    </tr>
                    <tr>
                        <td class="text-dark fw-semibold">เข้าถึงเมนู "จัดการเจ้าหน้าที่ระบบ" (/admin/users)</td>
                        <td class="text-center text-success"><i class="bx bx-check-circle fs-4"></i></td>
                        <td class="text-center text-success"><i class="bx bx-check-circle fs-4"></i></td>
                        <td class="text-center text-muted"><i class="bx bx-minus fs-4"></i> <small class="text-muted d-block">(ไม่อนุญาต)</small></td>
                    </tr>
                    <tr>
                        <td class="text-dark fw-semibold">แต่งตั้ง / เปลี่ยนแปลงสิทธิ์ Supper Admin และ Admin</td>
                        <td class="text-center text-success"><i class="bx bx-check-circle fs-4"></i></td>
                        <td class="text-center text-muted"><i class="bx bx-minus fs-4"></i></td>
                        <td class="text-center text-muted"><i class="bx bx-minus fs-4"></i></td>
                    </tr>
                    <tr>
                        <td class="text-dark fw-semibold">ระงับการใช้งานบัญชี (Lock Account)</td>
                        <td class="text-center text-success"><i class="bx bx-check-circle fs-4"></i></td>
                        <td class="text-center text-success"><i class="bx bx-check-circle fs-4"></i></td>
                        <td class="text-center text-muted"><i class="bx bx-minus fs-4"></i></td>
                    </tr>
                    <tr>
                        <td class="text-dark fw-semibold">ตรวจสอบประวัติกิจกรรม Audit Trail ทั้งหมด</td>
                        <td class="text-center text-success"><i class="bx bx-check-circle fs-4"></i></td>
                        <td class="text-center text-success"><i class="bx bx-check-circle fs-4"></i></td>
                        <td class="text-center text-muted"><i class="bx bx-minus fs-4"></i></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
