<?php $activeMenu = 'dashboard'; $pageTitle = 'داشبورد'; ?>

<!-- Welcome Card -->
<div class="card" style="margin-bottom:24px;background:linear-gradient(135deg,#3B82F6 0%,#8B5CF6 100%);border:none;border-radius:12px;">
    <div class="card-body" style="padding:24px 28px;">
        <div style="display:flex;align-items:center;gap:20px;">
            <div style="width:56px;height:56px;border-radius:50%;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="fas fa-chalkboard-teacher" style="color:#fff;font-size:1.5rem;"></i>
            </div>
            <div>
                <h2 style="margin:0 0 4px 0;color:#fff;font-size:1.25rem;font-weight:700;">
                    <?php echo e($coach['first_name'] . ' ' . $coach['last_name']); ?> عزیز، خوش آمدید
                </h2>
                <p style="margin:0;color:rgba(255,255,255,0.8);font-size:0.9rem;">
                    <?php echo e($coach['specialty'] ?? 'مربی باشگاه'); ?>
                    &nbsp;|&nbsp;
                    امروز: <?php echo e($todayName); ?>
                    &nbsp;|&nbsp;
                    <?php echo e(jalali()->formatDate(date('Y-m-d'))); ?>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:16px;margin-bottom:24px;">
    <!-- کلاس‌های من -->
    <div class="card" style="margin-bottom:0;">
        <div class="card-body" style="padding:20px;">
            <div style="display:flex;align-items:center;justify-content:space-between;">
                <div>
                    <div style="font-size:1.4rem;font-weight:700;color:#1f2937;margin-bottom:4px;">
                        <?php echo count($myClasses ?? []); ?>
                    </div>
                    <div style="font-size:0.8rem;color:#6B7A8D;">کلاس فعال</div>
                    <div style="font-size:0.8rem;color:#9CA3AF;margin-top:8px;">
                        <i class="fas fa-chalkboard-teacher" style="margin-left:4px;"></i>
                        کلاس‌های من
                    </div>
                </div>
                <div style="width:48px;height:48px;border-radius:12px;background:rgba(59,130,246,0.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="fas fa-chalkboard-teacher" style="font-size:1.2rem;color:#3B82F6;"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- کل دانشجویان -->
    <div class="card" style="margin-bottom:0;">
        <div class="card-body" style="padding:20px;">
            <div style="display:flex;align-items:center;justify-content:space-between;">
                <div>
                    <div style="font-size:1.4rem;font-weight:700;color:#1f2937;margin-bottom:4px;">
                        <?php echo $totalStudents ?? 0; ?>
                    </div>
                    <div style="font-size:0.8rem;color:#6B7A8D;">ورزشکار فعال</div>
                    <div style="font-size:0.8rem;color:#9CA3AF;margin-top:8px;">
                        <i class="fas fa-users" style="margin-left:4px;"></i>
                        کل دانشجویان
                    </div>
                </div>
                <div style="width:48px;height:48px;border-radius:12px;background:rgba(16,185,129,0.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="fas fa-users" style="font-size:1.2rem;color:#10B981;"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- کلاس‌های امروز -->
    <div class="card" style="margin-bottom:0;">
        <div class="card-body" style="padding:20px;">
            <div style="display:flex;align-items:center;justify-content:space-between;">
                <div>
                    <div style="font-size:1.4rem;font-weight:700;color:#1f2937;margin-bottom:4px;">
                        <?php echo count($todayClasses ?? []); ?>
                    </div>
                    <div style="font-size:0.8rem;color:#6B7A8D;">کلاس برای امروز</div>
                    <div style="font-size:0.8rem;color:#9CA3AF;margin-top:8px;">
                        <i class="fas fa-calendar-day" style="margin-left:4px;"></i>
                        برنامه امروز
                    </div>
                </div>
                <div style="width:48px;height:48px;border-radius:12px;background:rgba(245,158,11,0.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="fas fa-calendar-day" style="font-size:1.2rem;color:#F59E0B;"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- حضور امروز -->
    <div class="card" style="margin-bottom:0;">
        <div class="card-body" style="padding:20px;">
            <div style="display:flex;align-items:center;justify-content:space-between;">
                <div>
                    <div style="font-size:1.4rem;font-weight:700;color:#1f2937;margin-bottom:4px;">
                        <?php echo $todayAttendance ?? 0; ?>
                    </div>
                    <div style="font-size:0.8rem;color:#6B7A8D;">نفر ثبت شده</div>
                    <div style="font-size:0.8rem;color:#9CA3AF;margin-top:8px;">
                        <i class="fas fa-clipboard-check" style="margin-left:4px;"></i>
                        حضور امروز
                    </div>
                </div>
                <div style="width:48px;height:48px;border-radius:12px;background:rgba(16,185,129,0.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="fas fa-clipboard-check" style="font-size:1.2rem;color:#10B981;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Today's Schedule -->
<div class="card" style="margin-bottom:16px;">
    <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;">
        <h3 style="margin:0;font-size:0.95rem;font-weight:600;">
            <i class="fas fa-calendar-alt" style="margin-left:8px;color:#3B82F6;"></i>
            برنامه کلاس‌های امروز
            <span style="font-size:0.8rem;color:#6B7A8D;font-weight:400;">(<?php echo e($todayName); ?>)</span>
        </h3>
        <a href="<?php echo url('coach/classes'); ?>" style="font-size:0.8rem;color:#3B82F6;text-decoration:none;">
            مشاهده همه کلاس‌ها <i class="fas fa-arrow-left" style="margin-right:4px;font-size:0.7rem;"></i>
        </a>
    </div>
    <div class="card-body">
        <?php if (!empty($todayClasses)): ?>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:12px;">
            <?php foreach ($todayClasses as $cls): ?>
            <div style="border:1px solid #E5E7EB;border-radius:10px;padding:16px;transition:all 0.3s ease;" onmouseover="this.style.borderColor='#3B82F6';this.style.boxShadow='0 4px 12px rgba(59,130,246,0.1)'" onmouseout="this.style.borderColor='#E5E7EB';this.style.boxShadow='none'">
                <div style="display:flex;justify-content:space-between;align-items:start;">
                    <div>
                        <div style="font-weight:600;color:#1f2937;margin-bottom:6px;">
                            <i class="fas fa-dumbbell" style="color:#3B82F6;margin-left:6px;font-size:0.85rem;"></i>
                            <?php echo e($cls['name']); ?>
                        </div>
                        <div style="font-size:0.82rem;color:#6B7A8D;">
                            <i class="fas fa-clock" style="margin-left:4px;"></i>
                            <span style="direction:ltr;display:inline-block;"><?php echo e($cls['start_time'] ?? substr($cls['schedule_time'], 0, 5)); ?></span>
                            <?php if (!empty($cls['end_time'])): ?>
                                تا <span style="direction:ltr;display:inline-block;"><?php echo e(substr($cls['end_time'], 0, 5)); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div style="background:rgba(59,130,246,0.1);color:#3B82F6;padding:4px 10px;border-radius:20px;font-size:0.8rem;font-weight:600;">
                        <i class="fas fa-user" style="margin-left:3px;"></i>
                        <?php echo $cls['student_count'] ?? 0; ?>
                    </div>
                </div>
                <?php if (!empty($cls['description'])): ?>
                <div style="font-size:0.8rem;color:#9CA3AF;margin-top:8px;line-height:1.6;">
                    <?php echo e(mb_substr($cls['description'], 0, 80)); ?><?php echo mb_strlen($cls['description']) > 80 ? '...' : ''; ?>
                </div>
                <?php endif; ?>
                <div style="margin-top:10px;">
                    <a href="<?php echo url('coach/attendance-form?class_id=' . $cls['id'] . '&date=' . date('Y-m-d')); ?>" class="btn btn-sm btn-primary">
                        <i class="fas fa-clipboard-check" style="margin-left:4px;"></i>
                        ثبت حضور و غیاب
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="table-empty">
            <div class="empty-state">
                <i class="fas fa-calendar-check" style="font-size:48px;color:#D1D5DB;margin-bottom:16px;display:block;"></i>
                <h3>امروز کلاسی ندارید</h3>
                <p>برنامه امروز شما خالی است. از فرصت استراحت استفاده کنید!</p>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- All My Classes Table -->
<div class="card" style="margin-bottom:16px;">
    <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;">
        <h3 style="margin:0;font-size:0.95rem;font-weight:600;">
            <i class="fas fa-list" style="margin-left:8px;color:#3B82F6;"></i>
            لیست کلاس‌های من
        </h3>
    </div>
    <div class="card-body">
        <?php if (!empty($myClasses)): ?>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>نام کلاس</th>
                        <th>روزهای برگزاری</th>
                        <th>ساعت</th>
                        <th>ثبت‌نام شده</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($myClasses as $cls): ?>
                    <tr>
                        <td style="font-weight:500;"><?php echo e($cls['name']); ?></td>
                        <td style="font-size:0.85rem;"><?php echo e($cls['schedule_days'] ?? $cls['schedule_day'] ?? '-'); ?></td>
                        <td style="direction:ltr;text-align:right;">
                            <?php echo e($cls['start_time'] ?? substr($cls['schedule_time'], 0, 5)); ?>
                            <?php if (!empty($cls['end_time'])): ?>
                                - <?php echo e(substr($cls['end_time'], 0, 5)); ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span style="background:rgba(59,130,246,0.1);color:#3B82F6;padding:2px 8px;border-radius:12px;font-size:0.82rem;font-weight:600;">
                                <?php echo $cls['student_count'] ?? 0; ?>
                            </span>
                        </td>
                        <td>
                            <div class="table-actions">
                                <a href="<?php echo url('coach/class-students/' . $cls['id']); ?>" class="btn btn-info btn-xs">
                                    <i class="fas fa-users"></i>
                                    لیست
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="table-empty">
            <div class="empty-state">
                <i class="fas fa-chalkboard-teacher" style="font-size:48px;color:#D1D5DB;margin-bottom:16px;display:block;"></i>
                <h3>کلاسی یافت نشد</h3>
                <p>هنوز کلاسی به شما اختصاص داده نشده است.</p>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Recent Notifications -->
<?php if (!empty($recentNotifications)): ?>
<div class="card">
    <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;">
        <h3 style="margin:0;font-size:0.95rem;font-weight:600;">
            <i class="fas fa-bell" style="margin-left:8px;color:#F59E0B;"></i>
            آخرین اعلانات
        </h3>
        <a href="<?php echo url('coach/notifications'); ?>" style="font-size:0.8rem;color:#3B82F6;text-decoration:none;">
            مشاهده همه <i class="fas fa-arrow-left" style="margin-right:4px;font-size:0.7rem;"></i>
        </a>
    </div>
    <div class="card-body" style="padding:0;">
        <?php foreach (array_slice($recentNotifications, 0, 3) as $notif): ?>
        <div style="padding:14px 20px;border-bottom:1px solid #F3F4F6;display:flex;align-items:start;gap:12px;">
            <div style="width:8px;height:8px;border-radius:50%;background:#3B82F6;margin-top:8px;flex-shrink:0;"></div>
            <div style="flex:1;">
                <div style="font-size:0.88rem;font-weight:500;color:#1f2937;"><?php echo e($notif['title']); ?></div>
                <div style="font-size:0.8rem;color:#6B7A8D;margin-top:2px;"><?php echo e($notif['message']); ?></div>
                <div style="font-size:0.75rem;color:#9CA3AF;margin-top:4px;"><?php echo formatDateTime($notif['created_at']); ?></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>