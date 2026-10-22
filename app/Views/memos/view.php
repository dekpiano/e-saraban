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
                    <a href="<?= base_url($document['file_path']) ?>" target="_blank" class="btn btn-outline-danger btn-sm">
                        <i class="bx bxs-file-pdf me-1"></i> ดูไฟล์ PDF แนบ
                    </a>
                <?php endif; ?>
                <?php if ($document['status'] === 'draft' || $document['status'] === 'returned'): ?>
                    <a href="<?= base_url('admin/memos/' . $document['id'] . '/edit') ?>" class="btn btn-warning btn-sm text-dark fw-bold">
                        <i class="bx bx-edit me-1"></i> แก้ไขข้อความ
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- A4 Document Paper -->
        <div class="card bg-light p-3 p-md-4 mb-4 border-0 shadow-sm">
            <div class="page" id="printableMemoPaper">
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
                        <span class="editable-field"><?= esc($document['doc_no']) ?></span>
                    </div>
                    <div class="col-right">
                        <span class="label">วันที่</span>
                        <span class="editable-field"><?= thai_date($document['doc_date'], 'medium') ?></span>
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

                <!-- Official Endorsements / การเกษียนหนังสือที่ลงนามแล้ว -->
                <?php 
                $signedRoutes = array_filter($routes, function($r) {
                    return $r['status'] === 'signed';
                });
                ?>
                <?php if (!empty($signedRoutes)): ?>
                    <div style="margin-top: 40px; border-top: 1px dashed #cbd5e1; padding-top: 20px;">
                        <h6 class="fw-bold text-dark mb-3" style="font-family:'Sarabun', sans-serif; font-size: 14pt;">
                            <i class="bx bx-check-shield text-success me-1"></i> บันทึกความเห็นและการเกษียนลงนาม (Digital Endorsements)
                        </h6>
                        <div class="row g-3">
                            <?php foreach ($signedRoutes as $sr): ?>
                                <div class="col-md-6">
                                    <div class="memo-endorse-box">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="badge bg-primary px-2" style="font-size: 0.75rem;">ขั้นที่ <?= $sr['step_order'] ?></span>
                                            <small class="text-muted" style="font-size: 0.75rem;"><?= thai_date($sr['signed_at'], 'medium') ?></small>
                                        </div>
                                        <div class="fw-bold text-primary mb-1" style="font-size: 13pt;">
                                            <?= esc($sr['comment_preset']) ?>
                                        </div>
                                        <?php if (!empty($sr['comment_text'])): ?>
                                            <div class="text-dark small mb-2"><?= nl2br(esc($sr['comment_text'])) ?></div>
                                        <?php endif; ?>
                                        <div class="text-center pt-2 border-top">
                                            <?php if (!empty($sr['signature_img'])): ?>
                                                <img src="<?= base_url($sr['signature_img']) ?>" alt="ลายเซ็น" style="max-height: 45px; max-width: 140px;" class="mb-1">
                                            <?php endif; ?>
                                            <div class="fw-semibold small text-dark"><?= esc(($sr['prefix'] ?? '') . ($sr['first_name'] ?? '') . ' ' . ($sr['last_name'] ?? '')) ?></div>
                                            <div class="text-muted" style="font-size: 0.75rem;"><?= esc($sr['position_name'] ?? '') ?></div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
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

        <!-- Routing Stepper Card -->
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="card-title mb-0 fw-bold text-dark">
                    <i class="bx bx-git-branch me-1 text-primary"></i> เส้นทางการเดินแฟ้มเกษียน
                </h6>
            </div>
            <div class="card-body p-3">
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

<script>
function promptReturnMemo() {
    const reason = prompt('กรุณาระบุเหตุผลหรือข้อความที่ต้องการให้ผู้เสนอเรื่องแก้ไข:');
    if (reason && reason.trim() !== '') {
        document.getElementById('signActionType').value = 'return';
        const form = document.getElementById('signForm');
        const textInput = form.querySelector('textarea[name="comment_text"]');
        if (textInput) textInput.value = reason;
        form.submit();
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

document.addEventListener('DOMContentLoaded', function() {
    setTimeout(updatePageBreakIndicators, 150);
    window.addEventListener('resize', updatePageBreakIndicators);
});
</script>
<?= $this->endSection() ?>
