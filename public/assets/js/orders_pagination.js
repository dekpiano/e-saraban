/**
 * e-Saraban Order Multi-Sheet Pagination Engine (v3.0 - Intelligent Document Balancing)
 * แบ่งเอกสารคำสั่งโรงเรียนออกเป็นแผ่นกระดาษ A4 จริง (Sheet 1, Sheet 2, ...)
 * พร้อมเว้นระยะขอบรอบกระดาษเป๊ะตามมาตรฐานสากลงานสารบรรณไทย:
 * ขอบบน 1.5 ซม. / ขอบซ้าย 3.0 ซม. / ขอบขวา 2.0 ซม. / ขอบล่าง 2.0 ซม.
 * - ไม่ตัดแบ่งย่อหน้าหากเหลือพื้นที่ไม่ถึง 5 บรรทัด (หลีกเลี่ยง Widow/Orphan และไม่ตัดครึ่งประโยคแบบยัดเยียด)
 * - หน้าลงนามต้องมีเนื้อหาเสมออย่างน้อย 6-10 บรรทัด (Intelligent Page Balancing)
 * - รักษาระยะขอบล่าง 2.0 ซม. เป๊ะ ไม่มีตัวหนังสือหลุดกรอบหรือตกขอบ
 * - ปลอดภัยต่อการเรียกซ้ำ 100% (Pristine Source Clone)
 */
window.SarabanOrderPaginator = {
    // แปลงเลขอารบิกเป็นเลขไทย
    toThaiDigits: function (num) {
        const thai = ['๐', '๑', '๒', '๓', '๔', '๕', '๖', '๗', '๘', '๙'];
        return String(num).split('').map(d => (thai[d] !== undefined ? thai[d] : d)).join('');
    },

    // วัดความสูง 1 หน้ากระดาษ A4 (29.7cm) ในหน่วยพิกเซลของเบราว์เซอร์
    getA4HeightPx: function () {
        const testDiv = document.createElement('div');
        testDiv.style.height = '29.7cm';
        testDiv.style.position = 'absolute';
        testDiv.style.visibility = 'hidden';
        testDiv.style.top = '-9999px';
        document.body.appendChild(testDiv);
        const px = testDiv.getBoundingClientRect().height;
        document.body.removeChild(testDiv);
        return px || 1122.5;
    },

    /**
     * ตัดแบ่งย่อหน้าที่ยาวเกินให้พอดีกับพื้นที่ที่เหลือ
     * โดยต้องมีเนื้อหาอย่างน้อย minTokens (ประมาณ 5-6 บรรทัด) เพื่อความสง่างามตามระเบียบงานสารบรรณ
     */
    splitParagraph: function (origP, currentSheet, bDiv, maxAllowedHeight, minTokens = 30) {
        const text = origP.innerText || origP.textContent || '';
        if (text.trim().length < 100) return null;

        // แยกเป็นโทเคน: คำ, ช่องว่าง, และ Zero-Width Space (\u200B)
        const tokens = text.split(/(\s+|\u200B)/);
        if (tokens.length < minTokens * 2) return null;

        const p1 = origP.cloneNode(false);
        p1.style.marginBottom = '0';
        bDiv.appendChild(p1);

        let low = minTokens;
        let high = tokens.length - 1;
        let best = -1;

        while (low <= high) {
            const mid = Math.floor((low + high) / 2);
            p1.textContent = tokens.slice(0, mid + 1).join('');
            if (currentSheet.offsetHeight <= maxAllowedHeight) {
                best = mid;
                low = mid + 1;
            } else {
                high = mid - 1;
            }
        }

        // ต้องมีเนื้อหาเหลือสำหรับแผ่นถัดไปด้วยอย่างน้อย minTokens
        if (best >= minTokens && (tokens.length - best >= minTokens)) {
            const part1Text = tokens.slice(0, best + 1).join('');
            const part2Text = tokens.slice(best + 1).join('');

            p1.textContent = part1Text;

            // ย่อหน้าส่วนที่เหลือสำหรับนำไปต่อบนแผ่นใหม่
            const p2 = origP.cloneNode(false);
            p2.className = (origP.className ? origP.className + ' ' : '') + 'order-paragraph-continued';
            p2.textContent = part2Text;
            return p2;
        } else {
            bDiv.removeChild(p1);
            return null;
        }
    },

    /**
     * คัดลอก Node พร้อมรักษาสถานะและค่าของ input, select, textarea ครบถ้วน 100%
     */
    cloneWithInputs: function (el) {
        if (!el) return null;
        const cloned = el.cloneNode(true);
        const origInputs = el.querySelectorAll('input, select, textarea');
        const clonedInputs = cloned.querySelectorAll('input, select, textarea');
        origInputs.forEach((inp, idx) => {
            if (clonedInputs[idx]) {
                clonedInputs[idx].value = inp.value;
                if (inp.type === 'checkbox' || inp.type === 'radio') {
                    clonedInputs[idx].checked = inp.checked;
                }
            }
        });
        return cloned;
    },

    /**
     * ดึงข้อความรวมจากทุกแผ่น พร้อมเชื่อมต่อย่อหน้าที่ถูกตัดข้ามหน้าให้เป็นย่อหน้าเดียวกันอย่างสมบูรณ์
     */
    getCombinedContent: function (containerRef) {
        const container = typeof containerRef === 'string' ? document.getElementById(containerRef) : containerRef;
        if (!container) return '';

        const bodyDivs = container.querySelectorAll('.order-body-content');
        if (bodyDivs.length === 0) {
            const editor = document.getElementById('orderEditor');
            return editor ? editor.innerHTML : '';
        }

        const dummy = document.createElement('div');
        bodyDivs.forEach(div => {
            Array.from(div.children).forEach(child => {
                dummy.appendChild(child.cloneNode(true));
            });
        });

        // ควบรวมย่อหน้าที่ต่อเนื่องกัน (order-paragraph-continued) กลับสู่ย่อหน้าต้นทาง
        dummy.querySelectorAll('.order-paragraph-continued').forEach(p2 => {
            const p1 = p2.previousElementSibling;
            if (p1 && p1.tagName === 'P') {
                p1.innerHTML = p1.innerHTML + ' ' + p2.innerHTML;
                p2.remove();
            } else {
                p2.classList.remove('order-paragraph-continued');
            }
        });

        return dummy.innerHTML;
    },

    /**
     * จัดแบ่งหน้าใหม่สำหรับโหมดแก้ไข (อัปเดตข้อมูลที่พิมพ์ล่าสุดเข้าสู่ Source ต้นฉบับก่อนคำนวณ)
     */
    rePaginate: function (containerRef) {
        const container = typeof containerRef === 'string' ? document.getElementById(containerRef) : containerRef;
        if (!container) return;

        // 1. ดึงค่า inputs ล่าสุดบนหน้าจอ
        const docNo = container.querySelector('input[name="doc_no"]')?.value;
        const title = container.querySelector('input[name="title"]')?.value;
        const effective = container.querySelector('input[name="order_effective_text"]')?.value;
        const docDate = container.querySelector('input[name="doc_date"]')?.value;
        const signerName = container.querySelector('input[name="signer_name"]')?.value;
        const signerPos = container.querySelector('input[name="signer_position"]')?.value;

        // 2. ดึงเนื้อหาข้อความที่ควบรวมแล้ว
        const combinedContent = this.getCombinedContent(container);

        // 3. ปรับปรุง Source ต้นฉบับ
        if (container._sarabanOriginalSource) {
            const orig = container._sarabanOriginalSource;
            const b = orig.querySelector('.order-body-content');
            if (b && combinedContent) b.innerHTML = combinedContent;
            if (docNo !== undefined) { const i = orig.querySelector('input[name="doc_no"]'); if (i) i.value = docNo; }
            if (title !== undefined) { const i = orig.querySelector('input[name="title"]'); if (i) i.value = title; }
            if (effective !== undefined) { const i = orig.querySelector('input[name="order_effective_text"]'); if (i) i.value = effective; }
            if (docDate !== undefined) { const i = orig.querySelector('input[name="doc_date"]'); if (i) i.value = docDate; }
            if (signerName !== undefined) { const i = orig.querySelector('input[name="signer_name"]'); if (i) i.value = signerName; }
            if (signerPos !== undefined) { const i = orig.querySelector('input[name="signer_position"]'); if (i) i.value = signerPos; }
        }

        // 4. สั่งแบ่งหน้าใหม่
        this.paginate(container);
    },

    /**
     * แบ่งหน้ากระดาษคำสั่งโรงเรียนออกเป็นแผ่น A4 แยกกันจริง
     * @param {string|HTMLElement} containerRef - ID หรือ Element ที่บรรจุกระดาษคำสั่ง
     */
    paginate: function (containerRef) {
        const container = typeof containerRef === 'string' ? document.getElementById(containerRef) : containerRef;
        if (!container) return;

        // บันทึกและดึงต้นฉบับที่สมบูรณ์ไว้เสมอ ป้องกันการเรียกซ้ำแล้วข้อมูลสูญหาย
        if (!container._sarabanOriginalSource) {
            const rawPaper = container.querySelector('.saraban-order-paper') || container;
            container._sarabanOriginalSource = this.cloneWithInputs(rawPaper);
        }

        const sourcePaper = this.cloneWithInputs(container._sarabanOriginalSource);

        // สกัดองค์ประกอบหลักของคำสั่งโรงเรียน
        const garuda = sourcePaper.querySelector('.order-garuda-center');
        const headerBlock = sourcePaper.querySelector('.order-header-block');
        const bodyContent = sourcePaper.querySelector('.order-body-content');
        const effectiveClause = sourcePaper.querySelector('.order-effective-clause');
        const dateClause = sourcePaper.querySelector('.order-date-clause');
        const sigContainer = sourcePaper.querySelector('.order-signature-container');

        if (!bodyContent) return;

        // ตรวจสอบสถานะการแก้ไข (contenteditable)
        const isEditable = (bodyContent && bodyContent.getAttribute('contenteditable') === 'true') ||
                           (container.querySelector('[contenteditable="true"]') !== null) ||
                           (sourcePaper.querySelector('[contenteditable="true"]') !== null);

        // สกัดย่อหน้าเนื้อหาทั้งหมด (แต่ละ <p> หรือบล็อกองค์ประกอบ)
        let blocks = Array.from(bodyContent.children).filter(el => el.nodeType === 1);
        if (blocks.length === 0 && bodyContent.innerHTML.trim().length > 0) {
            const p = document.createElement('p');
            p.innerHTML = bodyContent.innerHTML;
            blocks = [p];
        }

        const a4HeightPx = this.getA4HeightPx();
        // เผื่อระยะขอบล่าง 2.0 ซม. (75.6px) + Buffer 28px เพื่อความปลอดภัยสูงสุด ให้มีขอบล่างโปร่งสบายตา 2.0 - 2.5 ซม. เสมอ
        const maxAllowedSheetHeight = a4HeightPx - 28;

        // หาก container มีคลาส saraban-order-paper ให้ถอดคลาสและรีเซ็ตสไตล์ของ container ออกทันที
        if (container.classList.contains('saraban-order-paper')) {
            container.classList.remove('saraban-order-paper', 'shadow');
            container.style.cssText = 'background: transparent !important; padding: 0 !important; box-shadow: none !important; min-height: 0 !important; height: auto !important; width: 100% !important; margin: 0 auto !important; border: none !important;';
        }

        // เตรียม Wrapper หลักสำหรับบรรจุแผ่นกระดาษ
        const sheetsWrapper = document.createElement('div');
        sheetsWrapper.className = 'order-pages-container';

        let pageNum = 1;
        const self = this;

        function makeNewSheet(pNum) {
            const sheet = document.createElement('div');
            sheet.className = 'saraban-order-paper order-sheet page-' + pNum;
            sheet.style.minHeight = '0';
            sheet.style.height = 'auto';
            sheet.style.maxHeight = 'none';
            sheet.setAttribute('data-page', pNum);

            if (pNum > 1) {
                // หัวกระดาษแผ่นที่ ๒ ขึ้นไป (- ๒ -) กึ่งกลางบรรทัดแรก
                const hNum = document.createElement('div');
                hNum.className = 'order-page-header-number';
                hNum.innerHTML = `- ${self.toThaiDigits(pNum)} -`;
                sheet.appendChild(hNum);
            }

            const bDiv = document.createElement('div');
            bDiv.className = 'order-body-content';
            if (isEditable) {
                bDiv.setAttribute('contenteditable', 'true');
                bDiv.style.outline = 'none';
            }
            if (pNum === 1) {
                bDiv.id = 'orderEditor';
            }
            sheet.appendChild(bDiv);

            return { sheet, bDiv };
        }

        function finalizeSheet(sheet) {
            // เมื่อจัดเนื้อหาเสร็จแล้ว กำหนดขนาดเต็ม A4 และซ่อน overflow บนหน้าจอ
            sheet.style.minHeight = '29.7cm';
            sheet.style.height = '29.7cm';
            sheet.style.maxHeight = '29.7cm';
            sheet.style.overflow = 'hidden';
        }

        function makeSheetGap(fromP, toP) {
            const gap = document.createElement('div');
            gap.className = 'order-sheet-gap no-print';
            gap.innerHTML = `
                <span class="order-sheet-gap-badge">
                    <i class="bx bx-cut"></i> สิ้นสุดแผ่นที่ ${self.toThaiDigits(fromP)} / 📄 ขึ้นแผ่นที่ ${self.toThaiDigits(toP)} (- ${self.toThaiDigits(toP)} -)
                </span>
            `;
            return gap;
        }

        // 1. สร้างแผ่นที่ ๑
        let current = makeNewSheet(1);
        if (garuda) current.sheet.insertBefore(garuda.cloneNode(true), current.bDiv);
        if (headerBlock) current.sheet.insertBefore(this.cloneWithInputs(headerBlock), current.bDiv);

        sheetsWrapper.appendChild(current.sheet);
        container.innerHTML = '';
        container.appendChild(sheetsWrapper);

        // 2. ทยอยใส่ย่อหน้าเนื้อหาทีละบล็อก
        const blockQueue = blocks.map(b => b.cloneNode(true));
        let safetyCounter = 0;
        const MAX_STEPS = 500;

        while (blockQueue.length > 0 && safetyCounter++ < MAX_STEPS) {
            const block = blockQueue.shift();
            current.bDiv.appendChild(block);

            // ตรวจสอบความสูงจริงของเนื้อหาบนแผ่น
            if (current.sheet.offsetHeight > maxAllowedSheetHeight) {
                current.bDiv.removeChild(block);

                // หากบนแผ่นปัจจุบันมีเนื้อหาอยู่แล้ว และเหลือพื้นที่ไม่มาก ให้ยกทั้งย่อหน้าไปแผ่นใหม่เพื่อความต่อเนื่อง
                let remainder = null;
                // ถ้าแผ่นปัจจุบันยังว่างอยู่ หรือมีพื้นที่ว่างมากพอ ค่อยพยายามตัดแบ่ง
                if (current.bDiv.children.length === 0 || (maxAllowedSheetHeight - current.sheet.offsetHeight > 140)) {
                    remainder = self.splitParagraph(block, current.sheet, current.bDiv, maxAllowedSheetHeight, 25);
                }

                if (remainder) {
                    blockQueue.unshift(remainder);
                } else {
                    if (current.bDiv.children.length === 0) {
                        // หากเป็นบล็อกเดียวบนแผ่น ให้ตัดแบ่งแบบยืดหยุ่น
                        remainder = self.splitParagraph(block, current.sheet, current.bDiv, maxAllowedSheetHeight, 10);
                        if (remainder) {
                            blockQueue.unshift(remainder);
                        } else {
                            current.bDiv.appendChild(block);
                        }
                    } else {
                        // มีเนื้อหาเดิมอยู่แล้ว ยกบล็อกนี้ไปแผ่นถัดไปทั้งก้อน
                        blockQueue.unshift(block);
                    }
                }

                finalizeSheet(current.sheet);

                const gap = makeSheetGap(pageNum, pageNum + 1);
                sheetsWrapper.appendChild(gap);

                pageNum++;
                current = makeNewSheet(pageNum);
                sheetsWrapper.appendChild(current.sheet);
            }
        }

        // 3. ทยอยใส่องค์ประกอบท้ายคำสั่ง (ย่อหน้าบังคับใช้ + วันที่ + ส่วนลงนาม)
        const effClone = effectiveClause ? this.cloneWithInputs(effectiveClause) : null;
        const signBlockGroup = document.createElement('div');
        signBlockGroup.className = 'order-signing-bundle';
        if (dateClause) signBlockGroup.appendChild(this.cloneWithInputs(dateClause));
        if (sigContainer) signBlockGroup.appendChild(this.cloneWithInputs(sigContainer));

        if (effClone) current.sheet.appendChild(effClone);
        current.sheet.appendChild(signBlockGroup);

        if (current.sheet.offsetHeight > maxAllowedSheetHeight) {
            // ไม่พอดีบนแผ่นปัจจุบัน: นำออกแล้วเปิดแผ่นใหม่
            current.sheet.removeChild(signBlockGroup);
            if (effClone) current.sheet.removeChild(effClone);

            // กฎเหล็กงานสารบรรณ: ห้ามขึ้นหน้าใหม่เฉพาะส่วนลงนามโดยไม่มีเนื้อหาเด็ดขาด
            // ถ้าแผ่นปัจจุบันมีข้อความยาว ให้แบ่งเนื้อหาท่อนล่าง (ประมาณ 30-40%) ข้ามไปอยู่เป็นเนื้อหานำในหน้าลงนาม
            let transferredText = null;
            if (current.bDiv.lastElementChild) {
                const lastP = current.bDiv.lastElementChild;
                const text = lastP.innerText || lastP.textContent || '';
                const tokens = text.split(/(\s+|\u200B)/);

                if (tokens.length > 50) {
                    // ตัดแบ่งครึ่งย่อหน้าสุดท้าย ให้ท่อนแรกอยู่แผ่นเดิม ท่อนหลังข้ามไปหน้าลงนาม
                    const splitPoint = Math.floor(tokens.length * 0.6);
                    const part1 = tokens.slice(0, splitPoint).join('');
                    const part2 = tokens.slice(splitPoint).join('');

                    lastP.textContent = part1;

                    transferredText = lastP.cloneNode(false);
                    transferredText.className = (lastP.className ? lastP.className + ' ' : '') + 'order-paragraph-continued';
                    transferredText.textContent = part2;
                } else if (current.bDiv.children.length > 1) {
                    // ย่อหน้าสุดท้ายสั้น ดึงทั้งย่อหน้าข้ามไปหน้าลงนาม
                    transferredText = lastP;
                    current.bDiv.removeChild(lastP);
                }
            }

            finalizeSheet(current.sheet);

            const gap = makeSheetGap(pageNum, pageNum + 1);
            sheetsWrapper.appendChild(gap);

            pageNum++;
            current = makeNewSheet(pageNum);
            sheetsWrapper.appendChild(current.sheet);

            if (transferredText) {
                current.bDiv.appendChild(transferredText);
            }

            if (effClone) current.sheet.appendChild(effClone);
            current.sheet.appendChild(signBlockGroup);
        }

        // สรุปขนาดแผ่นสุดท้ายให้เต็ม A4
        finalizeSheet(current.sheet);

        // อัปเดตตัวนับหน้าใน Toolbar (ถ้ามี)
        const counterBadge = document.getElementById('editOrderPageCounter') || document.getElementById('createOrderPageCounter');
        const pageText = document.getElementById('pageCountText');
        if (pageText) {
            if (pageNum > 1) {
                pageText.innerHTML = `เอกสาร ${self.toThaiDigits(pageNum)} แผ่น (A4)`;
                if (counterBadge) counterBadge.className = 'badge bg-warning text-dark fw-bold px-2.5 py-2 shadow-xs';
            } else {
                pageText.innerHTML = `เอกสาร ๑ แผ่น (A4)`;
                if (counterBadge) counterBadge.className = 'badge bg-dark text-white fw-bold px-2.5 py-2 shadow-xs';
            }
        }
    }
};
