<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?>บันทึกข้อความรอสารบรรณตรวจสอบ<?= $this->endSection() ?>
<?= $this->section('content') ?>
<div class="container-fluid py-3">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h4 class="mb-1"><i class="bx bx-clipboard-check me-2"></i>บันทึกข้อความรอสารบรรณตรวจสอบ</h4>
            <p class="text-muted mb-0">ตรวจสอบความครบถ้วนก่อนออกเลขหนังสือและส่งเข้าสู่เส้นทางเกษียน</p>
        </div>
        <a href="<?= base_url('admin/memos') ?>" class="btn btn-outline-secondary"><i class="bx bx-arrow-back me-1"></i>ทะเบียนบันทึกข้อความ</a>
    </div>

    <?php if (empty($memos)): ?>
        <div class="card"><div class="card-body text-center py-5">
            <i class="bx bx-check-circle text-success" style="font-size:3rem"></i>
            <h5 class="mt-3">ไม่มีบันทึกข้อความรอตรวจสอบ</h5>
            <p class="text-muted mb-0">เมื่อผู้จัดทำส่งบันทึกข้อความเข้ามา รายการจะแสดงที่หน้านี้</p>
        </div></div>
    <?php else: ?>
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead><tr><th>วันที่ส่ง</th><th>ผู้จัดทำ</th><th>เรื่อง</th><th>หน่วยงาน</th><th class="text-end">ดำเนินการ</th></tr></thead>
                    <tbody>
                    <?php foreach ($memos as $memo): ?>
                        <tr>
                            <td class="text-nowrap"><?= esc(thai_date($memo['created_at'] ?? $memo['doc_date'] ?? '')) ?></td>
                            <td><?= esc(trim(($memo['prefix'] ?? '') . ($memo['first_name'] ?? '') . ' ' . ($memo['last_name'] ?? ''))) ?></td>
                            <td><div class="fw-semibold"><?= esc($memo['title'] ?? '-') ?></div><small class="text-muted">ยังไม่ออกเลขหนังสือ</small></td>
                            <td><?= esc($memo['creator_department'] ?? $memo['from_org'] ?? '-') ?></td>
                            <td class="text-end">
                                <div class="d-flex flex-wrap justify-content-end gap-1">
                                    <a href="<?= base_url('admin/memos/' . (int)$memo['id']) ?>" class="btn btn-sm btn-outline-primary"><i class="bx bx-show me-1"></i>ตรวจดู</a>
                                    <form action="<?= base_url('admin/memos/' . (int)$memo['id'] . '/issue-number') ?>" method="post" class="d-inline" onsubmit="return confirm('ยืนยันว่าได้ตรวจสอบเอกสารแล้ว และต้องการออกเลขหนังสือเพื่อส่งเข้าสู่เส้นทางเกษียนหรือไม่?')">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-success"><i class="bx bx-hash me-1"></i>ออกเลขและส่งเกษียน</button>
                                    </form>
                                </div>
                                <details class="mt-2 text-start">
                                    <summary class="small text-primary" style="cursor:pointer;">ปรับเส้นทางเกษียน</summary>
                                    <?php $memoRoutes = $routeMap[(int)$memo['id']] ?? []; ?>
                                    <?php if (empty($memoRoutes)) $memoRoutes = [['user_id' => 0, 'pers_id' => '']]; ?>
                                    <form class="route-editor border rounded p-2 mt-2 bg-light" action="<?= base_url('admin/memos/' . (int)$memo['id'] . '/update-route') ?>" method="post">
                                        <?= csrf_field() ?>
                                        <div class="route-editor-steps">
                                            <?php foreach ($memoRoutes as $route): ?>
                                                <div class="d-flex gap-1 align-items-center mb-2 route-editor-step">
                                                    <select name="route_users[]" class="form-select form-select-sm" required>
                                                        <option value="">-- เลือกผู้เกษียน --</option>
                                                        <?php foreach ($allPersonnel as $person): ?>
                                                            <option value="<?= esc($person['pers_id']) ?>" <?= (string)($route['pers_id'] ?? '') === (string)$person['pers_id'] ? 'selected' : '' ?>>
                                                                <?= esc($person['full_name'] ?: $person['pers_username'] ?: $person['pers_id']) ?> (<?= esc($person['position_name'] ?? 'ไม่ระบุตำแหน่ง') ?><?= !empty($person['group_name']) ? ' · ' . esc($person['group_name']) : '' ?>)
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRouteEditorStep(this)" title="ลบลำดับ"><i class="bx bx-trash"></i></button>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addRouteEditorStep(this)"><i class="bx bx-plus"></i> เพิ่มลำดับ</button>
                                            <button type="submit" class="btn btn-sm btn-primary" onclick="return confirm('บันทึกเส้นทางเกษียนที่ปรับใหม่หรือไม่?')">บันทึกเส้นทาง</button>
                                        </div>
                                    </form>
                                </details>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>
<script>
function addRouteEditorStep(button) {
    const form = button.closest('form');
    const container = form ? form.querySelector('.route-editor-steps') : null;
    const first = container ? container.querySelector('.route-editor-step') : null;
    if (!container || !first) return;
    const clone = first.cloneNode(true);
    const select = clone.querySelector('select');
    if (select) select.selectedIndex = 0;
    container.appendChild(clone);
}
function removeRouteEditorStep(button) {
    const container = button.closest('.route-editor-steps');
    if (!container) return;
    const steps = container.querySelectorAll('.route-editor-step');
    if (steps.length <= 1) {
        const select = steps[0]?.querySelector('select');
        if (select) select.selectedIndex = 0;
        return;
    }
    button.closest('.route-editor-step')?.remove();
}
</script>
<?= $this->endSection() ?>
