<?php $isEdit = !empty($isEdit); ?>

<div class="page-header-row">
    <div class="page-header">
        <h2><i class="fas fa-shield-alt" style="margin-left:8px;"></i> <?php echo $isEdit ? 'ویرایش بیمه' : 'ثبت بیمه عضو'; ?></h2>
        <p><?php echo $isEdit ? 'اطلاعات بیمه را ویرایش کنید' : 'اطلاعات بیمه جدید را وارد کنید'; ?></p>
    </div>
    <div class="page-header-actions">
        <a href="<?php echo url('admin/insurance'); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i>
            بازگشت
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?php echo $isEdit ? url('admin/insurance/' . $insurance['id'] . '/update') : url('admin/insurance/store'); ?>" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>

            <div class="form-group">
                <label class="form-label"><i class="fas fa-user" style="margin-left:6px;"></i> عضو <span class="required">*</span></label>
                <select name="member_id" class="form-select" required>
                    <option value="">انتخاب عضو...</option>
                    <?php if (!empty($members)): ?>
                    <?php foreach ($members as $m): ?>
                    <option value="<?php echo e($m['id']); ?>" <?php echo (isset($insurance) && $insurance['member_id'] == $m['id']) ? 'selected' : ''; ?><?php echo old('member_id') == $m['id'] ? 'selected' : ''; ?>>
                        <?php echo e($m['first_name'] . ' ' . $m['last_name'] . ' (' . $m['national_code'] . ')'); ?>
                    </option>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-hashtag" style="margin-left:6px;"></i> شماره بیمه‌نامه</label>
                    <input type="text" name="policy_number" class="form-input" placeholder="شماره بیمه‌نامه" value="<?php echo e($insurance['policy_number'] ?? old('policy_number') ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-tag" style="margin-left:6px;"></i> نوع بیمه</label>
                    <input type="text" name="insurance_type" class="form-input" placeholder="مثال: بیمه تکمیلی" value="<?php echo e($insurance['insurance_type'] ?? old('insurance_type') ?? ''); ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-calendar-alt" style="margin-left:6px;"></i> تاریخ شروع <span class="required">*</span></label>
                    <input type="text" name="start_date" class="form-input jalali-date" data-datepicker required placeholder="انتخاب تاریخ" value="<?php echo e($insurance['start_date'] ?? old('start_date') ?? ''); ?>" readonly>
                </div>
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-calendar-check" style="margin-left:6px;"></i> تاریخ پایان <span class="required">*</span></label>
                    <input type="text" name="end_date" class="form-input jalali-date" data-datepicker required placeholder="انتخاب تاریخ" value="<?php echo e($insurance['end_date'] ?? old('end_date') ?? ''); ?>" readonly>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-money-bill-wave" style="margin-left:6px;"></i> حق بیمه (تومان)</label>
                    <input type="number" name="premium_amount" class="form-input" placeholder="مبلغ حق بیمه" value="<?php echo e($insurance['premium_amount'] ?? old('premium_amount') ?? '0'); ?>" min="0">
                </div>
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-toggle-on" style="margin-left:6px;"></i> وضعیت</label>
                    <select name="status" class="form-select">
                        <option value="active" <?php echo (isset($insurance) && $insurance['status'] === 'active') || old('status') === 'active' ? 'selected' : ''; ?>>فعال</option>
                        <option value="expired" <?php echo (isset($insurance) && $insurance['status'] === 'expired') || old('status') === 'expired' ? 'selected' : ''; ?>>منقضی</option>
                        <option value="cancelled" <?php echo (isset($insurance) && $insurance['status'] === 'cancelled') || old('status') === 'cancelled' ? 'selected' : ''; ?>>لغو شده</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label"><i class="fas fa-file-upload" style="margin-left:6px;"></i> فایل بیمه‌نامه</label>
                <input type="file" name="document" id="document_file" class="form-input" accept=".pdf,.jpg,.jpeg,.png,.gif,.webp">
                <input type="hidden" name="document_path" id="document_path" value="<?php echo e($insurance['document_path'] ?? old('document_path') ?? ''); ?>">
                <div id="upload_status" style="margin-top:6px;font-size:13px;"></div>
                <?php if (!empty($insurance['document_path'])): ?>
                <div id="existing_file" style="margin-top:8px;">
                    <a href="<?php echo url(ltrim($insurance['document_path'], '/')); ?>" target="_blank" class="btn btn-secondary btn-sm" style="margin-left:8px;">
                        <i class="fas fa-download"></i>
                        دانلود فایل فعلی
                    </a>
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeDocument()">
                        <i class="fas fa-trash"></i>
                        حذف فایل
                    </button>
                </div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label class="form-label"><i class="fas fa-sticky-note" style="margin-left:6px;"></i> یادداشت</label>
                <textarea name="notes" class="form-textarea" rows="3" placeholder="توضیحات اختیاری..."><?php echo e($insurance['notes'] ?? old('notes') ?? ''); ?></textarea>
            </div>

            <div class="form-actions" style="display:flex;gap:8px;margin-top:24px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save" style="margin-left:6px;"></i>
                    <?php echo $isEdit ? 'ذخیره تغییرات' : 'ذخیره'; ?>
                </button>
                <a href="<?php echo url('admin/insurance'); ?>" class="btn btn-secondary">
                    <i class="fas fa-times" style="margin-left:6px;"></i>
                    انصراف
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var fileInput = document.getElementById('document_file');
    var pathInput = document.getElementById('document_path');
    var statusDiv = document.getElementById('upload_status');

    if (fileInput) {
        fileInput.addEventListener('change', function() {
            var file = this.files[0];
            if (!file) return;

            var formData = new FormData();
            formData.append('file', file);

            statusDiv.innerHTML = '<span style="color:#2563eb;"><i class="fas fa-spinner fa-spin"></i> در حال آپلود...</span>';

            var xhr = new XMLHttpRequest();
            xhr.open('POST', '<?php echo url("upload"); ?>', true);

            xhr.onload = function() {
                try {
                    var res = JSON.parse(xhr.responseText);
                    if (res.success) {
                        pathInput.value = res.file_path;
                        statusDiv.innerHTML = '<span style="color:#16a34a;"><i class="fas fa-check-circle"></i> ' + (res.message || 'فایل آپلود شد.') + '</span>';
                    } else {
                        statusDiv.innerHTML = '<span style="color:#dc2626;"><i class="fas fa-exclamation-circle"></i> ' + (res.message || 'خطا در آپلود.') + '</span>';
                        pathInput.value = '';
                    }
                } catch (e) {
                    statusDiv.innerHTML = '<span style="color:#dc2626;"><i class="fas fa-exclamation-circle"></i> خطا در پردازش پاسخ.</span>';
                }
            };

            xhr.onerror = function() {
                statusDiv.innerHTML = '<span style="color:#dc2626;"><i class="fas fa-exclamation-circle"></i> خطا در ارتباط با سرور.</span>';
            };

            xhr.send(formData);
        });
    }
});

function removeDocument() {
    var pathInput = document.getElementById('document_path');
    var existingDiv = document.getElementById('existing_file');
    if (pathInput) pathInput.value = '';
    if (existingDiv) existingDiv.style.display = 'none';
}
</script>