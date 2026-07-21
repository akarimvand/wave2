<?php $activeMenu = 'coaches'; $pageTitle = 'مربیان'; ?>
<div class="page-header-row">
    <div class="page-header">
        <h2 class="page-title">مربیان</h2>
        <p>مدیریت مربیان باشگاه</p>
    </div>
    <div class="page-header-actions">
        <a href="<?php echo url('admin/coaches/create'); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            مربی جدید
        </a>
    </div>
</div>

<!-- Search & Filter -->
<form method="GET" action="<?php echo url('admin/coaches'); ?>" class="search-bar">
    <div class="search-input-group">
        <i class="fas fa-search"></i>
        <input type="text" name="search" value="<?php echo e($_GET['search'] ?? ''); ?>" placeholder="جستجو بر اساس نام مربی..." class="form-input">
    </div>
    <select name="status" class="search-filter">
        <option value="">همه وضعیت‌ها</option>
        <option value="active" <?php echo (($_GET['status'] ?? '') === 'active') ? 'selected' : ''; ?>>فعال</option>
        <option value="inactive" <?php echo (($_GET['status'] ?? '') === 'inactive') ? 'selected' : ''; ?>>غیرفعال</option>
    </select>
    <button type="submit" class="btn btn-primary btn-sm">جستجو</button>
    <a href="<?php echo url('admin/coaches'); ?>" class="btn btn-secondary btn-sm">پاک کردن</a>
</form>

<!-- Table -->
<div class="card">
    <div class="card-body">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ردیف</th>
                        <th>نام</th>
                        <th>تخصص</th>
                        <th>تلفن</th>
                        <th>حقوق</th>
                        <th>حساب کاربری</th>
                        <th>وضعیت</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($coaches)): ?>
                    <?php $rowNum = ($page - 1) * $perPage + 1; ?>
                    <?php foreach ($coaches as $row): ?>
                    <tr>
                        <td><?php echo $rowNum++; ?></td>
                        <td style="font-weight:500;"><?php echo e($row['first_name'] . ' ' . $row['last_name']); ?></td>
                        <td><?php echo e($row['specialty'] ?? '-'); ?></td>
                        <td style="direction:ltr;text-align:right;"><?php echo e($row['phone']); ?></td>
                        <td><?php echo formatCurrency($row['salary'] ?? 0); ?></td>
                        <td>
                            <?php if (!empty($row['user_username'])): ?>
                            <span style="color:#059669;font-size:0.85rem;">
                                <i class="fas fa-check-circle" style="margin-left:3px;"></i>
                                @<?php echo e($row['user_username']); ?>
                            </span>
                            <?php else: ?>
                            <span style="color:#9CA3AF;font-size:0.85rem;">
                                <i class="fas fa-times-circle" style="margin-left:3px;"></i>
                                بدون حساب
                            </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (($row['is_active'] ?? 1) == 1): ?>
                            <span class="badge badge-success">فعال</span>
                            <?php else: ?>
                            <span class="badge badge-secondary">غیرفعال</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="table-actions">
                                <a href="<?php echo url('admin/coaches/' . $row['id'] . '/edit'); ?>" class="btn btn-warning btn-xs" title="ویرایش">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="<?php echo url('admin/coaches/' . $row['id'] . '/delete'); ?>" style="display:inline;">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="btn btn-danger btn-xs" title="حذف" onclick="return confirmDelete('آیا از حذف این مربی مطمئن هستید؟')">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            <?php if (empty($coaches)): ?>
            <div class="table-empty">
                <div class="empty-state">
                    <i class="fas fa-user-tie" style="font-size:2.5rem;color:#9CA3AF;margin-bottom:12px;"></i>
                    <h3>موردی یافت نشد</h3>
                    <p>هنوز مربی‌ای ثبت نشده است.</p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php echo $pagination ?? ''; ?>