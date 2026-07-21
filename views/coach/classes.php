<?php $activeMenu = 'classes'; $pageTitle = 'کلاس‌های من'; ?>

<div class="page-header-row">
    <div class="page-header">
        <h2 class="page-title">
            <i class="fas fa-chalkboard-teacher" style="color:#3B82F6;margin-left:8px;"></i>
            کلاس‌های من
        </h2>
        <p>لیست کلاس‌هایی که به شما اختصاص داده شده</p>
    </div>
</div>

<?php if (!empty($classes)): ?>
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;margin-bottom:24px;">
    <?php foreach ($classes as $cls): ?>
    <div class="card" style="margin-bottom:0;overflow:hidden;">
        <div style="background:linear-gradient(135deg,#3B82F6,#8B5CF6);padding:16px 20px;">
            <div style="display:flex;justify-content:space-between;align-items:start;">
                <div>
                    <h4 style="margin:0 0 6px 0;color:#fff;font-size:1rem;font-weight:600;">
                        <?php echo e($cls['name']); ?>
                    </h4>
                    <div style="font-size:0.8rem;color:rgba(255,255,255,0.8);">
                        <i class="fas fa-calendar-alt" style="margin-left:4px;"></i>
                        <?php echo e($cls['schedule_days'] ?? $cls['schedule_day'] ?? '-'); ?>
                    </div>
                </div>
                <div style="background:rgba(255,255,255,0.2);color:#fff;padding:6px 12px;border-radius:20px;font-size:0.82rem;font-weight:600;">
                    <?php echo e($cls['start_time'] ?? substr($cls['schedule_time'], 0, 5)); ?>
                </div>
            </div>
        </div>
        <div style="padding:16px 20px;">
            <?php if (!empty($cls['description'])): ?>
            <p style="font-size:0.85rem;color:#6B7A8D;margin:0 0 12px 0;line-height:1.6;">
                <?php echo e(mb_substr($cls['description'], 0, 100)); ?>
            </p>
            <?php endif; ?>

            <div style="display:flex;gap:20px;margin-bottom:14px;flex-wrap:wrap;">
                <div style="font-size:0.82rem;color:#6B7A8D;">
                    <i class="fas fa-users" style="color:#3B82F6;margin-left:4px;"></i>
                    <strong style="color:#1f2937;"><?php echo $cls['student_count'] ?? 0; ?></strong>
                    / <?php echo $cls['max_participants'] ?: 'نامحدود'; ?> نفر
                </div>
                <?php if (!empty($cls['end_time'])): ?>
                <div style="font-size:0.82rem;color:#6B7A8D;">
                    <i class="fas fa-clock" style="color:#F59E0B;margin-left:4px;"></i>
                    <span style="direction:ltr;display:inline-block;">
                        <?php echo e(substr($cls['schedule_time'], 0, 5)); ?> - <?php echo e(substr($cls['end_time'], 0, 5)); ?>
                    </span>
                </div>
                <?php endif; ?>
            </div>

            <!-- Capacity bar -->
            <?php
            $current = (int) ($cls['student_count'] ?? 0);
            $max = (int) ($cls['max_participants'] ?? 0);
            $percent = $max > 0 ? min(($current / $max) * 100, 100) : 0;
            $barColor = $percent >= 90 ? '#EF4444' : ($percent >= 70 ? '#F59E0B' : '#10B981');
            ?>
            <?php if ($max > 0): ?>
            <div style="margin-bottom:14px;">
                <div style="height:6px;background:#F3F4F6;border-radius:3px;overflow:hidden;">
                    <div style="height:100%;width:<?php echo $percent; ?>%;background:<?php echo $barColor; ?>;border-radius:3px;transition:width 0.5s ease;"></div>
                </div>
                <div style="font-size:0.72rem;color:#9CA3AF;margin-top:4px;"><?php echo round($percent); ?>٪ ظرفیت پر شده</div>
            </div>
            <?php endif; ?>

            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                <a href="<?php echo url('coach/class-students/' . $cls['id']); ?>" class="btn btn-sm btn-info" style="flex:1;">
                    <i class="fas fa-users" style="margin-left:4px;"></i>
                    لیست ورزشکاران
                </a>
                <a href="<?php echo url('coach/attendance-form?class_id=' . $cls['id'] . '&date=' . date('Y-m-d')); ?>" class="btn btn-sm btn-primary" style="flex:1;">
                    <i class="fas fa-clipboard-check" style="margin-left:4px;"></i>
                    ثبت حضور
                </a>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php else: ?>
<div class="card">
    <div class="card-body">
        <div class="table-empty">
            <div class="empty-state">
                <i class="fas fa-chalkboard-teacher" style="font-size:56px;color:#D1D5DB;margin-bottom:16px;display:block;"></i>
                <h3>کلاسی یافت نشد</h3>
                <p>هنوز کلاسی به شما اختصاص داده نشده است. با مدیریت باشگاه تماس بگیرید.</p>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>