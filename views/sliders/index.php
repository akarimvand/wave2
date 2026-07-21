<?php $activeMenu = 'settings'; ?>
<div class="page-header-row">
    <div class="page-header">
        <h2 class="page-title">
            <i class="fas fa-images" style="color:#8B5CF6;margin-left:8px;"></i>
            مدیریت اسلایدرها
        </h2>
        <p>مدیریت اسلایدرهای نمایشی صفحه اعضا</p>
    </div>
    <div class="page-header-actions">
        <a href="<?php echo url('admin/sliders/create'); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            اسلاید جدید
        </a>
    </div>
</div>

<!-- Info Banner -->
<div style="padding:14px 20px;background:#F5F3FF;border:1px solid #DDD6FE;border-radius:10px;margin-bottom:20px;display:flex;align-items:center;gap:10px;">
    <i class="fas fa-info-circle" style="color:#8B5CF6;font-size:1.1rem;"></i>
    <span style="font-size:0.88rem;color:#6D28D9;">
        اسلایدرها در صفحه اعضا نمایش داده می‌شوند. مدیر محتوا می‌تواند تصاویر، عناوین و توضیحات را مدیریت کند.
    </span>
</div>

<?php if (!empty($sliders)): ?>
<div class="card">
    <div class="card-body">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width:60px;">ترتیب</th>
                        <th>تصویر</th>
                        <th>عنوان</th>
                        <th class="hide-mobile">توضیحات</th>
                        <th class="hide-mobile">لینک</th>
                        <th>وضعیت</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sliders as $slider): ?>
                    <tr>
                        <td>
                            <span style="background:#F3F4F6;padding:4px 10px;border-radius:8px;font-weight:600;font-size:0.85rem;">
                                <?php echo $slider['sort_order']; ?>
                            </span>
                        </td>
                        <td>
                            <div style="width:80px;height:50px;border-radius:8px;overflow:hidden;background:#F3F4F6;">
                                <img src="<?php echo asset($slider['image_path']); ?>" alt="<?php echo e($slider['title']); ?>" 
                                     style="width:100%;height:100%;object-fit:cover;">
                            </div>
                        </td>
                        <td style="font-weight:500;"><?php echo e($slider['title']); ?></td>
                        <td class="hide-mobile" style="max-width:300px;font-size:0.85rem;color:#6B7A8D;">
                            <?php echo e(mb_substr(strip_tags($slider['description'] ?? ''), 0, 80)) . (mb_strlen(strip_tags($slider['description'] ?? '')) > 80 ? '...' : ''); ?>
                        </td>
                        <td class="hide-mobile">
                            <?php if (!empty($slider['link_url'])): ?>
                            <a href="<?php echo e($slider['link_url']); ?>" target="_blank" style="color:#3B82F6;font-size:0.85rem;">
                                <i class="fas fa-external-link-alt" style="margin-left:4px;"></i>
                                لینک
                            </a>
                            <?php else: ?>
                            <span style="color:#9CA3AF;font-size:0.82rem;">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($slider['is_active']): ?>
                            <span class="badge badge-success" style="cursor:pointer;" onclick="toggleSliderStatus(<?php echo $slider['id']; ?>)">
                                <i class="fas fa-check-circle" style="margin-left:4px;"></i>فعال
                            </span>
                            <?php else: ?>
                            <span class="badge badge-secondary" style="cursor:pointer;" onclick="toggleSliderStatus(<?php echo $slider['id']; ?>)">
                                <i class="fas fa-times-circle" style="margin-left:4px;"></i>غیرفعال
                            </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="table-actions">
                                <a href="<?php echo url('admin/sliders/' . $slider['id'] . '/edit'); ?>" class="btn btn-warning btn-xs" title="ویرایش">
                                    <i class="fas fa-pen-to-square"></i>
                                    ویرایش
                                </a>
                                <form method="POST" action="<?php echo url('admin/sliders/' . $slider['id'] . '/delete'); ?>" 
                                      style="display:inline;" 
                                      onsubmit="return confirm('آیا از حذف این اسلاید اطمینان دارید؟')">
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
                <i class="fas fa-images" style="font-size:48px;color:#D1D5DB;margin-bottom:16px;display:block;"></i>
                <h3>اسلایدی وجود ندارد</h3>
                <p>اولین اسلاید خود را ایجاد کنید.</p>
                <a href="<?php echo url('admin/sliders/create'); ?>" class="btn btn-primary btn-sm" style="margin-top:12px;">
                    <i class="fas fa-plus"></i>
                    اسلاید جدید
                </a>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
function toggleSliderStatus(sliderId) {
    fetch('<?php echo url("admin/sliders"); ?>/' + sliderId + '/toggle', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            _token: '<?php echo csrf_token(); ?>'
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'خطا در تغییر وضعیت');
        }
    })
    .catch(() => alert('خطا در ارتباط با سرور'));
}
</script>
