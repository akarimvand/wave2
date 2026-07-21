<!-- Multi-Step Registration Form -->
<style>
    /* Step Indicator */
    .reg-steps {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0;
        margin-bottom: 1.75rem;
        padding: 0 0.5rem;
    }
    .reg-step-item {
        display: flex;
        align-items: center;
        gap: 8px;
        position: relative;
    }
    .reg-step-circle {
        width: 36px; height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        font-weight: 700;
        border: 2px solid #d1d5db;
        color: #9ca3af;
        background: #f9fafb;
        transition: all 0.35s ease;
        flex-shrink: 0;
    }
    .reg-step-item.active .reg-step-circle {
        border-color: #1877F2;
        background: linear-gradient(135deg, #1877F2, #0A3178);
        color: #fff;
        box-shadow: 0 4px 12px rgba(24, 119, 242, 0.35);
    }
    .reg-step-item.done .reg-step-circle {
        border-color: #10b981;
        background: #10b981;
        color: #fff;
    }
    .reg-step-label {
        font-size: 0.78rem;
        font-weight: 600;
        color: #9ca3af;
        white-space: nowrap;
        transition: color 0.3s ease;
    }
    .reg-step-item.active .reg-step-label {
        color: #1f2937;
    }
    .reg-step-item.done .reg-step-label {
        color: #10b981;
    }
    .reg-step-connector {
        width: 40px;
        height: 2px;
        background: #e5e7eb;
        margin: 0 8px;
        border-radius: 1px;
        transition: background 0.3s ease;
        flex-shrink: 0;
    }
    .reg-step-connector.done {
        background: #10b981;
    }

    /* Step Panels */
    .reg-step-panel {
        display: none;
        animation: regStepFadeIn 0.35s ease forwards;
    }
    .reg-step-panel.active {
        display: block;
    }
    @keyframes regStepFadeIn {
        from { opacity: 0; transform: translateX(-12px); }
        to   { opacity: 1; transform: translateX(0); }
    }

    /* Step Title */
    .reg-step-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .reg-step-title i {
        color: #1877F2;
        font-size: 1rem;
    }

    /* Form textarea in auth context */
    .reg-textarea {
        width: 100%;
        padding: 0.7rem 0.85rem;
        border: 1.5px solid #e5e7eb;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.95);
        color: #1f2937;
        font-family: 'Vazirmatn', 'Vazir', 'Segoe UI', Tahoma, Arial, sans-serif;
        font-size: 0.85rem;
        direction: rtl;
        transition: all 0.25s ease;
        outline: none;
        resize: vertical;
        min-height: 70px;
    }
    .reg-textarea::placeholder {
        color: #9ca3af;
        font-size: 0.82rem;
    }
    .reg-textarea:focus {
        border-color: #1877F2;
        box-shadow: 0 0 0 3px rgba(24, 119, 242, 0.15);
        background: #fff;
    }

    /* File upload area */
    .reg-file-upload {
        border: 2px dashed #d1d5db;
        border-radius: 12px;
        padding: 1.25rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.25s ease;
        background: rgba(249, 250, 251, 0.8);
        position: relative;
    }
    .reg-file-upload:hover {
        border-color: #1877F2;
        background: rgba(24, 119, 242, 0.03);
    }
    .reg-file-upload.has-file {
        border-color: #10b981;
        background: rgba(16, 185, 129, 0.05);
    }
    .reg-file-upload i {
        font-size: 1.75rem;
        color: #9ca3af;
        margin-bottom: 0.5rem;
        display: block;
    }
    .reg-file-upload.has-file i { color: #10b981; }
    .reg-file-upload p {
        font-size: 0.82rem;
        color: #6b7280;
        margin: 0;
    }
    .reg-file-upload .file-name {
        font-size: 0.82rem;
        color: #10b981;
        font-weight: 600;
        margin-top: 0.35rem;
    }
    .reg-file-upload input[type="file"] {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
    }

    /* Navigation buttons */
    .reg-nav-btns {
        display: flex;
        gap: 10px;
        margin-top: 1.5rem;
    }
    .reg-btn-prev {
        flex: 1;
        padding: 0.7rem 1rem;
        border: 1.5px solid #d1d5db;
        border-radius: 12px;
        background: #fff;
        color: #374151;
        font-family: 'Vazirmatn', 'Vazir', 'Segoe UI', Tahoma, Arial, sans-serif;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.25s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }
    .reg-btn-prev:hover {
        background: #f3f4f6;
        border-color: #9ca3af;
    }
    .reg-btn-next {
        flex: 1;
        padding: 0.7rem 1rem;
        border: none;
        border-radius: 12px;
        background: linear-gradient(135deg, #1877F2, #0A3178);
        color: #fff;
        font-family: 'Vazirmatn', 'Vazir', 'Segoe UI', Tahoma, Arial, sans-serif;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.25s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        box-shadow: 0 4px 12px rgba(24, 119, 242, 0.3);
    }
    .reg-btn-next:hover {
        background: linear-gradient(135deg, #4293FF, #1877F2);
        box-shadow: 0 6px 18px rgba(24, 119, 242, 0.4);
        transform: translateY(-1px);
    }

    /* Responsive: smaller step labels on mobile */
    @media (max-width: 480px) {
        .reg-step-label { font-size: 0.7rem; }
        .reg-step-connector { width: 24px; margin: 0 5px; }
        .reg-step-circle { width: 32px; height: 32px; font-size: 0.8rem; }
    }
</style>

<link rel="stylesheet" href="<?php echo asset('css/flatpickr.min.css'); ?>">

<form method="POST" action="<?php echo url('auth/register'); ?>" id="registerForm" novalidate enctype="multipart/form-data">
    <?php echo csrf_field(); ?>

    <!-- Step Indicators -->
    <div class="reg-steps">
        <div class="reg-step-item active" data-step="1">
            <div class="reg-step-circle">۱</div>
            <span class="reg-step-label">اطلاعات پایه</span>
        </div>
        <div class="reg-step-connector"></div>
        <div class="reg-step-item" data-step="2">
            <div class="reg-step-circle">۲</div>
            <span class="reg-step-label">اطلاعات پزشکی</span>
        </div>
        <div class="reg-step-connector"></div>
        <div class="reg-step-item" data-step="3">
            <div class="reg-step-circle">۳</div>
            <span class="reg-step-label">بیمه</span>
        </div>
    </div>

    <!-- ========== STEP 1 — Basic Info ========== -->
    <div class="reg-step-panel active" data-panel="1">
        <div class="reg-step-title"><i class="fas fa-user-circle"></i> اطلاعات پایه</div>

        <div class="form-row">
            <div class="form-group">
                <label for="first_name">نام <span class="required">*</span></label>
                <div class="login-input-wrapper">
                    <input type="text" name="first_name" class="form-control" id="first_name" required
                           placeholder="نام"
                           value="<?php echo e(old('first_name') ?? ''); ?>">
                    <i class="fas fa-user input-icon"></i>
                </div>
            </div>
            <div class="form-group">
                <label for="last_name">نام خانوادگی <span class="required">*</span></label>
                <div class="login-input-wrapper">
                    <input type="text" name="last_name" class="form-control" id="last_name" required
                           placeholder="نام خانوادگی"
                           value="<?php echo e(old('last_name') ?? ''); ?>">
                    <i class="fas fa-id-card input-icon"></i>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="national_code">کد ملی <span class="required">*</span></label>
            <div class="login-input-wrapper">
                <input type="text" name="national_code" class="form-control ltr-input" id="national_code" required
                       maxlength="10" dir="ltr" placeholder="کد ملی ۱۰ رقمی"
                       value="<?php echo e(old('national_code') ?? ''); ?>">
                <i class="fas fa-fingerprint input-icon"></i>
            </div>
            <small style="font-size:0.75rem;color:#6b7280;margin-top:4px;display:block;">کد ملی شما به عنوان نام کاربری و رمز عبور استفاده می‌شود.</small>
        </div>

        <div class="form-group">
            <label for="phone">شماره تلفن <span class="required">*</span></label>
            <div class="login-input-wrapper">
                <input type="tel" name="phone" class="form-control ltr-input" id="phone" required
                       placeholder="09123456789"
                       value="<?php echo e(old('phone') ?? ''); ?>">
                <i class="fas fa-mobile-alt input-icon"></i>
            </div>
        </div>

        <div class="form-group">
            <label for="email">ایمیل</label>
            <div class="login-input-wrapper">
                <input type="email" name="email" class="form-control ltr-input" id="email"
                       placeholder="example@email.com" dir="ltr"
                       value="<?php echo e(old('email') ?? ''); ?>">
                <i class="fas fa-envelope input-icon"></i>
            </div>
        </div>

        <div class="form-group">
            <label for="birth_date">تاریخ تولد</label>
            <input type="text" name="birth_date" class="form-control-plain" id="birth_date"
                   placeholder="انتخاب تاریخ" autocomplete="off"
                   value="<?php echo e(old('birth_date') ?? ''); ?>">
        </div>

        <div class="reg-nav-btns">
            <button type="button" class="reg-btn-next" onclick="goToStep(2)">
                مرحله بعد
                <i class="fas fa-arrow-left" style="font-size:0.8rem;"></i>
            </button>
        </div>
    </div>

    <!-- ========== STEP 2 — Health Info ========== -->
    <div class="reg-step-panel" data-panel="2">
        <div class="reg-step-title"><i class="fas fa-heartbeat"></i> اطلاعات پزشکی</div>

        <div class="form-group">
            <label for="blood_type">گروه خونی</label>
            <select name="blood_type" id="blood_type" class="form-control-plain">
                <option value="">انتخاب کنید</option>
                <option value="A+" <?php echo (old('blood_type') ?? '') === 'A+' ? 'selected' : ''; ?>>A+</option>
                <option value="A-" <?php echo (old('blood_type') ?? '') === 'A-' ? 'selected' : ''; ?>>A-</option>
                <option value="B+" <?php echo (old('blood_type') ?? '') === 'B+' ? 'selected' : ''; ?>>B+</option>
                <option value="B-" <?php echo (old('blood_type') ?? '') === 'B-' ? 'selected' : ''; ?>>B-</option>
                <option value="AB+" <?php echo (old('blood_type') ?? '') === 'AB+' ? 'selected' : ''; ?>>AB+</option>
                <option value="AB-" <?php echo (old('blood_type') ?? '') === 'AB-' ? 'selected' : ''; ?>>AB-</option>
                <option value="O+" <?php echo (old('blood_type') ?? '') === 'O+' ? 'selected' : ''; ?>>O+</option>
                <option value="O-" <?php echo (old('blood_type') ?? '') === 'O-' ? 'selected' : ''; ?>>O-</option>
            </select>
        </div>

        <div class="form-group">
            <label for="allergies">آلرژی‌ها و حساسیت‌ها</label>
            <textarea name="allergies" id="allergies" class="reg-textarea"
                      placeholder="در صورت داشتن آلرژی یا حساسیت خاصی، اینجا ذکر کنید..."><?php echo e(old('allergies') ?? ''); ?></textarea>
        </div>

        <div class="form-group">
            <label for="medications">داروهای مصرفی</label>
            <textarea name="medications" id="medications" class="reg-textarea"
                      placeholder="داروهایی که مصرف می‌کنید را ذکر کنید..."><?php echo e(old('medications') ?? ''); ?></textarea>
        </div>

        <div class="form-group">
            <label for="medical_history">سوابق پزشکی</label>
            <textarea name="medical_history" id="medical_history" class="reg-textarea"
                      placeholder="سوابق بیماری‌ها، جراحی‌ها یا مشکلات پزشکی..."><?php echo e(old('medical_history') ?? ''); ?></textarea>
        </div>

        <div class="form-group">
            <label for="address">آدرس</label>
            <textarea name="address" id="address" class="reg-textarea"
                      placeholder="آدرس محل سکونت..."><?php echo e(old('address') ?? ''); ?></textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="emergency_contact">نام فرد تماس اضطراری</label>
                <div class="login-input-wrapper">
                    <input type="text" name="emergency_contact" class="form-control" id="emergency_contact"
                           placeholder="نام و نام خانوادگی"
                           value="<?php echo e(old('emergency_contact') ?? ''); ?>">
                    <i class="fas fa-phone-alt input-icon"></i>
                </div>
            </div>
            <div class="form-group">
                <label for="emergency_phone">شماره تماس اضطراری</label>
                <div class="login-input-wrapper">
                    <input type="tel" name="emergency_phone" class="form-control ltr-input" id="emergency_phone"
                           placeholder="09123456789"
                           value="<?php echo e(old('emergency_phone') ?? ''); ?>">
                    <i class="fas fa-phone-volume input-icon"></i>
                </div>
            </div>
        </div>

        <div class="reg-nav-btns">
            <button type="button" class="reg-btn-prev" onclick="goToStep(1)">
                <i class="fas fa-arrow-right" style="font-size:0.8rem;"></i>
                مرحله قبل
            </button>
            <button type="button" class="reg-btn-next" onclick="goToStep(3)">
                مرحله بعد
                <i class="fas fa-arrow-left" style="font-size:0.8rem;"></i>
            </button>
        </div>
    </div>

    <!-- ========== STEP 3 — Insurance ========== -->
    <div class="reg-step-panel" data-panel="3">
        <div class="reg-step-title"><i class="fas fa-shield-alt"></i> بیمه</div>

        <div class="form-group">
            <label for="insurance_type">نوع بیمه</label>
            <select name="insurance_type" id="insurance_type" class="form-control-plain">
                <option value="">بدون بیمه</option>
                <option value="بیمه ورزشی" <?php echo (old('insurance_type') ?? '') === 'بیمه ورزشی' ? 'selected' : ''; ?>>بیمه ورزشی</option>
                <option value="بیمه درمان" <?php echo (old('insurance_type') ?? '') === 'بیمه درمان' ? 'selected' : ''; ?>>بیمه درمان</option>
                <option value="سایر" <?php echo (old('insurance_type') ?? '') === 'سایر' ? 'selected' : ''; ?>>سایر</option>
            </select>
        </div>

        <div class="form-group">
            <label for="policy_number">شماره بیمه‌نامه</label>
            <div class="login-input-wrapper">
                <input type="text" name="policy_number" class="form-control ltr-input" id="policy_number"
                       placeholder="شماره بیمه‌نامه" dir="ltr"
                       value="<?php echo e(old('policy_number') ?? ''); ?>">
                <i class="fas fa-file-alt input-icon"></i>
            </div>
        </div>

        <div class="form-group">
            <label>فایل بیمه‌نامه</label>
            <div class="reg-file-upload" id="fileUploadArea" onclick="document.getElementById('insurance_document').click()">
                <i class="fas fa-cloud-upload-alt" id="fileUploadIcon"></i>
                <p id="fileUploadText">تصویر یا PDF بیمه‌نامه را اینجا بکشید یا کلیک کنید</p>
                <div class="file-name" id="fileName" style="display:none;"></div>
                <input type="file" name="document" id="insurance_document" accept="image/*,.pdf"
                       onchange="handleFileSelect(this)">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="start_date">تاریخ شروع</label>
                <input type="text" name="start_date" class="form-control-plain" id="start_date"
                       placeholder="انتخاب تاریخ" autocomplete="off"
                       value="<?php echo e(old('start_date') ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="end_date">تاریخ پایان</label>
                <input type="text" name="end_date" class="form-control-plain" id="end_date"
                       placeholder="انتخاب تاریخ" autocomplete="off"
                       value="<?php echo e(old('end_date') ?? ''); ?>">
            </div>
        </div>

        <div class="reg-nav-btns">
            <button type="button" class="reg-btn-prev" onclick="goToStep(2)">
                <i class="fas fa-arrow-right" style="font-size:0.8rem;"></i>
                مرحله قبل
            </button>
            <button type="submit" class="reg-btn-next" id="registerBtn">
                <span id="registerBtnText">ثبت‌نام</span>
                <i class="fas fa-user-plus" id="registerBtnIcon" style="font-size:0.85rem;"></i>
                <span id="registerBtnSpinner" class="btn-spinner"></span>
            </button>
        </div>
    </div>
</form>

<!-- Login Link -->
<div class="auth-footer">
    <span>قبلاً ثبت‌نام کرده‌اید؟</span>
    <a href="<?php echo url('auth/login'); ?>">
        <i class="fas fa-sign-in-alt" style="font-size: 0.75rem; margin-left: 0.25rem;"></i>
        وارد شوید
    </a>
</div>

<script src="<?php echo asset('js/flatpickr.min.js'); ?>"></script>
<script src="<?php echo asset('js/flatpickr-fa.js'); ?>"></script>
<script>
(function() {
    var currentStep = 1;
    var totalSteps = 3;

    window.goToStep = function(step) {
        // Validate current step before going forward
        if (step > currentStep) {
            if (!validateStep(currentStep)) return;
        }

        // Hide current panel
        document.querySelectorAll('.reg-step-panel').forEach(function(p) {
            p.classList.remove('active');
        });

        // Show target panel
        document.querySelector('.reg-step-panel[data-panel="' + step + '"]').classList.add('active');

        // Update step indicators
        document.querySelectorAll('.reg-step-item').forEach(function(item) {
            var s = parseInt(item.getAttribute('data-step'));
            item.classList.remove('active', 'done');
            if (s < step) item.classList.add('done');
            if (s === step) item.classList.add('active');
        });
        document.querySelectorAll('.reg-step-connector').forEach(function(conn, idx) {
            conn.classList.toggle('done', idx < step - 1);
        });

        currentStep = step;

        // Init datepickers on step 1 or 3
        if (step === 1) initDatepicker('birth_date');
        if (step === 3) {
            initDatepicker('start_date');
            initDatepicker('end_date');
        }
    };

    function validateStep(step) {
        if (step === 1) {
            var fn = document.getElementById('first_name').value.trim();
            var ln = document.getElementById('last_name').value.trim();
            var nc = document.getElementById('national_code').value.trim();
            var ph = document.getElementById('phone').value.trim();
            if (!fn || !ln) { alert('نام و نام خانوادگی الزامی است.'); return false; }
            if (!nc || nc.length !== 10 || !/^\d{10}$/.test(nc)) { alert('کد ملی باید دقیقاً ۱۰ رقم باشد.'); return false; }
            if (!ph || ph.length < 10) { alert('شماره تلفن نامعتبر است.'); return false; }
        }
        return true;
    }

    // Datepicker init with flatpickr
    var datepickerInited = {};
    function initDatepicker(id) {
        if (datepickerInited[id]) return;
        var el = document.getElementById(id);
        if (!el) return;
        try {
            flatpickr(el, {
                locale: 'fa',
                dateFormat: 'Y/m/d',
                autoClose: true,
                todayButton: 'امروز'
            });
            datepickerInited[id] = true;
        } catch(e) {}
    }

    // Init birth_date datepicker on load
    initDatepicker('birth_date');

    // File upload handler
    window.handleFileSelect = function(input) {
        var area = document.getElementById('fileUploadArea');
        var icon = document.getElementById('fileUploadIcon');
        var text = document.getElementById('fileUploadText');
        var nameEl = document.getElementById('fileName');
        if (input.files && input.files[0]) {
            area.classList.add('has-file');
            icon.className = 'fas fa-file-circle-check';
            text.textContent = 'فایل انتخاب شد';
            nameEl.textContent = input.files[0].name;
            nameEl.style.display = 'block';
        } else {
            area.classList.remove('has-file');
            icon.className = 'fas fa-cloud-upload-alt';
            text.textContent = 'تصویر یا PDF بیمه‌نامه را اینجا بکشید یا کلیک کنید';
            nameEl.style.display = 'none';
        }
    };

    // Form submission
    var form = document.getElementById('registerForm');
    var btn = document.getElementById('registerBtn');
    if (form && btn) {
        form.addEventListener('submit', function() {
            btn.disabled = true;
            document.getElementById('registerBtnText').textContent = 'در حال ثبت‌نام...';
            document.getElementById('registerBtnIcon').style.display = 'none';
            document.getElementById('registerBtnSpinner').style.display = 'inline-block';
        });
    }
})();
</script>