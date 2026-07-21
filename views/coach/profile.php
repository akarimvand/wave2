<?php $activeMenu = 'profile'; $pageTitle = 'پروفایل'; ?>

<div class="page-header-row">
    <div class="page-header">
        <h2 class="page-title">
            <i class="fas fa-user-cog" style="color:#3B82F6;margin-left:8px;"></i>
            پروفایل
        </h2>
        <p>مشاهده و ویرایش اطلاعات پروفایل</p>
    </div>
</div>

<div class="grid-2col profile-grid">
    <!-- Profile Info Card -->
    <div class="card">
        <div class="card-header">
            <h3 style="margin:0;font-size:0.95rem;font-weight:600;">
                <i class="fas fa-user" style="margin-left:8px;color:#3B82F6;"></i>
                اطلاعات شخصی
            </h3>
        </div>
        <div class="card-body">
            <form method="POST" action="<?php echo url('coach/profile/update'); ?>">
                <?php echo csrf_field(); ?>

                <div style="text-align:center;margin-bottom:24px;">
                    <div style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,#3B82F6,#8B5CF6);display:inline-flex;align-items:center;justify-content:center;">
                        <i class="fas fa-user" style="color:#fff;font-size:2rem;"></i>
                    </div>
                    <h4 style="margin:12px 0 4px 0;font-size:1.1rem;">
                        <?php echo e($coach['first_name'] . ' ' . $coach['last_name']); ?>
                    </h4>
                    <span style="background:rgba(59,130,246,0.1);color:#3B82F6;padding:3px 12px;border-radius:12px;font-size:0.82rem;">
                        <?php echo e($coach['specialty'] ?? 'مربی'); ?>
                    </span>
                </div>

                <div style="display:grid;gap:16px;">
                    <div>
                        <label style="display:block;font-size:0.85rem;font-weight:500;color:#374151;margin-bottom:6px;">
                            <i class="fas fa-user" style="margin-left:4px;color:#3B82F6;"></i>
                            نام
                        </label>
                        <input type="text" value="<?php echo e($coach['first_name']); ?>" disabled
                               class="form-input" style="background:#F9FAFB;color:#6B7A8D;">
                    </div>
                    <div>
                        <label style="display:block;font-size:0.85rem;font-weight:500;color:#374151;margin-bottom:6px;">
                            <i class="fas fa-user" style="margin-left:4px;color:#3B82F6;"></i>
                            نام خانوادگی
                        </label>
                        <input type="text" value="<?php echo e($coach['last_name']); ?>" disabled
                               class="form-input" style="background:#F9FAFB;color:#6B7A8D;">
                    </div>
                    <div>
                        <label style="display:block;font-size:0.85rem;font-weight:500;color:#374151;margin-bottom:6px;">
                            <i class="fas fa-phone" style="margin-left:4px;color:#3B82F6;"></i>
                            تلفن
                        </label>
                        <input type="tel" name="phone" value="<?php echo e($coach['phone'] ?? ''); ?>"
                               class="form-input" style="direction:ltr;text-align:right;"
                               placeholder="09xxxxxxxxx">
                    </div>
                    <div>
                        <label style="display:block;font-size:0.85rem;font-weight:500;color:#374151;margin-bottom:6px;">
                            <i class="fas fa-envelope" style="margin-left:4px;color:#3B82F6;"></i>
                            ایمیل
                        </label>
                        <input type="email" name="email" value="<?php echo e($coach['email'] ?? ''); ?>"
                               class="form-input" style="direction:ltr;text-align:right;"
                               placeholder="email@example.com">
                    </div>
                    <div>
                        <label style="display:block;font-size:0.85rem;font-weight:500;color:#374151;margin-bottom:6px;">
                            <i class="fas fa-info-circle" style="margin-left:4px;color:#3B82F6;"></i>
                            درباره من
                        </label>
                        <textarea name="bio" rows="3"
                                  class="form-textarea"
                                  placeholder="درباره خودتان بنویسید..."><?php echo e($coach['bio'] ?? ''); ?></textarea>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%;margin-top:20px;padding:12px;font-size:0.95rem;">
                    <i class="fas fa-save" style="margin-left:6px;"></i>
                    ذخیره تغییرات
                </button>
            </form>
        </div>
    </div>

    <!-- Info & Change Password -->
    <div style="display:flex;flex-direction:column;gap:20px;">
        <!-- Account Info -->
        <div class="card">
            <div class="card-header">
                <h3 style="margin:0;font-size:0.95rem;font-weight:600;">
                    <i class="fas fa-id-badge" style="margin-left:8px;color:#3B82F6;"></i>
                    اطلاعات حساب
                </h3>
            </div>
            <div class="card-body">
                <div style="display:grid;gap:14px;">
                    <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #F3F4F6;">
                        <span style="font-size:0.85rem;color:#6B7A8D;">تخصص</span>
                        <span style="font-size:0.85rem;font-weight:500;"><?php echo e($coach['specialty'] ?? '-'); ?></span>
                    </div>
                    <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #F3F4F6;">
                        <span style="font-size:0.85rem;color:#6B7A8D;">تاریخ استخدام</span>
                        <span style="font-size:0.85rem;font-weight:500;"><?php echo formatDate($coach['hire_date'] ?? ''); ?></span>
                    </div>
                    <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #F3F4F6;">
                        <span style="font-size:0.85rem;color:#6B7A8D;">نام کاربری</span>
                        <span style="font-size:0.85rem;font-weight:500;direction:ltr;">@<?php echo e(auth()->user()['username'] ?? ''); ?></span>
                    </div>
                    <div style="display:flex;justify-content:space-between;padding:10px 0;">
                        <span style="font-size:0.85rem;color:#6B7A8D;">وضعیت</span>
                        <span class="badge badge-success">فعال</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Change Password -->
        <div class="card">
            <div class="card-header">
                <h3 style="margin:0;font-size:0.95rem;font-weight:600;">
                    <i class="fas fa-lock" style="margin-left:8px;color:#F59E0B;"></i>
                    تغییر رمز عبور
                </h3>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo url('coach/profile/change-password'); ?>">
                    <?php echo csrf_field(); ?>
                    <div style="display:grid;gap:14px;">
                        <div>
                            <label style="display:block;font-size:0.85rem;font-weight:500;color:#374151;margin-bottom:6px;">رمز عبور فعلی</label>
                            <input type="password" name="current_password" required class="form-input">
                        </div>
                        <div>
                            <label style="display:block;font-size:0.85rem;font-weight:500;color:#374151;margin-bottom:6px;">رمز عبور جدید</label>
                            <input type="password" name="new_password" required minlength="6" class="form-input">
                        </div>
                        <div>
                            <label style="display:block;font-size:0.85rem;font-weight:500;color:#374151;margin-bottom:6px;">تکرار رمز عبور جدید</label>
                            <input type="password" name="confirm_password" required minlength="6" class="form-input">
                        </div>
                    </div>
                    <button type="submit" class="btn" style="width:100%;margin-top:16px;background:#F59E0B;color:#fff;border:none;border-radius:10px;padding:10px;font-size:0.9rem;">
                        <i class="fas fa-key" style="margin-left:6px;"></i>
                        تغییر رمز عبور
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>