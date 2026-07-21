<div class="page-header-row">
    <div class="page-header">
        <h2>رویدادها</h2>
        <p>مدیریت رویدادها و مسابقات باشگاه</p>
    </div>
    <div class="page-header-actions">
        <a href="<?php echo url('admin/events/create'); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            رویداد جدید
        </a>
    </div>
</div>

<!-- Search & Filter -->
<form method="GET" action="<?php echo url('admin/events'); ?>" class="search-bar">
    <div class="search-input-group">
        <i class="fas fa-search"></i>
        <input type="text" name="search" value="<?php echo e($search ?? ''); ?>" placeholder="جستجو بر اساس عنوان رویداد..." class="form-input">
    </div>
    <select name="status" class="search-filter">
        <option value="">همه وضعیت‌ها</option>
        <option value="active" <?php echo (($filters['status'] ?? '') === 'active') ? 'selected' : ''; ?>>فعال</option>
        <option value="cancelled" <?php echo (($filters['status'] ?? '') === 'cancelled') ? 'selected' : ''; ?>>لغو شده</option>
    </select>
    <button type="submit" class="btn btn-primary btn-sm">جستجو</button>
    <a href="<?php echo url('admin/events'); ?>" class="btn btn-secondary btn-sm">پاک کردن</a>
</form>

<!-- Table -->
<div class="card">
    <div class="card-body">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ردیف</th>
                        <th>عنوان</th>
                        <th>تاریخ</th>
                        <th>مکان</th>
                        <th>ظرفیت</th>
                        <th>وضعیت</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($events)): ?>
                    <?php $rowNum = ($currentPage ?? 1) * ($perPage ?? 20) - ($perPage ?? 20) + 1; ?>
                    <?php foreach ($events as $row): ?>
                    <?php
                    $status = $row['status'] ?? 'active';
                    if ($status === 'active') { $badgeClass = 'badge-success'; $statusLabel = 'فعال'; }
                    elseif ($status === 'cancelled') { $badgeClass = 'badge-danger'; $statusLabel = 'لغو شده'; }
                    else { $badgeClass = 'badge-secondary'; $statusLabel = e($status); }
                    ?>
                    <tr>
                        <td><?php echo $rowNum++; ?></td>
                        <td><?php echo e($row['title']); ?></td>
                        <td><?php echo formatDate($row['event_date'] ?? ''); ?></td>
                        <td><?php echo e($row['location'] ?? '-'); ?></td>
                        <td><?php echo e($row['capacity'] ?? $row['max_participants'] ?? '-'); ?></td>
                        <td><span class="badge <?php echo $badgeClass; ?>"><?php echo $statusLabel; ?></span></td>
                        <td>
                            <div class="table-actions">
                                <a href="<?php echo url('admin/events/' . $row['id'] . '/edit'); ?>" class="btn btn-warning btn-xs" title="ویرایش">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="<?php echo url('admin/events/' . $row['id'] . '/delete'); ?>" style="display:inline;">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="btn btn-danger btn-xs" title="حذف" onclick="return confirmDelete('آیا از حذف این رویداد مطمئن هستید؟')">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <?php endif; ?>
                </tbody>
            </table>
            <?php if (empty($events)): ?>
            <div class="table-empty">
                <div class="empty-state">
                    <i class="fas fa-calendar-alt" style="font-size:2.5rem;color:#9CA3AF;margin-bottom:12px;"></i>
                    <h3>موردی یافت نشد</h3>
                    <p>هنوز رویدادی ثبت نشده است.</p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php echo $pagination ?? ''; ?>