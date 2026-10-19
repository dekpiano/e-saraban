<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>คำขอไปราชการ <?= esc($travel['doc_no']) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
    $userRole      = session()->get('role');
    $isOfficer     = in_array($userRole, ['super_admin', 'admin', 'saraban', 'executive'], true);
    $stepReceivers = json_decode($travel['step_receivers'] ?? '{}', true) ?: [];
?>
<!-- Page Header & Key Summary Banner (Sneat Theme Indigo) -->
<div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #696cff 0%, #5f61e6 50%, #4338ca 100%); border-radius: 1rem; position: relative; overflow: hidden;">
    <div class="card-body p-4 text-white">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>" class="text-white opacity-75">แดชบอร์ด</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('admin/travel') ?>" class="text-white opacity-75">ทะเบียนไปราชการ</a></li>
                        <li class="breadcrumb-item active text-white fw-bold">รายละเอียดคำขอ</li>
                    </ol>
                </nav>
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <span class="badge bg-white text-primary fw-bold px-2 py-1 font-monospace shadow-xs">
                        <i class="bx bx-barcode me-1"></i><?= esc($travel['doc_no']) ?>
                    </span>
                    <?= travel_status_badge($travel['status']) ?>
                    <?php if (!empty($travel['current_receiver'])): ?>
                        <span class="badge bg-dark text-white fw-bold px-2 py-1 shadow-xs border border-white border-opacity-25">
                            <i class="bx bx-user-pin text-warning me-1"></i> ผู้ถือเอกสาร: <?= esc($travel['current_receiver']) ?>
                        </span>
                    <?php endif; ?>
                </div>
                <h4 class="fw-bold mb-1 text-white" style="font-family: 'K2D', sans-serif;">
                    <?= esc($travel['title']) ?>
                </h4>
                <div class="small text-white opacity-90 d-flex flex-wrap align-items-center gap-3 mt-1">
                    <span><i class="bx bx-user me-1"></i> <?= esc($travel['prefix'] . $travel['first_name'] . ' ' . $travel['last_name']) ?> (<?= esc($travel['position_name']) ?>)</span>
                    <span><i class="bx bx-map-pin me-1"></i> <?= esc($travel['destination']) ?> (<?= esc($travel['province']) ?>)</span>
                    <span><i class="bx bx-calendar me-1"></i> <?= thai_date($travel['start_date'], 'short') ?> - <?= thai_date($travel['end_date'], 'short') ?> (<?= esc($travel['total_days']) ?> วัน)</span>
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <?php if ($isOfficer): ?>
                    <button type="button" class="btn btn-success text-white btn-sm fw-bold px-3 shadow-sm" onclick="openDefaultTrackingModal()">
                        <i class="bx bx-edit-alt me-1 fs-6 align-middle"></i> บันทึกติดตามสถานะ
                    </button>
                    <button type="button" class="btn btn-warning text-dark btn-sm fw-bold px-3 shadow-xs" data-bs-toggle="modal" data-bs-target="#uploadTravelModal">
                        <i class="bx bx-upload me-1"></i> <?= empty($travel['file_path']) ? 'อัปโหลดคำสั่ง (PDF)' : 'เปลี่ยนไฟล์แนบ' ?>
                    </button>
                <?php endif; ?>
                <?php if (!empty($travel['file_path'])): ?>
                    <a href="<?= document_url($travel['file_path']) ?>" target="_blank" class="btn btn-white text-primary btn-sm fw-bold px-3 shadow-xs" style="background:#ffffff;">
                        <i class="bx bx-download me-1"></i> ไฟล์ฉบับเต็ม
                    </a>
                <?php endif; ?>
<?php
    $currentUserId = (int)session()->get('user_id');
    $canDelete     = $isOfficer || ($currentUserId === (int)$travel['user_id'] && $travel['status'] !== 'approved');
?>
                <?php if ($canDelete): ?>
                    <button type="button" class="btn btn-danger text-white btn-sm fw-bold px-3 shadow-xs" onclick="confirmDeleteTravel()">
                        <i class="bx bx-trash me-1"></i> ลบคำขอ
                    </button>
                    <!-- Form ซ่อนไว้สำหรับส่ง POST ลบข้อมูล -->
                    <form id="deleteTravelForm" action="<?= base_url('admin/travel/' . $travel['id'] . '/delete') ?>" method="POST" class="d-none">
                        <?= csrf_field() ?>
                    </form>
                <?php endif; ?>
                <a href="<?= base_url('admin/travel') ?>" class="btn btn-outline-light btn-sm fw-bold px-3 shadow-xs">
                    <i class="bx bx-arrow-back me-1"></i> ย้อนกลับ
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Workflow Stepper Container: แสดงให้เห็นทั้งหนังสือขาไป และ ขากลับ พร้อมกัน -->
<?php
    $isApproved        = ($travel['status'] === 'approved');
    $curStep           = (int)$travel['current_step'];
    $curRetStep        = (int)($travel['return_step'] ?? 0);
    $isApprovedByClerk = ($isApproved && !empty($travel['approver_title']) && str_contains($travel['approver_title'], 'ปลัด'));

    $trackingStages = [
        1 => ['name' => 'ยื่นขออนุมัติ', 'desc' => 'ผู้ขอยื่นคำขอในระบบ / ตรวจสอบเอกสาร', 'icon' => 'bx-paper-plane'],
        2 => ['name' => 'โรงเรียน', 'desc' => 'จัดทำเรื่อง / ตรวจเอกสารจริง / แนบไฟล์คำสั่ง', 'icon' => 'bx-building'],
        3 => ['name' => 'กองการศึกษา', 'desc' => 'กองการศึกษา ศาสนา และวัฒนธรรม', 'icon' => 'bx-book-reader'],
        4 => ['name' => 'กองการเจ้าหน้าที่', 'desc' => 'ตรวจสอบคุณสมบัติและระเบียบ อบจ.', 'icon' => 'bx-id-card'],
        5 => ['name' => 'สำนักปลัด', 'desc' => 'กลั่นกรองเสนอผู้บริหาร (ปลัดอนุมัติแทนได้)', 'icon' => 'bx-briefcase-alt-2'],
        6 => ['name' => 'นายก อบจ.', 'desc' => 'พิจารณาลงนามอนุมัติคำขอ', 'icon' => 'bx-crown'],
    ];

    $approvedAtStep = $curStep;
    if ($isApprovedByClerk) {
        $approvedAtStep = 5;
    } elseif ($isApproved && $curStep < 5) {
        $approvedAtStep = max(1, $curStep);
    } elseif ($isApproved && empty($travel['approver_title'])) {
        $approvedAtStep = max(1, $curStep);
    }

    $allNodeStages = [
        1 => ['name' => 'ผู้ขอยื่นเรื่อง', 'desc' => 'ผู้ขอรับหนังสือตัวจริงเรียบร้อย (เสร็จสิ้น)', 'icon' => 'bx-user-check'],
        2 => ['name' => 'โรงเรียน', 'desc' => 'งานสารบรรณ/ธุรการ รร. ลงรับแฟ้มตัวจริง', 'icon' => 'bx-building'],
        3 => ['name' => 'กองการศึกษา', 'desc' => 'กองการศึกษาฯ ส่งกลับโรงเรียน', 'icon' => 'bx-book-reader'],
        4 => ['name' => 'กองการเจ้าหน้าที่', 'desc' => 'กองการเจ้าหน้าที่ ส่งต่อกองการศึกษา', 'icon' => 'bx-id-card'],
        5 => ['name' => 'สำนักปลัด', 'desc' => 'สำนักปลัด อบจ. ส่งเอกสารตามสายงาน', 'icon' => 'bx-briefcase-alt-2'],
        6 => ['name' => 'นายก อบจ.', 'desc' => 'นายก อบจ. ออกเลขส่งคืนตัวจริง', 'icon' => 'bx-crown'],
    ];

    $returnStages = [];
    $retIndex = 1;
    for ($s = $approvedAtStep; $s >= 1; $s--) {
        $node = $allNodeStages[$s];
        $returnStages[$retIndex] = [
            'orig_step' => $s,
            'name'      => $node['name'],
            'desc'      => ($s === $approvedAtStep ? ($s === 6 ? 'นายก อบจ. ส่งคืนตัวจริง' : 'ผู้อนุมัติส่งคืนตัวจริง') : ($s === 1 ? 'ผู้ขอรับหนังสือตัวจริง (เสร็จสิ้น)' : $node['desc'])),
            'icon'      => $node['icon'],
        ];
        $retIndex++;
    }
    $totalReturnSteps = count($returnStages);
?>
<style>
.step-item-clickable {
    cursor: pointer;
    transition: all 0.2s ease-in-out;
    padding: 6px;
    border-radius: 10px;
    position: relative;
}
.step-item-clickable:hover {
    background-color: rgba(105, 108, 255, 0.08);
    transform: translateY(-2px);
}
.step-item-clickable:hover .step-bubble {
    box-shadow: 0 4px 12px rgba(105, 108, 255, 0.35);
    transform: scale(1.08);
}
.step-item-clickable .click-indicator {
    display: inline-block;
    transition: opacity 0.2s;
}
.step-bubble {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
</style>
<div class="card mb-4 border-0 shadow-sm" style="border-radius: 0.85rem;">
    <!-- 1. ส่วนหนังสือขาไป (เสนอขออนุมัติ) -->
    <div class="card-header bg-light py-2 px-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-primary text-white p-2 rounded">
                <i class="bx bx-right-arrow-alt fs-5"></i>
            </span>
            <div>
                <h6 class="mb-0 fw-bold text-dark">1. หนังสือขาไป (สายการเสนอขออนุมัติราชการ)</h6>
                <small class="text-muted">ขั้นตอนการเสนอแฟ้มเอกสารจากโรงเรียนไปยัง อบจ.</small>
            </div>
        </div>
        <div>
            <?php if (!$isApproved): ?>
                <span class="badge bg-primary text-white px-2 py-1"><i class="bx bx-loader-alt bx-spin me-1"></i> กำลังดำเนินการ: ขั้นที่ <?= $curStep ?></span>
            <?php else: ?>
                <span class="badge bg-success text-white px-2 py-1"><i class="bx bx-check-circle me-1"></i> ผ่านการอนุมัติเรียบร้อย</span>
            <?php endif; ?>
        </div>
    </div>
    <div class="card-body p-3 border-bottom">
        <div class="step-stepper mb-0">
            <?php foreach ($trackingStages as $stepNum => $stg): ?>
                <?php
                    $hasRecorder = !empty($stepReceivers[$stepNum]['name']);
                    $isCurrent = ($stepNum == $curStep && !$isApproved && $travel['status'] !== 'returned');
                    $isReturn  = ($travel['status'] === 'returned' && $stepNum == $curStep);
                    $isSkipped = ($isApprovedByClerk && $stepNum === 6);
                    
                    if ($isApproved) {
                        $isDone = ($stepNum <= 5 || !$isApprovedByClerk) && ($hasRecorder || $stepNum <= $curStep);
                    } else {
                        $isDone = ($stepNum < $curStep && $travel['status'] !== 'returned');
                    }

                    $bubbleClass = $isDone ? 'signed' : ($isReturn ? 'returned' : ($isSkipped ? 'bg-light text-muted border' : ($isCurrent ? 'active' : 'pending')));
                ?>
                <div class="step-item flex-fill <?= $isOfficer ? 'step-item-clickable' : '' ?>" 
                    <?= $isOfficer ? 'onclick="openOutboundTrackingModal(' . $stepNum . ')" title="คลิกเพื่อบันทึกสถานะขั้นตอนนี้"' : '' ?>>
                    <?php if ($stepNum < 6): ?>
                        <span class="position-absolute top-0 text-primary opacity-50 d-none d-md-block" style="right: -14px; transform: translateY(12px); z-index: 3;">
                            <i class="bx bx-chevron-right fs-4"></i>
                        </span>
                    <?php endif; ?>

                    <div class="step-bubble <?= $bubbleClass ?> position-relative">
                        <?php if ($isDone): ?>
                            <i class="bx bx-check fs-4"></i>
                        <?php elseif ($isReturn): ?>
                            <i class="bx bx-x fs-4"></i>
                        <?php elseif ($isSkipped): ?>
                            <i class="bx bx-check-shield text-muted fs-5"></i>
                        <?php elseif ($isCurrent): ?>
                            <i class="bx <?= $stg['icon'] ?> fs-5"></i>
                        <?php else: ?>
                            <?= $stepNum ?>
                        <?php endif; ?>
                        <?php if ($isOfficer): ?>
                            <span class="position-absolute bottom-0 end-0 bg-white text-primary rounded-circle border shadow-xs d-flex align-items-center justify-content-center" style="width: 18px; height: 18px; font-size: 10px;">
                                <i class="bx bx-edit"></i>
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="fw-bold text-dark small mb-0" style="font-size: 0.85rem;">
                        <?= $stepNum ?>. <?= esc($stg['name']) ?>
                    </div>
                    <div class="mt-1">
                        <?php if ($stepNum === 5 && $isApprovedByClerk): ?>
                            <span class="badge bg-success text-white" style="font-size: 0.68rem;">ปลัด อบจ. อนุมัติ</span>
                        <?php elseif ($stepNum === 6 && $isApprovedByClerk): ?>
                            <span class="badge bg-label-secondary" style="font-size: 0.68rem;">ข้ามขั้นตอนนี้</span>
                        <?php elseif ($isDone): ?>
                            <span class="badge bg-label-success text-success" style="font-size: 0.68rem;">ผ่านแล้ว</span>
                        <?php elseif ($isReturn): ?>
                            <span class="badge bg-danger text-white" style="font-size: 0.68rem;">ส่งคืนแก้ไข</span>
                        <?php elseif ($isCurrent): ?>
                            <span class="badge bg-primary text-white" style="font-size: 0.68rem;">อยู่ที่นี่</span>
                        <?php else: ?>
                            <span class="badge bg-label-secondary text-muted" style="font-size: 0.68rem;">รอ</span>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($stepReceivers[$stepNum]['name'])): ?>
                        <div class="mt-1">
                            <span class="text-secondary d-inline-block text-truncate" style="max-width: 130px; font-size: 0.68rem;" title="<?= esc($stepReceivers[$stepNum]['name']) ?>">
                                <i class="bx bx-user text-primary"></i> <?= esc($stepReceivers[$stepNum]['name']) ?>
                            </span>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- 2. ส่วนหนังสือขากลับ (ส่งคืนตัวจริง) -->
    <div class="card-header bg-light py-2 px-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-success text-white p-2 rounded">
                <i class="bx bx-left-arrow-alt fs-5"></i>
            </span>
            <div>
                <h6 class="mb-0 fw-bold text-dark">2. หนังสือขากลับ (สายการส่งมอบหนังสือตัวจริงคืนโรงเรียน)</h6>
                <small class="text-muted">ขั้นตอนการส่งมอบแฟ้มเอกสารตัวจริงย้อนกลับคืนสู่ผู้ขอ</small>
            </div>
        </div>
        <div>
            <?php if (!$isApproved): ?>
                <span class="badge bg-label-secondary text-muted px-2 py-1"><i class="bx bx-time me-1"></i> รอคำขออนุมัติเรียบร้อยก่อน</span>
            <?php elseif ($curRetStep >= $totalReturnSteps): ?>
                <span class="badge bg-success text-white px-2 py-1"><i class="bx bx-check-double me-1"></i> ผู้ขอรับหนังสือตัวจริงเรียบร้อยแล้ว</span>
            <?php else: ?>
                <span class="badge bg-warning text-dark px-2 py-1"><i class="bx bx-package me-1"></i> ส่งคืนขั้นที่: <?= $curRetStep ?>/<?= $totalReturnSteps ?></span>
            <?php endif; ?>
        </div>
    </div>
    <div class="card-body p-3">
        <div class="step-stepper flex-row-reverse mb-0" style="position: relative;">
            <?php foreach ($returnStages as $rNum => $rstg): ?>
                <?php
                    $isRetDone = ($curRetStep >= $rNum);
                    $isRetCur  = ($curRetStep == $rNum && $curRetStep < $totalReturnSteps && $isApproved);
                    $retReceiver = $stepReceivers['return_' . $rNum]['name'] ?? null;
                    $bubbleClass = $isRetDone ? 'signed' : ($isRetCur ? 'active' : 'pending');
                ?>
                <div class="step-item flex-fill <?= ($isOfficer && $isApproved) ? 'step-item-clickable' : '' ?>"
                    <?= ($isOfficer && $isApproved) ? 'onclick="openReturnTrackingModal(' . $rNum . ')" title="คลิกเพื่อบันทึกส่งมอบขากลับขั้นตอนนี้"' : '' ?>>
                    <?php if ($rNum < $totalReturnSteps): ?>
                        <span class="position-absolute top-0 text-success opacity-50 d-none d-md-block" style="left: -14px; transform: translateY(12px); z-index: 3;">
                            <i class="bx bx-chevron-left fs-4"></i>
                        </span>
                    <?php endif; ?>

                    <div class="step-bubble <?= $bubbleClass ?> position-relative" style="<?= !$isApproved ? 'opacity: 0.4;' : '' ?>">
                        <?php if ($isRetDone): ?>
                            <i class="bx bx-check fs-4"></i>
                        <?php elseif ($isRetCur): ?>
                            <i class="bx <?= $rstg['icon'] ?> fs-5"></i>
                        <?php else: ?>
                            <?= $rNum ?>
                        <?php endif; ?>
                        <?php if ($isOfficer && $isApproved): ?>
                            <span class="position-absolute bottom-0 end-0 bg-white text-success rounded-circle border shadow-xs d-flex align-items-center justify-content-center" style="width: 18px; height: 18px; font-size: 10px;">
                                <i class="bx bx-edit"></i>
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="fw-bold text-dark small mb-0" style="font-size: 0.85rem;">
                        <?= $rNum ?>. <?= esc($rstg['name']) ?>
                    </div>
                    <div class="mt-1">
                        <?php if (!$isApproved): ?>
                            <span class="badge bg-label-secondary text-muted" style="font-size: 0.68rem;">รออนุมัติ</span>
                        <?php elseif ($isRetDone): ?>
                            <span class="badge bg-success text-white" style="font-size: 0.68rem;">เรียบร้อย</span>
                        <?php elseif ($isRetCur): ?>
                            <span class="badge bg-warning text-dark" style="font-size: 0.68rem;">อยู่ที่นี่</span>
                        <?php else: ?>
                            <span class="badge bg-label-secondary text-muted" style="font-size: 0.68rem;">รอ</span>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($retReceiver)): ?>
                        <div class="mt-1">
                            <span class="text-secondary d-inline-block text-truncate" style="max-width: 130px; font-size: 0.68rem;" title="<?= esc($retReceiver) ?>">
                                <i class="bx bx-user text-success"></i> <?= esc($retReceiver) ?>
                            </span>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="row">
    <!-- Left Column: Details, Transport, Teaching Assign, PDF Preview -->
    <div class="col-lg-7 mb-4">
        <!-- Details & Trip Metadata Card -->
        <div class="card mb-4 border-0 shadow-sm" style="border-radius: 0.85rem;">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <span class="badge bg-label-primary p-2 rounded me-2">
                        <i class="bx bx-file-find fs-4 text-primary"></i>
                    </span>
                    <div>
                        <h5 class="card-title mb-0 fw-bold text-dark">รายละเอียดการขออนุมัติไปราชการ</h5>
                        <small class="text-muted">ข้อมูลการเดินทาง งบประมาณ และการปฏิบัติหน้าที่</small>
                    </div>
                </div>
                <div>
                    <?= travel_status_badge($travel['status']) ?>
                </div>
            </div>
            <div class="card-body p-4">
                <!-- Info Grid -->
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="p-3 rounded bg-light border-0 h-100">
                            <small class="text-muted d-block mb-1"><i class="bx bx-user text-primary me-1"></i> ผู้ขออนุมัติ (เจ้าของเรื่อง)</small>
                            <h6 class="text-dark fw-bold mb-0"><?= esc($travel['prefix'] . $travel['first_name'] . ' ' . $travel['last_name']) ?></h6>
                            <div class="small text-secondary mt-1"><?= esc($travel['position_name']) ?> • <span class="badge bg-white text-dark border"><?= esc($travel['department_name']) ?></span></div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 rounded bg-light border-0 h-100">
                            <small class="text-muted d-block mb-1"><i class="bx bx-barcode text-primary me-1"></i> เลขที่หนังสือราชการ</small>
                            <span class="text-primary fw-bold fs-6 font-monospace"><?= esc($travel['doc_no']) ?></span>
                            <div class="small text-muted mt-1"><i class="bx bx-calendar-check me-1"></i> ยื่นเมื่อ: <?= thai_date($travel['created_at'], 'short', true) ?></div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="p-3 rounded border border-primary border-opacity-25" style="background: #f8faff;">
                            <small class="text-primary fw-bold text-uppercase d-block mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">เรื่อง / วัตถุประสงค์การไปราชการ</small>
                            <h5 class="text-dark fw-bold mb-0" style="line-height: 1.5; font-family: 'K2D', sans-serif;">
                                <?= esc($travel['title']) ?>
                            </h5>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <small class="text-muted d-block mb-1"><i class="bx bx-map-pin text-danger me-1"></i> สถานที่ไปราชการ</small>
                        <div class="fw-bold text-dark fs-6"><?= esc($travel['destination']) ?></div>
                        <span class="badge bg-label-secondary mt-1"><i class="bx bx-map me-1"></i> จ. <?= esc($travel['province']) ?></span>
                    </div>

                    <div class="col-sm-6">
                        <small class="text-muted d-block mb-1"><i class="bx bx-time-five text-info me-1"></i> ระยะเวลาปฏิบัติงาน</small>
                        <div class="fw-bold text-dark fs-6"><?= thai_date($travel['start_date'], 'short') ?> - <?= thai_date($travel['end_date'], 'short') ?></div>
                        <span class="badge bg-label-info mt-1"><i class="bx bx-calendar me-1"></i> รวม <?= esc($travel['total_days']) ?> วัน</span>
                    </div>

                    <!-- Transport & Budget Details -->
                    <?php
                        $transportLabels = [
                            'school_bus'       => 'รถตู้ / รถยนต์ส่วนกลางของโรงเรียน',
                            'private_car'      => 'รถยนต์ส่วนบุคคล',
                            'public_transport' => 'รถโดยสารสาธารณะ / เครื่องบิน',
                            'other'            => 'พาหนะอื่น ๆ',
                        ];
                        $budgetLabels = [
                            'school'    => 'งบประมาณโรงเรียน',
                            'organizer' => 'ผู้จัดอบรม / เจ้าภาพรับผิดชอบ',
                            'no_budget' => 'ไม่ขอเบิกงบประมาณ (ฟรี/ไม่ใช้จ่าย)',
                            'other'     => 'งบประมาณอื่น ๆ',
                        ];
                    ?>
                    <div class="col-sm-6">
                        <div class="p-2 px-3 rounded border bg-white h-100">
                            <small class="text-muted d-block mb-1"><i class="bx bx-car text-primary me-1"></i> พาหนะเดินทาง</small>
                            <div class="fw-semibold text-dark small">
                                <?= esc($transportLabels[$travel['transport_type']] ?? $travel['transport_type'] ?? 'ไม่ระบุ') ?>
                                <?php if (!empty($travel['transport_detail'])): ?>
                                    <div class="text-muted mt-1 small"><i class="bx bx-detail me-1"></i><?= esc($travel['transport_detail']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="p-2 px-3 rounded border bg-white h-100">
                            <small class="text-muted d-block mb-1"><i class="bx bx-wallet text-success me-1"></i> งบประมาณ</small>
                            <div class="fw-semibold text-dark small">
                                <?= esc($budgetLabels[$travel['budget_type']] ?? $travel['budget_type'] ?? 'ไม่ระบุ') ?>
                                <?php if (!empty($travel['budget_amount']) && (float)$travel['budget_amount'] > 0): ?>
                                    <span class="badge bg-label-success ms-1"><?= number_format($travel['budget_amount'], 2) ?> บาท</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($travel['companions'])): ?>
                        <div class="col-12">
                            <div class="p-2 px-3 rounded border bg-white">
                                <small class="text-muted d-block mb-1"><i class="bx bx-group text-primary me-1"></i> ผู้ร่วมเดินทาง</small>
                                <div class="text-dark small fw-semibold"><?= nl2br(esc($travel['companions'])) ?></div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($travel['teaching_assign'])): ?>
                        <div class="col-12">
                            <div class="p-2 px-3 rounded border bg-white">
                                <small class="text-muted d-block mb-1"><i class="bx bx-transfer-alt text-warning me-1"></i> บันทึกการมอบหมายการสอนแทน</small>
                                <div class="text-dark small"><?= nl2br(esc($travel['teaching_assign'])) ?></div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <hr class="my-4">

                <!-- Document Checklist Items Inside Card -->
                <?php
                    $attachedList = !empty($travel['attached_docs']) ? json_decode($travel['attached_docs'], true) : [];
                    if (!is_array($attachedList)) {
                        $attachedList = [];
                    }
                    $allChecklistItems = [
                        'หนังสือเชิญ / ต้นเรื่อง'     => 'bx-envelope text-primary',
                        'กำหนดการ'                  => 'bx-calendar text-info',
                        'คำสั่ง'                     => 'bx-file text-warning',
                        'หนังสือขออนุญาตจากผู้ปกครอง' => 'bx-user-check text-success',
                        'บันทึกขอแลกคาบ'             => 'bx-transfer-alt text-danger',
                        'โครงการ'                   => 'bx-spreadsheet text-primary',
                        'ระเบียบ'                   => 'bx-book-bookmark text-secondary',
                        'ประมาณการโครงการ'          => 'bx-calculator text-dark',
                        'หนังสือขอใช้รถ'            => 'bx-car text-info',
                        'อื่น ๆ'                    => 'bx-dots-horizontal-rounded text-secondary',
                    ];
                ?>
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center">
                            <i class="bx bx-check-shield text-primary me-2 fs-5"></i> การตรวจสอบเอกสารที่ส่งมาด้วย (Checklist)
                        </h6>
                        <span class="badge bg-primary text-white rounded-pill px-2 py-1">
                            <?= count($attachedList) ?> รายการที่แนบมา
                        </span>
                    </div>
                    <div class="row g-2">
                        <?php foreach ($allChecklistItems as $item => $icon): ?>
                            <?php $isChecked = in_array($item, $attachedList); ?>
                            <div class="col-md-6 col-12">
                                <div class="d-flex align-items-center justify-content-between p-2 rounded border <?= $isChecked ? 'bg-light border-primary border-opacity-50' : 'bg-white text-muted border-light' ?>" style="min-height: 40px;">
                                    <div class="d-flex align-items-center min-w-0">
                                        <i class="bx <?= $icon ?> me-2 flex-shrink-0"></i>
                                        <span class="<?= $isChecked ? 'fw-bold text-dark' : 'text-muted' ?> text-truncate" style="font-size: 0.85rem;">
                                            <?= esc($item) ?>
                                            <?php if ($item === 'อื่น ๆ' && $isChecked && !empty($travel['other_doc_detail'])): ?>
                                                <span class="badge bg-info text-white ms-1 fw-normal"><?= esc($travel['other_doc_detail']) ?></span>
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                    <div>
                                        <?php if ($isChecked): ?>
                                            <span class="badge bg-success text-white py-1 px-2" style="font-size: 0.72rem;"><i class="bx bx-check me-1"></i> มีเอกสาร</span>
                                        <?php else: ?>
                                            <span class="badge bg-light text-muted border py-1 px-2" style="font-size: 0.72rem;">ไม่มี</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>



        <!-- Attachment PDF Preview Card -->
        <div class="card mb-4">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="bx bxs-file-pdf text-danger me-2"></i> เอกสารคำสั่ง / หนังสือเชิญแนบ
                </h5>
                <div class="d-flex align-items-center gap-2">
                    <?php if ($isOfficer): ?>
                        <button type="button" class="btn btn-sm btn-outline-primary shadow-xs" data-bs-toggle="modal" data-bs-target="#uploadTravelModal">
                            <i class="bx bx-upload me-1"></i> <?= empty($travel['file_path']) ? 'อัปโหลดไฟล์เอกสาร' : 'เปลี่ยนไฟล์แนบ' ?>
                        </button>
                    <?php endif; ?>
                    <?php if (!empty($travel['file_path'])): ?>
                        <a href="<?= document_url($travel['file_path']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                            <i class="bx bx-fullscreen me-1"></i> เต็มจอ
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($travel['file_path'])): ?>
                    <div style="height: 480px; width: 100%; background: #525659;">
                        <iframe src="<?= document_url($travel['file_path']) ?>#toolbar=0" style="width: 100%; height: 100%; border: none;"></iframe>
                    </div>
                <?php else: ?>
                    <div class="p-4 p-md-5 text-center">
                        <div class="avatar avatar-xl bg-label-warning mx-auto mb-3" style="width: 64px; height: 64px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                            <i class="bx bx-file-blank fs-2 text-warning"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-2">ยังไม่มีไฟล์เอกสารแนบสำหรับคำขอนี้</h6>
                        <?php if ($isOfficer): ?>
                            <?php if ((int)$travel['current_step'] === 1): ?>
                                <div class="alert alert-info d-inline-block text-start small mb-3 p-3 shadow-none border" style="max-width: 620px; background: #e7f7ff; color: #005b82;">
                                    <div class="d-flex align-items-start gap-2">
                                        <i class="bx bx-info-circle fs-4 text-info flex-shrink-0 mt-1"></i>
                                        <div>
                                            <strong>ขั้นตอนที่ 1: บันทึกยื่นขออนุมัติเรียบร้อยแล้ว</strong><br>
                                            ระบบบันทึกคำขอและตรวจสอบรายการเอกสารที่ส่งมาด้วย (Checklist) เรียบร้อย เมื่อเจ้าหน้าที่ธุรการตรวจสอบเอกสารฉบับจริงแล้ว จะดำเนินการอัปโหลดไฟล์คำสั่ง / หนังสือเชิญ (PDF) ใน <strong>ขั้นตอนที่ 2 (โรงเรียนจัดทำเรื่อง)</strong>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-warning d-inline-block text-start small mb-3 p-3 shadow-none border" style="max-width: 620px; background: #fffbe6; color: #614700; border-color: #ffe58f !important;">
                                    <div class="d-flex align-items-start gap-2">
                                        <i class="bx bx-upload fs-4 text-warning flex-shrink-0 mt-1"></i>
                                        <div>
                                            <strong>ขั้นตอนที่ 2: โรงเรียนจัดทำเรื่อง / แนบไฟล์</strong><br>
                                            เจ้าหน้าที่ธุรการโปรดตรวจสอบเอกสารและคลิกปุ่ม <strong>"อัปโหลดไฟล์เอกสาร (PDF)"</strong> ด้านล่างนี้เพื่อแนบไฟล์เอกสารคำสั่งก่อนส่งต่อให้กองการศึกษา
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div>
                                <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#uploadTravelModal">
                                    <i class="bx bx-upload me-1"></i> <?= (int)$travel['current_step'] >= 2 ? 'แนบไฟล์เอกสารคำสั่ง (PDF) ตอนนี้' : 'อัปโหลดไฟล์เอกสาร (PDF)' ?>
                                </button>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-light border text-start small mb-0 p-3 shadow-none d-inline-block" style="max-width: 620px; background: #fdfdfd; text-align: left;">
                                <div class="d-flex align-items-start gap-2">
                                    <i class="bx bx-shield-quarter fs-4 text-primary flex-shrink-0 mt-1"></i>
                                    <div>
                                        <strong class="text-dark">สงวนสิทธิ์การแนบเอกสารสำหรับเจ้าหน้าที่ธุรการ:</strong><br>
                                        <span class="text-muted">ขณะนี้คำขออยู่ในกระบวนการดำเนินงาน เจ้าหน้าที่ธุรการโรงเรียนจะเป็นผู้ตรวจสอบเอกสารฉบับจริง และดำเนินการแนบไฟล์คำสั่ง / หนังสือเชิญ (PDF) เข้าสู่ระบบใน <strong>ขั้นตอนที่ 2 (โรงเรียนจัดทำเรื่อง)</strong> ท่านสามารถติดตามสถานะการพิจารณาได้จากแผงควบคุมติดตามงานด้านขวามือ</span>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($isOfficer): ?>
        <!-- Modal Upload File (เฉพาะเจ้าหน้าที่เท่านั้น พร้อมแถบแสดงเปอร์เซ็นต์โหลด) -->
        <div class="modal fade" id="uploadTravelModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 1rem;">
                    <form id="travelUploadForm" action="<?= base_url('admin/travel/' . $travel['id'] . '/upload') ?>" method="POST" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <div class="modal-header bg-light py-3 border-bottom">
                            <h5 class="modal-title fw-bold text-dark mb-0">
                                <i class="bx bx-cloud-upload text-primary me-2 fs-4"></i> อัปโหลดเอกสารคำสั่ง / หนังสือเชิญ (PDF)
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="uploadModalCloseBtn"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="mb-3" id="fileSelectSection">
                                <label class="form-label required fw-bold text-dark">เลือกไฟล์เอกสารคำสั่ง (รองรับไฟล์ PDF)</label>
                                
                                <!-- Hidden actual file input -->
                                <input type="file" name="doc_file" id="travelDocFileInput" accept=".pdf,application/pdf" style="display: none;" required />

                                <!-- Drag & Drop Box -->
                                <div id="travelModalDropzone" class="dropzone-box" onclick="triggerBrowseTravelModalFile()">
                                    <div class="d-flex flex-column align-items-center">
                                        <div class="mb-2">
                                            <span class="badge bg-label-primary p-3 rounded-circle">
                                                <i class="bx bxs-cloud-upload fs-2 text-primary"></i>
                                            </span>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-1">ลากและวางไฟล์ PDF ที่นี่ หรือคลิกเพื่อเลือกไฟล์</h6>
                                        <p class="text-muted small mb-2">รองรับเฉพาะเอกสารรูปแบบ <strong>.PDF</strong> (ระบบรองรับการแบ่งส่วน Chunked Upload)</p>
                                        <button type="button" class="btn btn-outline-primary btn-sm px-3 shadow-xs">
                                            <i class="bx bx-file me-1"></i> เลือกไฟล์ PDF จากอุปกรณ์
                                        </button>
                                    </div>
                                </div>

                                <!-- File Preview Box -->
                                <div id="travelModalFilePreview" class="dropzone-preview mt-3">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                                        <div class="d-flex align-items-center">
                                            <div class="badge bg-label-danger p-2 rounded-3 me-2">
                                                <i class="bx bxs-file-pdf fs-3 text-danger"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-bold text-dark text-break" id="travelModalPreviewName">filename.pdf</h6>
                                                <small class="text-muted" id="travelModalPreviewSize">0 KB</small>
                                            </div>
                                        </div>
                                        <div>
                                            <button type="button" class="btn btn-outline-danger btn-xs py-1 px-2" onclick="removeTravelModalFile(event)">
                                                <i class="bx bx-trash me-1"></i> ลบ / เลือกใหม่
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-text small text-secondary mt-2">
                                    <i class="bx bx-info-circle me-1 text-primary"></i> ไฟล์จะถูกส่งไปจัดเก็บบนเซิร์ฟเวอร์หลัก (skj.nsnpao.go.th) และแสดงในกล่องพรีวิวทันที
                                    <span class="d-block text-success fw-semibold mt-1">
                                        <i class="bx bx-check-shield me-1"></i> ระบบรองรับการแบ่งส่วนข้อมูลอัตโนมัติ (Chunked Upload) ไม่จำกัดขนาด 1 MB
                                    </span>
                                </div>
                            </div>

                            <!-- Progress Bar Container (ซ่อนไว้ก่อนอัปโหลด) -->
                            <div id="uploadProgressSection" class="d-none">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-bold text-dark small" id="uploadStatusText">
                                        <i class="bx bx-loader-alt bx-spin text-primary me-1"></i> กำลังอัปโหลดไฟล์...
                                    </span>
                                    <span class="badge bg-primary text-white fw-bold fs-6" id="uploadPercentBadge">0%</span>
                                </div>
                                <div class="progress mb-2" style="height: 16px; border-radius: 8px; background-color: #e2e8f0;">
                                    <div id="uploadProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <div class="small text-muted d-flex justify-content-between flex-wrap gap-1">
                                    <div>
                                        <span id="uploadLoadedSize" class="fw-semibold text-dark">0.00 MB</span> / 
                                        <span id="uploadTotalSize" class="text-secondary">0.00 MB</span>
                                    </div>
                                    <div>
                                        <span id="uploadSpeedText" class="badge bg-light text-primary border me-1"><i class="bx bx-tachometer me-1"></i>0 KB/s</span>
                                        <span id="uploadRemainingText" class="badge bg-light text-dark border"><i class="bx bx-time me-1"></i>คำนวณเวลา...</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Alert Box for AJAX errors -->
                            <div id="uploadErrorAlert" class="alert alert-danger d-none small mb-0 py-2 mt-3"></div>
                        </div>
                        <div class="modal-footer bg-light py-2 border-top">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" id="uploadCancelBtn">ยกเลิก</button>
                            <button type="submit" class="btn btn-primary fw-bold px-3 shadow-sm" id="uploadSubmitBtn">
                                <i class="bx bx-upload me-1"></i> เริ่มอัปโหลดไฟล์
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- ======================================================== -->
        <!-- หมวด: รายงานผลการไปราชการ (Travel Performance Report)      -->
        <!-- ======================================================== -->
        <?php
            $currentUserId = (int)session()->get('user_id');
            $canReport     = $isOfficer || ($currentUserId === (int)$travel['user_id']);
            $isReported    = ($travel['status'] === 'reported');
            $isApproved    = in_array($travel['status'], ['approved', 'reported'], true);
        ?>
        <div class="card mb-4 border shadow-sm" id="reportSection" style="border-radius: 0.85rem;">
            <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="d-flex align-items-center">
                    <span class="badge <?= $isReported ? 'bg-success' : ($isApproved ? 'bg-primary' : 'bg-secondary') ?> p-2 rounded-circle me-2">
                        <i class="bx <?= $isReported ? 'bx-check-double' : 'bx-task' ?> text-white fs-4"></i>
                    </span>
                    <div>
                        <h5 class="card-title mb-0 fw-bold text-dark" style="font-family: 'K2D', sans-serif;">
                            รายงานผลการไปราชการ
                        </h5>
                        <small class="text-muted">บันทึกข้อความสรุปผลการปฏิบัติราชการและเอกสารแนบ</small>
                    </div>
                </div>
                <div>
                    <?php if ($isReported): ?>
                        <span class="badge bg-success text-white fw-bold px-3 py-1.5 shadow-xs">
                            <i class="bx bx-check-circle me-1"></i> รายงานผลแล้วเมื่อ <?= !empty($travel['reported_at']) ? thai_date($travel['reported_at'], 'short', true) : '-' ?>
                        </span>
                    <?php elseif ($isApproved): ?>
                        <span class="badge bg-label-warning text-dark fw-bold px-3 py-1.5 shadow-xs">
                            <i class="bx bx-time-five me-1"></i> รอส่งรายงานผล
                        </span>
                    <?php else: ?>
                        <span class="badge bg-label-secondary text-muted fw-bold px-3 py-1.5">
                            <i class="bx bx-lock-alt me-1"></i> รอคำขอได้รับอนุมัติก่อน
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card-body p-4">
                <!-- ข้อมูลอ้างอิงถึงหนังสือขออนุมัติไปราชการต้นเรื่อง (Official Reference) -->
                <div class="alert alert-light border border-primary border-start-4 mb-4 shadow-xs" style="border-left: 5px solid #696cff !important; background-color: #f8faff;">
                    <div class="d-flex align-items-start gap-3">
                        <span class="badge bg-label-primary p-2 rounded-circle flex-shrink-0 mt-1">
                            <i class="bx bx-bookmark-alt fs-4 text-primary"></i>
                        </span>
                        <div class="flex-grow-1">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-1">
                                <span class="badge bg-primary text-white font-monospace px-2 py-1">
                                    <i class="bx bx-file me-1"></i> อ้างถึงหนังสือขออนุมัติเลขที่: <?= esc($travel['doc_no']) ?>
                                </span>
                                <small class="text-muted">
                                    <i class="bx bx-calendar me-1"></i> ยื่นคำขอเมื่อ <?= thai_date($travel['created_at'], 'medium') ?>
                                </small>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">
                                เรื่อง: <?= esc($travel['title']) ?>
                            </h6>
                            <div class="small text-secondary d-flex flex-wrap align-items-center gap-3">
                                <span><i class="bx bx-user me-1 text-primary"></i> ผู้ขอ: <strong><?= esc($travel['prefix'] . $travel['first_name'] . ' ' . $travel['last_name']) ?></strong></span>
                                <span><i class="bx bx-map-pin me-1 text-danger"></i> <?= esc($travel['destination']) ?> (<?= esc($travel['province']) ?>)</span>
                                <span><i class="bx bx-calendar me-1 text-success"></i> <?= thai_date($travel['start_date'], 'short') ?> - <?= thai_date($travel['end_date'], 'short') ?> (<?= esc($travel['total_days']) ?> วัน)</span>
                                <?php if (!empty($travel['companions'])): ?>
                                    <span class="w-100 text-truncate"><i class="bx bx-group me-1 text-info"></i> ผู้ร่วมเดินทาง: <?= esc($travel['companions']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if ($isReported): ?>
                    <!-- แสดงรายงานผลที่บันทึกแล้ว -->
                    <div id="reportedDisplaySection">
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label fw-bold text-dark fs-6 mb-0">
                                    <i class="bx bx-detail text-primary me-1"></i> สรุปผลการปฏิบัติราชการและความรู้/ประโยชน์ที่ได้รับ:
                                </label>
                                <div class="d-flex gap-2">
                                    <a href="<?= base_url('admin/travel-reports/' . $travel['id']) ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bx bx-link-external me-1"></i> หน้ารายงานผลฉบับเต็ม
                                    </a>
                                    <?php if ($canReport): ?>
                                        <button type="button" class="btn btn-sm btn-outline-warning text-dark fw-bold" onclick="toggleReportEditMode()">
                                            <i class="bx bx-edit-alt me-1"></i> แก้ไขรายงาน
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="p-3 bg-light rounded-3 border text-dark" style="white-space: pre-wrap; line-height: 1.8; font-size: 0.95rem; min-height: 80px;">
                                <?= nl2br(esc($travel['report_detail'])) ?>
                            </div>
                        </div>

                        <!-- เล่มรายงาน / เกียรติบัตร (PDF) -->
                        <div class="mb-2">
                            <label class="form-label fw-bold text-dark fs-6 d-flex justify-content-between align-items-center mb-2">
                                <span><i class="bx bxs-file-pdf text-danger me-1"></i> เอกสารเล่มรายงานสรุปผล / เกียรติบัตร (PDF):</span>
                                <?php if (!empty($travel['report_file'])): ?>
                                    <div class="d-flex gap-2">
                                        <a href="<?= document_url($travel['report_file']) ?>" target="_blank" class="btn btn-xs btn-outline-primary">
                                            <i class="bx bx-fullscreen me-1"></i> ดูเต็มจอ
                                        </a>
                                        <a href="<?= document_url($travel['report_file']) ?>" download class="btn btn-xs btn-outline-secondary">
                                            <i class="bx bx-download me-1"></i> ดาวน์โหลด
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </label>

                            <?php if (!empty($travel['report_file'])): ?>
                                <div class="border rounded-3 p-3 bg-light">
                                    <div class="d-flex align-items-center justify-content-between p-2 bg-white rounded border mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="badge bg-label-danger p-2 rounded-3 me-3">
                                                <i class="bx bxs-file-pdf fs-2 text-danger"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-bold text-dark text-break">เอกสารเล่มรายงาน / เกียรติบัตร</h6>
                                                <small class="text-success"><i class="bx bx-check-circle me-1"></i>แนบไฟล์เรียบร้อยแล้ว</small>
                                            </div>
                                        </div>
                                        <a href="<?= document_url($travel['report_file']) ?>" target="_blank" class="btn btn-sm btn-primary px-3 shadow-xs">
                                            <i class="bx bx-file me-1"></i> เปิดอ่านเอกสาร PDF
                                        </a>
                                    </div>
                                    <div style="height: 480px; width: 100%; background: #525659; border-radius: 6px; overflow: hidden;">
                                        <iframe src="<?= document_url($travel['report_file']) ?>#toolbar=0" style="width: 100%; height: 100%; border: none;"></iframe>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-light border text-muted small p-3">
                                    <i class="bx bx-info-circle me-1"></i> ยังไม่มีการแนบไฟล์เล่มรายงาน (สามารถคลิกปุ่มแก้ไขรายงานเพื่อแนบไฟล์ PDF ได้)
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- แบบฟอร์มแก้ไขรายงานผล (ซ่อนไว้ก่อน กดปุ่มแก้ไขรายงานเพื่อแสดง) -->
                    <?php if ($canReport): ?>
                    <div id="reportEditSection" style="display: none;">
                        <div class="alert alert-warning py-2 px-3 small mb-3 d-flex align-items-center justify-content-between">
                            <div>
                                <i class="bx bx-edit text-warning me-1"></i> โหมดแก้ไขข้อมูลรายงานผลการไปราชการ
                            </div>
                            <button type="button" class="btn btn-outline-secondary btn-xs" onclick="toggleReportEditMode()">
                                <i class="bx bx-x me-1"></i> ยกเลิกการแก้ไข
                            </button>
                        </div>
                        <form action="<?= base_url('admin/travel/' . $travel['id'] . '/report') ?>" method="POST" enctype="multipart/form-data">
                            <?= csrf_field() ?>
                            <div class="mb-3">
                                <label class="form-label required fw-bold text-dark">
                                    สรุปผลการปฏิบัติราชการและความรู้/ประโยชน์ที่ได้รับ:
                                </label>
                                <textarea name="report_detail" class="form-control" rows="5" required placeholder="ระบุสรุปสาระสำคัญของการเข้าร่วมประชุม/อบรม/ปฏิบัติราชการ และผลการดำเนินงาน..."><?= esc($travel['report_detail']) ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark">
                                    แนบเล่มรายงานสรุปผล / เกียรติบัตร (PDF)
                                    <span class="badge bg-label-secondary ms-1 small" style="font-size:0.75rem;">หากไม่ต้องการเปลี่ยนไฟล์เดิม ให้เว้นว่างไว้</span>
                                </label>
                                
                                <input type="file" name="report_file" id="reportEditFileInput" accept=".pdf,application/pdf" style="display: none;" />

                                <div id="reportEditDropzone" class="dropzone-box" onclick="triggerBrowseReportEditFile()">
                                    <div class="d-flex flex-column align-items-center">
                                        <div class="mb-2">
                                            <span class="badge bg-label-primary p-3 rounded-circle">
                                                <i class="bx bxs-cloud-upload fs-2 text-primary"></i>
                                            </span>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-1">ลากและวางไฟล์ PDF ใหม่ที่นี่ หรือคลิกเพื่อเลือกไฟล์</h6>
                                        <p class="text-muted small mb-2">รองรับเฉพาะเอกสารรูปแบบ <strong>.PDF</strong> (เล่มรายงาน หรือ เกียรติบัตร)</p>
                                        <button type="button" class="btn btn-outline-primary btn-sm px-3 shadow-xs">
                                            <i class="bx bx-file me-1"></i> เลือกไฟล์ PDF ใหม่จากคอมพิวเตอร์
                                        </button>
                                    </div>
                                </div>

                                <div id="reportEditFilePreview" class="dropzone-preview mt-3">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                                        <div class="d-flex align-items-center">
                                            <div class="badge bg-label-danger p-2 rounded-3 me-3">
                                                <i class="bx bxs-file-pdf fs-2 text-danger"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-bold text-dark text-break" id="reportEditPreviewName">filename.pdf</h6>
                                                <div class="d-flex align-items-center gap-2 mt-1">
                                                    <small class="text-muted" id="reportEditPreviewSize">0 KB</small>
                                                    <span class="badge bg-label-success" style="font-size: 0.7rem;"><i class="bx bx-check-circle me-1"></i>พร้อมบันทึกแทนที่</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeReportEditFile(event)">
                                                <i class="bx bx-trash me-1"></i> ยกเลิกไฟล์นี้
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4 pt-2 border-top">
                                <button type="button" class="btn btn-outline-secondary" onclick="toggleReportEditMode()">ยกเลิก</button>
                                <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">
                                    <i class="bx bx-save me-1"></i> บันทึกการแก้ไขรายงานผล
                                </button>
                            </div>
                        </form>
                    </div>
                    <?php endif; ?>

                <?php elseif ($isApproved): ?>
                    <!-- กรณีอนุมัติแล้ว แต่ยังไม่ได้ส่งรายงานผล -->
                    <?php if ($canReport): ?>
                        <form action="<?= base_url('admin/travel/' . $travel['id'] . '/report') ?>" method="POST" enctype="multipart/form-data">
                            <?= csrf_field() ?>
                            <div class="mb-3">
                                <label class="form-label required fw-bold text-dark">
                                    <i class="bx bx-edit text-primary me-1"></i> สรุปผลการปฏิบัติราชการและความรู้/ประโยชน์ที่ได้รับ:
                                </label>
                                <textarea name="report_detail" class="form-control" rows="5" required placeholder="ระบุสรุปสาระสำคัญของการเข้าร่วมประชุม/อบรม/ปฏิบัติราชการ ความรู้และประโยชน์ที่ได้รับต่อการจัดการเรียนการสอน หรือการปฏิบัติงานในโรงเรียน..."></textarea>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark">
                                    <i class="bx bxs-file-pdf text-danger me-1"></i> แนบเล่มรายงานสรุปผล / เกียรติบัตร (PDF)
                                    <span class="badge bg-label-info ms-1 small" style="font-size:0.75rem;">ไม่บังคับแนบไฟล์</span>
                                </label>

                                <input type="file" name="report_file" id="reportCreateFileInput" accept=".pdf,application/pdf" style="display: none;" />

                                <!-- Drag & Drop Box -->
                                <div id="reportCreateDropzone" class="dropzone-box" onclick="triggerBrowseReportCreateFile()">
                                    <div class="d-flex flex-column align-items-center">
                                        <div class="mb-2">
                                            <span class="badge bg-label-primary p-3 rounded-circle">
                                                <i class="bx bxs-cloud-upload fs-1 text-primary"></i>
                                            </span>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1">ลากและวางไฟล์ PDF ที่นี่ หรือคลิกเพื่อเลือกไฟล์</h5>
                                        <p class="text-muted small mb-2">รองรับเฉพาะเอกสารรูปแบบ <strong>.PDF</strong> (เล่มรายงานสรุปผล หรือ เกียรติบัตรการเข้าร่วม)</p>
                                        <button type="button" class="btn btn-outline-primary btn-sm px-3 shadow-xs">
                                            <i class="bx bx-file me-1"></i> เลือกไฟล์ PDF จากคอมพิวเตอร์
                                        </button>
                                    </div>
                                </div>

                                <!-- File Preview Box -->
                                <div id="reportCreateFilePreview" class="dropzone-preview mt-3">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                                        <div class="d-flex align-items-center">
                                            <div class="badge bg-label-danger p-2 rounded-3 me-3">
                                                <i class="bx bxs-file-pdf fs-2 text-danger"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-bold text-dark text-break" id="reportCreatePreviewName">filename.pdf</h6>
                                                <div class="d-flex align-items-center gap-2 mt-1">
                                                    <small class="text-muted" id="reportCreatePreviewSize">0 KB</small>
                                                    <span class="badge bg-label-success" style="font-size: 0.7rem;"><i class="bx bx-check-circle me-1"></i>พร้อมบันทึกรายงาน</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeReportCreateFile(event)">
                                                <i class="bx bx-trash me-1"></i> ลบ / เลือกใหม่
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                                <button type="submit" class="btn btn-success px-4 py-2 fw-bold shadow-sm" style="font-size: 1rem;">
                                    <i class="bx bx-send me-1"></i> บันทึกส่งรายงานผลการไปราชการ
                                </button>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <div class="avatar avatar-lg bg-label-warning mx-auto mb-3" style="width: 56px; height: 56px; display:flex; align-items:center; justify-content:center; border-radius: 50%;">
                                <i class="bx bx-time-five fs-2 text-warning"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">อยู่ระหว่างรอผู้ไปราชการจัดทำและส่งรายงานผล</h6>
                            <p class="text-muted small mb-0">คำขอได้รับการอนุมัติเรียบร้อยแล้ว เมื่อผู้ไปราชการปฏิบัติราชการเสร็จสิ้นจะเข้ามารายงานผลผ่านระบบนี้</p>
                        </div>
                    <?php endif; ?>

                <?php else: ?>
                    <!-- กรณียังอยู่ระหว่างขออนุมัติ หรือส่งคืนแก้ไข -->
                    <div class="text-center py-4">
                        <div class="avatar avatar-lg bg-label-secondary mx-auto mb-3" style="width: 56px; height: 56px; display:flex; align-items:center; justify-content:center; border-radius: 50%;">
                            <i class="bx bx-lock-alt fs-2 text-secondary"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">ยังไม่สามารถส่งรายงานผลได้ในขณะนี้</h6>
                        <p class="text-muted small mb-0">
                            ระบบรายงานผลจะเปิดให้ใช้งานโดยอัตโนมัติเมื่อคำขอไปราชการนี้ได้รับการลงนามอนุมัติเสร็จสมบูรณ์เรียบร้อยแล้ว
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <!-- Right Column: Document Tracking Action & Workflow -->
    <div class="col-lg-5 mb-4">

        <?php
            $isApproved    = ($travel['status'] === 'approved');
            $curStep       = (int)$travel['current_step'];
            $nextStep      = min(7, $curStep + 1);
            $curReturnStep = (int)($travel['return_step'] ?? 0);
        ?>

        <!-- กล่องสรุปสถานะปัจจุบัน (กระชับพื้นที่ พร้อมปุ่มเปิดบันทึกติดตามงาน) -->
        <div class="card border mb-4 shadow-sm" style="border-radius: 0.85rem;">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <span class="badge bg-label-primary p-2 rounded me-2">
                        <i class="bx bx-check-shield fs-4 text-primary"></i>
                    </span>
                    <div>
                        <h6 class="card-title mb-0 fw-bold text-dark">สถานะการดำเนินเรื่องปัจจุบัน</h6>
                        <small class="text-muted">ความคืบหน้าของแฟ้มเอกสารและผู้ถือเรื่อง</small>
                    </div>
                </div>
                <div>
                    <?= travel_status_badge($travel['status']) ?>
                </div>
            </div>
            <div class="card-body pt-3 pb-3">
                <div class="p-3 bg-light rounded-3 mb-3 border">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small">สถานะคำขอ:</span>
                        <?= travel_status_badge($travel['status']) ?>
                    </div>
                    <?php if ($isApproved): ?>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">ผู้อนุมัติ:</span>
                            <strong class="text-success small"><i class="bx bx-check-double me-1"></i><?= esc($travel['approver_title'] ?: 'นายก อบจ.') ?></strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">การส่งมอบตัวจริง:</span>
                            <?= travel_return_badge($curReturnStep) ?>
                        </div>
                        <?php if (!empty($travel['return_note'])): ?>
                            <div class="mt-2 pt-2 border-top">
                                <small class="text-muted d-block"><i class="bx bx-detail me-1"></i>หมายเหตุการส่งคืน:</small>
                                <span class="small text-dark fw-semibold"><?= esc($travel['return_note']) ?></span>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">ขั้นตอนปัจจุบัน:</span>
                            <?= travel_step_badge($curStep, $travel['status']) ?>
                        </div>
                        <?php if (!empty($travel['current_receiver'])): ?>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted small">ผู้ถือเอกสาร:</span>
                                <span class="badge bg-white text-dark border fw-bold"><i class="bx bx-user me-1 text-primary"></i><?= esc($travel['current_receiver']) ?></span>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <?php if ($isOfficer): ?>
                    <div class="d-grid gap-2">
                        <?php if ($isApproved): ?>
                            <button type="button" class="btn btn-success fw-bold py-2 shadow-sm" onclick="openReturnTrackingModal(<?= min($totalReturnSteps, $curReturnStep + 1) ?>)">
                                <i class="bx bx-package me-1 fs-5 align-middle"></i> บันทึกส่งมอบขากลับ (ขั้นที่ <?= min($totalReturnSteps, $curReturnStep + 1) ?>)
                            </button>
                        <?php else: ?>
                            <button type="button" class="btn btn-primary fw-bold py-2 shadow-sm" onclick="openOutboundTrackingModal(<?= $nextStep <= 7 ? $nextStep : 6 ?>)">
                                <i class="bx bx-edit-alt me-1 fs-5 align-middle"></i> บันทึกติดตามสถานะ (ขั้นที่ <?= $nextStep <= 7 ? $nextStep : 6 ?>)
                            </button>
                        <?php endif; ?>
                    </div>
                    <div class="text-center mt-2">
                        <small class="text-muted">
                            <i class="bx bx-pointer me-1 text-primary"></i> หรือคลิกที่ <strong>ไอคอนขั้นตอนบนหัว Stepper</strong> ด้านบนได้โดยตรง
                        </small>
                    </div>
                <?php else: ?>
                    <div class="alert alert-light border small mb-0 p-2 text-muted">
                        <i class="bx bx-info-circle me-1 text-info"></i> ระบบติดตามความคืบหน้าแบบเรียลไทม์ (Read-Only สำหรับผู้ขอ)
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($isOfficer): ?>
            <!-- ============================================================== -->
            <!-- Modal 1: ติดตามขาไป เสนอขออนุมัติ (Outbound Tracking Modal)   -->
            <!-- ============================================================== -->
            <div class="modal fade" id="outboundTrackingModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content border-0 shadow-lg" style="border-radius: 1rem;">
                        <form action="<?= base_url('admin/travel/' . $travel['id'] . '/update-step') ?>" method="POST" id="outboundTrackingForm">
                            <?= csrf_field() ?>
                            <input type="hidden" name="tracking_mode" value="outbound">
                            <input type="hidden" name="approval_type" id="approvalTypeInput" value="">

                            <div class="modal-header bg-primary text-white py-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2 bg-white text-primary rounded d-flex align-items-center justify-content-center">
                                        <i class="bx bx-git-commit fs-4"></i>
                                    </div>
                                    <div>
                                        <h5 class="modal-title text-white fw-bold mb-0">บันทึกติดตามขั้นตอนเอกสาร (ขาไป)</h5>
                                        <small class="text-white-50">สำหรับธุรการ / สารบรรณ อัปเดตความคืบหน้าของแฟ้มคำขอ</small>
                                    </div>
                                </div>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <div class="modal-body p-4">
                                <?php
                                    $allSteps = [
                                        1 => ['num' => 1, 'name' => 'ขั้นที่ 1: ยื่นขออนุมัติ', 'desc' => 'ผู้ขอยื่นคำขอในระบบ / ตรวจเอกสารที่ส่งมาด้วย'],
                                        2 => ['num' => 2, 'name' => 'ขั้นที่ 2: โรงเรียน', 'desc' => 'จัดทำเรื่อง / ตรวจเอกสารฉบับจริง / แนบไฟล์คำสั่ง'],
                                        3 => ['num' => 3, 'name' => 'ขั้นที่ 3: กองการศึกษา', 'desc' => 'กองการศึกษา ศาสนา และวัฒนธรรม'],
                                        4 => ['num' => 4, 'name' => 'ขั้นที่ 4: กองการเจ้าหน้าที่', 'desc' => 'ตรวจสอบคุณสมบัติและระเบียบ อบจ.'],
                                        5 => ['num' => 5, 'name' => 'ขั้นที่ 5: สำนักปลัด', 'desc' => 'กลั่นกรองเสนอผู้บริหาร อบจ. (ปลัดอนุมัติแทนได้)'],
                                        6 => ['num' => 6, 'name' => 'ขั้นที่ 6: นายก อบจ.', 'desc' => 'รอลงนามอนุมัติ'],
                                        7 => ['num' => 7, 'name' => 'อนุมัติแล้ว: นายก อบจ.', 'desc' => 'ลงนามอนุมัติเรียบร้อยแล้ว (เสร็จสิ้น)'],
                                    ];

                                    $canSelect = function($sVal) use ($curStep, $nextStep) {
                                        return ($sVal === $curStep || $sVal === $nextStep);
                                    };
                                ?>

                                <!-- คำแนะนำการป้องกันการข้ามขั้นตอน -->
                                <div class="alert alert-primary py-2 px-3 small mb-3 d-flex align-items-center shadow-none border-0" style="background: #e7e7ff; color: #43449e;">
                                    <i class="bx bx-shield-quarter fs-5 me-2 flex-shrink-0"></i>
                                    <div>
                                        <strong>ระบบป้องกันการข้ามขั้นตอน:</strong> สามารถเลือกได้เฉพาะขั้นตอนปัจจุบัน (<span class="fw-bold">ขั้นที่ <?= $curStep ?></span>) หรือขั้นตอนถัดไป (<span class="fw-bold text-primary">ขั้นที่ <?= $nextStep <= 6 ? $nextStep : 'อนุมัติ' ?></span>)
                                    </div>
                                </div>

                                <?php if (empty($travel['file_path'])): ?>
                                    <!-- แจ้งเตือนต้องแนบไฟล์คำสั่งก่อนไปขั้นที่ 3 -->
                                    <div class="alert alert-danger py-2 px-3 small mb-3 shadow-sm border border-danger d-flex align-items-center" style="background-color: #fee2e2; color: #991b1b;">
                                        <i class="bx bxs-error-circle fs-4 me-2 flex-shrink-0 text-danger"></i>
                                        <div class="flex-grow-1">
                                            <strong class="d-block text-danger">ยังไม่ได้แนบไฟล์เอกสารคำสั่ง (PDF):</strong>
                                            <span>ในขั้นตอนที่ 2 ต้องอัปโหลดไฟล์คำสั่งก่อน จึงจะสามารถกดบันทึกส่งต่อให้กองการศึกษา (ขั้นที่ 3) ได้</span>
                                            <div class="mt-2">
                                                <button type="button" class="btn btn-xs btn-danger fw-bold shadow-xs" data-bs-toggle="modal" data-bs-target="#uploadTravelModal">
                                                    <i class="bx bx-upload me-1"></i> อัปโหลดไฟล์คำสั่ง (PDF) ทันที
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- จุดตัดสินใจที่สำนักปลัด (ขั้นที่ 5) -->
                                <?php if ($curStep >= 5): ?>
                                    <div class="card border border-warning mb-3 shadow-none" style="background: #fffbeb;">
                                        <div class="card-body p-3">
                                            <div class="fw-bold text-dark small mb-2 d-flex align-items-center">
                                                <i class="bx bx-git-branch text-warning fs-5 me-1"></i> จุดตัดสินใจการอนุมัติ (สำนักปลัด):
                                            </div>
                                            <div class="row g-2">
                                                <div class="col-6">
                                                    <button type="button" class="btn btn-sm btn-success w-100 fw-bold py-2 shadow-sm" onclick="setDualApproval('clerk')">
                                                        <i class="bx bx-check-circle me-1"></i> ปลัด อบจ. อนุมัติ<br>
                                                        <small class="fw-normal" style="font-size:0.68rem;">(ปฏิบัติราชการแทน)</small>
                                                    </button>
                                                </div>
                                                <div class="col-6">
                                                    <button type="button" class="btn btn-sm btn-primary w-100 fw-bold py-2 shadow-sm" onclick="setDualApproval('president_step')">
                                                        <i class="bx bx-crown me-1"></i> เสนอต่อ นายก อบจ.<br>
                                                        <small class="fw-normal" style="font-size:0.68rem;">(ไปที่ขั้นที่ 6)</small>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <div class="row g-3">
                                    <div class="col-md-7">
                                        <label class="form-label fw-bold text-dark d-flex justify-content-between align-items-center">
                                            <span><i class="bx bx-check-shield text-primary me-1"></i> ปรับขั้นตอนที่เอกสารเดินทางไปถึง</span>
                                            <span class="badge bg-label-info fw-normal text-dark" style="font-size: 0.7rem;">ปัจจุบัน: ขั้นที่ <?= esc($curStep) ?></span>
                                        </label>
                                        <select class="form-select form-select-lg fw-bold" name="step" id="trackingStepSelect" required onchange="handleStepSelectChange(this)">
                                            <?php foreach ($allSteps as $sVal => $sInfo): ?>
                                                <?php
                                                    $isPast    = ($sVal < $curStep);
                                                    $isCurrent = ($sVal === $curStep);
                                                    $isNext    = ($sVal === $nextStep);
                                                    $disabled  = !$canSelect($sVal);
                                                    $selected  = ($isNext || ($isCurrent && $nextStep > 7));
                                                ?>
                                                <option value="<?= $sVal ?>" 
                                                    <?= $selected ? 'selected' : '' ?> 
                                                    <?= $disabled ? 'disabled' : '' ?>
                                                    style="<?= $isNext ? 'font-weight: bold; background: #e7e7ff; color: #43449e;' : ($disabled ? 'color: #a1acb8; background: #f8f9fa;' : '') ?>"
                                                >
                                                    <?php if ($isNext): ?>
                                                        👉 <?= esc($sInfo['name']) ?> (<?= esc($sInfo['desc']) ?>) ★ ขั้นตอนถัดไป
                                                    <?php elseif ($isCurrent): ?>
                                                        📍 <?= esc($sInfo['name']) ?> (<?= esc($sInfo['desc']) ?>) [ขั้นตอนปัจจุบัน]
                                                    <?php elseif ($isPast): ?>
                                                        ✓ <?= esc($sInfo['name']) ?> (ผ่านขั้นตอนนี้แล้ว)
                                                    <?php else: ?>
                                                        🔒 <?= esc($sInfo['name']) ?> (ยังไม่ถึงขั้นตอนนี้ - ห้ามข้าม)
                                                    <?php endif; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="col-md-5">
                                        <label class="form-label fw-semibold text-dark">สถานะคำขอ</label>
                                        <select class="form-select form-select-lg" name="status" id="trackingStatusSelect">
                                            <option value="pending" <?= $travel['status'] === 'pending' ? 'selected' : '' ?>>กำลังดำเนินการ (Pending)</option>
                                            <option value="approved" <?= $travel['status'] === 'approved' ? 'selected' : '' ?>>อนุมัติเรียบร้อย (Approved)</option>
                                            <option value="returned" <?= $travel['status'] === 'returned' ? 'selected' : '' ?>>ส่งคืนเอกสาร (Returned)</option>
                                        </select>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label fw-bold text-dark d-flex justify-content-between align-items-center">
                                            <span><i class="bx bx-user-check text-primary me-1"></i> ผู้บันทึกข้อมูลขั้นตอนนี้</span>
                                            <span class="badge bg-label-primary" style="font-size: 0.7rem;">บันทึกอัตโนมัติ</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white"><i class="bx bx-user text-primary"></i></span>
                                            <input type="text" class="form-control fw-semibold bg-light" value="<?= esc(session()->get('full_name') ?: (session()->get('first_name') . ' ' . session()->get('last_name'))) ?>" readonly />
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label fw-semibold text-dark">ปุ่มลัดเลือกขั้นตอน & ข้อความด่วน</label>
                                        <div class="d-flex flex-wrap gap-1 mb-2">
                                            <button type="button" class="btn btn-xs <?= $curStep == 1 ? 'btn-outline-primary active' : 'btn-outline-secondary' ?>" onclick="selectStepAndNote(1, 'ผู้ขอยื่นขออนุมัติในระบบเรียบร้อย (ตรวจสอบรายการเอกสารที่ส่งมาด้วย)')" <?= $canSelect(1) ? '' : 'disabled style="opacity:0.35;"' ?>>1. ยื่นขออนุมัติ</button>
                                            <button type="button" class="btn btn-xs <?= $nextStep == 2 ? 'btn-primary' : 'btn-outline-primary' ?>" onclick="selectStepAndNote(2, 'เอกสารอยู่ที่โรงเรียน (ธุรการจัดทำเรื่อง/ตรวจสอบเอกสาร/แนบไฟล์)')" <?= $canSelect(2) ? '' : 'disabled style="opacity:0.35;"' ?>>2. โรงเรียน (แนบไฟล์)</button>
                                            <button type="button" class="btn btn-xs <?= $nextStep == 3 ? 'btn-primary' : 'btn-outline-info' ?>" onclick="selectStepAndNote(3, 'ส่งเรื่องถึงกองการศึกษาเรียบร้อยแล้ว')" <?= $canSelect(3) ? '' : 'disabled style="opacity:0.35;"' ?>>3. ส่งกองการศึกษา</button>
                                            <button type="button" class="btn btn-xs <?= $nextStep == 4 ? 'btn-primary' : 'btn-outline-info' ?>" onclick="selectStepAndNote(4, 'ส่งเรื่องถึงกองการเจ้าหน้าที่เรียบร้อยแล้ว')" <?= $canSelect(4) ? '' : 'disabled style="opacity:0.35;"' ?>>4. ส่งกองการเจ้าหน้าที่</button>
                                            <button type="button" class="btn btn-xs <?= $nextStep == 5 ? 'btn-primary' : 'btn-outline-warning text-dark' ?>" onclick="selectStepAndNote(5, 'ส่งเรื่องถึงสำนักปลัดเรียบร้อยแล้ว')" <?= $canSelect(5) ? '' : 'disabled style="opacity:0.35;"' ?>>5. ส่งสำนักปลัด</button>
                                            <button type="button" class="btn btn-xs <?= $nextStep == 6 ? 'btn-primary' : 'btn-outline-danger' ?>" onclick="selectStepAndNote(6, 'เสนอแฟ้มถึงห้องนายก อบจ. เพื่อพิจารณา')" <?= $canSelect(6) ? '' : 'disabled style="opacity:0.35;"' ?>>6. เสนอนายก อบจ.</button>
                                            <button type="button" class="btn btn-xs btn-outline-success" onclick="selectStepAndNote(7, 'นายก อบจ. ลงนามอนุมัติเรียบร้อยแล้ว')" <?= $canSelect(7) ? '' : 'disabled style="opacity:0.35;"' ?>>นายกฯ อนุมัติ</button>
                                        </div>
                                        <textarea class="form-control" name="tracking_note" id="trackingNoteInput" rows="2" placeholder="ระบุตำแหน่งของแฟ้มเอกสาร หรือหมายเหตุเพิ่มเติม..."></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer bg-light py-2 border-top">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                                <button type="submit" class="btn btn-primary fw-bold px-4 shadow-sm">
                                    <i class="bx bx-save me-1 fs-5 align-middle"></i> บันทึกอัปเดตขั้นตอนเอกสาร
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- ============================================================== -->
            <!-- Modal 2: บันทึกส่งมอบขากลับ (Return Tracking Modal)              -->
            <!-- ============================================================== -->
            <div class="modal fade" id="returnTrackingModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content border-0 shadow-lg" style="border-radius: 1rem;">
                        <form action="<?= base_url('admin/travel/' . $travel['id'] . '/update-step') ?>" method="POST" id="returnTrackingForm">
                            <?= csrf_field() ?>
                            <input type="hidden" name="tracking_mode" value="return">

                            <div class="modal-header bg-success text-white py-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2 bg-white text-success rounded d-flex align-items-center justify-content-center">
                                        <i class="bx bx-package fs-4"></i>
                                    </div>
                                    <div>
                                        <h5 class="modal-title text-white fw-bold mb-0">บันทึกส่งมอบเอกสารขากลับ</h5>
                                        <small class="text-white-50">สำหรับธุรการ ติดตามหนังสือตัวจริงส่งคืนผู้ขอ</small>
                                    </div>
                                </div>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <div class="modal-body p-4">
                                <div class="alert alert-success py-2 px-3 small mb-3 border-0 d-flex align-items-center">
                                    <i class="bx bx-check-shield fs-4 me-2 flex-shrink-0"></i>
                                    <div>
                                        <strong>อนุมัติเรียบร้อยแล้ว:</strong> โดย <span class="fw-bold"><?= esc($travel['approver_title'] ?: 'นายก อบจ.') ?></span>
                                    </div>
                                </div>

                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label fw-bold text-dark d-flex justify-content-between align-items-center">
                                            <span><i class="bx bx-transfer text-success me-1"></i> ขั้นตอนการส่งมอบขากลับ</span>
                                            <span class="badge bg-label-info text-dark" style="font-size: 0.7rem;">ปัจจุบัน: ขั้นที่ <?= esc($curReturnStep) ?></span>
                                        </label>
                                        <select class="form-select form-select-lg fw-bold" name="return_step" id="returnStepSelect" required>
                                            <?php foreach ($returnStages as $rIdx => $rItem): ?>
                                                <?php
                                                    $nextRetStep = $curReturnStep + 1;
                                                    $canPickRet = ($rIdx === $curReturnStep || $rIdx === $nextRetStep);
                                                    $isRetSelected = ($rIdx === $nextRetStep || ($curReturnStep >= $totalReturnSteps && $rIdx === $totalReturnSteps));
                                                ?>
                                                <option value="<?= $rIdx ?>" 
                                                    <?= $isRetSelected ? 'selected' : '' ?>
                                                    <?= !$canPickRet ? 'disabled' : '' ?>
                                                    style="<?= $rIdx === $nextRetStep ? 'font-weight: bold; background: #e8fadf; color: #71dd37;' : (!$canPickRet ? 'color: #a1acb8; background: #f8f9fa;' : '') ?>"
                                                >
                                                    <?php if ($rIdx === $nextRetStep): ?>
                                                        👉 <?= $rIdx ?>. <?= esc($rItem['name']) ?> (<?= esc($rItem['desc']) ?>) ★ ขั้นตอนถัดไป
                                                    <?php elseif ($rIdx === $curReturnStep): ?>
                                                        📍 <?= $rIdx ?>. <?= esc($rItem['name']) ?> [ขั้นตอนปัจจุบัน]
                                                    <?php elseif ($rIdx < $curReturnStep): ?>
                                                        ✓ <?= $rIdx ?>. <?= esc($rItem['name']) ?> (ผ่านขั้นตอนนี้แล้ว)
                                                    <?php else: ?>
                                                        🔒 <?= $rIdx ?>. <?= esc($rItem['name']) ?> (ยังไม่ถึงขั้นตอนนี้ - ห้ามข้าม)
                                                    <?php endif; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label fw-bold text-dark d-flex justify-content-between align-items-center">
                                            <span><i class="bx bx-user-check text-success me-1"></i> ผู้บันทึกข้อมูลขั้นตอนนี้</span>
                                            <span class="badge bg-label-success" style="font-size: 0.7rem;">บันทึกอัตโนมัติ</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white"><i class="bx bx-user text-success"></i></span>
                                            <input type="text" class="form-control fw-semibold bg-light" value="<?= esc(session()->get('full_name') ?: (session()->get('first_name') . ' ' . session()->get('last_name'))) ?>" readonly />
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label fw-semibold text-dark">ปุ่มลัดระบุสถานะและหมายเหตุ</label>
                                        <div class="d-flex flex-wrap gap-1 mb-2">
                                            <?php foreach ($returnStages as $rIdx => $rItem): ?>
                                                <?php
                                                    $nextRetStep = $curReturnStep + 1;
                                                    $canClick = ($rIdx === $curReturnStep || $rIdx === $nextRetStep);
                                                    $btnColor = ($rIdx === $totalReturnSteps) ? 'btn-outline-success' : (($rIdx === 1) ? 'btn-outline-primary' : 'btn-outline-info');
                                                ?>
                                                <button type="button" class="btn btn-sm <?= $btnColor ?> <?= !$canClick ? 'disabled opacity-50' : '' ?>" onclick="setReturnStepAndNote(<?= $rIdx ?>, 'ส่งมอบเอกสารขากลับ: <?= esc($rItem['name']) ?> เรียบร้อยแล้ว')">
                                                    <i class="bx <?= $rItem['icon'] ?> me-1"></i> <?= $rIdx ?>. <?= esc($rItem['name']) ?>
                                                    <?php if ($rIdx === $nextRetStep): ?>
                                                        <span class="badge bg-success text-white ms-1">ถัดไป</span>
                                                    <?php endif; ?>
                                                </button>
                                            <?php endforeach; ?>
                                        </div>
                                        <label class="form-label small text-muted mb-1">บันทึกหมายเหตุ / ผู้รับหนังสือตัวจริง</label>
                                        <textarea class="form-control" name="return_note" id="returnNoteInput" rows="2" placeholder="ระบุรายละเอียด เช่น ผู้มารับเอกสาร วันที่รับ หรือหมายเลขทะเบียนรับ..."><?= esc($travel['return_note'] ?? '') ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer bg-light py-2 border-top">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                                <button type="submit" class="btn btn-success fw-bold px-4 shadow-sm">
                                    <i class="bx bx-save me-1 fs-5 align-middle"></i> บันทึกสถานะการส่งมอบขากลับ
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>



        <!-- ประวัติการติดตามความคืบหน้า (Tracking History / Audit Logs) -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0 fw-bold text-dark">
                    <i class="bx bx-history text-info me-2"></i> ประวัติการติดตามความคืบหน้า (Tracking History)
                </h6>
                <span class="badge bg-label-secondary"><?= count($logs) ?> รายการ</span>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush small">
                    <?php if (empty($logs)): ?>
                        <li class="list-group-item text-center py-3 text-muted">ยังไม่มีบันทึกประวัติ</li>
                    <?php else: ?>
                        <?php foreach ($logs as $log): ?>
                            <li class="list-group-item p-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="badge <?= $log['action'] === 'APPROVE' ? 'bg-success' : ($log['action'] === 'TRACK' ? 'bg-primary' : 'bg-label-info') ?>">
                                        <?= esc($log['action']) ?>
                                    </span>
                                    <span class="text-muted" style="font-size: 0.72rem;">
                                        <i class="bx bx-time me-1"></i> <?= thai_date($log['created_at'], 'short', true) ?>
                                    </span>
                                </div>
                                <div class="text-dark fw-semibold">
                                    <?= esc($log['prefix'] . $log['first_name'] . ' ' . $log['last_name']) ?>:
                                </div>
                                <div class="text-muted mt-1">
                                    <?= esc($log['detail']) ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
const hasUploadedTravelFile = <?= !empty($travel['file_path']) ? 'true' : 'false' ?>;

document.addEventListener('DOMContentLoaded', function() {
    const trackingForm = document.getElementById('outboundTrackingForm');
    if (trackingForm) {
        trackingForm.addEventListener('submit', function(e) {
            const stepSelect = document.getElementById('trackingStepSelect');
            const approvalInput = document.getElementById('approvalTypeInput');
            const statusSelect = document.getElementById('trackingStatusSelect');

            const chosenStep = stepSelect ? parseInt(stepSelect.value) : 1;
            const chosenApproval = approvalInput ? approvalInput.value : '';
            const chosenStatus = statusSelect ? statusSelect.value : '';

            // ถ้าก้าวข้ามขั้นตอนที่ 2 หรืออนุมัติ แต่ยังไม่มีไฟล์เอกสารแนบ
            if (!hasUploadedTravelFile && (chosenStep > 2 || chosenApproval === 'clerk' || chosenApproval === 'president' || chosenStatus === 'approved')) {
                e.preventDefault();
                AppAlert.warning('ในขั้นตอนที่ 2 (โรงเรียนจัดทำเรื่อง) เจ้าหน้าที่ธุรการจำเป็นต้องอัปโหลดไฟล์คำสั่ง / หนังสือเชิญ (PDF) ให้เรียบร้อยก่อนส่งเรื่องต่อไปยังกองการศึกษา (ขั้นที่ 3)', 'ไม่สามารถบันทึกไปขั้นตอนต่อไปได้');
                
                // เปิด modal อัปโหลดไฟล์ให้อัตโนมัติ
                const uploadModalEl = document.getElementById('uploadTravelModal');
                if (uploadModalEl && typeof bootstrap !== 'undefined') {
                    const uploadModal = new bootstrap.Modal(uploadModalEl);
                    uploadModal.show();
                }
                return false;
            }
        });
    }

    // จัดการอัปโหลดไฟล์แบบ AJAX พร้อมแสดงแถบเปอร์เซ็นต์โหลด
    const uploadForm = document.getElementById('travelUploadForm');
    if (uploadForm) {
        uploadForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const fileInput = document.getElementById('travelDocFileInput');
            if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
                AppAlert.warning('กรุณาเลือกไฟล์ PDF ก่อนกดอัปโหลด', 'ยังไม่ได้เลือกไฟล์');
                return;
            }

            const file = fileInput.files[0];
            if (!file.name.toLowerCase().endsWith('.pdf')) {
                AppAlert.error('ระบบรองรับเฉพาะไฟล์นามสกุล .pdf เท่านั้น', 'รูปแบบไฟล์ไม่ถูกต้อง');
                return;
            }

            const progressSection  = document.getElementById('uploadProgressSection');
            const progressBar      = document.getElementById('uploadProgressBar');
            const percentBadge     = document.getElementById('uploadPercentBadge');
            const statusText       = document.getElementById('uploadStatusText');
            const loadedSizeSpan   = document.getElementById('uploadLoadedSize');
            const totalSizeSpan    = document.getElementById('uploadTotalSize');
            const speedSpan        = document.getElementById('uploadSpeedText');
            const remainingSpan    = document.getElementById('uploadRemainingText');
            const submitBtn        = document.getElementById('uploadSubmitBtn');
            const cancelBtn        = document.getElementById('uploadCancelBtn');
            const closeBtn         = document.getElementById('uploadModalCloseBtn');
            const errorAlert       = document.getElementById('uploadErrorAlert');

            // ซ่อน error ถ้ามีอยู่เดิม
            if (errorAlert) {
                errorAlert.classList.add('d-none');
                errorAlert.textContent = '';
            }

            // แสดงส่วน Progress Bar
            if (progressSection) progressSection.classList.remove('d-none');
            if (progressBar) {
                progressBar.style.width = '0%';
                progressBar.setAttribute('aria-valuenow', '0');
                progressBar.className = 'progress-bar progress-bar-striped progress-bar-animated bg-primary';
            }
            if (percentBadge) {
                percentBadge.className = 'badge bg-primary text-white fw-bold fs-6';
                percentBadge.textContent = '0%';
            }
            if (statusText) statusText.innerHTML = '<i class="bx bx-loader-alt bx-spin text-primary me-1"></i> เริ่มต้นส่งข้อมูลไฟล์...';
            if (speedSpan) speedSpan.innerHTML = '<i class="bx bx-tachometer me-1"></i>0 KB/s';
            if (remainingSpan) remainingSpan.innerHTML = '<i class="bx bx-time me-1"></i>กำลังคำนวณ...';

            // Disable ปุ่มกดระหว่างอัปโหลด
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> กำลังอัปโหลด...';
            }
            if (cancelBtn) cancelBtn.disabled = true;
            if (closeBtn) closeBtn.disabled = true;

            const formData = new FormData(uploadForm);
            const xhr = new XMLHttpRequest();
            xhr.open('POST', uploadForm.action, true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

            // ตัวแปรสำหรับคำนวณความเร็วและเวลาแบบ Real-time
            const startTime = Date.now();
            let lastLoaded = 0;
            let lastTime = startTime;

            // Event ติดตามเปอร์เซ็นต์การโหลดตามเวลาจริง
            xhr.upload.addEventListener('progress', function(event) {
                if (event.lengthComputable && event.total > 0) {
                    const currentTime = Date.now();
                    const percent = Math.min(99, Math.round((event.loaded / event.total) * 100));

                    if (progressBar) {
                        progressBar.style.width = percent + '%';
                        progressBar.setAttribute('aria-valuenow', percent);
                    }
                    if (percentBadge) {
                        percentBadge.textContent = percent + '%';
                    }

                    const loadedMB = (event.loaded / (1024 * 1024)).toFixed(2);
                    const totalMB  = (event.total / (1024 * 1024)).toFixed(2);
                    if (loadedSizeSpan) loadedSizeSpan.textContent = loadedMB + ' MB';
                    if (totalSizeSpan) totalSizeSpan.textContent = totalMB + ' MB';

                    // คำนวณความเร็ว (Bytes/sec)
                    const timeElapsedSinceStart = (currentTime - startTime) / 1000;
                    if (timeElapsedSinceStart > 0.2) {
                        const currentSpeed = event.loaded / timeElapsedSinceStart; // bytes/sec
                        
                        let speedStr = '';
                        if (currentSpeed >= 1024 * 1024) {
                            speedStr = (currentSpeed / (1024 * 1024)).toFixed(2) + ' MB/s';
                        } else {
                            speedStr = Math.round(currentSpeed / 1024) + ' KB/s';
                        }
                        if (speedSpan) {
                            speedSpan.innerHTML = '<i class="bx bx-tachometer me-1"></i>' + speedStr;
                        }

                        // คำนวณเวลาที่เหลือ (Remaining Time)
                        const remainingBytes = event.total - event.loaded;
                        const remainingSeconds = Math.round(remainingBytes / currentSpeed);
                        if (remainingSpan) {
                            if (remainingSeconds > 60) {
                                const mins = Math.floor(remainingSeconds / 60);
                                const secs = remainingSeconds % 60;
                                remainingSpan.innerHTML = `<i class="bx bx-time me-1"></i>เหลือ ~${mins} นาที ${secs} วิ`;
                            } else if (remainingSeconds > 0) {
                                remainingSpan.innerHTML = `<i class="bx bx-time me-1"></i>เหลือ ~${remainingSeconds} วินาที`;
                            } else {
                                remainingSpan.innerHTML = '<i class="bx bx-time me-1"></i>กำลังเสร็จสิ้น...';
                            }
                        }
                    }

                    if (percent >= 99 && statusText) {
                        statusText.innerHTML = '<i class="bx bx-sync bx-spin text-info me-1"></i> กำลังส่งต่อและรวมไฟล์แบบ Chunked ไปยัง skj.nsnpao.go.th...';
                        if (remainingSpan) remainingSpan.innerHTML = '<i class="bx bx-cog bx-spin me-1"></i>กำลังรวมไฟล์...';
                    } else if (statusText) {
                        statusText.innerHTML = '<i class="bx bx-upload text-primary me-1"></i> กำลังส่งข้อมูลไฟล์...';
                    }
                }
            });

            function parseJsonResponse(rawText) {
                if (!rawText) return null;
                // ตัด HTML / Debug Toolbar comment ออกหากมีปนมาท้าย response
                let cleaned = rawText.trim();
                const jsonStart = cleaned.indexOf('{');
                const jsonEnd = cleaned.lastIndexOf('}');
                if (jsonStart !== -1 && jsonEnd !== -1 && jsonEnd > jsonStart) {
                    cleaned = cleaned.substring(jsonStart, jsonEnd + 1);
                }
                try {
                    return JSON.parse(cleaned);
                } catch(e) {
                    return null;
                }
            }

            xhr.onload = function() {
                const res = parseJsonResponse(xhr.responseText);

                if (xhr.status >= 200 && xhr.status < 300) {
                    if (res && res.status === 'success') {
                        if (progressBar) {
                            progressBar.style.width = '100%';
                            progressBar.className = 'progress-bar bg-success';
                        }
                        if (percentBadge) {
                            percentBadge.className = 'badge bg-success text-white fw-bold fs-6';
                            percentBadge.textContent = '100% สำเร็จ!';
                        }
                        if (statusText) {
                            statusText.innerHTML = '<i class="bx bx-check-circle text-success me-1"></i> ' + (res.message || 'อัปโหลดไฟล์เรียบร้อยแล้ว!');
                        }
                        setTimeout(function() {
                            window.location.reload();
                        }, 700);
                        return;
                    } else {
                        const errMsg = (res && res.message) ? res.message : 'เซิร์ฟเวอร์ประมวลผลไม่สำเร็จ หรือรูปแบบการตอบกลับไม่ถูกต้อง';
                        handleUploadError(errMsg);
                    }
                } else {
                    let errMsg = 'เกิดข้อผิดพลาดจากเซิร์ฟเวอร์ (รหัส ' + xhr.status + ')';
                    if (xhr.status === 413 || (xhr.responseText && xhr.responseText.includes('413 Request Entity Too Large'))) {
                        errMsg = 'ไฟล์เอกสารมีขนาดใหญ่เกินกว่าที่เซิร์ฟเวอร์ปลายทางอนุญาต (413 Request Entity Too Large) กรุณาย่อขนาดไฟล์ PDF หรือแบ่งไฟล์ไม่ให้เกิน 1 MB';
                    } else if (res && res.message) {
                        errMsg = res.message;
                    } else if (xhr.responseText) {
                        // ดึงข้อความ error ย่อ ๆ ออกมาถ้าเป็น HTML
                        const tempDiv = document.createElement('div');
                        tempDiv.innerHTML = xhr.responseText;
                        const textContent = tempDiv.textContent || tempDiv.innerText || '';
                        const cleanText = textContent.replace(/\s+/g, ' ').trim();
                        if (cleanText.length > 0 && cleanText.length < 200) {
                            errMsg = cleanText;
                        }
                    }
                    handleUploadError(errMsg);
                }
            };

            xhr.onerror = function() {
                handleUploadError('การเชื่อมต่อกับเซิร์ฟเวอร์ขัดข้อง กรุณาลองใหม่อีกครั้ง');
            };

            function handleUploadError(msg) {
                if (errorAlert) {
                    errorAlert.textContent = '⚠️ ' + msg;
                    errorAlert.classList.remove('d-none');
                }
                if (progressBar) {
                    progressBar.className = 'progress-bar bg-danger';
                }
                if (statusText) {
                    statusText.innerHTML = '<span class="text-danger fw-bold"><i class="bx bx-x-circle me-1"></i> การอัปโหลดล้มเหลว</span>';
                }
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="bx bx-upload me-1"></i> ลองใหม่อีกครั้ง';
                }
                if (cancelBtn) cancelBtn.disabled = false;
                if (closeBtn) closeBtn.disabled = false;
            }

            xhr.send(formData);
        });
    }
});

function selectStepAndNote(stepNum, text) {
    const stepSelect = document.getElementById('trackingStepSelect');
    const statusSelect = document.getElementById('trackingStatusSelect');
    const noteInput = document.getElementById('trackingNoteInput');
    const approvalInput = document.getElementById('approvalTypeInput');

    if (approvalInput) approvalInput.value = '';

    if (stepSelect) {
        stepSelect.value = stepNum;
        if (parseInt(stepNum) === 7 && statusSelect) {
            statusSelect.value = 'approved';
        } else if (statusSelect && statusSelect.value === 'approved' && parseInt(stepNum) < 7) {
            statusSelect.value = 'pending';
        }
    }
    if (noteInput) {
        noteInput.value = text;
        noteInput.focus();
    }
}

function handleStepSelectChange(sel) {
    const statusSelect = document.getElementById('trackingStatusSelect');
    const approvalInput = document.getElementById('approvalTypeInput');

    if (approvalInput) approvalInput.value = '';

    if (!statusSelect) return;
    if (parseInt(sel.value) === 7) {
        statusSelect.value = 'approved';
    } else if (statusSelect.value === 'approved') {
        statusSelect.value = 'pending';
    }
}

function setDualApproval(mode) {
    const stepSelect = document.getElementById('trackingStepSelect');
    const statusSelect = document.getElementById('trackingStatusSelect');
    const noteInput = document.getElementById('trackingNoteInput');
    const approvalInput = document.getElementById('approvalTypeInput');

    if (mode === 'clerk') {
        if (approvalInput) approvalInput.value = 'clerk';
        if (statusSelect) statusSelect.value = 'approved';
        if (stepSelect) stepSelect.value = '5';
        if (noteInput) {
            noteInput.value = 'ปลัด อบจ. ลงนามอนุมัติเรียบร้อยแล้ว (ปฏิบัติราชการแทนนายก อบจ.)';
            noteInput.focus();
        }
    } else if (mode === 'president_step') {
        if (approvalInput) approvalInput.value = '';
        if (statusSelect) statusSelect.value = 'pending';
        if (stepSelect) stepSelect.value = '6';
        if (noteInput) {
            noteInput.value = 'ส่งเรื่องเสนอห้องนายก อบจ. เพื่อพิจารณาอนุมัติ';
            noteInput.focus();
        }
    }
}

function setReturnStepAndNote(stepNum, text) {
    const returnSelect = document.getElementById('returnStepSelect');
    const noteInput = document.getElementById('returnNoteInput');

    if (returnSelect) {
        returnSelect.value = stepNum;
    }
    if (noteInput) {
        noteInput.value = text;
        noteInput.focus();
    }
}

function openDefaultTrackingModal() {
    <?php if ($isApproved): ?>
        openReturnTrackingModal(<?= min($totalReturnSteps, $curReturnStep + 1) ?>);
    <?php else: ?>
        openOutboundTrackingModal(<?= $nextStep <= 7 ? $nextStep : 6 ?>);
    <?php endif; ?>
}

function openOutboundTrackingModal(stepNum) {
    const modalEl = document.getElementById('outboundTrackingModal');
    if (!modalEl || typeof bootstrap === 'undefined') return;

    if (stepNum) {
        const stepSelect = document.getElementById('trackingStepSelect');
        if (stepSelect) {
            // เช็คว่า option นี้เปิดใช้งานอยู่หรือไม่ ถ้าถูก disabled จะไม่ force เปลี่ยน
            const targetOption = stepSelect.querySelector(`option[value="${stepNum}"]`);
            if (targetOption && !targetOption.disabled) {
                stepSelect.value = stepNum;
                handleStepSelectChange(stepSelect);
            }
        }
    }

    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    modal.show();
}

function openReturnTrackingModal(returnStepNum) {
    const modalEl = document.getElementById('returnTrackingModal');
    if (!modalEl || typeof bootstrap === 'undefined') return;

    if (returnStepNum) {
        const returnSelect = document.getElementById('returnStepSelect');
        if (returnSelect) {
            const targetOption = returnSelect.querySelector(`option[value="${returnStepNum}"]`);
            if (targetOption && !targetOption.disabled) {
                returnSelect.value = returnStepNum;
            }
        }
    }

    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    modal.show();
}

function confirmDeleteTravel() {
    const docNo = <?= json_encode($travel['doc_no']) ?>;
    const form = document.getElementById('deleteTravelForm');
    if (!form) return;

    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'ยืนยันการลบคำขอไปราชการ?',
            html: `ต้องการลบคำขอเลขที่ <strong>${docNo}</strong> ใช่หรือไม่?<br><span class="text-danger small"><i class="bx bx-error-circle"></i> ข้อมูลคำขอ บันทึกขั้นตอน และไฟล์แนบทั้งหมดจะถูกลบถาวร</span>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ff3e1d',
            cancelButtonColor: '#8592a3',
            confirmButtonText: '<i class="bx bx-trash me-1"></i> ยืนยันลบข้อมูล',
            cancelButtonText: 'ยกเลิก',
            reverseButtons: true,
            focusCancel: true
        }).then((result) => {
            if (result.isConfirmed) {
                // แสดงสถานะกำลังลบ
                Swal.fire({
                    title: 'กำลังลบข้อมูลและไฟล์...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                form.submit();
            }
        });
    } else {
        if (confirm(`คุณต้องการลบคำขอเลขที่ ${docNo} และไฟล์แนบทั้งหมดใช่หรือไม่?`)) {
            form.submit();
        }
    }
}

// -------------------------------------------------------------
// Drag & Drop Handlers สำหรับ uploadTravelModal
// -------------------------------------------------------------
function triggerBrowseTravelModalFile() {
    const input = document.getElementById('travelDocFileInput');
    if (input) input.click();
}

function handleTravelModalFileSelect(file) {
    if (!file) return;

    if (!file.name.toLowerCase().endsWith('.pdf') && file.type !== 'application/pdf') {
        if (typeof AppAlert !== 'undefined') {
            AppAlert.error('ระบบรองรับเฉพาะไฟล์เอกสารรูปแบบ .PDF เท่านั้น', 'รูปแบบไฟล์ไม่ถูกต้อง');
        } else {
            alert('ระบบรองรับเฉพาะไฟล์เอกสารรูปแบบ .PDF เท่านั้น');
        }
        removeTravelModalFile();
        return;
    }

    const dropzone = document.getElementById('travelModalDropzone');
    const preview = document.getElementById('travelModalFilePreview');
    const nameEl = document.getElementById('travelModalPreviewName');
    const sizeEl = document.getElementById('travelModalPreviewSize');

    if (nameEl) nameEl.textContent = file.name;
    if (sizeEl) {
        const sizeKB = (file.size / 1024).toFixed(1);
        const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
        sizeEl.textContent = (file.size >= 1024 * 1024) ? `${sizeMB} MB` : `${sizeKB} KB`;
    }

    if (dropzone) dropzone.style.display = 'none';
    if (preview) preview.style.display = 'block';
}

function removeTravelModalFile(e) {
    if (e) e.stopPropagation();
    const input = document.getElementById('travelDocFileInput');
    const dropzone = document.getElementById('travelModalDropzone');
    const preview = document.getElementById('travelModalFilePreview');

    if (input) input.value = '';
    if (dropzone) dropzone.style.display = 'block';
    if (preview) preview.style.display = 'none';
}

// Event Listeners สำหรับ Drag & Drop ใน uploadTravelModal
document.addEventListener('DOMContentLoaded', function() {
    const dropzone = document.getElementById('travelModalDropzone');
    const fileInput = document.getElementById('travelDocFileInput');

    if (dropzone && fileInput) {
        ['dragenter', 'dragover'].forEach(evtName => {
            dropzone.addEventListener(evtName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.add('dragover');
            });
        });

        ['dragleave', 'drop'].forEach(evtName => {
            dropzone.addEventListener(evtName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.remove('dragover');
            });
        });

        dropzone.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            if (files && files.length > 0) {
                fileInput.files = files;
                handleTravelModalFileSelect(files[0]);
            }
        });

        fileInput.addEventListener('change', function() {
            if (this.files && this.files.length > 0) {
                handleTravelModalFileSelect(this.files[0]);
            }
        });
    }
});

// ========================================================
// ฟังก์ชันจัดการรายงานผลการไปราชการ (Report Section Handlers)
// ========================================================
function toggleReportEditMode() {
    const displaySection = document.getElementById('reportedDisplaySection');
    const editSection = document.getElementById('reportEditSection');
    if (!displaySection || !editSection) return;

    if (editSection.style.display === 'none' || editSection.style.display === '') {
        editSection.style.display = 'block';
        displaySection.style.display = 'none';
    } else {
        editSection.style.display = 'none';
        displaySection.style.display = 'block';
    }
}

// 1. Handlers สำหรับ Create Report
function triggerBrowseReportCreateFile() {
    const input = document.getElementById('reportCreateFileInput');
    if (input) input.click();
}

function handleReportCreateFileSelect(file) {
    if (!file) return;

    if (file.type !== 'application/pdf' && !file.name.toLowerCase().endsWith('.pdf')) {
        Swal.fire({
            icon: 'warning',
            title: 'รูปแบบไฟล์ไม่ถูกต้อง',
            text: 'ระบบรองรับเฉพาะไฟล์เอกสารรายงานหรือเกียรติบัตรประเภท .PDF เท่านั้น',
            confirmButtonColor: '#696cff'
        });
        removeReportCreateFile();
        return;
    }

    const dropzone = document.getElementById('reportCreateDropzone');
    const preview = document.getElementById('reportCreateFilePreview');
    const nameEl = document.getElementById('reportCreatePreviewName');
    const sizeEl = document.getElementById('reportCreatePreviewSize');

    if (nameEl) nameEl.textContent = file.name;
    if (sizeEl) sizeEl.textContent = formatReportBytes(file.size);

    if (dropzone) dropzone.style.display = 'none';
    if (preview) {
        preview.style.display = 'block';
        preview.classList.add('show');
    }
}

function removeReportCreateFile(e) {
    if (e) {
        e.preventDefault();
        e.stopPropagation();
    }
    const input = document.getElementById('reportCreateFileInput');
    const dropzone = document.getElementById('reportCreateDropzone');
    const preview = document.getElementById('reportCreateFilePreview');

    if (input) input.value = '';
    if (preview) {
        preview.style.display = 'none';
        preview.classList.remove('show');
    }
    if (dropzone) dropzone.style.display = 'block';
}

// 2. Handlers สำหรับ Edit Report
function triggerBrowseReportEditFile() {
    const input = document.getElementById('reportEditFileInput');
    if (input) input.click();
}

function handleReportEditFileSelect(file) {
    if (!file) return;

    if (file.type !== 'application/pdf' && !file.name.toLowerCase().endsWith('.pdf')) {
        Swal.fire({
            icon: 'warning',
            title: 'รูปแบบไฟล์ไม่ถูกต้อง',
            text: 'ระบบรองรับเฉพาะไฟล์เอกสารรายงานหรือเกียรติบัตรประเภท .PDF เท่านั้น',
            confirmButtonColor: '#696cff'
        });
        removeReportEditFile();
        return;
    }

    const dropzone = document.getElementById('reportEditDropzone');
    const preview = document.getElementById('reportEditFilePreview');
    const nameEl = document.getElementById('reportEditPreviewName');
    const sizeEl = document.getElementById('reportEditPreviewSize');

    if (nameEl) nameEl.textContent = file.name;
    if (sizeEl) sizeEl.textContent = formatReportBytes(file.size);

    if (dropzone) dropzone.style.display = 'none';
    if (preview) {
        preview.style.display = 'block';
        preview.classList.add('show');
    }
}

function removeReportEditFile(e) {
    if (e) {
        e.preventDefault();
        e.stopPropagation();
    }
    const input = document.getElementById('reportEditFileInput');
    const dropzone = document.getElementById('reportEditDropzone');
    const preview = document.getElementById('reportEditFilePreview');

    if (input) input.value = '';
    if (preview) {
        preview.style.display = 'none';
        preview.classList.remove('show');
    }
    if (dropzone) dropzone.style.display = 'block';
}

function formatReportBytes(bytes, decimals = 2) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const dm = decimals < 0 ? 0 : decimals;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
}

// Event Listeners สำหรับ Drag & Drop ใน Report Create & Edit
document.addEventListener('DOMContentLoaded', function() {
    // Create Report Dropzone
    const createDropzone = document.getElementById('reportCreateDropzone');
    const createFileInput = document.getElementById('reportCreateFileInput');

    if (createDropzone && createFileInput) {
        ['dragenter', 'dragover'].forEach(evtName => {
            createDropzone.addEventListener(evtName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                createDropzone.classList.add('dragover');
            });
        });

        ['dragleave', 'drop'].forEach(evtName => {
            createDropzone.addEventListener(evtName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                createDropzone.classList.remove('dragover');
            });
        });

        createDropzone.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            if (files && files.length > 0) {
                createFileInput.files = files;
                handleReportCreateFileSelect(files[0]);
            }
        });

        createFileInput.addEventListener('change', function() {
            if (this.files && this.files.length > 0) {
                handleReportCreateFileSelect(this.files[0]);
            }
        });
    }

    // Edit Report Dropzone
    const editDropzone = document.getElementById('reportEditDropzone');
    const editFileInput = document.getElementById('reportEditFileInput');

    if (editDropzone && editFileInput) {
        ['dragenter', 'dragover'].forEach(evtName => {
            editDropzone.addEventListener(evtName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                editDropzone.classList.add('dragover');
            });
        });

        ['dragleave', 'drop'].forEach(evtName => {
            editDropzone.addEventListener(evtName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                editDropzone.classList.remove('dragover');
            });
        });

        editDropzone.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            if (files && files.length > 0) {
                editFileInput.files = files;
                handleReportEditFileSelect(files[0]);
            }
        });

        editFileInput.addEventListener('change', function() {
            if (this.files && this.files.length > 0) {
                handleReportEditFileSelect(this.files[0]);
            }
        });
    }
});
</script>
<?= $this->endSection() ?>
