<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>หนังสือรอฉันลงนาม/เกษียนเรื่อง<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-1 fw-bold text-warning">
                        <i class="bx bx-edit-alt me-2"></i> หนังสือรอฉันลงนาม / เสนอเกษียน (Inbox)
                    </h5>
                    <small class="text-muted">รายการเอกสารที่ส่งถึงคุณตามเส้นทางการเดินแฟ้มและรอการอนุมัติ/ลงนาม</small>
                </div>
                <span class="badge bg-warning text-dark rounded-pill fs-6 fw-bold"><?= count($documents) ?> รายการ</span>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ความเร่งด่วน</th>
                            <th>ประเภท</th>
                            <th>เลขที่หนังสือ</th>
                            <th>เรื่อง</th>
                            <th>ผู้เสนอเรื่อง</th>
                            <th>วันที่รับเข้า</th>
                            <th>ขั้นตอนที่</th>
                            <th class="text-center">การดำเนินการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($documents)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="bx bx-check-shield text-success display-4 d-block mb-3"></i>
                                    <h5 class="text-muted">ไม่มีหนังสือรอลงนามในขณะนี้</h5>
                                    <p class="text-muted small">เมื่อมีหนังสือส่งมาถึงคุณตามสายงาน เอกสารจะปรากฏในหน้านี้ทันที</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($documents as $doc): ?>
                                <tr>
                                    <td><?= urgency_badge($doc['urgency']) ?></td>
                                    <td><?= doc_type_badge($doc['doc_type']) ?></td>
                                    <td><strong><?= esc($doc['doc_no']) ?></strong></td>
                                    <td>
                                        <a href="<?= base_url('admin/documents/' . $doc['id']) ?>" class="fw-semibold text-dark">
                                            <?= esc($doc['title']) ?>
                                        </a>
                                        <div class="small text-muted">จาก: <?= esc($doc['from_org']) ?></div>
                                    </td>
                                    <td><?= esc(($doc['creator_first'] ?? '') . ' ' . ($doc['creator_last'] ?? '')) ?></td>
                                    <td><?= thai_date($doc['receive_date'], 'short') ?></td>
                                    <td><span class="badge bg-label-info">ขั้นที่ <?= esc($doc['step_order']) ?></span></td>
                                    <td class="text-center">
                                        <a href="<?= base_url('admin/documents/' . $doc['id']) ?>" class="btn btn-warning text-dark fw-semibold">
                                            <i class="bx bx-pencil me-1"></i> เกษียน / ลงนาม
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
