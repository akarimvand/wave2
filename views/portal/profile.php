<div class="page-header">
    <h2 class="page-title"><i class="fas fa-user" style="margin-left:8px;color:#3B82F6;"></i>پروفایل من</h2>
    <p>اطلاعات شخصی شما</p>
</div>

<div class="page-actions" style="margin-bottom:20px;display:flex;justify-content:flex-end;">
    <button type="button" id="profile-toggle-btn" class="btn btn-primary" onclick="toggleProfileMode()">
        <i class="fas fa-pen-to-square" style="margin-left:6px;"></i>
        ویرایش پروفایل
    </button>
</div>

<!-- 3D Flip Membership Card -->
<?php
$primaryColor = function_exists('getSetting') ? getSetting('primary_color', '#3B82F6') : '#3B82F6';
$secondaryColor = function_exists('getSetting') ? getSetting('secondary_color', '#8B5CF6') : '#8B5CF6';
$clubNameCard = function_exists('getSetting') ? getSetting('club_name', 'WAVE CLUB') : 'WAVE CLUB';
$memberInitials = mb_substr($member['first_name'] ?? '', 0, 1) . mb_substr($member['last_name'] ?? '', 0, 1);
?>
<div class="flip-card-container" style="--primary-color:<?php echo e($primaryColor); ?>;--secondary-color:<?php echo e($secondaryColor); ?>;">
    <div class="flip-card" id="membershipCard" onclick="this.classList.toggle('flipped')">
        <!-- FRONT -->
        <div class="flip-card-front">
            <div class="flip-card-header">
                <span class="flip-card-logo"><?php echo e($clubNameCard); ?></span>
                <span class="flip-card-badge">
                    <?php if (($member['status'] ?? '') === 'active'): ?>
                    <i class="fas fa-circle-check" style="margin-left:4px;"></i>عضو فعال
                    <?php elseif (($member['approval_status'] ?? '') === 'pending'): ?>
                    <i class="fas fa-clock" style="margin-left:4px;"></i>در انتظار تأیید
                    <?php else: ?>
                    <?php echo e($member['status'] ?? ''); ?>
                    <?php endif; ?>
                </span>
            </div>
            <div class="flip-card-body">
                <div class="flip-card-avatar">
                    <?php if (!empty($member['avatar_path'])): ?>
                    <img src="<?php echo asset($member['avatar_path']); ?>" alt="">
                    <?php else: ?>
                    <?php echo e($memberInitials); ?>
                    <?php endif; ?>
                </div>
                <div class="flip-card-user-info">
                    <h3><?php echo e(($member['first_name'] ?? '') . ' ' . ($member['last_name'] ?? '')); ?></h3>
                    <p><?php echo e($member['phone'] ?? ''); ?></p>
                </div>
            </div>
            <div class="flip-card-footer">
                <span class="flip-card-id">ID: <?php echo e($member['national_code'] ?? $member['id'] ?? ''); ?></span>
                <span class="flip-card-validity"><?php echo e($member['blood_type'] ?? ''); ?></span>
            </div>
        </div>
        <!-- BACK -->
        <div class="flip-card-back">
            <div class="flip-card-back-content">
                <div class="flip-card-info-row">
                    <i class="fas fa-phone"></i>
                    <span><?php echo e($member['phone'] ?? '-'); ?></span>
                </div>
                <div class="flip-card-info-row">
                    <i class="fas fa-envelope"></i>
                    <span><?php echo e($member['email'] ?? 'ثبت نشده'); ?></span>
                </div>
                <div class="flip-card-info-row">
                    <i class="fas fa-droplet"></i>
                    <span>گروه خونی: <?php echo e($member['blood_type'] ?? 'نامشخص'); ?></span>
                </div>
                <div class="flip-card-info-row">
                    <i class="fas fa-calendar-check"></i>
                    <span>عضویت: <?php echo formatDate($member['created_at'] ?? ''); ?></span>
                </div>
                <?php if (!empty($member['allergies'])): ?>
                <div class="flip-card-info-row">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>حساسیت: <?php echo e(mb_substr($member['allergies'], 0, 40)); ?></span>
                </div>
                <?php endif; ?>
            </div>
            <div class="flip-card-tap-hint">
                <i class="fas fa-sync-alt" style="margin-left:4px;"></i>
                برای بازگشت کلیک کنید
            </div>
        </div>
    </div>
</div>

<!-- Display Mode -->
<div id="profile-display">
    <div class="card">
        <div class="card-body">
            <!-- Avatar + Name -->
            <div style="display:flex;align-items:center;gap:20px;margin-bottom:24px;padding-bottom:20px;border-bottom:1px solid #E5E7EB;">
                <div style="width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,#3B82F6,#8B5CF6);display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.5rem;font-weight:700;flex-shrink:0;">
                    <?php echo mb_substr(e($member['first_name'] ?? ''), 0, 1) . mb_substr(e($member['last_name'] ?? ''), 0, 1); ?>
                </div>
                <div>
                    <h3 style="margin:0 0 4px 0;font-size:1.1rem;font-weight:600;">
                        <?php echo e($member['first_name'] ?? '') . ' ' . e($member['last_name'] ?? ''); ?>
                    </h3>
                    <span class="badge <?php echo ($member['status'] ?? '') === 'active' ? 'badge-success' : (($member['status'] ?? '') === 'inactive' ? 'badge-danger' : 'badge-warning'); ?>">
                        <?php
                        $status = $member['status'] ?? '';
                        if ($status === 'active') echo 'فعال';
                        elseif ($status === 'inactive') echo 'غیرفعال';
                        elseif ($status === 'pending') echo 'در انتظار تأیید';
                        else echo e($status);
                        ?>
                    </span>
                </div>
            </div>

            <!-- Basic Info -->
            <h4 style="margin:0 0 16px 0;font-size:0.9rem;font-weight:600;color:#374151;">
                <i class="fas fa-id-card" style="margin-left:6px;color:#6B7A8D;"></i>
                اطلاعات پایه
            </h4>
            <div class="profile-grid">
                <div class="profile-field">
                    <span class="profile-field-label">نام</span>
                    <span class="profile-field-value"><?php echo e($member['first_name'] ?? ''); ?></span>
                </div>
                <div class="profile-field">
                    <span class="profile-field-label">نام خانوادگی</span>
                    <span class="profile-field-value"><?php echo e($member['last_name'] ?? ''); ?></span>
                </div>
                <div class="profile-field">
                    <span class="profile-field-label">کد ملی</span>
                    <span class="profile-field-value" style="direction:ltr;text-align:right;"><?php echo e($member['national_code'] ?? ''); ?></span>
                </div>
                <div class="profile-field">
                    <span class="profile-field-label">شماره تلفن</span>
                    <span class="profile-field-value" style="direction:ltr;text-align:right;"><?php echo e($member['phone'] ?? ''); ?></span>
                </div>
                <div class="profile-field">
                    <span class="profile-field-label">ایمیل</span>
                    <span class="profile-field-value" style="direction:ltr;text-align:right;"><?php echo e($member['email'] ?? '-'); ?></span>
                </div>
                <div class="profile-field">
                    <span class="profile-field-label">تاریخ عضویت</span>
                    <span class="profile-field-value"><?php echo formatDate($member['created_at'] ?? ''); ?></span>
                </div>
            </div>

            <!-- Address -->
            <h4 style="margin:24px 0 16px 0;font-size:0.9rem;font-weight:600;color:#374151;">
                <i class="fas fa-map-marker-alt" style="margin-left:6px;color:#6B7A8D;"></i>
                آدرس
            </h4>
            <div class="profile-grid">
                <div class="profile-field" style="grid-column:1/-1;">
                    <span class="profile-field-label">آدرس محل سکونت</span>
                    <span class="profile-field-value"><?php echo e($member['address'] ?? '-'); ?></span>
                </div>
            </div>

            <!-- Emergency Contact -->
            <h4 style="margin:24px 0 16px 0;font-size:0.9rem;font-weight:600;color:#374151;">
                <i class="fas fa-phone-alt" style="margin-left:6px;color:#6B7A8D;"></i>
                تماس اضطراری
            </h4>
            <div class="profile-grid">
                <div class="profile-field">
                    <span class="profile-field-label">نام فرد تماس</span>
                    <span class="profile-field-value"><?php echo e($member['emergency_contact'] ?? '-'); ?></span>
                </div>
                <div class="profile-field">
                    <span class="profile-field-label">شماره تماس اضطراری</span>
                    <span class="profile-field-value" style="direction:ltr;text-align:right;"><?php echo e($member['emergency_phone'] ?? '-'); ?></span>
                </div>
            </div>

            <!-- Account Info -->
            <h4 style="margin:24px 0 16px 0;font-size:0.9rem;font-weight:600;color:#374151;">
                <i class="fas fa-cog" style="margin-left:6px;color:#6B7A8D;"></i>
                اطلاعات حساب
            </h4>
            <div class="profile-grid">
                <div class="profile-field">
                    <span class="profile-field-label">نام کاربری</span>
                    <span class="profile-field-value"><?php echo e($member['username'] ?? auth()->user()['username'] ?? ''); ?></span>
                </div>
                <div class="profile-field">
                    <span class="profile-field-label">تاریخ ثبت‌نام</span>
                    <span class="profile-field-value"><?php echo formatDate($member['created_at'] ?? ''); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Mode -->
<div id="profile-edit" style="display:none;">
    <div class="card">
        <form method="POST" action="<?php echo url('portal/profile/update'); ?>" data-validate>
            <?php echo csrf_field(); ?>

            <!-- Basic Info Section -->
            <div class="card-header">
                <h4 style="margin:0;font-size:0.95rem;font-weight:600;">
                    <i class="fas fa-id-card" style="margin-left:8px;color:#3B82F6;"></i>
                    اطلاعات پایه
                </h4>
            </div>
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">نام <span class="required">*</span></label>
                        <input type="text" name="first_name" class="form-input" required value="<?php echo e($member['first_name'] ?? old('first_name') ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">نام خانوادگی <span class="required">*</span></label>
                        <input type="text" name="last_name" class="form-input" required value="<?php echo e($member['last_name'] ?? old('last_name') ?? ''); ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">شماره تلفن <span class="required">*</span></label>
                        <input type="tel" name="phone" class="form-input" required value="<?php echo e($member['phone'] ?? old('phone') ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">ایمیل</label>
                        <input type="email" name="email" class="form-input" value="<?php echo e($member['email'] ?? old('email') ?? ''); ?>" dir="ltr" style="text-align:right;">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">آدرس</label>
                    <textarea name="address" class="form-textarea" rows="2"><?php echo e($member['address'] ?? old('address') ?? ''); ?></textarea>
                </div>
            </div>

            <!-- Emergency Contact Section -->
            <div class="card-header" style="border-top:1px solid #E5E7EB;">
                <h4 style="margin:0;font-size:0.95rem;font-weight:600;">
                    <i class="fas fa-phone-alt" style="margin-left:8px;color:#3B82F6;"></i>
                    تماس اضطراری
                </h4>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label">نام فرد تماس اضطراری</label>
                    <input type="text" name="emergency_contact" class="form-input" value="<?php echo e($member['emergency_contact'] ?? old('emergency_contact') ?? ''); ?>">
                </div>
            </div>

            <!-- Actions -->
            <div style="padding:16px 20px;border-top:1px solid #E5E7EB;display:flex;gap:12px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save" style="margin-left:6px;"></i>
                    ذخیره تغییرات
                </button>
                <button type="button" class="btn btn-secondary" onclick="toggleProfileMode()">
                    <i class="fas fa-times" style="margin-left:6px;"></i>
                    انصراف
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleProfileMode() {
    const display = document.getElementById('profile-display');
    const edit = document.getElementById('profile-edit');
    const btn = document.getElementById('profile-toggle-btn');
    if (display.style.display === 'none') {
        display.style.display = '';
        edit.style.display = 'none';
        btn.innerHTML = '<i class="fas fa-pen-to-square" style="margin-left:6px;"></i> ویرایش پروفایل';
    } else {
        display.style.display = 'none';
        edit.style.display = '';
        btn.innerHTML = '<i class="fas fa-eye" style="margin-left:6px;"></i> مشاهده پروفایل';
    }
}
</script>