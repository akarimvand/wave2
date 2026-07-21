<div class="page-header">
    <h2 class="page-title"><i class="fas fa-calendar-alt" style="margin-left:8px;color:#3B82F6;"></i>کلاس‌ها</h2>
    <p>کلاس‌های موجود و ثبت‌نام شما</p>
</div>

<!-- My Classes Section -->
<?php
$myClasses = [];
if (!empty($availableClasses) && !empty($myRegisteredIds)):
    foreach ($availableClasses as $cls):
        if (in_array($cls['id'], $myRegisteredIds)):
            $myClasses[] = $cls;
        endif;
    endforeach;
endif;
?>

<?php if (!empty($myClasses)): ?>
<div class="card" style="margin-bottom:24px;">
    <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;">
        <h3 style="margin:0;font-size:0.95rem;font-weight:600;">
            <i class="fas fa-check-circle" style="margin-left:8px;color:#10B981;"></i>
            کلاس‌های من
            <span class="badge badge-success" style="font-size:0.7rem;margin-right:8px;"><?php echo count($myClasses); ?></span>
        </h3>
    </div>
    <div class="card-body">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>نام کلاس</th>
                        <th class="hide-mobile">مربی</th>
                        <th>برنامه</th>
                        <th class="hide-mobile">وضعیت</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($myClasses as $cls): ?>
                    <?php
                    $coachName = trim(($cls['coach_first_name'] ?? '') . ' ' . ($cls['coach_last_name'] ?? ''));
                    $regStatus = $cls['reg_status'] ?? 'active';
                    $regBadge = 'badge-success';
                    $regLabel = 'فعال';
                    if ($regStatus === 'cancelled') { $regBadge = 'badge-danger'; $regLabel = 'لغو شده'; }
                    elseif ($regStatus === 'pending') { $regBadge = 'badge-warning'; $regLabel = 'در انتظار'; }
                    ?>
                    <tr>
                        <td style="font-weight:500;"><?php echo e($cls['name']); ?></td>
                        <td class="hide-mobile"><?php echo e($coachName) ?: '-'; ?></td>
                        <td>
                            <span style="font-size:0.85rem;">
                                <?php echo e($cls['schedule_day'] ?? ''); ?>
                                <?php if (!empty($cls['start_time'])): ?>
                                &nbsp;
                                <span style="direction:ltr;display:inline-block;"><?php echo e($cls['start_time']); ?></span>
                                <?php endif; ?>
                            </span>
                        </td>
                        <td class="hide-mobile"><span class="badge <?php echo $regBadge; ?>"><?php echo $regLabel; ?></span></td>
                        <td>
                            <div class="table-actions">
                                <form method="POST" action="<?php echo url('portal/classes/' . $cls['id'] . '/unregister'); ?>" style="display:inline;" onsubmit="return confirmDelete('آیا از لغو ثبت‌نام اطمینان دارید؟')">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="fas fa-times-circle" style="margin-left:4px;"></i>
                                        لغو ثبت‌نام
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Available Classes Section -->
<div class="card">
    <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;">
        <h3 style="margin:0;font-size:0.95rem;font-weight:600;">
            <i class="fas fa-list" style="margin-left:8px;color:#3B82F6;"></i>
            کلاس‌های موجود
        </h3>
    </div>
    <div class="card-body">
        <?php if (!empty($availableClasses)): ?>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>نام کلاس</th>
                        <th class="hide-mobile">مربی</th>
                        <th>برنامه</th>
                        <th class="hide-mobile">ظرفیت</th>
                        <th>قیمت</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($availableClasses as $cls): ?>
                    <?php
                    $coachName = trim(($cls['coach_first_name'] ?? '') . ' ' . ($cls['coach_last_name'] ?? ''));
                    $isEnrolled = in_array($cls['id'] ?? 0, $myRegisteredIds ?? []);
                    $currentEnroll = (int)($cls['registered_count'] ?? 0);
                    $maxCap = (int)($cls['max_capacity'] ?? $cls['max_participants'] ?? 0);
                    $isFull = $maxCap > 0 && $currentEnroll >= $maxCap;
                    ?>
                    <tr>
                        <td style="font-weight:500;"><?php echo e($cls['name']); ?></td>
                        <td class="hide-mobile"><?php echo e($coachName) ?: '-'; ?></td>
                        <td>
                            <span style="font-size:0.85rem;">
                                <?php echo e($cls['schedule_day'] ?? ''); ?>
                                <?php if (!empty($cls['start_time'])): ?>
                                &nbsp;
                                <span style="direction:ltr;display:inline-block;"><?php echo e($cls['start_time']); ?></span>
                                <?php endif; ?>
                            </span>
                        </td>
                        <td class="hide-mobile">
                            <span style="color:<?php echo $isFull ? '#EF4444' : '#10B981'; ?>;font-size:0.85rem;font-weight:500;">
                                <i class="fas fa-users" style="margin-left:4px;font-size:0.75rem;"></i>
                                <?php echo $currentEnroll; ?>/<?php echo $maxCap; ?>
                            </span>
                        </td>
                        <td style="font-weight:600;"><?php echo formatCurrency($cls['price'] ?? 0); ?></td>
                        <td>
                            <div class="table-actions">
                                <?php if ($isEnrolled): ?>
                                    <span class="badge badge-success" style="font-size:0.75rem;">
                                        <i class="fas fa-check" style="margin-left:4px;"></i>
                                        ثبت‌نام شده
                                    </span>
                                <?php elseif ($isFull): ?>
                                    <span class="badge badge-secondary" style="font-size:0.75rem;">
                                        <i class="fas fa-ban" style="margin-left:4px;"></i>
                                        ظرفیت تکمیل
                                    </span>
                                <?php else: ?>
                                    <form method="POST" action="<?php echo url('portal/classes/' . $cls['id'] . '/register'); ?>" style="display:inline;">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="fas fa-plus-circle" style="margin-left:4px;"></i>
                                            ثبت‌نام
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="table-empty">
            <div class="empty-state">
                <i class="fas fa-calendar-times" style="font-size:48px;color:#D1D5DB;margin-bottom:16px;display:block;"></i>
                <h3>کلاسی یافت نشد</h3>
                <p>در حال حاضر کلاس فعالی موجود نیست.</p>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>