<!DOCTYPE html>
<html lang="th" class="light-style layout-wide" dir="ltr" data-theme="theme-default">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>แก้ไขคำสั่งโรงเรียน | e-Saraban โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= base_url('assets/img/favicon/favicon.ico') ?>" />

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=K2D:wght@300;400;500;600;700&family=Sarabun:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">

    <!-- Icons & Core CSS -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="<?= base_url('assets/vendor/css/core.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/css/demo.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/css/orders_saraban.css?v=' . time()) ?>" />

    <style>
        body {
            background-color: #525659;
            font-family: 'K2D', sans-serif;
            margin: 0;
            padding: 0;
        }
    </style>
</head>

<body>

    <!-- Form for Updating Order -->
    <form action="<?= base_url('admin/orders/' . $order['id'] . '/update') ?>" method="post" id="editOrderForm">
        <?= csrf_field() ?>
        <input type="hidden" name="status" id="orderStatusInput" value="<?= esc($order['status']) ?>" />
        <input type="hidden" name="content" id="orderContentInput" value="" />
        <input type="hidden" name="year" value="<?= esc($order['year']) ?>" />

        <!-- Unified Top Sticky Bar (ฟิกเมนูทั้ง Action Bar และ Ribbon ไว้ด้านบน ไม่ให้เลื่อนตาม) -->
        <div class="document-top-sticky no-print">
            <!-- Top Action Bar -->
            <div class="order-action-bar d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <a href="<?= base_url('admin/orders/' . $order['id']) ?>" class="btn btn-outline-secondary btn-sm fw-semibold">
                    <i class="bx bx-arrow-back me-1"></i> ย้อนกลับ
                </a>
                <span class="badge bg-warning text-dark fw-bold px-3 py-1.5 fs-7">
                    <i class="bx bx-edit me-1"></i> กำลังแก้ไขคำสั่งเลขที่ <?= esc($order['doc_no']) ?>
                </span>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex align-items-center gap-2">
                <div id="editOrderPageCounter" class="badge bg-dark text-white fw-bold px-2.5 py-2 shadow-xs">
                    <i class="bx bx-file text-warning me-1"></i> <span id="pageCountText">เอกสาร ๑ แผ่น (A4)</span>
                </div>
                <button type="button" class="btn btn-outline-dark btn-sm fw-semibold" onclick="openA4PreviewModal()" title="ดูตัวอย่างการจัดหน้า A4 แยกแผ่นจริง">
                    <i class="bx bx-show me-1"></i> ดูตัวอย่าง A4
                </button>
                <button type="button" class="btn btn-primary btn-sm fw-bold px-3 shadow-xs" onclick="submitEditOrder(this)">
                    <i class="bx bx-save me-1"></i> บันทึกการแก้ไข
                </button>
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
                    <option value="29pt">29 pt (หัวเรื่องคำสั่ง)</option>
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

            <!-- Group 6: เครื่องมือสารบรรณ (Divider Line, จัดคำไทยสวย, จัดแบ่งหน้า A4) -->
            <div class="ribbon-group">
                <button type="button" class="ribbon-btn text-secondary" onmousedown="event.preventDefault(); insertHorizontalRule()" title="แทรกเส้นคั่นแบ่งส่วน">
                    <i class="bx bx-minus"></i>
                </button>
                <button type="button" class="ribbon-btn text-info fw-semibold" onmousedown="event.preventDefault(); formatThaiTypography()" title="ตัดคำภาษาไทยให้อ่านง่ายและกระจายขอบสวยงาม">
                    <i class="bx bx-font me-1"></i><span class="small d-none d-xl-inline">จัดคำไทยสวย</span>
                </button>
                <button type="button" class="ribbon-btn text-dark fw-semibold" onmousedown="event.preventDefault(); rePaginateEditPaper()" title="คำนวณและจัดแบ่งแผ่นกระดาษ A4 ใหม่ตามเนื้อหาปัจจุบัน">
                    <i class="bx bx-layout me-1"></i><span class="small d-none d-xl-inline">จัดแบ่งหน้า A4</span>
                </button>
            </div>

            <!-- Group 7: หมวดหมู่และสถานะคำสั่ง (จัดวางชิดขวาของแท็บ Ribbon) -->
            <div class="ms-auto d-flex align-items-center gap-2">
                <!-- หมวดหมู่งานคำสั่ง -->
                <div class="ribbon-meta-item shadow-xs" title="เลือกหมวดหมู่งานคำสั่ง">
                    <i class="bx bx-category text-primary me-1 fs-6"></i>
                    <span class="small fw-bold text-dark me-1 d-none d-lg-inline" style="font-size: 11px;">หมวดหมู่:</span>
                    <select name="order_category" class="ribbon-meta-select">
                        <option value="บริหารทั่วไป" <?= ($order['order_category'] ?? '') === 'บริหารทั่วไป' ? 'selected' : '' ?>>บริหารทั่วไป</option>
                        <option value="วิชาการ" <?= ($order['order_category'] ?? '') === 'วิชาการ' ? 'selected' : '' ?>>วิชาการ</option>
                        <option value="กิจการนักเรียน" <?= ($order['order_category'] ?? '') === 'กิจการนักเรียน' ? 'selected' : '' ?>>กิจการนักเรียน</option>
                        <option value="บุคคลและงบประมาณ" <?= ($order['order_category'] ?? '') === 'บุคคลและงบประมาณ' ? 'selected' : '' ?>>บุคคลและงบประมาณ</option>
                    </select>
                </div>

                <!-- สถานะคำสั่ง -->
                <div class="ribbon-meta-item shadow-xs" title="เลือกสถานะคำสั่ง">
                    <i class="bx bx-check-shield text-success me-1 fs-6"></i>
                    <span class="small fw-bold text-dark me-1 d-none d-lg-inline" style="font-size: 11px;">สถานะ:</span>
                    <select name="status" class="ribbon-meta-select" id="statusSelect">
                        <option value="published" <?= $order['status'] === 'published' ? 'selected' : '' ?>>เผยแพร่แล้ว (มีผลบังคับใช้)</option>
                        <option value="draft" <?= $order['status'] === 'draft' ? 'selected' : '' ?>>แบบร่าง (ยังไม่เผยแพร่)</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

        <!-- Official Paper Container (1:1 with แบบฟอร์ม_คำสั่ง.docx) -->
        <div class="order-page-wrapper">
            <div class="saraban-order-paper" id="orderEditPaperEl">

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
                        ที่ <input type="text" name="doc_no" id="docNoInput" value="<?= esc($order['doc_no']) ?>" class="order-editable-input text-center fw-bold" style="width: 140px;" required />
                    </div>
                    <div class="order-subject-title d-flex justify-content-center align-items-center">
                        <span>เรื่อง</span>
                        <input type="text" name="title" id="orderTitleInput" value="<?= esc($order['title']) ?>" class="order-editable-input fw-bold ms-2" style="width: 78%;" required />
                    </div>
                    <div class="order-divider-line"></div>
                </div>

                <!-- 3. เนื้อหาคำสั่ง -->
                <div id="orderEditor" class="order-body-content" contenteditable="true" style="min-height: 280px; outline: none;">
                    <?= !empty($order['content']) ? $order['content'] : '<p>พิมพ์เนื้อหาคำสั่ง...</p>' ?>
                </div>

                <!-- 4. ย่อหน้าบังคับใช้ (ทั้งนี้ ตั้งแต่...) -->
                <div class="order-effective-clause">
                    ทั้งนี้ ตั้งแต่ <input type="text" name="order_effective_text" value="<?= esc($order['order_effective_text'] ?: 'บัดนี้เป็นต้นไป') ?>" class="order-editable-input" style="width: 60%;" />
                </div>

                <!-- 5. วันสั่งการ (สั่ง ณ วันที่ ...) -->
                <div class="order-date-clause">
                    สั่ง ณ วันที่ 
                    <input type="date" name="doc_date" id="docDateInput" value="<?= esc($order['doc_date']) ?>" class="form-control form-control-sm d-inline-block text-center" style="width: 155px; font-family: var(--order-font); font-size: 15pt;" required />
                </div>

                <!-- 6. ส่วนลงนามผู้อำนวยการ/ผู้บริหาร (เมนูเลือกในวงเล็บและตำแหน่งแบบเนียน ๆ) -->
                <div class="order-signature-container">
                    <div class="order-signature-image-wrap">
                        <?php if (!empty($order['creator_signature'])): ?>
                            <img src="<?= base_url(esc($order['creator_signature'])) ?>" alt="ลายเซ็น" class="order-signature-img" />
                        <?php endif; ?>
                    </div>

                    <!-- แถวชื่อ-สกุล ในวงเล็บ พร้อมเมนูเลือกแบบเนียน ๆ -->
                    <div class="order-signer-fullname d-flex justify-content-center align-items-center">
                        <span class="me-1">(</span>
                        <div class="dropup d-inline-flex align-items-center">
                            <input type="text" name="signer_name" id="signerNameInput" 
                                   value="<?= esc($order['signer_name'] ?: 'นายพงษ์ศักดิ์ เงินสันเทียะ') ?>" 
                                   class="order-editable-input text-center fw-medium" 
                                   style="width: 250px;" 
                                   title="พิมพ์ชื่อผู้ลงนาม หรือกดลูกศรเพื่อเลือกจากฐานข้อมูล" />
                            <?php if (!empty($executives)): ?>
                                <button type="button" class="btn btn-sm btn-link text-muted p-0 ms-1 no-print" 
                                        data-bs-toggle="dropdown" aria-expanded="false" 
                                        title="คลิกเลือกผู้บริหารจากฐานข้อมูล tb_personnel" style="text-decoration: none; line-height: 1;">
                                    <i class="bx bx-caret-down text-primary" style="font-size: 13pt;"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow border-0 py-1" style="min-width: 290px; z-index: 1050;">
                                    <li class="dropdown-header text-primary fw-bold small py-1 border-bottom">
                                        <i class="bx bx-user-check me-1"></i> เลือกผู้บริหาร (tb_personnel)
                                    </li>
                                    <?php foreach ($executives as $ex): ?>
                                        <li>
                                            <a class="dropdown-item py-1.5 small <?= $ex['is_director'] ? 'fw-bold text-dark' : '' ?>" 
                                               href="javascript:void(0);" 
                                               onclick="selectSigner('<?= esc($ex['full_name'], 'js') ?>', '<?= esc($ex['position_name'], 'js') ?>', <?= $ex['is_director'] ? 'true' : 'false' ?>)">
                                                <?= esc($ex['full_name']) ?>
                                                <span class="badge <?= $ex['is_director'] ? 'bg-primary' : 'bg-secondary' ?> text-white ms-1" style="font-size: 9px;"><?= esc($ex['posi_name']) ?></span>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                        <span class="ms-1">)</span>
                    </div>

                    <!-- แถวตำแหน่ง พร้อมเมนูเลือกรักษาการแทนแบบเนียน ๆ -->
                    <div class="order-signer-position d-flex justify-content-center align-items-center mt-1">
                        <div class="dropup d-inline-flex align-items-center">
                            <input type="text" name="signer_position" id="signerPositionInput" 
                                   value="<?= esc($order['signer_position'] ?: 'ผู้อำนวยการโรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์') ?>" 
                                   class="order-editable-input text-center" 
                                   style="width: 440px;" 
                                   title="พิมพ์ตำแหน่ง หรือกดลูกศรเพื่อเลือกรูปแบบรักษาการแทน" />
                            <button type="button" class="btn btn-sm btn-link text-muted p-0 ms-1 no-print" 
                                    data-bs-toggle="dropdown" aria-expanded="false" 
                                    title="คลิกเปลี่ยนตำแหน่ง / รูปแบบรักษาการแทน" style="text-decoration: none; line-height: 1;">
                                <i class="bx bx-caret-down text-primary" style="font-size: 13pt;"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 py-1" style="min-width: 420px; z-index: 1050;">
                                <li class="dropdown-header text-primary fw-bold small py-1 border-bottom">
                                    <i class="bx bx-briefcase-alt-2 me-1"></i> เลือกตำแหน่ง / รูปแบบรักษาการ
                                </li>
                                <li>
                                    <a class="dropdown-item py-1.5 small" href="javascript:void(0);" 
                                       onclick="selectPosition('ผู้อำนวยการโรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์')">
                                        <i class="bx bx-check-circle text-success me-1"></i> ผู้อำนวยการโรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-1.5 small fw-semibold text-warning" href="javascript:void(0);" 
                                       onclick="selectPosition('รองผู้อำนวยการโรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์ รักษาการในตำแหน่งผู้อำนวยการโรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์')">
                                        <i class="bx bx-refresh me-1"></i> รอง ผอ. รักษาการในตำแหน่ง ผอ.
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-1.5 small text-info" href="javascript:void(0);" 
                                       onclick="selectPosition('รักษาการในตำแหน่งผู้อำนวยการโรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์')">
                                        <i class="bx bx-time-five me-1"></i> รักษาการในตำแหน่งผู้อำนวยการโรงเรียน...
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-1.5 small text-primary" href="javascript:void(0);" 
                                       onclick="selectPosition('ปฏิบัติราชการแทนผู้อำนวยการโรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์')">
                                        <i class="bx bx-transfer-alt me-1"></i> ปฏิบัติราชการแทนผู้อำนวยการโรงเรียน...
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-1.5 small text-secondary" href="javascript:void(0);" 
                                       onclick="selectPosition('รองผู้อำนวยการโรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์')">
                                        <i class="bx bx-user me-1"></i> รองผู้อำนวยการโรงเรียน... (ตำแหน่งปกติ)
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </form>

    <!-- Scripts -->
    <script src="<?= base_url('assets/vendor/libs/jquery/jquery.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/js/bootstrap.js') ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?= base_url('assets/js/app-swal.js?v=' . (file_exists(FCPATH . 'assets/js/app-swal.js') ? filemtime(FCPATH . 'assets/js/app-swal.js') : time())) ?>"></script>
    <script src="<?= base_url('assets/js/submit-loading.js?v=' . (file_exists(FCPATH . 'assets/js/submit-loading.js') ? filemtime(FCPATH . 'assets/js/submit-loading.js') : time())) ?>"></script>
    <script src="<?= base_url('assets/js/orders_pagination.js?v=' . time()) ?>"></script>
    <script>
        function submitEditOrder(btnElement) {
            const titleInput = document.getElementById('orderTitleInput') || document.querySelector('input[name="title"]');
            const title = titleInput ? titleInput.value.trim() : '';
            if (!title) {
                if (window.AppAlert && typeof window.AppAlert.warning === 'function') {
                    AppAlert.warning('กรุณาระบุชื่อเรื่องคำสั่งโรงเรียนก่อนบันทึก', 'ยังไม่ได้ระบุชื่อเรื่อง');
                } else {
                    alert('กรุณาระบุชื่อเรื่องคำสั่งโรงเรียน');
                }
                if (titleInput) titleInput.focus();
                return;
            }

            // รวบรวมเนื้อหาคำสั่งจากทุกแผ่นกระดาษอย่างสมบูรณ์
            let content = '';
            if (typeof SarabanOrderPaginator !== 'undefined') {
                content = SarabanOrderPaginator.getCombinedContent('orderEditPaperEl');
            } else {
                const editor = document.getElementById('orderEditor');
                content = editor ? editor.innerHTML : '';
            }

            document.getElementById('orderContentInput').value = content;
            document.getElementById('orderStatusInput').value = document.getElementById('statusSelect').value;

            // Loading state บนปุ่มที่กด และ SweetAlert2 loading
            if (btnElement && window.SubmitLoading) {
                SubmitLoading.set(btnElement, 'กำลังบันทึกการแก้ไข...');
            }
            if (window.AppAlert && typeof window.AppAlert.showLoading === 'function') {
                AppAlert.showLoading('กำลังบันทึกการแก้ไข...', 'ระบบกำลังจัดเก็บข้อมูลคำสั่งโรงเรียน กรุณารอสักครู่');
            }

            document.getElementById('editOrderForm').submit();
        }

        function rePaginateEditPaper() {
            if (typeof SarabanOrderPaginator !== 'undefined') {
                SarabanOrderPaginator.rePaginate('orderEditPaperEl');
            }
        }

        function formatThaiTypography() {
            const container = document.getElementById('orderEditPaperEl');
            if (!container) return;

            if (typeof Intl !== 'undefined' && Intl.Segmenter) {
                const segmenter = new Intl.Segmenter('th', { granularity: 'word' });
                const editors = container.querySelectorAll('.order-body-content');
                editors.forEach(editor => {
                    const walker = document.createTreeWalker(editor, NodeFilter.SHOW_TEXT, null, false);
                    let node;
                    const textNodes = [];
                    while (node = walker.nextNode()) {
                        textNodes.push(node);
                    }
                    textNodes.forEach(tNode => {
                        if (tNode.nodeValue.trim().length > 0) {
                            const words = Array.from(segmenter.segment(tNode.nodeValue)).map(s => s.segment);
                            tNode.nodeValue = words.join('\u200B');
                        }
                    });
                });
                alert('จัดระเบียบการตัดคำภาษาไทยเรียบร้อยแล้ว');
                rePaginateEditPaper();
            } else {
                alert('เบราว์เซอร์ของท่านรองรับการจัดระเบียบคำภาษาไทยเรียบร้อยแล้ว');
            }
        }

        function openA4PreviewModal() {
            const paper = document.getElementById('orderEditPaperEl');
            const modalBody = document.getElementById('modalPreviewContainer');
            if (!paper || !modalBody) return;

            modalBody.innerHTML = '';
            const clone = paper.cloneNode(true);
            clone.id = 'modalClonedPaper';

            clone.querySelectorAll('input').forEach(inp => {
                const span = document.createElement('span');
                span.textContent = inp.value;
                span.className = inp.className;
                inp.parentNode.replaceChild(span, inp);
            });

            modalBody.appendChild(clone);

            if (typeof SarabanOrderPaginator !== 'undefined') {
                SarabanOrderPaginator.paginate(modalBody);
            }

            const modal = new bootstrap.Modal(document.getElementById('a4PreviewModal'));
            modal.show();
        }

        // ฟังก์ชันเลือกผู้ลงนามจากรายการผู้บริหาร (tb_personnel)
        function selectSigner(name, pos, isDirector) {
            const nameInput = document.getElementById('signerNameInput') || document.querySelector('input[name="signer_name"]');
            const posInput = document.getElementById('signerPositionInput') || document.querySelector('input[name="signer_position"]');
            if (nameInput) nameInput.value = name;
            
            // หากเลือก รอง ผอ. ให้สลับตำแหน่งเป็น "รักษาการในตำแหน่ง ผอ." ทันทีโดยอัตโนมัติ
            if (posInput) {
                if (!isDirector) {
                    posInput.value = 'รองผู้อำนวยการโรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์ รักษาการในตำแหน่งผู้อำนวยการโรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์';
                } else {
                    posInput.value = 'ผู้อำนวยการโรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์';
                }
            }

            // ซิงค์ทุกแผ่นที่แบ่งหน้า A4
            document.querySelectorAll('input[name="signer_name"]').forEach(inp => inp.value = name);
            document.querySelectorAll('input[name="signer_position"]').forEach(inp => {
                if (posInput) inp.value = posInput.value;
            });
        }

        // ฟังก์ชันเลือกตำแหน่ง/รูปแบบรักษาการ
        function selectPosition(pos) {
            const posInput = document.getElementById('signerPositionInput') || document.querySelector('input[name="signer_position"]');
            if (posInput) posInput.value = pos;
            document.querySelectorAll('input[name="signer_position"]').forEach(inp => inp.value = pos);
        }

        // ฟังก์ชันตัดคำภาษาไทยแทรก Zero-Width Space เพื่อให้เบราว์เซอร์กระจายคำได้ 100%
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

        // ฟังก์ชันช่วยจัดสไตล์การวางแนวข้อความให้เฉพาะเจาะจงบล็อกนั้น ๆ
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
                block.style.textIndent = 'var(--indent-size)';
                
                if (typeof segmentThaiElement === 'function') {
                    segmentThaiElement(block);
                }
                const textLen = (block.innerText || block.textContent || '').trim().length;
                if (textLen < 60 && textLen > 5) {
                    block.style.textAlignLast = 'justify';
                } else {
                    block.style.textAlignLast = 'left';
                }
            }
        }

        // ค้นหา Element บล็อก (P หรือ DIV) ที่อยู่ภายใน Editor โดยไม่เกินขอบเขต Editor
        function getClosestBlockInEditor(node, editor) {
            if (!node || !editor) return null;
            let curr = node.nodeType === 3 ? node.parentNode : node;
            let foundBlock = null;
            while (curr && curr !== editor && curr !== document.body) {
                if (curr.nodeType === 1 && (curr.tagName === 'P' || curr.tagName === 'DIV' || curr.tagName === 'LI')) {
                    if (!curr.classList.contains('order-body-content') &&
                        !curr.classList.contains('saraban-order-paper') &&
                        !curr.classList.contains('order-pages-container')) {
                        foundBlock = curr;
                        break;
                    }
                }
                curr = curr.parentNode;
            }
            return foundBlock;
        }

        // ฟังก์ชันจัดตำแหน่งข้อความ (กระทำเฉพาะแถวที่เคอร์เซอร์อยู่ หรือเฉพาะข้อความที่คลุมดำเท่านั้น 100%)
        function formatAlignment(alignType) {
            const selection = window.getSelection();
            if (!selection || selection.rangeCount === 0) return;
            const range = selection.getRangeAt(0);

            // ค้นหา Editor Container (.order-body-content)
            let editor = null;
            let checkNode = range.commonAncestorContainer;
            while (checkNode && checkNode !== document.body) {
                if (checkNode.nodeType === 1 && checkNode.classList.contains('order-body-content')) {
                    editor = checkNode;
                    break;
                }
                checkNode = checkNode.parentNode;
            }

            if (!editor) {
                editor = document.querySelector('.order-body-content[contenteditable="true"]') || document.getElementById('orderEditor');
            }
            if (!editor) return;

            // =========================================================================
            // กรณีที่ 1: มีการคลุมดำข้อความ (Selection ไม่ว่างเปล่า / !range.collapsed)
            // =========================================================================
            if (!range.collapsed) {
                const selectedText = selection.toString();
                if (!selectedText || selectedText.trim().length === 0) return;

                const startBlock = getClosestBlockInEditor(range.startContainer, editor);
                const endBlock = getClosestBlockInEditor(range.endContainer, editor);

                // กรณี 1.1: คลุมดำอยู่ภายในบล็อกเดียวกัน (Single Block Selection)
                if (startBlock && endBlock && startBlock === endBlock) {
                    const blockText = (startBlock.innerText || startBlock.textContent || '').trim();
                    
                    // ถ้าคลุมดำทั้งย่อหน้าพอดี ให้จัดตำแหน่งย่อหน้านั้นเลย
                    if (selectedText.trim() === blockText) {
                        applyAlignmentStyle(startBlock, alignType);
                    } else {
                        // คลุมดำ "เฉพาะบางข้อความ/บางแถว" ภายในย่อหน้า
                        // แยกส่วนที่คลุมดำออกมาเป็นบล็อกอิสระ เพื่อให้มีสไตล์จัดแนวเฉพาะข้อความที่เลือก
                        try {
                            // สกัดส่วนท้ายหลัง Selection
                            const afterRange = document.createRange();
                            afterRange.setStart(range.endContainer, range.endOffset);
                            afterRange.setEnd(startBlock, startBlock.childNodes.length);
                            const afterFragment = afterRange.extractContents();

                            // สกัดส่วนที่คลุมดำ
                            const selectedRange = document.createRange();
                            selectedRange.setStart(range.startContainer, range.startOffset);
                            selectedRange.setEnd(startBlock, startBlock.childNodes.length);
                            const selectedFragment = selectedRange.extractContents();

                            // สร้างบล็อกใหม่สำหรับส่วนที่เลือก
                            const selectedBlock = document.createElement('p');
                            selectedBlock.appendChild(selectedFragment);
                            applyAlignmentStyle(selectedBlock, alignType);

                            // สร้างบล็อกสำหรับส่วนท้าย (ถ้ามีเนื้อหา)
                            let afterBlock = null;
                            if (afterFragment.textContent.trim().length > 0 || afterFragment.querySelector('img, br')) {
                                afterBlock = document.createElement('p');
                                afterBlock.appendChild(afterFragment);
                            }

                            // แทรกต่อจาก startBlock
                            startBlock.parentNode.insertBefore(selectedBlock, startBlock.nextSibling);
                            if (afterBlock) {
                                selectedBlock.parentNode.insertBefore(afterBlock, selectedBlock.nextSibling);
                            }

                            // ถ้าส่วนก่อนหน้าไม่มีข้อความเหลือ ให้ลบ startBlock เดิมออก
                            if (startBlock.textContent.trim().length === 0 && !startBlock.querySelector('img, br')) {
                                startBlock.parentNode.removeChild(startBlock);
                            }

                            // ไฮไลต์กลับไปที่บล็อกที่เพิ่งจัดตำแหน่ง
                            const newRange = document.createRange();
                            newRange.selectNodeContents(selectedBlock);
                            selection.removeAllRanges();
                            selection.addRange(newRange);
                        } catch (err) {
                            console.warn('Splitting block fallback:', err);
                            applyAlignmentStyle(startBlock, alignType);
                        }
                    }
                    return;
                }

                // กรณี 1.2: คลุมดำข้ามหลายย่อหน้า (Multi-Block Selection)
                // จัดตำแหน่งเฉพาะย่อหน้าที่ "ถูกคลุมดำจริง ๆ" เท่านั้น
                const candidateBlocks = editor.querySelectorAll('p, div:not(.order-body-content)');
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
                return;
            }

            // =========================================================================
            // กรณีที่ 2: เคอร์เซอร์กะพริบอยู่เฉย ๆ ไม่ได้คลุมดำ (range.collapsed === true)
            // =========================================================================
            let block = getClosestBlockInEditor(range.startContainer, editor);

            // ถ้าไม่มีบล็อก <p> หรือ <div> ครอบ (เช่น เคอร์เซอร์จิ้มตรง ๆ ใน editor)
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

            // จัดตำแหน่ง "เฉพาะบล็อกย่อหน้าที่เคอร์เซอร์อยู่เพียงบล็อกเดียวเท่านั้น"
            applyAlignmentStyle(block, alignType);
        }

        // ฟังก์ชันจัดการ Shift + Enter: บังคับแถวเดิมให้ "กระจายเต็มแถว" (Full Justify) และขึ้นบรรทัดต่อ
        function handleShiftEnterJustify() {
            const selection = window.getSelection();
            if (!selection || selection.rangeCount === 0) return;
            const range = selection.getRangeAt(0);

            let editor = null;
            let checkNode = range.commonAncestorContainer;
            while (checkNode && checkNode !== document.body) {
                if (checkNode.nodeType === 1 && checkNode.classList.contains('order-body-content')) {
                    editor = checkNode;
                    break;
                }
                checkNode = checkNode.parentNode;
            }
            if (!editor) {
                editor = document.querySelector('.order-body-content[contenteditable="true"]') || document.getElementById('orderEditor');
            }
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

            // ถ้ามีการคลุมดำข้อความ ให้ลบข้อความที่คลุมดำออกก่อน
            if (!range.collapsed) {
                range.deleteContents();
            }

            // สกัดเนื้อหาหลังตำแหน่งเคอร์เซอร์
            const afterRange = document.createRange();
            afterRange.setStart(range.endContainer, range.endOffset);
            afterRange.setEnd(block, block.childNodes.length);
            const afterFragment = afterRange.extractContents();

            // กำหนดให้แถวเดิม "กระจายเต็มแถว" (Thai Distributed / Full Justify)
            // และไม่เว้นระยะห่างบรรทัด (margin-bottom: 0) เพื่อให้ระยะชิดเป็นบรรทัดเดียวกันตามมาตรฐาน ไม่เว้นระยะเหมือนกด Enter
            block.classList.add('order-soft-break');
            block.style.marginBottom = '0';
            block.style.textAlign = 'justify';
            block.style.textAlignLast = 'justify';
            block.style.textJustify = 'inter-character';
            block.style.webkitTextJustify = 'inter-character';

            // แทรก Zero-Width Space (\u200B) เพื่อให้เบราว์เซอร์กระจายคำไทยเต็มความกว้าง
            if (typeof segmentThaiElement === 'function') {
                segmentThaiElement(block);
            }

            // สร้างแถวใหม่สำหรับพิมพ์ต่อ (เป็นบรรทัดต่อเนื่อง ชิดขอบซ้าย text-indent: 0 และไม่มีระยะบน)
            const nextBlock = document.createElement('p');
            nextBlock.className = 'order-paragraph-continued';
            nextBlock.style.textIndent = '0';
            nextBlock.style.marginTop = '0';
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

            // เลื่อนเคอร์เซอร์ไปที่บรรทัดใหม่
            const newRange = document.createRange();
            if (nextBlock.firstChild) {
                newRange.setStart(nextBlock.firstChild, 0);
            } else {
                newRange.setStart(nextBlock, 0);
            }
            newRange.collapse(true);
            selection.removeAllRanges();
            selection.addRange(newRange);
        }

        // ฟังก์ชันจัดการ Enter ปกติ: ขึ้นย่อหน้าใหม่ (มี Indent 2.5 ซม. และบรรทัดท้ายชิดซ้าย)
        function handleNormalEnter(e) {
            const selection = window.getSelection();
            if (!selection || selection.rangeCount === 0) return;
            const range = selection.getRangeAt(0);

            let editor = null;
            let checkNode = range.commonAncestorContainer;
            while (checkNode && checkNode !== document.body) {
                if (checkNode.nodeType === 1 && checkNode.classList.contains('order-body-content')) {
                    editor = checkNode;
                    break;
                }
                checkNode = checkNode.parentNode;
            }
            if (!editor) return;

            const block = getClosestBlockInEditor(range.startContainer, editor);
            if (!block) return;

            e.preventDefault();

            if (!range.collapsed) {
                range.deleteContents();
            }

            // สกัดเนื้อหาหลังเคอร์เซอร์
            const afterRange = document.createRange();
            afterRange.setStart(range.endContainer, range.endOffset);
            afterRange.setEnd(block, block.childNodes.length);
            const afterFragment = afterRange.extractContents();

            // สำหรับ Enter ปกติ: สิ้นสุดย่อหน้าเดิม เว้นระยะห่างย่อหน้า (var(--paragraph-spacing))
            block.classList.remove('order-soft-break');
            block.style.marginBottom = 'var(--paragraph-spacing)';
            if (block.style.textAlignLast !== 'justify') {
                block.style.textAlignLast = 'left';
            }

            // สร้างย่อหน้าใหม่ตามระเบียบสารบรรณ (Indent 2.5 ซม. และเว้นระยะห่างย่อหน้า)
            const newP = document.createElement('p');
            newP.style.textAlign = 'justify';
            newP.style.textAlignLast = 'left';
            newP.style.textJustify = 'inter-character';
            newP.style.webkitTextJustify = 'inter-character';
            newP.style.textIndent = 'var(--indent-size)';
            newP.style.marginTop = '0';
            newP.style.marginBottom = 'var(--paragraph-spacing)';

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
        }

        // ดักจับการกดแป้นพิมพ์ Shift+Enter และ Enter ในเนื้อหาคำสั่ง
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                const selection = window.getSelection();
                if (!selection || selection.rangeCount === 0) return;
                
                let node = selection.getRangeAt(0).commonAncestorContainer;
                let insideEditor = false;
                while (node && node !== document.body) {
                    if (node.nodeType === 1 && node.classList && node.classList.contains('order-body-content')) {
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
                // คีย์ลัดแบบ Microsoft Word
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

        // =========================================================================
        // ฟังก์ชันเครื่องมือจัดรูปแบบข้อความสไตล์ Microsoft Word (Ribbon Functions)
        // =========================================================================
        const fontSizesList = [12, 13, 14, 15, 16, 18, 20, 22, 24, 29];

        function execFormat(cmd, val = null) {
            document.execCommand(cmd, false, val);
            updateToolbarStatus();
        }

        function applyFontSize(sizeStr) {
            const selection = window.getSelection();
            if (!selection || selection.rangeCount === 0) return;
            const range = selection.getRangeAt(0);

            let editor = null;
            let checkNode = range.commonAncestorContainer;
            while (checkNode && checkNode !== document.body) {
                if (checkNode.nodeType === 1 && checkNode.classList.contains('order-body-content')) {
                    editor = checkNode;
                    break;
                }
                checkNode = checkNode.parentNode;
            }
            if (!editor) editor = document.querySelector('.order-body-content[contenteditable="true"]') || document.getElementById('orderEditPaperEl');
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
        }

        function stepFontSize(delta) {
            const select = document.getElementById('fontSizeSelect');
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

        function changeParagraphIndent(delta) {
            const selection = window.getSelection();
            if (!selection || selection.rangeCount === 0) return;
            const range = selection.getRangeAt(0);
            let editor = null;
            let checkNode = range.commonAncestorContainer;
            while (checkNode && checkNode !== document.body) {
                if (checkNode.nodeType === 1 && checkNode.classList.contains('order-body-content')) {
                    editor = checkNode;
                    break;
                }
                checkNode = checkNode.parentNode;
            }
            if (!editor) editor = document.querySelector('.order-body-content[contenteditable="true"]') || document.getElementById('orderEditPaperEl');
            if (!editor) return;

            const block = getClosestBlockInEditor(range.startContainer, editor);
            if (!block) return;

            if (delta > 0) {
                block.style.textIndent = 'var(--indent-size)';
            } else {
                block.style.textIndent = '0';
            }
            updateToolbarStatus();
        }

        function toggleParagraphIndent() {
            const selection = window.getSelection();
            if (!selection || selection.rangeCount === 0) return;
            const range = selection.getRangeAt(0);
            let editor = null;
            let checkNode = range.commonAncestorContainer;
            while (checkNode && checkNode !== document.body) {
                if (checkNode.nodeType === 1 && checkNode.classList.contains('order-body-content')) {
                    editor = checkNode;
                    break;
                }
                checkNode = checkNode.parentNode;
            }
            if (!editor) editor = document.querySelector('.order-body-content[contenteditable="true"]') || document.getElementById('orderEditPaperEl');
            if (!editor) return;

            const block = getClosestBlockInEditor(range.startContainer, editor);
            if (!block) return;

            const currIndent = (block.style.textIndent || '').trim();
            if (currIndent === '0px' || currIndent === '0' || currIndent === '') {
                block.style.textIndent = 'var(--indent-size)';
            } else {
                block.style.textIndent = '0';
            }
            updateToolbarStatus();
        }

        function insertHorizontalRule() {
            const selection = window.getSelection();
            if (!selection || selection.rangeCount === 0) return;
            const range = selection.getRangeAt(0);
            const hr = document.createElement('div');
            hr.className = 'order-divider-line';
            hr.style.cssText = 'width: 100%; height: 1px; background-color: #000000; margin: 12pt 0;';
            range.insertNode(hr);
            range.setStartAfter(hr);
            range.collapse(true);
            selection.removeAllRanges();
            selection.addRange(range);
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
                                isIndented = (indent === 'var(--indent-size)' || indent === '2.5cm');
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

        function rePaginateEditPaper() {
            if (typeof SarabanOrderPaginator !== 'undefined') {
                SarabanOrderPaginator.paginate('orderEditPaperEl');
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            setTimeout(function() {
                rePaginateEditPaper();
            }, 150);
        });
    </script>

    <!-- Modal ตัวอย่างจัดหน้า A4 แยกแผ่นจริง -->
    <div class="modal fade" id="a4PreviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-dark text-white py-3">
                    <h6 class="modal-title text-white fw-bold">
                        <i class="bx bx-file me-2 text-warning"></i> ตัวอย่างจัดหน้าคำสั่ง A4 แยกแผ่นจริง (ตามมาตรฐานสากลงานสารบรรณ)
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3 p-md-4" style="background-color: #525659; overflow-x: auto;">
                    <div id="modalPreviewContainer" class="d-flex flex-column align-items-center"></div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <span class="small text-muted me-auto">
                        <i class="bx bx-info-circle me-1"></i> เว้นระยะขอบรอบกระดาษเป๊ะ: บน 1.5 ซม. / ซ้าย 3.0 ซม. / ขวา 2.0 ซม. / ล่าง 2.0 ซม.
                    </span>
                    <button type="button" class="btn btn-secondary btn-sm fw-semibold" data-bs-dismiss="modal">ปิดหน้าต่าง</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
