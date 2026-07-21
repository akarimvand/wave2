<?php $isEdit = !empty($role); ?>
<?php
$rolePermissions = [];
if ($isEdit) {
    $raw = $role['permissions'] ?? [];
    if (is_string($raw)) {
        $rolePermissions = json_decode($raw, true) ?: [];
    } elseif (is_array($raw)) {
        $rolePermissions = $raw;
    }
}
$oldPermissions = old('permissions') ?? [];
?>

<div class="page-header-row">
    <div class="page-header">
        <h2><i class="fas fa-user-shield" style="margin-left:8px;color:#8B5CF6;"></i><?php echo $isEdit ? 'ویرایش نقش' : 'افزودن نقش جدید'; ?></h2>
        <p><?php echo $isEdit ? 'ویرایش اطلاعات و مجوزهای نقش' : 'تعریف نقش جدید و تنظیم دسترسی‌ها'; ?></p>
    </div>
    <div class="page-header-actions">
        <a href="<?php echo url('admin/roles'); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i>
            بازگشت
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?php echo $isEdit ? url('admin/roles/' . $role['id'] . '/update') : url('admin/roles/store'); ?>">
            <?php echo csrf_field(); ?>

            <div class="form-group">
                <label class="form-label">نام نقش <span class="required">*</span></label>
                <input type="text" name="name" class="form-input" required value="<?php echo e($role['name'] ?? old('name') ?? ''); ?>" placeholder="نام نقش را وارد کنید">
            </div>

            <div class="form-group">
                <label class="form-label">توضیحات</label>
                <textarea name="description" class="form-input" rows="3" placeholder="توضیحات مختصر درباره این نقش..."><?php echo e($role['description'] ?? old('description') ?? ''); ?></textarea>
            </div>

            <!-- Permissions -->
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-key" style="margin-left:4px;"></i>
                    مجوزها
                </label>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:12px;padding:16px;background:#F9FAFB;border-radius:8px;border:1px solid #E5E7EB;">
                    <?php
                    $allPermissions = [
                        ['value' => 'members_view', 'label' => 'مشاهده اعضا', 'icon' => 'fa-eye'],
                        ['value' => 'members_create', 'label' => 'ایجاد عضو', 'icon' => 'fa-user-plus'],
                        ['value' => 'members_edit', 'label' => 'ویرایش عضو', 'icon' => 'fa-user-edit'],
                        ['value' => 'members_delete', 'label' => 'حذف عضو', 'icon' => 'fa-user-minus'],
                        ['value' => 'classes_view', 'label' => 'مشاهده کلاس‌ها', 'icon' => 'fa-calendar-alt'],
                        ['value' => 'classes_create', 'label' => 'ایجاد کلاس', 'icon' => 'fa-calendar-plus'],
                        ['value' => 'payments_view', 'label' => 'مشاهده پرداخت‌ها', 'icon' => 'fa-credit-card'],
                        ['value' => 'reports_view', 'label' => 'مشاهده گزارشات', 'icon' => 'fa-chart-bar'],
                        ['value' => 'settings_manage', 'label' => 'مدیریت تنظیمات', 'icon' => 'fa-cog'],
                    ];
                    foreach ($allPermissions as $perm):
                        $isChecked = false;
                        if (!empty($oldPermissions)) {
                            $isChecked = in_array($perm['value'], $oldPermissions);
                        } else {
                            $isChecked = in_array($perm['value'], $rolePermissions);
                        }
                    ?>
                    <label style="display:flex;align-items:center;gap:8px;padding:8px 12px;background:#fff;border-radius:6px;border:1px solid #E5E7EB;cursor:pointer;transition:all 0.15s;">
                        <input type="checkbox" name="permissions[]" value="<?php echo $perm['value']; ?>" <?php echo $isChecked ? 'checked' : ''; ?>>
                        <i class="fas <?php echo $perm['icon']; ?>" style="color:#6b7280;font-size:13px;width:16px;text-align:center;"></i>
                        <span style="font-size:0.9rem;color:#374151;"><?php echo $perm['label']; ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div style="display:flex;gap:8px;margin-top:24px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save" style="margin-left:6px;"></i>
                    <?php echo $isEdit ? 'ذخیره تغییرات' : 'ذخیره نقش'; ?>
                </button>
                <a href="<?php echo url('admin/roles'); ?>" class="btn btn-secondary">
                    <i class="fas fa-times" style="margin-left:6px;"></i>
                    انصراف
                </a>
            </div>
        </form>
    </div>
</div>