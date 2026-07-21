<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ثبت‌نام | <?php echo APP_NAME; ?></title>

    <!-- Local Vazirmatn Font -->
    <link href="<?php echo asset('fonts/vazirmatn.css'); ?>" rel="stylesheet">
    <!-- Local Font Awesome -->
    <link rel="stylesheet" href="<?php echo asset('css/all.min.css'); ?>">
    <!-- Custom Styles -->
    <link rel="stylesheet" href="<?php echo asset('css/style.css'); ?>">

    <style>
        .auth-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            padding: 40px 20px;
        }
        .auth-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 540px;
            overflow: hidden;
        }
        .auth-header {
            background: linear-gradient(135deg, #1B4F8A, #2d6bb5);
            padding: 30px 30px 24px;
            text-align: center;
            color: #fff;
        }
        .auth-header .logo-icon {
            width: 60px;
            height: 60px;
            background: rgba(255,255,255,0.15);
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
        }
        .auth-header .logo-icon i { font-size: 28px; }
        .auth-header h1 { font-size: 24px; font-weight: 800; letter-spacing: 2px; margin-bottom: 2px; }
        .auth-header p { font-size: 12px; opacity: 0.85; margin: 0; }
        .auth-body { padding: 30px; }
        .auth-body .page-title {
            font-size: 18px; font-weight: 700; color: #1f2937;
            text-align: center; margin-bottom: 24px;
        }
        .form-group { margin-bottom: 16px; }
        .form-group label {
            display: block; font-size: 13px; font-weight: 600;
            color: #374151; margin-bottom: 6px;
        }
        .form-group label .required { color: #ef4444; }
        .form-input {
            width: 100%; height: 44px; padding: 0 14px;
            border: 2px solid #e5e7eb; border-radius: 10px;
            font-family: 'Vazirmatn', Tahoma, sans-serif; font-size: 14px;
            color: #1f2937; background: #f9fafb; transition: all 0.2s; outline: none;
        }
        .form-input:focus { border-color: #1B4F8A; background: #fff; box-shadow: 0 0 0 3px rgba(27,79,138,0.1); }
        .form-input::placeholder { color: #9ca3af; }
        .form-textarea {
            width: 100%; padding: 10px 14px;
            border: 2px solid #e5e7eb; border-radius: 10px;
            font-family: 'Vazirmatn', Tahoma, sans-serif; font-size: 14px;
            color: #1f2937; background: #f9fafb; transition: all 0.2s; outline: none; resize: vertical;
        }
        .form-textarea:focus { border-color: #1B4F8A; background: #fff; }
        .btn {
            display: inline-flex; align-items: center; justify-content: center;
            height: 44px; padding: 0 24px; border-radius: 10px;
            font-family: 'Vazirmatn', Tahoma, sans-serif; font-size: 14px;
            font-weight: 600; border: none; cursor: pointer; transition: all 0.2s;
        }
        .btn-primary {
            background: linear-gradient(135deg, #1B4F8A, #2d6bb5); color: #fff; width: 100%;
        }
        .btn-primary:hover { background: linear-gradient(135deg, #153d6e, #1B4F8A); box-shadow: 0 4px 12px rgba(27,79,138,0.4); }
        .auth-footer {
            text-align: center; padding-top: 16px;
            font-size: 13px; color: #6b7280;
        }
        .auth-footer a { color: #1B4F8A; font-weight: 600; }
        .auth-footer a:hover { text-decoration: underline; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        @media (max-width: 480px) { .form-row { grid-template-columns: 1fr; } }
        .alert { padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; font-size: 13px; }
        .alert-success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
        .alert-error { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
    </style>
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-header">
                <div class="logo-icon"><i class="fas fa-water"></i></div>
                <h1>WAVE</h1>
                <p>باشگاه ویو کنگان</p>
            </div>
            <div class="auth-body">
                <h2 class="page-title"><i class="fas fa-user-plus" style="margin-left:8px;"></i>ثبت‌نام عضویت</h2>

                <form method="POST" action="<?php echo url('registration/store'); ?>">
                    <?php echo csrf_field(); ?>

                    <div class="form-row">
                        <div class="form-group">
                            <label>نام <span class="required">*</span></label>
                            <input type="text" name="first_name" class="form-input" required placeholder="نام" value="<?php echo e(old('first_name') ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>نام خانوادگی <span class="required">*</span></label>
                            <input type="text" name="last_name" class="form-input" required placeholder="نام خانوادگی" value="<?php echo e(old('last_name') ?? ''); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>کد ملی <span class="required">*</span></label>
                        <input type="text" name="national_code" class="form-input" required maxlength="10" placeholder="کد ملی ۱۰ رقمی" value="<?php echo e(old('national_code') ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label>تلفن <span class="required">*</span></label>
                        <input type="tel" name="phone" class="form-input" required placeholder="شماره تلفن" value="<?php echo e(old('phone') ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label>ایمیل</label>
                        <input type="email" name="email" class="form-input" placeholder="ایمیل (اختیاری)" value="<?php echo e(old('email') ?? ''); ?>" dir="ltr" style="text-align:right;">
                    </div>

                    <div class="form-group">
                        <label>تاریخ تولد</label>
                        <input type="text" name="birth_date" class="form-input jalali-date" data-jalali placeholder="انتخاب تاریخ" value="<?php echo e(old('birth_date') ?? ''); ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label>آدرس</label>
                        <textarea name="address" class="form-textarea" rows="2" placeholder="آدرس محل سکونت"><?php echo e(old('address') ?? ''); ?></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>تماس اضطراری</label>
                            <input type="text" name="emergency_contact" class="form-input" placeholder="نام شخص" value="<?php echo e(old('emergency_contact') ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>تلفن اضطراری</label>
                            <input type="tel" name="emergency_phone" class="form-input" placeholder="شماره تلفن" value="<?php echo e(old('emergency_phone') ?? ''); ?>">
                        </div>
                    </div>

                    <div style="margin-top:24px;">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check" style="margin-left:8px;"></i>
                            ثبت‌نام
                        </button>
                    </div>
                </form>

                <div class="auth-footer">
                    قبلاً ثبت‌نام کرده‌اید؟ <a href="<?php echo url('auth/login'); ?>">وارد شوید</a>
                </div>
            </div>
        </div>
    </div>
    <script src="<?php echo asset('js/jquery.min.js'); ?>"></script>
    <script src="<?php echo asset('js/app.js'); ?>"></script>
</body>
</html>