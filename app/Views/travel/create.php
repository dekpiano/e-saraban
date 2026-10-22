<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>ยื่นขออนุมัติเดินทางไปราชการ<?= $this->endSection() ?>

<?= $this->section('content') ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<style>
/* Select2 Custom Styling for Teacher Selection */
.select2-container--bootstrap-5 .select2-selection {
    border-radius: 0.5rem !important;
    min-height: calc(2.6rem + 2px) !important;
    font-size: 0.95rem !important;
    border: 1.5px solid #d9dee3 !important;
    padding: 0.4rem 0.6rem !important;
    background-color: #ffffff !important;
}
.select2-container--bootstrap-5.select2-container--focus .select2-selection,
.select2-container--bootstrap-5.select2-container--open .select2-selection {
    border-color: #696cff !important;
    box-shadow: 0 0 0 0.25rem rgba(105, 108, 255, 0.15) !important;
}
.select2-container--bootstrap-5 .select2-dropdown {
    border-radius: 0.75rem !important;
    box-shadow: 0 12px 28px rgba(50, 50, 93, 0.18), 0 6px 12px rgba(0, 0, 0, 0.08) !important;
    border: 1.5px solid #d9dee3 !important;
    z-index: 9999 !important;
}
.select2-container--bootstrap-5 .select2-results__group {
    font-weight: 700 !important;
    color: #435971 !important;
    background: #f5f6f8 !important;
    padding: 7px 12px !important;
    font-size: 0.82rem !important;
    letter-spacing: 0.3px;
    border-bottom: 1px solid #edf0f5 !important;
    border-top: 1px solid #edf0f5 !important;
}
.select2-container--bootstrap-5 .select2-results__option--highlighted {
    background-color: #e7e7ff !important;
    color: #5f61e6 !important;
}
.applicant-preview-box {
    border: 1.5px solid #dce2ea;
    border-radius: 0.75rem;
    background: #ffffff;
    transition: all 0.25s ease-in-out;
}
.applicant-preview-box.is-proxy {
    border-color: #71dd37;
    background: #fcfdfa;
}
.doc-btn-check + label {
    border-width: 2px !important;
    border-radius: 12px !important;
    cursor: pointer;
    background-color: #ffffff;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}
.doc-btn-check + label:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(105, 108, 255, 0.18) !important;
}
.doc-btn-check + label .check-indicator {
    opacity: 0.35;
    transition: all 0.2s ease-in-out;
}
.doc-btn-check:checked + label {
    background-color: #696cff !important;
    border-color: #696cff !important;
    color: #ffffff !important;
    box-shadow: 0 6px 18px rgba(105, 108, 255, 0.35) !important;
}
.doc-btn-check:checked + label i {
    color: #ffffff !important;
}
.doc-btn-check:checked + label .check-indicator {
    opacity: 1 !important;
    transform: scale(1.15);
}

/* Dropzone drag and drop styling */
.dropzone-box {
    border: 2px dashed #696cff !important;
    border-radius: 14px !important;
    background: #f8f9ff !important;
    padding: 2.2rem 1.5rem !important;
    text-align: center !important;
    cursor: pointer !important;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
    position: relative !important;
}
.dropzone-box:hover, .dropzone-box.dragover {
    background: #eef0ff !important;
    border-color: #5f61e6 !important;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(105, 108, 255, 0.18) !important;
}
.dropzone-box.is-invalid {
    border-color: #ff3e1d !important;
    background: #fff5f4 !important;
}
.dropzone-preview {
    display: none;
    border: 1.5px solid #696cff;
    border-radius: 12px;
    background: #ffffff;
    padding: 1.25rem;
    box-shadow: 0 4px 16px rgba(105, 108, 255, 0.12);
    animation: fadeIn 0.3s ease-in-out;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-6px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-1 fw-bold text-primary">
                        <i class="bx bx-briefcase-alt-2 me-2"></i> แบบฟอร์มขออนุมัติเดินทางไปราชการ / อบรมพัฒนาตนเอง
                    </h5>
                    <small class="text-muted">กรอกข้อมูลการเดินทางไปราชการ การจัดสอนแทน และเลือกสายการอนุมัติตามลำดับขั้น</small>
                </div>
                <a href="<?= base_url('admin/travel') ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="bx bx-arrow-back me-1"></i> ย้อนกลับ
                </a>
            </div>

            <div class="card-body">
                <form action="<?= base_url('admin/travel/store') ?>" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <input type="hidden" name="memo_id" value="<?= (int)($memoId ?? 0) ?>">
                    <div class="alert alert-info d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
                        <div><strong><i class="bx bx-file me-1"></i>บันทึกข้อความต้นเรื่อง</strong><div class="small mt-1"><?= esc(($memo['doc_no'] ?? '-') . ' · ' . ($memo['title'] ?? '-')) ?></div></div>
                        <a class="btn btn-sm btn-outline-primary" target="_blank" href="<?= base_url('admin/memos/' . (int)($memoId ?? 0)) ?>">เปิดบันทึกข้อความ</a>
                    </div>

                    <!-- คำชี้แจงการกรอกข้อมูล -->
                    <div class="alert alert-primary d-flex align-items-center mb-4 py-2 px-3 shadow-sm border-0" style="background: #e7e7ff; color: #43449e;">
                        <i class="bx bx-info-circle fs-4 me-2"></i>
                        <div class="small">
                            <strong>คำแนะนำการกรอกข้อมูล:</strong> ช่องที่มีเครื่องหมาย <span class="text-danger fw-bold fs-6">*</span> และป้าย <span class="badge bg-danger py-0 px-1" style="font-size: 0.68rem;">จำเป็น</span> หมายถึง <strong>ช่องที่ต้องกรอก/เลือกข้อมูลให้ครบถ้วน</strong> ก่อนส่งคำขออนุมัติ
                        </div>
                    </div>

                    <!-- หมวด 1: ข้อมูลผู้ยื่นขอ / ผู้ขอไปราชการ -->
                    <?php
                        $sessionRole = session()->get('role');
                        $isOfficer   = in_array($sessionRole, ['super_admin', 'admin', 'saraban', 'executive'], true);

                        $groupedPersonnel = [];
                        if (!empty($personnelList)) {
                            foreach ($personnelList as $p) {
                                $grp = $p['group_name'] ?: 'บุคลากรทั่วไป';
                                $groupedPersonnel[$grp][] = $p;
                            }
                        }
                    ?>

                    <?php if ($isOfficer): ?>
                        <div class="card border border-primary mb-4 shadow-sm" style="background: #fdfdff;">
                            <div class="card-header bg-label-primary py-2 px-3 d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-primary">
                                    <i class="bx bx-user-pin me-1 fs-5 align-middle"></i> ผู้ยื่นขออนุมัติไปราชการ (เจ้าของเรื่อง)
                                </span>
                                <span class="badge bg-primary text-white"><i class="bx bx-check-shield me-1"></i> โหมดเจ้าหน้าที่: บันทึกแทนครูได้</span>
                            </div>
                            <div class="card-body p-3">
                                <div class="row g-3">
                                    <div class="col-lg-7">
                                        <label class="form-label fw-bold text-dark mb-1">
                                            <i class="bx bx-search-alt text-primary me-1"></i> เลือกครู / บุคลากรผู้ขอไปราชการ <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select select2-applicant" name="applicant_user_id" id="applicantUserSelect" style="width: 100%;" required>
                                            <option value="<?= $user['id'] ?>" selected 
                                                data-pers-id="<?= esc($user['pers_id'] ?? '') ?>"
                                                data-fullname="<?= esc($user['prefix'] . $user['first_name'] . ' ' . $user['last_name']) ?>"
                                                data-email="<?= esc($user['email'] ?? $user['username'] ?? '') ?>"
                                                data-position="<?= esc($user['position_name']) ?>" 
                                                data-department="<?= esc($user['department_name']) ?>"
                                                data-initial="<?= esc(mb_substr($user['first_name'] ?: 'ต', 0, 1)) ?>"
                                                data-is-self="1">
                                                👤 ยื่นขอในนามตนเอง: <?= esc($user['prefix'] . $user['first_name'] . ' ' . $user['last_name']) ?> (<?= esc($user['position_name']) ?>)
                                            </option>

                                            <?php if (!empty($groupedPersonnel)): ?>
                                                <?php foreach ($groupedPersonnel as $groupTitle => $members): ?>
                                                    <optgroup label="── <?= esc($groupTitle) ?> (<?= count($members) ?> ท่าน) ──">
                                                        <?php foreach ($members as $m): ?>
                                                            <?php 
                                                                $isSelf = (!empty($user['pers_id']) && $user['pers_id'] === $m['pers_id']) || (!empty($user['email']) && strtolower($user['email']) === strtolower($m['pers_username']));
                                                                if ($isSelf) continue;
                                                                $val = !empty($m['pers_id']) ? $m['pers_id'] : ($m['user_id'] ?? $m['pers_username']);
                                                                $initial = mb_substr($m['pers_firstname'] ?: 'ค', 0, 1);
                                                            ?>
                                                            <option value="<?= esc($val) ?>"
                                                                data-pers-id="<?= esc($m['pers_id']) ?>"
                                                                data-fullname="<?= esc($m['full_name']) ?>"
                                                                data-email="<?= esc($m['pers_username']) ?>"
                                                                data-position="<?= esc($m['position_name']) ?>" 
                                                                data-department="<?= esc($m['department_name']) ?>"
                                                                data-initial="<?= esc($initial) ?>"
                                                                data-phone="<?= esc($m['pers_phone'] ?? '') ?>"
                                                                data-is-self="0">
                                                                <?= esc($m['full_name']) ?> — <?= esc($m['position_name']) ?> (<?= esc($m['department_name']) ?>)
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </optgroup>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                        <div class="form-text text-muted small mt-1">
                                            <i class="bx bx-info-circle me-1 text-primary"></i> สามารถพิมพ์ค้นหาด้วย <strong>ชื่อ-สกุลครู</strong>, <strong>กลุ่มสาระฯ</strong>, <strong>ตำแหน่ง</strong> หรือ <strong>อีเมล @skj.ac.th</strong> เพื่อเลือกเจ้าของเรื่อง
                                        </div>
                                    </div>

                                    <div class="col-lg-5">
                                        <!-- Dynamic Applicant Preview Card -->
                                        <div id="applicantPreviewCard" class="applicant-preview-box p-3 shadow-xs">
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <small class="text-muted fw-semibold">ข้อมูลสังกัดของเจ้าของเรื่อง:</small>
                                                <span id="dispApplicantBadge" class="badge bg-label-primary px-2 py-1">
                                                    <i class="bx bx-user me-1"></i> ยื่นขอในนามตนเอง
                                                </span>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-md me-3 flex-shrink-0">
                                                    <span id="dispApplicantAvatar" class="avatar-initial rounded-circle bg-primary text-white fw-bold fs-5 shadow-xs">
                                                        <?= mb_substr($user['first_name'] ?: 'U', 0, 1) ?>
                                                    </span>
                                                </div>
                                                <div class="flex-grow-1 min-w-0">
                                                    <h6 id="dispApplicantName" class="mb-1 fw-bold text-dark text-truncate" style="font-family: 'K2D', sans-serif;">
                                                        <?= esc($user['prefix'] . $user['first_name'] . ' ' . $user['last_name']) ?>
                                                    </h6>
                                                    <div class="d-flex flex-wrap align-items-center gap-1 text-secondary small">
                                                        <span id="dispApplicantPosition" class="fw-semibold text-dark"><?= esc($user['position_name']) ?></span>
                                                        <span class="text-muted">•</span>
                                                        <span id="dispApplicantDepartment" class="badge bg-label-info text-dark" style="font-size: 0.72rem;"><?= esc($user['department_name']) ?></span>
                                                    </div>
                                                    <div id="dispApplicantEmailContainer" class="text-muted small mt-1 text-truncate">
                                                        <i class="bx bx-envelope text-primary me-1"></i><code id="dispApplicantEmail" class="text-primary small"><?= esc($user['email'] ?? $user['username'] ?? '') ?></code>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="dispApplicantAction" class="mt-2 pt-2 border-top text-end d-none">
                                                <button type="button" class="btn btn-outline-secondary btn-xs py-1 px-2" onclick="resetApplicantToSelf()">
                                                    <i class="bx bx-undo me-1"></i> คืนค่าเป็นตนเอง
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-light border p-3 mb-4">
                            <input type="hidden" name="applicant_user_id" value="<?= $user['id'] ?>">
                            <div class="row g-2 align-items-center">
                                <div class="col-md-4">
                                    <small class="text-muted d-block">ผู้ขออนุมัติไปราชการ:</small>
                                    <strong class="text-dark fs-6"><?= esc($user['prefix'] . $user['first_name'] . ' ' . $user['last_name']) ?></strong>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted d-block">ตำแหน่ง:</small>
                                    <span><?= esc($user['position_name']) ?></span>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted d-block">กลุ่มสาระฯ / ฝ่ายงาน:</small>
                                    <span><?= esc($user['department_name']) ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- หมวด 1: ข้อมูลการเดินทางไปราชการ -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold text-primary mb-0">
                            <i class="bx bx-info-circle me-1"></i> 1. ข้อมูลการเดินทางไปราชการ
                        </h6>
                        <small class="text-muted"><span class="text-danger">*</span> คือข้อมูลที่จำเป็นต้องกรอก</small>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold d-flex justify-content-between align-items-center mb-1">
                                <span>
                                    เลขที่หนังสือขอไปราชการ <span class="text-danger">*</span>
                                    <span class="badge bg-label-danger" style="font-size:0.68rem;">จำเป็น</span>
                                </span>
                                <span class="badge bg-label-primary font-monospace" style="font-size:0.7rem; cursor: pointer;" onclick="fetchNextDocNo()" title="คลิกเพื่อรีเฟรชเลขรันล่าสุด">
                                    <i class="bx bx-refresh me-0.5"></i> รันเลขล่าสุด
                                </span>
                            </label>
                            <div class="input-group">
                                <input type="text" class="form-control fw-bold text-primary font-monospace" name="doc_no" id="docNoInput" required value="<?= esc($nextDocNo) ?>" placeholder="ระบุเลขที่หนังสือ..." autocomplete="off" />
                                <button type="button" class="btn btn-outline-primary" onclick="fetchNextDocNo()" title="สแกนหาเลขลำดับล่าสุดอัตโนมัติ">
                                    <i class="bx bx-refresh fs-5"></i>
                                </button>
                            </div>
                            <div id="docNoFeedback" class="small mt-1" style="display: none;"></div>
                            <div class="form-text small text-muted">
                                <i class="bx bx-check-shield text-success me-1"></i> ระบบตรวจสอบลำดับล่าสุดอัตโนมัติ ไม่ซ้ำกับเอกสารเดิม
                            </div>
                        </div>

                        <div class="col-md-8">
                            <label class="form-label fw-semibold">
                                เรื่อง / วัตถุประสงค์การไปราชการ <span class="text-danger">*</span> 
                                <span class="badge bg-label-danger" style="font-size:0.68rem;">จำเป็น</span>
                            </label>
                            <input type="text" class="form-control" name="title" id="titleInput" required placeholder="เช่น ขออนุมัติเดินทางไปราชการเพื่อเข้าร่วมการประชุมเชิงปฏิบัติการ..." value="" autocomplete="off" />
                            <div id="titleFeedback" class="small mt-1" style="display: none;"></div>
                        </div>

                        <div class="col-md-8">
                            <label class="form-label fw-semibold">สถานที่ไปราชการ (หน่วยงาน/โรงแรม/โรงเรียน) <span class="text-danger">*</span> <span class="badge bg-label-danger" style="font-size:0.68rem;">จำเป็น</span></label>
                            <input type="text" class="form-control" name="destination" required placeholder="เช่น ศูนย์การประชุมแห่งชาติสิริกิติ์ หรือ สำนักงานเขตพื้นที่ฯ" value="" />
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">จังหวัด <span class="text-danger">*</span> <span class="badge bg-label-danger" style="font-size:0.68rem;">จำเป็น</span></label>
                            <input type="text" class="form-control" name="province" id="provinceInput" list="provinceList" required placeholder="พิมพ์ชื่อจังหวัด เช่น จันทบุรี, กรุงเทพมหานคร" value="<?= esc(old('province')) ?>" autocomplete="off" />
                            <datalist id="provinceList">
                                <option value="กรุงเทพมหานคร">
                                <option value="กระบี่">
                                <option value="กาญจนบุรี">
                                <option value="กาฬสินธุ์">
                                <option value="กำแพงเพชร">
                                <option value="ขอนแก่น">
                                <option value="จันทบุรี">
                                <option value="ฉะเชิงเทรา">
                                <option value="ชลบุรี">
                                <option value="ชัยนาท">
                                <option value="ชัยภูมิ">
                                <option value="ชุมพร">
                                <option value="เชียงราย">
                                <option value="เชียงใหม่">
                                <option value="ตรัง">
                                <option value="ตราด">
                                <option value="ตาก">
                                <option value="นครนายก">
                                <option value="นครปฐม">
                                <option value="นครพนม">
                                <option value="นครราชสีมา">
                                <option value="นครศรีธรรมราช">
                                <option value="นครสวรรค์">
                                <option value="นนทบุรี">
                                <option value="นราธิวาส">
                                <option value="น่าน">
                                <option value="บึงกาฬ">
                                <option value="บุรีรัมย์">
                                <option value="ปทุมธานี">
                                <option value="ประจวบคีรีขันธ์">
                                <option value="ปราจีนบุรี">
                                <option value="ปัตตานี">
                                <option value="พระนครศรีอยุธยา">
                                <option value="พะเยา">
                                <option value="พังงา">
                                <option value="พัทลุง">
                                <option value="พิจิตร">
                                <option value="พิษณุโลก">
                                <option value="เพชรบุรี">
                                <option value="เพชรบูรณ์">
                                <option value="แพร่">
                                <option value="ภูเก็ต">
                                <option value="มหาสารคาม">
                                <option value="มุกดาหาร">
                                <option value="แม่ฮ่องสอน">
                                <option value="ยโสธร">
                                <option value="ยะลา">
                                <option value="ร้อยเอ็ด">
                                <option value="ระนอง">
                                <option value="ระยอง">
                                <option value="ราชบุรี">
                                <option value="ลพบุรี">
                                <option value="ลำปาง">
                                <option value="ลำพูน">
                                <option value="เลย">
                                <option value="ศรีสะเกษ">
                                <option value="สกลนคร">
                                <option value="สงขลา">
                                <option value="สตูล">
                                <option value="สมุทรปราการ">
                                <option value="สมุทรสงคราม">
                                <option value="สมุทรสาคร">
                                <option value="สระแก้ว">
                                <option value="สระบุรี">
                                <option value="สิงห์บุรี">
                                <option value="สุโขทัย">
                                <option value="สุพรรณบุรี">
                                <option value="สุราษฎร์ธานี">
                                <option value="สุรินทร์">
                                <option value="หนองคาย">
                                <option value="หนองบัวลำภู">
                                <option value="อ่างทอง">
                                <option value="อำนาจเจริญ">
                                <option value="อุดรธานี">
                                <option value="อุตรดิตถ์">
                                <option value="อุทัยธานี">
                                <option value="อุบลราชธานี">
                            </datalist>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">ตั้งแต่วันที่ (ปฏิทิน พ.ศ.) <span class="text-danger">*</span> <span class="badge bg-label-danger" style="font-size:0.68rem;">จำเป็น</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bx bx-calendar text-primary"></i></span>
                                <input type="text" class="form-control" name="start_date" id="startDate" required placeholder="เลือกวันที่..." value="" />
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">ถึงวันที่ (ปฏิทิน พ.ศ.) <span class="text-danger">*</span> <span class="badge bg-label-danger" style="font-size:0.68rem;">จำเป็น</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bx bx-calendar text-primary"></i></span>
                                <input type="text" class="form-control" name="end_date" id="endDate" required placeholder="เลือกวันที่..." value="" />
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">รวมจำนวนวันเดินทาง <span class="badge bg-label-info" style="font-size:0.68rem;">คำนวณอัตโนมัติ</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="totalDaysDisp" value="0" readonly />
                                <span class="input-group-text">วัน</span>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                แนบเอกสารคำสั่ง / หนังสือเชิญ (PDF) 
                                <span class="badge bg-label-info ms-1" style="font-size:0.7rem;">
                                    <i class="bx bx-info-circle me-1"></i> ไม่บังคับในขั้นตอนนี้ (เจ้าหน้าที่จะแนบในขั้นตอนที่ 2 จัดทำเรื่อง)
                                </span>
                            </label>

                            <!-- Hidden actual file input (Optional in Step 1) -->
                            <input type="file" name="doc_file" id="realDocFileInput" accept=".pdf,application/pdf" style="display: none;" />

                            <!-- Dropzone Drag & Drop Area -->
                            <div id="pdfDropzone" class="dropzone-box" onclick="triggerBrowseFile()">
                                <div class="d-flex flex-column align-items-center">
                                    <div class="mb-3">
                                        <span class="badge bg-label-primary p-3 rounded-circle">
                                            <i class="bx bxs-cloud-upload fs-1 text-primary"></i>
                                        </span>
                                    </div>
                                    <h5 class="fw-bold text-dark mb-1">ลากและวางไฟล์ PDF ที่นี่ (ถ้ามี) หรือคลิกเพื่อเลือกไฟล์</h5>
                                    <p class="text-muted small mb-3">รองรับเฉพาะเอกสารรูปแบบ <strong>.PDF</strong> (หากยังไม่มีไฟล์ สามารถกดข้ามได้ เจ้าหน้าที่ธุรการจะตรวจสอบเอกสารและแนบไฟล์ในขั้นตอนที่ 2)</p>
                                    <button type="button" class="btn btn-outline-primary btn-sm px-3 shadow-xs">
                                        <i class="bx bx-file me-1"></i> เลือกไฟล์ PDF จากคอมพิวเตอร์ (ถ้ามี)
                                    </button>
                                </div>
                            </div>

                            <!-- File Preview Card when file is selected -->
                            <div id="pdfFilePreview" class="dropzone-preview mt-3">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                                    <div class="d-flex align-items-center">
                                        <div class="badge bg-label-danger p-2 rounded-3 me-3">
                                            <i class="bx bxs-file-pdf fs-2 text-danger"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold text-dark text-break" id="previewFileName">filename.pdf</h6>
                                            <div class="d-flex align-items-center gap-2 mt-1">
                                                <small class="text-muted" id="previewFileSize">0 KB</small>
                                                <span class="badge bg-label-success" style="font-size: 0.7rem;"><i class="bx bx-check-circle me-1"></i>ตรวจสอบไฟล์เรียบร้อย</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeSelectedFile(event)">
                                            <i class="bx bx-trash me-1"></i> ลบ / เลือกไฟล์ใหม่
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Error Message -->
                            <div id="pdfFileError" class="text-danger small mt-2" style="display: none;">
                                <i class="bx bx-error-circle me-1"></i> <span id="pdfFileErrorText">กรุณาแนบไฟล์เอกสารคำสั่ง / หนังสือเชิญ (PDF) ก่อนบันทึกข้อมูล</span>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4" />

                    <!-- หมวด 2: การตรวจสอบเอกสารที่ส่งมาด้วย (Checklist สำหรับเจ้าหน้าที่และผู้ยื่นคำขอ) -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold text-primary mb-0">
                                <i class="bx bx-check-shield me-1"></i> 2. การตรวจสอบเอกสารที่ส่งมาด้วย (Checklist เอกสารแนบพร้อมแฟ้ม)
                            </h6>
                            <span class="badge bg-label-warning text-dark fs-7"><i class="bx bx-error-circle me-1"></i> ขั้นตอนที่ 1: ตรวจสอบเอกสารก่อนส่งแฟ้ม</span>
                        </div>
                        <div class="alert alert-warning py-2 px-3 small mb-3 d-flex align-items-center border-0 shadow-none" style="background:#fff8e6; color:#8a6100; border-left: 4px solid #ffab00 !important;">
                            <i class="bx bx-info-circle fs-4 me-2"></i>
                            <div>
                                <strong>คำแนะนำ:</strong> ในขั้นตอนนี้ยังไม่ต้องแนบไฟล์เอกสาร ให้ทำเครื่องหมายเลือกรายการเอกสารฉบับจริงที่มี (Checklist) และนำแฟ้มตัวจริงส่งให้เจ้าหน้าที่ธุรการ เพื่อตรวจสอบและแนบไฟล์ในขั้นตอนที่ 2 (โรงเรียนจัดทำเรื่อง)
                            </div>
                        </div>
                        <p class="text-muted small mb-3">กรุณากดเลือกปุ่มเอกสารที่ส่งมาด้วย (ระบบแสดงเป็นปุ่มสีน้ำเงินเมื่อเลือก) เพื่อให้เจ้าหน้าที่และผู้บริหารตรวจสอบความครบถ้วน <span class="badge bg-label-secondary" style="font-size:0.68rem;">เลือกเฉพาะรายการที่มีจริง</span></p>

                        <div class="row g-3">
                            <!-- 1. หนังสือเชิญ / ต้นเรื่อง -->
                            <div class="col-sm-6 col-lg-4">
                                <input type="checkbox" class="btn-check doc-btn-check" id="chkDoc1" name="attached_docs[]" value="หนังสือเชิญ / ต้นเรื่อง" autocomplete="off" />
                                <label class="btn btn-outline-primary w-100 p-3 text-start d-flex align-items-center justify-content-between h-100" for="chkDoc1">
                                    <div class="d-flex align-items-center">
                                        <i class="bx bx-envelope fs-3 me-2"></i>
                                        <span class="fw-bold fs-6">หนังสือเชิญ / ต้นเรื่อง</span>
                                    </div>
                                    <i class="bx bx-check-circle fs-4 check-indicator"></i>
                                </label>
                            </div>

                            <!-- 2. กำหนดการ -->
                            <div class="col-sm-6 col-lg-4">
                                <input type="checkbox" class="btn-check doc-btn-check" id="chkDoc2" name="attached_docs[]" value="กำหนดการ" autocomplete="off" />
                                <label class="btn btn-outline-primary w-100 p-3 text-start d-flex align-items-center justify-content-between h-100" for="chkDoc2">
                                    <div class="d-flex align-items-center">
                                        <i class="bx bx-calendar fs-3 me-2"></i>
                                        <span class="fw-bold fs-6">กำหนดการ</span>
                                    </div>
                                    <i class="bx bx-check-circle fs-4 check-indicator"></i>
                                </label>
                            </div>

                            <!-- 3. คำสั่ง -->
                            <div class="col-sm-6 col-lg-4">
                                <input type="checkbox" class="btn-check doc-btn-check" id="chkDoc3" name="attached_docs[]" value="คำสั่ง" autocomplete="off" />
                                <label class="btn btn-outline-primary w-100 p-3 text-start d-flex align-items-center justify-content-between h-100" for="chkDoc3">
                                    <div class="d-flex align-items-center">
                                        <i class="bx bx-file fs-3 me-2"></i>
                                        <span class="fw-bold fs-6">คำสั่ง</span>
                                    </div>
                                    <i class="bx bx-check-circle fs-4 check-indicator"></i>
                                </label>
                            </div>

                            <!-- 4. หนังสือขออนุญาตจากผู้ปกครอง -->
                            <div class="col-sm-6 col-lg-4">
                                <input type="checkbox" class="btn-check doc-btn-check" id="chkDoc4" name="attached_docs[]" value="หนังสือขออนุญาตจากผู้ปกครอง" autocomplete="off" />
                                <label class="btn btn-outline-primary w-100 p-3 text-start d-flex align-items-center justify-content-between h-100" for="chkDoc4">
                                    <div class="d-flex align-items-center">
                                        <i class="bx bx-user-check fs-3 me-2"></i>
                                        <span class="fw-bold fs-6">หนังสือขออนุญาตจากผู้ปกครอง</span>
                                    </div>
                                    <i class="bx bx-check-circle fs-4 check-indicator"></i>
                                </label>
                            </div>

                            <!-- 5. บันทึกขอแลกคาบ -->
                            <div class="col-sm-6 col-lg-4">
                                <input type="checkbox" class="btn-check doc-btn-check" id="chkDoc5" name="attached_docs[]" value="บันทึกขอแลกคาบ" autocomplete="off" />
                                <label class="btn btn-outline-primary w-100 p-3 text-start d-flex align-items-center justify-content-between h-100" for="chkDoc5">
                                    <div class="d-flex align-items-center">
                                        <i class="bx bx-transfer-alt fs-3 me-2"></i>
                                        <span class="fw-bold fs-6">บันทึกขอแลกคาบ</span>
                                    </div>
                                    <i class="bx bx-check-circle fs-4 check-indicator"></i>
                                </label>
                            </div>

                            <!-- 6. โครงการ -->
                            <div class="col-sm-6 col-lg-4">
                                <input type="checkbox" class="btn-check doc-btn-check" id="chkDoc6" name="attached_docs[]" value="โครงการ" autocomplete="off" />
                                <label class="btn btn-outline-primary w-100 p-3 text-start d-flex align-items-center justify-content-between h-100" for="chkDoc6">
                                    <div class="d-flex align-items-center">
                                        <i class="bx bx-spreadsheet fs-3 me-2"></i>
                                        <span class="fw-bold fs-6">โครงการ</span>
                                    </div>
                                    <i class="bx bx-check-circle fs-4 check-indicator"></i>
                                </label>
                            </div>

                            <!-- 7. ระเบียบ -->
                            <div class="col-sm-6 col-lg-4">
                                <input type="checkbox" class="btn-check doc-btn-check" id="chkDoc7" name="attached_docs[]" value="ระเบียบ" autocomplete="off" />
                                <label class="btn btn-outline-primary w-100 p-3 text-start d-flex align-items-center justify-content-between h-100" for="chkDoc7">
                                    <div class="d-flex align-items-center">
                                        <i class="bx bx-book-bookmark fs-3 me-2"></i>
                                        <span class="fw-bold fs-6">ระเบียบ</span>
                                    </div>
                                    <i class="bx bx-check-circle fs-4 check-indicator"></i>
                                </label>
                            </div>

                            <!-- 8. ประมาณการโครงการ -->
                            <div class="col-sm-6 col-lg-4">
                                <input type="checkbox" class="btn-check doc-btn-check" id="chkDoc8" name="attached_docs[]" value="ประมาณการโครงการ" autocomplete="off" />
                                <label class="btn btn-outline-primary w-100 p-3 text-start d-flex align-items-center justify-content-between h-100" for="chkDoc8">
                                    <div class="d-flex align-items-center">
                                        <i class="bx bx-calculator fs-3 me-2"></i>
                                        <span class="fw-bold fs-6">ประมาณการโครงการ</span>
                                    </div>
                                    <i class="bx bx-check-circle fs-4 check-indicator"></i>
                                </label>
                            </div>

                            <!-- 9. หนังสือขอใช้รถ -->
                            <div class="col-sm-6 col-lg-4">
                                <input type="checkbox" class="btn-check doc-btn-check" id="chkDoc9" name="attached_docs[]" value="หนังสือขอใช้รถ" autocomplete="off" />
                                <label class="btn btn-outline-primary w-100 p-3 text-start d-flex align-items-center justify-content-between h-100" for="chkDoc9">
                                    <div class="d-flex align-items-center">
                                        <i class="bx bx-car fs-3 me-2"></i>
                                        <span class="fw-bold fs-6">หนังสือขอใช้รถ</span>
                                    </div>
                                    <i class="bx bx-check-circle fs-4 check-indicator"></i>
                                </label>
                            </div>

                            <!-- 10. อื่น ๆ -->
                            <div class="col-12">
                                <div class="card border p-3 bg-light">
                                    <div class="row align-items-center g-2">
                                        <div class="col-auto">
                                            <input type="checkbox" class="btn-check doc-btn-check" id="chkDoc10" name="attached_docs[]" value="อื่น ๆ" autocomplete="off" onchange="toggleOtherDocInput(this)" />
                                            <label class="btn btn-outline-secondary px-4 py-3 text-start d-flex align-items-center" for="chkDoc10">
                                                <i class="bx bx-dots-horizontal-rounded fs-3 me-2"></i>
                                                <span class="fw-bold fs-6 me-3">อื่น ๆ</span>
                                                <i class="bx bx-check-circle fs-4 check-indicator"></i>
                                            </label>
                                        </div>
                                        <div class="col-md-7 col-sm-12" id="otherDocContainer" style="display: none;">
                                            <div class="input-group">
                                                <span class="input-group-text bg-white"><i class="bx bx-edit me-1"></i> ระบุเอกสารอื่น ๆ:</span>
                                                <input type="text" class="form-control" name="other_doc_detail" id="otherDocDetail" placeholder="พิมพ์ระบุชื่อเอกสารแนบเพิ่มเติม เช่น ใบสูจิบัตร, กำหนดการเดินทาง..." />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4" />

                    <!-- ข้อแนะนำการส่งเอกสารและกระบวนการติดตาม (ขั้นตอน 6 ลำดับของ อบจ.) -->
                    <div class="card border-primary bg-label-primary p-4 mb-4 shadow-sm">
                        <div class="d-flex align-items-start gap-3">
                            <div class="badge bg-primary p-2 rounded-3 mt-1">
                                <i class="bx bx-trip fs-2 text-white"></i>
                            </div>
                            <div class="w-100">
                                <div class="d-flex flex-wrap justify-content-between align-items-center mb-1">
                                    <h6 class="fw-bold text-primary mb-0 fs-6">
                                        <i class="bx bx-git-commit me-1"></i> ขั้นตอนการเสนอเอกสาร & การติดตามความคืบหน้า (6 ขั้นตอน)
                                    </h6>
                                    <span class="badge bg-primary text-white"><i class="bx bx-right-arrow-alt me-1"></i> เสนอขออนุมัติ: โรงเรียน ➔ นายก อบจ.</span>
                                </div>
                                <p class="mb-3 text-dark small">
                                    เมื่อกดบันทึกคำขอแล้ว (ขั้นตอนที่ 1) ผู้ขอไปราชการกรุณารวบรวม <strong>เอกสารฉบับจริงพร้อมเอกสารแนบตาม Checklist</strong> นำส่งให้ <strong>เจ้าหน้าที่ธุรการโรงเรียน</strong> เพื่อดำเนินการใน <strong>ขั้นตอนที่ 2 (โรงเรียนจัดทำเรื่อง / ตรวจสอบความถูกต้อง / แนบไฟล์คำสั่ง)</strong> ก่อนส่งต่อตามลำดับของ อบจ.
                                </p>

                                <!-- เส้นทาง 6 ขั้นตอน -->
                                <div class="bg-white p-3 rounded-3 border">
                                    <div class="small fw-bold text-muted mb-2 text-uppercase" style="font-size: 0.73rem; letter-spacing: 0.5px;">
                                        <i class="bx bx-map-pin me-1 text-primary"></i> ลำดับขั้นตอนการดำเนินงานแฟ้มเอกสารไปราชการ:
                                    </div>
                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                        <span class="badge bg-label-primary border border-primary border-opacity-25 py-2 px-3 fs-7">
                                            <i class="bx bx-file me-1 fs-6 align-middle"></i> 1. บันทึกยื่นขออนุมัติ
                                        </span>
                                        <i class="bx bx-chevron-right text-primary fs-5"></i>
                                        <span class="badge bg-white text-dark border py-2 px-3 fs-7">
                                            <i class="bx bx-building me-1 fs-6 align-middle text-primary"></i> 2. โรงเรียน (จัดทำเรื่อง / แนบไฟล์)
                                        </span>
                                        <i class="bx bx-chevron-right text-muted fs-5"></i>
                                        <span class="badge bg-white text-dark border py-2 px-3 fs-7">
                                            <i class="bx bx-book-reader me-1 fs-6 align-middle text-info"></i> 3. กองการศึกษา
                                        </span>
                                        <i class="bx bx-chevron-right text-muted fs-5"></i>
                                        <span class="badge bg-white text-dark border py-2 px-3 fs-7">
                                            <i class="bx bx-id-card me-1 fs-6 align-middle text-secondary"></i> 4. กองการเจ้าหน้าที่
                                        </span>
                                        <i class="bx bx-chevron-right text-muted fs-5"></i>
                                        <span class="badge bg-white text-dark border py-2 px-3 fs-7">
                                            <i class="bx bx-briefcase-alt-2 me-1 fs-6 align-middle text-warning"></i> 5. สำนักปลัด
                                        </span>
                                        <i class="bx bx-chevron-right text-muted fs-5"></i>
                                        <span class="badge bg-white text-dark border py-2 px-3 fs-7">
                                            <i class="bx bx-crown me-1 fs-6 align-middle text-danger"></i> 6. นายก อบจ. (อนุมัติ)
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 mt-2 pt-2 border-top text-muted" style="font-size: 0.74rem;">
                                        <span class="badge bg-label-success px-2 py-1"><i class="bx bx-left-arrow-alt me-1"></i> ขากลับ</span>
                                        <span>เมื่อ <strong>นายก อบจ. อนุมัติแล้ว</strong> ระบบจะบันทึกการส่งเอกสารอนุมัติตัวจริงกลับตามสายงานจนถึงโรงเรียน</span>
                                    </div>
                                </div>

                                <!-- ผู้บันทึกข้อมูลยื่นเรื่องในขั้นตอนที่ 1 -->
                                <div class="mt-3 p-3 bg-white rounded-3 border">
                                    <label class="form-label fw-bold text-dark d-flex align-items-center mb-1">
                                        <i class="bx bx-user-check text-primary me-1 fs-5"></i> ผู้บันทึกข้อมูลและส่งเรื่อง (ขั้นตอนที่ 1)
                                    </label>
                                    <input type="text" class="form-control bg-light fw-semibold" value="<?= esc(session()->get('full_name') ?: (session()->get('first_name') . ' ' . session()->get('last_name'))) ?>" readonly />
                                    <small class="text-muted">ระบบจะบันทึกรหัสผู้ใช้งาน (ID: <?= esc(session()->get('user_id')) ?>) และชื่อผู้บันทึกนี้ลงในประวัติการดำเนินงานโดยอัตโนมัติ</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="<?= base_url('admin/travel') ?>" class="btn btn-outline-secondary">ยกเลิก</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bx bx-save me-1"></i> บันทึกข้อมูลและส่งมอบเอกสารให้ธุรการ
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- External Plugin Scripts -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>

<script>
let fpStart, fpEnd;

document.addEventListener('DOMContentLoaded', function() {
    // Initialize Thai Buddhist Datepickers
    fpStart = initThaiDatePicker('#startDate', {
        onChange: function(selectedDates, dateStr) {
            if (fpEnd && selectedDates[0]) {
                fpEnd.set('minDate', selectedDates[0]);
            }
            calcDays();
        }
    });

    fpEnd = initThaiDatePicker('#endDate', {
        onChange: function(selectedDates, dateStr) {
            calcDays();
        }
    });

    calcDays();

    setTimeout(function() {
        document.querySelectorAll('.flatpickr-thai-input').forEach(function(el) {
            if (el.value && el.value.indexOf('B') !== -1) {
                el.value = el.value.replace(/\bB\b/g, '<?= date('Y') + 543 ?>').replace(/\s+B$/, ' <?= date('Y') + 543 ?>');
            }
        });
    }, 50);
});

function calcDays() {
    const startInput = document.getElementById('startDate');
    const endInput = document.getElementById('endDate');
    if (startInput && endInput && startInput.value && endInput.value) {
        const start = new Date(startInput.value);
        const end = new Date(endInput.value);
        if (!isNaN(start) && !isNaN(end) && end >= start) {
            const diffTime = Math.abs(end - start);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
            document.getElementById('totalDaysDisp').value = diffDays;
            return;
        }
    }
    document.getElementById('totalDaysDisp').value = 0;
}

function handleApplicantChange(sel) {
    const opt = sel.options[sel.selectedIndex];
    const pos = opt.getAttribute('data-position') || '-';
    const dept = opt.getAttribute('data-department') || '-';
    const dispPos = document.getElementById('dispApplicantPosition');
    const dispDept = document.getElementById('dispApplicantDepartment');
    if (dispPos) dispPos.textContent = pos;
    if (dispDept) dispDept.textContent = dept;
}

function toggleOtherDocInput(chk) {
    const container = document.getElementById('otherDocContainer');
    const input = document.getElementById('otherDocDetail');
    if (chk.checked) {
        container.style.display = 'block';
        input.focus();
    } else {
        container.style.display = 'none';
        input.value = '';
    }
}

// Dropzone Drag & Drop Logic
const dropzoneEl   = document.getElementById('pdfDropzone');
const fileInputEl  = document.getElementById('realDocFileInput');
const previewCard  = document.getElementById('pdfFilePreview');
const previewName  = document.getElementById('previewFileName');
const previewSize  = document.getElementById('previewFileSize');
const errorMsgEl   = document.getElementById('pdfFileError');
const errorTextEl  = document.getElementById('pdfFileErrorText');

function triggerBrowseFile() {
    if (fileInputEl) fileInputEl.click();
}

function formatBytes(bytes, decimals = 2) {
    if (!+bytes) return '0 Bytes';
    const k = 1024;
    const dm = decimals < 0 ? 0 : decimals;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return `${parseFloat((bytes / Math.pow(k, i)).toFixed(dm))} ${sizes[i]}`;
}

function handleSelectedPdf(file) {
    if (!file) return;

    // Check PDF extension or mime
    const isPdf = file.name.toLowerCase().endsWith('.pdf') || file.type === 'application/pdf';
    if (!isPdf) {
        showDropzoneError('กรุณาเลือกเฉพาะไฟล์เอกสารรูปแบบ .PDF เท่านั้น');
        fileInputEl.value = '';
        return;
    }

    // Check size (25MB limit)
    if (file.size > 25 * 1024 * 1024) {
        showDropzoneError('ขนาดไฟล์เกินกำหนด (ต้องไม่เกิน 25 MB)');
        fileInputEl.value = '';
        return;
    }

    hideDropzoneError();

    // Update real file input if dropped
    try {
        const dt = new DataTransfer();
        dt.items.add(file);
        fileInputEl.files = dt.files;
    } catch (e) {
        // Fallback for older browsers
    }

    // Update preview card
    if (previewName) previewName.textContent = file.name;
    if (previewSize) previewSize.textContent = formatBytes(file.size);
    if (dropzoneEl) dropzoneEl.style.display = 'none';
    if (previewCard) previewCard.style.display = 'block';
}

function removeSelectedFile(e) {
    if (e) e.stopPropagation();
    if (fileInputEl) fileInputEl.value = '';
    if (previewCard) previewCard.style.display = 'none';
    if (dropzoneEl) {
        dropzoneEl.style.display = 'block';
        dropzoneEl.classList.remove('is-invalid');
    }
    hideDropzoneError();
}

function showDropzoneError(msg) {
    if (errorTextEl) errorTextEl.textContent = msg;
    if (errorMsgEl) errorMsgEl.style.display = 'block';
    if (dropzoneEl) dropzoneEl.classList.add('is-invalid');
}

function hideDropzoneError() {
    if (errorMsgEl) errorMsgEl.style.display = 'none';
    if (dropzoneEl) dropzoneEl.classList.remove('is-invalid');
}

// File input change event
if (fileInputEl) {
    fileInputEl.addEventListener('change', function(e) {
        if (this.files && this.files[0]) {
            handleSelectedPdf(this.files[0]);
        }
    });
}

// Drag & Drop events on Dropzone
if (dropzoneEl) {
    ['dragenter', 'dragover'].forEach(eventName => {
        dropzoneEl.addEventListener(eventName, function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropzoneEl.classList.add('dragover');
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropzoneEl.addEventListener(eventName, function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropzoneEl.classList.remove('dragover');
        }, false);
    });

    dropzoneEl.addEventListener('drop', function(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        if (files && files.length > 0) {
            handleSelectedPdf(files[0]);
        }
    }, false);
}

// Form submit handler
const travelForm = document.querySelector('form[action*="travel/store"]');
if (travelForm) {
    travelForm.addEventListener('submit', function(e) {
        // PDF attachment is optional in Step 1 (Applicant Submit)
        // Officers will inspect real documents and attach files in Step 2
    });
}
// Initialize Select2 on Applicant dropdown inside document ready
$(document).ready(function() {
    if (typeof $.fn.select2 !== 'undefined' && $('#applicantUserSelect').length) {
        $('#applicantUserSelect').select2({
            theme: 'bootstrap-5',
            placeholder: '🔍 ค้นหาด้วยชื่อ-สกุล, ตำแหน่ง, กลุ่มสาระฯ หรืออีเมล @skj.ac.th...',
            allowClear: false,
            templateResult: formatApplicantOption,
            templateSelection: formatApplicantSelection
        });

        $('#applicantUserSelect').on('change', function() {
            const $selected   = $(this).find(':selected');
            const fullName   = $selected.data('fullname') || $selected.text();
            const position   = $selected.data('position') || '-';
            const department = $selected.data('department') || '-';
            const email      = $selected.data('email') || '';
            const initial    = $selected.data('initial') || fullName.charAt(0) || 'ค';
            const isSelf     = $selected.data('is-self') == 1;

            $('#dispApplicantName').text(fullName);
            $('#dispApplicantPosition').text(position);
            $('#dispApplicantDepartment').text(department);
            $('#dispApplicantAvatar').text(initial);

            if (email) {
                $('#dispApplicantEmail').text(email);
                $('#dispApplicantEmailContainer').removeClass('d-none');
            } else {
                $('#dispApplicantEmailContainer').addClass('d-none');
            }

            const previewCard = document.getElementById('applicantPreviewCard');
            if (isSelf) {
                $('#dispApplicantBadge').html('<i class="bx bx-user me-1"></i> ยื่นขอในนามตนเอง')
                    .removeClass('bg-success text-white').addClass('bg-label-primary');
                $('#dispApplicantAction').addClass('d-none');
                if (previewCard) previewCard.classList.remove('is-proxy');
            } else {
                $('#dispApplicantBadge').html('<i class="bx bx-check-shield me-1"></i> บันทึกแทนครู/บุคลากร')
                    .removeClass('bg-label-primary').addClass('bg-success text-white');
                $('#dispApplicantAction').removeClass('d-none');
                if (previewCard) previewCard.classList.add('is-proxy');
            }
        });
    }
});

function resetApplicantToSelf() {
    const selfVal = '<?= $user['id'] ?>';
    $('#applicantUserSelect').val(selfVal).trigger('change');
}

function formatApplicantOption(state) {
    if (!state.id) return state.text;
    const $el = $(state.element);
    const fullName   = $el.data('fullname') || state.text;
    const email      = $el.data('email') || '';
    const position   = $el.data('position') || '';
    const department = $el.data('department') || '';
    const initial    = $el.data('initial') || fullName.charAt(0) || 'ค';
    const isSelf     = $el.data('is-self') == 1;

    return $(`
        <div class="d-flex align-items-center py-1">
            <div class="avatar avatar-sm me-3 flex-shrink-0">
                <span class="avatar-initial rounded-circle ${isSelf ? 'bg-primary text-white' : 'bg-label-primary text-primary'} fw-bold">${initial}</span>
            </div>
            <div class="flex-grow-1" style="min-width: 0;">
                <div class="d-flex align-items-center justify-content-between gap-2">
                    <span class="fw-bold text-dark fs-6 text-truncate">${fullName}</span>
                    <span class="badge ${isSelf ? 'bg-primary text-white' : 'bg-label-info text-dark'} small flex-shrink-0" style="font-size: 0.72rem;">${position}</span>
                </div>
                <div class="d-flex align-items-center text-muted small mt-1 text-truncate">
                    ${email ? `<i class="bx bx-envelope me-1 text-primary"></i><span class="text-primary me-2 text-truncate">${email}</span>` : ''}
                    ${department ? `<i class="bx bx-buildings me-1 text-secondary"></i><span class="text-truncate">${department}</span>` : ''}
                </div>
            </div>
        </div>
    `);
}

function formatApplicantSelection(state) {
    if (!state.id) return state.text;
    const $el = $(state.element);
    const fullName   = $el.data('fullname');
    const position   = $el.data('position');
    const department = $el.data('department');
    const isSelf     = $el.data('is-self') == 1;

    if (fullName) {
        if (isSelf) {
            return `👤 ยื่นขอในนามตนเอง: ${fullName} (${position})`;
        }
        return `📝 บันทึกแทน: ${fullName} — ${position} (${department})`;
    }
    return state.text;
}

// -------------------------------------------------------------
// ระบบตรวจสอบเลขที่หนังสือ (doc_no) และชื่อเรื่อง (title) ซ้ำแบบ Real-time
// -------------------------------------------------------------
let isDocNoDuplicate = false;
let isTitleDuplicate = false;
let docNoTimer = null;
let titleTimer = null;

function checkDocNoDuplicate() {
    const input = document.getElementById('docNoInput');
    const feedback = document.getElementById('docNoFeedback');
    if (!input || !feedback) return;

    const val = input.value.trim();
    if (!val) {
        input.classList.remove('is-valid', 'is-invalid');
        feedback.style.display = 'none';
        isDocNoDuplicate = false;
        return;
    }

    feedback.style.display = 'block';
    feedback.innerHTML = '<span class="text-muted"><i class="bx bx-loader-alt bx-spin me-1"></i> กำลังตรวจสอบเลขที่หนังสือ...</span>';

    fetch(`<?= base_url('admin/travel/check-duplicate') ?>?doc_no=${encodeURIComponent(val)}`)
        .then(res => res.json())
        .then(data => {
            if (data.duplicate_doc_no) {
                isDocNoDuplicate = true;
                input.classList.add('is-invalid');
                input.classList.remove('is-valid');
                feedback.innerHTML = `<span class="text-danger fw-bold"><i class="bx bx-x-circle me-1"></i> ${data.doc_no_msg}</span>`;
            } else {
                isDocNoDuplicate = false;
                input.classList.remove('is-invalid');
                input.classList.add('is-valid');
                feedback.innerHTML = '<span class="text-success fw-semibold"><i class="bx bx-check-circle me-1"></i> สามารถใช้เลขที่หนังสือนี้ได้ (ไม่ซ้ำ)</span>';
            }
        })
        .catch(err => {
            console.error(err);
            feedback.style.display = 'none';
        });
}

function checkTitleDuplicate() {
    const input = document.getElementById('titleInput');
    const feedback = document.getElementById('titleFeedback');
    if (!input || !feedback) return;

    const val = input.value.trim();
    if (!val) {
        input.classList.remove('is-valid', 'is-invalid');
        feedback.style.display = 'none';
        isTitleDuplicate = false;
        return;
    }

    feedback.style.display = 'block';
    feedback.innerHTML = '<span class="text-muted"><i class="bx bx-loader-alt bx-spin me-1"></i> กำลังตรวจสอบชื่อเรื่อง...</span>';

    fetch(`<?= base_url('admin/travel/check-duplicate') ?>?title=${encodeURIComponent(val)}`)
        .then(res => res.json())
        .then(data => {
            if (data.duplicate_title) {
                isTitleDuplicate = true;
                input.classList.add('is-invalid');
                input.classList.remove('is-valid');
                feedback.innerHTML = `<span class="text-danger fw-bold"><i class="bx bx-x-circle me-1"></i> ${data.title_msg}</span>`;
            } else {
                isTitleDuplicate = false;
                input.classList.remove('is-invalid');
                input.classList.add('is-valid');
                feedback.innerHTML = '<span class="text-success fw-semibold"><i class="bx bx-check-circle me-1"></i> ชื่อเรื่องนี้สามารถใช้งานได้ (ไม่ซ้ำ)</span>';
            }
        })
        .catch(err => {
            console.error(err);
            feedback.style.display = 'none';
        });
}

function fetchNextDocNo() {
    const input = document.getElementById('docNoInput');
    const feedback = document.getElementById('docNoFeedback');
    if (!input) return;

    if (feedback) {
        feedback.style.display = 'block';
        feedback.innerHTML = '<span class="text-primary"><i class="bx bx-loader-alt bx-spin me-1"></i> กำลังสแกนหาเลขรันลำดับล่าสุด...</span>';
    }

    fetch(`<?= base_url('admin/travel/check-duplicate') ?>?doc_no=refresh_next`)
        .then(res => res.json())
        .then(data => {
            if (data.next_doc_no) {
                input.value = data.next_doc_no;
                input.classList.remove('is-invalid');
                input.classList.add('is-valid');
                if (feedback) {
                    feedback.style.display = 'block';
                    feedback.innerHTML = `<span class="text-success fw-bold"><i class="bx bx-check-double me-1"></i> อัปเดตเป็นเลขลำดับล่าสุดเรียบร้อยแล้ว: ${data.next_doc_no}</span>`;
                }
                isDocNoDuplicate = false;
            }
        })
        .catch(err => {
            console.error('Error fetching next doc no:', err);
            if (feedback) {
                feedback.innerHTML = '<span class="text-danger small">ไม่สามารถดึงเลขลำดับล่าสุดได้ กรุณาลองใหม่อีกครั้ง</span>';
            }
        });
}

// ผูก Event Listener กับ input
document.addEventListener('DOMContentLoaded', function() {
    const docNoInput = document.getElementById('docNoInput');
    if (docNoInput) {
        docNoInput.addEventListener('input', function() {
            clearTimeout(docNoTimer);
            docNoTimer = setTimeout(checkDocNoDuplicate, 400);
        });
        docNoInput.addEventListener('blur', checkDocNoDuplicate);
        // ตรวจสอบเลขเริ่มต้นอัตโนมัติ
        setTimeout(checkDocNoDuplicate, 300);
    }

    const titleInput = document.getElementById('titleInput');
    if (titleInput) {
        titleInput.addEventListener('input', function() {
            clearTimeout(titleTimer);
            titleTimer = setTimeout(checkTitleDuplicate, 400);
        });
        titleInput.addEventListener('blur', checkTitleDuplicate);
    }

    const travelForm = document.querySelector('form[action*="travel/store"]');
    if (travelForm) {
        travelForm.addEventListener('submit', function(e) {
            if (isDocNoDuplicate) {
                e.preventDefault();
                AppAlert.warning('เลขที่หนังสือขอไปราชการซ้ำกับข้อมูลที่มีอยู่ในระบบแล้ว กรุณาเปลี่ยนเลขที่ใหม่', 'เลขที่หนังสือซ้ำ');
                if (docNoInput) docNoInput.focus();
                return false;
            }
            if (isTitleDuplicate) {
                e.preventDefault();
                AppAlert.warning('เรื่องขอไปราชการนี้มีบันทึกในระบบแล้ว กรุณาตรวจสอบเพื่อป้องกันการยื่นเรื่องซ้ำซ้อน', 'เรื่องขอไปราชการซ้ำ');
                if (titleInput) titleInput.focus();
                return false;
            }
        });
    }
});
</script>
<?= $this->endSection() ?>
