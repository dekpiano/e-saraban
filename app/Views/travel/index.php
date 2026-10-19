<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>ทะเบียนขออนุมัติไปราชการทั้งหมด<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h5 class="card-title mb-1 fw-bold text-primary">
                        <i class="bx bx-briefcase-alt-2 me-2"></i> ทะเบียนขออนุมัติไปราชการ & ติดตามสถานะ
                    </h5>
                    <small class="text-muted">ระบบติดตามคำขออนุมัติเดินทางไปราชการ/อบรมพัฒนาตนเองของบุคลากรในโรงเรียน</small>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?= base_url('admin/travel/create') ?>" class="btn btn-primary">
                        <i class="bx bx-plus me-1"></i> ยื่นขออนุมัติไปราชการ
                    </a>
                </div>
            </div>

            <!-- Filter Tabs -->
            <div class="card-body pb-0">
                <ul class="nav nav-pills flex-column flex-md-row mb-3">
                    <li class="nav-item">
                        <a class="nav-link <?= empty($currentStatus) ? 'active' : '' ?>" href="<?= base_url('admin/travel') ?>">
                            <i class="bx bx-grid-alt me-1"></i> ทั้งหมด
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentStatus === 'pending' ? 'active' : '' ?>" href="<?= base_url('admin/travel?status=pending') ?>">
                            <i class="bx bx-time me-1"></i> อยู่ระหว่างพิจารณา
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentStatus === 'approved' ? 'active' : '' ?>" href="<?= base_url('admin/travel?status=approved') ?>">
                            <i class="bx bx-check-circle me-1"></i> อนุมัติแล้ว
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentStatus === 'reported' ? 'active' : '' ?>" href="<?= base_url('admin/travel?status=reported') ?>">
                            <i class="bx bx-task me-1"></i> รายงานผลแล้ว
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentStatus === 'returned' ? 'active' : '' ?>" href="<?= base_url('admin/travel?status=returned') ?>">
                            <i class="bx bx-revision me-1"></i> ส่งคืนแก้ไข
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Table (Single Screen / No Horizontal Scrollbar) -->
            <div class="table-responsive" style="overflow-x: hidden;">
                <table class="table table-hover align-middle mb-0" style="table-layout: fixed; width: 100%;">
                    <thead class="table-light">
                        <tr class="text-dark small fw-bold" style="border-bottom: 2px solid #cbd5e1;">
                            <th style="width: 14%;">เลขที่ขอ / ผู้ขอ</th>
                            <th style="width: 32%;">เรื่องและสถานที่ไปราชการ</th>
                            <th style="width: 14%;">ช่วงวันเดินทาง</th>
                            <th style="width: 13%;">สถานะคำขอ</th>
                            <th style="width: 17%;">ขั้นตอนปัจจุบัน</th>
                            <th style="width: 10%;" class="text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        <?php if (empty($requests)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bx bx-briefcase fs-1 text-secondary d-block mb-2"></i>
                                    ไม่พบรายการขออนุมัติไปราชการในหมวดหมู่นี้
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($requests as $r): ?>
                                <tr>
                                    <!-- 1. เลขที่ขอ / ผู้ขอ -->
                                    <td style="word-break: break-word;">
                                        <div class="badge font-monospace fw-bold mb-1 px-2 py-1 text-start d-inline-block text-wrap" style="background: #e2e8f0; color: #0f172a; border: 1px solid #cbd5e1; font-size: 0.74rem;">
                                            <?= esc($r['doc_no']) ?>
                                        </div>
                                        <div class="fw-bold text-dark text-truncate" style="font-size: 0.82rem;" title="<?= esc($r['prefix'] . $r['first_name'] . ' ' . $r['last_name']) ?>">
                                            <i class="bx bx-user me-1 text-primary"></i><?= esc($r['prefix'] . $r['first_name'] . ' ' . $r['last_name']) ?>
                                        </div>
                                        <div class="text-muted text-truncate" style="font-size: 0.72rem;" title="<?= esc($r['department_name']) ?>">
                                            <?= esc($r['department_name']) ?>
                                        </div>
                                    </td>

                                    <!-- 2. เรื่องและสถานที่ -->
                                    <td style="word-break: break-word; overflow-wrap: break-word;">
                                        <a href="<?= base_url('admin/travel/' . $r['id']) ?>" class="fw-bold text-dark text-decoration-none hover-primary d-block mb-1" style="font-size: 0.88rem; line-height: 1.35;">
                                            <?= esc($r['title']) ?>
                                        </a>
                                        <div class="text-muted fw-semibold small text-truncate" style="font-size: 0.76rem;" title="ณ <?= esc($r['destination']) ?> (<?= esc($r['province']) ?>)">
                                            <i class="bx bx-map-pin me-1 text-danger"></i>ณ <?= esc($r['destination']) ?> <span class="badge bg-light text-dark border px-1 py-0.5 ms-1" style="font-size: 0.7rem;"><?= esc($r['province']) ?></span>
                                        </div>
                                    </td>

                                    <!-- 3. ช่วงวันเดินทาง -->
                                    <td style="word-break: break-word;">
                                        <div class="fw-bold text-dark font-monospace" style="font-size: 0.78rem; line-height: 1.3;">
                                            <?= thai_date($r['start_date'], 'short') ?>
                                            <div class="text-muted fw-normal" style="font-size: 0.72rem;">ถึง <?= thai_date($r['end_date'], 'short') ?></div>
                                        </div>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-2 py-0.5 mt-1" style="font-size: 0.72rem;">
                                            <i class="bx bx-time-five me-0.5"></i> <?= esc($r['total_days']) ?> วัน
                                        </span>
                                    </td>

                                    <!-- 4. สถานะคำขอ -->
                                    <td style="word-break: break-word;">
                                        <div class="d-inline-block text-wrap">
                                            <?= travel_status_badge($r['status']) ?>
                                        </div>
                                    </td>

                                    <!-- 5. ขั้นตอนปัจจุบัน -->
                                    <td style="word-break: break-word; overflow-wrap: break-word;">
                                        <div class="d-inline-block text-wrap" style="max-width: 100%;">
                                            <?= travel_step_badge($r['current_step'], $r['status'], $r['approver_title'] ?? null, $r['return_step'] ?? 0) ?>
                                        </div>
                                    </td>

                                    <!-- 6. ปุ่มจัดการ/ติดตาม -->
                                    <td class="text-center align-middle">
                                        <a href="<?= base_url('admin/travel/' . $r['id']) ?>" class="btn btn-primary btn-sm px-2.5 py-1.5 shadow-sm fw-bold w-100" style="font-size: 0.78rem;">
                                            <i class="bx bx-search-alt me-1"></i> ไทม์ไลน์
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
