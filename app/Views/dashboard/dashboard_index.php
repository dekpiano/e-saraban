<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>แดชบอร์ดภาพรวมระบบงานสารบรรณ<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
/* Dashboard Aesthetic & Animation */
.dashboard-hero-banner {
    background: linear-gradient(135deg, #696cff 0%, #5f61e6 50%, #4338ca 100%);
    border-radius: 1.25rem;
    box-shadow: 0 10px 25px -5px rgba(105, 108, 255, 0.35), 0 8px 10px -6px rgba(105, 108, 255, 0.2);
    position: relative;
    overflow: hidden;
    color: #ffffff;
}

.dashboard-hero-banner::before {
    content: '';
    position: absolute;
    top: -40%;
    right: -10%;
    width: 340px;
    height: 340px;
    background: radial-gradient(circle, rgba(255,255,255,0.18) 0%, rgba(255,255,255,0) 70%);
    border-radius: 50%;
    pointer-events: none;
}

.dashboard-hero-banner::after {
    content: '';
    position: absolute;
    bottom: -30%;
    left: 20%;
    width: 260px;
    height: 260px;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
    border-radius: 50%;
    pointer-events: none;
}

.stat-card-saraban {
    border-radius: 1rem;
    border: 1px solid rgba(226, 232, 240, 0.9);
    background: #ffffff;
    box-shadow: 0 4px 18px -2px rgba(0, 0, 0, 0.05);
    transition: all 0.25s ease;
    position: relative;
    overflow: hidden;
}

.stat-card-saraban:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px -4px rgba(0, 0, 0, 0.08);
    border-color: #cbd5e1;
}

.alert-action-card {
    border-radius: 1rem;
    transition: all 0.25s ease;
    border: 1px solid transparent;
}

.alert-action-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px -2px rgba(0, 0, 0, 0.08);
}

.pulse-badge {
    animation: pulseBadge 1.8s infinite;
}

@keyframes pulseBadge {
    0% {
        box-shadow: 0 0 0 0 rgba(255, 62, 29, 0.7);
    }
    70% {
        box-shadow: 0 0 0 8px rgba(255, 62, 29, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(255, 62, 29, 0);
    }
}

.custom-dashboard-table thead th {
    font-size: 0.825rem;
    font-weight: 700;
    color: #334155 !important;
    background-color: #f8fafc !important;
    border-top: none;
    border-bottom: 2px solid #e2e8f0;
    padding: 0.85rem 1rem;
}

.custom-dashboard-table tbody tr {
    transition: background-color 0.15s ease;
}

.custom-dashboard-table tbody tr:hover {
    background-color: #f8faff;
}

.custom-dashboard-table td {
    vertical-align: middle;
    padding: 0.85rem 1rem;
    border-bottom: 1px solid #f1f5f9;
}

.dashboard-nav-tabs .nav-link {
    font-weight: 600;
    color: #475569;
    border: none;
    border-bottom: 3px solid transparent;
    padding: 0.85rem 1.25rem;
    border-radius: 0;
    transition: all 0.2s ease;
}

.dashboard-nav-tabs .nav-link.active {
    color: #696cff;
    font-weight: 700;
    background: transparent;
    border-bottom-color: #696cff;
}

.dashboard-nav-tabs .nav-link:hover:not(.active) {
    color: #1e293b;
}
</style>

<?php
$userRole = (string) (session()->get('role') ?? 'teacher');
$isExecutive = ($userRole === 'executive');
$isAdmin = in_array($userRole, ['super_admin', 'admin', 'saraban'], true);
$isTeacher = !$isExecutive && !$isAdmin;

$totalAlerts = $isExecutive 
    ? ($pendingCount + $urgentDocsCount)
    : ($isTeacher 
        ? ($pendingCount + ($pendingTravelCount ?? 0))
        : ($pendingSarabanCount + $pendingCount + $urgentDocsCount + $allPendingTravelCount + $draftOrderCount));
?>

<!-- ========================================== -->
<!-- 1. Hero Section: Saraban Mission Control   -->
<!-- ========================================== -->
<div class="row mb-4">
    <div class="col-12">
        <div class="dashboard-hero-banner p-4 p-md-5">
            <div class="row align-items-center">
                <div class="col-lg-8 position-relative z-1">
                    <?php if ($isExecutive): ?>
                        <div class="d-inline-flex align-items-center gap-2 px-3 py-1 mb-3 rounded-pill bg-white text-dark small fw-bold shadow-sm">
                            <i class="bx bxs-crown text-warning fs-5"></i> ระบบงานสารบรรณสำหรับฝ่ายบริหาร (Executive Portal)
                        </div>
                        <h2 class="text-white fw-bold mb-2">
                            สวัสดีท่าน <?= esc(session()->get('full_name') ?? 'ผู้บริหาร') ?> 👔
                        </h2>
                        <p class="text-white fs-6 mb-4 opacity-95">
                            ตำแหน่ง: <span class="badge bg-white text-dark fw-bold ms-1 px-2.5 py-1 shadow-sm"><?= esc(session()->get('position_name') ?? 'ฝ่ายบริหารโรงเรียน') ?></span>
                            <span class="mx-2 text-white fw-bold">•</span>
                            สังกัด: <span class="badge bg-white text-dark fw-bold ms-1 px-2.5 py-1 shadow-sm"><?= esc(session()->get('department_name') ?? 'โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์') ?></span>
                        </p>

                        <!-- Executive Quick Action Buttons: ONLY SIGNING & ALL DOCUMENTS -->
                        <div class="d-flex flex-wrap gap-2">
                            <a href="<?= base_url('admin/documents/inbox') ?>" class="btn btn-warning text-dark fw-bold px-3 py-2 shadow-sm d-flex align-items-center gap-1">
                                <i class="bx bx-edit-alt fs-5"></i> แฟ้มรอฉันเกษียณ / ลงนามหนังสือ
                                <?php if ($pendingCount > 0): ?>
                                    <span class="badge bg-danger text-white rounded-pill ms-1 px-2 py-0.5"><?= $pendingCount ?></span>
                                <?php endif; ?>
                            </a>
                            <a href="<?= base_url('admin/documents/signed') ?>" class="btn btn-light text-primary fw-bold px-3 py-2 shadow-sm">
                                <i class="bx bx-check-double me-1"></i> หนังสือที่ฉันลงนามแล้ว
                            </a>
                            <a href="<?= base_url('admin/documents') ?>" class="btn btn-light text-primary fw-bold px-3 py-2 shadow-sm">
                                <i class="bx bx-folder me-1"></i> คลังเอกสารราชการทั้งหมด
                            </a>
                            <a href="<?= base_url('admin/center') ?>" class="btn btn-light text-primary fw-bold px-3 py-2 shadow-sm">
                                <i class="bx bx-search-alt me-1"></i> ศูนย์กลางค้นหาเอกสาร
                            </a>
                        </div>
                    <?php elseif ($isTeacher): ?>
                        <div class="d-inline-flex align-items-center gap-2 px-3 py-1 mb-3 rounded-pill bg-white text-dark small fw-bold shadow-sm">
                            <i class="bx bx-briefcase-alt-2 text-primary fs-5"></i> ระบบงานไปราชการและรายงานผลสำหรับครูและบุคลากร
                        </div>
                        <h2 class="text-white fw-bold mb-2">
                            สวัสดีคุณครู <?= esc(session()->get('full_name') ?? 'คุณครู') ?> 🍎
                        </h2>
                        <p class="text-white fs-6 mb-4 opacity-95">
                            ตำแหน่ง: <span class="badge bg-white text-dark fw-bold ms-1 px-2.5 py-1 shadow-sm"><?= esc(session()->get('position_name') ?? 'ครูผู้สอน') ?></span>
                            <span class="mx-2 text-white fw-bold">•</span>
                            สังกัด: <span class="badge bg-white text-dark fw-bold ms-1 px-2.5 py-1 shadow-sm"><?= esc(session()->get('department_name') ?? 'โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์') ?></span>
                        </p>

                        <!-- Quick Action Buttons for Teacher Role ONLY (เฉพาะงานไปราชการ) -->
                        <div class="d-flex flex-wrap gap-2">
                            <a href="<?= base_url('admin/travel/create') ?>" class="btn btn-warning text-dark fw-bold px-3 py-2 shadow-sm">
                                <i class="bx bx-plus-circle me-1"></i> ยื่นขอไปราชการ
                            </a>
                            <a href="<?= base_url('admin/travel/my') ?>" class="btn btn-light text-primary fw-bold px-3 py-2 shadow-sm">
                                <i class="bx bx-list-check me-1"></i> ติดตามงานไปราชการของฉัน
                            </a>
                            <a href="<?= base_url('admin/travel') ?>" class="btn btn-light text-primary fw-bold px-3 py-2 shadow-sm">
                                <i class="bx bx-briefcase-alt-2 me-1"></i> ทะเบียนไปราชการทั้งหมด
                            </a>
                            <a href="<?= base_url('admin/travel-reports') ?>" class="btn btn-light text-primary fw-bold px-3 py-2 shadow-sm">
                                <i class="bx bxs-report me-1"></i> เขียนรายงานผลไปราชการ
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="d-inline-flex align-items-center gap-2 px-3 py-1 mb-3 rounded-pill bg-white text-dark small fw-bold shadow-sm">
                            <i class="bx bxs-institution text-primary fs-5"></i> ศูนย์ประสานงานและสารบรรณอิเล็กทรอนิกส์ (Saraban Registry)
                        </div>
                        <h2 class="text-white fw-bold mb-2">
                            สวัสดีคุณ <?= esc(session()->get('full_name') ?? 'เจ้าหน้าที่สารบรรณ') ?> 👋
                        </h2>
                        <p class="text-white fs-6 mb-4 opacity-95">
                            ตำแหน่ง: <span class="badge bg-white text-dark fw-bold ms-1 px-2.5 py-1 shadow-sm"><?= esc(session()->get('position_name') ?? 'เจ้าหน้าที่งานสารบรรณ') ?></span>
                            <span class="mx-2 text-white fw-bold">•</span>
                            สังกัด: <span class="badge bg-white text-dark fw-bold ms-1 px-2.5 py-1 shadow-sm"><?= esc(session()->get('department_name') ?? 'โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์') ?></span>
                        </p>

                        <!-- Quick Action Buttons for Saraban Tasks -->
                        <div class="d-flex flex-wrap gap-2">
                            <a href="<?= base_url('admin/documents/create') ?>" class="btn btn-warning text-dark fw-bold px-3 py-2 shadow-sm">
                                <i class="bx bx-plus-circle me-1"></i> ออกเลข & ลงรับ/ส่งหนังสือ
                            </a>
                            <a href="<?= base_url('admin/orders/create') ?>" class="btn btn-light text-primary fw-bold px-3 py-2 shadow-sm">
                                <i class="bx bx-award me-1"></i> ออกคำสั่งโรงเรียนใหม่
                            </a>
                            <a href="<?= base_url('admin/memos/create') ?>" class="btn btn-light text-primary fw-bold px-3 py-2 shadow-sm">
                                <i class="bx bx-edit me-1"></i> เขียนบันทึกข้อความ
                            </a>
                            <a href="<?= base_url('admin/documents') ?>" class="btn btn-light text-primary fw-bold px-3 py-2 shadow-sm">
                                <i class="bx bx-folder me-1"></i> คลังทะเบียนสารบรรณ
                            </a>
                            <a href="<?= base_url('admin/center') ?>" class="btn btn-light text-primary fw-bold px-3 py-2 shadow-sm">
                                <i class="bx bx-search-alt me-1"></i> ค้นหาเอกสาร
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="col-lg-4 text-center mt-4 mt-lg-0 d-none d-lg-block position-relative z-1">
                    <img src="<?= base_url('assets/img/illustrations/man-with-laptop-light.png') ?>" 
                         alt="Saraban Officer Illustration" 
                         class="img-fluid" 
                         style="max-height: 165px; filter: drop-shadow(0 10px 15px rgba(0,0,0,0.25));" />
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- 2. Central Action Alerts (งานด่วนทุกระบบ) -->
<!-- ========================================== -->
<div class="mb-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
            <i class="bx bx-bell-ring text-danger fs-4"></i>
            <span>ศูนย์แจ้งเตือนงานด่วนที่ต้องดำเนินการ (Action Required)</span>
            <?php if ($totalAlerts > 0): ?>
                <span class="badge bg-danger text-white rounded-pill pulse-badge px-2.5 py-1 small fw-bold">
                    <?= $totalAlerts ?> งานค้าง
                </span>
            <?php else: ?>
                <span class="badge bg-success text-white rounded-pill px-2.5 py-1 small fw-bold">
                    <i class="bx bx-check-double me-1"></i> ไม่มีงานคั่งค้าง
                </span>
            <?php endif; ?>
        </h5>
        <small class="text-muted">คลิกที่การ์ดเพื่อเปิดทำงานได้ทันที</small>
    </div>

    <div class="row g-3">
        <!-- แจ้งเตือน 1: บันทึกข้อความรอสารบรรณตรวจและออกเลข (เฉพาะเจ้าหน้าที่สารบรรณ) -->
        <?php if ($isAdmin && $pendingSarabanCount > 0): ?>
        <div class="col-xl-4 col-md-6">
            <div class="card alert-action-card border-danger shadow-sm bg-white h-100" style="border-left: 5px solid #ff3e1d !important;">
                <div class="card-body p-3.5">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="badge bg-danger text-white fw-bold px-2 py-1 pulse-badge">
                            <i class="bx bx-error-circle me-1"></i> ต้องออกเลขด่วน
                        </span>
                        <h3 class="fw-bold mb-0 text-danger"><?= number_format($pendingSarabanCount) ?></h3>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">บันทึกข้อความรอสารบรรณออกเลข</h6>
                    <p class="small text-muted mb-3">ครูและบุคลากรส่งเรื่องเข้ามา รอตรวจทานและรันเลขทะเบียนส่งเกษียน</p>
                    <a href="<?= base_url('admin/memos/saraban-inbox') ?>" class="btn btn-danger btn-sm text-white fw-bold w-100 shadow-xs">
                        <i class="bx bx-hash me-1"></i> เปิดกล่องออกเลขทันที (<?= $pendingSarabanCount ?>)
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- แจ้งเตือน 2: หนังสือรอท่านเกษียน/ลงนาม -->
        <?php if ($pendingCount > 0): ?>
        <div class="col-xl-4 col-md-6">
            <div class="card alert-action-card border-warning shadow-sm bg-white h-100" style="border-left: 5px solid #ffab00 !important;">
                <div class="card-body p-3.5">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="badge bg-warning text-dark fw-bold px-2 py-1">
                            <i class="bx bx-pencil me-1"></i> แฟ้มเสนอเกษียน
                        </span>
                        <h3 class="fw-bold mb-0 text-warning"><?= number_format($pendingCount) ?></h3>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">หนังสือรอท่านพิจารณาลงนาม</h6>
                    <p class="small text-muted mb-3">มีเอกสารราชการส่งถึงท่านตามสายงานและต้องการการเกษียนความเห็น</p>
                    <a href="<?= base_url('admin/documents/inbox') ?>" class="btn btn-warning btn-sm text-dark fw-bold w-100 shadow-xs">
                        <i class="bx bx-edit-alt me-1"></i> เข้าสู่แฟ้มลงนาม (<?= $pendingCount ?>)
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- แจ้งเตือน 3: หนังสือราชการเร่งด่วนกำลังอยู่ระหว่างเสนอ -->
        <?php if (!$isTeacher && $urgentDocsCount > 0): ?>
        <div class="col-xl-4 col-md-6">
            <div class="card alert-action-card border-primary shadow-sm bg-white h-100" style="border-left: 5px solid #696cff !important;">
                <div class="card-body p-3.5">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="badge bg-primary text-white fw-bold px-2 py-1">
                            <i class="bx bxs-flame me-1"></i> เรื่องด่วนในระบบ
                        </span>
                        <h3 class="fw-bold mb-0 text-primary"><?= number_format($urgentDocsCount) ?></h3>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">หนังสือด่วนกำลังอยู่ระหว่างเสนอ</h6>
                    <p class="small text-muted mb-3">หนังสือระดับด่วน/ด่วนมาก/ด่วนที่สุด ที่ยังเดินเรื่องไม่เสร็จสิ้น</p>
                    <a href="<?= base_url('admin/documents') ?>" class="btn btn-outline-primary btn-sm fw-bold w-100">
                        <i class="bx bx-navigation me-1"></i> ติดตามเรื่องด่วน
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- แจ้งเตือน 4: คำขอไปราชการ -->
        <?php if ($isTeacher && ($pendingTravelCount ?? 0) > 0): ?>
        <div class="col-xl-4 col-md-6">
            <div class="card alert-action-card border-info shadow-sm bg-white h-100" style="border-left: 5px solid #03c3ec !important;">
                <div class="card-body p-3.5">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="badge bg-info text-white fw-bold px-2 py-1">
                            <i class="bx bx-briefcase-alt-2 me-1"></i> งานไปราชการ
                        </span>
                        <h3 class="fw-bold mb-0 text-info"><?= number_format($pendingTravelCount) ?></h3>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">คำขอไปราชการรอท่านลงนาม</h6>
                    <p class="small text-muted mb-3">มีคำขอไปราชการส่งถึงท่านเพื่อพิจารณาความเห็นชอบ</p>
                    <a href="<?= base_url('admin/travel/inbox') ?>" class="btn btn-outline-info btn-sm fw-bold w-100">
                        <i class="bx bx-list-check me-1"></i> ตรวจสอบคำขอ (<?= $pendingTravelCount ?>)
                    </a>
                </div>
            </div>
        </div>
        <?php elseif (!$isTeacher && ($allPendingTravelCount > 0 || $returnTravelCount > 0)): ?>
        <div class="col-xl-4 col-md-6">
            <div class="card alert-action-card border-info shadow-sm bg-white h-100" style="border-left: 5px solid #03c3ec !important;">
                <div class="card-body p-3.5">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="badge bg-info text-white fw-bold px-2 py-1">
                            <i class="bx bx-briefcase-alt-2 me-1"></i> งานไปราชการ
                        </span>
                        <h3 class="fw-bold mb-0 text-info"><?= number_format($allPendingTravelCount + $returnTravelCount) ?></h3>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">คำขอไปราชการรอจัดการ</h6>
                    <p class="small text-muted mb-3">รอพิจารณา <?= $allPendingTravelCount ?> รายการ • รอคืนเอกสารตัวจริง <?= $returnTravelCount ?> รายการ</p>
                    <a href="<?= base_url('admin/travel') ?>" class="btn btn-outline-info btn-sm fw-bold w-100">
                        <i class="bx bx-list-check me-1"></i> ตรวจสอบงานไปราชการ
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- แจ้งเตือน 5: คำสั่งโรงเรียนฉบับร่าง -->
        <?php if ($isAdmin && $draftOrderCount > 0): ?>
        <div class="col-xl-4 col-md-6">
            <div class="card alert-action-card border-secondary shadow-sm bg-white h-100" style="border-left: 5px solid #8592a3 !important;">
                <div class="card-body p-3.5">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="badge bg-secondary text-white fw-bold px-2 py-1">
                            <i class="bx bx-file-blank me-1"></i> คำสั่งฉบับร่าง
                        </span>
                        <h3 class="fw-bold mb-0 text-secondary"><?= number_format($draftOrderCount) ?></h3>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">คำสั่งโรงเรียนฉบับร่าง</h6>
                    <p class="small text-muted mb-3">ร่างคำสั่งที่ครู/หัวหน้าโครงการหรือเจ้าหน้าที่จัดทำขึ้น รอการตรวจทานและเสนอลงนาม</p>
                    <a href="<?= base_url('admin/orders?status=draft') ?>" class="btn btn-outline-secondary btn-sm fw-bold w-100">
                        <i class="bx bx-show me-1"></i> ดูคำสั่งฉบับร่าง (<?= $draftOrderCount ?>)
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- แจ้งเตือน 6: ลายเซ็นดิจิทัล (ถ้ายังไม่มี) -->
        <?php if (!$hasSignature): ?>
        <div class="col-xl-4 col-md-6">
            <div class="card alert-action-card border-warning shadow-sm bg-white h-100" style="border-left: 5px solid #ffab00 !important;">
                <div class="card-body p-3.5">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="badge bg-warning text-dark fw-bold px-2 py-1">
                            <i class="bx bx-signature me-1"></i> ลายเซ็นอิเล็กทรอนิกส์
                        </span>
                        <i class="bx bx-pencil text-warning fs-3"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">ท่านยังไม่ได้ตั้งค่าลายเซ็น</h6>
                    <p class="small text-muted mb-3">กรุณาวาดหรืออัปโหลดลายเซ็นดิจิทัลเพื่อใช้ในการเกษียนและลงนาม</p>
                    <a href="<?= base_url('admin/profile') ?>" class="btn btn-warning btn-sm text-dark fw-bold w-100">
                        <i class="bx bx-cog me-1"></i> ตั้งค่าลายเซ็นทันที
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- ถ้าไม่มีงานคั่งค้างเลย -->
        <?php if ($totalAlerts === 0): ?>
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-white p-4 text-center">
                <div class="avatar avatar-lg rounded-circle bg-label-success mx-auto mb-2 d-flex align-items-center justify-content-center">
                    <i class="bx bx-check-double text-success fs-2"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">ระบบงานสารบรรณเป็นปัจจุบันเรียบร้อย!</h5>
                <p class="text-muted small mb-0">ไม่มีบันทึกข้อความรอออกเลข และไม่มีหนังสือคั่งค้างที่ต้องการการดำเนินการเร่งด่วนในขณะนี้</p>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- ========================================== -->
<!-- 3. Metrics Section                         -->
<!-- ========================================== -->
<?php if ($isTeacher): ?>
<!-- สถิติงานสำหรับครูและบุคลากร (มุ่งเน้นเฉพาะระบบไปราชการ) -->
<div class="row g-3 mb-4">
    <!-- 1. คำขอไปราชการของฉัน -->
    <div class="col-xl-4 col-sm-6">
        <div class="card stat-card-saraban h-100 shadow-sm border-0">
            <div class="card-body p-3.5">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="avatar avatar-md bg-label-info rounded p-1">
                        <i class="bx bx-briefcase-alt-2 fs-3 text-info"></i>
                    </div>
                    <span class="badge bg-info text-white fw-bold">ไปราชการ</span>
                </div>
                <div class="text-secondary small fw-semibold">งานไปราชการของฉัน</div>
                <div class="d-flex align-items-baseline gap-2 my-1">
                    <h2 class="fw-bold mb-0 text-dark"><?= number_format($myTravelCount) ?></h2>
                    <span class="text-muted small">รายการ</span>
                </div>
                <div class="border-top pt-2 mt-2">
                    <a href="<?= base_url('admin/travel/my') ?>" class="small fw-bold text-info d-inline-flex align-items-center gap-1 text-decoration-none">
                        ติดตามสถานะไปราชการ <i class="bx bx-chevron-right fs-5"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. ยื่นขอไปราชการใหม่ -->
    <div class="col-xl-4 col-sm-6">
        <div class="card stat-card-saraban h-100 shadow-sm border-0">
            <div class="card-body p-3.5">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="avatar avatar-md bg-label-primary rounded p-1">
                        <i class="bx bx-plus-circle fs-3 text-primary"></i>
                    </div>
                    <span class="badge bg-primary text-white fw-bold">สร้างใหม่</span>
                </div>
                <div class="text-secondary small fw-semibold">ยื่นขอไปราชการ</div>
                <div class="d-flex align-items-baseline gap-2 my-1">
                    <h5 class="fw-bold mb-0 text-primary">ขออนุมัติเดินทาง</h5>
                </div>
                <div class="border-top pt-2 mt-2">
                    <a href="<?= base_url('admin/travel/create') ?>" class="small fw-bold text-primary d-inline-flex align-items-center gap-1 text-decoration-none">
                        เขียนคำขอใหม่ทันที <i class="bx bx-chevron-right fs-5"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. รายงานผลไปราชการ -->
    <div class="col-xl-4 col-sm-6">
        <div class="card stat-card-saraban h-100 shadow-sm border-0">
            <div class="card-body p-3.5">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="avatar avatar-md bg-label-warning rounded p-1">
                        <i class="bx bxs-report fs-3 text-warning"></i>
                    </div>
                    <span class="badge bg-warning text-dark fw-bold">รายงานผล</span>
                </div>
                <div class="text-secondary small fw-semibold">รายงานผลการปฏิบัติงาน</div>
                <div class="d-flex align-items-baseline gap-2 my-1">
                    <h5 class="fw-bold mb-0 text-dark">บันทึกรายงานผล</h5>
                </div>
                <div class="border-top pt-2 mt-2">
                    <a href="<?= base_url('admin/travel-reports') ?>" class="small fw-bold text-warning d-inline-flex align-items-center gap-1 text-decoration-none">
                        ส่งรายงานผลไปราชการ <i class="bx bx-chevron-right fs-5"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php else: ?>
<!-- 4 เสาหลักสารบรรณ (Registry Metrics สำหรับเจ้าหน้าที่สารบรรณ/ฝ่ายบริหาร) -->
<div class="row g-3 mb-4">
    <!-- 1. หนังสือรับภายนอก -->
    <div class="col-xl-3 col-sm-6">
        <div class="card stat-card-saraban h-100">
            <div class="card-body p-3.5">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="avatar avatar-md bg-label-primary rounded p-1">
                        <i class="bx bx-log-in-circle fs-3 text-primary"></i>
                    </div>
                    <span class="badge bg-primary text-white fw-bold">หนังสือรับ</span>
                </div>
                <div class="text-secondary small fw-semibold">ทะเบียนหนังสือรับภายนอก</div>
                <div class="d-flex align-items-baseline gap-2 my-1">
                    <h2 class="fw-bold mb-0 text-dark"><?= number_format($inCount) ?></h2>
                    <span class="text-muted small">ฉบับ</span>
                </div>
                <div class="border-top pt-2 mt-2">
                    <a href="<?= base_url('admin/documents?type=in') ?>" class="small fw-bold text-primary d-inline-flex align-items-center gap-1 text-decoration-none">
                        เปิดทะเบียนหนังสือรับ <i class="bx bx-chevron-right fs-5"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. หนังสือส่งภายนอก -->
    <div class="col-xl-3 col-sm-6">
        <div class="card stat-card-saraban h-100">
            <div class="card-body p-3.5">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="avatar avatar-md bg-label-success rounded p-1">
                        <i class="bx bx-log-out-circle fs-3 text-success"></i>
                    </div>
                    <span class="badge bg-success text-white fw-bold">หนังสือส่ง</span>
                </div>
                <div class="text-secondary small fw-semibold">ทะเบียนหนังสือส่งภายนอก</div>
                <div class="d-flex align-items-baseline gap-2 my-1">
                    <h2 class="fw-bold mb-0 text-dark"><?= number_format($outCount) ?></h2>
                    <span class="text-muted small">ฉบับ</span>
                </div>
                <div class="border-top pt-2 mt-2">
                    <a href="<?= base_url('admin/documents?type=out') ?>" class="small fw-bold text-success d-inline-flex align-items-center gap-1 text-decoration-none">
                        เปิดทะเบียนหนังสือส่ง <i class="bx bx-chevron-right fs-5"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. บันทึกข้อความภายใน -->
    <div class="col-xl-3 col-sm-6">
        <div class="card stat-card-saraban h-100">
            <div class="card-body p-3.5">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="avatar avatar-md bg-label-info rounded p-1">
                        <i class="bx bx-message-detail fs-3 text-info"></i>
                    </div>
                    <span class="badge bg-info text-white fw-bold">บันทึกข้อความ</span>
                </div>
                <div class="text-secondary small fw-semibold">ทะเบียนบันทึกข้อความ</div>
                <div class="d-flex align-items-baseline gap-2 my-1">
                    <h2 class="fw-bold mb-0 text-dark"><?= number_format($internalCount) ?></h2>
                    <span class="text-muted small">ฉบับ</span>
                    <?php if ($pendingSarabanCount > 0): ?>
                        <span class="badge bg-danger text-white ms-auto small fw-bold">รอออกเลข <?= $pendingSarabanCount ?></span>
                    <?php endif; ?>
                </div>
                <div class="border-top pt-2 mt-2">
                    <a href="<?= base_url('admin/memos') ?>" class="small fw-bold text-info d-inline-flex align-items-center gap-1 text-decoration-none">
                        เปิดทะเบียนบันทึกข้อความ <i class="bx bx-chevron-right fs-5"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. คำสั่งโรงเรียน -->
    <div class="col-xl-3 col-sm-6">
        <div class="card stat-card-saraban h-100">
            <div class="card-body p-3.5">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="avatar avatar-md bg-label-warning rounded p-1">
                        <i class="bx bx-award fs-3 text-warning"></i>
                    </div>
                    <span class="badge bg-warning text-dark fw-bold">คำสั่ง รร.</span>
                </div>
                <div class="text-secondary small fw-semibold">ทะเบียนคำสั่งโรงเรียน</div>
                <div class="d-flex align-items-baseline gap-2 my-1">
                    <h2 class="fw-bold mb-0 text-dark"><?= number_format($orderCount) ?></h2>
                    <span class="text-muted small">ฉบับ</span>
                    <?php if ($draftOrderCount > 0): ?>
                        <span class="badge bg-secondary text-white ms-auto small">ร่าง <?= $draftOrderCount ?></span>
                    <?php endif; ?>
                </div>
                <div class="border-top pt-2 mt-2">
                    <a href="<?= base_url('admin/orders') ?>" class="small fw-bold text-dark d-inline-flex align-items-center gap-1 text-decoration-none">
                        เปิดทะเบียนคำสั่งโรงเรียน <i class="bx bx-chevron-right fs-5"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- ========================================== -->
<!-- 4. Interactive Workspace Tables (แท็บจัดการงาน) -->
<!-- ========================================== -->
<div class="card border-0 shadow-sm" style="border-radius: 1rem;">
    <div class="card-header bg-white pb-0 border-bottom-0 pt-3 px-4">
        <ul class="nav nav-tabs dashboard-nav-tabs" id="sarabanDashboardTab" role="tablist">
            <?php if ($isAdmin): ?>
            <!-- แท็บ 1: บันทึกข้อความรอออกเลข (เฉพาะงานสารบรรณ) -->
            <li class="nav-item" role="presentation">
                <button class="nav-link <?= $pendingSarabanCount > 0 ? 'active' : '' ?> d-flex align-items-center gap-2" id="memos-pending-tab" data-bs-toggle="tab" data-bs-target="#tab-memos-pending" type="button" role="tab">
                    <i class="bx bx-hash text-danger fs-5"></i>
                    <span>บันทึกข้อความรอสารบรรณออกเลข</span>
                    <?php if ($pendingSarabanCount > 0): ?>
                        <span class="badge bg-danger text-white rounded-pill px-2 fw-bold"><?= $pendingSarabanCount ?></span>
                    <?php endif; ?>
                </button>
            </li>
            <?php endif; ?>

            <?php if (!$isTeacher): ?>
            <!-- แท็บ 2: รอคุณเกษียน/ลงนาม -->
            <li class="nav-item" role="presentation">
                <button class="nav-link <?= ($isExecutive || (!$isTeacher && $pendingSarabanCount === 0 && $pendingCount > 0)) ? 'active' : '' ?> d-flex align-items-center gap-2" id="pending-tab" data-bs-toggle="tab" data-bs-target="#tab-pending" type="button" role="tab">
                    <i class="bx bx-pencil text-warning fs-5"></i>
                    <span>รอคุณเกษียน/ลงนาม</span>
                    <?php if ($pendingCount > 0): ?>
                        <span class="badge bg-warning text-dark rounded-pill px-2 fw-bold"><?= $pendingCount ?></span>
                    <?php endif; ?>
                </button>
            </li>

            <!-- แท็บ 3: หนังสือราชการล่าสุดทุกหมวด -->
            <li class="nav-item" role="presentation">
                <button class="nav-link <?= (!$isExecutive && !$isTeacher && $pendingSarabanCount === 0 && $pendingCount === 0) ? 'active' : '' ?> d-flex align-items-center gap-2" id="recent-all-tab" data-bs-toggle="tab" data-bs-target="#tab-recent-all" type="button" role="tab">
                    <i class="bx bx-time-five text-primary fs-5"></i>
                    <span>หนังสือเข้าใหม่ล่าสุด</span>
                </button>
            </li>

            <!-- แท็บ 4: คำสั่งโรงเรียนล่าสุด -->
            <li class="nav-item" role="presentation">
                <button class="nav-link d-flex align-items-center gap-2" id="orders-tab" data-bs-toggle="tab" data-bs-target="#tab-orders" type="button" role="tab">
                    <i class="bx bx-award text-warning fs-5"></i>
                    <span>คำสั่งโรงเรียนล่าสุด</span>
                </button>
            </li>
            <?php endif; ?>

            <!-- แท็บ 5: คำขอไปราชการ -->
            <li class="nav-item" role="presentation">
                <button class="nav-link <?= $isTeacher ? 'active' : '' ?> d-flex align-items-center gap-2" id="travel-tab" data-bs-toggle="tab" data-bs-target="#tab-travel" type="button" role="tab">
                    <i class="bx bx-briefcase-alt-2 text-info fs-5"></i>
                    <span><?= $isTeacher ? 'งานไปราชการของฉัน' : 'คำขอไปราชการล่าสุด' ?></span>
                    <?php if ($isTeacher && !empty($myTravel)): ?>
                        <span class="badge bg-info text-white rounded-pill px-2 fw-bold"><?= count($myTravel) ?></span>
                    <?php endif; ?>
                </button>
            </li>
        </ul>
    </div>

    <div class="tab-content p-0" id="sarabanDashboardTabContent">
        <?php if ($isAdmin): ?>
        <!-- Tab 1: บันทึกข้อความรอสารบรรณออกเลข (เฉพาะเจ้าหน้าที่สารบรรณ) -->
        <div class="tab-pane fade <?= $pendingSarabanCount > 0 ? 'show active' : '' ?>" id="tab-memos-pending" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center px-4 py-3 bg-light bg-opacity-50 border-bottom">
                <span class="text-secondary small fw-semibold">
                    <i class="bx bx-info-circle me-1 text-danger"></i> รายการบันทึกข้อความที่บุคลากรส่งเรื่องเข้ามา เพื่อให้งานสารบรรณตรวจสอบความถูกต้องและออกเลขหนังสือ
                </span>
                <a href="<?= base_url('admin/memos/saraban-inbox') ?>" class="btn btn-sm btn-danger text-white fw-bold shadow-xs">
                    เปิดกล่องรอออกเลขทั้งหมด (<?= $pendingSarabanCount ?>) <i class="bx bx-chevron-right"></i>
                </a>
            </div>

            <div class="table-responsive">
                <table class="table custom-dashboard-table mb-0" style="width: 100%; table-layout: auto;">
                    <thead>
                        <tr>
                            <th style="width: 110px;" class="text-center">วันที่ส่ง</th>
                            <th style="width: 150px;">ผู้จัดทำ</th>
                            <th>เรื่องบันทึกข้อความ</th>
                            <th style="width: 180px;">หน่วยงาน/กลุ่มสาระ</th>
                            <th class="text-center" style="width: 160px;">การจัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pendingSarabanMemos)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="bx bx-check-circle text-success fs-1 d-block mb-2"></i>
                                    <div class="fw-bold text-dark">ไม่มีบันทึกข้อความรอออกเลขในขณะนี้</div>
                                    <div class="small text-muted mt-1">เมื่อมีบุคลากรส่งเรื่องเข้ามา รายการจะแสดงที่นี่ทันที</div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($pendingSarabanMemos as $memo): ?>
                                <tr>
                                    <td class="text-center text-nowrap">
                                        <span class="small text-muted"><?= esc(thai_date($memo['created_at'] ?? $memo['doc_date'] ?? '', 'short')) ?></span>
                                    </td>
                                    <td class="text-nowrap">
                                        <span class="fw-semibold text-dark">
                                            <i class="bx bx-user text-muted me-1"></i><?= esc(trim(($memo['prefix'] ?? '') . ($memo['first_name'] ?? '') . ' ' . ($memo['last_name'] ?? ''))) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('admin/memos/' . $memo['id']) ?>" class="fw-bold text-dark text-decoration-none hover-primary d-block text-wrap" style="line-height: 1.4;">
                                            <?= esc($memo['title'] ?? '-') ?>
                                        </a>
                                        <div class="d-flex align-items-center gap-1 flex-wrap mt-1">
                                            <span class="badge bg-label-danger py-0 px-1 small">รอสารบรรณออกเลข</span>
                                            <span class="badge bg-label-primary py-0 px-1 small"><i class="bx bxs-crown text-warning me-1"></i>เสนอ: ผอ.โรงเรียน</span>
                                        </div>
                                    </td>
                                    <td class="small text-muted text-wrap">
                                        <?= esc($memo['creator_department'] ?? $memo['memo_department'] ?? '-') ?>
                                    </td>
                                    <td class="text-center text-nowrap">
                                        <div class="d-flex align-items-center justify-content-center gap-1">
                                            <a href="<?= base_url('admin/memos/' . $memo['id']) ?>" class="btn btn-sm btn-outline-primary px-2 py-1" title="ตรวจดูเนื้อหา">
                                                <i class="bx bx-show me-1"></i>ตรวจดู
                                            </a>
                                            <a href="<?= base_url('admin/memos/' . $memo['id']) ?>" class="btn btn-sm btn-success text-white px-2 py-1 fw-bold" title="เปิดบันทึกข้อความเพื่อตรวจทานและประทับตราออกเลข">
                                                <i class="bx bx-stamp me-1"></i>ออกเลข
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($isTeacher): ?>
        <!-- Tab: บันทึกข้อความของฉัน (สำหรับคุณครูและบุคลากร) -->
        <div class="tab-pane fade <?= ($pendingCount === 0) ? 'show active' : '' ?>" id="tab-my-memos" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center px-4 py-3 bg-light bg-opacity-50 border-bottom">
                <span class="text-secondary small fw-semibold">
                    <i class="bx bx-info-circle me-1 text-primary"></i> รายการบันทึกข้อความที่ท่านสร้างขึ้นในระบบ พร้อมติดตามสถานะการเสนอเกษียน
                </span>
                <a href="<?= base_url('admin/memos/create') ?>" class="btn btn-sm btn-warning text-dark fw-bold shadow-xs">
                    <i class="bx bx-plus-circle me-1"></i> เขียนบันทึกข้อความใหม่
                </a>
            </div>

            <div class="table-responsive">
                <table class="table custom-dashboard-table mb-0" style="width: 100%; table-layout: auto;">
                    <thead>
                        <tr>
                            <th style="width: 110px;" class="text-center">วันที่จัดทำ</th>
                            <th style="width: 160px;">เลขที่บันทึก</th>
                            <th>เรื่องบันทึกข้อความ</th>
                            <th style="width: 150px;" class="text-center">สถานะ</th>
                            <th class="text-center" style="width: 140px;">การจัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($myMemos)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="bx bx-notepad text-primary fs-1 d-block mb-2"></i>
                                    <div class="fw-bold text-dark">ท่านยังไม่มีบันทึกข้อความในระบบ</div>
                                    <div class="small text-muted mt-1 mb-3">เมื่อท่านสร้างบันทึกข้อความเพื่อเสนอขออนุมัติหรือรายงาน รายการจะแสดงที่นี่</div>
                                    <a href="<?= base_url('admin/memos/create') ?>" class="btn btn-sm btn-primary">
                                        <i class="bx bx-plus me-1"></i> เขียนบันทึกข้อความฉบับแรก
                                    </a>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($myMemos as $memo): ?>
                                <tr>
                                    <td class="text-center text-nowrap">
                                        <span class="small text-muted"><?= esc(thai_date($memo['doc_date'] ?? $memo['created_at'] ?? '', 'short')) ?></span>
                                    </td>
                                    <td class="text-nowrap">
                                        <?php if (!empty($memo['doc_no'])): ?>
                                            <span class="fw-bold text-dark"><?= esc($memo['doc_no']) ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-label-secondary small"><i class="bx bx-time-five me-1"></i>รอสารบรรณออกเลข</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('admin/memos/' . $memo['id']) ?>" class="fw-bold text-dark text-decoration-none hover-primary d-block text-wrap" style="line-height: 1.4;">
                                            <?= esc($memo['title'] ?? '-') ?>
                                        </a>
                                        <div class="d-flex align-items-center gap-1 flex-wrap mt-1">
                                            <?php if (($memo['status'] ?? '') === 'pending_saraban'): ?>
                                                <span class="badge bg-danger text-white py-0 px-1 small">รอสารบรรณออกเลข</span>
                                            <?php elseif (($memo['status'] ?? '') === 'routing'): ?>
                                                <span class="badge bg-warning text-dark py-0 px-1 small">อยู่ระหว่างเสนอเกษียน</span>
                                            <?php elseif (($memo['status'] ?? '') === 'approved' || ($memo['status'] ?? '') === 'completed'): ?>
                                                <span class="badge bg-success text-white py-0 px-1 small">เกษียนเรียบร้อยแล้ว</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary text-white py-0 px-1 small"><?= esc($memo['status'] ?? 'ฉบับร่าง') ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="text-center text-nowrap">
                                        <?= doc_status_badge($memo['status'] ?? 'draft') ?>
                                    </td>
                                    <td class="text-center text-nowrap">
                                        <div class="d-flex align-items-center justify-content-center gap-1">
                                            <a href="<?= base_url('admin/memos/' . $memo['id']) ?>" class="btn btn-sm btn-outline-primary px-2 py-1" title="ดูรายละเอียด">
                                                <i class="bx bx-show me-1"></i>ดูเรื่อง
                                            </a>
                                            <a href="<?= base_url('admin/memos/' . $memo['id'] . '/print') ?>" target="_blank" class="btn btn-sm btn-outline-secondary px-2 py-1" title="พิมพ์/ส่งออก">
                                                <i class="bx bx-printer"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- Tab 2: รอคุณเกษียน/ลงนาม -->
        <div class="tab-pane fade <?= ($isExecutive || ($pendingSarabanCount === 0 && $pendingCount > 0)) ? 'show active' : '' ?>" id="tab-pending" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center px-4 py-3 bg-light bg-opacity-50 border-bottom">
                <span class="text-secondary small fw-semibold">
                    <i class="bx bx-info-circle me-1 text-primary"></i> แสดงเอกสารที่ส่งถึงคุณและต้องการการพิจารณาลงนามเป็นลำดับถัดไป
                </span>
                <a href="<?= base_url('admin/documents/inbox') ?>" class="btn btn-sm btn-warning text-dark fw-bold shadow-xs">
                    ดูทั้งหมด (<?= $pendingCount ?>) <i class="bx bx-chevron-right"></i>
                </a>
            </div>

            <div class="table-responsive">
                <table class="table custom-dashboard-table mb-0" style="width: 100%; table-layout: auto;">
                    <thead>
                        <tr>
                            <th style="width: 90px;" class="text-center">ความเร่งด่วน</th>
                            <th style="width: 120px;">ประเภท</th>
                            <th style="width: 130px;">เลขที่หนังสือ</th>
                            <th>เรื่อง / สาระสำคัญ</th>
                            <th style="width: 150px;">ผู้เสนอเรื่อง</th>
                            <th style="width: 95px;" class="text-center">วันที่รับ</th>
                            <th class="text-center" style="width: 100px;">ดำเนินการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentPending)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bx bx-check-double text-success fs-1 d-block mb-2"></i>
                                    <div class="fw-bold text-dark">ยอดเยี่ยมมาก! ไม่มีงานรอคุณลงนาม</div>
                                    <div class="small text-muted mt-1">ขณะนี้ไม่มีหนังสือราชการที่รอคุณลงนามหรือเกษียนความเห็น</div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentPending as $doc): ?>
                                <tr>
                                    <td class="text-center text-nowrap"><?= urgency_badge($doc['urgency']) ?></td>
                                    <td class="text-nowrap"><?= doc_type_badge($doc['doc_type']) ?></td>
                                    <td class="text-nowrap">
                                        <span class="fw-bold text-dark"><?= esc($doc['doc_no']) ?></span>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('admin/documents/' . $doc['id']) ?>" class="fw-semibold text-dark text-decoration-none hover-primary d-block text-wrap" style="line-height: 1.4;">
                                            <?= esc($doc['title']) ?>
                                        </a>
                                        <span class="text-muted small">จาก: <?= esc($doc['from_org'] ?? 'ไม่ระบุ') ?></span>
                                    </td>
                                    <td class="text-nowrap">
                                        <span class="text-dark fw-semibold small">
                                            <i class="bx bx-user me-1 text-muted"></i><?= esc(($doc['creator_first'] ?? '') . ' ' . ($doc['creator_last'] ?? '')) ?>
                                        </span>
                                    </td>
                                    <td class="text-center text-nowrap small text-muted">
                                        <?= thai_date($doc['receive_date'] ?? $doc['doc_date'] ?? $doc['created_at'], 'short') ?>
                                    </td>
                                    <td class="text-center text-nowrap">
                                        <a href="<?= base_url('admin/documents/' . $doc['id']) ?>" class="btn btn-sm btn-warning text-dark fw-bold px-2.5 py-1 rounded shadow-xs">
                                            <i class="bx bx-edit me-1"></i> เกษียน
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tab 3: หนังสือราชการล่าสุดทุกหมวด -->
        <div class="tab-pane fade <?= (!$isExecutive && $pendingSarabanCount === 0 && $pendingCount === 0) ? 'show active' : '' ?>" id="tab-recent-all" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center px-4 py-3 bg-light bg-opacity-50 border-bottom">
                <span class="text-muted small fw-semibold">
                    <i class="bx bx-info-circle me-1"></i> ภาพรวมหนังสือราชการล่าสุดที่มีการบันทึกเข้าระบบ
                </span>
                <a href="<?= base_url('admin/documents') ?>" class="btn btn-sm btn-outline-secondary fw-semibold">
                    ดูทะเบียนทั้งหมด <i class="bx bx-chevron-right"></i>
                </a>
            </div>

            <div class="table-responsive">
                <table class="table custom-dashboard-table mb-0" style="width: 100%; table-layout: auto;">
                    <thead>
                        <tr>
                            <th style="width: 135px;" class="text-center">สถานะ</th>
                            <th style="width: 120px;">ประเภท</th>
                            <th style="width: 130px;">เลขที่หนังสือ</th>
                            <th>เรื่อง</th>
                            <th style="width: 200px;">จากหน่วยงาน</th>
                            <th style="width: 95px;" class="text-center">ลงวันที่</th>
                            <th class="text-center" style="width: 85px;">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentAll)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bx bx-folder-open fs-1 d-block mb-2 text-secondary"></i>
                                    <div class="fw-semibold">ยังไม่มีประวัติเอกสารในระบบ</div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentAll as $doc): ?>
                                <tr>
                                    <td class="text-center text-nowrap"><?= doc_status_badge($doc['status']) ?></td>
                                    <td class="text-nowrap"><?= doc_type_badge($doc['doc_type']) ?></td>
                                    <td class="text-nowrap">
                                        <span class="fw-bold text-dark"><?= esc($doc['doc_no']) ?></span>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('admin/documents/' . $doc['id']) ?>" class="text-dark fw-semibold text-decoration-none hover-primary d-block text-wrap" style="line-height: 1.4;">
                                            <?= esc($doc['title']) ?>
                                        </a>
                                    </td>
                                    <td class="small text-muted text-wrap">
                                        <i class="bx bx-building me-1"></i><?= esc($doc['from_org'] ?? '-') ?>
                                    </td>
                                    <td class="text-center text-nowrap small text-muted">
                                        <?= thai_date($doc['doc_date'] ?? $doc['created_at'], 'short') ?>
                                    </td>
                                    <td class="text-center text-nowrap">
                                        <a href="<?= base_url('admin/documents/' . $doc['id']) ?>" class="btn btn-sm btn-outline-primary px-2.5 py-1">
                                            <i class="bx bx-show me-1"></i>ดูเรื่อง
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tab 4: คำสั่งโรงเรียนล่าสุด -->
        <div class="tab-pane fade" id="tab-orders" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center px-4 py-3 bg-light bg-opacity-50 border-bottom">
                <span class="text-muted small fw-semibold">
                    <i class="bx bx-award me-1 text-warning"></i> รายการคำสั่งโรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์ ล่าสุด
                </span>
                <a href="<?= base_url('admin/orders') ?>" class="btn btn-sm btn-warning text-dark fw-bold shadow-xs">
                    เปิดทะเบียนคำสั่งทั้งหมด <i class="bx bx-chevron-right"></i>
                </a>
            </div>

            <div class="table-responsive">
                <table class="table custom-dashboard-table mb-0" style="width: 100%; table-layout: auto;">
                    <thead>
                        <tr>
                            <th style="width: 120px;" class="text-center">เลขที่คำสั่ง</th>
                            <th>เรื่องคำสั่งโรงเรียน</th>
                            <th style="width: 120px;" class="text-center">วันสั่งการ</th>
                            <th style="width: 180px;">ผู้มีอำนาจสั่งการ</th>
                            <th style="width: 100px;" class="text-center">สถานะ</th>
                            <th class="text-center" style="width: 85px;">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentOrders)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bx bx-award fs-1 d-block mb-2 text-secondary"></i>
                                    <div class="fw-semibold">ยังไม่มีคำสั่งโรงเรียนในระบบ</div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentOrders as $order): ?>
                                <tr>
                                    <td class="text-center text-nowrap">
                                        <span class="badge bg-dark text-white fw-bold px-2 py-1"><?= esc($order['doc_no'] ?: '-') ?></span>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('admin/orders/' . $order['id']) ?>" class="fw-semibold text-dark text-decoration-none hover-primary d-block text-wrap" style="line-height: 1.4;">
                                            <?= esc($order['title']) ?>
                                        </a>
                                    </td>
                                    <td class="text-center text-nowrap small text-muted">
                                        <?= !empty($order['doc_date']) ? esc(thai_date($order['doc_date'], 'short')) : '-' ?>
                                    </td>
                                    <td class="small text-muted text-nowrap">
                                        <?= esc($order['signer_name'] ?? 'ผู้อำนวยการโรงเรียน') ?>
                                    </td>
                                    <td class="text-center text-nowrap">
                                        <?php if (($order['status'] ?? '') === 'published'): ?>
                                            <span class="badge bg-success text-white fw-semibold">มีผลบังคับใช้</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary text-white">แบบร่าง</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center text-nowrap">
                                        <a href="<?= base_url('admin/orders/' . $order['id']) ?>" class="btn btn-sm btn-outline-primary px-2.5 py-1">
                                            <i class="bx bx-show me-1"></i>ดูคำสั่ง
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tab 5: คำขอไปราชการ -->
        <div class="tab-pane fade <?= $isTeacher ? 'show active' : '' ?>" id="tab-travel" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center px-4 py-3 bg-light bg-opacity-50 border-bottom">
                <span class="text-muted small fw-semibold">
                    <i class="bx bx-briefcase-alt-2 me-1 text-info"></i> <?= $isTeacher ? 'ประวัติและรายการขออนุมัติเดินทางไปราชการของท่าน' : 'รายการคำขออนุมัติเดินทางไปราชการล่าสุดของบุคลากร' ?>
                </span>
                <div class="d-flex gap-2">
                    <?php if ($isTeacher): ?>
                        <a href="<?= base_url('admin/travel/create') ?>" class="btn btn-sm btn-info text-white fw-bold shadow-xs">
                            <i class="bx bx-plus-circle me-1"></i> ยื่นขอไปราชการ
                        </a>
                        <a href="<?= base_url('admin/travel/my') ?>" class="btn btn-sm btn-outline-secondary fw-semibold">
                            ดูของฉันทั้งหมด <i class="bx bx-chevron-right"></i>
                        </a>
                    <?php else: ?>
                        <a href="<?= base_url('admin/travel') ?>" class="btn btn-sm btn-outline-info fw-bold">
                            เปิดทะเบียนไปราชการ <i class="bx bx-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table custom-dashboard-table mb-0" style="width: 100%; table-layout: auto;">
                    <thead>
                        <tr>
                            <?php if (!$isTeacher): ?>
                                <th style="width: 150px;">ผู้ขอไปราชการ</th>
                            <?php endif; ?>
                            <th>เรื่อง / วัตถุประสงค์</th>
                            <th style="width: 160px;">สถานที่ปลายทาง</th>
                            <th style="width: 110px;" class="text-center">วันที่เดินทาง</th>
                            <th style="width: 140px;" class="text-center">สถานะ</th>
                            <th class="text-center" style="width: 85px;">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $travelItemsToDisplay = $isTeacher ? ($myTravelList ?? []) : ($recentTravel ?? []);
                        ?>
                        <?php if (empty($travelItemsToDisplay)): ?>
                            <tr>
                                <td colspan="<?= $isTeacher ? '5' : '6' ?>" class="text-center py-5 text-muted">
                                    <i class="bx bx-briefcase-alt-2 fs-1 d-block mb-2 text-secondary"></i>
                                    <div class="fw-semibold"><?= $isTeacher ? 'ท่านยังไม่มีรายการขอไปราชการในระบบ' : 'ยังไม่มีรายการขอไปราชการในระบบ' ?></div>
                                    <?php if ($isTeacher): ?>
                                        <div class="small text-muted mt-1 mb-3">เมื่อท่านยื่นขออนุมัติไปราชการ รายการจะแสดงเพื่อติดตามสถานะที่นี่</div>
                                        <a href="<?= base_url('admin/travel/create') ?>" class="btn btn-sm btn-primary">
                                            <i class="bx bx-plus me-1"></i> ยื่นขออนุมัติไปราชการ
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($travelItemsToDisplay as $tr): ?>
                                <tr>
                                    <?php if (!$isTeacher): ?>
                                    <td class="text-nowrap">
                                        <span class="fw-semibold text-dark">
                                            <i class="bx bx-user text-muted me-1"></i><?= esc(($tr['prefix'] ?? '') . ($tr['first_name'] ?? '') . ' ' . ($tr['last_name'] ?? '')) ?>
                                        </span>
                                    </td>
                                    <?php endif; ?>
                                    <td>
                                        <a href="<?= base_url('admin/travel/' . $tr['id']) ?>" class="fw-semibold text-dark text-decoration-none hover-primary d-block text-wrap" style="line-height: 1.4;">
                                            <?= esc($tr['title']) ?>
                                        </a>
                                    </td>
                                    <td class="small text-muted text-wrap">
                                        <i class="bx bx-map-pin text-danger me-1"></i><?= esc($tr['destination']) ?><?= !empty($tr['province']) ? ' จ.' . esc($tr['province']) : '' ?>
                                    </td>
                                    <td class="text-center text-nowrap small text-muted">
                                        <?= !empty($tr['start_date']) ? esc(thai_date($tr['start_date'], 'short')) : '-' ?>
                                    </td>
                                    <td class="text-center text-nowrap">
                                        <?= travel_status_badge($tr['status']) ?>
                                    </td>
                                    <td class="text-center text-nowrap">
                                        <a href="<?= base_url('admin/travel/' . $tr['id']) ?>" class="btn btn-sm btn-outline-info px-2.5 py-1">
                                            <i class="bx bx-show me-1"></i>ดูเรื่อง
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
