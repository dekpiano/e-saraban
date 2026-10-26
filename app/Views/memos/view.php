<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>บันทึกข้อความ เลขที่ <?= esc($document['doc_no']) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/saraban-memo.css?v=' . (file_exists(FCPATH . 'assets/css/saraban-memo.css') ? filemtime(FCPATH . 'assets/css/saraban-memo.css') : '2.0')) ?>">
<style>
.memo-endorse-box {
    border: 1px solid #cbd5e1;
    background-color: #f8fafc;
    border-radius: 6px;
    padding: 12px 16px;
    margin-bottom: 12px;
}

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
        min-height: 29.7cm !important;
    }
    .layout-navbar,
    .layout-menu,
    .layout-menu-toggle,
    .footer,
    .col-lg-4,
    .col-xl-4,
    .btn,
    .no-print,
    .alert {
        display: none !important;
    }
    .layout-wrapper,
    .layout-container,
    .layout-page,
    .content-wrapper,
    .container-xxl,
    .card,
    .row,
    .col-lg-8,
    .col-xl-8 {
        display: block !important;
        margin: 0 !important;
        padding: 0 !important;
        border: none !important;
        background: transparent !important;
        width: 100% !important;
        position: static !important;
        transform: none !important;
        box-shadow: none !important;
    }
    .page {
        box-shadow: none !important;
        width: 21cm !important;
        height: auto !important;
        min-height: 29.7cm !important;
        padding: 1.5cm 2cm 2cm 3cm !important;
        margin: 0 !important;
        position: relative !important;
        left: 0 !important;
        top: 0 !important;
        transform: none !important;
    }
}
</style>

<div class="row">
    <?php if (!empty($linkedTravel)): ?>
        <div class="col-12 no-print"><div class="alert alert-info d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3"><div><strong><i class="bx bx-link me-1"></i>เชื่อมโยงกับคำขอไปราชการ</strong><div class="small mt-1"><?= esc($linkedTravel['doc_no']) ?> · <?= esc($linkedTravel['title']) ?> · สถานะ <?= esc($linkedTravel['status']) ?></div></div><a class="btn btn-sm btn-outline-primary" href="<?= base_url('admin/travel/' . (int)$linkedTravel['id']) ?>">เปิดคำขอไปราชการ <i class="bx bx-link-external ms-1"></i></a></div></div>
    <?php endif; ?>
    <?php
    $isSarabanOfficer = in_array((string)session()->get('role'), ['saraban', 'admin', 'super_admin'], true);
    $isPendingSaraban = ($document['status'] === 'pending_saraban');
    $showReceiveStamp = isset($document['has_receive_stamp']) ? ((int)$document['has_receive_stamp'] === 1) : true;
    $hasReceiveStamp = !empty($document['internal_reg_no']) || !empty($document['received_at']) || (!$isPendingSaraban && !empty($document['doc_no']));
    $currentReceiverName = trim(($currentUser['prefix'] ?? '') . ($currentUser['first_name'] ?? '') . ' ' . ($currentUser['last_name'] ?? ''));

    // ตรวจสอบว่าผู้บริหารหรือผู้บังคับบัญชาลงนามแล้วหรือไม่
    $hasSignedRoute = false;
    if (!empty($routes)) {
        foreach ($routes as $rt) {
            if (($rt['status'] ?? '') === 'signed') {
                $hasSignedRoute = true;
                break;
            }
        }
    }
    $isExecutiveSigned = ($document['status'] === 'completed') || $hasSignedRoute;
    ?>

    <!-- Main Memo Display (A4 Canvas) -->
    <div class="col-lg-8 col-xl-8">

        <!-- Top Navigation Bar -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 no-print gap-2">
            <a href="<?= base_url('admin/memos') ?>" class="btn btn-outline-secondary btn-sm">
                <i class="bx bx-arrow-back me-1"></i> กลับไปทะเบียนบันทึกข้อความ
            </a>
            <div class="d-flex align-items-center gap-2">
                <span id="memoPageCounterBadge" class="bar-page-counter badge-single-page text-dark" onclick="scrollToNextPageBreak()" style="cursor: pointer; background: #fff; border: 1px solid #cbd5e1;" title="ความยาวเอกสาร A4 (คลิกเพื่อเลื่อนไปยังจุดแบ่งหน้า)">
                    <i class="bx bx-file text-primary me-1"></i> เอกสาร ๑ แผ่น (A4)
                </span>
                <a href="<?= base_url('admin/memos/' . $document['id'] . '/print') ?>" target="_blank" class="btn btn-primary btn-sm fw-semibold shadow-sm">
                    <i class="bx bx-printer me-1"></i> พิมพ์ขนาด A4
                </a>
                <?php if (!empty($document['file_path'])): ?>
                    <a href="<?= document_url($document['file_path']) ?>" target="_blank" class="btn btn-outline-danger btn-sm">
                        <i class="bx bxs-file-pdf me-1"></i> ดูไฟล์ PDF แนบ
                    </a>
                <?php endif; ?>
                <?php if ($isExecutiveSigned): ?>
                    <span class="badge bg-success text-white py-2 px-3 fw-bold shadow-xs">
                        <i class="bx bx-check-shield me-1"></i> ผู้บริหารลงนามแล้ว (ล็อกการแก้ไข)
                    </span>
                <?php elseif ($isSarabanOfficer && $hasReceiveStamp && !$isPendingSaraban): ?>
                    <button type="button" class="btn btn-warning btn-sm text-dark fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#editIssueModal">
                        <i class="bx bx-edit me-1"></i> แก้ไขการออกเลข / ตรายาง
                    </button>
                <?php endif; ?>
                <?php if (!$isExecutiveSigned && in_array($document['status'], ['draft', 'returned', 'pending_saraban'], true)): ?>
                    <a href="<?= base_url('admin/memos/' . $document['id'] . '/edit') ?>" class="btn btn-warning btn-sm text-dark fw-bold">
                        <i class="bx bx-edit me-1"></i> แก้ไขข้อความ
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- A4 Document Paper -->
        <div class="card bg-light p-3 p-md-4 mb-4 border-0 shadow-sm">
            <div class="page" id="printableMemoPaper" style="position: relative;">

                <!-- 1. Live Draggable & Resizable ตรายางรับหนังสือราชการ สำหรับเจ้าหน้าที่สารบรรณขณะตรวจทานออกเลข (รูปตรายาง รร.ส.ก.จ. จริง 100%) -->
                <?php if ($isPendingSaraban && $isSarabanOfficer): ?>
                    <div id="liveReceiveStamp" class="saraban-receive-stamp-img-wrap is-draggable shadow" style="position: absolute; left: 72.2%; top: 3.5%; width: 23.8%; aspect-ratio: 452 / 227; height: auto; transform: rotate(-1deg) scale(1.0); transform-origin: top left; display: <?= $showReceiveStamp ? 'block' : 'none' ?>; z-index: 60; user-select: none; cursor: grab; box-sizing: border-box;">
                        <div class="stamp-controls-bar no-print">
                            <span class="stamp-btn-move" title="แตะหรือลากเพื่อย้ายตำแหน่งตรายางรับหนังสือ"><i class="bx bx-move"></i> ย้าย</span>
                            <span class="stamp-btn-tool" onclick="changeReceiveStampScale(-0.05, event)" title="ย่อขนาดเล็กลง"><i class="bx bx-minus"></i></span>
                            <span class="stamp-scale-badge" id="liveReceiveStampScaleDisplay">100%</span>
                            <span class="stamp-btn-tool" onclick="changeReceiveStampScale(0.05, event)" title="ขยายขนาดใหญ่ขึ้น"><i class="bx bx-plus"></i></span>
                            <span class="stamp-btn-tool" onclick="resetReceiveStampPos(event)" title="รีเซ็ตตำแหน่งมุมบนขวา"><i class="bx bx-reset"></i></span>
                        </div>
                        <div style="position: relative; width: 100%; height: 100%; background: #ffffff; border-radius: 1px; overflow: hidden; box-shadow: 0 4px 12px rgba(20,42,130,0.18);">
                            <img src="<?= base_url('assets/img/stamp_template_skj.png') ?>" alt="ตรายาง รร.ส.ก.จ." style="width: 100%; height: 100%; display: block; object-fit: fill; pointer-events: none;">
                            <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; font-family: 'TH SarabunIT๙', 'TH Sarabun New', 'Sarabun', sans-serif; font-weight: bold; color: #142a82; line-height: 1;">
                                <span id="liveReceiveStampRegNoDisplay" style="position: absolute; left: 27%; top: 43%; transform: translateY(-82%); font-size: 1.3vw; min-font-size: 13pt; max-font-size: 17pt; white-space: nowrap;"><?= esc($suggestedRegNo) ?></span>
                                <span id="liveReceiveStampDateDisplay" style="position: absolute; left: 25.2%; top: 66.3%; transform: translateY(-82%); font-size: 1.3vw; min-font-size: 13pt; max-font-size: 17pt; white-space: nowrap;"><?= esc(thai_date(date('Y-m-d'), 'medium')) ?></span>
                                <span id="liveReceiveStampTimeDisplay" style="position: absolute; left: 24%; top: 89.6%; transform: translateY(-82%); font-size: 1.3vw; min-font-size: 13pt; max-font-size: 17pt; white-space: nowrap;"><?= date('H:i') . ' น.' ?></span>
                            </div>
                        </div>
                        <!-- Corner Resize Handle -->
                        <div class="stamp-resize-handle no-print" id="receiveStampResizeHandle" title="คลิกหรือแตะลากเพื่อย่อ-ขยายขนาด">
                            <i class="bx bx-expand-alt"></i>
                        </div>
                    </div>
                <?php elseif ($hasReceiveStamp && $showReceiveStamp): ?>
                    <!-- 2. Permanent Stamped ตรายางประทับรับหนังสือราชการ (เมื่อออกเลขแล้ว รูปตรายาง รร.ส.ก.จ. จริง 100%) -->
                    <?php
                    $rX = !empty($document['receive_stamp_x']) ? $document['receive_stamp_x'] : '72.2%';
                    $rY = !empty($document['receive_stamp_y']) ? $document['receive_stamp_y'] : '3.5%';
                    $rScale = !empty($document['receive_stamp_scale']) ? (float)$document['receive_stamp_scale'] : 1.0;
                    $rReg = !empty($document['internal_reg_no']) ? $document['internal_reg_no'] : (!empty($document['doc_no']) ? $document['doc_no'] : '-');
                    $rDate = !empty($document['receive_date']) ? $document['receive_date'] : ($document['doc_date'] ?? date('Y-m-d'));
                    $rTime = !empty($document['receive_stamp_time']) ? $document['receive_stamp_time'] : (!empty($document['received_at']) ? date('H:i น.', strtotime($document['received_at'])) : date('H:i น.'));
                    ?>
                    <div id="issuedReceiveStamp" class="saraban-receive-stamp-img-wrap shadow-xs" style="position: absolute; left: <?= esc($rX) ?>; top: <?= esc($rY) ?>; width: 23.8%; aspect-ratio: 452 / 227; height: auto; transform: rotate(-1deg) scale(<?= $rScale ?>); transform-origin: top left; z-index: 50; display: block;">
                        <div style="position: relative; width: 100%; height: 100%; background: #ffffff; border-radius: 1px; overflow: hidden; box-shadow: 0 4px 12px rgba(20,42,130,0.18);">
                            <img src="<?= base_url('assets/img/stamp_template_skj.png') ?>" alt="ตรายาง รร.ส.ก.จ." style="width: 100%; height: 100%; display: block; object-fit: fill; pointer-events: none;">
                            <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; font-family: 'TH SarabunIT๙', 'TH Sarabun New', 'Sarabun', sans-serif; font-weight: bold; color: #142a82; line-height: 1;">
                                <span style="position: absolute; left: 27%; top: 43%; transform: translateY(-82%); font-size: 1.3vw; min-font-size: 13pt; max-font-size: 17pt; white-space: nowrap;"><?= esc(to_thai_number($rReg)) ?></span>
                                <span style="position: absolute; left: 25.2%; top: 66.3%; transform: translateY(-82%); font-size: 1.3vw; min-font-size: 13pt; max-font-size: 17pt; white-space: nowrap;"><?= esc(to_thai_number(thai_date($rDate, 'medium'))) ?></span>
                                <span style="position: absolute; left: 24%; top: 89.6%; transform: translateY(-82%); font-size: 1.3vw; min-font-size: 13pt; max-font-size: 17pt; white-space: nowrap;"><?= esc(to_thai_number($rTime)) ?></span>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="header">
                    <img src="<?= base_url('assets/img/krut_thai.png') ?>" alt="ตราครุฑ" class="garuda">
                    <div class="document-title">บันทึกข้อความ</div>
                </div>

                <div class="form-row">
                    <span class="label">ส่วนราชการ</span>
                    <span class="editable-field"><?= esc($document['from_org'] ?: $document['memo_department']) ?><?= !empty($document['memo_contact']) ? ' โทร. ' . esc($document['memo_contact']) : '' ?></span>
                </div>

                <div class="col-2">
                    <div class="col-left">
                        <span class="label">ที่</span>
                        <span class="editable-field" id="headerDocNoField"><?= esc($document['doc_no'] ?: ($isPendingSaraban ? ($suggestedDocNo ?: 'รอออกเลข') : '-')) ?></span>
                    </div>
                    <div class="col-right">
                        <span class="label">วันที่</span>
                        <span class="editable-field" id="headerDocDateField"><?= thai_date($document['doc_date'], 'medium') ?></span>
                    </div>
                </div>

                <div class="form-row subject-row">
                    <span class="label">เรื่อง</span>
                    <span class="editable-field"><?= esc($document['title']) ?></span>
                </div>

                <div class="salutation space-before-6pt">
                    <span class="label">เรียน</span>
                    <span class="editable-field" style="border-bottom: none;"><?= esc($document['to_org']) ?></span>
                </div>

                <!-- Document Body -->
                <div class="body-text indent space-before-6pt">
                    <?= $document['content'] ?>
                </div>

                <!-- Signer Block -->
                <div class="signature-section">
                    <div class="signature-box">
                        <div class="signature-line">
                            <?php if (!empty($document['creator_signature'])): ?>
                                <img src="<?= base_url($document['creator_signature']) ?>" alt="ลายเซ็น" class="signature-img">
                            <?php endif; ?>
                        </div>
                        <div class="sign-name">( <?= esc(($creator['prefix'] ?? '') . ($creator['first_name'] ?? '') . ' ' . ($creator['last_name'] ?? '')) ?> )</div>
                        <div class="sign-position">ตำแหน่ง <?= esc($creator['position_name'] ?? 'ครูผู้สอน') ?></div>
                    </div>
                </div>

                <!-- Official Endorsements / การเกษียนหนังสือที่ลงนามแล้ว: Realistic Blue Rubber Stamps -->
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
                <?php if (!empty($signedRoutes) || $canSign): ?>
                    <div style="position: relative; margin-top: 30px; min-height: 140px;">
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
                                <div class="stamp-signer-pos" id="previewStampPos"><?= esc($currentRoute['personnel_position'] ?? $currentRoute['position_name'] ?? $currentUser['position_name'] ?? 'ผู้อำนวยการสถานศึกษา') ?></div>
                                <div class="stamp-footer">
                                    <span>วันที่ <?= thai_date(date('Y-m-d'), 'short') ?></span>
                                    <span class="badge bg-primary text-white" style="font-size: 8pt; padding: 1px 4px;">รอลงนาม</span>
                                </div>
                                <div class="stamp-resize-handle no-print" id="stampResizeHandle" title="คลิกหรือแตะลากเพื่อย่อ-ขยายขนาด">
                                    <i class="bx bx-expand-alt"></i>
                                </div>
                                <div class="stamp-width-handle no-print" id="stampWidthHandle" title="คลิกหรือแตะลากเพื่อยืดความกว้าง"></div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

            </div>
        </div>

    </div>

    <!-- Right Column: Status & Action / Signing Box -->
    <div class="col-lg-4 col-xl-4 no-print">

        <!-- Status Card -->
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-white border-bottom py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0 fw-bold text-dark">
                        <i class="bx bx-info-circle me-1 text-primary"></i> สถานะบันทึกข้อความ
                    </h6>
                    <?php if ($document['status'] === 'completed'): ?>
                        <span class="badge bg-success text-white fw-bold">อนุมัติเรียบร้อย</span>
                    <?php elseif ($document['status'] === 'routing'): ?>
                        <span class="badge bg-warning text-dark fw-bold">รอเกษียน (ขั้น <?= $document['current_step'] ?>)</span>
                    <?php elseif ($document['status'] === 'returned'): ?>
                        <span class="badge bg-danger text-white fw-bold">ส่งคืนแก้ไข</span>
                    <?php elseif ($document['status'] === 'pending_saraban'): ?>
                        <span class="badge bg-info text-white fw-bold"><i class="bx bx-time-five me-1"></i> รอสารบรรณออกเลข</span>
                    <?php elseif ($document['status'] === 'draft'): ?>
                        <span class="badge bg-secondary text-white fw-bold"><i class="bx bx-file-blank me-1"></i> ฉบับร่าง</span>
                    <?php else: ?>
                        <span class="badge bg-secondary text-white"><?= esc($document['status']) ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card-body p-3">
                <div class="mb-2">
                    <small class="text-muted d-block">เลขทะเบียนภายใน รร.</small>
                    <span class="fw-bold text-dark"><?= esc($document['internal_reg_no'] ?: '-') ?></span>
                </div>
                <div class="mb-2">
                    <small class="text-muted d-block">ผู้เสนอเรื่อง</small>
                    <span class="fw-semibold text-dark"><?= esc(($creator['prefix'] ?? '') . ($creator['first_name'] ?? '') . ' ' . ($creator['last_name'] ?? '')) ?></span>
                    <small class="text-muted d-block">(<?= esc($creator['department_name'] ?? '-') ?>)</small>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <small class="text-muted d-block">ความเร่งด่วน</small>
                        <span class="badge bg-light text-dark border"><?= esc($document['urgency']) ?></span>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block">ชั้นความลับ</small>
                        <span class="badge bg-light text-dark border"><?= esc($document['secret_level']) ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Saraban Issue Number Box (If Document is Waiting for Saraban and User is Officer) -->
        <?php if ($document['status'] === 'pending_saraban' && in_array((string)session()->get('role'), ['saraban', 'admin', 'super_admin'], true)): ?>
            <div class="card shadow-sm border-2 border-success mb-3">
                <div class="card-header bg-success text-white py-3">
                    <h6 class="card-title text-white mb-0 fw-bold">
                        <i class="bx bx-stamp me-1"></i> เจ้าหน้าที่สารบรรณ: ตรวจสอบ & ออกเลข
                    </h6>
                    <small class="text-white opacity-90">บันทึกข้อความนี้รอการรันเลขที่หนังสือทางการ</small>
                </div>
                <div class="card-body p-3">
                    <div class="alert alert-primary bg-label-primary py-2 px-3 small mb-3" id="issueFormModeAlert">
                        <i class="bx bx-info-circle me-1"></i>
                        <span id="issueFormAlertText"><strong>ตรายางรับหนังสือ</strong> แสดงอยู่ที่มุมบนขวาของกระดาษ A4 ท่านสามารถแตะหรือลากย้ายเพื่อจัดวางตำแหน่งได้</span>
                    </div>

                    <form id="viewIssueForm" action="<?= base_url('admin/memos/' . $document['id'] . '/issue-number') ?>" method="post" data-no-swal="true" data-no-loading="true">
                        <?= csrf_field() ?>
                        <input type="hidden" name="receive_stamp_x" id="inputReceiveStampX" value="72.2%">
                        <input type="hidden" name="receive_stamp_y" id="inputReceiveStampY" value="3.5%">
                        <input type="hidden" name="receive_stamp_scale" id="inputReceiveStampScale" value="1.0">

                        <!-- เลือกระบบการออกเลข (Dual System Selection) -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-dark mb-1">
                                <i class="bx bx-cog text-primary me-1"></i> รูปแบบการออกเลขสารบรรณ
                            </label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="card p-2 text-center border cursor-pointer h-100 shadow-none mode-card" id="modeCardStamp" style="cursor: pointer; border-color: #22c55e !important; background-color: #f0fdf4;" onclick="handleStampModeChange(1)">
                                        <input type="radio" name="has_receive_stamp" id="modeRadioStamp" value="1" class="d-none" checked>
                                        <div class="d-flex flex-column align-items-center">
                                            <span class="badge bg-success text-white mb-1 px-2 py-0.5" style="font-size: 11px;"><i class="bx bx-stamp me-1"></i>แบบที่ ๑</span>
                                            <strong class="text-dark small" style="line-height: 1.2;">มีตรายางรับ</strong>
                                            <span class="text-muted" style="font-size: 10.5px;">(สแตมป์มุมบนขวา)</span>
                                        </div>
                                    </label>
                                </div>
                                <div class="col-6">
                                    <label class="card p-2 text-center border cursor-pointer h-100 shadow-none mode-card" id="modeCardClean" style="cursor: pointer; border-color: #cbd5e1; background-color: #ffffff;" onclick="handleStampModeChange(0)">
                                        <input type="radio" name="has_receive_stamp" id="modeRadioClean" value="0" class="d-none">
                                        <div class="d-flex flex-column align-items-center">
                                            <span class="badge bg-secondary text-white mb-1 px-2 py-0.5" style="font-size: 11px;"><i class="bx bx-file me-1"></i>แบบที่ ๒</span>
                                            <strong class="text-dark small" style="line-height: 1.2;">มาตรฐานราชการ</strong>
                                            <span class="text-muted" style="font-size: 10.5px;">(ลงเลขที่ ไม่สแตมป์)</span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- เลขที่หนังสือ (แสดงทั้ง 2 ระบบ) -->
                        <div class="mb-2">
                            <label class="form-label small fw-bold text-dark mb-1">เลขที่หนังสือ (ออกเลขสารบรรณ)</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light"><i class="bx bx-hash"></i></span>
                                <input type="text" name="doc_no" id="inputDocNo" class="form-control fw-bold text-primary" value="<?= esc($suggestedDocNo) ?>" required oninput="syncLiveDocNo(this.value)">
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label small fw-bold text-dark mb-1">วันที่ออกเลข</label>
                            <input type="date" name="doc_date" id="inputDocDate" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>" onchange="syncLiveDocDate(this.value)">
                        </div>

                        <!-- ส่วนเฉพาะระบบตรายางรับหนังสือ (แบบที่ ๑) -->
                        <div id="stampDetailsGroup">
                            <div class="mb-2">
                                <label class="form-label small fw-bold text-dark mb-1">เลขทะเบียนรับ (ตรายางรับ)</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light"><i class="bx bx-bookmark"></i></span>
                                    <input type="text" name="internal_reg_no" id="inputRegNo" class="form-control fw-bold text-dark" value="<?= esc($suggestedRegNo) ?>" oninput="syncLiveStampRegNo(this.value)">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-dark mb-1">เวลารับ</label>
                                <input type="text" name="receive_stamp_time" id="inputReceiveTime" class="form-control form-control-sm" value="<?= date('H:i') . ' น.' ?>" oninput="syncLiveStampTime(this.value)">
                            </div>

                            <div class="mb-3" id="stampPositionSection">
                                <label class="form-label small fw-bold text-dark mb-1">
                                    <i class="bx bx-move me-1 text-primary"></i> จัดวางตำแหน่งตรายางบนกระดาษ
                                </label>
                                <div class="btn-group btn-group-sm w-100 mb-1" role="group">
                                    <button type="button" class="btn btn-outline-primary fw-bold" onclick="quickSetReceiveStamp('top-right')" title="วางมุมบนขวาตามระเบียบงานสารบรรณ">
                                        <i class="bx bx-dock-right me-1"></i> บนขวา (มาตรฐาน)
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary fw-bold" onclick="quickSetReceiveStamp('top-left')" title="วางมุมบนซ้าย">
                                        <i class="bx bx-dock-left me-1"></i> บนซ้าย
                                    </button>
                                    <button type="button" class="btn btn-outline-dark" onclick="resetReceiveStampPos(event)" title="รีเซ็ตตำแหน่ง">
                                        <i class="bx bx-reset"></i>
                                    </button>
                                </div>
                                <div class="text-muted" style="font-size: 11px;">
                                    <i class="bx bx-info-circle me-1 text-primary"></i> แตะปุ่มด้านบน หรือแตะลากตรายางบน A4 ได้โดยตรง (ระบบล็อกขอบกระดาษให้อัตโนมัติ)
                                </div>
                            </div>
                        </div>

                        <button type="button" id="btnSubmitIssue" class="btn btn-success w-100 fw-bold py-2.5 shadow-sm" onclick="confirmIssueNumberFromView(this)">
                            <i class="bx bx-stamp me-1"></i> ประทับตราออกเลขและส่งเกษียน
                        </button>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <!-- ข้อมูลการออกเลขสารบรรณ + ปุ่มแก้ไขการออกเลข (เมื่อออกเลขแล้ว) -->
        <?php if ($hasReceiveStamp && !$isPendingSaraban && $isSarabanOfficer): ?>
            <div class="card shadow-sm border-0 mb-3 border-start <?= $showReceiveStamp ? 'border-success' : 'border-info' ?> border-4">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="card-title text-dark mb-0 fw-bold">
                        <i class="bx <?= $showReceiveStamp ? 'bx-stamp text-success' : 'bx-file text-info' ?> me-1"></i> ออกเลขสารบรรณแล้ว
                    </h6>
                    <span class="badge bg-primary text-white fw-bold"><?= esc($document['doc_no'] ?: '-') ?></span>
                </div>
                <div class="card-body p-3">
                    <div class="mb-3">
                        <?php if ($showReceiveStamp): ?>
                            <span class="badge bg-label-success text-success fw-bold d-inline-flex align-items-center">
                                <i class="bx bx-stamp me-1"></i> แบบที่ ๑: มีตรายางรับหนังสือ
                            </span>
                        <?php else: ?>
                            <span class="badge bg-label-info text-info fw-bold d-inline-flex align-items-center">
                                <i class="bx bx-file me-1"></i> แบบที่ ๒: มาตรฐานราชการ (ไม่สแตมป์)
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="mb-2 small">
                        <?php if ($showReceiveStamp): ?>
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted">เลขทะเบียนรับ :</span>
                                <strong class="text-dark"><?= esc($document['internal_reg_no'] ?: '-') ?></strong>
                            </div>
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted">วันที่ลงรับ :</span>
                                <span class="text-dark fw-semibold"><?= esc(thai_date($document['doc_date'] ?? date('Y-m-d'), 'medium')) ?></span>
                            </div>
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted">เวลาลงรับ :</span>
                                <span class="text-dark fw-semibold"><?= esc($document['receive_stamp_time'] ?: '-') ?></span>
                            </div>
                            <div class="d-flex justify-content-between py-1">
                                <span class="text-muted">ผู้ประทับตรา :</span>
                                <span class="text-dark fw-semibold"><?= esc(!empty($receiverUser) ? trim(($receiverUser['prefix'] ?? '') . ($receiverUser['first_name'] ?? '') . ' ' . ($receiverUser['last_name'] ?? '')) : 'เจ้าหน้าที่สารบรรณ') ?></span>
                            </div>
                        <?php else: ?>
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted">เลขที่หนังสือ :</span>
                                <strong class="text-primary"><?= esc($document['doc_no'] ?: '-') ?></strong>
                            </div>
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted">วันที่ออกเลข :</span>
                                <span class="text-dark fw-semibold"><?= esc(thai_date($document['doc_date'] ?? date('Y-m-d'), 'medium')) ?></span>
                            </div>
                            <div class="d-flex justify-content-between py-1">
                                <span class="text-muted">ผู้ออกเลข :</span>
                                <span class="text-dark fw-semibold"><?= esc(!empty($receiverUser) ? trim(($receiverUser['prefix'] ?? '') . ($receiverUser['first_name'] ?? '') . ' ' . ($receiverUser['last_name'] ?? '')) : 'เจ้าหน้าที่สารบรรณ') ?></span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if (!$isExecutiveSigned): ?>
                        <div class="d-grid gap-2 mt-3">
                            <button type="button" class="btn btn-warning text-dark fw-bold btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#editIssueModal">
                                <i class="bx bx-edit me-1"></i> แก้ไขการออกเลข / ปรับรูปแบบ
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-secondary py-2 px-3 small mt-3 mb-0 d-flex align-items-center border-0" style="background-color: #f1f5f9;">
                            <i class="bx bx-lock-alt text-success fs-4 me-2 flex-shrink-0"></i>
                            <div>
                                <strong class="text-dark d-block">ล็อกการแก้ไข</strong>
                                <span class="text-muted" style="font-size: 11px;">เอกสารมีผู้บริหารลงนามแล้ว ไม่สามารถแก้ไขได้</span>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Signing Box (If Current User Can Sign) -->
        <?php if ($canSign && $currentRoute): ?>
            <div class="card shadow-sm border-2 border-warning mb-3">
                <div class="card-header bg-warning text-dark py-3">
                    <h6 class="card-title text-dark mb-0 fw-bold">
                        <i class="bx bx-edit me-1"></i> ถึงลำดับการเกษียน/ลงนามของคุณ
                    </h6>
                    <small class="text-dark">ขั้นตอนที่ <?= $currentRoute['step_order'] ?> ในสายการบังคับบัญชา</small>
                </div>
                <div class="card-body p-3">
                    <form action="<?= base_url('admin/memos/' . $document['id'] . '/sign') ?>" method="POST" id="signForm">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action_type" id="signActionType" value="approve">
                        <input type="hidden" name="signature_type" id="signatureType" value="preset">
                        <input type="hidden" name="signature_data" id="signatureData">

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-dark">ข้อความเกษียนสำเร็จรูป</label>
                            <select class="form-select" name="comment_preset" id="commentPresetSelect">
                                <option value="เห็นควรอนุมัติ">เห็นควรอนุมัติ</option>
                                <option value="เพื่อโปรดพิจารณาอนุมัติ">เพื่อโปรดพิจารณาอนุมัติ</option>
                                <option value="อนุมัติ">อนุมัติ / อนุญาต</option>
                                <option value="ทราบ / ดำเนินการตามเสนอ">ทราบ / ดำเนินการตามเสนอ</option>
                                <option value="มอบหมายงานสารบรรณดำเนินการต่อ">มอบหมายงานสารบรรณดำเนินการต่อ</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-dark">บันทึกข้อความเพิ่มเติม (ถ้ามี)</label>
                            <textarea class="form-control" name="comment_text" rows="2" placeholder="ระบุข้อความเกษียนหรือสั่งการเพิ่มเติม..."></textarea>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success fw-bold py-2 shadow-sm" onclick="document.getElementById('signActionType').value='approve'">
                                <i class="bx bx-check-circle me-1"></i> ลงนามเกษียน / อนุมัติเรื่อง
                            </button>
                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="promptReturnMemo()">
                                <i class="bx bx-reply me-1"></i> ส่งคืนให้ผู้เสนอแก้ไข
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <!-- Routing Stepper Card (แสดงเมื่อเจ้าหน้าที่สารบรรณกำหนดเส้นทางแล้ว) -->
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="card-title mb-0 fw-bold text-dark">
                    <i class="bx bx-git-branch me-1 text-primary"></i> เส้นทางการเดินแฟ้มเกษียน
                </h6>
            </div>
            <div class="card-body p-3">
                <?php if (in_array($document['status'], ['pending_saraban', 'draft'], true) || empty($routes)): ?>
                    <div class="text-center py-4 px-2">
                        <div class="avatar avatar-md bg-label-info rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center">
                            <i class="bx bx-time-five fs-3 text-info"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1" style="font-family: 'K2D', sans-serif;">รอเจ้าหน้าที่สารบรรณกำหนดเส้นทาง</h6>
                        <p class="text-muted small mb-0">
                            เจ้าหน้าที่งานสารบรรณจะทำการตรวจทาน ออกเลขหนังสือ และกำหนดผู้เกษียนตามลำดับการบังคับบัญชา
                        </p>
                    </div>
                <?php else: ?>
                    <div class="d-flex flex-column gap-3">
                        <?php foreach ($routes as $r): ?>
                            <div class="d-flex align-items-start gap-2">
                                <div class="mt-1">
                                    <?php if ($r['status'] === 'signed'): ?>
                                        <span class="badge bg-success rounded-circle p-2"><i class="bx bx-check text-white"></i></span>
                                    <?php elseif ($r['status'] === 'pending' && $r['step_order'] == $document['current_step'] && $document['status'] === 'routing'): ?>
                                        <span class="badge bg-warning rounded-circle p-2"><i class="bx bx-time text-dark"></i></span>
                                    <?php elseif ($r['status'] === 'returned'): ?>
                                        <span class="badge bg-danger rounded-circle p-2"><i class="bx bx-reply text-white"></i></span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary rounded-circle p-2"><i class="bx bx-radio-circle text-white"></i></span>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold small text-dark">ขั้นที่ <?= $r['step_order'] ?>: <?= esc(($r['prefix'] ?? '') . ($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? '')) ?></div>
                                    <small class="text-muted d-block"><?= esc($r['position_name'] ?? '') ?></small>
                                    <?php if ($r['status'] === 'signed'): ?>
                                        <span class="badge bg-label-success mt-1" style="font-size: 0.7rem;">
                                            <i class="bx bx-check me-1"></i>ลงนามเมื่อ <?= thai_date($r['signed_at'], 'short') ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Audit Trail Logs -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="card-title mb-0 fw-bold text-dark">
                    <i class="bx bx-history me-1 text-primary"></i> ประวัติกิจกรรม (Audit Trail)
                </h6>
            </div>
            <div class="card-body p-3">
                <div class="small">
                    <?php if (empty($logs)): ?>
                        <div class="text-muted text-center py-2">ไม่มีประวัติ</div>
                    <?php else: ?>
                        <?php foreach ($logs as $log): ?>
                            <div class="mb-2 pb-2 border-bottom">
                                <div class="fw-semibold text-dark"><?= esc($log['action']) ?> - <?= esc($log['detail']) ?></div>
                                <small class="text-muted"><?= esc(($log['prefix'] ?? '') . ($log['first_name'] ?? '') . ' ' . ($log['last_name'] ?? '')) ?> | <?= thai_date($log['created_at'], 'short') ?></small>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Modal แก้ไขการออกเลข และ ปรับตำแหน่งตรายางรับหนังสือ -->
<?php if ($isSarabanOfficer && $hasReceiveStamp && !$isPendingSaraban && !$isExecutiveSigned): ?>
<div class="modal fade" id="editIssueModal" tabindex="-1" aria-labelledby="editIssueModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-warning py-3">
                <h5 class="modal-title text-dark fw-bold mb-0" id="editIssueModalLabel">
                    <i class="bx bx-edit me-1"></i> แก้ไขการออกเลข / ปรับตำแหน่งตรายาง
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('admin/memos/' . $document['id'] . '/update-issue') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body p-4">
                    <div class="alert alert-primary bg-label-primary py-2 px-3 small mb-3">
                        <i class="bx bx-info-circle me-1"></i> ท่านสามารถเลือกระบบการออกเลข (มีตรายางรับ หรือมาตรฐานราชการ) แก้ไขเลขที่หนังสือ เลขทะเบียนรับ วันที่ หรือปรับตำแหน่งตรายางได้
                    </div>

                    <!-- เลือกระบบการออกเลข (Dual System) ใน Modal -->
                    <?php $currentModalStampMode = isset($document['has_receive_stamp']) ? (int)$document['has_receive_stamp'] : 1; ?>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark mb-1">
                            <i class="bx bx-cog text-primary me-1"></i> รูปแบบการออกเลขสารบรรณ
                        </label>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="card p-2 text-center border cursor-pointer h-100 shadow-none modal-mode-card" id="modalCardStamp" style="cursor: pointer; <?= $currentModalStampMode === 1 ? 'border-color: #22c55e !important; background-color: #f0fdf4;' : 'border-color: #cbd5e1; background-color: #ffffff;' ?>" onclick="handleModalStampModeChange(1)">
                                    <input type="radio" name="has_receive_stamp" id="modalRadioStamp" value="1" class="d-none" <?= $currentModalStampMode === 1 ? 'checked' : '' ?>>
                                    <div class="d-flex flex-column align-items-center">
                                        <span class="badge bg-success text-white mb-1 px-2 py-0.5" style="font-size: 11px;"><i class="bx bx-stamp me-1"></i>แบบที่ ๑</span>
                                        <strong class="text-dark small" style="line-height: 1.2;">มีตรายางรับ</strong>
                                        <span class="text-muted" style="font-size: 10.5px;">(สแตมป์มุมบนขวา)</span>
                                    </div>
                                </label>
                            </div>
                            <div class="col-6">
                                <label class="card p-2 text-center border cursor-pointer h-100 shadow-none modal-mode-card" id="modalCardClean" style="cursor: pointer; <?= $currentModalStampMode === 0 ? 'border-color: #0284c7 !important; background-color: #f0f9ff;' : 'border-color: #cbd5e1; background-color: #ffffff;' ?>" onclick="handleModalStampModeChange(0)">
                                    <input type="radio" name="has_receive_stamp" id="modalRadioClean" value="0" class="d-none" <?= $currentModalStampMode === 0 ? 'checked' : '' ?>>
                                    <div class="d-flex flex-column align-items-center">
                                        <span class="badge bg-secondary text-white mb-1 px-2 py-0.5" style="font-size: 11px;"><i class="bx bx-file me-1"></i>แบบที่ ๒</span>
                                        <strong class="text-dark small" style="line-height: 1.2;">มาตรฐานราชการ</strong>
                                        <span class="text-muted" style="font-size: 10.5px;">(ลงเลขที่ ไม่สแตมป์)</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark mb-1">เลขที่หนังสือ (ออกเลขสารบรรณ)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bx bx-hash"></i></span>
                            <input type="text" name="doc_no" class="form-control fw-bold text-primary" value="<?= esc($document['doc_no']) ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark mb-1">วันที่ออกเลข</label>
                        <input type="date" name="doc_date" class="form-control" value="<?= esc($document['doc_date'] ?? date('Y-m-d')) ?>">
                    </div>

                    <!-- ส่วนเฉพาะระบบตรายางรับหนังสือ (แบบที่ ๑) ใน Modal -->
                    <div id="modalStampFields" style="display: <?= $currentModalStampMode === 1 ? 'block' : 'none' ?>;">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark mb-1">เลขทะเบียนรับ (ตรายางรับ)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bx bx-bookmark"></i></span>
                                <input type="text" name="internal_reg_no" class="form-control fw-bold text-dark" value="<?= esc($document['internal_reg_no']) ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark mb-1">เวลารับ</label>
                            <input type="text" name="receive_stamp_time" class="form-control" value="<?= esc($document['receive_stamp_time'] ?: date('H:i น.')) ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark mb-1">ตำแหน่งตรายางบนกระดาษ A4</label>
                            <div class="btn-group w-100 mb-2">
                                <button type="button" class="btn btn-outline-primary btn-sm fw-bold" onclick="setEditModalStampPos('65%', '1.2cm')">
                                    <i class="bx bx-dock-right me-1"></i> บนขวา (มาตรฐาน)
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm fw-bold" onclick="setEditModalStampPos('8%', '1.2cm')">
                                    <i class="bx bx-dock-left me-1"></i> บนซ้าย
                                </button>
                            </div>
                            <div class="row g-2">
                                <div class="col-4">
                                    <label class="small text-muted mb-0">แนวนอน (X)</label>
                                    <input type="text" name="receive_stamp_x" id="editModalStampX" class="form-control form-control-sm text-center fw-semibold" value="<?= esc($document['receive_stamp_x'] ?: '65%') ?>">
                                </div>
                                <div class="col-4">
                                    <label class="small text-muted mb-0">แนวตั้ง (Y)</label>
                                    <input type="text" name="receive_stamp_y" id="editModalStampY" class="form-control form-control-sm text-center fw-semibold" value="<?= esc($document['receive_stamp_y'] ?: '1.2cm') ?>">
                                </div>
                                <div class="col-4">
                                    <label class="small text-muted mb-0">ขนาด (สเกล)</label>
                                    <input type="number" step="0.05" min="0.6" max="1.6" name="receive_stamp_scale" id="editModalStampScale" class="form-control form-control-sm text-center fw-semibold" value="<?= esc($document['receive_stamp_scale'] ?: '0.95') ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-3">

                    <div class="bg-light p-3 rounded border">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <strong class="text-danger small"><i class="bx bx-error-circle me-1"></i> ยกเลิกการออกเลขฉบับนี้?</strong>
                                <div class="text-muted" style="font-size: 11px;">ส่งบันทึกข้อความกลับไปรอออกเลขใหม่</div>
                            </div>
                            <button type="button" class="btn btn-outline-danger btn-sm fw-bold" onclick="submitCancelIssue()">
                                <i class="bx bx-undo me-1"></i> ยกเลิกการออกเลข
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">ปิด</button>
                    <button type="submit" class="btn btn-warning btn-sm text-dark fw-bold px-3">
                        <i class="bx bx-check me-1"></i> บันทึกการแก้ไข
                    </button>
                </div>
            </form>

            <!-- Hidden Form สำหรับยกเลิกการออกเลข -->
            <form id="cancelIssueForm" action="<?= base_url('admin/memos/' . $document['id'] . '/cancel-issue') ?>" method="post" style="display: none;">
                <?= csrf_field() ?>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
function promptReturnMemo() {
    if (window.AppAlert && typeof window.AppAlert.prompt === 'function') {
        AppAlert.prompt('ส่งคืนให้ผู้เสนอแก้ไข', {
            text: 'กรุณาระบุเหตุผลหรือข้อความที่ต้องการให้ผู้เสนอเรื่องแก้ไข:',
            placeholder: 'ระบุเหตุผลในการส่งคืน...',
            inputType: 'text',
            required: true,
            confirmText: '<i class="bx bx-reply me-1"></i> ยืนยันส่งคืน'
        }).then(function(reason) {
            if (reason && reason.trim() !== '') {
                document.getElementById('signActionType').value = 'return';
                const form = document.getElementById('signForm');
                const textInput = form.querySelector('textarea[name="comment_text"]');
                if (textInput) textInput.value = reason.trim();
                const returnBtn = document.querySelector('button[onclick="promptReturnMemo()"]');
                if (window.SubmitLoading && returnBtn) SubmitLoading.set(returnBtn, 'กำลังส่งคืน...');
                if (window.AppAlert) AppAlert.showLoading('กำลังส่งคืนเอกสาร...', 'ระบบกำลังบันทึกและส่งคืนให้ผู้เสนอแก้ไข');
                form.submit();
            }
        });
    } else {
        const reason = prompt('กรุณาระบุเหตุผลหรือข้อความที่ต้องการให้ผู้เสนอเรื่องแก้ไข:');
        if (reason && reason.trim() !== '') {
            document.getElementById('signActionType').value = 'return';
            const form = document.getElementById('signForm');
            const textInput = form.querySelector('textarea[name="comment_text"]');
            if (textInput) textInput.value = reason;
            form.submit();
        }
    }
}

// ==========================================
// ระบบตรวจจับและแสดงเส้นแบ่งหน้า A4 อัตโนมัติ (Page Break Detection System)
// ==========================================
let cachedA4Height = 0;
function getA4PageHeightPx() {
    if (cachedA4Height > 0) return cachedA4Height;
    const testDiv = document.createElement('div');
    testDiv.style.height = '29.7cm';
    testDiv.style.position = 'absolute';
    testDiv.style.left = '-9999px';
    testDiv.style.top = '-9999px';
    testDiv.style.visibility = 'hidden';
    document.body.appendChild(testDiv);
    cachedA4Height = testDiv.getBoundingClientRect().height;
    document.body.removeChild(testDiv);
    return cachedA4Height || 1122.52;
}

function updatePageBreakIndicators() {
    const page = document.getElementById('printableMemoPaper');
    if (!page) return;

    page.querySelectorAll('.page-break-indicator, .page-header-number').forEach(el => el.remove());

    const a4Height = getA4PageHeightPx();
    const currentHeight = Math.max(page.scrollHeight, page.offsetHeight);
    const threshold = 15;
    const totalPages = (currentHeight > a4Height + threshold)
        ? Math.ceil((currentHeight - threshold) / a4Height)
        : 1;

    const counterBadge = document.getElementById('memoPageCounterBadge');
    const thaiDigits = ['๐', '๑', '๒', '๓', '๔', '๕', '๖', '๗', '๘', '๙'];

    if (counterBadge) {
        if (totalPages > 1) {
            const pageThai = totalPages < 10 ? thaiDigits[totalPages] : totalPages;
            counterBadge.innerHTML = `<i class="bx bxs-file text-warning me-1"></i> <span class="fw-bold text-warning">ขึ้นแผ่นที่ ${pageThai} แล้ว</span> (รวม ${pageThai} หน้า)`;
            counterBadge.className = 'bar-page-counter badge-multi-page';
        } else {
            counterBadge.innerHTML = `<i class="bx bx-file text-primary me-1"></i> เอกสาร ๑ แผ่น (A4)`;
            counterBadge.className = 'bar-page-counter badge-single-page text-dark';
        }
    }

    if (totalPages <= 1) return;

    for (let p = 2; p <= totalPages; p++) {
        const topPx = (p - 1) * a4Height;
        const pageThai = p < 10 ? thaiDigits[p] : p;
        const prevPageThai = (p - 1) < 10 ? thaiDigits[p - 1] : (p - 1);

        const indicator = document.createElement('div');
        indicator.className = 'page-break-indicator no-print';
        indicator.style.top = topPx + 'px';
        indicator.id = 'pageBreak_' + p;
        indicator.innerHTML = `<span class="page-break-badge"><i class="bx bx-cut"></i> สิ้นสุดแผ่นที่ ${prevPageThai} / 📄 ขึ้นแผ่นที่ ${pageThai} (- ${pageThai} -)</span>`;
        page.appendChild(indicator);

        const headerNum = document.createElement('div');
        headerNum.className = 'page-header-number no-print';
        headerNum.style.top = (topPx + 24) + 'px';
        headerNum.innerHTML = `- ${pageThai} -`;
        page.appendChild(headerNum);
    }
}

function scrollToNextPageBreak() {
    const firstBreak = document.querySelector('.page-break-indicator');
    if (firstBreak) {
        firstBreak.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}

function confirmIssueNumberFromView(btn) {
    const form = document.getElementById('viewIssueForm');
    if (!form) return;

    const doSubmit = function() {
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> กำลังออกเลข...';
        }
        if (window.AppAlert && typeof window.AppAlert.showLoading === 'function') {
            AppAlert.showLoading('กำลังออกเลขหนังสือ...', 'ระบบกำลังออกเลขหนังสือทางการและส่งเข้าสู่เส้นทางเกษียน กรุณารอสักครู่');
        }
        form.submit();
    };

    if (window.AppAlert && typeof window.AppAlert.confirm === 'function') {
        AppAlert.confirm('ยืนยันว่าได้ตรวจทานบันทึกข้อความเรียบร้อยแล้ว และต้องการรันเลขที่หนังสือทางการเพื่อส่งเข้าสู่การเกษียนทันทีใช่หรือไม่?', {
            title: 'ออกเลขหนังสือและส่งเกษียน',
            confirmText: '<i class="bx bx-hash me-1"></i> ยืนยันออกเลขทันที',
            cancelText: 'ตรวจทานก่อน'
        }).then(function(confirmed) {
            if (confirmed) {
                doSubmit();
            }
        });
        return;
    }

    if (confirm('ยืนยันว่าได้ตรวจทานบันทึกข้อความเรียบร้อยแล้ว และต้องการรันเลขที่หนังสือทางการเพื่อส่งเข้าสู่การเกษียนทันทีใช่หรือไม่?')) {
        doSubmit();
    }
}

// ==========================================
// ระบบควบคุมตรายางรับหนังสือ (Live Receiving Stamp Controls)
// ==========================================
let receiveStampScale = 1.0;

function changeReceiveStampScale(delta, event) {
    if (event) event.stopPropagation();
    receiveStampScale = Math.min(Math.max(receiveStampScale + delta, 0.6), 1.6);
    const stamp = document.getElementById('liveReceiveStamp');
    const display = document.getElementById('liveReceiveStampScaleDisplay');
    const inputScale = document.getElementById('inputReceiveStampScale');
    if (stamp) {
        stamp.style.transform = `rotate(-1deg) scale(${receiveStampScale})`;
    }
    if (display) {
        display.textContent = Math.round(receiveStampScale * 100) + '%';
    }
    if (inputScale) {
        inputScale.value = receiveStampScale.toFixed(2);
    }
}

function resetReceiveStampPos(event) {
    if (event) event.stopPropagation();
    quickSetReceiveStamp('top-right');
    receiveStampScale = 1.0;
    changeReceiveStampScale(0);
}

function quickSetReceiveStamp(pos, event) {
    if (event) event.stopPropagation();
    const stamp = document.getElementById('liveReceiveStamp');
    const inputX = document.getElementById('inputReceiveStampX');
    const inputY = document.getElementById('inputReceiveStampY');
    let x = '65%', y = '1.2cm';
    if (pos === 'top-left') {
        x = '8%';
        y = '1.2cm';
    } else if (pos === 'top-right') {
        x = '65%';
        y = '1.2cm';
    }
    if (stamp) {
        stamp.style.left = x;
        stamp.style.top = y;
    }
    if (inputX) inputX.value = x;
    if (inputY) inputY.value = y;
}

function setEditModalStampPos(x, y) {
    const elX = document.getElementById('editModalStampX');
    const elY = document.getElementById('editModalStampY');
    if (elX) elX.value = x;
    if (elY) elY.value = y;
}

function submitCancelIssue() {
    const runConfirm = () => {
        const form = document.getElementById('cancelIssueForm');
        if (form) form.submit();
    };

    if (window.AppAlert && typeof window.AppAlert.confirm === 'function') {
        AppAlert.confirm('ยืนยันยกเลิกการออกเลข?', {
            text: 'บันทึกข้อความนี้จะถูกดึงกลับไปเป็นสถานะ "รอออกเลข" เพื่อให้ท่านสามารถตรวจสอบและรันเลขใหม่ได้',
            icon: 'warning',
            confirmText: '<i class="bx bx-undo me-1"></i> ใช่, ยกเลิกการออกเลข',
            cancelText: 'ปิด'
        }).then((confirmed) => {
            if (confirmed) runConfirm();
        });
    } else if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'ยืนยันยกเลิกการออกเลข?',
            text: 'บันทึกข้อความนี้จะถูกดึงกลับไปเป็นสถานะ "รอออกเลข" เพื่อให้ท่านสามารถตรวจสอบและรันเลขใหม่ได้',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'ใช่, ยกเลิกการออกเลข',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) runConfirm();
        });
    } else {
        if (confirm('ยืนยันว่าต้องการยกเลิกการออกเลข และดึงบันทึกข้อความกลับไปเป็นสถานะรอออกเลขใหม่หรือไม่?')) {
            runConfirm();
        }
    }
}

function handleStampModeChange(mode) {
    const radioStamp = document.getElementById('modeRadioStamp');
    const radioClean = document.getElementById('modeRadioClean');
    const cardStamp = document.getElementById('modeCardStamp');
    const cardClean = document.getElementById('modeCardClean');
    const stampGroup = document.getElementById('stampDetailsGroup');
    const liveStamp = document.getElementById('liveReceiveStamp');
    const submitBtn = document.getElementById('btnSubmitIssue');
    const alertText = document.getElementById('issueFormAlertText');

    if (mode === 1) {
        if (radioStamp) radioStamp.checked = true;
        if (cardStamp) {
            cardStamp.style.setProperty('border-color', '#22c55e', 'important');
            cardStamp.style.backgroundColor = '#f0fdf4';
        }
        if (cardClean) {
            cardClean.style.setProperty('border-color', '#cbd5e1', 'important');
            cardClean.style.backgroundColor = '#ffffff';
        }
        if (stampGroup) stampGroup.style.display = 'block';
        if (liveStamp) liveStamp.style.display = 'block';
        if (submitBtn) submitBtn.innerHTML = '<i class="bx bx-stamp me-1"></i> ประทับตราออกเลขและส่งเกษียน';
        if (alertText) alertText.innerHTML = '<strong>ตรายางรับหนังสือ</strong> แสดงอยู่ที่มุมบนขวาของกระดาษ A4 ท่านสามารถแตะหรือลากย้ายเพื่อจัดวางตำแหน่งได้';
    } else {
        if (radioClean) radioClean.checked = true;
        if (cardClean) {
            cardClean.style.setProperty('border-color', '#0284c7', 'important');
            cardClean.style.backgroundColor = '#f0f9ff';
        }
        if (cardStamp) {
            cardStamp.style.setProperty('border-color', '#cbd5e1', 'important');
            cardStamp.style.backgroundColor = '#ffffff';
        }
        if (stampGroup) stampGroup.style.display = 'none';
        if (liveStamp) liveStamp.style.display = 'none';
        if (submitBtn) submitBtn.innerHTML = '<i class="bx bx-send me-1"></i> ออกเลขที่หนังสือและส่งเกษียน (ไม่สแตมป์)';
        if (alertText) alertText.innerHTML = '<strong>ระบบมาตรฐานราชการ</strong>: ออกเฉพาะเลขที่หนังสือในหัวข้อ "ที่" โดยไม่มีการประทับตรายางรับ';
    }
}

function handleModalStampModeChange(mode) {
    const radioStamp = document.getElementById('modalRadioStamp');
    const radioClean = document.getElementById('modalRadioClean');
    const cardStamp = document.getElementById('modalCardStamp');
    const cardClean = document.getElementById('modalCardClean');
    const fields = document.getElementById('modalStampFields');

    if (mode === 1) {
        if (radioStamp) radioStamp.checked = true;
        if (cardStamp) {
            cardStamp.style.setProperty('border-color', '#22c55e', 'important');
            cardStamp.style.backgroundColor = '#f0fdf4';
        }
        if (cardClean) {
            cardClean.style.setProperty('border-color', '#cbd5e1', 'important');
            cardClean.style.backgroundColor = '#ffffff';
        }
        if (fields) fields.style.display = 'block';
    } else {
        if (radioClean) radioClean.checked = true;
        if (cardClean) {
            cardClean.style.setProperty('border-color', '#0284c7', 'important');
            cardClean.style.backgroundColor = '#f0f9ff';
        }
        if (cardStamp) {
            cardStamp.style.setProperty('border-color', '#cbd5e1', 'important');
            cardStamp.style.backgroundColor = '#ffffff';
        }
        if (fields) fields.style.display = 'none';
    }
}

function syncLiveDocNo(val) {
    const el = document.getElementById('headerDocNoField');
    if (el) el.textContent = val || 'รอออกเลข';
}

function syncLiveDocDate(val) {
    const el = document.getElementById('headerDocDateField');
    if (!el || !val) return;
    const parts = val.split('-');
    if (parts.length === 3) {
        const yearTh = parseInt(parts[0], 10) + 543;
        const thaiMonths = ['', 'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'];
        const m = parseInt(parts[1], 10);
        const d = parseInt(parts[2], 10);
        const dateText = `${d} ${thaiMonths[m] || ''} ${yearTh}`;
        el.textContent = dateText;
        const stampDate = document.getElementById('liveReceiveStampDateDisplay');
        if (stampDate) stampDate.textContent = dateText;
    }
}

function syncLiveStampRegNo(val) {
    const el = document.getElementById('liveReceiveStampRegNoDisplay');
    if (el) el.textContent = val || '-';
}

function syncLiveStampTime(val) {
    const el = document.getElementById('liveReceiveStampTimeDisplay');
    if (el) el.textContent = val || '-';
}

function initLiveReceiveStampDrag() {
    const stamp = document.getElementById('liveReceiveStamp');
    const container = document.getElementById('printableMemoPaper');
    const inputX = document.getElementById('inputReceiveStampX');
    const inputY = document.getElementById('inputReceiveStampY');
    if (!stamp || !container) return;

    let isDragging = false;
    let startX = 0, startY = 0;
    let initialLeft = 0, initialTop = 0;

    function onPointerDown(e) {
        if (e.target.closest('.stamp-controls-bar') || e.target.closest('.stamp-resize-handle')) {
            return;
        }
        isDragging = true;
        const clientX = e.clientX || (e.touches && e.touches[0].clientX);
        const clientY = e.clientY || (e.touches && e.touches[0].clientY);
        startX = clientX;
        startY = clientY;

        const rect = stamp.getBoundingClientRect();
        const parentRect = container.getBoundingClientRect();
        initialLeft = rect.left - parentRect.left;
        initialTop = rect.top - parentRect.top;

        document.addEventListener('mousemove', onPointerMove);
        document.addEventListener('mouseup', onPointerUp);
        document.addEventListener('touchmove', onPointerMove, { passive: false });
        document.addEventListener('touchend', onPointerUp);
    }

    function onPointerMove(e) {
        if (!isDragging) return;
        if (e.cancelable) e.preventDefault();
        const clientX = e.clientX || (e.touches && e.touches[0].clientX);
        const clientY = e.clientY || (e.touches && e.touches[0].clientY);
        const deltaX = clientX - startX;
        const deltaY = clientY - startY;

        let newLeft = initialLeft + deltaX;
        let newTop = initialTop + deltaY;

        // ล็อกไม่ให้ลากกรอบเลยกระดาษเด็ดขาด (มีระยะกันชน 10px จากขอบทุกด้าน)
        const margin = 10;
        const scaleVal = (typeof receiveStampScale !== 'undefined' && receiveStampScale > 0) ? receiveStampScale : 1.0;
        const stampW = stamp.offsetWidth * scaleVal;
        const stampH = stamp.offsetHeight * scaleVal;
        const maxLeft = Math.max(margin, container.clientWidth - stampW - margin);
        const maxTop = Math.max(margin, container.clientHeight - stampH - margin);

        newLeft = Math.max(margin, Math.min(newLeft, maxLeft));
        newTop = Math.max(margin, Math.min(newTop, maxTop));

        const leftPct = ((newLeft / container.clientWidth) * 100).toFixed(1) + '%';
        const topPx = Math.round(newTop) + 'px';

        stamp.style.left = leftPct;
        stamp.style.top = topPx;

        if (inputX) inputX.value = leftPct;
        if (inputY) inputY.value = topPx;
    }

    function onPointerUp() {
        isDragging = false;
        document.removeEventListener('mousemove', onPointerMove);
        document.removeEventListener('mouseup', onPointerUp);
        document.removeEventListener('touchmove', onPointerMove);
        document.removeEventListener('touchend', onPointerUp);
    }

    stamp.addEventListener('mousedown', onPointerDown);
    stamp.addEventListener('touchstart', onPointerDown, { passive: true });

    // Handle corner resize
    const resizeHandle = document.getElementById('receiveStampResizeHandle');
    if (resizeHandle) {
        let isResizing = false;
        let startDist = 0;
        let initialScale = 1.0;

        function onResizeDown(e) {
            e.stopPropagation();
            if (e.cancelable) e.preventDefault();
            isResizing = true;
            const clientX = e.clientX || (e.touches && e.touches[0].clientX);
            const clientY = e.clientY || (e.touches && e.touches[0].clientY);
            const stampRect = stamp.getBoundingClientRect();
            startDist = Math.hypot(clientX - stampRect.left, clientY - stampRect.top);
            initialScale = receiveStampScale;

            document.addEventListener('mousemove', onResizeMove);
            document.addEventListener('mouseup', onResizeUp);
            document.addEventListener('touchmove', onResizeMove, { passive: false });
            document.addEventListener('touchend', onResizeUp);
        }

        function onResizeMove(e) {
            if (!isResizing) return;
            if (e.cancelable) e.preventDefault();
            const clientX = e.clientX || (e.touches && e.touches[0].clientX);
            const clientY = e.clientY || (e.touches && e.touches[0].clientY);
            const stampRect = stamp.getBoundingClientRect();
            const currentDist = Math.hypot(clientX - stampRect.left, clientY - stampRect.top);
            const scaleFactor = (currentDist / startDist);
            receiveStampScale = Math.min(Math.max(initialScale * scaleFactor, 0.6), 1.6);

            stamp.style.transform = `rotate(-1deg) scale(${receiveStampScale})`;
            const display = document.getElementById('liveReceiveStampScaleDisplay');
            const inputScale = document.getElementById('inputReceiveStampScale');
            if (display) display.textContent = Math.round(receiveStampScale * 100) + '%';
            if (inputScale) inputScale.value = receiveStampScale.toFixed(2);
        }

        function onResizeUp() {
            isResizing = false;
            document.removeEventListener('mousemove', onResizeMove);
            document.removeEventListener('mouseup', onResizeUp);
            document.removeEventListener('touchmove', onResizeMove);
            document.removeEventListener('touchend', onResizeUp);
        }

        resizeHandle.addEventListener('mousedown', onResizeDown);
        resizeHandle.addEventListener('touchstart', onResizeDown, { passive: false });
    }
}

document.addEventListener('DOMContentLoaded', function() {
    setTimeout(updatePageBreakIndicators, 150);
    window.addEventListener('resize', updatePageBreakIndicators);
    initLiveReceiveStampDrag();
});
</script>
<?= $this->endSection() ?>
