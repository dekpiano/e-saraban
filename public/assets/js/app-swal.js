/**
 * Global SweetAlert2 Bridge & Helpers (School e-Saraban)
 * - รองรับ Font 'K2D' และสไตล์ Sneat Palette
 * - มีฟังก์ชันสำเร็จรูป: AppAlert.success, error, warning, info, confirm, toast, prompt
 * - Overwrite window.alert และ window.confirm แบบ seamless เพื่อความสวยงามทั่วทั้งระบบ
 */
(function(window) {
    'use strict';

    // กำหนด Theme Defaults ให้เข้ากับ Sneat Theme & K2D Typography
    const customSwal = (typeof Swal !== 'undefined') ? Swal.mixin({
        customClass: {
            popup: 'rounded-4 shadow-lg border-0',
            title: 'fw-bold text-dark fs-5',
            htmlContainer: 'text-secondary fs-6',
            confirmButton: 'btn btn-primary fw-bold px-4 py-2 mx-1 shadow-sm',
            cancelButton: 'btn btn-outline-secondary fw-bold px-4 py-2 mx-1',
            denyButton: 'btn btn-danger fw-bold px-4 py-2 mx-1 shadow-sm'
        },
        buttonsStyling: false
    }) : null;

    const AppAlert = {
        success: function(message, title) {
            if (!customSwal) { window.alert(message); return Promise.resolve(); }
            return customSwal.fire({
                icon: 'success',
                title: title || 'สำเร็จ',
                text: message,
                confirmButtonText: '<i class="bx bx-check me-1"></i> ตกลง',
                timer: 4000,
                timerProgressBar: true
            });
        },

        error: function(message, title) {
            if (!customSwal) { window.alert(message); return Promise.resolve(); }
            return customSwal.fire({
                icon: 'error',
                title: title || 'ข้อผิดพลาด',
                text: message,
                confirmButtonText: '<i class="bx bx-x me-1"></i> ปิด'
            });
        },

        warning: function(message, title) {
            if (!customSwal) { window.alert(message); return Promise.resolve(); }
            return customSwal.fire({
                icon: 'warning',
                title: title || 'แจ้งเตือน',
                text: message,
                confirmButtonText: '<i class="bx bx-check me-1"></i> รับทราบ'
            });
        },

        info: function(message, title) {
            if (!customSwal) { window.alert(message); return Promise.resolve(); }
            return customSwal.fire({
                icon: 'info',
                title: title || 'ข้อมูลเพิ่มเติม',
                text: message,
                confirmButtonText: '<i class="bx bx-info-circle me-1"></i> ตกลง'
            });
        },

        toast: function(message, type) {
            if (typeof Swal === 'undefined') return;
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3500,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });
            Toast.fire({
                icon: type || 'success',
                title: message
            });
        },

        confirm: function(message, options) {
            if (!customSwal) {
                return Promise.resolve(window.confirm(message));
            }
            options = options || {};
            return customSwal.fire({
                icon: options.icon || 'warning',
                title: options.title || 'ยืนยันการดำเนินการ',
                text: message,
                showCancelButton: true,
                confirmButtonText: options.confirmText || '<i class="bx bx-check me-1"></i> ยืนยัน',
                cancelButtonText: options.cancelText || '<i class="bx bx-x me-1"></i> ยกเลิก',
                reverseButtons: true,
                focusCancel: true
            }).then((result) => {
                return result.isConfirmed;
            });
        },

        prompt: function(title, options) {
            if (!customSwal) {
                return Promise.resolve(window.prompt(title));
            }
            options = options || {};
            return customSwal.fire({
                title: title,
                text: options.text || '',
                input: options.inputType || 'text',
                inputPlaceholder: options.placeholder || '',
                inputAttributes: {
                    autocapitalize: 'off',
                    required: options.required ? 'true' : 'false'
                },
                showCancelButton: true,
                confirmButtonText: options.confirmText || '<i class="bx bx-check me-1"></i> ตกลง',
                cancelButtonText: options.cancelText || '<i class="bx bx-x me-1"></i> ยกเลิก',
                reverseButtons: true,
                inputValidator: (value) => {
                    if (options.required && (!value || !value.trim())) {
                        return options.errorMessage || 'กรุณาระบุข้อมูลให้ครบถ้วน';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    return result.value;
                }
                return null;
            });
        },

        showUploadProgress: function(options) {
            if (!customSwal) return Promise.resolve();
            options = options || {};
            const title = options.title || 'กำลังบันทึกและอัปโหลดไฟล์...';
            const filename = options.filename || 'เอกสารแนบ';
            const totalSizeText = options.totalSizeText || '';

            const html = `
                <div class="text-center p-2" style="font-family: 'K2D', sans-serif;">
                    <div class="mb-3">
                        <span class="rounded-circle d-inline-flex align-items-center justify-content-center shadow-xs" style="width: 72px; height: 72px; background: linear-gradient(135deg, #e8e9ff 0%, #d8daff 100%);">
                            <i class="bx bxs-cloud-upload fs-1 text-primary"></i>
                        </span>
                    </div>
                    <h5 class="fw-bold text-dark mb-1" id="swalUploadTitle" style="font-family: 'K2D', sans-serif;">${title}</h5>
                    <div class="text-muted small mb-3 text-truncate px-2" id="swalUploadFilename" style="max-width: 340px; margin: 0 auto;">
                        <i class="bx bxs-file-pdf text-danger me-1"></i> ${filename}
                    </div>

                    <!-- Progress Bar Container -->
                    <div class="progress mb-2 shadow-xs" style="height: 20px; border-radius: 12px; background-color: #f1f3f9; overflow: hidden;">
                        <div id="swalUploadBar" class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 0%; font-size: 0.8rem; font-weight: 700; transition: width 0.15s ease;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                            <span id="swalUploadBarText">0%</span>
                        </div>
                    </div>

                    <!-- Counter & Percentage -->
                    <div class="d-flex justify-content-between align-items-center small text-muted px-1 mb-2">
                        <span id="swalUploadBytes"><i class="bx bx-data me-1"></i>${totalSizeText ? '0 MB / ' + totalSizeText : 'กำลังเริ่มส่งข้อมูล...'}</span>
                        <span class="fw-bold text-primary fs-5" id="swalUploadPercent">0%</span>
                    </div>

                    <!-- Status Text -->
                    <div class="small fw-semibold mt-2 px-3 py-2 rounded-3 text-truncate" id="swalUploadStatus" style="background-color: #f8fafc; color: #475569; border: 1px solid #e2e8f0;">
                        <i class="bx bx-loader-alt bx-spin me-1 text-primary"></i> กำลังส่งข้อมูลไปยังเซิร์ฟเวอร์...
                    </div>
                    <div class="text-muted mt-2" style="font-size: 0.75rem;">
                        <i class="bx bx-shield-quarter text-success me-1"></i> กรุณาอย่าปิดหรือรีเฟรชหน้าต่างเบราว์เซอร์จนกว่าจะบันทึกเสร็จสิ้น
                    </div>
                </div>
            `;

            return customSwal.fire({
                html: html,
                showConfirmButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                allowEnterKey: false
            });
        },

        updateUploadProgress: function(percent, loadedText, totalText, customStatus) {
            const bar = document.getElementById('swalUploadBar');
            const barText = document.getElementById('swalUploadBarText');
            const percentEl = document.getElementById('swalUploadPercent');
            const bytesEl = document.getElementById('swalUploadBytes');
            const statusEl = document.getElementById('swalUploadStatus');

            percent = Math.max(0, Math.min(100, Math.round(percent)));

            if (bar) {
                bar.style.width = percent + '%';
                bar.setAttribute('aria-valuenow', percent);
                if (percent >= 100) {
                    bar.classList.remove('bg-primary');
                    bar.classList.add('bg-success');
                }
            }
            if (barText) {
                barText.textContent = percent + '%';
            }
            if (percentEl) {
                percentEl.textContent = percent + '%';
                if (percent >= 100) {
                    percentEl.classList.remove('text-primary');
                    percentEl.classList.add('text-success');
                }
            }
            if (bytesEl && loadedText && totalText) {
                bytesEl.innerHTML = '<i class="bx bx-data me-1"></i>' + loadedText + ' / ' + totalText;
            }
            if (statusEl) {
                if (customStatus) {
                    statusEl.innerHTML = customStatus;
                } else if (percent >= 100) {
                    statusEl.innerHTML = '<i class="bx bx-check-circle me-1 text-success"></i> อัปโหลดไฟล์ครบ 100% แล้ว กำลังประมวลผลและจัดเก็บข้อมูล... (ใกล้เสร็จแล้ว)';
                    statusEl.style.color = '#15803d';
                    statusEl.style.backgroundColor = '#f0fdf4';
                    statusEl.style.borderColor = '#bbf7d0';
                } else {
                    statusEl.innerHTML = '<i class="bx bx-loader-alt bx-spin me-1 text-primary"></i> กำลังอัปโหลดข้อมูลไปยังเซิร์ฟเวอร์... ' + percent + '%';
                }
            }
        },

        closeUploadProgress: function() {
            if (customSwal) {
                customSwal.close();
            }
        }
    };

    window.AppAlert = AppAlert;

    // แทนที่ window.alert ปกติด้วย SweetAlert2 สวยงามและไม่บล็อกเธรด
    if (typeof Swal !== 'undefined') {
        window.alert = function(msg) {
            AppAlert.warning(msg);
        };
    }

})(window);
