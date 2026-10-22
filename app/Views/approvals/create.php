<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?>สร้างคำขออนุมัติ/ขออนุญาต<?= $this->endSection() ?>
<?= $this->section('content') ?>
<div class="mb-4"><a href="<?= base_url('admin/approvals') ?>" class="btn btn-sm btn-outline-secondary mb-3"><i class="bx bx-arrow-back me-1"></i>กลับ</a><h4 class="mb-1">สร้างคำขออนุมัติ / ขออนุญาต</h4><p class="text-muted mb-0">ต้องสร้างบันทึกข้อความภายในและส่งเสนอแล้วก่อน จึงจะเปิดคำขอได้</p></div>
<?php if(session()->getFlashdata('error')): ?><div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div><?php endif ?>
<form method="post" action="<?= base_url('admin/approvals/store') ?>">
<?= csrf_field() ?>
<div class="card mb-4"><div class="card-header"><h5 class="mb-0">เอกสารหลักและประเภทคำขอ</h5></div><div class="card-body row g-3">
<div class="col-12" id="memoField"><label class="form-label" id="memoLabel">บันทึกข้อความหลัก</label><select name="document_id" id="approvalDocumentId" class="form-select"><option value="">เลือกบันทึกข้อความที่สร้างไว้แล้ว</option><?php foreach($memos as $m): ?><option value="<?= (int)$m['id'] ?>" <?= old('document_id')==(string)$m['id']?'selected':'' ?>><?= esc($m['doc_no'].' · '.$m['title'].' · '.$m['created_at']) ?></option><?php endforeach ?></select><div class="form-text" id="memoHelp">ประเภทงานบางรายการต้องมีบันทึกข้อความที่ส่งเสนอแล้วเป็นเอกสารหลัก</div></div>
<div class="col-md-6"><label class="form-label required">ประเภทงาน</label><select name="approval_type_id" id="approvalTypeId" class="form-select" required><option value="">เลือกประเภท</option><?php foreach($types as $t): ?><option value="<?= (int)$t['id'] ?>" data-requires-memo="<?= (int)($t['requires_memo']??($t['category']==='approval'?1:0)) ?>" <?= (old('approval_type_id')==(string)$t['id'] || (($selectedType??'')===(string)$t['code']))?'selected':'' ?>><?= $t['category']==='approval'?'ขออนุมัติ':'ขออนุญาต' ?> — <?= esc($t['name']) ?></option><?php endforeach ?></select></div>
<div class="col-md-6"><label class="form-label required">เรื่องคำขอ</label><input class="form-control" name="title" value="<?= esc(old('title')??'') ?>" required maxlength="255"></div>
<div class="col-12"><label class="form-label">วัตถุประสงค์ / เหตุผล</label><textarea class="form-control" name="purpose" rows="2"><?= esc(old('purpose')??'') ?></textarea></div>
<div class="col-12"><label class="form-label">รายละเอียดคำขอ</label><textarea class="form-control" name="details" rows="4"><?= esc(old('details')??'') ?></textarea></div>
<div class="col-md-6"><label class="form-label">สถานที่ / หน่วยงาน</label><input class="form-control" name="location" value="<?= esc(old('location')??'') ?>"></div>
<div class="col-md-3"><label class="form-label">วันที่เริ่ม</label><input type="text" id="approvalStartDate" class="form-control flatpickr-thai-input" name="start_date" value="<?= esc(old('start_date')??'') ?>" placeholder="เลือกวันที่เริ่ม"></div>
<div class="col-md-3"><label class="form-label">วันที่สิ้นสุด</label><input type="text" id="approvalEndDate" class="form-control flatpickr-thai-input" name="end_date" value="<?= esc(old('end_date')??'') ?>" placeholder="เลือกวันที่สิ้นสุด"></div>
<div class="col-md-4"><label class="form-label">วงเงิน (บาท)</label><input type="number" min="0" step="0.01" class="form-control" name="amount" value="<?= esc(old('amount')??'0.00') ?>"></div>
<div class="col-md-8"><label class="form-label">เลขอ้างอิง / ข้อมูลเพิ่มเติม</label><input class="form-control" name="reference_no" value="<?= esc(old('reference_no')??'') ?>"></div>
</div></div>
<div class="d-flex justify-content-end gap-2"><a class="btn btn-outline-secondary" href="<?= base_url('admin/approvals') ?>">ยกเลิก</a><button class="btn btn-primary" type="submit"><i class="bx bx-send me-1"></i>บันทึกและส่งตามลำดับ</button></div>
</form>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const typeSelect = document.getElementById('approvalTypeId');
    const memoField = document.getElementById('memoField');
    const memoSelect = document.getElementById('approvalDocumentId');
    const memoLabel = document.getElementById('memoLabel');
    const memoHelp = document.getElementById('memoHelp');
    function syncMemoRequirement() {
        const option = typeSelect.options[typeSelect.selectedIndex];
        const selected = option && option.value !== '';
        const required = selected && option.dataset.requiresMemo === '1';
        memoField.style.display = required ? '' : (selected ? 'none' : '');
        memoSelect.required = !!required;
        memoLabel.classList.toggle('required', !!required);
        memoHelp.textContent = required
            ? 'ประเภทนี้ต้องสร้างบันทึกข้อความภายในและส่งเสนอแล้วก่อน จึงจะบันทึกคำขอได้'
            : 'ประเภทนี้ไม่บังคับบันทึกข้อความ สามารถกรอกและส่งแบบฟอร์มได้โดยตรง';
        if (!required) memoSelect.value = '';
    }
    typeSelect.addEventListener('change', syncMemoRequirement);
    syncMemoRequirement();
    if (typeof initThaiDatePicker === 'function') {
        initThaiDatePicker('#approvalStartDate');
        initThaiDatePicker('#approvalEndDate');
    }
});
</script>
<?= $this->endSection() ?>