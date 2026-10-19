<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>ตั้งค่าลายเซ็นดิจิทัลส่วนตัว<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <h5 class="card-header fw-bold text-primary">
                <i class="bx bx-pencil me-2"></i> จัดการลายเซ็นดิจิทัลส่วนตัว (e-Signature Settings)
            </h5>
            <div class="card-body">
                <div class="row">
                    <!-- Current User Info -->
                    <div class="col-md-6 mb-4">
                        <div class="card bg-light border-0">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3">ข้อมูลผู้ใช้งาน</h6>
                                <p class="mb-1"><strong>ชื่อ-นามสกุล:</strong> <?= esc($user['prefix'] . $user['first_name'] . ' ' . $user['last_name']) ?></p>
                                <p class="mb-1"><strong>ตำแหน่ง:</strong> <?= esc($user['position_name']) ?></p>
                                <p class="mb-1"><strong>ฝ่ายงาน/กลุ่มสาระ:</strong> <?= esc($user['department_name']) ?></p>
                                <p class="mb-1"><strong>สิทธิ์การสั่งการขั้นสุดท้าย:</strong> <?= !empty($user['can_sign_final']) ? '<span class="badge bg-success">มีสิทธิ์อนุมัติ/สั่งการ</span>' : '<span class="badge bg-secondary">เสนอความเห็นตามสายงาน</span>' ?></p>
                            </div>
                        </div>

                        <!-- Current Saved Signature Preview -->
                        <div class="card border mt-3">
                            <div class="card-body text-center">
                                <h6 class="fw-bold mb-2">ลายเซ็นเริ่มต้นปัจจุบันของคุณ</h6>
                                <?php if (!empty($user['signature_img'])): ?>
                                    <div class="p-3 bg-white border rounded d-inline-block">
                                        <img src="<?= base_url($user['signature_img']) ?>" class="signature-preview" alt="Current Signature" />
                                    </div>
                                    <p class="text-success small mt-2 mb-2"><i class="bx bx-check-circle me-1"></i> มีลายเซ็นพร้อมใช้งานสำหรับการเกษียน 1-Click</p>
                                    <div class="pt-2 border-top">
                                        <form action="<?= base_url('admin/profile/signature/delete') ?>" method="POST" id="deleteSigForm" class="d-inline">
                                            <?= csrf_field() ?>
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteProfileSignature(this)">
                                                <i class="bx bx-trash me-1"></i> ลบลายเซ็นปัจจุบัน
                                            </button>
                                        </form>
                                    </div>
                                <?php else: ?>
                                    <div class="p-4 bg-light text-muted">
                                        <i class="bx bx-error-circle fs-3 d-block mb-1"></i>
                                        ยังไม่ได้ตั้งค่าลายเซ็นเริ่มต้น
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Draw & Save New Signature -->
                    <div class="col-md-6 mb-4">
                        <div class="card border">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-bold">วาดลายเซ็นดิจิทัลใหม่</h6>
                                <button type="button" class="btn btn-xs btn-outline-danger" id="clearPadBtn">
                                    <i class="bx bx-eraser me-1"></i> ล้าง
                                </button>
                            </div>
                            <div class="card-body">
                                <form action="<?= base_url('admin/profile/signature') ?>" method="POST" id="profileSigForm">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="signature_data" id="sigDataInput" value="" />

                                    <!-- แถบเลือกสีน้ำหมึกปากกา -->
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="small text-muted fw-bold"><i class="bx bx-paint me-1"></i>สีหมึกปากกา:</span>
                                        <button type="button" class="btn btn-sm sig-color-btn active" id="btnColorBlue" onclick="changePenColor('#0038a8', this)" style="background: #0038a8; color: #fff; border-radius: 20px; font-size: 11px; padding: 2px 10px; border: 2px solid #0038a8;">
                                            <i class="bx bx-check me-1"></i> น้ำเงินจริง (มาตรฐาน)
                                        </button>
                                        <button type="button" class="btn btn-sm sig-color-btn" onclick="changePenColor('#0052cc', this)" style="background: #0052cc; color: #fff; border-radius: 20px; font-size: 11px; padding: 2px 10px; border: 2px solid transparent;">
                                            น้ำเงินสด
                                        </button>
                                        <button type="button" class="btn btn-sm sig-color-btn" onclick="changePenColor('#1e293b', this)" style="background: #1e293b; color: #fff; border-radius: 20px; font-size: 11px; padding: 2px 10px; border: 2px solid transparent;">
                                            หมึกดำ
                                        </button>
                                    </div>

                                    <div class="signature-pad-container p-2 mb-3 text-center" style="background: #ffffff; border: 1.5px dashed #93c5fd; border-radius: 8px;">
                                        <canvas id="profileCanvas" width="400" height="180" style="width: 100%; height: 180px; cursor: crosshair; touch-action: none; display: block;"></canvas>
                                    </div>

                                    <p class="text-muted small mb-3">
                                        <i class="bx bx-info-circle me-1 text-primary"></i> วาดลายเซ็นลงในกรอบด้านบนด้วยเมาส์หรือนิ้ว ลายเซ็นนี้จะใช้หมึกสีน้ำเงินเหมือนปากกาจริง และถูกบันทึกเพื่อใช้ประทับลงในแฟ้มเกษียนหนังสืออัตโนมัติ
                                    </p>

                                    <button type="button" class="btn btn-primary w-100 fw-bold shadow-sm" onclick="saveProfileSignature(this)">
                                        <i class="bx bx-save me-1"></i> บันทึกลายเซ็นเริ่มต้น
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
let pad = null;
let currentPenColor = '#0038a8'; // สีน้ำเงินเสมือนปากกาจริงเริ่มต้น

document.addEventListener('DOMContentLoaded', function () {
    const canvas = document.getElementById('profileCanvas');
    if (canvas) {
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext("2d").scale(ratio, ratio);

        pad = new SignaturePad(canvas, {
            penColor: currentPenColor,
            backgroundColor: 'rgba(255, 255, 255, 0)',
            minWidth: 1.5,
            maxWidth: 3.2,
            velocityFilterWeight: 0.7
        });

        document.getElementById('clearPadBtn').addEventListener('click', function () {
            pad.clear();
        });
    }
});

function changePenColor(color, btn) {
    currentPenColor = color;
    if (pad) {
        pad.penColor = color;
    }
    document.querySelectorAll('.sig-color-btn').forEach(b => {
        b.style.borderColor = 'transparent';
        b.innerHTML = b.textContent.trim().replace(/^✓\s*|^\s*/, '');
    });
    if (btn) {
        btn.style.borderColor = '#ffffff';
        btn.style.boxShadow = `0 0 0 2px ${color}`;
        btn.innerHTML = `<i class="bx bx-check me-1"></i> ` + btn.textContent.trim();
    }
}

function saveProfileSignature(btn) {
    if (!pad || pad.isEmpty()) {
        AppAlert.warning('กรุณาวาดลายเซ็นก่อนทำการบันทึก', 'ยังไม่มีลายเซ็น');
        return;
    }
    document.getElementById('sigDataInput').value = pad.toDataURL('image/png');
    if (btn && window.SubmitLoading) {
        window.SubmitLoading.set(btn, 'กำลังบันทึกลายเซ็น...');
    }
    document.getElementById('profileSigForm').submit();
}

function deleteProfileSignature(btn) {
    AppAlert.confirm('คุณต้องการลบลายเซ็นเริ่มต้นนี้ออกจากระบบใช่หรือไม่? เมื่อลบแล้วจะไม่สามารถกู้คืนได้', {
        title: 'ยืนยันการลบลายเซ็น?',
        icon: 'warning',
        confirmText: '<i class="bx bx-trash me-1"></i> ใช่, ลบเลย',
        cancelText: '<i class="bx bx-x me-1"></i> ยกเลิก'
    }).then(function(confirmed) {
        if (confirmed) {
            if (btn && window.SubmitLoading) {
                window.SubmitLoading.set(btn, 'กำลังลบ...');
            }
            document.getElementById('deleteSigForm').submit();
        }
    });
}
</script>
<?= $this->endSection() ?>
