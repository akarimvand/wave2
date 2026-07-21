<?php $activeMenu = 'settings'; ?>
<div class="page-header-row">
    <div class="page-header">
        <h2 class="page-title">
            <i class="fas fa-images" style="color:#8B5CF6;margin-left:8px;"></i>
            <?php echo e($pageTitle); ?>
        </h2>
    </div>
    <div class="page-header-actions">
        <a href="<?php echo url('admin/sliders'); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i>
            بازگشت
        </a>
    </div>
</div>

<form method="POST" action="<?php echo $isEdit ? url('admin/sliders/' . $slider['id'] . '/update') : url('admin/sliders/store'); ?>" 
      enctype="multipart/form-data">
    <?php echo csrf_field(); ?>

    <div class="card" style="margin-bottom:16px;">
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">عنوان اسلاید <span class="required">*</span></label>
                <input type="text" name="title" class="form-input" required 
                       value="<?php echo e($slider['title'] ?? ''); ?>" 
                       placeholder="عنوان اسلاید را وارد کنید">
            </div>

            <div class="form-group">
                <label class="form-label">توضیحات</label>
                <textarea name="description" class="form-textarea" rows="3" 
                          placeholder="توضیحات اسلاید (اختیاری)"><?php echo e($slider['description'] ?? ''); ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">لینک (اختیاری)</label>
                    <input type="url" name="link_url" class="form-input" 
                           value="<?php echo e($slider['link_url'] ?? ''); ?>" 
                           placeholder="https://example.com">
                </div>
                <div class="form-group">
                    <label class="form-label">ترتیب نمایش</label>
                    <input type="number" name="sort_order" class="form-input" min="0" 
                           value="<?php echo e($slider['sort_order'] ?? 0); ?>">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">تصویر اسلاید</label>
                <div style="display:flex;align-items:flex-start;gap:16px;flex-wrap:wrap;">
                    <div style="flex:1;min-width:250px;">
                        <input type="file" name="image_file" accept="image/*" class="form-input" 
                               style="padding:8px 12px;">
                        <small style="color:#6b7280;font-size:0.78rem;margin-top:4px;display:block;">
                            فرمت‌های مجاز: JPG, PNG, GIF, WebP — حداکثر ۵ مگابایت
                        </small>
                    </div>
                    <?php if ($isEdit && !empty($slider['image_path'])): ?>
                    <div style="width:150px;height:100px;border-radius:12px;overflow:hidden;border:2px solid #e5e7eb;flex-shrink:0;background:#f8fafc;display:flex;align-items:center;justify-content:center;">
                        <img src="<?php echo asset($slider['image_path']); ?>" alt="تصویر فعلی" 
                             style="width:100%;height:100%;object-fit:contain;">
                    </div>
                    <div style="width:100%;">
                        <label style="display:inline-flex;align-items:center;gap:6px;cursor:pointer;">
                            <input type="checkbox" name="remove_image" value="1" style="width:16px;height:16px;">
                            <span style="font-size:0.85rem;color:#EF4444;">حذف تصویر فعلی</span>
                        </label>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-group">
                <label style="display:inline-flex;align-items:center;gap:8px;cursor:pointer;">
                    <input type="checkbox" name="is_active" value="1" 
                           <?php echo empty($slider) || ($slider['is_active'] ?? 1) ? 'checked' : ''; ?> 
                           style="width:18px;height:18px;">
                    <span style="font-weight:500;">فعال باشد</span>
                </label>
            </div>
        </div>
    </div>

    <div style="display:flex;justify-content:flex-end;gap:8px;">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save" style="margin-left:6px;"></i>
            ذخیره اسلاید
        </button>
        <a href="<?php echo url('admin/sliders'); ?>" class="btn btn-secondary">انصراف</a>
    </div>
</form>
