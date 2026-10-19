<!DOCTYPE html>
<html lang="th" class="light-style layout-wide" dir="ltr" data-theme="theme-default">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>เลือกสิทธิ์การเข้าใช้งานระบบ | e-Saraban</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= base_url('assets/img/favicon/favicon.ico') ?>" />

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=K2D:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&family=Sarabun:wght@300;400;500;600&display=swap" rel="stylesheet">

    <!-- Icons & Core CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/vendor/fonts/boxicons.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/vendor/css/core.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/vendor/css/theme-default.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/css/demo.css') ?>" />

    <style>
        body, h1, h2, h3, h4, h5, h6, .btn, .badge {
            font-family: 'K2D', sans-serif;
        }
        .text-sarabun {
            font-family: 'Sarabun', sans-serif;
        }

        /* Full page backdrop with soft gradient */
        .select-role-wrapper {
            min-height: 100vh;
            background: radial-gradient(circle at 10% 20%, rgba(105, 108, 255, 0.08) 0%, rgba(245, 245, 249, 1) 90%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .select-role-modal {
            max-width: 820px;
            width: 100%;
            background: #ffffff;
            border-radius: 1.25rem;
            box-shadow: 0 1.25rem 3rem rgba(34, 48, 62, 0.16);
            overflow: hidden;
            animation: modalFadeIn 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes modalFadeIn {
            from {
                opacity: 0;
                transform: translateY(20px) scale(0.98);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* Role Selection Cards */
        .role-option-card {
            border: 2px solid #e7e7e8;
            border-radius: 1rem;
            padding: 1.5rem;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            background: #ffffff;
        }

        .role-option-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 0.75rem 1.75rem rgba(105, 108, 255, 0.15);
            border-color: #696cff;
        }

        .role-option-card.admin-option:hover {
            border-color: #696cff;
        }
        .role-option-card.staff-option:hover {
            border-color: #71dd37;
            box-shadow: 0 0.75rem 1.75rem rgba(113, 221, 55, 0.18);
        }

        .role-icon-box {
            width: 56px;
            height: 56px;
            border-radius: 0.85rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin-bottom: 1rem;
        }

        .avatar-initial-large {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: 700;
        }
    </style>
</head>

<body>
    <div class="select-role-wrapper">
        <div class="select-role-modal border-0">
            <!-- Modal Header with Brand Banner -->
            <div class="p-4 p-md-5 pb-3 border-bottom bg-light">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar-initial-large bg-primary text-white me-3 shadow-xs">
                            <?= esc(mb_substr($fullName ?? 'U', 0, 1)) ?>
                        </div>
                        <div>
                            <span class="badge bg-label-primary px-3 py-1 mb-1 fw-bold">
                                <i class="bx bx-check-shield me-1"></i> ยืนยันตัวตนสำเร็จ
                            </span>
                            <h4 class="mb-0 fw-bold text-dark"><?= esc($fullName) ?></h4>
                            <div class="text-secondary small mt-1">
                                <span><i class="bx bx-envelope text-primary me-1"></i><?= esc($email) ?></span>
                                <span class="mx-1">•</span>
                                <span class="badge bg-label-info"><?= esc($positionName ?: 'บุคลากร') ?></span>
                                <span class="badge bg-label-secondary"><?= esc($departmentName ?: 'สำนักงาน') ?></span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <a href="<?= base_url('logout') ?>" class="btn btn-outline-danger btn-sm fw-semibold" title="ออกจากระบบเพื่อเปลี่ยนบัญชี">
                            <i class="bx bx-power-off me-1"></i> สลับบัญชี
                        </a>
                    </div>
                </div>
            </div>

            <!-- Modal Content: Role Selection -->
            <div class="p-4 p-md-5">
                <div class="text-center mb-4">
                    <h4 class="fw-bold text-dark mb-1">
                        <i class="bx bx-user-pin text-primary me-1"></i> เลือกสิทธิ์การเข้าใช้งานระบบ
                    </h4>
                    <p class="text-secondary small mb-0">
                        พบบัญชีของท่านมีสิทธิ์การใช้งานมากกว่า 1 สิทธิ์ในระบบ กรุณาเลือกบทบาทที่ต้องการเข้าใช้งานในครั้งนี้
                    </p>
                </div>

                <!-- Alert error if any -->
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
                        <i class="bx bx-error fs-4 me-2"></i>
                        <div><?= esc(session()->getFlashdata('error')) ?></div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="row g-4">
                    <?php foreach ($availableRoles as $key => $r): ?>
                        <?php 
                            $isAdmin = in_array($key, ['super_admin', 'admin'], true);
                            $cardClass = $isAdmin ? 'admin-option' : 'staff-option';
                        ?>
                        <div class="col-md-6">
                            <form action="<?= base_url('select-role') ?>" method="post" class="h-100">
                                <?= csrf_field() ?>
                                <input type="hidden" name="role" value="<?= esc($key) ?>">

                                <div class="role-option-card <?= $cardClass ?>" onclick="$(this).closest('form').submit();">
                                    <div>
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <div class="role-icon-box <?= $isAdmin ? 'bg-label-primary text-primary' : 'bg-label-success text-success' ?>">
                                                <i class="bx <?= esc($r['icon']) ?>"></i>
                                            </div>
                                            <span class="badge <?= esc($r['badge_class']) ?> text-white px-3 py-1 fw-bold fs-7">
                                                <?= esc($r['title']) ?>
                                            </span>
                                        </div>

                                        <h5 class="fw-bold text-dark mb-1">เข้าใช้งานในฐานะ <?= esc($r['title']) ?></h5>
                                        <div class="text-primary small fw-semibold mb-2"><?= esc($r['subtitle']) ?></div>

                                        <p class="text-secondary small mb-3 text-sarabun" style="line-height: 1.55;">
                                            <?= esc($r['description']) ?>
                                        </p>
                                    </div>

                                    <div class="pt-3 border-top mt-auto">
                                        <button type="submit" class="btn <?= $isAdmin ? 'btn-primary' : 'btn-success' ?> w-100 fw-bold shadow-xs py-2">
                                            <i class="bx bx-log-in me-1"></i> เข้าใช้งานสิทธิ์นี้
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Footer Hint -->
                <div class="mt-4 p-3 bg-light rounded-3 text-center border">
                    <small class="text-secondary">
                        <i class="bx bx-info-circle text-primary me-1"></i> 
                        <strong>คำแนะนำ:</strong> ท่านสามารถสลับบทบาทการใช้งานไปมาระหว่างวันได้ตลอดเวลาจากปุ่ม <strong>"สลับสิทธิ์"</strong> ที่แถบเมนูด้านบน
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="<?= base_url('assets/vendor/libs/jquery/jquery.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/libs/bootstrap/bootstrap.js') ?>"></script>
    <script src="<?= base_url('assets/js/submit-loading.js') ?>"></script>
</body>
</html>
