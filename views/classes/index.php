<div class="page-header-row">
    <div class="page-header">
        <h2>کلاس‌ها</h2>
        <p>مدیریت کلاس‌های ورزشی باشگاه</p>
    </div>
    <div class="page-header-actions">
        <a href="<?php echo url('admin/classes/create'); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            کلاس جدید
        </a>
    </div>
</div>

<!-- Search & Filter -->
<form method="GET" action="<?php echo url('admin/classes'); ?>" class="search-bar">
    <div class="search-input-group">
        <i class="fas fa-search"></i>
        <input type="text" name="search" value="<?php echo e($search ?? ''); ?>" placeholder="جستجو بر اساس نام کلاس..." class="form-input">
    </div>
    <select name="schedule_day" class="search-filter">
        <option value="">همه روزها</option>
        <?php
        $allDays = ['شنبه', 'یکشنبه', 'دوشنبه', 'سه‌شنبه', 'چهارشنبه', 'پنجشنبه', 'جمعه'];
        foreach ($allDays as $d):
        ?>
            <option value="<?php echo e($d); ?>" <?php echo ($scheduleDay ?? '') === $d ? 'selected' : ''; ?>><?php echo e($d); ?></option>
        <?php endforeach; ?>
    </select>
    <select name="status" class="search-filter">
        <option value="">همه وضعیت‌ها</option>
        <option value="1" <?php echo (($_GET['status'] ?? '') === '1') ? 'selected' : ''; ?>>فعال</option>
        <option value="0" <?php echo (($_GET['status'] ?? '') === '0') ? 'selected' : ''; ?>>غیرفعال</option>
    </select>
    <button type="submit" class="btn btn-primary btn-sm">جستجو</button>
    <a href="<?php echo url('admin/classes'); ?>" class="btn btn-secondary btn-sm">پاک کردن</a>
</form>

<!-- Table -->
<div class="card">
    <div class="card-body">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ردیف</th>
                        <th>نام کلاس</th>
                        <th>مربی</th>
                        <th>روزهای برگزاری</th>
                        <th>ساعت</th>
                        <th>ظرفیت</th>
                        <th>وضعیت</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($classes)): ?>
                    <?php $rowNum = ($page ?? 1) * ($perPage ?? 20) - ($perPage ?? 20) + 1; ?>
                    <?php foreach ($classes as $row): ?>
                    <?php
                    if (($row['is_active'] ?? 1) == 1) {
                        $badgeClass = 'badge-success';
                        $statusLabel = 'فعال';
                    } else {
                        $badgeClass = 'badge-secondary';
                        $statusLabel = 'غیرفعال';
                    }
                    $coachName = trim(($row['coach_first_name'] ?? '') . ' ' . ($row['coach_last_name'] ?? ''));
                    $timeRange = '';
                    if (!empty($row['start_time']) && !empty($row['end_time'])) {
                        $timeRange = e($row['start_time']) . ' تا ' . e($row['end_time']);
                    } elseif (!empty($row['start_time'])) {
                        $timeRange = 'از ' . e($row['start_time']);
                    } else {
                        $timeRange = '-';
                    }
                    ?>
                    <tr>
                        <td><?php echo $rowNum++; ?></td>
                        <td><?php echo e($row['name']); ?></td>
                        <td><?php echo !empty($coachName) ? e($coachName) : '-'; ?></td>
                        <td><?php echo !empty($row['schedule_days']) ? e($row['schedule_days']) : '-'; ?></td>
                        <td style="direction:ltr;text-align:right;"><?php echo $timeRange; ?></td>
                        <td><?php echo e($row['max_participants'] ?? '-'); ?></td>
                        <td><span class="badge <?php echo $badgeClass; ?>"><?php echo $statusLabel; ?></span></td>
                        <td>
                            <div class="table-actions">
                                <a href="<?php echo url('admin/classes/' . $row['id'] . '/edit'); ?>" class="btn btn-warning btn-xs" title="ویرایش">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="<?php echo url('admin/classes/' . $row['id'] . '/delete'); ?>" style="display:inline;">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="btn btn-danger btn-xs" title="حذف" onclick="return confirmDelete('آیا از حذف این کلاس مطمئن هستید؟')">
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
            <?php if (empty($classes)): ?>
            <div class="table-empty">
                <div class="empty-state">
                    <i class="fas fa-chalkboard-teacher" style="font-size:2.5rem;color:#9CA3AF;margin-bottom:12px;"></i>
                    <h3>موردی یافت نشد</h3>
                    <p>هنوز کلاسی ثبت نشده است.</p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php echo $pagination ?? ''; ?>