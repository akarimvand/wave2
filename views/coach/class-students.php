<?php $activeMenu = 'classes'; $pageTitle = 'لیست ورزشکاران'; ?>

<div class="page-header-row">
    <div class="page-header">
        <h2 class="page-title">
            <i class="fas fa-users" style="color:#3B82F6;margin-left:8px;"></i>
            ورزشکاران کلاس: <?php echo e($class['name']); ?>
        </h2>
        <p><?php echo e($class['schedule_days'] ?? $class['schedule_day'] ?? ''); ?> | ساعت <?php echo e($class['start_time'] ?? substr($class['schedule_time'], 0, 5)); ?></p>
    </div>
    <div class="page-header-actions">
        <a href="<?php echo url('coach/classes'); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i>
            بازگشت
        </a>
        <a href="<?php echo url('coach/attendance-form?class_id=' . $class['id'] . '&date=' . date('Y-m-d')); ?>" class="btn btn-primary">
            <i class="fas fa-clipboard-check"></i>
            ثبت حضور امروز
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div style="margin-bottom:16px;padding:12px 16px;background:#EFF6FF;border-radius:10px;display:flex;align-items:center;gap:10px;">
            <i class="fas fa-info-circle" style="color:#3B82F6;"></i>
            <span style="font-size:0.85rem;color:#1E40AF;">
                تعداد <strong><?php echo count($students); ?></strong> ورزشکار در این کلاس ثبت‌نام شده‌اند.
            </span>
        </div>

        <?php if (!empty($students)): ?>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ردیف</th>
                        <th>نام و نام خانوادگی</th>
                        <th class="hide-mobile">کد ملی</th>
                        <th>تلفن</th>
                        <th class="hide-mobile">تاریخ ثبت‌نام</th>
                        <th>بیمه</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $row = 0; foreach ($students as $s): $row++; ?>
                    <tr>
                        <td><?php echo $row; ?></td>
                        <td style="font-weight:500;">
                            <?php echo e($s['first_name'] . ' ' . $s['last_name']); ?>
                        </td>
                        <td class="hide-mobile" style="direction:ltr;text-align:right;font-size:0.85rem;">
                            <?php echo e($s['national_code'] ?? '-'); ?>
                        </td>
                        <td style="direction:ltr;text-align:right;">
                            <?php echo e($s['phone']); ?>
                        </td>
                        <td class="hide-mobile"><?php echo formatDate($s['registration_date']); ?></td>
                        <td>
                            <?php if (!empty($s['insurance_status']) && $s['insurance_status'] === 'active'): ?>
                            <span class="badge badge-success">
                                <i class="fas fa-shield-alt" style="margin-left:3px;"></i>
                                فعال
                            </span>
                            <?php else: ?>
                            <span class="badge badge-danger">
                                <i class="fas fa-exclamation-triangle" style="margin-left:3px;"></i>
                                بدون
                            </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="table-empty">
            <div class="empty-state">
                <i class="fas fa-user-slash" style="font-size:48px;color:#D1D5DB;margin-bottom:16px;display:block;"></i>
                <h3>ورزشکاری ثبت‌نام نکرده</h3>
                <p>هنوز هیچ ورزشکاری در این کلاس ثبت‌نام نکرده است.</p>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>