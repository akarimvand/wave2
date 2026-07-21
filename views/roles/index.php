<div class="page-header-row">
    <div class="page-header">
        <h2><i class="fas fa-user-shield" style="margin-left:8px;color:#8B5CF6;"></i>نقش‌ها و مجوزها</h2>
        <p>مدیریت نقش‌ها و سطح دسترسی کاربران</p>
    </div>
    <div class="page-header-actions">
        <a href="<?php echo url('admin/roles/create'); ?>" class="btn btn-primary">
            <i class="fas fa-plus" style="margin-left:6px;"></i>
            نقش جدید
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
                        <th>نام نقش</th>
                        <th>تعداد کاربران</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($roles)): ?>
                    <?php $row = 1; ?>
                    <?php foreach ($roles as $role): ?>
                    <tr>
                        <td><?php echo $row++; ?></td>
                        <td>
                            <div style="display:flex;align-items:center;gap:8px;">
                                <i class="fas fa-shield-alt" style="color:#8B5CF6;font-size:14px;flex-shrink:0;"></i>
                                <div>
                                    <div style="font-weight:500;color:#1f2937;"><?php echo e($role['display_name'] ?? $role['name'] ?? ''); ?></div>
                                    <?php if (!empty($role['description'])): ?>
                                    <div style="font-size:0.8rem;color:#9CA3AF;margin-top:2px;"><?php echo e($role['description']); ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span style="display:inline-flex;align-items:center;gap:4px;">
                                <i class="fas fa-users" style="color:#6b7280;font-size:12px;"></i>
                                <?php echo e($role['users_count'] ?? 0); ?>
                            </span>
                        </td>
                        <td>
                            <div class="table-actions">
                                <a href="<?php echo url('admin/roles/' . $role['id'] . '/edit'); ?>" class="btn btn-primary btn-xs" title="ویرایش">
                                    <i class="fas fa-edit"></i>
                                    ویرایش
                                </a>
                                <form method="POST" action="<?php echo url('admin/roles/' . $role['id'] . '/delete'); ?>" style="display:inline;" onsubmit="return confirmDelete('آیا از حذف این نقش مطمئن هستید؟')">
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
            <?php if (empty($roles)): ?>
            <div class="table-empty">
                <div class="empty-state">
                    <i class="fas fa-user-shield" style="font-size:48px;color:#D1D5DB;"></i>
                    <h3>موردی یافت نشد</h3>
                    <p>هنوز نقشی ثبت نشده است.</p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>