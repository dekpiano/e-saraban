<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>แก้ไขรายงานผลการไปราชการ <?= esc($travel['doc_no']) ?><?= $this->endSection() ?>

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
                        <li class="breadcrumb-item"><a href="<?= base_url('admin/travel-reports/' . $travel['id']) ?>" class="text-white opacity-75"><?= esc($travel['doc_no']) ?></a></li>
                        <li class="breadcrumb-item active text-white fw-bold">แก้ไขรายงานผล</li>
                    </ol>
                </nav>
                <h4 class="fw-bold mb-1 text-white" style="font-family: 'K2D', sans-serif;">
                    <i class="bx bx-edit-alt me-2"></i>แก้ไขรายงานผลการไปราชการ
                </h4>
                <p class="mb-0 text-white opacity-90 small">
                    ปรับปรุงข้อความสรุปผลการปฏิบัติงาน หรือเปลี่ยนไฟล์เอกสารเล่มรายงาน / เกียรติบัตร (PDF)
                </p>
            </div>
            <div>
                <a href="<?= base_url('admin/travel-reports/' . $travel['id']) ?>" class="btn btn-outline-light btn-sm fw-bold px-3">
                    <i class="bx bx-arrow-back me-1"></i> ย้อนกลับ
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-10 col-12">
        <form action="<?= base_url('admin/travel-reports/' . $travel['id'] . '/update') ?>" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <!-- Reference Card -->
            <div class="card border mb-4 shadow-sm" style="border-radius: 0.85rem;">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                        <span class="badge bg-primary text-white p-2 rounded-circle me-2">
                            <i class="bx bx-bookmark-alt fs-5"></i>
                        </span>
                        อ้างอิงคำขออนุมัติไปราชการต้นเรื่อง
                    </h6>
                    <span class="badge bg-light text-primary border font-monospace"><?= esc($travel['doc_no']) ?></span>
                </div>
                <div class="card-body p-4">
                    <div class="alert alert-light border border-primary border-start-4 mb-0 shadow-xs" style="border-left: 5px solid #696cff !important; background-color: #f8faff;">
                        <h6 class="fw-bold text-dark mb-2">
                            เรื่อง: <?= esc($travel['title']) ?>
                        </h6>
                        <div class="row g-2 small text-secondary">
                            <div class="col-md-6 col-12">
                                <i class="bx bx-user me-1 text-primary"></i> ผู้ขอ: <strong><?= esc($travel['prefix'] . $travel['first_name'] . ' ' . $travel['last_name']) ?></strong>
                            </div>
                            <div class="col-md-6 col-12">
                                <i class="bx bx-map-pin me-1 text-danger"></i> สถานที่: <?= esc($travel['destination']) ?> (<?= esc($travel['province']) ?>)
                            </div>
                            <div class="col-md-6 col-12">
                                <i class="bx bx-calendar me-1 text-success"></i> วันที่เดินทาง: <?= thai_date($travel['start_date'], 'short') ?> - <?= thai_date($travel['end_date'], 'short') ?> (<?= esc($travel['total_days']) ?> วัน)
                            </div>
                            <?php if (!empty($travel['companions'])): ?>
                                <div class="col-md-6 col-12">
                                    <i class="bx bx-group me-1 text-info"></i> ผู้ร่วมเดินทาง: <?= esc($travel['companions']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Edit Details -->
            <div class="card border mb-4 shadow-sm" style="border-radius: 0.85rem;">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                        <span class="badge bg-success text-white p-2 rounded-circle me-2">
                            <i class="bx bx-detail fs-5"></i>
                        </span>
                        ข้อความสรุปผลการปฏิบัติราชการและไฟล์แนบ
                    </h6>
                    <span class="badge bg-label-warning text-dark">โหมดแก้ไข</span>
                </div>
                <div class="card-body p-4">
                    <div class="mb-4">
                        <label class="form-label required fw-bold text-dark">
                            สรุปผลการปฏิบัติงาน / ผลการประชุม / ประโยชน์ที่ได้รับต่อโรงเรียนและนักเรียน:
                        </label>
                        <textarea name="report_detail" class="form-control" rows="6" required placeholder="ระบุสรุปสาระสำคัญของการเข้าร่วมประชุม/อบรม/ปฏิบัติราชการ ความรู้และประโยชน์ที่ได้รับ..."><?= esc($travel['report_detail']) ?></textarea>
                    </div>

                    <div class="mb-2">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label fw-bold text-dark mb-0">
                                แนบเล่มรายงานสรุปผล / เกียรติบัตร (PDF)
                            </label>
                            <?php if (!empty($travel['report_file'])): ?>
                                <span class="badge bg-label-success" style="font-size:0.75rem;">
                                    <i class="bx bx-check-circle me-1"></i> มีไฟล์เดิมแนบอยู่แล้ว (หากไม่เลือกใหม่ จะใช้ไฟล์เดิม)
                                </span>
                            <?php endif; ?>
                        </div>
                        <p class="text-muted small mb-2">หากต้องการเปลี่ยนไฟล์ ให้ลากวางไฟล์ใหม่ด้านล่าง หรือหากต้องการใช้ไฟล์เดิม ให้เว้นว่างไว้</p>

                        <!-- Hidden File Input -->
                        <input type="file" name="report_file" id="reportEditFileInput" accept=".pdf,application/pdf" style="display: none;" />

                        <!-- Drag & Drop Dropzone Box -->
                        <div id="reportEditDropzone" class="dropzone-box" onclick="triggerBrowseReportEditFile()">
                            <div class="d-flex flex-column align-items-center">
                                <div class="mb-2">
                                    <span class="badge bg-label-primary p-3 rounded-circle">
                                        <i class="bx bxs-cloud-upload fs-1 text-primary"></i>
                                    </span>
                                </div>
                                <h5 class="fw-bold text-dark mb-1">ลากและวางไฟล์ PDF ใหม่ที่นี่ หรือคลิกเพื่อเลือกไฟล์</h5>
                                <p class="text-muted small mb-2">รองรับเฉพาะเอกสารรูปแบบ <strong>.PDF</strong></p>
                                <button type="button" class="btn btn-outline-primary btn-sm px-3 shadow-xs">
                                    <i class="bx bx-file me-1"></i> เลือกไฟล์ PDF ใหม่จากคอมพิวเตอร์
                                </button>
                            </div>
                        </div>

                        <!-- File Preview Card when file is selected -->
                        <div id="reportEditFilePreview" class="dropzone-preview mt-3">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                                <div class="d-flex align-items-center">
                                    <div class="badge bg-label-danger p-2 rounded-3 me-3">
                                        <i class="bx bxs-file-pdf fs-2 text-danger"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold text-dark text-break" id="reportEditPreviewName">filename.pdf</h6>
                                        <div class="d-flex align-items-center gap-2 mt-1">
                                            <small class="text-muted" id="reportEditPreviewSize">0 KB</small>
                                            <span class="badge bg-label-success" style="font-size: 0.7rem;"><i class="bx bx-check-circle me-1"></i>พร้อมบันทึกแทนที่</span>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeReportEditFile(event)">
                                        <i class="bx bx-trash me-1"></i> ยกเลิกไฟล์ใหม่นี้
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
                    <a href="<?= base_url('admin/travel-reports/' . $travel['id']) ?>" class="btn btn-outline-secondary px-4">
                        ยกเลิก
                    </a>
                    <button type="submit" class="btn btn-primary px-4 py-2 fw-bold shadow-sm" style="font-size: 1rem;">
                        <i class="bx bx-save me-1"></i> บันทึกการแก้ไขรายงานผล
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function triggerBrowseReportEditFile() {
    const input = document.getElementById('reportEditFileInput');
    if (input) input.click();
}

function handleReportEditFileSelect(file) {
    if (!file) return;

    if (file.type !== 'application/pdf' && !file.name.toLowerCase().endsWith('.pdf')) {
        Swal.fire({
            icon: 'warning',
            title: 'รูปแบบไฟล์ไม่ถูกต้อง',
            text: 'ระบบรองรับเฉพาะไฟล์เอกสารรายงานหรือเกียรติบัตรประเภท .PDF เท่านั้น',
            confirmButtonColor: '#696cff'
        });
        removeReportEditFile();
        return;
    }

    const dropzone = document.getElementById('reportEditDropzone');
    const preview = document.getElementById('reportEditFilePreview');
    const nameEl = document.getElementById('reportEditPreviewName');
    const sizeEl = document.getElementById('reportEditPreviewSize');

    if (nameEl) nameEl.textContent = file.name;
    if (sizeEl) sizeEl.textContent = formatBytes(file.size);

    if (dropzone) dropzone.style.display = 'none';
    if (preview) {
        preview.style.display = 'block';
        preview.classList.add('show');
    }
}

function removeReportEditFile(e) {
    if (e) {
        e.preventDefault();
        e.stopPropagation();
    }
    const input = document.getElementById('reportEditFileInput');
    const dropzone = document.getElementById('reportEditDropzone');
    const preview = document.getElementById('reportEditFilePreview');

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
    const dropzone = document.getElementById('reportEditDropzone');
    const fileInput = document.getElementById('reportEditFileInput');

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
                handleReportEditFileSelect(files[0]);
            }
        });

        fileInput.addEventListener('change', function() {
            if (this.files && this.files.length > 0) {
                handleReportEditFileSelect(this.files[0]);
            }
        });
    }
});
</script>
<?= $this->endSection() ?>
