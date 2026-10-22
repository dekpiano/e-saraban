<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>ศูนย์กลางงานเอกสาร<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <div class="text-muted small mb-1">E-SARABAN / DOCUMENT CENTER</div>
        <h3 class="mb-1 fw-bold"><i class="bx bx-folder-open text-primary me-2"></i>ศูนย์กลางงานเอกสาร</h3>
        <p class="text-muted mb-0">เข้าถึงเอกสารและงานอนุมัติจากจุดเดียว พร้อมรองรับการเพิ่มระบบใหม่ในอนาคต</p>
    </div>
    <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-outline-secondary"><i class="bx bx-arrow-back me-1"></i>กลับแดชบอร์ด</a>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-4"><div class="card h-100 border-0 shadow-sm"><div class="card-body d-flex align-items-center gap-3"><div class="avatar avatar-lg rounded bg-label-primary"><i class="bx bx-file fs-3"></i></div><div><div class="text-muted small">เอกสารในทะเบียน</div><div class="fs-3 fw-bold"><?= number_format($totalDocuments ?? 0) ?></div><a href="<?= base_url('admin/documents') ?>" class="small">เปิดทะเบียนเอกสาร <i class="bx bx-chevron-right"></i></a></div></div></div></div>
    <div class="col-sm-6 col-xl-4"><div class="card h-100 border-0 shadow-sm"><div class="card-body d-flex align-items-center gap-3"><div class="avatar avatar-lg rounded bg-label-info"><i class="bx bx-notepad fs-3"></i></div><div><div class="text-muted small">บันทึกข้อความภายใน</div><div class="fs-3 fw-bold"><?= number_format($totalMemos ?? 0) ?></div><a href="<?= base_url('admin/memos') ?>" class="small">เปิดบันทึกข้อความ <i class="bx bx-chevron-right"></i></a></div></div></div></div>
    <div class="col-sm-6 col-xl-4"><div class="card h-100 border-0 shadow-sm"><div class="card-body d-flex align-items-center gap-3"><div class="avatar avatar-lg rounded bg-label-success"><i class="bx bx-plane fs-3"></i></div><div><div class="text-muted small">คำขอไปราชการ</div><div class="fs-3 fw-bold"><?= number_format($totalTravel ?? 0) ?></div><a href="<?= base_url('admin/travel') ?>" class="small">เปิดทะเบียนไปราชการ <i class="bx bx-chevron-right"></i></a></div></div></div></div>
</div>

<div class="row g-3 mb-4">
    <div class="col-12"><div class="card border-0 shadow-sm"><div class="card-header bg-transparent"><h5 class="mb-0 fw-bold"><i class="bx bx-grid-alt text-primary me-2"></i>ระบบงาน</h5><small class="text-muted">แต่ละระบบทำงานแยกกัน แต่สามารถพัฒนาให้เชื่อมโยงเอกสารกันได้</small></div><div class="card-body"><div class="row g-3">
        <div class="col-md-6 col-xl-3"><a href="<?= base_url('admin/memos') ?>" class="card h-100 border text-reset text-decoration-none"><div class="card-body"><i class="bx bx-edit fs-2 text-primary"></i><h6 class="fw-bold mt-2 mb-1">บันทึกข้อความ</h6><p class="small text-muted mb-0">สร้างหนังสือภายใน เสนอเรื่อง และติดตามการลงนาม</p></div></a></div>
        <div class="col-md-6 col-xl-3"><a href="<?= base_url('admin/documents') ?>" class="card h-100 border text-reset text-decoration-none"><div class="card-body"><i class="bx bx-mail-send fs-2 text-info"></i><h6 class="fw-bold mt-2 mb-1">ทะเบียนเอกสาร</h6><p class="small text-muted mb-0">จัดการเอกสารและรายการรอดำเนินการ</p></div></a></div>
        <div class="col-md-6 col-xl-3"><a href="<?= base_url('admin/travel') ?>" class="card h-100 border text-reset text-decoration-none"><div class="card-body"><i class="bx bx-plane fs-2 text-success"></i><h6 class="fw-bold mt-2 mb-1">ขออนุมัติไปราชการ</h6><p class="small text-muted mb-0">ยื่นคำขอ ติดตามขั้นตอน และจัดการเอกสารประกอบ</p></div></a></div>
        <div class="col-md-6 col-xl-3"><div class="card h-100 border border-dashed bg-light"><div class="card-body"><i class="bx bx-plus-circle fs-2 text-secondary"></i><h6 class="fw-bold mt-2 mb-1">เพิ่มระบบในอนาคต</h6><p class="small text-muted mb-0">เช่น ขอใช้รถ ขอใช้ห้อง หรือขออนุมัติงบประมาณ</p><span class="badge bg-label-secondary">เตรียมรองรับ</span></div></div></div>
    </div></div></div></div>
</div>

<div class="row g-3">
    <div class="col-xl-7"><div class="card h-100 shadow-sm border-0"><div class="card-header d-flex justify-content-between align-items-center bg-transparent"><h5 class="mb-0 fw-bold"><i class="bx bx-history text-primary me-2"></i>เอกสารล่าสุด</h5><a href="<?= base_url('admin/documents') ?>" class="small">ดูทั้งหมด</a></div><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead><tr><th>ประเภท</th><th>เลขที่ / เรื่อง</th><th>ผู้สร้าง</th><th>สถานะ</th><th></th></tr></thead><tbody>
    <?php if (!empty($recentDocuments)): foreach ($recentDocuments as $item): ?>
        <tr><td><span class="badge bg-label-primary"><?= ($item['doc_type'] ?? '') === 'internal' ? 'บันทึกข้อความ' : 'เอกสาร' ?></span></td><td><div class="fw-semibold"><?= esc($item['doc_no'] ?? '-') ?></div><div class="small text-muted text-truncate" style="max-width:280px"><?= esc($item['title'] ?? '-') ?></div></td><td class="small"><?= esc(trim(($item['first_name'] ?? '') . ' ' . ($item['last_name'] ?? '')) ?: '-') ?></td><td><span class="badge bg-label-secondary"><?= esc($item['status'] ?? '-') ?></span></td><td><a class="btn btn-sm btn-outline-primary" href="<?= base_url(($item['doc_type'] ?? '') === 'internal' ? 'admin/memos/' . $item['id'] : 'admin/documents/' . $item['id']) ?>" aria-label="เปิดเอกสาร"><i class="bx bx-link-external"></i></a></td></tr>
    <?php endforeach; else: ?><tr><td colspan="5" class="text-center text-muted py-4">ยังไม่มีเอกสารในระบบ</td></tr><?php endif; ?>
    </tbody></table></div></div></div>
    <div class="col-xl-5"><div class="card h-100 shadow-sm border-0"><div class="card-header d-flex justify-content-between align-items-center bg-transparent"><h5 class="mb-0 fw-bold"><i class="bx bx-map-pin text-success me-2"></i>คำขอไปราชการล่าสุด</h5><a href="<?= base_url('admin/travel') ?>" class="small">ดูทั้งหมด</a></div><div class="card-body"><div class="list-group list-group-flush">
    <?php if (!empty($recentTravel)): foreach ($recentTravel as $item): ?>
        <a href="<?= base_url('admin/travel/' . $item['id']) ?>" class="list-group-item list-group-item-action px-0"><div class="d-flex justify-content-between gap-2"><div class="fw-semibold text-truncate"><?= esc($item['title'] ?? '-') ?></div><span class="badge bg-label-secondary flex-shrink-0"><?= esc($item['status'] ?? '-') ?></span></div><div class="small text-muted mt-1"><?= esc($item['doc_no'] ?? '-') ?> · <?= esc(trim(($item['first_name'] ?? '') . ' ' . ($item['last_name'] ?? '')) ?: '-') ?></div></a>
    <?php endforeach; else: ?><div class="text-muted text-center py-4">ยังไม่มีคำขอไปราชการ</div><?php endif; ?>
    </div></div></div></div>
</div>
<?= $this->endSection() ?>
