<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? e($pageTitle) . ' | ' : ''; ?><?php echo APP_NAME; ?></title>

    <link href="<?php echo asset('fonts/vazirmatn.css'); ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo asset('css/all.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo asset('css/bootstrap.rtl.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo asset('css/persian-datepicker.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo asset('css/style.css'); ?>">
</head>
<body>
    <div class="app-layout">
        <div id="sidebar-overlay" class="sidebar-overlay"></div>

        <aside id="sidebar" class="sidebar">
            <div class="sidebar-brand">
                <?php $clubLogoC = getSetting('logo_path'); if ($clubLogoC): ?>
                <img src="<?php echo asset($clubLogoC); ?>" alt="<?php echo e(getSetting('club_name', 'WAVE')); ?>" class="sidebar-brand-logo">
                <?php else: ?>
                <div class="sidebar-brand-icon" style="background:var(--app-gradient);">W</div>
                <?php endif; ?>
                <div class="sidebar-brand-text">
                    <h1><?php echo e(getSetting('club_name', APP_NAME)); ?></h1>
                    <p>پنل مربیان</p>
                </div>
            </div>

            <nav class="sidebar-nav">
                <a href="<?php echo url('coach/dashboard'); ?>" class="<?php echo (isset($activeMenu) && $activeMenu === 'dashboard') ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i>
                    داشبورد
                </a>
                <a href="<?php echo url('coach/classes'); ?>" class="<?php echo (isset($activeMenu) && $activeMenu === 'classes') ? 'active' : ''; ?>">
                    <i class="fas fa-chalkboard-teacher"></i>
                    کلاس‌های من
                </a>
                <a href="<?php echo url('coach/attendance'); ?>" class="<?php echo (isset($activeMenu) && $activeMenu === 'attendance') ? 'active' : ''; ?>">
                    <i class="fas fa-clipboard-check"></i>
                    حضور و غیاب
                </a>
                <a href="<?php echo url('coach/profile'); ?>" class="<?php echo (isset($activeMenu) && $activeMenu === 'profile') ? 'active' : ''; ?>">
                    <i class="fas fa-user-cog"></i>
                    پروفایل
                </a>
                <a href="<?php echo url('coach/notifications'); ?>" class="<?php echo (isset($activeMenu) && $activeMenu === 'notifications') ? 'active' : ''; ?>">
                    <i class="fas fa-bell"></i>
                    اعلانات
                </a>
            </nav>

            <div class="sidebar-footer">
                <?php if (auth()->hasRole('admin') || auth()->hasRole('manager')): ?>
                    <a href="<?php echo url('admin/dashboard'); ?>" class="sidebar-footer-link">
                        <i class="fas fa-arrow-right"></i>
                        بازگشت به مدیریت
                    </a>
                <?php endif; ?>
                <a href="<?php echo url('auth/logout'); ?>" class="sidebar-footer-link sidebar-logout">
                    <i class="fas fa-sign-out-alt"></i>
                    خروج از حساب
                </a>
            </div>
        </aside>

        <main class="main-content">
            <header class="topbar">
                <div class="topbar-right">
                    <button id="sidebar-toggle" class="sidebar-toggle" type="button">
                        <i class="fas fa-bars"></i>
                    </button>
                    <span class="topbar-user" style="display:flex;align-items:center;gap:8px;">
                        <?php $avatarC = auth()->user()['avatar_path'] ?? ''; $uNameC = auth()->user()['full_name'] ?? auth()->user()['username'] ?? 'مربی'; ?>
                        <?php if (!empty($avatarC)): ?>
                        <img src="<?php echo asset($avatarC); ?>" alt="" style="width:32px;height:32px;border-radius:50%;object-fit:cover;border:2px solid var(--app-primary);">
                        <?php else: ?>
                        <span style="width:32px;height:32px;border-radius:50%;background:var(--app-gradient);display:inline-flex;align-items:center;justify-content:center;color:#fff;font-size:0.8rem;font-weight:700;flex-shrink:0;"><?php echo e(mb_substr($uNameC, 0, 1)); ?></span>
                        <?php endif; ?>
                        <?php echo e($uNameC); ?>
                    </span>
                </div>
                <div class="topbar-left">
                    <span style="font-size:0.8rem;color:#6B7280;margin-left:12px;">
                        <i class="fas fa-id-badge" style="margin-left:4px;color:var(--app-primary);"></i>
                        پنل مربیان
                    </span>
                    <a href="<?php echo url('auth/logout'); ?>" class="btn btn-sm btn-danger" onclick="return confirm('آیا مطمئن هستید؟')">
                        <i class="fas fa-sign-out-alt"></i>
                        خروج
                    </a>
                </div>
            </header>

            <div class="page-content">
                <?php echo renderFlash(); ?>
                <?php if (isset($content)) echo $content; ?>
            </div>

            <!-- Footer -->
            <footer class="app-footer">
                <div class="app-footer-content">
                    <?php if (getSetting('logo_path')): ?>
                    <img src="<?php echo asset(getSetting('logo_path')); ?>" alt="لوگو" class="app-footer-logo">
                    <?php endif; ?>
                    <span class="app-footer-name"><?php echo e(getSetting('club_name', APP_NAME)); ?></span>
                    <span class="app-footer-sep">|</span>
                    <span class="app-footer-copy">تمامی حقوق محفوظ است © <?php echo date('Y'); ?></span>
                </div>
            </footer>
        </main>
    </div>

    <script src="<?php echo asset('js/jquery.min.js'); ?>"></script>
    <script src="<?php echo asset('js/bootstrap.bundle.min.js'); ?>"></script>
    <script src="<?php echo asset('js/persian-datepicker.min.js'); ?>"></script>
    <script src="<?php echo asset('js/app.js'); ?>"></script>

    <script>
    (function(){
        var primary = <?php echo json_encode(getSetting('primary_color', '#1877F2')); ?>;
        var secondary = <?php echo json_encode(getSetting('secondary_color', '#0A3178')); ?>;
        if (!primary || primary.charAt(0) !== '#') return;
        function hexToRgb(hex){ var h=hex.replace('#',''); return [parseInt(h.slice(0,2),16),parseInt(h.slice(2,4),16),parseInt(h.slice(4,6),16)]; }
        function shift(c,a){ return Math.min(255,Math.max(0,c+a)); }
        function rgb(r,g,b){ return 'rgb('+r+','+g+','+b+')'; }
        function rgba(r,g,b,a){ return 'rgba('+r+','+g+','+b+','+a+')'; }
        var p = hexToRgb(primary);
        var s = hexToRgb(secondary);
        var lum = (0.299*p[0]+0.587*p[1]+0.114*p[2])/255;
        var isDark = lum < 0.55;
        var pLight = [shift(p[0],40), shift(p[1],40), shift(p[2],40)];
        var rootVars = {
            '--app-primary': primary, '--app-primary-rgb': p[0]+','+p[1]+','+p[2],
            '--app-primary-dark': secondary, '--app-primary-dark-rgb': s[0]+','+s[1]+','+s[2],
            '--app-primary-light': rgb.apply(null,pLight), '--app-primary-light-rgb': pLight[0]+','+pLight[1]+','+pLight[2],
            '--app-secondary': secondary, '--app-secondary-rgb': s[0]+','+s[1]+','+s[2],
            '--app-gradient': 'linear-gradient(135deg, '+primary+', '+secondary+')',
            '--app-gradient-reverse': 'linear-gradient(135deg, '+secondary+', '+primary+')'
        };
        var root = document.documentElement;
        for (var k in rootVars) root.style.setProperty(k, rootVars[k]);
        var sidebar = document.getElementById('sidebar');
        if (sidebar) {
            var sdR = Math.max(0, p[0]-70), sdG = Math.max(0, p[1]-70), sdB = Math.max(0, p[2]-70);
            var sideVars = {
                '--sidebar-bg':          rgb(sdR, sdG, sdB),
                '--sidebar-border':      isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)',
                '--sidebar-text-color':  isDark ? 'rgba(255,255,255,0.65)' : 'rgba(0,0,0,0.6)',
                '--sidebar-text-hover':  isDark ? 'rgba(255,255,255,0.95)' : 'rgba(0,0,0,0.9)',
                '--sidebar-text-active': isDark ? '#ffffff' : '#000000',
                '--sidebar-hover-bg':    isDark ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.06)',
                '--sidebar-active-bg':   isDark ? 'rgba(255,255,255,0.12)' : 'rgba(0,0,0,0.1)',
                '--sidebar-brand-sub':   isDark ? 'rgba(255,255,255,0.5)' : 'rgba(0,0,0,0.5)',
                '--sidebar-section-color': isDark ? 'rgba(255,255,255,0.3)' : 'rgba(0,0,0,0.3)',
                '--sidebar-divider':     isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)',
                '--sidebar-scrollbar':   isDark ? 'rgba(255,255,255,0.12)' : 'rgba(0,0,0,0.12)',
                '--sidebar-scrollbar-hover': isDark ? 'rgba(255,255,255,0.2)' : 'rgba(0,0,0,0.2)',
                '--sidebar-accent':      rgb(Math.min(255,p[0]+60), Math.min(255,p[1]+60), Math.min(255,p[2]+60)),
                '--sidebar-accent-glow': rgba(Math.min(255,p[0]+60), Math.min(255,p[1]+60), Math.min(255,p[2]+60), 0.15),
                '--sidebar-logout-color': '#f87171'
            };
            for (var k in sideVars) sidebar.style.setProperty(k, sideVars[k]);
        }
    })();
    </script>
</body>
</html>