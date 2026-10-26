<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ใบสรุปหนังสือรับเข้า - <?= esc($document['internal_reg_no'] ?: $document['doc_no']) ?></title>
    
    <link rel="icon" type="image/x-icon" href="<?= base_url('assets/img/favicon/favicon.ico') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;600;700&family=K2D:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/saraban-memo.css?v=' . time()) ?>">

    <style>
        @page {
            size: A4 portrait;
            margin: 1.5cm 2cm 2cm 2.5cm;
        }
        body {
            background: #e2e8f0;
            font-family: 'Sarabun', 'TH Sarabun New', sans-serif;
            color: #0f172a;
            margin: 0;
            padding: 20px;
        }
        .slip-sheet {
            background: #ffffff;
            width: 21cm;
            min-height: 29.7cm;
            margin: 0 auto;
            padding: 1.5cm 2cm 2cm 2.5cm;
            box-sizing: border-box;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            position: relative;
        }
        .print-btn-bar {
            width: 21cm;
            margin: 0 auto 15px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
            }
            .slip-sheet {
                box-shadow: none !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                min-height: auto !important;
            }
            .print-btn-bar {
                display: none !important;
            }
        }
        .slip-header-title {
            font-size: 20pt;
            font-weight: bold;
            text-align: center;
            line-height: 1.3;
            margin-bottom: 4px;
        }
        .slip-header-sub {
            font-size: 14pt;
            text-align: center;
            color: #334155;
            margin-bottom: 18px;
        }
        .slip-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 15pt;
            margin-bottom: 20px;
        }
        .slip-table th, .slip-table td {
            border: 1px solid #475569;
            padding: 8px 12px;
            vertical-align: top;
        }
        .slip-table th {
            background-color: #f1f5f9;
            font-weight: bold;
            width: 28%;
            text-align: left;
        }
        .endorse-box {
            border: 1.5px solid #004085;
            border-radius: 6px;
            padding: 12px 16px;
            margin-bottom: 15px;
            background: #f8fafc;
        }
    </style>
</head>
<body>

    <div class="print-btn-bar">
        <a href="<?= base_url('admin/inbound/' . $document['id']) ?>" style="text-decoration: none; color: #475569; font-family: 'K2D', sans-serif; font-size: 14px;">
            ← กลับไปหน้าหนังสือรับ
        </a>
        <button onclick="window.print()" style="background: #2563eb; color: #fff; border: none; padding: 8px 20px; border-radius: 6px; font-family: 'K2D', sans-serif; font-size: 14px; font-weight: bold; cursor: pointer;">
            🖨️ พิมพ์ใบสรุป / สลิปนำส่ง (A4)
        </button>
    </div>

    <div class="slip-sheet">
        <div style="text-align: center; margin-bottom: 10px;">
            <img src="<?= base_url('assets/img/krut_thai.png') ?>" alt="ตราครุฑ" style="width: 2.5cm; height: auto;">
        </div>

        <div class="slip-header-title">ใบสรุปหนังสือรับเข้า & เส้นทางเกษียนสั่งการ</div>
        <div class="slip-header-sub">โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์</div>

        <table class="slip-table">
            <tr>
                <th>เลขทะเบียนรับ (สกจ.)</th>
                <td style="font-weight: bold; font-size: 16pt; color: #004085;">
                    <?= esc($document['internal_reg_no'] ?: '-') ?>
                </td>
            </tr>
            <tr>
                <th>เลขที่หนังสือต้นทาง</th>
                <td><?= esc($document['doc_no'] ?: '-') ?></td>
            </tr>
            <tr>
                <th>ลงวันที่ในหนังสือ</th>
                <td><?= esc(thai_date($document['doc_date'], 'medium')) ?></td>
            </tr>
            <tr>
                <th>จาก (หน่วยงาน)</th>
                <td><?= esc($document['from_org']) ?></td>
            </tr>
            <tr>
                <th>ถึง (ผู้รับ)</th>
                <td><?= esc($document['to_org']) ?></td>
            </tr>
            <tr>
                <th>เรื่อง</th>
                <td style="font-weight: bold;"><?= esc($document['title']) ?></td>
            </tr>
            <tr>
                <th>วันที่ & เวลารับเข้า</th>
                <td><?= esc(thai_date($document['receive_date'], 'medium')) ?> (<?= esc($document['receive_stamp_time'] ?: '-') ?>)</td>
            </tr>
            <tr>
                <th>ความเร่งด่วน / ชั้นความลับ</th>
                <td>
                    <?= $document['urgency'] === 'most_urgent' ? 'ด่วนที่สุด' : ($document['urgency'] === 'very_urgent' ? 'ด่วนมาก' : ($document['urgency'] === 'urgent' ? 'ด่วน' : 'ปกติ')) ?>
                    / <?= esc($document['secret_level']) ?>
                </td>
            </tr>
            <?php if (!empty($document['executive_instruction'])): ?>
                <tr>
                    <th>คำสั่งการของ ผอ.</th>
                    <td style="font-weight: bold; color: #166534; background: #f0fdf4;">
                        "<?= esc($document['executive_instruction']) ?>"
                    </td>
                </tr>
            <?php endif; ?>
            <?php if (!empty($document['assigned_first']) || !empty($document['assigned_department'])): ?>
                <tr>
                    <th>ผู้รับผิดชอบงาน</th>
                    <td>
                        <?= esc(($document['assigned_prefix'] ?? '') . $document['assigned_first'] . ' ' . $document['assigned_last']) ?>
                        <?= !empty($document['assigned_department']) ? ' (' . esc($document['assigned_department']) . ')' : '' ?>
                    </td>
                </tr>
            <?php endif; ?>
        </table>

        <!-- รายการเกษียนสั่งการ -->
        <div style="font-size: 16pt; font-weight: bold; margin-bottom: 10px; border-bottom: 2px solid #004085; padding-bottom: 4px;">
            บันทึกการเกษียนสั่งการตามลำดับ
        </div>

        <?php foreach ($routes as $r): ?>
            <div class="endorse-box">
                <div style="display: flex; justify-content: space-between; margin-bottom: 6px; font-weight: bold;">
                    <span>ขั้นที่ <?= $r['step_order'] ?> : <?= esc($r['approver_name']) ?> (<?= esc($r['personnel_position']) ?>)</span>
                    <span>สถานะ : <?= $r['status'] === 'signed' ? 'ลงนามแล้ว' : 'รอดำเนินการ' ?></span>
                </div>
                <?php if ($r['status'] === 'signed'): ?>
                    <div style="font-size: 15pt; color: #004085; font-weight: bold; margin-bottom: 4px;">
                        ความเห็น/คำสั่ง : "<?= esc($r['comment_preset']) ?>"
                    </div>
                    <?php if (!empty($r['comment_text'])): ?>
                        <div style="font-size: 14pt; margin-bottom: 8px;">
                            <?= nl2br(esc($r['comment_text'])) ?>
                        </div>
                    <?php endif; ?>
                    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-top: 10px;">
                        <div>
                            <?php if (!empty($r['signature_img'])): ?>
                                <img src="<?= base_url($r['signature_img']) ?>" alt="ลายเซ็น" style="height: 44px; max-width: 150px; object-fit: contain;">
                            <?php endif; ?>
                            <div style="font-size: 13pt; color: #475569;">( <?= esc($r['approver_name']) ?> )</div>
                        </div>
                        <div style="font-size: 12pt; color: #64748b;">
                            ลงนามเมื่อ <?= esc(thai_date($r['signed_at'], 'medium')) ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div style="color: #94a3b8; font-style: italic; font-size: 13pt;">
                        (อยู่ระหว่างรอการเสนอเกษียน)
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <div style="margin-top: 30px; font-size: 11pt; color: #94a3b8; text-align: right;">
            ออกโดยระบบงานสารบรรณอิเล็กทรอนิกส์ โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์ เมื่อ <?= thai_date(date('Y-m-d H:i:s'), 'medium') ?>
        </div>
    </div>

</body>
</html>
