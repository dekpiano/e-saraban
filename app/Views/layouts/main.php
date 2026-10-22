<!DOCTYPE html>
<html lang="th" class="light-style layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title><?= $this->renderSection('title') ?> | ระบบงานสารบรรณ สกจ.</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= base_url('assets/img/favicon/favicon.ico') ?>" />

    <!-- Google Fonts: K2D & Sarabun -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=K2D:wght@200;300;400;500;600;700;800&family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

    <!-- Official Thai Government Font: TH SarabunIT๙, TH Sarabun PSK & TH Sarabun New (Embedded Data-URI) -->
    <link rel="stylesheet" href="<?= base_url('assets/vendor/fonts/sarabun-embedded.css?v=' . (file_exists(FCPATH . 'assets/vendor/fonts/sarabun-embedded.css') ? filemtime(FCPATH . 'assets/vendor/fonts/sarabun-embedded.css') : time())) ?>" />

    <!-- Boxicons Icons -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />

    <!-- Sneat Core & Vendor CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/vendor/css/core.css') ?>" class="template-customizer-core-css" />
    <link rel="stylesheet" href="<?= base_url('assets/css/demo.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') ?>" />

    <!-- Flatpickr Datepicker CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/airbnb.css" />

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" />

    <style>
        .flatpickr-calendar, .flatpickr-calendar input, .flatpickr-month, .flatpickr-weekdays, .flatpickr-days {
            font-family: 'K2D', sans-serif !important;
            border-radius: 12px !important;
        }
        .flatpickr-calendar.buddhist-calendar-flatpickr {
            box-shadow: 0 10px 30px rgba(50, 50, 93, 0.15), 0 5px 15px rgba(0, 0, 0, 0.08) !important;
            border: 1px solid #e2e8f0 !important;
        }
        .buddhist-calendar-flatpickr .numInputWrapper {
            width: 72px !important;
        }
        .buddhist-calendar-flatpickr input.cur-year {
            font-weight: 700 !important;
            color: #2b3445 !important;
            font-size: 1.05rem !important;
            text-align: center !important;
        }
        .buddhist-calendar-flatpickr .flatpickr-monthDropdown-months {
            font-weight: 700 !important;
            color: #2b3445 !important;
            font-size: 1.05rem !important;
        }
        .flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange {
            background: #696cff !important;
            border-color: #696cff !important;
            color: #ffffff !important;
            font-weight: 700 !important;
        }
        .flatpickr-day.today {
            border-color: #696cff !important;
            color: #696cff !important;
        }
        .flatpickr-day.today.selected {
            color: #ffffff !important;
        }
        .flatpickr-thai-input {
            background-color: #ffffff !important;
            cursor: pointer;
            font-weight: 500;
        }
        body, html, input, button, select, textarea, .menu, .navbar, .card-title, h1, h2, h3, h4, h5, h6, .form-control, .form-select, .btn, .badge {
            font-family: 'K2D', sans-serif !important;
        }
        .thai-doc-content {
            font-family: 'K2D', 'Sarabun', sans-serif !important;
        }
        .required::after {
            content: " *";
            color: #ff3e1d;
            font-weight: bold;
        }
        .signature-pad-container {
            border: 2px dashed #d9dee3;
            border-radius: 8px;
            background: #ffffff;
            touch-action: none;
            position: relative;
        }
        .signature-preview {
            max-height: 80px;
            max-width: 180px;
            object-fit: contain;
            display: inline-block;
        }
        .step-stepper {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin-bottom: 2rem;
        }
        .step-stepper::before {
            content: '';
            position: absolute;
            top: 24px;
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
            background: #ffffff;
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
        }
        .step-bubble.active {
            background-color: #696cff;
            color: #fff;
            animation: pulse-step 2s infinite;
        }
        .step-bubble.signed {
            background-color: #71dd37;
            color: #fff;
        }
        .step-bubble.pending {
            background-color: #e7e7ff;
            color: #696cff;
        }
        .step-bubble.returned {
            background-color: #ff3e1d;
            color: #fff;
        }
        @keyframes pulse-step {
            0% { box-shadow: 0 0 0 0 rgba(105, 108, 255, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(105, 108, 255, 0); }
            100% { box-shadow: 0 0 0 0 rgba(105, 108, 255, 0); }
        }
        .badge-urgent {
            animation: pulse-urgent 1.5s infinite;
        }
        @keyframes pulse-urgent {
            0% { opacity: 1; }
            50% { opacity: 0.6; }
            100% { opacity: 1; }
        }
        .bg-menu-theme .menu-inner > .menu-item.active {
            position: relative;
        }
        .bg-menu-theme .menu-inner > .menu-item.active > .menu-link {
            background: linear-gradient(72.47deg, #696cff 22.16%, rgba(105, 108, 255, 0.85) 76.47%) !important;
            color: #ffffff !important;
            font-weight: 600 !important;
            box-shadow: 0 2px 8px 0 rgba(105, 108, 255, 0.45) !important;
            border-radius: 0.375rem !important;
        }
        .bg-menu-theme .menu-inner > .menu-item.active > .menu-link i,
        .bg-menu-theme .menu-inner > .menu-item.active > .menu-link div,
        .bg-menu-theme .menu-inner > .menu-item.active > .menu-link i.text-warning,
        .bg-menu-theme .menu-inner > .menu-item.active > .menu-link i.text-primary,
        .bg-menu-theme .menu-inner > .menu-item.active > .menu-link i.text-success,
        .bg-menu-theme .menu-inner > .menu-item.active > .menu-link i.text-info {
            color: #ffffff !important;
        }

        /* High Contrast System for Badges & Labels (Tri-Master Color Contrast Rule) */
        .bg-label-primary {
            background-color: #e7e7ff !important;
            color: #4f46e5 !important;
        }
        .bg-label-secondary {
            background-color: #f1f3f6 !important;
            color: #475569 !important;
        }
        .bg-label-success {
            background-color: #e8fadf !important;
            color: #1b873f !important;
        }
        .bg-label-info {
            background-color: #d7f5fc !important;
            color: #0369a1 !important;
        }
        .bg-label-warning {
            background-color: #fff2d6 !important;
            color: #92400e !important;
        }
        .bg-label-danger {
            background-color: #ffe0db !important;
            color: #b91c1c !important;
        }
        .bg-label-dark {
            background-color: #e2e8f0 !important;
            color: #0f172a !important;
        }

        /* Solid Badges & Buttons contrast enforcement */
        .badge.bg-primary { background-color: #4f46e5 !important; color: #ffffff !important; }
        .badge.bg-success { background-color: #16a34a !important; color: #ffffff !important; }
        .badge.bg-danger { background-color: #dc2626 !important; color: #ffffff !important; }
        .badge.bg-warning { background-color: #f59e0b !important; color: #1e293b !important; }
        .badge.bg-info { background-color: #0284c7 !important; color: #ffffff !important; }
        .badge.bg-secondary { background-color: #64748b !important; color: #ffffff !important; }

        /* General text contrast helper classes */
        .text-muted { color: #566a7f !important; }
        .table thead th { color: #334155 !important; }

        /* Compact Detached Navbar */
        .layout-navbar.navbar-detached {
            min-height: 54px !important;
            height: 54px !important;
            padding-top: 0.25rem !important;
            padding-bottom: 0.25rem !important;
            margin-top: 0.65rem !important;
            margin-bottom: 0.85rem !important;
        }

        /* -------------------------------------------------------------
           PREMIUM SNEAT MODAL DESIGN SYSTEM
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
        .modal-header.bg-primary .btn-close,
        .modal-header.bg-success .btn-close,
        .modal-header.bg-danger .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
            opacity: 0.85;
        }
        .modal-header.bg-primary .btn-close:hover,
        .modal-header.bg-success .btn-close:hover,
        .modal-header.bg-danger .btn-close:hover {
            opacity: 1;
            background-color: rgba(255, 255, 255, 0.2);
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
        }

        /* SweetAlert2 Highest Z-Index (swal2 อยู่บนสุดเสมอ เหนือ modal, backdrop และ navbar) */
        .swal2-container {
            z-index: 100000 !important;
        }
        .swal2-popup {
            font-family: 'K2D', sans-serif !important;
        }

        /* Universal Drag & Drop Upload Styling */
        .dropzone-box {
            border: 2px dashed #696cff !important;
            border-radius: 14px !important;
            background: #f8f9ff !important;
            padding: 1.75rem 1.25rem !important;
            text-align: center !important;
            cursor: pointer !important;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
            position: relative !important;
        }
        .dropzone-box:hover, .dropzone-box.dragover {
            background: #eef0ff !important;
            border-color: #5f61e6 !important;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(105, 108, 255, 0.18) !important;
        }
        .dropzone-box.is-invalid {
            border-color: #ff3e1d !important;
            background: #fff5f4 !important;
        }
        .dropzone-preview {
            display: none;
            border: 1.5px solid #d9dee3;
            border-radius: 12px;
            padding: 0.85rem 1rem;
            background: #ffffff;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
            animation: fadeIn 0.3s ease-in-out;
        }
    </style>

    <!-- Core jQuery in head so any page scripts or plugins have access immediately -->
    <script src="<?= base_url('assets/vendor/libs/jquery/jquery.js') ?>"></script>

    <!-- Helpers -->
    <script src="<?= base_url('assets/vendor/js/helpers.js') ?>"></script>
    <script src="<?= base_url('assets/js/config.js') ?>"></script>

    <?= $this->renderSection('styles') ?>
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">

            <!-- Menu / Sidebar -->
            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                <div class="app-brand demo py-3">
                    <a href="<?= base_url('admin/dashboard') ?>" class="app-brand-link">
                        <span class="app-brand-logo demo me-2">
                            <span class="badge bg-primary p-2 rounded-3">
                                <i class="bx bxs-institution fs-3 text-white"></i>
                            </span>
                        </span>
                        <div class="d-flex flex-column">
                            <span class="app-brand-text demo menu-text fw-bolder fs-5 text-primary">ระบบงานสารบรรณ สกจ.</span>
                            <small class="text-muted" style="font-size: 0.70rem;">โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ)</small>
                        </div>
                    </a>

                    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
                        <i class="bx bx-chevron-left bx-sm align-middle"></i>
                    </a>
                </div>

                <div class="menu-inner-shadow"></div>

                <?php
                $rawUri = trim(uri_string(), '/');
                $currentUri = preg_replace('#^admin/?#', '', $rawUri);
                $customActive = trim($this->renderSection('activeMenu') ?? '');

                // Helper to check active state across routes & subroutes
                $isActiveMenu = function(string $key, array $patterns = []) use ($customActive, $currentUri) {
                    if ($customActive !== '') {
                        return ($customActive === $key) ? 'active' : '';
                    }
                    foreach ($patterns as $pattern) {
                        if ($pattern === $currentUri) {
                            return 'active';
                        }
                        if (str_ends_with($pattern, '*') && str_starts_with($currentUri, rtrim($pattern, '*'))) {
                            return 'active';
                        }
                    }
                    return '';
                };

                // คำนวณจำนวนเอกสารและสถานะค้างสำหรับ Badge เมนูตามสิทธิ์ผู้ใช้งาน
                $userId   = (int) (session()->get('user_id') ?? 0);
                $userRole = (string) (session()->get('role') ?? '');
                $pendingSignCount    = 0;
                $pendingSarabanCount = 0;

                if ($userId > 0) {
                    try {
                        $db = \Config\Database::connect();
                        // 1. งานรอฉันลงนาม/เกษียน
                        $pendingSignCount = $db->table('document_routes')
                            ->join('documents', 'documents.id = document_routes.document_id')
                            ->where('document_routes.user_id', $userId)
                            ->where('document_routes.status', 'pending')
                            ->where('documents.current_step = document_routes.step_order')
                            ->where('documents.status', 'routing')
                            ->countAllResults();

                        // 2. สำหรับสารบรรณ / แอดมิน: บันทึกข้อความรอตรวจและออกเลข
                        if (in_array($userRole, ['saraban', 'admin', 'super_admin'], true)) {
                            $pendingSarabanCount = $db->table('documents')
                                ->where('doc_type', 'internal')
                                ->where('status', 'pending_saraban')
                                ->countAllResults();
                        }
                    } catch (\Throwable $e) {
                        // ป้องกันกรณี database เกิด exception
                    }
                }
                ?>
                <?php if (session()->get('logged_in')): ?>
                <ul class="menu-inner py-1">
                    <!-- Dashboard -->
                    <li class="menu-item <?= $isActiveMenu('dashboard', ['', 'dashboard']) ?>">
                        <a href="<?= base_url('admin/dashboard') ?>" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-home-circle text-primary"></i>
                            <div>แดชบอร์ดภาพรวม</div>
                        </a>
                    </li>

                    <li class="menu-item <?= $isActiveMenu('center', ['center', 'center/*']) ?>">
                        <a href="<?= base_url('admin/center') ?>" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-search-alt text-info"></i>
                            <div>ศูนย์กลางค้นหาเอกสาร</div>
                        </a>
                    </li>

                    <!-- ==========================================
                         งานของฉันและการติดตาม (Personal Workspace)
                         ========================================== -->
                    <li class="menu-header small text-uppercase">
                        <span class="menu-header-text">งานของฉันและการติดตาม</span>
                    </li>
                    <li class="menu-item <?= $isActiveMenu('documents_inbox', ['documents/inbox', 'documents/inbox/*']) ?>">
                        <a href="<?= base_url('admin/documents/inbox') ?>" class="menu-link d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <i class="menu-icon tf-icons bx bx-edit-alt text-warning"></i>
                                <div>รอฉันลงนาม / เกษียน</div>
                            </div>
                            <?php if ($pendingSignCount > 0): ?>
                                <span class="badge rounded-pill bg-danger text-white fw-bold px-2 py-1 shadow-sm" style="font-size: 0.72rem;">
                                    <?= $pendingSignCount ?>
                                </span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="menu-item <?= $isActiveMenu('documents_my', ['documents/my', 'documents/my/*']) ?>">
                        <a href="<?= base_url('admin/documents/my') ?>" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-send text-info"></i>
                            <div>หนังสือที่ฉันสร้าง / ติดตาม</div>
                        </a>
                    </li>
                    <li class="menu-item <?= $isActiveMenu('documents_signed', ['documents/signed', 'documents/signed/*']) ?>">
                        <a href="<?= base_url('admin/documents/signed') ?>" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-check-double text-success"></i>
                            <div>หนังสือที่ฉันลงนามแล้ว</div>
                        </a>
                    </li>

                    <!-- ==========================================
                         ประเภทหนังสือราชการ (6 ชนิดตามระเบียบงานสารบรรณ)
                         ========================================== -->
                    <li class="menu-header small text-uppercase">
                        <span class="menu-header-text">ประเภทหนังสือราชการ (๖ ชนิด)</span>
                    </li>

                    <!-- 1. หนังสือภายนอก -->
                    <li class="menu-item <?= str_starts_with($currentUri, 'documents') && !in_array($currentUri, ['documents/inbox', 'documents/my', 'documents/signed']) && !str_starts_with($currentUri, 'documents/inbox/') && !str_starts_with($currentUri, 'documents/my/') && !str_starts_with($currentUri, 'documents/signed/') && (string) service('request')->getGet('type') !== 'order' ? 'open' : '' ?>">
                        <a href="javascript:void(0)" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons bx bx-mail-send text-info"></i>
                            <div>1. หนังสือภายนอก</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item <?= $currentUri === 'documents' && (string) service('request')->getGet('type') === 'in' ? 'active' : '' ?>">
                                <a href="<?= base_url('admin/documents?type=in') ?>" class="menu-link">
                                    <div>ทะเบียนหนังสือรับ</div>
                                </a>
                            </li>
                            <li class="menu-item <?= $currentUri === 'documents' && (string) service('request')->getGet('type') === 'out' ? 'active' : '' ?>">
                                <a href="<?= base_url('admin/documents?type=out') ?>" class="menu-link">
                                    <div>ทะเบียนหนังสือส่ง</div>
                                </a>
                            </li>
                            <li class="menu-item <?= $currentUri === 'documents/create' ? 'active' : '' ?>">
                                <a href="<?= base_url('admin/documents/create') ?>" class="menu-link">
                                    <div>ลงทะเบียน / ส่งหนังสือ</div>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- 2. หนังสือภายใน -->
                    <li class="menu-item <?= str_starts_with($currentUri, 'memos') ? 'open' : '' ?>">
                        <a href="javascript:void(0)" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons bx bx-notepad text-primary"></i>
                            <div>2. หนังสือภายใน</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item <?= $isActiveMenu('memos_create', ['memos/create', 'memos/create/*']) ?>">
                                <a href="<?= base_url('admin/memos/create') ?>" class="menu-link">
                                    <div>เขียนบันทึกข้อความ</div>
                                </a>
                            </li>
                            <li class="menu-item <?= ($currentUri === 'memos' || preg_match('#^memos/[0-9]+$#', $currentUri)) ? 'active' : '' ?>">
                                <a href="<?= base_url('admin/memos') ?>" class="menu-link">
                                    <div>ทะเบียนบันทึกข้อความ</div>
                                </a>
                            </li>
                            <?php if (in_array($userRole, ['saraban', 'admin', 'super_admin'], true)): ?>
                            <li class="menu-item <?= $currentUri === 'memos/saraban-inbox' ? 'active' : '' ?>">
                                <a href="<?= base_url('admin/memos/saraban-inbox') ?>" class="menu-link d-flex justify-content-between align-items-center">
                                    <div>รอตรวจและออกเลข (สารบรรณ)</div>
                                    <?php if ($pendingSarabanCount > 0): ?>
                                        <span class="badge rounded-pill bg-warning text-dark fw-bold px-2 py-1 shadow-sm" style="font-size: 0.72rem;">
                                            <?= $pendingSarabanCount ?>
                                        </span>
                                    <?php endif; ?>
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </li>

                    <!-- 3. หนังสือประทับตรา -->
                    <li class="menu-item">
                        <a href="javascript:void(0)" class="menu-link menu-toggle text-muted">
                            <i class="menu-icon tf-icons bx bx-stamp text-secondary"></i>
                            <div>3. หนังสือประทับตรา</div>
                            <span class="badge bg-secondary text-white ms-auto" style="font-size: 0.65rem;">เร็ว ๆ นี้</span>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item disabled">
                                <a href="javascript:void(0)" class="menu-link">
                                    <div>รอเปิดใช้งานระบบ</div>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- 4. หนังสือสั่งการ -->
                    <li class="menu-item <?= $currentUri === 'documents' && (string) service('request')->getGet('type') === 'order' ? 'open' : '' ?>">
                        <a href="javascript:void(0)" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons bx bx-file text-warning"></i>
                            <div>4. หนังสือสั่งการ</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item <?= $currentUri === 'documents' && (string) service('request')->getGet('type') === 'order' ? 'active' : '' ?>">
                                <a href="<?= base_url('admin/documents?type=order') ?>" class="menu-link">
                                    <div>ทะเบียนคำสั่งโรงเรียน</div>
                                </a>
                            </li>
                            <li class="menu-item disabled">
                                <a href="javascript:void(0)" class="menu-link text-muted">
                                    <div>ระเบียบ / ข้อบังคับ (เร็ว ๆ นี้)</div>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- 5. หนังสือประชาสัมพันธ์ -->
                    <li class="menu-item">
                        <a href="javascript:void(0)" class="menu-link menu-toggle text-muted">
                            <i class="menu-icon tf-icons bx bx-broadcast text-success"></i>
                            <div>5. หนังสือประชาสัมพันธ์</div>
                            <span class="badge bg-secondary text-white ms-auto" style="font-size: 0.65rem;">เร็ว ๆ นี้</span>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item disabled">
                                <a href="javascript:void(0)" class="menu-link">
                                    <div>ประกาศ / แถลงการณ์ (เร็ว ๆ นี้)</div>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- 6. หนังสือที่เจ้าหน้าที่ทำขึ้นหรือรับไว้เป็นหลักฐาน -->
                    <li class="menu-item">
                        <a href="javascript:void(0)" class="menu-link menu-toggle text-muted">
                            <i class="menu-icon tf-icons bx bx-badge-check text-secondary"></i>
                            <div>6. หนังสือหลักฐานราชการ</div>
                            <span class="badge bg-secondary text-white ms-auto" style="font-size: 0.65rem;">เร็ว ๆ นี้</span>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item disabled">
                                <a href="javascript:void(0)" class="menu-link">
                                    <div>รายงานการประชุม / รับรอง (เร็ว ๆ นี้)</div>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- ==========================================
                         คำขอและงานดำเนินการ (e-Requests & Workflows)
                         ========================================== -->
                    <li class="menu-header small text-uppercase">
                        <span class="menu-header-text">คำขอและงานดำเนินการ</span>
                    </li>

                    <!-- ระบบคำขออนุมัติและอนุญาต (รวมศูนย์ ไม่ซ้ำซ้อน) -->
                    <li class="menu-item <?= (str_starts_with($currentUri, 'approvals') && !str_contains($currentUri, 'settings')) ? 'open' : '' ?>">
                        <a href="javascript:void(0)" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons bx bx-check-square text-success"></i>
                            <div>คำขออนุมัติ / ขออนุญาต</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item <?= $currentUri === 'approvals/create' ? 'active' : '' ?>">
                                <a href="<?= base_url('admin/approvals/create') ?>" class="menu-link">
                                    <div>ยื่นคำขอใหม่</div>
                                </a>
                            </li>
                            <li class="menu-item <?= $currentUri === 'approvals' ? 'active' : '' ?>">
                                <a href="<?= base_url('admin/approvals') ?>" class="menu-link">
                                    <div>รายการคำขอทั้งหมด</div>
                                </a>
                            </li>
                            <?php if (in_array((string) session()->get('role'), ['super_admin', 'admin'], true)): ?>
                            <li class="menu-item <?= $isActiveMenu('approval_settings', ['approvals/settings']) ?>">
                                <a href="<?= base_url('admin/approvals/settings') ?>" class="menu-link">
                                    <div>ตั้งค่าผู้พิจารณา</div>
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </li>

                    <!-- งานไปราชการและรายงานผล -->
                    <li class="menu-item <?= str_starts_with($currentUri, 'travel') || str_starts_with($currentUri, 'travel-reports') ? 'open' : '' ?>">
                        <a href="javascript:void(0)" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons bx bx-briefcase-alt-2 text-primary"></i>
                            <div>ไปราชการและรายงานผล</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item <?= $currentUri === 'travel/create' ? 'active' : '' ?>">
                                <a href="<?= base_url('admin/travel/create') ?>" class="menu-link">
                                    <div>ยื่นขอไปราชการ</div>
                                </a>
                            </li>
                            <li class="menu-item <?= $currentUri === 'travel/my' ? 'active' : '' ?>">
                                <a href="<?= base_url('admin/travel/my') ?>" class="menu-link">
                                    <div>ติดตามสถานะของฉัน</div>
                                </a>
                            </li>
                            <li class="menu-item <?= $currentUri === 'travel' ? 'active' : '' ?>">
                                <a href="<?= base_url('admin/travel') ?>" class="menu-link">
                                    <div>ทะเบียนไปราชการ</div>
                                </a>
                            </li>
                            <li class="menu-item <?= str_starts_with($currentUri, 'travel-reports') ? 'active' : '' ?>">
                                <a href="<?= base_url('admin/travel-reports') ?>" class="menu-link">
                                    <div>รายงานผลไปราชการ</div>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- ==========================================
                         ส่วนตัว & ตั้งค่าระบบ
                         ========================================== -->
                    <li class="menu-header small text-uppercase">
                        <span class="menu-header-text">ส่วนตัว & ผู้ดูแลระบบ</span>
                    </li>

                    <!-- ตั้งค่าลายเซ็น -->
                    <li class="menu-item <?= $isActiveMenu('profile', ['profile', 'profile/*']) ?>">
                        <a href="<?= base_url('admin/profile') ?>" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-pencil text-info"></i>
                            <div>ลายเซ็น e-Signature</div>
                        </a>
                    </li>

                    <?php if (in_array(session()->get('role'), ['super_admin', 'admin'], true)): ?>
                        <li class="menu-item <?= $isActiveMenu('users', ['users', 'users/*']) ?>">
                            <a href="<?= base_url('admin/users') ?>" class="menu-link text-primary fw-semibold">
                                <i class="menu-icon tf-icons bx bx-shield-quarter"></i>
                                <div>จัดการเจ้าหน้าที่ระบบ</div>
                            </a>
                        </li>
                        <li class="menu-item <?= $isActiveMenu('roles', ['roles', 'roles/*']) ?>">
                            <a href="<?= base_url('admin/roles') ?>" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-table text-info"></i>
                                <div>ตารางสิทธิ์ (Matrix)</div>
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="http://localhost:8081" target="_blank" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-data text-secondary"></i>
                                <div>จัดการฐานข้อมูล (PMA)</div>
                            </a>
                        </li>
                    <?php endif; ?>

                    <!-- หน้าแรกสาธารณะ (Public Portal) -->
                    <li class="menu-item">
                        <a href="<?= base_url('/') ?>" class="menu-link text-primary" target="_blank">
                            <i class="menu-icon tf-icons bx bx-globe"></i>
                            <div>หน้าแรกสาธารณะ (เปิดดู)</div>
                        </a>
                    </li>

                    <li class="menu-item">
                        <a href="<?= base_url('logout') ?>" class="menu-link text-danger">
                            <i class="menu-icon tf-icons bx bx-power-off"></i>
                            <div>ออกจากระบบ</div>
                        </a>
                    </li>
                </ul>
                <?php else: ?>
                <ul class="menu-inner py-1">
                    <!-- หน้าแรกสาธารณะ (Public Portal) -->
                    <li class="menu-item <?= ($rawUri === '' || $rawUri === 'tracking' || str_starts_with($rawUri, 'tracking/')) ? 'active' : '' ?>">
                        <a href="<?= base_url('/') ?>" class="menu-link text-primary">
                            <i class="menu-icon tf-icons bx bx-home-circle"></i>
                            <div>หน้าแรกสาธารณะ</div>
                        </a>
                    </li>
                    <li class="menu-item <?= (str_starts_with($currentUri, 'travel-reports') || str_starts_with($rawUri, 'travel-reports')) ? 'active' : '' ?>">
                        <a href="<?= base_url('admin/travel-reports') ?>" class="menu-link">
                            <i class="menu-icon tf-icons bx bxs-report text-warning"></i>
                            <div>รายงานผลไปราชการ</div>
                        </a>
                    </li>
                    <li class="menu-item <?= ($rawUri === 'login') ? 'active' : '' ?>">
                        <a href="<?= base_url('login') ?>" class="menu-link text-success fw-bold">
                            <i class="menu-icon tf-icons bx bx-log-in"></i>
                            <div>เข้าสู่ระบบ (Login)</div>
                        </a>
                    </li>
                </ul>
                <?php endif; ?>
            </aside>
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">

                <!-- Navbar -->
                <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                            <i class="bx bx-menu bx-sm"></i>
                        </a>
                    </div>

                    <div class="navbar-nav-right d-flex align-items-center w-100" id="navbar-collapse">
                        <!-- Dual Role Indicator & Switcher -->
                        <?php if (session()->get('has_dual_roles')): ?>
                            <div class="navbar-nav align-items-center me-2">
                                <div class="d-flex align-items-center bg-light px-2 py-1 rounded-pill border">
                                    <small class="text-secondary me-1 ms-1 d-none d-lg-inline">สิทธิ์ปัจจุบัน:</small>
                                    <?php if (session()->get('role') === 'super_admin'): ?>
                                        <span class="badge bg-primary text-white py-1 px-2 fw-semibold">
                                            <i class="bx bx-crown me-1"></i> Supper Admin
                                        </span>
                                    <?php elseif (session()->get('role') === 'admin'): ?>
                                        <span class="badge bg-info text-white py-1 px-2 fw-semibold">
                                            <i class="bx bx-folder-open me-1"></i> Admin (สารบรรณ)
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-success text-white py-1 px-2 fw-semibold">
                                            <i class="bx bx-user me-1"></i> ครู / ทั่วไป
                                        </span>
                                    <?php endif; ?>
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill ms-2 px-2 py-0 fw-semibold" data-bs-toggle="modal" data-bs-target="#switchRoleModal" style="font-size: 0.78rem;">
                                        <i class="bx bx-sync me-1"></i> สลับสิทธิ์
                                    </button>
                                </div>
                            </div>
                        <?php endif; ?>

                        <ul class="navbar-nav flex-row align-items-center ms-auto">
                            <?php if (session()->get('logged_in')): ?>
                            <!-- User info pill -->
                            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                <a class="nav-link dropdown-toggle hide-arrow d-flex align-items-center" href="javascript:void(0);" data-bs-toggle="dropdown">
                                    <div class="text-end me-2 d-none d-sm-block">
                                        <div class="fw-semibold text-dark"><?= esc(session()->get('full_name')) ?></div>
                                        <small class="text-muted"><i class="bx bx-envelope text-primary me-1"></i><?= esc(session()->get('email') ?? session()->get('username')) ?></small>
                                    </div>
                                    <div class="avatar avatar-online">
                                        <span class="avatar-initial rounded-circle bg-label-primary fw-bold">
                                            <?= mb_substr(session()->get('first_name') ?? '', 0, 1) ?>
                                        </span>
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 me-3">
                                                    <div class="avatar avatar-online">
                                                        <span class="avatar-initial rounded-circle bg-label-primary fw-bold">
                                                            <?= mb_substr(session()->get('first_name') ?? '', 0, 1) ?>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <span class="fw-semibold d-block"><?= esc(session()->get('full_name')) ?></span>
                                                    <small class="text-muted d-block"><?= esc(session()->get('email') ?? session()->get('username')) ?></small>
                                                    <small class="text-secondary"><?= esc(session()->get('position_name')) ?> &bull; <?= esc(session()->get('department_name')) ?></small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <li><div class="dropdown-divider"></div></li>
                                    <li>
                                        <a class="dropdown-item" href="<?= base_url('admin/profile') ?>">
                                            <i class="bx bx-pencil me-2 text-primary"></i>
                                            <span class="align-middle">จัดการลายเซ็นดิจิทัล</span>
                                        </a>
                                    </li>
                                    <?php if (session()->get('has_dual_roles')): ?>
                                        <li>
                                            <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#switchRoleModal">
                                                <i class="bx bx-sync me-2 text-success"></i>
                                                <span class="align-middle fw-semibold">สลับสิทธิ์การใช้งาน (Role Switch)</span>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    <li><div class="dropdown-divider"></div></li>
                                    <li>
                                        <a class="dropdown-item text-danger" href="<?= base_url('logout') ?>">
                                            <i class="bx bx-power-off me-2"></i>
                                            <span class="align-middle">ออกจากระบบ</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <?php else: ?>
                            <li class="nav-item">
                                <a href="<?= base_url('login') ?>" class="btn btn-primary btn-sm px-3 shadow-sm fw-bold">
                                    <i class="bx bx-log-in me-1"></i> เข้าสู่ระบบ (Login)
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </nav>
                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">

                        <!-- Flash Alerts -->
                        <?php if (session()->getFlashdata('success')): ?>
                            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
                                <i class="bx bx-check-circle fs-4 me-2"></i>
                                <div><?= session()->getFlashdata('success') ?></div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
                                <i class="bx bx-error-circle fs-4 me-2"></i>
                                <div><?= session()->getFlashdata('error') ?></div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (session()->getFlashdata('warning')): ?>
                            <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center" role="alert">
                                <i class="bx bx-error fs-4 me-2"></i>
                                <div><?= session()->getFlashdata('warning') ?></div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <?= $this->renderSection('content') ?>

                    </div>
                    <!-- / Content -->

                    <!-- Footer -->
                    <footer class="content-footer footer bg-footer-theme">
                        <div class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
                            <div class="mb-2 mb-md-0">
                                © <?= date('Y') + 543 ?> <strong>ระบบงานสารบรรณ สกจ. (e-Saraban SKJ)</strong> | โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์
                            </div>
                            <div>
                                <span class="badge bg-label-info">CI4 v4.7.5 + PHP 8.3 + Docker</span>
                            </div>
                        </div>
                    </footer>
                    <!-- / Footer -->

                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <script src="<?= base_url('assets/vendor/libs/popper/popper.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/js/bootstrap.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/js/menu.js') ?>"></script>
    <script src="<?= base_url('assets/js/main.js') ?>"></script>
    <script src="<?= base_url('assets/js/submit-loading.js?v=' . (file_exists(FCPATH . 'assets/js/submit-loading.js') ? filemtime(FCPATH . 'assets/js/submit-loading.js') : time())) ?>"></script>

    <!-- Signature Pad library CDN -->
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>

    <!-- Flatpickr Datepicker & Thai Buddhist Plugin -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/th.js"></script>
    <script src="<?= base_url('assets/js/flatpickr-thai-buddhist.js?v=' . (file_exists(FCPATH . 'assets/js/flatpickr-thai-buddhist.js') ? filemtime(FCPATH . 'assets/js/flatpickr-thai-buddhist.js') : time())) ?>"></script>

    <!-- SweetAlert2 library & Global AppAlert Bridge -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?= base_url('assets/js/app-swal.js?v=' . (file_exists(FCPATH . 'assets/js/app-swal.js') ? filemtime(FCPATH . 'assets/js/app-swal.js') : time())) ?>"></script>

    <script>
    // แสดง Flash Alerts ผ่าน SweetAlert2 แบบสวยงาม อัตโนมัติ
    document.addEventListener('DOMContentLoaded', function() {
        // 1. ตรวจสอบข้อความแจ้งเตือนจาก Session ปกติ
        <?php if (session()->getFlashdata('success')): ?>
            AppAlert.success(<?= json_encode(session()->getFlashdata('success')) ?>, 'ดำเนินการสำเร็จ');
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            AppAlert.error(<?= json_encode(session()->getFlashdata('error')) ?>, 'แจ้งเตือนข้อผิดพลาด');
        <?php endif; ?>
        <?php if (session()->getFlashdata('warning')): ?>
            AppAlert.warning(<?= json_encode(session()->getFlashdata('warning')) ?>, 'ข้อควรทราบ');
        <?php endif; ?>

        // 2. ตรวจสอบข้อความแจ้งเตือนจาก sessionStorage (กรณี AJAX File Upload สำเร็จแล้ว redirect)
        try {
            var ssSuccess = sessionStorage.getItem('flash_success');
            if (ssSuccess) {
                sessionStorage.removeItem('flash_success');
                AppAlert.success(ssSuccess, 'ดำเนินการสำเร็จ');
            }
            var ssError = sessionStorage.getItem('flash_error');
            if (ssError) {
                sessionStorage.removeItem('flash_error');
                AppAlert.error(ssError, 'แจ้งเตือนข้อผิดพลาด');
            }
        } catch(e) {}
    });
    </script>

    <?= $this->renderSection('scripts') ?>

    <?php if (session()->get('has_dual_roles') && !empty(session()->get('available_roles'))): ?>
        <!-- Dual Role Switch Modal -->
        <div class="modal fade" id="switchRoleModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 1.25rem;">
                    <div class="modal-header border-bottom py-3 px-4 bg-light">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm me-2">
                                <span class="avatar-initial rounded bg-label-primary text-primary">
                                    <i class="bx bx-transfer-alt fs-4"></i>
                                </span>
                            </div>
                            <div>
                                <h5 class="modal-title fw-bold text-dark mb-0" style="font-family: 'K2D', sans-serif;">สลับสิทธิ์การใช้งานระบบ</h5>
                                <small class="text-secondary">เลือกบทบาทที่ต้องการปฏิบัติงานในขณะนี้</small>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <?php foreach (session()->get('available_roles') as $rKey => $rVal): ?>
                                <?php 
                                    $isCurrent = (session()->get('role') === $rKey);
                                    $isAdmin = in_array($rKey, ['super_admin', 'admin'], true);
                                ?>
                                <div class="col-md-6">
                                    <div class="p-3 border rounded-3 h-100 d-flex flex-column justify-content-between position-relative <?= $isCurrent ? 'border-primary bg-label-primary' : 'bg-white' ?>" style="border-width: 2px !important;">
                                        <?php if ($isCurrent): ?>
                                            <span class="position-absolute top-0 end-0 m-2 badge bg-primary text-white small">
                                                <i class="bx bx-check me-1"></i>กำลังใช้งานสิทธิ์นี้
                                            </span>
                                        <?php endif; ?>

                                        <div>
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="avatar avatar-sm me-2">
                                                    <span class="avatar-initial rounded <?= $isAdmin ? 'bg-primary text-white' : 'bg-success text-white' ?>">
                                                        <i class="bx <?= esc($rVal['icon']) ?>"></i>
                                                    </span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 fw-bold text-dark" style="font-family: 'K2D', sans-serif;"><?= esc($rVal['title']) ?></h6>
                                                    <small class="text-secondary"><?= esc($rVal['subtitle']) ?></small>
                                                </div>
                                            </div>
                                            <p class="text-secondary small mb-3" style="line-height: 1.5;">
                                                <?= esc($rVal['description']) ?>
                                            </p>
                                        </div>

                                        <div class="pt-2 border-top">
                                            <?php if ($isCurrent): ?>
                                                <button type="button" class="btn btn-secondary btn-sm w-100 fw-bold" disabled>
                                                    <i class="bx bx-check-circle me-1"></i> กำลังอยู่ในบทบาทนี้
                                                </button>
                                            <?php else: ?>
                                                <a href="<?= base_url('switch-role/' . $rKey) ?>" class="btn <?= $isAdmin ? 'btn-primary' : 'btn-success' ?> btn-sm w-100 fw-bold shadow-xs">
                                                    <i class="bx bx-sync me-1"></i> สลับเป็นสิทธิ์นี้ทันที
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</body>
</html>
