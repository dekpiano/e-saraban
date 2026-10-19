<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>ออกเลข & เสนอเกษียนหนังสือใหม่<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-1 fw-bold text-primary">
                        <i class="bx bx-plus-circle me-2"></i> ลงทะเบียนหนังสือราชการ & กำหนดเส้นทางเดินแฟ้ม
                    </h5>
                    <small class="text-muted">บันทึกข้อมูลเอกสาร แนบไฟล์ PDF และกำหนดผู้มีอำนาจลงนามตามลำดับขั้น</small>
                </div>
                <a href="<?= base_url('admin/documents') ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="bx bx-arrow-back me-1"></i> ย้อนกลับ
                </a>
            </div>

            <div class="card-body">
                <form action="<?= base_url('admin/documents/store') ?>" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>

                    <!-- หมวด 1: ข้อมูลหนังสือ -->
                    <h6 class="fw-bold text-primary mb-3">
                        <i class="bx bx-file me-1"></i> 1. ข้อมูลหนังสือราชการ
                    </h6>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label required fw-semibold">ประเภทหนังสือ</label>
                            <select class="form-select" name="doc_type" id="doc_type" required>
                                <option value="in" <?= $defaultType === 'in' ? 'selected' : '' ?>>หนังสือรับ (จากภายนอก)</option>
                                <option value="out" <?= $defaultType === 'out' ? 'selected' : '' ?>>หนังสือส่ง (ถึงภายนอก)</option>
                                <option value="internal" <?= $defaultType === 'internal' ? 'selected' : '' ?>>บันทึกข้อความ (ภายในโรงเรียน)</option>
                                <option value="order" <?= $defaultType === 'order' ? 'selected' : '' ?>>คำสั่งโรงเรียน</option>
                                <option value="circular" <?= $defaultType === 'circular' ? 'selected' : '' ?>>หนังสือเวียน</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label required fw-semibold">เลขที่หนังสือ (เลขต้นเรื่อง)</label>
                            <input type="text" class="form-control" name="doc_no" required placeholder="เช่น ศธ 04225/ว1023 หรือ สพม.จบ/123" value="ศธ 04225/ว<?= rand(100, 999) ?>" />
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">เลขทะเบียนรับ/ส่งภายใน รร.</label>
                            <input type="text" class="form-control" name="internal_reg_no" value="<?= esc($nextRegNo) ?>" placeholder="รันเลขอัตโนมัติ" />
                        </div>

                        <div class="col-md-8">
                            <label class="form-label required fw-semibold">เรื่อง (ชื่อหนังสือราชการ)</label>
                            <input type="text" class="form-control" name="title" required placeholder="ระบุชื่อเรื่องหนังสือ เช่น ขออนุมัติจัดกิจกรรมค่ายวิทยาศาสตร์ ประจำปีการศึกษา 2569" value="ขออนุมัติจัดโครงการพัฒนาศักยภาพผู้เรียนและการเรียนรู้ดิจิทัล" />
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-semibold">ความเร่งด่วน</label>
                            <select class="form-select" name="urgency">
                                <option value="normal">ปกติ</option>
                                <option value="urgent">ด่วน</option>
                                <option value="very_urgent">ด่วนมาก</option>
                                <option value="most_urgent" selected>ด่วนที่สุด</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-semibold">ชั้นความลับ</label>
                            <select class="form-select" name="secret_level">
                                <option value="normal" selected>ปกติ</option>
                                <option value="confidential">ลับ</option>
                                <option value="top_secret">ลับที่สุด</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label required fw-semibold">จาก (หน่วยงานต้นทาง/ผู้เสนอ)</label>
                            <input type="text" class="form-control" name="from_org" required placeholder="เช่น กลุ่มสาระการเรียนรู้วิทยาศาสตร์ หรือ สพฐ." value="กลุ่มสาระการเรียนรู้วิทยาศาสตร์และเทคโนโลยี" />
                        </div>

                        <div class="col-md-6">
                            <label class="form-label required fw-semibold">ถึง (หน่วยงานปลายทาง/ผู้รับหนังสือ)</label>
                            <input type="text" class="form-control" name="to_org" required placeholder="เช่น ผู้อำนวยการโรงเรียน" value="ผู้อำนวยการโรงเรียน" />
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">ลงวันที่ในหนังสือ (ปฏิทิน พ.ศ.)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bx bx-calendar text-primary"></i></span>
                                <input type="text" class="form-control" name="doc_date" id="docDate" value="<?= date('Y-m-d') ?>" required placeholder="เลือกวันที่..." />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">วันที่รับเข้าสารบรรณ (ปฏิทิน พ.ศ.)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bx bx-calendar text-primary"></i></span>
                                <input type="text" class="form-control" name="receive_date" id="receiveDate" value="<?= date('Y-m-d') ?>" required placeholder="เลือกวันที่..." />
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                แนบไฟล์เอกสารต้นเรื่อง (PDF)
                                <span class="badge bg-label-info ms-1" style="font-size:0.75rem;">
                                    <i class="bx bx-info-circle me-1"></i> หากไม่แนบ ระบบจะใช้ตัวอย่าง PDF ต้นฉบับให้อัตโนมัติ
                                </span>
                            </label>

                            <!-- Hidden actual file input -->
                            <input type="file" name="doc_file" id="docFileInput" accept=".pdf,application/pdf" style="display: none;" />

                            <!-- Drag & Drop Zone Area -->
                            <div id="docDropzone" class="dropzone-box" onclick="triggerBrowseDocFile()">
                                <div class="d-flex flex-column align-items-center">
                                    <div class="mb-2">
                                        <span class="badge bg-label-primary p-3 rounded-circle">
                                            <i class="bx bxs-cloud-upload fs-1 text-primary"></i>
                                        </span>
                                    </div>
                                    <h5 class="fw-bold text-dark mb-1">ลากและวางไฟล์ PDF ที่นี่ หรือคลิกเพื่อเลือกไฟล์</h5>
                                    <p class="text-muted small mb-2">รองรับเฉพาะเอกสารรูปแบบ <strong>.PDF</strong> (หากยังไม่มีไฟล์ ระบบจะจำลองเอกสารตัวอย่างให้)</p>
                                    <button type="button" class="btn btn-outline-primary btn-sm px-3 shadow-xs">
                                        <i class="bx bx-file me-1"></i> เลือกไฟล์ PDF จากคอมพิวเตอร์
                                    </button>
                                </div>
                            </div>

                            <!-- File Preview Card when file is selected -->
                            <div id="docFilePreview" class="dropzone-preview mt-3">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                                    <div class="d-flex align-items-center">
                                        <div class="badge bg-label-danger p-2 rounded-3 me-3">
                                            <i class="bx bxs-file-pdf fs-2 text-danger"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold text-dark text-break" id="docPreviewFileName">filename.pdf</h6>
                                            <div class="d-flex align-items-center gap-2 mt-1">
                                                <small class="text-muted" id="docPreviewFileSize">0 KB</small>
                                                <span class="badge bg-label-success" style="font-size: 0.7rem;"><i class="bx bx-check-circle me-1"></i>พร้อมอัปโหลด</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeSelectedDocFile(event)">
                                            <i class="bx bx-trash me-1"></i> ลบ / เลือกไฟล์ใหม่
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4" />

                    <!-- หมวด 2: กำหนดเส้นทางเดินแฟ้มเกษียน (Routing Chain) -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold text-primary mb-0">
                            <i class="bx bx-git-branch me-1"></i> 2. กำหนดลำดับขั้นตอนการเสนอเกษียนหนังสือ (Workflow Routing)
                        </h6>
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-primary" onclick="setPresetWorkflow('standard')">
                                <i class="bx bx-check me-1"></i> มาตรฐานทั่วไป (3 ขั้น)
                            </button>
                            <button type="button" class="btn btn-outline-info" onclick="setPresetWorkflow('direct_director')">
                                <i class="bx bx-crown me-1"></i> เสนอตรง ผอ. (1 ขั้น)
                            </button>
                        </div>
                    </div>

                    <div class="alert alert-light border mb-4">
                        <small class="text-muted d-block">
                            <i class="bx bx-info-circle me-1"></i> ระบบจะส่งหนังสือไปตามลำดับขั้นตอน เมื่อผู้มีอำนาจในขั้นตอนนั้น ๆ เกษียนลงนามแล้ว ระบบจึงจะส่งต่อให้ผู้มีอำนาจในขั้นตอนถัดไปโดยอัตโนมัติ
                        </small>
                    </div>

                    <!-- Routing Steps Container -->
                    <div id="routingStepsContainer" class="d-flex flex-column gap-3 mb-4">
                        <!-- Step 1 -->
                        <div class="card border p-3 step-card" data-step="1">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="badge bg-primary px-3 py-2 fs-6">
                                    <i class="bx bx-check-circle me-1"></i> ขั้นตอนที่ 1: เสนอพิจารณาชั้นต้น
                                </span>
                            </div>
                            <div class="row g-2">
                                <div class="col-md-8">
                                    <label class="form-label small text-muted">ผู้มีอำนาจลงนาม/เกษียนเรื่อง</label>
                                    <select class="form-select user-select-step" name="route_users[]" required>
                                        <option value="">-- เลือกผู้ลงนาม --</option>
                                        <?php foreach ($users as $u): ?>
                                            <option value="<?= $u['id'] ?>" <?= $u['id'] == 4 ? 'selected' : '' ?>>
                                                <?= esc($u['prefix'] . $u['first_name'] . ' ' . $u['last_name']) ?> (<?= esc($u['position_name']) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small text-muted">บทบาท</label>
                                    <input type="text" class="form-control" value="หัวหน้ากลุ่มสาระฯ / ตรวจสอบ" readonly />
                                </div>
                            </div>
                        </div>

                        <!-- Step 2 -->
                        <div class="card border p-3 step-card" data-step="2">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="badge bg-info px-3 py-2 fs-6">
                                    <i class="bx bx-check-circle me-1"></i> ขั้นตอนที่ 2: เสนอพิจารณากลั่นกรอง
                                </span>
                            </div>
                            <div class="row g-2">
                                <div class="col-md-8">
                                    <label class="form-label small text-muted">ผู้มีอำนาจลงนาม/เกษียนเรื่อง</label>
                                    <select class="form-select user-select-step" name="route_users[]" required>
                                        <option value="">-- เลือกผู้ลงนาม --</option>
                                        <?php foreach ($users as $u): ?>
                                            <option value="<?= $u['id'] ?>" <?= $u['id'] == 2 ? 'selected' : '' ?>>
                                                <?= esc($u['prefix'] . $u['first_name'] . ' ' . $u['last_name']) ?> (<?= esc($u['position_name']) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small text-muted">บทบาท</label>
                                    <input type="text" class="form-control" value="รองผู้อำนวยการฝ่าย" readonly />
                                </div>
                            </div>
                        </div>

                        <!-- Step 3 -->
                        <div class="card border p-3 step-card" data-step="3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="badge bg-warning text-dark px-3 py-2 fs-6">
                                    <i class="bx bx-crown me-1"></i> ขั้นตอนที่ 3: เสนอผู้อำนวยการ (สั่งการ/อนุมัติขั้นสุดท้าย)
                                </span>
                            </div>
                            <div class="row g-2">
                                <div class="col-md-8">
                                    <label class="form-label small text-muted">ผู้มีอำนาจลงนาม/เกษียนเรื่อง</label>
                                    <select class="form-select user-select-step" name="route_users[]" required>
                                        <option value="">-- เลือกผู้ลงนาม --</option>
                                        <?php foreach ($users as $u): ?>
                                            <option value="<?= $u['id'] ?>" <?= $u['id'] == 1 ? 'selected' : '' ?>>
                                                <?= esc($u['prefix'] . $u['first_name'] . ' ' . $u['last_name']) ?> (<?= esc($u['position_name']) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small text-muted">บทบาท</label>
                                    <input type="text" class="form-control" value="ผู้อำนวยการโรงเรียน (อนุมัติ)" readonly />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="<?= base_url('admin/documents') ?>" class="btn btn-outline-secondary">ยกเลิก</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bx bx-paper-plane me-1"></i> บันทึกและเริ่มเสนอเดินแฟ้ม
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    initThaiDatePicker('#docDate', {
        defaultDate: '<?= date('Y-m-d') ?>'
    });
    initThaiDatePicker('#receiveDate', {
        defaultDate: '<?= date('Y-m-d') ?>'
    });

    // Extra guarantee for browser display: replace any trailing 'B' with current Thai Buddhist year
    setTimeout(function() {
        document.querySelectorAll('.flatpickr-thai-input').forEach(function(el) {
            if (el.value && el.value.indexOf('B') !== -1) {
                el.value = el.value.replace(/\bB\b/g, '<?= date('Y') + 543 ?>').replace(/\s+B$/, ' <?= date('Y') + 543 ?>');
            }
        });
    }, 50);
});

function setPresetWorkflow(preset) {
    const selects = document.querySelectorAll('.user-select-step');
    if (preset === 'standard') {
        if (selects[0]) selects[0].value = '4'; // Head of Sci
        if (selects[1]) selects[1].value = '2'; // Deputy Gen
        if (selects[2]) selects[2].value = '1'; // Director
    } else if (preset === 'direct_director') {
        if (selects[0]) selects[0].value = '1'; // Direct to director
    }
}

// Drag & Drop File Upload Handlers
function triggerBrowseDocFile() {
    const fileInput = document.getElementById('docFileInput');
    if (fileInput) fileInput.click();
}

function handleDocFileSelect(file) {
    if (!file) return;

    if (file.type !== 'application/pdf' && !file.name.toLowerCase().endsWith('.pdf')) {
        Swal.fire({
            icon: 'warning',
            title: 'รูปแบบไฟล์ไม่ถูกต้อง',
            text: 'ระบบรองรับเฉพาะไฟล์เอกสารประเภท .PDF เท่านั้น',
            confirmButtonColor: '#696cff'
        });
        removeSelectedDocFile();
        return;
    }

    const dropzone = document.getElementById('docDropzone');
    const preview = document.getElementById('docFilePreview');
    const nameEl = document.getElementById('docPreviewFileName');
    const sizeEl = document.getElementById('docPreviewFileSize');

    if (nameEl) nameEl.textContent = file.name;
    if (sizeEl) sizeEl.textContent = formatBytes(file.size);

    if (dropzone) dropzone.style.display = 'none';
    if (preview) {
        preview.style.display = 'block';
        preview.classList.add('show');
    }
}

function removeSelectedDocFile(e) {
    if (e) {
        e.preventDefault();
        e.stopPropagation();
    }
    const fileInput = document.getElementById('docFileInput');
    const dropzone = document.getElementById('docDropzone');
    const preview = document.getElementById('docFilePreview');

    if (fileInput) fileInput.value = '';
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

// Attach Drag & Drop Listeners
const docDropzone = document.getElementById('docDropzone');
const docFileInput = document.getElementById('docFileInput');

if (docDropzone && docFileInput) {
    ['dragenter', 'dragover'].forEach(eventName => {
        docDropzone.addEventListener(eventName, function(e) {
            e.preventDefault();
            e.stopPropagation();
            docDropzone.classList.add('dragover');
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        docDropzone.addEventListener(eventName, function(e) {
            e.preventDefault();
            e.stopPropagation();
            docDropzone.classList.remove('dragover');
        }, false);
    });

    docDropzone.addEventListener('drop', function(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        if (files && files.length > 0) {
            docFileInput.files = files;
            handleDocFileSelect(files[0]);
        }
    }, false);

    docFileInput.addEventListener('change', function() {
        if (this.files && this.files.length > 0) {
            handleDocFileSelect(this.files[0]);
        }
    });
}
</script>
<?= $this->endSection() ?>
