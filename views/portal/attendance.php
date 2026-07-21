<div class="page-header">
    <h2 class="page-title"><i class="fas fa-clipboard-check" style="margin-left:8px;color:#3B82F6;"></i>حضور و غیاب</h2>
    <p>سابقه حضور و غیاب شما در کلاس‌ها</p>
</div>

<?php $pageTitle = 'حضور و غیاب'; ?>

<!-- Summary Stats -->
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:12px;margin-bottom:20px;">
    <div class="card" style="margin-bottom:0;">
        <div class="card-body" style="padding:16px 20px;display:flex;align-items:center;gap:14px;">
            <div style="width:42px;height:42px;border-radius:10px;background:#10B98115;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="fas fa-check-circle" style="font-size:1.1rem;color:#10B981;"></i>
            </div>
            <div>
                <div style="font-size:1.2rem;font-weight:700;color:#1f2937;"><?php echo $totalPresent ?? 0; ?></div>
                <div style="font-size:0.8rem;color:#6B7A8D;">حاضر</div>
            </div>
        </div>
    </div>
    <div class="card" style="margin-bottom:0;">
        <div class="card-body" style="padding:16px 20px;display:flex;align-items:center;gap:14px;">
            <div style="width:42px;height:42px;border-radius:10px;background:#EF444415;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="fas fa-times-circle" style="font-size:1.1rem;color:#EF4444;"></i>
            </div>
            <div>
                <div style="font-size:1.2rem;font-weight:700;color:#1f2937;"><?php echo $totalAbsent ?? 0; ?></div>
                <div style="font-size:0.8rem;color:#6B7A8D;">غایب</div>
            </div>
        </div>
    </div>
    <div class="card" style="margin-bottom:0;">
        <div class="card-body" style="padding:16px 20px;display:flex;align-items:center;gap:14px;">
            <div style="width:42px;height:42px;border-radius:10px;background:#F59E0B15;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="fas fa-clock" style="font-size:1.1rem;color:#F59E0B;"></i>
            </div>
            <div>
                <div style="font-size:1.2rem;font-weight:700;color:#1f2937;"><?php echo $totalLate ?? 0; ?></div>
                <div style="font-size:0.8rem;color:#6B7A8D;">تأخیر</div>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card" style="margin-bottom:16px;">
    <div class="card-body" style="padding:16px 20px;">
        <form method="GET" action="<?php echo url('portal/attendance'); ?>" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
            <div style="flex:1;min-width:180px;">
                <label style="display:block;font-size:0.82rem;color:#6B7A8D;margin-bottom:4px;">
                    <i class="fas fa-calendar" style="margin-left:4px;"></i>
                    تاریخ
                </label>
                <input type="date" name="date" value="<?php echo e($dateFilter ?? ''); ?>" class="form-control" style="max-width:220px;">
            </div>
            <div style="flex:1;min-width:180px;">
                <label style="display:block;font-size:0.82rem;color:#6B7A8D;margin-bottom:4px;">
                    <i class="fas fa-chalkboard-teacher" style="margin-left:4px;"></i>
                    کلاس
                </label>
                <select name="class_id" class="form-control" style="max-width:220px;">
                    <option value="">همه کلاس‌ها</option>
                    <?php foreach ($registeredClasses ?? [] as $cls): ?>
                    <option value="<?php echo $cls['id']; ?>" <?php echo ($classFilter ?? 0) == $cls['id'] ? 'selected' : ''; ?>>
                        <?php echo e($cls['name']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="display:flex;gap:8px;">
                <button type="submit" class="btn btn-primary" style="height:38px;padding:0 20px;">
                    <i class="fas fa-search" style="margin-left:4px;"></i>
                    جستجو
                </button>
                <a href="<?php echo url('portal/attendance'); ?>" class="btn btn-secondary" style="height:38px;padding:0 20px;">
                    پاک کردن
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Attendance Table -->
<div class="card">
    <div class="card-body">
        <?php if (!empty($attendanceRecords)): ?>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>تاریخ</th>
                        <th>نام کلاس</th>
                        <th>وضعیت</th>
                        <th class="hide-mobile">توضیحات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($attendanceRecords as $att): ?>
                    <?php
                    $status = $att['attendance_status'] ?? $att['status'] ?? '';
                    $badgeClass = 'badge-secondary';
                    $statusLabel = 'نامشخص';
                    if ($status === 'present') { $badgeClass = 'badge-success'; $statusLabel = 'حاضر'; }
                    elseif ($status === 'absent') { $badgeClass = 'badge-danger'; $statusLabel = 'غایب'; }
                    elseif ($status === 'late') { $badgeClass = 'badge-warning'; $statusLabel = 'تأخیر'; }
                    elseif ($status === 'excused') { $badgeClass = 'badge-info'; $statusLabel = 'موجه'; }
                    ?>
                    <tr>
                        <td><?php echo formatDate($att['attendance_date'] ?? ''); ?></td>
                        <td style="font-weight:500;"><?php echo e($att['class_name'] ?? ''); ?></td>
                        <td><span class="badge <?php echo $badgeClass; ?>"><?php echo $statusLabel; ?></span></td>
                        <td class="hide-mobile" style="color:#6B7A8D;font-size:0.85rem;"><?php echo e($att['notes'] ?? '-'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="table-empty">
            <div class="empty-state">
                <i class="fas fa-clipboard-check" style="font-size:48px;color:#D1D5DB;margin-bottom:16px;display:block;"></i>
                <h3>رکوردی یافت نشد</h3>
                <p>سابقه حضور و غیابی برای فیلترهای انتخاب شده یافت نشد.</p>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>