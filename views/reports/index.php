<div class="page-header-row">
    <div class="page-header">
        <h2><i class="fas fa-chart-bar" style="margin-left:8px;color:#3B82F6;"></i>گزارشات</h2>
        <p>گزارش‌های مالی و آماری باشگاه</p>
    </div>
    <div class="page-header-actions"></div>
</div>

<!-- Filter Form -->
<form method="GET" action="<?php echo url('admin/reports'); ?>" class="search-bar">
    <div class="search-input-group">
        <i class="fas fa-calendar-alt"></i>
        <input type="text" name="date_from" class="form-input jalali-date" data-jalali value="<?php echo e($filters['date_from'] ?? $filters['from_date'] ?? ''); ?>" placeholder="از تاریخ..." readonly>
    </div>
    <div class="search-input-group">
        <i class="fas fa-calendar-alt"></i>
        <input type="text" name="date_to" class="form-input jalali-date" data-jalali value="<?php echo e($filters['date_to'] ?? $filters['to_date'] ?? ''); ?>" placeholder="تا تاریخ..." readonly>
    </div>
    <select name="report_type" class="search-filter">
        <option value="members" <?php echo (($reportType ?? $filters['report_type'] ?? '') === 'members') ? 'selected' : ''; ?>>اعضا</option>
        <option value="payments" <?php echo (($reportType ?? $filters['report_type'] ?? 'payments') === 'payments') ? 'selected' : ''; ?>>پرداخت‌ها</option>
        <option value="classes" <?php echo (($reportType ?? '') === 'classes') ? 'selected' : ''; ?>>کلاس‌ها</option>
    </select>
    <button type="submit" class="btn btn-primary btn-sm">
        <i class="fas fa-search"></i> مشاهده گزارش
    </button>
    <a href="<?php echo url('admin/reports'); ?>" class="btn btn-secondary btn-sm">
        <i class="fas fa-times"></i> پاک کردن
    </a>
</form>

<!-- Summary Cards -->
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:16px;margin-bottom:20px;">
    <!-- Total Members -->
    <div class="card" style="overflow:hidden;">
        <div class="card-body" style="display:flex;align-items:center;gap:16px;padding:20px;">
            <div style="flex-shrink:0;width:50px;height:50px;border-radius:12px;background:#3B82F615;display:flex;align-items:center;justify-content:center;">
                <i class="fas fa-users" style="font-size:22px;color:#3B82F6;"></i>
            </div>
            <div>
                <div style="font-size:1.4rem;font-weight:700;color:#1f2937;line-height:1.2;">
                    <?php echo formatNumber($stats['total_members'] ?? $stats['totalMembers'] ?? 0); ?>
                </div>
                <div style="font-size:0.85rem;color:#6b7280;margin-top:2px;">کل اعضا</div>
            </div>
        </div>
    </div>

    <!-- Active Members -->
    <div class="card" style="overflow:hidden;">
        <div class="card-body" style="display:flex;align-items:center;gap:16px;padding:20px;">
            <div style="flex-shrink:0;width:50px;height:50px;border-radius:12px;background:#10B98115;display:flex;align-items:center;justify-content:center;">
                <i class="fas fa-user-check" style="font-size:22px;color:#10B981;"></i>
            </div>
            <div>
                <div style="font-size:1.4rem;font-weight:700;color:#1f2937;line-height:1.2;">
                    <?php echo formatNumber($stats['active_members'] ?? $stats['activeMembers'] ?? 0); ?>
                </div>
                <div style="font-size:0.85rem;color:#6b7280;margin-top:2px;">اعضای فعال</div>
            </div>
        </div>
    </div>

    <!-- Total Revenue -->
    <div class="card" style="overflow:hidden;">
        <div class="card-body" style="display:flex;align-items:center;gap:16px;padding:20px;">
            <div style="flex-shrink:0;width:50px;height:50px;border-radius:12px;background:#10B98115;display:flex;align-items:center;justify-content:center;">
                <i class="fas fa-money-bill-wave" style="font-size:22px;color:#10B981;"></i>
            </div>
            <div>
                <div style="font-size:1.4rem;font-weight:700;color:#1f2937;line-height:1.2;">
                    <?php echo formatCurrency($stats['total_revenue'] ?? $stats['totalRevenue'] ?? 0); ?>
                </div>
                <div style="font-size:0.85rem;color:#6b7280;margin-top:2px;">مجموع درآمد</div>
            </div>
        </div>
    </div>

    <!-- Total Classes -->
    <div class="card" style="overflow:hidden;">
        <div class="card-body" style="display:flex;align-items:center;gap:16px;padding:20px;">
            <div style="flex-shrink:0;width:50px;height:50px;border-radius:12px;background:#8B5CF615;display:flex;align-items:center;justify-content:center;">
                <i class="fas fa-chalkboard-teacher" style="font-size:22px;color:#8B5CF6;"></i>
            </div>
            <div>
                <div style="font-size:1.4rem;font-weight:700;color:#1f2937;line-height:1.2;">
                    <?php echo formatNumber($stats['total_classes'] ?? $stats['totalClasses'] ?? $stats['active_classes'] ?? 0); ?>
                </div>
                <div style="font-size:0.85rem;color:#6b7280;margin-top:2px;">کلاس‌ها</div>
            </div>
        </div>
    </div>
</div>

<!-- Report Table -->
<div class="card">
    <div class="card-body">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ردیف</th>
                        <th>شرح</th>
                        <th>مقدار</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($reportData)): ?>
                    <?php $row = 1; ?>
                    <?php foreach ($reportData as $item): ?>
                    <tr>
                        <td><?php echo $row++; ?></td>
                        <td><?php echo e($item['label'] ?? $item['title'] ?? $item['name'] ?? ''); ?></td>
                        <td>
                            <?php if (isset($item['amount'])): ?>
                                <?php echo formatCurrency($item['amount']); ?>
                            <?php elseif (isset($item['count'])): ?>
                                <?php echo formatNumber($item['count']); ?>
                            <?php elseif (isset($item['value'])): ?>
                                <?php echo e($item['value']); ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <?php endif; ?>
                </tbody>
            </table>
            <?php if (empty($reportData)): ?>
            <div class="table-empty">
                <div class="empty-state">
                    <i class="fas fa-chart-pie" style="font-size:48px;color:#D1D5DB;"></i>
                    <h3>داده‌ای یافت نشد</h3>
                    <p>برای مشاهده گزارش، بازه زمانی و نوع گزارش را انتخاب کنید.</p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>