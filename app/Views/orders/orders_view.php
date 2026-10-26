<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>คำสั่งโรงเรียนเลขที่ <?= esc($order['doc_no']) ?> | e-Saraban<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- External Stylesheet for Order Paper Rendering -->
<link rel="stylesheet" href="<?= base_url('assets/css/orders_saraban.css?v=' . time()) ?>" />

<div class="container-xxl flex-grow-1 container-p-y">

    <!-- Top Breadcrumb & Action Bar -->
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/orders') ?>" class="text-primary fw-semibold">ทะเบียนคำสั่งโรงเรียน</a></li>
                    <li class="breadcrumb-item active text-dark fw-bold" aria-current="page"><?= esc($order['doc_no']) ?></li>
                </ol>
            </nav>
            <h4 class="mb-0 fw-bold text-dark">
                <?= esc($order['title']) ?>
            </h4>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="<?= base_url('admin/orders/' . $order['id'] . '/print') ?>" target="_blank" class="btn btn-dark btn-sm fw-bold px-3 shadow-xs">
                <i class="bx bx-printer me-1"></i> พิมพ์คำสั่ง (A4 1:1)
            </a>
            <a href="<?= base_url('admin/orders/' . $order['id'] . '/edit') ?>" class="btn btn-outline-warning btn-sm fw-bold px-3">
                <i class="bx bx-edit me-1"></i> แก้ไขคำสั่ง
            </a>
            <a href="<?= base_url('admin/orders') ?>" class="btn btn-outline-secondary btn-sm fw-semibold">
                <i class="bx bx-arrow-back me-1"></i> กลับหน้ารวม
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
            <i class="bx bx-check-circle fs-4 me-2"></i>
            <div class="fw-bold"><?= esc(session()->getFlashdata('success')) ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
            <i class="bx bx-error fs-4 me-2"></i>
            <div class="fw-bold"><?= esc(session()->getFlashdata('error')) ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        
        <!-- Left Column: Order Meta Details, Sign & Upload Actions -->
        <div class="col-lg-4 col-md-5">

            <!-- Order Status & Summary Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light border-bottom py-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="badge bg-dark text-white fw-bold px-2.5 py-1">
                            <?= esc($order['doc_no']) ?>
                        </span>
                        <?php if ($order['status'] === 'published'): ?>
                            <span class="badge bg-success text-white fw-bold px-2.5 py-1">
                                <i class="bx bx-check-circle me-1"></i> มีผลบังคับใช้
                            </span>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark fw-bold px-2.5 py-1">
                                <i class="bx bx-time me-1"></i> ฉบับร่าง
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">เรื่องคำสั่ง:</small>
                        <h6 class="fw-bold text-dark mb-0"><?= esc($order['title']) ?></h6>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">หมวดหมู่งาน:</small>
                        <span class="badge bg-label-info fw-bold"><?= esc($order['order_category'] ?: 'บริหารทั่วไป') ?></span>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">วันสั่งการ:</small>
                        <span class="fw-semibold text-dark">
                            <i class="bx bx-calendar text-primary me-1"></i><?= !empty($order['doc_date']) ? esc(thai_date_full($order['doc_date'])) : '-' ?>
                        </span>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">ผู้มีอำนาจสั่งการ:</small>
                        <div class="fw-bold text-dark"><?= esc($order['signer_name'] ?: 'ผู้อำนวยการโรงเรียน') ?></div>
                        <div class="small text-secondary"><?= esc($order['signer_position'] ?: 'ผู้อำนวยการสถานศึกษา') ?></div>
                    </div>
                    <div>
                        <small class="text-muted d-block mb-1">ผู้บันทึกข้อมูลเข้าระบบ:</small>
                        <div class="small text-dark">
                            <i class="bx bx-user me-1 text-primary"></i>
                            <?= esc(($order['prefix'] ?? '') . ($order['first_name'] ?? '') . ' ' . ($order['last_name'] ?? '')) ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PDF Attachment & Upload Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light border-bottom py-3 d-flex align-items-center justify-content-between">
                    <span class="fw-bold text-dark"><i class="bx bxs-file-pdf text-danger me-1"></i> เอกสารคำสั่ง (PDF)</span>
                    <button type="button" class="btn btn-outline-primary btn-xs fw-semibold" data-bs-toggle="modal" data-bs-target="#uploadOrderModal">
                        <i class="bx bx-upload me-1"></i> <?= empty($order['file_path']) ? 'แนบไฟล์ PDF' : 'เปลี่ยนไฟล์' ?>
                    </button>
                </div>
                <div class="card-body p-3 text-center">
                    <?php if (!empty($order['file_path'])): ?>
                        <div class="p-3 bg-light rounded-3 border mb-3">
                            <i class="bx bxs-file-pdf fs-1 text-danger d-block mb-1"></i>
                            <span class="fw-bold text-dark small d-block mb-1">มีไฟล์สแกนคำสั่งฉบับสมบูรณ์</span>
                            <small class="text-muted">สามารถดาวน์โหลดเพื่อนำไปแนบในการขอไปราชการได้</small>
                        </div>
                        <a href="<?= base_url($order['file_path']) ?>" target="_blank" class="btn btn-danger btn-sm w-100 fw-bold py-2 shadow-xs">
                            <i class="bx bx-download me-1"></i> เปิด / ดาวน์โหลดไฟล์ PDF คำสั่ง
                        </a>
                    <?php else: ?>
                        <div class="p-4 bg-light rounded-3 text-muted">
                            <i class="bx bx-file-blank fs-2 d-block mb-1 text-secondary"></i>
                            <small>ยังไม่ได้แนบไฟล์คำสั่งสแกนฉบับจริง (PDF)<br>ท่านสามารถพิมพ์จากระบบหรือแนบไฟล์ได้ตลอดเวลา</small>
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm w-100 mt-3" data-bs-toggle="modal" data-bs-target="#uploadOrderModal">
                            <i class="bx bx-cloud-upload me-1"></i> แนบไฟล์ PDF ตอนนี้
                        </button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- 1-Click Executive Digital Signature Card -->
            <?php if (!empty($currentUser['can_sign_final'])): ?>
                <div class="card border-primary border-2 shadow-sm mb-4">
                    <div class="card-body p-3">
                        <h6 class="fw-bold text-primary mb-2">
                            <i class="bx bx-pen me-1"></i> สำหรับผู้อำนวยการสถานศึกษา
                        </h6>
                        <p class="small text-secondary mb-3">
                            ท่านมีสิทธิ์ลงนามคำสั่งโรงเรียน สามารถกดลงนามดิจิทัล 1-Click เพื่อประทับลายเซ็นลงบนคำสั่งและสั่งการทันที
                        </p>
                        <?php if (!empty($order['creator_signature'])): ?>
                            <div class="p-3 bg-success-subtle border border-success-subtle rounded text-center mb-3">
                                <span class="badge bg-success text-white fw-bold mb-2">ลงนามดิจิทัลแล้ว</span>
                                <div class="mb-2">
                                    <img src="<?= base_url(esc($order['creator_signature'])) ?>" alt="ลายเซ็น" style="max-height: 52px;" />
                                </div>
                                <div class="small fw-bold text-dark"><?= esc($order['signer_name']) ?></div>
                                <div class="small text-secondary" style="font-size: 0.8rem;"><?= esc($order['signer_position']) ?></div>
                            </div>
                            <form action="<?= base_url('admin/orders/' . $order['id'] . '/sign') ?>" method="post" id="signOrderForm">
                                <?= csrf_field() ?>
                                <button type="button" class="btn btn-outline-primary btn-sm w-100 fw-bold" id="btnSignOrder">
                                    <i class="bx bx-refresh me-1"></i> อัปเดตลงนามด้วยชื่อ/ตำแหน่งปัจจุบันของฉัน
                                </button>
                            </form>
                        <?php else: ?>
                            <form action="<?= base_url('admin/orders/' . $order['id'] . '/sign') ?>" method="post" id="signOrderForm">
                                <?= csrf_field() ?>
                                <button type="button" class="btn btn-primary w-100 fw-bold py-2 shadow-sm" id="btnSignOrder">
                                    <i class="bx bx-check-shield me-1"></i> ลงนามคำสั่งดิจิทัล (1-Click Sign)
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Delete Order Button -->
            <div class="text-center">
                <form action="<?= base_url('admin/orders/' . $order['id'] . '/delete') ?>" method="post" onsubmit="return confirm('คำเตือน: คุณต้องการลบคำสั่งโรงเรียนฉบับนี้ใช่หรือไม่? การกระทำนี้ไม่สามารถย้อนกลับได้');">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-outline-danger btn-sm">
                        <i class="bx bx-trash me-1"></i> ลบคำสั่งโรงเรียนฉบับนี้
                    </button>
                </form>
            </div>

        </div>

        <!-- Right Column: Interactive Embedded A4 Paper Preview -->
        <div class="col-lg-8 col-md-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-bottom py-3 d-flex align-items-center justify-content-between">
                    <span class="fw-bold text-dark"><i class="bx bx-show me-1 text-primary"></i> ตัวอย่างเอกสารคำสั่ง A4 มาตรฐาน (1:1 พรีวิว)</span>
                    <a href="<?= base_url('admin/orders/' . $order['id'] . '/print') ?>" target="_blank" class="btn btn-dark btn-xs fw-bold px-2.5">
                        <i class="bx bx-printer me-1"></i> พิมพ์เอกสาร
                    </a>
                </div>
                <div class="card-body p-2 p-md-4" style="background-color: #525659; overflow-x: auto;">
                    
                    <!-- Paper Form Content Rendering -->
                    <div class="saraban-order-paper shadow" id="previewOrderPaper" style="margin: 0 auto; min-height: 27cm;">
                        
                        <!-- ตราครุฑ กึ่งกลาง 3.0 ซม. -->
                        <div class="order-garuda-center">
                            <img src="<?= base_url('assets/img/krut_thai.png') ?>" alt="ตราครุฑ" class="order-garuda-img" />
                        </div>

                        <!-- หัวคำสั่งโรงเรียน -->
                        <div class="order-header-block">
                            <div class="order-school-title">
                                คำสั่งโรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์
                            </div>
                            <div class="order-number-title">
                                ที่ <?= esc($order['doc_no']) ?>
                            </div>
                            <div class="order-subject-title">
                                เรื่อง <?= esc($order['title']) ?>
                            </div>
                            <div class="order-divider-line"></div>
                        </div>

                        <!-- เนื้อความคำสั่ง -->
                        <div class="order-body-content">
                            <?= !empty($order['content']) ? $order['content'] : '<p class="text-muted">ยังไม่ได้ระบุเนื้อหาคำสั่ง</p>' ?>
                        </div>

                        <!-- ย่อหน้าบังคับใช้ -->
                        <?php if (!empty($order['order_effective_text'])): ?>
                            <div class="order-effective-clause">
                                ทั้งนี้ ตั้งแต่ <?= esc($order['order_effective_text']) ?>
                            </div>
                        <?php endif; ?>

                        <!-- วันสั่งการ -->
                        <div class="order-date-clause">
                            สั่ง ณ วันที่ <?= !empty($order['doc_date']) ? esc(thai_date_full($order['doc_date'])) : '-' ?>
                        </div>

                        <!-- ส่วนลงนาม -->
                        <div class="order-signature-container">
                            <div class="order-signature-image-wrap">
                                <?php if (!empty($order['creator_signature'])): ?>
                                    <img src="<?= base_url(esc($order['creator_signature'])) ?>" alt="ลายเซ็น" class="order-signature-img" />
                                <?php endif; ?>
                            </div>
                            <div class="order-signer-fullname">
                                ( <?= esc($order['signer_name'] ?: 'นายพงษ์ศักดิ์ เงินสันเทียะ') ?> )
                            </div>
                            <div class="order-signer-position">
                                <?= esc($order['signer_position'] ?: 'ผู้อำนวยการโรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์') ?>
                            </div>
                        </div>

                    </div>

                </div>
            </div>

            <!-- Audit Trail / Log History -->
            <?php if (!empty($logs)): ?>
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-header bg-light border-bottom py-3">
                        <span class="fw-bold text-dark small"><i class="bx bx-history me-1 text-primary"></i> ประวัติการดำเนินงาน (Audit Logs)</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive text-nowrap">
                            <table class="table table-sm table-striped mb-0 small">
                                <thead>
                                    <tr>
                                        <th>วัน-เวลา</th>
                                        <th>ผู้ดำเนินการ</th>
                                        <th>กิจกรรม</th>
                                        <th>รายละเอียด</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($logs as $l): ?>
                                        <tr>
                                            <td><?= esc($l['created_at']) ?></td>
                                            <td class="fw-bold"><?= esc(($l['first_name'] ?? '') . ' ' . ($l['last_name'] ?? '')) ?></td>
                                            <td><span class="badge bg-label-primary"><?= esc($l['action']) ?></span></td>
                                            <td class="text-wrap"><?= esc($l['detail']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </div>

    </div>

</div>

<!-- Upload PDF Modal -->
<div class="modal fade" id="uploadOrderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="<?= base_url('admin/orders/' . $order['id'] . '/upload') ?>" method="post" enctype="multipart/form-data" class="modal-content">
            <?= csrf_field() ?>
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-dark"><i class="bx bxs-file-pdf text-danger me-1"></i> อัปโหลดไฟล์คำสั่งฉบับจริง (PDF)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label required fw-bold text-dark">เลือกไฟล์เอกสารคำสั่ง (PDF เท่านั้น):</label>
                    <input type="file" name="order_file" class="form-control" accept=".pdf" required />
                    <small class="text-muted mt-1 d-block">ไฟล์นี้จะถูกจัดเก็บในคลังคำสั่งเพื่อให้ครูและบุคลากรดาวน์โหลดไปแนบขอไปราชการ</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="submit" class="btn btn-primary btn-sm fw-bold">อัปโหลดไฟล์</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btnSign = document.getElementById('btnSignOrder');
    const signForm = document.getElementById('signOrderForm');

    if (btnSign && signForm) {
        btnSign.addEventListener('click', function (e) {
            e.preventDefault();

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'ยืนยันการลงนามดิจิทัล?',
                    text: 'คำสั่งโรงเรียนนี้จะได้รับการลงนามและมีผลบังคับใช้เป็นทางการทันที',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#4f46e5',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: '<i class="bx bx-check me-1"></i> ยืนยันลงนามทันที',
                    cancelButtonText: 'ยกเลิก',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        signForm.submit();
                    }
                });
            } else {
                if (confirm('ยืนยันการลงนามดิจิทัลคำสั่งโรงเรียนฉบับนี้หรือไม่?')) {
                    signForm.submit();
                }
            }
        });
    }

    // เรียกใช้ระบบแบ่งแผ่นกระดาษ A4 เสมือนจริง
    if (typeof SarabanOrderPaginator !== 'undefined') {
        SarabanOrderPaginator.paginate('previewOrderPaper');
    }
});
</script>
<script src="<?= base_url('assets/js/orders_pagination.js?v=' . time()) ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    setTimeout(function() {
        if (typeof SarabanOrderPaginator !== 'undefined') {
            SarabanOrderPaginator.paginate('previewOrderPaper');
        }
    }, 100);
});
</script>
<?= $this->endSection() ?>

