<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>ทะเบียนหนังสือราชการ<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h5 class="card-title mb-1 fw-bold text-primary">
                        <i class="bx bx-folder me-2"></i> ทะเบียนหนังสือราชการสารบรรณ
                    </h5>
                    <small class="text-muted">คลังทะเบียนรับ-ส่ง บันทึกข้อความ และคำสั่งโรงเรียน</small>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?= base_url('admin/documents/create') ?>" class="btn btn-primary">
                        <i class="bx bx-plus me-1"></i> ออกเลข & ลงทะเบียนหนังสือ
                    </a>
                </div>
            </div>

            <!-- Filter Tabs -->
            <div class="card-body pb-0">
                <ul class="nav nav-pills flex-column flex-md-row mb-3">
                    <li class="nav-item">
                        <a class="nav-link <?= empty($currentType) ? 'active' : '' ?>" href="<?= base_url('admin/documents') ?>">
                            <i class="bx bx-grid-alt me-1"></i> ทั้งหมด
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentType === 'in' ? 'active' : '' ?>" href="<?= base_url('admin/documents?type=in') ?>">
                            <i class="bx bx-log-in-circle me-1"></i> หนังสือรับ
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentType === 'out' ? 'active' : '' ?>" href="<?= base_url('admin/documents?type=out') ?>">
                            <i class="bx bx-log-out-circle me-1"></i> หนังสือส่ง
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentType === 'internal' ? 'active' : '' ?>" href="<?= base_url('admin/documents?type=internal') ?>">
                            <i class="bx bx-message-detail me-1"></i> บันทึกข้อความ
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentType === 'order' ? 'active' : '' ?>" href="<?= base_url('admin/documents?type=order') ?>">
                            <i class="bx bx-award me-1"></i> คำสั่งโรงเรียน
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentType === 'circular' ? 'active' : '' ?>" href="<?= base_url('admin/documents?type=circular') ?>">
                            <i class="bx bx-broadcast me-1"></i> หนังสือเวียน
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Documents Table -->
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>เลขทะเบียน รร.</th>
                            <th>ประเภท</th>
                            <th>ความเร่งด่วน</th>
                            <th>เลขที่หนังสือ</th>
                            <th>เรื่อง</th>
                            <th>จาก / ถึง</th>
                            <th>ลงวันที่</th>
                            <th>สถานะการเดินแฟ้ม</th>
                            <th class="text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        <?php if (empty($documents)): ?>
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="bx bx-file-blank fs-1 text-secondary d-block mb-2"></i>
                                    ไม่พบรายการหนังสือในหมวดหมู่นี้
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($documents as $doc): ?>
                                <tr>
                                    <td><span class="badge bg-label-dark"><?= esc($doc['internal_reg_no'] ?: '-') ?></span></td>
                                    <td><?= doc_type_badge($doc['doc_type']) ?></td>
                                    <td><?= urgency_badge($doc['urgency']) ?></td>
                                    <td><strong><?= esc($doc['doc_no']) ?></strong></td>
                                    <td>
                                        <a href="<?= base_url('admin/documents/' . $doc['id']) ?>" class="fw-semibold text-dark text-decoration-none">
                                            <?= esc($doc['title']) ?>
                                        </a>
                                    </td>
                                    <td>
                                        <small class="d-block text-muted">จาก: <?= esc($doc['from_org']) ?></small>
                                        <small class="d-block text-muted">ถึง: <?= esc($doc['to_org']) ?></small>
                                    </td>
                                    <td><?= thai_date($doc['doc_date'], 'short') ?></td>
                                    <td><?= doc_status_badge($doc['status']) ?></td>
                                    <td class="text-center">
                                        <a href="<?= base_url('admin/documents/' . $doc['id']) ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="bx bx-folder-open me-1"></i> ดูเรื่อง
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
