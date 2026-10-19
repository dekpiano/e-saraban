<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
แก้ไขสิทธิ์เจ้าหน้าที่: <?= esc($user['first_name'] ? ($user['prefix'] . $user['first_name'] . ' ' . $user['last_name']) : ($user['email'] ?? $user['username'])) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Google Fonts & Custom CSS -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=K2D:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&family=Sarabun:wght@300;400;500;600&display=swap" rel="stylesheet">
<!-- Select2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

<style>
    body, h1, h2, h3, h4, h5, h6, .form-label, .btn, .nav-link, .badge {
        font-family: 'K2D', sans-serif;
    }
    .text-sarabun {
        font-family: 'Sarabun', sans-serif;
    }

    /* Role Cards */
    .role-card {
        border: 2px solid #e7e7e8;
        border-radius: 0.85rem;
        cursor: pointer;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        background-color: #ffffff;
    }
    .role-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(105, 108, 255, 0.12);
        border-color: #696cff;
    }
    .role-card.selected {
        border-color: #696cff !important;
        background-color: #f5f6ff;
        box-shadow: 0 0.5rem 1.25rem rgba(105, 108, 255, 0.18);
    }
    .role-card.selected.role-admin {
        border-color: #03c3ec !important;
        background-color: #f1fbfe;
        box-shadow: 0 0.5rem 1.25rem rgba(3, 195, 236, 0.18);
    }
    .role-card.selected.role-general {
        border-color: #8592a3 !important;
        background-color: #f8f9fa;
        box-shadow: 0 0.5rem 1.25rem rgba(133, 146, 163, 0.18);
    }

    .role-check-indicator {
        position: absolute;
        top: 0.75rem;
        right: 0.75rem;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background-color: #696cff;
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        transition: all 0.2s ease;
    }
    .role-card.role-admin .role-check-indicator {
        background-color: #03c3ec;
    }
    .role-card.role-general .role-check-indicator {
        background-color: #8592a3;
    }
    .role-card:not(.selected) .role-check-indicator {
        background-color: #e7e7e8;
        color: transparent;
    }

    /* Personnel Preview Card */
    .person-preview-card {
        background: linear-gradient(135deg, #f8f9fa 0%, #eef1f6 100%);
        border: 1px solid #d9dee3;
        border-radius: 0.85rem;
        transition: all 0.2s ease;
    }

    /* Profile Widget Hero */
    .profile-hero {
        background: linear-gradient(135deg, #696cff 0%, #4f51e6 100%);
        height: 85px;
        border-radius: 0.75rem 0.75rem 0 0;
        position: relative;
    }
    .profile-hero.admin-hero {
        background: linear-gradient(135deg, #03c3ec 0%, #0294b3 100%);
    }
    .profile-hero.general-hero {
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
    }

    .profile-avatar-wrapper {
        position: relative;
        margin-top: -45px;
        display: inline-block;
    }
    .profile-avatar {
        width: 86px;
        height: 86px;
        border-radius: 50%;
        border: 4px solid #ffffff;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        background-color: #ffffff;
        object-fit: cover;
    }
    .profile-status-indicator {
        position: absolute;
        bottom: 4px;
        right: 4px;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        border: 2.5px solid #ffffff;
    }

    /* Select2 customization */
    .select2-container--bootstrap-5 .select2-selection {
        border-radius: 0.5rem;
        min-height: 44px;
        display: flex;
        align-items: center;
        border-color: #d9dee3;
        font-family: 'K2D', sans-serif;
    }
    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
        padding-left: 0.75rem;
        font-size: 0.95rem;
        color: #2b3445;
    }
    .select2-dropdown {
        border-radius: 0.65rem;
        box-shadow: 0 0.5rem 1.25rem rgba(0, 0, 0, 0.1);
        border-color: #d9dee3;
    }
</style>

<div class="container-xxl flex-grow-1 container-p-y">

    <!-- Breadcrumb & Header Section -->
    <div class="d-md-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-1">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>" class="text-secondary"><i class="bx bx-home-alt"></i> แดชบอร์ด</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/users') ?>" class="text-secondary">จัดการเจ้าหน้าที่ระบบ</a></li>
                    <li class="breadcrumb-item active text-primary fw-semibold">แก้ไขสิทธิ์: #<?= esc($user['id']) ?></li>
                </ol>
            </nav>
            <h4 class="fw-bold mb-0 text-dark">
                <i class="bx bx-user-pin text-primary me-2"></i>แก้ไขสิทธิ์และข้อมูลเจ้าหน้าที่
                <?php if ((int)$user['id'] === (int)session()->get('user_id')): ?>
                    <span class="badge bg-label-primary text-primary ms-2 fs-7 align-middle">
                        <i class="bx bx-user-check me-1"></i>บัญชีของคุณเอง
                    </span>
                <?php endif; ?>
            </h4>
        </div>
        <div class="mt-3 mt-md-0 d-flex gap-2">
            <a href="<?= base_url('admin/roles') ?>" class="btn btn-outline-info btn-sm fw-semibold">
                <i class="bx bx-table me-1"></i> ตารางสิทธิ์ (Matrix)
            </a>
            <a href="<?= base_url('admin/users') ?>" class="btn btn-outline-secondary btn-sm fw-semibold">
                <i class="bx bx-arrow-back me-1"></i> ย้อนกลับหน้ารายชื่อ
            </a>
        </div>
    </div>

    <!-- Alert / Validation Feedback -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center shadow-sm" role="alert">
            <i class="bx bx-check-circle fs-4 me-2"></i>
            <div><?= esc(session()->getFlashdata('success')) ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center shadow-sm" role="alert">
            <i class="bx bx-error fs-4 me-2"></i>
            <div><?= esc(session()->getFlashdata('error')) ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <div class="fw-bold mb-1"><i class="bx bx-error-circle me-1"></i> พบข้อผิดพลาดในการบันทึกข้อมูล:</div>
            <ul class="mb-0 ps-3">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Left Column: Edit Form -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius: 0.85rem;">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="card-title mb-0 fw-bold text-dark">
                            <i class="bx bx-edit text-primary me-2"></i>แบบฟอร์มกำหนดสิทธิ์การใช้งาน
                        </h5>
                        <small class="text-secondary">ปรับปรุงบทบาทและสถานะการใช้งานสำหรับบัญชีโรงเรียน</small>
                    </div>
                    <span class="badge bg-label-secondary text-dark fw-bold">ID: #<?= esc($user['id']) ?></span>
                </div>

                <div class="card-body p-4">
                    <form method="post" action="<?= base_url('admin/users/' . $user['id'] . '/update') ?>" id="editUserForm">
                        <?= csrf_field() ?>

                        <!-- 1. ค้นหา/เชื่อมโยงบุคลากรโรงเรียน -->
                        <div class="mb-4">
                            <label for="selectPersonnel" class="form-label fw-bold text-dark fs-6 d-flex align-items-center justify-content-between">
                                <span><i class="bx bx-user-check text-primary me-1"></i> เชื่อมโยงข้อมูลบุคลากรโรงเรียน</span>
                                <span class="badge bg-label-info text-info small fw-normal">ฐานข้อมูล สกจ.</span>
                            </label>

                            <select class="form-select select2-personnel" id="selectPersonnel" name="pers_select" style="width: 100%;">
                                <option value="">-- ค้นหาหรือเลือกบุคลากรเพื่อเปลี่ยนการเชื่อมโยง --</option>
                                <?php if (!empty($personnelList)): ?>
                                    <?php foreach ($personnelList as $p): ?>
                                        <?php 
                                            $fullName = trim($p['pers_prefix'] . $p['pers_firstname'] . ' ' . $p['pers_lastname']);
                                            $isSelected = ($user['pers_id'] === $p['pers_id'] || $user['email'] === $p['pers_username']);
                                        ?>
                                        <option value="<?= esc($p['pers_id']) ?>" 
                                            data-fullname="<?= esc($fullName) ?>"
                                            data-email="<?= esc($p['pers_username']) ?>"
                                            data-phone="<?= esc($p['pers_phone']) ?>"
                                            data-position="<?= esc($p['posi_name'] ?: 'บุคลากร') ?>"
                                            data-department="<?= esc($p['lear_namethai'] ?: 'สำนักงาน') ?>"
                                            data-initial="<?= esc(mb_substr($p['pers_firstname'], 0, 1)) ?>"
                                            <?= $isSelected ? 'selected' : '' ?>>
                                            <?= esc($fullName) ?> (<?= esc($p['pers_username']) ?>) - <?= esc($p['posi_name'] ?: 'บุคลากร') ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <input type="hidden" name="pers_id" id="inputPersId" value="<?= esc(old('pers_id', $user['pers_id'])) ?>">
                            <small class="text-muted d-block mt-2">
                                <i class="bx bx-info-circle me-1"></i> สามารถพิมพ์ค้นหาได้ทั้งชื่อภาษาไทย ตำแหน่ง กลุ่มสาระฯ หรืออีเมลโรงเรียน
                            </small>
                        </div>

                        <!-- Highlight Selected Personnel Card -->
                        <div id="personPreviewCard" class="person-preview-card p-3 mb-4">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-md me-3 flex-shrink-0">
                                        <span id="previewAvatar" class="avatar-initial rounded-circle bg-primary text-white fw-bold fs-5">
                                            <?= esc(mb_substr($user['first_name'] ?: ($user['email'] ?? 'U'), 0, 1)) ?>
                                        </span>
                                    </div>
                                    <div>
                                        <div class="d-flex align-items-center gap-2">
                                            <h6 id="previewFullName" class="mb-0 fw-bold text-dark fs-6">
                                                <?= esc($user['first_name'] ? ($user['prefix'] . $user['first_name'] . ' ' . $user['last_name']) : $user['email']) ?>
                                            </h6>
                                            <span class="badge bg-success py-1 px-2 text-white small" style="font-size: 0.72rem;">
                                                <i class="bx bx-check me-1"></i>เชื่อมโยงแล้ว
                                            </span>
                                        </div>
                                        <div class="d-flex flex-wrap align-items-center gap-2 mt-1 text-secondary small">
                                            <span><i class="bx bx-envelope text-primary me-1"></i><code id="previewEmail" class="text-primary fw-bold"><?= esc($user['email'] ?? $user['username']) ?></code></span>
                                            <span>•</span>
                                            <span id="previewPosition" class="badge bg-label-primary"><?= esc($user['position_name'] ?: 'บุคลากร') ?></span>
                                            <span id="previewDepartment" class="badge bg-label-secondary"><?= esc($user['department_name'] ?: 'สำนักงาน') ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2. อีเมลโรงเรียนสำหรับเข้าสู่ระบบ -->
                        <div class="mb-4">
                            <label for="inputEmail" class="form-label fw-bold text-dark fs-6">
                                <i class="bx bx-envelope text-primary me-1"></i> อีเมลโรงเรียน (Google Workspace @skj.ac.th) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text bg-white"><i class="bx bx-envelope text-secondary"></i></span>
                                <input type="email" name="email" id="inputEmail" class="form-control" value="<?= esc(old('email', $user['email'] ?? $user['username'])) ?>" placeholder="เช่น admin@skj.ac.th" required>
                            </div>
                            <small class="text-muted d-block mt-1">
                                ใช้สำหรับเข้าสู่ระบบผ่าน Google Login โรงเรียน (หากไม่ระบุ @skj.ac.th ระบบจะเติมให้อัตโนมัติ)
                            </small>
                        </div>

                        <!-- 3. กำหนดสิทธิ์การใช้งาน (Role Selection) -->
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark fs-6 mb-2">
                                <i class="bx bx-shield text-primary me-1"></i> สิทธิ์การใช้งานระบบ (Role Privileges) <span class="text-danger">*</span>
                            </label>

                            <?php 
                                $currentRole = old('role', $user['role']);
                                $isSuperAdmin = ($currentRole === 'super_admin');
                                $isAdmin = ($currentRole === 'admin');
                                $isGeneral = (!$isSuperAdmin && !$isAdmin);
                            ?>

                            <div class="row g-3">
                                <!-- Option 1: Supper Admin -->
                                <div class="col-md-4">
                                    <div class="role-card p-3 h-100 <?= $isSuperAdmin ? 'selected' : '' ?>" id="roleCardSuperAdmin" onclick="selectRole('super_admin')">
                                        <div class="role-check-indicator">
                                            <i class="bx bx-check <?= $isSuperAdmin ? '' : 'd-none' ?>" id="checkIconSuperAdmin"></i>
                                        </div>
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="avatar avatar-sm me-2 flex-shrink-0">
                                                <span class="avatar-initial rounded bg-label-warning text-warning">
                                                    <i class="bx bx-crown fs-4"></i>
                                                </span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-bold text-dark">Supper Admin</h6>
                                                <span class="badge bg-warning text-dark py-0 px-2 fw-semibold" style="font-size: 0.7rem;">ผู้ดูแลระบบสูงสุด</span>
                                            </div>
                                        </div>
                                        <p class="text-secondary small mb-0 mt-2" style="font-size: 0.82rem; line-height: 1.45;">
                                            สิทธิ์สูงสุด: ควบคุมระบบทั้งหมด จัดการแต่งตั้ง/ระงับสิทธิ์เจ้าหน้าที่ และดูทะเบียนทุกสายงาน
                                        </p>
                                    </div>
                                </div>

                                <!-- Option 2: Admin -->
                                <div class="col-md-4">
                                    <div class="role-card role-admin p-3 h-100 <?= $isAdmin ? 'selected' : '' ?>" id="roleCardAdmin" onclick="selectRole('admin')">
                                        <div class="role-check-indicator">
                                            <i class="bx bx-check <?= $isAdmin ? '' : 'd-none' ?>" id="checkIconAdmin"></i>
                                        </div>
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="avatar avatar-sm me-2 flex-shrink-0">
                                                <span class="avatar-initial rounded bg-label-info text-info">
                                                    <i class="bx bx-folder-open fs-4"></i>
                                                </span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-bold text-dark">Admin</h6>
                                                <span class="badge bg-info text-white py-0 px-2 fw-semibold" style="font-size: 0.7rem;">เจ้าหน้าที่สารบรรณ</span>
                                            </div>
                                        </div>
                                        <p class="text-secondary small mb-0 mt-2" style="font-size: 0.82rem; line-height: 1.45;">
                                            เจ้าหน้าที่สารบรรณ: ลงรับ-ส่งหนังสือ แนบไฟล์ PDF คุมเลขทะเบียน และเดินแฟ้มเอกสาร
                                        </p>
                                    </div>
                                </div>

                                <!-- Option 3: General User -->
                                <div class="col-md-4">
                                    <div class="role-card role-general p-3 h-100 <?= $isGeneral ? 'selected' : '' ?>" id="roleCardGeneral" onclick="selectRole('teacher')">
                                        <div class="role-check-indicator">
                                            <i class="bx bx-check <?= $isGeneral ? '' : 'd-none' ?>" id="checkIconGeneral"></i>
                                        </div>
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="avatar avatar-sm me-2 flex-shrink-0">
                                                <span class="avatar-initial rounded bg-label-secondary text-secondary">
                                                    <i class="bx bx-user fs-4"></i>
                                                </span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-bold text-dark">ผู้ใช้งานทั่วไป</h6>
                                                <span class="badge bg-secondary text-white py-0 px-2 fw-semibold" style="font-size: 0.7rem;">อีเมลโรงเรียน</span>
                                            </div>
                                        </div>
                                        <p class="text-secondary small mb-0 mt-2" style="font-size: 0.82rem; line-height: 1.45;">
                                            ผู้ใช้งานทั่วไป: ยื่นคำขอไปราชการ สร้างบันทึกข้อความ และลงนาม/เกษียนตามขั้นตอน
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Hidden Radio inputs for form submission -->
                            <input type="radio" name="role" value="super_admin" id="radioRoleSuperAdmin" class="d-none" <?= $isSuperAdmin ? 'checked' : '' ?> required>
                            <input type="radio" name="role" value="admin" id="radioRoleAdmin" class="d-none" <?= $isAdmin ? 'checked' : '' ?> required>
                            <input type="radio" name="role" value="teacher" id="radioRoleGeneral" class="d-none" <?= $isGeneral ? 'checked' : '' ?> required>
                        </div>

                        <!-- 4. สถานะเปิดใช้งาน (Active Switch) -->
                        <div class="p-3 bg-light rounded-3 mb-4 border">
                            <div class="form-check form-switch mb-0 d-flex align-items-center justify-content-between ps-0">
                                <div>
                                    <label class="form-check-label fw-bold text-dark fs-6 d-block" for="isActiveSwitch">
                                        สถานะการใช้งานบัญชี (Account Status)
                                    </label>
                                    <small class="text-muted d-block" id="statusHelpText">
                                        <?= old('is_active', $user['is_active']) ? '🟢 บัญชีเปิดใช้งานตามปกติ บุคลากรสามารถล็อกอินและดำเนินการในระบบได้' : '🔴 บัญชีถูกระงับการใช้งานชั่วคราว ไม่สามารถเข้าสู่ระบบได้' ?>
                                    </small>
                                </div>
                                <div class="ms-3">
                                    <input class="form-check-input fs-4" type="checkbox" name="is_active" id="isActiveSwitch" value="1" 
                                        <?= old('is_active', $user['is_active']) ? 'checked' : '' ?> 
                                        <?= ((int)$user['id'] === (int)session()->get('user_id')) ? 'disabled' : '' ?>>
                                </div>
                            </div>
                            <?php if ((int)$user['id'] === (int)session()->get('user_id')): ?>
                                <small class="text-danger d-block mt-2">
                                    <i class="bx bx-shield-quarter me-1"></i> ไม่อนุญาตให้ปิดการใช้งานบัญชีของตนเองที่กำลังเข้าสู่ระบบอยู่
                                </small>
                            <?php endif; ?>
                        </div>

                        <!-- Card Action Footer -->
                        <div class="pt-3 border-top d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="text-muted small">
                                <i class="bx bx-time-five me-1"></i> อัปเดตล่าสุด: <?= !empty($user['updated_at']) ? thai_date($user['updated_at'], true) : '-' ?>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="<?= base_url('admin/users') ?>" class="btn btn-outline-secondary fw-semibold px-4">
                                    ยกเลิก
                                </a>
                                <button type="submit" class="btn btn-primary fw-bold px-4 shadow-sm">
                                    <i class="bx bx-save me-1"></i> บันทึกการเปลี่ยนแปลง
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        <!-- Right Column: Executive Profile Card & Quick Stats -->
        <div class="col-lg-4">
            <!-- Profile Hero Widget -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 0.85rem; overflow: hidden;">
                <div class="profile-hero <?= ($user['role'] === 'super_admin') ? '' : (($user['role'] === 'admin') ? 'admin-hero' : 'general-hero') ?>" id="profileHeroBanner"></div>
                <div class="card-body p-4 pt-0 text-center">
                    <div class="profile-avatar-wrapper mb-2">
                        <span class="avatar-initial rounded-circle profile-avatar fs-2 fw-bold d-flex align-items-center justify-content-center text-white
                            <?= ($user['role'] === 'super_admin') ? 'bg-primary' : (($user['role'] === 'admin') ? 'bg-info' : 'bg-secondary') ?>" id="sideProfileAvatar">
                            <i class="bx <?= ($user['role'] === 'super_admin') ? 'bx-crown' : (($user['role'] === 'admin') ? 'bx-folder-open' : 'bx-user') ?>"></i>
                        </span>
                        <span class="profile-status-indicator <?= ((int)$user['is_active'] === 1) ? 'bg-success' : 'bg-danger' ?>" id="sideStatusDot"></span>
                    </div>

                    <h5 class="fw-bold mb-1 text-dark" id="sideFullName" style="font-family: 'K2D', sans-serif;">
                        <?= esc($user['first_name'] ? ($user['prefix'] . $user['first_name'] . ' ' . $user['last_name']) : ($user['email'] ?? $user['username'])) ?>
                    </h5>
                    <div class="text-secondary small mb-2" id="sideEmail">
                        <i class="bx bx-envelope text-primary me-1"></i><?= esc($user['email'] ?? $user['username']) ?>
                    </div>

                    <div class="mb-3" id="sideRoleBadge">
                        <?php if ($user['role'] === 'super_admin'): ?>
                            <span class="badge bg-primary text-white px-3 py-1 fw-bold fs-7 shadow-xs">
                                <i class="bx bx-crown me-1"></i> Supper Admin
                            </span>
                        <?php elseif ($user['role'] === 'admin'): ?>
                            <span class="badge bg-info text-white px-3 py-1 fw-bold fs-7 shadow-xs">
                                <i class="bx bx-folder-open me-1"></i> Admin (สารบรรณ)
                            </span>
                        <?php else: ?>
                            <span class="badge bg-secondary text-white px-3 py-1 fw-bold fs-7 shadow-xs">
                                <i class="bx bx-user me-1"></i> ผู้ใช้งานทั่วไป
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex justify-content-center gap-1 mb-4 flex-wrap">
                        <span class="badge bg-label-primary"><?= esc($user['position_name'] ?: 'บุคลากร') ?></span>
                        <span class="badge bg-label-secondary"><?= esc($user['department_name'] ?: 'งานสารบรรณ') ?></span>
                    </div>

                    <!-- System Engagement Metrics -->
                    <div class="row g-2 mb-3">
                        <div class="col-4">
                            <div class="p-2 bg-light rounded text-center border">
                                <div class="text-primary fw-bold fs-5 mb-0"><?= number_format($stats['created_docs'] ?? 0) ?></div>
                                <small class="text-secondary" style="font-size: 0.72rem;">เอกสารที่สร้าง</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 bg-light rounded text-center border">
                                <div class="text-success fw-bold fs-5 mb-0"><?= number_format($stats['signed_docs'] ?? 0) ?></div>
                                <small class="text-secondary" style="font-size: 0.72rem;">ลงนามแล้ว</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 bg-light rounded text-center border">
                                <div class="text-info fw-bold fs-5 mb-0"><?= number_format($stats['travel_docs'] ?? 0) ?></div>
                                <small class="text-secondary" style="font-size: 0.72rem;">ไปราชการ</small>
                            </div>
                        </div>
                    </div>

                    <!-- Account Details List -->
                    <div class="p-3 bg-light rounded text-start small mb-3 border">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-secondary">รหัสอ้างอิง:</span>
                            <span class="fw-bold text-dark">#<?= esc($user['id']) ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-secondary">เบอร์ติดต่อ:</span>
                            <span class="fw-semibold text-dark"><?= esc($user['phone'] ?: '-') ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-secondary">วันที่ลงทะเบียน:</span>
                            <span class="text-dark"><?= !empty($user['created_at']) ? thai_date($user['created_at']) : '-' ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-secondary">สถานะบัญชี:</span>
                            <span class="fw-bold <?= ((int)$user['is_active'] === 1) ? 'text-success' : 'text-danger' ?>">
                                <?= ((int)$user['is_active'] === 1) ? '🟢 กำลังเปิดใช้งาน' : '🔴 ระงับการใช้งาน' ?>
                            </span>
                        </div>
                    </div>

                    <!-- Quick Action: Toggle Status -->
                    <?php if ((int)$user['id'] !== (int)session()->get('user_id')): ?>
                        <form action="<?= base_url('admin/users/' . $user['id'] . '/toggle-status') ?>" method="post">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-outline-<?= ((int)$user['is_active'] === 1) ? 'danger' : 'success' ?> btn-sm w-100 fw-bold">
                                <i class="bx <?= ((int)$user['is_active'] === 1) ? 'bx-lock-alt' : 'bx-lock-open-alt' ?> me-1"></i>
                                <?= ((int)$user['is_active'] === 1) ? 'ระงับการใช้งานบัญชีนี้' : 'เปิดใช้งานบัญชีนี้' ?>
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Guidelines Card -->
            <div class="card border-0 shadow-sm" style="border-radius: 0.85rem; border-left: 4px solid #696cff !important;">
                <div class="card-body p-3">
                    <h6 class="fw-bold text-dark mb-2">
                        <i class="bx bx-info-circle text-primary me-1"></i> คำแนะนำการกำหนดสิทธิ์
                    </h6>
                    <ul class="text-secondary small mb-0 ps-3" style="line-height: 1.6;">
                        <li><strong>Supper Admin:</strong> ควบคุมระบบทั้งหมด จัดการแต่งตั้งเจ้าหน้าที่</li>
                        <li><strong>Admin:</strong> เจ้าหน้าที่สารบรรณ ลงรับ-ส่ง และคุมเลขทะเบียน</li>
                        <li><strong>ผู้ใช้งานทั่วไป:</strong> สำหรับครูและบุคลากร สามารถล็อกอินด้วยอีเมล <code>@skj.ac.th</code> ได้อัตโนมัติ</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Select2 JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize Select2 for Personnel Selection
    $('#selectPersonnel').select2({
        theme: 'bootstrap-5',
        placeholder: '-- ค้นหาชื่อ-สกุล, ตำแหน่ง, หรืออีเมลบุคลากร --',
        allowClear: true,
        templateResult: formatPersonnelOption,
        templateSelection: formatPersonnelSelection
    });

    // Handle Personnel Selection Change
    $('#selectPersonnel').on('change', function() {
        const val = $(this).val();
        if (!val) return;

        const selectedOption = $(this).find('option:selected');
        const fullName = selectedOption.data('fullname');
        const email = selectedOption.data('email');
        const phone = selectedOption.data('phone') || '-';
        const position = selectedOption.data('position');
        const department = selectedOption.data('department');
        const initial = selectedOption.data('initial') || fullName.charAt(0) || 'U';

        // Update hidden & input fields
        $('#inputPersId').val(val);
        if (email) {
            $('#inputEmail').val(email);
        }

        // Update Left Highlight Card
        $('#previewFullName').text(fullName);
        $('#previewEmail').text(email);
        $('#previewPosition').text(position);
        $('#previewDepartment').text(department);
        $('#previewAvatar').text(initial);
        $('#personPreviewCard').removeClass('d-none');

        // Update Right Profile Widget
        $('#sideFullName').text(fullName);
        $('#sideEmail').html('<i class="bx bx-envelope text-primary me-1"></i>' + email);
    });

    // Active Switch Dynamic Feedback
    $('#isActiveSwitch').on('change', function() {
        const isChecked = $(this).is(':checked');
        if (isChecked) {
            $('#statusHelpText').text('🟢 บัญชีเปิดใช้งานตามปกติ บุคลากรสามารถล็อกอินและดำเนินการในระบบได้');
            $('#sideStatusDot').removeClass('bg-danger').addClass('bg-success');
        } else {
            $('#statusHelpText').text('🔴 บัญชีถูกระงับการใช้งานชั่วคราว ไม่สามารถเข้าสู่ระบบได้');
            $('#sideStatusDot').removeClass('bg-success').addClass('bg-danger');
        }
    });
});

// Format option layout inside dropdown
function formatPersonnelOption(state) {
    if (!state.id) return state.text;
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

// Format selected text in the closed select box
function formatPersonnelSelection(state) {
    if (!state.id) return state.text;
    const $el = $(state.element);
    const fullName = $el.data('fullname');
    const email = $el.data('email') || state.id;
    if (fullName) {
        return `${fullName} (${email})`;
    }
    return state.text;
}

// Interactive Role Selection
function selectRole(role) {
    $('.role-card').removeClass('selected');
    $('#checkIconSuperAdmin, #checkIconAdmin, #checkIconGeneral').addClass('d-none');

    const hero = $('#profileHeroBanner');
    const avatar = $('#sideProfileAvatar');
    const badge = $('#sideRoleBadge');

    if (role === 'super_admin') {
        $('#radioRoleSuperAdmin').prop('checked', true);
        $('#roleCardSuperAdmin').addClass('selected');
        $('#checkIconSuperAdmin').removeClass('d-none');

        // Update Right Widget visuals
        hero.removeClass('admin-hero general-hero');
        avatar.removeClass('bg-info bg-secondary').addClass('bg-primary').html('<i class="bx bx-crown"></i>');
        badge.html('<span class="badge bg-primary text-white px-3 py-1 fw-bold fs-7 shadow-xs"><i class="bx bx-crown me-1"></i> Supper Admin</span>');
    } else if (role === 'admin') {
        $('#radioRoleAdmin').prop('checked', true);
        $('#roleCardAdmin').addClass('selected');
        $('#checkIconAdmin').removeClass('d-none');

        // Update Right Widget visuals
        hero.removeClass('general-hero').addClass('admin-hero');
        avatar.removeClass('bg-primary bg-secondary').addClass('bg-info').html('<i class="bx bx-folder-open"></i>');
        badge.html('<span class="badge bg-info text-white px-3 py-1 fw-bold fs-7 shadow-xs"><i class="bx bx-folder-open me-1"></i> Admin (สารบรรณ)</span>');
    } else {
        $('#radioRoleGeneral').prop('checked', true);
        $('#roleCardGeneral').addClass('selected');
        $('#checkIconGeneral').removeClass('d-none');

        // Update Right Widget visuals
        hero.removeClass('admin-hero').addClass('general-hero');
        avatar.removeClass('bg-primary bg-info').addClass('bg-secondary').html('<i class="bx bx-user"></i>');
        badge.html('<span class="badge bg-secondary text-white px-3 py-1 fw-bold fs-7 shadow-xs"><i class="bx bx-user me-1"></i> ผู้ใช้งานทั่วไป</span>');
    }
}
</script>
<?= $this->endSection() ?>
