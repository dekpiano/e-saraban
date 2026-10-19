<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>ระบบรายงานผลการไปราชการ<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
    $isMyOnly = !empty($isMyOnly);
?>
<!-- Page Header Banner (Sneat Theme Indigo) -->
<div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #696cff 0%, #5f61e6 50%, #4338ca 100%); border-radius: 1rem; position: relative; overflow: hidden;">
    <div class="card-body p-4 text-white">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>" class="text-white opacity-75">แดชบอร์ด</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('admin/travel') ?>" class="text-white opacity-75">ทะเบียนไปราชการ</a></li>
                        <li class="breadcrumb-item active text-white fw-bold">รายงานผลการไปราชการ</li>
                    </ol>
                </nav>
                <h4 class="fw-bold mb-1 text-white" style="font-family: 'K2D', sans-serif;">
                    <i class="bx bxs-report me-2"></i><?= $isLoggedIn ? ($showMyOnly ? 'รายงานผลการไปราชการของฉัน' : 'ระบบรายงานผลการไปราชการทั้งหมด') : 'คลังรายงานผลการไปราชการ (อ่านอย่างเดียว)' ?>
                </h4>
                <p class="mb-0 text-white opacity-90 small">
                    <?= $isLoggedIn ? 'สรุปผลการปฏิบัติราชการ อ้างอิงจากหนังสือขออนุมัติเดินทาง พร้อมแนบไฟล์เล่มรายงานและเกียรติบัตร (PDF)' : 'เอกสารสรุปผลการปฏิบัติงานและองค์ความรู้จากการไปราชการของบุคลากรโรงเรียน' ?>
                </p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <?php if ($isLoggedIn): ?>
                    <a href="<?= base_url('admin/travel-reports/create') ?>" class="btn btn-warning text-dark fw-bold px-3 shadow-sm">
                        <i class="bx bx-plus me-1"></i> เขียนรายงานผลใหม่
                    </a>
                    <?php if ($isOfficer): ?>
                        <a href="<?= base_url('admin/travel-reports?scope=' . ($showMyOnly ? 'all' : 'my')) ?>" class="btn btn-outline-light fw-bold px-3 shadow-xs">
                            <i class="bx <?= $showMyOnly ? 'bx-list-ul' : 'bx-user' ?> me-1"></i> <?= $showMyOnly ? 'ดูของทุกคน' : 'เฉพาะของฉัน' ?>
                        </a>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="<?= base_url('login') ?>" class="btn btn-warning text-dark fw-bold px-3 shadow-sm">
                        <i class="bx bx-log-in me-1"></i> เข้าสู่ระบบเพื่อจัดการ
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if (!$isLoggedIn): ?>
    <div class="alert alert-info py-2.5 px-3 small mb-4 d-flex align-items-center justify-content-between border-0 shadow-sm" style="background: #e7f7ff; color: #005b82; border-left: 4px solid #03c3ec !important; border-radius: 0.75rem;">
        <div>
            <i class="bx bx-info-circle fs-5 me-1 align-middle"></i>
            <strong>โหมดผู้เยี่ยมชม (อ่านอย่างเดียว):</strong> แสดงเฉพาะรายการที่รายงานผลแล้วเพื่อการเผยแพร่องค์ความรู้ ท่านสามารถเปิดอ่านรายงานผลหรือเปิดดูไฟล์ PDF ได้ แต่จะไม่สามารถเขียนหรือแก้ไขรายงานได้
        </div>
        <a href="<?= base_url('login') ?>" class="btn btn-primary btn-sm py-1 px-3 fw-bold ms-2 flex-shrink-0">
            <i class="bx bx-log-in me-1"></i> เข้าสู่ระบบ
        </a>
    </div>
<?php endif; ?>

<!-- Stats Counter Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 0.85rem; border-left: 4px solid #696cff !important;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small d-block">อนุมัติแล้วทั้งหมด</span>
                        <h3 class="fw-bold text-dark mb-0 mt-1"><?= number_format($totalApproved) ?></h3>
                        <small class="text-primary font-monospace">รายการที่ต้องรายงาน</small>
                    </div>
                    <span class="badge bg-label-primary p-3 rounded-circle">
                        <i class="bx bx-briefcase-alt fs-3 text-primary"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 0.85rem; border-left: 4px solid #71dd37 !important;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small d-block">ส่งรายงานผลแล้ว</span>
                        <h3 class="fw-bold text-success mb-0 mt-1"><?= number_format($totalReported) ?></h3>
                        <small class="text-success fw-semibold"><i class="bx bx-check-double me-1"></i>เสร็จสมบูรณ์</small>
                    </div>
                    <span class="badge bg-label-success p-3 rounded-circle">
                        <i class="bx bx-check-circle fs-3 text-success"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 0.85rem; border-left: 4px solid #ffab00 !important;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small d-block">รอส่งรายงานผล</span>
                        <h3 class="fw-bold text-warning mb-0 mt-1"><?= number_format($totalPending) ?></h3>
                        <small class="text-warning fw-semibold"><i class="bx bx-time me-1"></i>ยังไม่ส่งรายงาน</small>
                    </div>
                    <span class="badge bg-label-warning p-3 rounded-circle">
                        <i class="bx bx-time-five fs-3 text-warning"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 0.85rem; border-left: 4px solid #03c3ec !important;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small d-block">ร้อยละการรายงานผล</span>
                        <h3 class="fw-bold text-info mb-0 mt-1"><?= $percentReported ?>%</h3>
                        <small class="text-info fw-semibold">เทียบกับคำขอที่อนุมัติ</small>
                    </div>
                    <span class="badge bg-label-info p-3 rounded-circle">
                        <i class="bx bx-pie-chart-alt fs-3 text-info"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Data Card -->
<div class="card border shadow-sm" style="border-radius: 0.85rem;">
    <!-- Filter Tabs & Search Bar -->
    <div class="card-header bg-white py-3 border-bottom">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <ul class="nav nav-pills flex-nowrap overflow-auto py-1" style="max-width: 100%;">
                <?php
                    $baseRoute = $isMyOnly ? 'admin/travel-reports/my' : 'admin/travel-reports';
                    $searchParam = !empty($search) ? '&search=' . urlencode($search) : '';
                ?>
                <li class="nav-item">
                    <a class="nav-link <?= ($currentTab === 'all') ? 'active' : '' ?> fw-semibold" href="<?= base_url($baseRoute . '?tab=all' . $searchParam) ?>">
                        <i class="bx bx-list-ul me-1"></i> ทั้งหมด (<?= $totalApproved ?>)
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($currentTab === 'reported') ? 'active' : '' ?> fw-semibold" href="<?= base_url($baseRoute . '?tab=reported' . $searchParam) ?>">
                        <i class="bx bx-check-circle me-1"></i> รายงานผลแล้ว (<?= $totalReported ?>)
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($currentTab === 'pending_report') ? 'active' : '' ?> fw-semibold" href="<?= base_url($baseRoute . '?tab=pending_report' . $searchParam) ?>">
                        <i class="bx bx-time me-1"></i> รอส่งรายงานผล (<?= $totalPending ?>)
                    </a>
                </li>
            </ul>

            <form action="<?= base_url($baseRoute) ?>" method="GET" class="d-flex align-items-center gap-2">
                <input type="hidden" name="tab" value="<?= esc($currentTab) ?>" />
                <div class="input-group input-group-merge input-group-sm" style="min-width: 260px;">
                    <span class="input-group-text"><i class="bx bx-search"></i></span>
                    <input type="text" class="form-control" name="search" value="<?= esc($search) ?>" placeholder="ค้นหาเลขที่, เรื่อง, ผู้ขอ..." />
                    <?php if (!empty($search)): ?>
                        <a href="<?= base_url($baseRoute . '?tab=' . $currentTab) ?>" class="input-group-text text-danger" title="ล้างการค้นหา">
                            <i class="bx bx-x"></i>
                        </a>
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary btn-sm px-3">ค้นหา</button>
            </form>
        </div>
    </div>

    <!-- Table List -->
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="width: 100%;">
            <thead class="table-light">
                <tr class="text-dark small fw-bold" style="border-bottom: 2px solid #cbd5e1;">
                    <th style="width: 16%;">เลขที่คำขอต้นเรื่อง</th>
                    <th style="width: 30%;">เรื่องและสถานที่ไปราชการ</th>
                    <th style="width: 16%;">ผู้ไปราชการ / สังกัด</th>
                    <th style="width: 14%;">ช่วงวันที่เดินทาง</th>
                    <th style="width: 12%;" class="text-center">สถานะรายงาน</th>
                    <th style="width: 12%;" class="text-center">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($reports)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <div class="avatar avatar-xl bg-label-secondary mx-auto mb-3" style="width: 60px; height: 60px; display:flex; align-items:center; justify-content:center; border-radius: 50%;">
                                <i class="bx bx-file-blank fs-2 text-secondary"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">ไม่พบข้อมูลรายงานผลการไปราชการ</h6>
                            <p class="small text-muted mb-3">
                                <?php if (!empty($search)): ?>
                                    ไม่พบรายการที่ตรงกับคำค้นหา "<strong><?= esc($search) ?></strong>"
                                <?php else: ?>
                                    ยังไม่มีรายการคำขอไปราชการในหมวดนี้
                                <?php endif; ?>
                            </p>
                            <a href="<?= base_url('admin/travel-reports/create') ?>" class="btn btn-primary btn-sm">
                                <i class="bx bx-plus me-1"></i> เขียนรายงานผลการไปราชการ
                            </a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($reports as $item): ?>
                        <?php
                            $isReported = ($item['status'] === 'reported');
                            $hasFile    = !empty($item['report_file']);
                        ?>
                        <tr>
                            <!-- 1. เลขที่ขอ -->
                            <td>
                                <span class="badge bg-light text-primary font-monospace fw-bold border mb-1" style="font-size: 0.8rem;">
                                    <?= esc($item['doc_no']) ?>
                                </span>
                                <div class="small text-muted" style="font-size: 0.75rem;">
                                    <i class="bx bx-time me-1"></i>ยื่นคำขอ <?= thai_date($item['created_at'], 'short') ?>
                                </div>
                            </td>

                            <!-- 2. เรื่องและสถานที่ -->
                            <td>
                                <a href="<?= base_url('admin/travel-reports/' . $item['id']) ?>" class="fw-bold text-dark text-decoration-none d-block text-break" style="font-size: 0.92rem; line-height: 1.35;">
                                    <?= esc($item['title']) ?>
                                </a>
                                <div class="small text-muted mt-1">
                                    <i class="bx bx-map-pin text-danger me-1"></i><?= esc($item['destination']) ?> (<?= esc($item['province']) ?>)
                                </div>
                                <?php if ($isReported && !empty($item['report_detail'])): ?>
                                    <div class="small text-secondary mt-1 text-truncate" style="max-width: 380px;">
                                        <i class="bx bx-detail text-primary me-1"></i><?= esc(mb_substr($item['report_detail'], 0, 70)) ?>...
                                    </div>
                                <?php endif; ?>
                            </td>

                            <!-- 3. ผู้ไปราชการ -->
                            <td>
                                <div class="fw-semibold text-dark text-break" style="font-size: 0.88rem;">
                                    <?= esc($item['prefix'] . $item['first_name'] . ' ' . $item['last_name']) ?>
                                </div>
                                <div class="small text-muted" style="font-size: 0.78rem;">
                                    <?= esc($item['department_name'] ?: 'สำนักงาน') ?>
                                </div>
                            </td>

                            <!-- 4. ช่วงวันที่เดินทาง -->
                            <td>
                                <div class="small fw-semibold text-dark">
                                    <?= thai_date($item['start_date'], 'short') ?> - <?= thai_date($item['end_date'], 'short') ?>
                                </div>
                                <div class="small text-muted" style="font-size: 0.75rem;">
                                    (รวม <?= esc($item['total_days']) ?> วัน)
                                </div>
                            </td>

                            <!-- 5. สถานะรายงาน -->
                            <td class="text-center">
                                <?php if ($isReported): ?>
                                    <span class="badge bg-success text-white py-1 px-2.5 shadow-xs" style="font-size: 0.78rem;">
                                        <i class="bx bx-check-circle me-1"></i> รายงานผลแล้ว
                                    </span>
                                    <?php if (!empty($item['reported_at'])): ?>
                                        <div class="small text-muted mt-1" style="font-size: 0.7rem;">
                                            <?= thai_date($item['reported_at'], 'short') ?>
                                        </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark py-1 px-2.5 shadow-xs" style="font-size: 0.78rem;">
                                        <i class="bx bx-time-five me-1"></i> รอส่งรายงาน
                                    </span>
                                <?php endif; ?>
                            </td>

                            <!-- 6. จัดการ -->
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <?php if ($isReported): ?>
                                        <a href="<?= base_url('admin/travel-reports/' . $item['id']) ?>" class="btn btn-outline-primary" title="ดูรายละเอียดรายงานผล">
                                            <i class="bx bx-show me-1"></i> ดูรายงาน
                                        </a>
                                        <?php if ($hasFile): ?>
                                            <a href="<?= document_url($item['report_file']) ?>" target="_blank" class="btn btn-outline-danger" title="เปิดไฟล์เล่มรายงาน (PDF)">
                                                <i class="bx bxs-file-pdf"></i>
                                            </a>
                                        <?php endif; ?>
                                    <?php elseif ($isLoggedIn): ?>
                                        <a href="<?= base_url('admin/travel-reports/create?travel_id=' . $item['id']) ?>" class="btn btn-success fw-bold" title="เขียนรายงานผล">
                                            <i class="bx bx-edit me-1"></i> เขียนรายงาน
                                        </a>
                                    <?php else: ?>
                                        <span class="badge bg-light text-muted border py-1 px-2">รอรายงานผล</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
