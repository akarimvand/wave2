<div class="page-header-row">
    <div class="page-header">
        <h2>داشبورد</h2>
        <p>خلاصه وضعیت باشگاه</p>
    </div>
    <div class="page-header-actions"></div>
</div>

<!-- Stats Grid -->
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:16px;margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(59,130,246,0.1);color:#3B82F6;">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-info">
            <div class="stat-value"><?php echo formatNumber($stats['totalMembers'] ?? $stats['total_members'] ?? 0); ?></div>
            <div class="stat-label">کل اعضا</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(16,185,129,0.1);color:#10B981;">
            <i class="fas fa-user-check"></i>
        </div>
        <div class="stat-info">
            <div class="stat-value"><?php echo formatNumber($stats['activeMembers'] ?? $stats['active_members'] ?? 0); ?></div>
            <div class="stat-label">اعضای فعال</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(245,158,11,0.1);color:#F59E0B;">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-info">
            <div class="stat-value"><?php echo formatNumber($stats['pendingMembers'] ?? $stats['pending_members'] ?? 0); ?></div>
            <div class="stat-label">درخواست‌های در انتظار</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(139,92,246,0.1);color:#8B5CF6;">
            <i class="fas fa-id-card"></i>
        </div>
        <div class="stat-info">
            <div class="stat-value"><?php echo formatNumber($stats['activeMemberships'] ?? $stats['active_memberships'] ?? 0); ?></div>
            <div class="stat-label">اشتراک‌های فعال</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(16,185,129,0.1);color:#10B981;">
            <i class="fas fa-money-bill-wave"></i>
        </div>
        <div class="stat-info">
            <div class="stat-value"><?php echo formatCurrency($stats['todayPaymentsTotal'] ?? $stats['today_revenue'] ?? 0); ?></div>
            <div class="stat-label">درآمد امروز</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(59,130,246,0.1);color:#3B82F6;">
            <i class="fas fa-chart-line"></i>
        </div>
        <div class="stat-info">
            <div class="stat-value"><?php echo formatCurrency($stats['monthlyRevenue'] ?? $stats['month_revenue'] ?? 0); ?></div>
            <div class="stat-label">درآمد ماهانه</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(239,68,68,0.1);color:#EF4444;">
            <i class="fas fa-headset"></i>
        </div>
        <div class="stat-info">
            <div class="stat-value"><?php echo formatNumber($stats['openTickets'] ?? $stats['open_tickets'] ?? 0); ?></div>
            <div class="stat-label">تیکت‌های باز</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(6,182,212,0.1);color:#06B6D4;">
            <i class="fas fa-calendar-check"></i>
        </div>
        <div class="stat-info">
            <div class="stat-value"><?php echo formatNumber($stats['activeClasses'] ?? $stats['active_classes'] ?? 0); ?></div>
            <div class="stat-label">کلاس‌های فعال</div>
        </div>
    </div>
</div>

<!-- Two Side-by-Side Cards -->
<div class="grid-2col">
    <!-- Upcoming Classes -->
    <div class="card">
        <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;">
            <h3 style="margin:0;font-size:0.95rem;font-weight:600;">کلاس‌های پیش رو</h3>
            <a href="<?php echo url('admin/classes'); ?>" class="btn btn-sm btn-secondary">مشاهده همه</a>
        </div>
        <div class="card-body">
            <?php if (!empty($upcomingClasses)): ?>
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>نام</th>
                            <th>مربی</th>
                            <th>روز</th>
                            <th>ساعت</th>
                            <th>ظرفیت</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($upcomingClasses as $cls): ?>
                        <tr>
                            <td><?php echo e($cls['name']); ?></td>
                            <td><?php echo e($cls['coach_name'] ?? ''); ?></td>
                            <td><?php echo e($cls['day_of_week'] ?? $cls['schedule_day'] ?? ''); ?></td>
                            <td style="direction:ltr;text-align:right;"><?php echo e($cls['start_time'] ?? ''); ?></td>
                            <td><?php echo e($cls['current_enrollment'] ?? 0); ?>/<?php echo e($cls['capacity'] ?? $cls['max_capacity'] ?? 0); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-calendar-times" style="font-size:32px;color:#cbd5e1;margin-bottom:8px;"></i>
                <h3>موردی یافت نشد</h3>
                <p>کلاس پیش‌رویی وجود ندارد.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent Tickets -->
    <div class="card">
        <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;">
            <h3 style="margin:0;font-size:0.95rem;font-weight:600;">تیکت‌های اخیر</h3>
            <a href="<?php echo url('admin/tickets'); ?>" class="btn btn-sm btn-secondary">مشاهده همه</a>
        </div>
        <div class="card-body">
            <?php if (!empty($recentTickets)): ?>
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>عضو</th>
                            <th>موضوع</th>
                            <th>وضعیت</th>
                            <th>تاریخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentTickets as $ticket): ?>
                        <?php
                        $tStatus = $ticket['status'] ?? '';
                        $tBadge = 'badge-info';
                        $tLabel = e($tStatus);
                        if ($tStatus === 'open') { $tBadge = 'badge-success'; $tLabel = 'باز'; }
                        elseif ($tStatus === 'closed') { $tBadge = 'badge-secondary'; $tLabel = 'بسته'; }
                        elseif ($tStatus === 'pending' || $tStatus === 'in_progress') { $tBadge = 'badge-warning'; $tLabel = 'در انتظار'; }
                        ?>
                        <tr>
                            <td><?php echo e($ticket['member_name'] ?? ''); ?></td>
                            <td><a href="<?php echo url('admin/tickets/' . $ticket['id']); ?>" style="color:#1E293B;text-decoration:none;"><?php echo e($ticket['subject']); ?></a></td>
                            <td><span class="badge <?php echo $tBadge; ?>"><?php echo $tLabel; ?></span></td>
                            <td style="font-size:0.85rem;color:#6B7A8D;"><?php echo formatDate($ticket['created_at']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-inbox" style="font-size:32px;color:#cbd5e1;margin-bottom:8px;"></i>
                <h3>موردی یافت نشد</h3>
                <p>تیکت جدیدی وجود ندارد.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>