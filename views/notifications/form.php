<div class="page-header-row">
    <div class="page-header">
        <h2><i class="fas fa-paper-plane" style="margin-left:8px;color:#3B82F6;"></i>ارسال اعلان جدید</h2>
        <p>ایجاد و ارسال اعلان به کاربران سیستم</p>
    </div>
    <div class="page-header-actions">
        <a href="<?php echo url('admin/notifications'); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i>
            بازگشت
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?php echo url('admin/notifications/store'); ?>">
            <?php echo csrf_field(); ?>

            <div class="form-group">
                <label class="form-label">عنوان <span class="required">*</span></label>
                <input type="text" name="title" class="form-input" required value="<?php echo e(old('title') ?? ''); ?>" placeholder="عنوان اعلان را وارد کنید">
            </div>

            <div class="form-group">
                <label class="form-label">پیام <span class="required">*</span></label>
                <textarea name="message" class="form-input" rows="5" required placeholder="متن پیام اعلان را وارد کنید..."><?php echo e(old('message') ?? ''); ?></textarea>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div class="form-group">
                    <label class="form-label">نوع <span class="required">*</span></label>
                    <select name="type" class="form-input" required>
                        <option value="" disabled <?php echo empty(old('type')) ? 'selected' : ''; ?>>انتخاب نوع</option>
                        <option value="all" <?php echo old('type') === 'all' ? 'selected' : ''; ?>>همه</option>
                        <option value="sms" <?php echo old('type') === 'sms' ? 'selected' : ''; ?>>پیامک</option>
                        <option value="email" <?php echo old('type') === 'email' ? 'selected' : ''; ?>>ایمیل</option>
                        <option value="push" <?php echo old('type') === 'push' ? 'selected' : ''; ?>>اعلان (Push)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">گیرندگان <span class="required">*</span></label>
                    <select name="target_role" class="form-input" required>
                        <option value="" disabled <?php echo empty(old('target_role')) ? 'selected' : ''; ?>>انتخاب گیرندگان</option>
                        <option value="all" <?php echo old('target_role') === 'all' ? 'selected' : ''; ?>>همه کاربران</option>
                        <option value="admin" <?php echo old('target_role') === 'admin' ? 'selected' : ''; ?>>مدیران</option>
                        <option value="manager" <?php echo old('target_role') === 'manager' ? 'selected' : ''; ?>>مدیران ارشد</option>
                        <option value="member" <?php echo old('target_role') === 'member' ? 'selected' : ''; ?>>اعضا</option>
                        <option value="receptionist" <?php echo old('target_role') === 'receptionist' ? 'selected' : ''; ?>>پذیرش</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-calendar-alt" style="margin-left:4px;"></i>
                    تاریخ ارسال
                    <span style="font-size:0.8rem;color:#9CA3AF;font-weight:normal;">(اختیاری - در صورت خالی بودن بلافاصله ارسال می‌شود)</span>
                </label>
                <input type="text" name="send_at" class="form-input jalali-date" data-datepicker value="<?php echo e(old('send_at') ?? ''); ?>" placeholder="انتخاب تاریخ ارسال">
            </div>

            <div class="form-group" style="display:flex;align-items:center;gap:8px;margin-top:8px;">
                <input type="checkbox" name="send_now" id="send_now" value="1" <?php echo old('send_now') ? 'checked' : ''; ?>>
                <label for="send_now" style="margin:0;cursor:pointer;font-size:0.9rem;">
                    ارسال بلافاصله
                </label>
            </div>

            <div style="display:flex;gap:8px;margin-top:24px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane" style="margin-left:6px;"></i>
                    ارسال اعلان
                </button>
                <a href="<?php echo url('admin/notifications'); ?>" class="btn btn-secondary">
                    <i class="fas fa-times" style="margin-left:6px;"></i>
                    انصراف
                </a>
            </div>
        </form>
    </div>
</div>