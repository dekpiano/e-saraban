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
                                    <p class="text-success small mt-2 mb-0"><i class="bx bx-check-circle me-1"></i> มีลายเซ็นพร้อมใช้งานสำหรับการเกษียน 1-Click</p>
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

                                    <div class="signature-pad-container p-2 mb-3 text-center">
                                        <canvas id="profileCanvas" width="400" height="180" style="width: 100%; height: 180px; cursor: crosshair;"></canvas>
                                    </div>

                                    <p class="text-muted small mb-3">
                                        <i class="bx bx-info-circle me-1"></i> วาดลายเซ็นลงในกรอบด้านบนด้วยเมาส์หรือนิ้ว ลายเซ็นนี้จะถูกบันทึกเพื่อใช้ประทับลงในแฟ้มเกษียนหนังสืออัตโนมัติ
                                    </p>

                                    <button type="button" class="btn btn-primary w-100" onclick="saveProfileSignature(this)">
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
document.addEventListener('DOMContentLoaded', function () {
    const canvas = document.getElementById('profileCanvas');
    if (canvas) {
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext("2d").scale(ratio, ratio);

        pad = new SignaturePad(canvas, {
            penColor: 'rgb(24, 43, 73)',
            backgroundColor: 'rgba(255, 255, 255, 0)',
            minWidth: 1.5,
            maxWidth: 3.5
        });

        document.getElementById('clearPadBtn').addEventListener('click', function () {
            pad.clear();
        });
    }
});

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
</script>
<?= $this->endSection() ?>
