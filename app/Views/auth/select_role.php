<!DOCTYPE html>
<html lang="th" class="light-style layout-wide" dir="ltr" data-theme="theme-default">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>เลือกบทบาทการเข้าใช้งานระบบ | e-Saraban โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= base_url('assets/img/favicon/favicon.ico') ?>" />

    <!-- Google Fonts: K2D for Modern UI & Sarabun for Official Text -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=K2D:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&family=Sarabun:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">

    <!-- Icons & Core CSS -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="<?= base_url('assets/vendor/css/core.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/css/demo.css') ?>" />

    <style>
        :root {
            --primary-color: #4f46e5;
            --primary-hover: #4338ca;
            --success-color: #059669;
            --success-hover: #047857;
            --slate-900: #0f172a;
            --slate-800: #1e293b;
            --slate-700: #334155;
            --slate-600: #475569;
            --slate-200: #e2e8f0;
            --slate-100: #f1f5f9;
            --slate-50: #f8fafc;
        }

        body, html, input, button, select, textarea, h1, h2, h3, h4, h5, h6, .btn, .badge {
            font-family: 'K2D', sans-serif !important;
        }
        .text-sarabun {
            font-family: 'Sarabun', sans-serif !important;
        }

        /* Ambient Canvas & Responsive Viewport */
        body {
            background-color: #f1f5f9;
            min-height: 100vh;
            margin: 0;
            color: var(--slate-800);
            overflow-x: hidden;
            position: relative;
        }

        /* Ambient Animated Mesh Background */
        .ambient-bg {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
            overflow: hidden;
        }
        .ambient-bg::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(79, 70, 229, 0.14) 1.25px, transparent 1.25px);
            background-size: 26px 26px;
            opacity: 0.85;
        }
        .ambient-glow {
            position: absolute;
            border-radius: 50%;
            filter: blur(90px);
            opacity: 0.4;
            animation: pulseGlow 18s ease-in-out infinite alternate;
        }
        .glow-indigo {
            width: 500px;
            height: 500px;
            background: linear-gradient(135deg, rgba(79, 70, 229, 0.5), rgba(59, 130, 246, 0.35));
            top: -120px;
            right: -100px;
        }
        .glow-emerald {
            width: 480px;
            height: 480px;
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.42), rgba(6, 182, 212, 0.3));
            bottom: -100px;
            left: -120px;
            animation-duration: 22s;
            animation-delay: -5s;
        }
        @keyframes pulseGlow {
            0% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(25px, -20px) scale(1.06); }
            100% { transform: translate(-20px, 20px) scale(0.95); }
        }

        /* Center Layout Wrapper */
        .select-role-wrapper {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2.5rem 1.25rem 4rem 1.25rem;
        }

        /* Main Executive Modal Card */
        .select-role-card {
            max-width: 940px;
            width: 100%;
            background: #ffffff;
            border-radius: 1.5rem;
            border: 1px solid var(--slate-200);
            box-shadow: 0 25px 60px -15px rgba(15, 23, 42, 0.16), 0 0 0 1px rgba(226, 232, 240, 0.85);
            overflow: hidden;
            animation: cardScaleUp 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes cardScaleUp {
            from {
                opacity: 0;
                transform: translateY(24px) scale(0.97);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* Top Brand Strip */
        .top-brand-strip {
            background: linear-gradient(90deg, #1e1b4b 0%, #312e81 100%);
            color: #ffffff;
            padding: 0.85rem 2rem;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
        }

        /* User Header Banner */
        .user-header-banner {
            background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
            border-bottom: 1px solid var(--slate-200);
            padding: 1.75rem 2.25rem;
        }

        .user-avatar-wrap {
            position: relative;
            width: 62px;
            height: 62px;
            flex-shrink: 0;
        }
        .user-avatar-img {
            width: 62px;
            height: 62px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #ffffff;
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.15);
        }
        .user-avatar-initials {
            width: 62px;
            height: 62px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            font-weight: 700;
            color: #ffffff;
            background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
            border: 3px solid #ffffff;
            box-shadow: 0 4px 14px rgba(79, 70, 229, 0.35);
        }

        /* Role Selection Cards */
        .role-card-option {
            background: #ffffff;
            border: 2px solid var(--slate-200);
            border-radius: 1.25rem;
            padding: 1.75rem;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            cursor: pointer;
            transition: all 0.28s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
        }

        .role-card-option::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            opacity: 0;
            transition: opacity 0.25s ease;
        }

        /* 1. Super Admin Option Styling */
        .role-card-option.theme-super-admin:hover,
        .role-card-option.theme-super-admin.is-selected {
            border-color: #4f46e5;
            transform: translateY(-5px);
            box-shadow: 0 18px 36px -8px rgba(79, 70, 229, 0.28);
        }
        .role-card-option.theme-super-admin::before {
            background: linear-gradient(90deg, #4f46e5, #7c3aed);
        }
        .role-card-option.theme-super-admin:hover::before,
        .role-card-option.theme-super-admin.is-selected::before {
            opacity: 1;
        }

        /* 2. Admin Option Styling */
        .role-card-option.theme-admin:hover,
        .role-card-option.theme-admin.is-selected {
            border-color: #0284c7;
            transform: translateY(-5px);
            box-shadow: 0 18px 36px -8px rgba(2, 132, 199, 0.28);
        }
        .role-card-option.theme-admin::before {
            background: linear-gradient(90deg, #0284c7, #2563eb);
        }
        .role-card-option.theme-admin:hover::before,
        .role-card-option.theme-admin.is-selected::before {
            opacity: 1;
        }

        /* 3. Executive Option Styling */
        .role-card-option.theme-executive:hover,
        .role-card-option.theme-executive.is-selected {
            border-color: #d97706;
            transform: translateY(-5px);
            box-shadow: 0 18px 36px -8px rgba(217, 119, 6, 0.28);
        }
        .role-card-option.theme-executive::before {
            background: linear-gradient(90deg, #d97706, #f59e0b);
        }
        .role-card-option.theme-executive:hover::before,
        .role-card-option.theme-executive.is-selected::before {
            opacity: 1;
        }

        /* 4. Staff Option Styling */
        .role-card-option.theme-staff:hover,
        .role-card-option.theme-staff.is-selected {
            border-color: #059669;
            transform: translateY(-5px);
            box-shadow: 0 18px 36px -8px rgba(16, 185, 129, 0.28);
        }
        .role-card-option.theme-staff::before {
            background: linear-gradient(90deg, #059669, #10b981);
        }
        .role-card-option.theme-staff:hover::before,
        .role-card-option.theme-staff.is-selected::before {
            opacity: 1;
        }

        /* Icon Boxes (High Contrast & Vibrant) */
        .role-icon-container {
            width: 62px;
            height: 62px;
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: #ffffff !important;
            flex-shrink: 0;
            transition: transform 0.25s ease;
        }
        .role-card-option:hover .role-icon-container {
            transform: scale(1.08) rotate(2deg);
        }
        .icon-super-admin {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            box-shadow: 0 8px 18px rgba(79, 70, 229, 0.35);
        }
        .icon-admin {
            background: linear-gradient(135deg, #0284c7 0%, #2563eb 100%);
            box-shadow: 0 8px 18px rgba(2, 132, 199, 0.35);
        }
        .icon-executive {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
            box-shadow: 0 8px 18px rgba(217, 119, 6, 0.35);
        }
        .icon-staff {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            box-shadow: 0 8px 18px rgba(16, 185, 129, 0.35);
        }

        /* Checklist Box Per Theme */
        .checklist-box-super-admin {
            background: #f5f3ff;
            border: 1px solid #ede9fe;
            border-radius: 0.85rem;
            padding: 1rem;
            margin-bottom: 1.25rem;
        }
        .checklist-box-admin {
            background: #f0f9ff;
            border: 1px solid #e0f2fe;
            border-radius: 0.85rem;
            padding: 1rem;
            margin-bottom: 1.25rem;
        }
        .checklist-box-executive {
            background: #fffbeb;
            border: 1px solid #fef3c7;
            border-radius: 0.85rem;
            padding: 1rem;
            margin-bottom: 1.25rem;
        }
        .checklist-box-staff {
            background: #f0fdf4;
            border: 1px solid #dcfce7;
            border-radius: 0.85rem;
            padding: 1rem;
            margin-bottom: 1.25rem;
        }

        .feature-item {
            display: flex;
            align-items: flex-start;
            font-size: 0.925rem;
            color: var(--slate-800);
            line-height: 1.5;
            margin-bottom: 0.6rem;
        }
        .feature-item:last-child {
            margin-bottom: 0;
        }
        .feature-item i {
            margin-top: 2px;
            flex-shrink: 0;
            font-size: 1.15rem;
        }

        /* Action Buttons with High Contrast & Hover Effects */
        .btn-action-super-admin {
            background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%) !important;
            color: #ffffff !important;
            border: none !important;
            padding: 0.85rem 1.25rem !important;
            border-radius: 0.75rem !important;
            font-weight: 700 !important;
            font-size: 1rem !important;
            letter-spacing: 0.2px;
            box-shadow: 0 4px 14px rgba(79, 70, 229, 0.3) !important;
            transition: all 0.25s ease !important;
        }
        .btn-action-super-admin:hover {
            background: linear-gradient(135deg, #4338ca 0%, #3730a3 100%) !important;
            box-shadow: 0 6px 22px rgba(79, 70, 229, 0.45) !important;
            transform: translateY(-2px);
        }

        .btn-action-admin {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%) !important;
            color: #ffffff !important;
            border: none !important;
            padding: 0.85rem 1.25rem !important;
            border-radius: 0.75rem !important;
            font-weight: 700 !important;
            font-size: 1rem !important;
            letter-spacing: 0.2px;
            box-shadow: 0 4px 14px rgba(2, 132, 199, 0.3) !important;
            transition: all 0.25s ease !important;
        }
        .btn-action-admin:hover {
            background: linear-gradient(135deg, #0369a1 0%, #075985 100%) !important;
            box-shadow: 0 6px 22px rgba(2, 132, 199, 0.45) !important;
            transform: translateY(-2px);
        }

        .btn-action-executive {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%) !important;
            color: #ffffff !important;
            border: none !important;
            padding: 0.85rem 1.25rem !important;
            border-radius: 0.75rem !important;
            font-weight: 700 !important;
            font-size: 1rem !important;
            letter-spacing: 0.2px;
            box-shadow: 0 4px 14px rgba(217, 119, 6, 0.3) !important;
            transition: all 0.25s ease !important;
        }
        .btn-action-executive:hover {
            background: linear-gradient(135deg, #b45309 0%, #92400e 100%) !important;
            box-shadow: 0 6px 22px rgba(217, 119, 6, 0.45) !important;
            transform: translateY(-2px);
        }

        .btn-action-staff {
            background: linear-gradient(135deg, #059669 0%, #047857 100%) !important;
            color: #ffffff !important;
            border: none !important;
            padding: 0.85rem 1.25rem !important;
            border-radius: 0.75rem !important;
            font-weight: 700 !important;
            font-size: 1rem !important;
            letter-spacing: 0.2px;
            box-shadow: 0 4px 14px rgba(16, 185, 129, 0.3) !important;
            transition: all 0.25s ease !important;
        }
        .btn-action-staff:hover {
            background: linear-gradient(135deg, #047857 0%, #065f46 100%) !important;
            box-shadow: 0 6px 22px rgba(16, 185, 129, 0.45) !important;
            transform: translateY(-2px);
        }

        .solid-badge-amber {
            background-color: #d97706 !important;
            color: #ffffff !important;
        }
        .solid-badge-blue {
            background-color: #0284c7 !important;
            color: #ffffff !important;
        }
        .role-card-option:hover .btn-action-staff i {
            transform: translateX(4px);
        }

        .btn i {
            transition: transform 0.2s ease;
        }

        /* High-Contrast Info Banner */
        .info-tip-box {
            background: #ffffff;
            border: 1px solid var(--slate-200);
            border-left: 5px solid #4f46e5;
            border-radius: 0.85rem;
            padding: 1.15rem 1.35rem;
            color: var(--slate-800);
            box-shadow: 0 2px 10px rgba(15, 23, 42, 0.05);
        }

        /* High-contrast solid badge rule */
        .solid-badge-dark {
            background-color: #0f172a !important;
            color: #ffffff !important;
        }
        .solid-badge-indigo {
            background-color: #4f46e5 !important;
            color: #ffffff !important;
        }
        .solid-badge-emerald {
            background-color: #059669 !important;
            color: #ffffff !important;
        }
        .solid-badge-warning {
            background-color: #f59e0b !important;
            color: #0f172a !important;
        }

        @media (max-width: 767.98px) {
            .select-role-card {
                border-radius: 1rem;
            }
            .user-header-banner {
                padding: 1.25rem;
            }
            .role-card-option {
                padding: 1.25rem;
            }
            .top-brand-strip {
                flex-direction: column;
                gap: 0.35rem;
                text-align: center;
                padding: 0.65rem 1rem;
            }
        }
    </style>
</head>

<body>
    <!-- Ambient Animated Background -->
    <div class="ambient-bg">
        <div class="ambient-glow glow-indigo"></div>
        <div class="ambient-glow glow-emerald"></div>
    </div>

    <!-- Center Layout Wrapper -->
    <div class="select-role-wrapper">
        <div class="select-role-card">
            
            <!-- 1. Top Brand Strip -->
            <div class="top-brand-strip">
                <div class="d-flex align-items-center gap-2">
                    <i class="bx bxs-shield-check fs-5 text-warning"></i>
                    <span class="fw-bold">โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์</span>
                </div>
                <div class="d-flex align-items-center gap-2 small">
                    <span class="badge bg-white text-dark fw-bold px-2 py-0.5">e-Saraban</span>
                    <span class="opacity-75">ระบบงานสารบรรณอิเล็กทรอนิกส์</span>
                </div>
            </div>

            <!-- 2. User Profile Header Banner -->
            <div class="user-header-banner">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="user-avatar-wrap">
                            <?php if (!empty($userImg)): ?>
                                <img src="<?= base_url(esc($userImg)) ?>" alt="<?= esc($fullName) ?>" class="user-avatar-img" onerror="this.style.display='none'; document.getElementById('user-avatar-fallback').style.display='flex';" />
                            <?php endif; ?>
                            <div id="user-avatar-fallback" class="user-avatar-initials" style="<?= !empty($userImg) ? 'display: none;' : '' ?>">
                                <?= esc(mb_substr($fullName ?? 'U', 0, 1)) ?>
                            </div>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                <span class="badge solid-badge-emerald px-2.5 py-1 fw-bold fs-8 shadow-xs">
                                    <i class="bx bx-check-shield me-1"></i> ยืนยันตัวตนสำเร็จ
                                </span>
                                <?php if (!empty($positionName)): ?>
                                    <span class="badge solid-badge-dark px-2.5 py-1 fw-semibold fs-8 shadow-xs">
                                        <i class="bx bx-briefcase me-1 text-warning"></i> <?= esc($positionName) ?>
                                    </span>
                                <?php endif; ?>
                                <?php if (!empty($departmentName)): ?>
                                    <span class="badge solid-badge-indigo px-2.5 py-1 fw-semibold fs-8 shadow-xs">
                                        <i class="bx bx-buildings me-1"></i> <?= esc($departmentName) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            <h4 class="mb-0 fw-bold" style="color: #0f172a; letter-spacing: -0.2px;">
                                <?= esc($fullName) ?>
                            </h4>
                            <div class="text-secondary small mt-1 d-flex align-items-center gap-2">
                                <span class="fw-semibold text-dark">
                                    <i class="bx bx-envelope text-primary me-1"></i><?= esc($email) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <a href="<?= base_url('logout') ?>" class="btn btn-outline-danger btn-sm fw-bold px-3 py-2 shadow-xs d-inline-flex align-items-center gap-1" title="ออกจากระบบเพื่อสลับบัญชี Google อื่น">
                            <i class="bx bx-power-off"></i> สลับบัญชี Google
                        </a>
                    </div>
                </div>
            </div>

            <!-- 3. Role Selection Body -->
            <div class="p-4 p-md-5">
                
                <!-- Section Title & Subtitle -->
                <div class="text-center mb-4">
                    <span class="badge bg-dark text-white fw-bold px-3 py-1.5 mb-2 shadow-xs" style="letter-spacing: 0.5px;">
                        <i class="bx bx-select-multiple me-1 text-warning"></i> ระบบตรวจพบสิทธิ์การใช้งานหลายบทบาท
                    </span>
                    <h3 class="fw-bold mb-1" style="color: #0f172a;">
                        กรุณาเลือกบทบาทที่ต้องการเข้าใช้งาน
                    </h3>
                    <p class="mb-0 text-secondary" style="font-size: 0.95rem; line-height: 1.5;">
                        ท่านได้รับมอบหมายสิทธิ์การเข้าถึงมากกว่าหนึ่งหน้าที่ในระบบงานสารบรรณ เลือกบทบาทที่ต้องการปฏิบัติงานในเซสชันนี้
                    </p>
                </div>

                <!-- Alert Error Message (if any) -->
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
                        <i class="bx bx-error fs-4 me-2"></i>
                        <div class="fw-bold"><?= esc(session()->getFlashdata('error')) ?></div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <!-- Two-Column Role Selection Cards -->
                <div class="row g-4 mb-4 align-items-stretch">
                    <?php foreach ($availableRoles as $key => $r): ?>
                        <?php 
                            $isCurrentRole = ($currentRole === $key);
                            $themeSlug = str_replace('_', '-', $key);
                            $cardTheme = $r['theme'] ?? 'theme-' . $themeSlug;
                            $iconClass = 'icon-' . $themeSlug;
                            $btnClass = 'btn-action-' . $themeSlug;
                            $checklistClass = 'checklist-box-' . $themeSlug;

                            // Badge Theme
                            $roleBadgeClass = 'solid-badge-indigo';
                            $accentColorClass = 'text-primary';
                            if ($key === 'admin') {
                                $roleBadgeClass = 'solid-badge-blue';
                                $accentColorClass = 'text-info';
                            } elseif ($key === 'executive') {
                                $roleBadgeClass = 'solid-badge-amber';
                                $accentColorClass = 'text-warning';
                            } elseif ($key === 'teacher') {
                                $roleBadgeClass = 'solid-badge-emerald';
                                $accentColorClass = 'text-success';
                            }

                            // Feature Bullet Points
                            $features = $r['features'] ?? [
                                'ปฏิบัติงานตามภาระหน้าที่ที่ได้รับมอบหมาย',
                                'เข้าถึงข้อมูลและจัดการตามสิทธิ์ของบทบาท',
                                'ลงนามอิเล็กทรอนิกส์และเกษียนหนังสือตามสายงาน'
                            ];
                        ?>
                        <div class="col-md-6 d-flex">
                            <form action="<?= base_url('select-role') ?>" method="post" class="w-100 d-flex flex-column">
                                <?= csrf_field() ?>
                                <input type="hidden" name="role" value="<?= esc($key) ?>">

                                <div class="role-card-option <?= $cardTheme ?> <?= $isCurrentRole ? 'is-selected' : '' ?>" onclick="$(this).closest('form').submit();">
                                    
                                    <div>
                                        <!-- Header within Card: Icon & Badge -->
                                        <div class="d-flex align-items-center justify-content-between mb-3" style="min-height: 62px;">
                                            <div class="role-icon-container <?= $iconClass ?>">
                                                <i class="bx <?= esc($r['icon'] ?? 'bx-user') ?>"></i>
                                            </div>
                                            <div class="d-flex align-items-center gap-1">
                                                <?php if ($isCurrentRole): ?>
                                                    <span class="badge solid-badge-warning fw-bold px-2.5 py-1.5 shadow-xs" title="สิทธิ์การเข้าใช้งานที่ใช้อยู่ในปัจจุบัน">
                                                        <i class="bx bxs-star me-1"></i> สิทธิ์ปัจจุบัน
                                                    </span>
                                                <?php endif; ?>
                                                <span class="badge <?= esc($roleBadgeClass) ?> px-3 py-1.5 fw-bold fs-7 shadow-xs">
                                                    <?= esc($r['role_badge'] ?? 'ผู้ใช้งาน') ?>
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Card Titles -->
                                        <h4 class="fw-bold mb-1" style="color: #0f172a; font-size: 1.28rem; min-height: 2rem;">
                                            เข้าใช้งานในฐานะ <?= esc($r['title']) ?>
                                        </h4>
                                        <div class="fw-bold mb-2 <?= $accentColorClass ?>" style="font-size: 0.95rem;">
                                            <i class="bx bx-check-double me-1"></i><?= esc($r['subtitle']) ?>
                                        </div>

                                        <!-- Role Description -->
                                        <p class="small mb-3 text-sarabun" style="line-height: 1.6; font-size: 0.95rem; color: #475569 !important; min-height: 48px;">
                                            <?= esc($r['description']) ?>
                                        </p>

                                        <!-- Responsibilities Checklist -->
                                        <div class="<?= $checklistClass ?>">
                                            <div class="small fw-bold mb-2 <?= $accentColorClass ?>">
                                                <i class="bx bx-task me-1"></i> สิทธิ์และหน้าที่หลักที่ได้รับ:
                                            </div>
                                            <?php foreach ($features as $f): ?>
                                                <div class="feature-item">
                                                    <i class="bx bxs-check-circle me-2 <?= $accentColorClass ?>"></i>
                                                    <span class="fw-medium" style="color: #1e293b;"><?= esc($f) ?></span>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>

                                    <!-- Bottom Action Button -->
                                    <div class="pt-3 border-top mt-auto">
                                        <button type="submit" class="btn <?= $btnClass ?> w-100 d-flex align-items-center justify-content-center">
                                            <span>เข้าใช้งานในสิทธิ์นี้</span>
                                            <i class="bx bx-right-arrow-alt fs-5 ms-2"></i>
                                        </button>
                                    </div>

                                </div>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- 4. High-Contrast Informational Banner -->
                <div class="info-tip-box d-flex align-items-start gap-2">
                    <i class="bx bx-info-circle text-primary fs-4 mt-0.5 flex-shrink-0"></i>
                    <div style="font-size: 0.925rem; line-height: 1.6;">
                        <strong class="text-dark">คำแนะนำในการสลับบทบาท:</strong> 
                        ท่านสามารถสลับสิทธิ์การใช้งานไปมาระหว่างวันได้ตลอดเวลาจากเมนู 
                        <span class="badge bg-dark text-white fw-bold px-2 py-0.5 mx-1">
                            <i class="bx bx-transfer-alt me-1 text-warning"></i> สลับสิทธิ์
                        </span> 
                        ที่แถบนำทางด้านบนของทุกหน้า โดยไม่ต้องลงชื่อออกจากระบบ
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="<?= base_url('assets/vendor/libs/jquery/jquery.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/js/bootstrap.js') ?>"></script>
    <script src="<?= base_url('assets/js/submit-loading.js') ?>"></script>
</body>
</html>
