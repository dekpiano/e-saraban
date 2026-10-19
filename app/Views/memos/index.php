<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>ทะเบียนบันทึกข้อความ (ภายในโรงเรียน)<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">

        <!-- Stat Cards -->
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="card shadow-sm border-0 border-start border-primary border-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <small class="text-muted fw-semibold d-block mb-1">บันทึกข้อความทั้งหมด</small>
                                <h3 class="card-title mb-0 fw-bold text-dark"><?= number_format($stats['total'] ?? 0) ?></h3>
                                <small class="text-primary fw-medium"><i class="bx bx-file me-1"></i>บันทึกข้อความภายใน รร.</small>
                            </div>
                            <div class="avatar bg-label-primary p-2 rounded-3">
                                <i class="bx bx-notepad fs-2 text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card shadow-sm border-0 border-start border-warning border-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <small class="text-muted fw-semibold d-block mb-1">กำลังเดินเรื่อง / รอเกษียน</small>
                                <h3 class="card-title mb-0 fw-bold text-warning"><?= number_format($stats['routing'] ?? 0) ?></h3>
                                <small class="text-warning fw-medium"><i class="bx bx-time-five me-1"></i>อยู่ในสายการบังคับบัญชา</small>
                            </div>
                            <div class="avatar bg-label-warning p-2 rounded-3">
                                <i class="bx bx-git-branch fs-2 text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card shadow-sm border-0 border-start border-success border-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <small class="text-muted fw-semibold d-block mb-1">อนุมัติ / เสร็จสิ้นสมบูรณ์</small>
                                <h3 class="card-title mb-0 fw-bold text-success"><?= number_format($stats['completed'] ?? 0) ?></h3>
                                <small class="text-success fw-medium"><i class="bx bx-check-double me-1"></i>ลงนามครบทุกขั้นตอน</small>
                            </div>
                            <div class="avatar bg-label-success p-2 rounded-3">
                                <i class="bx bx-check-shield fs-2 text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card shadow-sm border-0 border-start border-danger border-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <small class="text-muted fw-semibold d-block mb-1">ส่งคืนให้แก้ไข</small>
                                <h3 class="card-title mb-0 fw-bold text-danger"><?= number_format($stats['returned'] ?? 0) ?></h3>
                                <small class="text-danger fw-medium"><i class="bx bx-reply me-1"></i>รอปรับปรุงข้อความ</small>
                            </div>
                            <div class="avatar bg-label-danger p-2 rounded-3">
                                <i class="bx bx-rotate-left fs-2 text-danger"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Card -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2 border-bottom py-3">
                <div>
                    <h5 class="card-title mb-1 fw-bold text-primary">
                        <i class="bx bx-notepad me-2"></i> ทะเบียนบันทึกข้อความ (ภายในโรงเรียน)
                    </h5>
                    <small class="text-muted">ระบบร่างบันทึกข้อความแบบพิมพ์เหมือน Word ออกเลขทะเบียน และเดินเรื่องขออนุมัติออนไลน์</small>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?= base_url('admin/memos/create') ?>" class="btn btn-primary fw-semibold shadow-sm">
                        <i class="bx bx-edit me-1"></i> เขียน/ออกเลขบันทึกข้อความใหม่
                    </a>
                </div>
            </div>

            <!-- Filter Tabs & Search Bar -->
            <div class="card-body pb-0 pt-3">
                <div class="row align-items-center g-3 mb-3">
                    <div class="col-md-7">
                        <ul class="nav nav-pills flex-wrap gap-1">
                            <li class="nav-item">
                                <a class="nav-link <?= empty($currentStatus) ? 'active' : '' ?>" href="<?= base_url('admin/memos') ?>">
                                    <i class="bx bx-grid-alt me-1"></i> ทั้งหมด (<?= $stats['total'] ?? 0 ?>)
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= $currentStatus === 'routing' ? 'active' : '' ?>" href="<?= base_url('admin/memos?status=routing') ?>">
                                    <i class="bx bx-time-five me-1"></i> กำลังเดินเรื่อง (<?= $stats['routing'] ?? 0 ?>)
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= $currentStatus === 'completed' ? 'active' : '' ?>" href="<?= base_url('admin/memos?status=completed') ?>">
                                    <i class="bx bx-check-circle me-1"></i> อนุมัติแล้ว (<?= $stats['completed'] ?? 0 ?>)
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= $currentStatus === 'returned' ? 'active' : '' ?>" href="<?= base_url('admin/memos?status=returned') ?>">
                                    <i class="bx bx-reply me-1"></i> ส่งคืนแก้ไข (<?= $stats['returned'] ?? 0 ?>)
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= $currentStatus === 'draft' ? 'active' : '' ?>" href="<?= base_url('admin/memos?status=draft') ?>">
                                    <i class="bx bx-file-blank me-1"></i> ฉบับร่าง
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="col-md-5">
                        <form method="GET" action="<?= base_url('admin/memos') ?>" class="d-flex gap-2">
                            <?php if (!empty($currentStatus)): ?>
                                <input type="hidden" name="status" value="<?= esc($currentStatus) ?>">
                            <?php endif; ?>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bx bx-search"></i></span>
                                <input type="text" name="search" class="form-control" placeholder="ค้นหาเลขที่, เรื่อง, หรือผู้เสนอ..." value="<?= esc($search ?? '') ?>">
                            </div>
                            <button type="submit" class="btn btn-outline-primary">ค้นหา</button>
                            <?php if (!empty($search)): ?>
                                <a href="<?= base_url('admin/memos' . (!empty($currentStatus) ? '?status=' . $currentStatus : '')) ?>" class="btn btn-outline-secondary">ล้าง</a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Documents Table -->
            <div class="table-responsive text-nowrap">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 130px;">เลขที่บันทึกข้อความ</th>
                            <th style="width: 110px;">เลขทะเบียน รร.</th>
                            <th style="width: 100px;">ความเร่งด่วน</th>
                            <th>เรื่อง (หัวข้อบันทึกข้อความ)</th>
                            <th>หน่วยงานผู้เสนอ</th>
                            <th style="width: 110px;">ลงวันที่</th>
                            <th style="width: 130px;">สถานะการเดินเรื่อง</th>
                            <th style="width: 140px;" class="text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        <?php if (empty($memos)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <div class="mb-2"><i class="bx bx-folder-open fs-1 text-muted"></i></div>
                                    <h6 class="text-dark fw-bold mb-1">ไม่พบบันทึกข้อความ</h6>
                                    <p class="small text-muted mb-3">ยังไม่มีบันทึกข้อความในเงื่อนไขที่เลือก หรือยังไม่มีการสร้างบันทึกข้อความใหม่</p>
                                    <a href="<?= base_url('admin/memos/create') ?>" class="btn btn-primary btn-sm">
                                        <i class="bx bx-plus me-1"></i> เขียนบันทึกข้อความฉบับแรก
                                    </a>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($memos as $memo): ?>
                                <tr>
                                    <td>
                                        <a href="<?= base_url('admin/memos/' . $memo['id']) ?>" class="fw-bold text-primary">
                                            <?= esc($memo['doc_no']) ?>
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            <?= esc($memo['internal_reg_no'] ?: '-') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($memo['urgency'] === 'most_urgent'): ?>
                                            <span class="badge bg-danger text-white fw-bold">ด่วนที่สุด</span>
                                        <?php elseif ($memo['urgency'] === 'very_urgent'): ?>
                                            <span class="badge bg-danger text-white fw-bold">ด่วนมาก</span>
                                        <?php elseif ($memo['urgency'] === 'urgent'): ?>
                                            <span class="badge bg-warning text-dark fw-bold">ด่วน</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary text-white">ปกติ</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="text-wrap" style="max-width: 320px;">
                                            <a href="<?= base_url('admin/memos/' . $memo['id']) ?>" class="text-dark fw-semibold text-decoration-none hover-primary">
                                                <?= esc($memo['title']) ?>
                                            </a>
                                            <div class="small text-muted mt-1">
                                                <i class="bx bx-user me-1"></i> ผู้เสนอ: <?= esc(($memo['prefix'] ?? '') . ($memo['first_name'] ?? '') . ' ' . ($memo['last_name'] ?? '')) ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-label-info text-info fw-semibold text-wrap" style="max-width: 180px;">
                                            <?= esc($memo['from_org'] ?: ($memo['creator_department'] ?? 'กลุ่มสาระฯ/งาน')) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted fw-semibold">
                                            <?= thai_date($memo['doc_date'], 'short') ?>
                                        </small>
                                    </td>
                                    <td>
                                        <?php if ($memo['status'] === 'completed'): ?>
                                            <span class="badge bg-success text-white fw-bold">
                                                <i class="bx bx-check me-1"></i> อนุมัติสมบูรณ์
                                            </span>
                                        <?php elseif ($memo['status'] === 'routing'): ?>
                                            <span class="badge bg-warning text-dark fw-bold">
                                                <i class="bx bx-time me-1"></i> กำลังเสนอ (ขั้น <?= $memo['current_step'] ?>)
                                            </span>
                                        <?php elseif ($memo['status'] === 'returned'): ?>
                                            <span class="badge bg-danger text-white fw-bold">
                                                <i class="bx bx-reply me-1"></i> ส่งคืนให้แก้ไข
                                            </span>
                                        <?php elseif ($memo['status'] === 'draft'): ?>
                                            <span class="badge bg-secondary text-white">
                                                <i class="bx bx-file-blank me-1"></i> ฉบับร่าง
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary text-white"><?= esc($memo['status']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?= base_url('admin/memos/' . $memo['id']) ?>" class="btn btn-outline-primary" title="เปิดดูและตรวจสอบ">
                                                <i class="bx bx-show"></i>
                                            </a>
                                            <a href="<?= base_url('admin/memos/' . $memo['id'] . '/print') ?>" target="_blank" class="btn btn-outline-secondary" title="พิมพ์ขนาด A4">
                                                <i class="bx bx-printer"></i>
                                            </a>
                                            <?php if ($memo['status'] === 'draft' || $memo['status'] === 'returned'): ?>
                                                <a href="<?= base_url('admin/memos/' . $memo['id'] . '/edit') ?>" class="btn btn-outline-warning" title="แก้ไขข้อความ">
                                                    <i class="bx bx-edit"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Table Footer -->
            <div class="card-footer d-flex justify-content-between align-items-center py-3 bg-light">
                <small class="text-muted">
                    แสดงทั้งหมด <strong><?= count($memos) ?></strong> รายการ
                </small>
                <div class="small text-muted">
                    <i class="bx bx-info-circle me-1"></i>คลิกที่ชื่อเรื่องเพื่อดูรายละเอียดฉบับเต็มและประวัติการเดินเรื่อง
                </div>
            </div>
        </div>

    </div>
</div>
<?= $this->endSection() ?>
