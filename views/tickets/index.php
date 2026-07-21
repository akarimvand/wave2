<div class="page-header-row">
    <div class="page-header">
        <h2><i class="fas fa-headset" style="margin-left:8px;color:#3B82F6;"></i>تیکت‌ها</h2>
        <p>مدیریت تیکت‌ها و پشتیبانی اعضا</p>
    </div>
    <div class="page-header-actions"></div>
</div>

<!-- Search & Filter -->
<form method="GET" action="<?php echo url('admin/tickets'); ?>" class="search-bar">
    <div class="search-input-group">
        <i class="fas fa-search"></i>
        <input type="text" name="search" value="<?php echo e($search ?? ''); ?>" placeholder="جستجو بر اساس عنوان..." class="form-input">
    </div>
    <select name="status" class="search-filter">
        <option value="">همه وضعیت‌ها</option>
        <option value="open" <?php echo (($filters['status'] ?? '') === 'open') ? 'selected' : ''; ?>>باز</option>
        <option value="in_progress" <?php echo (($filters['status'] ?? '') === 'in_progress') ? 'selected' : ''; ?>>در حال بررسی</option>
        <option value="closed" <?php echo (($filters['status'] ?? '') === 'closed') ? 'selected' : ''; ?>>بسته</option>
    </select>
    <select name="priority" class="search-filter">
        <option value="">همه اولویت‌ها</option>
        <option value="high" <?php echo (($filters['priority'] ?? '') === 'high') ? 'selected' : ''; ?>>بالا</option>
        <option value="medium" <?php echo (($filters['priority'] ?? '') === 'medium') ? 'selected' : ''; ?>>متوسط</option>
        <option value="low" <?php echo (($filters['priority'] ?? '') === 'low') ? 'selected' : ''; ?>>پایین</option>
    </select>
    <button type="submit" class="btn btn-primary btn-sm">
        <i class="fas fa-search"></i> جستجو
    </button>
    <a href="<?php echo url('admin/tickets'); ?>" class="btn btn-secondary btn-sm">
        <i class="fas fa-times"></i> پاک کردن
    </a>
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
                        <th>فرستنده</th>
                        <th>اولویت</th>
                        <th>وضعیت</th>
                        <th>تاریخ</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($tickets)): ?>
                    <?php $row = 1; ?>
                    <?php foreach ($tickets as $ticket): ?>
                    <?php
                    $priority = $ticket['priority'] ?? 'low';
                    $pClass = 'badge-success';
                    $pLabel = 'پایین';
                    if ($priority === 'high') { $pClass = 'badge-danger'; $pLabel = 'بالا'; }
                    elseif ($priority === 'medium') { $pClass = 'badge-warning'; $pLabel = 'متوسط'; }

                    $status = $ticket['status'] ?? '';
                    $sClass = 'badge-secondary';
                    $sLabel = 'نامشخص';
                    if ($status === 'open') { $sClass = 'badge-info'; $sLabel = 'باز'; }
                    elseif ($status === 'closed') { $sClass = 'badge-secondary'; $sLabel = 'بسته'; }
                    elseif ($status === 'in_progress') { $sClass = 'badge-warning'; $sLabel = 'در حال بررسی'; }
                    ?>
                    <tr>
                        <td><?php echo $row++; ?></td>
                        <td>
                            <a href="<?php echo url('admin/tickets/' . $ticket['id']); ?>" style="color:#1E293B;text-decoration:none;font-weight:500;">
                                <?php echo e($ticket['title'] ?? $ticket['subject'] ?? ''); ?>
                            </a>
                        </td>
                        <td><?php echo e($ticket['member_name'] ?? $ticket['sender_name'] ?? ''); ?></td>
                        <td><span class="badge <?php echo $pClass; ?>"><?php echo $pLabel; ?></span></td>
                        <td><span class="badge <?php echo $sClass; ?>"><?php echo $sLabel; ?></span></td>
                        <td><?php echo formatDateTime($ticket['created_at']); ?></td>
                        <td>
                            <div class="table-actions">
                                <a href="<?php echo url('admin/tickets/' . $ticket['id']); ?>" class="btn btn-primary btn-xs" title="مشاهده">
                                    <i class="fas fa-eye"></i>
                                    مشاهده
                                </a>
                                <form method="POST" action="<?php echo url('admin/tickets/' . $ticket['id'] . '/delete'); ?>" style="display:inline;" onsubmit="return confirmDelete('آیا از حذف این تیکت مطمئن هستید؟')">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="btn btn-danger btn-xs" title="حذف">
                                        <i class="fas fa-trash-alt"></i>
                                        حذف
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
            <?php if (empty($tickets)): ?>
            <div class="table-empty">
                <div class="empty-state">
                    <i class="fas fa-headset" style="font-size:48px;color:#D1D5DB;"></i>
                    <h3>موردی یافت نشد</h3>
                    <p>هنوز تیکتی ثبت نشده است.</p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if (!empty($pagination)): ?>
<div class="pagination">
    <?php echo $pagination; ?>
</div>
<?php endif; ?>