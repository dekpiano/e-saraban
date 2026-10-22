<!doctype html>
<html lang="th"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>ชุดเอกสาร<?= esc($request['request_title']) ?></title>
<link href="<?= base_url('assets/vendor/fonts/sarabun-embedded.css') ?>" rel="stylesheet">
<style>
@page{size:A4;margin:22mm 22mm 20mm 30mm}*{box-sizing:border-box}body{font-family:'Sarabun',sans-serif;color:#111;font-size:16pt;line-height:1.45;margin:0}.toolbar{font-family:Arial,sans-serif;margin:16px auto;max-width:900px;display:flex;gap:8px}.toolbar a,.toolbar button{padding:8px 14px;border:1px solid #999;background:#fff;border-radius:6px;text-decoration:none;color:#111;font-size:14px}.sheet{page-break-after:always}.sheet:last-child{page-break-after:auto}.center{text-align:center}.right{text-align:right}.mt{margin-top:22px}.field{margin:8px 0}.line{display:inline-block;min-width:160px;border-bottom:1px dotted #333}.signatures{margin-top:42px;display:flex;justify-content:flex-end}.sig{width:55%;text-align:center}.step{margin:14px 0;padding:10px;border-bottom:1px solid #aaa}.small{font-size:13pt}.memo-content{white-space:normal;overflow-wrap:anywhere}@media print{.toolbar{display:none}}
</style></head><body>
<div class="toolbar"><button onclick="window.print()">พิมพ์ / บันทึก PDF</button><a href="<?= base_url('admin/approvals/'.(int)$request['id']) ?>">กลับรายละเอียด</a></div>
<?php if(!empty($request['memo_id'])): ?><section class="sheet">
<div class="center"><strong>บันทึกข้อความ</strong></div>
<div class="field">ส่วนราชการ <?= esc($request['memo_department']?:$request['from_org']) ?> โทร. <?= esc($request['memo_contact']?:'-') ?></div>
<div class="field">ที่ <?= esc($request['doc_no']) ?> <span style="margin-left:30px">วันที่ <?= esc($request['doc_date']) ?></span></div>
<div class="field">เรื่อง <?= esc($request['memo_title']) ?></div>
<div class="field">เรียน <?= esc($request['to_org']) ?></div>
<div class="memo-content mt"><?= $request['memo_content'] ?></div>
<?php if(!empty($request['creator_signature'])): ?><div class="signatures"><div class="sig"><img src="<?= base_url($request['creator_signature']) ?>" style="max-width:180px;max-height:70px" alt="ลายเซ็น"><div>(<?= esc(trim(($request['memo_creator_first_name']??'').' '.($request['memo_creator_last_name']??''))) ?>)</div></div></div><?php endif ?>
</section><?php endif ?>
<section class="sheet">
<div class="center"><strong>แบบฟอร์ม<?= $request['category']==='approval'?'ขออนุมัติ':'ขออนุญาต' ?></strong><br><strong><?= esc($request['type_name']) ?></strong></div>
<div class="field mt">เรื่อง <?= esc($request['request_title']) ?></div>
<?php if(!empty($request['memo_id'])): ?><div class="field">อ้างถึงบันทึกข้อความเลขที่ <?= esc($request['doc_no']) ?> เรื่อง <?= esc($request['memo_title']) ?></div><?php else: ?><div class="field">แบบฟอร์มนี้ไม่ต้องใช้บันทึกข้อความหลัก</div><?php endif ?>
<div class="field">วัตถุประสงค์ / เหตุผล</div><div><?= nl2br(esc($request['purpose']?:'-')) ?></div>
<div class="field mt">รายละเอียด</div><div><?= nl2br(esc($request['details']?:'-')) ?></div>
<div class="field mt">สถานที่ / หน่วยงาน <?= esc($request['location']?:'-') ?></div>
<div class="field">วันที่ <?= esc($request['start_date']?:'-') ?> ถึง <?= esc($request['end_date']?:'-') ?></div>
<div class="field">วงเงิน <?= number_format((float)$request['amount'],2) ?> บาท</div>
<div class="field mt">จึงเรียนมาเพื่อโปรดพิจารณา</div>
<div class="signatures"><div class="sig">(<?= esc(trim(($request['requester_prefix']??'').($request['requester_first_name']??'').' '.($request['requester_last_name']??''))) ?>)<br>ผู้ยื่นคำขอ</div></div>
<div class="mt"><strong>ผลการพิจารณา</strong>
<?php foreach($steps as $s): ?><div class="step"><strong><?= (int)$s['step_order'] ?>. <?= esc($s['step_name']) ?></strong><br>ผู้พิจารณา <?= esc(trim(($s['prefix']??'').($s['first_name']??'').' '.($s['last_name']??''))) ?><?= !empty($s['position_name'])?' · '.esc($s['position_name']):'' ?><br>ผล <?= ['approved'=>'เห็นชอบ / อนุมัติ','rejected'=>'ไม่อนุมัติ','returned'=>'ส่งกลับแก้ไข','pending'=>'รอพิจารณา','skipped'=>'ข้ามขั้น'][$s['status']]??esc($s['status']) ?><br>ความเห็น <?= nl2br(esc($s['comment']?:'-')) ?><br>วันที่ <?= esc($s['action_at']?:'-') ?></div><?php endforeach ?>
</div>
</section></body></html>