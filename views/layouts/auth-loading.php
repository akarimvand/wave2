<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ورود به سیستم</title>
    <link href="<?php echo asset('fonts/vazirmatn.css'); ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo asset('css/all.min.css'); ?>">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Vazirmatn', Tahoma, Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            overflow: hidden;
        }
        .loading-container {
            text-align: center;
            position: relative;
            z-index: 1;
        }
        /* Animated background circles */
        .bg-circle {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            pointer-events: none;
            z-index: 0;
        }
        .bg-circle-1 {
            width: 400px; height: 400px;
            background: rgba(59, 130, 246, 0.15);
            top: -100px; right: -80px;
            animation: floatBg 8s ease-in-out infinite;
        }
        .bg-circle-2 {
            width: 300px; height: 300px;
            background: rgba(139, 92, 246, 0.12);
            bottom: -80px; left: -60px;
            animation: floatBg 10s ease-in-out infinite reverse;
        }
        @keyframes floatBg {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(20px, -20px); }
        }

        /* Avatar */
        .loading-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3B82F6, #8B5CF6);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 2.2rem;
            font-weight: 800;
            margin-bottom: 24px;
            position: relative;
            animation: avatarPulse 2s ease-in-out infinite;
        }
        .loading-avatar img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }
        .loading-avatar-ring {
            position: absolute;
            inset: -6px;
            border-radius: 50%;
            border: 2px solid transparent;
            border-top-color: #3B82F6;
            border-right-color: #8B5CF6;
            animation: ringRotate 1.5s linear infinite;
        }
        @keyframes ringRotate {
            to { transform: rotate(360deg); }
        }
        @keyframes avatarPulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.4); }
            50% { box-shadow: 0 0 0 20px rgba(59, 130, 246, 0); }
        }

        /* Success checkmark - badge on avatar */
        .loading-check {
            position: absolute;
            bottom: 2px;
            left: 2px;
            width: 32px; height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #10B981, #059669);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 0.85rem;
            border: 3px solid #0f172a;
            z-index: 2;
            animation: checkBounce 0.6s ease 0.5s both;
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.5);
        }
        @keyframes checkBounce {
            0% { transform: scale(0); opacity: 0; }
            50% { transform: scale(1.3); }
            100% { transform: scale(1); opacity: 1; }
        }

        /* User info */
        .loading-name {
            color: #f1f5f9;
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 8px;
            animation: fadeSlideUp 0.6s ease 0.5s both;
        }
        .loading-role {
            display: inline-block;
            background: rgba(59, 130, 246, 0.2);
            color: #93c5fd;
            padding: 4px 16px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 8px;
            animation: fadeSlideUp 0.6s ease 0.7s both;
        }
        .loading-club {
            color: #64748b;
            font-size: 0.85rem;
            margin-bottom: 32px;
            animation: fadeSlideUp 0.6s ease 0.9s both;
        }
        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Progress bar */
        .loading-progress-bar {
            width: 200px;
            height: 4px;
            background: rgba(255,255,255,0.1);
            border-radius: 2px;
            margin: 0 auto;
            overflow: hidden;
            animation: fadeSlideUp 0.6s ease 1.1s both;
        }
        .loading-progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #3B82F6, #8B5CF6);
            border-radius: 2px;
            animation: progressFill 2.2s ease-in-out forwards;
            animation-delay: 0.5s;
        }
        @keyframes progressFill {
            0% { width: 0%; }
            30% { width: 40%; }
            60% { width: 70%; }
            100% { width: 100%; }
        }
        .loading-text {
            color: #475569;
            font-size: 0.78rem;
            margin-top: 12px;
            animation: fadeSlideUp 0.6s ease 1.3s both;
        }

        /* Reduce motion */
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation-duration: 0.01s !important; }
        }
    </style>
</head>
<body>
    <div class="bg-circle bg-circle-1"></div>
    <div class="bg-circle bg-circle-2"></div>

    <div class="loading-container">
        <?php
        $user = auth()->user();
        $fullName = $user['full_name'] ?? $user['username'] ?? 'کاربر';
        $avatarPath = $user['avatar_path'] ?? '';
        $initials = mb_substr($fullName, 0, 1);

        $roleMap = [
            'admin' => 'مدیر سیستم',
            'manager' => 'مدیر عامل',
            'receptionist' => 'رسپشن',
            'accountant' => 'حسابدار',
            'member' => 'عضو باشگاه',
            'coach' => 'مربی',
        ];
        $roles = $user['roles'] ?? [];
        $roleLabel = 'کاربر';
        foreach ($roles as $r) {
            if (isset($roleMap[$r])) { $roleLabel = $roleMap[$r]; break; }
        }

        $clubName = function_exists('getSetting') ? getSetting('club_name', APP_NAME) : APP_NAME;
        ?>

        <div class="loading-avatar">
            <div class="loading-check">
                <i class="fas fa-check"></i>
            </div>
            <?php if (!empty($avatarPath)): ?>
            <img src="<?php echo asset($avatarPath); ?>" alt="<?php echo e($fullName); ?>">
            <?php else: ?>
            <?php echo e($initials); ?>
            <?php endif; ?>
            <div class="loading-avatar-ring"></div>
        </div>

        <div class="loading-name"><?php echo e($fullName); ?></div>
        <div class="loading-role"><i class="fas fa-shield-halved" style="margin-left:6px;"></i><?php echo e($roleLabel); ?></div>
        <div class="loading-club"><?php echo e($clubName); ?></div>

        <div class="loading-progress-bar">
            <div class="loading-progress-fill"></div>
        </div>
        <div class="loading-text">در حال بارگذاری...</div>
    </div>

    <script>
        setTimeout(function() {
            window.location.href = '<?php echo url($redirectUrl ?? 'admin/dashboard'); ?>';
        }, 2800);
    </script>
</body>
</html>