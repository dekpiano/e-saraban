<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>พิมพ์คำสั่งโรงเรียนเลขที่ <?= esc($order['doc_no']) ?> | e-Saraban</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= base_url('assets/img/favicon/favicon.ico') ?>" />

    <!-- Core Order CSS Stylesheet -->
    <link rel="stylesheet" href="<?= base_url('assets/css/orders_saraban.css?v=' . time()) ?>" />

    <style>
        body {
            background-color: #525659;
            margin: 0;
            padding: 20px 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .print-floating-bar {
            position: fixed;
            top: 15px;
            right: 20px;
            z-index: 9999;
            background: #ffffff;
            padding: 8px 16px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.25);
            display: flex;
            align-items: center;
            gap: 10px;
            font-family: 'K2D', sans-serif;
        }

        .print-btn {
            background: #4f46e5;
            color: #ffffff;
            border: none;
            padding: 6px 14px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 14px;
        }
        .print-btn:hover {
            background: #4338ca;
        }

        .close-btn {
            background: #f1f5f9;
            color: #334155;
            border: 1px solid #cbd5e1;
            padding: 6px 14px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
        }
        .close-btn:hover {
            background: #e2e8f0;
        }

        @media print {
            .print-floating-bar {
                display: none !important;
            }
            body {
                background: #ffffff !important;
                padding: 0 !important;
            }
        }
    </style>
</head>

<body>

    <!-- Floating Print Controller (Hidden in Print Mode) -->
    <div class="print-floating-bar no-print">
        <span style="font-size: 13px; color: #475569; font-weight: 500;">
            คำสั่งเลขที่: <strong><?= esc($order['doc_no']) ?></strong>
        </span>
        <button type="button" class="print-btn" onclick="window.print()">
            🖨️ พิมพ์เอกสาร (Print)
        </button>
        <a href="<?= base_url('admin/orders/' . $order['id']) ?>" class="close-btn">
            ✕ ปิด
        </a>
    </div>

    <!-- Official A4 Paper Container -->
    <div id="printableOrderPaper" class="order-print-outer-wrapper w-100 d-flex flex-column align-items-center">
        <div class="saraban-order-paper">

        <!-- 1. ตราครุฑกึ่งกลางหน้ากระดาษ (สูง 3.0 ซม. กว้าง 2.73 ซม.) -->
        <div class="order-garuda-center">
            <img src="<?= base_url('assets/img/krut_thai.png') ?>" alt="ตราครุฑ" class="order-garuda-img" />
        </div>

        <!-- 2. หัวคำสั่งโรงเรียน (16pt ตัวหนา กึ่งกลาง) -->
        <div class="order-header-block">
            <div class="order-school-title">
                คำสั่งโรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์
            </div>
            <div class="order-number-title">
                ที่ <?= esc($order['doc_no']) ?>
            </div>
            <div class="order-subject-title">
                เรื่อง <?= esc($order['title']) ?>
            </div>
            <div class="order-divider-line"></div>
        </div>

        <!-- 3. เนื้อหาคำสั่ง (ย่อหน้า 2.5 ซม. สระโปร่ง Thai Distribute) -->
        <div class="order-body-content">
            <?= !empty($order['content']) ? $order['content'] : '' ?>
        </div>

        <!-- 4. ย่อหน้าบังคับใช้ (ทั้งนี้ ตั้งแต่...) -->
        <?php if (!empty($order['order_effective_text'])): ?>
            <div class="order-effective-clause">
                ทั้งนี้ ตั้งแต่ <?= esc($order['order_effective_text']) ?>
            </div>
        <?php endif; ?>

        <!-- 5. วันสั่งการ (สั่ง ณ วันที่ ...) -->
        <div class="order-date-clause">
            สั่ง ณ วันที่ <?= !empty($order['doc_date']) ? esc(thai_date_full($order['doc_date'])) : '-' ?>
        </div>

        <!-- 6. ส่วนลงนามผู้อำนวยการ -->
        <div class="order-signature-container">
            <div class="order-signature-image-wrap">
                <?php if (!empty($order['creator_signature'])): ?>
                    <img src="<?= base_url(esc($order['creator_signature'])) ?>" alt="ลายเซ็น" class="order-signature-img" />
                <?php endif; ?>
            </div>
            <div class="order-signer-fullname">
                ( <?= esc($order['signer_name'] ?: 'นายพงษ์ศักดิ์ เงินสันเทียะ') ?> )
            </div>
            <div class="order-signer-position">
                <?= esc($order['signer_position'] ?: 'ผู้อำนวยการโรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์') ?>
            </div>
        </div>
    </div>

    </div>

    <!-- Multi-page Real Sheet Paginator for Order Paper -->
    <script src="<?= base_url('assets/js/orders_pagination.js?v=' . time()) ?>"></script>
    <script>
    function runOrderPagination() {
        SarabanOrderPaginator.paginate('printableOrderPaper');
    }
    if (document.readyState === 'complete') {
        runOrderPagination();
    } else {
        window.addEventListener('load', runOrderPagination);
        document.addEventListener('DOMContentLoaded', runOrderPagination);
    }
    </script>
</body>
</html>
