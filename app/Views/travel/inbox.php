<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>คำขอไปราชการรอฉันพิจารณาอนุมัติ<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-1 fw-bold text-warning">
                        <i class="bx bx-user-check me-2"></i> คำขอไปราชการรอฉันพิจารณา / ลงนามอนุมัติ (Inbox)
                    </h5>
                    <small class="text-muted">รายการขออนุมัติไปราชการที่ส่งถึงคุณตามสายงานเพื่อตรวจสอบ ให้ความเห็นชอบ หรืออนุมัติ</small>
                </div>
                <span class="badge bg-warning text-dark rounded-pill fs-6 fw-bold"><?= count($requests) ?> รายการ</span>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>เลขที่ขอไปราชการ</th>
                            <th>ผู้ขอไปราชการ</th>
                            <th>เรื่อง / สถานที่ไปราชการ</th>
                            <th>ช่วงวันที่</th>
                            <th>ขั้นตอน</th>
                            <th class="text-center">การพิจารณา</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($requests)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="bx bx-check-shield text-success display-4 d-block mb-3"></i>
                                    <h5 class="text-muted">ไม่มีคำขอไปราชการรอพิจารณาในขณะนี้</h5>
                                    <p class="text-muted small">เมื่อมีครูหรือบุคลากรยื่นขออนุมัติมาถึงขั้นตอนของคุณ รายการจะปรากฏที่นี่ทันที</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($requests as $r): ?>
                                <tr>
                                    <td><span class="badge bg-label-dark"><?= esc($r['doc_no']) ?></span></td>
                                    <td>
                                        <div class="fw-semibold text-dark"><?= esc($r['prefix'] . $r['first_name'] . ' ' . $r['last_name']) ?></div>
                                        <small class="text-muted"><?= esc($r['position_name']) ?></small>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('admin/travel/' . $r['id']) ?>" class="fw-semibold text-dark">
                                            <?= esc($r['title']) ?>
                                        </a>
                                        <small class="d-block text-muted">ณ <?= esc($r['destination']) ?> (<?= esc($r['province']) ?>)</small>
                                    </td>
                                    <td><?= thai_date($r['start_date'], 'short') ?> - <?= thai_date($r['end_date'], 'short') ?></td>
                                    <td><span class="badge bg-label-info">ขั้นที่ <?= esc($r['step_order']) ?></span></td>
                                    <td class="text-center">
                                        <a href="<?= base_url('admin/travel/' . $r['id']) ?>" class="btn btn-warning text-dark fw-semibold">
                                            <i class="bx bx-pencil me-1"></i> พิจารณา / ลงนาม
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
