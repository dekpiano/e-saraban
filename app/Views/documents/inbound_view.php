<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>หนังสือรับ เลขทะเบียน <?= esc($document['internal_reg_no'] ?: $document['doc_no']) ?> | ระบบงานสารบรรณ สกจ.<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Saraban Memo CSS for official stamp styling -->
<link rel="stylesheet" href="<?= base_url('assets/css/saraban-memo.css?v=' . (file_exists(FCPATH . 'assets/css/saraban-memo.css') ? filemtime(FCPATH . 'assets/css/saraban-memo.css') : '2.0')) ?>">

<style>
/* =========================================================
   Executive UI/UX Styles for Inbound Document Viewer
   ========================================================= */
.inbound-hero-card {
    background: #ffffff;
    border-radius: 14px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 18px rgba(0, 0, 0, 0.04);
}

.document-desk {
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 20px;
    position: relative;
}

.inbound-paper-sheet {
    width: 100%;
    max-width: 21cm;
    min-height: 0;
    height: auto;
    background: #ffffff;
    box-shadow: 0 12px 35px rgba(0, 0, 0, 0.12), 0 3px 10px rgba(0, 0, 0, 0.05);
    border-radius: 4px;
    position: relative;
    margin: 0 auto;
    box-sizing: border-box;
    padding: 0 !important;
    overflow: hidden;
    line-height: 0;
    transition: transform 0.2s ease;
}

/* Quick Fact Cards */
.fact-tile {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 12px 14px;
    display: flex;
    align-items: center;
    gap: 12px;
    transition: all 0.2s ease;
}

.fact-tile:hover {
    border-color: #cbd5e1;
    box-shadow: 0 4px 12px rgba(0,0,0,0.04);
    transform: translateY(-1px);
}

.fact-icon {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
}

.fact-label {
    font-size: 11.5px;
    font-weight: 600;
    color: #64748b;
    margin-bottom: 2px;
    font-family: 'K2D', sans-serif;
}

.fact-val {
    font-size: 14px;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.25;
}

/* Preset Chips */
.preset-chip-btn {
    border: 1.5px solid #cbd5e1;
    background: #ffffff;
    color: #334155;
    font-size: 12.5px;
    font-weight: 600;
    padding: 6px 12px;
    border-radius: 20px;
    cursor: pointer;
    transition: all 0.15s ease;
    font-family: 'K2D', sans-serif;
    user-select: none;
}

.preset-chip-btn:hover {
    border-color: #696cff;
    color: #696cff;
    background: #f8fafc;
}

.preset-chip-btn.is-selected {
    border-color: #696cff;
    background: #696cff;
    color: #ffffff;
    box-shadow: 0 3px 8px rgba(105, 108, 255, 0.3);
}

/* Stepper & Route Timeline */
.route-timeline-item {
    position: relative;
    padding-left: 36px;
    padding-bottom: 22px;
}

.route-timeline-item:last-child {
    padding-bottom: 0;
}

.route-timeline-item::before {
    content: '';
    position: absolute;
    left: 13px;
    top: 26px;
    bottom: 0;
    width: 2px;
    background: #e2e8f0;
}

.route-timeline-item:last-child::before {
    display: none;
}

.route-timeline-dot {
    position: absolute;
    left: 0;
    top: 0;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    font-weight: bold;
    z-index: 2;
}

.route-timeline-dot.is-done {
    background: #22c55e;
    color: #ffffff;
    box-shadow: 0 0 0 3px #dcfce7;
}

.route-timeline-dot.is-active {
    background: #f59e0b;
    color: #ffffff;
    box-shadow: 0 0 0 4px #fef3c7;
    animation: pulse-dot 1.8s infinite;
}

.route-timeline-dot.is-pending {
    background: #e2e8f0;
    color: #64748b;
}

@keyframes pulse-dot {
    0% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.5); }
    70% { box-shadow: 0 0 0 8px rgba(245, 158, 11, 0); }
    100% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0); }
}

.endorse-card-signed {
    background: #ffffff;
    border: 1.5px solid #86efac;
    border-left: 5px solid #22c55e;
    border-radius: 10px;
    box-shadow: 0 3px 12px rgba(34, 197, 94, 0.08);
}

/* Copy Button Styling */
.btn-copy-code {
    cursor: pointer;
    padding: 2px 6px;
    font-size: 11px;
    border-radius: 4px;
    transition: background 0.15s;
}
.btn-copy-code:hover {
    background: rgba(0,0,0,0.08);
}
</style>

<div class="container-xxl flex-grow-1 container-p-y p-0">

    <?php 
    $fileExt = !empty($document['file_path']) ? strtolower(pathinfo($document['file_path'], PATHINFO_EXTENSION)) : '';
    $fileUrl = !empty($document['file_path']) ? document_url($document['file_path']) : '';
    $rawFileUrl = !empty($document['raw_file_path']) ? document_url($document['raw_file_path']) : '';
    $hasRawBackup = !empty($document['raw_file_path']);
    $signedRoutes = array_filter($routes, function($r) {
        return $r['status'] === 'signed';
    });
    $isCompleted = ($document['status'] === 'completed');
    $isSarabanOfficer = in_array((string)session()->get('role'), ['saraban', 'admin', 'super_admin'], true);
    $canEditStamp = $isSarabanOfficer && $hasRawBackup && empty($signedRoutes) && !$isCompleted;
    $hasStamp = isset($document['has_receive_stamp']) ? ((int)$document['has_receive_stamp'] === 1) : true;
    $rX = !empty($document['receive_stamp_x']) ? $document['receive_stamp_x'] : '72.2%';
    $rY = !empty($document['receive_stamp_y']) ? $document['receive_stamp_y'] : '3.5%';
    $rScale = (!empty($document['receive_stamp_scale']) && (float)$document['receive_stamp_scale'] >= 0.4) ? (float)$document['receive_stamp_scale'] : 1.0;
    ?>

    <!-- ๑. ส่วนหัวหน้าจอ (Executive Hero Header & Action Controls) -->
    <div class="inbound-hero-card p-3 p-md-4 mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div class="flex-grow-1">
                <!-- Navigation Breadcrumb & Status Badges -->
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <a href="<?= base_url('admin/inbound') ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 shadow-xs">
                        <i class="bx bx-arrow-back me-1"></i> ทะเบียนรับเข้า
                    </a>

                    <!-- เลขทะเบียนรับ สกจ. -->
                    <span class="badge bg-primary text-white fw-bold px-2.5 py-1.5 font-monospace shadow-xs d-inline-flex align-items-center gap-1" style="font-size: 13px;">
                        <i class="bx bx-bookmark"></i>
                        <span>เลขรับ: <span id="regNoCopyTarget"><?= esc($document['internal_reg_no'] ?: '-') ?></span></span>
                        <span class="btn-copy-code ms-1" onclick="copyText('<?= esc($document['internal_reg_no'] ?: '') ?>')" title="คัดลอกเลขทะเบียน">
                            <i class="bx bx-copy"></i>
                        </span>
                    </span>

                    <!-- เลขที่หนังสือต้นทาง -->
                    <?php if (!empty($document['doc_no'])): ?>
                        <span class="badge bg-dark text-white fw-bold px-2.5 py-1.5 font-monospace shadow-xs d-inline-flex align-items-center gap-1" style="font-size: 12px;">
                            <i class="bx bx-hash"></i>
                            <span>เลขที่: <?= esc($document['doc_no']) ?></span>
                            <span class="btn-copy-code ms-1" onclick="copyText('<?= esc($document['doc_no']) ?>')" title="คัดลอกเลขที่หนังสือ">
                                <i class="bx bx-copy"></i>
                            </span>
                        </span>
                    <?php endif; ?>

                    <!-- ความเร่งด่วน -->
                    <?php if ($document['urgency'] === 'most_urgent'): ?>
                        <span class="badge bg-danger text-white fw-bold px-2.5 py-1.5 shadow-xs"><i class="bx bx-alarm-exclamation me-1"></i> ด่วนที่สุด</span>
                    <?php elseif ($document['urgency'] === 'very_urgent'): ?>
                        <span class="badge bg-warning text-dark fw-bold px-2.5 py-1.5 shadow-xs"><i class="bx bx-alarm me-1"></i> ด่วนมาก</span>
                    <?php elseif ($document['urgency'] === 'urgent'): ?>
                        <span class="badge bg-warning text-dark fw-bold px-2.5 py-1.5 shadow-xs">ด่วน</span>
                    <?php endif; ?>

                    <!-- สถานะขั้นตอนการเกษียน -->
                    <?php if ($document['status'] === 'completed'): ?>
                        <span class="badge bg-success text-white fw-bold px-3 py-1.5 rounded-pill shadow-xs">
                            <i class="bx bx-check-shield me-1"></i> ผอ. สั่งการเรียบร้อยแล้ว
                        </span>
                    <?php else: ?>
                        <span class="badge bg-warning text-dark fw-bold px-3 py-1.5 rounded-pill shadow-xs">
                            <i class="bx bx-time-five me-1"></i> อยู่ระหว่างเสนอเกษียน (ขั้นที่ <?= $document['current_step'] ?>)
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Document Subject / Title -->
                <h4 class="fw-bold text-dark mb-2" style="font-family: 'K2D', sans-serif; font-size: 22px; line-height: 1.35;">
                    <?= esc($document['title']) ?>
                </h4>

                <!-- Document Key Metadata Bar -->
                <div class="d-flex flex-wrap align-items-center gap-3 text-muted small mt-1">
                    <span class="d-inline-flex align-items-center">
                        <i class="bx bx-building text-primary me-1 fs-6"></i> จาก: <strong class="text-dark ms-1"><?= esc($document['from_org']) ?></strong>
                    </span>
                    <span>•</span>
                    <span class="d-inline-flex align-items-center">
                        <i class="bx bx-send text-info me-1 fs-6"></i> ถึง: <strong class="text-dark ms-1"><?= esc($document['to_org']) ?></strong>
                    </span>
                    <span>•</span>
                    <span class="d-inline-flex align-items-center">
                        <i class="bx bx-calendar text-secondary me-1 fs-6"></i> ลงวันที่: <strong class="text-dark ms-1"><?= esc(thai_date($document['doc_date'], 'medium')) ?></strong>
                    </span>
                    <span>•</span>
                    <span class="d-inline-flex align-items-center">
                        <i class="bx bx-time text-warning me-1 fs-6"></i> รับเมื่อ: <strong class="text-dark ms-1"><?= esc(thai_date($document['receive_date'], 'medium')) ?> (<?= esc($document['receive_stamp_time'] ?: '-') ?>)</strong>
                    </span>
                </div>
            </div>

            <!-- Action Buttons Toolbar -->
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="<?= base_url('admin/inbound/' . $document['id'] . '/print') ?>" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-3 shadow-xs fw-semibold">
                    <i class="bx bx-printer me-1"></i> พิมพ์ใบสรุป/สลิปนำส่ง
                </a>

                <?php if (!empty($document['file_path'])): ?>
                    <a href="<?= $fileUrl ?>" target="_blank" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm fw-semibold">
                        <i class="bx bx-window-open me-1"></i> เปิดดูไฟล์เต็มจอ
                    </a>
                    <a href="<?= $fileUrl ?>" download class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-xs">
                        <i class="bx bx-download me-1"></i> ดาวน์โหลด
                    </a>
                <?php endif; ?>

                <?php if ($canEditStamp): ?>
                    <button type="button" class="btn btn-outline-warning btn-sm rounded-pill px-3 shadow-xs fw-bold text-dark" id="toggleStampEditBtn" onclick="toggleStampEditMode()">
                        <i class="bx bx-move me-1"></i> ปรับตำแหน่งตรายาง / สแตมป์ใหม่
                    </button>
                <?php elseif ($isCompleted): ?>
                    <span class="badge bg-light text-muted border rounded-pill px-3 py-1.5 fw-medium" title="ผอ. เกษียนอนุมัติแล้ว ระบบลบไฟล์สำรองเพื่อประหยัดพื้นที่จัดเก็บ">
                        <i class="bx bx-check-shield text-success me-1"></i> ผอ. สั่งการเสร็จสิ้น (คืนพื้นที่จัดเก็บแล้ว)
                    </span>
                <?php elseif (!empty($signedRoutes)): ?>
                    <span class="badge bg-light text-muted border rounded-pill px-3 py-1.5 fw-medium" title="เอกสารอยู่ระหว่างหรือผ่านการเกษียนแล้ว ไม่สามารถแก้ไขตรายางได้">
                        <i class="bx bx-lock-alt text-warning me-1"></i> มีการเกษียนแล้ว (ล็อคตรายาง)
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ๒. แถบสรุปข้อมูลหนังสือแบบ 4 ช่อง (Quick Facts Ribbon) -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="fact-tile h-100">
                <div class="fact-icon bg-primary text-white">
                    <i class="bx bx-bookmark"></i>
                </div>
                <div class="overflow-hidden">
                    <div class="fact-label">เลขทะเบียนรับ (สกจ.)</div>
                    <div class="fact-val font-monospace text-primary text-truncate"><?= esc($document['internal_reg_no'] ?: '-') ?></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="fact-tile h-100">
                <div class="fact-icon bg-secondary text-white">
                    <i class="bx bx-file"></i>
                </div>
                <div class="overflow-hidden">
                    <div class="fact-label">เลขที่หนังสือต้นทาง</div>
                    <div class="fact-val text-truncate"><?= esc($document['doc_no'] ?: '-') ?></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="fact-tile h-100">
                <div class="fact-icon bg-success text-white">
                    <i class="bx bx-building"></i>
                </div>
                <div class="overflow-hidden">
                    <div class="fact-label">หน่วยงานต้นทาง</div>
                    <div class="fact-val text-truncate"><?= esc($document['from_org']) ?></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="fact-tile h-100">
                <div class="fact-icon bg-info text-white">
                    <i class="bx bx-user-check"></i>
                </div>
                <div class="overflow-hidden">
                    <div class="fact-label">ผู้ลงทะเบียนรับ</div>
                    <div class="fact-val text-truncate">
                        <?= esc($document['creator_first'] ? $document['creator_first'] . ' ' . $document['creator_last'] : 'เจ้าหน้าที่สารบรรณ') ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- แถบแจ้งเตือนเมื่ออยู่ในโหมดปรับตำแหน่งตรายาง (Stamp Adjustment Banner) -->
    <div id="stampEditModeBar" class="alert alert-warning border border-warning shadow-sm py-2.5 px-3 mb-4 d-none">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-2">
                <i class="bx bx-move fs-4 text-warning"></i>
                <div>
                    <strong class="text-dark">โหมดปรับตำแหน่งตรายางรับหนังสือ (สลับแสดงไฟล์ต้นฉบับสะอาด) :</strong>
                    <span class="small text-dark opacity-90 ms-1">แตะหรือลากตรายางไปยังจุดที่ต้องการ หรือใช้ปุ่มลัดด้านขวา เมื่อกดบันทึกระบบจะประทับตราลงในเอกสารทันที</span>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-outline-dark" onclick="quickSetReceiveStamp('top-right')" title="วางมุมขวาบน">
                        <i class="bx bx-align-right me-1"></i> ขวาบน
                    </button>
                    <button type="button" class="btn btn-outline-dark" onclick="quickSetReceiveStamp('top-left')" title="วางมุมซ้ายบน">
                        <i class="bx bx-align-left me-1"></i> ซ้ายบน
                    </button>
                </div>
                <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-outline-dark" onclick="adjustReceiveStampScale(-0.05)" title="ย่อขนาด">
                        <i class="bx bx-minus"></i> ย่อ
                    </button>
                    <span class="btn btn-light border text-dark fw-bold px-2.5" id="currentStampScaleText"><?= round($rScale * 100) ?>%</span>
                    <button type="button" class="btn btn-outline-dark" onclick="adjustReceiveStampScale(0.05)" title="ขยายขนาด">
                        <i class="bx bx-plus"></i> ขยาย
                    </button>
                </div>
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="resetReceiveStampPosition()">
                    <i class="bx bx-reset"></i> รีเซ็ต
                </button>
                <button type="button" class="btn btn-success btn-sm fw-bold px-3 shadow-xs" id="btnSaveStampPosition" onclick="saveReceiveStampPosition()">
                    <i class="bx bx-save me-1"></i> บันทึกและประทับตราใหม่
                </button>
                <button type="button" class="btn btn-light btn-sm" onclick="toggleStampEditMode(false)">
                    ปิด
                </button>
            </div>
        </div>
    </div>

    <!-- ๓. พื้นที่หลัก 2 คอลัมน์ (Desktop 8 : 4) -->
    <div class="row g-4">
        
        <!-- คอลัมน์ซ้าย (8 ส่วน): โต๊ะตรวจอ่านเอกสาร A4 เสมือนจริง & บันทึกเกษียนที่ลงนามแล้ว -->
        <div class="col-lg-8">

            <!-- โต๊ะเอกสาร & ตัวควบคุม A4 (Interactive Document Desk) -->
            <div class="document-desk mb-4">
                
                <!-- แถบควบคุม PDF / เอกสารด้านบน (Zoom, Fit, Page navigation) -->
                <div class="d-flex flex-wrap align-items-center justify-content-between bg-white px-3 py-2 border rounded-3 mb-3 shadow-xs gap-2">
                    
                    <!-- Multi-page controls -->
                    <div class="d-flex align-items-center gap-1.5 flex-wrap" id="viewPdfPagination">
                        <button type="button" class="btn btn-outline-secondary btn-sm py-1 px-2.5 fw-bold" id="viewPdfPrevBtn" onclick="changeViewPdfPage(-1)">
                            <i class="bx bx-chevron-left me-1"></i> หน้าก่อน
                        </button>
                        <span class="badge bg-primary text-white border px-3 py-1.5 fw-bold fs-6" id="viewPdfPageDisplay">หน้า ๑ / ๑</span>
                        <button type="button" class="btn btn-outline-secondary btn-sm py-1 px-2.5 fw-bold" id="viewPdfNextBtn" onclick="changeViewPdfPage(1)">
                            ถัดไป <i class="bx bx-chevron-right ms-1"></i>
                        </button>
                        <select id="viewPdfJumpSelect" class="form-select form-select-sm d-inline-block w-auto py-0 px-2 fw-bold text-dark border-primary" style="height: 30px; font-size: 12px;" onchange="jumpToViewPdfPage(parseInt(this.value))">
                            <option value="1">หน้า ๑</option>
                        </select>
                        <span class="badge bg-light text-dark border px-2 py-1 fw-bold" id="viewPdfTotalBadge">
                            <i class="bx bx-file text-primary me-1"></i> เอกสารทั้งหมด <span id="viewPdfTotalCount">๑</span> หน้า
                        </span>
                    </div>

                    <!-- Direct Open Full PDF & Zoom Controls -->
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <?php if (!empty($document['file_path'])): ?>
                            <a href="<?= $fileUrl ?>" target="_blank" class="btn btn-outline-primary btn-sm fw-semibold" title="เปิดดูไฟล์ต้นฉบับเต็มจอทุกหน้า">
                                <i class="bx bx-window-open me-1"></i> ดูไฟล์เต็มจอ
                            </a>
                        <?php endif; ?>

                        <div class="d-flex align-items-center gap-1">
                            <button type="button" class="btn btn-outline-secondary btn-sm p-1 px-2" onclick="zoomDocDesk(-0.1)" title="ย่อขนาดกระดาษ">
                                <i class="bx bx-zoom-out"></i>
                            </button>
                            <span class="small fw-bold px-2 text-dark" id="docDeskZoomDisplay">100%</span>
                            <button type="button" class="btn btn-outline-secondary btn-sm p-1 px-2" onclick="zoomDocDesk(0.1)" title="ขยายขนาดกระดาษ">
                                <i class="bx bx-zoom-in"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm p-1 px-2 ms-1" onclick="resetDocDeskZoom()" title="รีเซ็ตขนาด 100%">
                                <i class="bx bx-reset"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Stamp Notice Pill -->
                    <div>
                        <?php if ($hasStamp): ?>
                            <span class="badge bg-label-primary text-primary fw-bold" style="font-size: 11.5px;">
                                <i class="bx bx-stamp me-1"></i> ประทับตรายางรับหนังสือที่หน้าแรก
                            </span>
                        <?php else: ?>
                            <span class="badge bg-label-secondary text-secondary fw-bold" style="font-size: 11.5px;">
                                <i class="bx bx-check me-1"></i> ลงทะเบียนรับแบบไม่สแตมป์
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- แจ้งเตือนเมื่อตรวจอ่านหน้าที่ ๒ เป็นต้นไป -->
                <div id="viewPdfNonFirstNotice" class="alert alert-info py-2 px-3 mb-2 small d-none shadow-xs border-info text-dark" style="background-color: #f0f9ff;">
                    <i class="bx bx-info-circle text-primary me-1 fs-6"></i>
                    <span>กำลังตรวจอ่าน <strong>หน้า <span id="viewNoticeCurrentPage">๒</span></strong> จากเอกสารทั้งหมด <strong id="viewNoticeTotalPages">๑</strong> หน้า (ตรายางรับหนังสือและการเกษียนสั่งการจัดแสดงไว้ที่หน้าแรก)</span>
                </div>

                <!-- แผ่นกระดาษ A4 เสมือนจริง (Physical A4 Stamped Sheet) -->
                <div class="d-flex justify-content-center align-items-start overflow-auto position-relative py-2" id="docDeskScrollContainer">
                    <div id="viewPaperWrapper" class="inbound-paper-sheet">
                        
                        <!-- ตรายางรับหนังสือราชการแบบประทับถาวร (Official Rubber Stamp) -->
                        <?php 
                        $isPermanentlyImprinted = !empty($document['file_path']) && (str_starts_with($document['file_path'], 'saraban/') || file_exists(FCPATH . $document['file_path']));
                        $showStampDirectly = $hasStamp && !$isPermanentlyImprinted;
                        ?>
                        <?php if ($canEditStamp || $showStampDirectly): ?>
                            <div id="issuedReceiveStamp" class="saraban-receive-stamp-img-wrap shadow-xs <?= ($showStampDirectly ? '' : 'd-none') ?>" 
                                 style="left: <?= esc($rX) ?>; top: <?= esc($rY) ?>; width: 23.8%; aspect-ratio: 452 / 227; height: auto; transform: rotate(-1.2deg) scale(<?= $rScale ?>); transform-origin: top left; position: absolute; z-index: 50; display: <?= ($showStampDirectly ? 'block' : 'none') ?>; cursor: grab;">
                                <div style="position: relative; width: 100%; height: 100%; background: #ffffff; border-radius: 1px; overflow: hidden; box-shadow: 0 4px 12px rgba(20,42,130,0.18);">
                                    <img src="<?= base_url('assets/img/stamp_template_skj.png') ?>" alt="ตรายาง รร.ส.ก.จ." style="width: 100%; height: 100%; display: block; object-fit: fill; pointer-events: none;">
                                    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; font-family: 'TH SarabunIT๙', 'TH Sarabun New', 'Sarabun', sans-serif; font-weight: bold; color: #142a82; line-height: 1;">
                                        <span style="position: absolute; left: 27%; top: 43%; transform: translateY(-82%); font-size: 1.3vw; min-font-size: 13pt; max-font-size: 17pt; white-space: nowrap;"><?= esc(to_thai_number($document['internal_reg_no'] ?: '-')) ?></span>
                                        <span style="position: absolute; left: 25.2%; top: 66.3%; transform: translateY(-82%); font-size: 1.3vw; min-font-size: 13pt; max-font-size: 17pt; white-space: nowrap;"><?= esc(to_thai_number(thai_date($document['receive_date'] ?? date('Y-m-d'), 'medium'))) ?></span>
                                        <span style="position: absolute; left: 24%; top: 89.6%; transform: translateY(-82%); font-size: 1.3vw; min-font-size: 13pt; max-font-size: 17pt; white-space: nowrap;"><?= esc(to_thai_number($document['receive_stamp_time'] ?: date('H:i') . ' น.')) ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- ตรายางเกษียนหนังสือที่ลงนามแล้ว (Official Signed Endorsements: Realistic Blue Rubber Stamps เหมือนระบบบันทึกข้อความ) -->
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
                            <div class="saraban-blue-stamp saraban-endorse-stamp shadow-sm" style="left: <?= esc($posX) ?>; top: <?= esc($posY) ?>; <?= $transformStyle ?> <?= $widthStyle ?> position: absolute; z-index: 55;">
                                <div class="stamp-preset"><?= esc($sr['comment_preset']) ?></div>
                                <?php if (!empty($sr['comment_text'])): ?>
                                    <div class="stamp-comment">"<?= esc($sr['comment_text']) ?>"</div>
                                <?php endif; ?>
                                <div class="stamp-sig-wrap">
                                    <?php if (!empty($sr['signature_img'])): ?>
                                        <img src="<?= base_url($sr['signature_img']) ?>" alt="ลายเซ็น" class="stamp-sig-img">
                                    <?php endif; ?>
                                </div>
                                <div class="stamp-signer-name">( <?= esc($sr['approver_name'] ?? '') ?> )</div>
                                <div class="stamp-signer-pos"><?= esc($sr['personnel_position'] ?? '') ?></div>
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

                        <!-- PDF Canvas Render / Image Fallback -->
                        <?php if (!empty($document['file_path'])): ?>
                            <?php if ($fileExt === 'pdf'): ?>
                                <canvas id="viewPdfCanvas" class="w-100" style="display: block; width: 100%; height: auto;"></canvas>
                                <iframe id="viewPdfFallbackIframe" src="<?= $fileUrl ?>" class="w-100 d-none" style="min-height: 950px; border: none;"></iframe>
                            <?php else: ?>
                                <img src="<?= $fileUrl ?>" alt="เอกสารรับเข้า" class="w-100" style="display: block; width: 100%; height: auto; object-fit: contain;">
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <div class="avatar avatar-xl bg-label-secondary mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle" style="width: 72px; height: 72px; margin: 0 auto;">
                                    <i class="bx bx-file-blank fs-1 text-muted"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1">ไม่มีไฟล์เอกสารแนบ</h6>
                                <p class="text-muted small">หนังสือรับรายการนี้ไม่มีไฟล์สแกนต้นฉบับแนบมาในระบบ</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- รายการเกษียนสั่งการที่ลงนามแล้ว (Official Signed Endorsements) -->
            <?php 
            $signedRoutes = array_filter($routes, function($r) {
                return $r['status'] === 'signed';
            });
            ?>
            <?php if (!empty($signedRoutes)): ?>
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bx bx-check-shield text-success fs-4"></i>
                            <h6 class="card-title text-dark mb-0 fw-bold" style="font-family: 'K2D', sans-serif;">
                                บันทึกการเกษียน & คำสั่งการของผู้บริหาร
                            </h6>
                        </div>
                        <span class="badge bg-success text-white fw-bold px-2.5 py-1">
                            <?= count($signedRoutes) ?> ลำดับการสั่งการ
                        </span>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex flex-column gap-3">
                            <?php foreach ($signedRoutes as $sr): ?>
                                <div class="endorse-card-signed p-3.5 p-md-4">
                                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2 pb-2 border-bottom">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-success text-white rounded-circle p-1">
                                                <i class="bx bx-check"></i>
                                            </span>
                                            <div>
                                                <strong class="text-dark fs-6">ขั้นที่ <?= $sr['step_order'] ?>: <?= esc($sr['approver_name']) ?></strong>
                                                <small class="text-muted d-block"><?= esc($sr['personnel_position']) ?></small>
                                            </div>
                                        </div>
                                        <span class="badge bg-light text-dark border px-2.5 py-1">
                                            <i class="bx bx-calendar me-1"></i> ลงนามเมื่อ <?= thai_date($sr['signed_at'], 'medium') ?>
                                        </span>
                                    </div>

                                    <div class="mt-2">
                                        <div class="fw-bold text-primary fs-6 mb-1">
                                            <i class="bx bx-message-square-dots me-1"></i> "<?= esc($sr['comment_preset']) ?>"
                                        </div>
                                        <?php if (!empty($sr['comment_text'])): ?>
                                            <div class="text-dark small p-2.5 rounded bg-light border mb-2">
                                                <?= nl2br(esc($sr['comment_text'])) ?>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (!empty($sr['signature_img'])): ?>
                                            <div class="mt-2 pt-2 border-top d-flex align-items-center justify-content-between">
                                                <img src="<?= base_url($sr['signature_img']) ?>" alt="ลายเซ็น" style="height: 48px; max-width: 160px; object-fit: contain;">
                                                <span class="badge bg-label-success text-success small">
                                                    <i class="bx bx-check-double me-1"></i> ลายมือชื่ออิเล็กทรอนิกส์ยืนยันแล้ว
                                                </span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </div>

        <!-- คอลัมน์ขวา (4 ส่วน): การสั่งการ & มอบหมายงาน, ฟอร์มลงนาม, เส้นทางเดินแฟ้ม, Audit Trail -->
        <div class="col-lg-4">

            <!-- การ์ดผลการสั่งการ & มอบหมายงาน (เมื่อ ผอ. สั่งการเสร็จสิ้นแล้ว) -->
            <?php if ($document['status'] === 'completed'): ?>
                <div class="card shadow-sm border-2 border-success mb-4">
                    <div class="card-header bg-success text-white py-3">
                        <h6 class="card-title text-white mb-0 fw-bold" style="font-family: 'K2D', sans-serif;">
                            <i class="bx bx-check-double me-1"></i> ข้อสั่งการของผู้อำนวยการโรงเรียน
                        </h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">ข้อความสั่งการ :</small>
                            <div class="p-3 rounded bg-light border text-dark fw-bold" style="font-size: 14.5px;">
                                "<?= esc($document['executive_instruction'] ?: 'ทราบ / ดำเนินการตามเสนอ') ?>"
                            </div>
                        </div>

                        <?php if (!empty($document['assigned_first']) || !empty($document['assigned_department'])): ?>
                            <div class="p-3 rounded border border-primary mb-3" style="background-color: #f0f7ff !important;">
                                <small class="text-muted d-block mb-1 font-semibold"><i class="bx bx-user-pin text-primary me-1"></i> ผู้รับผิดชอบงานที่ได้รับมอบหมาย :</small>
                                <?php if (!empty($document['assigned_first'])): ?>
                                    <strong class="text-primary d-block fs-6">
                                        <?= esc(($document['assigned_prefix'] ?? '') . $document['assigned_first'] . ' ' . $document['assigned_last']) ?>
                                    </strong>
                                <?php endif; ?>
                                <?php if (!empty($document['assigned_department'])): ?>
                                    <span class="badge bg-primary text-white mt-1">
                                        <?= esc($document['assigned_department']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <div class="alert alert-secondary py-2 px-3 small mb-0 d-flex align-items-center">
                            <i class="bx bx-check-circle text-success fs-5 me-2 flex-shrink-0"></i>
                            <div>ระบบได้กระจายเรื่องและแจ้งเตือนไปยังผู้รับผิดชอบเรียบร้อยแล้ว</div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- กล่องสำหรับลงนามเกษียนสั่งการ (Signing Box สำหรับผู้บริหาร) -->
            <?php if ($canSign && $currentRoute): ?>
                <div class="card shadow-sm border-2 border-warning mb-4">
                    <div class="card-header bg-warning text-dark py-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bx bx-edit fs-4 text-dark"></i>
                            <div>
                                <h6 class="card-title text-dark mb-0 fw-bold" style="font-family: 'K2D', sans-serif;">
                                    ถึงลำดับการเกษียน / สั่งการของคุณ
                                </h6>
                                <small class="text-dark opacity-90">ขั้นตอนที่ <?= $currentRoute['step_order'] ?>: <?= esc($currentRoute['approver_name']) ?></small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <form action="<?= base_url('admin/inbound/' . $document['id'] . '/endorse') ?>" method="POST" id="endorseForm">
                            <?= csrf_field() ?>
                            <input type="hidden" name="signature_type" id="signatureType" value="preset">
                            <input type="hidden" name="signature_data" id="signatureData">

                            <!-- ชิปเลือกคำสั่งการด่วน (Preset Decision Chips) -->
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-dark mb-2">
                                    <i class="bx bx-message-dots text-primary me-1"></i> เลือกคำสั่งการ / ข้อความเกษียน
                                </label>
                                <div class="d-flex flex-wrap gap-1.5 mb-2">
                                    <button type="button" class="preset-chip-btn is-selected" onclick="selectPresetChip(this, 'มอบหมายดำเนินการ')">
                                        มอบหมายดำเนินการ
                                    </button>
                                    <button type="button" class="preset-chip-btn" onclick="selectPresetChip(this, 'ทราบ / ถือปฏิบัติตาม')">
                                        ทราบ / ถือปฏิบัติตาม
                                    </button>
                                    <button type="button" class="preset-chip-btn" onclick="selectPresetChip(this, 'เพื่อโปรดพิจารณา')">
                                        เพื่อโปรดพิจารณา
                                    </button>
                                    <button type="button" class="preset-chip-btn" onclick="selectPresetChip(this, 'เห็นควรอนุมัติ / อนุญาต')">
                                        เห็นควรอนุมัติ / อนุญาต
                                    </button>
                                    <button type="button" class="preset-chip-btn" onclick="selectPresetChip(this, 'ดำเนินการตามระเบียบ')">
                                        ดำเนินการตามระเบียบ
                                    </button>
                                </div>
                                <input type="hidden" name="comment_preset" id="commentPresetInput" value="มอบหมายดำเนินการ">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small text-dark mb-1">ข้อความสั่งการเพิ่มเติม (ถ้ามี)</label>
                                <textarea class="form-control" name="comment_text" rows="2" placeholder="ระบุข้อความสั่งการ หรือข้อสั่งการพิเศษ..."></textarea>
                            </div>

                            <!-- ตัวเลือกมอบหมายงาน (Task Delegation / Assignment) -->
                            <div class="bg-light p-3 rounded-3 border mb-3">
                                <label class="form-label fw-bold small text-primary mb-2">
                                    <i class="bx bx-user-plus me-1"></i> มอบหมายงานให้ผู้ปฏิบัติ (สั่งการ)
                                </label>
                                <div class="mb-2">
                                    <label class="small text-muted mb-1 d-block">มอบหมายกลุ่มงาน/กลุ่มบริหาร</label>
                                    <select name="assigned_department" class="form-select form-select-sm">
                                        <option value="">-- ไม่ระบุกลุ่มงาน --</option>
                                        <?php foreach ($departments as $dept): ?>
                                            <option value="<?= esc($dept['name']) ?>"><?= esc($dept['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div>
                                    <label class="small text-muted mb-1 d-block">หรือระบุชื่อคุณครูผู้รับผิดชอบ</label>
                                    <select name="assigned_to_user_id" class="form-select form-select-sm">
                                        <option value="">-- ไม่ระบุชื่อครูเจาะจง --</option>
                                        <?php foreach ($personnelList as $p): ?>
                                            <?php if (!empty($p['user_id'])): ?>
                                                <option value="<?= esc($p['user_id']) ?>">
                                                    <?= esc($p['full_name']) ?> (<?= esc($p['position_name'] ?? 'ครู') ?>)
                                                </option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success w-100 fw-bold py-2.5 shadow-sm fs-6">
                                <i class="bx bx-check-circle me-1"></i> ลงนามเกษียน & สั่งการทันที
                            </button>
                        </form>
                    </div>
                </div>
            <?php elseif ($document['status'] === 'routing'): ?>
                <?php 
                $pendingStepRoute = null;
                foreach ($routes as $r) {
                    if ($r['step_order'] == $document['current_step'] && $r['status'] === 'pending') {
                        $pendingStepRoute = $r;
                        break;
                    }
                }
                ?>
                <div class="card shadow-sm border-0 mb-4" style="background: #ffffff; border: 1px solid #e2e8f0;">
                    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bx bx-time-five text-warning fs-4"></i>
                            <h6 class="card-title text-dark mb-0 fw-bold" style="font-family: 'K2D', sans-serif;">
                                สถานะการเสนอเกษียนหนังสือ
                            </h6>
                        </div>
                        <span class="badge bg-warning text-dark fw-bold px-2.5 py-1">รอผู้บริหารสั่งการ</span>
                    </div>
                    <div class="card-body p-3">
                        <div class="p-3 rounded-3 bg-light border mb-3">
                            <small class="text-muted d-block mb-1 font-semibold">
                                <i class="bx bx-user text-primary me-1"></i> อยู่ระหว่างรอการเกษียนสั่งการจาก :
                            </small>
                            <div class="fw-bold text-dark fs-6">
                                <?= esc($pendingStepRoute['approver_name'] ?? 'ผู้อำนวยการสถานศึกษา') ?>
                            </div>
                            <div class="text-muted small">
                                <?= esc($pendingStepRoute['personnel_position'] ?? 'ผู้อำนวยการโรงเรียน') ?>
                            </div>
                        </div>

                        <div class="alert alert-light border py-2.5 px-3 small text-muted mb-0 d-flex align-items-start gap-2">
                            <i class="bx bx-info-circle text-primary fs-5 mt-0.5 flex-shrink-0"></i>
                            <div>
                                <strong>โหมดดูข้อมูล (View Only) :</strong> บัญชีของท่าน (เจ้าหน้าที่สารบรรณ / ครู) สามารถตรวจดูเอกสารและติดตามความคืบหน้าได้ เมื่อผู้บริหารเข้าสู่ระบบจะสามารถลงนามเกษียนและมอบหมายงานได้ทันที
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- การ์ดเส้นทางการเดินแฟ้มเกษียน (Routing Stepper Timeline) -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                    <h6 class="card-title mb-0 fw-bold text-dark" style="font-family: 'K2D', sans-serif;">
                        <i class="bx bx-git-branch me-1 text-primary"></i> เส้นทางการเดินแฟ้มเกษียน
                    </h6>
                    <span class="badge bg-light text-dark border">
                        <?= count($routes) ?> ลำดับ
                    </span>
                </div>
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex flex-column">
                        <?php foreach ($routes as $r): ?>
                            <?php 
                            $isDone = ($r['status'] === 'signed');
                            $isActive = ($r['status'] === 'pending' && $r['step_order'] == $document['current_step'] && $document['status'] === 'routing');
                            ?>
                            <div class="route-timeline-item">
                                <div class="route-timeline-dot <?= $isDone ? 'is-done' : ($isActive ? 'is-active' : 'is-pending') ?>">
                                    <?php if ($isDone): ?>
                                        <i class="bx bx-check"></i>
                                    <?php elseif ($isActive): ?>
                                        <i class="bx bx-time"></i>
                                    <?php else: ?>
                                        <span><?= $r['step_order'] ?></span>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <strong class="text-dark small">ขั้นที่ <?= $r['step_order'] ?>: <?= esc($r['approver_name']) ?></strong>
                                        <?php if ($isDone): ?>
                                            <span class="badge bg-success text-white" style="font-size: 10px;">สั่งการแล้ว</span>
                                        <?php elseif ($isActive): ?>
                                            <span class="badge bg-warning text-dark fw-bold" style="font-size: 10px;">รอสั่งการ</span>
                                        <?php else: ?>
                                            <span class="badge bg-light text-muted border" style="font-size: 10px;">รอดำเนินการ</span>
                                        <?php endif; ?>
                                    </div>
                                    <small class="text-muted d-block"><?= esc($r['personnel_position']) ?></small>
                                    <?php if ($isDone): ?>
                                        <div class="mt-1 small text-success fw-semibold" style="font-size: 11px;">
                                            <i class="bx bx-check-circle me-1"></i> เมื่อ <?= thai_date($r['signed_at'], 'short') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- การ์ดประวัติกิจกรรม (Audit Trail) -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                    <h6 class="card-title mb-0 fw-bold text-dark" style="font-family: 'K2D', sans-serif;">
                        <i class="bx bx-history me-1 text-primary"></i> ประวัติกิจกรรม (Audit Trail)
                    </h6>
                    <span class="badge bg-light text-muted border"><?= count($logs) ?></span>
                </div>
                <div class="card-body p-3">
                    <div class="small">
                        <?php if (empty($logs)): ?>
                            <div class="text-muted text-center py-3">ไม่มีประวัติกิจกรรม</div>
                        <?php else: ?>
                            <div class="d-flex flex-column gap-2" style="max-height: 380px; overflow-y: auto;">
                                <?php foreach ($logs as $log): ?>
                                    <div class="p-2.5 rounded bg-light border">
                                        <div class="fw-semibold text-dark"><?= esc($log['action']) ?></div>
                                        <div class="text-muted small my-0.5"><?= esc($log['detail']) ?></div>
                                        <div class="text-muted" style="font-size: 11px;">
                                            <i class="bx bx-user me-1"></i><?= esc(($log['prefix'] ?? '') . ($log['first_name'] ?? '') . ' ' . ($log['last_name'] ?? '')) ?> 
                                            <span class="mx-1">•</span>
                                            <i class="bx bx-time me-1"></i><?= thai_date($log['created_at'], 'medium') ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

<!-- PDF.js Library & Render Script -->
<script src="<?= base_url('assets/vendor/libs/pdfjs/pdf.min.js') ?>"></script>
<script>
    if (typeof pdfjsLib !== 'undefined') {
        pdfjsLib.GlobalWorkerOptions.workerSrc = "<?= base_url('assets/vendor/libs/pdfjs/pdf.worker.min.js') ?>";
    }

    let viewPdfDoc = null;
    let viewPdfPageNum = 1;
    let viewPdfTotalPages = 1;
    let docDeskZoom = 1.0;

    // Stamp Dragging & Scaling State
    let isStampEditMode = false;
    let stampCurrentScale = <?= (float)$rScale ?>;
    let isStampDragging = false;
    let stampStartX, stampStartY, stampInitialLeft, stampInitialTop;

    const stampedPdfUrl = "<?= $fileUrl ?>";
    const rawPdfUrl = "<?= $rawFileUrl ?>";

    function loadPdfDocument(targetUrl) {
        if (typeof pdfjsLib === 'undefined' || !targetUrl) return;
        return pdfjsLib.getDocument(targetUrl).promise.then(function(pdf) {
            viewPdfDoc = pdf;
            viewPdfTotalPages = pdf.numPages;
            viewPdfPageNum = 1;

            if (viewPdfTotalPages > 1) {
                const pagBar = document.getElementById('viewPdfPagination');
                if (pagBar) pagBar.classList.remove('d-none');
                updateViewPdfPageDisplay();
            } else {
                const pagBar = document.getElementById('viewPdfPagination');
                if (pagBar) pagBar.classList.add('d-none');
            }
            renderViewPdfPage();
        }).catch(function(err) {
            console.warn('PDF view render fallback:', err);
            const fb = document.getElementById('viewPdfFallbackIframe');
            const cv = document.getElementById('viewPdfCanvas');
            if (fb) {
                fb.classList.remove('d-none');
                fb.style.display = 'block';
            }
            if (cv) cv.classList.add('d-none');
        });
    }

    <?php if (!empty($document['file_path']) && $fileExt === 'pdf'): ?>
    document.addEventListener('DOMContentLoaded', function() {
        loadPdfDocument(stampedPdfUrl);
    });

    function renderViewPdfPage() {
        if (!viewPdfDoc) return;
        const canvas = document.getElementById('viewPdfCanvas');
        const stamp = document.getElementById('issuedReceiveStamp');
        const fallback = document.getElementById('viewPdfFallbackIframe');
        if (!canvas) return;

        viewPdfDoc.getPage(viewPdfPageNum).then(function(page) {
            const wrapper = document.getElementById('viewPaperWrapper');
            const unscaledViewport = page.getViewport({ scale: 1.0 });
            const isLandscape = unscaledViewport.width > unscaledViewport.height;
            if (wrapper) {
                wrapper.style.maxWidth = isLandscape ? '29.7cm' : '21cm';
                wrapper.style.aspectRatio = `${unscaledViewport.width} / ${unscaledViewport.height}`;
                wrapper.style.minHeight = 'auto';
            }

            const containerWidth = (wrapper && wrapper.clientWidth > 0) ? wrapper.clientWidth : (isLandscape ? 1000 : 760);
            const scale = (containerWidth / unscaledViewport.width) * 1.5;
            const viewport = page.getViewport({ scale: scale });

            const context = canvas.getContext('2d');
            canvas.height = viewport.height;
            canvas.width = viewport.width;

            page.render({ canvasContext: context, viewport: viewport }).promise.then(function() {
                canvas.classList.remove('d-none');
                canvas.style.display = 'block';

                // จัดการแสดงผลตรายางและลายเซ็นต์เฉพาะหน้าแรก
                if (stamp) {
                    stamp.style.display = (viewPdfPageNum === 1) ? 'block' : 'none';
                }
                const endorseStamps = document.querySelectorAll('.saraban-endorse-stamp');
                endorseStamps.forEach(function(el) {
                    el.style.display = (viewPdfPageNum === 1) ? 'block' : 'none';
                });
            }).catch(function(renderErr) {
                console.warn('Canvas render error, fallback to iframe:', renderErr);
                if (fallback) {
                    fallback.classList.remove('d-none');
                    fallback.style.display = 'block';
                }
            });
        });
    }

    function toThaiDigits(num) {
        const thai = ['๐', '๑', '๒', '๓', '๔', '๕', '๖', '๗', '๘', '๙'];
        return String(num).replace(/[0-9]/g, d => thai[d]);
    }

    function changeViewPdfPage(delta) {
        if (!viewPdfDoc || viewPdfTotalPages <= 1) return;
        const newPage = viewPdfPageNum + delta;
        if (newPage >= 1 && newPage <= viewPdfTotalPages) {
            viewPdfPageNum = newPage;
            updateViewPdfPageDisplay();
            renderViewPdfPage();
        }
    }

    function jumpToViewPdfPage(pageNum) {
        if (!viewPdfDoc || pageNum < 1 || pageNum > viewPdfTotalPages) return;
        viewPdfPageNum = pageNum;
        updateViewPdfPageDisplay();
        renderViewPdfPage();
    }

    function updateViewPdfPageDisplay() {
        const display = document.getElementById('viewPdfPageDisplay');
        const prevBtn = document.getElementById('viewPdfPrevBtn');
        const nextBtn = document.getElementById('viewPdfNextBtn');
        const jumpSelect = document.getElementById('viewPdfJumpSelect');
        const totalCount = document.getElementById('viewPdfTotalCount');
        const totalBadge = document.getElementById('viewPdfTotalBadge');
        const noticeEl = document.getElementById('viewPdfNonFirstNotice');
        const noticeCur = document.getElementById('viewNoticeCurrentPage');
        const noticeTot = document.getElementById('viewNoticeTotalPages');

        if (display) display.textContent = `หน้า ${toThaiDigits(viewPdfPageNum)} / ${toThaiDigits(viewPdfTotalPages)}`;
        if (prevBtn) prevBtn.disabled = (viewPdfPageNum <= 1);
        if (nextBtn) nextBtn.disabled = (viewPdfPageNum >= viewPdfTotalPages);

        if (totalCount) totalCount.textContent = toThaiDigits(viewPdfTotalPages);
        if (totalBadge) totalBadge.classList.toggle('d-none', viewPdfTotalPages <= 1);

        if (jumpSelect) {
            if (jumpSelect.options.length !== viewPdfTotalPages) {
                jumpSelect.innerHTML = '';
                for (let i = 1; i <= viewPdfTotalPages; i++) {
                    const opt = document.createElement('option');
                    opt.value = i;
                    opt.textContent = `หน้า ${toThaiDigits(i)}`;
                    jumpSelect.appendChild(opt);
                }
            }
            jumpSelect.value = viewPdfPageNum;
            jumpSelect.style.display = viewPdfTotalPages > 1 ? 'inline-block' : 'none';
        }

        if (noticeEl) {
            if (viewPdfPageNum > 1) {
                if (noticeCur) noticeCur.textContent = toThaiDigits(viewPdfPageNum);
                if (noticeTot) noticeTot.textContent = toThaiDigits(viewPdfTotalPages);
                noticeEl.classList.remove('d-none');
            } else {
                noticeEl.classList.add('d-none');
            }
        }
    }
    <?php endif; ?>

    // =========================================================
    // Zoom Desk Controls
    // =========================================================
    function zoomDocDesk(delta) {
        docDeskZoom = Math.min(Math.max(0.6, docDeskZoom + delta), 1.6);
        applyDocDeskZoom();
    }

    function resetDocDeskZoom() {
        docDeskZoom = 1.0;
        applyDocDeskZoom();
    }

    function applyDocDeskZoom() {
        const paper = document.getElementById('viewPaperWrapper');
        const zoomText = document.getElementById('docDeskZoomDisplay');
        if (paper) {
            paper.style.transform = `scale(${docDeskZoom})`;
            paper.style.transformOrigin = 'top center';
        }
        if (zoomText) {
            zoomText.textContent = `${Math.round(docDeskZoom * 100)}%`;
        }
    }

    // =========================================================
    // Preset Chip Selection
    // =========================================================
    function selectPresetChip(btn, text) {
        document.querySelectorAll('.preset-chip-btn').forEach(b => b.classList.remove('is-selected'));
        btn.classList.add('is-selected');
        const input = document.getElementById('commentPresetInput');
        if (input) input.value = text;
    }

    // =========================================================
    // Copy Text to Clipboard with SweetAlert Toast
    // =========================================================
    function copyText(text) {
        if (!text) return;
        navigator.clipboard.writeText(text).then(function() {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: `คัดลอก "${text}" เรียบร้อยแล้ว`,
                    showConfirmButton: false,
                    timer: 1800
                });
            } else {
                alert(`คัดลอก "${text}" เรียบร้อยแล้ว`);
            }
        });
    }

    // =========================================================
    // Stamp Edit & Repositioning Mode (ปรับตำแหน่งตรายาง / สแตมป์ใหม่)
    // =========================================================
    function toggleStampEditMode(forcedState) {
        const bar = document.getElementById('stampEditModeBar');
        const stamp = document.getElementById('issuedReceiveStamp');
        const btn = document.getElementById('toggleStampEditBtn');

        isStampEditMode = (typeof forcedState === 'boolean') ? forcedState : !isStampEditMode;

        if (isStampEditMode) {
            if (bar) bar.classList.remove('d-none');
            if (btn) {
                btn.classList.replace('btn-outline-warning', 'btn-warning');
                btn.innerHTML = `<i class="bx bx-x me-1"></i> ยกเลิกการปรับตรายาง`;
            }
            if (stamp) {
                stamp.classList.remove('d-none');
                stamp.style.display = 'block';
                stamp.classList.add('is-draggable');
                initStampDraggable(stamp);
                updateStampTransform();
            }
            // สลับการแสดงผลหน้ากระดาษเป็นไฟล์ต้นฉบับสะอาด (un-stamped raw PDF)
            if (rawPdfUrl) {
                loadPdfDocument(rawPdfUrl);
            }
        } else {
            if (bar) bar.classList.add('d-none');
            if (btn) {
                btn.classList.replace('btn-warning', 'btn-outline-warning');
                btn.innerHTML = `<i class="bx bx-move me-1"></i> ปรับตำแหน่งตรายาง / สแตมป์ใหม่`;
            }
            if (stamp) {
                stamp.classList.remove('is-draggable');
                <?php if ($isPermanentlyImprinted): ?>
                stamp.classList.add('d-none');
                stamp.style.display = 'none';
                <?php endif; ?>
            }
            // สลับกลับมาแสดงไฟล์ที่มีตรายางประทับแล้ว
            if (stampedPdfUrl) {
                loadPdfDocument(stampedPdfUrl);
            }
        }
    }

    function quickSetReceiveStamp(pos) {
        const stamp = document.getElementById('issuedReceiveStamp');
        const wrapper = document.getElementById('viewPaperWrapper');
        if (!stamp || !wrapper) return;

        const baseWPct = 23.8;
        const currentWPct = baseWPct * stampCurrentScale;
        let xPct = Math.max(1.0, (100.0 - currentWPct - 4.0)).toFixed(2) + '%';
        let yPct = '3.50%';

        if (pos === 'top-left') {
            xPct = '4.00%';
            yPct = '3.50%';
        }

        stamp.style.left = xPct;
        stamp.style.top = yPct;
    }

    function adjustReceiveStampScale(delta) {
        stampCurrentScale = Math.min(Math.max(0.4, stampCurrentScale + delta), 2.0);
        updateStampTransform();
    }

    function resetReceiveStampPosition() {
        stampCurrentScale = 1.0;
        updateStampTransform();
        quickSetReceiveStamp('top-right');
    }

    function toThaiDigits(num) {
        const thai = ['๐', '๑', '๒', '๓', '๔', '๕', '๖', '๗', '๘', '๙'];
        return String(num).replace(/[0-9]/g, d => thai[d]);
    }

    function updateStampTransform() {
        const stamp = document.getElementById('issuedReceiveStamp');
        const txt = document.getElementById('currentStampScaleText');
        if (stamp) {
            stamp.style.transform = `rotate(-1.2deg) scale(${stampCurrentScale})`;
        }
        const pct = `${toThaiDigits(Math.round(stampCurrentScale * 100))}%`;
        if (txt) txt.textContent = pct;
    }

    function initStampDraggable(stamp) {
        if (!stamp || stamp._dragInitialized) return;
        stamp._dragInitialized = true;

        stamp.addEventListener('mousedown', function(e) {
            if (e.target.closest('#stampDraggableHandle') || e.target.closest('.stamp-btn-tool')) return;
            isStampDragging = true;
            stampStartX = e.clientX;
            stampStartY = e.clientY;
            stampInitialLeft = stamp.offsetLeft;
            stampInitialTop = stamp.offsetTop;
            stamp.style.cursor = 'grabbing';
            e.preventDefault();
        });

        document.addEventListener('mousemove', function(e) {
            if (!isStampDragging) return;
            const wrapper = document.getElementById('viewPaperWrapper');
            if (!wrapper) return;

            const dx = e.clientX - stampStartX;
            const dy = e.clientY - stampStartY;
            const newLeft = stampInitialLeft + dx;
            const newTop = stampInitialTop + dy;

            const docW = wrapper.clientWidth;
            const docH = wrapper.clientHeight;
            const leftPct = Math.max(0, Math.min(((newLeft / docW) * 100), 95)).toFixed(2) + '%';
            const topPct = Math.max(0, Math.min(((newTop / docH) * 100), 95)).toFixed(2) + '%';

            stamp.style.left = leftPct;
            stamp.style.top = topPct;
        });

        document.addEventListener('mouseup', function() {
            if (isStampDragging) {
                isStampDragging = false;
                stamp.style.cursor = 'grab';
            }
        });

        // Touch Support
        stamp.addEventListener('touchstart', function(e) {
            const touch = e.touches[0];
            isStampDragging = true;
            stampStartX = touch.clientX;
            stampStartY = touch.clientY;
            stampInitialLeft = stamp.offsetLeft;
            stampInitialTop = stamp.offsetTop;
        }, { passive: true });

        document.addEventListener('touchmove', function(e) {
            if (!isStampDragging) return;
            const touch = e.touches[0];
            const wrapper = document.getElementById('viewPaperWrapper');
            if (!wrapper) return;

            const dx = touch.clientX - stampStartX;
            const dy = touch.clientY - stampStartY;
            const newLeft = stampInitialLeft + dx;
            const newTop = stampInitialTop + dy;

            const docW = wrapper.clientWidth;
            const docH = wrapper.clientHeight;
            const leftPct = Math.max(0, Math.min(((newLeft / docW) * 100), 95)).toFixed(2) + '%';
            const topPct = Math.max(0, Math.min(((newTop / docH) * 100), 95)).toFixed(2) + '%';

            stamp.style.left = leftPct;
            stamp.style.top = topPct;
        }, { passive: false });

        document.addEventListener('touchend', function() {
            isStampDragging = false;
        });
    }

    function saveReceiveStampPosition() {
        const stamp = document.getElementById('issuedReceiveStamp');
        const btn = document.getElementById('btnSaveStampPosition');
        const wrapper = document.getElementById('viewPaperWrapper');
        if (!stamp || !wrapper) return;

        const docW = wrapper.clientWidth;
        const docH = wrapper.clientHeight;
        let left = stamp.style.left;
        let top = stamp.style.top;

        if (left.includes('px')) {
            left = ((parseFloat(left) / docW) * 100).toFixed(2) + '%';
        }
        if (top.includes('px')) {
            top = ((parseFloat(top) / docH) * 100).toFixed(2) + '%';
        }

        const scale = stampCurrentScale;
        const wPct = (23.8 * scale).toFixed(2) + '%';

        if (btn) {
            btn.disabled = true;
            btn.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span> กำลังประทับตราใหม่...`;
        }

        const formData = new FormData();
        formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
        formData.append('receive_stamp_x', left);
        formData.append('receive_stamp_y', top);
        formData.append('receive_stamp_scale', scale);
        formData.append('receive_stamp_w_pct', wPct);
        formData.append('has_receive_stamp', '1');

        fetch("<?= base_url('admin/inbound/' . $document['id'] . '/update-stamp') ?>", {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'ประทับตราใหม่สำเร็จ!',
                        text: data.message || 'ระบบได้ประทับตรายางลงในเอกสารตามตำแหน่งใหม่เรียบร้อยแล้ว',
                        timer: 1600,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    alert(data.message || 'ประทับตราใหม่สำเร็จ');
                    window.location.reload();
                }
            } else {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = `<i class="bx bx-save me-1"></i> บันทึกและประทับตราใหม่`;
                }
                if (typeof Swal !== 'undefined') {
                    Swal.fire('ข้อผิดพลาด', data.message || 'เกิดข้อผิดพลาดในการประทับตราใหม่', 'error');
                } else {
                    alert(data.message || 'เกิดข้อผิดพลาดในการประทับตราใหม่');
                }
            }
        })
        .catch(err => {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = `<i class="bx bx-save me-1"></i> บันทึกและประทับตราใหม่`;
            }
            console.error('Save stamp error:', err);
            alert('ไม่สามารถประทับตราใหม่ได้ กรุณาลองใหม่อีกครั้ง');
        });
    }
</script>
<?= $this->endSection() ?>
