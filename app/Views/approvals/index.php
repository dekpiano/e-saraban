<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?>ระบบขออนุมัติและขออนุญาต<?= $this->endSection() ?>
<?= $this->section('content') ?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
 <div><h4 class="mb-1">ระบบขออนุมัติและขออนุญาต</h4><p class="text-muted mb-0">ติดตามคำขอที่ยื่นและรายการที่ได้รับมอบหมายให้พิจารณา</p></div>
 <div class="d-flex gap-2"><a class="btn btn-primary" href="<?= base_url('admin/approvals/create') ?>"><i class="bx bx-plus me-1"></i>สร้างคำขอ</a><?php if(in_array((string)session()->get('role'),['admin','super_admin'],true)): ?><a class="btn btn-outline-secondary" href="<?= base_url('admin/approvals/settings') ?>">ตั้งค่าผู้พิจารณา</a><?php endif ?></div>
</div>
<?php if(session()->getFlashdata('success')): ?><div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div><?php endif ?>
<?php if(session()->getFlashdata('error')): ?><div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div><?php endif ?>
<div class="card"><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead><tr><th>เรื่องคำขอ</th><th>ประเภท</th><th>บันทึกข้อความหลัก</th><th>ผู้ยื่น</th><th>สถานะ</th><th>วันที่</th><th></th></tr></thead><tbody>
<?php if(empty($requests)): ?><tr><td colspan="7" class="text-center text-muted py-5">ยังไม่มีรายการคำขอ</td></tr><?php else: foreach($requests as $r): ?>
<tr><td><a class="fw-semibold" href="<?= base_url('admin/approvals/'.(int)$r['id']) ?>"><?= esc($r['title']) ?></a></td><td><?= esc($r['type_name']) ?></td><td><a href="<?= base_url('admin/memos/'.(int)$r['document_id']) ?>"><?= esc($r['doc_no'].' · '.$r['memo_title']) ?></a></td><td><?= esc(trim(($r['first_name']??'').' '.($r['last_name']??''))) ?></td><td><?php $labels=['pending'=>'รอพิจารณา','approved'=>'อนุมัติแล้ว','rejected'=>'ไม่อนุมัติ','returned'=>'ส่งกลับแก้ไข','cancelled'=>'ยกเลิก']; $colors=['pending'=>'warning','approved'=>'success','rejected'=>'danger','returned'=>'info','cancelled'=>'secondary']; ?><span class="badge bg-label-<?= $colors[$r['status']]??'secondary' ?>"><?= $labels[$r['status']]??esc($r['status']) ?></span></td><td><?= esc($r['created_at']) ?></td><td><a class="btn btn-sm btn-outline-primary" href="<?= base_url('admin/approvals/'.(int)$r['id']) ?>">รายละเอียด</a></td></tr>
<?php endforeach; endif ?></tbody></table></div></div>
<?= $this->endSection() ?>