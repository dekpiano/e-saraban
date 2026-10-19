<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
จัดการผู้ดูแลระบบและเจ้าหน้าที่สารบรรณ (Supper Admin & Admin Management)
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-xxl flex-grow-1 container-p-y">

    <!-- Header Section -->
    <div class="d-md-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark" style="font-family: 'K2D', sans-serif;">
                <i class="bx bx-shield-quarter text-primary me-2"></i>จัดการสิทธิ์ผู้ใช้งาน (Supper Admin & Admin)
            </h4>
            <p class="text-secondary mb-0 small">
                ระบบมี 2 สิทธิ์หลักสำหรับจัดการระบบ ได้แก่ <strong>Supper Admin</strong> (ผู้ดูแลระบบสูงสุด) และ <strong>Admin</strong> (เจ้าหน้าที่สารบรรณ) ส่วนบุคลากรที่มีอีเมลโรงเรียน <code>@skj.ac.th</code> เข้าใช้งานทั่วไปได้ทุกคน
            </p>
        </div>
        <div class="mt-3 mt-md-0 d-flex gap-2">
            <a href="<?= base_url('admin/roles') ?>" class="btn btn-outline-secondary btn-sm fw-bold">
                <i class="bx bx-table me-1"></i> ตารางสิทธิ์ระบบ (Matrix)
            </a>
            <a href="<?= base_url('admin/users/create') ?>" class="btn btn-primary btn-sm fw-bold shadow-sm">
                <i class="bx bx-user-plus me-1"></i> แต่งตั้งสิทธิ์เจ้าหน้าที่
            </a>
        </div>
    </div>

    <!-- Info Banner: ข้อมูลบุคลากรและการเข้าสู่ระบบ -->
    <div class="alert alert-info border-0 shadow-sm d-flex align-items-center mb-4" role="alert" style="border-radius: 0.75rem; background-color: #e7e7ff; color: #435ebe;">
        <i class="bx bx-info-circle fs-3 me-3 text-primary flex-shrink-0"></i>
        <div class="small">
            <strong class="d-block mb-1" style="color: #2b3445; font-size: 0.95rem;">
                📌 โครงสร้างสิทธิ์การใช้งาน e-Saraban
            </strong>
            <span style="color: #4b5563;">
                • <strong>2 สิทธิ์หลักสำหรับผู้ดูแล:</strong> 👑 <strong>Supper Admin</strong> (ผู้ดูแลระบบสูงสุด) และ 📂 <strong>Admin</strong> (เจ้าหน้าที่สารบรรณ)<br>
                • <strong>ผู้ใช้งานทั่วไป:</strong> ครู บุคลากร และผู้บริหารทุกท่านที่มีอีเมลโรงเรียน <code>@skj.ac.th</code> สามารถเข้าสู่ระบบด้วย Google และใช้งานได้ทั่วไป (ยื่นเรื่อง ขอไปราชการ ลงนามเกษียนหนังสือ) ได้โดยอัตโนมัติ
            </span>
        </div>
    </div>

    <!-- Alert Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible d-flex align-items-center fade show shadow-sm" role="alert">
            <i class="bx bx-check-circle fs-4 me-2"></i>
            <div><?= esc(session()->getFlashdata('success')) ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible d-flex align-items-center fade show shadow-sm" role="alert">
            <i class="bx bx-error fs-4 me-2"></i>
            <div><?= esc(session()->getFlashdata('error')) ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Stat Cards Overview: ผู้ใช้งานทั้งหมดในระบบ -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 0.75rem; border-left: 4px solid #696cff !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-secondary fw-semibold small">ผู้ใช้งานในระบบทั้งหมด</span>
                            <h3 class="fw-bold mb-0 text-dark mt-1"><?= number_format($stats['total'] ?? 0) ?></h3>
                        </div>
                        <div class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-primary text-primary">
                                <i class="bx bx-group fs-3"></i>
                            </span>
                        </div>
                    </div>
                    <div class="mt-2 small text-secondary">
                        <span class="badge bg-success text-white px-2 py-0">เปิดใช้งาน: <?= $stats['active'] ?? 0 ?></span>
                        <?php if (($stats['inactive'] ?? 0) > 0): ?>
                            <span class="badge bg-danger text-white px-2 py-0 ms-1">ระงับ: <?= $stats['inactive'] ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 0.75rem; border-left: 4px solid #ffab00 !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-secondary fw-semibold small">👑 Supper Admin (ผู้ดูแลระบบสูงสุด)</span>
                            <h3 class="fw-bold mb-0 text-dark mt-1"><?= number_format($stats['super_admin'] ?? 0) ?></h3>
                        </div>
                        <div class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-warning text-warning">
                                <i class="bx bx-crown fs-3"></i>
                            </span>
                        </div>
                    </div>
                    <div class="mt-2 small text-secondary">
                        สิทธิ์สูงสุด: ควบคุมและกำหนดสิทธิ์ผู้ใช้ทั้งหมด
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 0.75rem; border-left: 4px solid #03c3ec !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-secondary fw-semibold small">📂 Admin (เจ้าหน้าที่สารบรรณ)</span>
                            <h3 class="fw-bold mb-0 text-dark mt-1"><?= number_format($stats['admin'] ?? 0) ?></h3>
                        </div>
                        <div class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-info text-info">
                                <i class="bx bx-folder-open fs-3"></i>
                            </span>
                        </div>
                    </div>
                    <div class="mt-2 small text-secondary">
                        เจ้าหน้าที่สารบรรณ: ลงรับ-ส่ง คุมเลข และเดินเรื่อง
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Filter Card -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 0.75rem;">
        <div class="card-body p-3">
            <form method="get" action="<?= base_url('admin/users') ?>" class="row g-2 align-items-center">
                <div class="col-md-6">
                    <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="bx bx-search text-secondary"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="ค้นหาชื่อ-สกุล, ตำแหน่ง หรืออีเมล (@skj.ac.th)..." value="<?= esc($filters['search'] ?? '') ?>">
                    </div>
                </div>

                <div class="col-md-4">
                    <select name="role" class="form-select">
                        <option value="">-- กรองทุกสิทธิ์การใช้งาน (ทั้งหมด) --</option>
                        <option value="super_admin" <?= (($filters['role'] ?? '') === 'super_admin') ? 'selected' : '' ?>>👑 Supper Admin (ผู้ดูแลระบบสูงสุด)</option>
                        <option value="admin" <?= (($filters['role'] ?? '') === 'admin') ? 'selected' : '' ?>>📂 Admin (เจ้าหน้าที่งานสารบรรณ)</option>
                        <option value="general" <?= (($filters['role'] ?? '') === 'general') ? 'selected' : '' ?>>👤 ผู้ใช้งานทั่วไป (อีเมลโรงเรียน @skj.ac.th)</option>
                    </select>
                </div>

                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100 fw-bold">
                        <i class="bx bx-filter-alt me-1"></i> ค้นหา
                    </button>
                    <?php if (!empty($filters['search']) || !empty($filters['role']) || (isset($filters['is_active']) && $filters['is_active'] !== '')): ?>
                        <a href="<?= base_url('admin/users') ?>" class="btn btn-outline-secondary" title="ล้างตัวกรอง">
                            <i class="bx bx-reset"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table Card -->
    <div class="card border-0 shadow-sm" style="border-radius: 0.75rem;">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h5 class="card-title mb-0 fw-bold text-dark" style="font-family: 'K2D', sans-serif;">
                <i class="bx bx-list-ul text-primary me-2"></i>รายชื่อผู้ใช้งานในระบบ (<?= count($users) ?> บัญชี)
            </h5>
            <span class="badge bg-label-primary fw-bold px-3 py-2">
                ผู้ใช้งานปัจจุบัน: <?= esc(session()->get('username')) ?> (<?= esc(session()->get('role') === 'super_admin' ? 'Supper Admin' : session()->get('role')) ?>)
            </span>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 70px;">ID</th>
                        <th>บุคลากร / ชื่อ-สกุล (Personnel)</th>
                        <th>อีเมลโรงเรียน (School Email)</th>
                        <th class="text-center">สิทธิ์การใช้งาน (Role)</th>
                        <th class="text-center">สถานะ</th>
                        <th class="text-center" style="width: 140px;">จัดการ</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-secondary">
                                <i class="bx bx-user-x fs-1 text-muted mb-2 d-block"></i>
                                <div class="fw-bold fs-6 text-dark">ไม่พบข้อมูลผู้ใช้งานที่ค้นหา</div>
                                <small>กดปุ่ม "เพิ่มเจ้าหน้าที่ใหม่" เพื่อแต่งตั้งหรือเพิ่มผู้ใช้งานในระบบ</small>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $index => $u): ?>
                            <?php 
                                $fullName = trim(($u['prefix'] ?? '') . ($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? ''));
                                if (empty($fullName)) {
                                    $fullName = $u['email'] ?: $u['username'];
                                }
                                $initial = mb_substr($u['first_name'] ?: ($u['email'] ?: 'U'), 0, 1);
                                $posName = $u['position_name'] ?? '-';
                                $deptName = $u['department_name'] ?? '-';
                            ?>
                            <tr>
                                <td class="text-center text-secondary fw-semibold">#<?= $u['id'] ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-3 flex-shrink-0">
                                            <span class="avatar-initial rounded-circle 
                                                <?php if ($u['role'] === 'super_admin'): ?>
                                                    bg-primary text-white
                                                <?php elseif ($u['role'] === 'executive'): ?>
                                                    bg-warning text-dark
                                                <?php elseif ($u['role'] === 'admin'): ?>
                                                    bg-info text-white
                                                <?php else: ?>
                                                    bg-secondary text-white
                                                <?php endif; ?>">
                                                <?= esc($initial) ?>
                                            </span>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark fs-6">
                                                <?= esc($fullName) ?>
                                            </div>
                                            <div class="d-flex align-items-center gap-1 small text-muted">
                                                <span class="badge bg-label-secondary py-0 px-1" style="font-size: 0.72rem;"><?= esc($posName) ?></span>
                                                <?php if (!empty($deptName) && $deptName !== '-'): ?>
                                                    <span>•</span>
                                                    <span><?= esc($deptName) ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold text-primary">
                                        <i class="bx bx-envelope me-1"></i><?= esc($u['email'] ?: $u['username']) ?>
                                    </div>
                                    <small class="text-muted">Username: <?= esc($u['username']) ?></small>
                                </td>
                                <td class="text-center">
                                    <?php if ($u['role'] === 'super_admin'): ?>
                                        <span class="badge bg-primary text-white fw-bold px-3 py-1">
                                            <i class="bx bx-crown me-1"></i> Supper Admin
                                        </span>
                                    <?php elseif ($u['role'] === 'admin'): ?>
                                        <span class="badge bg-info text-white fw-bold px-3 py-1">
                                            <i class="bx bx-folder-open me-1"></i> Admin (สารบรรณ)
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary text-white fw-semibold px-3 py-1">
                                            <i class="bx bx-user me-1"></i> ผู้ใช้งานทั่วไป
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ((int)$u['is_active'] === 1): ?>
                                        <span class="badge bg-success text-white fw-bold">ปกติ</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger text-white fw-bold">ระงับ</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-sm btn-icon btn-outline-secondary dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end shadow-sm">
                                            <a class="dropdown-item" href="<?= base_url('admin/users/' . $u['id'] . '/edit') ?>">
                                                <i class="bx bx-edit-alt me-2 text-primary"></i> แก้ไข & เปลี่ยนสิทธิ์
                                            </a>
                                            
                                            <?php if ((int)$u['id'] !== (int)session()->get('user_id')): ?>
                                                <div class="dropdown-divider"></div>
                                                <form action="<?= base_url('admin/users/' . $u['id'] . '/toggle-status') ?>" method="post" class="d-inline">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="dropdown-item <?= ((int)$u['is_active'] === 1) ? 'text-warning' : 'text-success' ?>">
                                                        <i class="bx <?= ((int)$u['is_active'] === 1) ? 'bx-lock-alt' : 'bx-lock-open-alt' ?> me-2"></i>
                                                        <?= ((int)$u['is_active'] === 1) ? 'ระงับการใช้งาน' : 'เปิดใช้งานบัญชี' ?>
                                                    </button>
                                                </form>
                                                <button type="button" class="dropdown-item text-danger" onclick="confirmDelete('<?= $u['id'] ?>', '<?= esc($fullName) ?>')">
                                                    <i class="bx bx-trash me-2"></i> ลบบัญชีผู้ใช้
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Form ซ่อนสำหรับ Delete -->
<form id="deleteUserForm" method="post" style="display: none;">
    <?= csrf_field() ?>
</form>

<script>
function confirmDelete(userId, userName) {
    AppAlert.confirm('คุณต้องการลบบัญชีเจ้าหน้าที่ "' + userName + '" ใช่หรือไม่? ข้อมูลนี้จะไม่สามารถกู้คืนได้', {
        title: 'ยืนยันการลบบัญชี',
        confirmText: '<i class="bx bx-trash me-1"></i> ลบบัญชี',
        cancelText: 'ยกเลิก',
        icon: 'warning'
    }).then(function(confirmed) {
        if (confirmed) {
            const form = document.getElementById('deleteUserForm');
            form.action = '<?= base_url('admin/users') ?>/' + userId + '/delete';
            form.submit();
        }
    });
}
</script>
<?= $this->endSection() ?>
