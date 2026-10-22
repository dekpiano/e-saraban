<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>พิมพ์ชุดเอกสารไปราชการ<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
.bundle-toolbar { position: sticky; top: 0; z-index: 100; background: #fff; padding: 12px 0; border-bottom: 1px solid #dee2e6; }
.bundle-page { background: #fff; width: 210mm; min-height: 297mm; margin: 18px auto; padding: 22mm 24mm; box-shadow: 0 2px 14px rgba(0,0,0,.12); color: #111; font-family: 'TH Sarabun New', 'Sarabun', sans-serif; font-size: 16pt; line-height: 1.45; }
.bundle-page h2 { text-align: center; font-size: 22pt; margin: 0 0 18px; }
.bundle-meta { border-bottom: 1px solid #bbb; padding-bottom: 10px; margin-bottom: 18px; font-size: 14pt; }
.bundle-content { overflow-wrap: anywhere; }
.bundle-content img { max-width: 100%; height: auto; }
.bundle-field { margin: 7px 0; }
@media print {
    body { background: #fff !important; }
    .bundle-toolbar, .no-print, nav, aside, header, footer { display: none !important; }
    .container, .container-fluid, main { width: 100% !important; max-width: none !important; margin: 0 !important; padding: 0 !important; }
    .bundle-page { width: 100%; min-height: 0; margin: 0; padding: 18mm 22mm; box-shadow: none; page-break-after: always; break-after: page; }
    .bundle-page:last-child { page-break-after: auto; break-after: auto; }
}
</style>

<div class="bundle-toolbar no-print">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h5 class="mb-1 fw-bold"><i class="bx bx-printer me-2"></i>พิมพ์ชุดเอกสารไปราชการ</h5>
            <div class="small text-muted">รวมบันทึกข้อความที่เชื่อมโยงและข้อมูลรายการในชุดเดียวกัน</div>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-primary" onclick="window.print()"><i class="bx bx-printer me-1"></i>พิมพ์ / บันทึก PDF</button>
            <a class="btn btn-outline-secondary" href="<?= base_url('admin/travel/' . (int)$travel['id']) ?>">กลับหน้ารายการ</a>
        </div>
    </div>
</div>

<?php foreach ($memos as $memo): ?>
    <section class="bundle-page">
        <div class="text-center mb-3">
            <img src="<?= base_url('assets/img/krut_thai.png') ?>" alt="ตราครุฑ" style="height:70px;max-width:100px;object-fit:contain">
            <h2>บันทึกข้อความ</h2>
        </div>
        <div class="bundle-meta">
            <div><strong>ส่วนราชการ:</strong> <?= esc($memo['memo_department'] ?? $memo['from_org'] ?? 'โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์') ?></div>
            <div class="row">
                <div class="col-7"><strong>ที่:</strong> <?= esc($memo['doc_no'] ?? '-') ?></div>
                <div class="col-5"><strong>วันที่:</strong> <?= !empty($memo['doc_date']) ? esc(thai_date($memo['doc_date'], 'medium')) : '-' ?></div>
            </div>
            <div><strong>เรื่อง:</strong> <?= esc($memo['title'] ?? '-') ?></div>
            <div><strong>เรียน:</strong> <?= esc($memo['to_org'] ?? '-') ?></div>
        </div>
        <div class="bundle-content"><?= $memo['content'] ?? '' ?></div>
        <?php if (!empty($memo['creator_signature'])): ?>
            <div class="mt-4 text-end">
                <img src="<?= base_url($memo['creator_signature']) ?>" alt="ลายมือชื่อผู้จัดทำ" style="max-height:80px;max-width:180px;object-fit:contain">
            </div>
        <?php endif; ?>
        <div class="mt-4 small text-muted text-end">เอกสารที่เชื่อมโยงกับคำขอไปราชการเลขที่ <?= esc($travel['doc_no'] ?? '-') ?></div>
    </section>
<?php endforeach; ?>

<section class="bundle-page">
    <h2>รายละเอียดคำขออนุมัติไปราชการ</h2>
    <div class="bundle-meta">
        <div><strong>เลขที่คำขอ:</strong> <?= esc($travel['doc_no'] ?? '-') ?></div>
        <div><strong>เรื่อง:</strong> <?= esc($travel['title'] ?? '-') ?></div>
        <div><strong>สถานะ:</strong> <?= esc($travel['status'] ?? '-') ?></div>
    </div>
    <div class="bundle-field"><strong>สถานที่เดินทาง:</strong> <?= esc($travel['destination'] ?? '-') ?> จังหวัด<?= esc($travel['province'] ?? '-') ?></div>
    <div class="bundle-field"><strong>วันที่เดินทาง:</strong> <?= !empty($travel['start_date']) ? esc(thai_date($travel['start_date'], 'medium')) : '-' ?> ถึง <?= !empty($travel['end_date']) ? esc(thai_date($travel['end_date'], 'medium')) : '-' ?> (<?= esc($travel['total_days'] ?? '-') ?> วัน)</div>
    <div class="bundle-field"><strong>ผู้ร่วมเดินทาง:</strong> <?= esc($travel['companions'] ?? 'ไม่มี') ?></div>
    <div class="bundle-field"><strong>ประเภท/วงเงินงบประมาณ:</strong> <?= esc($travel['budget_type'] ?? '-') ?> / <?= number_format((float)($travel['budget_amount'] ?? 0), 2) ?> บาท</div>
    <?php if (!empty($travel['report_detail'])): ?>
        <hr class="my-4">
        <h2>รายงานผลการไปราชการ</h2>
        <div class="bundle-content"><?= nl2br(esc($travel['report_detail'])) ?></div>
        <div class="bundle-field mt-3"><strong>วันที่รายงานผล:</strong> <?= !empty($travel['reported_at']) ? esc(thai_date(substr($travel['reported_at'], 0, 10), 'medium')) : '-' ?></div>
    <?php endif; ?>
    <?php if (empty($memos)): ?>
        <p class="mt-4"><strong>หมายเหตุ:</strong> ยังไม่มีบันทึกข้อความเชื่อมโยงกับรายการนี้</p>
    <?php endif; ?>
</section>
<?= $this->endSection() ?>
