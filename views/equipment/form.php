<?php $isEdit = !empty($item); ?>

<div class="page-header-row">
    <div class="page-header">
        <h2><i class="fas fa-dumbbell" style="margin-left:8px;"></i> <?php echo $isEdit ? 'ویرایش تجهیزات' : 'افزودن تجهیزات جدید'; ?></h2>
    </div>
    <div class="page-header-actions">
        <a href="<?php echo url('admin/equipment'); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i>
            بازگشت
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?php echo $isEdit ? url('admin/equipment/' . $item['id'] . '/update') : url('admin/equipment/store'); ?>">
            <?php echo csrf_field(); ?>

            <div class="form-group">
                <label>نام <span class="required">*</span></label>
                <input type="text" name="name" class="form-input" required value="<?php echo e($item['name'] ?? old('name') ?? ''); ?>">
            </div>

            <div class="form-group">
                <label>توضیحات</label>
                <textarea name="description" class="form-input" rows="3" placeholder="توضیحات تجهیز..."><?php echo e($item['description'] ?? old('description') ?? ''); ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>قیمت خرید (تومان)</label>
                    <input type="number" name="purchase_price" class="form-input" min="0" value="<?php echo e($item['purchase_price'] ?? old('purchase_price') ?? '0'); ?>">
                </div>
                <div class="form-group">
                    <label>تاریخ خرید</label>
                    <input type="text" name="purchase_date" class="form-input jalali-date" data-jalali value="<?php echo e(old('purchase_date') ?? ''); ?>" placeholder="انتخاب تاریخ" readonly>
                </div>
            </div>

            <div class="form-group">
                <label>وضعیت <span class="required">*</span></label>
                <select name="condition_status" class="form-input" required>
                    <option value="new" <?php echo (isset($item) && $item['condition_status'] === 'new') || old('condition_status') === 'new' ? 'selected' : ''; ?>>نو</option>
                    <option value="good" <?php echo (isset($item) && $item['condition_status'] === 'good') || old('condition_status') === 'good' ? 'selected' : ''; ?>>خوب</option>
                    <option value="fair" <?php echo (isset($item) && $item['condition_status'] === 'fair') || old('condition_status') === 'fair' ? 'selected' : ''; ?>>متوسط</option>
                    <option value="poor" <?php echo (isset($item) && $item['condition_status'] === 'poor') || old('condition_status') === 'poor' ? 'selected' : ''; ?>>ضعیف</option>
                    <option value="broken" <?php echo (isset($item) && $item['condition_status'] === 'broken') || old('condition_status') === 'broken' ? 'selected' : ''; ?>>خراب</option>
                </select>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>آخرین سرویس</label>
                    <input type="text" name="last_maintenance" class="form-input jalali-date" data-jalali value="<?php echo e(old('last_maintenance') ?? ''); ?>" placeholder="انتخاب تاریخ" readonly>
                </div>
                <div class="form-group">
                    <label>سرویس بعدی</label>
                    <input type="text" name="next_maintenance" class="form-input jalali-date" data-jalali value="<?php echo e(old('next_maintenance') ?? ''); ?>" placeholder="انتخاب تاریخ" readonly>
                </div>
            </div>

            <div style="margin-top:24px; display:flex; gap:12px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save" style="margin-left:8px;"></i>
                    ذخیره
                </button>
                <a href="<?php echo url('admin/equipment'); ?>" class="btn btn-secondary">انصراف</a>
            </div>
        </form>
    </div>
</div>