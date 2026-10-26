<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>ทะเบียนหนังสือรับ (ภายนอก) | ระบบงานสารบรรณ สกจ.<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-xxl flex-grow-1 container-p-y p-0">

    <!-- Header Banner -->
    <div class="card bg-primary text-white mb-4 border-0 shadow-sm overflow-hidden" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);">
        <div class="card-body p-4 position-relative">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <span class="badge bg-white text-primary fw-bold px-2.5 py-1 mb-2 shadow-xs" style="font-size: 0.8rem;">
                        <i class="bx bx-mail-send me-1"></i> หมวดที่ ๑ หนังสือภายนอก
                    </span>
                    <h3 class="fw-bold text-white mb-1" style="font-family: 'K2D', sans-serif;">ทะเบียนหนังสือรับ (รับเข้าจากภายนอก)</h3>
                    <p class="text-white opacity-90 mb-0" style="font-size: 0.95rem;">
                        งานสารบรรณโรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์ — บันทึกรับหนังสือ ประทับตรา และเสนอผู้บริหารสั่งการ
                    </p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <?php if (in_array((string)session()->get('role'), ['super_admin', 'admin', 'saraban'], true)): ?>
                        <a href="<?= base_url('admin/inbound/create') ?>" class="btn btn-warning text-dark fw-bold shadow-sm px-3 py-2">
                            <i class="bx bx-plus-circle me-1"></i> ลงทะเบียนรับหนังสือเข้า
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-3">
            <form action="<?= base_url('admin/inbound') ?>" method="GET" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-dark mb-1">ค้นหาคำสำคัญ</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light"><i class="bx bx-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="เลขทะเบียนรับ, เลขที่หนังสือ, เรื่อง, จาก..." value="<?= esc($search ?? '') ?>">
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-dark mb-1">ความเร่งด่วน</label>
                    <select name="urgency" class="form-select form-select-sm">
                        <option value="">ทั้งหมด</option>
                        <option value="normal" <?= ($urgency ?? '') === 'normal' ? 'selected' : '' ?>>ปกติ</option>
                        <option value="urgent" <?= ($urgency ?? '') === 'urgent' ? 'selected' : '' ?>>ด่วน</option>
                        <option value="very_urgent" <?= ($urgency ?? '') === 'very_urgent' ? 'selected' : '' ?>>ด่วนมาก</option>
                        <option value="most_urgent" <?= ($urgency ?? '') === 'most_urgent' ? 'selected' : '' ?>>ด่วนที่สุด</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-dark mb-1">สถานะ</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">ทุกสถานะ</option>
                        <option value="routing" <?= ($status ?? '') === 'routing' ? 'selected' : '' ?>>รอ ผอ. เกษียนสั่งการ</option>
                        <option value="completed" <?= ($status ?? '') === 'completed' ? 'selected' : '' ?>>สั่งการเรียบร้อยแล้ว</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-dark mb-1">วันที่รับเข้า (พ.ศ.)</label>
                    <input type="text" name="start_date" class="form-control form-control-sm flatpickr-thai-input" value="<?= esc($startDate ?? '') ?>" placeholder="วว/ดด/ปปปป">
                </div>
                <div class="col-md-2 d-flex gap-1">
                    <button type="submit" class="btn btn-primary btn-sm fw-bold w-100">
                        <i class="bx bx-filter-alt me-1"></i> กรอง
                    </button>
                    <a href="<?= base_url('admin/inbound') ?>" class="btn btn-outline-secondary btn-sm" title="ล้างค่า">
                        <i class="bx bx-reset"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 fw-bold text-dark" style="font-family: 'K2D', sans-serif;">
                <i class="bx bx-list-ul text-primary me-1"></i> รายการหนังสือรับเข้า (<?= count($documents) ?> ฉบับ)
            </h5>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 110px;" class="text-center">เลขทะเบียนรับ</th>
                        <th style="width: 90px;" class="text-center">ความเร่งด่วน</th>
                        <th style="width: 140px;">เลขที่หนังสือต้นทาง</th>
                        <th style="width: 110px;">วันที่รับ</th>
                        <th>เรื่อง / หน่วยงานต้นทาง</th>
                        <th style="width: 160px;">สถานะ / คำสั่งการ</th>
                        <th style="width: 140px;">ผู้รับมอบหมาย</th>
                        <th style="width: 90px;" class="text-center">จัดการ</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    <?php if (empty($documents)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="avatar avatar-xl bg-label-secondary mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle" style="width: 64px; height: 64px;">
                                    <i class="bx bx-folder-open text-muted fs-1"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1">ไม่พบข้อมูลหนังสือรับเข้า</h6>
                                <p class="text-muted small mb-0">ยังไม่มีรายการหนังสือรับ หรือไม่พบข้อมูลตามเงื่อนไขที่ค้นหา</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($documents as $doc): ?>
                            <tr>
                                <!-- เลขทะเบียนรับ -->
                                <td class="text-center">
                                    <span class="badge bg-primary text-white fw-bold px-2 py-1 shadow-xs font-monospace" style="font-size: 0.85rem;">
                                        <?= esc($doc['internal_reg_no'] ?: '-') ?>
                                    </span>
                                </td>

                                <!-- ความเร่งด่วน -->
                                <td class="text-center">
                                    <?php if ($doc['urgency'] === 'most_urgent'): ?>
                                        <span class="badge bg-danger text-white fw-bold">ด่วนที่สุด</span>
                                    <?php elseif ($doc['urgency'] === 'very_urgent'): ?>
                                        <span class="badge bg-warning text-dark fw-bold">ด่วนมาก</span>
                                    <?php elseif ($doc['urgency'] === 'urgent'): ?>
                                        <span class="badge bg-warning text-dark fw-bold">ด่วน</span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-dark border">ปกติ</span>
                                    <?php endif; ?>
                                </td>

                                <!-- เลขที่หนังสือต้นทาง -->
                                <td>
                                    <strong class="text-dark d-block"><?= esc($doc['doc_no'] ?: '-') ?></strong>
                                    <small class="text-muted"><?= esc(thai_date($doc['doc_date'], 'short')) ?></small>
                                </td>

                                <!-- วันที่รับ -->
                                <td>
                                    <span class="text-dark fw-semibold d-block"><?= esc(thai_date($doc['receive_date'], 'short')) ?></span>
                                    <small class="text-muted"><?= esc($doc['receive_stamp_time'] ?: '') ?></small>
                                </td>

                                <!-- เรื่อง และ หน่วยงานต้นทาง -->
                                <td style="min-width: 280px; white-space: normal;">
                                    <a href="<?= base_url('admin/inbound/' . $doc['id']) ?>" class="fw-bold text-dark text-decoration-none hover-primary d-block mb-1" style="line-height: 1.4;">
                                        <?= esc($doc['title']) ?>
                                    </a>
                                    <div class="d-flex align-items-center gap-2 small">
                                        <span class="badge bg-secondary text-white fw-semibold">
                                            <i class="bx bx-building me-1"></i>จาก: <?= esc($doc['from_org']) ?>
                                        </span>
                                        <?php if (!empty($doc['file_path'])): ?>
                                            <span class="badge bg-danger text-white fw-semibold">
                                                <i class="bx bxs-file-pdf me-1"></i>มีไฟล์แนบ
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>

                                <!-- สถานะ / คำสั่งการ -->
                                <td>
                                    <?php if ($doc['status'] === 'completed'): ?>
                                        <span class="badge bg-success text-white fw-bold mb-1">
                                            <i class="bx bx-check-circle me-1"></i> ผอ. สั่งการแล้ว
                                        </span>
                                        <?php if (!empty($doc['executive_instruction'])): ?>
                                            <small class="text-dark d-block text-truncate fw-semibold" style="max-width: 160px;" title="<?= esc($doc['executive_instruction']) ?>">
                                                "<?= esc($doc['executive_instruction']) ?>"
                                            </small>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark fw-bold">
                                            <i class="bx bx-time-five me-1"></i> รอ ผอ. สั่งการ
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <!-- ผู้รับมอบหมาย -->
                                <td>
                                    <?php if (!empty($doc['assigned_first'])): ?>
                                        <div class="d-flex align-items-center gap-1">
                                            <i class="bx bx-user text-primary"></i>
                                            <span class="fw-semibold text-dark small">
                                                <?= esc(($doc['assigned_prefix'] ?? '') . $doc['assigned_first'] . ' ' . $doc['assigned_last']) ?>
                                            </span>
                                        </div>
                                    <?php elseif (!empty($doc['assigned_department'])): ?>
                                        <span class="badge bg-primary text-white fw-semibold small">
                                            <?= esc($doc['assigned_department']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>

                                <!-- การจัดการ -->
                                <td class="text-center">
                                    <a href="<?= base_url('admin/inbound/' . $doc['id']) ?>" class="btn btn-sm btn-outline-primary px-2.5 py-1" title="เปิดตรวจดูเอกสาร">
                                        <i class="bx bx-show me-1"></i> ตรวจดู
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
<?= $this->endSection() ?>
