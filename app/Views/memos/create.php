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

    <!-- Icons & Core CSS -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/vendor/css/core.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/demo.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/orders_saraban.css?v=' . time()) ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/saraban-memo.css?v=' . (file_exists(FCPATH . 'assets/css/saraban-memo.css') ? filemtime(FCPATH . 'assets/css/saraban-memo.css') : time())) ?>">
</head>

<body>

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

        <!-- Unified Top Sticky Bar (ฟิกเมนูทั้ง Action Bar และ Ribbon ไว้ด้านบน ไม่ให้เลื่อนตาม) -->
        <div class="document-top-sticky no-print">
            <!-- Top Action Bar (แถบการกระทำหลัก ตรึงติดบนสุด) -->
            <div class="order-action-bar d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <a href="<?= base_url('admin/memos') ?>" class="btn btn-outline-secondary btn-sm fw-semibold">
                    <i class="bx bx-arrow-back me-1"></i> กลับตาราง
                </a>
                <span class="badge bg-primary text-white fw-bold px-3 py-1.5 fs-7">
                    <i class="bx bxs-file-doc me-1"></i> บันทึกข้อความราชการ (ปี พ.ศ. <?= esc($currentYear ?? (date('Y') + 543)) ?>)
                </span>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex align-items-center gap-2">
                <!-- ปุ่มปรับย่อ/ขยายมุมมองหน้าจอ -->
                <div class="d-inline-flex align-items-center bg-light border rounded px-1.5 py-1 gap-1" title="ปรับย่อ/ขยายมุมมองหน้าจอ (ไม่มีผลต่อการพิมพ์)">
                    <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-1" onclick="adjustZoom(-0.05)" title="ย่อหน้ากระดาษ"><i class="bx bx-minus"></i></button>
                    <span class="small fw-bold px-1 text-dark" id="zoomLevelText" style="font-size: 11px;">100%</span>
                    <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-1" onclick="adjustZoom(0.05)" title="ขยายหน้ากระดาษ"><i class="bx bx-plus"></i></button>
                    <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-1" onclick="resetZoom()" title="ขนาดปกติ 100%"><i class="bx bx-reset"></i></button>
                </div>

                <!-- ป้ายแสดงสถานะจำนวนหน้า (Page Counter) -->
                <div id="memoPageCounterBadge" class="badge bg-dark text-white fw-bold px-2.5 py-2 shadow-xs" style="cursor: pointer;" onclick="scrollToNextPageBreak()" title="สถานะความยาวเอกสาร A4 (คลิกเพื่อเลื่อนไปยังจุดแบ่งหน้า)">
                    <i class="bx bx-file text-warning me-1"></i> เอกสาร ๑ แผ่น (A4)
                </div>

                <button type="button" class="btn btn-outline-primary btn-sm fw-semibold" onclick="toggleDrawer()" title="ตั้งค่าผู้ตรวจ/เกษียนหนังสือ และแนบไฟล์เอกสารเพิ่มเติม">
                    <i class="bx bx-slider-alt me-1"></i> ตั้งค่าเดินเรื่อง
                </button>
                <button type="button" class="btn btn-outline-dark btn-sm fw-semibold" onclick="window.print()" title="สั่งพิมพ์เอกสารหรือบันทึกเป็น PDF ขนาด A4">
                    <i class="bx bx-printer me-1"></i> พิมพ์ / PDF
                </button>
                <!-- รวมปุ่มบันทึกและส่งสารบรรณเข้าด้วยกันแบบ Split Action Button -->
                <div class="btn-group shadow-xs" role="group">
                    <button type="button" id="btnSubmitMemo" class="btn btn-primary btn-sm fw-bold px-3" onclick="submitMemoForm('route')" title="บันทึกและส่งให้งานสารบรรณตรวจสอบทันที">
                        <i class="bx bx-paper-plane me-1"></i> บันทึกและส่งให้สารบรรณ
                    </button>
                    <button type="button" class="btn btn-primary btn-sm dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false" title="ตัวเลือกเพิ่มเติม">
                        <span class="visually-hidden">ตัวเลือกเพิ่มเติม</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 py-1" style="min-width: 220px; font-size: 13px;">
                        <li>
                            <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="javascript:void(0)" onclick="submitMemoForm('draft')">
                                <i class="bx bx-save text-secondary fs-5"></i>
                                <div>
                                    <div class="fw-bold text-dark">บันทึกเป็นฉบับร่าง</div>
                                    <div class="small text-muted" style="font-size: 11px;">เก็บไว้แก้ไขต่อ ยังไม่ส่งสารบรรณ</div>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Microsoft Word-Style Formatting Ribbon Toolbar (แท็บเครื่องมือพิมพ์และจัดรูปแบบสไตล์ Word) -->
        <div class="order-word-ribbon no-print d-flex align-items-center justify-content-start flex-wrap gap-1">
            <!-- Group 1: ประวัติ (Undo / Redo) -->
            <div class="ribbon-group" title="ประวัติการแก้ไข">
                <button type="button" class="ribbon-btn" onmousedown="event.preventDefault(); execFormat('undo')" title="เลิกทำ (Undo - Ctrl+Z)">
                    <i class="bx bx-undo"></i>
                </button>
                <button type="button" class="ribbon-btn" onmousedown="event.preventDefault(); execFormat('redo')" title="ทำซ้ำ (Redo - Ctrl+Y)">
                    <i class="bx bx-redo"></i>
                </button>
            </div>

            <div class="ribbon-divider"></div>

            <!-- Group 2: ฟอนต์และขนาดตัวอักษร (Font & Size) -->
            <div class="ribbon-group d-flex align-items-center gap-1">
                <span class="ribbon-font-label d-none d-lg-inline" title="ฟอนต์มาตรฐานเอกสารราชการไทย">
                    <i class="bx bx-text me-1 text-primary"></i>สารบรรณ
                </span>
                <select id="fontSizeSelect" class="form-select form-select-sm ribbon-select" onchange="applyFontSize(this.value)" title="เลือกขนาดตัวอักษร (pt)">
                    <option value="14pt">14 pt (เชิงอรรถ)</option>
                    <option value="15pt">15 pt</option>
                    <option value="16pt" selected>16 pt (เนื้อหามาตรฐาน)</option>
                    <option value="18pt">18 pt (หัวข้อย่อย)</option>
                    <option value="20pt">20 pt (หัวข้อส่วนราชการ)</option>
                    <option value="22pt">22 pt (หัวเรื่อง)</option>
                    <option value="24pt">24 pt (หัวเรื่องใหญ่)</option>
                    <option value="29pt">29 pt (หัวเรื่องบันทึกข้อความ)</option>
                </select>
                <button type="button" class="ribbon-btn" onmousedown="event.preventDefault(); stepFontSize(1)" title="เพิ่มขนาดตัวอักษร (A+ / Ctrl + ])">
                    <span class="fw-bold" style="font-size: 13px;">A<sup>+</sup></span>
                </button>
                <button type="button" class="ribbon-btn" onmousedown="event.preventDefault(); stepFontSize(-1)" title="ลดขนาดตัวอักษร (A- / Ctrl + [)">
                    <span class="fw-bold" style="font-size: 11px;">A<sup>-</sup></span>
                </button>
            </div>

            <div class="ribbon-divider"></div>

            <!-- Group 3: ลักษณะตัวอักษร (Font Styles: B, I, U, S, Eraser) -->
            <div class="ribbon-group">
                <button type="button" class="ribbon-btn" id="btnBold" onmousedown="event.preventDefault(); execFormat('bold')" title="ตัวหนา (Bold - Ctrl+B)">
                    <i class="bx bx-bold"></i>
                </button>
                <button type="button" class="ribbon-btn" id="btnItalic" onmousedown="event.preventDefault(); execFormat('italic')" title="ตัวเอียง (Italic - Ctrl+I)">
                    <i class="bx bx-italic"></i>
                </button>
                <button type="button" class="ribbon-btn" id="btnUnderline" onmousedown="event.preventDefault(); execFormat('underline')" title="ขีดเส้นใต้ (Underline - Ctrl+U)">
                    <i class="bx bx-underline"></i>
                </button>
                <button type="button" class="ribbon-btn" id="btnStrike" onmousedown="event.preventDefault(); execFormat('strikethrough')" title="ขีดฆ่า (Strikethrough)">
                    <i class="bx bx-strikethrough"></i>
                </button>
                <button type="button" class="ribbon-btn text-danger" onmousedown="event.preventDefault(); execFormat('removeFormat')" title="ล้างการจัดรูปแบบ (Clear Formatting)">
                    <i class="bx bx-eraser"></i>
                </button>
            </div>

            <div class="ribbon-divider"></div>

            <!-- Group 4: การจัดตำแหน่งข้อความ (Alignments) -->
            <div class="ribbon-group">
                <button type="button" class="ribbon-btn" id="btnAlignLeft" onmousedown="event.preventDefault(); formatAlignment('left')" title="จัดชิดซ้าย (Align Left - Ctrl+L)">
                    <i class="bx bx-align-left"></i>
                </button>
                <button type="button" class="ribbon-btn" id="btnAlignCenter" onmousedown="event.preventDefault(); formatAlignment('center')" title="จัดกึ่งกลาง (Align Center - Ctrl+E)">
                    <i class="bx bx-align-middle"></i>
                </button>
                <button type="button" class="ribbon-btn" id="btnAlignRight" onmousedown="event.preventDefault(); formatAlignment('right')" title="จัดชิดขวา (Align Right - Ctrl+R)">
                    <i class="bx bx-align-right"></i>
                </button>
                <button type="button" class="ribbon-btn" id="btnAlignJustify" onmousedown="event.preventDefault(); formatAlignment('justify')" title="จัดกระจายซ้าย-ขวาเท่ากันแบบราชการ (Thai Distributed - Ctrl+J)">
                    <i class="bx bx-align-justify"></i>
                </button>
            </div>

            <div class="ribbon-divider"></div>

            <!-- Group 5: รายการและระยะเยื้อง/ย่อหน้า (Lists & Indentation) -->
            <div class="ribbon-group">
                <button type="button" class="ribbon-btn" id="btnNumList" onmousedown="event.preventDefault(); execFormat('insertOrderedList')" title="ลำดับเลข (Numbered List 1. 2. 3.)">
                    <i class="bx bx-list-ol"></i>
                </button>
                <button type="button" class="ribbon-btn" id="btnBulletList" onmousedown="event.preventDefault(); execFormat('insertUnorderedList')" title="สัญลักษณ์หัวข้อย่อย (Bullet List)">
                    <i class="bx bx-list-ul"></i>
                </button>
                <button type="button" class="ribbon-btn" onmousedown="event.preventDefault(); changeParagraphIndent(-1)" title="ลดระยะเยื้องย่อหน้า (Decrease Indent)">
                    <i class="bx bx-left-indent"></i>
                </button>
                <button type="button" class="ribbon-btn" onmousedown="event.preventDefault(); changeParagraphIndent(1)" title="เพิ่มระยะเยื้องย่อหน้า (Increase Indent)">
                    <i class="bx bx-right-indent"></i>
                </button>
                <button type="button" class="ribbon-btn" id="btnParagraphIndent" onmousedown="event.preventDefault(); toggleParagraphIndent()" title="จัดย่อหน้าราชการ ๒.๕ ซม. (Paragraph Indent)">
                    <i class="bx bx-paragraph"></i>
                </button>
            </div>

            <div class="ribbon-divider"></div>

            <!-- Group 6: เครื่องมือสารบรรณ (Divider, แปลงเลขไทย, จัดคำไทยสวย, ลายเซ็นออนไลน์) -->
            <div class="ribbon-group">
                <button type="button" class="ribbon-btn text-secondary" onmousedown="event.preventDefault(); insertHorizontalRule()" title="แทรกเส้นคั่นแบ่งส่วน">
                    <i class="bx bx-minus"></i>
                </button>
                <button type="button" class="ribbon-btn text-dark fw-bold" onmousedown="event.preventDefault(); convertNumbersInBody()" title="แปลงตัวเลขในเนื้อหาเป็นเลขไทย">
                    <span style="font-size: 13px;">๑๒๓</span>
                </button>
                <button type="button" class="ribbon-btn text-info fw-semibold" onmousedown="event.preventDefault(); formatThaiTypography()" title="ตัดคำภาษาไทยให้อ่านง่ายและกระจายขอบสวยงาม">
                    <i class="bx bx-font me-1"></i><span class="small d-none d-xl-inline">จัดคำไทยสวย</span>
                </button>
                <button type="button" class="ribbon-btn text-warning fw-semibold" onmousedown="event.preventDefault(); openSignatureModal()" title="เพิ่ม/จัดการลายเซ็นดิจิทัลออนไลน์">
                    <i class="bx bx-pen me-1"></i><span class="small d-none d-xl-inline">ลายเซ็นออนไลน์</span>
                    <span id="sigStatusBadge" class="badge bg-secondary ms-1" style="font-size: 9px;">เว้นว่าง</span>
                </button>
            </div>

            <!-- Group 7: ข้อมูลเมตาของบันทึกข้อความ (ความเร่งด่วน & ชั้นความลับ ชิดขวาของแท็บ Ribbon) -->
            <div class="ms-auto d-flex align-items-center gap-2">
                <!-- ความเร่งด่วน -->
                <div class="ribbon-meta-item shadow-xs" title="ระดับความเร่งด่วนของหนังสือ">
                    <i class="bx bx-alarm text-warning me-1 fs-6"></i>
                    <span class="small fw-bold text-dark me-1 d-none d-lg-inline" style="font-size: 11px;">ความเร่งด่วน:</span>
                    <select name="urgency" class="ribbon-meta-select">
                        <option value="normal" selected>ปกติ</option>
                        <option value="urgent">ด่วน</option>
                        <option value="very_urgent">ด่วนมาก</option>
                        <option value="most_urgent">ด่วนที่สุด</option>
                    </select>
                </div>

                <!-- ชั้นความลับ -->
                <div class="ribbon-meta-item shadow-xs" title="ชั้นความลับของหนังสือ">
                    <i class="bx bx-shield-quarter text-secondary me-1 fs-6"></i>
                    <span class="small fw-bold text-dark me-1 d-none d-lg-inline" style="font-size: 11px;">ความลับ:</span>
                    <select name="secret_level" class="ribbon-meta-select">
                        <option value="normal" selected>ปกติ (ไม่ลับ)</option>
                        <option value="confidential">ลับ</option>
                        <option value="secret">ลับมาก</option>
                        <option value="top_secret">ลับที่สุด</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

        <?php if (in_array(($workflowPurpose ?? ''), ['travel_request', 'travel_report'], true)): ?>
            <div class="container my-2 no-print" style="max-width: 21cm;">
                <div class="alert alert-info border-0 shadow-sm mb-0 py-2 px-3 d-flex align-items-center">
                    <i class="bx bx-link fs-4 me-2"></i>
                    <div class="small">
                        <strong>บันทึกข้อความสำหรับงานที่เกี่ยวข้อง:</strong>
                        <?= ($workflowPurpose === 'travel_request') ? 'เมื่อบันทึกข้อความแล้ว ระบบจะพาไปกรอกคำขออนุมัติไปราชการต่อ และเชื่อมเอกสารเข้าด้วยกันโดยอัตโนมัติ' : 'เมื่อบันทึกข้อความแล้ว ระบบจะพาไปกรอกรายงานผลไปราชการต่อ และเชื่อมเอกสารเข้ากับคำขอต้นเรื่อง' ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- แผ่นกระดาษ A4 - ตามแม่แบบ 100% -->
        <div class="page-wrapper">
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
                            <i class="bx bx-pen me-1"></i> คลิกเพิ่มลายเซ็นออนไลน์
                        </span>
                    </div>
                    <div class="sign-name" contenteditable="true">( <?= esc(($user['prefix'] ?? '') . ($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?> )</div>
                    <div class="sign-position" contenteditable="true">ตำแหน่ง <?= esc($user['position_name'] ?? 'ครูผู้สอน') ?></div>
                </div>
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
                    <label>แนบไฟล์เอกสารเพิ่มเติม (PDF/รูปภาพ)</label>
                    <input type="file" name="doc_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                </div>

                <div class="form-group" style="margin-top:20px;">
                    <div class="alert alert-primary border-0 p-2 mb-3 d-flex align-items-center gap-2">
                        <i class="bx bxs-info-circle fs-4 text-primary"></i>
                        <div class="small">
                            <strong>เส้นทางเกษียนเสนอเซ็น:</strong> ระบบกำหนดเริ่มต้นแสดง <strong>ผู้อำนวยการโรงเรียน (ผอ.)</strong> เพียงคนเดียวก่อนเสมอ หากต้องการส่งผ่านหัวหน้ากลุ่มสาระหรือรองผู้อำนวยการ สามารถกดปุ่ม <code>+ เพิ่มผู้เกษียนอีกลำดับ</code> ด้านล่างได้ตามต้องการ
                        </div>
                    </div>

                    <label class="form-label fw-bold text-dark d-flex justify-content-between align-items-center">
                        <span>เสนอแนะเส้นทางเกษียน (ไม่บังคับ)</span>
                    </label>

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
                                    <option value="">-- ไม่ระบุ (ให้สารบรรณกำหนดให้) --</option>
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

        // =========================================================================
        // ฟังก์ชันเครื่องมือจัดรูปแบบข้อความสไตล์ Microsoft Word (Ribbon Functions)
        // =========================================================================
        const fontSizesList = [12, 13, 14, 15, 16, 18, 20, 22, 24, 29];

        function getMemoEditor(range) {
            let checkNode = range ? range.commonAncestorContainer : null;
            while (checkNode && checkNode !== document.body) {
                if (checkNode.nodeType === 1 && (checkNode.id === 'memoBody' || checkNode.classList.contains('body-text'))) {
                    return checkNode;
                }
                checkNode = checkNode.parentNode;
            }
            return document.getElementById('memoBody');
        }

        function getClosestBlockInEditor(node, editor) {
            if (!node || !editor) return null;
            let curr = node.nodeType === 3 ? node.parentNode : node;
            let foundBlock = null;
            while (curr && curr !== editor && curr !== document.body) {
                if (curr.nodeType === 1 && (curr.tagName === 'P' || curr.tagName === 'DIV' || curr.tagName === 'LI')) {
                    if (!curr.classList.contains('body-text') &&
                        !curr.classList.contains('page') &&
                        !curr.classList.contains('page-wrapper')) {
                        foundBlock = curr;
                        break;
                    }
                }
                curr = curr.parentNode;
            }
            return foundBlock;
        }

        function execFormat(cmd, val = null) {
            document.execCommand(cmd, false, val);
            updateToolbarStatus();
            debounceUpdatePageBreaks();
        }

        function applyFontSize(sizeStr) {
            const selection = window.getSelection();
            if (!selection || selection.rangeCount === 0) return;
            const range = selection.getRangeAt(0);
            const editor = getMemoEditor(range);
            if (!editor) return;

            if (!range.collapsed) {
                try {
                    const span = document.createElement('span');
                    span.style.fontSize = sizeStr;
                    const fragment = range.extractContents();
                    span.appendChild(fragment);
                    range.insertNode(span);

                    const newRange = document.createRange();
                    newRange.selectNodeContents(span);
                    selection.removeAllRanges();
                    selection.addRange(newRange);
                } catch(e) {
                    console.warn(e);
                }
            } else {
                const block = getClosestBlockInEditor(range.startContainer, editor);
                if (block) {
                    block.style.fontSize = sizeStr;
                }
            }
            syncFontSizeSelect();
            debounceUpdatePageBreaks();
        }

        function stepFontSize(delta) {
            const select = document.getElementById('fontSizeSelect');
            if (!select) return;
            let currentVal = parseInt(select.value, 10) || 16;
            let idx = fontSizesList.indexOf(currentVal);
            if (idx === -1) {
                let closest = fontSizesList.reduce((prev, curr) => Math.abs(curr - currentVal) < Math.abs(prev - currentVal) ? curr : prev);
                idx = fontSizesList.indexOf(closest);
            }
            let newIdx = Math.max(0, Math.min(fontSizesList.length - 1, idx + delta));
            let newSize = fontSizesList[newIdx] + 'pt';
            select.value = newSize;
            applyFontSize(newSize);
        }

        function syncFontSizeSelect() {
            const selection = window.getSelection();
            if (!selection || selection.rangeCount === 0) return;
            const range = selection.getRangeAt(0);
            let node = range.startContainer;
            if (node.nodeType === 3) node = node.parentNode;
            if (!node) return;

            const computed = window.getComputedStyle(node);
            const fontSizePx = parseFloat(computed.fontSize);
            const fontSizePt = Math.round(fontSizePx * 0.75);
            const select = document.getElementById('fontSizeSelect');
            if (select) {
                let closest = fontSizesList.reduce((prev, curr) => Math.abs(curr - fontSizePt) < Math.abs(prev - fontSizePt) ? curr : prev);
                select.value = closest + 'pt';
            }
        }

        function segmentThaiElement(el) {
            if (typeof Intl !== 'undefined' && Intl.Segmenter) {
                const segmenter = new Intl.Segmenter('th', { granularity: 'word' });
                const walker = document.createTreeWalker(el, NodeFilter.SHOW_TEXT, null, false);
                let node;
                const textNodes = [];
                while (node = walker.nextNode()) {
                    textNodes.push(node);
                }
                textNodes.forEach(tNode => {
                    const cleanText = tNode.nodeValue.replace(/\u200B/g, '');
                    if (cleanText.trim().length > 0) {
                        const words = Array.from(segmenter.segment(cleanText)).map(s => s.segment);
                        tNode.nodeValue = words.join('\u200B');
                    }
                });
            }
        }

        function applyAlignmentStyle(block, alignType) {
            if (!block || !block.style) return;
            block.removeAttribute('align');
            if (alignType === 'left') {
                block.style.textAlign = 'left';
                block.style.textAlignLast = 'left';
                block.style.textJustify = 'auto';
                block.style.textIndent = '0';
            } else if (alignType === 'center') {
                block.style.textAlign = 'center';
                block.style.textAlignLast = 'center';
                block.style.textJustify = 'auto';
                block.style.textIndent = '0';
            } else if (alignType === 'right') {
                block.style.textAlign = 'right';
                block.style.textAlignLast = 'right';
                block.style.textJustify = 'auto';
                block.style.textIndent = '0';
            } else if (alignType === 'justify') {
                block.style.textAlign = 'justify';
                block.style.textJustify = 'inter-character';
                block.style.webkitTextJustify = 'inter-character';
                block.style.textIndent = '2.5cm';
                
                segmentThaiElement(block);
                const textLen = (block.innerText || block.textContent || '').trim().length;
                if (textLen < 60 && textLen > 5) {
                    block.style.textAlignLast = 'justify';
                } else {
                    block.style.textAlignLast = 'left';
                }
            }
        }

        function formatAlignment(alignType) {
            const selection = window.getSelection();
            if (!selection || selection.rangeCount === 0) return;
            const range = selection.getRangeAt(0);
            const editor = getMemoEditor(range);
            if (!editor) return;

            if (!range.collapsed) {
                const selectedText = selection.toString();
                if (!selectedText || selectedText.trim().length === 0) return;

                const startBlock = getClosestBlockInEditor(range.startContainer, editor);
                const endBlock = getClosestBlockInEditor(range.endContainer, editor);

                if (startBlock && endBlock && startBlock === endBlock) {
                    const blockText = (startBlock.innerText || startBlock.textContent || '').trim();
                    if (selectedText.trim() === blockText) {
                        applyAlignmentStyle(startBlock, alignType);
                    } else {
                        try {
                            const afterRange = document.createRange();
                            afterRange.setStart(range.endContainer, range.endOffset);
                            afterRange.setEnd(startBlock, startBlock.childNodes.length);
                            const afterFragment = afterRange.extractContents();

                            const selectedRange = document.createRange();
                            selectedRange.setStart(range.startContainer, range.startOffset);
                            selectedRange.setEnd(startBlock, startBlock.childNodes.length);
                            const selectedFragment = selectedRange.extractContents();

                            const selectedBlock = document.createElement('p');
                            selectedBlock.appendChild(selectedFragment);
                            applyAlignmentStyle(selectedBlock, alignType);

                            let afterBlock = null;
                            if (afterFragment.textContent.trim().length > 0 || afterFragment.querySelector('img, br')) {
                                afterBlock = document.createElement('p');
                                afterBlock.appendChild(afterFragment);
                            }

                            startBlock.parentNode.insertBefore(selectedBlock, startBlock.nextSibling);
                            if (afterBlock) {
                                selectedBlock.parentNode.insertBefore(afterBlock, selectedBlock.nextSibling);
                            }

                            if (startBlock.textContent.trim().length === 0 && !startBlock.querySelector('img, br')) {
                                startBlock.parentNode.removeChild(startBlock);
                            }

                            const newRange = document.createRange();
                            newRange.selectNodeContents(selectedBlock);
                            selection.removeAllRanges();
                            selection.addRange(newRange);
                        } catch (err) {
                            console.warn('Splitting block fallback:', err);
                            applyAlignmentStyle(startBlock, alignType);
                        }
                    }
                    debounceUpdatePageBreaks();
                    return;
                }

                const candidateBlocks = editor.querySelectorAll('p, div');
                let formattedCount = 0;
                candidateBlocks.forEach(el => {
                    if (selection.containsNode(el, true)) {
                        applyAlignmentStyle(el, alignType);
                        formattedCount++;
                    }
                });

                if (formattedCount === 0 && startBlock) {
                    applyAlignmentStyle(startBlock, alignType);
                }
                debounceUpdatePageBreaks();
                return;
            }

            let block = getClosestBlockInEditor(range.startContainer, editor);
            if (!block || block === editor) {
                let targetNode = range.startContainer;
                while (targetNode && targetNode.parentNode !== editor) {
                    targetNode = targetNode.parentNode;
                }
                if (targetNode && targetNode !== editor) {
                    const p = document.createElement('p');
                    editor.insertBefore(p, targetNode);
                    p.appendChild(targetNode);
                    block = p;
                } else {
                    const p = document.createElement('p');
                    p.innerHTML = '<br>';
                    editor.appendChild(p);
                    block = p;
                }
            }

            applyAlignmentStyle(block, alignType);
            debounceUpdatePageBreaks();
        }

        function handleShiftEnterJustify() {
            const selection = window.getSelection();
            if (!selection || selection.rangeCount === 0) return;
            const range = selection.getRangeAt(0);
            const editor = getMemoEditor(range);
            if (!editor) return;

            let block = getClosestBlockInEditor(range.startContainer, editor);
            if (!block || block === editor) {
                let targetNode = range.startContainer;
                while (targetNode && targetNode.parentNode !== editor) {
                    targetNode = targetNode.parentNode;
                }
                if (targetNode && targetNode !== editor) {
                    const p = document.createElement('p');
                    editor.insertBefore(p, targetNode);
                    p.appendChild(targetNode);
                    block = p;
                } else {
                    const p = document.createElement('p');
                    p.innerHTML = '<br>';
                    editor.appendChild(p);
                    block = p;
                }
            }

            if (!range.collapsed) {
                range.deleteContents();
            }

            const afterRange = document.createRange();
            afterRange.setStart(range.endContainer, range.endOffset);
            afterRange.setEnd(block, block.childNodes.length);
            const afterFragment = afterRange.extractContents();

            block.style.marginBottom = '0';
            block.style.textAlign = 'justify';
            block.style.textAlignLast = 'justify';
            block.style.textJustify = 'inter-character';
            block.style.webkitTextJustify = 'inter-character';

            segmentThaiElement(block);

            const nextBlock = document.createElement('p');
            nextBlock.style.textIndent = '0';
            nextBlock.style.marginTop = '0';
            nextBlock.style.marginBottom = '0';
            nextBlock.style.textAlign = 'justify';
            nextBlock.style.textAlignLast = 'left';
            nextBlock.style.textJustify = 'inter-character';
            nextBlock.style.webkitTextJustify = 'inter-character';

            if (afterFragment.textContent.trim().length > 0 || afterFragment.querySelector('img, br')) {
                nextBlock.appendChild(afterFragment);
            } else {
                nextBlock.innerHTML = '<br>';
            }

            block.parentNode.insertBefore(nextBlock, block.nextSibling);

            const newRange = document.createRange();
            if (nextBlock.firstChild) {
                newRange.setStart(nextBlock.firstChild, 0);
            } else {
                newRange.setStart(nextBlock, 0);
            }
            newRange.collapse(true);
            selection.removeAllRanges();
            selection.addRange(newRange);
            debounceUpdatePageBreaks();
        }

        function handleNormalEnter(e) {
            const selection = window.getSelection();
            if (!selection || selection.rangeCount === 0) return;
            const range = selection.getRangeAt(0);
            const editor = getMemoEditor(range);
            if (!editor) return;

            const block = getClosestBlockInEditor(range.startContainer, editor);
            if (!block) return;

            e.preventDefault();

            if (!range.collapsed) {
                range.deleteContents();
            }

            const afterRange = document.createRange();
            afterRange.setStart(range.endContainer, range.endOffset);
            afterRange.setEnd(block, block.childNodes.length);
            const afterFragment = afterRange.extractContents();

            block.style.marginBottom = 'var(--paragraph-spacing, 10pt)';
            if (block.style.textAlignLast !== 'justify') {
                block.style.textAlignLast = 'left';
            }

            const newP = document.createElement('p');
            newP.style.textAlign = 'justify';
            newP.style.textAlignLast = 'left';
            newP.style.textJustify = 'inter-character';
            newP.style.webkitTextJustify = 'inter-character';
            newP.style.textIndent = '2.5cm';
            newP.style.marginTop = '0';
            newP.style.marginBottom = 'var(--paragraph-spacing, 10pt)';

            if (afterFragment.textContent.trim().length > 0 || afterFragment.querySelector('img, br')) {
                newP.appendChild(afterFragment);
            } else {
                newP.innerHTML = '<br>';
            }

            block.parentNode.insertBefore(newP, block.nextSibling);

            const newRange = document.createRange();
            if (newP.firstChild) {
                newRange.setStart(newP.firstChild, 0);
            } else {
                newRange.setStart(newP, 0);
            }
            newRange.collapse(true);
            selection.removeAllRanges();
            selection.addRange(newRange);
            debounceUpdatePageBreaks();
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                const selection = window.getSelection();
                if (!selection || selection.rangeCount === 0) return;
                
                let node = selection.getRangeAt(0).commonAncestorContainer;
                let insideEditor = false;
                while (node && node !== document.body) {
                    if (node.nodeType === 1 && (node.id === 'memoBody' || node.classList.contains('body-text'))) {
                        insideEditor = true;
                        break;
                    }
                    node = node.parentNode;
                }

                if (insideEditor) {
                    if (e.shiftKey) {
                        e.preventDefault();
                        handleShiftEnterJustify();
                    } else {
                        handleNormalEnter(e);
                    }
                }
            } else if (e.ctrlKey || e.metaKey) {
                if (e.key === ']') {
                    e.preventDefault();
                    stepFontSize(1);
                } else if (e.key === '[') {
                    e.preventDefault();
                    stepFontSize(-1);
                } else if (e.key === 'l' || e.key === 'L') {
                    e.preventDefault();
                    formatAlignment('left');
                } else if (e.key === 'e' || e.key === 'E') {
                    e.preventDefault();
                    formatAlignment('center');
                } else if (e.key === 'r' || e.key === 'R') {
                    e.preventDefault();
                    formatAlignment('right');
                } else if (e.key === 'j' || e.key === 'J') {
                    e.preventDefault();
                    formatAlignment('justify');
                }
            }
        });

        function changeParagraphIndent(delta) {
            const selection = window.getSelection();
            if (!selection || selection.rangeCount === 0) return;
            const range = selection.getRangeAt(0);
            const editor = getMemoEditor(range);
            if (!editor) return;

            const block = getClosestBlockInEditor(range.startContainer, editor);
            if (!block) return;

            if (delta > 0) {
                block.style.textIndent = '2.5cm';
            } else {
                block.style.textIndent = '0';
            }
            updateToolbarStatus();
            debounceUpdatePageBreaks();
        }

        function toggleParagraphIndent() {
            const selection = window.getSelection();
            if (!selection || selection.rangeCount === 0) return;
            const range = selection.getRangeAt(0);
            const editor = getMemoEditor(range);
            if (!editor) return;

            const block = getClosestBlockInEditor(range.startContainer, editor);
            if (!block) return;

            const currIndent = (block.style.textIndent || '').trim();
            if (currIndent === '0px' || currIndent === '0' || currIndent === '') {
                block.style.textIndent = '2.5cm';
            } else {
                block.style.textIndent = '0';
            }
            updateToolbarStatus();
            debounceUpdatePageBreaks();
        }

        function insertHorizontalRule() {
            const selection = window.getSelection();
            if (!selection || selection.rangeCount === 0) return;
            const range = selection.getRangeAt(0);
            const hr = document.createElement('div');
            hr.className = 'memo-divider-line';
            hr.style.cssText = 'width: 100%; height: 1px; background-color: #000000; margin: 12pt 0;';
            range.insertNode(hr);
            range.setStartAfter(hr);
            range.collapse(true);
            selection.removeAllRanges();
            selection.addRange(range);
            debounceUpdatePageBreaks();
        }

        function updateToolbarStatus() {
            try {
                const boldBtn = document.getElementById('btnBold');
                const italicBtn = document.getElementById('btnItalic');
                const underBtn = document.getElementById('btnUnderline');
                const strikeBtn = document.getElementById('btnStrike');
                const numListBtn = document.getElementById('btnNumList');
                const bulletListBtn = document.getElementById('btnBulletList');
                const paragraphBtn = document.getElementById('btnParagraphIndent');

                if (boldBtn) boldBtn.classList.toggle('active', document.queryCommandState('bold'));
                if (italicBtn) italicBtn.classList.toggle('active', document.queryCommandState('italic'));
                if (underBtn) underBtn.classList.toggle('active', document.queryCommandState('underline'));
                if (strikeBtn) strikeBtn.classList.toggle('active', document.queryCommandState('strikethrough'));
                if (numListBtn) numListBtn.classList.toggle('active', document.queryCommandState('insertOrderedList'));
                if (bulletListBtn) bulletListBtn.classList.toggle('active', document.queryCommandState('insertUnorderedList'));

                if (paragraphBtn) {
                    const selection = window.getSelection();
                    if (selection && selection.rangeCount > 0) {
                        const range = selection.getRangeAt(0);
                        let checkNode = range.startContainer;
                        let isIndented = false;
                        while (checkNode && checkNode !== document.body) {
                            if (checkNode.nodeType === 1 && (checkNode.tagName === 'P' || checkNode.tagName === 'DIV')) {
                                const indent = checkNode.style.textIndent;
                                isIndented = (indent === '2.5cm' || indent === 'var(--indent-size)');
                                break;
                            }
                            checkNode = checkNode.parentNode;
                        }
                        paragraphBtn.classList.toggle('active', isIndented);
                    }
                }

                syncFontSizeSelect();
            } catch (e) {}
        }
        document.addEventListener('selectionchange', updateToolbarStatus);

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
            try {
                formatThaiTypography(false);
            } catch (e) {
                console.warn('formatThaiTypography warning:', e);
            }
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
            const bodyContent = memoBody ? memoBody.innerHTML : '';
            document.getElementById('hiddenContent').value = bodyContent;

            // ตรวจสอบชื่อเรื่อง
            if (!title) {
                if (typeof AppAlert !== 'undefined' && AppAlert.warning) {
                    AppAlert.warning('กรุณาระบุชื่อเรื่องบันทึกข้อความก่อนบันทึก', 'ยังไม่ได้ระบุชื่อเรื่อง').then(() => {
                        const el = document.getElementById('field_title');
                        if (el) { el.focus(); el.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
                    });
                } else {
                    alert('กรุณาระบุชื่อเรื่องบันทึกข้อความ');
                    const el = document.getElementById('field_title');
                    if (el) el.focus();
                }
                return;
            }

            const form = document.getElementById('memoForm');

            function executeSubmitWithLoading(titleText, bodyText) {
                const mainBtn = document.getElementById('btnSubmitMemo');
                if (mainBtn && typeof AppAlert !== 'undefined' && AppAlert.setButtonLoading) {
                    AppAlert.setButtonLoading(mainBtn, action === 'draft' ? 'กำลังบันทึกร่าง...' : 'กำลังส่งสารบรรณ...');
                }
                if (typeof AppAlert !== 'undefined' && AppAlert.showLoading) {
                    AppAlert.showLoading(titleText, bodyText);
                }
                form.submit();
            }

            // บันทึกร่าง: ส่งฟอร์มทันที
            if (action === 'draft') {
                executeSubmitWithLoading('กำลังบันทึกฉบับร่าง...', 'ระบบกำลังบันทึกข้อมูลร่าง กรุณารอสักครู่');
                return;
            }

            // บันทึกและส่งให้สารบรรณ: แจ้งยืนยันและส่งให้งานสารบรรณออกเลขและกำหนดเส้นทางเกษียน
            if (action === 'route') {
                if (typeof AppAlert !== 'undefined' && AppAlert.confirm) {
                    AppAlert.confirm('ต้องการบันทึกและส่งบันทึกข้อความนี้ให้ "งานสารบรรณ" ตรวจสอบและออกเลขหนังสือใช่หรือไม่?', {
                        title: 'ยืนยันการส่งบันทึกข้อความ',
                        confirmText: '<i class="bx bx-paper-plane me-1"></i> ยืนยันส่งสารบรรณ',
                        cancelText: 'ตรวจสอบก่อน'
                    }).then((confirmed) => {
                        if (confirmed) {
                            executeSubmitWithLoading('กำลังส่งสารบรรณ...', 'ระบบกำลังบันทึกและส่งเรื่องให้งานสารบรรณ กรุณารอสักครู่');
                        }
                    });
                } else {
                    if (confirm('ต้องการบันทึกและส่งบันทึกข้อความนี้ให้ "งานสารบรรณ" ตรวจสอบและออกเลขหนังสือใช่หรือไม่?')) {
                        executeSubmitWithLoading('กำลังส่งสารบรรณ...', 'ระบบกำลังบันทึกและส่งเรื่องให้งานสารบรรณ กรุณารอสักครู่');
                    }
                }
            }
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
    <script src="<?= base_url('assets/vendor/libs/jquery/jquery.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/js/bootstrap.js') ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?= base_url('assets/js/app-swal.js?v=' . (file_exists(FCPATH . 'assets/js/app-swal.js') ? filemtime(FCPATH . 'assets/js/app-swal.js') : time())) ?>"></script>
    <script src="<?= base_url('assets/js/submit-loading.js?v=' . (file_exists(FCPATH . 'assets/js/submit-loading.js') ? filemtime(FCPATH . 'assets/js/submit-loading.js') : time())) ?>"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php if (session()->getFlashdata('success')): ?>
            AppAlert.success(<?= json_encode(session()->getFlashdata('success')) ?>, 'ดำเนินการสำเร็จ');
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            AppAlert.error(<?= json_encode(session()->getFlashdata('error')) ?>, 'แจ้งเตือนข้อผิดพลาด');
        <?php endif; ?>
        <?php if (session()->getFlashdata('warning')): ?>
            AppAlert.warning(<?= json_encode(session()->getFlashdata('warning')) ?>, 'ข้อควรทราบ');
        <?php endif; ?>
    });
    </script>
</body>

</html>