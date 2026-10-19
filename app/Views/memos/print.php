<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>พิมพ์บันทึกข้อความ - <?= esc($document['doc_no']) ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= base_url('assets/img/favicon/favicon.ico') ?>">

    <!-- Google Fonts: Sarabun & K2D -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;600;700&family=K2D:wght@400;600;700&display=swap" rel="stylesheet">
    
    <!-- ฟอนต์ TH Sarabun New ฝังแบบ Base64 -->
    <link rel="stylesheet" href="<?= base_url('assets/vendor/fonts/sarabun-embedded.css') ?>">
    
    <!-- Boxicons -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    <!-- สไตล์ชีตบันทึกข้อความมาตรฐานราชการไทย -->
    <link rel="stylesheet" href="<?= base_url('assets/css/saraban-memo.css?v=' . (file_exists(FCPATH . 'assets/css/saraban-memo.css') ? filemtime(FCPATH . 'assets/css/saraban-memo.css') : time())) ?>">

    <style>
        /* สไตล์สำหรับส่วนการเกษียน/คำสั่งการเพิ่มเติม */
        .endorse-section {
            margin-top: 30px;
            border-top: 1px dashed #94a3b8;
            padding-top: 16px;
            page-break-inside: avoid;
        }

        .endorse-card {
            border: 1px solid #cbd5e1;
            padding: 10px 14px;
            margin-bottom: 12px;
            font-size: var(--font-normal);
            line-height: 1.25;
            background-color: #fafafa;
            border-radius: 4px;
        }

        @media print {
            .endorse-section {
                border-top: 1px dashed #000;
            }
            .endorse-card {
                border: 1px solid #333;
                background-color: transparent !important;
            }
        }
    </style>
</head>
<body>

    <!-- แถบเมนูควบคุมก่อนพิมพ์ (Floating Action Topbar) -->
    <div class="floating-action-bar no-print">
        <div class="bar-title">
            <i class="bx bx-printer text-primary"></i> พิมพ์บันทึกข้อความ: <?= esc($document['doc_no']) ?>
        </div>

        <button type="button" class="bar-btn" onclick="window.close()" title="ปิดหน้าต่างนี้">
            <i class="bx bx-x"></i> ปิดหน้านี้
        </button>

        <!-- ปุ่มปรับย่อ/ขยายมุมมองหน้าจอ -->
        <div class="bar-zoom-group" title="ปรับย่อ/ขยายมุมมองหน้าจอ (ไม่มีผลต่อการพิมพ์)">
            <button type="button" class="bar-zoom-btn" onclick="adjustZoom(-0.05)" title="ย่อหน้ากระดาษ"><i class="bx bx-minus"></i></button>
            <span class="bar-zoom-label" id="zoomLevelText">100%</span>
            <button type="button" class="bar-zoom-btn" onclick="adjustZoom(0.05)" title="ขยายหน้ากระดาษ"><i class="bx bx-plus"></i></button>
            <button type="button" class="bar-zoom-btn" onclick="resetZoom()" title="ขนาดปกติ 100%"><i class="bx bx-reset"></i></button>
        </div>

        <!-- ป้ายแสดงสถานะจำนวนหน้า (Page Counter) -->
        <div id="memoPageCounterBadge" class="bar-page-counter badge-single-page" onclick="scrollToNextPageBreak()" title="สถานะความยาวเอกสาร A4 (คลิกเพื่อเลื่อนไปยังจุดแบ่งหน้า)">
            <i class="bx bx-file text-white me-1"></i> เอกสาร ๑ แผ่น (A4)
        </div>

        <button type="button" class="bar-btn bar-btn-print" onclick="window.print()" title="สั่งพิมพ์เอกสารหรือบันทึกเป็น PDF ขนาด A4">
            <i class="bx bx-printer"></i> สั่งพิมพ์เอกสาร (Print / PDF)
        </button>
    </div>

    <!-- ส่วนเนื้อหากระดาษ A4 -->
    <div class="page-container">
        <div class="page" id="printableMemoPaper">
            <div class="header">
                <img src="<?= base_url('assets/img/krut_thai.png') ?>" alt="ตราครุฑ" class="garuda">
                <div class="document-title">บันทึกข้อความ</div>
            </div>

            <div class="form-row">
                <span class="label">ส่วนราชการ</span>
                <span class="editable-field"><?= esc($document['from_org'] ?: $document['memo_department']) ?><?= !empty($document['memo_contact']) ? ' โทร. ' . esc($document['memo_contact']) : '' ?></span>
            </div>

            <div class="col-2">
                <div class="col-left">
                    <span class="label">ที่</span>
                    <span class="editable-field"><?= esc($document['doc_no']) ?></span>
                </div>
                <div class="col-right">
                    <span class="label">วันที่</span>
                    <span class="editable-field"><?= thai_date($document['doc_date'], 'medium') ?></span>
                </div>
            </div>

            <div class="form-row subject-row">
                <span class="label">เรื่อง</span>
                <span class="editable-field"><?= esc($document['title']) ?></span>
            </div>

            <div class="salutation space-before-6pt">
                <span class="label">เรียน</span>
                <span class="editable-field" style="border-bottom: none;"><?= esc($document['to_org']) ?></span>
            </div>

            <!-- เนื้อหาบันทึกข้อความ -->
            <div class="body-text space-before-6pt">
                <?= $document['content'] ?>
            </div>

            <!-- ส่วนลงนามผู้ขอ/เจ้าของเรื่อง -->
            <div class="signature-section">
                <div class="signature-box">
                    <div class="signature-line">
                        <?php if (!empty($document['creator_signature'])): ?>
                            <img src="<?= base_url($document['creator_signature']) ?>" alt="ลายเซ็น" class="signature-img">
                        <?php endif; ?>
                    </div>
                    <div class="sign-name">( <?= esc(($creator['prefix'] ?? '') . ($creator['first_name'] ?? '') . ' ' . ($creator['last_name'] ?? '')) ?> )</div>
                    <div class="sign-position">ตำแหน่ง <?= esc($creator['position_name'] ?? 'ครูผู้สอน') ?><br><?= esc($creator['department_name'] ?? '') ?></div>
                </div>
            </div>

            <!-- คำสั่งการ / การเกษียนลงนามตามลำดับขั้น (Digital Endorsements) -->
            <?php 
            $signedRoutes = array_filter($routes ?? [], function($r) {
                return $r['status'] === 'signed';
            });
            ?>
            <?php if (!empty($signedRoutes)): ?>
                <div class="endorse-section">
                    <div style="font-weight: 700; font-size: var(--font-normal); margin-bottom: 10px;">
                        คำสั่งการ / การเกษียนลงนามตามลำดับขั้น (Digital Endorsements)
                    </div>
                    <div style="display: flex; flex-wrap: wrap; gap: 12px;">
                        <?php foreach ($signedRoutes as $sr): ?>
                            <div style="flex: 1 1 calc(50% - 12px); box-sizing: border-box;">
                                <div class="endorse-card">
                                    <div style="font-weight: 700; color: #1e3a8a; margin-bottom: 4px;">
                                        <?= esc($sr['comment_preset']) ?>
                                    </div>
                                    <?php if (!empty($sr['comment_text'])): ?>
                                        <div style="margin-bottom: 6px;"><?= nl2br(esc($sr['comment_text'])) ?></div>
                                    <?php endif; ?>
                                    <div style="text-align: center; padding-top: 6px; border-top: 1px dotted #cbd5e1;">
                                        <?php if (!empty($sr['signature_img'])): ?>
                                            <img src="<?= base_url($sr['signature_img']) ?>" alt="ลายเซ็น" style="max-height: 40px; max-width: 130px; display: block; margin: 0 auto 4px auto;">
                                        <?php endif; ?>
                                        <div style="font-weight: 600;"><?= esc(($sr['prefix'] ?? '') . ($sr['first_name'] ?? '') . ' ' . ($sr['last_name'] ?? '')) ?></div>
                                        <div style="font-size: 13px; color: #475569;"><?= esc($sr['position_name'] ?? '') ?> (<?= thai_date($sr['signed_at'], 'short') ?>)</div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
    // ฟังก์ชันย่อ/ขยายมุมมองหน้ากระดาษ (ไม่มีผลต่อการสั่งพิมพ์)
    let currentZoom = 1.0;
    function adjustZoom(delta) {
        currentZoom = Math.min(Math.max(currentZoom + delta, 0.7), 1.4);
        const page = document.getElementById('printableMemoPaper');
        if (page) {
            page.style.transform = `scale(${currentZoom})`;
            page.style.transformOrigin = 'top center';
        }
        const label = document.getElementById('zoomLevelText');
        if (label) label.textContent = Math.round(currentZoom * 100) + '%';
    }

    function resetZoom() {
        currentZoom = 1.0;
        const page = document.getElementById('printableMemoPaper');
        if (page) {
            page.style.transform = 'none';
        }
        const label = document.getElementById('zoomLevelText');
        if (label) label.textContent = '100%';
    }

    // ==========================================
    // ระบบตรวจจับและแสดงเส้นแบ่งหน้า A4 อัตโนมัติ (Page Break Detection System)
    // ==========================================
    let cachedA4Height = 0;
    function getA4PageHeightPx() {
        if (cachedA4Height > 0) return cachedA4Height;
        const testDiv = document.createElement('div');
        testDiv.style.height = '29.7cm';
        testDiv.style.position = 'absolute';
        testDiv.style.left = '-9999px';
        testDiv.style.top = '-9999px';
        testDiv.style.visibility = 'hidden';
        document.body.appendChild(testDiv);
        cachedA4Height = testDiv.getBoundingClientRect().height;
        document.body.removeChild(testDiv);
        return cachedA4Height || 1122.52;
    }

    function updatePageBreakIndicators() {
        const page = document.getElementById('printableMemoPaper');
        if (!page) return;

        page.querySelectorAll('.page-break-indicator, .page-header-number').forEach(el => el.remove());

        const a4Height = getA4PageHeightPx();
        const currentHeight = Math.max(page.scrollHeight, page.offsetHeight);
        const threshold = 15;
        const totalPages = (currentHeight > a4Height + threshold)
            ? Math.ceil((currentHeight - threshold) / a4Height)
            : 1;

        const counterBadge = document.getElementById('memoPageCounterBadge');
        const thaiDigits = ['๐', '๑', '๒', '๓', '๔', '๕', '๖', '๗', '๘', '๙'];

        if (counterBadge) {
            if (totalPages > 1) {
                const pageThai = totalPages < 10 ? thaiDigits[totalPages] : totalPages;
                counterBadge.innerHTML = `<i class="bx bxs-file text-warning me-1"></i> <span class="fw-bold text-warning">ขึ้นแผ่นที่ ${pageThai} แล้ว</span> (รวม ${pageThai} หน้า)`;
                counterBadge.className = 'bar-page-counter badge-multi-page';
            } else {
                counterBadge.innerHTML = `<i class="bx bx-file text-white me-1"></i> เอกสาร ๑ แผ่น (A4)`;
                counterBadge.className = 'bar-page-counter badge-single-page';
            }
        }

        if (totalPages <= 1) return;

        for (let p = 2; p <= totalPages; p++) {
            const topPx = (p - 1) * a4Height;
            const pageThai = p < 10 ? thaiDigits[p] : p;
            const prevPageThai = (p - 1) < 10 ? thaiDigits[p - 1] : (p - 1);

            const indicator = document.createElement('div');
            indicator.className = 'page-break-indicator no-print';
            indicator.style.top = topPx + 'px';
            indicator.id = 'pageBreak_' + p;
            indicator.innerHTML = `<span class="page-break-badge"><i class="bx bx-cut"></i> สิ้นสุดแผ่นที่ ${prevPageThai} / 📄 ขึ้นแผ่นที่ ${pageThai} (- ${pageThai} -)</span>`;
            page.appendChild(indicator);

            const headerNum = document.createElement('div');
            headerNum.className = 'page-header-number no-print';
            headerNum.style.top = (topPx + 24) + 'px';
            headerNum.innerHTML = `- ${pageThai} -`;
            page.appendChild(headerNum);
        }
    }

    function scrollToNextPageBreak() {
        const firstBreak = document.querySelector('.page-break-indicator');
        if (firstBreak) {
            firstBreak.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(updatePageBreakIndicators, 150);
        window.addEventListener('resize', updatePageBreakIndicators);
    });
    </script>
</body>
</html>
