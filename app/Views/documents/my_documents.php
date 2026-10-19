<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>หนังสือที่ฉันเป็นผู้สร้าง<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-1 fw-bold text-info">
                        <i class="bx bx-send me-2"></i> หนังสือที่ฉันเป็นผู้สร้าง / เสนอเรื่อง
                    </h5>
                    <small class="text-muted">ติดตามสถานะความคืบหน้าของเอกสารที่คุณส่งเสนอเข้าสู่ระบบ</small>
                </div>
                <a href="<?= base_url('admin/documents/create') ?>" class="btn btn-primary btn-sm">
                    <i class="bx bx-plus me-1"></i> เสนอเรื่องใหม่
                </a>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ประเภท</th>
                            <th>เลขที่หนังสือ</th>
                            <th>เรื่อง</th>
                            <th>วันที่สร้าง</th>
                            <th>สถานะการเดินแฟ้ม</th>
                            <th>ขั้นตอนปัจจุบัน</th>
                            <th class="text-center">ดูความคืบหน้า</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($documents)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bx bx-file-blank fs-1 d-block mb-2"></i>
                                    คุณยังไม่ได้สร้างรายการหนังสือใดๆ
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($documents as $doc): ?>
                                <tr>
                                    <td><?= doc_type_badge($doc['doc_type']) ?></td>
                                    <td><strong><?= esc($doc['doc_no']) ?></strong></td>
                                    <td>
                                        <a href="<?= base_url('admin/documents/' . $doc['id']) ?>" class="fw-semibold text-dark">
                                            <?= esc($doc['title']) ?>
                                        </a>
                                    </td>
                                    <td><?= thai_date($doc['created_at'], 'short', true) ?></td>
                                    <td><?= doc_status_badge($doc['status']) ?></td>
                                    <td><span class="badge bg-label-info">ขั้นที่ <?= esc($doc['current_step']) ?></span></td>
                                    <td class="text-center">
                                        <a href="<?= base_url('admin/documents/' . $doc['id']) ?>" class="btn btn-sm btn-outline-info">
                                            <i class="bx bx-time-five me-1"></i> ติดตาม Timeline
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
