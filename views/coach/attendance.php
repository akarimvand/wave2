<?php $activeMenu = 'attendance'; $pageTitle = 'حضور و غیاب'; ?>

<div class="page-header-row">
    <div class="page-header">
        <h2 class="page-title">
            <i class="fas fa-clipboard-check" style="color:#3B82F6;margin-left:8px;"></i>
            حضور و غیاب
        </h2>
        <p>ثبت و مشاهده حضور و غیاب ورزشکاران</p>
    </div>
</div>

<!-- Date Selector & Class Cards -->
<div class="card" style="margin-bottom:16px;">
    <div class="card-body">
        <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:20px;">
            <div style="display:flex;align-items:center;gap:8px;">
                <i class="fas fa-calendar" style="color:#3B82F6;"></i>
                <label style="font-size:0.9rem;font-weight:500;">تاریخ:</label>
            </div>
            <form method="GET" action="<?php echo url('coach/attendance'); ?>" style="display:flex;align-items:center;gap:8px;">
                <input type="date" name="date" value="<?php echo e($selectedDate); ?>"
                       style="padding:8px 12px;border:1px solid #D1D5DB;border-radius:8px;font-family:Vazirmatn;font-size:0.9rem;direction:ltr;"
                       onchange="this.form.submit()">
                <?php if ($selectedDate !== date('Y-m-d')): ?>
                <a href="<?php echo url('coach/attendance'); ?>" class="btn btn-sm btn-secondary">
                    امروز
                </a>
                <?php endif; ?>
            </form>
        </div>

        <?php if (!empty($classes)): ?>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:12px;">
            <?php foreach ($classes as $cls): ?>
            <?php
            $hasAttendance = false;
            foreach ($attendanceRecords as $ar) {
                if ($ar['class_id'] == $cls['id']) { $hasAttendance = true; break; }
            }
            ?>
            <div style="border:1px solid <?php echo $hasAttendance ? '#BFDBFE' : '#E5E7EB'; ?>;border-radius:10px;padding:16px;background:<?php echo $hasAttendance ? '#EFF6FF' : '#fff'; ?>;">
                <div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:10px;">
                    <div style="font-weight:600;color:#1f2937;font-size:0.95rem;">
                        <i class="fas fa-dumbbell" style="color:#3B82F6;margin-left:6px;font-size:0.85rem;"></i>
                        <?php echo e($cls['name']); ?>
                    </div>
                    <?php if ($hasAttendance): ?>
                    <span style="font-size:0.75rem;background:#3B82F6;color:#fff;padding:2px 8px;border-radius:10px;">
                        <i class="fas fa-check" style="margin-left:2px;"></i>
                        ثبت شده
                    </span>
                    <?php endif; ?>
                </div>
                <div style="font-size:0.82rem;color:#6B7A8D;margin-bottom:12px;">
                    <i class="fas fa-clock" style="margin-left:4px;"></i>
                    <span style="direction:ltr;display:inline-block;"><?php echo e($cls['start_time'] ?? substr($cls['schedule_time'], 0, 5)); ?></span>
                    &nbsp;|&nbsp;
                    <i class="fas fa-users" style="margin-left:4px;"></i>
                    <?php echo $cls['student_count'] ?? 0; ?> ورزشکار
                </div>
                <a href="<?php echo url('coach/attendance-form?class_id=' . $cls['id'] . '&date=' . urlencode($selectedDate)); ?>"
                   class="btn btn-sm btn-primary" style="width:100%;text-align:center;">
                    <i class="fas fa-clipboard-check" style="margin-left:4px;"></i>
                    <?php echo $hasAttendance ? 'ویرایش حضور و غیاب' : 'ثبت حضور و غیاب'; ?>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="table-empty">
            <div class="empty-state">
                <i class="fas fa-chalkboard-teacher" style="font-size:48px;color:#D1D5DB;margin-bottom:16px;display:block;"></i>
                <h3>کلاسی ندارید</h3>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Attendance Records for Selected Date -->
<?php if (!empty($attendanceRecords)): ?>
<div class="card">
    <div class="card-header">
        <h3 style="margin:0;font-size:0.95rem;font-weight:600;">
            <i class="fas fa-list-check" style="margin-left:8px;color:#3B82F6;"></i>
            حضور و غیاب ثبت شده - <?php echo formatDate($selectedDate); ?>
        </h3>
    </div>
    <div class="card-body">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>کلاس</th>
                        <th>نام ورزشکار</th>
                        <th>وضعیت</th>
                        <th class="hide-mobile">توضیحات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($attendanceRecords as $ar): ?>
                    <tr>
                        <td><?php echo e($ar['class_name']); ?></td>
                        <td style="font-weight:500;"><?php echo e($ar['first_name'] . ' ' . $ar['last_name']); ?></td>
                        <td>
                            <?php
                            $statusMap = [
                                'present' => ['حاضر', 'badge-success'],
                                'absent'  => ['غایب', 'badge-danger'],
                                'late'    => ['تأخیر', 'badge-warning'],
                                'excused' => ['موجه', 'badge-info'],
                            ];
                            $s = $statusMap[$ar['status']] ?? [$ar['status'], 'badge-secondary'];
                            ?>
                            <span class="badge <?php echo $s[1]; ?>"><?php echo $s[0]; ?></span>
                        </td>
                        <td class="hide-mobile" style="font-size:0.85rem;color:#6B7A8D;"><?php echo e($ar['notes'] ?? '-'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>