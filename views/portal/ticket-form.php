<div class="page-header">
    <h2 class="page-title"><i class="fas fa-headset" style="margin-left:8px;color:#3B82F6;"></i>تیکت جدید</h2>
    <p>ثبت تیکت پشتیبانی</p>
</div>

<div class="page-actions" style="margin-bottom:20px;">
    <a href="<?php echo url('portal/tickets'); ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-right" style="margin-left:6px;"></i>
        بازگشت به لیست
    </a>
</div>

<div class="card">
    <form method="POST" action="<?php echo url('portal/tickets/store'); ?>" data-validate>
        <?php echo csrf_field(); ?>

        <div class="card-body">
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-heading" style="margin-left:6px;color:#6B7A8D;"></i>
                    عنوان <span class="required">*</span>
                </label>
                <input type="text" name="subject" class="form-input" required placeholder="عنوان تیکت خود را وارد کنید" value="<?php echo e(old('subject') ?? ''); ?>">
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-flag" style="margin-left:6px;color:#6B7A8D;"></i>
                    اولویت
                </label>
                <select name="priority" class="form-input">
                    <option value="medium" <?php echo (old('priority') ?? '') === 'medium' ? 'selected' : ''; ?>>
                        <i class="fas fa-minus-circle"></i> متوسط
                    </option>
                    <option value="high" <?php echo (old('priority') ?? '') === 'high' ? 'selected' : ''; ?>>
                        بالا
                    </option>
                    <option value="low" <?php echo (old('priority') ?? '') === 'low' ? 'selected' : ''; ?>>
                        پایین
                    </option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-comment-dots" style="margin-left:6px;color:#6B7A8D;"></i>
                    پیام <span class="required">*</span>
                </label>
                <textarea name="description" class="form-input" rows="6" required placeholder="متن پیام خود را بنویسید..." style="min-height:140px;"><?php echo e(old('description') ?? ''); ?></textarea>
            </div>
        </div>

        <div style="padding:16px 20px;border-top:1px solid #E5E7EB;display:flex;gap:12px;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-paper-plane" style="margin-left:6px;"></i>
                ارسال تیکت
            </button>
            <a href="<?php echo url('portal/tickets'); ?>" class="btn btn-secondary">
                <i class="fas fa-times" style="margin-left:6px;"></i>
                انصراف
            </a>
        </div>
    </form>
</div>