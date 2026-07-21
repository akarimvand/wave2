<?php $isEdit = isset($membership) && !empty($membership); ?>

<div class="page-header-row">
    <div class="page-header">
        <h2 class="page-title"><?php echo $isEdit ? 'ویرایش طرح اشتراک' : 'ایجاد طرح اشتراک جدید'; ?></h2>
        <p><?php echo $isEdit ? 'اطلاعات طرح اشتراک را ویرایش کنید' : 'اطلاعات طرح جدید را وارد کنید'; ?></p>
    </div>
    <div class="page-header-actions">
        <a href="<?php echo url('admin/memberships'); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i>
            بازگشت به لیست
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?php echo $isEdit ? url('admin/memberships/' . $membership['id'] . '/update') : url('admin/memberships/store'); ?>">
            <?php echo csrf_field(); ?>

            <!-- Row 1: name, duration_days -->
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">نام اشتراک <span class="required">*</span></label>
                    <input type="text" name="name" class="form-input" required value="<?php echo e($isEdit ? $membership['name'] : old('name') ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">مدت به روز <span class="required">*</span></label>
                    <input type="number" name="duration_days" class="form-input" required min="1" value="<?php echo e($isEdit ? $membership['duration_days'] : old('duration_days') ?? ''); ?>">
                </div>
            </div>

            <!-- Row 2: price, max_sessions -->
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">قیمت (تومان) <span class="required">*</span></label>
                    <input type="number" name="price" class="form-input" required min="0" value="<?php echo e($isEdit ? $membership['price'] : old('price') ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">حداکثر جلسات</label>
                    <input type="number" name="max_sessions" class="form-input" min="0" placeholder="خالی = نامحدود" value="<?php echo e($isEdit ? $membership['max_sessions'] : old('max_sessions') ?? ''); ?>">
                </div>
            </div>

            <!-- Row 3: description -->
            <div class="form-group">
                <label class="form-label">توضیحات</label>
                <textarea name="description" class="form-textarea" rows="3"><?php echo e($isEdit ? $membership['description'] : old('description') ?? ''); ?></textarea>
            </div>

            <!-- Row 4: status -->
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">وضعیت</label>
                    <select name="status" class="form-input">
                        <option value="active" <?php echo ($isEdit && ($membership['status'] ?? $membership['is_active'] ?? '') === 'active') || ($isEdit && ($membership['is_active'] ?? 0) == 1) || (!$isEdit && old('status') === 'active') ? 'selected' : ''; ?>>فعال</option>
                        <option value="inactive" <?php echo ($isEdit && ($membership['status'] ?? $membership['is_active'] ?? '') === 'inactive') || ($isEdit && ($membership['is_active'] ?? 0) == 0) || (!$isEdit && old('status') === 'inactive') ? 'selected' : ''; ?>>غیرفعال</option>
                    </select>
                </div>
            </div>

            <!-- Actions -->
            <div class="form-actions" style="display:flex;gap:8px;margin-top:24px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    <?php echo $isEdit ? 'ذخیره تغییرات' : 'ثبت طرح'; ?>
                </button>
                <a href="<?php echo url('admin/memberships'); ?>" class="btn btn-secondary">انصراف</a>
            </div>
        </form>
    </div>
</div>