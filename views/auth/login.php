<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ورود به باشگاه موج</title>
    <link rel="stylesheet" href="assets/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet" type="text/css" />
    <style>
        :root {
            --ios-blue: #007AFF;
            --ios-gray: #F2F2F7;
            --ios-text: #1C1C1E;
            --ios-subtext: #8E8E93;
            --ios-border: #C6C6C8;
            --shadow-sm: 0 2px 8px rgba(0,0,0,0.04);
            --shadow-md: 0 8px 24px rgba(0,0,0,0.08);
            --radius-lg: 16px;
            --radius-md: 12px;
        }

        body {
            font-family: 'Vazirmatn', sans-serif;
            background-color: var(--ios-gray);
            color: var(--ios-text);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            overflow: hidden;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
            padding: 20px;
            animation: fadeIn 0.6s ease-out;
        }

        .login-card {
            background: #ffffff;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            padding: 40px 30px;
            text-align: center;
            border: 1px solid rgba(255,255,255,0.8);
        }

        .brand-logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #007AFF, #0055b3);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            color: white;
            font-size: 36px;
            box-shadow: 0 10px 20px rgba(0,122,255,0.25);
        }

        h2 {
            font-weight: 800;
            font-size: 24px;
            margin-bottom: 8px;
            color: var(--ios-text);
        }

        p.subtitle {
            color: var(--ios-subtext);
            font-size: 14px;
            margin-bottom: 30px;
        }

        .form-floating > .form-control {
            border: 1px solid var(--ios-border);
            border-radius: var(--radius-md);
            height: 55px;
            padding: 12px 15px;
            font-size: 15px;
            background-color: #FAFAFA;
            transition: all 0.2s ease;
        }

        .form-floating > .form-control:focus {
            background-color: #fff;
            border-color: var(--ios-blue);
            box-shadow: 0 0 0 4px rgba(0,122,255,0.1);
        }

        .form-floating > label {
            padding: 12px 15px;
            color: var(--ios-subtext);
        }

        .btn-login {
            background: var(--ios-blue);
            border: none;
            border-radius: var(--radius-md);
            height: 55px;
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin-top: 20px;
            box-shadow: 0 4px 12px rgba(0,122,255,0.3);
            transition: transform 0.1s ease, box-shadow 0.2s ease;
        }

        .btn-login:hover {
            background: #0066d6;
            box-shadow: 0 6px 16px rgba(0,122,255,0.4);
            transform: translateY(-1px);
        }

        .btn-login:active {
            transform: scale(0.98);
        }

        .forgot-password {
            display: block;
            margin-top: 15px;
            color: var(--ios-blue);
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
        }

        .alert {
            border-radius: var(--radius-md);
            font-size: 14px;
            border: none;
            box-shadow: var(--shadow-sm);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 30px 20px;
            }
            h2 { font-size: 22px; }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="brand-logo">
                <i class="fa fa-water"></i>
            </div>
            <h2>باشگاه موج</h2>
            <p class="subtitle">برای ادامه وارد حساب خود شوید</p>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fa fa-exclamation-circle ms-2"></i><?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="username" name="username" placeholder="نام کاربری" required autofocus>
                    <label for="username">نام کاربری</label>
                </div>
                
                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="password" name="password" placeholder="رمز عبور" required>
                    <label for="password">رمز عبور</label>
                </div>

                <button type="submit" class="btn btn-primary w-100 btn-login">
                    ورود به پنل
                </button>

                <a href="#" class="forgot-password">رمز عبور را فراموش کرده‌اید؟</a>
            </form>
        </div>
        <div class="text-center mt-4 text-muted" style="font-size: 12px;">
            &copy; <?= date('Y') ?> سیستم مدیریت باشگاه موج
        </div>
    </div>
</body>
</html>
