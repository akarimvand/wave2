<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? e($pageTitle) . ' | ' : ''; ?><?php echo APP_NAME; ?></title>

    <!-- Local Vazirmatn Font -->
    <link href="<?php echo asset('fonts/vazirmatn.css'); ?>" rel="stylesheet">

    <!-- Local Font Awesome -->
    <link rel="stylesheet" href="<?php echo asset('css/all.min.css'); ?>">

    <!-- Local Bootstrap RTL -->
    <link rel="stylesheet" href="<?php echo asset('css/bootstrap.rtl.min.css'); ?>">

    <!-- Local Persian Datepicker -->
    <link rel="stylesheet" href="<?php echo asset('css/persian-datepicker.min.css'); ?>">

    <!-- Custom Styles -->
    <link rel="stylesheet" href="<?php echo asset('css/style.css'); ?>">
</head>
<body>
    <div class="app-layout">
        <!-- Sidebar Overlay (mobile) -->
        <div id="sidebar-overlay" class="sidebar-overlay"></div>

        <!-- Sidebar -->
        <aside id="sidebar" class="sidebar">
            <div class="sidebar-brand">
                <?php $clubLogo = getSetting('logo_path'); if ($clubLogo): ?>
                <img src="<?php echo asset($clubLogo); ?>" alt="<?php echo e(getSetting('club_name', 'WAVE')); ?>" class="sidebar-brand-logo">
                <?php else: ?>
                <div class="sidebar-brand-icon" style="background:var(--app-gradient);">W</div>
                <?php endif; ?>
                <div class="sidebar-brand-text">
                    <h1><?php echo e(getSetting('club_name', APP_NAME)); ?></h1>
                    <p>پنل مدیریت</p>
                </div>
            </div>

            <nav class="sidebar-nav">
                <?php $pendingCount = 0; try { $pc = db()->count('members', ['deleted_at' => null, 'approval_status' => 'pending']); $pendingCount = (int) $pc; } catch (Exception $e) {} ?>

                <!-- Dashboard -->
                <a href="<?php echo url('admin/dashboard'); ?>" class="<?php echo (isset($activeMenu) && $activeMenu === 'dashboard') ? 'active' : ''; ?>">
                    <i class="fas fa-th-large"></i>
                    داشبورد
                </a>
                <div class="sidebar-nav-divider"></div>

                <!-- مدیریت اعضا -->
                <div class="sidebar-accordion <?php echo in_array($activeMenu ?? '', ['members', 'memberships', 'assignments', 'insurance']) ? 'open' : ''; ?>">
                    <button type="button" class="sidebar-accordion-header" onclick="this.parentElement.classList.toggle('open')">
                        <i class="fas fa-users acc-icon"></i>
                        مدیریت اعضا
                        <i class="fas fa-chevron-down acc-arrow"></i>
                    </button>
                    <div class="sidebar-accordion-body">
                        <a href="<?php echo url('admin/members'); ?>" class="<?php echo ($activeMenu ?? '') === 'members' ? 'active' : ''; ?>">
                            اعضا
                            <?php if ($pendingCount > 0): ?><span class="nav-badge"><?php echo $pendingCount; ?></span><?php endif; ?>
                        </a>
                        <a href="<?php echo url('admin/memberships'); ?>" class="<?php echo ($activeMenu ?? '') === 'memberships' ? 'active' : ''; ?>">
                            اشتراک‌ها
                        </a>
                        <a href="<?php echo url('admin/assignments'); ?>" class="<?php echo ($activeMenu ?? '') === 'assignments' ? 'active' : ''; ?>">
                            تخصیص اعضا
                        </a>
                        <a href="<?php echo url('admin/insurance'); ?>" class="<?php echo ($activeMenu ?? '') === 'insurance' ? 'active' : ''; ?>">
                            بیمه
                        </a>
                    </div>
                </div>

                <!-- برنامه‌ریزی -->
                <div class="sidebar-accordion <?php echo in_array($activeMenu ?? '', ['classes', 'coaches', 'equipment']) ? 'open' : ''; ?>">
                    <button type="button" class="sidebar-accordion-header" onclick="this.parentElement.classList.toggle('open')">
                        <i class="fas fa-calendar-alt acc-icon"></i>
                        برنامه‌ریزی
                        <i class="fas fa-chevron-down acc-arrow"></i>
                    </button>
                    <div class="sidebar-accordion-body">
                        <a href="<?php echo url('admin/classes'); ?>" class="<?php echo ($activeMenu ?? '') === 'classes' ? 'active' : ''; ?>">
                            کلاس‌ها
                        </a>
                        <a href="<?php echo url('admin/coaches'); ?>" class="<?php echo ($activeMenu ?? '') === 'coaches' ? 'active' : ''; ?>">
                            مربیان
                        </a>
                        <a href="<?php echo url('admin/equipment'); ?>" class="<?php echo ($activeMenu ?? '') === 'equipment' ? 'active' : ''; ?>">
                            تجهیزات
                        </a>
                    </div>
                </div>

                <!-- مالی -->
                <div class="sidebar-accordion <?php echo in_array($activeMenu ?? '', ['payments', 'reports']) ? 'open' : ''; ?>">
                    <button type="button" class="sidebar-accordion-header" onclick="this.parentElement.classList.toggle('open')">
                        <i class="fas fa-coins acc-icon"></i>
                        مالی
                        <i class="fas fa-chevron-down acc-arrow"></i>
                    </button>
                    <div class="sidebar-accordion-body">
                        <a href="<?php echo url('admin/payments'); ?>" class="<?php echo ($activeMenu ?? '') === 'payments' ? 'active' : ''; ?>">
                            پرداخت‌ها
                        </a>
                        <a href="<?php echo url('admin/reports'); ?>" class="<?php echo ($activeMenu ?? '') === 'reports' ? 'active' : ''; ?>">
                            گزارشات
                        </a>
                    </div>
                </div>

                <!-- ارتباطات -->
                <div class="sidebar-accordion <?php echo in_array($activeMenu ?? '', ['tickets', 'notifications', 'events']) ? 'open' : ''; ?>">
                    <button type="button" class="sidebar-accordion-header" onclick="this.parentElement.classList.toggle('open')">
                        <i class="fas fa-comments acc-icon"></i>
                        ارتباطات
                        <i class="fas fa-chevron-down acc-arrow"></i>
                    </button>
                    <div class="sidebar-accordion-body">
                        <a href="<?php echo url('admin/tickets'); ?>" class="<?php echo ($activeMenu ?? '') === 'tickets' ? 'active' : ''; ?>">
                            تیکت‌ها
                        </a>
                        <a href="<?php echo url('admin/notifications'); ?>" class="<?php echo ($activeMenu ?? '') === 'notifications' ? 'active' : ''; ?>">
                            اعلانات
                        </a>
                        <a href="<?php echo url('admin/events'); ?>" class="<?php echo ($activeMenu ?? '') === 'events' ? 'active' : ''; ?>">
                            رویدادها
                        </a>
                    </div>
                </div>

                <!-- سیستم -->
                <div class="sidebar-accordion <?php echo in_array($activeMenu ?? '', ['roles', 'settings', 'activity-logs']) ? 'open' : ''; ?>">
                    <button type="button" class="sidebar-accordion-header" onclick="this.parentElement.classList.toggle('open')">
                        <i class="fas fa-cog acc-icon"></i>
                        سیستم
                        <i class="fas fa-chevron-down acc-arrow"></i>
                    </button>
                    <div class="sidebar-accordion-body">
                        <a href="<?php echo url('admin/roles'); ?>" class="<?php echo ($activeMenu ?? '') === 'roles' ? 'active' : ''; ?>">
                            نقش‌ها
                        </a>
                        <a href="<?php echo url('admin/settings'); ?>" class="<?php echo ($activeMenu ?? '') === 'settings' ? 'active' : ''; ?>">
                            تنظیمات
                        </a>
                        <a href="<?php echo url('admin/activity-logs'); ?>" class="<?php echo ($activeMenu ?? '') === 'activity-logs' ? 'active' : ''; ?>">
                            لاگ فعالیت
                        </a>
                    </div>
                </div>
            </nav>

            <!-- Sidebar Footer -->
            <div class="sidebar-footer">
                <?php if (auth()->hasRole('coach')): ?>
                <a href="<?php echo url('coach/dashboard'); ?>" class="sidebar-footer-link">
                    <i class="fas fa-chalkboard-teacher"></i>
                    پنل مربی
                </a>
                <?php endif; ?>
                <a href="<?php echo url('portal/dashboard'); ?>" class="sidebar-footer-link">
                    <i class="fas fa-user"></i>
                    پنل عضو
                </a>
                <a href="<?php echo url('auth/logout'); ?>" class="sidebar-footer-link sidebar-logout">
                    <i class="fas fa-sign-out-alt"></i>
                    خروج
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Top Bar -->
            <header class="topbar">
                <div class="topbar-right">
                    <button id="sidebar-toggle" class="sidebar-toggle" type="button">
                        <i class="fas fa-bars"></i>
                    </button>
                    <span class="topbar-user" style="display:flex;align-items:center;gap:8px;">
                        <?php $avatar = auth()->user()['avatar_path'] ?? ''; $uName = auth()->user()['full_name'] ?? auth()->user()['username'] ?? ''; ?>
                        <?php if (!empty($avatar)): ?>
                        <img src="<?php echo asset($avatar); ?>" alt="" style="width:32px;height:32px;border-radius:50%;object-fit:cover;border:2px solid var(--app-primary);">
                        <?php else: ?>
                        <span style="width:32px;height:32px;border-radius:50%;background:var(--app-gradient);display:inline-flex;align-items:center;justify-content:center;color:#fff;font-size:0.8rem;font-weight:700;flex-shrink:0;"><?php echo e(mb_substr($uName, 0, 1)); ?></span>
                        <?php endif; ?>
                        <?php echo e($uName); ?>
                    </span>
                </div>
                <div class="topbar-left">
                    <a href="<?php echo url('auth/logout'); ?>" class="btn btn-sm btn-danger" onclick="return confirm('آیا مطمئن هستید؟')">
                        <i class="fas fa-sign-out-alt"></i>
                        خروج
                    </a>
                </div>
            </header>

            <!-- Page Content -->
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

    <!-- Local Scripts -->
    <script src="<?php echo asset('js/jquery.min.js'); ?>"></script>
    <script src="<?php echo asset('js/bootstrap.bundle.min.js'); ?>"></script>
    <script src="<?php echo asset('js/persian-datepicker.min.js'); ?>"></script>
    <script src="<?php echo asset('js/app.js'); ?>"></script>

    <script>
    (function(){
        var primary = <?php echo json_encode(getSetting('primary_color', '#1877F2')); ?>;
        var secondary = <?php echo json_encode(getSetting('secondary_color', '#0A3178')); ?>;
        if (!primary || primary.charAt(0) !== '#') return;

        // hex → r,g,b
        function hexToRgb(hex){ var h=hex.replace('#',''); return [parseInt(h.slice(0,2),16),parseInt(h.slice(2,4),16),parseInt(h.slice(4,6),16)]; }
        function shift(c,a){ return Math.min(255,Math.max(0,c+a)); }
        function rgb(r,g,b){ return 'rgb('+r+','+g+','+b+')'; }
        function rgba(r,g,b,a){ return 'rgba('+r+','+g+','+b+','+a+')'; }

        var p = hexToRgb(primary);
        var s = hexToRgb(secondary);
        var lum = (0.299*p[0]+0.587*p[1]+0.114*p[2])/255;
        var isDark = lum < 0.55;

        // Derived primary shades
        var pLight = [shift(p[0],40), shift(p[1],40), shift(p[2],40)];
        var pDark  = [shift(p[0],-30), shift(p[1],-30), shift(p[2],-30)];

        // === 1. Set :root variables (entire app) ===
        var rootVars = {
            '--app-primary':            primary,
            '--app-primary-rgb':        p[0]+','+p[1]+','+p[2],
            '--app-primary-dark':       secondary,
            '--app-primary-dark-rgb':   s[0]+','+s[1]+','+s[2],
            '--app-primary-light':      rgb.apply(null,pLight),
            '--app-primary-light-rgb':  pLight[0]+','+pLight[1]+','+pLight[2],
            '--app-secondary':          secondary,
            '--app-secondary-rgb':      s[0]+','+s[1]+','+s[2],
            '--app-gradient':           'linear-gradient(135deg, '+primary+', '+secondary+')',
            '--app-gradient-reverse':   'linear-gradient(135deg, '+secondary+', '+primary+')'
        };
        var root = document.documentElement;
        for (var k in rootVars) root.style.setProperty(k, rootVars[k]);

        // === 2. Set sidebar variables ===
        var sidebar = document.getElementById('sidebar');
        if (sidebar) {
            // Darken primary for sidebar background
            var sbR = Math.max(0, p[0]-40), sbG = Math.max(0, p[1]-40), sbB = Math.max(0, p[2]-40);
            // Darker variant for deep background
            var sdR = Math.max(0, p[0]-70), sdG = Math.max(0, p[1]-70), sdB = Math.max(0, p[2]-70);
            var isDark = lum < 0.55;
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