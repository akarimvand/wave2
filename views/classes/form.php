<?php $isEdit = !empty($class); ?>
<?php
$persianDays = ['شنبه', 'یکشنبه', 'دوشنبه', 'سه‌شنبه', 'چهارشنبه', 'پنجشنبه', 'جمعه'];
$selectedDays = [];
if ($isEdit && !empty($class['schedule_days'])) {
    $selectedDays = array_map('trim', explode(',', $class['schedule_days']));
}
?>
<div class="page-header-row">
    <div class="page-header">
        <h2><?php echo $isEdit ? 'ویرایش کلاس' : 'افزودن کلاس جدید'; ?></h2>
        <p><?php echo $isEdit ? 'اطلاعات کلاس را ویرایش کنید' : 'اطلاعات کلاس جدید را وارد کنید'; ?></p>
    </div>
    <div class="page-header-actions">
        <a href="<?php echo url('admin/classes'); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i>
            بازگشت
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?php echo $isEdit ? url('admin/classes/' . $class['id'] . '/update') : url('admin/classes/store'); ?>">
            <?php echo csrf_field(); ?>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-tag"></i> نام کلاس <span class="required">*</span></label>
                    <input type="text" name="name" class="form-input" required placeholder="مثال: یوگا صبحگاهی" value="<?php echo e($class['name'] ?? old('name') ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-user-tie"></i> مربی</label>
                    <select name="coach_id" class="form-select">
                        <option value="">انتخاب مربی...</option>
                        <?php foreach ($coaches as $c): ?>
                            <option value="<?php echo e($c['id']); ?>" <?php echo ($isEdit && $class['coach_id'] == $c['id']) || old('coach_id') == $c['id'] ? 'selected' : ''; ?>>
                                <?php echo e($c['full_name'] ?? ($c['first_name'] . ' ' . $c['last_name'])); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label"><i class="fas fa-calendar-week"></i> روزهای برگزاری <span class="required">*</span></label>
                <div class="day-checkboxes" style="display:flex;flex-wrap:wrap;gap:12px;margin-top:8px;">
                    <?php foreach ($persianDays as $day): ?>
                        <label style="display:flex;align-items:center;gap:6px;cursor:pointer;background:#F3F4F6;padding:8px 16px;border-radius:8px;border:1px solid #E5E7EB;transition:all .2s;">
                            <input type="checkbox" name="schedule_days[]" value="<?php echo e($day); ?>"
                                <?php echo in_array($day, $selectedDays) || old('schedule_days') === $day ? 'checked' : ''; ?>
                                style="width:16px;height:16px;accent-color:#3B82F6;">
                            <span style="font-size:14px;"><?php echo e($day); ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
                <input type="hidden" name="schedule_days" id="schedule_days_hidden" value="">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-clock"></i> ساعت شروع</label>
                    <input type="time" name="start_time" class="form-input" value="<?php echo e($class['start_time'] ?? old('start_time') ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-clock"></i> ساعت پایان</label>
                    <input type="time" name="end_time" class="form-input" value="<?php echo e($class['end_time'] ?? old('end_time') ?? ''); ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-users"></i> ظرفیت (نفر) <span class="required">*</span></label>
                    <input type="number" name="max_participants" class="form-input" required min="1" placeholder="مثال: ۲۰" value="<?php echo e($class['max_participants'] ?? old('max_participants') ?? ''); ?>">
                </div>
                <div class="form-group" style="display:flex;align-items:flex-end;padding-bottom:4px;">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:14px;">
                        <input type="checkbox" name="is_active" value="1"
                            <?php echo ($isEdit && $class['is_active'] == 1) || old('is_active') === '1' ? 'checked' : ''; ?>
                            style="width:18px;height:18px;accent-color:#3B82F6;">
                        <i class="fas fa-check-circle" style="color:#10B981;"></i>
                        فعال بودن کلاس
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label"><i class="fas fa-align-left"></i> توضیحات</label>
                <textarea name="description" class="form-textarea" rows="3" placeholder="توضیحات تکمیلی درباره کلاس..."><?php echo e($class['description'] ?? old('description') ?? ''); ?></textarea>
            </div>

            <div class="form-actions" style="display:flex;gap:8px;margin-top:24px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    <?php echo $isEdit ? 'ذخیره تغییرات' : 'ذخیره'; ?>
                </button>
                <a href="<?php echo url('admin/classes'); ?>" class="btn btn-secondary">انصراف</a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var form = document.querySelector('form[method="POST"]');
    var hiddenInput = document.getElementById('schedule_days_hidden');

    form.addEventListener('submit', function() {
        var checked = form.querySelectorAll('input[name="schedule_days[]"]:checked');
        var values = [];
        checked.forEach(function(cb) {
            values.push(cb.value);
        });
        hiddenInput.value = values.join(',');
    });
});
</script>