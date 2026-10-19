<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>หนังสือที่ฉันลงนามแล้ว<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-1 fw-bold text-success">
                        <i class="bx bx-check-double me-2"></i> ประวัติหนังสือที่ฉันลงนามแล้ว
                    </h5>
                    <small class="text-muted">รายการเอกสารทั้งหมดที่คุณได้ดำเนินการเกษียนและประทับลายเซ็นดิจิทัลแล้ว</small>
                </div>
                <span class="badge bg-success rounded-pill fs-6"><?= count($documents) ?> รายการ</span>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ประเภท</th>
                            <th>เลขที่หนังสือ</th>
                            <th>เรื่อง</th>
                            <th>ความเห็นเกษียนของฉัน</th>
                            <th>วันที่ลงนาม</th>
                            <th>สถานะเอกสารรวม</th>
                            <th class="text-center">ตรวจสอบ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($documents)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bx bx-history fs-1 d-block mb-2"></i>
                                    ยังไม่มีประวัติการลงนามเอกสาร
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
                                    <td><span class="badge bg-label-primary"><?= esc($doc['comment_preset'] ?: 'ลงนามแล้ว') ?></span></td>
                                    <td><?= thai_date($doc['signed_at'], 'short', true) ?></td>
                                    <td><?= doc_status_badge($doc['status']) ?></td>
                                    <td class="text-center">
                                        <a href="<?= base_url('admin/documents/' . $doc['id']) ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="bx bx-show me-1"></i> ดูรายละเอียด
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
