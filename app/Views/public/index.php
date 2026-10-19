<!DOCTYPE html>
<html lang="th" class="light-style layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>ระบบงานสารบรรณ สกจ. | ติดตามหนังสือราชการและสถานะงาน</title>

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

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" />

    <style>
        /* Base Typography */
        body, html, input, button, select, textarea, .card-title, h1, h2, h3, h4, h5, h6, .form-control, .form-select, .btn, .badge, .navbar, .modal-title {
            font-family: 'K2D', sans-serif !important;
        }

        /* -------------------------------------------------------------
           SUBTLE ANIMATED BACKGROUND SYSTEM (อนิเมชั่นพื้นหลังพรีเมียม)
        ------------------------------------------------------------- */
        body {
            position: relative;
            background-color: #f5f5f9;
            min-height: 100vh;
            overflow-x: hidden;
            color: #1e293b;
        }

        /* Ambient Animated Mesh Background */
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

        /* Tech Dot Grid Pattern */
        .ambient-bg::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(105, 108, 255, 0.15) 1px, transparent 1px);
            background-size: 28px 28px;
            opacity: 0.5;
        }

        /* Floating Animated Luminous Blobs */
        .floating-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(85px);
            opacity: 0.35;
            animation: floatBlob 18s ease-in-out infinite alternate;
        }

        .blob-1 {
            width: 450px;
            height: 450px;
            background: linear-gradient(135deg, rgba(105, 108, 255, 0.45), rgba(113, 221, 55, 0.2));
            top: -120px;
            right: -80px;
            animation-duration: 22s;
        }

        .blob-2 {
            width: 400px;
            height: 400px;
            background: linear-gradient(135deg, rgba(3, 195, 236, 0.35), rgba(105, 108, 255, 0.25));
            bottom: 5%;
            left: -100px;
            animation-duration: 26s;
            animation-delay: -5s;
        }

        .blob-3 {
            width: 300px;
            height: 300px;
            background: linear-gradient(135deg, rgba(255, 171, 0, 0.25), rgba(255, 62, 29, 0.15));
            top: 40%;
            right: 25%;
            animation-duration: 20s;
            animation-delay: -9s;
        }

        @keyframes floatBlob {
            0% { transform: translate(0px, 0px) scale(1) rotate(0deg); }
            50% { transform: translate(35px, -45px) scale(1.06) rotate(30deg); }
            100% { transform: translate(-25px, 25px) scale(0.95) rotate(-20deg); }
        }

        /* Container Layer over ambient background */
        .main-wrapper {
            position: relative;
            z-index: 1;
        }

        /* -------------------------------------------------------------
           ULTRA HIGH-CONTRAST SNEAT GLASS & CARD STYLES
        ------------------------------------------------------------- */
        .glass-card {
            background: #ffffff !important;
            border: 1.5px solid #e2e8f0 !important;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.06) !important;
            border-radius: 0.85rem !important;
        }

        /* Sneat Hero Card with High-Contrast Typography */
        .hero-sneat-card {
            background: linear-gradient(135deg, #4f46e5 0%, #4338ca 50%, #312e81 100%) !important;
            color: #ffffff !important;
            border-radius: 1rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(67, 56, 202, 0.25);
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        .hero-sneat-card::before {
            content: '';
            position: absolute;
            width: 320px;
            height: 320px;
            background: radial-gradient(circle, rgba(255,255,255,0.18) 0%, rgba(255,255,255,0) 70%);
            top: -120px;
            right: 80px;
            border-radius: 50%;
        }

        .stat-sneat-card {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            border: 1px solid #e2e8f0 !important;
            background: #ffffff !important;
            border-radius: 10px !important;
        }

        .stat-sneat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(15, 23, 42, 0.08) !important;
            border-color: #cbd5e1 !important;
        }

        /* High-Contrast Nav Pills */
        .custom-nav-pill {
            background: #ffffff !important;
            color: #1e293b !important;
            font-weight: 600 !important;
            border: 1.5px solid #cbd5e1 !important;
            border-radius: 0.5rem !important;
            padding: 8px 16px !important;
            transition: all 0.2s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.03);
        }

        .custom-nav-pill:hover {
            background: #f8fafc !important;
            color: #4338ca !important;
            border-color: #94a3b8 !important;
        }

        .custom-nav-pill.active {
            background: #4f46e5 !important;
            color: #ffffff !important;
            border-color: #4f46e5 !important;
            font-weight: 700 !important;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.35) !important;
        }

        /* Stepper Modal Component (Modern Card Layout with High-Contrast Colors) */
        .step-stepper-box {
            background: #ffffff;
            border: 2px solid #cbd5e1;
            border-radius: 1rem;
            padding: 1.25rem 1.25rem 1.25rem 1.25rem;
            box-shadow: 0 4px 15px rgba(15, 23, 42, 0.05);
            position: relative;
            overflow: hidden;
        }

        .step-stepper-modal-wrapper {
            width: 100%;
            overflow-x: auto;
            overflow-y: hidden;
            padding-bottom: 8px;
            /* Thin modern scrollbar */
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
        }

        .step-stepper-modal-wrapper::-webkit-scrollbar {
            height: 6px;
        }

        .step-stepper-modal-wrapper::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 4px;
        }

        .step-stepper-modal {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            position: relative;
            margin: 0;
            padding: 10px 10px 15px 10px;
            min-width: 650px; /* ป้องกันการบีบอัดจนเละเมื่อหน้าจอเล็กลง */
        }

        /* เส้นเชื่อมระหว่างขั้นตอน - วางอยู่กึ่งกลาง Bubble พอดี */
        .step-stepper-modal::before {
            content: '';
            position: absolute;
            top: 33px; /* กึ่งกลางของ bubble 48px + padding 10px = 34px */
            left: 5%;
            right: 5%;
            height: 4px;
            background: #cbd5e1;
            z-index: 1;
            border-radius: 4px;
        }

        .step-stepper-modal.return-flow::before {
            background: #86efac;
        }

        .step-item-modal {
            position: relative;
            z-index: 2;
            text-align: center;
            background: transparent;
            padding: 0 8px;
            flex: 1 1 0px;
            min-width: 100px;
            max-width: 220px;
        }

        .step-bubble-modal {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1.15rem;
            margin-bottom: 0.65rem;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.15);
            border: 3px solid #ffffff;
            transition: all 0.25s ease;
            position: relative;
            background: #ffffff;
        }

        /* สำเร็จแล้ว: เขียวเข้มเด่นชัด คอนทราสต์สูงสุด */
        .step-bubble-modal.signed {
            background-color: #15803d;
            color: #ffffff !important;
            box-shadow: 0 4px 14px rgba(21, 128, 61, 0.4);
        }

        /* กำลังดำเนินการ (ขาไป): น้ำเงิน Indigo เข้ม พร้อมแสงกระพริบ */
        .step-bubble-modal.active {
            background-color: #4338ca;
            color: #ffffff !important;
            box-shadow: 0 4px 16px rgba(67, 56, 202, 0.45);
            animation: pulse-step 2s infinite;
        }

        /* กำลังดำเนินการ (ขากลับ): สีส้มอำพันเด่นชัด */
        .step-bubble-modal.return-active {
            background-color: #d97706;
            color: #ffffff !important;
            box-shadow: 0 4px 16px rgba(217, 119, 6, 0.5);
            animation: pulse-return 2s infinite;
        }

        /* รอดำเนินการ: สีเทากระจ่าง ชัดเจนไม่กลืน */
        .step-bubble-modal.pending {
            background-color: #e2e8f0;
            color: #334155 !important;
            border-color: #ffffff;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        }

        /* ถูกส่งคืน */
        .step-bubble-modal.returned {
            background-color: #dc2626;
            color: #ffffff !important;
            box-shadow: 0 4px 14px rgba(220, 38, 38, 0.4);
        }

        @keyframes pulse-step {
            0% { box-shadow: 0 0 0 0 rgba(67, 56, 202, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(67, 56, 202, 0); }
            100% { box-shadow: 0 0 0 0 rgba(67, 56, 202, 0); }
        }

        @keyframes pulse-return {
            0% { box-shadow: 0 0 0 0 rgba(217, 119, 6, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(217, 119, 6, 0); }
            100% { box-shadow: 0 0 0 0 rgba(217, 119, 6, 0); }
        }

        .step-title-text {
            color: #0f172a !important;
            font-size: 0.82rem !important;
            font-weight: 800 !important;
            line-height: 1.35;
            word-break: break-word;
            overflow-wrap: break-word;
        }

        .step-desc-text {
            color: #334155 !important;
            font-size: 0.76rem !important;
            font-weight: 600 !important;
            line-height: 1.3;
            word-break: break-word;
        }

        .step-user-badge {
            display: inline-block;
            background: #ffffff;
            border: 1.5px solid #cbd5e1;
            color: #0f172a;
            font-weight: 700;
            border-radius: 6px;
            padding: 2px 6px;
            font-size: 0.72rem;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        .current-handler-pill {
            background: #eef2ff;
            border: 1.5px solid #a5b4fc;
            color: #312e81;
            border-radius: 6px;
            padding: 4px 10px;
            font-size: 0.82rem;
            font-weight: 700;
            display: inline-block;
        }

        /* -------------------------------------------------------------
           MOBILE FIRST & TOUCH ENHANCEMENTS (UI/UX Master Designer)
        ------------------------------------------------------------- */
        .scrollbar-none::-webkit-scrollbar {
            display: none;
        }
        .scrollbar-none {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .doc-mobile-card {
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 0.85rem;
            transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
        }

        .doc-mobile-card:active {
            transform: scale(0.985);
            background-color: #f8fafc;
        }

        @media (max-width: 767.98px) {
            .hero-sneat-card h2 {
                font-size: 1.08rem !important;
                line-height: 1.3 !important;
            }
            .custom-nav-pill {
                padding: 6px 14px !important;
                font-size: 0.82rem !important;
            }
            .layout-navbar {
                padding-left: 0.75rem !important;
                padding-right: 0.75rem !important;
                margin-top: 0.5rem !important;
                margin-bottom: 0.75rem !important;
            }
            .modal-dialog {
                margin: 0.5rem;
            }
            .step-stepper-box {
                padding: 0.85rem !important;
            }
        }

        /* -------------------------------------------------------------
           COMPACT DETACHED NAVBAR DESIGN
        ------------------------------------------------------------- */
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
        .navbar-compact .nav-link {
            font-size: 0.84rem !important;
            padding: 0.35rem 0.65rem !important;
            border-radius: 6px !important;
            transition: all 0.15s ease-in-out;
            color: #334155 !important;
        }
        .navbar-compact .nav-link:hover {
            background-color: #f1f5f9 !important;
            color: #4f46e5 !important;
        }
        .navbar-compact .nav-link.active {
            background-color: #eef2ff !important;
            color: #4338ca !important;
            font-weight: 700 !important;
        }

        /* -------------------------------------------------------------
           PREMIUM SNEAT MODAL DESIGN SYSTEM (PUBLIC WORKFLOW TRACKER)
        ------------------------------------------------------------- */
        .modal-content {
            border: 1px solid rgba(226, 232, 240, 0.9) !important;
            border-radius: 16px !important;
            box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.25) !important;
            overflow: hidden;
            font-family: 'K2D', sans-serif !important;
        }
        .modal-header {
            padding: 0.95rem 1.25rem !important;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0 !important;
        }
        .modal-header .modal-title {
            font-weight: 700 !important;
            font-size: 1.05rem !important;
            color: #0f172a !important;
            letter-spacing: -0.2px;
        }
        .modal-header .btn-close {
            padding: 0.5rem !important;
            margin: -0.25rem -0.25rem -0.25rem auto !important;
            border-radius: 8px;
            transition: all 0.15s ease-in-out;
            opacity: 0.7;
        }
        .modal-header .btn-close:hover {
            opacity: 1;
            background-color: #e2e8f0;
        }
        .modal-body {
            padding: 1.25rem 1.25rem !important;
            color: #334155;
        }
        .modal-footer {
            padding: 0.75rem 1.25rem !important;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0 !important;
        }
        .modal-footer .btn {
            font-weight: 600 !important;
            border-radius: 8px !important;
            padding: 0.45rem 1rem !important;
            font-size: 0.875rem !important;
        }
        .modal-backdrop.show {
            opacity: 0.55 !important;
            backdrop-filter: blur(4px) !important;
            -webkit-backdrop-filter: blur(4px) !important;
        }
        @media (max-width: 767.98px) {
            .modal-dialog {
                margin: 0.5rem !important;
            }
            .modal-content {
                border-radius: 14px !important;
            }
            .modal-header {
                padding: 0.85rem 1rem !important;
            }
            .modal-body {
                padding: 1rem !important;
            }
            .modal-footer {
                padding: 0.75rem 1rem !important;
            }

            /* Mobile Fullscreen App-style Dialog */
            #trackModal .modal-dialog {
                margin: 0 !important;
                max-width: 100% !important;
                height: 100% !important;
            }
            #trackModal .modal-content {
                border-radius: 0 !important;
                min-height: 100vh !important;
                border: none !important;
            }
            #trackModal .modal-header {
                position: sticky !important;
                top: 0 !important;
                z-index: 1060 !important;
                background: #ffffff !important;
                box-shadow: 0 2px 10px rgba(15, 23, 42, 0.06) !important;
                padding: 0.75rem 1rem !important;
            }
            #trackModal .modal-body {
                padding: 0.85rem !important;
            }
            #trackModal .modal-footer {
                position: sticky !important;
                bottom: 0 !important;
                z-index: 1060 !important;
                background: #ffffff !important;
                box-shadow: 0 -2px 10px rgba(15, 23, 42, 0.06) !important;
                padding: 0.75rem 1rem !important;
            }

            /* Mobile-First Vertical Timeline for Stepper */
            .step-stepper-modal-wrapper {
                overflow-x: visible !important;
                padding-bottom: 0 !important;
            }
            .step-stepper-modal {
                display: flex !important;
                flex-direction: column !important;
                min-width: 100% !important;
                padding: 0.5rem 0.25rem 0.5rem 0.5rem !important;
                position: relative !important;
            }
            /* Vertical Timeline Connector Line */
            .step-stepper-modal::before {
                top: 20px !important;
                bottom: 20px !important;
                left: 20px !important;
                width: 3px !important;
                height: auto !important;
                right: auto !important;
                background: #cbd5e1 !important;
            }
            .step-stepper-modal.return-flow::before {
                background: #86efac !important;
            }
            .step-item-modal {
                display: flex !important;
                flex-direction: row !important;
                align-items: flex-start !important;
                text-align: left !important;
                width: 100% !important;
                max-width: 100% !important;
                min-width: 100% !important;
                padding: 0.45rem 0 !important;
                position: relative !important;
            }
            .step-bubble-modal {
                width: 38px !important;
                height: 38px !important;
                min-width: 38px !important;
                font-size: 0.95rem !important;
                margin-bottom: 0 !important;
                margin-right: 0.85rem !important;
                border-width: 2.5px !important;
                z-index: 2 !important;
            }
            .step-info-col {
                flex-grow: 1 !important;
                background: #ffffff !important;
                border: 1.5px solid #e2e8f0 !important;
                border-radius: 10px !important;
                padding: 0.55rem 0.75rem !important;
                box-shadow: 0 1px 4px rgba(15, 23, 42, 0.04) !important;
                text-align: left !important;
            }
            .step-info-col .step-title-text {
                font-size: 0.84rem !important;
                font-weight: 700 !important;
                margin-bottom: 3px !important;
            }
            .step-stepper-box {
                padding: 0.75rem !important;
                border-radius: 12px !important;
            }
            #modalRouteTable th, #modalRouteTable td {
                font-size: 0.76rem !important;
                padding: 0.4rem 0.5rem !important;
            }
        }
    </style>
</head>

<body>

    <!-- Ambient Animated Background Layer -->
    <div class="ambient-bg" aria-hidden="true">
        <div class="floating-blob blob-1"></div>
        <div class="floating-blob blob-2"></div>
        <div class="floating-blob blob-3"></div>
    </div>

    <!-- Main Content Container -->
    <div class="main-wrapper">

        <!-- Sneat Header Navbar (Compact & Responsive Detached) -->
        <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center glass-card navbar-compact" id="layout-navbar" style="background: #ffffff !important;">
            <div class="container-fluid px-1 px-sm-2 d-flex align-items-center justify-content-between">
                <!-- Brand Logo & Title -->
                <a class="navbar-brand d-flex align-items-center m-0" href="<?= base_url('/') ?>">
                    <span class="badge bg-primary rounded-3 shadow-sm d-flex align-items-center justify-content-center me-2" style="width: 36px; height: 36px; min-width: 36px;">
                        <i class="bx bxs-institution fs-4 text-white"></i>
                    </span>
                    <div class="lh-1">
                        <span class="fw-bolder text-primary" style="font-size: 1.02rem; letter-spacing: 0.3px;">ระบบงานสารบรรณ สกจ.</span>
                        <small class="d-none d-sm-block text-secondary fw-semibold" style="font-size: 0.68rem; margin-top: 2px;">โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์</small>
                    </div>
                </a>

                <!-- Desktop Navigation Links (Visible on Large/Desktop Screens) -->
                <div class="d-none d-xl-flex align-items-center">
                    <ul class="navbar-nav flex-row align-items-center gap-1">
                        <li class="nav-item">
                            <a class="nav-link <?= empty($currentType) ? 'active' : '' ?>" href="<?= base_url('/') ?>">
                                <i class="bx bx-file-find me-1"></i> ติดตามหนังสือ
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $currentType === 'in' ? 'active' : '' ?>" href="javascript:void(0)" onclick="filterTableByType('in')">
                                <i class="bx bx-log-in-circle me-1 text-primary"></i> หนังสือรับ
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $currentType === 'out' ? 'active' : '' ?>" href="javascript:void(0)" onclick="filterTableByType('out')">
                                <i class="bx bx-log-out-circle me-1 text-success"></i> หนังสือส่ง
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $currentType === 'order' ? 'active' : '' ?>" href="javascript:void(0)" onclick="filterTableByType('order')">
                                <i class="bx bx-award me-1 text-warning"></i> คำสั่ง รร.
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $currentType === 'travel' ? 'active' : '' ?>" href="javascript:void(0)" onclick="filterTableByType('travel')">
                                <i class="bx bx-briefcase-alt-2 me-1 text-info"></i> ขอไปราชการ
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Right Action Buttons & Mobile Hamburger -->
                <div class="d-flex align-items-center gap-1">
                    <?php if ($isLoggedIn): ?>
                        <div class="d-none d-sm-flex align-items-center bg-light rounded-pill px-2 py-0.5 border me-1">
                            <span class="badge bg-success rounded-circle p-0 me-1.5" style="width: 7px; height: 7px; min-width: 7px;"></span>
                            <span class="fw-bold text-dark text-truncate" style="font-size: 0.78rem; max-width: 125px;"><?= esc($userFullName) ?></span>
                        </div>
                        <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-primary btn-sm px-2.5 py-1 shadow-sm fw-semibold d-inline-flex align-items-center" style="font-size: 0.82rem; height: 34px;" title="แดชบอร์ดงานของฉัน">
                            <i class="bx bx-grid-alt me-1"></i> <span>แดชบอร์ด</span>
                        </a>
                    <?php else: ?>
                        <a href="<?= base_url('login') ?>" class="btn btn-primary btn-sm px-2.5 py-1 shadow-sm fw-semibold d-inline-flex align-items-center" style="font-size: 0.82rem; height: 34px;">
                            <i class="bx bx-log-in me-1"></i> <span class="d-none d-sm-inline">เข้าสู่ระบบสำหรับบุคลากร</span><span class="d-inline d-sm-none">เข้าสู่ระบบ</span>
                        </a>
                    <?php endif; ?>

                    <!-- Mobile Menu Hamburger Button (Trigger Offcanvas on < 1200px) -->
                    <button class="btn btn-light btn-sm d-xl-none border d-inline-flex align-items-center justify-content-center ms-1 p-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileNavbarOffcanvas" aria-controls="mobileNavbarOffcanvas" title="เปิดเมนูนำทาง" style="width: 34px; height: 34px;">
                        <i class="bx bx-menu fs-4 align-middle text-dark"></i>
                    </button>
                </div>
            </div>
        </nav>

        <!-- Mobile Offcanvas Menu (Clean Modern Drawer) -->
        <div class="offcanvas offcanvas-end" tabindex="-1" id="mobileNavbarOffcanvas" aria-labelledby="mobileNavbarOffcanvasLabel" style="width: 300px;">
            <div class="offcanvas-header border-bottom py-3" style="background: #f8fafc;">
                <div class="d-flex align-items-center">
                    <span class="badge bg-primary p-2 rounded-3 me-2 shadow-sm">
                        <i class="bx bxs-institution fs-5 text-white"></i>
                    </span>
                    <div>
                        <h6 class="offcanvas-title fw-bold text-dark mb-0" id="mobileNavbarOffcanvasLabel">ระบบงานสารบรรณ สกจ.</h6>
                        <small class="text-secondary" style="font-size: 0.72rem;">โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ)</small>
                    </div>
                </div>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body p-3 d-flex flex-column justify-content-between">
                <ul class="nav nav-pills flex-column gap-1">
                    <li class="nav-item">
                        <a class="nav-link <?= empty($currentType) ? 'active fw-bold' : 'text-dark fw-semibold' ?> py-2" href="<?= base_url('/') ?>">
                            <i class="bx bx-file-find me-2 text-primary fs-5 align-middle"></i> ติดตามหนังสือราชการ
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentType === 'in' ? 'active fw-bold' : 'text-dark fw-semibold' ?> py-2" href="javascript:void(0)" onclick="filterTableByType('in')">
                            <i class="bx bx-log-in-circle me-2 text-primary fs-5 align-middle"></i> หนังสือรับ (ภายนอก)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentType === 'out' ? 'active fw-bold' : 'text-dark fw-semibold' ?> py-2" href="javascript:void(0)" onclick="filterTableByType('out')">
                            <i class="bx bx-log-out-circle me-2 text-success fs-5 align-middle"></i> หนังสือส่ง (ภายนอก)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentType === 'internal' ? 'active fw-bold' : 'text-dark fw-semibold' ?> py-2" href="javascript:void(0)" onclick="filterTableByType('internal')">
                            <i class="bx bx-message-detail me-2 text-info fs-5 align-middle"></i> บันทึกข้อความ (ภายใน)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentType === 'order' ? 'active fw-bold' : 'text-dark fw-semibold' ?> py-2" href="javascript:void(0)" onclick="filterTableByType('order')">
                            <i class="bx bx-award me-2 text-warning fs-5 align-middle"></i> คำสั่งโรงเรียน
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentType === 'travel' ? 'active fw-bold' : 'text-dark fw-semibold' ?> py-2" href="javascript:void(0)" onclick="filterTableByType('travel')">
                            <i class="bx bx-briefcase-alt-2 me-2 text-info fs-5 align-middle"></i> ขออนุมัติไปราชการ
                        </a>
                    </li>
                </ul>

                <div class="pt-3 border-top mt-3">
                    <?php if ($isLoggedIn): ?>
                        <div class="d-flex align-items-center mb-3 p-2 bg-light rounded">
                            <div class="avatar avatar-online avatar-sm me-2">
                                <span class="avatar-initial rounded-circle bg-primary text-white fw-bold">
                                    <?= mb_substr(session()->get('first_name') ?? 'U', 0, 1) ?>
                                </span>
                            </div>
                            <div class="text-truncate">
                                <div class="fw-bold text-dark small text-truncate"><?= esc($userFullName) ?></div>
                                <span class="badge bg-success text-white" style="font-size: 0.68rem;">เข้าสู่ระบบแล้ว</span>
                            </div>
                        </div>
                        <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-primary w-100 fw-bold shadow-sm">
                            <i class="bx bx-grid-alt me-1"></i> แดชบอร์ดงานของฉัน
                        </a>
                    <?php else: ?>
                        <a href="<?= base_url('login') ?>" class="btn btn-primary w-100 fw-bold shadow-sm">
                            <i class="bx bx-log-in me-1"></i> เข้าสู่ระบบสำหรับบุคลากร
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Main Body -->
        <div class="container-xxl">

            <!-- Hero Banner (Compact & Streamlined) -->
            <div class="card hero-sneat-card mb-2 mb-md-3 border-0">
                <div class="card-body p-2.5 p-sm-3 p-md-3">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <!-- High Contrast Top Badge -->
                            <div class="d-inline-flex align-items-center px-2 py-0.5 rounded-pill mb-1.5" style="background: rgba(0, 0, 0, 0.45); border: 1px solid rgba(255, 255, 255, 0.4); max-width: 100%;">
                                <i class="bx bx-check-shield text-warning me-1 flex-shrink-0" style="font-size: 0.85rem;"></i>
                                <span class="fw-bold text-white text-truncate" style="font-size: 0.72rem;">Public Portal &bull; ติดตามสถานะสำหรับประชาชน</span>
                            </div>
                            <h2 class="fw-bolder text-white mb-1" style="font-size: 1.15rem; line-height: 1.35; text-shadow: 0 1px 3px rgba(0,0,0,0.3);">ติดตามสถานะหนังสือราชการและขั้นตอนการดำเนินงาน</h2>
                            <p class="text-white mb-2 fw-normal small opacity-90" style="font-size: 0.80rem; line-height: 1.35; max-width: 600px; text-shadow: 0 1px 2px rgba(0,0,0,0.25);">
                                ตรวจสอบความคืบหน้าเอกสารได้อย่างโปร่งใส รู้ทันทีว่าเรื่องอยู่ขั้นตอนใดและใครกำลังพิจารณา
                            </p>
                            <!-- Feature Pills with Pure White Background and Dark Text -->
                            <div class="d-flex flex-wrap gap-1.5 align-items-center">
                                <span class="badge px-2 py-1 rounded-pill shadow-xs" style="background: #ffffff; color: #1e293b; font-weight: 700; border: 1px solid #cbd5e1; font-size: 0.70rem;">
                                    <i class="bx bx-git-commit me-1 text-primary align-middle"></i> เรียลไทม์
                                </span>
                                <span class="badge px-2 py-1 rounded-pill shadow-xs" style="background: #ffffff; color: #1e293b; font-weight: 700; border: 1px solid #cbd5e1; font-size: 0.70rem;">
                                    <i class="bx bx-lock-alt me-1 text-warning align-middle"></i> โหมดอ่านอย่างเดียว
                                </span>
                            </div>
                        </div>
                        <div class="col-lg-4 text-center d-none d-lg-block">
                            <img src="<?= base_url('assets/img/illustrations/man-with-laptop.png') ?>" alt="Tracking Illustration" class="img-fluid" style="max-height: 100px; filter: drop-shadow(0 6px 10px rgba(0,0,0,0.25));" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search & Filter Card (Responsive Grid on Mobile) -->
            <div class="card glass-card mb-3 mb-md-4">
                <div class="card-body p-3 p-sm-4">
                    <form action="<?= base_url('/') ?>" method="GET" class="row g-2 g-sm-3 align-items-end">
                        <div class="col-12 col-md-6 col-lg-5">
                            <label class="form-label text-dark small fw-bold">
                                <i class="bx bx-search-alt me-1 text-primary"></i> ค้นหาเอกสาร
                            </label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text bg-white border-end-0"><i class="bx bx-search text-primary"></i></span>
                                <input type="text" id="searchInput" name="search" class="form-control border-start-0 text-dark fw-medium" placeholder="พิมพ์เลขที่หนังสือ, เลขทะเบียน, หรือชื่อเรื่อง..." value="<?= esc($search) ?>" />
                            </div>
                        </div>
                        <div class="col-6 col-md-3 col-lg-3">
                            <label class="form-label text-dark small fw-bold">
                                <i class="bx bx-folder me-1 text-primary"></i> ประเภทหนังสือ
                            </label>
                            <select name="type" id="typeSelect" class="form-select text-dark fw-medium" style="font-size: 0.85rem;">
                                <option value="">-- ทุกประเภท --</option>
                                <option value="in" <?= $currentType === 'in' ? 'selected' : '' ?>>หนังสือรับ (ภายนอก)</option>
                                <option value="out" <?= $currentType === 'out' ? 'selected' : '' ?>>หนังสือส่ง (ภายนอก)</option>
                                <option value="internal" <?= $currentType === 'internal' ? 'selected' : '' ?>>บันทึกข้อความ (ภายใน)</option>
                                <option value="order" <?= $currentType === 'order' ? 'selected' : '' ?>>คำสั่งโรงเรียน</option>
                                <option value="travel" <?= $currentType === 'travel' ? 'selected' : '' ?>>ขออนุมัติไปราชการ</option>
                                <option value="circular" <?= $currentType === 'circular' ? 'selected' : '' ?>>หนังสือเวียน</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-3 col-lg-2">
                            <label class="form-label text-dark small fw-bold">
                                <i class="bx bx-flag me-1 text-primary"></i> สถานะเอกสาร
                            </label>
                            <select name="status" id="statusSelect" class="form-select text-dark fw-medium" style="font-size: 0.85rem;">
                                <option value="">-- ทุกสถานะ --</option>
                                <option value="routing" <?= $currentStatus === 'routing' ? 'selected' : '' ?>>อยู่ระหว่างดำเนินเรื่อง</option>
                                <option value="completed" <?= $currentStatus === 'completed' ? 'selected' : '' ?>>เสร็จสิ้น/อนุมัติแล้ว</option>
                                <option value="returned" <?= $currentStatus === 'returned' ? 'selected' : '' ?>>ส่งคืนแก้ไข</option>
                            </select>
                        </div>
                        <div class="col-12 col-lg-2 d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100 fw-bold py-2 shadow-sm">
                                <i class="bx bx-search me-1"></i> ค้นหา
                            </button>
                            <?php if (!empty($search) || !empty($currentType) || !empty($currentStatus)): ?>
                                <a href="<?= base_url('/') ?>" class="btn btn-outline-secondary fw-semibold d-flex align-items-center justify-content-center px-3" title="ล้างค่าการค้นหา">
                                    <i class="bx bx-reset"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <!-- High Contrast 4 Statistics Cards (Ultra-Compact Modern Grid) -->
            <div class="row g-2 mb-3 mb-md-4">
                <!-- ทั้งหมด -->
                <div class="col-6 col-lg-3">
                    <div class="card stat-sneat-card h-100" onclick="window.location.href='<?= base_url('/') ?>'" title="ดูทะเบียนหนังสือทั้งหมด">
                        <div class="card-body p-2 p-sm-2.5 d-flex align-items-center">
                            <div class="avatar avatar-sm me-2 me-sm-2.5 flex-shrink-0" style="width: 36px; height: 36px;">
                                <span class="avatar-initial rounded-3 bg-primary text-white shadow-sm">
                                    <i class="bx bx-folder-open fs-5"></i>
                                </span>
                            </div>
                            <div class="min-w-0 flex-grow-1">
                                <span class="d-block text-secondary fw-bold text-truncate" style="font-size: 0.74rem; line-height: 1.2;">ทะเบียนรวม</span>
                                <div class="d-flex align-items-baseline gap-1 mt-0.5">
                                    <span class="fw-bolder text-dark lh-1" style="font-size: 1.25rem !important;"><?= number_format($totalCount) ?></span>
                                    <span class="text-secondary fw-normal" style="font-size: 0.70rem;">เรื่อง</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- กำลังพิจารณา -->
                <div class="col-6 col-lg-3">
                    <div class="card stat-sneat-card h-100" onclick="window.location.href='<?= base_url('?status=routing') ?>'" title="ดูหนังสือที่อยู่ระหว่างพิจารณา">
                        <div class="card-body p-2 p-sm-2.5 d-flex align-items-center">
                            <div class="avatar avatar-sm me-2 me-sm-2.5 flex-shrink-0" style="width: 36px; height: 36px;">
                                <span class="avatar-initial rounded-3 text-white shadow-sm" style="background: #d97706;">
                                    <i class="bx bx-time-five fs-5"></i>
                                </span>
                            </div>
                            <div class="min-w-0 flex-grow-1">
                                <span class="d-block text-secondary fw-bold text-truncate" style="font-size: 0.74rem; line-height: 1.2;">กำลังพิจารณา</span>
                                <div class="d-flex align-items-baseline gap-1 mt-0.5">
                                    <span class="fw-bolder lh-1" style="color: #b45309; font-size: 1.25rem !important;"><?= number_format($routingCount) ?></span>
                                    <span class="text-secondary fw-normal" style="font-size: 0.70rem;">เรื่อง</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- เสร็จสิ้น -->
                <div class="col-6 col-lg-3">
                    <div class="card stat-sneat-card h-100" onclick="window.location.href='<?= base_url('?status=completed') ?>'" title="ดูหนังสือที่เสร็จสิ้นสมบูรณ์">
                        <div class="card-body p-2 p-sm-2.5 d-flex align-items-center">
                            <div class="avatar avatar-sm me-2 me-sm-2.5 flex-shrink-0" style="width: 36px; height: 36px;">
                                <span class="avatar-initial rounded-3 bg-success text-white shadow-sm">
                                    <i class="bx bx-check-circle fs-5"></i>
                                </span>
                            </div>
                            <div class="min-w-0 flex-grow-1">
                                <span class="d-block text-secondary fw-bold text-truncate" style="font-size: 0.74rem; line-height: 1.2;">เสร็จสิ้น</span>
                                <div class="d-flex align-items-baseline gap-1 mt-0.5">
                                    <span class="fw-bolder lh-1" style="color: #15803d; font-size: 1.25rem !important;"><?= number_format($completedCount) ?></span>
                                    <span class="text-secondary fw-normal" style="font-size: 0.70rem;">เรื่อง</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ส่งคืน -->
                <div class="col-6 col-lg-3">
                    <div class="card stat-sneat-card h-100" onclick="window.location.href='<?= base_url('?status=returned') ?>'" title="ดูหนังสือที่ส่งคืนให้แก้ไข">
                        <div class="card-body p-2 p-sm-2.5 d-flex align-items-center">
                            <div class="avatar avatar-sm me-2 me-sm-2.5 flex-shrink-0" style="width: 36px; height: 36px;">
                                <span class="avatar-initial rounded-3 bg-danger text-white shadow-sm">
                                    <i class="bx bx-revision fs-5"></i>
                                </span>
                            </div>
                            <div class="min-w-0 flex-grow-1">
                                <span class="d-block text-secondary fw-bold text-truncate" style="font-size: 0.74rem; line-height: 1.2;">ส่งคืนแก้ไข</span>
                                <div class="d-flex align-items-baseline gap-1 mt-0.5">
                                    <span class="fw-bolder lh-1" style="color: #b91c1c; font-size: 1.25rem !important;"><?= number_format($returnedCount) ?></span>
                                    <span class="text-secondary fw-normal" style="font-size: 0.70rem;">เรื่อง</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Readonly Policy Alert Banner (High Contrast Warm Yellow) -->
            <div class="alert d-flex align-items-center justify-content-between mb-3 mb-md-4 shadow-sm py-2 px-3" role="alert" style="background: #fffbe6; border: 1.5px solid #ffe58f; color: #614700; border-radius: 0.75rem;">
                <div class="d-flex align-items-center">
                    <i class="bx bxs-info-circle fs-3 me-2 flex-shrink-0" style="color: #d48806;"></i>
                    <div class="small fw-medium" style="font-size: 0.8rem;">
                        <strong class="fw-bold" style="color: #382400;">โหมดตรวจสอบสาธารณะ (Read-only):</strong> บุคคลทั่วไปสามารถติดตามความคืบหน้าได้ แต่ไม่สามารถลงนามได้ เจ้าหน้าที่/ผู้บริหาร <a href="<?= base_url('login') ?>" class="alert-link text-decoration-underline fw-bold" style="color: #002c8a;">เข้าสู่ระบบที่นี่</a>
                    </div>
                </div>
                <span class="badge px-2.5 py-1.5 fw-bold d-none d-md-inline-block shadow-sm flex-shrink-0 ms-2" style="background: #faad14; color: #000000; font-size: 0.72rem;">
                    <i class="bx bx-lock-alt me-1"></i> โหมดอ่านอย่างเดียว
                </span>
            </div>

            <!-- Nav Pills Tabs (Horizontal Touch Scroll on Mobile) -->
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-3 gap-2">
                <div class="w-100 overflow-hidden">
                    <ul class="nav nav-pills flex-nowrap overflow-x-auto pb-1 gap-2 scrollbar-none" style="-webkit-overflow-scrolling: touch;">
                        <li class="nav-item flex-shrink-0">
                            <a class="nav-link custom-nav-pill <?= empty($currentType) ? 'active' : '' ?>" href="<?= base_url('/') ?>">
                                <i class="bx bx-grid-alt me-1"></i> ทั้งหมด
                            </a>
                        </li>
                        <li class="nav-item flex-shrink-0">
                            <a class="nav-link custom-nav-pill <?= $currentType === 'in' ? 'active' : '' ?>" href="<?= base_url('?type=in') ?>">
                                <i class="bx bx-log-in-circle me-1"></i> หนังสือรับ
                            </a>
                        </li>
                        <li class="nav-item flex-shrink-0">
                            <a class="nav-link custom-nav-pill <?= $currentType === 'out' ? 'active' : '' ?>" href="<?= base_url('?type=out') ?>">
                                <i class="bx bx-log-out-circle me-1"></i> หนังสือส่ง
                            </a>
                        </li>
                        <li class="nav-item flex-shrink-0">
                            <a class="nav-link custom-nav-pill <?= $currentType === 'internal' ? 'active' : '' ?>" href="<?= base_url('?type=internal') ?>">
                                <i class="bx bx-message-detail me-1"></i> บันทึกข้อความ
                            </a>
                        </li>
                        <li class="nav-item flex-shrink-0">
                            <a class="nav-link custom-nav-pill <?= $currentType === 'order' ? 'active' : '' ?>" href="<?= base_url('?type=order') ?>">
                                <i class="bx bx-award me-1"></i> คำสั่งโรงเรียน
                            </a>
                        </li>
                        <li class="nav-item flex-shrink-0">
                            <a class="nav-link custom-nav-pill <?= $currentType === 'travel' ? 'active' : '' ?>" href="<?= base_url('?type=travel') ?>">
                                <i class="bx bx-briefcase-alt-2 me-1"></i> ขออนุมัติไปราชการ
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="text-dark fw-bold small flex-shrink-0 bg-white px-2.5 py-1.5 rounded border shadow-xs d-flex align-items-center align-self-end align-self-sm-auto" style="font-size: 0.78rem;">
                    แสดง <span class="badge bg-primary px-2 py-0.5 mx-1"><?= count($documents) ?></span> เรื่อง
                </div>
            </div>

            <!-- Desktop Document Table Card (d-none d-md-block) -->
            <div class="card glass-card mb-4 border-0 d-none d-md-block">
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover align-middle mb-0" id="docsTable">
                        <thead style="background: #f1f5f9; border-bottom: 2px solid #cbd5e1;">
                            <tr>
                                <th style="width: 140px; color: #0f172a; font-weight: 700;">เลขทะเบียน รร.</th>
                                <th style="width: 160px; color: #0f172a; font-weight: 700;">เลขที่หนังสือ</th>
                                <th style="color: #0f172a; font-weight: 700;">เรื่อง & หน่วยงาน</th>
                                <th style="width: 120px; color: #0f172a; font-weight: 700;">ลงวันที่</th>
                                <th style="color: #0f172a; font-weight: 700;">ขั้นตอนปัจจุบัน / ผู้ถือครองเอกสาร</th>
                                <th style="width: 140px; color: #0f172a; font-weight: 700;">สถานะ</th>
                                <th class="text-center" style="width: 140px; color: #0f172a; font-weight: 700;">ติดตามขั้นตอน</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            <?php if (empty($documents)): ?>
                                <!-- High-Contrast Empty State -->
                                <tr>
                                    <td colspan="7" class="text-center py-5" style="background: #ffffff;">
                                        <div class="py-4">
                                            <i class="bx bx-folder-open display-3 d-block mb-3" style="color: #4f46e5;"></i>
                                            <h4 class="fw-bolder mb-2" style="color: #0f172a;">ไม่พบรายการหนังสือตามเงื่อนไข</h4>
                                            <p class="mb-3 fw-medium" style="color: #475569; max-width: 500px; margin: 0 auto;">
                                                ยังไม่มีรายการหนังสือในหมวดหมู่นี้ หรือไม่มีเอกสารที่ตรงกับคำค้นหาของท่าน
                                            </p>
                                            <a href="<?= base_url('/') ?>" class="btn btn-primary px-4 py-2 shadow-sm fw-bold">
                                                <i class="bx bx-refresh me-1"></i> ดูรายการหนังสือทั้งหมด
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($documents as $doc): ?>
                                    <?php if (($doc['status'] ?? '') === 'draft') continue; ?>
                                    <tr>
                                        <!-- เลขทะเบียน รร. -->
                                        <td>
                                            <span class="badge font-monospace fw-bold" style="background: #e2e8f0; color: #0f172a; border: 1px solid #cbd5e1; font-size: 0.85rem;">
                                                <?= esc($doc['internal_reg_no'] ?: '-') ?>
                                            </span>
                                        </td>

                                        <!-- เลขที่หนังสือ -->
                                        <td>
                                            <div class="fw-bold text-dark fs-6"><?= esc($doc['doc_no']) ?></div>
                                            <div class="mt-1">
                                                <?= doc_type_badge($doc['doc_type']) ?>
                                                <?= urgency_badge($doc['urgency']) ?>
                                            </div>
                                        </td>

                                        <!-- ชื่อเรื่อง & หน่วยงาน -->
                                        <td style="white-space: normal; min-width: 250px; max-width: 380px;">
                                            <a href="javascript:void(0)" onclick="openTrackModal(<?= $doc['id'] ?>, '<?= esc($doc['doc_type']) ?>')" class="fw-bold text-dark text-decoration-none hover-primary">
                                                <?= esc($doc['title']) ?>
                                            </a>
                                            <div class="small mt-1 text-secondary fw-medium">
                                                <span><i class="bx bx-user me-1 text-primary"></i>จาก: <strong class="text-dark"><?= esc($doc['from_org']) ?></strong></span>
                                                <span class="ms-2"><i class="bx bx-right-arrow-alt me-1 text-success"></i>ถึง: <strong class="text-dark"><?= esc($doc['to_org']) ?></strong></span>
                                            </div>
                                        </td>

                                        <!-- วันที่ -->
                                        <td>
                                            <strong class="text-dark d-block"><?= thai_date($doc['doc_date'], 'short') ?></strong>
                                        </td>

                                        <!-- ขั้นตอนปัจจุบัน (Current Workflow) -->
                                        <td style="white-space: normal; min-width: 220px;">
                                            <?php if ($doc['status'] === 'completed'): ?>
                                                <div class="small fw-bold" style="color: #15803d;">
                                                    <i class="bx bx-check-double fs-5 align-middle me-1"></i> สิ้นสุดกระบวนการ (อนุมัติแล้ว)
                                                </div>
                                            <?php elseif ($doc['status'] === 'returned'): ?>
                                                <div class="small fw-bold" style="color: #b91c1c;">
                                                    <i class="bx bx-error-circle fs-5 align-middle me-1"></i> เอกสารถูกส่งคืนให้แก้ไข
                                                </div>
                                            <?php else: ?>
                                                <?php if (!empty($doc['current_handler'])): ?>
                                                    <div class="current-handler-pill mb-1">
                                                        <i class="bx bx-git-commit me-1"></i>
                                                        <strong>ขั้นที่ <?= $doc['current_step'] ?></strong>: รอ <?= esc($doc['current_handler']['position_name']) ?>
                                                    </div>
                                                    <div class="small text-dark fw-medium">
                                                        ผู้พิจารณา: <?= esc(($doc['current_handler']['prefix'] ?? '') . $doc['current_handler']['first_name'] . ' ' . $doc['current_handler']['last_name']) ?>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-dark small">-</span>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </td>

                                        <!-- สถานะ -->
                                        <td>
                                            <?= doc_status_badge($doc['status']) ?>
                                        </td>

                                        <!-- ปุ่มติดตาม -->
                                        <td class="text-center">
                                            <button type="button" class="btn btn-primary btn-sm px-3 shadow-sm fw-bold" onclick="openTrackModal(<?= $doc['id'] ?>, '<?= esc($doc['doc_type']) ?>')" title="ดูไทม์ไลน์และขั้นตอนอย่างละเอียด">
                                                <i class="bx bx-git-repo-forked me-1"></i> ดูขั้นตอน
                                            </button>
                                            <a href="<?= base_url(($doc['is_travel'] ?? false) ? 'travel/' . $doc['id'] : 'tracking/' . $doc['id']) ?>" class="btn btn-icon btn-outline-secondary btn-sm ms-1" title="เปิดหน้าเต็ม">
                                                <i class="bx bx-expand-alt"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Mobile Document Cards List (Dedicated Mobile-First UI: d-block d-md-none) -->
            <div class="d-block d-md-none mb-4">
                <?php if (empty($documents)): ?>
                    <div class="card glass-card text-center p-4">
                        <i class="bx bx-folder-open display-4 d-block mb-2 text-primary"></i>
                        <h5 class="fw-bold text-dark mb-1">ไม่พบรายการหนังสือตามเงื่อนไข</h5>
                        <p class="text-secondary small mb-3">ยังไม่มีรายการหนังสือในหมวดหมู่นี้ หรือไม่มีเอกสารที่ตรงกับคำค้นหาของท่าน</p>
                        <a href="<?= base_url('/') ?>" class="btn btn-primary btn-sm px-3 fw-bold">
                            <i class="bx bx-refresh me-1"></i> ดูรายการหนังสือทั้งหมด
                        </a>
                    </div>
                <?php else: ?>
                    <div class="d-flex flex-column gap-2.5">
                        <?php foreach ($documents as $doc): ?>
                            <?php if (($doc['status'] ?? '') === 'draft') continue; ?>
                            <div class="card glass-card p-3 doc-mobile-card shadow-xs">
                                <!-- Top Row: Registration No & Status -->
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge font-monospace fw-bold" style="background: #e2e8f0; color: #0f172a; border: 1px solid #cbd5e1; font-size: 0.78rem;">
                                        <?= esc($doc['internal_reg_no'] ?: '-') ?>
                                    </span>
                                    <div>
                                        <?= doc_status_badge($doc['status']) ?>
                                    </div>
                                </div>

                                <!-- Doc Number & Badges -->
                                <div class="d-flex flex-wrap align-items-center gap-1.5 mb-2">
                                    <span class="fw-bolder text-dark" style="font-size: 0.95rem;"><?= esc($doc['doc_no']) ?></span>
                                    <?= doc_type_badge($doc['doc_type']) ?>
                                    <?= urgency_badge($doc['urgency']) ?>
                                </div>

                                <!-- Title (clickable) -->
                                <h6 class="fw-bold mb-2 lh-base" style="font-size: 0.92rem;">
                                    <a href="javascript:void(0)" onclick="openTrackModal(<?= $doc['id'] ?>, '<?= esc($doc['doc_type']) ?>')" class="text-dark text-decoration-none hover-primary">
                                        <?= esc($doc['title']) ?>
                                    </a>
                                </h6>

                                <!-- Sender / Receiver & Date Meta -->
                                <div class="small text-secondary mb-2 bg-light p-2 rounded" style="font-size: 0.78rem;">
                                    <div class="text-truncate mb-1">
                                        <i class="bx bx-user me-1 text-primary"></i>จาก: <strong class="text-dark"><?= esc($doc['from_org']) ?></strong>
                                    </div>
                                    <div class="text-truncate mb-1">
                                        <i class="bx bx-right-arrow-alt me-1 text-success"></i>ถึง: <strong class="text-dark"><?= esc($doc['to_org']) ?></strong>
                                    </div>
                                    <div>
                                        <i class="bx bx-calendar me-1 text-primary"></i>ลงวันที่: <strong class="text-dark"><?= thai_date($doc['doc_date'], 'short') ?></strong>
                                    </div>
                                </div>

                                <!-- Workflow Step Indicator -->
                                <div class="mb-3">
                                    <?php if ($doc['status'] === 'completed'): ?>
                                        <div class="small fw-bold px-2 py-1 rounded" style="background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; font-size: 0.78rem;">
                                            <i class="bx bx-check-double me-1 fs-6 align-middle"></i> สิ้นสุดกระบวนการ (อนุมัติแล้ว)
                                        </div>
                                    <?php elseif ($doc['status'] === 'returned'): ?>
                                        <div class="small fw-bold px-2 py-1 rounded" style="background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; font-size: 0.78rem;">
                                            <i class="bx bx-error-circle me-1 fs-6 align-middle"></i> เอกสารถูกส่งคืนให้แก้ไข
                                        </div>
                                    <?php else: ?>
                                        <?php if (!empty($doc['current_handler'])): ?>
                                            <div class="small fw-bold px-2 py-1 rounded" style="background: #eef2ff; color: #3730a3; border: 1px solid #c7d2fe; font-size: 0.78rem;">
                                                <i class="bx bx-git-commit me-1"></i>
                                                <strong>ขั้นที่ <?= $doc['current_step'] ?></strong>: รอ <?= esc($doc['current_handler']['position_name']) ?>
                                                <div class="fw-normal text-dark text-truncate mt-0.5">
                                                    ผู้พิจารณา: <?= esc(($doc['current_handler']['prefix'] ?? '') . $doc['current_handler']['first_name'] . ' ' . $doc['current_handler']['last_name']) ?>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <div class="text-muted small">-</div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>

                                <!-- Action Buttons -->
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-primary btn-sm flex-grow-1 fw-bold py-2 shadow-sm" onclick="openTrackModal(<?= $doc['id'] ?>, '<?= esc($doc['doc_type']) ?>')">
                                        <i class="bx bx-git-repo-forked me-1"></i> ดูขั้นตอน
                                    </button>
                                    <a href="<?= base_url(($doc['is_travel'] ?? false) ? 'travel/' . $doc['id'] : 'tracking/' . $doc['id']) ?>" class="btn btn-outline-secondary btn-sm d-flex align-items-center justify-content-center px-3" title="เปิดหน้าเต็ม">
                                        <i class="bx bx-expand-alt"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

        </div>

        <!-- Footer -->
        <footer class="footer bg-white border-top py-4 mt-5 glass-card">
            <div class="container-xxl text-center small" style="color: #334155;">
                <div class="fw-bold">
                    © <?= date('Y') + 543 ?> <strong>ระบบงานสารบรรณ สกจ. (e-Saraban SKJ)</strong>
                </div>
                <div class="mt-1 fw-medium text-secondary">
                    พัฒนาเพื่อการติดตามงานราชการที่โปร่งใส ตรวจสอบได้ทุกขั้นตอน
                </div>
            </div>
        </footer>

    </div>

    <!-- Quick Workflow Tracker Modal (High Contrast & Extra Wide) -->
    <!-- Quick Workflow Tracker Modal (Mobile-First Fullscreen & High Contrast) -->
    <div class="modal fade" id="trackModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen-sm-down modal-xl modal-dialog-centered" style="max-width: 1200px;">
            <div class="modal-content border-0 shadow-lg" style="background: #ffffff;">
                <div class="modal-header border-bottom py-2.5 px-3 px-sm-4 bg-light">
                    <div class="d-flex align-items-center">
                        <span class="badge bg-primary p-2 rounded-3 me-2.5 shadow-sm">
                            <i class="bx bx-git-commit fs-4 text-white"></i>
                        </span>
                        <div class="lh-1">
                            <h5 class="modal-title fw-bold text-dark mb-0" style="font-size: 1rem;">สถานะและเส้นทางเดินหนังสือราชการ</h5>
                            <small class="text-secondary fw-semibold" id="modalDocSubheader" style="font-size: 0.72rem;">ระบบติดตามแบบสาธารณะ (Workflow Tracking)</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body py-3 px-2 px-sm-3 px-md-4">
                    <!-- Loading Spinner -->
                    <div id="modalLoading" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">กำลังโหลดข้อมูล...</span>
                        </div>
                        <p class="text-dark fw-semibold small mt-2">กำลังดึงข้อมูลขั้นตอนและไทม์ไลน์...</p>
                    </div>

                    <!-- Modal Content Container -->
                    <div id="modalContent" style="display: none;">
                        <!-- Doc Info Card (Mobile-First High Contrast) -->
                        <div class="card border p-2.5 p-sm-3 mb-3" style="background: #f8fafc; border: 1.5px solid #cbd5e1 !important; border-radius: 0.75rem;">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-1.5 mb-2">
                                <div class="d-flex flex-wrap align-items-center gap-1">
                                    <span id="modalDocType"></span>
                                    <span id="modalUrgency"></span>
                                    <strong class="fs-6 text-dark ms-1" id="modalDocNo"></strong>
                                </div>
                                <span id="modalDocStatus"></span>
                            </div>
                            <h6 class="fw-bold text-primary mb-2" id="modalDocTitle" style="font-size: 0.95rem; line-height: 1.4;"></h6>
                            <div class="row g-2 small text-dark fw-medium" style="font-size: 0.78rem;">
                                <div class="col-6 col-sm-6">
                                    <i class="bx bx-barcode me-1 text-primary"></i> ทะเบียน: <strong class="text-dark" id="modalRegNo"></strong>
                                </div>
                                <div class="col-6 col-sm-6">
                                    <i class="bx bx-calendar me-1 text-primary"></i> วันที่: <strong class="text-dark" id="modalDocDate"></strong>
                                </div>
                                <div class="col-6 col-sm-6">
                                    <i class="bx bx-paper-plane me-1 text-primary"></i> จาก: <strong class="text-dark" id="modalFromOrg"></strong>
                                </div>
                                <div class="col-6 col-sm-6">
                                    <i class="bx bx-building me-1 text-primary"></i> ถึง: <strong class="text-dark" id="modalToOrg"></strong>
                                </div>
                            </div>
                        </div>

                        <!-- Stepper Visual (Outbound: สายเสนอเรื่อง) -->
                        <div class="mb-3 step-stepper-box" style="border-left: 5px solid #4338ca; border-top: 1.5px solid #cbd5e1; border-right: 1.5px solid #cbd5e1; border-bottom: 1.5px solid #cbd5e1; background: #fafbff;">
                            <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom" style="border-color: #e2e8f0 !important;">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-primary text-white fw-bold px-2 py-1 shadow-sm" style="font-size: 0.76rem;">
                                        <i class="bx bx-right-arrow-alt me-1 fs-6 align-middle"></i> ขาไป ➔
                                    </span>
                                    <div>
                                        <h6 class="text-dark fw-bolder mb-0" style="font-size: 0.88rem;">
                                            เส้นทางเดินหนังสือขออนุมัติและเกษียนลงนาม
                                        </h6>
                                        <small class="text-muted fw-semibold d-none d-sm-block" style="font-size: 0.74rem;">ส่งต่อตามลำดับชั้นบังคับบัญชาเพื่อพิจารณาอนุมัติ</small>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-1">
                                    <span class="badge bg-white text-secondary border d-md-none" style="font-size: 0.68rem;">
                                        <i class="bx bx-sort-down me-1"></i> ลำดับขั้นตอน
                                    </span>
                                    <span class="badge bg-white text-primary border border-primary fw-bold px-2 py-0.5 shadow-xs" style="font-size: 0.72rem;">
                                        สายเสนอเรื่อง
                                    </span>
                                </div>
                            </div>
                            <div class="step-stepper-modal-wrapper">
                                <div class="step-stepper-modal" id="modalStepper">
                                    <!-- Steps rendered dynamically -->
                                </div>
                            </div>
                        </div>

                        <!-- Stepper Visual (Return: ขากลับ ส่งคืนตัวจริง) -->
                        <div class="mb-3 step-stepper-box" id="modalReturnSection" style="display: none; border-left: 5px solid #16a34a; border-top: 1.5px solid #bbf7d0; border-right: 1.5px solid #bbf7d0; border-bottom: 1.5px solid #bbf7d0; background: #f0fdf4;">
                            <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom" style="border-color: #bbf7d0 !important;">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-success text-white fw-bold px-2 py-1 shadow-sm" style="font-size: 0.76rem;">
                                        <i class="bx bx-left-arrow-alt me-1 fs-6 align-middle"></i> ขากลับ ⬅️
                                    </span>
                                    <div>
                                        <h6 class="text-dark fw-bolder mb-0" style="font-size: 0.88rem;">
                                            เส้นทางเดินหนังสือส่งมอบฉบับจริงคืนโรงเรียน
                                        </h6>
                                        <small class="text-muted fw-semibold d-none d-sm-block" style="font-size: 0.74rem;">ส่งคืนแฟ้มฉบับจริงที่ลงนามแล้วย้อนกลับสู่โรงเรียนผู้ขอ</small>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-1">
                                    <span class="badge bg-white text-secondary border d-md-none" style="font-size: 0.68rem;">
                                        <i class="bx bx-sort-down me-1"></i> ลำดับขั้นตอน
                                    </span>
                                    <span class="badge bg-white text-success border border-success fw-bold px-2 py-0.5 shadow-xs" style="font-size: 0.72rem;">
                                        ส่งมอบตัวจริง
                                    </span>
                                </div>
                            </div>
                            <div class="step-stepper-modal-wrapper">
                                <div class="step-stepper-modal return-flow" id="modalReturnStepper">
                                    <!-- Return Steps rendered dynamically -->
                                </div>
                            </div>
                        </div>

                        <!-- Step Details Table -->
                        <div class="mb-3">
                            <h6 class="text-dark fw-bold text-uppercase mb-2" style="font-size: 0.82rem; letter-spacing: 0.5px;">
                                <i class="bx bx-list-check text-primary me-1"></i> รายละเอียดบันทึกการพิจารณาแต่ละขั้นตอน
                            </h6>
                            <div class="table-responsive rounded-3 border" style="background: #ffffff;">
                                <table class="table table-bordered table-sm mb-0">
                                    <thead style="background: #f1f5f9;">
                                        <tr class="small text-dark fw-bold" style="font-size: 0.76rem;">
                                            <th class="text-center" style="width: 65px;">สายงาน</th>
                                            <th class="text-center" style="width: 45px;">ขั้น</th>
                                            <th>ผู้พิจารณา / ตำแหน่ง</th>
                                            <th style="width: 120px;">สถานะ</th>
                                            <th style="width: 130px;">วัน-เวลา</th>
                                            <th>บันทึก / ข้อคิดเห็น</th>
                                        </tr>
                                    </thead>
                                    <tbody id="modalRouteTable" class="small text-dark fw-medium">
                                        <!-- Rows rendered dynamically -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- System Logs -->
                        <div>
                            <h6 class="text-dark fw-bold text-uppercase mb-2" style="font-size: 0.82rem; letter-spacing: 0.5px;">
                                <i class="bx bx-history text-primary me-1"></i> ประวัติความเคลื่อนไหวของเอกสาร (History Logs)
                            </h6>
                            <div class="p-2.5 p-sm-3 rounded-3" style="background: #f8fafc; border: 1.5px solid #cbd5e1; max-height: 140px; overflow-y: auto;">
                                <ul class="list-unstyled mb-0 small text-dark" id="modalLogList">
                                    <!-- Logs rendered dynamically -->
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-top py-2 px-3 px-sm-4 bg-light d-flex flex-column flex-sm-row justify-content-between align-items-stretch align-items-sm-center gap-2">
                    <div class="small fw-semibold text-secondary text-center text-sm-start" style="font-size: 0.76rem;">
                        <i class="bx bx-lock-alt me-1 text-warning align-middle"></i> สิทธิ์ดูอย่างเดียว หากต้องการลงนาม กรุณาเข้าสู่ระบบ
                    </div>
                    <div class="d-flex gap-2 justify-content-end w-100 w-sm-auto">
                        <a id="modalDetailLink" href="#" class="btn btn-outline-primary btn-sm fw-bold flex-grow-1 flex-sm-grow-0 d-inline-flex align-items-center justify-content-center" style="height: 36px;">
                            <i class="bx bx-file me-1"></i> เปิดดูหน้าเต็ม
                        </a>
                        <button type="button" class="btn btn-secondary btn-sm fw-bold flex-grow-1 flex-sm-grow-0 d-inline-flex align-items-center justify-content-center" data-bs-dismiss="modal" style="height: 36px;">
                            <i class="bx bx-x me-1"></i> ปิดหน้าต่าง
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sneat Core JS & Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url('assets/js/submit-loading.js') ?>"></script>

    <!-- SweetAlert2 library & Global AppAlert Bridge -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?= base_url('assets/js/app-swal.js?v=' . (file_exists(FCPATH . 'assets/js/app-swal.js') ? filemtime(FCPATH . 'assets/js/app-swal.js') : time())) ?>"></script>

    <script>
        function filterTableByType(type) {
            window.location.href = '<?= base_url('/') ?>?type=' + type;
        }

        function openTrackModal(docId, docType) {
            const modalEl = document.getElementById('trackModal');
            const modal = new bootstrap.Modal(modalEl);
            modal.show();

            const loading = document.getElementById('modalLoading');
            const content = document.getElementById('modalContent');
            loading.style.display = 'block';
            content.style.display = 'none';

            let apiUrl = '<?= base_url('api/track-document/') ?>' + docId;
            if (docType === 'travel') {
                apiUrl += '?type=travel';
            }

            fetch(apiUrl)
                .then(res => res.json())
                .then(data => {
                    if (!data.success) {
                        AppAlert.error(data.message || 'เกิดข้อผิดพลาดในการโหลดข้อมูล', 'ไม่พบข้อมูล');
                        return;
                    }

                    const doc = data.document;
                    document.getElementById('modalDocNo').textContent = doc.doc_no;
                    document.getElementById('modalDocTitle').textContent = doc.title;
                    document.getElementById('modalRegNo').textContent = doc.internal_reg_no || '-';
                    document.getElementById('modalDocDate').textContent = doc.doc_date_th;
                    document.getElementById('modalFromOrg').textContent = doc.from_org || '-';
                    document.getElementById('modalToOrg').textContent = doc.to_org || '-';
                    document.getElementById('modalDocType').innerHTML = doc.doc_type_badge;
                    document.getElementById('modalUrgency').innerHTML = doc.urgency_badge;
                    document.getElementById('modalDocStatus').innerHTML = doc.status_badge;
                    document.getElementById('modalDetailLink').href = (doc.doc_type === 'travel')
                        ? '<?= base_url('travel/') ?>' + doc.id
                        : '<?= base_url('tracking/') ?>' + doc.id;

                    // Render Stepper (Outbound)
                    const stepper = document.getElementById('modalStepper');
                    stepper.innerHTML = '';
                    data.routes.forEach(r => {
                        let bubbleClass = 'pending';
                        let iconHtml = r.step_order;
                        let statusText = 'รอดำเนินการ';
                        let statusBadge = 'bg-secondary text-white';

                        if (r.status === 'signed') {
                            bubbleClass = 'signed';
                            iconHtml = '<i class="bx bx-check fs-4"></i>';
                            statusText = 'ลงนามแล้ว';
                            statusBadge = 'bg-success text-white';
                        } else if (r.status === 'returned') {
                            bubbleClass = 'returned';
                            iconHtml = '<i class="bx bx-x fs-4"></i>';
                            statusText = 'ส่งคืนแก้ไข';
                            statusBadge = 'bg-danger text-white';
                        } else if (r.status === 'skipped') {
                            bubbleClass = 'pending';
                            iconHtml = '<i class="bx bx-minus fs-4"></i>';
                            statusText = 'ข้ามขั้นตอนนี้';
                            statusBadge = 'bg-light text-muted border';
                        } else if (r.is_current) {
                            bubbleClass = 'active';
                            iconHtml = '<i class="bx bx-edit fs-4"></i>';
                            statusText = 'กำลังพิจารณา';
                            statusBadge = 'bg-primary text-white';
                        }

                        const stepCol = document.createElement('div');
                        stepCol.className = 'step-item-modal';
                        stepCol.innerHTML = `
                            <div class="step-bubble-modal ${bubbleClass}">
                                ${iconHtml}
                            </div>
                            <div class="step-info-col">
                                <div class="step-title-text mb-1">${r.step_order}. ${r.position_name}</div>
                                <div class="mb-1">
                                    <span class="badge ${statusBadge} fw-bold shadow-xs px-2 py-0.5" style="font-size: 0.72rem;">${statusText}</span>
                                </div>
                                ${r.user_name && r.user_name !== '-' ? `
                                    <div class="step-user-badge mt-1" title="${r.user_name}">
                                        <i class="bx bx-user me-1 text-primary"></i>${r.user_name}
                                    </div>
                                ` : `<div class="text-muted small mt-1" style="font-size: 0.7rem;">-</div>`}
                            </div>
                        `;
                        stepper.appendChild(stepCol);
                    });

                    // Render Return Stepper (ขากลับ: สำหรับขอไปราชการ)
                    const returnSection = document.getElementById('modalReturnSection');
                    const returnStepper = document.getElementById('modalReturnStepper');
                    returnStepper.innerHTML = '';

                    if (data.return_routes && data.return_routes.length > 0) {
                        returnSection.style.display = 'block';
                        data.return_routes.forEach(r => {
                            let bubbleClass = 'pending';
                            let iconHtml = r.step_order;
                            let statusText = 'รอส่งมอบ';
                            let statusBadge = 'bg-secondary text-white';

                            if (r.status === 'signed') {
                                bubbleClass = 'signed';
                                iconHtml = '<i class="bx bx-check fs-4"></i>';
                                statusText = 'ส่งมอบแล้ว';
                                statusBadge = 'bg-success text-white';
                            } else if (r.status === 'active' || r.is_current) {
                                bubbleClass = 'return-active';
                                iconHtml = '<i class="bx bx-package fs-4"></i>';
                                statusText = 'กำลังส่งมอบ';
                                statusBadge = 'bg-warning text-dark';
                            }

                            const stepCol = document.createElement('div');
                            stepCol.className = 'step-item-modal';
                            stepCol.innerHTML = `
                                <div class="step-bubble-modal ${bubbleClass}">
                                    ${iconHtml}
                                </div>
                                <div class="step-info-col">
                                    <div class="step-title-text mb-1" style="color: #14532d !important;">${r.position_name}</div>
                                    <div class="mb-1">
                                        <span class="badge ${statusBadge} fw-bold shadow-xs px-2 py-0.5" style="font-size: 0.72rem;">${statusText}</span>
                                    </div>
                                    ${r.user_name && r.user_name !== '-' ? `
                                        <div class="step-user-badge mt-1 border-success border-opacity-50" title="${r.user_name}">
                                            <i class="bx bx-user-check me-1 text-success"></i>${r.user_name}
                                        </div>
                                    ` : `<div class="text-muted small mt-1" style="font-size: 0.7rem;">-</div>`}
                                </div>
                            `;
                            returnStepper.appendChild(stepCol);
                        });
                    } else {
                        returnSection.style.display = 'none';
                    }

                    // Render Route Table (รวมทั้งขาไป และ ขากลับ ถ้ามี)
                    const routeTbody = document.getElementById('modalRouteTable');
                    routeTbody.innerHTML = '';

                    // 1. แถวขาไป
                    data.routes.forEach(r => {
                        let statusBadge = '<span class="badge bg-secondary text-white fw-bold">รอดำเนินการ</span>';
                        if (r.status === 'signed') {
                            statusBadge = '<span class="badge bg-success text-white fw-bold shadow-xs px-2 py-1"><i class="bx bx-check me-1"></i>ลงนามแล้ว</span>';
                        } else if (r.status === 'returned') {
                            statusBadge = '<span class="badge bg-danger text-white fw-bold shadow-xs px-2 py-1"><i class="bx bx-x me-1"></i>ส่งคืนแก้ไข</span>';
                        } else if (r.status === 'skipped') {
                            statusBadge = '<span class="badge bg-light text-dark border fw-bold px-2 py-1">ข้ามขั้นตอน</span>';
                        } else if (r.is_current) {
                            statusBadge = '<span class="badge bg-primary text-white fw-bold shadow-xs px-2 py-1"><i class="bx bx-edit me-1"></i>กำลังพิจารณา</span>';
                        }

                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td class="text-center align-middle">
                                <span class="badge bg-primary text-white fw-bold px-2 py-1 shadow-xs" style="font-size: 0.72rem;">ขาไป ➔</span>
                            </td>
                            <td class="text-center align-middle fw-bolder text-primary fs-6">${r.step_order}</td>
                            <td class="align-middle">
                                <div class="fw-bolder text-dark" style="font-size: 0.88rem;">${r.user_name}</div>
                                <div class="text-muted fw-bold" style="font-size: 0.76rem;">${r.position_name}</div>
                            </td>
                            <td class="align-middle">${statusBadge}</td>
                            <td class="align-middle fw-bold text-dark font-monospace" style="font-size: 0.8rem;">${r.signed_at_th || '-'}</td>
                            <td class="align-middle">
                                ${r.comment_preset ? `<div class="fw-bold text-primary mb-0">${r.comment_preset}</div>` : ''}
                                ${r.comment_text ? `<div class="text-dark fst-italic" style="font-size: 0.8rem;">"${r.comment_text}"</div>` : (!r.comment_preset ? '<span class="text-muted">-</span>' : '')}
                            </td>
                        `;
                        routeTbody.appendChild(tr);
                    });

                    // 2. แถวขากลับ (ถ้ามี)
                    if (data.return_routes && data.return_routes.length > 0) {
                        data.return_routes.forEach(r => {
                            let statusBadge = '<span class="badge bg-secondary text-white fw-bold">รอส่งมอบ</span>';
                            if (r.status === 'signed') {
                                statusBadge = '<span class="badge bg-success text-white fw-bold shadow-xs px-2 py-1"><i class="bx bx-check-double me-1"></i>ส่งมอบแล้ว</span>';
                            } else if (r.status === 'active' || r.is_current) {
                                statusBadge = '<span class="badge bg-warning text-dark fw-bold shadow-xs px-2 py-1"><i class="bx bx-package me-1"></i>กำลังส่งมอบ</span>';
                            }

                            const tr = document.createElement('tr');
                            tr.style.backgroundColor = '#f0fdf4';
                            tr.innerHTML = `
                                <td class="text-center align-middle">
                                    <span class="badge bg-success text-white fw-bold px-2 py-1 shadow-xs" style="font-size: 0.72rem;">ขากลับ ⬅️</span>
                                </td>
                                <td class="text-center align-middle fw-bolder text-success fs-6">${r.step_order}</td>
                                <td class="align-middle">
                                    <div class="fw-bolder text-dark" style="font-size: 0.88rem;">${r.user_name}</div>
                                    <div class="text-success fw-bold" style="font-size: 0.76rem;">${r.position_name}</div>
                                </td>
                                <td class="align-middle">${statusBadge}</td>
                                <td class="align-middle fw-bold text-dark font-monospace" style="font-size: 0.8rem;">${r.signed_at_th || '-'}</td>
                                <td class="align-middle">
                                    ${r.comment_preset ? `<div class="fw-bold text-success mb-0">${r.comment_preset}</div>` : ''}
                                    ${r.comment_text ? `<div class="text-dark fst-italic" style="font-size: 0.8rem;">"${r.comment_text}"</div>` : (!r.comment_preset ? '<span class="text-muted">-</span>' : '')}
                                </td>
                            `;
                            routeTbody.appendChild(tr);
                        });
                    }

                    // Render Logs
                    const logList = document.getElementById('modalLogList');
                    logList.innerHTML = '';
                    if (data.logs.length === 0) {
                        logList.innerHTML = '<li class="text-secondary fw-medium">ไม่มีบันทึกประวัติเพิ่มเติม</li>';
                    } else {
                        data.logs.forEach(l => {
                            const li = document.createElement('li');
                            li.className = 'mb-1 pb-1 border-bottom border-light';
                            li.innerHTML = `
                                <span class="text-secondary fw-bold">[${l.created_at_th}]</span>
                                <strong class="text-dark">${l.user_name} (${l.action}):</strong>
                                <span class="text-dark">${l.detail || '-'}</span>
                            `;
                            logList.appendChild(li);
                        });
                    }

                    loading.style.display = 'none';
                    content.style.display = 'block';
                })
                .catch(err => {
                    console.error(err);
                    loading.style.display = 'none';
                    AppAlert.error('เกิดข้อผิดพลาดในการโหลดข้อมูลขั้นตอน', 'การเชื่อมต่อผิดพลาด');
                });
        }
    </script>
</body>
</html>
