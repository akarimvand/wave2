<?php $activeMenu = 'attendance'; $pageTitle = 'ثبت حضور و غیاب'; ?>

<div class="page-header-row">
    <div class="page-header">
        <h2 class="page-title">
            <i class="fas fa-clipboard-check" style="color:#3B82F6;margin-left:8px;"></i>
            ثبت حضور و غیاب
        </h2>
        <p>
            <?php echo e($class['name']); ?>
            &nbsp;|&nbsp;
            <?php echo formatDate($selectedDate); ?>
        </p>
    </div>
    <div class="page-header-actions">
        <a href="<?php echo url('coach/attendance?date=' . urlencode($selectedDate)); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i>
            بازگشت
        </a>
    </div>
</div>

<?php if (!empty($students)): ?>
<form method="POST" action="<?php echo url('coach/attendance/save'); ?>">
    <input type="hidden" name="class_id" value="<?php echo $class['id']; ?>">
    <input type="hidden" name="date" value="<?php echo e($selectedDate); ?>">
    <?php echo csrf_field(); ?>

    <div class="card">
        <div class="card-body">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:20px;flex-wrap:wrap;">
                <button type="button" class="btn btn-sm btn-primary" onclick="setAllStatus('present')">
                    <i class="fas fa-check-double"></i>
                    همه حاضر
                </button>
                <button type="button" class="btn btn-sm btn-secondary" onclick="setAllStatus('absent')">
                    <i class="fas fa-times"></i>
                    همه غایب
                </button>
            </div>

            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width:40px;">ردیف</th>
                            <th>نام و نام خانوادگی</th>
                            <th class="hide-mobile" style="width:130px;">اشتراک</th>
                            <th class="hide-mobile" style="width:90px;">بیمه</th>
                            <th style="width:200px;">وضعیت</th>
                            <th class="hide-mobile" style="width:200px;">توضیحات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $row = 0; foreach ($students as $s): $row++;
                        $hasMembership = !empty($s['membership_status']) && $s['membership_status'] === 'active';
                        $hasInsurance = !empty($s['insurance_status']) && $s['insurance_status'] === 'active';
                        $rowBg = !$hasMembership ? '#FFF5F5' : '';
                        ?>
                        <input type="hidden" name="attendance_id[<?php echo $s['registration_id']; ?>]" value="<?php echo $s['attendance_id'] ?? ''; ?>">
                        <tr style="<?php echo $rowBg ? 'background:' . $rowBg . ';' : ''; ?>">
                            <td><?php echo $row; ?></td>
                            <td style="font-weight:500;">
                                <?php echo e($s['first_name'] . ' ' . $s['last_name']); ?>
                                <?php if (!$hasMembership): ?>
                                <span style="font-size:0.72rem;color:#EF4444;margin-right:6px;" title="بدون اشتراک فعال">
                                    <i class="fas fa-exclamation-circle"></i> بدون اشتراک
                                </span>
                                <?php endif; ?>
                            </td>
                            <td class="hide-mobile" style="font-size:0.82rem;">
                                <?php if ($hasMembership): ?>
                                <span style="color:#10B981;">
                                    <i class="fas fa-check-circle" style="margin-left:3px;"></i>
                                    <?php echo e($s['plan_name'] ?? 'فعال'); ?>
                                </span>
                                <br><small style="color:#9CA3AF;"><?php echo formatDate($s['membership_end']); ?></small>
                                <?php else: ?>
                                <span style="color:#EF4444;">
                                    <i class="fas fa-times-circle" style="margin-left:3px;"></i>
                                    بدون اشتراک
                                </span>
                                <?php endif; ?>
                            </td>
                            <td class="hide-mobile">
                                <?php if ($hasInsurance): ?>
                                <span class="badge badge-success" style="font-size:0.75rem;">
                                    <i class="fas fa-shield-alt" style="margin-left:2px;"></i> فعال
                                </span>
                                <?php else: ?>
                                <span class="badge badge-danger" style="font-size:0.75rem;">
                                    <i class="fas fa-exclamation-triangle" style="margin-left:2px;"></i> بدون
                                </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="display:flex;gap:6px;flex-wrap:wrap;">
                                    <?php
                                    $statuses = [
                                        'present' => ['حاضر', '#10B981'],
                                        'absent'  => ['غایب', '#EF4444'],
                                        'late'    => ['تأخیر', '#F59E0B'],
                                        'excused' => ['موجه', '#3B82F6'],
                                    ];
                                    $currentStatus = $s['attendance_status'] ?? 'present';
                                    foreach ($statuses as $val => $label):
                                        $isActive = ($currentStatus === $val);
                                    ?>
                                    <label style="display:inline-flex;align-items:center;gap:4px;padding:5px 12px;border-radius:20px;font-size:0.8rem;cursor:pointer;transition:all 0.2s;border:2px solid <?php echo $isActive ? $label[1] : '#E5E7EB'; ?>;background:<?php echo $isActive ? $label[1] . '15' : '#fff'; ?>;color:<?php echo $isActive ? $label[1] : '#6B7A8D'; ?>;">
                                        <input type="radio" name="status[<?php echo $s['registration_id']; ?>]" value="<?php echo $val; ?>" <?php echo $isActive ? 'checked' : ''; ?> style="display:none;" onchange="updateStatusLabel(this)">
                                        <span><?php echo $label[0]; ?></span>
                                    </label>
                                    <?php endforeach; ?>
                                </div>
                            </td>
                            <td class="hide-mobile">
                                <input type="text" name="notes[<?php echo $s['registration_id']; ?>]"
                                       value="<?php echo e($s['attendance_notes'] ?? ''); ?>"
                                       placeholder="توضیحات (اختیاری)"
                                       style="width:100%;padding:7px 10px;border:1px solid #D1D5DB;border-radius:8px;font-family:Vazirmatn;font-size:0.85rem;">
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div style="margin-top:16px;display:flex;gap:10px;flex-wrap:wrap;">
        <button type="submit" class="btn btn-lg btn-primary" style="padding:12px 32px;font-size:0.95rem;">
            <i class="fas fa-save" style="margin-left:6px;"></i>
            ذخیره حضور و غیاب
        </button>
        <a href="<?php echo url('coach/attendance?date=' . urlencode($selectedDate)); ?>" class="btn btn-lg btn-secondary">
            انصراف
        </a>
    </div>
</form>

<script>
function updateStatusLabel(radio) {
    const container = radio.closest('td');
    container.querySelectorAll('label').forEach(label => {
        label.style.borderColor = '#E5E7EB';
        label.style.background = '#fff';
        label.style.color = '#6B7A8D';
    });
    const activeLabel = radio.closest('label');
    const colorMap = {
        'present': '#10B981',
        'absent': '#EF4444',
        'late': '#F59E0B',
        'excused': '#3B82F6'
    };
    const color = colorMap[radio.value] || '#6B7A8D';
    activeLabel.style.borderColor = color;
    activeLabel.style.background = color + '15';
    activeLabel.style.color = color;
}

function setAllStatus(status) {
    document.querySelectorAll('input[type="radio"][value="' + status + '"]').forEach(radio => {
        radio.checked = true;
        updateStatusLabel(radio);
    });
}
</script>
<?php else: ?>
<div class="card">
    <div class="card-body">
        <div class="table-empty">
            <div class="empty-state">
                <i class="fas fa-user-slash" style="font-size:48px;color:#D1D5DB;margin-bottom:16px;display:block;"></i>
                <h3>ورزشکاری در این کلاس نیست</h3>
                <p>هیچ ورزشکاری در این کلاس ثبت‌نام نکرده است.</p>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>