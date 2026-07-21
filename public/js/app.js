/**
 * Wave Club Management System - Client-side JavaScript
 * Complete self-contained file with Jalali Datepicker, form handling, and utilities.
 */

// ============================================================
// 1. JALALI (SOLAR HIJRI) DATE UTILITIES
// ============================================================

var PERSIAN_MONTHS = [
    'فروردین','اردیبهشت','خرداد','تیر','مرداد','شهریور',
    'مهر','آبان','آذر','دی','بهمن','اسفند'
];

var PERSIAN_WEEKDAYS_SHORT = ['ش','ی','د','س','چ','پ','ج'];

var PERSIAN_NUMBERS = ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'];

/**
 * Convert digits in a string to Persian numerals
 */
function toPersianNum(num) {
    return String(num).replace(/\d/g, function (d) {
        return PERSIAN_NUMBERS[d];
    });
}

/**
 * Convert Gregorian date to Jalali
 */
function toJalali(gy, gm, gd) {
    var g_d_m = [0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334];
    var jy = (gy <= 1600) ? 0 : 979;
    gy -= (gy <= 1600) ? 621 : 1600;
    var gy2 = (gm > 2) ? (gy + 1) : gy;
    var days = 365 * gy + Math.floor((gy2 + 3) / 4) - Math.floor((gy2 + 99) / 100)
        + Math.floor((gy2 + 399) / 400) - 80 + gd + g_d_m[gm - 1];
    jy += 33 * Math.floor(days / 12053);
    days %= 12053;
    jy += 4 * Math.floor(days / 1461);
    days %= 1461;
    if (days > 365) {
        jy += Math.floor((days - 1) / 365);
        days = (days - 1) % 365;
    }
    var jm = (days < 186) ? 1 + Math.floor(days / 31) : 7 + Math.floor((days - 186) / 30);
    var jd = 1 + ((days < 186) ? (days % 31) : ((days - 186) % 30));
    return { jy: jy, jm: jm, jd: jd };
}

/**
 * Convert Jalali date to Gregorian
 */
function toGregorian(jy, jm, jd) {
    jy += 1595;
    var days = -355668 + 365 * jy + (Math.floor(jy / 33)) * 8
        + Math.floor((jy % 33 + 3) / 4) + jd
        + ((jm < 7) ? (jm - 1) * 31 : ((jm - 7) * 30 + 186));
    var gy = 400 * Math.floor(days / 146097);
    days %= 146097;
    if (days > 36524) {
        gy += 100 * Math.floor(--days / 36524);
        days %= 36524;
        if (days >= 365) days++;
    }
    gy += 4 * Math.floor(days / 1461);
    days %= 1461;
    if (days > 365) {
        gy += Math.floor((days - 1) / 365);
        days = (days - 1) % 365;
    }
    var gd = days + 1;
    var sal_a = [0, 31, ((gy % 4 === 0 && gy % 100 !== 0) || (gy % 400 === 0)) ? 29 : 28,
        31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
    var gm = 0;
    for (var i = 0; i < 13 && gd > sal_a[i]; i++) {
        gd -= sal_a[i];
        gm++;
    }
    return { gy: gy, gm: gm, gd: gd };
}

/**
 * Check if a Jalali year is a leap year
 */
function isJalaliLeap(jy) {
    return [1, 5, 9, 13, 17, 22, 26, 30].indexOf(jy % 33) !== -1;
}

/**
 * Get the number of days in a Jalali month
 */
function jalaliMonthDays(jy, jm) {
    if (jm <= 6) return 31;
    if (jm <= 11) return 30;
    return isJalaliLeap(jy) ? 30 : 29;
}

/**
 * Format Jalali date as YYYY/MM/DD with Persian numerals
 */
function formatJalali(jy, jm, jd) {
    var pad = function (n) { return n < 10 ? '0' + n : '' + n; };
    return toPersianNum(pad(jy) + '/' + pad(jm) + '/' + pad(jd));
}

/**
 * Get today's date in Jalali
 */
function todayJalali() {
    var now = new Date();
    return toJalali(now.getFullYear(), now.getMonth() + 1, now.getDate());
}

/**
 * Parse a Jalali date string (with Persian or Latin numerals) and return {jy, jm, jd}
 */
function persianDate(str) {
    if (!str) return null;
    // Convert Persian numerals back to Latin for parsing
    var normalized = str.replace(/[۰-۹]/g, function (d) {
        return PERSIAN_NUMBERS.indexOf(d);
    });
    var parts = normalized.split(/[\/\-]/);
    if (parts.length !== 3) return null;
    var jy = parseInt(parts[0], 10);
    var jm = parseInt(parts[1], 10);
    var jd = parseInt(parts[2], 10);
    if (isNaN(jy) || isNaN(jm) || isNaN(jd)) return null;
    return { jy: jy, jm: jm, jd: jd };
}

/**
 * Convert a Jalali date string to a Gregorian Date object
 */
function persianToGregorian(jalaliStr) {
    var d = persianDate(jalaliStr);
    if (!d) return null;
    var g = toGregorian(d.jy, d.jm, d.jd);
    return new Date(g.gy, g.gm - 1, g.gd);
}

// ============================================================
// 2. JALALI DATEPICKER CLASS
// ============================================================

var _activeDatepicker = null;

function JalaliDatepicker(input) {
    this.input = input;
    this.today = todayJalali();
    this.currentYear = this.today.jy;
    this.currentMonth = this.today.jm;

    // Read initial value from input
    if (input.value) {
        var parsed = persianDate(input.value);
        if (parsed) {
            this.currentYear = parsed.jy;
            this.currentMonth = parsed.jm;
        }
    }

    // Read min/max constraints
    this.minDate = null;
    this.maxDate = null;
    if (input.hasAttribute('data-jalali-min')) {
        this.minDate = persianDate(input.getAttribute('data-jalali-min'));
    }
    if (input.hasAttribute('data-jalali-max')) {
        this.maxDate = persianDate(input.getAttribute('data-jalali-max'));
    }

    this.popup = null;
    this._boundClickOutside = this._handleClickOutside.bind(this);
    this._boundKeydown = this._handleKeydown.bind(this);
    this._create();
    this._bindEvents();
}

JalaliDatepicker.prototype._create = function () {
    var self = this;

    this.popup = document.createElement('div');
    this.popup.className = 'jalali-datepicker';
    this.popup.style.display = 'none';
    this.popup.style.position = 'absolute';
    this.popup.style.zIndex = '10000';
    this.popup.style.background = '#fff';
    this.popup.style.border = '1px solid #d1d5db';
    this.popup.style.borderRadius = '8px';
    this.popup.style.boxShadow = '0 10px 25px rgba(0,0,0,0.15)';
    this.popup.style.fontFamily = 'Tahoma, Arial, sans-serif';
    this.popup.style.direction = 'rtl';
    this.popup.style.userSelect = 'none';
    this.popup.style.width = '300px';
    this.popup.style.padding = '0';
    this.popup.style.boxSizing = 'border-box';

    this._render();
    document.body.appendChild(this.popup);
};

JalaliDatepicker.prototype._render = function () {
    var self = this;
    this.popup.innerHTML = '';

    // --- Header: navigation ---
    var header = document.createElement('div');
    header.style.cssText = 'display:flex;align-items:center;justify-content:space-between;padding:10px 12px;background:#f9fafb;border-bottom:1px solid #e5e7eb;border-radius:8px 8px 0 0;';

    var prevBtn = document.createElement('button');
    prevBtn.type = 'button';
    prevBtn.innerHTML = '&#9664;';
    prevBtn.style.cssText = 'background:none;border:none;font-size:16px;cursor:pointer;padding:4px 8px;color:#374151;border-radius:4px;';
    prevBtn.addEventListener('click', function () { self._changeMonth(-1); });
    prevBtn.addEventListener('mouseenter', function () { prevBtn.style.background = '#e5e7eb'; });
    prevBtn.addEventListener('mouseleave', function () { prevBtn.style.background = 'none'; });

    var nextBtn = document.createElement('button');
    nextBtn.type = 'button';
    nextBtn.innerHTML = '&#9654;';
    nextBtn.style.cssText = prevBtn.style.cssText;
    nextBtn.addEventListener('click', function () { self._changeMonth(1); });
    nextBtn.addEventListener('mouseenter', function () { nextBtn.style.background = '#e5e7eb'; });
    nextBtn.addEventListener('mouseleave', function () { nextBtn.style.background = 'none'; });

    var label = document.createElement('span');
    label.style.cssText = 'font-size:14px;font-weight:600;color:#111827;';
    label.textContent = PERSIAN_MONTHS[this.currentMonth - 1] + ' ' + toPersianNum(this.currentYear);

    header.appendChild(prevBtn);
    header.appendChild(label);
    header.appendChild(nextBtn);
    this.popup.appendChild(header);

    // --- Weekday headers ---
    var weekRow = document.createElement('div');
    weekRow.style.cssText = 'display:grid;grid-template-columns:repeat(7,1fr);padding:8px 8px 4px 8px;';
    for (var w = 0; w < 7; w++) {
        var wd = document.createElement('div');
        wd.style.cssText = 'text-align:center;font-size:12px;font-weight:600;color:#6b7280;';
        wd.textContent = PERSIAN_WEEKDAYS_SHORT[w];
        weekRow.appendChild(wd);
    }
    this.popup.appendChild(weekRow);

    // --- Day grid ---
    var grid = document.createElement('div');
    grid.style.cssText = 'display:grid;grid-template-columns:repeat(7,1fr);padding:4px 8px 8px 8px;gap:2px;';

    var firstDayMonth = new Date(toGregorian(this.currentYear, this.currentMonth, 1).gy,
        toGregorian(this.currentYear, this.currentMonth, 1).gm - 1,
        toGregorian(this.currentYear, this.currentMonth, 1).gd).getDay();
    // Convert Gregorian day-of-week (0=Sun) to Jalali start (Saturday=0)
    // Saturday=6, Sunday=0, Monday=1, ..., Friday=5
    // Jalali week: ش Sat=0, ی Sun=1, د Mon=2, س Tue=3, چ Wed=4, پ Thu=5, ج Fri=6
    var jalaliFirstDow = (firstDayMonth + 1) % 7;

    var daysInMonth = jalaliMonthDays(this.currentYear, this.currentMonth);
    var prevMonthDays = jalaliMonthDays(
        this.currentMonth === 1 ? this.currentYear - 1 : this.currentYear,
        this.currentMonth === 1 ? 12 : this.currentMonth - 1
    );

    // Previous month trailing days
    for (var p = jalaliFirstDow - 1; p >= 0; p--) {
        var pDay = prevMonthDays - p;
        grid.appendChild(this._createDayCell(pDay, true, false, false, false));
    }

    // Current month days
    var selected = persianDate(this.input.value);
    for (var d = 1; d <= daysInMonth; d++) {
        var isToday = (d === this.today.jd && this.currentMonth === this.today.jm && this.currentYear === this.today.jy);
        var isSelected = selected && (d === selected.jd && this.currentMonth === selected.jm && this.currentYear === selected.jy);
        var isDisabled = this._isDayDisabled(this.currentYear, this.currentMonth, d);
        grid.appendChild(this._createDayCell(d, false, isToday, isSelected, isDisabled));
    }

    // Next month leading days
    var totalCells = jalaliFirstDow + daysInMonth;
    var remaining = totalCells % 7 === 0 ? 0 : 7 - (totalCells % 7);
    for (var n = 1; n <= remaining; n++) {
        grid.appendChild(this._createDayCell(n, true, false, false, false));
    }

    this.popup.appendChild(grid);

    // --- Footer buttons ---
    var footer = document.createElement('div');
    footer.style.cssText = 'display:flex;justify-content:space-between;padding:8px 12px;border-top:1px solid #e5e7eb;';

    var todayBtn = document.createElement('button');
    todayBtn.type = 'button';
    todayBtn.textContent = 'امروز';
    todayBtn.style.cssText = 'background:#10b981;color:#fff;border:none;padding:6px 16px;border-radius:6px;font-size:13px;cursor:pointer;font-family:inherit;';
    todayBtn.addEventListener('click', function () { self._selectToday(); });
    todayBtn.addEventListener('mouseenter', function () { todayBtn.style.background = '#059669'; });
    todayBtn.addEventListener('mouseleave', function () { todayBtn.style.background = '#10b981'; });

    var clearBtn = document.createElement('button');
    clearBtn.type = 'button';
    clearBtn.textContent = 'پاک کردن';
    clearBtn.style.cssText = 'background:#ef4444;color:#fff;border:none;padding:6px 16px;border-radius:6px;font-size:13px;cursor:pointer;font-family:inherit;';
    clearBtn.addEventListener('click', function () { self._clear(); });
    clearBtn.addEventListener('mouseenter', function () { clearBtn.style.background = '#dc2626'; });
    clearBtn.addEventListener('mouseleave', function () { clearBtn.style.background = '#ef4444'; });

    footer.appendChild(todayBtn);
    footer.appendChild(clearBtn);
    this.popup.appendChild(footer);
};

JalaliDatepicker.prototype._createDayCell = function (day, isOther, isToday, isSelected, isDisabled) {
    var self = this;
    var cell = document.createElement('div');
    cell.textContent = toPersianNum(day);

    var baseStyle = 'text-align:center;padding:7px 0;font-size:13px;border-radius:6px;cursor:pointer;transition:background 0.15s,color 0.15s;';

    if (isOther) {
        cell.className = 'dp-day other-month';
        cell.style.cssText = baseStyle + 'color:#d1d5db;cursor:default;';
        return cell;
    }

    if (isDisabled) {
        cell.className = 'dp-day disabled';
        cell.style.cssText = baseStyle + 'color:#d1d5db;cursor:not-allowed;text-decoration:line-through;';
        return cell;
    }

    cell.className = 'dp-day';
    if (isToday) cell.classList.add('today');
    if (isSelected) cell.classList.add('selected');

    if (isSelected) {
        cell.style.cssText = baseStyle + 'background:#10b981;color:#fff;font-weight:600;';
    } else if (isToday) {
        cell.style.cssText = baseStyle + 'background:#ecfdf5;color:#059669;font-weight:600;border:1px solid #6ee7b7;';
    } else {
        cell.style.cssText = baseStyle + 'color:#374151;';
    }

    cell.addEventListener('mouseenter', function () {
        if (!isSelected) {
            cell.style.background = '#f3f4f6';
        }
    });
    cell.addEventListener('mouseleave', function () {
        if (isToday && !isSelected) {
            cell.style.background = '#ecfdf5';
        } else if (!isSelected) {
            cell.style.background = '';
        }
    });

    (function (dayVal) {
        cell.addEventListener('click', function () {
            self._selectDay(self.currentYear, self.currentMonth, dayVal);
        });
    })(day);

    return cell;
};

JalaliDatepicker.prototype._isDayDisabled = function (jy, jm, jd) {
    if (this.minDate) {
        if (jy < this.minDate.jy) return true;
        if (jy === this.minDate.jy && jm < this.minDate.jm) return true;
        if (jy === this.minDate.jy && jm === this.minDate.jm && jd < this.minDate.jd) return true;
    }
    if (this.maxDate) {
        if (jy > this.maxDate.jy) return true;
        if (jy === this.maxDate.jy && jm > this.maxDate.jm) return true;
        if (jy === this.maxDate.jy && jm === this.maxDate.jm && jd > this.maxDate.jd) return true;
    }
    return false;
};

JalaliDatepicker.prototype._selectDay = function (jy, jm, jd) {
    this.input.value = formatJalali(jy, jm, jd);
    this.input.dispatchEvent(new Event('change', { bubbles: true }));
    this.input.dispatchEvent(new Event('input', { bubbles: true }));
    this.close();
};

JalaliDatepicker.prototype._selectToday = function () {
    this.currentYear = this.today.jy;
    this.currentMonth = this.today.jm;
    this.input.value = formatJalali(this.today.jy, this.today.jm, this.today.jd);
    this.input.dispatchEvent(new Event('change', { bubbles: true }));
    this.input.dispatchEvent(new Event('input', { bubbles: true }));
    this.close();
};

JalaliDatepicker.prototype._clear = function () {
    this.input.value = '';
    this.input.dispatchEvent(new Event('change', { bubbles: true }));
    this.input.dispatchEvent(new Event('input', { bubbles: true }));
    this.close();
};

JalaliDatepicker.prototype._changeMonth = function (dir) {
    this.currentMonth += dir;
    if (this.currentMonth > 12) {
        this.currentMonth = 1;
        this.currentYear++;
    } else if (this.currentMonth < 1) {
        this.currentMonth = 12;
        this.currentYear--;
    }
    this._render();
};

JalaliDatepicker.prototype.open = function () {
    // Close any other open datepicker
    if (_activeDatepicker && _activeDatepicker !== this) {
        _activeDatepicker.close();
    }
    _activeDatepicker = this;

    // Update to selected date or today
    if (this.input.value) {
        var parsed = persianDate(this.input.value);
        if (parsed) {
            this.currentYear = parsed.jy;
            this.currentMonth = parsed.jm;
        }
    } else {
        this.currentYear = this.today.jy;
        this.currentMonth = this.today.jm;
    }

    this._render();

    // Position popup below the input
    var rect = this.input.getBoundingClientRect();
    var popupHeight = 320;
    var spaceBelow = window.innerHeight - rect.bottom;
    var spaceAbove = rect.top;

    this.popup.style.display = 'block';

    if (spaceBelow < popupHeight && spaceAbove > spaceBelow) {
        this.popup.style.top = (rect.top - this.popup.offsetHeight - 4) + 'px';
    } else {
        this.popup.style.top = (rect.bottom + 4) + 'px';
    }

    // Horizontal: align right edge (RTL), prevent overflow
    var leftPos = rect.right - 300;
    if (leftPos < 8) leftPos = 8;
    if (rect.right > window.innerWidth - 8) {
        leftPos = window.innerWidth - 308;
    }
    this.popup.style.left = leftPos + 'px';

    document.addEventListener('click', this._boundClickOutside);
    document.addEventListener('keydown', this._boundKeydown);
};

JalaliDatepicker.prototype.close = function () {
    this.popup.style.display = 'none';
    if (_activeDatepicker === this) {
        _activeDatepicker = null;
    }
    document.removeEventListener('click', this._boundClickOutside);
    document.removeEventListener('keydown', this._boundKeydown);
};

JalaliDatepicker.prototype._handleClickOutside = function (e) {
    if (!this.popup.contains(e.target) && e.target !== this.input) {
        this.close();
    }
};

JalaliDatepicker.prototype._handleKeydown = function (e) {
    if (e.key === 'Escape') {
        this.close();
        this.input.blur();
    }
};

JalaliDatepicker.prototype._bindEvents = function () {
    var self = this;
    this.input.addEventListener('focus', function () { self.open(); });
    this.input.addEventListener('click', function () { self.open(); });
    // Prevent the native date picker on mobile
    this.input.setAttribute('readonly', 'true');
};

// ============================================================
// 3. DOM READY - INITIALIZE EVERYTHING
// ============================================================

document.addEventListener('DOMContentLoaded', function () {

    // === Auto-initialize Flatpickr Jalali Datepickers ===
    var jalaliInputs = document.querySelectorAll('[data-jalali], .jalali-date[data-datepicker], [data-datepicker].jalali-date, input[type="text"][data-date-format], .flatpickr-date');
    jalaliInputs.forEach(function (input) {
        // Skip if already initialized
        if (input._flatpickr) return;
        
        flatpickr(input, {
            locale: 'fa',
            dateFormat: 'Y/m/d',
            allowInput: true,
            static: true
        });
    });

    // === Flash Message Auto-dismiss ===
    var alerts = document.querySelectorAll('.alert');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            alert.style.transition = 'opacity 0.4s, transform 0.4s';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(function () {
                if (alert.parentNode) {
                    alert.parentNode.removeChild(alert);
                }
            }, 400);
        }, 5000);
    });

    // === Form Validation (data-validate) ===
    var forms = document.querySelectorAll('form[data-validate]');
    forms.forEach(function (form) {
        form.addEventListener('submit', function (e) {
            var requiredFields = form.querySelectorAll('[required]');
            var valid = true;
            var firstInvalid = null;

            // Clear previous errors
            form.querySelectorAll('.form-error').forEach(function (err) {
                err.textContent = '';
            });

            requiredFields.forEach(function (field) {
                var value = field.value.trim();
                if (!value) {
                    valid = false;
                    if (!firstInvalid) firstInvalid = field;

                    // Add error class
                    field.classList.add('error');

                    // Show error message
                    var errorEl = field.parentNode.querySelector('.form-error');
                    if (!errorEl) {
                        errorEl = document.createElement('div');
                        errorEl.className = 'form-error';
                        field.parentNode.appendChild(errorEl);
                    }
                    errorEl.textContent = 'این فیلد الزامی است.';
                } else {
                    field.classList.remove('error');
                }
            });

            if (!valid) {
                e.preventDefault();
                if (firstInvalid) {
                    firstInvalid.focus();
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });

        // Remove .error class on input event
        form.querySelectorAll('[required]').forEach(function (field) {
            field.addEventListener('input', function () {
                field.classList.remove('error');
                var errorEl = field.parentNode.querySelector('.form-error');
                if (errorEl) errorEl.textContent = '';
            });
        });
    });

    // === Confirm Dialogs (data-confirm) ===
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('[data-confirm]');
        if (btn) {
            var message = btn.getAttribute('data-confirm') || 'آیا مطمئن هستید؟';
            if (!confirm(message)) {
                e.preventDefault();
                e.stopImmediatePropagation();
            }
        }
    });

    // === Mobile Sidebar Toggle ===
    var sidebarToggle = document.getElementById('sidebar-toggle');
    var sidebar = document.getElementById('sidebar');
    var sidebarOverlay = document.getElementById('sidebar-overlay');

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function () {
            sidebar.classList.toggle('open');
            if (sidebarOverlay) {
                sidebarOverlay.classList.toggle('active');
            }
        });
    }

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function () {
            if (sidebar) sidebar.classList.remove('open');
            sidebarOverlay.classList.remove('active');
        });
    }

    // Close mobile sidebar when clicking a nav link
    if (sidebar) {
        document.querySelectorAll('.sidebar-nav a, .sidebar-footer-link').forEach(function (link) {
            link.addEventListener('click', function () {
                if (window.innerWidth < 1024 && sidebar.classList.contains('open')) {
                    sidebar.classList.remove('open');
                    if (sidebarOverlay) sidebarOverlay.classList.remove('active');
                }
            });
        });
    }

    // === AJAX Form Handling (data-ajax) ===
    var ajaxForms = document.querySelectorAll('form[data-ajax]');
    ajaxForms.forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            var formData = new FormData(form);
            var action = form.getAttribute('action');
            var targetSelector = form.getAttribute('data-ajax-target');

            // Basic validation for textareas
            var textarea = form.querySelector('textarea[name="message"]') || form.querySelector('textarea[name="body"]');
            if (textarea && !textarea.value.trim()) {
                alert('لطفاً پیام خود را وارد کنید.');
                textarea.focus();
                return;
            }

            fetch(action, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(function (response) {
                if (response.redirected) {
                    window.location.href = response.url;
                } else {
                    return response.text();
                }
            })
            .then(function (html) {
                if (html !== undefined) {
                    if (targetSelector) {
                        var target = document.querySelector(targetSelector);
                        if (target) {
                            target.innerHTML = html;
                        } else {
                            window.location.reload();
                        }
                    } else {
                        window.location.reload();
                    }
                    // Clear form after success
                    form.reset();
                }
            })
            .catch(function () {
                alert('خطا در ارسال اطلاعات. لطفاً دوباره تلاش کنید.');
            });
        });
    });

    // === Active Sidebar Link Highlight ===
    var currentPath = window.location.pathname;
    document.querySelectorAll('.sidebar-nav a, .portal-nav a').forEach(function (link) {
        var href = link.getAttribute('href');
        if (href && currentPath.indexOf(href.replace(/^\//, '')) !== -1) {
            link.classList.add('active');
        }
    });

    // === Checkbox Toggle (data-toggle="checkbox") ===
    document.querySelectorAll('[data-toggle="checkbox"]').forEach(function (el) {
        el.addEventListener('click', function () {
            var targetId = el.getAttribute('data-target');
            if (!targetId) return;
            var checkbox = document.querySelector(targetId);
            if (checkbox) {
                checkbox.checked = !checkbox.checked;
                checkbox.dispatchEvent(new Event('change', { bubbles: true }));
            }
        });
    });

    // === Select All Checkbox (data-select-all) ===
    document.querySelectorAll('[data-select-all]').forEach(function (masterCheckbox) {
        masterCheckbox.addEventListener('change', function () {
            var table = masterCheckbox.closest('table');
            if (!table) return;
            var checkboxes = table.querySelectorAll('input[type="checkbox"]');
            checkboxes.forEach(function (cb) {
                if (cb !== masterCheckbox) {
                    cb.checked = masterCheckbox.checked;
                }
            });
        });
    });

    // === Number Input Formatting - Currency (data-format="currency") ===
    document.querySelectorAll('[data-format="currency"]').forEach(function (input) {
        // Format on input
        input.addEventListener('input', function () {
            var raw = input.value.replace(/[^0-9]/g, '');
            if (raw === '') {
                input.value = '';
                input.removeAttribute('data-raw-value');
                return;
            }
            // Store raw value
            input.setAttribute('data-raw-value', raw);
            // Format with Persian comma separators
            var formatted = '';
            var count = 0;
            for (var i = raw.length - 1; i >= 0; i--) {
                if (count > 0 && count % 3 === 0) {
                    formatted = '٬' + formatted; // Persian-style comma
                }
                formatted = raw[i] + formatted;
                count++;
            }
            input.value = toPersianNum(formatted);
        });

        // Format on focus out
        input.addEventListener('blur', function () {
            var raw = input.value.replace(/[^0-9]/g, '');
            if (raw === '') {
                input.value = '';
                return;
            }
            var formatted = '';
            var count = 0;
            for (var i = raw.length - 1; i >= 0; i--) {
                if (count > 0 && count % 3 === 0) {
                    formatted = '٬' + formatted;
                }
                formatted = raw[i] + formatted;
                count++;
            }
            input.value = toPersianNum(formatted);
        });

        // Show raw number on focus
        input.addEventListener('focus', function () {
            var raw = input.getAttribute('data-raw-value');
            if (raw) {
                input.value = toPersianNum(raw);
            }
        });
    });

    // === Smooth Scroll for Anchor Links ===
    document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
        anchor.addEventListener('click', function (e) {
            var targetId = anchor.getAttribute('href');
            if (targetId && targetId.length > 1) {
                var target = document.querySelector(targetId);
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
        });
    });

});

// ============================================================
// GLOBAL UTILITIES
// ============================================================

function togglePass(inputId, btn) {
    var input = document.getElementById(inputId);
    if (!input) return;
    var icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fas fa-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'fas fa-eye';
    }
}

function confirmDelete(msg) {
    return confirm(msg || 'آیا از حذف این مورد اطمینان دارید؟');
}