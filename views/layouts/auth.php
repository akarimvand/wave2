<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? e($pageTitle) . ' | ' : ''; ?><?php echo APP_NAME; ?></title>

    <!-- Favicon -->
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🏊</text></svg>">

    <!-- Local Vazirmatn Font -->
    <link href="<?php echo asset('fonts/vazirmatn.css'); ?>" rel="stylesheet">

    <!-- Local Font Awesome -->
    <link rel="stylesheet" href="<?php echo asset('css/all.min.css'); ?>">

    <!-- UI/UX Enhancements -->
    <link rel="stylesheet" href="<?php echo asset('css/ui-ux-enhancements.css'); ?>">

    <style>
        /* ===== Base Reset ===== */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { direction: rtl; font-size: 16px; }
        body {
            font-family: 'Vazirmatn', 'Vazir', 'Segoe UI', Tahoma, Arial, sans-serif;
            line-height: 1.7;
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
            margin: 0;
        }
        a { text-decoration: none; color: inherit; }
        button { font-family: inherit; cursor: pointer; border: none; background: none; }
        input, select, textarea { font-family: inherit; }

        /* ===== Login Page Wrapper - Gradient Background ===== */
        .login-page-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0A3178 0%, #0E4298 25%, #1877F2 55%, #1565D8 75%, #4293FF 100%);
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
        }

        /* ===== Animated Decorative Blur Circles ===== */
        .login-bg-circle {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            pointer-events: none;
            z-index: 0;
        }
        .login-bg-circle-1 {
            width: 500px; height: 500px;
            background: rgba(255, 255, 255, 0.08);
            top: -150px; right: -120px;
            animation: floatCircle1 8s ease-in-out infinite;
        }
        .login-bg-circle-2 {
            width: 350px; height: 350px;
            background: rgba(66, 147, 255, 0.15);
            bottom: -100px; left: -80px;
            animation: floatCircle2 10s ease-in-out infinite;
        }
        .login-bg-circle-3 {
            width: 200px; height: 200px;
            background: rgba(255, 255, 255, 0.06);
            top: 40%; left: 10%;
            animation: floatCircle3 12s ease-in-out infinite;
        }
        .login-bg-circle-4 {
            width: 280px; height: 280px;
            background: rgba(24, 119, 242, 0.1);
            top: 15%; right: 20%;
            animation: floatCircle4 9s ease-in-out infinite;
        }
        .login-bg-circle-5 {
            width: 150px; height: 150px;
            background: rgba(255, 255, 255, 0.1);
            bottom: 25%; right: 5%;
            animation: floatCircle5 7s ease-in-out infinite;
        }

        @keyframes floatCircle1 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(-30px, 40px) scale(1.05); }
        }
        @keyframes floatCircle2 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(40px, -30px) scale(1.08); }
        }
        @keyframes floatCircle3 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(20px, -25px) scale(0.95); }
        }
        @keyframes floatCircle4 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(-25px, 20px) scale(1.03); }
        }
        @keyframes floatCircle5 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(15px, 30px) scale(1.1); }
        }

        /* ===== Glassmorphism Card ===== */
        .login-card-fixed {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(32px);
            -webkit-backdrop-filter: blur(32px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 24px;
            padding: 2.5rem 2.25rem;
            width: 100%;
            max-width: 440px;
            box-shadow:
                0 20px 60px rgba(0, 0, 0, 0.15),
                0 0 0 1px rgba(255, 255, 255, 0.3) inset;
            animation: loginCardScaleIn 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            position: relative;
            z-index: 1;
        }
        @keyframes loginCardScaleIn {
            from { opacity: 0; transform: scale(0.9) translateY(20px); }
            to   { opacity: 1; transform: scale(1) translateY(0); }
        }

        /* ===== Logo Area ===== */
        .login-logo-area {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-logo-icon {
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
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .login-logo-icon:hover {
            transform: scale(1.08) rotate(-3deg);
            box-shadow: 0 12px 32px rgba(10, 49, 120, 0.45);
        }
        .login-card-fixed h1 {
            color: #1f2937;
            font-size: 1.3rem;
            font-weight: 800;
            margin-bottom: 0.3rem;
            line-height: 1.5;
            letter-spacing: 1px;
        }
        .login-subtitle-fixed {
            color: #6b7280;
            font-size: 0.85rem;
            text-align: center;
            margin-bottom: 0;
        }

        /* ===== Page Title (for register page) ===== */
        .auth-page-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1f2937;
            text-align: center;
            margin-bottom: 1.5rem;
        }

        /* ===== Form Controls ===== */
        .form-group {
            margin-bottom: 1.15rem;
        }
        .form-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.35rem;
        }
        .form-group label .required {
            color: #ef4444;
        }

        /* Input wrapper with icon */
        .login-input-wrapper {
            position: relative;
        }
        .login-input-wrapper .input-icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 0.9rem;
            pointer-events: none;
            transition: color 0.2s ease;
            z-index: 1;
        }
        .login-input-wrapper .form-control {
            width: 100%;
            padding: 0.7rem 0.85rem;
            padding-right: 2.5rem;
            border: 1.5px solid #e5e7eb;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.95);
            color: #1f2937;
            font-family: 'Vazirmatn', 'Vazir', 'Segoe UI', Tahoma, Arial, sans-serif;
            font-size: 0.9rem;
            direction: rtl;
            transition: all 0.25s ease;
            outline: none;
            height: 46px;
        }
        .login-input-wrapper .form-control.ltr-input {
            direction: ltr;
            text-align: left;
            padding-right: 0.85rem;
            padding-left: 2.5rem;
        }
        .login-input-wrapper .form-control.ltr-input ~ .input-icon {
            right: auto;
            left: 12px;
        }
        .login-input-wrapper .form-control::placeholder {
            color: #9ca3af;
            font-size: 0.85rem;
        }
        .login-input-wrapper .form-control:focus {
            border-color: #1877F2;
            box-shadow: 0 0 0 3px rgba(24, 119, 242, 0.15);
            background: #fff;
        }
        .login-input-wrapper .form-control:focus ~ .input-icon {
            color: #1877F2;
        }

        /* Plain input (no icon, for register page) */
        .form-control-plain {
            width: 100%;
            padding: 0.7rem 0.85rem;
            border: 1.5px solid #e5e7eb;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.95);
            color: #1f2937;
            font-family: 'Vazirmatn', 'Vazir', 'Segoe UI', Tahoma, Arial, sans-serif;
            font-size: 0.9rem;
            direction: rtl;
            transition: all 0.25s ease;
            outline: none;
            height: 46px;
        }
        .form-control-plain::placeholder {
            color: #9ca3af;
            font-size: 0.85rem;
        }
        .form-control-plain:focus {
            border-color: #1877F2;
            box-shadow: 0 0 0 3px rgba(24, 119, 242, 0.15);
            background: #fff;
        }

        /* Password toggle button */
        .password-toggle {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #9ca3af;
            cursor: pointer;
            font-size: 0.9rem;
            padding: 4px;
            transition: color 0.2s ease;
            z-index: 2;
        }
        .password-toggle:hover {
            color: #6b7280;
        }

        /* Form row (two columns) */
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        /* Remember me checkbox */
        .login-remember {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }
        .login-remember input[type="checkbox"] {
            width: 18px; height: 18px;
            accent-color: #1877F2;
            cursor: pointer;
            border-radius: 4px;
        }
        .login-remember label {
            font-size: 0.85rem;
            color: #4b5563;
            cursor: pointer;
            user-select: none;
        }

        /* ===== Login / Submit Button ===== */
        .login-btn {
            width: 100%;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 12px;
            background: linear-gradient(135deg, #1877F2, #0A3178);
            color: #fff;
            font-family: 'Vazirmatn', 'Vazir', 'Segoe UI', Tahoma, Arial, sans-serif;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.25s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            box-shadow: 0 4px 16px rgba(24, 119, 242, 0.35);
            position: relative;
            overflow: hidden;
            height: 48px;
        }
        .login-btn::before {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
            transition: left 0.5s ease;
        }
        .login-btn:hover:not(:disabled)::before {
            left: 100%;
        }
        .login-btn:hover:not(:disabled) {
            background: linear-gradient(135deg, #4293FF, #1877F2);
            box-shadow: 0 6px 24px rgba(24, 119, 242, 0.45);
            transform: translateY(-2px);
        }
        .login-btn:active:not(:disabled) {
            transform: translateY(0);
            box-shadow: 0 2px 8px rgba(24, 119, 242, 0.3);
        }
        .login-btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }
        .login-btn .btn-spinner {
            width: 18px; height: 18px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
            display: none;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* ===== Flash / Error Alert ===== */
        .login-error-fixed {
            background: rgba(239, 68, 68, 0.08);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #b91c1c;
            padding: 0.7rem 1rem;
            border-radius: 12px;
            font-size: 0.85rem;
            margin-bottom: 1.25rem;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            animation: loginErrorShake 0.4s ease;
        }
        .login-success-fixed {
            background: rgba(16, 185, 129, 0.08);
            border: 1px solid rgba(16, 185, 129, 0.2);
            color: #065f46;
            padding: 0.7rem 1rem;
            border-radius: 12px;
            font-size: 0.85rem;
            margin-bottom: 1.25rem;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            animation: loginCardScaleIn 0.3s ease;
        }
        @keyframes loginErrorShake {
            0%, 100% { transform: translateX(0); }
            20% { transform: translateX(-6px); }
            40% { transform: translateX(6px); }
            60% { transform: translateX(-4px); }
            80% { transform: translateX(4px); }
        }

        /* ===== Footer ===== */
        .auth-footer {
            text-align: center;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(0,0,0,0.06);
            font-size: 0.85rem;
            color: #6b7280;
        }
        .auth-footer a {
            color: #1877F2;
            font-weight: 600;
            transition: color 0.2s ease;
        }
        .auth-footer a:hover {
            color: #0A3178;
            text-decoration: underline;
        }

        .login-footer-fixed {
            text-align: center;
            margin-top: 1.75rem;
            color: #6b7280;
            font-size: 0.75rem;
        }
        .login-footer-fixed a {
            color: #1877F2;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .login-footer-fixed a:hover {
            text-decoration: underline;
            color: #0A3178;
        }

        /* ===== Mobile Optimization ===== */
        @media (max-width: 480px) {
            .login-page-wrapper {
                padding: 1rem;
                align-items: flex-start;
                padding-top: 2rem;
            }
            .login-card-fixed {
                padding: 2rem 1.5rem;
                border-radius: 18px;
                max-width: 100%;
            }
            .login-card-fixed h1 { font-size: 1.15rem; }
            .login-logo-icon {
                width: 56px; height: 56px;
                font-size: 1.5rem;
                border-radius: 14px;
            }
            .form-row { grid-template-columns: 1fr; }
            .login-bg-circle-1,
            .login-bg-circle-2 {
                display: none;
            }
            .login-bg-circle-3,
            .login-bg-circle-4,
            .login-bg-circle-5 {
                opacity: 0.4;
            }
        }

        /* ===== Extra Small Devices ===== */
        @media (max-width: 360px) {
            .login-card-fixed {
                padding: 1.75rem 1.25rem;
            }
            .login-input-wrapper .form-control,
            .form-control-plain {
                height: 44px;
                font-size: 0.85rem;
            }
            .login-btn {
                height: 46px;
                font-size: 0.9rem;
            }
            .login-logo-icon {
                width: 50px; height: 50px;
                font-size: 1.3rem;
            }
        }

        /* ===== Landscape Mode on Mobile ===== */
        @media (max-height: 500px) and (orientation: landscape) {
            .login-page-wrapper {
                align-items: center;
                padding: 1rem;
            }
            .login-card-fixed {
                padding: 1.5rem;
                max-width: 500px;
            }
            .login-logo-area {
                margin-bottom: 1rem;
            }
            .login-logo-icon {
                width: 48px; height: 48px;
                font-size: 1.3rem;
            }
            .form-group {
                margin-bottom: 0.85rem;
            }
            .login-footer-fixed {
                margin-top: 1rem;
            }
        }

        /* ===== Tablet Optimization ===== */
        @media (min-width: 481px) and (max-width: 768px) {
            .login-card-fixed {
                max-width: 400px;
            }
        }

        /* ===== Dark Mode Support ===== */
        @media (prefers-color-scheme: dark) {
            .login-card-fixed {
                background: rgba(30, 41, 59, 0.88);
                border-color: rgba(255, 255, 255, 0.1);
            }
            .login-card-fixed h1 {
                color: #f3f4f6;
            }
            .login-subtitle-fixed,
            .login-footer-fixed,
            .auth-footer {
                color: #9ca3af;
            }
            .form-group label {
                color: #e5e7eb;
            }
            .login-input-wrapper .form-control,
            .form-control-plain {
                background: rgba(55, 65, 81, 0.95);
                color: #f3f4f6;
                border-color: rgba(75, 85, 99, 0.5);
            }
            .login-input-wrapper .form-control::placeholder,
            .form-control-plain::placeholder {
                color: #6b7280;
            }
            .login-input-wrapper .input-icon,
            .password-toggle {
                color: #6b7280;
            }
            .login-remember label {
                color: #d1d5db;
            }
            .auth-footer a:hover {
                color: #60a5fa;
            }
            .login-footer-fixed a:hover {
                color: #60a5fa;
            }
        }

        /* ===== High Contrast Mode ===== */
        @media (prefers-contrast: high) {
            .login-card-fixed {
                border: 2px solid #000;
            }
            .login-input-wrapper .form-control,
            .form-control-plain {
                border-width: 2px;
            }
        }

        /* ===== Reduced Motion ===== */
        @media (prefers-reduced-motion: reduce) {
            .login-bg-circle, .login-card-fixed, .login-error-fixed, .login-success-fixed {
                animation: none !important;
            }
            .login-btn::before { display: none; }
        }

        /* ===== Custom select arrow ===== */
        select.form-control-plain {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%236b7280' viewBox='0 0 16 16'%3E%3Cpath d='M8 11L3 6h10l-5 5z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: left 14px center;
            padding-left: 36px;
            cursor: pointer;
        }

        /* ===== Mobile-friendly Datepicker ===== */
        .form-control-plain[readonly] {
            cursor: pointer;
        }
        @media (max-width: 480px) {
            .datepicker-plot-area {
                font-size: 13px !important;
            }
            .datepicker-plot-area .table-days td {
                width: 36px !important;
                height: 36px !important;
                line-height: 36px !important;
                font-size: 13px !important;
            }
            .datepicker-plot-area .table-days td span {
                width: 32px !important;
                height: 32px !important;
                line-height: 32px !important;
            }
            .datepicker-plot-area .header .month-box select,
            .datepicker-plot-area .header .year-box select {
                font-size: 13px !important;
                padding: 4px !important;
            }
        }
    </style>
</head>
<body>
    <div class="login-page-wrapper">
        <!-- Animated decorative blur circles -->
        <div class="login-bg-circle login-bg-circle-1"></div>
        <div class="login-bg-circle login-bg-circle-2"></div>
        <div class="login-bg-circle login-bg-circle-3"></div>
        <div class="login-bg-circle login-bg-circle-4"></div>
        <div class="login-bg-circle login-bg-circle-5"></div>

        <!-- Glassmorphism Login Card -->
        <div class="login-card-fixed">
            <!-- Logo & Title -->
            <div class="login-logo-area">
                <div class="login-logo-icon">
                    <i class="fas fa-water"></i>
                </div>
                <h1>WAVE CLUB</h1>
                <p class="login-subtitle-fixed">باشگاه ویو کنگان — سیستم مدیریت هوشمند</p>
            </div>

            <!-- Flash Messages -->
            <?php
            $flash = getFlash();
            if ($flash):
                $isError = ($flash['type'] === 'error' || $flash['type'] === 'danger');
                $icon = $isError ? 'fa-circle-exclamation' : 'fa-circle-check';
                $cssClass = $isError ? 'login-error-fixed' : 'login-success-fixed';
            ?>
                <div class="<?php echo $cssClass; ?>">
                    <i class="fas <?php echo $icon; ?>"></i>
                    <span><?php echo e($flash['message']); ?></span>
                </div>
            <?php endif; ?>

            <!-- Dynamic Content (login/register form) -->
            <?php if (isset($content)) echo $content; ?>

            <!-- Footer -->
            <div class="login-footer-fixed">
                <div style="margin-bottom: 0.35rem;">
                    <?php $authClubName = function_exists('getSetting') ? getSetting('club_name', APP_NAME) : APP_NAME; ?>
                    <?php echo e($authClubName); ?> — نسخه ۳.۰.۰
                </div>
                <div style="font-size: 0.72rem; opacity: 0.8;">تمامی حقوق محفوظ است © <?php echo date('Y'); ?></div>
            </div>
        </div>
    </div>

    <!-- Local Scripts -->
    <script src="<?php echo asset('js/jquery.min.js'); ?>"></script>
    <script src="<?php echo asset('js/bootstrap.bundle.min.js'); ?>"></script>
    <script src="<?php echo asset('js/app.js'); ?>"></script>
    <!-- Local Scripts -->
    <script src="<?php echo asset('js/jquery.min.js'); ?>"></script>
    <script src="<?php echo asset('js/bootstrap.bundle.min.js'); ?>"></script>
    <script src="<?php echo asset('js/app.js'); ?>"></script>

    <!-- UI/UX Enhancements -->
    <script src="<?php echo asset('js/ui-ux-enhancements.js'); ?>"></script>

    <!-- Enhanced Login UX Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Password visibility toggle enhancement
            const passwordToggles = document.querySelectorAll('.password-toggle');
            passwordToggles.forEach(toggle => {
                toggle.addEventListener('click', function() {
                    const wrapper = this.closest('.login-input-wrapper');
                    const input = wrapper.querySelector('input');
                    const icon = this.querySelector('i');
                    
                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                        this.setAttribute('aria-label', 'پنهان کردن رمز عبور');
                        input.focus();
                    } else {
                        input.type = 'password';
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                        this.setAttribute('aria-label', 'نمایش رمز عبور');
                        input.focus();
                    }
                });
            });

            // Form submission with enhanced loading state
            const loginForm = document.getElementById('loginForm');
            if (loginForm) {
                loginForm.addEventListener('submit', function(e) {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    if (submitBtn && !submitBtn.disabled) {
                        const btnText = submitBtn.querySelector('#loginBtnText');
                        const btnIcon = submitBtn.querySelector('#loginBtnIcon');
                        const btnSpinner = submitBtn.querySelector('#loginBtnSpinner');
                        
                        if (btnText) btnText.textContent = 'در حال ورود...';
                        if (btnIcon) btnIcon.style.display = 'none';
                        if (btnSpinner) btnSpinner.style.display = 'inline-block';
                        
                        submitBtn.disabled = true;
                        submitBtn.setAttribute('aria-busy', 'true');
                    }
                });
            }

            // Input focus animations
            const inputs = document.querySelectorAll('.login-input-wrapper input, .form-control-plain');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('focused');
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.classList.remove('focused');
                });
            });

            // Auto-focus first empty field
            const usernameField = document.getElementById('username');
            const passwordField = document.getElementById('login-password');
            
            if (usernameField && !usernameField.value) {
                setTimeout(() => usernameField.focus(), 300);
            } else if (passwordField && !passwordField.value) {
                setTimeout(() => passwordField.focus(), 300);
            }

            // Keyboard accessibility - Enter key on password field
            if (passwordField) {
                passwordField.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        loginForm.submit();
                    }
                });
            }

            // Touch-friendly enhancements for mobile
            if ('ontouchstart' in window) {
                document.body.classList.add('touch-device');
                
                // Add touch feedback to buttons
                const buttons = document.querySelectorAll('.login-btn, .password-toggle');
                buttons.forEach(btn => {
                    btn.addEventListener('touchstart', function() {
                        this.style.transform = 'scale(0.98)';
                    }, { passive: true });
                    
                    btn.addEventListener('touchend', function() {
                        this.style.transform = '';
                    }, { passive: true });
                });
            }

            // Prevent zoom on double-tap for iOS
            const metaViewport = document.querySelector('meta[name="viewport"]');
            if (metaViewport && /iPad|iPhone|iPod/.test(navigator.userAgent)) {
                metaViewport.setAttribute('content', 'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no');
            }

            // Error message auto-dismiss after 5 seconds
            const errorMessages = document.querySelectorAll('.login-error-fixed');
            errorMessages.forEach(msg => {
                setTimeout(() => {
                    msg.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                    msg.style.opacity = '0';
                    msg.style.transform = 'translateY(-10px)';
                    setTimeout(() => msg.remove(), 300);
                }, 5000);
            });

            // Form validation feedback
            if (loginForm) {
                const formInputs = loginForm.querySelectorAll('input[required]');
                formInputs.forEach(input => {
                    input.addEventListener('invalid', function(e) {
                        e.preventDefault();
                        this.classList.add('is-invalid');
                        this.setCustomValidity('');
                        
                        // Show validation message
                        const wrapper = this.closest('.form-group');
                        if (wrapper) {
                            const existingMsg = wrapper.querySelector('.invalid-feedback');
                            if (!existingMsg) {
                                const feedback = document.createElement('div');
                                feedback.className = 'invalid-feedback';
                                feedback.textContent = this.validationMessage;
                                feedback.style.color = '#ef4444';
                                feedback.style.fontSize = '0.75rem';
                                feedback.style.marginTop = '0.25rem';
                                wrapper.appendChild(feedback);
                            }
                        }
                    });
                    
                    input.addEventListener('input', function() {
                        this.classList.remove('is-invalid');
                        const wrapper = this.closest('.form-group');
                        if (wrapper) {
                            const feedback = wrapper.querySelector('.invalid-feedback');
                            if (feedback) feedback.remove();
                        }
                    });
                });
            }
        });
    </script>
</body>
</html>
