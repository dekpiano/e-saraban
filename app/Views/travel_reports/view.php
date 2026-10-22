<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>รายงานผลการไปราชการ <?= esc($travel['doc_no']) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Page Header Banner (Sneat Theme Indigo) -->
<div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #696cff 0%, #5f61e6 50%, #4338ca 100%); border-radius: 1rem; position: relative; overflow: hidden;">
    <div class="card-body p-4 text-white">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>" class="text-white opacity-75">แดชบอร์ด</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('admin/travel-reports') ?>" class="text-white opacity-75">รายงานผลไปราชการ</a></li>
                        <li class="breadcrumb-item active text-white fw-bold">รายละเอียดรายงานผล</li>
                    </ol>
                </nav>
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <span class="badge bg-white text-primary fw-bold px-2 py-1 font-monospace shadow-xs">
                        <i class="bx bx-barcode me-1"></i><?= esc($travel['doc_no']) ?>
                    </span>
                    <span class="badge bg-success text-white fw-bold px-2 py-1 shadow-xs">
                        <i class="bx bx-check-double me-1"></i> รายงานผลแล้ว
                    </span>
                    <?php if (!empty($travel['reported_at'])): ?>
                        <span class="badge bg-dark text-white fw-bold px-2 py-1 shadow-xs border border-white border-opacity-25">
                            <i class="bx bx-time-five text-warning me-1"></i> เมื่อ <?= thai_date($travel['reported_at'], 'short', true) ?>
                        </span>
                    <?php endif; ?>
                </div>
                <h4 class="fw-bold mb-1 text-white" style="font-family: 'K2D', sans-serif;">
                    รายงานผล: <?= esc($travel['title']) ?>
                </h4>
                <?php if (!empty($isLoggedIn)): ?>
                    <div class="mt-2 mb-2"><a href="<?= base_url('admin/travel/' . (int)$travel['id'] . '/print-bundle') ?>" target="_blank" class="btn btn-sm btn-light"><i class="bx bx-printer me-1"></i>พิมพ์ชุดบันทึกข้อความและรายงานผล</a></div>
                <?php endif; ?>
                <div class="small text-white opacity-90 d-flex flex-wrap align-items-center gap-3 mt-1">
                    <span><i class="bx bx-user me-1"></i> <?= esc($travel['prefix'] . $travel['first_name'] . ' ' . $travel['last_name']) ?> (<?= esc($travel['position_name']) ?>)</span>
                    <span><i class="bx bx-map-pin me-1"></i> <?= esc($travel['destination']) ?> (<?= esc($travel['province']) ?>)</span>
                    <span><i class="bx bx-calendar me-1"></i> <?= thai_date($travel['start_date'], 'short') ?> - <?= thai_date($travel['end_date'], 'short') ?> (<?= esc($travel['total_days']) ?> วัน)</span>
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <?php if ($isLoggedIn): ?>
                    <a href="<?= base_url('admin/travel/' . $travel['id']) ?>" class="btn btn-white text-primary btn-sm fw-bold px-3 shadow-xs" style="background:#ffffff;">
                        <i class="bx bx-link-external me-1"></i> ดูคำขออนุมัติต้นเรื่อง
                    </a>
                    <?php if ($canEdit): ?>
                        <a href="<?= base_url('admin/travel-reports/' . $travel['id'] . '/edit') ?>" class="btn btn-warning text-dark btn-sm fw-bold px-3 shadow-xs">
                            <i class="bx bx-edit me-1"></i> แก้ไขรายงาน
                        </a>
                        <button type="button" class="btn btn-danger text-white btn-sm fw-bold px-3 shadow-xs" onclick="confirmDeleteReport()">
                            <i class="bx bx-trash me-1"></i> ยกเลิกรายงาน
                        </button>
                        <!-- Form ซ่อนไว้สำหรับส่ง POST ลบ/ยกเลิกรายงาน -->
                        <form id="deleteReportForm" action="<?= base_url('admin/travel-reports/' . $travel['id'] . '/delete') ?>" method="POST" class="d-none">
                            <?= csrf_field() ?>
                        </form>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="<?= base_url('login') ?>" class="btn btn-warning text-dark btn-sm fw-bold px-3 shadow-xs">
                        <i class="bx bx-log-in me-1"></i> เข้าสู่ระบบเพื่อจัดการ
                    </a>
                <?php endif; ?>
                <a href="<?= base_url('admin/travel-reports') ?>" class="btn btn-outline-light btn-sm fw-bold px-3 shadow-xs">
                    <i class="bx bx-arrow-back me-1"></i> ย้อนกลับ
                </a>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-transparent d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div><h5 class="mb-1 fw-bold"><i class="bx bx-file me-2 text-primary"></i>บันทึกข้อความที่เกี่ยวข้อง</h5><small class="text-muted">เอกสารหลักที่เชื่อมกับคำขอและรายงานผลนี้</small></div>
        <a href="<?= base_url('admin/travel/' . (int)$travel['id'] . '/print-bundle') ?>" target="_blank" class="btn btn-outline-primary btn-sm"><i class="bx bx-printer me-1"></i>พิมพ์ชุดเอกสาร</a>
    </div>
    <div class="card-body">
        <?php if (!empty($linkedMemos)): ?>
            <div class="list-group list-group-flush">
                <?php foreach ($linkedMemos as $memo): ?>
                    <a href="<?= base_url('admin/memos/' . (int)$memo['id']) ?>" class="list-group-item list-group-item-action px-0 d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div><div class="fw-semibold"><?= esc($memo['title'] ?? '-') ?></div><small class="text-muted"><?= esc($memo['doc_no'] ?? '-') ?></small></div>
                        <span class="badge bg-label-secondary"><?= esc(($memo['relation_type'] ?? '') === 'report_memo' ? 'บันทึกข้อความรายงานผล' : 'บันทึกข้อความต้นเรื่อง') ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-muted small">ไม่พบบันทึกข้อความที่เชื่อมโยงกับรายการนี้</div>
        <?php endif; ?>
    </div>
</div>

<div class="row">
    <!-- Left Column: Report Details & PDF Preview -->
    <div class="col-lg-8 mb-4">
        <!-- 1. Official Reference Card -->
        <div class="card border mb-4 shadow-sm" style="border-radius: 0.85rem;">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                    <span class="badge bg-primary text-white p-2 rounded-circle me-2">
                        <i class="bx bx-bookmark-alt fs-5"></i>
                    </span>
                    ข้อมูลอ้างอิงคำขออนุมัติไปราชการต้นเรื่อง
                </h6>
                <a href="<?= base_url('admin/travel/' . $travel['id']) ?>" target="_blank" class="btn btn-xs btn-outline-primary">
                    <i class="bx bx-search-alt me-1"></i> ดูแฟ้มต้นเรื่อง
                </a>
            </div>
            <div class="card-body p-4">
                <div class="alert alert-light border border-primary border-start-4 mb-3 shadow-xs" style="border-left: 5px solid #696cff !important; background-color: #f8faff;">
                    <div class="d-flex align-items-start gap-3">
                        <div class="flex-grow-1">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-1">
                                <span class="badge bg-primary text-white font-monospace px-2 py-1">
                                    <i class="bx bx-file me-1"></i> หนังสือขออนุมัติเลขที่: <?= esc($travel['doc_no']) ?>
                                </span>
                                <small class="text-muted">
                                    <i class="bx bx-calendar me-1"></i> ยื่นคำขอเมื่อ <?= thai_date($travel['created_at'], 'medium') ?>
                                </small>
                            </div>
                            <h5 class="fw-bold text-dark mb-2">
                                <?= esc($travel['title']) ?>
                            </h5>
                            <div class="row g-2 small text-secondary">
                                <div class="col-md-6 col-12">
                                    <i class="bx bx-user me-1 text-primary"></i> ผู้ขออนุมัติ: <strong class="text-dark"><?= esc(trim(($travel['prefix'] ?? '') . ($travel['first_name'] ?? '') . ' ' . ($travel['last_name'] ?? '')) ?: 'ไม่ระบุ') ?></strong> (<?= esc($travel['position_name'] ?? 'บุคลากร') ?>)
                                </div>
                                <div class="col-md-6 col-12">
                                    <i class="bx bx-building me-1 text-info"></i> สังกัด: <span class="text-dark"><?= esc($travel['department_name'] ?: 'สำนักงาน') ?></span>
                                </div>
                                <div class="col-md-6 col-12">
                                    <i class="bx bx-map-pin me-1 text-danger"></i> สถานที่ไปราชการ: <span class="text-dark"><?= esc($travel['destination']) ?> (<?= esc($travel['province']) ?>)</span>
                                </div>
                                <div class="col-md-6 col-12">
                                    <i class="bx bx-calendar me-1 text-success"></i> ช่วงวันเดินทาง: <span class="text-dark"><?= thai_date($travel['start_date'], 'short') ?> - <?= thai_date($travel['end_date'], 'short') ?> (<?= esc($travel['total_days']) ?> วัน)</span>
                                </div>
                                <div class="col-md-6 col-12">
                                    <i class="bx bx-wallet me-1 text-warning"></i> งบประมาณ: <span class="text-dark"><?= travel_budget_badge($travel['budget_type'], $travel['budget_amount']) ?></span>
                                </div>
                                <div class="col-md-6 col-12">
                                    <i class="bx bx-car me-1 text-primary"></i> ยานพาหนะ: <span class="text-dark"><?= travel_transport_badge($travel['transport_type']) ?></span>
                                </div>
                                <?php if (!empty($travel['companions'])): ?>
                                    <div class="col-12">
                                        <i class="bx bx-group me-1 text-info"></i> ผู้ร่วมเดินทาง: <span class="text-dark"><?= esc($travel['companions']) ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. สรุปผลการปฏิบัติราชการ -->
        <div class="card border mb-4 shadow-sm" style="border-radius: 0.85rem;">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                    <span class="badge bg-success text-white p-2 rounded-circle me-2">
                        <i class="bx bx-detail fs-5"></i>
                    </span>
                    สรุปผลการปฏิบัติราชการและความรู้/ประโยชน์ที่ได้รับ
                </h6>
                <span class="badge bg-label-success">บันทึกข้อความสรุปผล</span>
            </div>
            <div class="card-body p-4">
                <div class="p-3 bg-light rounded-3 border text-dark" style="white-space: pre-wrap; line-height: 1.85; font-size: 0.98rem; min-height: 120px;">
                    <?= nl2br(esc($travel['report_detail'] ?: 'ยังไม่มีการระบุข้อความสรุปผล')) ?>
                </div>
            </div>
        </div>

        <!-- 3. เอกสารเล่มรายงานแนบ / เกียรติบัตร (PDF) -->
        <div class="card border mb-4 shadow-sm" style="border-radius: 0.85rem;">
            <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                    <span class="badge bg-danger text-white p-2 rounded-circle me-2">
                        <i class="bx bxs-file-pdf fs-5"></i>
                    </span>
                    เอกสารเล่มรายงานสรุปผล / เกียรติบัตร (PDF)
                </h6>
                <?php if (!empty($travel['report_file'])): ?>
                    <div class="d-flex gap-2">
                        <a href="<?= document_url($travel['report_file']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="bx bx-fullscreen me-1"></i> ดูเต็มจอ
                        </a>
                        <a href="<?= document_url($travel['report_file']) ?>" download class="btn btn-sm btn-outline-secondary">
                            <i class="bx bx-download me-1"></i> ดาวน์โหลด
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            <div class="card-body p-4">
                <?php if (!empty($travel['report_file'])): ?>
                    <div class="d-flex align-items-center justify-content-between p-3 bg-white rounded border mb-3">
                        <div class="d-flex align-items-center">
                            <div class="badge bg-label-danger p-2 rounded-3 me-3">
                                <i class="bx bxs-file-pdf fs-2 text-danger"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold text-dark text-break">เอกสารเล่มรายงานสรุปผล / เกียรติบัตร</h6>
                                <small class="text-success"><i class="bx bx-check-circle me-1"></i>อัปโหลดจัดเก็บบนระบบเรียบร้อย</small>
                            </div>
                        </div>
                        <a href="<?= document_url($travel['report_file']) ?>" target="_blank" class="btn btn-sm btn-primary px-3 shadow-xs">
                            <i class="bx bx-show me-1"></i> เปิดดูเอกสาร PDF
                        </a>
                    </div>
                    <div style="height: 520px; width: 100%; background: #525659; border-radius: 6px; overflow: hidden;">
                        <iframe src="<?= document_url($travel['report_file']) ?>#toolbar=0" style="width: 100%; height: 100%; border: none;"></iframe>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <div class="avatar avatar-xl bg-label-secondary mx-auto mb-3" style="width: 60px; height: 60px; display:flex; align-items:center; justify-content:center; border-radius: 50%;">
                            <i class="bx bx-file-blank fs-2 text-secondary"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">ยังไม่มีการแนบไฟล์เล่มรายงาน (PDF)</h6>
                        <p class="text-muted small mb-3">ท่านสามารถคลิกปุ่มแก้ไขรายงานเพื่อลากวางแนบไฟล์เล่มรายงานหรือเกียรติบัตรได้</p>
                        <?php if ($canEdit): ?>
                            <a href="<?= base_url('admin/travel-reports/' . $travel['id'] . '/edit') ?>" class="btn btn-sm btn-outline-primary">
                                <i class="bx bx-upload me-1"></i> แนบไฟล์เล่มรายงานตอนนี้
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Right Column: Sidebar & Timeline -->
    <div class="col-lg-4 mb-4">
        <!-- Summary Box -->
        <div class="card border mb-4 shadow-sm" style="border-radius: 0.85rem;">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="card-title mb-0 fw-bold text-dark">
                    <i class="bx bx-check-shield text-primary me-2"></i> สรุปสถานะรายงาน
                </h6>
            </div>
            <div class="card-body p-3">
                <div class="p-3 bg-light rounded-3 mb-3 border">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small">สถานะรายงาน:</span>
                        <span class="badge bg-success text-white py-1 px-2.5">
                            <i class="bx bx-check-double me-1"></i> รายงานผลแล้ว
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small">วันที่รายงาน:</span>
                        <strong class="text-dark small"><?= !empty($travel['reported_at']) ? thai_date($travel['reported_at'], 'medium', true) : '-' ?></strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small">ผู้ส่งรายงาน:</span>
                        <span class="text-dark small fw-semibold"><?= esc(trim(($travel['prefix'] ?? '') . ($travel['first_name'] ?? '') . ' ' . ($travel['last_name'] ?? '')) ?: 'ไม่ระบุ') ?></span>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <?php if ($canEdit): ?>
                        <a href="<?= base_url('admin/travel-reports/' . $travel['id'] . '/edit') ?>" class="btn btn-warning text-dark fw-bold shadow-xs">
                            <i class="bx bx-edit-alt me-1"></i> แก้ไขข้อมูลรายงาน
                        </a>
                    <?php endif; ?>
                    <?php if ($isLoggedIn): ?>
                        <a href="<?= base_url('admin/travel/' . $travel['id']) ?>" class="btn btn-outline-primary">
                            <i class="bx bx-file-blank me-1"></i> ดูแฟ้มขออนุมัติไปราชการ
                        </a>
                    <?php else: ?>
                        <a href="<?= base_url('login') ?>" class="btn btn-outline-primary">
                            <i class="bx bx-log-in me-1"></i> เข้าสู่ระบบเพื่อจัดการ
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if ($isLoggedIn): ?>
        <!-- Audit Trail Card (เฉพาะสมาชิกที่ล็อกอินแล้วเท่านั้น) -->
        <div class="card border shadow-sm" style="border-radius: 0.85rem;">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="card-title mb-0 fw-bold text-dark">
                    <i class="bx bx-history text-secondary me-2"></i> ประวัติการดำเนินการ (Audit Logs)
                </h6>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush small">
                    <?php if (empty($logs)): ?>
                        <li class="list-group-item text-center text-muted py-3">ยังไม่มีบันทึกประวัติ</li>
                    <?php else: ?>
                        <?php foreach ($logs as $log): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-start py-2.5">
                                <div>
                                    <span class="badge bg-label-info me-1"><?= esc($log['action']) ?></span>
                                    <?php 
                                        $loggerName = trim(($log['prefix'] ?? '') . ($log['first_name'] ?? '') . ' ' . ($log['last_name'] ?? ''));
                                        if (empty($loggerName)) {
                                            $loggerName = 'ผู้ใช้งาน ID: ' . ($log['user_id'] ?? '-');
                                        }
                                    ?>
                                    <strong class="text-dark"><?= esc($loggerName) ?>:</strong>
                                    <div class="text-secondary small mt-0.5"><?= esc($log['detail']) ?></div>
                                </div>
                                <span class="text-muted text-nowrap ms-2" style="font-size: 0.72rem;"><?= thai_date($log['created_at'], 'short', true) ?></span>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function confirmDeleteReport() {
    Swal.fire({
        title: 'ยืนยันการยกเลิกรายงานผล?',
        text: 'การยกเลิกจะลบข้อมูลสรุปและไฟล์เล่มรายงานออก และปรับสถานะคำขอกลับเป็น "รอส่งรายงานผล"',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ff3e1d',
        cancelButtonColor: '#8592a3',
        confirmButtonText: '<i class="bx bx-trash me-1"></i> ยืนยันการยกเลิก',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('deleteReportForm').submit();
        }
    });
}
</script>
<?= $this->endSection() ?>
