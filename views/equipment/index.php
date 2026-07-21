<div class="page-header-row">
    <div class="page-header">
        <h2><i class="fas fa-dumbbell" style="margin-left:8px;"></i> تجهیزات</h2>
        <p>مدیریت تجهیزات باشگاه</p>
    </div>
    <div class="page-header-actions">
        <a href="<?php echo url('admin/equipment/create'); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            تجهیز جدید
        </a>
    </div>
</div>

<!-- Search & Filter -->
<form method="GET" action="<?php echo url('admin/equipment'); ?>" class="search-bar">
    <div class="search-input-group">
        <i class="fas fa-search"></i>
        <input type="text" name="search" value="<?php echo e($search ?? ''); ?>" placeholder="جستجو..." class="form-input">
    </div>
    <select name="condition_status" class="search-filter">
        <option value="">همه وضعیت‌ها</option>
        <option value="new" <?php echo (($filters['condition_status'] ?? '') === 'new') ? 'selected' : ''; ?>>نو</option>
        <option value="good" <?php echo (($filters['condition_status'] ?? '') === 'good') ? 'selected' : ''; ?>>خوب</option>
        <option value="fair" <?php echo (($filters['condition_status'] ?? '') === 'fair') ? 'selected' : ''; ?>>متوسط</option>
        <option value="poor" <?php echo (($filters['condition_status'] ?? '') === 'poor') ? 'selected' : ''; ?>>ضعیف</option>
        <option value="broken" <?php echo (($filters['condition_status'] ?? '') === 'broken') ? 'selected' : ''; ?>>خراب</option>
    </select>
    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter"></i> فیلتر</button>
    <a href="<?php echo url('admin/equipment'); ?>" class="btn btn-secondary btn-sm"><i class="fas fa-times"></i> پاک کردن</a>
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
                        <th>قیمت خرید</th>
                        <th>تاریخ خرید</th>
                        <th>وضعیت</th>
                        <th>آخرین سرویس</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($equipment)): ?>
                    <?php $rowNumber = (($page ?? 1) - 1) * ($perPage ?? 20) + 1; ?>
                    <?php foreach ($equipment as $eq): ?>
                    <?php
                    $cs = $eq['condition_status'] ?? '';
                    $csBadge = 'badge-success'; $csLabel = 'نو';
                    if ($cs === 'good') { $csBadge = 'badge-info'; $csLabel = 'خوب'; }
                    elseif ($cs === 'fair') { $csBadge = 'badge-warning'; $csLabel = 'متوسط'; }
                    elseif ($cs === 'poor') { $csBadge = 'badge-danger'; $csLabel = 'ضعیف'; }
                    elseif ($cs === 'broken') { $csBadge = 'badge-danger'; $csLabel = 'خراب'; }
                    ?>
                    <tr>
                        <td><?php echo $rowNumber++; ?></td>
                        <td><?php echo e($eq['name']); ?></td>
                        <td><?php echo formatCurrency($eq['purchase_price'] ?? 0); ?></td>
                        <td><?php echo formatDate($eq['purchase_date'] ?? ''); ?></td>
                        <td><span class="badge <?php echo $csBadge; ?>"><?php echo $csLabel; ?></span></td>
                        <td><?php echo formatDate($eq['last_maintenance'] ?? ''); ?></td>
                        <td>
                            <div class="table-actions">
                                <a href="<?php echo url('admin/equipment/' . $eq['id'] . '/edit'); ?>" class="btn-icon success" title="ویرایش">
                                    <i class="fas fa-pen-to-square"></i>
                                </a>
                                <form method="POST" action="<?php echo url('admin/equipment/' . $eq['id'] . '/delete'); ?>" style="display:inline;">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="btn-icon danger" onclick="return confirmDelete('آیا از حذف مطمئن هستید؟')" title="حذف">
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
            <?php if (empty($equipment)): ?>
            <div class="empty-state">
                <i class="fas fa-dumbbell" style="font-size:32px;color:#cbd5e1;margin-bottom:8px;"></i>
                <h3>موردی یافت نشد</h3>
                <p>هنوز تجهیزاتی ثبت نشده است.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php echo $pagination ?? ''; ?>