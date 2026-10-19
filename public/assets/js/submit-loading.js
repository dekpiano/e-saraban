/**
 * Universal Submit Button Loading State Handler
 * ระบบจัดการสถานะ Loading สำหรับปุ่ม Submit ทุกปุ่มในระบบ e-Saraban & e-Office
 * 
 * - ป้องกันการกดซ้ำ (Double Submission Prevention)
 * - แสดง Spinner หมุนแบบพรีเมียมด้วย Bootstrap 5
 * - ปรับข้อความอัตโนมัติอย่างชาญฉลาด (กำลังบันทึก..., กำลังลงนาม..., กำลังค้นหา...)
 * - คงความกว้างเดิมของปุ่ม (Min-width locking) ไม่ให้ปุ่มหดหรือกระตุก
 * - รองรับทั้ง Native Submit, jQuery Submit และ Programmatic form.submit()
 */
(function (window, document) {
    'use strict';

    // บันทึกปุ่ม submit ที่ถูกคลิกล่าสุด
    let activeSubmitButton = null;

    // ดักจับการคลิกที่ปุ่ม submit ทุกปุ่มในระบบ
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('button[type="submit"], input[type="submit"], button:not([type]), [data-submit-loading]');
        if (btn && btn.closest('form')) {
            activeSubmitButton = btn;
            // ผูกเข้ากับ form โดยตรง
            btn.closest('form').__activeSubmitButton = btn;
        }
    }, true);

    /**
     * ดึงข้อความ Loading ที่เหมาะสมกับบริบทของปุ่ม
     */
    function resolveLoadingText(btn) {
        if (!btn) return 'กำลังดำเนินการ...';

        // 1. ตรวจสอบ data-loading-text ก่อน
        const customText = btn.getAttribute('data-loading-text');
        if (customText) return customText;

        const rawText = (btn.textContent || btn.innerText || btn.value || '').trim();
        if (!rawText) return ''; // ปุ่มที่มีเฉพาะไอคอน

        // 2. ตรวจสอบบริบทภาษาไทย
        if (rawText.includes('บันทึก')) return 'กำลังบันทึก...';
        if (rawText.includes('ลงนาม') || rawText.includes('เกษียน')) return 'กำลังลงนาม...';
        if (rawText.includes('อนุมัติ')) return 'กำลังอนุมัติ...';
        if (rawText.includes('ส่งคืน')) return 'กำลังส่งคืน...';
        if (rawText.includes('ส่งมอบ') || rawText.includes('ส่งเรื่อง') || rawText.includes('ส่งรายงาน')) return 'กำลังส่งข้อมูล...';
        if (rawText.includes('ค้นหา')) return 'กำลังค้นหา...';
        if (rawText.includes('อัปโหลด') || rawText.includes('Upload')) return 'กำลังอัปโหลด...';
        if (rawText.includes('ลบ')) return 'กำลังลบข้อมูล...';
        if (rawText.includes('แต่งตั้ง') || rawText.includes('สร้าง')) return 'กำลังสร้างข้อมูล...';
        if (rawText.includes('เข้าสู่ระบบ') || rawText.includes('Login')) return 'กำลังเข้าสู่ระบบ...';
        if (rawText.includes('ยืนยัน')) return 'กำลังยืนยัน...';
        if (rawText.includes('เปลี่ยน')) return 'กำลังดำเนินการ...';
        if (rawText.includes('ระงับ') || rawText.includes('เปิดใช้งาน')) return 'กำลังดำเนินการ...';

        return 'กำลังดำเนินการ...';
    }

    /**
     * เปลี่ยนปุ่มเข้าสู่สถานะ Loading
     */
    function applyLoadingState(btn, customText) {
        if (!btn || btn.classList.contains('is-loading-btn')) return;

        // ข้ามหากฟอร์มหรือปุ่มกำหนด data-no-loading="true"
        if (btn.getAttribute('data-no-loading') === 'true' || (btn.form && btn.form.getAttribute('data-no-loading') === 'true')) {
            return;
        }

        // 1. ล็อกความกว้างของปุ่ม (Min-width) ป้องกันปุ่มหดหรือกระโดด
        const rect = btn.getBoundingClientRect();
        if (rect && rect.width > 0) {
            btn.style.minWidth = rect.width + 'px';
        }

        // 2. เก็บสถานะเดิมไว้กู้คืน
        btn.setAttribute('data-original-html', btn.innerHTML);
        btn.setAttribute('data-original-disabled', btn.disabled ? 'true' : 'false');
        btn.classList.add('is-loading-btn');

        // 3. เตรียมไอคอน Spinner และข้อความ
        const text = customText !== undefined ? customText : resolveLoadingText(btn);
        const spinnerHtml = '<span class="spinner-border spinner-border-sm ' + (text ? 'me-2' : '') + '" role="status" aria-hidden="true" style="vertical-align: -0.125em;"></span>';

        if (btn.tagName === 'INPUT') {
            btn.value = text || 'กำลังโหลด...';
        } else {
            btn.innerHTML = spinnerHtml + (text ? '<span class="loading-label">' + text + '</span>' : '');
        }

        // 4. แสดงสถานะ disabled และปิด pointer-events เพื่อป้องกัน double submit
        btn.classList.add('disabled');
        btn.style.pointerEvents = 'none';
        btn.setAttribute('aria-disabled', 'true');

        // ดีเลย์ปิดปุ่มจริงนิดเดียวเพื่อให้ browser ส่งค่า name/value ของปุ่มไปยัง request ได้อย่างถูกต้อง
        setTimeout(function () {
            btn.disabled = true;
        }, 30);

        // 5. ป้องกันกรณีค้าง (Safeguard 20 วินาที คืนค่าเดิมหากหน้าไม่ reload เช่น ดาวน์โหลดไฟล์เสร็จ)
        setTimeout(function () {
            restoreOriginalState(btn);
        }, 20000);
    }

    /**
     * คืนค่าปุ่มกลับสู่สถานะปกติ
     */
    function restoreOriginalState(btn) {
        if (!btn || !btn.classList.contains('is-loading-btn')) return;

        btn.classList.remove('is-loading-btn');
        btn.classList.remove('disabled');
        btn.removeAttribute('aria-disabled');
        btn.style.pointerEvents = '';
        btn.style.minWidth = '';
        btn.disabled = btn.getAttribute('data-original-disabled') === 'true';

        const originalHtml = btn.getAttribute('data-original-html');
        if (originalHtml !== null) {
            if (btn.tagName === 'INPUT') {
                btn.value = originalHtml;
            } else {
                btn.innerHTML = originalHtml;
            }
            btn.removeAttribute('data-original-html');
        }
    }

    /**
     * ดึงรายการไฟล์ที่ถูกเลือกในฟอร์ม
     */
    function getSelectedFiles(form) {
        if (!form) return [];
        const fileInputs = form.querySelectorAll('input[type="file"]');
        const list = [];
        fileInputs.forEach(function (inp) {
            if (inp.files && inp.files.length > 0) {
                for (var i = 0; i < inp.files.length; i++) {
                    if (inp.files[i] && inp.files[i].size > 0) {
                        list.push(inp.files[i]);
                    }
                }
            }
        });
        return list;
    }

    /**
     * จัดการเมื่อเกิด Form Submit Event
     */
    function onFormSubmit(e) {
        const form = e.target;
        if (!form || form.tagName !== 'FORM') return;

        // ข้ามหากติด HTML5 Validation
        if (typeof form.checkValidity === 'function' && !form.checkValidity()) {
            return;
        }

        // หาปุ่ม submit ที่ทำงาน
        let btn = form.__activeSubmitButton || activeSubmitButton;
        if (!btn || !form.contains(btn)) {
            btn = form.querySelector('button[type="submit"]:not([disabled]), input[type="submit"]:not([disabled]), button:not([type]):not([disabled])');
        }

        if (!btn) return;

        // ตรวจสอบว่ามีการอัปโหลดไฟล์ในฟอร์มหรือไม่
        const selectedFiles = getSelectedFiles(form);
        const isPost = (form.method || 'POST').toUpperCase() === 'POST';
        const skipProgress = form.getAttribute('data-no-progress') === 'true' || form.id === 'travelUploadForm';

        // กรณีมีการแนบไฟล์ในฟอร์ม POST ให้ใช้ AJAX Upload พร้อมแสดงความก้าวหน้าแบบเปอร์เซ็นต์
        if (isPost && selectedFiles.length > 0 && !skipProgress) {
            e.preventDefault();

            applyLoadingState(btn, 'กำลังอัปโหลด... 0%');

            var totalBytes = 0;
            selectedFiles.forEach(function(f) { totalBytes += f.size; });
            var totalMbText = (totalBytes / (1024 * 1024)).toFixed(2) + ' MB';
            var firstFileName = selectedFiles[0].name + (selectedFiles.length > 1 ? ' (' + selectedFiles.length + ' ไฟล์)' : '');

            // แสดง SweetAlert2 Progress Dialog
            if (window.AppAlert && typeof window.AppAlert.showUploadProgress === 'function') {
                window.AppAlert.showUploadProgress({
                    title: 'กำลังบันทึกและอัปโหลดไฟล์...',
                    filename: firstFileName,
                    totalSizeText: totalMbText
                });
            }

            var formData = new FormData(form);
            if (btn && btn.name && !formData.has(btn.name)) {
                formData.append(btn.name, btn.value || '');
            }

            var xhr = new XMLHttpRequest();
            xhr.open(form.method || 'POST', form.action, true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

            // Event อัปเดตเปอร์เซ็นต์แบบ Real-time
            xhr.upload.onprogress = function(event) {
                if (event.lengthComputable && event.total > 0) {
                    var percent = Math.min(99, Math.round((event.loaded / event.total) * 100));
                    var loadedMb = (event.loaded / (1024 * 1024)).toFixed(2) + ' MB';
                    var totalMb = (event.total / (1024 * 1024)).toFixed(2) + ' MB';

                    if (window.AppAlert && typeof window.AppAlert.updateUploadProgress === 'function') {
                        window.AppAlert.updateUploadProgress(percent, loadedMb, totalMb);
                    }
                    if (btn) {
                        var label = btn.querySelector('.loading-label');
                        if (label) label.textContent = 'กำลังอัปโหลด... ' + percent + '%';
                    }
                }
            };

            xhr.upload.onload = function() {
                if (window.AppAlert && typeof window.AppAlert.updateUploadProgress === 'function') {
                    window.AppAlert.updateUploadProgress(100, totalMbText, totalMbText, '<i class="bx bx-check-circle me-1 text-success"></i> อัปโหลดไฟล์ครบ 100% แล้ว กำลังประมวลผลจัดเก็บข้อมูล... (ใกล้เสร็จแล้ว)');
                }
                if (btn) {
                    var label = btn.querySelector('.loading-label');
                    if (label) label.textContent = 'กำลังประมวลผลข้อมูล... 100%';
                }
            };

            xhr.onload = function() {
                if (xhr.status >= 200 && xhr.status < 300) {
                    var res = null;
                    var contentType = xhr.getResponseHeader('Content-Type') || '';
                    if (contentType.indexOf('application/json') !== -1) {
                        try { res = JSON.parse(xhr.responseText); } catch(e) {}
                    }

                    if (res && res.status === 'success') {
                        if (res.message) {
                            try { sessionStorage.setItem('flash_success', res.message); } catch(e) {}
                        }
                        if (res.redirect) {
                            window.location.href = res.redirect;
                            return;
                        } else {
                            window.location.reload();
                            return;
                        }
                    } else if (res && res.status === 'error') {
                        if (window.AppAlert && typeof window.AppAlert.closeUploadProgress === 'function') {
                            window.AppAlert.closeUploadProgress();
                        }
                        restoreOriginalState(btn);
                        if (window.AppAlert) {
                            window.AppAlert.error(res.message || 'เกิดข้อผิดพลาดในการบันทึกข้อมูล', 'แจ้งเตือนข้อผิดพลาด');
                        } else {
                            alert(res.message || 'เกิดข้อผิดพลาด');
                        }
                        return;
                    }

                    // หากเซิร์ฟเวอร์ส่ง HTML redirect มาตามปกติ
                    if (xhr.responseURL && xhr.responseURL !== window.location.href) {
                        window.location.href = xhr.responseURL;
                    } else {
                        window.location.reload();
                    }
                } else {
                    if (window.AppAlert && typeof window.AppAlert.closeUploadProgress === 'function') {
                        window.AppAlert.closeUploadProgress();
                    }
                    restoreOriginalState(btn);
                    var errMsg = 'เกิดข้อผิดพลาดจากเซิร์ฟเวอร์ (รหัส ' + xhr.status + ')';
                    try {
                        var jsonErr = JSON.parse(xhr.responseText);
                        if (jsonErr && jsonErr.message) errMsg = jsonErr.message;
                    } catch(e) {}
                    if (window.AppAlert) {
                        window.AppAlert.error(errMsg, 'ไม่สามารถบันทึกข้อมูลได้');
                    } else {
                        alert(errMsg);
                    }
                }
            };

            xhr.onerror = function() {
                if (window.AppAlert && typeof window.AppAlert.closeUploadProgress === 'function') {
                    window.AppAlert.closeUploadProgress();
                }
                restoreOriginalState(btn);
                if (window.AppAlert) {
                    window.AppAlert.error('การเชื่อมต่อเครือข่ายขัดข้อง กรุณาลองใหม่อีกครั้ง', 'เกิดข้อผิดพลาด');
                } else {
                    alert('การเชื่อมต่อเครือข่ายขัดข้อง');
                }
            };

            xhr.send(formData);
            return;
        }

        // ฟอร์มทั่วไปที่ไม่มีไฟล์ ให้ทำงานตามปกติ
        setTimeout(function () {
            if (e.defaultPrevented || (typeof e.isDefaultPrevented === 'function' && e.isDefaultPrevented())) {
                return;
            }
            applyLoadingState(btn);
        }, 0);
    }

    // ลงทะเบียนดักจับ Native Submit
    document.addEventListener('submit', onFormSubmit, false);

    // รองรับ jQuery form submission
    if (typeof window.jQuery !== 'undefined') {
        window.jQuery(document).on('submit', 'form', function (e) {
            const form = this;
            setTimeout(function () {
                if (e.isDefaultPrevented()) {
                    const btn = form.querySelector('.is-loading-btn');
                    if (btn) restoreOriginalState(btn);
                }
            }, 10);
        });
    }

    // Hook Programmatic form.submit() เผื่อบางจุดในระบบเรียก form.submit() ด้วย JavaScript
    const nativeFormSubmit = HTMLFormElement.prototype.submit;
    HTMLFormElement.prototype.submit = function () {
        const form = this;
        let btn = form.__activeSubmitButton || activeSubmitButton;
        if (!btn || !form.contains(btn)) {
            btn = form.querySelector('button[type="submit"], input[type="submit"], button:not([type])');
        }
        if (btn) {
            applyLoadingState(btn);
        }
        return nativeFormSubmit.apply(form, arguments);
    };

    // Public API สำหรับเรียกใช้งานโดยตรง
    window.SubmitLoading = {
        set: applyLoadingState,
        reset: restoreOriginalState,
        resetForm: function (form) {
            if (!form) return;
            const btns = form.querySelectorAll('.is-loading-btn');
            btns.forEach(restoreOriginalState);
        }
    };

})(window, document);
