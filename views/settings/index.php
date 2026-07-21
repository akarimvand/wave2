<?php $s = $settings ?? []; ?>
<div class="page-header-row">
    <div class="page-header">
        <h2><i class="fas fa-cog" style="margin-left:8px;color:#3B82F6;"></i>تنظیمات سیستم</h2>
        <p>شخصی‌سازی و تنظیمات عمومی باشگاه</p>
    </div>
    <div class="page-header-actions"></div>
</div>

<form method="POST" action="<?php echo url('admin/settings/update'); ?>" enctype="multipart/form-data">
    <?php echo csrf_field(); ?>

    <!-- اطلاعات باشگاه -->
    <div class="card" style="margin-bottom:16px;">
        <div class="card-header">
            <h3 style="margin:0;font-size:0.95rem;font-weight:600;">
                <i class="fas fa-building" style="margin-left:8px;color:#3B82F6;"></i>
                اطلاعات باشگاه
            </h3>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">نام باشگاه <span class="required">*</span></label>
                <input type="text" name="club_name" class="form-input" required value="<?php echo e($s['club_name'] ?? ''); ?>" placeholder="نام باشگاه را وارد کنید">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-phone-alt" style="margin-left:4px;"></i>تلفن</label>
                    <input type="text" name="club_phone" class="form-input" value="<?php echo e($s['club_phone'] ?? ''); ?>" placeholder="شماره تلفن باشگاه" style="direction:ltr;text-align:right;">
                </div>
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-envelope" style="margin-left:4px;"></i>ایمیل</label>
                    <input type="email" name="club_email" class="form-input" value="<?php echo e($s['club_email'] ?? ''); ?>" placeholder="ایمیل باشگاه" style="direction:ltr;text-align:right;">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label"><i class="fas fa-map-marker-alt" style="margin-left:4px;"></i>آدرس</label>
                <textarea name="club_address" class="form-textarea" rows="2" placeholder="آدرس کامل باشگاه"><?php echo e($s['club_address'] ?? ''); ?></textarea>
            </div>
            <div class="form-group">
                <label class="form-label"><i class="fas fa-clock" style="margin-left:4px;"></i>ساعت کاری</label>
                <input type="text" name="working_hours" class="form-input" value="<?php echo e($s['working_hours'] ?? ''); ?>" placeholder="مثلاً: ۸ صبح تا ۱۰ شب">
            </div>
            <div class="form-group">
                <label class="form-label"><i class="fas fa-info-circle" style="margin-left:4px;"></i>درباره باشگاه</label>
                <textarea name="about_club" class="form-textarea" rows="3" placeholder="توضیحات کوتاهی درباره باشگاه..."><?php echo e($s['about_club'] ?? ''); ?></textarea>
            </div>
        </div>
    </div>

    <!-- لوگو و رنگ -->
    <div class="card" style="margin-bottom:16px;">
        <div class="card-header">
            <h3 style="margin:0;font-size:0.95rem;font-weight:600;">
                <i class="fas fa-palette" style="margin-left:8px;color:#8B5CF6;"></i>
                لوگو و رنگ‌بندی
            </h3>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label"><i class="fas fa-image" style="margin-left:4px;"></i>لوگوی باشگاه</label>
                <div style="display:flex;align-items:flex-start;gap:16px;">
                    <div style="flex:1;">
                        <input type="file" name="logo_file" accept="image/*" class="form-input" style="padding:8px 12px;">
                        <small style="color:#6b7280;font-size:0.78rem;margin-top:4px;display:block;">فرمت‌های مجاز: JPG, PNG, GIF, WebP — حداکثر ۲ مگابایت</small>
                    </div>
                    <?php if (!empty($s['logo_path'])): ?>
                    <div style="width:80px;height:80px;border-radius:12px;overflow:hidden;border:2px solid #e5e7eb;flex-shrink:0;background:#f8fafc;display:flex;align-items:center;justify-content:center;">
                        <img src="<?php echo asset($s['logo_path']); ?>" alt="لوگو" style="width:100%;height:100%;object-fit:contain;">
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">رنگ اصلی</label>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <input type="color" name="primary_color" value="<?php echo e($s['primary_color'] ?? '#1877F2'); ?>" style="width:48px;height:40px;border:2px solid #e5e7eb;border-radius:8px;cursor:pointer;padding:2px;">
                        <input type="text" name="primary_color_text" class="form-input" style="max-width:120px;direction:ltr;text-align:center;font-family:monospace;" value="<?php echo e($s['primary_color'] ?? '#1877F2'); ?>" id="primary_color_text">
                    </div>
                    <small style="color:#6b7280;font-size:0.78rem;">رنگ اصلی کارت‌ها، دکمه‌ها و عناصر مهم</small>
                </div>
                <div class="form-group">
                    <label class="form-label">رنگ ثانویه</label>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <input type="color" name="secondary_color" value="<?php echo e($s['secondary_color'] ?? '#0A3178'); ?>" style="width:48px;height:40px;border:2px solid #e5e7eb;border-radius:8px;cursor:pointer;padding:2px;">
                        <input type="text" name="secondary_color_text" class="form-input" style="max-width:120px;direction:ltr;text-align:center;font-family:monospace;" value="<?php echo e($s['secondary_color'] ?? '#0A3178'); ?>" id="secondary_color_text">
                    </div>
                    <small style="color:#6b7280;font-size:0.78rem;">رنگ ثانویه برای گرادیانت‌ها و عناصر مکمل</small>
                </div>
            </div>
        </div>
    </div>

    <!-- تنظیمات مالی -->
    <div class="card" style="margin-bottom:16px;">
        <div class="card-header">
            <h3 style="margin:0;font-size:0.95rem;font-weight:600;">
                <i class="fas fa-money-bill-wave" style="margin-left:8px;color:#10B981;"></i>
                تنظیمات مالی
            </h3>
        </div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-coins" style="margin-left:4px;"></i>واحد پولی</label>
                    <select name="currency" class="form-input">
                        <option value="IRR" <?php echo ($s['currency'] ?? '') === 'IRR' ? 'selected' : ''; ?>>ریال</option>
                        <option value="Toman" <?php echo ($s['currency'] ?? '') === 'Toman' ? 'selected' : ''; ?>>تومان</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-percentage" style="margin-left:4px;"></i>نرخ مالیات (درصد)</label>
                    <input type="number" name="tax_rate" class="form-input" min="0" max="100" step="0.1" value="<?php echo e($s['tax_rate'] ?? '0'); ?>" placeholder="۰" style="max-width:200px;">
                </div>
            </div>
        </div>
    </div>

    <!-- تنظیمات سیستم -->
    <div class="card" style="margin-bottom:16px;">
        <div class="card-header">
            <h3 style="margin:0;font-size:0.95rem;font-weight:600;">
                <i class="fas fa-sliders-h" style="margin-left:8px;color:#F59E0B;"></i>
                تنظیمات سیستم
            </h3>
        </div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-list-ol" style="margin-left:4px;"></i>تعداد آیتم در صفحه</label>
                    <input type="number" name="items_per_page" class="form-input" min="5" max="100" value="<?php echo e($s['items_per_page'] ?? '20'); ?>" style="max-width:200px;">
                </div>
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-hourglass-half" style="margin-left:4px;"></i>مدت جلسه (دقیقه)</label>
                    <input type="number" name="session_timeout" class="form-input" min="5" max="480" value="<?php echo e($s['session_timeout'] ?? '30'); ?>" style="max-width:200px;">
                </div>
            </div>
        </div>
    </div>

    <!-- Save -->
    <div style="display:flex;justify-content:flex-end;gap:8px;">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save" style="margin-left:6px;"></i>
            ذخیره تنظیمات
        </button>
    </div>
</form>

<script>
// Sync color inputs
document.querySelectorAll('input[type="color"]').forEach(function(picker) {
    picker.addEventListener('input', function() {
        var name = this.name.replace('_color', '_color_text');
        var textInput = document.getElementById(name);
        if (textInput) textInput.value = this.value;
    });
});
document.querySelectorAll('input[name$="_color_text"]').forEach(function(textInput) {
    textInput.addEventListener('input', function() {
        var name = this.name.replace('_text', '');
        var picker = document.querySelector('input[name="' + name + '"]');
        if (picker && /^#[0-9a-fA-F]{6}$/.test(this.value)) picker.value = this.value;
    });
});
</script>