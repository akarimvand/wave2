<?php
/**
 * Setup Script - Run this once to set up the database and admin user
 * Access: http://localhost/wave2/setup.php
 * DELETE THIS FILE AFTER USE!
 */

require_once __DIR__ . '/config.php';

$demoImported = false;
$demoMessage = '';

// Handle demo data import request
if (isset($_POST['install_demo']) && $_POST['install_demo'] === '1') {
    try {
        $db = Database::getInstance();
        $demoFile = __DIR__ . '/database/demo.sql';
        if (file_exists($demoFile)) {
            $demoSql = file_get_contents($demoFile);
            $cleanedDemo = preg_replace('/--[^\n]*/', '', $demoSql);
            $cleanedDemo = preg_replace('/\n\s*\n/', "\n", $cleanedDemo);
            $demoStatements = explode(';', $cleanedDemo);
            $demoExecuted = 0;
            $demoSkipped = 0;
            foreach ($demoStatements as $stmt) {
                $stmt = trim($stmt);
                if (empty($stmt)) continue;
                try {
                    $db->query($stmt);
                    $demoExecuted++;
                } catch (Exception $e) {
                    $demoSkipped++;
                    $msg = $e->getMessage();
                    if (strpos($msg, 'Duplicate') !== false || strpos($msg, 'already exists') !== false) {
                        continue;
                    }
                    // Silently skip individual demo insert errors
                }
            }
            $demoImported = true;
            $demoMessage = $demoExecuted . ' دستور دمو اجرا شد';
        } else {
            $demoMessage = 'فایل demo.sql یافت نشد';
        }
    } catch (Exception $e) {
        $demoMessage = 'خطا: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    }
}

// Handle full setup (schema + updates + admin + optional demo)
if (isset($_POST['full_setup']) && $_POST['full_setup'] === '1') {
    $loadDemo = isset($_POST['load_demo']) && $_POST['load_demo'] === '1';

    // Step 1: Create database
    try {
        $pdo = new PDO('mysql:host=' . DB_HOST, DB_USER, DB_PASS);
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    } catch (Exception $e) {
        die('خطا در ایجاد دیتابیس: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
    }

    // Step 2: Run schema
    $db = Database::getInstance();
    $schema = file_get_contents(__DIR__ . '/database/schema.sql');
    $cleaned = preg_replace('/--[^\n]*/', '', $schema);
    $cleaned = preg_replace('/\n\s*\n/', "\n", $cleaned);
    $statements = explode(';', $cleaned);
    foreach ($statements as $stmt) {
        $stmt = trim($stmt);
        if (empty($stmt)) continue;
        try { $db->query($stmt); } catch (Exception $e) {
            $msg = $e->getMessage();
            if (strpos($msg, 'Duplicate') === false && strpos($msg, 'already exists') === false) {
                // Non-critical
            }
        }
    }

    // Step 3: Update admin password
    $hash = password_hash('admin123', PASSWORD_DEFAULT);
    try { $db->update('users', ['password_hash' => $hash], ['username' => 'admin']); } catch (Exception $e) {}

    // Step 3.5: Run schema updates
    $updatesFile = __DIR__ . '/database/updates.sql';
    if (file_exists($updatesFile)) {
        $updates = file_get_contents($updatesFile);
        $cleanedUpdates = preg_replace('/--[^\n]*/', '', $updates);
        $cleanedUpdates = preg_replace('/\n\s*\n/', "\n", $cleanedUpdates);
        $updateStatements = explode(';', $cleanedUpdates);
        foreach ($updateStatements as $stmt) {
            $stmt = trim($stmt);
            if (empty($stmt)) continue;
            try { $db->query($stmt); } catch (Exception $e) {
                $msg = $e->getMessage();
                if (strpos($msg, 'Duplicate') !== false || strpos($msg, 'already exists') !== false || strpos($msg, 'check constraint') !== false) continue;
            }
        }
    }

    // Step 4: Assign admin role
    try {
        $adminUser = $db->selectOne('users', ['username' => 'admin']);
        $adminRole = $db->selectOne('roles', ['name' => 'admin']);
        if ($adminUser && $adminRole) {
            $existing = $db->selectOne('user_roles', ['user_id' => $adminUser['id'], 'role_id' => $adminRole['id']]);
            if (!$existing) {
                $db->insert('user_roles', ['user_id' => $adminUser['id'], 'role_id' => $adminRole['id']]);
            }
        }
    } catch (Exception $e) {}

    // Step 5: Load demo data if requested
    if ($loadDemo) {
        $demoFile = __DIR__ . '/database/demo.sql';
        if (file_exists($demoFile)) {
            $demoSql = file_get_contents($demoFile);
            $cleanedDemo = preg_replace('/--[^\n]*/', '', $demoSql);
            $cleanedDemo = preg_replace('/\n\s*\n/', "\n", $cleanedDemo);
            $demoStatements = explode(';', $cleanedDemo);
            foreach ($demoStatements as $stmt) {
                $stmt = trim($stmt);
                if (empty($stmt)) continue;
                try { $db->query($stmt); } catch (Exception $e) {
                    $msg = $e->getMessage();
                    if (strpos($msg, 'Duplicate') !== false || strpos($msg, 'already exists') !== false) continue;
                }
            }
            $demoImported = true;
        }
    }

    // Redirect to login
    header('Location: ' . url('auth/login'));
    exit;
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نصب سیستم مدیریت ویو کلاب</title>
    <link href="<?php echo asset('fonts/vazirmatn.css'); ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo asset('css/all.min.css'); ?>">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Vazirmatn', Tahoma, sans-serif;
            background: #f0f2f5;
            color: #1f2937;
            min-height: 100vh;
            padding: 2rem;
            line-height: 1.8;
        }
        .setup-container {
            max-width: 700px;
            margin: 0 auto;
        }
        .setup-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .setup-header .logo-icon {
            width: 64px; height: 64px;
            border-radius: 18px;
            background: linear-gradient(135deg, #1877F2, #0A3178);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.75rem;
            margin-bottom: 1rem;
            box-shadow: 0 8px 24px rgba(10, 49, 120, 0.35);
        }
        .setup-header h1 {
            font-size: 1.5rem;
            font-weight: 800;
            color: #1f2937;
            margin-bottom: 0.3rem;
        }
        .setup-header p {
            color: #6b7280;
            font-size: 0.9rem;
        }
        .setup-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            padding: 2rem;
            margin-bottom: 1.5rem;
        }
        .setup-card h2 {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #e5e7eb;
        }
        .setup-card h3 {
            font-size: 0.95rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.75rem;
        }
        .setup-card p, .setup-card li {
            font-size: 0.9rem;
            color: #4b5563;
            margin-bottom: 0.5rem;
        }
        .setup-card ul {
            list-style: none;
            padding: 0;
        }
        .setup-card ul li {
            padding: 0.35rem 0;
            padding-right: 1.5rem;
            position: relative;
        }
        .setup-card ul li::before {
            content: '✓';
            position: absolute;
            right: 0;
            color: #10b981;
            font-weight: bold;
        }
        .setup-card ul li i {
            margin-left: 0.4rem;
            color: #6b7280;
            font-size: 0.8rem;
        }
        .setup-form .form-group {
            margin-bottom: 1.25rem;
        }
        .setup-form label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            color: #374151;
            cursor: pointer;
            padding: 0.75rem 1rem;
            border: 1.5px solid #e5e7eb;
            border-radius: 12px;
            transition: all 0.2s ease;
        }
        .setup-form label:hover {
            border-color: #1877F2;
            background: rgba(24, 119, 242, 0.03);
        }
        .setup-form label input[type="checkbox"] {
            width: 20px; height: 20px;
            accent-color: #1877F2;
            cursor: pointer;
        }
        .setup-form label small {
            display: block;
            color: #9ca3af;
            font-size: 0.8rem;
            margin-top: 0.25rem;
        }
        .btn-setup {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.85rem 2rem;
            border: none;
            border-radius: 12px;
            font-family: 'Vazirmatn', Tahoma, sans-serif;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.25s ease;
            width: 100%;
        }
        .btn-setup-primary {
            background: linear-gradient(135deg, #1877F2, #0A3178);
            color: #fff;
            box-shadow: 0 4px 16px rgba(24, 119, 242, 0.35);
        }
        .btn-setup-primary:hover {
            background: linear-gradient(135deg, #4293FF, #1877F2);
            box-shadow: 0 6px 24px rgba(24, 119, 242, 0.45);
            transform: translateY(-2px);
        }
        .btn-setup-secondary {
            background: #f3f4f6;
            color: #374151;
            border: 1.5px solid #e5e7eb;
        }
        .btn-setup-secondary:hover {
            background: #e5e7eb;
            transform: translateY(-1px);
        }
        .alert-success {
            background: #f0fdf4;
            color: #065f46;
            border: 1px solid #bbf7d0;
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .alert-info {
            background: #eff6ff;
            color: #1e40af;
            border: 1px solid #bfdbfe;
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .alert-warning {
            background: #fffbeb;
            color: #92400e;
            border: 1px solid #fde68a;
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1rem;
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
        }
        .credentials-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0.75rem;
        }
        .credentials-table th, .credentials-table td {
            padding: 0.5rem 0.75rem;
            text-align: right;
            font-size: 0.85rem;
            border-bottom: 1px solid #f3f4f6;
        }
        .credentials-table th {
            background: #f9fafb;
            font-weight: 600;
            color: #374151;
        }
        .credentials-table td {
            color: #4b5563;
        }
        .credentials-table td code {
            background: #f3f4f6;
            padding: 0.15rem 0.5rem;
            border-radius: 4px;
            font-size: 0.82rem;
            color: #1f2937;
            direction: ltr;
            display: inline-block;
        }
        .setup-footer {
            text-align: center;
            margin-top: 2rem;
            color: #9ca3af;
            font-size: 0.8rem;
        }
        .btn-group {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }
        .btn-group .btn-setup {
            flex: 1;
        }
        @media (max-width: 640px) {
            body { padding: 1rem; }
            .setup-card { padding: 1.5rem; }
            .btn-group { flex-direction: column; }
        }
    </style>
</head>
<body>
    <div class="setup-container">
        <!-- Header -->
        <div class="setup-header">
            <div class="logo-icon">
                <i class="fas fa-water"></i>
            </div>
            <h1>نصب سیستم مدیریت ویو کلاب</h1>
            <p>مراحل نصب و راه‌اندازی پایگاه داده و تنظیمات اولیه</p>
        </div>

        <?php if ($demoImported): ?>
        <div class="alert-success">
            <i class="fas fa-check-circle" style="font-size: 1.2rem;"></i>
            <div>
                <strong>داده‌های دمو با موفقیت اضافه شدند!</strong>
                <br><small><?php echo e($demoMessage); ?></small>
            </div>
        </div>
        <?php endif; ?>

        <!-- What Setup Does -->
        <div class="setup-card">
            <h2><i class="fas fa-clipboard-list" style="margin-left: 0.5rem; color: #1877F2;"></i> مراحل نصب خودکار</h2>
            <p>با اجرای نصب، عملیات زیر به صورت خودکار انجام می‌شود:</p>
            <ul>
                <li>ایجاد دیتابیس <code>wave_club</code> در صورت عدم وجود</li>
                <li>ایجاد تمام جداول مورد نیاز سیستم</li>
                <li>اعمال بروزرسانی‌های جدول (روزهای کلاس، فیلدهای پزشکی و...)</li>
                <li>ایجاد کاربر مدیر با رمز عبور <code>admin123</code></li>
                <li>تخصیص نقش مدیر به کاربر ادمین</li>
                <li>ایجاد منوها، مجوزها و تنظیمات پیش‌فرض</li>
            </ul>
        </div>

        <!-- Demo Data Info -->
        <div class="setup-card">
            <h2><i class="fas fa-database" style="margin-left: 0.5rem; color: #f59e0b;"></i> داده‌های دمو (اختیاری)</h2>
            <p>در صورت تیک زدن گزینه زیر، داده‌های نمونه زیر نیز وارد دیتابیس می‌شوند:</p>
            <ul>
                <li><i>کاربران تستی با نقش‌های مختلف (مدیر، پذیرش، حسابدار، ۸ عضو)</i></li>
                <li><i>۸ عضو باشگاه با اطلاعات کامل (نام، کد ملی، آدرس، وضعیت)</i></li>
                <li><i>۶ طرح عضویت (ماهانه، سه ماهه، شش ماهه، سالانه و...)</i></li>
                <li><i>۵ مربی با تخصص‌های مختلف (فیتنس، یوگا، شنا، زومبا، کنگ‌فو)</i></li>
                <li><i>۸ کلاس فعال با روزها و ساعات برگزاری</i></li>
                <li><i>۱۲ ثبت‌نام کلاس برای اعضا</i></li>
                <li><i>۱۰ دستگاه تجهیزات با وضعیت نگهداری</i></li>
                <li><i>۵ شرکت بیمه و ۷ بیمه‌نامه عضو</i></li>
                <li><i>۱۳ تراکنش مالی (عضویت، بیمه، در انتظار)</i></li>
                <li><i>۵ رویداد آینده و ۱۱ ثبت‌نام رویداد</i></li>
                <li><i>۶ تیکت پشتیبانی با ۱۰ پاسخ</i></li>
                <li><i>۲۰ اعلان برای اعضا</i></li>
                <li><i>۱۵ لاگ فعالیت کاربران</i></li>
                <li><i>۴ تخفیف فعال برای طرح‌های عضویت</i></li>
            </ul>
        </div>

        <!-- Credentials Preview -->
        <div class="setup-card">
            <h2><i class="fas fa-key" style="margin-left: 0.5rem; color: #10b981;"></i> حساب‌های کاربری پس از نصب</h2>
            <table class="credentials-table">
                <thead>
                    <tr><th>نقش</th><th>نام کاربری</th><th>رمز عبور</th></tr>
                </thead>
                <tbody>
                    <tr><td>مدیر سیستم</td><td><code>admin</code></td><td><code>admin123</code></td></tr>
                </tbody>
            </table>
            <h3 style="margin-top: 1rem;" id="demo-creds" class="<?php echo $demoImported ? '' : ''; ?>">
                <i class="fas fa-star" style="color: #f59e0b;"></i>
                حساب‌های دمو <span style="color: #9ca3af; font-weight: 400; font-size: 0.8rem;">(با نصب داده‌های دمو)</span>
            </h3>
            <table class="credentials-table">
                <thead>
                    <tr><th>نقش</th><th>نام کاربری</th><th>رمز عبور</th></tr>
                </thead>
                <tbody>
                    <tr><td>مدیر باشگاه</td><td><code>manager</code></td><td><code>123456</code></td></tr>
                    <tr><td>پذیرش</td><td><code>reception</code></td><td><code>123456</code></td></tr>
                    <tr><td>حسابدار</td><td><code>accountant</code></td><td><code>123456</code></td></tr>
                    <tr><td>عضو ۱</td><td><code>member1</code></td><td><code>123456</code></td></tr>
                    <tr><td>عضو ۲</td><td><code>member2</code></td><td><code>123456</code></td></tr>
                    <tr><td>عضو ۳</td><td><code>member3</code></td><td><code>123456</code></td></tr>
                </tbody>
            </table>
            <p style="margin-top: 0.5rem; font-size: 0.8rem; color: #9ca3af;">
                <i class="fas fa-info-circle"></i>
                عضو ۴ تا ۸ نیز با نام‌های <code>member4</code> تا <code>member8</code> و رمز <code>123456</code> ایجاد می‌شوند.
            </p>
        </div>

        <!-- Setup Form -->
        <div class="setup-card">
            <h2><i class="fas fa-play-circle" style="margin-left: 0.5rem; color: #1877F2;"></i> شروع نصب</h2>
            <form method="POST" class="setup-form">
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="load_demo" value="1" checked>
                        <div>
                            <strong>نصب داده‌های دمو</strong>
                            <small>توصیه می‌شود برای تست اولیه سیستم این گزینه فعال باشد</small>
                        </div>
                    </label>
                </div>

                <div class="btn-group">
                    <button type="submit" name="full_setup" value="1" class="btn-setup btn-setup-primary">
                        <i class="fas fa-rocket"></i>
                        نصب کامل سیستم
                    </button>
                    <a href="<?php echo url('auth/login'); ?>" class="btn-setup btn-setup-secondary" style="text-decoration:none;">
                        <i class="fas fa-sign-in-alt"></i>
                        ورود به سیستم
                    </a>
                </div>
            </form>
        </div>

        <!-- Warning -->
        <div class="alert-warning">
            <i class="fas fa-exclamation-triangle" style="margin-top: 3px;"></i>
            <div>
                <strong>توجه:</strong> پس از اتمام نصب، حتماً فایل <code>setup.php</code> را از سرور حذف کنید تا امنیت سیستم حفظ شود.
            </div>
        </div>

        <div class="setup-footer">
            سیستم مدیریت باشگاه ویو کنگان — نسخه ۳.۰.۰
        </div>
    </div>
</body>
</html>