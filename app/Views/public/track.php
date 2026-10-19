<!DOCTYPE html>
<html lang="th" class="light-style layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>ติดตามหนังสือเลขที่ <?= esc($document['doc_no']) ?> | ระบบงานสารบรรณ สกจ.</title>

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
            background: rgba(255, 255, 255, 0.92) !important;
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
            margin-bottom: 2rem;
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
            max-height: 70px;
            max-width: 160px;
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
        <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme glass-card navbar-compact" id="layout-navbar">
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
            <div class="alert d-flex align-items-center justify-content-between mb-4 shadow-sm" role="alert" style="background: #fffbe6; border: 1.5px solid #ffe58f; color: #614700; border-radius: 0.75rem;">
                <div class="d-flex align-items-center">
                    <i class="bx bxs-shield-quarter fs-3 me-2" style="color: #d48806;"></i>
                    <div class="small fw-medium">
                        <strong class="fw-bold" style="color: #382400;">โหมดติดตามสถานะสำหรับผู้ใช้ทั่วไป (Read-Only):</strong>
                        หน้านี้แสดงเส้นทางการเดินเรื่องและสถานะการพิจารณาเท่านั้น หากท่านเป็นผู้มีหน้าที่ลงนามในเอกสารนี้ กรุณา <a href="<?= base_url('login') ?>" class="alert-link text-decoration-underline fw-bold" style="color: #002c8a;">เข้าสู่ระบบ</a> เพื่อทำการเซ็นเกษียน
                    </div>
                </div>
                <span class="badge px-3 py-2 fw-bold d-none d-md-inline-block shadow-sm" style="background: #faad14; color: #000000; font-size: 0.75rem;">
                    <i class="bx bx-lock-alt me-1"></i> สิทธิ์ดูอย่างเดียว
                </span>
            </div>

            <!-- Header -->
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-1">
                            <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">หน้าแรกติดตามเอกสาร</a></li>
                            <li class="breadcrumb-item active">รายละเอียดการติดตาม</li>
                        </ol>
                    </nav>
                    <h4 class="fw-bold mb-0 text-primary">
                        <?= doc_type_badge($document['doc_type']) ?>
                        <?= esc($document['doc_no']) ?>: <?= esc($document['title']) ?>
                    </h4>
                </div>
                <div class="d-flex gap-2 mt-2 mt-md-0">
                    <?php if (!empty($document['file_path']) && file_exists(FCPATH . $document['file_path'])): ?>
                        <a href="<?= base_url($document['file_path']) ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="bx bx-file me-1"></i> ดูไฟล์เอกสารแนบ
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Visual Stepper Workflow -->
            <div class="card glass-card mb-4 border-0">
                <div class="card-body py-4">
                    <h6 class="text-muted fw-bold text-uppercase mb-4" style="font-size: 0.8rem; letter-spacing: 0.5px;">
                        <i class="bx bx-git-commit me-1 text-primary"></i> ลำดับขั้นตอนการเสนอแฟ้มและลงนามดิจิทัล (Workflow Progress)
                    </h6>
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
                                        <span class="badge bg-label-success" style="font-size: 0.7rem;">ลงนามแล้ว</span>
                                    <?php elseif ($isReturn): ?>
                                        <span class="badge bg-label-danger" style="font-size: 0.7rem;">ส่งคืนแก้ไข</span>
                                    <?php elseif ($isCurrent): ?>
                                        <span class="badge bg-primary" style="font-size: 0.7rem;">กำลังพิจารณา</span>
                                    <?php else: ?>
                                        <span class="badge bg-label-secondary" style="font-size: 0.7rem;">รอดำเนินการ</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Left Column: Document Details -->
                <div class="col-lg-6 mb-4">
                    <div class="card glass-card h-100 border-0">
                        <div class="card-header d-flex justify-content-between align-items-center bg-transparent border-bottom">
                            <h5 class="card-title mb-0 fw-bold">
                                <i class="bx bx-info-circle text-primary me-2"></i> ข้อมูลหนังสือราชการ
                            </h5>
                            <div>
                                <?= urgency_badge($document['urgency']) ?>
                                <?= doc_status_badge($document['status']) ?>
                            </div>
                        </div>
                        <div class="card-body pt-3">
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <small class="text-muted d-block">เลขทะเบียนภายใน รร.</small>
                                    <strong class="text-dark font-monospace"><?= esc($document['internal_reg_no'] ?: '-') ?></strong>
                                </div>
                                <div class="col-sm-6">
                                    <small class="text-muted d-block">เลขที่หนังสือ</small>
                                    <strong class="text-dark"><?= esc($document['doc_no']) ?></strong>
                                </div>
                                <div class="col-12">
                                    <small class="text-muted d-block">ชื่อเรื่อง</small>
                                    <div class="fw-bold text-dark fs-6"><?= esc($document['title']) ?></div>
                                </div>
                                <div class="col-sm-6">
                                    <small class="text-muted d-block">จาก (ต้นเรื่อง)</small>
                                    <div class="text-dark"><?= esc($document['from_org']) ?></div>
                                </div>
                                <div class="col-sm-6">
                                    <small class="text-muted d-block">ถึง (ปลายทาง)</small>
                                    <div class="text-dark"><?= esc($document['to_org']) ?></div>
                                </div>
                                <div class="col-sm-6">
                                    <small class="text-muted d-block">ลงวันที่</small>
                                    <div class="text-dark"><?= thai_date($document['doc_date'], 'full') ?></div>
                                </div>
                                <div class="col-sm-6">
                                    <small class="text-muted d-block">วันที่บันทึกเข้าระบบ</small>
                                    <div class="text-dark"><?= thai_date($document['created_at'], 'full', true) ?></div>
                                </div>
                                <div class="col-12">
                                    <small class="text-muted d-block">ผู้สร้างเอกสาร</small>
                                    <div class="text-dark">
                                        <i class="bx bx-user me-1 text-primary"></i>
                                        <?= esc(($document['prefix'] ?? '') . $document['first_name'] . ' ' . $document['last_name']) ?>
                                        (<?= esc($document['creator_department'] ?? 'สารบรรณ') ?>)
                                    </div>
                                </div>
                            </div>

                            <!-- Audit Logs -->
                            <div class="mt-4 pt-3 border-top">
                                <h6 class="text-muted fw-bold small text-uppercase mb-2">
                                    <i class="bx bx-history me-1 text-primary"></i> ประวัติการดำเนินการ (Audit Logs)
                                </h6>
                                <div class="bg-light p-3 rounded" style="max-height: 180px; overflow-y: auto;">
                                    <ul class="list-unstyled mb-0 small">
                                        <?php if (empty($logs)): ?>
                                            <li class="text-muted">ไม่มีประวัติการดำเนินการ</li>
                                        <?php else: ?>
                                            <?php foreach ($logs as $log): ?>
                                                <li class="mb-2 pb-2 border-bottom border-light">
                                                    <span class="text-muted">[<?= thai_date($log['created_at'], 'short', true) ?>]</span>
                                                    <strong class="text-dark"><?= esc(($log['prefix'] ?? '') . $log['first_name'] . ' ' . $log['last_name']) ?>:</strong>
                                                    <span class="badge bg-label-secondary ms-1"><?= esc($log['action']) ?></span>
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

                <!-- Right Column: Signatures History -->
                <div class="col-lg-6 mb-4">
                    <div class="card glass-card h-100 border-0">
                        <div class="card-header bg-transparent border-bottom">
                            <h5 class="card-title mb-0 fw-bold">
                                <i class="bx bx-edit text-primary me-2"></i> บันทึกข้อคิดเห็นและผลการเกษียนหนังสือ
                            </h5>
                        </div>
                        <div class="card-body pt-3">
                            <?php foreach ($routes as $r): ?>
                                <div class="border rounded p-3 mb-3 <?= $r['status'] === 'signed' ? 'bg-white border-success shadow-sm' : 'bg-light' ?>">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div>
                                            <span class="badge bg-label-primary me-1">ขั้นที่ <?= $r['step_order'] ?></span>
                                            <strong class="text-dark"><?= esc($r['prefix'] . $r['first_name'] . ' ' . $r['last_name']) ?></strong>
                                            <small class="text-muted d-block"><?= esc($r['position_name']) ?> (<?= esc($r['department_name'] ?? '') ?>)</small>
                                        </div>
                                        <div>
                                            <?php if ($r['status'] === 'signed'): ?>
                                                <span class="badge bg-success"><i class="bx bx-check me-1"></i> ลงนามแล้ว</span>
                                            <?php elseif ($r['status'] === 'returned'): ?>
                                                <span class="badge bg-danger"><i class="bx bx-x me-1"></i> ส่งคืนแก้ไข</span>
                                            <?php elseif ($r['step_order'] == $document['current_step'] && $document['status'] === 'routing'): ?>
                                                <span class="badge bg-primary"><i class="bx bx-time me-1"></i> กำลังรอลงนาม</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">รอดำเนินการ</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <?php if ($r['status'] === 'signed'): ?>
                                        <div class="bg-light p-2 rounded mb-2">
                                            <?php if (!empty($r['comment_preset'])): ?>
                                                <div class="fw-bold text-dark small"><i class="bx bx-check text-success me-1"></i> <?= esc($r['comment_preset']) ?></div>
                                            <?php endif; ?>
                                            <?php if (!empty($r['comment_text'])): ?>
                                                <div class="text-muted small fst-italic">"<?= esc($r['comment_text']) ?>"</div>
                                            <?php endif; ?>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-end mt-2 pt-2 border-top">
                                            <div>
                                                <?php if (!empty($r['signature_img'])): ?>
                                                    <img src="<?= base_url($r['signature_img']) ?>" alt="ลายเซ็นดิจิทัล" class="signature-preview" />
                                                <?php endif; ?>
                                            </div>
                                            <div class="text-end small text-muted">
                                                <div>ลงนามเมื่อ: <?= thai_date($r['signed_at'], 'full', true) ?></div>
                                                <?php if (!empty($r['verify_hash'])): ?>
                                                    <a href="<?= base_url('verify/' . $r['verify_hash']) ?>" target="_blank" class="text-primary text-decoration-none" style="font-size: 0.72rem;">
                                                        <i class="bx bx-check-shield text-success me-1"></i> ตรวจสอบลายมือชื่อดิจิทัล
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php elseif ($r['status'] === 'returned'): ?>
                                        <div class="alert alert-danger py-2 mb-0 small">
                                            <strong>เหตุผลที่ส่งคืน:</strong> <?= esc($r['comment_text'] ?: 'ไม่มีการระบุเหตุผล') ?>
                                            <div class="mt-1 text-muted">ส่งคืนเมื่อ: <?= thai_date($r['signed_at'], 'full', true) ?></div>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-muted small py-2">
                                            <i class="bx bx-hourglass me-1"></i> รอดำเนินการตามลำดับขั้นตอนการเสนอเรื่อง
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Footer -->
        <footer class="footer bg-white border-top py-4 mt-5 glass-card">
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
</body>
</html>
