/**
 * flatpickr-thai-buddhist.js
 * Enhanced Thai Buddhist Era (พ.ศ.) Plugin & Formatter for Flatpickr
 * 
 * Features:
 * - Accurately formats Thai Buddhist Era (B.E. = A.D. + 543) in display inputs (altInput)
 * - Converts back to Gregorian (A.D.) for underlying form values (Y-m-d) for database storage
 * - Displays Buddhist year in the calendar header without flickering or jumping to 3113+
 * - Smooth year navigation (arrow up/down & direct year typing) properly maps to Buddhist years
 * - Safe formatting & parsing of all Thai date tokens
 */
(function (global, factory) {
    var root = typeof globalThis !== 'undefined' ? globalThis : (typeof window !== 'undefined' ? window : global || self);
    if (typeof exports === 'object' && typeof module !== 'undefined') {
        module.exports = factory(root);
    } else if (typeof define === 'function' && define.amd) {
        define(function () { return factory(root); });
    } else {
        factory(root);
    }
}(this, (function (root) {
    'use strict';

    root = root || (typeof window !== 'undefined' ? window : globalThis);

    var thaiMonthsFull = [
        'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน',
        'พฤษภาคม', 'มิถุนายน', 'กรกฎาคม', 'สิงหาคม',
        'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
    ];

    var thaiMonthsShort = [
        'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.',
        'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.',
        'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'
    ];

    var thaiWeekdaysFull = [
        'อาทิตย์', 'จันทร์', 'อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์', 'เสาร์'
    ];

    var thaiWeekdaysShort = [
        'อา', 'จ', 'อ', 'พ', 'พฤ', 'ศ', 'ส'
    ];

    function pad(number, length) {
        if (length === void 0) { length = 2; }
        return ('000' + number).slice(length * -1);
    }

    /**
     * Format a Date object into a string with Thai Buddhist tokens support
     */
    function formatThaiDate(dateObj, frmt, locale) {
        if (!dateObj || !(dateObj instanceof Date) || isNaN(dateObj.getTime())) {
            return '';
        }

        var d = dateObj.getDate();
        var m = dateObj.getMonth();
        var y = dateObj.getFullYear();
        var be = y + 543;
        var dayOfWeek = dateObj.getDay();
        var hours = dateObj.getHours();
        var minutes = dateObj.getMinutes();
        var seconds = dateObj.getSeconds();

        var tokens = {
            // Day
            d: pad(d),
            j: String(d),
            // Month
            m: pad(m + 1),
            n: String(m + 1),
            F: thaiMonthsFull[m],
            M: thaiMonthsShort[m],
            // Gregorian Year
            Y: pad(y, 4),
            y: String(y).slice(-2),
            // Buddhist Year
            B: String(be),
            b: String(be).slice(-2),
            // Weekday
            l: thaiWeekdaysFull[dayOfWeek],
            D: thaiWeekdaysShort[dayOfWeek],
            w: String(dayOfWeek),
            // Time
            H: pad(hours),
            h: pad(hours % 12 || 12),
            G: String(hours % 12 || 12),
            i: pad(minutes),
            s: pad(seconds),
            K: hours >= 12 ? 'PM' : 'AM',
            U: String(Math.floor(dateObj.getTime() / 1000)),
            u: String(dateObj.getTime()),
            Z: dateObj.toISOString()
        };

        return frmt
            .split('')
            .map(function (c, i, arr) {
                if (tokens[c] !== undefined && arr[i - 1] !== '\\') {
                    return tokens[c];
                }
                return c !== '\\' ? c : '';
            })
            .join('');
    }

    /**
     * Parse input date string (Thai Buddhist or Gregorian or ISO) to Gregorian Date object
     */
    function parseThaiDate(dateStr, format) {
        if (!dateStr && dateStr !== 0) return undefined;
        if (dateStr instanceof Date) return new Date(dateStr.getTime());
        if (typeof dateStr === 'number') return new Date(dateStr);

        var datestr = String(dateStr).trim();
        if (datestr === 'today') {
            return new Date();
        }

        // 1. ISO YYYY-MM-DD
        var isoMatch = /^(\d{4})-(\d{1,2})-(\d{1,2})/.exec(datestr);
        if (isoMatch) {
            var y = parseInt(isoMatch[1], 10);
            var m = parseInt(isoMatch[2], 10) - 1;
            var d = parseInt(isoMatch[3], 10);
            if (y > 2400) y -= 543; // In case Buddhist year was entered as YYYY-MM-DD
            return new Date(y, m, d);
        }

        // 2. Slash/dash DD/MM/YYYY
        var slashMatch = /^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})/.exec(datestr);
        if (slashMatch) {
            var d = parseInt(slashMatch[1], 10);
            var m = parseInt(slashMatch[2], 10) - 1;
            var y = parseInt(slashMatch[3], 10);
            if (y > 2400) y -= 543;
            return new Date(y, m, d);
        }

        // 3. Thai Text: e.g. "13 ตุลาคม 2569" or "13 ต.ค. 2569"
        var parts = datestr.split(/[\s,]+/);
        var parsedDay, parsedMonth = -1, parsedYear;
        for (var i = 0; i < parts.length; i++) {
            var p = parts[i];
            if (/^\d{1,2}$/.test(p) && parsedDay === undefined) {
                parsedDay = parseInt(p, 10);
            } else if (/^\d{4}$/.test(p)) {
                parsedYear = parseInt(p, 10);
            } else {
                var fIdx = thaiMonthsFull.indexOf(p);
                if (fIdx !== -1) parsedMonth = fIdx;
                var sIdx = thaiMonthsShort.indexOf(p);
                if (sIdx !== -1) parsedMonth = sIdx;
            }
        }

        if (parsedDay !== undefined && parsedMonth !== -1 && parsedYear !== undefined) {
            if (parsedYear > 2400) parsedYear -= 543;
            return new Date(parsedYear, parsedMonth, parsedDay);
        }

        // Standard JS date parsing fallback
        var nativeParsed = new Date(datestr);
        if (!isNaN(nativeParsed.getTime())) {
            // Check if year is Buddhist
            if (nativeParsed.getFullYear() > 2400) {
                nativeParsed.setFullYear(nativeParsed.getFullYear() - 543);
            }
            return nativeParsed;
        }

        return undefined;
    }

    /**
     * Thai Buddhist Year Display Plugin for Flatpickr
     */
    function thaiBuddhistYearDisplayPlugin(pluginConfig) {
        return function (fp) {
            if (fp.config.noCalendar || fp.isMobile) {
                return {};
            }

            // Sync the year input element in flatpickr header to display Buddhist year
            function syncYearDisplay() {
                if (!fp.yearElements || !fp.yearElements.length) return;

                var beYear = fp.currentYear + 543;
                for (var i = 0; i < fp.yearElements.length; i++) {
                    var yEl = fp.yearElements[i];
                    if (!yEl) continue;

                    // Set min/max to Buddhist range if set
                    if (fp.config.minDate) {
                        yEl.min = (fp.config.minDate.getFullYear() + 543).toString();
                    } else {
                        yEl.removeAttribute('min');
                    }
                    if (fp.config.maxDate) {
                        yEl.max = (fp.config.maxDate.getFullYear() + 543).toString();
                    } else {
                        yEl.removeAttribute('max');
                    }

                    // Directly update the input value
                    yEl.value = beYear.toString();
                }
            }

            // Ensure altInput displays the correct Thai date
            function syncAltInputValue() {
                if (fp.altInput) {
                    var dateToUse = (fp.selectedDates && fp.selectedDates.length > 0)
                        ? fp.selectedDates[0]
                        : (fp.input && fp.input.value ? parseThaiDate(fp.input.value) : null);

                    if (dateToUse) {
                        fp.altInput.value = formatThaiDate(dateToUse, fp.config.altFormat);
                    }

                    // Strict failsafe: if 'B' character ever remains in altInput, replace it with current Buddhist year
                    if (fp.altInput.value && fp.altInput.value.indexOf('B') !== -1) {
                        var beYear = (fp.currentYear ? fp.currentYear : new Date().getFullYear()) + 543;
                        fp.altInput.value = fp.altInput.value.replace(/\bB\b/g, beYear.toString()).replace(/\s+B$/, ' ' + beYear);
                    }
                }
            }

            // Setup custom year handling to intercept Flatpickr's year navigation
            function setupYearElements() {
                if (!fp.yearElements || !fp.yearElements.length) return;

                for (var i = 0; i < fp.yearElements.length; i++) {
                    var yEl = fp.yearElements[i];
                    if (!yEl || yEl.__buddhistInitialized) continue;
                    yEl.__buddhistInitialized = true;

                    // Intercept the .value property setter so if flatpickr's updateNavigationCurrentMonth
                    // writes Gregorian year (e.g. 2026), it is converted to Buddhist year (2569) instantly!
                    var protoDesc = Object.getOwnPropertyDescriptor(HTMLInputElement.prototype, 'value');
                    if (protoDesc) {
                        (function (element) {
                            Object.defineProperty(element, 'value', {
                                get: function () {
                                    return protoDesc.get.call(this);
                                },
                                set: function (val) {
                                    var num = parseInt(val, 10);
                                    if (!isNaN(num) && num > 1000 && num < 2400) {
                                        val = (num + 543).toString();
                                    }
                                    protoDesc.set.call(this, val);
                                },
                                configurable: true
                            });
                        })(yEl);
                    }

                    // Intercept "increment" event triggered by arrowUp / arrowDown buttons
                    yEl.addEventListener('increment', function (e) {
                        e.stopImmediatePropagation();
                        e.preventDefault();
                        var delta = e.delta || 0;
                        fp.changeYear(fp.currentYear + delta);
                        syncYearDisplay();
                    }, true);

                    // Intercept manual keyboard typing in the year box
                    yEl.addEventListener('change', function (e) {
                        e.stopImmediatePropagation();
                        var val = parseInt(this.value, 10);
                        if (!isNaN(val)) {
                            if (val > 2400) val -= 543;
                            if (val >= 1000 && val <= 9999 && val !== fp.currentYear) {
                                fp.changeYear(val);
                                syncYearDisplay();
                            }
                        }
                    }, true);

                    yEl.addEventListener('blur', function () {
                        syncYearDisplay();
                    });
                }

                syncYearDisplay();
            }

            return {
                onReady: function () {
                    // Replace instance methods
                    fp.formatDate = function (dateObj, frmt, overrideLocale) {
                        return formatThaiDate(dateObj, frmt || fp.config.dateFormat, overrideLocale || fp.l10n);
                    };
                    fp.parseDate = function (dateStr, format) {
                        return parseThaiDate(dateStr, format);
                    };

                    if (fp.calendarContainer) {
                        fp.calendarContainer.classList.add('buddhist-calendar-flatpickr');
                    }

                    setupYearElements();
                    syncYearDisplay();
                    syncAltInputValue();
                },
                onMonthChange: function () {
                    syncYearDisplay();
                    setTimeout(syncYearDisplay, 0);
                },
                onYearChange: function () {
                    syncYearDisplay();
                    setTimeout(syncYearDisplay, 0);
                },
                onOpen: function () {
                    setupYearElements();
                    syncYearDisplay();
                },
                onValueUpdate: function () {
                    syncAltInputValue();
                    syncYearDisplay();
                }
            };
        };
    }

    /**
     * Global helper to initialize Thai Buddhist datepicker on inputs
     */
    function initThaiDatePicker(selector, options) {
        options = options || {};

        var customFormatDate = function (dateObj, frmt, locale) {
            return formatThaiDate(dateObj, frmt, locale);
        };
        var customParseDate = function (dateStr, format) {
            return parseThaiDate(dateStr, format);
        };

        var config = Object.assign({
            locale: 'th',
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'j F B', // e.g. 13 ตุลาคม 2569
            altInputClass: 'form-control flatpickr-thai-input',
            formatDate: customFormatDate,
            parseDate: customParseDate,
            disableMobile: true
        }, options);

        // Ensure Buddhist plugin is attached
        var plugin = thaiBuddhistYearDisplayPlugin();
        if (options.plugins && Array.isArray(options.plugins)) {
            config.plugins = [plugin].concat(options.plugins);
        } else {
            config.plugins = [plugin];
        }

        // Initialize flatpickr
        var fp = flatpickr(selector, config);

        // If selector is for multiple elements, flatpickr returns an array
        var instances = Array.isArray(fp) ? fp : [fp];
        instances.forEach(function (inst) {
            if (inst && inst.element) {
                // If the input is inside an input-group with a calendar icon, make clicking the icon open flatpickr
                var parentGroup = inst.element.closest('.input-group');
                if (parentGroup) {
                    var iconAddon = parentGroup.querySelector('.input-group-text');
                    if (iconAddon) {
                        iconAddon.style.cursor = 'pointer';
                        iconAddon.addEventListener('click', function () {
                            inst.open();
                        });
                    }
                }

                // Force initial altInput value check to ensure it never shows 'B'
                if (inst.altInput) {
                    var d = (inst.selectedDates && inst.selectedDates.length > 0)
                        ? inst.selectedDates[0]
                        : (inst.input && inst.input.value ? parseThaiDate(inst.input.value) : null);
                    if (d) {
                        inst.altInput.value = formatThaiDate(d, inst.config.altFormat);
                    }
                    if (inst.altInput.value && inst.altInput.value.indexOf('B') !== -1) {
                        var be = (inst.currentYear ? inst.currentYear : new Date().getFullYear()) + 543;
                        inst.altInput.value = inst.altInput.value.replace(/\bB\b/g, be.toString()).replace(/\s+B$/, ' ' + be);
                    }
                }
            }
        });

        return fp;
    }

    // Exports
    root.thaiBuddhistYearDisplayPlugin = thaiBuddhistYearDisplayPlugin;
    root.initThaiDatePicker = initThaiDatePicker;
    root.formatThaiDate = formatThaiDate;
    root.parseThaiDate = parseThaiDate;

    return thaiBuddhistYearDisplayPlugin;
})));
