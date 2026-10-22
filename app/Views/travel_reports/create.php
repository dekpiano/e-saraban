<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>เขียนรายงานผลการไปราชการ<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Page Header Banner -->
<div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #696cff 0%, #5f61e6 50%, #4338ca 100%); border-radius: 1rem; position: relative; overflow: hidden;">
    <div class="card-body p-4 text-white">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>" class="text-white opacity-75">แดชบอร์ด</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('admin/travel-reports') ?>" class="text-white opacity-75">รายงานผลไปราชการ</a></li>
                        <li class="breadcrumb-item active text-white fw-bold">เขียนรายงานผล</li>
                    </ol>
                </nav>
                <h4 class="fw-bold mb-1 text-white" style="font-family: 'K2D', sans-serif;">
                    <i class="bx bx-edit me-2"></i>เขียนรายงานผลการไปราชการ
                </h4>
                <p class="mb-0 text-white opacity-90 small">
                    กรอกสรุปสาระสำคัญผลการปฏิบัติราชการ อ้างอิงจากหนังสือขออนุมัติเดิม พร้อมแนบไฟล์เล่มรายงานหรือเกียรติบัตร (PDF)
                </p>
            </div>
            <div>
                <a href="<?= base_url('admin/travel-reports') ?>" class="btn btn-outline-light btn-sm fw-bold px-3">
                    <i class="bx bx-arrow-back me-1"></i> ย้อนกลับ
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-10 col-12">
        <form action="<?= base_url('admin/travel-reports/store') ?>" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <input type="hidden" name="memo_id" value="<?= (int)($memoId ?? 0) ?>">
            <?php if (!empty($memo)): ?>
                <div class="alert alert-info d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
                    <div><strong><i class="bx bx-file me-1"></i>บันทึกข้อความรายงานผล</strong><div class="small mt-1"><?= esc(($memo['doc_no'] ?? '-') . ' · ' . ($memo['title'] ?? '-')) ?></div></div>
                    <a class="btn btn-sm btn-outline-primary" target="_blank" href="<?= base_url('admin/memos/' . (int)$memoId) ?>">เปิดบันทึกข้อความ</a>
                </div>
            <?php endif; ?>

            <!-- Card 1: เลือกคำขอไปราชการต้นเรื่อง (Official Reference) -->
            <div class="card border mb-4 shadow-sm" style="border-radius: 0.85rem;">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                        <span class="badge bg-primary text-white p-2 rounded-circle me-2">
                            <i class="bx bx-bookmark-alt fs-5"></i>
                        </span>
                        1. อ้างอิงหนังสือขออนุมัติไปราชการต้นเรื่อง
                    </h6>
                    <span class="badge bg-label-info font-monospace">Official Reference</span>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label required fw-bold text-dark">
                            เลือกคำขอไปราชการที่ได้รับอนุมัติแล้ว:
                        </label>
                        <?php if (empty($pendingRequests) && empty($selectedTravel)): ?>
                            <div class="alert alert-warning mb-0 py-3 d-flex align-items-center">
                                <i class="bx bx-error-circle fs-3 me-2"></i>
                                <div>
                                    <strong>ไม่พบคำขอไปราชการที่รอการรายงานผล:</strong>
                                    <span class="d-block small text-muted">ท่านสามารถรายงานผลได้เฉพาะคำขอที่ผ่านการอนุมัติการเดินทางเสร็จสมบูรณ์เรียบร้อยแล้วเท่านั้น</span>
                                </div>
                            </div>
                        <?php else: ?>
                            <select name="travel_id" id="travelSelect" class="form-select form-select-lg" required onchange="handleTravelSelectChange(this.value)">
                                <option value="">-- โปรดเลือกคำขอไปราชการที่ต้องการส่งรายงาน --</option>
                                <?php foreach ($pendingRequests as $p): ?>
                                    <?php
                                        $isSelected = (!empty($selectedTravel) && (int)$selectedTravel['id'] === (int)$p['id']);
                                    ?>
                                    <option value="<?= $p['id'] ?>" <?= $isSelected ? 'selected' : '' ?>>
                                        <?= esc($p['doc_no']) ?> — <?= esc($p['title']) ?> (<?= esc($p['destination']) ?>, <?= thai_date($p['start_date'], 'short') ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="form-text text-muted">
                                <i class="bx bx-info-circle me-1"></i> เลือกรรายการคำขอที่ต้องการ ระบบจะดึงข้อมูลหนังสือต้นเรื่องมาแสดงโดยอัตโนมัติ
                            </small>
                            <?php if (empty($memoId)): ?>
                                <a id="createMemoFirstButton" href="<?= !empty($selectedTravel) ? base_url('admin/memos/create?purpose=travel_report&travel_id=' . (int)$selectedTravel['id']) : '#' ?>" class="btn btn-outline-primary btn-sm mt-2 <?= empty($selectedTravel) ? 'disabled' : '' ?>">
                                    <i class="bx bx-file-blank me-1"></i>สร้างบันทึกข้อความรายงานผลก่อน
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    <!-- กล่องแสดงรายละเอียดคำขอที่อ้างถึง (Auto Preview) -->
                    <div id="referenceCard" class="<?= empty($selectedTravel) ? 'd-none' : '' ?>">
                        <div class="alert alert-light border border-primary border-start-4 mb-0 shadow-xs" style="border-left: 5px solid #696cff !important; background-color: #f8faff;">
                            <div class="d-flex align-items-start gap-3">
                                <span class="badge bg-label-primary p-2 rounded-circle flex-shrink-0 mt-1">
                                    <i class="bx bx-check-shield fs-4 text-primary"></i>
                                </span>
                                <div class="flex-grow-1">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-1">
                                        <span class="badge bg-primary text-white font-monospace px-2 py-1" id="refDocNo">
                                            <?= !empty($selectedTravel) ? esc($selectedTravel['doc_no']) : '' ?>
                                        </span>
                                        <span class="badge bg-success text-white py-1 px-2" style="font-size:0.75rem;">
                                            <i class="bx bx-check-double me-1"></i>อนุมัติการเดินทางแล้ว
                                        </span>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-2" id="refTitle">
                                        <?= !empty($selectedTravel) ? esc($selectedTravel['title']) : '' ?>
                                    </h6>
                                    <div class="row g-2 small text-secondary">
                                        <div class="col-md-6 col-12">
                                            <i class="bx bx-user me-1 text-primary"></i> ผู้ขอ: <strong class="text-dark" id="refRequester"><?= !empty($selectedTravel) ? esc($selectedTravel['prefix'] . $selectedTravel['first_name'] . ' ' . $selectedTravel['last_name']) : '' ?></strong>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <i class="bx bx-map-pin me-1 text-danger"></i> สถานที่: <span class="text-dark" id="refDestination"><?= !empty($selectedTravel) ? esc($selectedTravel['destination'] . ' (' . $selectedTravel['province'] . ')') : '' ?></span>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <i class="bx bx-calendar me-1 text-success"></i> ช่วงวันเดินทาง: <span class="text-dark" id="refDates"><?= !empty($selectedTravel) ? (thai_date($selectedTravel['start_date'], 'short') . ' - ' . thai_date($selectedTravel['end_date'], 'short') . ' (' . $selectedTravel['total_days'] . ' วัน)') : '' ?></span>
                                        </div>
                                        <div class="col-md-6 col-12" id="refCompanionsBox">
                                            <i class="bx bx-group me-1 text-info"></i> ผู้ร่วมเดินทาง: <span class="text-dark" id="refCompanions"><?= !empty($selectedTravel['companions']) ? esc($selectedTravel['companions']) : 'ไม่มี' ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: สรุปผลการปฏิบัติราชการและแนบไฟล์ (PDF) -->
            <div class="card border mb-4 shadow-sm" style="border-radius: 0.85rem;">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                        <span class="badge bg-success text-white p-2 rounded-circle me-2">
                            <i class="bx bx-detail fs-5"></i>
                        </span>
                        2. สรุปผลการปฏิบัติราชการและประโยชน์ที่ได้รับ
                    </h6>
                    <span class="badge bg-label-success">บันทึกข้อความสรุปผล</span>
                </div>
                <div class="card-body p-4">
                    <div class="mb-4">
                        <label class="form-label required fw-bold text-dark">
                            สรุปผลการปฏิบัติงาน / ผลการประชุม / ประโยชน์ที่ได้รับต่อโรงเรียนและนักเรียน:
                        </label>
                        <textarea name="report_detail" class="form-control" rows="6" required placeholder="ระบุสรุปสาระสำคัญของการเข้าร่วมประชุม/อบรม/ปฏิบัติราชการ ความรู้และประโยชน์ที่ได้รับ เช่น การนำองค์ความรู้มาขยายผลสู่การจัดการเรียนการสอน หรือข้อเสนอแนะต่อโรงเรียน..."><?= !empty($selectedTravel['report_detail']) ? esc($selectedTravel['report_detail']) : '' ?></textarea>
                        <small class="form-text text-muted">
                            <i class="bx bx-edit-alt me-1"></i> บันทึกข้อความส่วนนี้จะถูกใช้เป็นสรุปรายงานทางการในระบบ
                        </small>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold text-dark">
                            แนบเล่มรายงานสรุปผล / เกียรติบัตรการเข้าร่วม (PDF)
                            <span class="badge bg-label-info ms-1 small" style="font-size:0.75rem;">ไม่บังคับแนบไฟล์</span>
                        </label>

                        <!-- Hidden File Input -->
                        <input type="file" name="report_file" id="reportFileInput" accept=".pdf,application/pdf" style="display: none;" />

                        <!-- Drag & Drop Dropzone Box -->
                        <div id="reportDropzone" class="dropzone-box" onclick="triggerBrowseReportFile()">
                            <div class="d-flex flex-column align-items-center">
                                <div class="mb-2">
                                    <span class="badge bg-label-primary p-3 rounded-circle">
                                        <i class="bx bxs-cloud-upload fs-1 text-primary"></i>
                                    </span>
                                </div>
                                <h5 class="fw-bold text-dark mb-1">ลากและวางไฟล์ PDF ที่นี่ หรือคลิกเพื่อเลือกไฟล์</h5>
                                <p class="text-muted small mb-2">รองรับเฉพาะเอกสารรูปแบบ <strong>.PDF</strong> (เล่มรายงานสรุปผล, เอกสารประกอบการประชุม หรือ เกียรติบัตร)</p>
                                <button type="button" class="btn btn-outline-primary btn-sm px-3 shadow-xs">
                                    <i class="bx bx-file me-1"></i> เลือกไฟล์ PDF จากคอมพิวเตอร์
                                </button>
                            </div>
                        </div>

                        <!-- File Preview Card when file is selected -->
                        <div id="reportFilePreview" class="dropzone-preview mt-3">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                                <div class="d-flex align-items-center">
                                    <div class="badge bg-label-danger p-2 rounded-3 me-3">
                                        <i class="bx bxs-file-pdf fs-2 text-danger"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold text-dark text-break" id="reportPreviewFileName">filename.pdf</h6>
                                        <div class="d-flex align-items-center gap-2 mt-1">
                                            <small class="text-muted" id="reportPreviewFileSize">0 KB</small>
                                            <span class="badge bg-label-success" style="font-size: 0.7rem;"><i class="bx bx-check-circle me-1"></i>พร้อมแนบในรายงาน</span>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeReportFile(event)">
                                        <i class="bx bx-trash me-1"></i> ลบ / เลือกใหม่
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Bar -->
            <div class="card border-0 shadow-sm p-3 mb-5" style="border-radius: 0.85rem; background: #ffffff;">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="<?= base_url('admin/travel-reports') ?>" class="btn btn-outline-secondary px-4">
                        ยกเลิก
                    </a>
                    <button type="submit" class="btn btn-success px-4 py-2 fw-bold shadow-sm" style="font-size: 1rem;">
                        <i class="bx bx-check-circle me-1"></i> บันทึกส่งรายงานผลการไปราชการ
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Dynamic fetch of travel request info
function handleTravelSelectChange(travelId) {
    const card = document.getElementById('referenceCard');
    const memoButton = document.getElementById('createMemoFirstButton');
    if (memoButton) {
        memoButton.href = travelId ? '<?= base_url('admin/memos/create?purpose=travel_report&travel_id=') ?>' + encodeURIComponent(travelId) : '#';
        memoButton.classList.toggle('disabled', !travelId);
    }
    if (!travelId) {
        card.classList.add('d-none');
        return;
    }

    fetch('<?= base_url('admin/travel-reports/get-travel-info/') ?>' + travelId)
        .then(response => response.json())
        .then(result => {
            if (result.status === 'success') {
                const data = result.data;
                document.getElementById('refDocNo').textContent = data.doc_no;
                document.getElementById('refTitle').textContent = data.title;
                document.getElementById('refRequester').textContent = data.requester + (data.position ? ' (' + data.position + ')' : '');
                document.getElementById('refDestination').textContent = data.destination + (data.province ? ' (' + data.province + ')' : '');
                document.getElementById('refDates').textContent = data.start_date + ' - ' + data.end_date + ' (' + data.total_days + ' วัน)';
                document.getElementById('refCompanions').textContent = data.companions || 'ไม่มี';

                card.classList.remove('d-none');
            } else {
                card.classList.add('d-none');
            }
        })
        .catch(err => {
            console.error('Error fetching travel info:', err);
            card.classList.add('d-none');
        });
}

// Drag & Drop File Handlers
function triggerBrowseReportFile() {
    const input = document.getElementById('reportFileInput');
    if (input) input.click();
}

function handleReportFileSelect(file) {
    if (!file) return;

    if (file.type !== 'application/pdf' && !file.name.toLowerCase().endsWith('.pdf')) {
        Swal.fire({
            icon: 'warning',
            title: 'รูปแบบไฟล์ไม่ถูกต้อง',
            text: 'ระบบรองรับเฉพาะไฟล์เอกสารรายงานหรือเกียรติบัตรประเภท .PDF เท่านั้น',
            confirmButtonColor: '#696cff'
        });
        removeReportFile();
        return;
    }

    const dropzone = document.getElementById('reportDropzone');
    const preview = document.getElementById('reportFilePreview');
    const nameEl = document.getElementById('reportPreviewFileName');
    const sizeEl = document.getElementById('reportPreviewFileSize');

    if (nameEl) nameEl.textContent = file.name;
    if (sizeEl) sizeEl.textContent = formatBytes(file.size);

    if (dropzone) dropzone.style.display = 'none';
    if (preview) {
        preview.style.display = 'block';
        preview.classList.add('show');
    }
}

function removeReportFile(e) {
    if (e) {
        e.preventDefault();
        e.stopPropagation();
    }
    const input = document.getElementById('reportFileInput');
    const dropzone = document.getElementById('reportDropzone');
    const preview = document.getElementById('reportFilePreview');

    if (input) input.value = '';
    if (preview) {
        preview.style.display = 'none';
        preview.classList.remove('show');
    }
    if (dropzone) dropzone.style.display = 'block';
}

function formatBytes(bytes, decimals = 2) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const dm = decimals < 0 ? 0 : decimals;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
}

document.addEventListener('DOMContentLoaded', function() {
    const dropzone = document.getElementById('reportDropzone');
    const fileInput = document.getElementById('reportFileInput');

    if (dropzone && fileInput) {
        ['dragenter', 'dragover'].forEach(evtName => {
            dropzone.addEventListener(evtName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.add('dragover');
            });
        });

        ['dragleave', 'drop'].forEach(evtName => {
            dropzone.addEventListener(evtName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.remove('dragover');
            });
        });

        dropzone.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            if (files && files.length > 0) {
                fileInput.files = files;
                handleReportFileSelect(files[0]);
            }
        });

        fileInput.addEventListener('change', function() {
            if (this.files && this.files.length > 0) {
                handleReportFileSelect(this.files[0]);
            }
        });
    }
});
</script>
<?= $this->endSection() ?>
