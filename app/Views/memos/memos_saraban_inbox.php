<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>บันทึกข้อความรอสารบรรณตรวจสอบ & ออกเลข<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
// คำนวณสถิติเบื้องต้น
$totalMemos = count($memos);
$urgentCount = 0;
$todayCount  = 0;
$todayDate   = date('Y-m-d');

foreach ($memos as $m) {
    if (!empty($m['urgency']) && $m['urgency'] !== 'normal') {
        $urgentCount++;
    }
    $createdDate = !empty($m['created_at']) ? substr($m['created_at'], 0, 10) : '';
    if ($createdDate === $todayDate) {
        $todayCount++;
    }
}
?>

<style>
.saraban-inbox-hero {
    background: linear-gradient(135deg, #696cff 0%, #5f61e6 50%, #4338ca 100%);
    border-radius: 1rem;
    box-shadow: 0 10px 25px -5px rgba(105, 108, 255, 0.35);
    position: relative;
    overflow: hidden;
    color: #ffffff;
}

.saraban-inbox-hero::before {
    content: '';
    position: absolute;
    top: -40%;
    right: -10%;
    width: 320px;
    height: 320px;
    background: radial-gradient(circle, rgba(255,255,255,0.18) 0%, rgba(255,255,255,0) 70%);
    border-radius: 50%;
    pointer-events: none;
}

.inbox-stat-card {
    border-radius: 0.85rem;
    border: 1px solid rgba(226, 232, 240, 0.9);
    background: #ffffff;
    box-shadow: 0 4px 15px -2px rgba(0, 0, 0, 0.04);
    transition: all 0.2s ease;
}

.inbox-stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px -3px rgba(0, 0, 0, 0.08);
}

.pulse-badge-danger {
    animation: pulseDanger 1.8s infinite;
}

@keyframes pulseDanger {
    0% { box-shadow: 0 0 0 0 rgba(255, 62, 29, 0.7); }
    70% { box-shadow: 0 0 0 8px rgba(255, 62, 29, 0); }
    100% { box-shadow: 0 0 0 0 rgba(255, 62, 29, 0); }
}

.route-step-chip {
    font-size: 0.78rem;
    padding: 0.25rem 0.6rem;
    border-radius: 1rem;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    color: #334155;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    white-space: nowrap;
}

.inbox-table th {
    font-size: 0.825rem;
    font-weight: 700;
    color: #334155 !important;
    background-color: #f8fafc !important;
    border-bottom: 2px solid #e2e8f0;
    padding: 0.85rem 1rem;
}

.inbox-table td {
    vertical-align: middle;
    padding: 0.85rem 1rem;
    border-bottom: 1px solid #f1f5f9;
}
</style>

<!-- ========================================== -->
<!-- 1. Hero Section                            -->
<!-- ========================================== -->
<div class="row mb-4">
    <div class="col-12">
        <div class="saraban-inbox-hero p-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar avatar-lg bg-white rounded-circle p-2 shadow-sm d-flex align-items-center justify-content-center">
                        <i class="bx bx-clipboard-check fs-2 text-primary"></i>
                    </div>
                    <div>
                        <span class="badge bg-white text-dark fw-bold px-2.5 py-1 mb-1 shadow-sm">
                            <i class="bx bx-check-shield text-primary me-1"></i> กล่องงานสารบรรณโรงเรียน
                        </span>
                        <h3 class="text-white fw-bold mb-1">บันทึกข้อความรอสารบรรณตรวจสอบ & ออกเลข</h3>
                        <p class="text-white opacity-90 mb-0 small">
                            ตรวจทานความครบถ้วนของบันทึกข้อความ ปรับเส้นทางเสนอเกษียน และรันเลขที่หนังสือทางการเพื่อส่งเข้าสู่การลงนาม
                        </p>
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="<?= base_url('admin/memos') ?>" class="btn btn-warning text-dark fw-bold px-3 py-2 shadow-sm">
                        <i class="bx bx-folder me-1"></i> ทะเบียนบันทึกข้อความทั้งหมด
                    </a>
                    <a href="<?= base_url('admin/memos/create') ?>" class="btn btn-light text-primary fw-bold px-3 py-2 shadow-sm">
                        <i class="bx bx-edit me-1"></i> เขียนบันทึกข้อความใหม่
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- 2. Stat Widgets Cards                      -->
<!-- ========================================== -->
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="card inbox-stat-card h-100">
            <div class="card-body p-3.5 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary small fw-semibold d-block mb-1">บันทึกข้อความรอออกเลข</span>
                    <h2 class="fw-bold mb-0 text-danger"><?= number_format($totalMemos) ?> <span class="fs-6 text-muted fw-normal">ฉบับ</span></h2>
                </div>
                <div class="avatar avatar-md bg-label-danger rounded p-2">
                    <i class="bx bx-time-five fs-3 text-danger"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-4">
        <div class="card inbox-stat-card h-100">
            <div class="card-body p-3.5 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary small fw-semibold d-block mb-1">เรื่องด่วน / ด่วนที่สุด</span>
                    <h2 class="fw-bold mb-0 text-warning"><?= number_format($urgentCount) ?> <span class="fs-6 text-muted fw-normal">ฉบับ</span></h2>
                </div>
                <div class="avatar avatar-md bg-label-warning rounded p-2">
                    <i class="bx bxs-flame fs-3 text-warning"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-4">
        <div class="card inbox-stat-card h-100">
            <div class="card-body p-3.5 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary small fw-semibold d-block mb-1">ส่งเข้ามาในวันนี้</span>
                    <h2 class="fw-bold mb-0 text-primary"><?= number_format($todayCount) ?> <span class="fs-6 text-muted fw-normal">ฉบับ</span></h2>
                </div>
                <div class="avatar avatar-md bg-label-primary rounded p-2">
                    <i class="bx bx-calendar-check fs-3 text-primary"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- 3. Memos Inbox Table Card                  -->
<!-- ========================================== -->
<div class="card border-0 shadow-sm" style="border-radius: 1rem;">
    <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-danger text-white rounded-pill px-2.5 py-1 fw-bold pulse-badge-danger"><?= $totalMemos ?></span>
            <span class="fw-bold text-dark fs-6">รายการบันทึกข้อความรอตรวจสอบ</span>
        </div>

        <!-- Quick Instant Search Filter -->
        <div class="d-flex align-items-center gap-2" style="min-width: 260px; max-width: 340px;">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-light border-end-0"><i class="bx bx-search text-muted"></i></span>
                <input type="text" id="inboxSearchInput" class="form-control border-start-0" placeholder="พิมพ์ค้นหาเรื่อง หรือผู้จัดทำ..." onkeyup="filterInboxTable()">
            </div>
        </div>
    </div>

    <?php if (empty($memos)): ?>
        <div class="card-body text-center py-5">
            <div class="avatar avatar-xl rounded-circle bg-label-success mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 72px; height: 72px;">
                <i class="bx bx-check-double text-success fs-1"></i>
            </div>
            <h5 class="fw-bold text-dark mb-1">ไม่มีบันทึกข้อความรอตรวจสอบในขณะนี้</h5>
            <p class="text-muted small mb-3">เมื่อผู้จัดทำส่งบันทึกข้อความเข้ามายังงานสารบรรณ รายการจะแสดงที่หน้านี้ทันที</p>
            <a href="<?= base_url('admin/memos') ?>" class="btn btn-sm btn-primary rounded-pill px-3">
                <i class="bx bx-folder me-1"></i> ไปที่ทะเบียนบันทึกข้อความ
            </a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table inbox-table align-middle mb-0" id="sarabanInboxTable" style="width: 100%; table-layout: auto;">
                <thead>
                    <tr>
                        <th style="width: 85px;" class="text-center text-nowrap">ความเร่งด่วน</th>
                        <th style="width: 105px;" class="text-center text-nowrap">วันที่ส่ง</th>
                        <th style="width: 190px;">ผู้จัดทำ & สังกัด</th>
                        <th style="min-width: 240px;">เรื่องบันทึกข้อความ</th>
                        <th style="width: 250px;">เส้นทางเกษียนเสนอเซ็น</th>
                        <th class="text-center text-nowrap" style="width: 180px;">การดำเนินการ</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    <?php foreach ($memos as $memo): ?>
                        <?php 
                        $memoId = (int)$memo['id'];
                        $routes = $routeMap[$memoId] ?? [];
                        ?>
                        <tr class="inbox-row">
                            <!-- ความเร่งด่วน -->
                            <td class="text-center text-nowrap">
                                <?= urgency_badge($memo['urgency'] ?? 'normal') ?>
                            </td>

                            <!-- วันที่ส่ง -->
                            <td class="text-center text-nowrap small text-muted">
                                <span class="d-block fw-semibold text-dark"><?= esc(thai_date($memo['created_at'] ?? $memo['doc_date'] ?? '', 'short')) ?></span>
                                <small class="text-muted"><?= !empty($memo['created_at']) ? substr($memo['created_at'], 11, 5) . ' น.' : '' ?></small>
                            </td>

                            <!-- ผู้จัดทำ & สังกัด -->
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar avatar-sm bg-label-primary rounded-circle flex-shrink-0 d-flex align-items-center justify-content-center">
                                        <i class="bx bx-user text-primary"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-dark search-target-creator">
                                            <?= esc(trim(($memo['prefix'] ?? '') . ($memo['first_name'] ?? '') . ' ' . ($memo['last_name'] ?? ''))) ?>
                                        </div>
                                        <small class="text-muted d-block text-truncate" style="max-width: 140px;">
                                            <?= esc($memo['creator_department'] ?? $memo['memo_department'] ?? '-') ?>
                                        </small>
                                    </div>
                                </div>
                            </td>

                            <!-- เรื่องบันทึกข้อความ -->
                            <td>
                                <a href="<?= base_url('admin/memos/' . $memoId) ?>" class="fw-bold text-dark text-decoration-none hover-primary d-block text-wrap search-target-title mb-1" style="line-height: 1.45;">
                                    <?= esc($memo['title'] ?? '-') ?>
                                </a>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <span class="badge bg-label-danger py-0 px-1 small">
                                        <i class="bx bx-time me-1"></i>รอสารบรรณออกเลข
                                    </span>
                                    <?php if (!empty($memo['file_path'])): ?>
                                        <span class="badge bg-label-info py-0 px-1 small">
                                            <i class="bx bx-paperclip me-1"></i>มีไฟล์แนบ
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </td>

                            <!-- เส้นทางเกษียนเสนอเซ็น -->
                            <td>
                                <?php if (empty($routes)): ?>
                                    <div class="route-step-chip border-primary bg-primary bg-opacity-10 text-primary fw-bold">
                                        <span class="badge bg-primary rounded-circle p-0 d-flex align-items-center justify-content-center" style="width: 18px; height: 18px; font-size: 0.68rem;">1</span>
                                        <span class="text-truncate" style="max-width: 190px;">
                                            <i class="bx bxs-crown text-warning me-1"></i><?= esc($defaultDirector['full_name'] ?? 'ผู้อำนวยการโรงเรียน') ?>
                                            <span class="text-muted small fw-normal">(ผอ.โรงเรียน)</span>
                                        </span>
                                    </div>
                                <?php else: ?>
                                    <div class="d-flex flex-column gap-1">
                                        <?php foreach ($routes as $idx => $r): ?>
                                            <?php 
                                            $isDirector = (!empty($r['personnel_position']) && str_contains($r['personnel_position'], 'ผู้อำนวยการ') && !str_contains($r['personnel_position'], 'รอง'))
                                                || (!empty($r['position_name']) && str_contains($r['position_name'], 'ผู้อำนวยการ') && !str_contains($r['position_name'], 'รอง'))
                                                || (!empty($r['pers_id']) && $r['pers_id'] === ($defaultDirector['pers_id'] ?? 'pers_060'));
                                            ?>
                                            <div class="route-step-chip <?= $isDirector ? 'border-primary bg-primary bg-opacity-10 text-primary fw-bold' : '' ?>">
                                                <span class="badge <?= $isDirector ? 'bg-primary text-white' : 'bg-secondary text-white' ?> rounded-circle p-0 d-flex align-items-center justify-content-center" style="width: 18px; height: 18px; font-size: 0.68rem;">
                                                    <?= esc($r['step_order'] ?? ($idx + 1)) ?>
                                                </span>
                                                <span class="text-truncate" style="max-width: 195px;" title="<?= esc($r['first_name'] . ' ' . $r['last_name'] . ' (' . ($r['position_name'] ?? '') . ')') ?>">
                                                    <?php if ($isDirector): ?><i class="bx bxs-crown text-warning me-1"></i><?php endif; ?>
                                                    <?= esc(trim(($r['prefix'] ?? '') . $r['first_name'] . ' ' . $r['last_name'])) ?>
                                                    <span class="text-muted small fw-normal">(<?= esc($r['position_name'] ?? 'ไม่ระบุ') ?>)</span>
                                                </span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </td>

                            <!-- การดำเนินการ -->
                            <td class="text-center text-nowrap">
                                <div class="d-flex align-items-center justify-content-center gap-1">
                                    <!-- ปุ่มตรวจดู -->
                                    <a href="<?= base_url('admin/memos/' . $memoId) ?>" class="btn btn-sm btn-outline-primary px-2.5 py-1" title="เปิดตรวจดูเอกสาร">
                                        <i class="bx bx-show me-1"></i>ตรวจดู
                                    </a>

                                    <!-- ปุ่มปรับเส้นทาง (เปิด Modal) -->
                                    <button type="button" class="btn btn-sm btn-outline-secondary px-2.5 py-1" data-bs-toggle="modal" data-bs-target="#editRouteModal_<?= $memoId ?>" title="ปรับเปลี่ยนเส้นทางเกษียน">
                                        <i class="bx bx-git-branch me-1"></i>เส้นทาง
                                    </button>

                                    <!-- ปุ่มออกเลขและส่งเกษียน (เปิดหน้ารายละเอียดเพื่อตรวจทานและประทับตรา) -->
                                    <a href="<?= base_url('admin/memos/' . $memoId) ?>" class="btn btn-sm btn-success px-2.5 py-1 fw-bold text-white shadow-xs" title="เปิดหน้าบันทึกข้อความเพื่อตรวจทานและประทับตราออกเลข">
                                        <i class="bx bx-stamp me-1"></i>ตรวจทาน & ออกเลข
                                    </a>
                                </div>
                            </td>
                        </tr>

                        <!-- ======================================================= -->
                        <!-- Modal ปรับเส้นทางเกษียน สำหรับ Memo นี้                  -->
                        <!-- ======================================================= -->
                        <div class="modal fade" id="editRouteModal_<?= $memoId ?>" tabindex="-1" aria-labelledby="modalLabel_<?= $memoId ?>" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content border-0 shadow">
                                    <div class="modal-header bg-light py-3 border-bottom">
                                        <h5 class="modal-title fw-bold text-dark" id="modalLabel_<?= $memoId ?>">
                                            <i class="bx bx-git-branch text-primary me-2"></i>ปรับลำดับเส้นทางเกษียนเสนอเซ็น
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="<?= base_url('admin/memos/' . $memoId . '/update-route') ?>" method="post">
                                        <?= csrf_field() ?>
                                        <div class="modal-body p-4">
                                            <div class="alert alert-info py-2 px-3 small d-flex align-items-center mb-3">
                                                <i class="bx bx-info-circle fs-5 me-2 text-primary flex-shrink-0"></i>
                                                <div>
                                                    <div class="fw-bold mb-0.5">เรื่อง: <?= esc($memo['title']) ?></div>
                                                    <span class="text-dark">ระบบกำหนดเส้นทางเริ่มต้นเป็น <strong>ผู้อำนวยการโรงเรียน (ผอ.)</strong> ไว้เสมอ หากต้องการให้ผ่านหัวหน้ากลุ่มสาระหรือรอง ผอ. ก่อน สามารถกดปุ่ม <code>+ เพิ่มลำดับผู้ลงนาม</code> ด้านล่างได้เลยครับ</span>
                                                </div>
                                            </div>

                                            <label class="form-label fw-bold text-dark mb-2">ลำดับผู้พิจารณาลงนาม (ตามสายบังคับบัญชา):</label>
                                            <div class="route-editor-steps-<?= $memoId ?> d-flex flex-column gap-2 mb-3">
                                                <?php 
                                                $memoRoutes = $routes;
                                                if (empty($memoRoutes)) {
                                                    $memoRoutes = [['user_id' => 0, 'pers_id' => ($defaultDirector['pers_id'] ?? 'pers_060')]];
                                                }
                                                ?>
                                                <?php foreach ($memoRoutes as $sIdx => $route): ?>
                                                    <div class="route-editor-step d-flex gap-2 align-items-center bg-light p-2 rounded border">
                                                        <span class="badge bg-primary rounded-circle p-0 d-flex align-items-center justify-content-center step-number-label" style="width: 24px; height: 24px;">
                                                            <?= $sIdx + 1 ?>
                                                        </span>
                                                        <select name="route_users[]" class="form-select form-select-sm" required>
                                                            <option value="">-- เลือกผู้เกษียน/ผู้ลงนาม --</option>
                                                            <?php foreach ($allPersonnel as $person): ?>
                                                                <?php 
                                                                $isSelected = (string)($route['pers_id'] ?? '') === (string)$person['pers_id']
                                                                    || (empty($route['pers_id']) && (string)$person['pers_id'] === ($defaultDirector['pers_id'] ?? 'pers_060'));
                                                                ?>
                                                                <option value="<?= esc($person['pers_id']) ?>" <?= $isSelected ? 'selected' : '' ?>>
                                                                    <?= esc($person['full_name'] ?: $person['pers_username'] ?: $person['pers_id']) ?> (<?= esc($person['position_name'] ?? 'ไม่ระบุตำแหน่ง') ?><?= !empty($person['group_name']) ? ' · ' . esc($person['group_name']) : '' ?>)
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                        <button type="button" class="btn btn-sm btn-outline-danger px-2" onclick="removeRouteStep(this, '<?= $memoId ?>')" title="ลบลำดับนี้">
                                                            <i class="bx bx-trash"></i>
                                                        </button>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>

                                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="addRouteStep('<?= $memoId ?>')">
                                                <i class="bx bx-plus me-1"></i> เพิ่มลำดับผู้ลงนามถัดไป
                                            </button>
                                        </div>
                                        <div class="modal-footer bg-light py-2.5 border-top">
                                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">ยกเลิก</button>
                                            <button type="submit" class="btn btn-primary btn-sm fw-bold px-3">
                                                <i class="bx bx-save me-1"></i> บันทึกเส้นทางเกษียน
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script>
// กรองตารางแบบเรียลไทม์ (Instant Search Filter)
function filterInboxTable() {
    const query = document.getElementById('inboxSearchInput').value.toLowerCase().trim();
    const rows = document.querySelectorAll('.inbox-row');

    rows.forEach(row => {
        const title = row.querySelector('.search-target-title')?.textContent.toLowerCase() || '';
        const creator = row.querySelector('.search-target-creator')?.textContent.toLowerCase() || '';
        if (title.includes(query) || creator.includes(query)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// ยืนยันและดำเนินการออกเลขหนังสือทางการ
function handleIssueNumber(memoId, btn) {
    const form = document.getElementById('issueForm_' + memoId);
    if (!form) return;

    const doSubmit = function() {
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> กำลังออกเลข...';
        }
        if (window.AppAlert && typeof window.AppAlert.showLoading === 'function') {
            AppAlert.showLoading('กำลังออกเลขหนังสือ...', 'ระบบกำลังออกเลขหนังสือทางการและส่งเข้าสู่เส้นทางเกษียน กรุณารอสักครู่');
        }
        form.submit();
    };

    if (window.AppAlert && typeof window.AppAlert.confirm === 'function') {
        AppAlert.confirm('ยืนยันว่าได้ตรวจทานเอกสารเรียบร้อยแล้ว และต้องการรันเลขที่หนังสือทางการเพื่อส่งเข้าสู่การเกษียนทันทีใช่หรือไม่?', {
            title: 'ออกเลขหนังสือและส่งเกษียน',
            confirmText: '<i class="bx bx-hash me-1"></i> ยืนยันออกเลขทันที',
            cancelText: 'ตรวจทานก่อน'
        }).then(function(confirmed) {
            if (confirmed) {
                doSubmit();
            }
        });
        return;
    }

    if (confirm('ยืนยันว่าได้ตรวจทานเอกสารเรียบร้อยแล้ว และต้องการรันเลขที่หนังสือทางการเพื่อส่งเข้าสู่การเกษียนทันทีใช่หรือไม่?')) {
        doSubmit();
    }
}

// เพิ่มลำดับใน Modal
function addRouteStep(memoId) {
    const container = document.querySelector('.route-editor-steps-' + memoId);
    if (!container) return;
    const steps = container.querySelectorAll('.route-editor-step');
    const first = steps[0];
    if (!first) return;

    const clone = first.cloneNode(true);
    const select = clone.querySelector('select');
    if (select) select.selectedIndex = 0;

    container.appendChild(clone);
    updateStepNumbers(memoId);
}

// ลบลำดับใน Modal
function removeRouteStep(btn, memoId) {
    const container = document.querySelector('.route-editor-steps-' + memoId);
    if (!container) return;
    const steps = container.querySelectorAll('.route-editor-step');
    if (steps.length <= 1) {
        const select = steps[0].querySelector('select');
        if (select) select.selectedIndex = 0;
        return;
    }
    btn.closest('.route-editor-step').remove();
    updateStepNumbers(memoId);
}

// อัปเดตตัวเลขลำดับ 1, 2, 3...
function updateStepNumbers(memoId) {
    const container = document.querySelector('.route-editor-steps-' + memoId);
    if (!container) return;
    const steps = container.querySelectorAll('.route-editor-step');
    steps.forEach((step, index) => {
        const badge = step.querySelector('.step-number-label');
        if (badge) badge.textContent = index + 1;
    });
}
</script>
<?= $this->endSection() ?>
