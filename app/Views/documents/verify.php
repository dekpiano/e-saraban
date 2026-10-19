<!DOCTYPE html>
<html lang="th" class="light-style" dir="ltr" data-theme="theme-default">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ตรวจสอบความถูกต้องของลายเซ็นดิจิทัล | School e-Office</title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=K2D:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="<?= base_url('assets/vendor/css/core.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/css/demo.css') ?>" />

    <style>
        body, html, input, button, select, textarea, h1, h2, h3, h4, h5, h6 { font-family: 'K2D', sans-serif !important; background-color: #f5f5f9; }
        .signature-preview { max-height: 80px; max-width: 200px; object-fit: contain; }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4 text-center">
                        <div class="mb-3">
                            <span class="badge bg-success p-3 rounded-circle">
                                <i class="bx bx-check-shield text-white display-4"></i>
                            </span>
                        </div>
                        <h4 class="fw-bold text-success mb-1">ผลการตรวจสอบลายเซ็นอิเล็กทรอนิกส์</h4>
                        <p class="text-muted">ระบบสารบรรณและเสนอเซ็นเกษียนหนังสืออิเล็กทรอนิกส์</p>

                        <?php if ($route): ?>
                            <div class="alert alert-success d-flex align-items-center text-start" role="alert">
                                <i class="bx bxs-check-circle fs-3 me-2"></i>
                                <div>
                                    <strong>เอกสารและลายเซ็นนี้ได้รับการรับรองความถูกต้อง</strong><br>
                                    ลงนามผ่านระบบสารบรรณอิเล็กทรอนิกส์โดยบุคลากรของโรงเรียน
                                </div>
                            </div>

                            <div class="card border text-start mb-4">
                                <div class="card-body">
                                    <h6 class="fw-bold text-primary mb-3"><i class="bx bx-file me-1"></i> รายละเอียดเอกสาร</h6>
                                    <div class="row g-2 mb-3">
                                        <div class="col-sm-4 text-muted">เลขที่หนังสือ:</div>
                                        <div class="col-sm-8 fw-bold"><?= esc($route['doc_no']) ?></div>

                                        <div class="col-sm-4 text-muted">เรื่อง:</div>
                                        <div class="col-sm-8 fw-semibold"><?= esc($route['title']) ?></div>
                                    </div>

                                    <hr />

                                    <h6 class="fw-bold text-primary mb-3"><i class="bx bx-pencil me-1"></i> รายละเอียดการลงนาม</h6>
                                    <div class="row g-2">
                                        <div class="col-sm-4 text-muted">ผู้ลงนาม:</div>
                                        <div class="col-sm-8 fw-bold">
                                            <?= esc($route['prefix'] . $route['first_name'] . ' ' . $route['last_name']) ?>
                                            <span class="badge bg-label-primary ms-1"><?= esc($route['position_name']) ?></span>
                                        </div>

                                        <div class="col-sm-4 text-muted">คำเกษียน/สั่งการ:</div>
                                        <div class="col-sm-8">“<?= esc($route['comment_preset']) ?>” <?= esc($route['comment_text'] ? '(' . $route['comment_text'] . ')' : '') ?></div>

                                        <div class="col-sm-4 text-muted">วัน-เวลาที่ลงนาม:</div>
                                        <div class="col-sm-8 fw-semibold text-dark"><?= esc($route['signed_at']) ?></div>

                                        <div class="col-sm-4 text-muted">IP Address:</div>
                                        <div class="col-sm-8"><code><?= esc($route['ip_address']) ?></code></div>

                                        <div class="col-sm-4 text-muted">ลายเซ็นที่ใช้:</div>
                                        <div class="col-sm-8">
                                            <?php if (!empty($route['signature_img'])): ?>
                                                <img src="<?= base_url($route['signature_img']) ?>" class="signature-preview border p-2 bg-white rounded" alt="Signature" />
                                            <?php else: ?>
                                                <span class="badge bg-secondary">ประทับลายเซ็นดิจิทัล</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="text-start p-3 bg-light rounded border mb-4">
                                <small class="text-muted d-block mb-1">SHA-256 Digital Verification Hash:</small>
                                <code class="user-select-all d-block text-break" style="font-size: 0.8rem;"><?= esc($hash) ?></code>
                            </div>

                        <?php else: ?>
                            <div class="alert alert-danger" role="alert">
                                <i class="bx bx-error-circle me-1"></i> ไม่พบข้อมูลลายเซ็นหรือรหัสยืนยันไม่ถูกต้องในระบบ
                            </div>
                        <?php endif; ?>

                        <a href="<?= base_url('login') ?>" class="btn btn-outline-primary">
                            <i class="bx bx-arrow-back me-1"></i> เข้าสู่ระบบ Saraban
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
