<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>ورود به باشگاه موج</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --ios-bg: #F2F2F7;
            --ios-card: rgba(255, 255, 255, 0.85);
            --ios-blue: #007AFF;
            --ios-gray: #8E8E93;
            --ios-separator: #C6C6C8;
            --ios-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            --ios-radius: 24px;
            --input-bg: rgba(255, 255, 255, 0.5);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            font-family: 'Vazirmatn', sans-serif;
            background: linear-gradient(135deg, #e0f7fa 0%, #ffffff 50%, #e3f2fd 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
            position: relative;
        }

        .bg-shape {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            z-index: 0;
            animation: float 10s infinite ease-in-out;
        }

        .shape-1 {
            width: 300px;
            height: 300px;
            background: rgba(0, 122, 255, 0.15);
            top: -50px;
            left: -50px;
        }

        .shape-2 {
            width: 250px;
            height: 250px;
            background: rgba(52, 199, 89, 0.15);
            bottom: -50px;
            right: -50px;
            animation-delay: -5s;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(20px, 30px); }
        }

        .login-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 420px;
            padding: 20px;
        }

        .login-card {
            background: var(--ios-card);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: var(--ios-radius);
            padding: 40px 30px;
            box-shadow: var(--ios-shadow);
            border: 1px solid rgba(255, 255, 255, 0.6);
            text-align: center;
            transition: transform 0.3s ease;
        }

        .logo-area {
            margin-bottom: 30px;
        }

        .logo-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #007AFF, #0056b3);
            border-radius: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            box-shadow: 0 10px 20px rgba(0, 122, 255, 0.3);
            font-size: 36px;
            color: white;
        }

        h1 {
            font-size: 24px;
            font-weight: 700;
            color: #1c1c1e;
            margin-bottom: 8px;
        }

        p.subtitle {
            color: var(--ios-gray);
            font-size: 14px;
            margin-bottom: 30px;
        }

        .input-group {
            margin-bottom: 20px;
            text-align: right;
            position: relative;
        }

        .input-wrapper {
            position: relative;
            background: var(--input-bg);
            border-radius: 14px;
            transition: all 0.3s ease;
            border: 1px solid transparent;
        }

        .input-wrapper:focus-within {
            background: #fff;
            border-color: var(--ios-blue);
            box-shadow: 0 0 0 4px rgba(0, 122, 255, 0.1);
        }

        .input-wrapper i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--ios-gray);
            font-size: 18px;
            transition: color 0.3s;
        }

        .input-wrapper:focus-within i {
            color: var(--ios-blue);
        }

        input {
            width: 100%;
            padding: 16px 50px 16px 15px;
            border: none;
            background: transparent;
            font-family: 'Vazirmatn', sans-serif;
            font-size: 16px;
            color: #1c1c1e;
            outline: none;
        }

        input::placeholder {
            color: #aeaeb2;
        }

        .btn-login {
            width: 100%;
            padding: 16px;
            background: var(--ios-blue);
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 16px;
            font-weight: 600;
            font-family: 'Vazirmatn', sans-serif;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 12px rgba(0, 122, 255, 0.25);
            margin-top: 10px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 122, 255, 0.35);
            background: #006ee6;
        }

        .btn-login:active {
            transform: scale(0.98);
        }

        .forgot-password {
            margin-top: 20px;
            display: block;
            color: var(--ios-blue);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }

        .footer-text {
            margin-top: 30px;
            font-size: 12px;
            color: var(--ios-gray);
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 30px 20px;
                border-radius: 20px;
            }
            
            .logo-icon {
                width: 70px;
                height: 70px;
                font-size: 30px;
            }
        }
    </style>
</head>
<body>
    <div class="bg-shape shape-1"></div>
    <div class="bg-shape shape-2"></div>

    <div class="login-container">
        <div class="login-card">
            <div class="logo-area">
                <div class="logo-icon">
                    <i class="fas fa-water"></i>
                </div>
                <h1>باشگاه موج</h1>
                <p class="subtitle">برای ادامه وارد حساب خود شوید</p>
            </div>

            <?php if (isset($error)): ?>
                <div style="background: rgba(255, 59, 48, 0.1); color: #ff3b30; padding: 12px; border-radius: 12px; margin-bottom: 20px; font-size: 14px;">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="input-group">
                    <div class="input-wrapper">
                        <i class="fas fa-user"></i>
                        <input type="text" name="username" placeholder="نام کاربری" required autocomplete="username">
                    </div>
                </div>

                <div class="input-group">
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" placeholder="رمز عبور" required autocomplete="current-password">
                    </div>
                </div>

                <button type="submit" class="btn-login">
                    ورود به پنل
                </button>
            </form>

            <a href="#" class="forgot-password">رمز عبور را فراموش کرده‌اید؟</a>

            <div class="footer-text">
                © ۱۴۰۳ سیستم مدیریت باشگاه موج
            </div>
        </div>
    </div>
</body>
</html>
