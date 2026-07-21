<div class="page-header-row">
    <div class="page-header">
        <h2><i class="fas fa-history" style="margin-left:8px;"></i>لاگ فعالیت‌ها</h2>
        <p>تاریخچه عملیات کاربران</p>
    </div>
    <div class="page-header-actions"></div>
</div>

<!-- Search & Filter -->
<form method="GET" action="<?php echo url('admin/activity-logs'); ?>" class="search-bar">
    <div class="search-input-group">
        <i class="fas fa-search"></i>
        <input type="text" name="search" value="<?php echo e($search ?? ''); ?>" placeholder="جستجو..." class="form-input">
    </div>
    <select name="module" class="search-filter">
        <option value="">همه ماژول‌ها</option>
        <?php if (!empty($modules)): ?>
        <?php foreach ($modules as $mod): ?>
        <option value="<?php echo e($mod); ?>" <?php echo (($filters['module'] ?? '') === $mod) ? 'selected' : ''; ?>><?php echo e($mod); ?></option>
        <?php endforeach; ?>
        <?php endif; ?>
    </select>
    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter"></i> فیلتر</button>
    <a href="<?php echo url('admin/activity-logs'); ?>" class="btn btn-secondary btn-sm"><i class="fas fa-times"></i> پاک کردن</a>
</form>

<!-- Table -->
<div class="card">
    <div class="card-body">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>کاربر</th>
                        <th>عملیات</th>
                        <th>ماژول</th>
                        <th>تاریخ</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($logs)): ?>
                    <?php foreach ($logs as $row): ?>
                    <tr>
                        <td><i class="fas fa-user" style="margin-left:6px;color:#94a3b8;"></i><?php echo e($row['user_name'] ?? 'سیستم'); ?></td>
                        <td><?php echo e($row['action']); ?></td>
                        <td><span class="badge badge-info"><?php echo e($row['module']); ?></span></td>
                        <td><?php echo formatDateTime($row['created_at']); ?></td>
                        <td style="direction:ltr;text-align:right;font-size:0.85rem;color:#6B7A8D;"><?php echo e($row['ip_address']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            <?php if (empty($logs)): ?>
            <div class="empty-state">
                <i class="fas fa-clipboard-list" style="font-size:32px;color:#cbd5e1;margin-bottom:8px;"></i>
                <h3>موردی یافت نشد</h3>
                <p>هنوز فعالیتی ثبت نشده است.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php echo $pagination ?? ''; ?>