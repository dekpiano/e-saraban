<!DOCTYPE html>
<html lang="th" class="light-style layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>ติดตามหนังสือเลขที่ <?= esc($document['doc_no'] ?: ($document['internal_reg_no'] ?? '-')) ?> | ระบบงานสารบรรณ สกจ.</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= base_url('assets/img/favicon/favicon.ico') ?>" />

    <!-- Google Fonts: K2D & Sarabun -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=K2D:wght@200;300;400;500;600;700;800&family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

    <!-- Boxicons Icons -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />

    <!-- Sneat Core & Vendor CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/vendor/css/core.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/css/demo.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/css/saraban-memo.css?v=' . (file_exists(FCPATH . 'assets/css/saraban-memo.css') ? filemtime(FCPATH . 'assets/css/saraban-memo.css') : '2.1')) ?>">
    <?php if ($document['doc_type'] === 'order'): ?>
        <link rel="stylesheet" href="<?= base_url('assets/css/orders_saraban.css?v=' . (file_exists(FCPATH . 'assets/css/orders_saraban.css') ? filemtime(FCPATH . 'assets/css/orders_saraban.css') : '2.1')) ?>">
    <?php endif; ?>

    <style>
        body, html, input, button, select, textarea, .card-title, h1, h2, h3, h4, h5, h6, .btn, .badge, .navbar {
            font-family: 'K2D', sans-serif !important;
        }

        /* Subtle Animated Background System */
        body {
            position: relative;
            background-color: #f5f5f9;
            min-height: 100vh;
            overflow-x: hidden;
        }

        .ambient-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            pointer-events: none;
            z-index: 0;
            overflow: hidden;
        }

        .ambient-bg::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(105, 108, 255, 0.12) 1px, transparent 1px);
            background-size: 28px 28px;
            opacity: 0.6;
        }

        .floating-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.45;
            animation: floatBlob 20s ease-in-out infinite alternate;
        }

        .blob-1 {
            width: 480px;
            height: 480px;
            background: linear-gradient(135deg, rgba(105, 108, 255, 0.4), rgba(113, 221, 55, 0.2));
            top: -100px;
            right: -80px;
        }

        .blob-2 {
            width: 420px;
            height: 420px;
            background: linear-gradient(135deg, rgba(3, 195, 236, 0.35), rgba(105, 108, 255, 0.2));
            bottom: 5%;
            left: -100px;
            animation-duration: 25s;
            animation-delay: -6s;
        }

        @keyframes floatBlob {
            0% { transform: translate(0px, 0px) scale(1); }
            50% { transform: translate(30px, -40px) scale(1.05); }
            100% { transform: translate(-20px, 20px) scale(0.95); }
        }

        .main-wrapper {
            position: relative;
            z-index: 1;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.94) !important;
            backdrop-filter: blur(12px) !important;
            -webkit-backdrop-filter: blur(12px) !important;
            border: 1px solid rgba(255, 255, 255, 0.8) !important;
            box-shadow: 0 4px 20px rgba(67, 89, 113, 0.08) !important;
            border-radius: 0.85rem !important;
        }

        /* Stepper */
        .step-stepper {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin-bottom: 1.5rem;
            padding: 10px 0;
        }
        .step-stepper::before {
            content: '';
            position: absolute;
            top: 32px;
            left: 20px;
            right: 20px;
            height: 3px;
            background: #e7e7ff;
            z-index: 1;
        }
        .step-item {
            position: relative;
            z-index: 2;
            text-align: center;
            background: transparent;
            padding: 0 10px;
        }
        .step-bubble {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
            border: 2px solid transparent;
            background: #ffffff;
        }
        .step-bubble.signed {
            background-color: #71dd37;
            color: #fff;
        }
        .step-bubble.active {
            background-color: #696cff;
            color: #fff;
            animation: pulse-step 2s infinite;
        }
        .step-bubble.pending {
            background-color: #f5f5f9;
            color: #a1acb8;
            border-color: #d9dee3;
        }
        .step-bubble.returned {
            background-color: #ff3e1d;
            color: #fff;
        }
        @keyframes pulse-step {
            0% { box-shadow: 0 0 0 0 rgba(105, 108, 255, 0.6); }
            70% { box-shadow: 0 0 0 10px rgba(105, 108, 255, 0); }
            100% { box-shadow: 0 0 0 0 rgba(105, 108, 255, 0); }
        }

        .signature-preview {
            max-height: 55px;
            max-width: 140px;
            object-fit: contain;
            display: inline-block;
        }

        /* Compact Detached Navbar */
        .layout-navbar.navbar-detached.navbar-compact {
            min-height: 54px !important;
            height: 54px !important;
            padding: 0 0.85rem !important;
            margin-top: 0.65rem !important;
            margin-bottom: 0.85rem !important;
            border-radius: 12px !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 3px 12px rgba(15, 23, 42, 0.05) !important;
        }
        .navbar-compact .navbar-brand {
            padding: 0;
        }

        /* Document Canvas & Paper Desk */
        .doc-desk-canvas {
            background-color: #e2e8f0;
            background-image: radial-gradient(#cbd5e1 1px, transparent 1px);
            background-size: 20px 20px;
            padding: 1.5rem 1rem;
            border-radius: 0.75rem;
            overflow-x: auto;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 820px;
            transition: all 0.2s ease;
        }

        .doc-paper-scale-wrap {
            transform-origin: top center;
            transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Official A4 Memo Paper Display */
        .saraban-paper-display {
            background-color: #ffffff;
            width: 21cm;
            min-height: 29.7cm;
            padding: 1.5cm 2cm 2cm 3cm;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.15), 0 8px 10px -6px rgba(15, 23, 42, 0.1);
            font-family: 'TH SarabunIT๙', 'TH Sarabun IT๙', 'THSarabunIT9', 'TH Sarabun New', 'TH Sarabun PSK', 'Sarabun', sans-serif;
            font-size: 16pt;
            line-height: 1.15;
            color: #000000;
            box-sizing: border-box;
            position: relative;
            text-align: justify;
            text-justify: inter-character;
            border-radius: 2px;
        }

        .saraban-paper-display * {
            font-family: 'TH SarabunIT๙', 'TH Sarabun IT๙', 'THSarabunIT9', 'TH Sarabun New', 'TH Sarabun PSK', 'Sarabun', sans-serif;
            box-sizing: border-box;
        }

        .saraban-paper-display .header {
            position: relative;
            margin-bottom: 0;
            height: 1.5cm;
            display: flex;
            align-items: flex-end;
            justify-content: center;
        }

        .saraban-paper-display .garuda {
            position: absolute;
            left: 0;
            top: 0;
            width: 1.37cm;
            height: 1.5cm;
            object-fit: contain;
        }

        .saraban-paper-display .document-title {
            font-size: 29pt;
            font-weight: bold;
            text-align: center;
            line-height: 35pt;
            padding-top: 10px;
            width: 100%;
            color: #000000;
        }

        .saraban-paper-display .form-row {
            display: flex;
            align-items: baseline;
            margin-top: 0;
            margin-bottom: 4px;
        }

        .saraban-paper-display .label {
            font-size: 20pt;
            font-weight: bold;
            color: #000000;
            margin-right: 8px;
            white-space: nowrap;
        }

        .saraban-paper-display .editable-field {
            flex: 1;
            font-size: 16pt;
            line-height: 1.1;
            min-height: 20px;
            padding: 0 4px 0px 4px;
            border-bottom: 1px dotted #000000;
            font-weight: normal;
            color: #000000;
        }

        .saraban-paper-display .saraban-grid-2,
        .saraban-paper-display .col-2 {
            display: flex !important;
            width: 100% !important;
            max-width: 100% !important;
            flex: 1 1 100% !important;
            gap: 20px;
            margin-bottom: 4px;
        }

        .saraban-paper-display .saraban-grid-2 > div,
        .saraban-paper-display .col-2 > div {
            display: flex;
            align-items: baseline;
        }

        .saraban-paper-display .col-left {
            flex: 1;
            display: flex;
            align-items: baseline;
        }

        .saraban-paper-display .col-right {
            flex: 1;
            display: flex;
            align-items: baseline;
        }

        .saraban-paper-display .salutation {
            margin-top: 6pt;
            margin-bottom: 6pt;
            display: flex;
            align-items: baseline;
        }

        .saraban-paper-display .salutation .label {
            font-size: 16pt;
            font-weight: normal;
            margin-right: 8px;
            width: auto;
            color: #000000;
        }

        .saraban-paper-display .body-text {
            font-size: 16pt;
            line-height: 1.15;
            margin-top: 8pt;
            text-align: justify;
            text-justify: inter-character;
            -webkit-text-justify: inter-character;
            word-break: normal;
            white-space: normal;
        }

        .saraban-paper-display .body-text p {
            text-indent: 2.5cm;
            margin-bottom: 10pt;
            line-height: 1.15;
        }

        .saraban-paper-display .signature-section {
            margin-top: 48pt;
            display: flex;
            justify-content: flex-start;
            padding-left: 7.5cm;
            page-break-inside: avoid;
        }

        .saraban-paper-display .signature-box {
            transform: none;
            width: fit-content;
            min-width: 0;
            max-width: 8.5cm;
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .saraban-paper-display .signature-line {
            min-height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 4px;
        }

        .saraban-paper-display .signature-img {
            max-height: 52px;
            object-fit: contain;
        }

        .saraban-paper-display .sign-name {
            font-size: 16pt;
            font-weight: normal;
            color: #000000;
            white-space: nowrap;
        }

        .saraban-paper-display .sign-position {
            font-size: 16pt;
            color: #000000;
            white-space: nowrap;
            margin-top: 2px;
        }

        .memo-endorse-box {
            border: 1.5px solid #cbd5e1;
            background-color: #f8fafc;
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.03);
        }

        /* Order Paper Styles */
        .order-paper-display {
            background-color: #ffffff;
            width: 21cm;
            min-height: 29.7cm;
            padding: 1.5cm 2cm 2cm 3cm;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.15), 0 8px 10px -6px rgba(15, 23, 42, 0.1);
            font-family: 'TH SarabunIT๙', 'TH Sarabun New', 'Sarabun', sans-serif;
            font-size: 16pt;
            line-height: 1.25;
            color: #000000;
            box-sizing: border-box;
            position: relative;
            border-radius: 2px;
        }

        .order-garuda-center {
            text-align: center;
            margin-bottom: 8px;
        }

        .order-garuda-img {
            width: 2.73cm;
            height: 3.0cm;
            object-fit: contain;
        }

        .order-school-title {
            font-size: 16pt;
            font-weight: bold;
            text-align: center;
            margin-bottom: 4px;
        }

        .order-number-title {
            font-size: 16pt;
            font-weight: bold;
            text-align: center;
            margin-bottom: 4px;
        }

        .order-subject-title {
            font-size: 16pt;
            font-weight: bold;
            text-align: center;
            margin-bottom: 8px;
        }

        .order-divider-line {
            border-bottom: 1px solid #000;
            margin: 8px 0 14px 0;
        }

        .order-body-content {
            font-size: 16pt;
            line-height: 1.25;
            text-align: justify;
            text-justify: inter-character;
            min-height: 150px;
        }

        .order-body-content p {
            text-indent: 2.5cm;
            margin-bottom: 10pt;
        }

        .order-date-clause {
            font-size: 16pt;
            text-align: center;
            margin-top: 24pt;
            margin-bottom: 24pt;
        }

        .order-signature-container {
            margin-left: auto;
            margin-right: auto;
            width: fit-content;
            max-width: 15cm;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .order-signature-img {
            max-height: 52px;
            object-fit: contain;
            margin-bottom: 4px;
        }

        /* Print Media Styles */
        @media print {
            @page {
                size: A4 portrait;
                margin: 0;
            }
            html, body {
                background: #ffffff !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 21cm !important;
                min-height: 29.7cm !important;
            }
            .layout-navbar,
            .ambient-bg,
            .no-print,
            .alert,
            nav[aria-label="breadcrumb"],
            .col-xl-4,
            .col-lg-5,
            .btn,
            .footer,
            .step-stepper,
            .card-header,
            .nav-pills {
                display: none !important;
            }
            .main-wrapper,
            .container-xxl,
            .row,
            .col-12,
            .col-xl-8,
            .col-lg-7,
            .card,
            .card-body,
            .tab-content,
            .tab-pane,
            .doc-desk-canvas,
            .doc-paper-scale-wrap {
                display: block !important;
                margin: 0 !important;
                padding: 0 !important;
                border: none !important;
                background: transparent !important;
                box-shadow: none !important;
                width: 100% !important;
                transform: none !important;
            }
            .saraban-paper-display,
            .order-paper-display {
                box-shadow: none !important;
                width: 21cm !important;
                min-height: 29.7cm !important;
                padding: 1.5cm 2cm 2cm 3cm !important;
                margin: 0 !important;
                position: relative !important;
            }
            .saraban-paper-display .signature-section {
                margin-top: 48pt !important;
                padding-left: 7.5cm !important;
                justify-content: flex-start !important;
            }
            .saraban-paper-display .signature-box {
                transform: none !important;
                width: fit-content !important;
                min-width: 0 !important;
                max-width: 8.5cm !important;
                display: inline-flex !important;
                flex-direction: column !important;
                align-items: center !important;
                text-align: center !important;
            }
        }
    </style>
</head>

<body>

    <!-- Ambient Animated Background Layer -->
    <div class="ambient-bg" aria-hidden="true">
        <div class="floating-blob blob-1"></div>
        <div class="floating-blob blob-2"></div>
    </div>

    <div class="main-wrapper">

        <!-- Detached Sneat Navbar (Compact) -->
        <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme glass-card navbar-compact no-print" id="layout-navbar">
            <div class="container-fluid px-1 px-sm-2 d-flex align-items-center justify-content-between">
                <a class="navbar-brand d-flex align-items-center m-0" href="<?= base_url('/') ?>">
                    <span class="badge bg-primary rounded-3 shadow-sm d-flex align-items-center justify-content-center me-2" style="width: 36px; height: 36px; min-width: 36px;">
                        <i class="bx bxs-institution fs-4 text-white"></i>
                    </span>
                    <div class="lh-1">
                        <span class="fw-bolder text-primary" style="font-size: 1.02rem; letter-spacing: 0.3px;">ระบบงานสารบรรณ สกจ.</span>
                        <small class="d-none d-sm-block text-secondary fw-semibold" style="font-size: 0.68rem; margin-top: 2px;">โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์</small>
                    </div>
                </a>

                <div class="d-flex align-items-center gap-1.5 ms-auto">
                    <a href="<?= base_url('/') ?>" class="btn btn-outline-secondary btn-sm px-2.5 py-1 d-inline-flex align-items-center" style="font-size: 0.82rem; height: 34px;">
                        <i class="bx bx-arrow-back me-1"></i> <span class="d-none d-sm-inline">กลับหน้าค้นหา</span><span class="d-inline d-sm-none">กลับ</span>
                    </a>
                    <?php if ($isLoggedIn): ?>
                        <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-primary btn-sm px-2.5 py-1 shadow-sm fw-semibold d-inline-flex align-items-center" style="font-size: 0.82rem; height: 34px;">
                            <i class="bx bx-grid-alt me-1"></i> <span>แดชบอร์ด</span>
                        </a>
                    <?php else: ?>
                        <a href="<?= base_url('login') ?>" class="btn btn-primary btn-sm px-2.5 py-1 shadow-sm fw-semibold d-inline-flex align-items-center" style="font-size: 0.82rem; height: 34px;">
                            <i class="bx bx-log-in me-1"></i> <span class="d-none d-sm-inline">เข้าสู่ระบบ (สำหรับบุคลากร)</span><span class="d-inline d-sm-none">เข้าสู่ระบบ</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>

        <div class="container-xxl py-2">

            <!-- Readonly Notice (High Contrast) -->
            <div class="alert d-flex align-items-center justify-content-between mb-4 shadow-sm no-print" role="alert" style="background: #fffbe6; border: 1.5px solid #ffe58f; color: #614700; border-radius: 0.75rem;">
                <div class="d-flex align-items-center">
                    <i class="bx bxs-shield-quarter fs-3 me-2" style="color: #d48806;"></i>
                    <div class="small fw-medium">
                        <strong class="fw-bold" style="color: #382400;">โหมดติดตามสถานะสำหรับผู้ใช้ทั่วไป (Read-Only):</strong>
                        หน้านี้แสดงต้นฉบับเอกสารราชการ เส้นทางการเดินเรื่อง และสถานะการพิจารณา หากท่านเป็นผู้มีหน้าที่ลงนามในเอกสารนี้ กรุณา <a href="<?= base_url('login') ?>" class="alert-link text-decoration-underline fw-bold" style="color: #002c8a;">เข้าสู่ระบบ</a> เพื่อทำการลงนามหรือเซ็นเกษียน
                    </div>
                </div>
                <span class="badge px-3 py-2 fw-bold d-none d-md-inline-block shadow-sm" style="background: #faad14; color: #000000; font-size: 0.75rem;">
                    <i class="bx bx-lock-alt me-1"></i> สิทธิ์ดูอย่างเดียว
                </span>
            </div>

            <!-- Header -->
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 no-print gap-2">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-1">
                            <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">หน้าแรกติดตามเอกสาร</a></li>
                            <li class="breadcrumb-item active">รายละเอียดการติดตาม</li>
                        </ol>
                    </nav>
                    <h4 class="fw-bold mb-0 text-primary d-flex align-items-center flex-wrap gap-2">
                        <?= doc_type_badge($document['doc_type']) ?>
                        <span><?= esc($document['doc_no'] ?: ($document['internal_reg_no'] ?? '-')) ?>: <?= esc($document['title']) ?></span>
                    </h4>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    <button type="button" class="btn btn-outline-primary btn-sm fw-semibold shadow-sm" onclick="window.print()">
                        <i class="bx bx-printer me-1"></i> พิมพ์เอกสาร (A4)
                    </button>
                    <?php if (!empty($document['file_path'])): ?>
                        <a href="<?= document_url($document['file_path']) ?>" target="_blank" class="btn btn-outline-danger btn-sm fw-semibold shadow-sm">
                            <i class="bx bxs-file-pdf me-1"></i> เปิดดูไฟล์ PDF
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Visual Stepper Workflow -->
            <div class="card glass-card mb-4 border-0 no-print">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="text-muted fw-bold text-uppercase mb-0" style="font-size: 0.8rem; letter-spacing: 0.5px;">
                            <i class="bx bx-git-commit me-1 text-primary"></i> ลำดับขั้นตอนการเสนอแฟ้มและลงนามดิจิทัล (Workflow Progress)
                        </h6>
                        <div>
                            <?= doc_status_badge($document['status']) ?>
                        </div>
                    </div>
                    <div class="step-stepper">
                        <?php foreach ($routes as $r): ?>
                            <?php
                                $isDone    = $r['status'] === 'signed';
                                $isCurrent = ($r['step_order'] == $document['current_step'] && $document['status'] === 'routing');
                                $isReturn  = $r['status'] === 'returned';
                                $bubbleClass = $isDone ? 'signed' : ($isReturn ? 'returned' : ($isCurrent ? 'active' : 'pending'));
                            ?>
                            <div class="step-item flex-fill">
                                <div class="step-bubble <?= $bubbleClass ?>">
                                    <?php if ($isDone): ?>
                                        <i class="bx bx-check fs-3"></i>
                                    <?php elseif ($isReturn): ?>
                                        <i class="bx bx-x fs-3"></i>
                                    <?php elseif ($isCurrent): ?>
                                        <i class="bx bx-edit fs-4"></i>
                                    <?php else: ?>
                                        <?= $r['step_order'] ?>
                                    <?php endif; ?>
                                </div>
                                <div class="fw-bold text-dark small mb-0">
                                    <?= esc($r['prefix'] . $r['first_name'] . ' ' . $r['last_name']) ?>
                                </div>
                                <div class="text-muted" style="font-size: 0.75rem;">
                                    <?= esc($r['position_name']) ?>
                                </div>
                                <div class="mt-1">
                                    <?php if ($isDone): ?>
                                        <span class="badge bg-success text-white" style="font-size: 0.7rem;">ลงนามแล้ว</span>
                                    <?php elseif ($isReturn): ?>
                                        <span class="badge bg-danger text-white" style="font-size: 0.7rem;">ส่งคืนแก้ไข</span>
                                    <?php elseif ($isCurrent): ?>
                                        <span class="badge bg-primary text-white" style="font-size: 0.7rem;">กำลังพิจารณา</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary text-white" style="font-size: 0.7rem;">รอดำเนินการ</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Main Layout: Left Reader (Document Display) + Right Information Dock -->
            <div class="row">
                <!-- Left Column: Official Document Reader (8 cols on desktop) -->
                <div class="col-12 col-xl-8 col-lg-7 mb-4">
                    <div class="card border-0 shadow-sm glass-card">
                        <!-- Reader Toolbar -->
                        <div class="card-header bg-white py-2.5 px-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2 no-print">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-primary p-2 rounded-3 text-white">
                                    <i class="bx bx-file-blank fs-4"></i>
                                </span>
                                <div>
                                    <h6 class="mb-0 fw-bold text-dark">
                                        <?php if ($document['doc_type'] === 'internal'): ?>
                                            ต้นฉบับบันทึกข้อความ (ขนาด A4 ทางการ)
                                        <?php elseif ($document['doc_type'] === 'order'): ?>
                                            ต้นฉบับคำสั่งโรงเรียน (ขนาด A4 ทางการ)
                                        <?php else: ?>
                                            ต้นฉบับเอกสารราชการ
                                        <?php endif; ?>
                                    </h6>
                                    <small class="text-muted">ความกว้าง ๒๑ ซม. x ๒๙.๗ ซม. ตามระเบียบสำนักนายกรัฐมนตรีว่าด้วยงานสารบรรณ</small>
                                </div>
                            </div>

                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <?php if (!empty($document['file_path'])): ?>
                                    <ul class="nav nav-pills" role="tablist">
                                        <li class="nav-item">
                                            <button type="button" class="nav-link active btn-sm py-1 px-2.5" role="tab" data-bs-toggle="tab" data-bs-target="#tab-reader-paper">
                                                <i class="bx bx-file me-1"></i> ข้อความในระบบ
                                            </button>
                                        </li>
                                        <li class="nav-item">
                                            <button type="button" class="nav-link btn-sm py-1 px-2.5" role="tab" data-bs-toggle="tab" data-bs-target="#tab-reader-pdf">
                                                <i class="bx bxs-file-pdf me-1 text-danger"></i> ไฟล์แนบ PDF
                                            </button>
                                        </li>
                                    </ul>
                                <?php endif; ?>

                                <!-- Zoom Controls -->
                                <div class="d-flex align-items-center gap-1">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" class="btn btn-outline-secondary py-1 px-2" onclick="setDocZoom(0.75)">75%</button>
                                        <button type="button" class="btn btn-outline-secondary py-1 px-2 active" id="btnZoom100" onclick="setDocZoom(1.0)">100%</button>
                                        <button type="button" class="btn btn-outline-secondary py-1 px-2" title="พอดีหน้าจอ" onclick="fitDocToWidth()"><i class="bx bx-fullscreen"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Reader Canvas -->
                        <div class="card-body p-0">
                            <div class="tab-content p-0">
                                <!-- Tab 1: Official Paper Display -->
                                <div class="tab-pane fade show active" id="tab-reader-paper" role="tabpanel">
                                    <div class="doc-desk-canvas" id="docDeskCanvas">
                                        <div class="doc-paper-scale-wrap" id="docPaperScaleWrap">
                                            <?php if ($document['doc_type'] === 'order'): ?>
                                                <!-- Official School Order Paper -->
                                                <div class="order-paper-display shadow" id="printableDocPaper">
                                                    <div class="order-garuda-center">
                                                        <img src="<?= base_url('assets/img/krut_thai.png') ?>" alt="ตราครุฑ" class="order-garuda-img">
                                                    </div>
                                                    <div class="order-school-title">คำสั่งโรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์</div>
                                                    <div class="order-number-title">
                                                        ที่ <?= esc($document['doc_no'] ?: ($document['internal_reg_no'] ?? '-')) ?>
                                                    </div>
                                                    <div class="order-subject-title">
                                                        เรื่อง <?= esc($document['title']) ?>
                                                    </div>
                                                    <div class="order-divider-line"></div>
                                                    <div class="order-body-content">
                                                        <?= !empty($document['content']) ? $document['content'] : '<p class="text-muted text-center py-4">(ไม่พบเนื้อหาคำสั่งในระบบ)</p>' ?>
                                                    </div>
                                                    <div class="order-date-clause">
                                                        สั่ง ณ วันที่ <?= thai_date($document['doc_date'], 'medium') ?>
                                                    </div>
                                                    <div class="order-signature-container">
                                                        <?php 
                                                            $effectiveSignImg = !empty($document['creator_signature']) ? $document['creator_signature'] : (!empty($document['user_signature']) ? $document['user_signature'] : '');
                                                        ?>
                                                        <?php if (!empty($effectiveSignImg)): ?>
                                                            <img src="<?= base_url($effectiveSignImg) ?>" alt="ลายเซ็น" class="order-signature-img">
                                                        <?php endif; ?>
                                                        <div class="fw-bold" style="font-size: 16pt;">
                                                            ( <?= esc($document['signer_name'] ?: 'นายพงษ์ศักดิ์ เงินสันเทียะ') ?> )
                                                        </div>
                                                        <div style="font-size: 16pt;">
                                                            <?= esc($document['signer_position'] ?: 'ผู้อำนวยการโรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์') ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <!-- Official Saraban Memo Paper (บันทึกข้อความ) -->
                                                <div class="saraban-paper-display shadow" id="printableDocPaper">
                                                    <div class="header">
                                                        <img src="<?= base_url('assets/img/krut_thai.png') ?>" alt="ตราครุฑ" class="garuda">
                                                        <div class="document-title">บันทึกข้อความ</div>
                                                    </div>

                                                    <div class="form-row">
                                                        <span class="label">ส่วนราชการ</span>
                                                        <span class="editable-field"><?= esc($document['from_org'] ?: ($document['memo_department'] ?? 'โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์')) ?><?= !empty($document['memo_contact']) ? ' โทร. ' . esc($document['memo_contact']) : '' ?></span>
                                                    </div>

                                                    <div class="saraban-grid-2">
                                                        <div class="col-left">
                                                            <span class="label">ที่</span>
                                                            <span class="editable-field"><?= esc($document['doc_no'] ?: ($document['internal_reg_no'] ?? '-')) ?></span>
                                                        </div>
                                                        <div class="col-right">
                                                            <span class="label">วันที่</span>
                                                            <span class="editable-field"><?= thai_date($document['doc_date'], 'medium') ?></span>
                                                        </div>
                                                    </div>

                                                    <div class="form-row subject-row">
                                                        <span class="label">เรื่อง</span>
                                                        <span class="editable-field fw-bold"><?= esc($document['title']) ?></span>
                                                    </div>

                                                    <div class="salutation">
                                                        <span class="label">เรียน</span>
                                                        <span class="editable-field" style="border-bottom: none;"><?= esc($document['to_org'] ?: 'ผู้อำนวยการโรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์') ?></span>
                                                    </div>

                                                    <!-- Document Body Content -->
                                                    <div class="body-text">
                                                        <?= !empty($document['content']) ? $document['content'] : '<p class="text-muted text-center py-4">(ไม่พบเนื้อหาข้อความในระบบ)</p>' ?>
                                                    </div>

                                                    <!-- Creator Signature Block -->
                                                    <div class="signature-section">
                                                        <div class="signature-box">
                                                            <div class="signature-line">
                                                                <?php 
                                                                    $creatorSign = !empty($document['creator_signature']) ? $document['creator_signature'] : (!empty($document['user_signature']) ? $document['user_signature'] : '');
                                                                ?>
                                                                <?php if (!empty($creatorSign)): ?>
                                                                    <img src="<?= base_url($creatorSign) ?>" alt="ลายเซ็น" class="signature-img">
                                                                <?php endif; ?>
                                                            </div>
                                                            <div class="sign-name">
                                                                ( <?= esc(($document['prefix'] ?? '') . ($document['first_name'] ?? '') . ' ' . ($document['last_name'] ?? '')) ?> )
                                                            </div>
                                                            <div class="sign-position">
                                                                ตำแหน่ง <?= esc($document['creator_position'] ?? ($document['creator_department'] ?? 'บุคลากรทางการศึกษา')) ?>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Digital Endorsements Section: Realistic Blue Rubber Stamps -->
                                                    <?php 
                                                    $signedRoutes = array_filter($routes, function($r) {
                                                        return $r['status'] === 'signed';
                                                    });
                                                    $defaultPositions = [
                                                        1 => ['x' => '8%',  'y' => '68%', 'rot' => '-1.5deg'],
                                                        2 => ['x' => '52%', 'y' => '68%', 'rot' => '1.2deg'],
                                                        3 => ['x' => '8%',  'y' => '82%', 'rot' => '-2.0deg'],
                                                        4 => ['x' => '52%', 'y' => '82%', 'rot' => '1.5deg'],
                                                    ];
                                                    ?>
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
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tab 2: Attached PDF Viewer -->
                                <?php if (!empty($document['file_path'])): ?>
                                    <div class="tab-pane fade" id="tab-reader-pdf" role="tabpanel">
                                        <div class="bg-dark p-2 text-center text-white small d-flex justify-content-between align-items-center">
                                            <span><i class="bx bxs-file-pdf text-danger me-1"></i> ไฟล์เอกสารแนบต้นฉบับ: <?= esc(basename($document['file_path'])) ?></span>
                                            <a href="<?= document_url($document['file_path']) ?>" target="_blank" class="btn btn-sm btn-light text-dark py-0.5 px-2">
                                                <i class="bx bx-download me-1"></i> เปิดดูเต็มจอ
                                            </a>
                                        </div>
                                        <iframe src="<?= document_url($document['file_path']) ?>#toolbar=1" width="100%" height="820px" style="border: none;" title="PDF Preview"></iframe>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Document Details, Signatures & Audit Logs (4 cols on desktop) -->
                <div class="col-12 col-xl-4 col-lg-5 mb-4 no-print">
                    <!-- Card 1: Document Metadata -->
                    <div class="card glass-card border-0 mb-4 shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center bg-transparent border-bottom py-3">
                            <h6 class="card-title mb-0 fw-bold text-dark">
                                <i class="bx bx-info-circle text-primary me-2"></i> ข้อมูลหนังสือราชการ
                            </h6>
                            <div>
                                <?= urgency_badge($document['urgency']) ?>
                            </div>
                        </div>
                        <div class="card-body pt-3">
                            <div class="row g-2.5">
                                <div class="col-6">
                                    <small class="text-muted d-block">เลขทะเบียนภายใน รร.</small>
                                    <strong class="text-dark font-monospace"><?= esc($document['internal_reg_no'] ?: '-') ?></strong>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">เลขที่หนังสือ</small>
                                    <strong class="text-dark"><?= esc($document['doc_no'] ?: '-') ?></strong>
                                </div>
                                <div class="col-12">
                                    <small class="text-muted d-block">ชื่อเรื่อง</small>
                                    <div class="fw-bold text-dark"><?= esc($document['title']) ?></div>
                                </div>
                                <div class="col-12">
                                    <small class="text-muted d-block">จาก (ต้นเรื่อง)</small>
                                    <div class="text-dark"><?= esc($document['from_org']) ?></div>
                                </div>
                                <div class="col-12">
                                    <small class="text-muted d-block">ถึง (ปลายทาง)</small>
                                    <div class="text-dark"><?= esc($document['to_org']) ?></div>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">ลงวันที่</small>
                                    <div class="text-dark"><?= thai_date($document['doc_date'], 'medium') ?></div>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">บันทึกเข้าระบบ</small>
                                    <div class="text-dark"><?= thai_date($document['created_at'], 'short', true) ?></div>
                                </div>
                                <div class="col-12">
                                    <small class="text-muted d-block">ผู้สร้างเอกสาร</small>
                                    <div class="text-dark">
                                        <i class="bx bx-user me-1 text-primary"></i>
                                        <?= esc(($document['prefix'] ?? '') . $document['first_name'] . ' ' . $document['last_name']) ?>
                                        <span class="text-muted small">(<?= esc($document['creator_department'] ?? 'สารบรรณ') ?>)</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: Signatures Timeline & Endorsements -->
                    <div class="card glass-card border-0 mb-4 shadow-sm">
                        <div class="card-header bg-transparent border-bottom py-3">
                            <h6 class="card-title mb-0 fw-bold text-dark">
                                <i class="bx bx-edit text-primary me-2"></i> บันทึกข้อคิดเห็นและผลการเกษียน
                            </h6>
                        </div>
                        <div class="card-body pt-3">
                            <?php foreach ($routes as $r): ?>
                                <div class="border rounded p-3 mb-3 <?= $r['status'] === 'signed' ? 'bg-white border-success shadow-sm' : 'bg-light' ?>">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div>
                                            <span class="badge bg-primary text-white me-1">ขั้นที่ <?= $r['step_order'] ?></span>
                                            <strong class="text-dark"><?= esc($r['prefix'] . $r['first_name'] . ' ' . $r['last_name']) ?></strong>
                                            <small class="text-muted d-block"><?= esc($r['position_name']) ?></small>
                                        </div>
                                        <div>
                                            <?php if ($r['status'] === 'signed'): ?>
                                                <span class="badge bg-success text-white"><i class="bx bx-check me-1"></i> ลงนามแล้ว</span>
                                            <?php elseif ($r['status'] === 'returned'): ?>
                                                <span class="badge bg-danger text-white"><i class="bx bx-x me-1"></i> ส่งคืนแก้ไข</span>
                                            <?php elseif ($r['step_order'] == $document['current_step'] && $document['status'] === 'routing'): ?>
                                                <span class="badge bg-primary text-white"><i class="bx bx-time me-1"></i> รอลงนาม</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary text-white">รอดำเนินการ</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <?php if ($r['status'] === 'signed'): ?>
                                        <div class="bg-light p-2 rounded mb-2">
                                            <?php if (!empty($r['comment_preset'])): ?>
                                                <div class="fw-bold text-primary small"><i class="bx bx-check text-success me-1"></i> <?= esc($r['comment_preset']) ?></div>
                                            <?php endif; ?>
                                            <?php if (!empty($r['comment_text'])): ?>
                                                <div class="text-dark small fst-italic">"<?= esc($r['comment_text']) ?>"</div>
                                            <?php endif; ?>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-end mt-2 pt-2 border-top">
                                            <div>
                                                <?php if (!empty($r['signature_img'])): ?>
                                                    <img src="<?= base_url($r['signature_img']) ?>" alt="ลายเซ็นดิจิทัล" class="signature-preview" />
                                                <?php endif; ?>
                                            </div>
                                            <div class="text-end small text-muted">
                                                <div><?= thai_date($r['signed_at'], 'short', true) ?></div>
                                                <?php if (!empty($r['verify_hash'])): ?>
                                                    <a href="<?= base_url('verify/' . $r['verify_hash']) ?>" target="_blank" class="text-primary text-decoration-none fw-semibold" style="font-size: 0.72rem;">
                                                        <i class="bx bx-check-shield text-success me-1"></i> ตรวจสอบลายมือชื่อ
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php elseif ($r['status'] === 'returned'): ?>
                                        <div class="alert alert-danger py-2 mb-0 small">
                                            <strong>เหตุผลที่ส่งคืน:</strong> <?= esc($r['comment_text'] ?: 'ไม่มีการระบุเหตุผล') ?>
                                            <div class="mt-1 text-muted">ส่งคืนเมื่อ: <?= thai_date($r['signed_at'], 'short', true) ?></div>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-muted small py-1">
                                            <i class="bx bx-hourglass me-1"></i> รอดำเนินการตามลำดับขั้นตอนการเสนอเรื่อง
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Card 3: Audit Logs -->
                    <div class="card glass-card border-0 shadow-sm">
                        <div class="card-header bg-transparent border-bottom py-3">
                            <h6 class="card-title mb-0 fw-bold text-dark">
                                <i class="bx bx-history text-primary me-2"></i> ประวัติการดำเนินการ (Audit Logs)
                            </h6>
                        </div>
                        <div class="card-body pt-3">
                            <div class="bg-light p-3 rounded" style="max-height: 220px; overflow-y: auto;">
                                <ul class="list-unstyled mb-0 small">
                                    <?php if (empty($logs)): ?>
                                        <li class="text-muted">ไม่มีประวัติการดำเนินการ</li>
                                    <?php else: ?>
                                        <?php foreach ($logs as $log): ?>
                                            <li class="mb-2 pb-2 border-bottom border-light">
                                                <span class="text-muted">[<?= thai_date($log['created_at'], 'short', true) ?>]</span>
                                                <strong class="text-dark"><?= esc(($log['prefix'] ?? '') . $log['first_name'] . ' ' . $log['last_name']) ?>:</strong>
                                                <span class="badge bg-secondary text-white ms-1"><?= esc($log['action']) ?></span>
                                                <div class="text-secondary ps-2"><?= esc($log['detail'] ?: '-') ?></div>
                                            </li>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Footer -->
        <footer class="footer bg-white border-top py-4 mt-4 glass-card no-print">
            <div class="container-xxl text-center text-muted small">
                <div>
                    © <?= date('Y') + 543 ?> <strong>ระบบงานสารบรรณ สกจ. (e-Saraban SKJ)</strong>
                </div>
                <div class="mt-1">
                    ระบบเปิดให้ประชาชนและบุคลากรติดตามการดำเนินงานอย่างโปร่งใส
                </div>
            </div>
        </footer>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentDocScale = 1.0;

        function setDocZoom(scale) {
            currentDocScale = scale;
            const wrap = document.getElementById('docPaperScaleWrap');
            if (wrap) {
                wrap.style.transform = `scale(${scale})`;
            }
            // Update active state in zoom buttons
            const zoomBtns = document.querySelectorAll('.btn-group button');
            zoomBtns.forEach(btn => btn.classList.remove('active'));
            if (scale === 1.0) {
                const btn100 = document.getElementById('btnZoom100');
                if (btn100) btn100.classList.add('active');
            }
        }

        function fitDocToWidth() {
            const canvas = document.getElementById('docDeskCanvas');
            const paper = document.getElementById('printableDocPaper');
            if (!canvas || !paper) return;

            const canvasWidth = canvas.clientWidth - 40;
            const paperWidth = paper.offsetWidth || 794; // approx A4 width in px (21cm at 96dpi)

            if (canvasWidth < paperWidth) {
                const calculatedScale = Math.max(0.45, Math.min(1.0, (canvasWidth / paperWidth)));
                setDocZoom(calculatedScale);
            } else {
                setDocZoom(1.0);
            }
        }

        // Auto-scale on small screens on load
        window.addEventListener('DOMContentLoaded', () => {
            if (window.innerWidth < 992) {
                fitDocToWidth();
            }
        });

        window.addEventListener('resize', () => {
            if (window.innerWidth < 992 && currentDocScale < 1.0) {
                fitDocToWidth();
            }
        });
    </script>
</body>
</html>
