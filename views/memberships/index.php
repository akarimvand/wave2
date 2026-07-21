<div class="page-header-row">
    <div class="page-header">
        <h2 class="page-title">مدیریت اشتراک‌ها</h2>
        <p>طرح‌های عضویت باشگاه</p>
    </div>
    <div class="page-header-actions">
        <a href="<?php echo url('admin/memberships/create'); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            طرح جدید
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
                        <th>نام</th>
                        <th>مدت (روز)</th>
                        <th>قیمت</th>
                        <th>تعداد اعضا</th>
                        <th>وضعیت</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($memberships)): ?>
                    <?php $rowNumber = 0; foreach ($memberships as $item): $rowNumber++; ?>
                    <tr>
                        <td><?php echo $rowNumber; ?></td>
                        <td><?php echo e($item['name']); ?></td>
                        <td><?php echo e($item['duration_days']); ?></td>
                        <td><?php echo formatCurrency($item['price']); ?></td>
                        <td><?php echo e($item['member_count'] ?? 0); ?></td>
                        <td>
                            <?php if (($item['status'] ?? $item['is_active'] ?? 1) == 'active' || ($item['status'] ?? $item['is_active'] ?? 1) == 1): ?>
                            <span class="badge badge-success">فعال</span>
                            <?php else: ?>
                            <span class="badge badge-warning">غیرفعال</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="table-actions">
                                <a href="<?php echo url('admin/memberships/' . $item['id'] . '/edit'); ?>" class="btn btn-warning btn-xs" title="ویرایش">
                                    <i class="fas fa-pen-to-square"></i>
                                    ویرایش
                                </a>
                                <form method="POST" action="<?php echo url('admin/memberships/' . $item['id'] . '/delete'); ?>" style="display:inline;" onclick="return confirmDelete('آیا از حذف این طرح اشتراک اطمینان دارید؟')">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="btn btn-danger btn-xs" title="حذف">
                                        <i class="fas fa-trash"></i>
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
            <?php if (empty($memberships)): ?>
            <div class="table-empty">
                <div class="empty-state">
                    <i class="fas fa-id-card" style="font-size:48px;color:#cbd5e1;margin-bottom:16px;display:block;"></i>
                    <h3>موردی یافت نشد</h3>
                    <p>هنوز طرح اشتراکی ثبت نشده است.</p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php echo pagination($page ?? 1, $total ?? 0, $perPage ?? 20, 'admin/memberships'); ?>