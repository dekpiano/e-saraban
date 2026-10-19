<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
แต่งตั้งสิทธิ์เจ้าหน้าที่ใหม่
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<!-- Select2 CSS & Select2 Bootstrap 5 Theme -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

<style>
    /* Custom Styling for Select2 and Role Cards */
    .select2-container--bootstrap-5 .select2-selection {
        border-radius: 0.65rem !important;
        border: 1.5px solid #d9dee3 !important;
        min-height: 48px !important;
        padding: 0.5rem 0.75rem !important;
        font-family: 'K2D', sans-serif !important;
        transition: all 0.2s ease-in-out !important;
    }
    .select2-container--bootstrap-5.select2-container--focus .select2-selection,
    .select2-container--bootstrap-5.select2-container--open .select2-selection {
        border-color: #696cff !important;
        box-shadow: 0 0 0 0.25rem rgba(105, 108, 255, 0.15) !important;
    }
    .select2-container--bootstrap-5 .select2-dropdown {
        border-radius: 0.75rem !important;
        border: 1px solid #e7e7ff !important;
        box-shadow: 0 10px 30px rgba(67, 89, 113, 0.12) !important;
        overflow: hidden !important;
        font-family: 'K2D', sans-serif !important;
        z-index: 1055 !important;
    }
    .select2-container--bootstrap-5 .select2-results__option--highlighted {
        background-color: #f5f5f9 !important;
        color: #566a7f !important;
    }
    .select2-container--bootstrap-5 .select2-results__option--selected {
        background-color: #e7e7ff !important;
        color: #696cff !important;
    }

    /* Role Selection Interactive Cards */
    .role-card {
        cursor: pointer;
        border: 2px solid #e7e7e8;
        border-radius: 0.85rem;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        background: #ffffff;
        position: relative;
    }
    .role-card:hover {
        border-color: #696cff;
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(105, 108, 255, 0.12);
    }
    .role-card.selected {
        border-color: #696cff !important;
        background: linear-gradient(145deg, #ffffff 0%, #f7f7ff 100%) !important;
        box-shadow: 0 6px 20px rgba(105, 108, 255, 0.15) !important;
    }
    .role-card .role-check-indicator {
        position: absolute;
        top: 1rem;
        right: 1rem;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        border: 2px solid #d9dee3;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }
    .role-card.selected .role-check-indicator {
        border-color: #696cff;
        background-color: #696cff;
        color: #ffffff;
    }

    /* Selected Personnel Preview Card */
    .person-preview-card {
        border-radius: 0.85rem;
        background: linear-gradient(135deg, #f8faff 0%, #edf2ff 100%);
        border: 1.5px solid #dbeafe;
        transition: all 0.3s ease;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-xxl flex-grow-1 container-p-y">

    <!-- Breadcrumb & Page Header -->
    <div class="d-md-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>" class="text-secondary">แดชบอร์ด</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/users') ?>" class="text-secondary">จัดการเจ้าหน้าที่ระบบ</a></li>
                    <li class="breadcrumb-item active text-primary fw-semibold">แต่งตั้งสิทธิ์เจ้าหน้าที่ใหม่</li>
                </ol>
            </nav>
            <h4 class="fw-bold mb-1 text-dark" style="font-family: 'K2D', sans-serif;">
                <i class="bx bx-shield-plus text-primary me-2"></i>แต่งตั้งสิทธิ์เจ้าหน้าที่ระบบ (Staff Role)
            </h4>
            <p class="text-secondary mb-0 small">
                ค้นหาและเลือกบุคลากรจากอีเมลโรงเรียน <code>@skj.ac.th</code> เพื่อมอบสิทธิ์ผู้ดูแลระบบสูงสุด (Supper Admin) หรือเจ้าหน้าที่สารบรรณ (Admin)
            </p>
        </div>
        <div class="mt-3 mt-md-0">
            <a href="<?= base_url('admin/users') ?>" class="btn btn-outline-secondary btn-sm px-3 shadow-xs fw-semibold">
                <i class="bx bx-arrow-back me-1"></i> ย้อนกลับหน้ารายชื่อ
            </a>
        </div>
    </div>

    <!-- Alert / Validation Errors -->
    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="bx bx-error-circle fs-4 me-2"></i>
                <div>
                    <strong class="d-block mb-1">กรุณาตรวจสอบข้อมูล:</strong>
                    <ul class="mb-0 ps-3 small">
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Form Section -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 1rem;">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <span class="badge bg-label-primary p-2 rounded me-2">
                            <i class="bx bx-user-pin fs-4 text-primary"></i>
                        </span>
                        <div>
                            <h5 class="card-title mb-0 fw-bold text-dark" style="font-family: 'K2D', sans-serif;">
                                เลือกบุคลากรและกำหนดสิทธิ์
                            </h5>
                            <small class="text-muted">ระบบจะผูกสิทธิ์เข้ากับอีเมล Google Workspace ของโรงเรียนโดยอัตโนมัติ</small>
                        </div>
                    </div>
                    <span class="badge bg-label-info fw-bold">ขั้นตอนเดียว</span>
                </div>

                <div class="card-body p-4">
                    <form method="post" action="<?= base_url('admin/users/store') ?>" id="createUserForm">
                        <?= csrf_field() ?>
                        <input type="hidden" name="pers_id" id="pers_id" value="<?= old('pers_id') ?>">
                        <input type="hidden" name="email" id="inputEmail" value="<?= old('email') ?>">

                        <!-- 1. ค้นหาบุคลากรด้วย Select2 -->
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark fs-6 mb-2">
                                <i class="bx bx-search-alt text-primary me-1"></i> เลือกบุคลากรจากอีเมลโรงเรียน (@skj.ac.th) <span class="text-danger">*</span>
                            </label>
                            <div class="position-relative">
                                <select id="selectPersonnel" class="form-select select2-personnel" style="width: 100%;">
                                    <option value="">-- พิมพ์ค้นหาด้วยชื่อ-สกุล, ตำแหน่ง หรืออีเมล @skj.ac.th --</option>
                                    <?php if (!empty($personnelList)): ?>
                                        <?php foreach ($personnelList as $p): ?>
                                            <?php 
                                                $fullName = trim($p['pers_prefix'] . $p['pers_firstname'] . ' ' . $p['pers_lastname']);
                                                $email = $p['pers_username'];
                                                $posName = $p['posi_name'] ?: 'บุคลากร';
                                                $deptName = $p['lear_namethai'] ?: 'สำนักงาน';
                                                $isSelected = (old('email') === $email || old('pers_id') === $p['pers_id']);
                                            ?>
                                            <option value="<?= esc($email) ?>"
                                                <?= $isSelected ? 'selected' : '' ?>
                                                data-pers-id="<?= esc($p['pers_id']) ?>"
                                                data-fullname="<?= esc($fullName) ?>"
                                                data-email="<?= esc($email) ?>"
                                                data-position="<?= esc($posName) ?>"
                                                data-department="<?= esc($deptName) ?>"
                                                data-initial="<?= esc(mb_substr($p['pers_firstname'], 0, 1)) ?>">
                                                <?= esc($fullName) ?> (<?= esc($email) ?>) - <?= esc($posName) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <small class="text-muted d-block mt-2">
                                <i class="bx bx-info-circle me-1"></i> สามารถพิมพ์ค้นหาได้ทั้งชื่อภาษาไทย ตำแหน่ง กลุ่มสาระฯ หรืออีเมลโรงเรียน
                            </small>
                        </div>

                        <!-- 2. Selected Personnel Dynamic Card Preview -->
                        <div id="personPreviewCard" class="person-preview-card p-3 mb-4 <?= old('email') ? '' : 'd-none' ?>">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-md me-3 flex-shrink-0">
                                        <span id="previewAvatar" class="avatar-initial rounded-circle bg-primary text-white fw-bold fs-5">
                                            <?= mb_substr(old('email') ?: 'U', 0, 1) ?>
                                        </span>
                                    </div>
                                    <div>
                                        <div class="d-flex align-items-center gap-2">
                                            <h6 id="previewFullName" class="mb-0 fw-bold text-dark fs-6">
                                                <?= esc(old('email') ?: 'กำลังโหลดข้อมูล...') ?>
                                            </h6>
                                            <span class="badge bg-success py-1 px-2 text-white small" style="font-size: 0.72rem;">
                                                <i class="bx bx-check me-1"></i>พบบัญชีในฐานข้อมูล
                                            </span>
                                        </div>
                                        <div class="d-flex flex-wrap align-items-center gap-2 mt-1 text-secondary small">
                                            <span><i class="bx bx-envelope text-primary me-1"></i><code id="previewEmail" class="text-primary fw-bold"><?= esc(old('email')) ?></code></span>
                                            <span>•</span>
                                            <span id="previewPosition" class="badge bg-label-primary"><?= esc(old('position_name') ?: 'บุคลากร') ?></span>
                                            <span id="previewDepartment" class="badge bg-label-secondary"><?= esc(old('department_name') ?: 'สำนักงาน') ?></span>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearSelectedPersonnel()">
                                    <i class="bx bx-x me-1"></i>เปลี่ยนคน
                                </button>
                            </div>
                        </div>

                        <!-- 3. กำหนดสิทธิ์ในระบบ (Role Selection Cards) -->
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark fs-6 mb-2">
                                <i class="bx bx-shield text-primary me-1"></i> เลือกสิทธิ์การใช้งาน (Role) <span class="text-danger">*</span>
                            </label>

                            <div class="row g-3">
                                <!-- Option 1: Supper Admin -->
                                <div class="col-md-6">
                                    <div class="role-card p-3 h-100 <?= (old('role') === 'super_admin') ? 'selected' : '' ?>" id="roleCardSuperAdmin" onclick="selectRole('super_admin')">
                                        <div class="role-check-indicator">
                                            <i class="bx bx-check <?= (old('role') === 'super_admin') ? '' : 'd-none' ?>" id="checkIconSuperAdmin"></i>
                                        </div>
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="avatar avatar-sm me-2">
                                                <span class="avatar-initial rounded bg-label-warning text-warning">
                                                    <i class="bx bx-crown fs-4"></i>
                                                </span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-bold text-dark" style="font-family: 'K2D', sans-serif;">Supper Admin</h6>
                                                <span class="badge bg-warning text-dark py-0 px-2 fw-semibold" style="font-size: 0.7rem;">ผู้ดูแลระบบสูงสุด</span>
                                            </div>
                                        </div>
                                        <p class="text-secondary small mb-0 mt-2" style="font-size: 0.82rem; line-height: 1.4;">
                                            สิทธิ์สูงสุด: ควบคุมระบบทั้งหมด จัดการแต่งตั้ง/ระงับสิทธิ์เจ้าหน้าที่ และดูทะเบียนหนังสือทุกสายงาน
                                        </p>
                                    </div>
                                </div>

                                <!-- Option 2: Admin -->
                                <div class="col-md-6">
                                    <div class="role-card p-3 h-100 <?= (old('role', 'admin') === 'admin') ? 'selected' : '' ?>" id="roleCardAdmin" onclick="selectRole('admin')">
                                        <div class="role-check-indicator">
                                            <i class="bx bx-check <?= (old('role', 'admin') === 'admin') ? '' : 'd-none' ?>" id="checkIconAdmin"></i>
                                        </div>
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="avatar avatar-sm me-2">
                                                <span class="avatar-initial rounded bg-label-info text-info">
                                                    <i class="bx bx-folder-open fs-4"></i>
                                                </span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-bold text-dark" style="font-family: 'K2D', sans-serif;">Admin</h6>
                                                <span class="badge bg-info text-white py-0 px-2 fw-semibold" style="font-size: 0.7rem;">เจ้าหน้าที่สารบรรณ</span>
                                            </div>
                                        </div>
                                        <p class="text-secondary small mb-0 mt-2" style="font-size: 0.82rem; line-height: 1.4;">
                                            เจ้าหน้าที่สารบรรณ: ลงรับ-ส่งหนังสือ แนบไฟล์ต้นฉบับ PDF คุมเลขทะเบียนสารบรรณ และเดินแฟ้มเอกสาร
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <small class="text-muted d-block mt-2">
                                <i class="bx bx-info-circle me-1"></i> ครู บุคลากร และผู้บริหารท่านอื่น ๆ สามารถเข้าสู่ระบบด้วย Google และใช้งานทั่วไปได้ทันทีโดยไม่ต้องแต่งตั้งสิทธิ์ที่นี่
                            </small>

                            <!-- Hidden Radio inputs for form submission -->
                            <input type="radio" name="role" value="super_admin" id="radioRoleSuperAdmin" class="d-none" <?= (old('role') === 'super_admin') ? 'checked' : '' ?> required>
                            <input type="radio" name="role" value="admin" id="radioRoleAdmin" class="d-none" <?= (old('role', 'admin') === 'admin') ? 'checked' : '' ?> required>
                        </div>

                        <!-- 4. สถานะเปิดใช้งาน (Active Switch) -->
                        <div class="p-3 bg-light rounded-3 mb-4 border">
                            <div class="form-check form-switch mb-0 d-flex align-items-center justify-content-between ps-0">
                                <div>
                                    <label class="form-check-label fw-bold text-dark fs-6 d-block mb-1" for="isActiveCheck">
                                        <i class="bx bx-check-circle text-success me-1"></i> เปิดใช้งานสิทธิ์ทันที (Active Account)
                                    </label>
                                    <small class="text-muted d-block" style="font-size: 0.82rem;">
                                        เมื่อเปิดใช้งาน ผู้ใช้สามารถเข้าสู่ระบบด้วย Google Workspace โรงเรียนได้ทันที
                                    </small>
                                </div>
                                <input class="form-check-input ms-3" type="checkbox" name="is_active" id="isActiveCheck" value="1" <?= old('is_active', '1') ? 'checked' : '' ?> style="width: 2.8rem; height: 1.5rem; cursor: pointer;">
                            </div>
                        </div>

                        <!-- Submit & Cancel Buttons -->
                        <div class="d-flex align-items-center justify-content-end gap-2 pt-3 border-top">
                            <a href="<?= base_url('admin/users') ?>" class="btn btn-outline-secondary px-4 fw-bold">
                                ยกเลิก
                            </a>
                            <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm" id="btnSubmit">
                                <i class="bx bx-save me-1"></i> บันทึกแต่งตั้งสิทธิ์
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar Guidelines -->
        <div class="col-lg-4">
            <!-- Guidelines Card -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 1rem;">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="card-title mb-0 fw-bold text-dark" style="font-family: 'K2D', sans-serif;">
                        <i class="bx bx-info-circle text-primary me-2"></i>หลักการจัดการสิทธิ์
                    </h6>
                </div>
                <div class="card-body p-3">
                    <div class="d-flex align-items-start mb-3">
                        <span class="badge bg-label-warning p-2 rounded me-2 flex-shrink-0">
                            <i class="bx bx-crown fs-5"></i>
                        </span>
                        <div>
                            <strong class="text-dark d-block mb-1">Supper Admin</strong>
                            <p class="text-secondary small mb-0">
                                ผู้ดูแลระบบระดับสูงสุด สามารถกำหนดสิทธิ์เจ้าหน้าที่คนอื่น และบริหารจัดการระบบทั้งหมด
                            </p>
                        </div>
                    </div>

                    <hr class="my-2">

                    <div class="d-flex align-items-start mb-3">
                        <span class="badge bg-label-info p-2 rounded me-2 flex-shrink-0">
                            <i class="bx bx-folder-open fs-5"></i>
                        </span>
                        <div>
                            <strong class="text-dark d-block mb-1">Admin (เจ้าหน้าที่สารบรรณ)</strong>
                            <p class="text-secondary small mb-0">
                                เจ้าหน้าที่ผู้รับผิดชอบงานสารบรรณ คุมสมุดทะเบียนหนังสือ และช่วยอำนวยความสะดวกการเดินแฟ้ม
                            </p>
                        </div>
                    </div>

                    <div class="alert alert-primary p-3 rounded-3 mb-0 mt-3 border-0" style="background-color: #f0f2ff;">
                        <div class="d-flex align-items-start">
                            <i class="bx bx-bulb fs-4 text-primary me-2 flex-shrink-0"></i>
                            <div class="small text-dark">
                                <strong>หมายเหตุสำคัญ:</strong><br>
                                บุคลากรทั่วไป (ผู้อำนวยการ, รองผู้อำนวยการ, และครู) <strong>ไม่จำเป็นต้องเพิ่มที่หน้านี้</strong> ระบบจะดึงสิทธิ์และข้อมูลตามตำแหน่งจากฐานข้อมูลโรงเรียน (tb_personnel) ให้อัตโนมัติเมื่อ Login ด้วย Google ครับ
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Select2 JavaScript Library -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    // 1. Initialize Select2 with Bootstrap 5 theme and custom templates
    $('#selectPersonnel').select2({
        theme: 'bootstrap-5',
        placeholder: '-- พิมพ์ค้นหาด้วยชื่อ-สกุล, ตำแหน่ง หรืออีเมล @skj.ac.th --',
        allowClear: true,
        width: '100%',
        templateResult: formatPersonnelOption,
        templateSelection: formatPersonnelSelection
    });

    // Handle Select2 change event
    $('#selectPersonnel').on('change', function() {
        const selectedVal = $(this).val();
        if (!selectedVal) {
            clearSelectedPersonnel();
            return;
        }

        const selectedOption = $(this).find(':selected');
        const persId = selectedOption.data('pers-id') || '';
        const email = selectedOption.data('email') || selectedVal;
        const fullName = selectedOption.data('fullname') || '';
        const position = selectedOption.data('position') || 'บุคลากร';
        const department = selectedOption.data('department') || 'สำนักงาน';
        const initial = selectedOption.data('initial') || fullName.charAt(0) || 'U';

        // Auto-fill hidden inputs
        $('#pers_id').val(persId);
        $('#inputEmail').val(email);

        // Update preview card
        $('#previewFullName').text(fullName);
        $('#previewEmail').text(email);
        $('#previewPosition').text(position);
        $('#previewDepartment').text(department);
        $('#previewAvatar').text(initial);
        $('#personPreviewCard').removeClass('d-none');
    });

    // Form submit validation
    $('#createUserForm').on('submit', function(e) {
        const email = $('#inputEmail').val().trim();
        if (!email) {
            e.preventDefault();
            AppAlert.warning('กรุณาเลือกบุคลากรจากอีเมลโรงเรียน (@skj.ac.th)', 'ยังไม่ได้เลือกบุคลากร');
            $('#selectPersonnel').select2('open');
            return false;
        }
    });
});

// Format option layout inside dropdown
function formatPersonnelOption(state) {
    if (!state.id) {
        return state.text;
    }
    const $el = $(state.element);
    const fullName = $el.data('fullname') || state.text;
    const email = $el.data('email') || '';
    const position = $el.data('position') || '';
    const department = $el.data('department') || '';
    const initial = $el.data('initial') || fullName.charAt(0) || 'U';

    return $(`
        <div class="d-flex align-items-center py-1">
            <div class="avatar avatar-sm me-3 flex-shrink-0">
                <span class="avatar-initial rounded-circle bg-label-primary fw-bold">${initial}</span>
            </div>
            <div class="flex-grow-1" style="min-width: 0;">
                <div class="d-flex align-items-center justify-content-between gap-2">
                    <span class="fw-bold text-dark fs-6 text-truncate">${fullName}</span>
                    <span class="badge bg-label-info text-dark small flex-shrink-0" style="font-size: 0.7rem;">${position}</span>
                </div>
                <div class="d-flex align-items-center text-muted small mt-1 text-truncate">
                    <i class="bx bx-envelope me-1 text-primary"></i> <span class="text-primary me-3 text-truncate">${email}</span>
                    ${department ? `<i class="bx bx-buildings me-1 text-secondary"></i><span class="text-truncate">${department}</span>` : ''}
                </div>
            </div>
        </div>
    `);
}

// Format selected text in the closed box
function formatPersonnelSelection(state) {
    if (!state.id) {
        return state.text;
    }
    const $el = $(state.element);
    const fullName = $el.data('fullname');
    const email = $el.data('email') || state.id;
    if (fullName) {
        return `${fullName} (${email})`;
    }
    return state.text;
}

// Interactive Role Select
function selectRole(role) {
    $('.role-card').removeClass('selected');
    $('#checkIconSuperAdmin, #checkIconAdmin').addClass('d-none');

    if (role === 'super_admin') {
        $('#radioRoleSuperAdmin').prop('checked', true);
        $('#roleCardSuperAdmin').addClass('selected');
        $('#checkIconSuperAdmin').removeClass('d-none');
    } else {
        $('#radioRoleAdmin').prop('checked', true);
        $('#roleCardAdmin').addClass('selected');
        $('#checkIconAdmin').removeClass('d-none');
    }
}

// Clear selected personnel
function clearSelectedPersonnel() {
    $('#selectPersonnel').val(null).trigger('change');
    $('#pers_id').val('');
    $('#inputEmail').val('');
    $('#personPreviewCard').addClass('d-none');
}
</script>
<?= $this->endSection() ?>
