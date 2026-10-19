<!DOCTYPE html>
<html lang="th" class="light-style customizer-hide" dir="ltr" data-theme="theme-default">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>ลงชื่อเข้าใช้ด้วย Google | ระบบสารบรรณอิเล็กทรอนิกส์</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= base_url('assets/img/favicon/favicon.ico') ?>" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=K2D:wght@200;300;400;500;600;700;800&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet" />

    <!-- Icons -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/vendor/css/core.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/css/demo.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/vendor/css/pages/page-auth.css') ?>" />

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" />

    <style>
        body, html, input, button, select, textarea, .card-title, h1, h2, h3, h4, h5, h6, .btn, .form-control {
            font-family: 'K2D', sans-serif !important;
        }
        body {
            position: relative;
            background-color: #f8fafc;
            min-height: 100vh;
            overflow-x: hidden;
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
        .ambient-bg::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(66, 133, 244, 0.12) 1px, transparent 1px);
            background-size: 28px 28px;
            opacity: 0.65;
        }
        .floating-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.4;
            animation: floatBlob 20s ease-in-out infinite alternate;
        }
        .blob-1 {
            width: 450px;
            height: 450px;
            background: linear-gradient(135deg, rgba(66, 133, 244, 0.4), rgba(52, 168, 83, 0.25));
            top: -100px;
            right: -80px;
        }
        .blob-2 {
            width: 400px;
            height: 400px;
            background: linear-gradient(135deg, rgba(251, 188, 5, 0.3), rgba(234, 67, 53, 0.25));
            bottom: 5%;
            left: -100px;
            animation-duration: 24s;
            animation-delay: -6s;
        }
        @keyframes floatBlob {
            0% { transform: translate(0px, 0px) scale(1); }
            50% { transform: translate(30px, -35px) scale(1.05); }
            100% { transform: translate(-25px, 25px) scale(0.96); }
        }
        .glass-login-card {
            background: rgba(255, 255, 255, 0.96) !important;
            backdrop-filter: blur(16px) !important;
            -webkit-backdrop-filter: blur(16px) !important;
            border: 1px solid rgba(226, 232, 240, 0.8) !important;
            box-shadow: 0 12px 36px rgba(15, 23, 42, 0.08) !important;
            border-radius: 1.25rem !important;
        }
        /* Google Sign-in Official Styled Button */
        .btn-google-signin {
            background-color: #ffffff !important;
            color: #1f2937 !important;
            border: 1.5px solid #dadce0 !important;
            border-radius: 0.75rem !important;
            padding: 0.75rem 1.25rem !important;
            font-weight: 600 !important;
            font-size: 1.02rem !important;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
            box-shadow: 0 1px 3px rgba(60,64,67,0.08), 0 1px 2px rgba(60,64,67,0.16) !important;
        }
        .btn-google-signin:hover {
            background-color: #f8fafc !important;
            border-color: #4285f4 !important;
            box-shadow: 0 4px 12px rgba(66, 133, 244, 0.18) !important;
            transform: translateY(-1px);
            color: #1a73e8 !important;
        }
        .google-badge {
            display: inline-flex;
            align-items: center;
            background: #e8f0fe;
            color: #1967d2;
            padding: 0.35rem 0.75rem;
            border-radius: 2rem;
            font-size: 0.82rem;
            font-weight: 600;
        }
    </style>
    <!-- Google Identity Services (GIS) - Modern Sign In with Google -->
    <script src="https://accounts.google.com/gsi/client" async defer></script>
</head>

<body>
    <!-- Ambient Background -->
    <div class="ambient-bg" aria-hidden="true">
        <div class="floating-blob blob-1"></div>
        <div class="floating-blob blob-2"></div>
    </div>

    <div class="container-xxl position-relative" style="z-index: 1;">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner py-4" style="max-width: 480px;">

                <!-- Google Login Main Card -->
                <div class="card glass-login-card border-0">
                    <div class="card-body p-4 p-md-5">

                        <!-- Header with Google & School Icon -->
                        <div class="text-center mb-4">
                            <div class="d-inline-flex align-items-center justify-content-center p-3 rounded-circle shadow-sm mb-3" style="background: #ffffff; border: 1px solid #e2e8f0;">
                                <!-- Google G Logo (SVG) -->
                                <svg width="38" height="38" viewBox="0 0 24 24">
                                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                                </svg>
                            </div>
                            <h4 class="fw-bold mb-1 text-dark" style="font-family: 'K2D', sans-serif;">
                                ระบบสารบรรณและเกษียนหนังสือ
                            </h4>
                            <p class="text-secondary small mb-2">
                                โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์
                            </p>
                            <span class="google-badge">
                                <i class="bx bxs-shield-check me-1 fs-5"></i> Google Workspace for Education
                            </span>
                        </div>

                        <!-- Alerts -->
                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                                <i class="bx bx-error-circle me-1"></i> <?= session()->getFlashdata('error') ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (session()->getFlashdata('success')): ?>
                            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                                <i class="bx bx-check-circle me-1"></i> <?= session()->getFlashdata('success') ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <!-- Google Identity Services (GIS - One Tap & Official Button) -->
                        <div id="g_id_onload"
                             data-client_id="<?= esc(env('google.clientID')) ?>"
                             data-context="signin"
                             data-ux_mode="popup"
                             data-callback="handleGoogleCredentialResponse"
                             data-auto_prompt="true"
                             data-auto_select="true"
                             data-itp_support="true"
                             data-hd="skj.ac.th">
                        </div>

                        <!-- Form สำหรับส่ง Google ID Token ไปยืนยันตัวตนที่ Backend -->
                        <form id="googleCredentialForm" method="POST" action="<?= base_url('login/google/verify') ?>" style="display: none;">
                            <?= csrf_field() ?>
                            <input type="hidden" name="credential" id="googleCredentialInput">
                        </form>

                        <!-- Google Sign-in Modern Button Area -->
                        <div class="mb-3 text-center">
                            <div class="d-flex justify-content-center mb-2" style="min-height: 44px;">
                                <div class="g_id_signin"
                                     data-type="standard"
                                     data-shape="rectangular"
                                     data-theme="outline"
                                     data-text="signin_with"
                                     data-size="large"
                                     data-logo_alignment="left"
                                     data-width="360">
                                </div>
                            </div>
                            <div class="text-center text-muted small mt-2" style="font-size: 0.8rem;">
                                <i class="bx bx-check-shield text-success me-1"></i>
                                เข้าใช้งานเฉพาะบัญชี Google Workspace โรงเรียน <code>@skj.ac.th</code>
                            </div>
                        </div>

                        <!-- Back to public tracking -->
                        <div class="text-center mt-4 pt-3 border-top">
                            <a href="<?= base_url('/') ?>" class="text-secondary small text-decoration-none">
                                <i class="bx bx-arrow-back me-1"></i> กลับไปยังหน้าค้นหาและติดตามเอกสารสาธารณะ
                            </a>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Core JS -->
    <script src="<?= base_url('assets/vendor/libs/jquery/jquery.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/js/bootstrap.js') ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?= base_url('assets/js/app-swal.js?v=' . (file_exists(FCPATH . 'assets/js/app-swal.js') ? filemtime(FCPATH . 'assets/js/app-swal.js') : time())) ?>"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (session()->getFlashdata('error')): ?>
                AppAlert.error(<?= json_encode(session()->getFlashdata('error')) ?>, 'เข้าสู่ระบบไม่สำเร็จ');
            <?php endif; ?>
            <?php if (session()->getFlashdata('success')): ?>
                AppAlert.success(<?= json_encode(session()->getFlashdata('success')) ?>, 'สำเร็จ');
            <?php endif; ?>
        });
        function handleGoogleCredentialResponse(response) {
            if (response && response.credential) {
                document.getElementById('googleCredentialInput').value = response.credential;
                document.getElementById('googleCredentialForm').submit();
            }
        }
    </script>
</body>
</html>
