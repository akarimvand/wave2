<?php $isEdit = !empty($event); ?>
<div class="page-header-row">
    <div class="page-header">
        <h2><?php echo $isEdit ? 'ویرایش رویداد' : 'افزودن رویداد جدید'; ?></h2>
        <p><?php echo $isEdit ? 'اطلاعات رویداد را ویرایش کنید' : 'اطلاعات رویداد جدید را وارد کنید'; ?></p>
    </div>
    <div class="page-header-actions">
        <a href="<?php echo url('admin/events'); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i>
            بازگشت
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?php echo $isEdit ? url('admin/events/' . $event['id'] . '/update') : url('admin/events/store'); ?>">
            <?php echo csrf_field(); ?>

            <div class="form-group">
                <label class="form-label">عنوان <span class="required">*</span></label>
                <input type="text" name="title" class="form-input" required value="<?php echo e($event['title'] ?? old('title') ?? ''); ?>">
            </div>

            <div class="form-group">
                <label class="form-label">توضیحات</label>
                <textarea name="description" class="form-textarea" rows="4"><?php echo e($event['description'] ?? old('description') ?? ''); ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">تاریخ رویداد <span class="required">*</span></label>
                    <input type="text" name="event_date" class="form-input jalali-date" data-datepicker required value="<?php echo e($event['event_date'] ?? old('event_date') ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">مکان</label>
                    <input type="text" name="location" class="form-input" placeholder="مثال: سالن اصلی باشگاه" value="<?php echo e($event['location'] ?? old('location') ?? ''); ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">ظرفیت</label>
                    <input type="number" name="capacity" class="form-input" min="0" value="<?php echo e($event['capacity'] ?? old('capacity') ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">وضعیت <span class="required">*</span></label>
                    <select name="status" class="form-select" required>
                        <option value="active" <?php echo (isset($event) && $event['status'] === 'active') || old('status') === 'active' ? 'selected' : ''; ?>>فعال</option>
                        <option value="cancelled" <?php echo (isset($event) && $event['status'] === 'cancelled') || old('status') === 'cancelled' ? 'selected' : ''; ?>>لغو شده</option>
                    </select>
                </div>
            </div>

            <div class="form-actions" style="display:flex;gap:8px;margin-top:24px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    <?php echo $isEdit ? 'ذخیره تغییرات' : 'ذخیره'; ?>
                </button>
                <a href="<?php echo url('admin/events'); ?>" class="btn btn-secondary">انصراف</a>
            </div>
        </form>
    </div>
</div>