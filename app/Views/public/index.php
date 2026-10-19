<!DOCTYPE html>
<html lang="th" class="light-style layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>ศูนย์ติดตามหนังสือราชการและสถานะงาน (School e-Document Tracking)</title>

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
            transition: all 0.25s ease-in-out;
            cursor: pointer;
            border: 1.5px solid #e2e8f0 !important;
            background: #ffffff !important;
        }

        .stat-sneat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.1) !important;
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

        <!-- Sneat Header Navbar (High Contrast & Detached) -->
        <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center glass-card my-3" id="layout-navbar" style="background: #ffffff !important;">
            <div class="container-fluid px-2">
                <a class="navbar-brand d-flex align-items-center me-3" href="<?= base_url('/') ?>">
                    <span class="app-brand-logo demo me-2">
                        <span class="badge bg-primary p-2 rounded-3 shadow-sm">
                            <i class="bx bxs-institution fs-3 text-white"></i>
                        </span>
                    </span>
                    <div>
                        <span class="fw-bolder fs-5 text-primary text-uppercase" style="letter-spacing: 0.5px;">School e-Office</span>
                        <small class="d-block text-secondary fw-semibold" style="font-size: 0.72rem; margin-top: -2px;">ระบบสารบรรณและติดตามงานราชการ</small>
                    </div>
                </a>

                <div class="collapse navbar-collapse d-none d-md-flex" id="navbar-collapse">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link active fw-bold text-primary px-3" href="<?= base_url('/') ?>">
                                <i class="bx bx-file-find me-1"></i> ติดตามหนังสือราชการ
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-dark fw-semibold px-3" href="javascript:void(0)" onclick="filterTableByType('in')">
                                <i class="bx bx-log-in-circle me-1 text-primary"></i> หนังสือรับ
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-dark fw-semibold px-3" href="javascript:void(0)" onclick="filterTableByType('out')">
                                <i class="bx bx-log-out-circle me-1 text-success"></i> หนังสือส่ง
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-dark fw-semibold px-3" href="javascript:void(0)" onclick="filterTableByType('order')">
                                <i class="bx bx-award me-1 text-warning"></i> คำสั่ง รร.
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-dark fw-semibold px-3" href="javascript:void(0)" onclick="filterTableByType('travel')">
                                <i class="bx bx-briefcase-alt-2 me-1 text-info"></i> ขอไปราชการ
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="navbar-nav flex-row align-items-center ms-auto">
                    <?php if ($isLoggedIn): ?>
                        <div class="d-flex align-items-center me-2">
                            <div class="text-end me-2 d-none d-sm-block">
                                <span class="badge bg-success text-white fw-bold px-2 py-1" style="font-size: 0.72rem;">เข้าสู่ระบบแล้ว</span>
                                <div class="fw-bold text-dark small"><?= esc($userFullName) ?></div>
                            </div>
                            <div class="avatar avatar-online">
                                <span class="avatar-initial rounded-circle bg-primary text-white fw-bold">
                                    <?= mb_substr(session()->get('first_name') ?? 'U', 0, 1) ?>
                                </span>
                            </div>
                        </div>
                        <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-primary btn-sm px-3 shadow-sm fw-semibold">
                            <i class="bx bx-grid-alt me-1"></i> แดชบอร์ดงานของฉัน
                        </a>
                    <?php else: ?>
                        <a href="<?= base_url('login') ?>" class="btn btn-primary btn-sm px-3 shadow-sm fw-semibold">
                            <i class="bx bx-log-in me-1"></i> เข้าสู่ระบบสำหรับบุคลากร
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>

        <!-- Main Body -->
        <div class="container-xxl">

            <!-- Hero Banner (High-Contrast Typography) -->
            <div class="card hero-sneat-card mb-4 border-0">
                <div class="card-body p-4 p-md-5">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <!-- High Contrast Top Badge -->
                            <div class="d-inline-flex align-items-center px-3 py-1 rounded-pill mb-3" style="background: rgba(0, 0, 0, 0.45); border: 1.5px solid rgba(255, 255, 255, 0.6);">
                                <i class="bx bx-check-shield text-warning me-1"></i>
                                <span class="small fw-bold text-white">Public Portal (ระบบตรวจสอบความคืบหน้าสำหรับประชาชน & ผู้รับบริการ)</span>
                            </div>
                            <h2 class="fw-bolder text-white mb-2" style="text-shadow: 0 2px 4px rgba(0,0,0,0.3);">ติดตามสถานะหนังสือราชการและขั้นตอนการดำเนินงาน</h2>
                            <p class="text-white mb-4 fw-medium" style="max-width: 620px; font-size: 1rem; text-shadow: 0 1px 3px rgba(0,0,0,0.3); opacity: 0.95;">
                                ระบบเปิดให้ประชาชนและบุคลากรติดตามการขับเคลื่อนเอกสารได้อย่างโปร่งใส สามารถตรวจสอบได้ทันทีว่าเรื่องอยู่ที่ขั้นตอนใด ใครกำลังพิจารณา และเสร็จสิ้นแล้วหรือไม่
                            </p>
                            <!-- Feature Pills with Pure White Background and Dark Text -->
                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge px-3 py-2 rounded-pill shadow-sm" style="background: #ffffff; color: #1e293b; font-weight: 700; border: 1.5px solid #cbd5e1; font-size: 0.82rem;">
                                    <i class="bx bx-git-commit me-1 text-primary fs-6 align-middle"></i> เช็กขั้นตอนแบบเรียลไทม์
                                </span>
                                <span class="badge px-3 py-2 rounded-pill shadow-sm" style="background: #ffffff; color: #1e293b; font-weight: 700; border: 1.5px solid #cbd5e1; font-size: 0.82rem;">
                                    <i class="bx bx-lock-alt me-1 text-warning fs-6 align-middle"></i> สิทธิ์ดูอย่างเดียว (Read-only) ปลอดภัย
                                </span>
                            </div>
                        </div>
                        <div class="col-lg-4 text-center d-none d-lg-block">
                            <img src="<?= base_url('assets/img/illustrations/man-with-laptop.png') ?>" alt="Tracking Illustration" class="img-fluid" style="max-height: 195px; filter: drop-shadow(0 10px 15px rgba(0,0,0,0.3));" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search & Filter Card -->
            <div class="card glass-card mb-4">
                <div class="card-body p-4">
                    <form action="<?= base_url('/') ?>" method="GET" class="row g-3 align-items-end">
                        <div class="col-lg-5 col-md-6">
                            <label class="form-label text-dark small fw-bold">
                                <i class="bx bx-search-alt me-1 text-primary"></i> ค้นหาเอกสาร
                            </label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text bg-white border-end-0"><i class="bx bx-search text-primary"></i></span>
                                <input type="text" id="searchInput" name="search" class="form-control border-start-0 text-dark fw-medium" placeholder="พิมพ์เลขที่หนังสือ, เลขทะเบียน รร., หรือชื่อเรื่อง..." value="<?= esc($search) ?>" />
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3">
                            <label class="form-label text-dark small fw-bold">
                                <i class="bx bx-folder me-1 text-primary"></i> ประเภทหนังสือ
                            </label>
                            <select name="type" id="typeSelect" class="form-select text-dark fw-medium">
                                <option value="">-- ทั้งหมดทุกประเภท --</option>
                                <option value="in" <?= $currentType === 'in' ? 'selected' : '' ?>>หนังสือรับ (ภายนอก)</option>
                                <option value="out" <?= $currentType === 'out' ? 'selected' : '' ?>>หนังสือส่ง (ภายนอก)</option>
                                <option value="internal" <?= $currentType === 'internal' ? 'selected' : '' ?>>บันทึกข้อความ (ภายใน)</option>
                                <option value="order" <?= $currentType === 'order' ? 'selected' : '' ?>>คำสั่งโรงเรียน</option>
                                <option value="travel" <?= $currentType === 'travel' ? 'selected' : '' ?>>ขออนุมัติไปราชการ (Travel)</option>
                                <option value="circular" <?= $currentType === 'circular' ? 'selected' : '' ?>>หนังสือเวียน</option>
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-3">
                            <label class="form-label text-dark small fw-bold">
                                <i class="bx bx-flag me-1 text-primary"></i> สถานะเอกสาร
                            </label>
                            <select name="status" id="statusSelect" class="form-select text-dark fw-medium">
                                <option value="">-- ทุกสถานะ --</option>
                                <option value="routing" <?= $currentStatus === 'routing' ? 'selected' : '' ?>>อยู่ระหว่างดำเนินเรื่อง</option>
                                <option value="completed" <?= $currentStatus === 'completed' ? 'selected' : '' ?>>เสร็จสิ้น/อนุมัติแล้ว</option>
                                <option value="returned" <?= $currentStatus === 'returned' ? 'selected' : '' ?>>ส่งคืนแก้ไข</option>
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-12 d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100 fw-bold">
                                <i class="bx bx-search me-1"></i> ค้นหา
                            </button>
                            <?php if (!empty($search) || !empty($currentType) || !empty($currentStatus)): ?>
                                <a href="<?= base_url('/') ?>" class="btn btn-outline-secondary fw-semibold" title="ล้างค่าการค้นหา">
                                    <i class="bx bx-reset"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <!-- High Contrast 4 Statistics Cards -->
            <div class="row g-3 mb-4">
                <!-- ทั้งหมด -->
                <div class="col-sm-6 col-lg-3">
                    <div class="card stat-sneat-card h-100" onclick="window.location.href='<?= base_url('/') ?>'">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="avatar flex-shrink-0">
                                    <span class="avatar-initial rounded bg-primary text-white"><i class="bx bx-folder-open fs-3"></i></span>
                                </div>
                                <span class="badge px-2 py-1 rounded-pill" style="background: #eef2ff; color: #3730a3; font-weight: 700; border: 1px solid #c7d2fe;">ทั้งหมด</span>
                            </div>
                            <span class="d-block mb-1 text-dark fw-bold small">ทะเบียนหนังสือรวม</span>
                            <h3 class="card-title mb-0 fw-bolder" style="color: #0f172a;"><?= number_format($totalCount) ?> <small class="fs-6 text-secondary fw-normal">เรื่อง</small></h3>
                        </div>
                    </div>
                </div>

                <!-- กำลังพิจารณา (High Contrast: Dark Amber on Soft Yellow) -->
                <div class="col-sm-6 col-lg-3">
                    <div class="card stat-sneat-card h-100" onclick="window.location.href='<?= base_url('?status=routing') ?>'">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="avatar flex-shrink-0">
                                    <span class="avatar-initial rounded" style="background: #b45309; color: #ffffff;"><i class="bx bx-time-five fs-3"></i></span>
                                </div>
                                <span class="badge px-2 py-1 rounded-pill" style="background: #fef3c7; color: #92400e; font-weight: 700; border: 1px solid #fde68a;">กำลังเดินเรื่อง</span>
                            </div>
                            <span class="d-block mb-1 text-dark fw-bold small">อยู่ระหว่างเสนอพิจารณา</span>
                            <h3 class="card-title mb-0 fw-bolder" style="color: #b45309;"><?= number_format($routingCount) ?> <small class="fs-6 text-secondary fw-normal">เรื่อง</small></h3>
                        </div>
                    </div>
                </div>

                <!-- เสร็จสิ้น (High Contrast: Dark Forest Green) -->
                <div class="col-sm-6 col-lg-3">
                    <div class="card stat-sneat-card h-100" onclick="window.location.href='<?= base_url('?status=completed') ?>'">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="avatar flex-shrink-0">
                                    <span class="avatar-initial rounded bg-success text-white"><i class="bx bx-check-circle fs-3"></i></span>
                                </div>
                                <span class="badge px-2 py-1 rounded-pill" style="background: #dcfce7; color: #166534; font-weight: 700; border: 1px solid #bbf7d0;">อนุมัติแล้ว</span>
                            </div>
                            <span class="d-block mb-1 text-dark fw-bold small">ดำเนินการเสร็จสิ้น</span>
                            <h3 class="card-title mb-0 fw-bolder" style="color: #15803d;"><?= number_format($completedCount) ?> <small class="fs-6 text-secondary fw-normal">เรื่อง</small></h3>
                        </div>
                    </div>
                </div>

                <!-- ส่งคืน (High Contrast: Deep Crimson Red) -->
                <div class="col-sm-6 col-lg-3">
                    <div class="card stat-sneat-card h-100" onclick="window.location.href='<?= base_url('?status=returned') ?>'">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="avatar flex-shrink-0">
                                    <span class="avatar-initial rounded bg-danger text-white"><i class="bx bx-revision fs-3"></i></span>
                                </div>
                                <span class="badge px-2 py-1 rounded-pill" style="background: #fee2e2; color: #991b1b; font-weight: 700; border: 1px solid #fecaca;">ส่งคืน</span>
                            </div>
                            <span class="d-block mb-1 text-dark fw-bold small">ส่งคืนให้แก้ไข</span>
                            <h3 class="card-title mb-0 fw-bolder" style="color: #b91c1c;"><?= number_format($returnedCount) ?> <small class="fs-6 text-secondary fw-normal">เรื่อง</small></h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Readonly Policy Alert Banner (High Contrast Warm Yellow) -->
            <div class="alert d-flex align-items-center justify-content-between mb-4 shadow-sm" role="alert" style="background: #fffbe6; border: 1.5px solid #ffe58f; color: #614700; border-radius: 0.75rem;">
                <div class="d-flex align-items-center">
                    <i class="bx bxs-info-circle fs-3 me-2" style="color: #d48806;"></i>
                    <div class="small fw-medium">
                        <strong class="fw-bold" style="color: #382400;">คำชี้แจงการใช้งานสำหรับบุคคลทั่วไป:</strong> หน้านี้เปิดให้ตรวจสอบความคืบหน้าของเอกสาร (Read-only) โดยบุคคลทั่วไปไม่สามารถเซ็นเกษียนหรือแก้ไขข้อมูลได้ หากท่านเป็นเจ้าหน้าที่หรือผู้บริหาร <a href="<?= base_url('login') ?>" class="alert-link text-decoration-underline fw-bold" style="color: #002c8a;">คลิกที่นี่เพื่อเข้าสู่ระบบ</a>
                    </div>
                </div>
                <span class="badge px-3 py-2 fw-bold d-none d-md-inline-block shadow-sm" style="background: #faad14; color: #000000; font-size: 0.75rem;">
                    <i class="bx bx-lock-alt me-1"></i> โหมดอ่านอย่างเดียว
                </span>
            </div>

            <!-- Nav Pills Tabs (High Contrast) -->
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                <ul class="nav nav-pills flex-column flex-sm-row gap-2">
                    <li class="nav-item">
                        <a class="nav-link custom-nav-pill <?= empty($currentType) ? 'active' : '' ?>" href="<?= base_url('/') ?>">
                            <i class="bx bx-grid-alt me-1"></i> ทั้งหมด
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link custom-nav-pill <?= $currentType === 'in' ? 'active' : '' ?>" href="<?= base_url('?type=in') ?>">
                            <i class="bx bx-log-in-circle me-1"></i> หนังสือรับ
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link custom-nav-pill <?= $currentType === 'out' ? 'active' : '' ?>" href="<?= base_url('?type=out') ?>">
                            <i class="bx bx-log-out-circle me-1"></i> หนังสือส่ง
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link custom-nav-pill <?= $currentType === 'internal' ? 'active' : '' ?>" href="<?= base_url('?type=internal') ?>">
                            <i class="bx bx-message-detail me-1"></i> บันทึกข้อความ
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link custom-nav-pill <?= $currentType === 'order' ? 'active' : '' ?>" href="<?= base_url('?type=order') ?>">
                            <i class="bx bx-award me-1"></i> คำสั่งโรงเรียน
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link custom-nav-pill <?= $currentType === 'travel' ? 'active' : '' ?>" href="<?= base_url('?type=travel') ?>">
                            <i class="bx bx-briefcase-alt-2 me-1"></i> ขออนุมัติไปราชการ
                        </a>
                    </li>
                </ul>

                <div class="text-dark fw-bold small mt-2 mt-sm-0 bg-white px-3 py-2 rounded border">
                    แสดงรายการหนังสือ <span class="badge bg-primary px-2 py-1"><?= count($documents) ?></span> รายการ
                </div>
            </div>

            <!-- Document Table Card (High Contrast) -->
            <div class="card glass-card mb-4 border-0">
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

        </div>

        <!-- Footer -->
        <footer class="footer bg-white border-top py-4 mt-5 glass-card">
            <div class="container-xxl text-center small" style="color: #334155;">
                <div class="fw-bold">
                    © <?= date('Y') + 543 ?> <strong>ระบบสารบรรณและเกษียนหนังสืออิเล็กทรอนิกส์ (School e-Saraban)</strong>
                </div>
                <div class="mt-1 fw-medium text-secondary">
                    พัฒนาเพื่อการติดตามงานราชการที่โปร่งใส ตรวจสอบได้ทุกขั้นตอน
                </div>
            </div>
        </footer>

    </div>

    <!-- Quick Workflow Tracker Modal (High Contrast & Extra Wide) -->
    <div class="modal fade" id="trackModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width: 1200px;">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 1rem; background: #ffffff;">
                <div class="modal-header border-bottom py-3" style="background: #f8fafc;">
                    <div class="d-flex align-items-center">
                        <div class="avatar flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-primary text-white"><i class="bx bx-git-commit fs-3"></i></span>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark mb-0">สถานะและเส้นทางเดินหนังสือราชการ</h5>
                            <small class="text-secondary fw-semibold" id="modalDocSubheader">ระบบติดตามแบบสาธารณะ (Read-only Workflow)</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body py-4">
                    <!-- Loading Spinner -->
                    <div id="modalLoading" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">กำลังโหลดข้อมูล...</span>
                        </div>
                        <p class="text-dark fw-semibold small mt-2">กำลังดึงข้อมูลขั้นตอนและไทม์ไลน์...</p>
                    </div>

                    <!-- Modal Content Container -->
                    <div id="modalContent" style="display: none;">
                        <!-- Doc Info Card (High Contrast) -->
                        <div class="card border p-3 mb-4" style="background: #f8fafc; border: 1.5px solid #cbd5e1 !important; border-radius: 0.75rem;">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <span id="modalDocType"></span>
                                    <span id="modalUrgency"></span>
                                    <strong class="fs-5 text-dark ms-1" id="modalDocNo"></strong>
                                </div>
                                <span id="modalDocStatus"></span>
                            </div>
                            <h5 class="fw-bold text-primary mb-2" id="modalDocTitle"></h5>
                            <div class="row g-2 small text-dark fw-medium">
                                <div class="col-sm-6">
                                    <i class="bx bx-barcode me-1 text-primary"></i> เลขทะเบียน รร.: <strong class="text-dark" id="modalRegNo"></strong>
                                </div>
                                <div class="col-sm-6">
                                    <i class="bx bx-calendar me-1 text-primary"></i> ลงวันที่: <strong class="text-dark" id="modalDocDate"></strong>
                                </div>
                                <div class="col-sm-6">
                                    <i class="bx bx-paper-plane me-1 text-primary"></i> จาก: <strong class="text-dark" id="modalFromOrg"></strong>
                                </div>
                                <div class="col-sm-6">
                                    <i class="bx bx-building me-1 text-primary"></i> ถึง: <strong class="text-dark" id="modalToOrg"></strong>
                                </div>
                            </div>
                        </div>

                        <!-- Stepper Visual (Outbound) -->
                        <div class="mb-4 step-stepper-box" style="border-left: 6px solid #4338ca; border-top: 1.5px solid #cbd5e1; border-right: 1.5px solid #cbd5e1; border-bottom: 1.5px solid #cbd5e1; background: #fafbff;">
                            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom" style="border-color: #e2e8f0 !important;">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-primary text-white fw-bold px-2 py-1 shadow-sm" style="font-size: 0.85rem;">
                                        <i class="bx bx-right-arrow-alt me-1 fs-6 align-middle"></i> ขาไป ➔
                                    </span>
                                    <div>
                                        <h6 class="text-dark fw-bolder mb-0" style="font-size: 0.95rem;">
                                            เส้นทางเดินหนังสือขออนุมัติและเกษียนลงนาม
                                        </h6>
                                        <small class="text-muted fw-semibold" style="font-size: 0.78rem;">ส่งต่อตามลำดับชั้นบังคับบัญชาเพื่อพิจารณาอนุมัติ</small>
                                    </div>
                                </div>
                                <span class="badge bg-white text-primary border border-primary fw-bold px-3 py-1 shadow-xs" style="font-size: 0.76rem;">
                                    สายเสนอเรื่อง
                                </span>
                            </div>
                            <div class="step-stepper-modal-wrapper">
                                <div class="step-stepper-modal" id="modalStepper">
                                    <!-- Steps rendered dynamically -->
                                </div>
                            </div>
                        </div>

                        <!-- Stepper Visual (Return: ขากลับ ส่งคืนตัวจริง) -->
                        <div class="mb-4 step-stepper-box" id="modalReturnSection" style="display: none; border-left: 6px solid #16a34a; border-top: 1.5px solid #bbf7d0; border-right: 1.5px solid #bbf7d0; border-bottom: 1.5px solid #bbf7d0; background: #f0fdf4;">
                            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom" style="border-color: #bbf7d0 !important;">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-success text-white fw-bold px-2 py-1 shadow-sm" style="font-size: 0.85rem;">
                                        <i class="bx bx-left-arrow-alt me-1 fs-6 align-middle"></i> ขากลับ ⬅️
                                    </span>
                                    <div>
                                        <h6 class="text-dark fw-bolder mb-0" style="font-size: 0.95rem;">
                                            เส้นทางเดินหนังสือส่งมอบฉบับจริงคืนโรงเรียน
                                        </h6>
                                        <small class="text-muted fw-semibold" style="font-size: 0.78rem;">ส่งคืนแฟ้มฉบับจริงที่ลงนามแล้วย้อนกลับสู่โรงเรียนผู้ขอ</small>
                                    </div>
                                </div>
                                <span class="badge bg-white text-success border border-success fw-bold px-3 py-1 shadow-xs" style="font-size: 0.76rem;">
                                    ส่งมอบเอกสารตัวจริง
                                </span>
                            </div>
                            <div class="step-stepper-modal-wrapper">
                                <div class="step-stepper-modal return-flow flex-row-reverse" id="modalReturnStepper">
                                    <!-- Return Steps rendered dynamically -->
                                </div>
                            </div>
                        </div>

                        <!-- Step Details Table -->
                        <div class="mb-4">
                            <h6 class="text-dark fw-bold text-uppercase mb-2" style="font-size: 0.85rem; letter-spacing: 0.5px;">
                                <i class="bx bx-list-check text-primary me-1"></i> รายละเอียดบันทึกการพิจารณาแต่ละขั้นตอน
                            </h6>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead style="background: #f1f5f9;">
                                        <tr class="small text-dark fw-bold">
                                            <th class="text-center" style="width: 70px;">สายงาน</th>
                                            <th class="text-center" style="width: 50px;">ขั้นที่</th>
                                            <th>ผู้พิจารณา / ตำแหน่ง</th>
                                            <th style="width: 140px;">สถานะ</th>
                                            <th style="width: 150px;">วันที่ดำเนินการ</th>
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
                            <h6 class="text-dark fw-bold text-uppercase mb-2" style="font-size: 0.85rem; letter-spacing: 0.5px;">
                                <i class="bx bx-history text-primary me-1"></i> ประวัติความเคลื่อนไหวของเอกสาร (History Logs)
                            </h6>
                            <div class="p-3 rounded" style="background: #f8fafc; border: 1.5px solid #cbd5e1; max-height: 150px; overflow-y: auto;">
                                <ul class="list-unstyled mb-0 small text-dark" id="modalLogList">
                                    <!-- Logs rendered dynamically -->
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-top py-2 bg-light d-flex justify-content-between">
                    <div class="small fw-bold text-dark">
                        <i class="bx bx-lock-alt me-1 text-warning fs-6 align-middle"></i> สิทธิ์ดูอย่างเดียว หากต้องการลงนาม กรุณาเข้าสู่ระบบ
                    </div>
                    <div class="d-flex gap-2">
                        <a id="modalDetailLink" href="#" class="btn btn-outline-primary btn-sm fw-bold">
                            <i class="bx bx-file me-1"></i> เปิดดูหน้าเต็ม
                        </a>
                        <button type="button" class="btn btn-secondary btn-sm fw-bold" data-bs-dismiss="modal">ปิดหน้าต่าง</button>
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
                            <div class="step-title-text mb-1">${r.step_order}. ${r.position_name}</div>
                            <div class="mb-1">
                                <span class="badge ${statusBadge} fw-bold shadow-xs px-2 py-0.5" style="font-size: 0.72rem;">${statusText}</span>
                            </div>
                            ${r.user_name && r.user_name !== '-' ? `
                                <div class="step-user-badge mt-1" title="${r.user_name}">
                                    <i class="bx bx-user me-1 text-primary"></i>${r.user_name}
                                </div>
                            ` : `<div class="text-muted small mt-1" style="font-size: 0.7rem;">-</div>`}
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
                                <div class="step-title-text mb-1" style="color: #14532d !important;">${r.position_name}</div>
                                <div class="mb-1">
                                    <span class="badge ${statusBadge} fw-bold shadow-xs px-2 py-0.5" style="font-size: 0.72rem;">${statusText}</span>
                                </div>
                                ${r.user_name && r.user_name !== '-' ? `
                                    <div class="step-user-badge mt-1 border-success border-opacity-50" title="${r.user_name}">
                                        <i class="bx bx-user-check me-1 text-success"></i>${r.user_name}
                                    </div>
                                ` : `<div class="text-muted small mt-1" style="font-size: 0.7rem;">-</div>`}
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
