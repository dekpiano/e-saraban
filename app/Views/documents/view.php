<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>หนังสือเลขที่ <?= esc($document['doc_no'] ?: ($document['internal_reg_no'] ?? 'รอดำเนินการ')) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/saraban-memo.css?v=' . (file_exists(FCPATH . 'assets/css/saraban-memo.css') ? filemtime(FCPATH . 'assets/css/saraban-memo.css') : '2.2')) ?>">
<style>
/* ==========================================================================
   Executive Document Endorsement & Reader UX Styles
   Universal Thai Official Document Standards + Modern Desk Aesthetic
   ========================================================================== */

/* 1. Canvas Backdrop (Modern Desk Tone) */
.doc-desk-canvas {
    background-color: #f1f5f9;
    background-image: radial-gradient(#cbd5e1 1px, transparent 1px);
    background-size: 20px 20px;
    padding: 2rem 1rem;
    border-radius: 0.75rem;
    overflow-x: auto;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    min-height: 800px;
    transition: all 0.2s ease;
}

.doc-paper-scale-wrap {
    transform-origin: top center;
    transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}

/* 2. Official Saraban A4 Memo Paper (Strict Physical A4 Standard: 21cm x 29.7cm) */
.saraban-paper-display {
    background-color: #ffffff;
    width: 21cm;
    height: 29.7cm;
    min-height: 29.7cm;
    max-height: 29.7cm;
    padding: 1.5cm 2cm 2cm 3cm;
    box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.15), 0 8px 10px -6px rgba(15, 23, 42, 0.1);
    font-family: 'TH SarabunIT๙', 'TH Sarabun New', 'Sarabun', sans-serif;
    font-size: 16pt;
    line-height: 1.15;
    color: #000000;
    box-sizing: border-box;
    position: relative;
    text-align: justify;
    text-justify: inter-character;
    border-radius: 2px;
    overflow: hidden; /* ล็อกขนาด A4 พอดี ๑ แผ่น ไม่ให้ยืดล้น */
}

.saraban-paper-display .header {
    position: relative;
    margin-bottom: 0;
    height: 1.5cm;
    display: flex;
    align-items: flex-end;
    justify-content: center;
}

.saraban-paper-display .garuda {
    position: absolute;
    left: 0;
    top: 0;
    width: 1.37cm;
    height: 1.5cm;
    object-fit: contain;
}

.saraban-paper-display .document-title {
    font-size: 29pt;
    font-weight: bold;
    text-align: center;
    line-height: 35pt;
    padding-top: 10px;
    width: 100%;
    color: #000000;
}

.saraban-paper-display .form-row {
    display: flex;
    align-items: baseline;
    margin-top: 0;
    margin-bottom: 4px;
}

.saraban-paper-display .label {
    font-size: 20pt;
    font-weight: bold;
    color: #000000;
    margin-right: 8px;
    white-space: nowrap;
}

.saraban-paper-display .editable-field {
    flex: 1;
    font-size: 16pt;
    line-height: 1.1;
    min-height: 20px;
    padding: 0 4px 0px 4px;
    border-bottom: 1px dotted #000000;
    font-weight: normal;
    color: #000000;
}

.saraban-paper-display .saraban-grid-2,
.saraban-paper-display .col-2 {
    display: flex !important;
    width: 100% !important;
    max-width: 100% !important;
    flex: 1 1 100% !important;
    gap: 20px;
    margin-bottom: 4px;
}

.saraban-paper-display .saraban-grid-2 > div,
.saraban-paper-display .col-2 > div {
    display: flex;
    align-items: baseline;
}

.saraban-paper-display .col-left {
    flex: 1;
    display: flex;
    align-items: baseline;
}

.saraban-paper-display .col-right {
    flex: 1;
    display: flex;
    align-items: baseline;
}

.saraban-paper-display .salutation {
    margin-top: 6pt;
    margin-bottom: 6pt;
    display: flex;
    align-items: baseline;
}

.saraban-paper-display .salutation .label {
    font-size: 16pt;
    font-weight: normal;
    margin-right: 8px;
    width: auto;
    color: #000000;
}

.saraban-paper-display .body-text {
    font-size: 16pt;
    line-height: 1.15;
    margin-top: 8pt;
    text-align: justify;
    text-justify: inter-character;
    word-break: normal;
    color: #000000;
}

.saraban-paper-display .body-text p {
    text-indent: 2.5cm;
    margin-bottom: 10pt;
    line-height: 1.15;
}

.saraban-paper-display .signature-section {
    margin-top: 40pt;
    padding-left: 7.5cm;
    page-break-inside: avoid;
}

.saraban-paper-display .signature-box {
    width: fit-content;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.saraban-paper-display .signature-line {
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 4px;
}

.saraban-paper-display .signature-img {
    max-height: 48px;
    max-width: 160px;
    object-fit: contain;
}

.saraban-paper-display .sign-name {
    font-size: 16pt;
    font-weight: normal;
    white-space: nowrap;
    color: #000000;
}

.saraban-paper-display .sign-position {
    font-size: 16pt;
    font-weight: normal;
    white-space: nowrap;
    color: #000000;
}

.saraban-paper-display .memo-endorse-box {
    border: 1px solid #cbd5e1;
    background-color: #f8fafc;
    border-radius: 8px;
    padding: 12px 16px;
    margin-bottom: 12px;
}

/* 3. Official School Order Paper */
.order-paper-display {
    background-color: #ffffff;
    width: 21cm;
    min-height: 29.7cm;
    padding: 1.5cm 2cm 2cm 3cm;
    box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.15), 0 8px 10px -6px rgba(15, 23, 42, 0.1);
    font-family: 'TH SarabunIT๙', 'TH Sarabun New', 'Sarabun', sans-serif;
    font-size: 16pt;
    line-height: 1.25;
    color: #000000;
    box-sizing: border-box;
    text-align: justify;
    text-justify: inter-character;
    border-radius: 2px;
}

.order-garuda-center {
    text-align: center;
    margin-bottom: 0.5cm;
}

.order-garuda-img {
    width: 2.73cm;
    height: 3.0cm;
    object-fit: contain;
}

.order-school-title {
    font-size: 20pt;
    font-weight: bold;
    text-align: center;
    color: #000000;
}

.order-number-title {
    font-size: 18pt;
    font-weight: bold;
    text-align: center;
    margin-bottom: 4px;
    color: #000000;
}

.order-subject-title {
    font-size: 18pt;
    font-weight: bold;
    text-align: center;
    margin-bottom: 12px;
    color: #000000;
}

.order-divider-line {
    border-bottom: 1px solid #000000;
    margin-bottom: 16px;
}

.order-body-content {
    font-size: 16pt;
    line-height: 1.25;
    color: #000000;
}

.order-body-content p {
    text-indent: 2.5cm;
    margin-bottom: 10pt;
    line-height: 1.25;
}

.order-effective-clause {
    text-indent: 2.5cm;
    margin-top: 14pt;
    margin-bottom: 14pt;
    color: #000000;
}

.order-date-clause {
    text-align: center;
    margin-top: 16pt;
    margin-bottom: 30pt;
    color: #000000;
}

.order-signature-container {
    margin-left: auto;
    margin-right: auto;
    width: fit-content;
    max-width: 15cm;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.order-signature-img {
    max-height: 52px;
    object-fit: contain;
    margin-bottom: 4px;
}

/* 4. Compact Stepper Bar */
.compact-stepper-bar {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    overflow-x: auto;
    padding: 0.5rem 0.25rem;
}

.compact-step-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.4rem 0.75rem;
    border-radius: 9999px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    white-space: nowrap;
    font-size: 0.825rem;
}

.compact-step-item.is-done {
    background: #ecfdf5;
    border-color: #a7f3d0;
    color: #065f46;
}

.compact-step-item.is-active {
    background: #eef2ff;
    border-color: #c7d2fe;
    color: #3730a3;
    font-weight: 600;
    box-shadow: 0 1px 3px rgba(79, 70, 229, 0.15);
}

.compact-step-divider {
    color: #94a3b8;
    font-size: 0.75rem;
}

/* 5. Executive Endorsement Panel (Compact & Sleek) */
.executive-desk-card {
    border: none;
    border-radius: 0.85rem;
    overflow: hidden;
    box-shadow: 0 8px 20px -4px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.04);
}

.executive-desk-header {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    color: #ffffff;
    padding: 0.75rem 1rem;
}

.quick-preset-chip {
    padding: 0.25rem 0.6rem;
    font-size: 0.75rem;
    border-radius: 9999px;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    color: #334155;
    cursor: pointer;
    transition: all 0.15s ease;
    font-weight: 500;
    line-height: 1.2;
}

.quick-preset-chip:hover {
    background: #f1f5f9;
    border-color: #94a3b8;
}

.quick-preset-chip.active-preset {
    background: #4f46e5;
    color: #ffffff;
    border-color: #4338ca;
    box-shadow: 0 1px 3px rgba(79, 70, 229, 0.25);
}

.signature-compact-box {
    border: 1px solid #e2e8f0;
    background: #f8fafc;
    border-radius: 0.5rem;
    padding: 0.5rem 0.75rem;
    transition: all 0.2s;
}

.signature-compact-box:hover {
    border-color: #cbd5e1;
    background: #f1f5f9;
}

/* 6. Information Tabs Dock */
.info-dock-card {
    border: none;
    border-radius: 0.75rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
}

.info-dock-tabs .nav-link {
    font-size: 0.875rem;
    font-weight: 600;
    color: #64748b;
    border: none;
    border-bottom: 2px solid transparent;
    padding: 0.75rem 1.25rem;
    border-radius: 0;
}

.info-dock-tabs .nav-link.active {
    color: #4f46e5;
    border-bottom-color: #4f46e5;
    background: transparent;
}

/* ==========================================================================
   Print Rules: Strict 1 Physical A4 Page (21cm x 29.7cm)
   ========================================================================== */
@media print {
    @page {
        size: A4 portrait;
        margin: 0;
    }
    html, body {
        background: #ffffff !important;
        margin: 0 !important;
        padding: 0 !important;
        width: 21cm !important;
        height: 29.7cm !important;
    }
    .layout-navbar,
    .layout-menu,
    .menu,
    .navbar,
    .footer,
    .no-print,
    .btn,
    .btn-group,
    .nav,
    .nav-pills,
    .breadcrumb,
    .alert,
    .col-12.col-xl-4,
    .col-lg-5,
    .executive-desk-card,
    .info-dock-card,
    .card-header,
    .compact-stepper-bar,
    hr {
        display: none !important;
    }
    .layout-wrapper,
    .layout-container,
    .layout-page,
    .content-wrapper,
    .container-xxl,
    .row,
    .col-12.col-xl-8,
    .col-lg-7,
    .card,
    .card-body,
    .tab-content,
    .tab-pane,
    #docDeskCanvas,
    #docPaperScaleWrap {
        margin: 0 !important;
        padding: 0 !important;
        border: none !important;
        box-shadow: none !important;
        background: transparent !important;
        width: 21cm !important;
        height: 29.7cm !important;
        max-height: 29.7cm !important;
        display: block !important;
        transform: none !important;
    }
    .saraban-paper-display,
    .order-paper-display {
        width: 21cm !important;
        height: 29.7cm !important;
        min-height: 29.7cm !important;
        max-height: 29.7cm !important;
        padding: 1.5cm 2cm 2cm 3cm !important;
        margin: 0 !important;
        box-shadow: none !important;
        border: none !important;
        box-sizing: border-box !important;
        overflow: hidden !important;
        page-break-after: avoid !important;
        break-after: avoid !important;
        page-break-inside: avoid !important;
        break-inside: avoid !important;
        position: relative !important;
    }
}
</style>

<!-- ==========================================================================
     Top Header & Action Toolbar
     ========================================================================== -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3 p-md-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <!-- Left: Document Context -->
            <div class="d-flex align-items-center gap-3">
                <a href="<?= base_url('admin/documents') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3" title="กลับไปรายการหนังสือ">
                    <i class="bx bx-arrow-back me-1"></i> รายการหนังสือ
                </a>
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                        <?= doc_type_badge($document['doc_type']) ?>
                        <span class="badge bg-label-info fw-semibold">
                            เลขที่ <?= esc($document['doc_no'] ?: ($document['internal_reg_no'] ?? '-')) ?>
                        </span>
                        <?php if ($document['status'] === 'routing'): ?>
                            <span class="badge bg-warning text-dark fw-bold">
                                <i class="bx bx-time-five me-1"></i> อยู่ระหว่างเสนอเกษียน
                            </span>
                        <?php elseif ($document['status'] === 'completed'): ?>
                            <span class="badge bg-success text-white fw-bold">
                                <i class="bx bx-check-double me-1"></i> เกษียนลงนามครบถ้วน
                            </span>
                        <?php elseif ($document['status'] === 'returned'): ?>
                            <span class="badge bg-danger text-white fw-bold">
                                <i class="bx bx-undo me-1"></i> ส่งคืนแก้ไข
                            </span>
                        <?php endif; ?>
                    </div>
                    <h5 class="fw-bold mb-0 text-dark">
                        <?= esc($document['title']) ?>
                    </h5>
                    <small class="text-muted">
                        วันที่ <?= thai_date($document['doc_date'], 'medium') ?> • 
                        ผู้เสนอ: <?= esc(($creator['prefix'] ?? $document['prefix'] ?? '') . ($creator['first_name'] ?? $document['first_name'] ?? '') . ' ' . ($creator['last_name'] ?? $document['last_name'] ?? '')) ?>
                        (<?= esc($creator['department_name'] ?? $document['creator_department'] ?? '-') ?>)
                    </small>
                </div>
            </div>

            <!-- Right: Quick Actions -->
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <?php if ($document['doc_type'] === 'internal'): ?>
                    <a href="<?= base_url('admin/memos/' . $document['id'] . '/print') ?>" target="_blank" class="btn btn-primary btn-sm fw-semibold shadow-xs">
                        <i class="bx bx-printer me-1"></i> พิมพ์ A4
                    </a>
                    <a href="<?= base_url('admin/memos/' . $document['id']) ?>" class="btn btn-outline-primary btn-sm">
                        <i class="bx bx-link-external me-1"></i> หน้าบันทึกข้อความ
                    </a>
                <?php elseif ($document['doc_type'] === 'order'): ?>
                    <a href="<?= base_url('admin/orders/' . $document['id'] . '/print') ?>" target="_blank" class="btn btn-primary btn-sm fw-semibold shadow-xs">
                        <i class="bx bx-printer me-1"></i> พิมพ์คำสั่ง A4
                    </a>
                    <a href="<?= base_url('admin/orders/' . $document['id']) ?>" class="btn btn-outline-primary btn-sm">
                        <i class="bx bx-link-external me-1"></i> หน้าคำสั่ง
                    </a>
                <?php endif; ?>

                <?php if (!empty($document['file_path'])): ?>
                    <a href="<?= document_url($document['file_path']) ?>" target="_blank" class="btn btn-outline-danger btn-sm">
                        <i class="bx bxs-file-pdf me-1"></i> ดู PDF
                    </a>
                <?php endif; ?>

                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#uploadDocModal">
                    <i class="bx bx-upload me-1"></i> <?= empty($document['file_path']) ? 'แนบ PDF' : 'เปลี่ยนไฟล์' ?>
                </button>
            </div>
        </div>

        <!-- Compact Workflow Stepper -->
        <hr class="my-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-label-secondary text-dark fw-bold px-2 py-1 small">
                    <i class="bx bx-git-commit me-1 text-primary"></i> ลำดับเสนอเซ็น:
                </span>
                <div class="compact-stepper-bar">
                    <?php 
                    $totalRoutes = count($routes);
                    foreach ($routes as $idx => $r): 
                        $isDone    = $r['status'] === 'signed';
                        $isCurrent = ($r['step_order'] == $document['current_step'] && $document['status'] === 'routing');
                        $isReturn  = $r['status'] === 'returned';
                        $cls = $isDone ? 'is-done' : ($isCurrent ? 'is-active' : '');
                    ?>
                        <div class="compact-step-item <?= $cls ?>" title="<?= esc($r['position_name']) ?>">
                            <?php if ($isDone): ?>
                                <i class="bx bx-check-circle text-success fs-5"></i>
                            <?php elseif ($isReturn): ?>
                                <i class="bx bx-x-circle text-danger fs-5"></i>
                            <?php elseif ($isCurrent): ?>
                                <i class="bx bx-edit text-primary fs-5"></i>
                            <?php else: ?>
                                <span class="badge bg-secondary rounded-circle" style="width: 18px; height: 18px; padding: 0; display: inline-flex; align-items: center; justify-content: center; font-size: 0.65rem;"><?= $r['step_order'] ?></span>
                            <?php endif; ?>
                            <span>
                                <strong>ขั้นที่ <?= $r['step_order'] ?>:</strong>
                                <?= esc($r['first_name'] . ' ' . $r['last_name']) ?>
                                <?php if ($isCurrent): ?>
                                    <span class="badge bg-primary text-white ms-1" style="font-size: 0.68rem;">กำลังพิจารณา</span>
                                <?php elseif ($isDone): ?>
                                    <span class="badge bg-success text-white ms-1" style="font-size: 0.68rem;">ลงนามแล้ว</span>
                                <?php endif; ?>
                            </span>
                        </div>
                        <?php if ($idx < $totalRoutes - 1): ?>
                            <i class="bx bx-chevron-right compact-step-divider"></i>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Zoom Controls for Reader -->
            <div class="d-flex align-items-center gap-1 ms-auto">
                <small class="text-muted me-1 fw-semibold"><i class="bx bx-search-alt me-1"></i> ย่อ-ขยาย:</small>
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-outline-secondary btn-sm py-1 px-2" onclick="setDocZoom(0.75)">75%</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm py-1 px-2" onclick="setDocZoom(0.85)">85%</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm py-1 px-2 active" id="btnZoom100" onclick="setDocZoom(1.0)">100%</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm py-1 px-2" onclick="setDocZoom(1.10)">110%</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm py-1 px-2" title="พอดีหน้าจอ" onclick="fitDocToWidth()"><i class="bx bx-fullscreen"></i></button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ==========================================================================
     Main Content: Left Reader (8 cols) + Right Endorsement Desk (4 cols)
     ========================================================================== -->
<div class="row">
    <!-- Left Column: Document Reader & Info Dock -->
    <div class="col-12 col-xl-8 col-lg-7 mb-4">

        <!-- Reader Card Toolbar -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white py-2.5 px-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-label-primary p-2 rounded-3">
                        <i class="bx bx-file-blank fs-4"></i>
                    </span>
                    <div>
                        <h6 class="mb-0 fw-bold text-dark">
                            <?php if ($document['doc_type'] === 'internal'): ?>
                                ต้นฉบับบันทึกข้อความ (ขนาด A4 ทางการ)
                            <?php elseif ($document['doc_type'] === 'order'): ?>
                                ต้นฉบับคำสั่งโรงเรียน (ขนาด A4 ทางการ)
                            <?php else: ?>
                                ต้นฉบับเอกสารราชการ
                            <?php endif; ?>
                        </h6>
                        <small class="text-muted">ความกว้าง ๒๑ ซม. x ๒๙.๗ ซม. ตามระเบียบสำนักนายกรัฐมนตรีว่าด้วยงานสารบรรณ</small>
                    </div>
                </div>

                <!-- Tabs if PDF is attached -->
                <?php if (!empty($document['file_path'])): ?>
                    <ul class="nav nav-pills" role="tablist">
                        <li class="nav-item">
                            <button type="button" class="nav-link active btn-sm" role="tab" data-bs-toggle="tab" data-bs-target="#tab-reader-paper">
                                <i class="bx bx-file me-1"></i> ข้อความในระบบ
                            </button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link btn-sm" role="tab" data-bs-toggle="tab" data-bs-target="#tab-reader-pdf">
                                <i class="bx bxs-file-pdf me-1 text-danger"></i> ไฟล์แนบ PDF
                            </button>
                        </li>
                    </ul>
                <?php endif; ?>
            </div>

            <div class="card-body p-0">
                <div class="tab-content p-0">
                    <!-- Tab 1: Official Paper Display -->
                    <div class="tab-pane fade show active" id="tab-reader-paper" role="tabpanel">
                        <div class="doc-desk-canvas" id="docDeskCanvas">
                            <div class="doc-paper-scale-wrap" id="docPaperScaleWrap">
                                <?php if ($document['doc_type'] === 'order'): ?>
                                    <!-- Official School Order Paper -->
                                    <div class="order-paper-display shadow">
                                        <div class="order-garuda-center">
                                            <img src="<?= base_url('assets/img/krut_thai.png') ?>" alt="ตราครุฑ" class="order-garuda-img">
                                        </div>
                                        <div class="order-school-title">คำสั่งโรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์</div>
                                        <div class="order-number-title">
                                            ที่ <?= esc($document['doc_no'] ?: ($document['internal_reg_no'] ?? '-')) ?>
                                        </div>
                                        <div class="order-subject-title">
                                            เรื่อง <?= esc($document['title']) ?>
                                        </div>
                                        <div class="order-divider-line"></div>
                                        <div class="order-body-content">
                                            <?= !empty($document['content']) ? $document['content'] : '<p class="text-muted text-center py-4">(ไม่พบเนื้อหาคำสั่งในระบบ)</p>' ?>
                                        </div>
                                        <div class="order-date-clause">
                                            สั่ง ณ วันที่ <?= thai_date($document['doc_date'], 'medium') ?>
                                        </div>
                                        <div class="order-signature-container">
                                            <?php if (!empty($document['creator_signature'])): ?>
                                                <img src="<?= base_url($document['creator_signature']) ?>" alt="ลายเซ็น" class="order-signature-img">
                                            <?php endif; ?>
                                            <div class="sign-name">
                                                ( นายพงษ์ศักดิ์ เงินสันเทียะ )
                                            </div>
                                            <div class="sign-position">
                                                ผู้อำนวยการโรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์
                                            </div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <!-- Official Saraban Memo Paper -->
                                    <div class="saraban-paper-display shadow" id="printableMemoPaper">
                                        <div class="header">
                                            <img src="<?= base_url('assets/img/krut_thai.png') ?>" alt="ตราครุฑ" class="garuda">
                                            <div class="document-title">บันทึกข้อความ</div>
                                        </div>

                                        <div class="form-row">
                                            <span class="label">ส่วนราชการ</span>
                                            <span class="editable-field"><?= esc($document['from_org'] ?: ($document['memo_department'] ?? '')) ?><?= !empty($document['memo_contact']) ? ' โทร. ' . esc($document['memo_contact']) : '' ?></span>
                                        </div>

                                        <div class="saraban-grid-2">
                                            <div class="col-left">
                                                <span class="label">ที่</span>
                                                <span class="editable-field"><?= esc($document['doc_no'] ?: ($document['internal_reg_no'] ?? '-')) ?></span>
                                            </div>
                                            <div class="col-right">
                                                <span class="label">วันที่</span>
                                                <span class="editable-field"><?= thai_date($document['doc_date'], 'medium') ?></span>
                                            </div>
                                        </div>

                                        <div class="form-row subject-row">
                                            <span class="label">เรื่อง</span>
                                            <span class="editable-field fw-bold"><?= esc($document['title']) ?></span>
                                        </div>

                                        <div class="salutation">
                                            <span class="label">เรียน</span>
                                            <span class="editable-field" style="border-bottom: none;"><?= esc($document['to_org'] ?: 'ผู้อำนวยการโรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์') ?></span>
                                        </div>

                                        <!-- Document Body Content -->
                                        <div class="body-text">
                                            <?= !empty($document['content']) ? $document['content'] : '<p class="text-muted text-center py-4">(ไม่พบเนื้อหาข้อความในระบบ)</p>' ?>
                                        </div>

                                        <!-- Creator Signature Block -->
                                        <div class="signature-section">
                                            <div class="signature-box">
                                                <div class="signature-line">
                                                    <?php if (!empty($document['creator_signature'])): ?>
                                                        <img src="<?= base_url($document['creator_signature']) ?>" alt="ลายเซ็น" class="signature-img">
                                                    <?php elseif (!empty($creator['signature_img'])): ?>
                                                        <img src="<?= base_url($creator['signature_img']) ?>" alt="ลายเซ็น" class="signature-img">
                                                    <?php endif; ?>
                                                </div>
                                                <div class="sign-name">
                                                    ( <?= esc(($creator['prefix'] ?? $document['prefix'] ?? '') . ($creator['first_name'] ?? $document['first_name'] ?? '') . ' ' . ($creator['last_name'] ?? $document['last_name'] ?? '')) ?> )
                                                </div>
                                                <div class="sign-position">
                                                    ตำแหน่ง <?= esc($creator['position_name'] ?? $document['creator_department'] ?? 'บุคลากรทางการศึกษา') ?>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Digital Endorsements Section: Realistic Blue Rubber Stamps -->
                                        <?php 
                                        $signedRoutes = array_filter($routes, function($r) {
                                            return $r['status'] === 'signed';
                                        });
                                        $defaultPositions = [
                                            1 => ['x' => '8%',  'y' => '68%', 'rot' => '-1.5deg'],
                                            2 => ['x' => '52%', 'y' => '68%', 'rot' => '1.2deg'],
                                            3 => ['x' => '8%',  'y' => '82%', 'rot' => '-2.0deg'],
                                            4 => ['x' => '52%', 'y' => '82%', 'rot' => '1.5deg'],
                                        ];
                                        ?>
                                        <?php foreach ($signedRoutes as $sr): ?>
                                            <?php
                                            $stepIdx = (int)($sr['step_order'] ?? 1);
                                            $posX = !empty($sr['stamp_x']) ? $sr['stamp_x'] : ($defaultPositions[$stepIdx]['x'] ?? '8%');
                                            $posY = !empty($sr['stamp_y']) ? $sr['stamp_y'] : ($defaultPositions[$stepIdx]['y'] ?? '70%');
                                            $scale = !empty($sr['stamp_scale']) ? (float)$sr['stamp_scale'] : 1.0;
                                            $widthStyle = !empty($sr['stamp_width']) ? 'width: ' . esc($sr['stamp_width']) . ';' : '';
                                            $rot  = $defaultPositions[$stepIdx]['rot'] ?? '-1.5deg';
                                            $transformStyle = "transform: rotate({$rot}) scale({$scale}); transform-origin: top left;";
                                            ?>
                                            <div class="saraban-blue-stamp shadow-sm" style="left: <?= esc($posX) ?>; top: <?= esc($posY) ?>; <?= $transformStyle ?> <?= $widthStyle ?>">
                                                <div class="stamp-preset"><?= esc($sr['comment_preset']) ?></div>
                                                <?php if (!empty($sr['comment_text'])): ?>
                                                    <div class="stamp-comment">"<?= esc($sr['comment_text']) ?>"</div>
                                                <?php endif; ?>
                                                <div class="stamp-sig-wrap">
                                                    <?php if (!empty($sr['signature_img'])): ?>
                                                        <img src="<?= base_url($sr['signature_img']) ?>" alt="ลายเซ็น" class="stamp-sig-img">
                                                    <?php endif; ?>
                                                </div>
                                                <div class="stamp-signer-name">( <?= esc(($sr['prefix'] ?? '') . ($sr['first_name'] ?? '') . ' ' . ($sr['last_name'] ?? '')) ?> )</div>
                                                <div class="stamp-signer-pos"><?= esc($sr['position_name'] ?? '') ?></div>
                                                <div class="stamp-footer">
                                                    <span>วันที่ <?= thai_date($sr['signed_at'], 'short') ?></span>
                                                    <?php if (!empty($sr['verify_hash'])): ?>
                                                        <a href="<?= base_url('verify/' . $sr['verify_hash']) ?>" target="_blank" class="stamp-verify-badge text-decoration-none">
                                                            ✓ ตรวจสอบ
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>

                                        <!-- Live Draggable & Resizable Stamp for Active Signer -->
                                        <?php if ($canSign): ?>
                                            <?php
                                            $savedSig = $currentUser['signature_img'] ?? session()->get('signature_img') ?? '';
                                            $defaultInitialX = count($signedRoutes) % 2 === 1 ? '52%' : '8%';
                                            $defaultInitialY = count($signedRoutes) >= 2 ? '82%' : '68%';
                                            ?>
                                            <div id="liveDraggableStamp" class="saraban-blue-stamp is-draggable shadow" style="left: <?= $defaultInitialX ?>; top: <?= $defaultInitialY ?>; transform: rotate(-1.5deg) scale(1.0); transform-origin: top left;">
                                                <!-- Stamp Top Floating Action & Resize Controls -->
                                                <div class="stamp-controls-bar no-print">
                                                    <span class="stamp-btn-move" title="แตะหรือลากเพื่อย้ายตำแหน่ง"><i class="bx bx-move"></i> ย้าย</span>
                                                    <span class="stamp-btn-tool" onclick="changeStampScale(-0.1, event)" title="ย่อขนาดเล็กลง"><i class="bx bx-minus"></i></span>
                                                    <span class="stamp-scale-badge" id="liveStampScaleDisplay">100%</span>
                                                    <span class="stamp-btn-tool" onclick="changeStampScale(0.1, event)" title="ขยายขนาดใหญ่ขึ้น"><i class="bx bx-plus"></i></span>
                                                    <span class="stamp-btn-tool" onclick="resetStampSize(event)" title="รีเซ็ตขนาดเริ่มต้น"><i class="bx bx-reset"></i></span>
                                                </div>

                                                <div class="stamp-preset" id="previewStampPreset">เห็นควรอนุมัติตามเสนอ</div>
                                                <div class="stamp-comment" id="previewStampComment">"ตรวจสอบแล้ว ถูกต้องตามระเบียบ เห็นควรอนุมัติการดำเนินงาน"</div>
                                                <div class="stamp-sig-wrap">
                                                    <img src="<?= !empty($savedSig) ? base_url($savedSig) : '' ?>" alt="ลายเซ็น" id="previewStampSigImg" class="stamp-sig-img" style="<?= empty($savedSig) ? 'display:none;' : '' ?>">
                                                    <span id="previewStampSigPlaceholder" class="text-primary small fst-italic" style="<?= !empty($savedSig) ? 'display:none;' : '' ?> font-size: 10pt;">[ รอประทับลายเซ็น ]</span>
                                                </div>
                                                <div class="stamp-signer-name" id="previewStampName">( <?= esc(($currentUser['prefix'] ?? '') . ($currentUser['first_name'] ?? '') . ' ' . ($currentUser['last_name'] ?? '')) ?> )</div>
                                                <div class="stamp-signer-pos" id="previewStampPos"><?= esc($currentRoute['position_name'] ?? $currentUser['position_name'] ?? session()->get('position_name') ?? 'รองผู้อำนวยการสถานศึกษา') ?></div>
                                                <div class="stamp-footer">
                                                    <span>วันที่ <?= thai_date(date('Y-m-d'), 'short') ?></span>
                                                    <span class="badge bg-primary text-white" style="font-size: 8pt; padding: 1px 4px;">รอลงนาม</span>
                                                </div>

                                                <!-- Corner Resize Handle (↘ Drag to Scale/Resize) -->
                                                <div class="stamp-resize-handle no-print" id="stampResizeHandle" title="คลิกหรือแตะลากเพื่อย่อ-ขยายขนาด">
                                                    <i class="bx bx-expand-alt"></i>
                                                </div>
                                                <!-- Right Edge Handle (↔ Drag to Stretch Width) -->
                                                <div class="stamp-width-handle no-print" id="stampWidthHandle" title="คลิกหรือแตะลากเพื่อยืดความกว้าง"></div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 2: Attached PDF Viewer -->
                    <?php if (!empty($document['file_path'])): ?>
                        <div class="tab-pane fade" id="tab-reader-pdf" role="tabpanel">
                            <div class="bg-dark p-2 text-center text-white small d-flex justify-content-between align-items-center">
                                <span><i class="bx bxs-file-pdf text-danger me-1"></i> ไฟล์เอกสารแนบต้นฉบับ: <?= esc(basename($document['file_path'])) ?></span>
                                <a href="<?= document_url($document['file_path']) ?>" target="_blank" class="btn btn-sm btn-light text-dark py-0.5 px-2">
                                    <i class="bx bx-download me-1"></i> เปิดดูเต็มจอ
                                </a>
                            </div>
                            <iframe src="<?= document_url($document['file_path']) ?>#toolbar=1" width="100%" height="820px" style="border: none;" title="PDF Preview"></iframe>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- ==========================================================================
             Information Dock (Tabs: Metadata, Routing Slips, Audit Logs)
             ========================================================================== -->
        <div class="card info-dock-card shadow-sm">
            <div class="card-header bg-white border-bottom p-0">
                <ul class="nav nav-tabs info-dock-tabs" role="tablist">
                    <li class="nav-item">
                        <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#dock-tab-meta">
                            <i class="bx bx-info-circle me-1 text-primary"></i> ข้อมูลสารบรรณ
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#dock-tab-routes">
                            <i class="bx bx-git-merge me-1 text-info"></i> เส้นทางเกษียน (<?= count($routes) ?> ขั้น)
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#dock-tab-logs">
                            <i class="bx bx-history me-1 text-secondary"></i> บันทึกประวัติ (<?= count($logs) ?>)
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body p-4">
                <div class="tab-content p-0">
                    <!-- Tab: Metadata -->
                    <div class="tab-pane fade show active" id="dock-tab-meta" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="text-muted small d-block">เลขทะเบียนภายใน รร.</label>
                                <span class="fw-bold text-dark fs-6"><?= esc($document['internal_reg_no'] ?: '-') ?></span>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small d-block">เลขที่หนังสือราชการ</label>
                                <span class="fw-bold text-primary fs-6"><?= esc($document['doc_no'] ?: '-') ?></span>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small d-block">จาก (หน่วยงานผู้ส่ง)</label>
                                <span class="text-dark"><?= esc($document['from_org'] ?: ($document['memo_department'] ?? '-')) ?></span>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small d-block">ถึง (หน่วยงานผู้รับ/ผู้บริหาร)</label>
                                <span class="text-dark"><?= esc($document['to_org'] ?: 'ผู้อำนวยการโรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์') ?></span>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small d-block">ลงวันที่ในหนังสือ</label>
                                <span class="text-dark"><?= thai_date($document['doc_date'], 'medium') ?></span>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small d-block">วันที่บันทึกเข้าระบบสารบรรณ</label>
                                <span class="text-dark"><?= thai_date($document['created_at'], 'medium') ?></span>
                            </div>
                            <div class="col-12">
                                <label class="text-muted small d-block">ผู้เสนอเรื่อง / ผู้ลงทะเบียน</label>
                                <div class="d-flex align-items-center gap-2 mt-1">
                                    <span class="badge bg-label-primary p-2 rounded-circle">
                                        <i class="bx bx-user fs-5"></i>
                                    </span>
                                    <div>
                                        <strong class="text-dark"><?= esc(($creator['prefix'] ?? $document['prefix'] ?? '') . ($creator['first_name'] ?? $document['first_name'] ?? '') . ' ' . ($creator['last_name'] ?? $document['last_name'] ?? '')) ?></strong>
                                        <small class="text-muted d-block"><?= esc($creator['position_name'] ?? $document['creator_department'] ?? '') ?> • <?= esc($creator['department_name'] ?? '-') ?></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab: Routing Slips Detail -->
                    <div class="tab-pane fade" id="dock-tab-routes" role="tabpanel">
                        <div class="d-flex flex-column gap-3">
                            <?php foreach ($routes as $r): ?>
                                <div class="border rounded-3 p-3 <?= $r['status'] === 'signed' ? 'bg-light border-success' : ($r['status'] === 'returned' ? 'bg-light border-danger' : '') ?>">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge <?= $r['status'] === 'signed' ? 'bg-success' : ($r['status'] === 'returned' ? 'bg-danger' : 'bg-primary') ?> px-2.5 py-1">
                                                ลำดับที่ <?= $r['step_order'] ?>
                                            </span>
                                            <strong class="text-dark"><?= esc($r['position_name']) ?></strong>
                                        </div>
                                        <span class="badge <?= $r['status'] === 'signed' ? 'bg-label-success' : ($r['status'] === 'returned' ? 'bg-label-danger' : 'bg-label-secondary') ?>">
                                            <?= $r['status'] === 'signed' ? 'ลงนามแล้ว' : ($r['status'] === 'returned' ? 'ส่งคืนแก้ไข' : 'รอดำเนินการ') ?>
                                        </span>
                                    </div>

                                    <?php if ($r['status'] === 'signed'): ?>
                                        <div class="p-2.5 bg-white rounded border mb-2">
                                            <div class="fw-bold text-primary mb-1"><?= esc($r['comment_preset']) ?></div>
                                            <?php if (!empty($r['comment_text'])): ?>
                                                <div class="text-dark small"><?= nl2br(esc($r['comment_text'])) ?></div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-end pt-1">
                                            <div class="d-flex align-items-center gap-2">
                                                <?php if (!empty($r['signature_img'])): ?>
                                                    <img src="<?= base_url($r['signature_img']) ?>" alt="ลายเซ็น" style="max-height: 40px; max-width: 120px;">
                                                <?php endif; ?>
                                                <div>
                                                    <div class="fw-semibold small text-dark">(<?= esc($r['prefix'] . $r['first_name'] . ' ' . $r['last_name']) ?>)</div>
                                                    <small class="text-muted"><?= esc($r['position_name']) ?></small>
                                                </div>
                                            </div>
                                            <div class="text-end">
                                                <small class="text-muted d-block"><i class="bx bx-calendar me-1"></i><?= thai_date($r['signed_at'], 'short', true) ?></small>
                                                <?php if (!empty($r['verify_hash'])): ?>
                                                    <a href="<?= base_url('verify/' . $r['verify_hash']) ?>" target="_blank" class="badge bg-label-info text-decoration-none mt-1">
                                                        <i class="bx bx-qr-scan me-1"></i> ตรวจสอบ
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-muted small py-2">
                                            <i class="bx bx-time-five me-1"></i> รอดำเนินการเกษียนจากขั้นตอนนี้
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Tab: Audit Logs -->
                    <div class="tab-pane fade" id="dock-tab-logs" role="tabpanel">
                        <ul class="list-group list-group-flush small">
                            <?php foreach ($logs as $log): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <div>
                                        <span class="badge bg-label-info me-2"><?= esc($log['action']) ?></span>
                                        <strong><?= esc($log['prefix'] . $log['first_name'] . ' ' . $log['last_name']) ?>:</strong>
                                        <span class="text-muted"><?= esc($log['detail']) ?></span>
                                    </div>
                                    <span class="text-muted text-nowrap"><?= thai_date($log['created_at'], 'short', true) ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ==========================================================================
         Right Column: Executive Endorsement Desk (Sticky Panel)
         ========================================================================== -->
    <div class="col-12 col-xl-4 col-lg-5 mb-4">
        <div class="sticky-top" style="top: 85px; z-index: 5;">

            <?php if ($canSign): ?>
                <!-- ACTIVE SIGNING PANEL (COMPACT) -->
                <div class="card executive-desk-card mb-4 shadow-sm">
                    <div class="executive-desk-header">
                        <div class="d-flex align-items-center justify-content-between gap-2">
                            <div class="d-flex align-items-center gap-2" style="min-width: 0;">
                                <i class="bx bxs-pen text-warning fs-5 flex-shrink-0"></i>
                                <div style="min-width: 0;">
                                    <div class="fw-bold text-white small" style="line-height: 1.2;">เกษียนหนังสือ / ลงนามดิจิทัล</div>
                                    <div class="text-white-50 text-truncate" style="font-size: 0.72rem; line-height: 1.2;" title="<?= esc($currentRoute['position_name'] ?? session()->get('position_name')) ?>">
                                        ขั้นที่ <?= esc($currentRoute['step_order']) ?>: <?= esc($currentRoute['position_name'] ?? session()->get('position_name') ?? 'ผู้อำนวยการโรงเรียน') ?>
                                    </div>
                                </div>
                            </div>
                            <span class="badge bg-success text-white px-2 py-0.5 flex-shrink-0" style="font-size: 0.68rem;">พร้อมลงนาม</span>
                        </div>
                    </div>

                    <div class="card-body p-3">
                        <form action="<?= base_url('admin/documents/' . $document['id'] . '/sign') ?>" method="POST" id="signForm">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action_type" id="action_type" value="approve" />
                            <input type="hidden" name="signature_data" id="signature_data" value="" />
                            <input type="hidden" name="stamp_x" id="input_stamp_x" value="<?= $defaultInitialX ?? '8%' ?>" />
                            <input type="hidden" name="stamp_y" id="input_stamp_y" value="<?= $defaultInitialY ?? '68%' ?>" />
                            <input type="hidden" name="stamp_scale" id="input_stamp_scale" value="1.0" />
                            <input type="hidden" name="stamp_width" id="input_stamp_width" value="" />
                            <input type="hidden" name="stamp_page" id="input_stamp_page" value="1" />

                            <!-- Draggable & Resizable Stamp Helper Banner -->
                            <div class="alert alert-primary py-2 px-2.5 small mb-2.5 d-flex align-items-center" style="background: #eef2ff; border: 1px solid #c7d2fe; color: #3730a3; border-radius: 6px;">
                                <i class="bx bx-move me-2 fs-5 flex-shrink-0"></i>
                                <div style="font-size: 0.78rem; line-height: 1.3;">
                                    <strong>โหมดประทับตรายาง:</strong> ลากตราปั๊มไปวางจุดที่ต้องการบน A4 และสามารถกด <strong>[+] / [-]</strong> หรือลากมุม <strong>↘</strong> เพื่อยืด/ย่อ-ขยายขนาดได้อิสระ
                                </div>
                            </div>

                            <!-- 1. Quick Presets -->
                            <div class="mb-2.5">
                                <label class="form-label fw-bold text-dark d-flex justify-content-between align-items-center mb-1 small">
                                    <span><i class="bx bx-comment-check text-primary me-1"></i> คำเกษียนสำเร็จรูป</span>
                                    <small class="text-muted" style="font-size: 0.7rem;">คลิกเพื่อเลือก</small>
                                </label>
                                <div class="d-flex flex-wrap gap-1 mb-1.5">
                                    <button type="button" class="quick-preset-chip active-preset" onclick="selectPreset('เห็นควรอนุมัติตามเสนอ', this)">
                                        ✓ เห็นควรอนุมัติ
                                    </button>
                                    <button type="button" class="quick-preset-chip" onclick="selectPreset('อนุมัติตามเสนอ', this)">
                                        ✍️ อนุมัติตามเสนอ
                                    </button>
                                    <button type="button" class="quick-preset-chip" onclick="selectPreset('ทราบ / ถือปฏิบัติตาม', this)">
                                        👁️ ทราบ/ปฏิบัติ
                                    </button>
                                    <button type="button" class="quick-preset-chip" onclick="selectPreset('เพื่อโปรดพิจารณา', this)">
                                        📌 เพื่อโปรดพิจารณา
                                    </button>
                                    <button type="button" class="quick-preset-chip" onclick="selectPreset('มอบหมายดำเนินการตามเสนอ', this)">
                                        ➡️ มอบหมาย
                                    </button>
                                </div>
                                <input type="text" class="form-control form-control-sm fw-semibold text-primary mb-1.5 py-1 px-2.5" name="comment_preset" id="comment_preset" value="เห็นควรอนุมัติตามเสนอ" required placeholder="หัวข้อคำสั่งการ/เกษียน" style="font-size: 0.85rem;" />
                                <textarea class="form-control form-control-sm py-1.5 px-2.5" name="comment_text" id="comment_text" rows="2" placeholder="ข้อความสั่งการ/ความเห็นเพิ่มเติม..." style="font-size: 0.825rem;">ตรวจสอบแล้ว ถูกต้องตามระเบียบ เห็นควรอนุมัติการดำเนินงาน</textarea>
                            </div>

                            <!-- 2. Signature Section -->
                            <?php 
                            $savedSig = $currentUser['signature_img'] ?? session()->get('signature_img') ?? null;
                            $hasSavedSig = !empty($savedSig);
                            ?>
                            <div class="mb-2.5">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label fw-bold text-dark mb-0 small">
                                        <i class="bx bx-fingerprint text-info me-1"></i> ลายเซ็นดิจิทัล:
                                    </label>
                                    <?php if ($hasSavedSig): ?>
                                        <div class="d-flex gap-2">
                                            <div class="form-check form-check-inline mb-0 me-0">
                                                <input class="form-check-input" type="radio" name="signature_type" id="sigPreset" value="preset" checked onchange="toggleSigMode('preset')" style="transform: scale(0.85);" />
                                                <label class="form-check-label fw-semibold text-dark" for="sigPreset" style="font-size: 0.72rem;">ลายเซ็นในระบบ</label>
                                            </div>
                                            <div class="form-check form-check-inline mb-0 me-0">
                                                <input class="form-check-input" type="radio" name="signature_type" id="sigDraw" value="draw" onchange="toggleSigMode('draw')" style="transform: scale(0.85);" />
                                                <label class="form-check-label text-muted" for="sigDraw" style="font-size: 0.72rem;">เซ็นสด</label>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Preset Signature Box -->
                                <?php if ($hasSavedSig): ?>
                                    <div id="presetSigArea" class="signature-compact-box">
                                        <div class="d-flex align-items-center justify-content-between gap-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="p-1 bg-white rounded border d-flex align-items-center justify-content-center shadow-xs" style="min-width: 80px; height: 38px;">
                                                    <img src="<?= base_url($savedSig) ?>" alt="ลายเซ็น" style="max-height: 32px; max-width: 75px; object-fit: contain;">
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark" style="font-size: 0.8rem; line-height: 1.1;">
                                                        <?= esc(($currentUser['prefix'] ?? '') . ($currentUser['first_name'] ?? '') . ' ' . ($currentUser['last_name'] ?? '')) ?>
                                                    </div>
                                                    <small class="text-muted d-block" style="font-size: 0.7rem; line-height: 1.1;">
                                                        <?= esc($currentUser['position_name'] ?? session()->get('position_name') ?? 'ผู้อำนวยการโรงเรียน') ?>
                                                    </small>
                                                </div>
                                            </div>
                                            <span class="badge bg-label-success py-1 px-1.5" style="font-size: 0.65rem;">
                                                <i class="bx bx-check"></i> พร้อมใช้
                                            </span>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Draw Canvas Area -->
                                <div id="drawSigArea" class="mb-1" style="<?= $hasSavedSig ? 'display: none;' : '' ?>">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <small class="text-muted" style="font-size: 0.7rem;"><i class="bx bx-mouse me-1"></i> ลากเมาส์/นิ้วเซ็นในกรอบ:</small>
                                        <button type="button" class="btn btn-xs btn-outline-danger py-0 px-1.5" id="clearCanvasBtn" style="font-size: 0.68rem;">
                                            <i class="bx bx-eraser me-0.5"></i> ล้าง
                                        </button>
                                    </div>
                                    <div class="border rounded bg-white p-1 text-center shadow-xs">
                                        <canvas id="signatureCanvas" width="340" height="95" style="width: 100%; height: 95px; cursor: crosshair; touch-action: none;"></canvas>
                                    </div>
                                </div>
                            </div>

                            <!-- 3. Action Buttons -->
                            <div class="d-grid gap-1.5 pt-1">
                                <button type="button" class="btn btn-success fw-bold shadow-sm py-2" onclick="submitSign('approve', this)" style="font-size: 0.95rem;">
                                    <i class="bx bx-check-circle me-1 align-middle fs-5"></i> ยืนยันการลงนามเกษียนหนังสือ
                                </button>
                                <button type="button" class="btn btn-link text-danger text-decoration-none btn-sm py-0.5" onclick="submitSign('return', this)" style="font-size: 0.8rem;">
                                    <i class="bx bx-undo me-1"></i> ส่งคืนหนังสือให้แก้ไข (Return)
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <!-- STATUS INFO (Not user's turn or completed) -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4 text-center">
                        <div class="mb-3">
                            <?php if ($document['status'] === 'completed'): ?>
                                <span class="badge bg-label-success p-3 rounded-circle">
                                    <i class="bx bx-check-double fs-1 text-success"></i>
                                </span>
                            <?php else: ?>
                                <span class="badge bg-label-info p-3 rounded-circle">
                                    <i class="bx bx-time fs-1 text-info"></i>
                                </span>
                            <?php endif; ?>
                        </div>
                        <h5 class="fw-bold text-dark mb-1">
                            <?= $document['status'] === 'completed' ? 'เอกสารลงนามเสร็จสมบูรณ์แล้ว' : 'อยู่ระหว่างรอการเกษียนลงนาม' ?>
                        </h5>
                        <p class="text-muted small mb-3">
                            <?php if ($document['status'] === 'completed'): ?>
                                เอกสารได้รับการเกษียนและลงนามครบถ้วนทุกขั้นตอนแล้ว สามารถพิมพ์หรือดาวน์โหลดเก็บไว้เป็นหลักฐานได้
                            <?php else: ?>
                                ขณะนี้หนังสืออยู่ในความรับผิดชอบของขั้นตอนที่ <?= esc($document['current_step']) ?>
                            <?php endif; ?>
                        </p>
                        <div class="d-flex justify-content-center gap-2">
                            <?php if ($document['doc_type'] === 'internal'): ?>
                                <a href="<?= base_url('admin/memos/' . $document['id'] . '/print') ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                                    <i class="bx bx-printer me-1"></i> พิมพ์ขนาด A4
                                </a>
                            <?php endif; ?>
                            <?php if (!empty($document['file_path'])): ?>
                                <a href="<?= document_url($document['file_path']) ?>" target="_blank" class="btn btn-outline-danger btn-sm">
                                    <i class="bx bxs-file-pdf me-1"></i> ดาวน์โหลด PDF
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<!-- ==========================================================================
     Modal Upload Document File
     ========================================================================== -->
<div class="modal fade" id="uploadDocModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form action="<?= base_url('admin/documents/' . $document['id'] . '/upload') ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-header border-bottom py-3 px-4 bg-light">
                    <div class="d-flex align-items-center">
                        <span class="badge bg-primary p-2 rounded-3 me-2.5 shadow-sm">
                            <i class="bx bx-upload fs-4 text-white"></i>
                        </span>
                        <div>
                            <h5 class="modal-title fw-bold text-dark mb-0">อัปโหลด / เปลี่ยนไฟล์เอกสารแนบ</h5>
                            <small class="text-secondary">แนบไฟล์ PDF ต้นเรื่องเพื่อใช้ประกอบการลงนาม</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label required fw-bold text-dark">เลือกไฟล์เอกสารต้นเรื่อง (รองรับไฟล์ PDF)</label>
                        <input type="file" name="doc_file" id="modalDocFileInput" accept=".pdf,application/pdf" style="display: none;" required />

                        <div id="modalDocDropzone" class="p-4 border border-2 border-dashed rounded-3 text-center cursor-pointer bg-light" onclick="triggerBrowseModalDocFile()">
                            <span class="badge bg-label-primary p-3 rounded-circle mb-2">
                                <i class="bx bxs-cloud-upload fs-2 text-primary"></i>
                            </span>
                            <h6 class="fw-bold text-dark mb-1">ลากและวางไฟล์ PDF ที่นี่ หรือคลิกเพื่อเลือกไฟล์</h6>
                            <p class="text-muted small mb-2">รองรับเฉพาะเอกสารรูปแบบ <strong>.PDF</strong></p>
                            <button type="button" class="btn btn-outline-primary btn-sm px-3 shadow-xs">
                                <i class="bx bx-file me-1"></i> เลือกไฟล์ PDF
                            </button>
                        </div>

                        <div id="modalDocFilePreview" class="mt-3 p-3 bg-light rounded-3 border" style="display: none;">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="badge bg-label-danger p-2 rounded-3 me-2">
                                        <i class="bx bxs-file-pdf fs-3 text-danger"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold text-dark text-break" id="modalDocPreviewName">filename.pdf</h6>
                                        <small class="text-muted" id="modalDocPreviewSize">0 KB</small>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-outline-danger btn-xs py-1 px-2" onclick="removeModalDocFile(event)">
                                    <i class="bx bx-trash me-1"></i> ลบ / เลือกใหม่
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light py-2.5 px-4 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm fw-semibold" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary btn-sm fw-semibold shadow-sm">
                        <i class="bx bx-save me-1"></i> บันทึกและอัปโหลดไฟล์
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
var signaturePad = null;
var currentZoom = 1.0;

// Initialize or resize Signature Pad
function initSignaturePad() {
    const canvas = document.getElementById('signatureCanvas');
    if (!canvas) return;

    if (typeof SignaturePad === 'undefined') {
        console.warn('SignaturePad library is not loaded yet');
        return;
    }

    const ratio = Math.max(window.devicePixelRatio || 1, 1);
    const parent = canvas.parentElement;
    const width = Math.max(parent ? parent.clientWidth - 16 : 300, 260);
    const height = 95;

    canvas.width = width * ratio;
    canvas.height = height * ratio;
    canvas.style.width = width + 'px';
    canvas.style.height = height + 'px';

    const ctx = canvas.getContext("2d");
    ctx.scale(ratio, ratio);

    if (!signaturePad) {
        signaturePad = new SignaturePad(canvas, {
            penColor: '#0038a8',
            backgroundColor: 'rgba(255, 255, 255, 0)',
            minWidth: 1.5,
            maxWidth: 3.2,
            velocityFilterWeight: 0.7
        });

        const clearBtn = document.getElementById('clearCanvasBtn');
        if (clearBtn) {
            clearBtn.addEventListener('click', function () {
                if (signaturePad) signaturePad.clear();
            });
        }
    }
}

document.addEventListener('DOMContentLoaded', function () {
    // Check if draw area is currently visible on load
    const drawArea = document.getElementById('drawSigArea');
    if (drawArea && drawArea.style.display !== 'none') {
        initSignaturePad();
    }

    // Auto-fit paper on load for medium screens if needed
    autoFitOnLoad();

    // Initialize Draggable Stamp Engine
    initDraggableStamp();
});

// Live Stamp Preview Updater
function updateStampLivePreview() {
    const preset = document.getElementById('comment_preset')?.value || '';
    const note = document.getElementById('comment_text')?.value || '';
    const pPreset = document.getElementById('previewStampPreset');
    const pComment = document.getElementById('previewStampComment');
    if (pPreset) pPreset.textContent = preset;
    if (pComment) pComment.textContent = note ? `"${note}"` : '';
}

// Draggable & Resizable Stamp Engine
var currentStampScale = 1.0;

function applyStampTransform() {
    const stamp = document.getElementById('liveDraggableStamp');
    const inputScale = document.getElementById('input_stamp_scale');
    const scaleBadge = document.getElementById('liveStampScaleDisplay');
    if (!stamp) return;
    stamp.style.transform = `rotate(-1.5deg) scale(${currentStampScale})`;
    stamp.style.transformOrigin = 'top left';
    if (scaleBadge) scaleBadge.textContent = Math.round(currentStampScale * 100) + '%';
    if (inputScale) inputScale.value = currentStampScale.toFixed(2);
}

function changeStampScale(delta, e) {
    if (e) { e.preventDefault(); e.stopPropagation(); }
    currentStampScale = Math.min(1.6, Math.max(0.6, parseFloat((currentStampScale + delta).toFixed(2))));
    applyStampTransform();
}

function resetStampSize(e) {
    if (e) { e.preventDefault(); e.stopPropagation(); }
    currentStampScale = 1.0;
    const stamp = document.getElementById('liveDraggableStamp');
    const inputWidth = document.getElementById('input_stamp_width');
    if (stamp) stamp.style.width = '6.8cm';
    if (inputWidth) inputWidth.value = '';
    applyStampTransform();
}

function initDraggableStamp() {
    const stamp = document.getElementById('liveDraggableStamp');
    const paper = document.getElementById('printableMemoPaper') || document.querySelector('.saraban-paper-display') || document.querySelector('.order-paper-display');
    const inputX = document.getElementById('input_stamp_x');
    const inputY = document.getElementById('input_stamp_y');
    if (!stamp || !paper) return;

    let isDragging = false;
    let startX = 0, startY = 0;
    let initialLeft = 0, initialTop = 0;

    function onPointerDown(e) {
        if (e.target.closest('#stampResizeHandle') || e.target.closest('#stampWidthHandle') || e.target.closest('.stamp-btn-tool') || e.target.closest('.no-drag')) {
            return;
        }
        isDragging = true;
        stamp.classList.add('is-dragging');
        
        const clientX = e.clientX ?? (e.touches && e.touches[0].clientX);
        const clientY = e.clientY ?? (e.touches && e.touches[0].clientY);
        startX = clientX;
        startY = clientY;
        
        initialLeft = stamp.offsetLeft;
        initialTop = stamp.offsetTop;

        window.addEventListener('pointermove', onPointerMove);
        window.addEventListener('pointerup', onPointerUp);
        window.addEventListener('touchmove', onPointerMove, { passive: false });
        window.addEventListener('touchend', onPointerUp);
        e.preventDefault();
    }

    function onPointerMove(e) {
        if (!isDragging) return;
        
        const clientX = e.clientX ?? (e.touches && e.touches[0].clientX);
        const clientY = e.clientY ?? (e.touches && e.touches[0].clientY);
        
        const scale = window.currentZoom || 1.0;
        const dx = (clientX - startX) / scale;
        const dy = (clientY - startY) / scale;
        
        let newLeft = initialLeft + dx;
        let newTop = initialTop + dy;
        
        // Bounds constraint (keep stamp inside paper)
        const maxLeft = paper.clientWidth - (stamp.offsetWidth * currentStampScale) - 10;
        const maxTop = paper.clientHeight - (stamp.offsetHeight * currentStampScale) - 10;
        
        newLeft = Math.max(10, Math.min(newLeft, maxLeft));
        newTop = Math.max(10, Math.min(newTop, maxTop));
        
        stamp.style.left = newLeft + 'px';
        stamp.style.top = newTop + 'px';
        
        // Calculate percentage relative to paper dimensions
        const percentX = ((newLeft / paper.clientWidth) * 100).toFixed(2) + '%';
        const percentY = ((newTop / paper.clientHeight) * 100).toFixed(2) + '%';
        
        if (inputX) inputX.value = percentX;
        if (inputY) inputY.value = percentY;

        if (e.cancelable) e.preventDefault();
    }

    function onPointerUp() {
        if (!isDragging) return;
        isDragging = false;
        stamp.classList.remove('is-dragging');
        
        window.removeEventListener('pointermove', onPointerMove);
        window.removeEventListener('pointerup', onPointerUp);
        window.removeEventListener('touchmove', onPointerMove);
        window.removeEventListener('touchend', onPointerUp);
    }

    stamp.addEventListener('pointerdown', onPointerDown);

    // Interactive Corner Resize (Scale & Dimensions)
    const resizeHandle = document.getElementById('stampResizeHandle');
    if (resizeHandle) {
        resizeHandle.addEventListener('pointerdown', function (e) {
            e.stopPropagation();
            e.preventDefault();
            const rStartX = e.clientX ?? (e.touches && e.touches[0].clientX);
            const rStartY = e.clientY ?? (e.touches && e.touches[0].clientY);
            const startScale = currentStampScale;

            function onResizeMove(ev) {
                const clientX = ev.clientX ?? (ev.touches && ev.touches[0].clientX);
                const clientY = ev.clientY ?? (ev.touches && ev.touches[0].clientY);
                const scale = window.currentZoom || 1.0;
                const dx = (clientX - rStartX) / scale;
                const dy = (clientY - rStartY) / scale;
                const deltaScale = (dx + dy) / 220;
                currentStampScale = Math.min(1.6, Math.max(0.6, parseFloat((startScale + deltaScale).toFixed(2))));
                applyStampTransform();
            }

            function onResizeUp() {
                window.removeEventListener('pointermove', onResizeMove);
                window.removeEventListener('pointerup', onResizeUp);
                window.removeEventListener('touchmove', onResizeMove);
                window.removeEventListener('touchend', onResizeUp);
            }

            window.addEventListener('pointermove', onResizeMove);
            window.addEventListener('pointerup', onResizeUp);
            window.addEventListener('touchmove', onResizeMove, { passive: false });
            window.addEventListener('touchend', onResizeUp);
        });
    }

    // Interactive Right-Edge Stretch (Width Resize)
    const widthHandle = document.getElementById('stampWidthHandle');
    if (widthHandle) {
        widthHandle.addEventListener('pointerdown', function (e) {
            e.stopPropagation();
            e.preventDefault();
            const wStartX = e.clientX ?? (e.touches && e.touches[0].clientX);
            const startWidth = stamp.offsetWidth;

            function onWidthMove(ev) {
                const clientX = ev.clientX ?? (ev.touches && ev.touches[0].clientX);
                const scale = window.currentZoom || 1.0;
                const dx = (clientX - wStartX) / scale;
                const newWidth = Math.min(460, Math.max(210, Math.round(startWidth + dx)));
                stamp.style.width = newWidth + 'px';
                const inputWidth = document.getElementById('input_stamp_width');
                if (inputWidth) inputWidth.value = newWidth + 'px';
            }

            function onWidthUp() {
                window.removeEventListener('pointermove', onWidthMove);
                window.removeEventListener('pointerup', onWidthUp);
                window.removeEventListener('touchmove', onWidthMove);
                window.removeEventListener('touchend', onWidthUp);
            }

            window.addEventListener('pointermove', onWidthMove);
            window.addEventListener('pointerup', onWidthUp);
            window.addEventListener('touchmove', onWidthMove, { passive: false });
            window.addEventListener('touchend', onWidthUp);
        });
    }

    // Live update preview text
    const presetInput = document.getElementById('comment_preset');
    const commentInput = document.getElementById('comment_text');
    if (presetInput) presetInput.addEventListener('input', updateStampLivePreview);
    if (commentInput) commentInput.addEventListener('input', updateStampLivePreview);
}

// Zoom and Scale Controllers
function setDocZoom(zoomLevel) {
    currentZoom = zoomLevel;
    const paperWrap = document.getElementById('docPaperScaleWrap');
    if (paperWrap) {
        paperWrap.style.transform = `scale(${zoomLevel})`;
    }

    document.querySelectorAll('.btn-group button').forEach(btn => btn.classList.remove('active'));
    if (zoomLevel === 1.0) {
        const b = document.getElementById('btnZoom100');
        if (b) b.classList.add('active');
    }
}

function fitDocToWidth() {
    const deskCanvas = document.getElementById('docDeskCanvas');
    const paper = document.getElementById('printableMemoPaper') || document.querySelector('.order-paper-display');
    if (deskCanvas && paper) {
        const availableWidth = deskCanvas.clientWidth - 48; // padding
        const paperWidth = paper.offsetWidth || 794; // approx 21cm
        const fitScale = Math.min(1.0, Math.max(0.6, availableWidth / paperWidth));
        setDocZoom(parseFloat(fitScale.toFixed(2)));
    }
}

function autoFitOnLoad() {
    const deskCanvas = document.getElementById('docDeskCanvas');
    if (deskCanvas && deskCanvas.clientWidth < 850) {
        fitDocToWidth();
    }
}

// Quick Preset Selection
function selectPreset(preset, btn) {
    document.getElementById('comment_preset').value = preset;
    document.querySelectorAll('.quick-preset-chip').forEach(c => c.classList.remove('active-preset'));
    if (btn) btn.classList.add('active-preset');

    // Auto set appropriate note
    const noteEl = document.getElementById('comment_text');
    if (noteEl && (!noteEl.value || noteEl.value.includes('ตรวจสอบแล้ว') || noteEl.value.includes('เห็นควรอนุมัติ'))) {
        if (preset === 'เห็นควรอนุมัติตามเสนอ') {
            noteEl.value = 'ตรวจสอบแล้ว ถูกต้องตามระเบียบ เห็นควรอนุมัติการดำเนินงาน';
        } else if (preset === 'อนุมัติตามเสนอ') {
            noteEl.value = 'อนุมัติให้ดำเนินการได้ตามเสนอ';
        } else if (preset === 'ทราบ / ถือปฏิบัติตาม') {
            noteEl.value = 'รับทราบข้อความและมอบหมายฝ่ายที่เกี่ยวข้องถือปฏิบัติตามระเบียบ';
        } else if (preset === 'เพื่อโปรดพิจารณา') {
            noteEl.value = 'เพื่อโปรดพิจารณาสั่งการ';
        } else if (preset === 'มอบหมายดำเนินการตามเสนอ') {
            noteEl.value = 'มอบหมายให้ฝ่ายที่เกี่ยวข้องดำเนินการตามขั้นตอนต่อไป';
        }
    }

    updateStampLivePreview();
}

// Toggle Signature Mode
function toggleSigMode(mode) {
    const drawArea = document.getElementById('drawSigArea');
    const presetArea = document.getElementById('presetSigArea');
    if (mode === 'draw') {
        if (drawArea) drawArea.style.display = 'block';
        if (presetArea) presetArea.style.display = 'none';
        setTimeout(initSignaturePad, 60);
    } else {
        if (drawArea) drawArea.style.display = 'none';
        if (presetArea) presetArea.style.display = 'block';
    }
}

// Form Submission
function submitSign(action, btn) {
    document.getElementById('action_type').value = action;

    if (action === 'return') {
        if (window.AppAlert && typeof window.AppAlert.prompt === 'function') {
            AppAlert.prompt('กรุณาระบุเหตุผลการส่งคืนหนังสือให้แก้ไข:', {
                placeholder: 'เช่น ข้อมูลไม่ครบถ้วน, เอกสารแนบไม่ถูกต้อง...',
                required: true,
                errorMessage: 'จำเป็นต้องระบุเหตุผลในการส่งคืน',
                confirmText: '<i class="bx bx-undo me-1"></i> ยืนยันการส่งคืน'
            }).then(function(reason) {
                if (!reason) return;
                document.getElementById('comment_text').value = reason;
                if (btn && window.SubmitLoading) {
                    window.SubmitLoading.set(btn, 'กำลังส่งคืนหนังสือ...');
                }
                document.getElementById('signForm').submit();
            });
        } else {
            const reason = prompt('กรุณาระบุเหตุผลการส่งคืนหนังสือให้แก้ไข:');
            if (reason) {
                document.getElementById('comment_text').value = reason;
                document.getElementById('signForm').submit();
            }
        }
        return;
    }

    const sigTypeInput = document.querySelector('input[name="signature_type"]:checked');
    const sigType = sigTypeInput ? sigTypeInput.value : 'preset';
    if (sigType === 'draw') {
        if (!signaturePad || signaturePad.isEmpty()) {
            if (window.AppAlert) {
                AppAlert.warning('กรุณาเซ็นชื่อลงในช่องลายเซ็นก่อนกดยืนยัน', 'ยังไม่ได้ลงนาม');
            } else {
                alert('กรุณาเซ็นชื่อลงในช่องลายเซ็นก่อนกดยืนยัน');
            }
            return;
        }
        document.getElementById('signature_data').value = signaturePad.toDataURL('image/png');
    }

    if (btn && window.SubmitLoading) {
        window.SubmitLoading.set(btn, 'กำลังบันทึกลงนามเกษียน...');
    }
    document.getElementById('signForm').submit();
}

// Upload Modal Handlers
function triggerBrowseModalDocFile() {
    const fileInput = document.getElementById('modalDocFileInput');
    if (fileInput) fileInput.click();
}

function handleModalDocFileSelect(file) {
    if (!file) return;

    if (file.type !== 'application/pdf' && !file.name.toLowerCase().endsWith('.pdf')) {
        if (window.Swal) {
            Swal.fire({
                icon: 'warning',
                title: 'รูปแบบไฟล์ไม่ถูกต้อง',
                text: 'ระบบรองรับเฉพาะไฟล์เอกสารประเภท .PDF เท่านั้น',
                confirmButtonColor: '#696cff'
            });
        } else {
            alert('ระบบรองรับเฉพาะไฟล์เอกสารประเภท .PDF เท่านั้น');
        }
        removeModalDocFile();
        return;
    }

    const dropzone = document.getElementById('modalDocDropzone');
    const preview = document.getElementById('modalDocFilePreview');
    const nameEl = document.getElementById('modalDocPreviewName');
    const sizeEl = document.getElementById('modalDocPreviewSize');

    if (nameEl) nameEl.textContent = file.name;
    if (sizeEl) sizeEl.textContent = formatDocBytes(file.size);

    if (dropzone) dropzone.style.display = 'none';
    if (preview) preview.style.display = 'block';
}

function removeModalDocFile(e) {
    if (e) {
        e.preventDefault();
        e.stopPropagation();
    }
    const fileInput = document.getElementById('modalDocFileInput');
    const dropzone = document.getElementById('modalDocDropzone');
    const preview = document.getElementById('modalDocFilePreview');

    if (fileInput) fileInput.value = '';
    if (preview) preview.style.display = 'none';
    if (dropzone) dropzone.style.display = 'block';
}

function formatDocBytes(bytes, decimals = 2) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const dm = decimals < 0 ? 0 : decimals;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
}

document.addEventListener('DOMContentLoaded', function() {
    const modalDropzone = document.getElementById('modalDocDropzone');
    const modalFileInput = document.getElementById('modalDocFileInput');

    if (modalDropzone && modalFileInput) {
        ['dragenter', 'dragover'].forEach(eventName => {
            modalDropzone.addEventListener(eventName, function(e) {
                e.preventDefault();
                e.stopPropagation();
                modalDropzone.classList.add('border-primary');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            modalDropzone.addEventListener(eventName, function(e) {
                e.preventDefault();
                e.stopPropagation();
                modalDropzone.classList.remove('border-primary');
            }, false);
        });

        modalDropzone.addEventListener('drop', function(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            if (files && files.length > 0) {
                modalFileInput.files = files;
                handleModalDocFileSelect(files[0]);
            }
        }, false);

        modalFileInput.addEventListener('change', function() {
            if (this.files && this.files.length > 0) {
                handleModalDocFileSelect(this.files[0]);
            }
        });
    }

    const uploadModalEl = document.getElementById('uploadDocModal');
    if (uploadModalEl) {
        uploadModalEl.addEventListener('hidden.bs.modal', function () {
            removeModalDocFile();
        });
    }
});
</script>
<?= $this->endSection() ?>
