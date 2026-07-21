<div class="page-header-row">
    <div class="page-header">
        <h2><i class="fas fa-bell" style="margin-left:8px;color:#F59E0B;"></i>اعلانات</h2>
        <p>مدیریت اعلانات و پیام‌های سیستم</p>
    </div>
    <div class="page-header-actions">
        <a href="<?php echo url('admin/notifications/create'); ?>" class="btn btn-primary">
            <i class="fas fa-plus" style="margin-left:6px;"></i>
            اعلان جدید
        </a>
    </div>
</div>

<!-- Table -->
<div class="card">
    <div class="card-body">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ردیف</th>
                        <th>عنوان</th>
                        <th>نوع</th>
                        <th>گیرندگان</th>
                        <th>تاریخ ارسال</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($notifications)): ?>
                    <?php $row = 1; ?>
                    <?php foreach ($notifications as $notif): ?>
                    <?php
                    $type = $notif['type'] ?? 'all';
                    $typeClass = 'badge-primary';
                    $typeLabel = 'همه';
                    if ($type === 'sms') { $typeClass = 'badge-success'; $typeLabel = 'پیامک'; }
                    elseif ($type === 'email') { $typeClass = 'badge-info'; $typeLabel = 'ایمیل'; }
                    elseif ($type === 'push') { $typeClass = 'badge-purple'; $typeLabel = 'اعلان'; }
                    elseif ($type === 'all') { $typeClass = 'badge-primary'; $typeLabel = 'همه'; }

                    $target = $notif['target_role'] ?? $notif['target_type'] ?? 'all';
                    $targetLabel = 'همه';
                    if ($target === 'admin') $targetLabel = 'مدیران';
                    elseif ($target === 'manager') $targetLabel = 'مدیران ارشد';
                    elseif ($target === 'member') $targetLabel = 'اعضا';
                    elseif ($target === 'receptionist') $targetLabel = 'پذیرش';
                    elseif ($target === 'members') $targetLabel = 'اعضا';
                    ?>
                    <tr>
                        <td><?php echo $row++; ?></td>
                        <td>
                            <div style="display:flex;align-items:center;gap:8px;">
                                <i class="fas fa-bell" style="color:#F59E0B;font-size:14px;flex-shrink:0;"></i>
                                <?php echo e($notif['title']); ?>
                            </div>
                        </td>
                        <td><span class="badge <?php echo $typeClass; ?>"><?php echo $typeLabel; ?></span></td>
                        <td><?php echo e($targetLabel); ?></td>
                        <td><?php echo formatDateTime($notif['created_at'] ?? $notif['send_at'] ?? ''); ?></td>
                        <td>
                            <div class="table-actions">
                                <form method="POST" action="<?php echo url('admin/notifications/' . $notif['id'] . '/delete'); ?>" style="display:inline;" onsubmit="return confirmDelete('آیا از حذف این اعلان مطمئن هستید؟')">
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
            <?php if (empty($notifications)): ?>
            <div class="table-empty">
                <div class="empty-state">
                    <i class="fas fa-bell-slash" style="font-size:48px;color:#D1D5DB;"></i>
                    <h3>موردی یافت نشد</h3>
                    <p>هنوز اعلانی ثبت نشده است.</p>
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