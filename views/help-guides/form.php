<?php $activeMenu = 'settings'; ?>
<div class="page-header-row">
    <div class="page-header">
        <h2 class="page-title">
            <i class="fas fa-book" style="color:#F59E0B;margin-left:8px;"></i>
            <?php echo e($pageTitle); ?>
        </h2>
    </div>
    <div class="page-header-actions">
        <a href="<?php echo url('admin/help-guides'); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i>
            بازگشت
        </a>
    </div>
</div>

<form method="POST" action="<?php echo $isEdit ? url('admin/help-guides/' . $guide['id'] . '/update') : url('admin/help-guides/store'); ?>">
    <?php echo csrf_field(); ?>

    <div class="card" style="margin-bottom:16px;">
        <div class="card-body">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">نقش <span class="required">*</span></label>
                    <select name="role_name" class="form-input" required <?php echo $isEdit ? 'disabled' : ''; ?>>
                        <option value="">انتخاب نقش</option>
                        <?php foreach ($roles as $role): ?>
                        <option value="<?php echo $role; ?>" 
                                <?php echo ($guide['role_name'] ?? '') === $role ? 'selected' : ''; ?>>
                            <?php 
                            $roleNames = [
                                'admin' => 'مدیر',
                                'coach' => 'مربی',
                                'receptionist' => 'پذیرش',
                                'accountant' => 'حسابدار',
                                'member' => 'عضو'
                            ];
                            echo e($roleNames[$role] ?? $role); 
                            ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($isEdit): ?>
                    <input type="hidden" name="role_name" value="<?php echo e($guide['role_name']); ?>">
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label class="form-label">کلید صفحه <span class="required">*</span></label>
                    <input type="text" name="page_key" class="form-input" required 
                           value="<?php echo e($guide['page_key'] ?? ''); ?>" 
                           placeholder="مثلاً: dashboard, members, classes">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">عنوان راهنما <span class="required">*</span></label>
                <input type="text" name="title" class="form-input" required 
                       value="<?php echo e($guide['title'] ?? ''); ?>" 
                       placeholder="عنوان راهنما را وارد کنید">
            </div>

            <div class="form-group">
                <label class="form-label">محتوا (HTML مجاز)</label>
                <textarea name="content" class="form-textarea" rows="6" 
                          placeholder="محتوای راهنما را وارد کنید"><?php echo e($guide['content'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">لینک ویدیو آموزشی (اختیاری)</label>
                <input type="url" name="video_url" class="form-input" 
                       value="<?php echo e($guide['video_url'] ?? ''); ?>" 
                       placeholder="https://example.com/video.mp4">
            </div>

            <div class="form-group">
                <label class="form-label">نکات کلیدی (هر نکته در یک خط)</label>
                <textarea name="tips[]" class="form-textarea" rows="4" 
                          placeholder="هر نکته مهم را در یک خط بنویسید"><?php echo !empty($tipsArray) ? implode("\n", $tipsArray) : ''; ?></textarea>
                <small style="color:#6b7280;font-size:0.78rem;">هر خط به عنوان یک نکته جداگانه ذخیره می‌شود.</small>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">ترتیب نمایش</label>
                    <input type="number" name="sort_order" class="form-input" min="0" 
                           value="<?php echo e($guide['sort_order'] ?? 0); ?>">
                </div>
                <div class="form-group">
                    <label style="display:inline-flex;align-items:center;gap:8px;cursor:pointer;margin-top:32px;">
                        <input type="checkbox" name="is_active" value="1" 
                               <?php echo empty($guide) || ($guide['is_active'] ?? 1) ? 'checked' : ''; ?> 
                               style="width:18px;height:18px;">
                        <span style="font-weight:500;">فعال باشد</span>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <div style="display:flex;justify-content:flex-end;gap:8px;">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save" style="margin-left:6px;"></i>
            ذخیره راهنما
        </button>
        <a href="<?php echo url('admin/help-guides'); ?>" class="btn btn-secondary">انصراف</a>
    </div>
</form>
