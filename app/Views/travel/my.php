<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>ติดตามสถานะคำขอไปราชการของฉัน<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
/* Card Minimal & Modern Design */
.travel-card {
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    border-radius: 16px;
    border: 1px solid #e9ecef;
    background: #ffffff;
    overflow: hidden;
}
.travel-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 32px rgba(30, 41, 59, 0.08) !important;
    border-color: #cbd5e1;
}

/* Subtle Status Stripe */
.border-status-pending { border-left: 4px solid #6366f1 !important; }
.border-status-approved { border-left: 4px solid #10b981 !important; }
.border-status-returned { border-left: 4px solid #ef4444 !important; }
.border-status-reported { border-left: 4px solid #06b6d4 !important; }

/* Filter Pill Buttons */
.filter-btn {
    border-radius: 9999px;
    font-size: 0.8rem;
    font-weight: 500;
    padding: 0.4rem 0.95rem;
    border: 1px solid #e2e8f0;
    color: #475569;
    background: #ffffff;
    transition: all 0.2s ease;
}
.filter-btn:hover {
    background: #f8fafc;
    color: #1e293b;
    border-color: #cbd5e1;
}
.filter-btn.active {
    background: #1e293b !important;
    color: #ffffff !important;
    border-color: #1e293b !important;
    box-shadow: 0 2px 8px rgba(30, 41, 59, 0.2);
}
.filter-btn.active .badge {
    background: rgba(255, 255, 255, 0.25) !important;
    color: #ffffff !important;
}

/* Location Status Banner */
.status-banner {
    border-radius: 12px;
    padding: 0.85rem 1.15rem;
    position: relative;
}
.status-banner-pending {
    background: #f8faff;
    border: 1px solid #e0e7ff;
}
.status-banner-approved {
    background: #f0fdf4;
    border: 1px solid #dcfce7;
}
.status-banner-returned {
    background: #fef2f2;
    border: 1px solid #fee2e2;
}

/* Pulse Rings */
.pulse-dot {
    display: inline-block;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    margin-right: 6px;
}
.pulse-dot-primary {
    background: #6366f1;
    box-shadow: 0 0 0 0 rgba(99, 102, 241, 0.7);
    animation: pulse-ring 1.8s infinite;
}
.pulse-dot-success {
    background: #10b981;
    box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
    animation: pulse-ring-success 1.8s infinite;
}
@keyframes pulse-ring {
    0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(99, 102, 241, 0.7); }
    70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(99, 102, 241, 0); }
    100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(99, 102, 241, 0); }
}
@keyframes pulse-ring-success {
    0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
    70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
    100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
}

/* Stepper Track Card */
.timeline-track-card {
    background: #f8fafc;
    border: 1px solid #f1f5f9;
    border-radius: 12px;
    padding: 1rem 1.25rem;
}

/* Minimal Stepper */
.flow-stepper {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    position: relative;
    margin-top: 0.5rem;
}
.flow-stepper::before {
    content: '';
    position: absolute;
    top: 15px;
    left: 4%;
    right: 4%;
    height: 2px;
    background: #e2e8f0;
    z-index: 1;
}
.flow-step-item {
    position: relative;
    z-index: 2;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    flex: 1 1 0;
    min-width: 0;
}
.flow-step-circle {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: #ffffff;
    border: 2px solid #cbd5e1;
    color: #64748b;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 700;
    margin-bottom: 0.35rem;
    transition: all 0.2s ease;
}
.flow-step-circle.done {
    background: #10b981;
    border-color: #10b981;
    color: #ffffff;
}
.flow-step-circle.current {
    background: #6366f1;
    border-color: #6366f1;
    color: #ffffff;
    box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.2);
}
.flow-step-circle.current-ret {
    background: #f59e0b;
    border-color: #f59e0b;
    color: #ffffff;
    box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.2);
}
.flow-step-circle.returned {
    background: #ef4444;
    border-color: #ef4444;
    color: #ffffff;
}
.flow-step-circle.skipped {
    background: #f1f5f9;
    border-color: #e2e8f0;
    color: #94a3b8;
}

.flow-step-label {
    font-size: 0.72rem;
    font-weight: 600;
    color: #334155;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 95%;
}
.flow-step-meta {
    font-size: 0.65rem;
    color: #64748b;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 95%;
    margin-top: 2px;
}

/* Direction Arrow Connectors */
.flow-arrow-right {
    position: absolute;
    top: 5px;
    right: -8px;
    font-size: 1.1rem;
    color: #94a3b8;
    z-index: 3;
}
.flow-arrow-left {
    position: absolute;
    top: 5px;
    left: -8px;
    font-size: 1.1rem;
    color: #94a3b8;
    z-index: 3;
}
</style>

<div class="row">
    <div class="col-12">
        <!-- Minimal Modern Header -->
        <div class="card mb-4 border-0 shadow-xs" style="border-radius: 16px; background: #ffffff;">
            <div class="card-body p-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-primary bg-opacity-10 text-primary p-2 rounded-3">
                                <i class="bx bx-trip fs-4"></i>
                            </span>
                            <h4 class="card-title mb-0 fw-bold text-dark">ติดตามสถานะคำขอไปราชการ</h4>
                        </div>
                        <p class="text-muted small mb-0 ms-1">
                            แสดงสถานะแบบ 2 ทิศทาง ทั้ง <strong>ขาไป ➔</strong> และ <strong>ขากลับ ⬅️</strong> พร้อมบันทึกผู้รับเอกสารจริงในแต่ละขั้นตอน
                        </p>
                    </div>
                    <div>
                        <a href="<?= base_url('admin/travel/create') ?>" class="btn btn-primary px-3 py-2 rounded-pill shadow-xs d-flex align-items-center gap-1">
                            <i class="bx bx-plus fs-5"></i>
                            <span class="fw-semibold">ยื่นขอไปราชการใหม่</span>
                        </a>
                    </div>
                </div>

                <!-- Status Filter Pills -->
                <div class="d-flex flex-wrap align-items-center gap-2 mt-3 pt-3 border-top">
                    <?php
                        $totalCount    = count($requests);
                        $pendingCount  = count(array_filter($requests, fn($r) => $r['status'] === 'pending'));
                        $approvedCount = count(array_filter($requests, fn($r) => $r['status'] === 'approved'));
                        $returnedCount = count(array_filter($requests, fn($r) => $r['status'] === 'returned'));
                        $reportedCount = count(array_filter($requests, fn($r) => $r['status'] === 'reported'));
                    ?>
                    <button type="button" class="filter-btn active" data-filter="all">
                        ทั้งหมด <span class="badge bg-light text-dark ms-1 rounded-pill"><?= $totalCount ?></span>
                    </button>
                    <button type="button" class="filter-btn" data-filter="pending">
                        <span class="pulse-dot pulse-dot-primary"></span> ขาไป: รออนุมัติ <span class="badge bg-light text-dark ms-1 rounded-pill"><?= $pendingCount ?></span>
                    </button>
                    <button type="button" class="filter-btn" data-filter="approved">
                        <span class="pulse-dot pulse-dot-success"></span> ขากลับ: อนุมัติแล้ว <span class="badge bg-light text-dark ms-1 rounded-pill"><?= $approvedCount ?></span>
                    </button>
                    <button type="button" class="filter-btn" data-filter="returned">
                        <i class="bx bx-revision text-danger me-1"></i> ส่งคืนแก้ไข <span class="badge bg-light text-dark ms-1 rounded-pill"><?= $returnedCount ?></span>
                    </button>
                    <button type="button" class="filter-btn" data-filter="reported">
                        <i class="bx bx-check-circle text-info me-1"></i> รายงานผลแล้ว <span class="badge bg-light text-dark ms-1 rounded-pill"><?= $reportedCount ?></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Travel Cards Grid -->
        <?php if (empty($requests)): ?>
            <div class="card text-center py-5 border-0 shadow-xs" style="border-radius: 16px;">
                <div class="card-body">
                    <div class="avatar avatar-xl bg-light mx-auto mb-3" style="width: 64px; height: 64px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                        <i class="bx bx-briefcase-alt fs-2 text-muted"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">ยังไม่มีคำขอไปราชการ</h5>
                    <p class="text-muted small mb-3">คุณยังไม่ได้ยื่นคำขออนุมัติเดินทางไปราชการในระบบ</p>
                    <a href="<?= base_url('admin/travel/create') ?>" class="btn btn-sm btn-primary rounded-pill px-3">
                        <i class="bx bx-plus me-1"></i> ยื่นขออนุมัติฉบับแรก
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="d-flex flex-column gap-3" id="travelCardsContainer">
                <?php foreach ($requests as $r): ?>
                    <?php
                        $statusClass = 'border-status-' . esc($r['status']);
                        $isApproved = in_array($r['status'], ['approved', 'reported']);
                        $curStep = (int)($r['current_step'] ?? 1);
                        $curRetStep = (int)($r['return_step'] ?? 0);
                        $isApprovedByClerk = (!empty($r['approver_title']) && str_contains($r['approver_title'], 'ปลัด'));

                        // คำนวณจุดที่ขาไปสิ้นสุด
                        $approvedAtStep = $curStep;
                        if ($isApprovedByClerk) {
                            $approvedAtStep = 5;
                        } elseif ($isApproved && $curStep < 5) {
                            $approvedAtStep = max(1, $curStep);
                        }

                        // ขาไป 6 ขั้นตอน (ซ้ายไปขวา)
                        $trackingStages = [
                            1 => ['name' => '1. ผู้ขอยื่นเรื่อง', 'icon' => 'bx-paper-plane'],
                            2 => ['name' => '2. โรงเรียน', 'icon' => 'bx-building'],
                            3 => ['name' => '3. กองการศึกษา', 'icon' => 'bx-book-reader'],
                            4 => ['name' => '4. กองการเจ้าหน้าที่', 'icon' => 'bx-id-card'],
                            5 => ['name' => '5. สำนักปลัด', 'icon' => 'bx-briefcase-alt-2'],
                            6 => ['name' => '6. นายก อบจ.', 'icon' => 'bx-crown'],
                        ];

                        // ขากลับ (ขวามาซ้าย: ย้อนจากจุดอนุมัติลงมาถึงขั้นที่ 1)
                        $allNodeStages = [
                            1 => ['name' => 'ผู้ขอรับตัวจริง', 'icon' => 'bx-user-check'],
                            2 => ['name' => 'โรงเรียน', 'icon' => 'bx-building'],
                            3 => ['name' => 'กองการศึกษา', 'icon' => 'bx-book-reader'],
                            4 => ['name' => 'กองการเจ้าหน้าที่', 'icon' => 'bx-id-card'],
                            5 => ['name' => 'สำนักปลัด', 'icon' => 'bx-briefcase-alt-2'],
                            6 => ['name' => 'นายก อบจ.', 'icon' => 'bx-crown'],
                        ];

                        $returnStages = [];
                        $retIndex = 1;
                        for ($s = $approvedAtStep; $s >= 1; $s--) {
                            $returnStages[$retIndex] = [
                                'orig_step' => $s,
                                'name'      => "{$retIndex}. " . $allNodeStages[$s]['name'],
                                'icon'      => $allNodeStages[$s]['icon'],
                            ];
                            $retIndex++;
                        }
                        $totalReturnSteps = count($returnStages);

                        $stepReceivers = json_decode($r['step_receivers'] ?? '{}', true) ?: [];
                    ?>
                    <div class="travel-card-item" data-status="<?= esc($r['status']) ?>">
                        <div class="card travel-card <?= $statusClass ?> shadow-xs">
                            <!-- Card Header -->
                            <div class="card-body p-3 p-md-4">
                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <span class="badge bg-light text-primary border border-primary border-opacity-25 px-2.5 py-1 font-monospace fw-semibold" style="font-size: 0.8rem;">
                                            <?= esc($r['doc_no']) ?>
                                        </span>
                                        <h5 class="fw-bold text-dark mb-0 fs-6">
                                            <a href="<?= base_url('admin/travel/' . $r['id']) ?>" class="text-dark text-decoration-none hover-primary">
                                                <?= esc($r['title']) ?>
                                            </a>
                                        </h5>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <?php if ($isApproved && $curRetStep >= $totalReturnSteps): ?>
                                            <span class="badge bg-success bg-opacity-10 text-success fw-semibold px-2.5 py-1">
                                                <i class="bx bx-check-double me-1"></i> ได้รับตัวจริงครบถ้วน
                                            </span>
                                        <?php elseif ($isApproved): ?>
                                            <span class="badge bg-warning bg-opacity-15 text-dark fw-semibold px-2.5 py-1">
                                                <i class="bx bx-left-arrow-alt me-1 text-warning"></i> ส่งคืนตัวจริง (ขั้น <?= $curRetStep ?>/<?= $totalReturnSteps ?>)
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-primary bg-opacity-10 text-primary fw-semibold px-2.5 py-1">
                                                <i class="bx bx-right-arrow-alt me-1"></i> เสนอขออนุมัติ (ขั้น <?= $curStep ?>/6)
                                            </span>
                                        <?php endif; ?>
                                        <?= travel_status_badge($r['status']) ?>
                                    </div>
                                </div>

                                <!-- Key Meta Tags Row -->
                                <div class="d-flex flex-wrap gap-2 text-muted small mb-3">
                                    <span class="d-inline-flex align-items-center bg-light px-2 py-1 rounded">
                                        <i class="bx bx-map-pin text-danger me-1"></i> <?= esc($r['destination']) ?> (<?= esc($r['province']) ?>)
                                    </span>
                                    <span class="d-inline-flex align-items-center bg-light px-2 py-1 rounded">
                                        <i class="bx bx-calendar text-primary me-1"></i> <?= thai_date($r['start_date'], 'short') ?> - <?= thai_date($r['end_date'], 'short') ?> (<?= esc($r['total_days']) ?> วัน)
                                    </span>
                                    <span class="d-inline-flex align-items-center bg-light px-2 py-1 rounded">
                                        <i class="bx bx-car text-secondary me-1"></i> <?= esc($r['transport_type']) ?>
                                    </span>
                                    <span class="d-inline-flex align-items-center bg-light px-2 py-1 rounded">
                                        <i class="bx bx-wallet text-success me-1"></i> <?= esc($r['budget_type']) ?> <?= number_format($r['budget_amount'] ?? 0) ?> บาท
                                    </span>
                                    <?php if (!empty($r['file_path'])): ?>
                                        <a href="<?= base_url($r['file_path']) ?>" target="_blank" class="d-inline-flex align-items-center text-danger text-decoration-none bg-danger bg-opacity-10 px-2 py-1 rounded">
                                            <i class="bx bxs-file-pdf me-1"></i> หนังสือแนบ
                                        </a>
                                    <?php endif; ?>
                                </div>

                                <!-- Smart Status Banner -->
                                <?php if ($isApproved): ?>
                                    <div class="status-banner status-banner-approved mb-3">
                                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="pulse-dot pulse-dot-success"></span>
                                                <span class="fw-bold text-success small">
                                                    📍 ขากลับ (ส่งคืนตัวจริง ⬅️):
                                                    <?php if ($curRetStep >= $totalReturnSteps): ?>
                                                        ผู้ขอรับหนังสืออนุมัติตัวจริงเรียบร้อยแล้ว (เสร็จสิ้นสมบูรณ์)
                                                    <?php elseif ($curRetStep > 0): ?>
                                                        กำลังส่งคืน ณ <?= esc($returnStages[$curRetStep]['name'] ?? '') ?>
                                                    <?php else: ?>
                                                        อนุมัติแล้วโดย <?= esc($r['approver_title'] ?: 'นายก อบจ.') ?> (รอส่งคืนตัวจริง)
                                                    <?php endif; ?>
                                                </span>
                                            </div>
                                            <?php
                                                $activeRetReceiver = !empty($stepReceivers['return_' . $curRetStep]['name']) 
                                                    ? $stepReceivers['return_' . $curRetStep]['name'] 
                                                    : ($r['current_receiver'] ?? null);
                                            ?>
                                            <?php if (!empty($activeRetReceiver)): ?>
                                                <span class="badge bg-white text-dark border small fw-normal">
                                                    <i class="bx bx-user-check text-success me-1"></i> ผู้ลงรับ: <strong><?= esc($activeRetReceiver) ?></strong>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php elseif ($r['status'] === 'returned'): ?>
                                    <div class="status-banner status-banner-returned mb-3">
                                        <div class="d-flex align-items-center justify-content-between gap-2">
                                            <div class="text-danger small">
                                                <i class="bx bx-error-circle me-1"></i> <strong>เอกสารถูกส่งคืนแก้ไข:</strong> กรุณาติดต่อเจ้าหน้าที่ธุรการโรงเรียนเพื่อดำเนินการแก้ไข
                                            </div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="status-banner status-banner-pending mb-3">
                                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="pulse-dot pulse-dot-primary"></span>
                                                <span class="fw-bold text-primary small">
                                                    📍 ขณะนี้เอกสารอยู่ที่: ขั้นที่ <?= $curStep ?> - <?= esc($trackingStages[$curStep]['name'] ?? 'กำลังดำเนินการ') ?>
                                                </span>
                                            </div>
                                            <?php if (!empty($r['current_receiver'])): ?>
                                                <span class="badge bg-white text-dark border small fw-normal">
                                                    <i class="bx bx-user-pin text-primary me-1"></i> ผู้ลงรับ: <strong><?= esc($r['current_receiver']) ?></strong>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Grid Timelines: กว้างพอดีและสมดุลเท่ากัน 100% -->
                                <div class="row g-3">
                                    <!-- 1. ขาไป (Outbound: ซ้ายไปขวา ➔) -->
                                    <div class="col-12 col-lg-6">
                                        <div class="timeline-track-card h-100">
                                            <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                                                <span class="fw-bold text-dark small d-flex align-items-center">
                                                    <i class="bx bx-right-arrow-alt text-primary fs-5 me-1"></i> 1. ขาไป: เสนอขออนุมัติ
                                                </span>
                                                <span class="badge <?= $isApproved ? 'bg-success' : 'bg-primary' ?> bg-opacity-10 <?= $isApproved ? 'text-success' : 'text-primary' ?> px-2 py-0.5" style="font-size: 0.65rem;">
                                                    <?= $isApproved ? 'อนุมัติแล้ว ✓' : 'กำลังเสนอเรื่อง' ?>
                                                </span>
                                            </div>

                                            <div class="flow-stepper">
                                                <?php for ($s = 1; $s <= 6; $s++): ?>
                                                    <?php
                                                        $hasRec = !empty($stepReceivers[$s]['name']);
                                                        $isSkipped = ($isApprovedByClerk && $s === 6);
                                                        $isCur = ($s === $curStep && !$isApproved && $r['status'] !== 'returned');
                                                        $isRet = ($r['status'] === 'returned' && $s === $curStep);

                                                        if ($isApproved) {
                                                            $isDone = ($s <= 5 || !$isApprovedByClerk) && ($hasRec || $s <= $curStep);
                                                        } else {
                                                            $isDone = ($s < $curStep && $r['status'] !== 'returned');
                                                        }

                                                        $circleClass = $isDone ? 'done' : ($isRet ? 'returned' : ($isSkipped ? 'skipped' : ($isCur ? 'current' : '')));
                                                    ?>
                                                    <div class="flow-step-item">
                                                        <?php if ($s < 6): ?>
                                                            <span class="flow-arrow-right d-none d-md-block">
                                                                <i class="bx bx-chevron-right"></i>
                                                            </span>
                                                        <?php endif; ?>

                                                        <div class="flow-step-circle <?= $circleClass ?>">
                                                            <?php if ($isDone): ?>
                                                                <i class="bx bx-check fs-6"></i>
                                                            <?php elseif ($isRet): ?>
                                                                <i class="bx bx-x fs-6"></i>
                                                            <?php elseif ($isSkipped): ?>
                                                                <i class="bx bx-shield text-muted" style="font-size: 0.65rem;"></i>
                                                            <?php elseif ($isCur): ?>
                                                                <i class="bx <?= $trackingStages[$s]['icon'] ?> fs-6"></i>
                                                            <?php else: ?>
                                                                <?= $s ?>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="flow-step-label <?= $isCur ? 'text-primary fw-bold' : '' ?>" title="<?= esc($trackingStages[$s]['name']) ?>">
                                                            <?= esc($trackingStages[$s]['name']) ?>
                                                        </div>
                                                        <?php if (!empty($stepReceivers[$s]['name'])): ?>
                                                            <div class="flow-step-meta text-truncate" title="ผู้รับ: <?= esc($stepReceivers[$s]['name']) ?>">
                                                                <i class="bx bx-user-check text-success"></i> <?= esc($stepReceivers[$s]['name']) ?>
                                                            </div>
                                                        <?php elseif ($isCur): ?>
                                                            <div class="badge bg-primary px-1.5 py-0 mt-0.5" style="font-size: 0.58rem;">
                                                                อยู่ที่นี่
                                                            </div>
                                                        <?php elseif ($isSkipped): ?>
                                                            <div class="flow-step-meta text-muted">ปลัดอนุมัติ</div>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endfor; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 2. ขากลับ (Return: ขวามาซ้าย ⬅️) -->
                                    <div class="col-12 col-lg-6">
                                        <div class="timeline-track-card h-100 <?= !$isApproved ? 'opacity-60' : '' ?>">
                                            <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                                                <span class="fw-bold text-dark small d-flex align-items-center">
                                                    <i class="bx bx-left-arrow-alt text-success fs-5 me-1"></i> 2. ขากลับ: ส่งคืนตัวจริง
                                                </span>
                                                <span class="badge <?= $isApproved ? 'bg-success' : 'bg-secondary' ?> bg-opacity-10 <?= $isApproved ? 'text-success' : 'text-secondary' ?> px-2 py-0.5" style="font-size: 0.65rem;">
                                                    ขวามาซ้าย (<?= $totalReturnSteps ?> ขั้น)
                                                </span>
                                            </div>

                                            <div class="flow-stepper flex-row-reverse">
                                                <?php foreach ($returnStages as $rNum => $rstg): ?>
                                                    <?php
                                                        $isRetDone = ($curRetStep >= $rNum && $isApproved);
                                                        $isRetCur  = ($curRetStep == $rNum && $curRetStep < $totalReturnSteps && $isApproved);
                                                        $retReceiver = $stepReceivers['return_' . $rNum]['name'] ?? null;
                                                        $circleClass = $isRetDone ? 'done' : ($isRetCur ? 'current-ret' : '');
                                                    ?>
                                                    <div class="flow-step-item">
                                                        <?php if ($rNum < $totalReturnSteps): ?>
                                                            <span class="flow-arrow-left d-none d-md-block">
                                                                <i class="bx bx-chevron-left"></i>
                                                            </span>
                                                        <?php endif; ?>

                                                        <div class="flow-step-circle <?= $circleClass ?>">
                                                            <?php if ($isRetDone): ?>
                                                                <i class="bx bx-check fs-6"></i>
                                                            <?php elseif ($isRetCur): ?>
                                                                <i class="bx <?= $rstg['icon'] ?> fs-6"></i>
                                                            <?php else: ?>
                                                                <?= $rNum ?>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="flow-step-label <?= $isRetCur ? 'text-warning fw-bold' : '' ?>" title="<?= esc($rstg['name']) ?>">
                                                            <?= esc($rstg['name']) ?>
                                                        </div>
                                                        <?php if (!empty($retReceiver)): ?>
                                                            <div class="flow-step-meta text-truncate" title="ผู้รับ: <?= esc($retReceiver) ?>">
                                                                <i class="bx bx-user-check text-success"></i> <?= esc($retReceiver) ?>
                                                            </div>
                                                        <?php elseif ($isRetCur): ?>
                                                            <div class="badge bg-warning text-dark px-1.5 py-0 mt-0.5" style="font-size: 0.58rem;">
                                                                อยู่ที่นี่
                                                            </div>
                                                        <?php elseif (!$isApproved): ?>
                                                            <div class="flow-step-meta text-muted">รออนุมัติ</div>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Minimal Card Footer -->
                            <div class="card-footer px-3 px-md-4 py-2.5 bg-white border-top d-flex flex-wrap justify-content-between align-items-center gap-2">
                                <small class="text-muted">
                                    <i class="bx bx-time-five me-1"></i> ยื่นคำขอเมื่อ <?= thai_date($r['created_at'], 'medium') ?>
                                </small>
                                <div class="d-flex gap-2">
                                    <a href="<?= base_url('admin/travel/' . $r['id']) ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1">
                                        <i class="bx bx-search-alt me-1"></i> ติดตาม & รายละเอียด
                                    </a>
                                    <?php if ($r['status'] === 'approved'): ?>
                                        <a href="<?= base_url('admin/travel-reports/create?travel_id=' . $r['id']) ?>" class="btn btn-sm btn-success rounded-pill px-3 py-1 fw-bold shadow-xs">
                                            <i class="bx bx-send me-1"></i> ส่งรายงานผล
                                        </a>
                                    <?php elseif ($r['status'] === 'reported'): ?>
                                        <a href="<?= base_url('admin/travel-reports/' . $r['id']) ?>" class="btn btn-sm btn-outline-success rounded-pill px-3 py-1 fw-bold shadow-xs">
                                            <i class="bx bx-check-circle me-1"></i> ดูรายงานผล
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Filter Pills interactive script
document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const cards = document.querySelectorAll('.travel-card-item');

    filterButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            filterButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const filterValue = this.getAttribute('data-filter');

            cards.forEach(card => {
                const cardStatus = card.getAttribute('data-status');
                if (filterValue === 'all' || cardStatus === filterValue) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
});
</script>
<?= $this->endSection() ?>


