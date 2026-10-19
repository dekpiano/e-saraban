<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>แดชบอร์ดภาพรวมระบบ<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
/* Modern Dashboard Styling */
.dashboard-hero-banner {
    background: linear-gradient(135deg, #4f46e5 0%, #6366f1 50%, #818cf8 100%);
    border-radius: 1.25rem;
    box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.3), 0 8px 10px -6px rgba(79, 70, 229, 0.2);
    position: relative;
    overflow: hidden;
    color: #ffffff;
}

.dashboard-hero-banner::before {
    content: '';
    position: absolute;
    top: -40%;
    right: -10%;
    width: 320px;
    height: 320px;
    background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 70%);
    border-radius: 50%;
    pointer-events: none;
}

.dashboard-hero-banner::after {
    content: '';
    position: absolute;
    bottom: -30%;
    left: 20%;
    width: 240px;
    height: 240px;
    background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, rgba(255,255,255,0) 70%);
    border-radius: 50%;
    pointer-events: none;
}

.stat-card-modern {
    border-radius: 1.15rem;
    border: 1px solid rgba(226, 232, 240, 0.8);
    background: #ffffff;
    box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.04);
    transition: all 0.25s cubic-bezier(0.165, 0.84, 0.44, 1);
    position: relative;
    overflow: hidden;
}

.stat-card-modern:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 28px -4px rgba(0, 0, 0, 0.09);
    border-color: #cbd5e1;
}

.stat-icon-wrapper {
    width: 54px;
    height: 54px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 1rem;
    font-size: 1.75rem;
    transition: transform 0.2s ease;
}

.stat-card-modern:hover .stat-icon-wrapper {
    transform: scale(1.08);
}

.pulse-badge-danger {
    animation: pulseDanger 1.8s infinite;
}

@keyframes pulseDanger {
    0% {
        box-shadow: 0 0 0 0 rgba(255, 62, 29, 0.6);
    }
    70% {
        box-shadow: 0 0 0 8px rgba(255, 62, 29, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(255, 62, 29, 0);
    }
}

.quick-action-pill {
    backdrop-filter: blur(8px);
    background: rgba(255, 255, 255, 0.16);
    border: 1px solid rgba(255, 255, 255, 0.3);
    color: #ffffff !important;
    transition: all 0.2s ease;
    font-weight: 600;
}

.quick-action-pill:hover {
    background: #ffffff;
    color: #4f46e5 !important;
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0,0,0,0.15);
}

.custom-dashboard-table thead th {
    font-size: 0.825rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 700;
    color: #475569 !important;
    background-color: #f8fafc !important;
    border-top: none;
    border-bottom: 2px solid #e2e8f0;
    padding-top: 0.9rem;
    padding-bottom: 0.9rem;
}

.custom-dashboard-table tbody tr {
    transition: background-color 0.15s ease;
}

.custom-dashboard-table tbody tr:hover {
    background-color: #f8faff;
}

.custom-dashboard-table td {
    vertical-align: middle;
    padding-top: 0.85rem;
    padding-bottom: 0.85rem;
    border-bottom: 1px solid #f1f5f9;
}

.dashboard-nav-tabs .nav-link {
    font-weight: 600;
    color: #64748b;
    border: none;
    border-bottom: 3px solid transparent;
    padding: 0.75rem 1.25rem;
    border-radius: 0;
    transition: all 0.2s ease;
}

.dashboard-nav-tabs .nav-link.active {
    color: #4f46e5;
    background: transparent;
    border-bottom-color: #4f46e5;
}

.dashboard-nav-tabs .nav-link:hover:not(.active) {
    color: #1e293b;
}

.chip-status {
    font-size: 0.75rem;
    font-weight: 600;
    padding: 0.35rem 0.65rem;
    border-radius: 2rem;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
}

.empty-state-card {
    padding: 3rem 1.5rem;
    text-align: center;
}
</style>

<!-- ========================================== -->
<!-- 1. Hero Welcome & Quick Actions Banner    -->
<!-- ========================================== -->
<div class="row mb-4">
    <div class="col-12">
        <div class="dashboard-hero-banner p-4 p-md-5">
            <div class="row align-items-center">
                <div class="col-lg-8 col-md-8 position-relative z-1">
                    <div class="d-inline-flex align-items-center gap-2 px-3 py-1 mb-3 rounded-pill bg-dark text-white small fw-bold shadow-sm" style="background-color: rgba(15, 23, 42, 0.75) !important;">
                        <i class="bx bxs-institution text-warning"></i> ระบบสารบรรณและเกษียนอิเล็กทรอนิกส์
                    </div>
                    <h2 class="text-white fw-bold mb-2">
                        สวัสดีคุณ <?= esc(session()->get('full_name') ?? 'ผู้ปฏิบัติงาน') ?> 👋
                    </h2>
                    <p class="text-white fs-6 mb-4">
                        ตำแหน่ง: <span class="badge bg-white text-dark fw-bold ms-1 px-2 py-1 shadow-sm"><?= esc(session()->get('position_name') ?? 'บุคลากร') ?></span>
                        <span class="mx-2 text-white fw-bold">•</span>
                        สังกัด: <span class="badge bg-white text-dark fw-bold ms-1 px-2 py-1 shadow-sm"><?= esc(session()->get('department_name') ?? 'โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์') ?></span>
                    </p>

                    <!-- Quick Action Buttons -->
                    <div class="d-flex flex-wrap gap-2">
                        <?php if ($pendingCount > 0): ?>
                            <a href="<?= base_url('admin/documents/inbox') ?>" class="btn btn-warning text-dark fw-bold px-3 shadow">
                                <i class="bx bx-edit-alt me-1"></i> หนังสือรอคุณเกษียน <span class="badge bg-danger text-white rounded-pill ms-1 fw-bold"><?= $pendingCount ?></span>
                            </a>
                        <?php endif; ?>

                        <a href="<?= base_url('admin/memos/create') ?>" class="btn btn-light text-primary fw-bold px-3 shadow-sm">
                            <i class="bx bx-edit me-1"></i> เขียนบันทึกข้อความ
                        </a>

                        <a href="<?= base_url('admin/travel/create') ?>" class="btn btn-light text-primary fw-bold px-3 shadow-sm">
                            <i class="bx bx-briefcase-alt-2 me-1"></i> ยื่นขอไปราชการ
                        </a>

                        <a href="<?= base_url('admin/documents') ?>" class="btn btn-light text-primary fw-bold px-3 shadow-sm">
                            <i class="bx bx-search-alt me-1"></i> ทะเบียนสารบรรณ
                        </a>
                    </div>
                </div>

                <div class="col-lg-4 col-md-4 text-center mt-4 mt-md-0 d-none d-md-block position-relative z-1">
                    <img src="<?= base_url('assets/img/illustrations/man-with-laptop-light.png') ?>" 
                         alt="Hero Illustration" 
                         class="img-fluid" 
                         style="max-height: 160px; filter: drop-shadow(0 10px 15px rgba(0,0,0,0.2));" />
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- 2. Core Operational Metrics (4 Stats)     -->
<!-- ========================================== -->
<div class="row g-3 mb-4">
    <!-- รอฉันลงนาม -->
    <div class="col-xl-3 col-md-6 col-sm-6">
        <div class="card stat-card-modern h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="stat-icon-wrapper bg-label-warning text-warning">
                        <i class="bx bx-edit-alt"></i>
                    </div>
                    <?php if ($pendingCount > 0): ?>
                        <span class="badge bg-danger text-white rounded-pill pulse-badge-danger px-2 py-1 small fw-bold">
                            <i class="bx bx-bell me-1"></i> ต้องดำเนินการ
                        </span>
                    <?php else: ?>
                        <span class="badge bg-label-success rounded-pill px-2 py-1 small fw-bold">
                            <i class="bx bx-check me-1"></i> เรียบร้อย
                        </span>
                    <?php endif; ?>
                </div>
                <div class="text-dark opacity-75 small fw-semibold mb-1">หนังสือรอฉันลงนาม/เกษียน</div>
                <div class="d-flex align-items-baseline gap-2 mb-2">
                    <h2 class="fw-bold mb-0 text-dark" style="color: #b45309 !important;"><?= number_format($pendingCount) ?></h2>
                    <span class="text-secondary small fw-medium">ฉบับ</span>
                </div>
                <div class="border-top pt-2">
                    <a href="<?= base_url('admin/documents/inbox') ?>" class="small fw-bold d-inline-flex align-items-center gap-1 text-decoration-none" style="color: #b45309;">
                        เข้าสู่แฟ้มเกษียนหนังสือ <i class="bx bx-chevron-right fs-5"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- ฉันลงนามแล้ว -->
    <div class="col-xl-3 col-md-6 col-sm-6">
        <div class="card stat-card-modern h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="stat-icon-wrapper bg-label-success text-success">
                        <i class="bx bx-check-shield"></i>
                    </div>
                    <span class="badge bg-label-success rounded-pill px-2 py-1 small fw-bold">
                        ประวัติ
                    </span>
                </div>
                <div class="text-dark opacity-75 small fw-semibold mb-1">ฉันลงนาม/เกษียนแล้ว</div>
                <div class="d-flex align-items-baseline gap-2 mb-2">
                    <h2 class="fw-bold mb-0" style="color: #15803d !important;"><?= number_format($signedCount) ?></h2>
                    <span class="text-secondary small fw-medium">ฉบับ</span>
                </div>
                <div class="border-top pt-2">
                    <a href="<?= base_url('admin/documents/signed') ?>" class="small fw-bold d-inline-flex align-items-center gap-1 text-decoration-none" style="color: #15803d;">
                        ดูประวัติที่ลงนามแล้ว <i class="bx bx-chevron-right fs-5"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- เอกสารที่ฉันสร้าง/เสนอ -->
    <div class="col-xl-3 col-md-6 col-sm-6">
        <div class="card stat-card-modern h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="stat-icon-wrapper bg-label-info text-info">
                        <i class="bx bx-send"></i>
                    </div>
                    <span class="badge bg-label-info rounded-pill px-2 py-1 small fw-bold">
                        ของฉัน
                    </span>
                </div>
                <div class="text-dark opacity-75 small fw-semibold mb-1">หนังสือที่ฉันเป็นผู้สร้าง</div>
                <div class="d-flex align-items-baseline gap-2 mb-2">
                    <h2 class="fw-bold mb-0" style="color: #0369a1 !important;"><?= number_format($myDocsCount) ?></h2>
                    <span class="text-secondary small fw-medium">ฉบับ</span>
                </div>
                <div class="border-top pt-2">
                    <a href="<?= base_url('admin/documents/my') ?>" class="small fw-bold d-inline-flex align-items-center gap-1 text-decoration-none" style="color: #0369a1;">
                        ติดตามสถานะที่เสนอ <i class="bx bx-chevron-right fs-5"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- ทะเบียนหนังสือทั้งหมด -->
    <div class="col-xl-3 col-md-6 col-sm-6">
        <div class="card stat-card-modern h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="stat-icon-wrapper bg-label-primary text-primary">
                        <i class="bx bx-archive"></i>
                    </div>
                    <span class="badge bg-label-primary rounded-pill px-2 py-1 small fw-bold">
                        ภาพรวม รร.
                    </span>
                </div>
                <div class="text-dark opacity-75 small fw-semibold mb-1">ทะเบียนหนังสือราชการทั้งหมด</div>
                <div class="d-flex align-items-baseline gap-2 mb-2">
                    <h2 class="fw-bold mb-0" style="color: #4338ca !important;"><?= number_format($totalCount) ?></h2>
                    <span class="text-secondary small fw-medium">ฉบับ</span>
                </div>
                <div class="border-top pt-2">
                    <a href="<?= base_url('admin/documents') ?>" class="small fw-bold d-inline-flex align-items-center gap-1 text-decoration-none" style="color: #4338ca;">
                        เปิดสารบรรณกลาง <i class="bx bx-chevron-right fs-5"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- 3. Travel & Operational Sub-Banner         -->
<!-- ========================================== -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: 1rem; background-color: #ffffff;">
    <div class="card-body p-3 p-md-4">
        <div class="row align-items-center">
            <div class="col-lg-3 col-md-4 mb-3 mb-md-0 border-end-md">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon-wrapper bg-label-secondary text-dark" style="width: 48px; height: 48px; font-size: 1.5rem;">
                        <i class="bx bx-briefcase-alt-2"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark fs-6">ระบบขออนุมัติไปราชการ</div>
                        <div class="text-muted small">บันทึก & สรุปผลรายงานราชการ</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-5 mb-3 mb-md-0">
                <div class="d-flex flex-wrap gap-4 align-items-center">
                    <div>
                        <span class="text-secondary small d-block fw-medium">รอพิจารณา/อนุมัติ:</span>
                        <span class="fw-bold fs-5" style="color: #b45309 !important;"><?= number_format($pendingTravelCount) ?> <small class="text-muted fs-6">รายการ</small></span>
                    </div>
                    <div class="border-start ps-3">
                        <span class="text-secondary small d-block fw-medium">คำขอของฉัน:</span>
                        <span class="fw-bold fs-5" style="color: #0369a1 !important;"><?= number_format($myTravelCount) ?> <small class="text-muted fs-6">รายการ</small></span>
                    </div>
                    <div class="border-start ps-3">
                        <span class="text-secondary small d-block fw-medium">คำขอทั้งระบบ:</span>
                        <span class="fw-bold fs-5 text-dark"><?= number_format($totalTravelCount) ?> <small class="text-muted fs-6">รายการ</small></span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 text-md-end">
                <a href="<?= base_url('admin/travel') ?>" class="btn btn-sm btn-outline-primary px-3 rounded-pill fw-semibold">
                    <i class="bx bx-list-ul me-1"></i> ทะเบียนไปราชการ
                </a>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- 4. Interactive Tabs & Data Tables          -->
<!-- ========================================== -->
<div class="card border-0 shadow-sm" style="border-radius: 1rem;">
    <div class="card-header bg-white pb-0 border-bottom-0 pt-3 px-4">
        <ul class="nav nav-tabs dashboard-nav-tabs" id="dashboardTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active d-flex align-items-center gap-2" id="pending-tab" data-bs-toggle="tab" data-bs-target="#tab-pending" type="button" role="tab">
                    <i class="bx bx-bell text-warning fs-5"></i> 
                    <span>รอคุณเกษียน/ลงนาม</span>
                    <?php if ($pendingCount > 0): ?>
                        <span class="badge bg-danger text-white rounded-pill px-2 fw-bold"><?= $pendingCount ?></span>
                    <?php endif; ?>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link d-flex align-items-center gap-2" id="recent-tab" data-bs-toggle="tab" data-bs-target="#tab-recent" type="button" role="tab">
                    <i class="bx bx-time-five text-primary fs-5"></i>
                    <span>หนังสือเข้าใหม่ล่าสุด</span>
                </button>
            </li>
        </ul>
    </div>

    <div class="tab-content p-0" id="dashboardTabContent">
        <!-- Tab 1: รอเกษียน/ลงนาม -->
        <div class="tab-pane fade show active" id="tab-pending" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center px-4 py-3 bg-light bg-opacity-50 border-bottom">
                <span class="text-secondary small fw-semibold">
                    <i class="bx bx-info-circle me-1 text-primary"></i> แสดงเอกสารที่ส่งถึงคุณและต้องการการพิจารณาลงนามเป็นลำดับถัดไป
                </span>
                <a href="<?= base_url('admin/documents/inbox') ?>" class="btn btn-sm btn-warning text-dark fw-bold shadow-sm">
                    ดูทั้งหมด (<?= $pendingCount ?>) <i class="bx bx-chevron-right"></i>
                </a>
            </div>

            <div class="table-responsive text-nowrap">
                <table class="table custom-dashboard-table mb-0">
                    <thead>
                        <tr>
                            <th style="width: 130px;">ความเร่งด่วน</th>
                            <th style="width: 140px;">ประเภท</th>
                            <th style="width: 160px;">เลขที่หนังสือ</th>
                            <th>เรื่อง / สาระสำคัญ</th>
                            <th style="width: 160px;">ผู้เสนอเรื่อง</th>
                            <th style="width: 130px;">วันที่รับ</th>
                            <th class="text-center" style="width: 120px;">ดำเนินการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentPending)): ?>
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state-card">
                                        <div class="avatar avatar-lg rounded-circle bg-label-success mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                                            <i class="bx bx-check-double text-success fs-2"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1">ยอดเยี่ยมมาก! ไม่มีงานคั่งค้าง</h5>
                                        <p class="text-muted small mb-3">ขณะนี้ไม่มีหนังสือราชการที่รอคุณลงนามหรือเกษียนความเห็น</p>
                                        <a href="<?= base_url('admin/documents') ?>" class="btn btn-sm btn-primary rounded-pill px-3">
                                            <i class="bx bx-archive me-1"></i> ไปที่สารบรรณกลาง
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentPending as $doc): ?>
                                <tr>
                                    <td><?= urgency_badge($doc['urgency']) ?></td>
                                    <td><?= doc_type_badge($doc['doc_type']) ?></td>
                                    <td>
                                        <span class="fw-bold text-dark font-monospace"><?= esc($doc['doc_no']) ?></span>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('admin/documents/' . $doc['id']) ?>" class="fw-semibold text-dark text-decoration-none d-block text-truncate" style="max-width: 380px;">
                                            <?= esc($doc['title']) ?>
                                        </a>
                                        <span class="text-muted small">จาก: <?= esc($doc['from_org'] ?? 'ไม่ระบุ') ?></span>
                                    </td>
                                    <td>
                                        <span class="text-dark fw-semibold small">
                                            <i class="bx bx-user me-1 text-muted"></i><?= esc(($doc['creator_first'] ?? '') . ' ' . ($doc['creator_last'] ?? '')) ?>
                                        </span>
                                    </td>
                                    <td class="text-muted small">
                                        <i class="bx bx-calendar me-1"></i><?= thai_date($doc['receive_date'] ?? $doc['doc_date'] ?? $doc['created_at'], 'short') ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= base_url('admin/documents/' . $doc['id']) ?>" class="btn btn-sm btn-warning text-dark fw-bold px-3 shadow-sm rounded-pill">
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

        <!-- Tab 2: หนังสือเข้าใหม่ล่าสุด -->
        <div class="tab-pane fade" id="tab-recent" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center px-4 py-3 bg-light bg-opacity-50 border-bottom">
                <span class="text-muted small fw-semibold">
                    <i class="bx bx-info-circle me-1"></i> ภาพรวมหนังสือราชการล่าสุดที่มีการบันทึกเข้าระบบ
                </span>
                <a href="<?= base_url('admin/documents') ?>" class="btn btn-sm btn-outline-secondary fw-semibold">
                    ดูทะเบียนทั้งหมด <i class="bx bx-chevron-right"></i>
                </a>
            </div>

            <div class="table-responsive text-nowrap">
                <table class="table custom-dashboard-table mb-0">
                    <thead>
                        <tr>
                            <th style="width: 140px;">สถานะ</th>
                            <th style="width: 130px;">ประเภท</th>
                            <th style="width: 160px;">เลขที่หนังสือ</th>
                            <th>เรื่อง</th>
                            <th>จากหน่วยงาน</th>
                            <th style="width: 130px;">ลงวันที่</th>
                            <th class="text-center" style="width: 100px;">รายละเอียด</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentAll)): ?>
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state-card">
                                        <i class="bx bx-folder-open text-muted fs-1 mb-2"></i>
                                        <p class="text-muted mb-0">ยังไม่มีประวัติเอกสารในระบบ</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentAll as $doc): ?>
                                <tr>
                                    <td><?= doc_status_badge($doc['status']) ?></td>
                                    <td><?= doc_type_badge($doc['doc_type']) ?></td>
                                    <td>
                                        <span class="fw-bold text-dark font-monospace"><?= esc($doc['doc_no']) ?></span>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('admin/documents/' . $doc['id']) ?>" class="text-dark fw-semibold text-decoration-none text-truncate d-block" style="max-width: 400px;">
                                            <?= esc($doc['title']) ?>
                                        </a>
                                    </td>
                                    <td class="text-muted small">
                                        <i class="bx bx-building me-1"></i><?= esc($doc['from_org'] ?? '-') ?>
                                    </td>
                                    <td class="text-muted small">
                                        <i class="bx bx-calendar me-1"></i><?= thai_date($doc['doc_date'] ?? $doc['created_at'], 'short') ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= base_url('admin/documents/' . $doc['id']) ?>" class="btn btn-icon btn-sm btn-outline-primary rounded-circle">
                                            <i class="bx bx-show"></i>
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

