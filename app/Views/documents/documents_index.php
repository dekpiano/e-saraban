<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>ทะเบียนหนังสือราชการ<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <!-- Header Banner (Purple Sneat Template Theme) -->
        <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #696cff 0%, #5f61e6 50%, #4338ca 100%); border-radius: 1rem; position: relative; overflow: hidden;">
            <div class="card-body p-4 text-white">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar avatar-lg bg-white rounded-circle p-2 shadow-sm d-flex align-items-center justify-content-center">
                            <i class="bx bx-folder-open fs-2 text-primary"></i>
                        </div>
                        <div>
                            <span class="badge bg-white text-dark fw-bold px-2.5 py-1 mb-1 shadow-sm">
                                <i class="bx bx-book-content me-1 text-primary"></i> สารบรรณกลางโรงเรียน
                            </span>
                            <h3 class="text-white fw-bold mb-1">ทะเบียนหนังสือราชการสารบรรณ</h3>
                            <p class="text-white opacity-90 mb-0 small">
                                คลังทะเบียนหนังสือรับ หนังสือส่ง บันทึกข้อความ และคำสั่งโรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์
                            </p>
                        </div>
                    </div>
                    <?php if (in_array(session()->get('role'), ['super_admin', 'admin', 'saraban'], true)): ?>
                    <div class="d-flex gap-2">
                        <a href="<?= base_url('admin/documents/create') ?>" class="btn btn-warning text-dark fw-bold px-3 py-2 shadow-sm">
                            <i class="bx bx-plus-circle me-1"></i> ออกเลข & ลงทะเบียนหนังสือ
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Filter Tabs & Search Bar -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-3">
                <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                    <!-- Filter Pills -->
                    <ul class="nav nav-pills flex-wrap gap-1 mb-0">
                        <li class="nav-item">
                            <a class="nav-link <?= empty($currentType) ? 'active fw-bold' : 'text-secondary' ?> py-1.5 px-3" href="<?= base_url('admin/documents' . (!empty($search) ? '?search=' . urlencode($search) : '')) ?>">
                                <i class="bx bx-grid-alt me-1"></i> ทั้งหมด
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $currentType === 'in' ? 'active fw-bold' : 'text-secondary' ?> py-1.5 px-3" href="<?= base_url('admin/documents?type=in' . (!empty($search) ? '&search=' . urlencode($search) : '')) ?>">
                                <i class="bx bx-log-in-circle me-1"></i> หนังสือรับ
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $currentType === 'out' ? 'active fw-bold' : 'text-secondary' ?> py-1.5 px-3" href="<?= base_url('admin/documents?type=out' . (!empty($search) ? '&search=' . urlencode($search) : '')) ?>">
                                <i class="bx bx-log-out-circle me-1"></i> หนังสือส่ง
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $currentType === 'internal' ? 'active fw-bold' : 'text-secondary' ?> py-1.5 px-3" href="<?= base_url('admin/documents?type=internal' . (!empty($search) ? '&search=' . urlencode($search) : '')) ?>">
                                <i class="bx bx-message-detail me-1"></i> บันทึกข้อความ
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $currentType === 'order' ? 'active fw-bold' : 'text-secondary' ?> py-1.5 px-3" href="<?= base_url('admin/documents?type=order' . (!empty($search) ? '&search=' . urlencode($search) : '')) ?>">
                                <i class="bx bx-award me-1"></i> คำสั่งโรงเรียน
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $currentType === 'circular' ? 'active fw-bold' : 'text-secondary' ?> py-1.5 px-3" href="<?= base_url('admin/documents?type=circular' . (!empty($search) ? '&search=' . urlencode($search) : '')) ?>">
                                <i class="bx bx-broadcast me-1"></i> หนังสือเวียน
                            </a>
                        </li>
                    </ul>

                    <!-- Search Box -->
                    <form action="<?= base_url('admin/documents') ?>" method="GET" class="d-flex align-items-center gap-2" style="min-width: 280px; max-width: 380px;">
                        <?php if (!empty($currentType)): ?>
                            <input type="hidden" name="type" value="<?= esc($currentType) ?>">
                        <?php endif; ?>
                        <div class="input-group input-group-sm">
                            <input type="text" name="search" class="form-control" placeholder="ค้นหาเลขที่, เรื่อง, จาก/ถึง..." value="<?= esc($search ?? '') ?>">
                            <button class="btn btn-primary" type="submit" title="ค้นหา">
                                <i class="bx bx-search"></i>
                            </button>
                            <?php if (!empty($search)): ?>
                                <a href="<?= base_url('admin/documents' . (!empty($currentType) ? '?type=' . $currentType : '')) ?>" class="btn btn-outline-secondary" title="ล้างการค้นหา">
                                    <i class="bx bx-x"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Documents Table Card -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent d-flex align-items-center justify-content-between py-3 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary text-white fw-bold px-2 py-1"><?= count($documents) ?></span>
                    <span class="fw-bold text-dark">รายการหนังสือในทะเบียน</span>
                </div>
                <small class="text-muted d-none d-sm-inline">
                    <i class="bx bx-info-circle me-1"></i>คลิกชื่อเรื่องหรือปุ่ม "ดูเรื่อง" เพื่อเปิดดูรายละเอียด
                </small>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="width: 100%; table-layout: auto;">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 105px;" class="text-center text-nowrap">เลขทะเบียน รร.</th>
                            <th style="width: 115px;" class="text-nowrap">ประเภท</th>
                            <th style="width: 85px;" class="text-center text-nowrap">ความเร่งด่วน</th>
                            <th style="width: 130px;" class="text-nowrap">เลขที่หนังสือ</th>
                            <th style="min-width: 220px;">เรื่อง</th>
                            <th style="width: 200px; max-width: 220px;">จาก / ถึง</th>
                            <th style="width: 95px;" class="text-center text-nowrap">ลงวันที่</th>
                            <th style="width: 135px;" class="text-center text-nowrap">สถานะการเดินแฟ้ม</th>
                            <th style="width: 85px;" class="text-center text-nowrap">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        <?php if (empty($documents)): ?>
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="bx bx-file-blank fs-1 text-secondary d-block mb-2"></i>
                                    <div class="fw-semibold">ไม่พบรายการหนังสือในหมวดหมู่นี้</div>
                                    <div class="small text-muted mt-1">สามารถกด "ออกเลข & ลงทะเบียนหนังสือ" เพื่อเริ่มลงทะเบียนใหม่ได้ทันที</div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($documents as $doc): ?>
                                <tr>
                                    <td class="text-center text-nowrap">
                                        <span class="badge bg-label-dark fw-bold px-2 py-1"><?= esc($doc['internal_reg_no'] ?: '-') ?></span>
                                    </td>
                                    <td class="text-nowrap">
                                        <?= doc_type_badge($doc['doc_type']) ?>
                                    </td>
                                    <td class="text-center text-nowrap">
                                        <?= urgency_badge($doc['urgency']) ?>
                                    </td>
                                    <td class="text-nowrap">
                                        <span class="fw-bold text-dark"><?= esc($doc['doc_no'] ?: '-') ?></span>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('admin/documents/' . $doc['id']) ?>" class="fw-semibold text-dark text-decoration-none hover-primary d-block text-wrap" style="line-height: 1.45;">
                                            <?= esc($doc['title']) ?>
                                        </a>
                                    </td>
                                    <td style="max-width: 220px;">
                                        <div class="small text-secondary lh-sm mb-1" style="word-break: break-word;">
                                            <span class="fw-semibold text-dark">จาก:</span> <?= esc($doc['from_org'] ?: '-') ?>
                                        </div>
                                        <div class="small text-secondary lh-sm" style="word-break: break-word;">
                                            <span class="fw-semibold text-dark">ถึง:</span> <?= esc($doc['to_org'] ?: '-') ?>
                                        </div>
                                    </td>
                                    <td class="text-center text-nowrap">
                                        <span class="small text-dark fw-medium"><?= thai_date($doc['doc_date'], 'short') ?></span>
                                    </td>
                                    <td class="text-center text-nowrap">
                                        <?= doc_status_badge($doc['status']) ?>
                                    </td>
                                    <td class="text-center text-nowrap">
                                        <a href="<?= base_url('admin/documents/' . $doc['id']) ?>" class="btn btn-sm btn-outline-primary px-2.5 py-1">
                                            <i class="bx bx-folder-open me-1"></i>ดูเรื่อง
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
