<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>บันทึกข้อความ - แม่แบบราชการ</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= base_url('assets/img/favicon/favicon.ico') ?>">

    <!-- Google Fonts: Sarabun & K2D -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=K2D:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- ฟอนต์ TH SarabunIT๙ & TH Sarabun New ฝังแบบ Base64 -->
    <link rel="stylesheet" href="<?= base_url('assets/vendor/fonts/sarabun-embedded.css?v=' . (file_exists(FCPATH . 'assets/vendor/fonts/sarabun-embedded.css') ? filemtime(FCPATH . 'assets/vendor/fonts/sarabun-embedded.css') : time())) ?>">

    <!-- Boxicons สำหรับปุ่มควบคุม -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    <!-- สไตล์ชีตบันทึกข้อความมาตรฐานราชการไทย -->
    <link rel="stylesheet"
        href="<?= base_url('assets/css/saraban-memo.css?v=' . (file_exists(FCPATH . 'assets/css/saraban-memo.css') ? filemtime(FCPATH . 'assets/css/saraban-memo.css') : time())) ?>">
</head>

<body>

    <!-- แถบเมนูลอยแบบ Clean & Modern ไม่รบกวนหน้าเอกสาร -->
    <div class="floating-action-bar no-print">
        <div class="bar-title">
            <i class="bx bxs-file-doc text-primary"></i> บันทึกข้อความราชการ
        </div>

        <a href="<?= base_url('admin/memos') ?>" class="bar-btn" title="กลับสู่หน้ารายการตารางบันทึกข้อความ">
            <i class="bx bx-arrow-back"></i> กลับตาราง
        </a>

        <!-- ปุ่มปรับย่อ/ขยายมุมมองหน้าจอ -->
        <div class="bar-zoom-group" title="ปรับย่อ/ขยายมุมมองหน้าจอ (ไม่มีผลต่อการพิมพ์)">
            <button type="button" class="bar-zoom-btn" onclick="adjustZoom(-0.05)" title="ย่อหน้ากระดาษ"><i
                    class="bx bx-minus"></i></button>
            <span class="bar-zoom-label" id="zoomLevelText">100%</span>
            <button type="button" class="bar-zoom-btn" onclick="adjustZoom(0.05)" title="ขยายหน้ากระดาษ"><i
                    class="bx bx-plus"></i></button>
            <button type="button" class="bar-zoom-btn" onclick="resetZoom()" title="ขนาดปกติ 100%"><i
                    class="bx bx-reset"></i></button>
        </div>

        <!-- ป้ายแสดงสถานะจำนวนหน้า (Page Counter) -->
        <div id="memoPageCounterBadge" class="bar-page-counter badge-single-page" onclick="scrollToNextPageBreak()"
            title="สถานะความยาวเอกสาร A4 (คลิกเพื่อเลื่อนไปยังจุดแบ่งหน้า)">
            <i class="bx bx-file text-white me-1"></i> เอกสาร ๑ แผ่น (A4)
        </div>

        <button type="button" class="bar-btn" onclick="convertNumbersInBody()" title="แปลงตัวเลขในเนื้อหาเป็นเลขไทย">
            <span style="font-size:14px; font-weight:bold;">๑๒๓</span> แปลงเลขไทย
        </button>

        <button type="button" class="bar-btn" onclick="formatThaiTypography()"
            title="จัดระเบียบวรรคตอนและตัดคำไทยอัตโนมัติ ไม่ให้คำขาดหรือตกบรรทัด">
            <i class="bx bx-font-size" style="color: #60a5fa;"></i> จัดคำไทยสวย
        </button>

        <button type="button" class="bar-btn" id="btnSignatureToggle" onclick="openSignatureModal()"
            title="เพิ่ม/จัดการลายเซ็นดิจิทัลออนไลน์">
            <i class="bx bx-pen" style="color: #fbbf24;"></i> ลายเซ็นออนไลน์
            <span id="sigStatusBadge"
                style="background: rgba(255,255,255,0.18); border-radius: 10px; padding: 1px 7px; font-size: 10px; margin-left: 4px; font-weight: normal;">เว้นว่าง</span>
        </button>

        <button type="button" class="bar-btn bar-btn-settings" onclick="toggleDrawer()"
            title="ตั้งค่าผู้ตรวจ/เกษียนหนังสือ และแนบไฟล์เอกสารเพิ่มเติม">
            <i class="bx bx-slider-alt"></i> ตั้งค่าเดินเรื่อง
        </button>

        <button type="button" class="bar-btn bar-btn-print" onclick="window.print()"
            title="สั่งพิมพ์เอกสารหรือบันทึกเป็น PDF ขนาด A4">
            <i class="bx bx-printer"></i> พิมพ์ / PDF
        </button>

        <button type="button" class="bar-btn bar-btn-draft" onclick="submitMemoForm('draft')" title="บันทึกฉบับร่าง">
            <i class="bx bx-save"></i> บันทึกร่าง
        </button>

        <button type="button" class="bar-btn bar-btn-submit" onclick="submitMemoForm('route')"
            title="ส่งบันทึกข้อความให้เจ้าหน้าที่สารบรรณตรวจสอบก่อนออกเลข">
            <i class="bx bx-send"></i> ส่งให้สารบรรณตรวจสอบ
        </button>
    </div>

    <!-- ฟอร์มหลักครอบคลุมเอกสารทั้งหมด -->
    <form id="memoForm" action="<?= base_url('admin/memos/store') ?>" method="POST" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <input type="hidden" name="from_org" id="hidden_from_org">
        <input type="hidden" name="memo_contact" id="hidden_memo_contact">
        <input type="hidden" name="doc_no" id="hidden_doc_no">
        <input type="hidden" name="internal_reg_no" value="<?= esc($nextRegNo) ?>">
        <input type="hidden" name="doc_date" id="actualDocDate" value="<?= date('Y-m-d') ?>">
        <input type="hidden" name="title" id="hidden_title">
        <input type="hidden" name="to_org" id="hidden_to_org">
        <input type="hidden" name="content" id="hiddenContent">
        <input type="hidden" name="signature_data" id="hidden_signature_data" value="">
        <input type="hidden" name="remove_signature" id="hidden_remove_signature" value="0">
        <input type="hidden" name="submit_action" id="submitAction" value="route">
        <input type="hidden" name="travel_request_id" value="<?= (int)($travelRequestId ?? 0) ?>">
        <input type="hidden" name="workflow_purpose" value="<?= esc($workflowPurpose ?? '') ?>">
        <input type="hidden" name="context_travel_id" value="<?= (int)($contextTravelId ?? 0) ?>">
        <?php if (in_array(($workflowPurpose ?? ''), ['travel_request', 'travel_report'], true)): ?>
            <div class="alert alert-info no-print mb-3">
                <strong><i class="bx bx-link me-1"></i>บันทึกข้อความสำหรับงานที่เกี่ยวข้อง</strong>
                <div class="small mt-1"><?= ($workflowPurpose === 'travel_request') ? 'เมื่อบันทึกข้อความแล้ว ระบบจะพาไปกรอกคำขออนุมัติไปราชการต่อ และเชื่อมเอกสารเข้าด้วยกันโดยอัตโนมัติ' : 'เมื่อบันทึกข้อความแล้ว ระบบจะพาไปกรอกรายงานผลไปราชการต่อ และเชื่อมเอกสารเข้ากับคำขอต้นเรื่อง' ?></div>
            </div>
        <?php endif; ?>

        <!-- แผ่นกระดาษ A4 - ตามแม่แบบ 100% -->
        <div class="page" id="printableMemoPaper">
            <div class="header">
                <!-- ตราครุฑย่อ สำหรับบันทึกข้อความ สูง 1.5cm -->
                <img src="<?= base_url('assets/img/krut_thai.png') ?>" alt="ตราครุฑ" class="garuda">
                <div class="document-title">บันทึกข้อความ</div>
            </div>

            <div class="form-row">
                <span class="label">ส่วนราชการ</span>
                <!-- <?= esc($defaultDept) ?>  -->
                <span class="editable-field" contenteditable="true" id="field_from_org">โรงเรียนสวนกุหลาบวิทยาลัย
                    (จิรประวัติ) นครสวรรค์ โทร. 056-200-765</span>
            </div>

            <div class="col-2">
                <div class="col-left">
                    <span class="label">ที่</span>
                    <span class="editable-field" contenteditable="true"
                        id="field_doc_no">นว 51008.2/</span>
                </div>
                <div class="col-right">
                    <span class="label">วันที่</span>
                    <span class="editable-field" contenteditable="true"
                        id="field_doc_date_text"><?= thai_date(date('Y-m-d'), 'medium') ?></span>
                </div>
            </div>

            <div class="form-row subject-row">
                <span class="label">เรื่อง</span>
                <span class="editable-field" contenteditable="true" id="field_title"
                    data-placeholder="ระบุชื่อเรื่องบันทึกข้อความ"><?= esc($defaultTitle ?? '') ?></span>
            </div>

            <div class="salutation space-before-6pt">
                <span class="label">เรียน</span>
                <span class="editable-field" contenteditable="true" id="field_to_org"
                    style="border-bottom: none;">ผู้อำนวยการโรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์</span>
            </div>

            <!-- ข้อความตัวอย่างแบบลายน้ำ (Watermark Sample) -->
            <div class="no-print d-flex justify-content-between align-items-center mt-2 mb-1" id="watermarkNotice"
                style="font-size: 11px; color: #94a3b8; font-family: 'K2D', sans-serif;">
                <span><i class="bx bx-info-circle me-1"></i> ข้อความตัวอย่าง (ลายน้ำ) —
                    คลิกแก้ไขหรือพิมพ์ทับได้ทันที</span>
                <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2"
                    style="font-size: 11px; border-radius: 4px;" onclick="clearMemoWatermark()">
                    <i class="bx bx-eraser me-1"></i> ล้างตัวอย่าง
                </button>
            </div>

            <div class="body-text space-before-6pt <?= !empty($defaultContent) ? '' : 'is-watermark' ?>" id="memoBody" contenteditable="true"
                data-placeholder="ด้วย (พิมพ์หรือแก้ไขข้อความเนื้อหาบันทึกข้อความที่นี่...)&#10;&#10;จึงเรียนมาเพื่อโปรดพิจารณา">
                <?php if (!empty($defaultContent)): ?>
                    <?= $defaultContent ?>
                <?php else: ?>
                    <p>ด้วย ข้าพเจ้ามีความประสงค์จะขออนุมัติ [ระบุเรื่องหรือโครงการ...] เพื่อ [ระบุวัตถุประสงค์สั้น ๆ] ...</p>
                    <p>จึงเรียนมาเพื่อโปรดพิจารณาอนุมัติ</p>
                <?php endif; ?>
            </div>

            <div class="signature-section">
                <div class="signature-box">
                    <div class="signature-line" id="signatureLine"
                        style="cursor: pointer; min-height: 40px; display: flex; align-items: center; justify-content: center;"
                        onclick="openSignatureModal()" title="คลิกเพื่อเลือกหรือวาดลายเซ็นออนไลน์">
                        <img id="signaturePreviewImg" class="signature-img" src=""
                            style="display: none; max-height: 55px;" alt="ลายเซ็นออนไลน์">
                        <span id="signatureHintText" class="no-print"
                            style="font-size: 11px; color: #94a3b8; border: 1px dashed #cbd5e1; border-radius: 4px; padding: 2px 8px; font-family: 'K2D', sans-serif;">
                            <i class="bx bx-pen me-1"></i> คลิกเพื่อเพิ่มลายเซ็นออนไลน์ (หรือเว้นว่างไว้ลงนามสด)
                        </span>
                    </div>
                    <div class="sign-name" contenteditable="true">( <?= esc(($user['prefix'] ?? '') . ($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?> )</div>
                    <div class="sign-position" contenteditable="true">ตำแหน่ง <?= esc($user['position_name'] ?? 'ครูผู้สอน') ?></div>
                </div>
            </div>
        </div>

        <!-- ลิ้นชักตั้งค่าการเดินเรื่อง & แนบไฟล์ (Side Drawer) -->
        <div id="drawerBackdrop" class="drawer-backdrop" onclick="toggleDrawer()"></div>
        <div id="settingsDrawer" class="settings-drawer no-print">
            <div class="drawer-header">
                <div class="drawer-title">
                    <i class="bx bx-slider-alt me-1"></i> การตั้งค่าบันทึกข้อความ
                </div>
                <button type="button" class="drawer-close" onclick="toggleDrawer()">&times;</button>
            </div>

            <div class="drawer-body">
                <div class="form-group">
                    <label>เลขทะเบียนรับ/ส่งภายใน รร.</label>
                    <input type="text" class="form-control" value="รอเจ้าหน้าที่สารบรรณออกเลข" readonly
                        style="background:#f1f5f9; font-weight:bold;">
                </div>

                <div class="form-group">
                    <label>ความเร่งด่วน</label>
                    <select class="form-select" name="urgency">
                        <option value="normal" selected>ปกติ</option>
                        <option value="urgent">ด่วน</option>
                        <option value="very_urgent">ด่วนมาก</option>
                        <option value="most_urgent">ด่วนที่สุด</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>ชั้นความลับ</label>
                    <select class="form-select" name="secret_level">
                        <option value="normal" selected>ปกติ (ไม่เป็นความลับ)</option>
                        <option value="confidential">ลับ</option>
                        <option value="secret">ลับมาก</option>
                        <option value="top_secret">ลับที่สุด</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>แนบไฟล์เอกสารเพิ่มเติม (PDF/รูปภาพ)</label>
                    <input type="file" name="doc_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                </div>

                <div class="form-group" style="margin-top:20px;">


                    <div id="routeStepsContainer">
                        <?php $initialRouteIds = array_values($suggestedRoutePersIds ?? []); if (empty($initialRouteIds)) $initialRouteIds = ['']; ?>
                        <?php foreach ($initialRouteIds as $routeIndex => $selectedRouteUserId): ?>
                            <div class="step-card route-step">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="step-badge">ขั้นที่ <?= (int)$routeIndex + 1 ?></span>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRouteStep(this)" <?= count($initialRouteIds) === 1 ? 'disabled' : '' ?>>
                                        <i class="bx bx-trash"></i> ลบขั้น
                                    </button>
                                </div>
                                <select class="form-select route-user-select" name="route_users[]">
                                    <option value="">-- เลือกผู้เกษียนลำดับที่ <?= (int)$routeIndex + 1 ?> --</option>
                                    <?php foreach ($allPersonnel as $person): ?>
                                        <option value="<?= esc($person['pers_id']) ?>" <?= (string)$selectedRouteUserId === (string)$person['pers_id'] ? 'selected' : '' ?>>
                                            <?= esc($person['full_name'] ?: $person['pers_username'] ?: $person['pers_id']) ?>
                                            (<?= esc($person['position_name'] ?? 'ไม่ระบุตำแหน่ง') ?><?= !empty($person['group_name']) ? ' · ' . esc($person['group_name']) : '' ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addRouteStep()">
                        <i class="bx bx-plus"></i> เพิ่มผู้เกษียนอีกลำดับ
                    </button>
                    <div class="small text-muted mt-2">
                        * ก่อนส่ง ระบบจะตรวจว่าทุกลำดับเลือกผู้รับครบและไม่มีชื่อซ้ำ
                    </div>
                </div>
            </div>

            <div style="padding: 12px 18px; border-top: 1px solid #e2e8f0; background: #f8fafc;">
                <button type="button" class="bar-btn bar-btn-submit" onclick="toggleDrawer()"
                    style="width:100%; justify-content:center; border-radius:6px; padding:8px;">
                    <i class="bx bx-check"></i> ตกลง / บันทึกการตั้งค่า
                </button>
            </div>
        </div>

    </form>

    <script>
        function toggleDrawer() {
            const drawer = document.getElementById('settingsDrawer');
            const backdrop = document.getElementById('drawerBackdrop');
            drawer.classList.toggle('open');
            backdrop.classList.toggle('show');
        }

        function renumberRouteSteps() {
            const steps = Array.from(document.querySelectorAll('#routeStepsContainer .route-step'));
            steps.forEach((step, index) => {
                const badge = step.querySelector('.step-badge');
                const select = step.querySelector('.route-user-select');
                const removeButton = step.querySelector('button');
                if (badge) badge.textContent = 'ขั้นที่ ' + (index + 1);
                if (select) {
                    select.options[0].textContent = '-- เลือกผู้เกษียนลำดับที่ ' + (index + 1) + ' --';
                }
                if (removeButton) removeButton.disabled = steps.length === 1;
            });
        }

        function addRouteStep() {
            const container = document.getElementById('routeStepsContainer');
            const firstStep = container ? container.querySelector('.route-step') : null;
            if (!container || !firstStep) return;

            const newStep = firstStep.cloneNode(true);
            const select = newStep.querySelector('.route-user-select');
            if (select) select.selectedIndex = 0;
            container.appendChild(newStep);
            renumberRouteSteps();
        }

        function removeRouteStep(button) {
            const container = document.getElementById('routeStepsContainer');
            if (!container || container.querySelectorAll('.route-step').length <= 1) return;
            const step = button.closest('.route-step');
            if (step) step.remove();
            renumberRouteSteps();
        }

        function showMemoToast(message, type = 'success') {
            let toast = document.getElementById('memoToastNotification');
            if (!toast) {
                toast = document.createElement('div');
                toast.id = 'memoToastNotification';
                toast.style.cssText = 'position: fixed; bottom: 30px; left: 50%; transform: translateX(-50%) translateY(20px); background: #0f172a; color: #ffffff; padding: 10px 22px; border-radius: 30px; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 8px; z-index: 999999; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.4); transition: all 0.28s cubic-bezier(0.16, 1, 0.3, 1); opacity: 0; pointer-events: none; border: 1px solid rgba(255,255,255,0.18); font-family: "K2D", sans-serif;';
                document.body.appendChild(toast);
            }
            const icon = type === 'success' ? '<i class="bx bx-check-circle" style="color: #4ade80; font-size: 18px;"></i>' : '<i class="bx bx-info-circle" style="color: #60a5fa; font-size: 18px;"></i>';
            toast.innerHTML = `${icon} <span>${message}</span>`;
            toast.style.opacity = '1';
            toast.style.transform = 'translateX(-50%) translateY(0)';

            clearTimeout(toast._timer);
            toast._timer = setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(-50%) translateY(20px)';
            }, 2600);
        }

        function formatThaiTypography(showToast = true) {
            const body = document.getElementById('memoBody');
            if (!body) return;

            const walker = document.createTreeWalker(body, NodeFilter.SHOW_TEXT, null, false);
            const textNodes = [];
            while (walker.nextNode()) {
                textNodes.push(walker.currentNode);
            }

            const thaiMonths = 'มกราคม|กุมภาพันธ์|มีนาคม|เมษายน|พฤษภาคม|มิถุนายน|กรกฎาคม|สิงหาคม|กันยายน|ตุลาคม|พฤศจิกายน|ธันวาคม';
            const thaiUnits = 'คน|บาท|ฉบับ|ราย|ชุด|เล่ม|ข้อ|แห่ง|วัน|เดือน|ปี|ครั้ง|โครงการ|รายการ|ห้อง|ชั่วโมง|นาที|เปอร์เซ็นต์|ร้อยละ';
            const thaiPrefixes = 'นาย|นาง|นางสาว|ว่าที่ร้อยตรี|ว่าที่ ร\\.ต\\.|ดร\\.|ครู|อาจารย์|ผอ\\.|รอง ผอ\\.';

            const dateRegex = new RegExp(`([0-9๑-๙๐]+)\\s+(${thaiMonths})\\s+([0-9๑-๙๐]+)`, 'g');
            const unitRegex = new RegExp(`([0-9๑-๙๐,]+)\\s+(${thaiUnits})`, 'g');
            const prefixRegex = new RegExp(`(${thaiPrefixes})\\s+([ก-๙]+)`, 'g');

            let segmenter = null;
            if (window.Intl && Intl.Segmenter) {
                segmenter = new Intl.Segmenter('th', { granularity: 'word' });
            }

            let modified = 0;
            textNodes.forEach(node => {
                let val = node.nodeValue;
                if (!val || !val.trim()) return;

                // ล้าง Zero-Width Space เดิมเพื่อป้องกันการแทรกซ้ำ
                val = val.replace(/\u200B/g, '');

                // ป้องกันคำเฉพาะหลุดบรรทัดด้วย Non-Breaking Space (\u00A0)
                val = val.replace(dateRegex, '$1\u00A0$2\u00A0$3');
                val = val.replace(unitRegex, '$1\u00A0$2');
                val = val.replace(prefixRegex, '$1\u00A0$2');

                // แทรก Zero-Width Space (\u200B) ที่รอยต่อคำไทยตามพจนานุกรม
                if (segmenter) {
                    const words = [];
                    for (const seg of segmenter.segment(val)) {
                        words.push(seg.segment);
                    }
                    val = words.join('\u200B');
                }

                if (node.nodeValue !== val) {
                    node.nodeValue = val;
                    modified++;
                }
            });

            if (showToast) {
                showMemoToast('จัดระเบียบวรรคตอนและตัดคำภาษาไทยเรียบร้อยแล้ว');
            }
            debounceUpdatePageBreaks();
        }

        function convertNumbersInBody() {
            const thaiDigits = ['๐', '๑', '๒', '๓', '๔', '๕', '๖', '๗', '๘', '๙'];
            const arabicDigits = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
            const body = document.getElementById('memoBody');
            let html = body.innerHTML;

            for (let i = 0; i < 10; i++) {
                const re = new RegExp(arabicDigits[i], 'g');
                html = html.replace(re, thaiDigits[i]);
            }
            body.innerHTML = html;
            showMemoToast('แปลงตัวเลขเป็นเลขไทยเรียบร้อยแล้ว');
            debounceUpdatePageBreaks();
        }

        // ระบบจัดการลายน้ำข้อความตัวอย่าง (Sample Watermark Control)
        let isMemoWatermarkActive = true;

        function clearMemoWatermark() {
            const memoBody = document.getElementById('memoBody');
            const notice = document.getElementById('watermarkNotice');
            if (memoBody) {
                memoBody.innerHTML = '';
                memoBody.classList.remove('is-watermark');
                memoBody.focus();
            }
            if (notice) {
                notice.style.display = 'none';
            }
            isMemoWatermarkActive = false;
            debounceUpdatePageBreaks();
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

        let pageBreakDebounceTimer = null;
        function debounceUpdatePageBreaks() {
            clearTimeout(pageBreakDebounceTimer);
            pageBreakDebounceTimer = setTimeout(updatePageBreakIndicators, 120);
        }

        function updatePageBreakIndicators() {
            const page = document.getElementById('printableMemoPaper');
            if (!page) return;

            // ลบตัวบอกตำแหน่งแบ่งหน้าเดิมออกก่อนคำนวณใหม่
            page.querySelectorAll('.page-break-indicator, .page-header-number').forEach(el => el.remove());

            const a4Height = getA4PageHeightPx();
            // วัดความสูงจริงของเนื้อหากระดาษ
            const currentHeight = Math.max(page.scrollHeight, page.offsetHeight);
            const threshold = 15; // เผื่อความคลาดเคลื่อน 15px
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

            // สร้างเส้นแบ่งหน้าสำหรับหน้าที่ 2 เป็นต้นไป
            for (let p = 2; p <= totalPages; p++) {
                const topPx = (p - 1) * a4Height;
                const pageThai = p < 10 ? thaiDigits[p] : p;
                const prevPageThai = (p - 1) < 10 ? thaiDigits[p - 1] : (p - 1);

                // 1. เส้นประแบ่งหน้า A4
                const indicator = document.createElement('div');
                indicator.className = 'page-break-indicator no-print';
                indicator.style.top = topPx + 'px';
                indicator.id = 'pageBreak_' + p;
                indicator.innerHTML = `<span class="page-break-badge"><i class="bx bx-cut"></i> สิ้นสุดแผ่นที่ ${prevPageThai} / 📄 ขึ้นแผ่นที่ ${pageThai} (- ${pageThai} -)</span>`;
                page.appendChild(indicator);

                // 2. เลขหน้าหัวกระดาษแผ่นที่ ๒ (- ๒ -) ตามระเบียบงานสารบรรณ
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
            } else {
                showMemoToast('เอกสารยังอยู่ภายใน ๑ หน้า A4');
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const memoBody = document.getElementById('memoBody');
            const notice = document.getElementById('watermarkNotice');
            if (memoBody) {
                memoBody.addEventListener('input', function () {
                    if (isMemoWatermarkActive) {
                        this.classList.remove('is-watermark');
                        isMemoWatermarkActive = false;
                        if (notice) notice.style.display = 'none';
                    }
                    debounceUpdatePageBreaks();
                });
            }

            document.querySelectorAll('.editable-field').forEach(el => {
                el.addEventListener('input', debounceUpdatePageBreaks);
            });

            window.addEventListener('resize', debounceUpdatePageBreaks);

            // ตรวจสอบความยาวกระดาษเริ่มต้น
            setTimeout(updatePageBreakIndicators, 150);
        });

        function submitMemoForm(action) {
            formatThaiTypography(false);
            document.getElementById('submitAction').value = action;

            const fromOrg = document.getElementById('field_from_org') ? document.getElementById('field_from_org').innerText.trim() : '';
            const docNo = document.getElementById('field_doc_no') ? document.getElementById('field_doc_no').innerText.trim() : '';
            const title = document.getElementById('field_title') ? document.getElementById('field_title').innerText.trim() : '';
            const toOrg = document.getElementById('field_to_org') ? document.getElementById('field_to_org').innerText.trim() : '';

            document.getElementById('hidden_from_org').value = fromOrg;
            document.getElementById('hidden_doc_no').value = docNo;
            document.getElementById('hidden_title').value = title;
            document.getElementById('hidden_to_org').value = toOrg;

            const memoBody = document.getElementById('memoBody');
            // ถ้ายังมีคลาสลายน้ำตัวอย่างค้างอยู่ ให้ยืนยันก่อนบันทึก
            if (memoBody && (memoBody.classList.contains('is-watermark') || isMemoWatermarkActive)) {
                if (!confirm('เนื้อหาข้อความยังคงเป็น "ข้อความตัวอย่าง (ลายน้ำ)" อยู่ คุณต้องการส่งข้อความนี้ใช่หรือไม่?')) {
                    memoBody.focus();
                    return;
                }
                memoBody.classList.remove('is-watermark');
            }

            const bodyContent = memoBody ? memoBody.innerHTML : '';
            document.getElementById('hiddenContent').value = bodyContent;

            if (!title) {
                AppAlert.warning('กรุณาระบุชื่อเรื่องบันทึกข้อความ');
                document.getElementById('field_title').focus();
                return;
            }

            const form = document.getElementById('memoForm');
            if (action === 'route') {
                const routeSelects = Array.from(document.querySelectorAll('#routeStepsContainer .route-user-select'));
                const routeUsers = routeSelects.map(select => select.value).filter(Boolean);

                if (routeSelects.length === 0 || routeUsers.length !== routeSelects.length) {
                    AppAlert.warning('กรุณาตั้งค่าเส้นทางเกษียนให้ครบทุกลำดับก่อนส่ง');
                    if (routeSelects.some(select => !select.value)) {
                        toggleDrawer();
                        const firstEmpty = routeSelects.find(select => !select.value);
                        if (firstEmpty) firstEmpty.focus();
                    }
                    return;
                }

                if (new Set(routeUsers).size !== routeUsers.length) {
                    AppAlert.warning('พบผู้เกษียนชื่อซ้ำ กรุณาเลือกผู้พิจารณาแต่ละลำดับให้แตกต่างกัน');
                    toggleDrawer();
                    return;
                }

                const lastStep = routeSelects[routeSelects.length - 1];
                if (!confirm('ยืนยันส่งบันทึกข้อความตามเส้นทางเกษียน ' + routeSelects.length + ' ลำดับ โดยผู้รับลำดับสุดท้ายคือ ' + lastStep.options[lastStep.selectedIndex].text.trim() + ' ใช่หรือไม่?')) {
                    toggleDrawer();
                    return;
                }
            }
            form.submit();
        }

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
        // ระบบลายเซ็นออนไลน์ (e-Signature System)
        // ==========================================
        let sigCanvas = null;
        let sigCtx = null;
        let isSigDrawing = false;
        let hasSigDrawn = false;
        let currentSigColor = '#0038a8'; // สีน้ำเงินเซ็นจริงเริ่มต้น

        function setSigColor(color, btn) {
            currentSigColor = color;
            if (sigCtx) {
                sigCtx.strokeStyle = color;
            }
            document.querySelectorAll('.sig-color-btn').forEach(b => {
                b.style.boxShadow = '0 0 0 1px #cbd5e1';
            });
            if (btn) {
                btn.style.boxShadow = `0 0 0 2px ${color}`;
            }
        }

        function initSigCanvas() {
            sigCanvas = document.getElementById('padCanvas');
            if (!sigCanvas) return;
            sigCtx = sigCanvas.getContext('2d');
            sigCtx.strokeStyle = currentSigColor; // สีน้ำเงินเซ็นจริง
            sigCtx.lineWidth = 2.4;
            sigCtx.lineCap = 'round';
            sigCtx.lineJoin = 'round';

            const getPos = (e) => {
                const rect = sigCanvas.getBoundingClientRect();
                const scaleX = sigCanvas.width / rect.width;
                const scaleY = sigCanvas.height / rect.height;
                const clientX = e.touches ? e.touches[0].clientX : e.clientX;
                const clientY = e.touches ? e.touches[0].clientY : e.clientY;
                return {
                    x: (clientX - rect.left) * scaleX,
                    y: (clientY - rect.top) * scaleY
                };
            };

            const startDraw = (e) => {
                isSigDrawing = true;
                hasSigDrawn = true;
                const p = getPos(e);
                sigCtx.beginPath();
                sigCtx.moveTo(p.x, p.y);
            };

            const draw = (e) => {
                if (!isSigDrawing) return;
                e.preventDefault();
                const p = getPos(e);
                sigCtx.lineTo(p.x, p.y);
                sigCtx.stroke();
            };

            const stopDraw = () => { isSigDrawing = false; };

            sigCanvas.addEventListener('mousedown', startDraw);
            sigCanvas.addEventListener('mousemove', draw);
            window.addEventListener('mouseup', stopDraw);

            sigCanvas.addEventListener('touchstart', startDraw, { passive: false });
            sigCanvas.addEventListener('touchmove', draw, { passive: false });
            window.addEventListener('touchend', stopDraw);
        }

        function clearPadCanvas() {
            if (!sigCanvas || !sigCtx) return;
            sigCtx.clearRect(0, 0, sigCanvas.width, sigCanvas.height);
            hasSigDrawn = false;
        }

        function openSignatureModal() {
            const modal = document.getElementById('sigModal');
            const backdrop = document.getElementById('sigModalBackdrop');
            if (modal && backdrop) {
                modal.classList.add('open');
                backdrop.classList.add('show');
                if (!sigCanvas) {
                    setTimeout(initSigCanvas, 80);
                }
            }
        }

        function closeSignatureModal() {
            const modal = document.getElementById('sigModal');
            const backdrop = document.getElementById('sigModalBackdrop');
            if (modal && backdrop) {
                modal.classList.remove('open');
                backdrop.classList.remove('show');
            }
        }

        function applyPresetSignature(imgPath) {
            document.getElementById('hidden_signature_data').value = imgPath;
            document.getElementById('hidden_remove_signature').value = '0';

            const preview = document.getElementById('signaturePreviewImg');
            const hint = document.getElementById('signatureHintText');
            const badge = document.getElementById('sigStatusBadge');

            preview.src = '<?= base_url() ?>' + imgPath;
            preview.style.display = 'block';
            if (hint) hint.style.display = 'none';
            if (badge) {
                badge.innerText = 'มีลายเซ็น';
                badge.style.background = '#22c55e';
            }
            closeSignatureModal();
        }

        function applyDrawnSignature() {
            if (!hasSigDrawn) {
                AppAlert.warning('กรุณาวาดลายเซ็นบนกระดานก่อนกดใช้');
                return;
            }
            const dataUrl = sigCanvas.toDataURL('image/png');
            document.getElementById('hidden_signature_data').value = dataUrl;
            document.getElementById('hidden_remove_signature').value = '0';

            const preview = document.getElementById('signaturePreviewImg');
            const hint = document.getElementById('signatureHintText');
            const badge = document.getElementById('sigStatusBadge');

            preview.src = dataUrl;
            preview.style.display = 'block';
            if (hint) hint.style.display = 'none';
            if (badge) {
                badge.innerText = 'มีลายเซ็น';
                badge.style.background = '#22c55e';
            }
            closeSignatureModal();
        }

        function removeSignature() {
            document.getElementById('hidden_signature_data').value = '';
            document.getElementById('hidden_remove_signature').value = '1';

            const preview = document.getElementById('signaturePreviewImg');
            const hint = document.getElementById('signatureHintText');
            const badge = document.getElementById('sigStatusBadge');

            preview.src = '';
            preview.style.display = 'none';
            if (hint) hint.style.display = 'inline-block';
            if (badge) {
                badge.innerText = 'เว้นว่าง';
                badge.style.background = 'rgba(255,255,255,0.18)';
            }
            clearPadCanvas();
            closeSignatureModal();
        }
    </script>

    <!-- Modal จัดการลายเซ็นออนไลน์ (e-Signature Modal) -->
    <div id="sigModalBackdrop" class="drawer-backdrop no-print" onclick="closeSignatureModal()"></div>
    <div id="sigModal" class="sig-modal no-print">
        <div class="sig-modal-header">
            <div style="font-weight: 700; font-size: 15px; color: #fff; display: flex; align-items: center; gap: 6px;">
                <i class="bx bx-pen" style="color: #fbbf24;"></i> จัดการลายเซ็นออนไลน์ (e-Signature)
            </div>
            <button type="button" class="drawer-close" onclick="closeSignatureModal()">&times;</button>
        </div>
        <div class="sig-modal-body">
            <?php if (!empty($user['signature_img'])): ?>
                <!-- ตัวเลือกที่ 1: ลายเซ็นในโปรไฟล์ -->
                <div class="sig-option-card" style="margin-bottom: 14px;">
                    <div style="font-weight: 700; font-size: 13px; color: #1e293b; margin-bottom: 8px;">
                        <i class="bx bx-check-circle text-success me-1"></i> 1. ลายเซ็นที่บันทึกไว้ในโปรไฟล์ของคุณ
                    </div>
                    <div
                        style="text-align: center; padding: 10px; background: #fff; border: 1px solid #cbd5e1; border-radius: 6px; margin-bottom: 8px;">
                        <img src="<?= base_url($user['signature_img']) ?>" alt="ลายเซ็นโปรไฟล์" style="max-height: 55px;">
                    </div>
                    <button type="button" class="sig-btn sig-btn-primary" style="width: 100%;"
                        onclick="applyPresetSignature('<?= esc($user['signature_img']) ?>')">
                        <i class="bx bx-check"></i> ใช้ลายเซ็นจากโปรไฟล์นี้
                    </button>
                </div>
            <?php endif; ?>

            <!-- ตัวเลือกที่ 2: วาดลายเซ็นสดใหม่ -->
            <div class="sig-option-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <div style="font-weight: 700; font-size: 13px; color: #1e293b;">
                        <i class="bx bx-edit text-primary me-1"></i>
                        <?= !empty($user['signature_img']) ? '2. หรือวาดลายเซ็นสดใหม่ (Draw)' : 'วาดลายเซ็นสดใหม่ (Draw)' ?>
                    </div>
                    <!-- ปุ่มเลือกสีปากกา -->
                    <div style="display: flex; gap: 6px; align-items: center;">
                        <span style="font-size: 11px; color: #64748b;">สีหมึก:</span>
                        <button type="button" class="sig-color-btn active" onclick="setSigColor('#0038a8', this)"
                            style="background: #0038a8; width: 22px; height: 22px; border-radius: 50%; border: 2px solid #fff; box-shadow: 0 0 0 2px #0038a8; cursor: pointer;"
                            title="สีน้ำเงินเซ็นจริง (ราชการ)"></button>
                        <button type="button" class="sig-color-btn" onclick="setSigColor('#1d4ed8', this)"
                            style="background: #1d4ed8; width: 22px; height: 22px; border-radius: 50%; border: 2px solid #fff; box-shadow: 0 0 0 1px #cbd5e1; cursor: pointer;"
                            title="สีน้ำเงินสด"></button>
                        <button type="button" class="sig-color-btn" onclick="setSigColor('#111827', this)"
                            style="background: #111827; width: 22px; height: 22px; border-radius: 50%; border: 2px solid #fff; box-shadow: 0 0 0 1px #cbd5e1; cursor: pointer;"
                            title="สีดำ"></button>
                    </div>
                </div>
                <div style="border: 1px solid #cbd5e1; border-radius: 6px; background: #ffffff; margin-bottom: 8px;">
                    <canvas id="padCanvas" width="380" height="120"
                        style="width: 100%; height: 120px; cursor: crosshair; touch-action: none; display: block; border-radius: 6px;"></canvas>
                </div>
                <div style="display: flex; gap: 8px;">
                    <button type="button" class="sig-btn sig-btn-secondary" style="flex: 1;" onclick="clearPadCanvas()">
                        <i class="bx bx-eraser"></i> ล้าง
                    </button>
                    <button type="button" class="sig-btn sig-btn-success" style="flex: 2;"
                        onclick="applyDrawnSignature()">
                        <i class="bx bx-check"></i> ใช้ลายเซ็นที่วาด
                    </button>
                </div>
            </div>

            <!-- ตัวเลือกที่ 3: ลบลายเซ็นออก -->
            <div style="margin-top: 14px; padding-top: 12px; border-top: 1px solid #e2e8f0;">
                <button type="button" class="sig-btn sig-btn-danger" style="width: 100%;" onclick="removeSignature()">
                    <i class="bx bx-trash"></i> นำลายเซ็นออก (เว้นว่างไว้ลงนามสดด้วยปากกา)
                </button>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?= base_url('assets/js/app-swal.js?v=' . (file_exists(FCPATH . 'assets/js/app-swal.js') ? filemtime(FCPATH . 'assets/js/app-swal.js') : time())) ?>"></script>
</body>

</html>