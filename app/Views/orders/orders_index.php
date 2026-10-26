<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>ทะเบียนคำสั่งโรงเรียน | e-Saraban<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-xxl flex-grow-1 container-p-y">

    <!-- Page Header Banner -->
    <div class="card bg-gradient-primary text-white border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);">
        <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar avatar-lg bg-white rounded-circle p-2 shadow-sm d-flex align-items-center justify-content-center">
                        <i class="bx bxs-file-doc fs-2 text-primary"></i>
                    </div>
                    <div>
                        <span class="badge bg-white text-dark fw-bold px-2.5 py-1 mb-1 shadow-xs">
                            <i class="bx bx-award me-1 text-warning"></i> ระบบทะเบียนคำสั่งทางการ
                        </span>
                        <h3 class="text-white fw-bold mb-1">ทะเบียนคำสั่งโรงเรียน</h3>
                        <p class="text-white-50 mb-0 small">
                            คลังทะเบียนคำสั่ง ร่างคำสั่งทางการ และจัดเก็บไฟล์คำสั่งโรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์
                        </p>
                    </div>
                </div>
                <?php if (session()->get('role') !== 'executive'): ?>
                <div>
                    <a href="<?= base_url('admin/orders/create') ?>" class="btn btn-warning text-dark fw-bold px-3 py-2 shadow-sm">
                        <i class="bx bx-plus-circle me-1"></i> ร่างคำสั่งโรงเรียนใหม่
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Alert Success/Error -->
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

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-secondary small fw-semibold">คำสั่งทั้งหมด (ปี <?= esc($selectedYear) ?>)</span>
                        <div class="avatar avatar-sm bg-label-primary rounded">
                            <i class="bx bx-file text-primary"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-0 text-dark"><?= number_format($stats['total']) ?></h3>
                    <small class="text-muted">ฉบับในสมุดทะเบียน</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-secondary small fw-semibold">มีผลบังคับใช้ / เผยแพร่</span>
                        <div class="avatar avatar-sm bg-label-success rounded">
                            <i class="bx bx-check-shield text-success"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-0 text-success"><?= number_format($stats['published']) ?></h3>
                    <small class="text-muted">พร้อมใช้งาน/อ้างอิง</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-secondary small fw-semibold">มีไฟล์สแกน (PDF)</span>
                        <div class="avatar avatar-sm bg-label-danger rounded">
                            <i class="bx bxs-file-pdf text-danger"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-0 text-danger"><?= number_format($stats['with_file']) ?></h3>
                    <small class="text-muted">ดาวน์โหลดแนบไปราชการได้</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-secondary small fw-semibold">แบบร่าง (Draft)</span>
                        <div class="avatar avatar-sm bg-label-warning rounded">
                            <i class="bx bx-edit text-warning"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-0 text-dark"><?= number_format($stats['draft']) ?></h3>
                    <small class="text-muted">รอดำเนินการลงนาม</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form action="<?= base_url('admin/orders') ?>" method="get" class="row g-2 align-items-center">
                
                <!-- เลือกปี พ.ศ. -->
                <div class="col-md-2 col-6">
                    <label class="form-label small text-secondary fw-bold mb-1">ปี พ.ศ.</label>
                    <select name="year" class="form-select form-select-sm" onchange="this.form.submit()">
                        <?php foreach ($yearsList as $yr): ?>
                            <option value="<?= esc($yr) ?>" <?= (int)$selectedYear === (int)$yr ? 'selected' : '' ?>>
                                พ.ศ. <?= esc($yr) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- หมวดหมู่คำสั่ง -->
                <div class="col-md-2 col-6">
                    <label class="form-label small text-secondary fw-bold mb-1">หมวดหมู่งาน</label>
                    <select name="category" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">-- ทุกหมวดหมู่ --</option>
                        <option value="บริหารทั่วไป" <?= $category === 'บริหารทั่วไป' ? 'selected' : '' ?>>บริหารทั่วไป</option>
                        <option value="วิชาการ" <?= $category === 'วิชาการ' ? 'selected' : '' ?>>วิชาการ</option>
                        <option value="กิจการนักเรียน" <?= $category === 'กิจการนักเรียน' ? 'selected' : '' ?>>กิจการนักเรียน</option>
                        <option value="บุคคลและงบประมาณ" <?= $category === 'บุคคลและงบประมาณ' ? 'selected' : '' ?>>บุคคลและงบประมาณ</option>
                    </select>
                </div>

                <!-- สถานะ -->
                <div class="col-md-2 col-6">
                    <label class="form-label small text-secondary fw-bold mb-1">สถานะคำสั่ง</label>
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">-- ทุกสถานะ --</option>
                        <option value="published" <?= $status === 'published' ? 'selected' : '' ?>>เผยแพร่แล้ว</option>
                        <option value="draft" <?= $status === 'draft' ? 'selected' : '' ?>>แบบร่าง</option>
                    </select>
                </div>

                <!-- ค้นหาคำสำคัญ -->
                <div class="col-md-4 col-6">
                    <label class="form-label small text-secondary fw-bold mb-1">ค้นหาเลขที่ / เรื่องคำสั่ง</label>
                    <div class="input-group input-group-sm">
                        <input type="text" name="search" class="form-control" placeholder="พิมพ์เลขที่, ชื่อเรื่อง หรือผู้ลงนาม..." value="<?= esc($search) ?>" />
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-search"></i>
                        </button>
                    </div>
                </div>

                <!-- ล้างตัวกรอง -->
                <div class="col-md-2 col-12 d-flex align-items-end mt-md-4">
                    <a href="<?= base_url('admin/orders') ?>" class="btn btn-outline-secondary btn-sm w-100">
                        <i class="bx bx-reset me-1"></i> รีเซ็ต
                    </a>
                </div>

            </form>
        </div>
    </div>

    <!-- Orders Data Table -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 130px;">เลขที่คำสั่ง</th>
                        <th>เรื่องคำสั่งโรงเรียน</th>
                        <th style="width: 140px;">วันสั่งการ</th>
                        <th style="width: 180px;">ผู้มีอำนาจสั่งการ</th>
                        <th style="width: 100px;" class="text-center">ไฟล์แนบ</th>
                        <th style="width: 110px;" class="text-center">สถานะ</th>
                        <th style="width: 130px;" class="text-center">การจัดการ</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bx bx-folder-open fs-1 d-block mb-2 text-secondary"></i>
                                <span class="fw-bold">ไม่พบข้อมูลคำสั่งโรงเรียนในเงื่อนไขที่เลือก</span>
                                <div class="small mt-1">ท่านสามารถกดปุ่ม "ออกคำสั่งโรงเรียนใหม่" เพื่อเริ่มสร้างคำสั่งแรกของปี พ.ศ. <?= esc($selectedYear) ?></div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $o): ?>
                            <tr>
                                <td>
                                    <span class="badge bg-dark text-white fw-bold px-2 py-1 fs-7">
                                        <?= esc($o['doc_no']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <a href="<?= base_url('admin/orders/' . $o['id']) ?>" class="fw-bold text-dark text-decoration-none hover-primary mb-1 text-wrap" style="max-width: 480px;">
                                            <?= esc($o['title']) ?>
                                        </a>
                                        <div class="small text-secondary d-flex align-items-center gap-2">
                                            <?php if (!empty($o['order_category'])): ?>
                                                <span class="badge bg-label-info"><?= esc($o['order_category']) ?></span>
                                            <?php endif; ?>
                                            <span><i class="bx bx-user text-primary me-1"></i><?= esc(($o['prefix'] ?? '') . ($o['first_name'] ?? '') . ' ' . ($o['last_name'] ?? '')) ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-dark small fw-semibold">
                                        <?= !empty($o['doc_date']) ? esc(thai_date_full($o['doc_date'])) : '-' ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="small">
                                        <div class="fw-bold text-dark"><?= esc($o['signer_name'] ?: 'ผู้อำนวยการโรงเรียน') ?></div>
                                        <div class="text-muted fs-8"><?= esc($o['signer_position'] ?: 'ผู้อำนวยการสถานศึกษา') ?></div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <?php if (!empty($o['file_path'])): ?>
                                        <a href="<?= document_url($o['file_path']) ?>" target="_blank" class="badge bg-danger text-white fw-bold px-2.5 py-1.5 shadow-xs" title="เปิดดูเอกสาร PDF คำสั่งฉบับจริง">
                                            <i class="bx bxs-file-pdf me-1"></i> PDF
                                        </a>
                                    <?php else: ?>
                                        <span class="badge bg-secondary text-white fw-normal px-2 py-1 fs-8" title="ยังไม่มีไฟล์สแกนแนบ">
                                            ฉบับร่าง
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($o['status'] === 'published'): ?>
                                        <span class="badge bg-success text-white fw-bold px-2.5 py-1 shadow-xs">
                                            <i class="bx bx-check-circle me-1"></i> มีผลบังคับใช้
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark fw-bold px-2.5 py-1 shadow-xs">
                                            <i class="bx bx-time me-1"></i> แบบร่าง
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="d-inline-flex gap-1">
                                        <a href="<?= base_url('admin/orders/' . $o['id']) ?>" class="btn btn-sm btn-icon btn-outline-primary" title="ดูรายละเอียด">
                                            <i class="bx bx-show"></i>
                                        </a>
                                        <a href="<?= base_url('admin/orders/' . $o['id'] . '/print') ?>" target="_blank" class="btn btn-sm btn-icon btn-outline-dark" title="พิมพ์คำสั่ง A4">
                                            <i class="bx bx-printer"></i>
                                        </a>
                                        <a href="<?= base_url('admin/orders/' . $o['id'] . '/edit') ?>" class="btn btn-sm btn-icon btn-outline-warning" title="แก้ไข">
                                            <i class="bx bx-edit"></i>
                                        </a>
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
<?= $this->endSection() ?>
