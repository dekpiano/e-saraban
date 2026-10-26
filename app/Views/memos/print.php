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
    
    <!-- ฟอนต์ TH SarabunIT๙ & TH Sarabun New ฝังแบบ Base64 -->
    <link rel="stylesheet" href="<?= base_url('assets/vendor/fonts/sarabun-embedded.css?v=' . (file_exists(FCPATH . 'assets/vendor/fonts/sarabun-embedded.css') ? filemtime(FCPATH . 'assets/vendor/fonts/sarabun-embedded.css') : time())) ?>">
    
    <!-- Boxicons -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    <!-- สไตล์ชีตบันทึกข้อความมาตรฐานราชการไทย -->
    <link rel="stylesheet" href="<?= base_url('assets/css/saraban-memo.css?v=' . (file_exists(FCPATH . 'assets/css/saraban-memo.css') ? filemtime(FCPATH . 'assets/css/saraban-memo.css') : time())) ?>">

    <style>
        .page {
            background-color: white;
            width: 21cm;
            height: 29.7cm;
            min-height: 29.7cm;
            max-height: 29.7cm;
            padding: 1.5cm 2cm 2cm 3cm;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
            position: relative;
            box-sizing: border-box;
            overflow: hidden;
            margin: 0 auto;
        }

        @media print {
            @page {
                size: A4 portrait;
                margin: 0;
            }
            body {
                background: #ffffff !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .page-container {
                padding: 0 !important;
                margin: 0 !important;
                width: 21cm !important;
                height: 29.7cm !important;
            }
            .page {
                box-shadow: none !important;
                border: none !important;
                width: 21cm !important;
                height: 29.7cm !important;
                min-height: 29.7cm !important;
                max-height: 29.7cm !important;
                padding: 1.5cm 2cm 2cm 3cm !important;
                overflow: hidden !important;
                page-break-after: avoid !important;
                break-after: avoid !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
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
        <div class="page" id="printableMemoPaper" style="position: relative;">
            <?php
            $showStamp = isset($document['has_receive_stamp']) ? ((int)$document['has_receive_stamp'] === 1) : true;
            $hasReceiveStamp = $showStamp && (!empty($document['internal_reg_no']) || !empty($document['received_at']) || (!empty($document['doc_no']) && ($document['status'] ?? '') !== 'pending_saraban'));
            ?>
            <?php if ($hasReceiveStamp): ?>
                <?php
                $rX = !empty($document['receive_stamp_x']) ? $document['receive_stamp_x'] : '72.2%';
                $rY = !empty($document['receive_stamp_y']) ? $document['receive_stamp_y'] : '3.5%';
                $rScale = !empty($document['receive_stamp_scale']) ? (float)$document['receive_stamp_scale'] : 1.0;
                $rReg = !empty($document['internal_reg_no']) ? $document['internal_reg_no'] : (!empty($document['doc_no']) ? $document['doc_no'] : '-');
                $rDate = !empty($document['receive_date']) ? $document['receive_date'] : ($document['doc_date'] ?? date('Y-m-d'));
                $rTime = !empty($document['receive_stamp_time']) ? $document['receive_stamp_time'] : (!empty($document['received_at']) ? date('H:i น.', strtotime($document['received_at'])) : date('H:i น.'));
                ?>
                <div id="issuedReceiveStamp" class="saraban-receive-stamp-img-wrap shadow-xs" style="position: absolute; left: <?= esc($rX) ?>; top: <?= esc($rY) ?>; width: 23.8%; aspect-ratio: 452 / 227; height: auto; transform: rotate(-1deg) scale(<?= $rScale ?>); transform-origin: top left; z-index: 50; display: block;">
                    <div style="position: relative; width: 100%; height: 100%; background: #ffffff; border-radius: 1px; overflow: hidden; box-shadow: 0 4px 12px rgba(20,42,130,0.18);">
                        <img src="<?= base_url('assets/img/stamp_template_skj.png') ?>" alt="ตรายาง รร.ส.ก.จ." style="width: 100%; height: 100%; display: block; object-fit: fill; pointer-events: none;">
                        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; font-family: 'TH SarabunIT๙', 'TH Sarabun New', 'Sarabun', sans-serif; font-weight: bold; color: #142a82; line-height: 1;">
                            <span style="position: absolute; left: 27%; top: 43%; transform: translateY(-82%); font-size: 1.3vw; min-font-size: 13pt; max-font-size: 17pt; white-space: nowrap;"><?= esc(to_thai_number($rReg)) ?></span>
                            <span style="position: absolute; left: 25.2%; top: 66.3%; transform: translateY(-82%); font-size: 1.3vw; min-font-size: 13pt; max-font-size: 17pt; white-space: nowrap;"><?= esc(to_thai_number(thai_date($rDate, 'medium'))) ?></span>
                            <span style="position: absolute; left: 24%; top: 89.6%; transform: translateY(-82%); font-size: 1.3vw; min-font-size: 13pt; max-font-size: 17pt; white-space: nowrap;"><?= esc(to_thai_number($rTime)) ?></span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

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
            <div class="body-text indent space-before-6pt">
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
                    <div class="sign-position">ตำแหน่ง <?= esc($creator['position_name'] ?? 'ครูผู้สอน') ?></div>
                </div>
            </div>

            <!-- การเกษียนลงนามตามลำดับขั้น: ตราปั๊มหมึกน้ำเงินเสมือนจริง (Realistic Blue Rubber Stamps) -->
            <?php 
            $signedRoutes = array_filter($routes ?? [], function($r) {
                return $r['status'] === 'signed';
            });
            $defaultPositions = [
                1 => ['x' => '8%',  'y' => '68%', 'rot' => '-1.5deg'],
                2 => ['x' => '52%', 'y' => '68%', 'rot' => '1.2deg'],
                3 => ['x' => '8%',  'y' => '82%', 'rot' => '-2.0deg'],
                4 => ['x' => '52%', 'y' => '82%', 'rot' => '1.5deg'],
            ];
            ?>
            <?php if (!empty($signedRoutes)): ?>
                <?php foreach ($signedRoutes as $sr): ?>
                    <?php
                    $stepIdx = (int)($sr['step_order'] ?? 1);
                    $posX = !empty($sr['stamp_x']) ? $sr['stamp_x'] : ($defaultPositions[$stepIdx]['x'] ?? '8%');
                    $posY = !empty($sr['stamp_y']) ? $sr['stamp_y'] : ($defaultPositions[$stepIdx]['y'] ?? '70%');
                    $scale = !empty($sr['stamp_scale']) ? (float)$sr['stamp_scale'] : 1.0;
                    $widthStyle = !empty($sr['stamp_width']) ? 'width: ' . esc($sr['stamp_width']) . ';' : '';
                    $rot  = $defaultPositions[$stepIdx]['rot'] ?? '-1.5deg';
                    $transformStyle = "transform: rotate({$rot}) scale({$scale}); transform-origin: top left;";
                    ?>
                    <div class="saraban-blue-stamp shadow-sm" style="left: <?= esc($posX) ?>; top: <?= esc($posY) ?>; <?= $transformStyle ?> <?= $widthStyle ?>">
                        <div class="stamp-preset"><?= esc($sr['comment_preset']) ?></div>
                        <?php if (!empty($sr['comment_text'])): ?>
                            <div class="stamp-comment">"<?= esc($sr['comment_text']) ?>"</div>
                        <?php endif; ?>
                        <div class="stamp-sig-wrap">
                            <?php if (!empty($sr['signature_img'])): ?>
                                <img src="<?= base_url($sr['signature_img']) ?>" alt="ลายเซ็น" class="stamp-sig-img">
                            <?php endif; ?>
                        </div>
                        <div class="stamp-signer-name">( <?= esc(($sr['prefix'] ?? '') . ($sr['first_name'] ?? '') . ' ' . ($sr['last_name'] ?? '')) ?> )</div>
                        <div class="stamp-signer-pos"><?= esc($sr['position_name'] ?? '') ?></div>
                        <div class="stamp-footer">
                            <span>วันที่ <?= thai_date($sr['signed_at'], 'short') ?></span>
                            <?php if (!empty($sr['verify_hash'])): ?>
                                <a href="<?= base_url('verify/' . $sr['verify_hash']) ?>" target="_blank" class="stamp-verify-badge text-decoration-none">
                                    ✓ ตรวจสอบ
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
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
