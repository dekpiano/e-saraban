<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>ลงทะเบียนรับหนังสือเข้า | ระบบงานสารบรรณ สกจ.<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Saraban Memo CSS (for .saraban-receive-stamp styles) -->
<link rel="stylesheet" href="<?= base_url('assets/css/saraban-memo.css?v=' . (file_exists(FCPATH . 'assets/css/saraban-memo.css') ? filemtime(FCPATH . 'assets/css/saraban-memo.css') : time())) ?>">

<!-- PDF.js Local Library -->
<script src="<?= base_url('assets/vendor/libs/pdfjs/pdf.min.js') ?>"></script>
<script>
    if (typeof pdfjsLib !== 'undefined') {
        pdfjsLib.GlobalWorkerOptions.workerSrc = "<?= base_url('assets/vendor/libs/pdfjs/pdf.worker.min.js') ?>";
    }
</script>

<style>
/* =========================================================
   Executive UI/UX Styles for Inbound Document Workspace
   ========================================================= */
:root {
    --inbound-primary: #696cff;
    --inbound-primary-dark: #5658e2;
    --inbound-surface: #ffffff;
    --inbound-border: #e2e8f0;
    --inbound-desk-bg: #f1f5f9;
}

body, .card, input, select, textarea, button, label {
    font-family: 'K2D', 'Sarabun', sans-serif;
}

/* ๑. Hero Banner Card */
.inbound-hero-banner {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-top: 4px solid var(--inbound-primary);
    border-radius: 14px;
    box-shadow: 0 4px 18px rgba(0, 0, 0, 0.04);
}

.step-indicator-pill {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    padding: 4px 12px;
    font-size: 12px;
    font-weight: 600;
    color: #475569;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.step-indicator-pill.is-active {
    background: #eef2ff;
    border-color: #c7d2fe;
    color: #4338ca;
}

.step-indicator-pill .step-num {
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: #64748b;
    color: #ffffff;
    font-size: 11px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.step-indicator-pill.is-active .step-num {
    background: #4f46e5;
}

/* ๒. Premium Section Cards */
.form-section-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
    overflow: hidden;
    transition: box-shadow 0.2s ease;
}

.form-section-card:hover {
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
}

.section-header-bar {
    background: #ffffff;
    border-bottom: 1px solid #f1f5f9;
    padding: 14px 18px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.section-title-wrap {
    display: flex;
    align-items: center;
    gap: 10px;
}

.section-icon-badge {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}

.form-control, .form-select {
    border-color: #cbd5e1;
    border-radius: 8px;
    font-size: 14px;
    padding: 8px 12px;
    color: #0f172a;
    transition: all 0.15s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #696cff;
    box-shadow: 0 0 0 3px rgba(105, 108, 255, 0.16);
    background-color: #ffffff;
}

.form-label-bold {
    font-weight: 700;
    font-size: 13px;
    color: #1e293b;
    margin-bottom: 5px;
    display: flex;
    align-items: center;
    gap: 4px;
}

/* ๓. Quick Fill Chips */
.quick-chip {
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    color: #334155;
    font-size: 11.5px;
    font-weight: 600;
    padding: 3px 9px;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.15s ease;
    user-select: none;
    display: inline-block;
    margin-right: 4px;
    margin-bottom: 4px;
}

.quick-chip:hover {
    background: #e0e7ff;
    border-color: #c7d2fe;
    color: #4338ca;
    transform: translateY(-1px);
}

/* ๔. Modern Drag & Drop Zone */
.modern-dropzone {
    border: 2px dashed #cbd5e1;
    border-radius: 12px;
    background: #f8fafc;
    padding: 22px 16px;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s ease;
    position: relative;
}

.modern-dropzone:hover, .modern-dropzone.is-dragover {
    border-color: #696cff;
    background: #eef2ff;
}

.modern-dropzone .dropzone-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: #ffffff;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 10px;
    color: #696cff;
    font-size: 24px;
}

/* ๕. Interactive Drafting Desk */
.drafting-desk {
    background: var(--inbound-desk-bg);
    border: 1px solid var(--inbound-border);
    border-radius: 14px;
    padding: 16px;
    position: relative;
}

.desk-paper-sheet {
    position: relative;
    display: inline-block;
    width: 100%;
    max-width: 21cm;
    min-height: 0 !important;
    height: auto;
    background: #ffffff;
    box-shadow: 0 10px 30px rgba(0,0,0,0.12), 0 2px 6px rgba(0,0,0,0.06);
    border-radius: 4px;
    margin: 0 auto;
    box-sizing: border-box;
    padding: 0 !important;
    overflow: hidden;
    line-height: 0;
}

/* ๖. Floating Desk Toolbar */
.floating-desk-toolbar {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 8px 14px;
    margin-bottom: 12px;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.03);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    flex-wrap: wrap;
}

/* ๗. Interactive Mode Selection Cards */
.stamp-mode-card {
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    background: #ffffff;
    padding: 12px;
    cursor: pointer;
    transition: all 0.2s ease;
    text-align: center;
    position: relative;
    user-select: none;
    height: 100%;
}

.stamp-mode-card:hover {
    border-color: #cbd5e1;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}

.stamp-mode-card.is-active-stamp {
    border-color: #22c55e !important;
    background-color: #f0fdf4 !important;
    box-shadow: 0 4px 14px rgba(34, 197, 94, 0.15) !important;
}

.stamp-mode-card.is-active-clean {
    border-color: #0284c7 !important;
    background-color: #f0f9ff !important;
    box-shadow: 0 4px 14px rgba(2, 132, 199, 0.15) !important;
}

/* ๘. Stepper Visual for Executive Route */
.route-step-card {
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    background: #ffffff;
    padding: 10px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    transition: all 0.15s ease;
}

.route-step-badge {
    width: 26px;
    height: 26px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 13px;
    color: #ffffff;
    flex-shrink: 0;
}

/* ๙. Submit Action Button */
.btn-submit-inbound {
    background: linear-gradient(135deg, #696cff 0%, #4338ca 100%);
    border: none;
    color: #ffffff;
    font-weight: 700;
    font-size: 15px;
    padding: 12px 20px;
    border-radius: 10px;
    box-shadow: 0 4px 14px rgba(105, 108, 255, 0.35);
    transition: all 0.2s ease;
}

.btn-submit-inbound:hover {
    background: linear-gradient(135deg, #5b5ee6 0%, #3730a3 100%);
    color: #ffffff;
    box-shadow: 0 6px 20px rgba(105, 108, 255, 0.5);
    transform: translateY(-1px);
}
</style>

<div class="container-xxl flex-grow-1 container-p-y p-0">

    <!-- ๑. ส่วนหัวหน้าจอ (Executive Hero Banner) -->
    <div class="inbound-hero-banner p-3 p-md-4 mb-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1.5 flex-wrap">
                    <span class="badge bg-primary text-white fw-bold px-2.5 py-1">
                        <i class="bx bx-bookmark me-1"></i> สมุดทะเบียนรับเข้า
                    </span>
                    <span class="text-muted small">โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์</span>
                </div>
                <h4 class="fw-bold text-dark mb-1" style="font-family: 'K2D', sans-serif;">
                    <i class="bx bx-file-blank text-primary me-1"></i> ลงทะเบียนรับหนังสือราชการเข้า (ภายนอก)
                </h4>
                <p class="text-muted small mb-0">
                    บันทึกข้อมูลรับหนังสือ • แนบไฟล์สแกนต้นฉบับ • ประทับตรายางรับ สกจ. มาตรฐานดิจิทัล • เสนอแฟ้มเข้าสายเกษียนสั่งการ
                </p>
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="<?= base_url('admin/inbound') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-xs">
                    <i class="bx bx-arrow-back me-1"></i> กลับไปทะเบียนรับ
                </a>
            </div>
        </div>

        <!-- Stepper Guide Ribbon -->
        <div class="d-flex align-items-center gap-2 mt-3 pt-3 border-top flex-wrap">
            <div class="step-indicator-pill is-active">
                <span class="step-num">๑</span>
                <span>ข้อมูลหนังสือต้นทาง</span>
            </div>
            <i class="bx bx-chevron-right text-muted"></i>
            <div class="step-indicator-pill is-active">
                <span class="step-num">๒</span>
                <span>ตรวจดู & จัดตรายางรับ</span>
            </div>
            <i class="bx bx-chevron-right text-muted"></i>
            <div class="step-indicator-pill is-active">
                <span class="step-num">๓</span>
                <span>เสนอสายเกษียน ผอ.</span>
            </div>
        </div>
    </div>

    <form action="<?= base_url('admin/inbound/store') ?>" method="POST" enctype="multipart/form-data" id="inboundCreateForm">
        <?= csrf_field() ?>

        <!-- Hidden Inputs for Stamp Coordinates & Status -->
        <input type="hidden" name="has_receive_stamp" id="inputHasReceiveStamp" value="1">
        <input type="hidden" name="receive_stamp_x" id="inputReceiveStampX" value="72.2%">
        <input type="hidden" name="receive_stamp_y" id="inputReceiveStampY" value="3.5%">
        <input type="hidden" name="receive_stamp_scale" id="inputReceiveStampScale" value="1.0">
        <input type="hidden" name="receive_stamp_w_pct" id="inputReceiveStampWPct" value="23.8%">

        <div class="row g-3">
            <!-- คอลัมน์ซ้าย: ข้อมูลหนังสือ และ อัปโหลดไฟล์พร้อมพรีวิวตรายางรับ (Col 8) -->
            <div class="col-lg-8">
                
                <!-- การ์ดที่ ๑: ข้อมูลหนังสือต้นทาง (จากภายนอก) -->
                <div class="form-section-card mb-3">
                    <div class="section-header-bar">
                        <div class="section-title-wrap">
                            <div class="section-icon-badge bg-primary text-white">
                                <i class="bx bx-file"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold text-dark" style="font-family: 'K2D', sans-serif;">๑. ข้อมูลหนังสือต้นทาง (จากภายนอก)</h6>
                                <span class="text-muted small">ระบุรายละเอียดตามที่ปรากฏบนหัวหนังสือต้นฉบับ</span>
                            </div>
                        </div>
                        <span class="badge bg-primary text-white fw-bold px-2.5 py-1">หนังสือรับเข้า</span>
                    </div>

                    <div class="card-body p-3 p-md-4">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label-bold">
                                    <i class="bx bx-hash text-primary"></i> เลขที่หนังสือต้นทาง <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="doc_no" class="form-control fw-bold text-dark font-monospace" placeholder="เช่น อบจ.นว ๕๑๐๐๑/ว ๑๒๓" required>
                                <span class="text-muted" style="font-size: 11.5px;">เลขที่ที่ระบุอยู่บนหัวหนังสือของหน่วยงานต้นทาง</span>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-bold">
                                    <i class="bx bx-calendar text-primary"></i> ลงวันที่ในหนังสือ (พ.ศ.) <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="doc_date" class="form-control flatpickr-thai-input" value="<?= date('Y-m-d') ?>" placeholder="เลือกวันที่ในหนังสือ" required>
                                <span class="text-muted" style="font-size: 11.5px;">วันที่ออกหนังสือของต้นทาง</span>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label-bold">
                                    <i class="bx bx-building text-primary"></i> จาก (หน่วยงานต้นทาง) <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="from_org" id="fromOrgInput" class="form-control" placeholder="ระบุหน่วยงานต้นทาง เช่น สพม.นครสวรรค์" required>
                                <!-- Quick Select Chips for Origin -->
                                <div class="mt-1.5">
                                    <span class="text-muted" style="font-size: 11px;">เลือกด่วน: </span>
                                    <span class="quick-chip" onclick="fillFromOrg('สพม.นครสวรรค์')">สพม.นครสวรรค์</span>
                                    <span class="quick-chip" onclick="fillFromOrg('อบจ.นครสวรรค์')">อบจ.นครสวรรค์</span>
                                    <span class="quick-chip" onclick="fillFromOrg('สพป.นครสวรรค์ เขต ๑')">สพป.นว.๑</span>
                                    <span class="quick-chip" onclick="fillFromOrg('ศธจ.นครสวรรค์')">ศธจ.นครสวรรค์</span>
                                    <span class="quick-chip" onclick="fillFromOrg('กระทรวงศึกษาธิการ')">กระทรวง ศธ.</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-bold">
                                    <i class="bx bx-user-check text-primary"></i> ถึง (ผู้รับหนังสือ) <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="to_org" class="form-control fw-semibold" value="ผู้อำนวยการโรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์" required>
                                <span class="text-muted" style="font-size: 11.5px;">ระบุเรียนหัวหน้าหน่วยงาน</span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label-bold">
                                <i class="bx bx-message-detail text-primary"></i> เรื่อง (ชื่อเรื่องหนังสือ) <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="title" class="form-control fw-bold text-primary fs-6" placeholder="ระบุชื่อเรื่องหนังสือตามต้นฉบับให้ชัดเจน..." required>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label-bold"><i class="bx bx-alarm text-warning"></i> ความเร่งด่วน</label>
                                <select name="urgency" class="form-select">
                                    <option value="normal" selected>ปกติ</option>
                                    <option value="urgent">ด่วน (สีส้ม)</option>
                                    <option value="very_urgent">ด่วนมาก (สีเหลืองอำพัน)</option>
                                    <option value="most_urgent">ด่วนที่สุด (สีแดง)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-bold"><i class="bx bx-shield-quarter text-info"></i> ชั้นความลับ</label>
                                <select name="secret_level" class="form-select">
                                    <option value="normal" selected>ปกติ</option>
                                    <option value="confidential">ลับ</option>
                                    <option value="secret">ลับมาก</option>
                                    <option value="top_secret">ลับที่สุด</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- การ์ดที่ ๒: อัปโหลดไฟล์สแกน & โต๊ะตรวจอ่าน & จัดวางตรายางรับ -->
                <div class="form-section-card mb-3">
                    <div class="section-header-bar">
                        <div class="section-title-wrap">
                            <div class="section-icon-badge bg-danger text-white">
                                <i class="bx bxs-file-pdf"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold text-dark" style="font-family: 'K2D', sans-serif;">๒. อัปโหลดไฟล์สแกนหนังสือ & ตรวจดูเนื้อหา & จัดวางตรายางรับ</h6>
                                <span class="text-muted small">รองรับไฟล์สแกน PDF หรือรูปภาพ (ระบบจำลองหน้า A4 พร้อมประทับตรา 1:1)</span>
                            </div>
                        </div>
                        <span class="badge bg-secondary text-white fw-bold" id="previewBadgeStatus">รอเลือกไฟล์</span>
                    </div>

                    <div class="card-body p-3 p-md-4">
                        <!-- Modern File Upload Dropzone -->
                        <div class="modern-dropzone mb-3" id="dropzoneArea" onclick="document.getElementById('inboundFileInput').click()">
                            <input type="file" name="doc_file" id="inboundFileInput" class="d-none" accept=".pdf,image/png,image/jpeg,image/webp" required onchange="previewInboundFile(this)">
                            <div class="dropzone-icon">
                                <i class="bx bx-cloud-upload"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">คลิกเลือกไฟล์ หรือลากไฟล์มาวางที่นี่</h6>
                            <p class="text-muted small mb-2">รองรับไฟล์เอกสาร PDF หรือรูปภาพสแกน (JPG, PNG, WEBP) ขนาดไม่เกิน 25MB</p>
                            <span class="btn btn-outline-primary btn-sm rounded-pill px-3 shadow-xs fw-semibold">
                                <i class="bx bx-folder-open me-1"></i> เลือกไฟล์จากเครื่อง
                            </span>
                        </div>

                        <!-- แถบสรุปชื่อและขนาดไฟล์เมื่อเลือกแล้ว -->
                        <div id="filePreviewNotice" class="alert alert-light border py-2.5 px-3 d-flex align-items-center justify-content-between gap-3 mb-3 d-none">
                            <div class="d-flex align-items-center gap-2 overflow-hidden">
                                <i class="bx bxs-file-pdf fs-3 text-danger flex-shrink-0"></i>
                                <div class="overflow-hidden">
                                    <strong class="text-dark d-block text-truncate" id="previewFileName">ยังไม่ได้เลือกไฟล์</strong>
                                    <span class="text-muted small" id="previewFileSize">-</span>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-2.5 py-1" onclick="document.getElementById('inboundFileInput').click()">
                                <i class="bx bx-refresh me-1"></i> เปลี่ยนไฟล์
                            </button>
                        </div>

                        <!-- โต๊ะตรวจอ่านเอกสาร & พรีวิวตรายางรับ (Interactive Document Desk) -->
                        <div class="drafting-desk">
                            
                            <!-- แถบเครื่องมือควบคุมกระดาษและตรายาง (Floating Desk Toolbar) -->
                            <div class="floating-desk-toolbar" id="deskToolbar">
                                <!-- Multi-page pagination -->
                                <div class="d-flex align-items-center gap-1.5 flex-wrap" id="pdfPaginationBar">
                                    <button type="button" class="btn btn-outline-secondary btn-sm py-1 px-2 fw-semibold" id="pdfPrevPageBtn" onclick="changePdfPage(-1)" disabled>
                                        <i class="bx bx-chevron-left me-1"></i> หน้าก่อน
                                    </button>
                                    <span class="badge bg-primary text-white border px-2.5 py-1.5 fw-bold" id="pdfPageNumDisplay">หน้า ๑ / ๑</span>
                                    <button type="button" class="btn btn-outline-secondary btn-sm py-1 px-2 fw-semibold" id="pdfNextPageBtn" onclick="changePdfPage(1)" disabled>
                                        ถัดไป <i class="bx bx-chevron-right ms-1"></i>
                                    </button>
                                    <select id="pdfPageJumpSelect" class="form-select form-select-sm d-inline-block w-auto py-0 px-2 fw-bold text-dark border-primary" style="height: 30px; font-size: 12px;" onchange="jumpToPdfPage(parseInt(this.value))">
                                        <option value="1">หน้า ๑</option>
                                    </select>
                                    <span class="badge bg-light text-dark border px-2 py-1 small d-none" id="pdfTotalPagesBadge">
                                        <i class="bx bx-file text-primary me-1"></i> รวม <span id="pdfTotalPagesCount">๑</span> หน้า
                                    </span>
                                </div>

                                <!-- Quick Stamp Controls -->
                                <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                    <span class="text-muted small fw-bold d-none d-md-inline">ตำแหน่งตรายาง:</span>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-primary fw-semibold" onclick="quickSetReceiveStamp('top-right')" title="วางมุมบนขวาตามระเบียบงานสารบรรณ (มาตรฐาน ๕ ซม.)">
                                            <i class="bx bx-dock-right me-1"></i> ขวาบน
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary fw-semibold" onclick="quickSetReceiveStamp('top-left')" title="วางมุมบนซ้าย">
                                            <i class="bx bx-dock-left me-1"></i> ซ้ายบน
                                        </button>
                                    </div>

                                    <!-- Realtime Scale Buttons -->
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-dark" onclick="changeReceiveStampScale(-0.05, event)" title="ย่อขนาดเล็กลง">
                                            <i class="bx bx-minus"></i>
                                        </button>
                                        <span class="btn btn-light border text-dark fw-bold px-2" id="stampScaleToolbarDisplay">100%</span>
                                        <button type="button" class="btn btn-outline-dark" onclick="changeReceiveStampScale(0.05, event)" title="ขยายขนาดใหญ่ขึ้น">
                                            <i class="bx bx-plus"></i>
                                        </button>
                                    </div>

                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="resetReceiveStampPos(event)" title="รีเซ็ตตำแหน่งมาตรฐาน">
                                        <i class="bx bx-reset"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- แจ้งเตือนเมื่อเปิดดูหน้าที่ ๒ เป็นต้นไป -->
                            <div id="nonFirstPageNotice" class="alert alert-info py-2 px-3 mb-2 small d-none shadow-xs border-info text-dark" style="background-color: #f0f9ff;">
                                <i class="bx bx-info-circle text-primary me-1 fs-6"></i>
                                <span>กำลังตรวจดู <strong>หน้า <span id="noticeCurrentPageNum">๒</span></strong> (ตรายางรับหนังสือจะประทับเฉพาะหน้าแรกเท่านั้นตามระเบียบงานสารบรรณ และระบบจะบันทึกเก็บรักษาทุกหน้าไว้ครบถ้วน ๑๐๐%)</span>
                            </div>

                            <!-- แผ่นกระดาษเสมือนจริง (Physical A4 Stamped Sheet) -->
                            <div class="d-flex justify-content-center align-items-start overflow-auto position-relative py-2" style="min-height: 520px; max-height: 960px;">
                                <div id="inboundPaperWrapper" class="desk-paper-sheet">

                                    <!-- ตรายางรับหนังสือราชการ รร.ส.ก.จ. (Official Rubber Stamp 5cm = 23.8% on A4) -->
                                    <div id="liveReceiveStamp" class="saraban-receive-stamp-img-wrap is-draggable shadow"
                                        style="position: absolute; left: 72.2%; top: 3.5%; width: 23.8%; aspect-ratio: 452 / 227; height: auto; transform: rotate(-1deg) scale(1.0); transform-origin: top left; display: block; z-index: 60; user-select: none; cursor: grab; box-sizing: border-box;">
                                        
                                        <!-- Mini Floating Toolbar over Stamp -->
                                        <div class="stamp-controls-bar no-print">
                                            <span class="stamp-btn-move" title="แตะหรือคลิกลากเพื่อย้ายตำแหน่งตรายางรับหนังสือ"><i class="bx bx-move"></i> ย้าย</span>
                                            <span class="stamp-btn-tool" onclick="changeReceiveStampScale(-0.05, event)" title="ย่อขนาด"><i class="bx bx-minus"></i></span>
                                            <span class="stamp-scale-badge" id="liveReceiveStampScaleDisplay">100%</span>
                                            <span class="stamp-btn-tool" onclick="changeReceiveStampScale(0.05, event)" title="ขยายขนาด"><i class="bx bx-plus"></i></span>
                                            <span class="stamp-btn-tool" onclick="resetReceiveStampPos(event)" title="รีเซ็ตตำแหน่ง"><i class="bx bx-reset"></i></span>
                                        </div>

                                        <!-- รูปตรายางแม่แบบตัดจากต้นฉบับจริง -->
                                        <div style="position: relative; width: 100%; height: 100%; background: #ffffff; border-radius: 1px; overflow: hidden; box-shadow: 0 4px 12px rgba(20,42,130,0.18);">
                                            <img src="<?= base_url('assets/img/stamp_template_skj.png') ?>" alt="ตรายาง รร.ส.ก.จ." style="width: 100%; height: 100%; display: block; object-fit: fill; pointer-events: none;">
                                            <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; font-family: 'TH SarabunIT๙', 'TH Sarabun New', 'Sarabun', sans-serif; font-weight: bold; color: #142a82; line-height: 1;">
                                                <span id="liveReceiveStampRegNoDisplay" style="position: absolute; left: 27%; top: 43%; transform: translateY(-82%); font-size: 1.3vw; min-font-size: 13pt; max-font-size: 17pt; white-space: nowrap;"><?= esc(to_thai_number($suggestedRegNo)) ?></span>
                                                <span id="liveReceiveStampDateDisplay" style="position: absolute; left: 25.2%; top: 66.3%; transform: translateY(-82%); font-size: 1.3vw; min-font-size: 13pt; max-font-size: 17pt; white-space: nowrap;"><?= esc(to_thai_number(thai_date(date('Y-m-d'), 'medium'))) ?></span>
                                                <span id="liveReceiveStampTimeDisplay" style="position: absolute; left: 24%; top: 89.6%; transform: translateY(-82%); font-size: 1.3vw; min-font-size: 13pt; max-font-size: 17pt; white-space: nowrap;"><?= to_thai_number(date('H:i') . ' น.') ?></span>
                                            </div>
                                        </div>

                                        <!-- Corner Resize Handle -->
                                        <div class="stamp-resize-handle no-print" id="receiveStampResizeHandle" title="คลิกหรือแตะลากเพื่อย่อ-ขยายขนาด">
                                            <i class="bx bx-expand-alt"></i>
                                        </div>
                                    </div>

                                    <!-- Placeholder กึ่งกลางหน้ากระดาษ (แสดงก่อนเลือกไฟล์) -->
                                    <div id="inboundFilePlaceholder" class="text-center py-5" style="padding-top: 170px !important; padding-bottom: 170px !important;">
                                        <div class="avatar avatar-xl bg-label-primary mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle" style="width: 72px; height: 72px; margin: 0 auto;">
                                            <i class="bx bx-file-blank fs-1 text-primary"></i>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-1">พื้นที่พรีวิวเอกสาร & จัดวางตรายางรับหนังสือ</h6>
                                        <p class="text-muted small mb-0 px-4">เลือกไฟล์ PDF หรือรูปภาพด้านบน เพื่อตรวจดูเนื้อหาและจัดวางตรายางบนเอกสารจริง</p>
                                        <div class="mt-3">
                                            <span class="badge bg-primary text-white px-3 py-1.5 fw-semibold" style="font-size: 12px;">
                                                <i class="bx bx-move me-1"></i> ตรายางรับมุมบนขวาพร้อมทำงาน: ท่านสามารถลากย้าย หรือย่อ-ขยายขนาดได้ทันที
                                            </span>
                                        </div>
                                    </div>

                                    <!-- PDF Canvas Render -->
                                    <canvas id="pdfPageCanvas" class="w-100 d-none" style="display: block; width: 100%; height: auto;"></canvas>

                                    <!-- Image Preview Render -->
                                    <img id="imagePreview" class="w-100 d-none" alt="พรีวิวเอกสาร" style="display: block; width: 100%; height: auto; object-fit: contain;">

                                    <!-- Fallback Iframe -->
                                    <iframe id="pdfIframeFallback" class="w-100 d-none" style="min-height: 1050px; border: none;"></iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- คอลัมน์ขวา: ทะเบียนรับสารบรรณ & กำหนดสายเกษียน (Col 4) -->
            <div class="col-lg-4">
                
                <!-- การ์ดที่ ๓: ทะเบียนรับสารบรรณ (สกจ.) -->
                <div class="form-section-card mb-3" style="border-top: 4px solid #22c55e;">
                    <div class="section-header-bar">
                        <div class="section-title-wrap">
                            <div class="section-icon-badge bg-success text-white">
                                <i class="bx bx-stamp"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold text-dark" style="font-family: 'K2D', sans-serif;">๓. ทะเบียนรับสารบรรณ (สกจ.)</h6>
                                <span class="text-muted small">กำหนดเลขรับ วันที่รับ และโหมดตรายาง</span>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-3 p-md-4">
                        <!-- เลือกระบบการออกเลข (Dual Mode Selection) -->
                        <div class="mb-3">
                            <label class="form-label-bold">
                                <i class="bx bx-cog text-success"></i> รูปแบบการลงทะเบียนรับสารบรรณ
                            </label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="stamp-mode-card is-active-stamp" id="modeCardStamp" onclick="handleStampModeChange(1)">
                                        <input type="radio" name="has_receive_stamp" id="modeRadioStamp" value="1" class="d-none" checked>
                                        <span class="badge bg-success text-white mb-1 px-2 py-0.5" style="font-size: 11px;">
                                            <i class="bx bx-stamp me-1"></i>แบบที่ ๑
                                        </span>
                                        <strong class="text-dark d-block small mb-0.5" style="line-height: 1.2;">มีตรายางรับ</strong>
                                        <span class="text-muted" style="font-size: 10.5px;">(สแตมป์มุมบนขวา ๕ ซม.)</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="stamp-mode-card" id="modeCardClean" onclick="handleStampModeChange(0)">
                                        <input type="radio" name="has_receive_stamp" id="modeRadioClean" value="0" class="d-none">
                                        <span class="badge bg-secondary text-white mb-1 px-2 py-0.5" style="font-size: 11px;">
                                            <i class="bx bx-file me-1"></i>แบบที่ ๒
                                        </span>
                                        <strong class="text-dark d-block small mb-0.5" style="line-height: 1.2;">มาตรฐานราชการ</strong>
                                        <span class="text-muted" style="font-size: 10.5px;">(ลงทะเบียน ไม่สแตมป์)</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- รายละเอียดเลขรับและเวลารับ -->
                        <div id="stampDetailsGroup">
                            <div class="mb-3">
                                <label class="form-label-bold">
                                    <i class="bx bx-bookmark text-primary"></i> เลขทะเบียนรับ (สกจ.) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-primary fw-bold"><i class="bx bx-hash"></i></span>
                                    <input type="text" name="internal_reg_no" id="inputRegNo" class="form-control fw-bold text-primary font-monospace fs-6" value="<?= esc($suggestedRegNo) ?>" required oninput="syncLiveStampRegNo(this.value)">
                                </div>
                                <span class="text-muted" style="font-size: 11.5px;">ระบบรันเลขอัตโนมัติตามสมุดทะเบียนรับประจำปี (แก้ไขได้)</span>
                            </div>

                            <div class="row g-2 mb-3">
                                <div class="col-7">
                                    <label class="form-label-bold"><i class="bx bx-calendar text-primary"></i> วันที่รับเข้า (พ.ศ.)</label>
                                    <input type="text" name="receive_date" id="inputReceiveDate" class="form-control form-control-sm flatpickr-thai-input" value="<?= date('Y-m-d') ?>" placeholder="เลือกวันที่รับ" onchange="syncLiveDocDate(this.value)">
                                </div>
                                <div class="col-5">
                                    <label class="form-label-bold"><i class="bx bx-time-five text-primary"></i> เวลารับ</label>
                                    <input type="text" name="receive_stamp_time" id="inputReceiveTime" class="form-control form-control-sm text-center fw-bold" value="<?= date('H:i') . ' น.' ?>" oninput="syncLiveStampTime(this.value)">
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-primary border-0 py-2.5 px-3 small mb-0 rounded-3" id="stampModeAlertNotice" style="background-color: #eef2ff;">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bx bx-info-circle text-primary fs-5 flex-shrink-0 mt-0.5"></i>
                                <span class="text-dark" style="font-size: 12px; line-height: 1.5;">
                                    เมื่อลงทะเบียนรับแล้ว ระบบจะประทับ <strong>ตรายางรับหนังสือ</strong> สีน้ำเงินลงในเนื้อไฟล์เอกสารจริงถาวรตามตำแหน่งและขนาดที่จัดวาง
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- การ์ดที่ ๔: เส้นทางเดินแฟ้มเสนอเกษียน & ข้อความนำเรียน -->
                <div class="form-section-card mb-3" style="border-top: 4px solid #f59e0b;">
                    <div class="section-header-bar">
                        <div class="section-title-wrap">
                            <div class="section-icon-badge bg-warning text-dark">
                                <i class="bx bx-git-branch"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold text-dark" style="font-family: 'K2D', sans-serif;">๔. เส้นทางเดินแฟ้มเสนอเกษียน</h6>
                                <span class="text-muted small">กำหนดลำดับผู้บริหารพิจารณาสั่งการ</span>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-3 p-md-4">
                        <!-- เลือกว่าจะผ่าน รอง ผอ. หรือไม่ -->
                        <div class="mb-3">
                            <label class="form-label-bold">
                                <i class="bx bx-user-voice text-warning"></i> ลำดับที่ ๑: เสนอ รอง ผอ. กลั่นกรอง
                            </label>
                            <select name="route_personnel[]" class="form-select form-select-sm mb-2" id="deputySelector" onchange="toggleDeputyStep(this.value)">
                                <option value="">-- ไม่ผ่าน รอง ผอ. (เสนอ ผอ. โดยตรง) --</option>
                                <?php foreach ($personnelList as $p): ?>
                                    <?php if (str_contains($p['position_name'] ?? '', 'รองผู้')): ?>
                                        <option value="<?= esc($p['pers_id']) ?>">
                                            <?= esc($p['full_name']) ?> (<?= esc($p['position_name']) ?>)
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                            <span class="text-muted" style="font-size: 11px;">เลือก รอง ผอ. กลุ่มงานที่เกี่ยวข้องเพื่อพิจารณากลั่นกรองก่อนเสนอ ผอ.</span>
                        </div>

                        <!-- ลำดับที่ ๒ (หรือ ลำดับสุดท้าย): ผู้อำนวยการโรงเรียน สั่งการ -->
                        <div class="mb-3">
                            <label class="form-label-bold">
                                <i class="bx bxs-crown text-warning"></i> ลำดับสั่งการสูงสุด (ผู้อำนวยการโรงเรียน)
                            </label>
                            <div class="route-step-card">
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="route-step-badge bg-primary">
                                        <i class="bx bxs-crown" style="font-size: 14px;"></i>
                                    </div>
                                    <div>
                                        <strong class="text-dark small d-block"><?= esc($defaultDirector['full_name'] ?? 'ผู้อำนวยการโรงเรียน') ?></strong>
                                        <span class="text-muted" style="font-size: 11px;">ผู้อำนวยการโรงเรียน (ผู้มีอำนาจสั่งการสูงสุด)</span>
                                    </div>
                                </div>
                                <span class="badge bg-warning text-dark fw-bold px-2 py-0.5" style="font-size: 10.5px;">ผู้สั่งการ</span>
                            </div>
                            <input type="hidden" name="route_personnel[]" value="<?= esc($defaultDirector['pers_id'] ?? 'pers_060') ?>">
                        </div>

                        <!-- ข้อความนำเรียน ผอ. / บันทึกเสนอสารบรรณ -->
                        <div class="mb-3 pt-2 border-top">
                            <label class="form-label-bold">
                                <i class="bx bx-comment-detail text-primary"></i> บันทึกเสนอสารบรรณ (นำเรียน ผอ.)
                            </label>
                            
                            <!-- Quick Proposal Chips -->
                            <div class="mb-2">
                                <span class="quick-chip" onclick="applyPresetProposal('เพื่อโปรดพิจารณา')">เพื่อโปรดพิจารณา</span>
                                <span class="quick-chip" onclick="applyPresetProposal('เพื่อโปรดทราบ')">เพื่อโปรดทราบ</span>
                                <span class="quick-chip" onclick="applyPresetProposal('เพื่อโปรดพิจารณาสั่งการ')">เพื่อโปรดพิจารณาสั่งการ</span>
                                <span class="quick-chip" onclick="applyPresetProposal('เห็นควรมอบหมายกลุ่มบริหารวิชาการ ดำเนินการต่อไป')">มอบวิชาการ</span>
                                <span class="quick-chip" onclick="applyPresetProposal('เห็นควรมอบหมายกลุ่มบริหารทั่วไป ดำเนินการต่อไป')">มอบทั่วไป</span>
                                <span class="quick-chip" onclick="applyPresetProposal('เห็นควรมอบหมายกลุ่มบริหารงานบุคคล ดำเนินการต่อไป')">มอบบุคคล</span>
                                <span class="quick-chip" onclick="applyPresetProposal('เห็นควรมอบหมายกลุ่มบริหารงบประมาณ ดำเนินการต่อไป')">มอบงบประมาณ</span>
                            </div>

                            <textarea name="proposal_comment" id="proposalCommentText" class="form-control" rows="2" placeholder="ระบุข้อความนำเรียน หรือบันทึกเสนอความเห็นของเจ้าหน้าที่สารบรรณ..."></textarea>
                        </div>

                        <div id="routeStepNotice" class="alert alert-secondary py-2 px-3 small mb-0 rounded-3" style="font-size: 11.5px;">
                            <i class="bx bx-check-shield text-success me-1"></i> เส้นทาง: <strong>เสนอ ผอ. ลงนามสั่งการโดยตรง (ขั้นที่ ๑)</strong>
                        </div>
                    </div>

                    <!-- Submit Button Area -->
                    <div class="p-3 bg-light border-top">
                        <button type="button" id="btnSubmitInbound" class="btn btn-submit-inbound w-100 shadow-sm" onclick="handleInboundSubmit()">
                            <i class="bx bx-check-circle me-1.5 fs-5 align-middle"></i> ลงทะเบียนรับหนังสือและส่งเกษียน
                        </button>
                        <div class="text-center text-muted small mt-2" style="font-size: 11px;">
                            <i class="bx bx-lock-alt me-1"></i> ระบบจะตรวจสอบความถูกต้องและประทับตราดิจิทัลอัตโนมัติ
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>

<script>
    // =========================================================
    // ตัวแปรและสถานะระบบพรีวิวเอกสาร & ตรายางรับ
    // =========================================================
    let currentPdfDoc = null;
    let currentPdfPageNum = 1;
    let pdfTotalPages = 1;
    let receiveStampScale = 1.0;
    let isStampModeActive = true;

    // Quick Fill Helper for Origin Org
    function fillFromOrg(name) {
        const input = document.getElementById('fromOrgInput');
        if (input) {
            input.value = name;
            input.focus();
        }
    }

    // =========================================================
    // ๑. ระบบพรีวิวไฟล์เอกสารสแกน (PDF.js / Image Viewer & Dropzone)
    // =========================================================
    const dropzoneArea = document.getElementById('dropzoneArea');
    if (dropzoneArea) {
        ['dragenter', 'dragover'].forEach(eventName => {
            dropzoneArea.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropzoneArea.classList.add('is-dragover');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropzoneArea.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropzoneArea.classList.remove('is-dragover');
            }, false);
        });

        dropzoneArea.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            if (files && files.length > 0) {
                const fileInput = document.getElementById('inboundFileInput');
                fileInput.files = files;
                previewInboundFile(fileInput);
            }
        });
    }

    function previewInboundFile(input) {
        if (!input.files || !input.files[0]) return;
        const file = input.files[0];
        const fileName = file.name;
        const fileExt = fileName.split('.').pop().toLowerCase();

        // อัปเดตข้อมูลกล่องข้อความ
        const noticeEl = document.getElementById('filePreviewNotice');
        if (noticeEl) noticeEl.classList.remove('d-none');
        document.getElementById('previewFileName').textContent = fileName;
        const sizeMb = (file.size / (1024 * 1024)).toFixed(2);
        document.getElementById('previewFileSize').textContent = `ขนาดไฟล์: ${sizeMb} MB | ชนิด: ${fileExt.toUpperCase()}`;

        const badgeStatus = document.getElementById('previewBadgeStatus');
        if (badgeStatus) {
            badgeStatus.className = 'badge bg-success text-white fw-bold';
            badgeStatus.textContent = 'แสดงตัวอย่างแล้ว';
        }

        // แสดง Wrapper หน้ากระดาษ ซ่อน Placeholder
        const placeholder = document.getElementById('inboundFilePlaceholder');
        if (placeholder) placeholder.classList.add('d-none');
        const paperWrapper = document.getElementById('inboundPaperWrapper');
        if (paperWrapper) paperWrapper.classList.remove('d-none');

        const canvas = document.getElementById('pdfPageCanvas');
        const imgPreview = document.getElementById('imagePreview');
        const fallbackIframe = document.getElementById('pdfIframeFallback');
        const paginationBar = document.getElementById('pdfPaginationBar');

        canvas.classList.add('d-none');
        imgPreview.classList.add('d-none');
        fallbackIframe.classList.add('d-none');

        if (fileExt === 'pdf' || (file.type && file.type.includes('pdf'))) {
            const fileReader = new FileReader();
            fileReader.onload = function () {
                const typedarray = new Uint8Array(this.result);
                if (typeof pdfjsLib !== 'undefined') {
                    pdfjsLib.getDocument({ data: typedarray }).promise.then(function (pdf) {
                        currentPdfDoc = pdf;
                        pdfTotalPages = pdf.numPages;
                        currentPdfPageNum = 1;

                        document.getElementById('previewFileSize').textContent = `ขนาดไฟล์: ${sizeMb} MB | ชนิด: PDF | เอกสารทั้งหมด ${toThaiDigits(pdfTotalPages)} หน้า (บันทึกเก็บครบทุกหน้า ตรายางรับประทับเฉพาะหน้าแรก)`;

                        updatePdfPageDisplay();
                        renderCurrentPdfPage();
                    }).catch(function (err) {
                        console.warn('PDF.js render error, fallback to iframe:', err);
                        if (fallbackIframe) {
                            fallbackIframe.src = URL.createObjectURL(file);
                            fallbackIframe.classList.remove('d-none');
                            fallbackIframe.style.display = 'block';
                        }
                        initLiveReceiveStampDrag();
                    });
                } else {
                    if (fallbackIframe) {
                        fallbackIframe.src = URL.createObjectURL(file);
                        fallbackIframe.classList.remove('d-none');
                        fallbackIframe.style.display = 'block';
                    }
                    initLiveReceiveStampDrag();
                }
            };
            fileReader.readAsArrayBuffer(file);
        } else {
            // Image preview
            currentPdfDoc = null;
            const reader = new FileReader();
            reader.onload = function (e) {
                imgPreview.src = e.target.result;
                imgPreview.onload = function () {
                    const wrapper = document.getElementById('inboundPaperWrapper');
                    const isLandscape = imgPreview.naturalWidth > imgPreview.naturalHeight;
                    if (wrapper) {
                        wrapper.style.maxWidth = isLandscape ? '29.7cm' : '21cm';
                        wrapper.style.aspectRatio = `${imgPreview.naturalWidth} / ${imgPreview.naturalHeight}`;
                        wrapper.style.height = 'auto';
                        wrapper.style.minHeight = '0';
                    }
                    imgPreview.classList.remove('d-none');
                    imgPreview.style.display = 'block';
                    const stamp = document.getElementById('liveReceiveStamp');
                    if (stamp && isStampModeActive) {
                        stamp.style.display = 'block';
                    }
                    initLiveReceiveStampDrag();
                    updateStampDimensions();
                };
            };
            reader.readAsDataURL(file);
        }
    }

    function renderCurrentPdfPage() {
        if (!currentPdfDoc) return;
        const canvas = document.getElementById('pdfPageCanvas');
        const stamp = document.getElementById('liveReceiveStamp');
        const fallbackIframe = document.getElementById('pdfIframeFallback');
        if (!canvas) return;

        currentPdfDoc.getPage(currentPdfPageNum).then(function (page) {
            const wrapper = document.getElementById('inboundPaperWrapper');
            const unscaledViewport = page.getViewport({ scale: 1.0 });
            const isLandscape = unscaledViewport.width > unscaledViewport.height;
            if (wrapper) {
                wrapper.style.maxWidth = isLandscape ? '29.7cm' : '21cm';
                wrapper.style.aspectRatio = `${unscaledViewport.width} / ${unscaledViewport.height}`;
                wrapper.style.height = 'auto';
                wrapper.style.minHeight = '0';
            }

            const containerWidth = (wrapper && wrapper.clientWidth > 0) ? wrapper.clientWidth : (isLandscape ? 1000 : 760);
            const scale = (containerWidth / unscaledViewport.width) * 1.5;
            const viewport = page.getViewport({ scale: scale });

            const context = canvas.getContext('2d');
            canvas.height = viewport.height;
            canvas.width = viewport.width;

            page.render({ canvasContext: context, viewport: viewport }).promise.then(function () {
                canvas.classList.remove('d-none');
                canvas.style.display = 'block';

                // แสดงตรายางเฉพาะหน้าแรกตามระเบียบงานสารบรรณ
                if (stamp) {
                    if (currentPdfPageNum === 1 && isStampModeActive) {
                        stamp.style.display = 'block';
                    } else {
                        stamp.style.display = 'none';
                    }
                }
                initLiveReceiveStampDrag();
                updateStampDimensions();
            }).catch(function (renderErr) {
                console.warn('Canvas render error, falling back to iframe:', renderErr);
                if (fallbackIframe) {
                    fallbackIframe.classList.remove('d-none');
                    fallbackIframe.style.display = 'block';
                }
            });
        });
    }

    function changePdfPage(delta) {
        if (!currentPdfDoc || pdfTotalPages <= 1) return;
        const newPage = currentPdfPageNum + delta;
        if (newPage >= 1 && newPage <= pdfTotalPages) {
            currentPdfPageNum = newPage;
            updatePdfPageDisplay();
            renderCurrentPdfPage();
        }
    }

    function jumpToPdfPage(pageNum) {
        if (!currentPdfDoc || pageNum < 1 || pageNum > pdfTotalPages) return;
        currentPdfPageNum = pageNum;
        updatePdfPageDisplay();
        renderCurrentPdfPage();
    }

    function updatePdfPageDisplay() {
        const display = document.getElementById('pdfPageNumDisplay');
        const prevBtn = document.getElementById('pdfPrevPageBtn');
        const nextBtn = document.getElementById('pdfNextPageBtn');
        const jumpSelect = document.getElementById('pdfPageJumpSelect');
        const totalBadge = document.getElementById('pdfTotalPagesBadge');
        const totalCountEl = document.getElementById('pdfTotalPagesCount');
        const noticeEl = document.getElementById('nonFirstPageNotice');
        const noticePageNum = document.getElementById('noticeCurrentPageNum');

        if (display) display.textContent = `หน้า ${toThaiDigits(currentPdfPageNum)} / ${toThaiDigits(pdfTotalPages)}`;
        if (prevBtn) prevBtn.disabled = (currentPdfPageNum <= 1);
        if (nextBtn) nextBtn.disabled = (currentPdfPageNum >= pdfTotalPages);

        if (totalBadge && totalCountEl) {
            totalCountEl.textContent = toThaiDigits(pdfTotalPages);
            totalBadge.classList.toggle('d-none', pdfTotalPages <= 1);
        }

        if (jumpSelect) {
            if (jumpSelect.options.length !== pdfTotalPages) {
                jumpSelect.innerHTML = '';
                for (let i = 1; i <= pdfTotalPages; i++) {
                    const opt = document.createElement('option');
                    opt.value = i;
                    opt.textContent = `หน้า ${toThaiDigits(i)}`;
                    jumpSelect.appendChild(opt);
                }
            }
            jumpSelect.value = currentPdfPageNum;
            jumpSelect.style.display = pdfTotalPages > 1 ? 'inline-block' : 'none';
        }

        if (noticeEl) {
            if (currentPdfPageNum > 1) {
                if (noticePageNum) noticePageNum.textContent = toThaiDigits(currentPdfPageNum);
                noticeEl.classList.remove('d-none');
            } else {
                noticeEl.classList.add('d-none');
            }
        }
    }

    // =========================================================
    // ๒. ระบบตรายางรับหนังสือ (Live Receiving Stamp Draggable & Scalable)
    // =========================================================
    function handleStampModeChange(mode) {
        const radioStamp = document.getElementById('modeRadioStamp');
        const radioClean = document.getElementById('modeRadioClean');
        const cardStamp = document.getElementById('modeCardStamp');
        const cardClean = document.getElementById('modeCardClean');
        const stampGroup = document.getElementById('stampDetailsGroup');
        const stampEl = document.getElementById('liveReceiveStamp');
        const inputHas = document.getElementById('inputHasReceiveStamp');
        const notice = document.getElementById('stampModeAlertNotice');

        if (mode === 1) {
            isStampModeActive = true;
            if (radioStamp) radioStamp.checked = true;
            if (cardStamp) cardStamp.className = 'stamp-mode-card is-active-stamp';
            if (cardClean) cardClean.className = 'stamp-mode-card';
            if (stampGroup) stampGroup.style.display = 'block';
            if (stampEl && (currentPdfPageNum === 1 || !currentPdfDoc)) {
                stampEl.style.display = 'block';
            }
            if (inputHas) inputHas.value = '1';
            if (notice) {
                notice.innerHTML = `
                    <div class="d-flex align-items-start gap-2">
                        <i class="bx bx-info-circle text-primary fs-5 flex-shrink-0 mt-0.5"></i>
                        <span class="text-dark" style="font-size: 12px; line-height: 1.5;">
                            เมื่อลงทะเบียนรับแล้ว ระบบจะประทับ <strong>ตรายางรับหนังสือ</strong> สีน้ำเงินลงในเนื้อไฟล์เอกสารจริงถาวรตามตำแหน่งและขนาดที่จัดวาง
                        </span>
                    </div>`;
            }
        } else {
            isStampModeActive = false;
            if (radioClean) radioClean.checked = true;
            if (cardClean) cardClean.className = 'stamp-mode-card is-active-clean';
            if (cardStamp) cardStamp.className = 'stamp-mode-card';
            if (stampGroup) stampGroup.style.display = 'none';
            if (stampEl) stampEl.style.display = 'none';
            if (inputHas) inputHas.value = '0';
            if (notice) {
                notice.innerHTML = `
                    <div class="d-flex align-items-start gap-2">
                        <i class="bx bx-check-circle text-info fs-5 flex-shrink-0 mt-0.5"></i>
                        <span class="text-dark" style="font-size: 12px; line-height: 1.5;">
                            ลงทะเบียนรับในระบบสารบรรณ (สกจ.) แบบ <strong>มาตรฐานราชการ (ไม่สแตมป์ตรายางบนเอกสาร)</strong>
                        </span>
                    </div>`;
            }
        }
    }

    function applyPresetProposal(val) {
        const textarea = document.getElementById('proposalCommentText');
        if (textarea && val) {
            textarea.value = val;
            textarea.focus();
        }
    }

    function getActiveDocElement() {
        const canvas = document.getElementById('pdfPageCanvas');
        const imgPreview = document.getElementById('imagePreview');
        const wrapper = document.getElementById('inboundPaperWrapper');
        if (canvas && !canvas.classList.contains('d-none') && canvas.clientHeight > 0) return canvas;
        if (imgPreview && !imgPreview.classList.contains('d-none') && imgPreview.clientHeight > 0) return imgPreview;
        return wrapper;
    }

    function updateStampDimensions() {
        const stamp = document.getElementById('liveReceiveStamp');
        const docEl = getActiveDocElement();
        const inputWPct = document.getElementById('inputReceiveStampWPct');
        if (!stamp || !docEl || docEl.clientWidth === 0) return;

        const scaleVal = (typeof receiveStampScale !== 'undefined' && receiveStampScale > 0) ? receiveStampScale : 1.0;
        const stampW = stamp.offsetWidth * scaleVal;
        const wPct = ((stampW / docEl.clientWidth) * 100).toFixed(2) + '%';
        if (inputWPct) inputWPct.value = wPct;
    }

    function toThaiDigits(num) {
        const thai = ['๐', '๑', '๒', '๓', '๔', '๕', '๖', '๗', '๘', '๙'];
        return String(num).replace(/[0-9]/g, d => thai[d]);
    }

    function changeReceiveStampScale(delta, event) {
        if (event) event.stopPropagation();
        receiveStampScale = Math.min(Math.max(receiveStampScale + delta, 0.4), 2.0);
        const stamp = document.getElementById('liveReceiveStamp');
        const display = document.getElementById('liveReceiveStampScaleDisplay');
        const tbDisplay = document.getElementById('stampScaleToolbarDisplay');
        const inputScale = document.getElementById('inputReceiveStampScale');
        if (stamp) {
            stamp.style.transform = `rotate(-1deg) scale(${receiveStampScale})`;
        }
        const percentText = Math.round(receiveStampScale * 100) + '%';
        if (display) display.textContent = percentText;
        if (tbDisplay) tbDisplay.textContent = percentText;
        if (inputScale) inputScale.value = receiveStampScale.toFixed(2);
        updateStampDimensions();
    }

    function resetReceiveStampPos(event) {
        if (event) event.stopPropagation();
        receiveStampScale = 1.0;
        changeReceiveStampScale(0);
        quickSetReceiveStamp('top-right');
    }

    function quickSetReceiveStamp(pos, event) {
        if (event) event.stopPropagation();
        const stamp = document.getElementById('liveReceiveStamp');
        const inputX = document.getElementById('inputReceiveStampX');
        const inputY = document.getElementById('inputReceiveStampY');

        const scaleVal = (typeof receiveStampScale !== 'undefined' && receiveStampScale > 0) ? receiveStampScale : 1.0;
        const baseWPct = 23.8;
        const currentWPct = baseWPct * scaleVal;

        const rightMarginPct = 4.0;
        const topMarginPct = 3.5;

        let xPct = Math.max(1.0, (100.0 - currentWPct - rightMarginPct)).toFixed(2) + '%';
        let yPct = topMarginPct.toFixed(2) + '%';

        if (pos === 'top-left') {
            xPct = '4.00%';
            yPct = topMarginPct.toFixed(2) + '%';
        }

        if (stamp) {
            stamp.style.left = xPct;
            stamp.style.top = yPct;
        }
        if (inputX) inputX.value = xPct;
        if (inputY) inputY.value = yPct;
        updateStampDimensions();
    }

    function syncLiveStampRegNo(val) {
        const el = document.getElementById('liveReceiveStampRegNoDisplay');
        if (el) el.textContent = toThaiDigits(val || '-');
    }

    function syncLiveDocDate(val) {
        const stampDate = document.getElementById('liveReceiveStampDateDisplay');
        if (!stampDate || !val) return;
        const parts = val.split('-');
        if (parts.length === 3) {
            const yearTh = parseInt(parts[0], 10) + 543;
            const thaiMonths = ['', 'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
            const m = parseInt(parts[1], 10);
            const d = parseInt(parts[2], 10);
            stampDate.textContent = toThaiDigits(`${d} ${thaiMonths[m] || ''} ${yearTh}`);
        }
    }

    function syncLiveStampTime(val) {
        const el = document.getElementById('liveReceiveStampTimeDisplay');
        if (el) el.textContent = toThaiDigits(val || '-');
    }

    function initLiveReceiveStampDrag() {
        const stamp = document.getElementById('liveReceiveStamp');
        const container = document.getElementById('inboundPaperWrapper');
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

            initialLeft = stamp.offsetLeft;
            initialTop = stamp.offsetTop;

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

            const docEl = getActiveDocElement();
            const docW = docEl.clientWidth;
            const docH = docEl.clientHeight;

            const scaleVal = (typeof receiveStampScale !== 'undefined' && receiveStampScale > 0) ? receiveStampScale : 1.0;
            const stampW = stamp.offsetWidth * scaleVal;
            const stampH = stamp.offsetHeight * scaleVal;
            const maxLeft = Math.max(0, docW - stampW);
            const maxTop = Math.max(0, docH - stampH);

            let newLeft = Math.max(0, Math.min(initialLeft + deltaX, maxLeft));
            let newTop = Math.max(0, Math.min(initialTop + deltaY, maxTop));

            const leftPct = ((newLeft / docW) * 100).toFixed(2) + '%';
            const topPct = ((newTop / docH) * 100).toFixed(2) + '%';

            stamp.style.left = leftPct;
            stamp.style.top = topPct;

            if (inputX) inputX.value = leftPct;
            if (inputY) inputY.value = topPct;
            updateStampDimensions();
        }

        function onPointerUp() {
            isDragging = false;
            document.removeEventListener('mousemove', onPointerMove);
            document.removeEventListener('mouseup', onPointerUp);
            document.removeEventListener('touchmove', onPointerMove);
            document.removeEventListener('touchend', onPointerUp);
        }

        stamp.removeEventListener('mousedown', onPointerDown);
        stamp.removeEventListener('touchstart', onPointerDown);
        stamp.addEventListener('mousedown', onPointerDown);
        stamp.addEventListener('touchstart', onPointerDown, { passive: true });

        // Corner Resize Handle
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
                receiveStampScale = Math.min(Math.max(initialScale * scaleFactor, 0.4), 2.0);

                stamp.style.transform = `rotate(-1deg) scale(${receiveStampScale})`;
                const display = document.getElementById('liveReceiveStampScaleDisplay');
                const tbDisplay = document.getElementById('stampScaleToolbarDisplay');
                const inputScale = document.getElementById('inputReceiveStampScale');
                const percentText = Math.round(receiveStampScale * 100) + '%';
                if (display) display.textContent = percentText;
                if (tbDisplay) tbDisplay.textContent = percentText;
                if (inputScale) inputScale.value = receiveStampScale.toFixed(2);
                updateStampDimensions();
            }

            function onResizeUp() {
                isResizing = false;
                document.removeEventListener('mousemove', onResizeMove);
                document.removeEventListener('mouseup', onResizeUp);
                document.removeEventListener('touchmove', onResizeMove);
                document.removeEventListener('touchend', onResizeUp);
            }

            resizeHandle.removeEventListener('mousedown', onResizeDown);
            resizeHandle.removeEventListener('touchstart', onResizeDown);
            resizeHandle.addEventListener('mousedown', onResizeDown);
            resizeHandle.addEventListener('touchstart', onResizeDown, { passive: false });
        }
    }

    function toggleDeputyStep(val) {
        const notice = document.getElementById('routeStepNotice');
        if (val) {
            notice.innerHTML = '<i class="bx bx-git-repo-forked text-primary me-1"></i> เส้นทาง: <strong>รอง ผอ. กลั่นกรอง (ขั้นที่ ๑)</strong> ➔ <strong>ผอ. ลงนามสั่งการ (ขั้นที่ ๒)</strong>';
        } else {
            notice.innerHTML = '<i class="bx bx-check-shield text-success me-1"></i> เส้นทาง: <strong>เสนอ ผอ. ลงนามสั่งการโดยตรง (ขั้นที่ ๑)</strong>';
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        initLiveReceiveStampDrag();

        const regInput = document.getElementById('inputRegNo');
        if (regInput) {
            regInput.addEventListener('input', function () { syncLiveStampRegNo(this.value); });
        }
        const dateInput = document.getElementById('inputReceiveDate');
        if (dateInput) {
            dateInput.addEventListener('change', function () { syncLiveDocDate(this.value); });
            dateInput.addEventListener('input', function () { syncLiveDocDate(this.value); });
        }
        const timeInput = document.getElementById('inputReceiveTime');
        if (timeInput) {
            timeInput.addEventListener('input', function () { syncLiveStampTime(this.value); });
        }
    });

    // =========================================================
    // ฟังก์ชันตรวจสอบและยืนยันการลงทะเบียนรับหนังสือเข้า
    // =========================================================
    function handleInboundSubmit() {
        const createForm = document.getElementById('inboundCreateForm');
        if (!createForm) return;

        // 1. ตรวจสอบ HTML5 Required Fields
        if (!createForm.checkValidity()) {
            createForm.reportValidity();
            return;
        }

        // ตรวจสอบไฟล์เอกสาร
        const fileInput = document.getElementById('inboundFileInput');
        if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'กรุณาเลือกไฟล์เอกสารสแกน',
                text: 'โปรดเลือกไฟล์ PDF หรือรูปภาพของหนังสือต้นฉบับก่อนลงทะเบียนรับเข้า',
                confirmButtonColor: '#696cff',
                confirmButtonText: 'ตกลง'
            });
            return;
        }

        const regNoVal = document.getElementById('inputRegNo') ? document.getElementById('inputRegNo').value : '';
        const titleVal = document.querySelector('input[name="title"]') ? document.querySelector('input[name="title"]').value : '';
        const docNoVal = document.querySelector('input[name="doc_no"]') ? document.querySelector('input[name="doc_no"]').value : '';
        const hasStamp = document.getElementById('inputHasReceiveStamp') ? document.getElementById('inputHasReceiveStamp').value === '1' : true;

        // 2. แสดง SweetAlert2 ยืนยันข้อมูลก่อนบันทึก
        Swal.fire({
            title: 'ยืนยันลงทะเบียนรับหนังสือเข้า?',
            html: `
                <div class="text-start p-3 bg-light rounded-3 small mb-2 border" style="font-size: 13px; line-height: 1.6;">
                    <div class="mb-1"><strong>เลขที่หนังสือต้นทาง:</strong> <span class="text-dark fw-bold font-monospace">${docNoVal || '-'}</span></div>
                    <div class="mb-1"><strong>เลขทะเบียนรับ สกจ.:</strong> <span class="text-primary font-monospace fw-bold fs-6">${regNoVal || '-'}</span></div>
                    <div class="mb-1 text-truncate"><strong>เรื่อง:</strong> <span class="text-dark">${titleVal || '-'}</span></div>
                    <div><strong>การประทับตรา:</strong> ${hasStamp ? '<span class="badge bg-success text-white fw-bold">ประทับตรายางรับลงเอกสาร</span>' : '<span class="badge bg-secondary text-white fw-bold">ไม่ประทับตรายาง</span>'}</div>
                </div>
                <div class="text-muted small mt-2">
                    <i class="bx bx-info-circle me-1 text-primary"></i> เมื่อยืนยัน ระบบจะประทับตราดิจิทัลลงในไฟล์ และส่งหนังสือเข้าสู่สายเกษียนทันที
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#696cff',
            cancelButtonColor: '#8592a3',
            confirmButtonText: '<i class="bx bx-check-circle me-1"></i> ยืนยันการลงทะเบียน',
            cancelButtonText: 'ยกเลิก',
            reverseButtons: true,
            customClass: {
                confirmButton: 'btn btn-primary fw-bold px-4 py-2',
                cancelButton: 'btn btn-outline-secondary px-3 py-2 ms-2'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                // 3. คำนวณพิกัดและขนาดตรายางรอบสุดท้าย
                const stamp = document.getElementById('liveReceiveStamp');
                const docEl = getActiveDocElement();
                const inputX = document.getElementById('inputReceiveStampX');
                const inputY = document.getElementById('inputReceiveStampY');
                const inputWPct = document.getElementById('inputReceiveStampWPct');
                const inputScale = document.getElementById('inputReceiveStampScale');

                if (stamp && docEl && docEl.clientWidth > 0 && docEl.clientHeight > 0) {
                    const scaleVal = (typeof receiveStampScale !== 'undefined' && receiveStampScale > 0) ? receiveStampScale : 1.0;
                    const leftPct = ((stamp.offsetLeft / docEl.clientWidth) * 100).toFixed(2) + '%';
                    const topPct = ((stamp.offsetTop / docEl.clientHeight) * 100).toFixed(2) + '%';
                    const stampW = stamp.offsetWidth * scaleVal;
                    const wPct = ((stampW / docEl.clientWidth) * 100).toFixed(2) + '%';

                    if (inputX) inputX.value = leftPct;
                    if (inputY) inputY.value = topPct;
                    if (inputWPct) inputWPct.value = wPct;
                    if (inputScale) inputScale.value = scaleVal.toFixed(2);
                }

                // 4. แสดง SweetAlert Loading หมุนเต็มจอ
                Swal.fire({
                    title: 'กำลังลงทะเบียนรับหนังสือ...',
                    html: '<div class="py-2"><span class="text-muted small">ระบบกำลังประทับตราดิจิทัลลงในเอกสารและส่งต่อสายเกษียน กรุณารอสักครู่...</span></div>',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // 5. ปรับสถานะปุ่มกดให้หมุน Loading
                const submitBtn = document.getElementById('btnSubmitInbound');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.classList.add('disabled');
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>กำลังประมวลผล...';
                }

                // ปิด data-no-progress ชั่วคราวเพื่อส่ง Form แบบปกติพร้อม Loading
                createForm.setAttribute('data-no-progress', 'true');
                createForm.submit();
            }
        });
    }
</script>
<?= $this->endSection() ?>