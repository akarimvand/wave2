<?php $activeMenu = 'settings'; ?>
<div class="page-header-row">
    <div class="page-header">
        <h2 class="page-title">
            <i class="fas fa-book" style="color:#F59E0B;margin-left:8px;"></i>
            مدیریت راهنماهای نقش‌محور
        </h2>
        <p>راهنماهای کمکی برای هر نقش و صفحه</p>
    </div>
    <div class="page-header-actions">
        <a href="<?php echo url('admin/help-guides/create'); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            راهنمای جدید
        </a>
    </div>
</div>

<?php if (!empty($guides)): ?>
<div class="card">
    <div class="card-body">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>نقش</th>
                        <th>کلید صفحه</th>
                        <th>عنوان</th>
                        <th class="hide-mobile">محتوا</th>
                        <th>وضعیت</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($guides as $guide): ?>
                    <tr>
                        <td>
                            <span style="background:#F3F4F6;padding:4px 10px;border-radius:8px;font-size:0.8rem;font-weight:600;">
                                <?php echo e($guide['role_name']); ?>
                            </span>
                        </td>
                        <td><code style="font-size:0.8rem;"><?php echo e($guide['page_key']); ?></code></td>
                        <td style="font-weight:500;"><?php echo e($guide['title']); ?></td>
                        <td class="hide-mobile" style="max-width:300px;font-size:0.85rem;color:#6B7A8D;">
                            <?php echo e(mb_substr(strip_tags($guide['content']), 0, 80)) . (mb_strlen(strip_tags($guide['content'])) > 80 ? '...' : ''); ?>
                        </td>
                        <td>
                            <?php if ($guide['is_active']): ?>
                            <span class="badge badge-success">فعال</span>
                            <?php else: ?>
                            <span class="badge badge-secondary">غیرفعال</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="table-actions">
                                <a href="<?php echo url('admin/help-guides/' . $guide['id'] . '/edit'); ?>" class="btn btn-warning btn-xs" title="ویرایش">
                                    <i class="fas fa-pen-to-square"></i>
                                    ویرایش
                                </a>
                                <form method="POST" action="<?php echo url('admin/help-guides/' . $guide['id'] . '/delete'); ?>" 
                                      style="display:inline;" 
                                      onsubmit="return confirm('آیا از حذف این راهنما اطمینان دارید؟')">
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
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php else: ?>
<div class="card">
    <div class="card-body">
        <div class="table-empty">
            <div class="empty-state">
                <i class="fas fa-book" style="font-size:48px;color:#D1D5DB;margin-bottom:16px;display:block;"></i>
                <h3>راهنمایی وجود ندارد</h3>
                <p>اولین راهنما را ایجاد کنید.</p>
                <a href="<?php echo url('admin/help-guides/create'); ?>" class="btn btn-primary btn-sm" style="margin-top:12px;">
                    <i class="fas fa-plus"></i>
                    راهنمای جدید
                </a>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
