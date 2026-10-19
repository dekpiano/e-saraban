<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>หนังสือเลขที่ <?= esc($document['doc_no']) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Breadcrumb & Header -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">แดชบอร์ด</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('admin/documents') ?>">ทะเบียนหนังสือ</a></li>
                <li class="breadcrumb-item active">รายละเอียดและเกษียนหนังสือ</li>
            </ol>
        </nav>
        <h4 class="fw-bold mb-0 text-primary">
            <?= doc_type_badge($document['doc_type']) ?>
            <?= esc($document['doc_no']) ?>: <?= esc($document['title']) ?>
        </h4>
    </div>
    <div class="d-flex gap-2 mt-2 mt-md-0">
        <a href="<?= base_url('admin/documents') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bx bx-arrow-back me-1"></i> ย้อนกลับ
        </a>
        <?php if (!empty($document['file_path'])): ?>
            <a href="<?= document_url($document['file_path']) ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                <i class="bx bx-download me-1"></i> ดาวน์โหลด PDF
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Workflow Visual Stepper -->
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-body py-4">
        <h6 class="text-muted fw-bold text-uppercase mb-4" style="font-size: 0.8rem; letter-spacing: 0.5px;">
            <i class="bx bx-git-commit me-1 text-primary"></i> ลำดับขั้นตอนการเสนอแฟ้มและลงนามดิจิทัล (Workflow Progress)
        </h6>
        <div class="step-stepper">
            <?php foreach ($routes as $r): ?>
                <?php
                    $isDone    = $r['status'] === 'signed';
                    $isCurrent = ($r['step_order'] == $document['current_step'] && $document['status'] === 'routing');
                    $isReturn  = $r['status'] === 'returned';
                    $bubbleClass = $isDone ? 'signed' : ($isReturn ? 'returned' : ($isCurrent ? 'active' : 'pending'));
                ?>
                <div class="step-item flex-fill">
                    <div class="step-bubble <?= $bubbleClass ?>">
                        <?php if ($isDone): ?>
                            <i class="bx bx-check fs-3"></i>
                        <?php elseif ($isReturn): ?>
                            <i class="bx bx-x fs-3"></i>
                        <?php elseif ($isCurrent): ?>
                            <i class="bx bx-edit fs-4"></i>
                        <?php else: ?>
                            <?= $r['step_order'] ?>
                        <?php endif; ?>
                    </div>
                    <div class="fw-bold text-dark small mb-0">
                        <?= esc($r['prefix'] . $r['first_name'] . ' ' . $r['last_name']) ?>
                    </div>
                    <div class="text-muted" style="font-size: 0.75rem;">
                        <?= esc($r['position_name']) ?>
                    </div>
                    <div class="mt-1">
                        <?php if ($isDone): ?>
                            <span class="badge bg-label-success" style="font-size: 0.7rem;">ลงนามแล้ว</span>
                        <?php elseif ($isReturn): ?>
                            <span class="badge bg-label-danger" style="font-size: 0.7rem;">ส่งคืนแก้ไข</span>
                        <?php elseif ($isCurrent): ?>
                            <span class="badge bg-primary" style="font-size: 0.7rem;">กำลังพิจารณา</span>
                        <?php else: ?>
                            <span class="badge bg-label-secondary" style="font-size: 0.7rem;">รอดำเนินการ</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="row">
    <!-- Left Column: Document Details & PDF Preview -->
    <div class="col-lg-7 mb-4">
        <!-- Details Card -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="bx bx-info-circle text-primary me-2"></i> ข้อมูลหนังสือราชการ
                </h5>
                <div>
                    <?= urgency_badge($document['urgency']) ?>
                    <?= doc_status_badge($document['status']) ?>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <small class="text-muted d-block">เลขทะเบียนภายใน รร.</small>
                        <strong class="text-dark"><?= esc($document['internal_reg_no'] ?: '-') ?></strong>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">เลขที่หนังสือ</small>
                        <strong class="text-primary"><?= esc($document['doc_no']) ?></strong>
                    </div>
                    <div class="col-12">
                        <small class="text-muted d-block">เรื่อง</small>
                        <h6 class="text-dark fw-bold mb-0"><?= esc($document['title']) ?></h6>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">จาก</small>
                        <span><?= esc($document['from_org']) ?></span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">ถึง</small>
                        <span><?= esc($document['to_org']) ?></span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">ลงวันที่ในหนังสือ</small>
                        <span><?= thai_date($document['doc_date'], 'short') ?></span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">วันที่รับเข้าสารบรรณ</small>
                        <span><?= thai_date($document['receive_date'], 'short') ?></span>
                    </div>
                    <div class="col-12">
                        <small class="text-muted d-block">ผู้เสนอเรื่อง / ผู้ลงทะเบียน</small>
                        <span><?= esc(($document['prefix'] ?? '') . ($document['first_name'] ?? '') . ' ' . ($document['last_name'] ?? '')) ?> (<?= esc($document['creator_department'] ?? '-') ?>)</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- PDF Preview Card -->
        <div class="card mb-4">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="bx bxs-file-pdf text-danger me-2"></i> ไฟล์เอกสารแนบต้นฉบับ
                </h5>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#uploadDocModal">
                        <i class="bx bx-upload me-1"></i> อัปโหลด / เปลี่ยนไฟล์แนบ
                    </button>
                    <?php if (!empty($document['file_path'])): ?>
                        <a href="<?= document_url($document['file_path']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                            <i class="bx bx-fullscreen me-1"></i> เปิดดูเต็มจอ
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($document['file_path'])): ?>
                    <div style="height: 520px; width: 100%; background: #525659;">
                        <iframe src="<?= document_url($document['file_path']) ?>#toolbar=0" style="width: 100%; height: 100%; border: none;">
                            <p>เบราว์เซอร์ของคุณไม่รองรับการแสดงตัวอย่าง PDF <a href="<?= document_url($document['file_path']) ?>">คลิกที่นี่เพื่อดาวน์โหลดไฟล์</a></p>
                        </iframe>
                    </div>
                <?php else: ?>
                    <div class="p-5 text-center text-muted">
                        <i class="bx bx-file-blank display-4 text-secondary mb-2 d-block"></i>
                        <h6>ยังไม่มีไฟล์เอกสารแนบ</h6>
                        <button type="button" class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#uploadDocModal">
                            <i class="bx bx-upload me-1"></i> อัปโหลดไฟล์เอกสาร (PDF)
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Modal Upload Document File -->
        <div class="modal fade" id="uploadDocModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="<?= base_url('admin/documents/' . $document['id'] . '/upload') ?>" method="POST" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold text-primary">
                                <i class="bx bx-upload me-1"></i> อัปโหลด / เปลี่ยนไฟล์เอกสารแนบ
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label class="form-label required fw-bold text-dark">เลือกไฟล์เอกสารต้นเรื่อง (รองรับไฟล์ PDF)</label>
                                
                                <!-- Hidden actual file input -->
                                <input type="file" name="doc_file" id="modalDocFileInput" accept=".pdf,application/pdf" style="display: none;" required />

                                <!-- Drag & Drop Box -->
                                <div id="modalDocDropzone" class="dropzone-box" onclick="triggerBrowseModalDocFile()">
                                    <div class="d-flex flex-column align-items-center">
                                        <div class="mb-2">
                                            <span class="badge bg-label-primary p-3 rounded-circle">
                                                <i class="bx bxs-cloud-upload fs-2 text-primary"></i>
                                            </span>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-1">ลากและวางไฟล์ PDF ที่นี่ หรือคลิกเพื่อเลือกไฟล์</h6>
                                        <p class="text-muted small mb-2">รองรับเฉพาะเอกสารรูปแบบ <strong>.PDF</strong></p>
                                        <button type="button" class="btn btn-outline-primary btn-sm px-3 shadow-xs">
                                            <i class="bx bx-file me-1"></i> เลือกไฟล์ PDF จากคอมพิวเตอร์
                                        </button>
                                    </div>
                                </div>

                                <!-- File Preview Box -->
                                <div id="modalDocFilePreview" class="dropzone-preview mt-3">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                                        <div class="d-flex align-items-center">
                                            <div class="badge bg-label-danger p-2 rounded-3 me-2">
                                                <i class="bx bxs-file-pdf fs-3 text-danger"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-bold text-dark text-break" id="modalDocPreviewName">filename.pdf</h6>
                                                <small class="text-muted" id="modalDocPreviewSize">0 KB</small>
                                            </div>
                                        </div>
                                        <div>
                                            <button type="button" class="btn btn-outline-danger btn-xs py-1 px-2" onclick="removeModalDocFile(event)">
                                                <i class="bx bx-trash me-1"></i> ลบ / เลือกใหม่
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-text small text-secondary mt-2">
                                    <i class="bx bx-info-circle me-1 text-primary"></i> ไฟล์ที่อัปโหลดจะถูกแสดงในหน้าพรีวิวและใช้ประกอบการพิจารณาเกษียนหนังสือ
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i> บันทึกและอัปโหลดไฟล์
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Audit Trail Card -->
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0 fw-bold">
                    <i class="bx bx-history text-secondary me-2"></i> บันทึกประวัติการดำเนินการ (Audit Logs)
                </h6>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush small">
                    <?php foreach ($logs as $log): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
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

    <!-- Right Column: Endorsement Slips & e-Signature Action -->
    <div class="col-lg-5 mb-4">

        <!-- ACTIVE SIGNING PANEL (If it is current user's turn) -->
        <?php if ($canSign): ?>
            <div class="card border-warning mb-4 shadow">
                <div class="card-header bg-warning text-dark py-3">
                    <div class="d-flex align-items-center">
                        <i class="bx bx-edit-alt fs-3 me-2"></i>
                        <div>
                            <h5 class="card-title mb-0 text-dark fw-bold">ถึงขั้นตอนของคุณในการพิจารณาลงนาม!</h5>
                            <small>ขั้นตอนที่ <?= esc($currentRoute['step_order']) ?>: <?= esc(session()->get('position_name')) ?></small>
                        </div>
                    </div>
                </div>

                <div class="card-body pt-3">
                    <form action="<?= base_url('admin/documents/' . $document['id'] . '/sign') ?>" method="POST" id="signForm">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action_type" id="action_type" value="approve" />
                        <input type="hidden" name="signature_data" id="signature_data" value="" />

                        <!-- Preset Comments -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">ข้อความเกษียนสำเร็จรูป (คลิกเพื่อเลือก)</label>
                            <div class="d-flex flex-wrap gap-1 mb-2">
                                <button type="button" class="btn btn-xs btn-outline-primary" onclick="setComment('เพื่อโปรดพิจารณา')">เพื่อโปรดพิจารณา</button>
                                <button type="button" class="btn btn-xs btn-outline-success" onclick="setComment('เห็นควรอนุมัติตามเสนอ')">เห็นควรอนุมัติ</button>
                                <button type="button" class="btn btn-xs btn-outline-info" onclick="setComment('ทราบ / ถือปฏิบัติตาม')">ทราบ/ปฏิบัติ</button>
                                <button type="button" class="btn btn-xs btn-outline-warning" onclick="setComment('อนุมัติตามเสนอ')">อนุมัติ</button>
                                <button type="button" class="btn btn-xs btn-outline-secondary" onclick="setComment('มอบหมายดำเนินการตามเสนอ')">มอบหมายดำเนินการ</button>
                            </div>
                            <input type="text" class="form-control" name="comment_preset" id="comment_preset" placeholder="ระบุหัวข้อคำสั่งการ/เกษียน" value="เห็นควรอนุมัติตามเสนอ" required />
                        </div>

                        <!-- Comment Text / Notes -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">บันทึกข้อความสั่งการ / ความเห็นเพิ่มเติม</label>
                            <textarea class="form-control" name="comment_text" id="comment_text" rows="2" placeholder="พิมพ์ข้อความเพิ่มเติม (ถ้ามี)...">ตรวจสอบแล้ว ถูกต้องตามระเบียบ เห็นควรอนุมัติการดำเนินงาน</textarea>
                        </div>

                        <!-- Signature Type Selection -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold d-block">รูปแบบลายเซ็นดิจิทัล (e-Signature)</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="signature_type" id="sigDraw" value="draw" checked onchange="toggleSigMode('draw')" />
                                <label class="form-check-label" for="sigDraw"><i class="bx bx-pencil me-1"></i> เซ็นสดด้วยลายมือ</label>
                            </div>
                            <?php if (!empty(session()->get('signature_img'))): ?>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="signature_type" id="sigPreset" value="preset" onchange="toggleSigMode('preset')" />
                                    <label class="form-check-label" for="sigPreset"><i class="bx bx-image me-1"></i> ใช้ลายเซ็นที่บันทึกไว้ในระบบ</label>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Draw Canvas Area -->
                        <div id="drawSigArea" class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <small class="text-muted"><i class="bx bx-fingerprint me-1"></i> ใช้เมาส์หรือนิ้วลากลายเซ็นในกรอบด้านล่าง</small>
                                <button type="button" class="btn btn-xs btn-outline-danger" id="clearCanvasBtn">
                                    <i class="bx bx-eraser me-1"></i> ล้างลายเซ็น
                                </button>
                            </div>
                            <div class="signature-pad-container p-1 text-center">
                                <canvas id="signatureCanvas" width="380" height="140" style="width: 100%; height: 140px; cursor: crosshair;"></canvas>
                            </div>
                        </div>

                        <!-- Preset Signature Preview -->
                        <?php if (!empty(session()->get('signature_img'))): ?>
                            <div id="presetSigArea" class="mb-3 p-3 bg-light rounded text-center" style="display: none;">
                                <small class="text-muted d-block mb-1">ลายเซ็นที่บันทึกไว้ในโปรไฟล์ของคุณ:</small>
                                <img src="<?= base_url(session()->get('signature_img')) ?>" class="signature-preview border p-1 bg-white" alt="Saved signature" />
                            </div>
                        <?php endif; ?>

                        <!-- Action Buttons -->
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-success py-2 fw-bold" onclick="submitSign('approve', this)">
                                <i class="bx bx-check-circle me-1 fs-5 align-middle"></i> ยืนยันการลงนามเกษียนหนังสือ
                            </button>
                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="submitSign('return', this)">
                                <i class="bx bx-revision me-1"></i> ส่งคืนหนังสือให้แก้ไข (Return)
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <!-- ENDORSEMENT SLIPS / เกษียนหนังสือดิจิทัล -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0 fw-bold text-dark">
                    <i class="bx bx-certification text-success me-2"></i> ใบปะหน้าเกษียนหนังสือดิจิทัล (Routing Slips)
                </h5>
            </div>
            <div class="card-body p-3">
                <div class="d-flex flex-column gap-3">
                    <?php foreach ($routes as $r): ?>
                        <div class="card border <?= $r['status'] === 'signed' ? 'border-success' : ($r['status'] === 'returned' ? 'border-danger' : 'border-light bg-light') ?>">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="badge <?= $r['status'] === 'signed' ? 'bg-success' : ($r['status'] === 'returned' ? 'bg-danger' : 'bg-secondary') ?>">
                                        ลำดับที่ <?= $r['step_order'] ?>: <?= esc($r['position_name']) ?>
                                    </span>
                                    <?php if ($r['status'] === 'signed'): ?>
                                        <small class="text-success fw-bold"><i class="bx bx-check-circle me-1"></i> ลงนามแล้ว</small>
                                    <?php elseif ($r['status'] === 'returned'): ?>
                                        <small class="text-danger fw-bold"><i class="bx bx-x-circle me-1"></i> ส่งคืนแก้ไข</small>
                                    <?php else: ?>
                                        <small class="text-muted"><i class="bx bx-time me-1"></i> ยังไม่ถึงคิว</small>
                                    <?php endif; ?>
                                </div>

                                <!-- Comment & Signature Box -->
                                <?php if ($r['status'] === 'signed' || $r['status'] === 'returned'): ?>
                                    <div class="p-3 bg-white rounded border my-2">
                                        <div class="fw-bold text-dark mb-1">
                                            “<?= esc($r['comment_preset']) ?>”
                                        </div>
                                        <?php if (!empty($r['comment_text'])): ?>
                                            <div class="text-muted small mb-2 fst-italic">
                                                <?= nl2br(esc($r['comment_text'])) ?>
                                            </div>
                                        <?php endif; ?>

                                        <!-- Signature Image -->
                                        <div class="d-flex justify-content-between align-items-end pt-2 border-top">
                                            <div>
                                                <?php if (!empty($r['signature_img'])): ?>
                                                    <img src="<?= base_url($r['signature_img']) ?>" class="signature-preview d-block mb-1" alt="Signature" />
                                                <?php else: ?>
                                                    <div class="badge bg-label-secondary mb-1">ประทับลายเซ็นดิจิทัล</div>
                                                <?php endif; ?>
                                                <div class="fw-semibold text-dark small">
                                                    (<?= esc($r['prefix'] . $r['first_name'] . ' ' . $r['last_name']) ?>)
                                                </div>
                                                <small class="text-muted d-block"><?= esc($r['position_name']) ?></small>
                                            </div>

                                            <div class="text-end">
                                                <small class="text-muted d-block" style="font-size: 0.75rem;">
                                                    <i class="bx bx-calendar me-1"></i> <?= thai_date($r['signed_at'], 'short', true) ?>
                                                </small>
                                                <?php if (!empty($r['verify_hash'])): ?>
                                                    <a href="<?= base_url('verify/' . $r['verify_hash']) ?>" target="_blank" class="badge bg-label-info text-decoration-none mt-1" style="font-size: 0.68rem;">
                                                        <i class="bx bx-qr-scan me-1"></i> ตรวจสอบความถูกต้อง
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="py-3 text-center text-muted small">
                                        <i class="bx bx-loader-alt bx-spin me-1"></i> รอดำเนินการเกษียนหนังสือจากขั้นตอนนี้
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
let signaturePad = null;

document.addEventListener('DOMContentLoaded', function () {
    const canvas = document.getElementById('signatureCanvas');
    if (canvas) {
        // Adjust canvas resolution for retina displays
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext("2d").scale(ratio, ratio);

        signaturePad = new SignaturePad(canvas, {
            penColor: 'rgb(24, 43, 73)',
            backgroundColor: 'rgba(255, 255, 255, 0)',
            minWidth: 1.5,
            maxWidth: 3.5
        });

        document.getElementById('clearCanvasBtn').addEventListener('click', function () {
            signaturePad.clear();
        });
    }
});

function toggleSigMode(mode) {
    if (mode === 'draw') {
        document.getElementById('drawSigArea').style.display = 'block';
        if (document.getElementById('presetSigArea')) {
            document.getElementById('presetSigArea').style.display = 'none';
        }
    } else {
        document.getElementById('drawSigArea').style.display = 'none';
        if (document.getElementById('presetSigArea')) {
            document.getElementById('presetSigArea').style.display = 'block';
        }
    }
}

function setComment(preset) {
    document.getElementById('comment_preset').value = preset;
}

function submitSign(action, btn) {
    document.getElementById('action_type').value = action;

    if (action === 'return') {
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
        return;
    }

    const sigType = document.querySelector('input[name="signature_type"]:checked').value;
    if (sigType === 'draw') {
        if (!signaturePad || signaturePad.isEmpty()) {
            AppAlert.warning('กรุณาเซ็นชื่อลงในช่องลายเซ็นก่อนกดยืนยัน', 'ยังไม่ได้ลงนาม');
            return;
        }
        document.getElementById('signature_data').value = signaturePad.toDataURL('image/png');
    }

    if (btn && window.SubmitLoading) {
        window.SubmitLoading.set(btn, 'กำลังบันทึกลงนามเกษียน...');
    }
    document.getElementById('signForm').submit();
}

// Document Modal Drag & Drop Handlers
function triggerBrowseModalDocFile() {
    const fileInput = document.getElementById('modalDocFileInput');
    if (fileInput) fileInput.click();
}

function handleModalDocFileSelect(file) {
    if (!file) return;

    if (file.type !== 'application/pdf' && !file.name.toLowerCase().endsWith('.pdf')) {
        Swal.fire({
            icon: 'warning',
            title: 'รูปแบบไฟล์ไม่ถูกต้อง',
            text: 'ระบบรองรับเฉพาะไฟล์เอกสารประเภท .PDF เท่านั้น',
            confirmButtonColor: '#696cff'
        });
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
    if (preview) {
        preview.style.display = 'block';
        preview.classList.add('show');
    }
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
    if (preview) {
        preview.style.display = 'none';
        preview.classList.remove('show');
    }
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
                modalDropzone.classList.add('dragover');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            modalDropzone.addEventListener(eventName, function(e) {
                e.preventDefault();
                e.stopPropagation();
                modalDropzone.classList.remove('dragover');
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

    // Reset when modal is dismissed
    const uploadModalEl = document.getElementById('uploadDocModal');
    if (uploadModalEl) {
        uploadModalEl.addEventListener('hidden.bs.modal', function () {
            removeModalDocFile();
        });
    }
});
</script>
<?= $this->endSection() ?>
