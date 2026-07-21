<?php $isEdit = !empty($coach); ?>
<div class="page-header-row">
    <div class="page-header">
        <h2><?php echo $isEdit ? 'ویرایش مربی' : 'افزودن مربی جدید'; ?></h2>
        <p><?php echo $isEdit ? 'اطلاعات مربی را ویرایش کنید' : 'اطلاعات مربی جدید را وارد کنید'; ?></p>
    </div>
    <div class="page-header-actions">
        <a href="<?php echo url('admin/coaches'); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i>
            بازگشت
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?php echo $isEdit ? url('admin/coaches/' . $coach['id'] . '/update') : url('admin/coaches/store'); ?>">
            <?php echo csrf_field(); ?>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-user" style="margin-left:4px;color:#1B4F8A;"></i>
                        نام <span class="required">*</span>
                    </label>
                    <input type="text" name="first_name" class="form-input" required placeholder="نام مربی" value="<?php echo e($coach['first_name'] ?? old('first_name') ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-user" style="margin-left:4px;color:#1B4F8A;"></i>
                        نام خانوادگی <span class="required">*</span>
                    </label>
                    <input type="text" name="last_name" class="form-input" required placeholder="نام خانوادگی مربی" value="<?php echo e($coach['last_name'] ?? old('last_name') ?? ''); ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-star" style="margin-left:4px;color:#1B4F8A;"></i>
                        تخصص
                    </label>
                    <input type="text" name="specialty" class="form-input" placeholder="مثال: بدنسازی، یوگا، ایروبیک" value="<?php echo e($coach['specialty'] ?? old('specialty') ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-phone" style="margin-left:4px;color:#1B4F8A;"></i>
                        تلفن
                    </label>
                    <input type="tel" name="phone" class="form-input" placeholder="09123456789" value="<?php echo e($coach['phone'] ?? old('phone') ?? ''); ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-envelope" style="margin-left:4px;color:#1B4F8A;"></i>
                        ایمیل
                    </label>
                    <input type="email" name="email" class="form-input" placeholder="example@mail.com" value="<?php echo e($coach['email'] ?? old('email') ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-money-bill-wave" style="margin-left:4px;color:#1B4F8A;"></i>
                        حقوق (تومان)
                    </label>
                    <input type="number" name="salary" class="form-input" min="0" placeholder="0" value="<?php echo e($coach['salary'] ?? old('salary') ?? ''); ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-calendar-alt" style="margin-left:4px;color:#1B4F8A;"></i>
                        تاریخ استخدام
                    </label>
                    <input type="text" name="hire_date" class="form-input jalali-date" data-datepicker placeholder="انتخاب تاریخ" value="<?php echo $coach['hire_date'] ? e(formatDate($coach['hire_date'])) : e(old('hire_date') ?? ''); ?>">
                </div>
                <div class="form-group" style="display:flex;align-items:flex-end;padding-bottom:4px;">
                    <label class="form-label" style="display:flex;align-items:center;gap:8px;cursor:pointer;margin:0;">
                        <input type="checkbox" name="is_active" value="1" <?php echo (isset($coach) && $coach['is_active'] == 1) || old('is_active') == '1' ? 'checked' : (!isset($coach) ? 'checked' : ''); ?> style="width:18px;height:18px;accent-color:#1B4F8A;">
                        <span style="font-weight:600;">
                            <i class="fas fa-check-circle" style="margin-left:4px;color:#16a34a;"></i>
                            فعال
                        </span>
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-align-left" style="margin-left:4px;color:#1B4F8A;"></i>
                    بیوگرافی
                </label>
                <textarea name="bio" class="form-textarea" rows="3" placeholder="توضیحات درباره مربی..."><?php echo e($coach['bio'] ?? old('bio') ?? ''); ?></textarea>
            </div>

            <?php if (!$isEdit): ?>
            <div style="border-top:2px solid #e2e8f0;margin:24px 0 16px;padding-top:16px;">
                <h3 style="font-size:0.95rem;font-weight:600;margin-bottom:12px;color:#334155;">
                    <i class="fas fa-user-shield" style="color:#059669;margin-left:6px;"></i>
                    ایجاد حساب کاربری برای ورود مربی به پنل
                </h3>
                <label class="form-label" style="display:flex;align-items:center;gap:8px;cursor:pointer;margin-bottom:12px;">
                    <input type="checkbox" id="createUserAccount" name="create_user_account" value="1" onchange="toggleCoachAccount()" style="width:18px;height:18px;accent-color:#059669;">
                    <span style="font-weight:500;">ایجاد حساب کاربری</span>
                    <small style="color:#6B7280;">(مربی بتواند با نام کاربری وارد پنل خود شود)</small>
                </label>
                <div id="coachAccountFields" style="display:none;">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">نام کاربری</label>
                            <input type="text" name="coach_username" id="coachUsername" class="form-input" dir="ltr" style="text-align:right;" placeholder="مثلا: coach_ali" oninput="this.value = this.value.replace(/\s/g, '_')">
                        </div>
                        <div class="form-group">
                            <label class="form-label">رمز عبور</label>
                            <input type="text" name="coach_password" class="form-input" dir="ltr" style="text-align:right;" placeholder="پیش‌فرض: 123456">
                            <small style="color:#9CA3AF;font-size:0.78rem;">در صورت خالی، 123456 تنظیم می‌شود</small>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="form-actions" style="display:flex;gap:8px;margin-top:24px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    <?php echo $isEdit ? 'ذخیره تغییرات' : 'ذخیره'; ?>
                </button>
                <a href="<?php echo url('admin/coaches'); ?>" class="btn btn-secondary">انصراف</a>
            </div>
        </form>
    </div>
</div>
<?php if (!$isEdit): ?>
<script>
function toggleCoachAccount() {
    var cb = document.getElementById('createUserAccount');
    var fields = document.getElementById('coachAccountFields');
    fields.style.display = cb.checked ? 'block' : 'none';
    if (cb.checked && !document.getElementById('coachUsername').value) {
        // Auto-suggest username from first/last name
        var fn = document.querySelector('[name="first_name"]').value;
        var ln = document.querySelector('[name="last_name"]').value;
        if (fn || ln) {
            document.getElementById('coachUsername').value = 'coach_' + (fn + ln).replace(/\s/g, '').toLowerCase();
        }
    }
}
</script>
<?php endif; ?>