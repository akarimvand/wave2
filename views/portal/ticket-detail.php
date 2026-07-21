<div class="page-header">
    <h2 class="page-title"><i class="fas fa-headset" style="margin-left:8px;color:#3B82F6;"></i>جزئیات تیکت</h2>
    <p>مشاهده تیکت پشتیبانی</p>
</div>

<div class="page-actions" style="margin-bottom:20px;">
    <a href="<?php echo url('portal/tickets'); ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-right" style="margin-left:6px;"></i>
        بازگشت به لیست
    </a>
</div>

<!-- Ticket Header -->
<?php
$priority = $ticket['priority'] ?? 'medium';
$pClass = 'badge-warning';
$pLabel = 'متوسط';
$pIcon = 'fa-minus-circle';
if ($priority === 'high' || $priority === 'urgent') {
    $pClass = 'badge-danger';
    $pLabel = ($priority === 'urgent') ? 'فوری' : 'بالا';
    $pIcon = 'fa-arrow-up';
} elseif ($priority === 'medium') {
    $pClass = 'badge-warning';
    $pLabel = 'متوسط';
    $pIcon = 'fa-minus-circle';
} elseif ($priority === 'low') {
    $pClass = 'badge-info';
    $pLabel = 'پایین';
    $pIcon = 'fa-arrow-down';
}

$status = $ticket['status'] ?? '';
$sClass = 'badge-secondary';
$sLabel = 'نامشخص';
$sIcon = 'fa-question-circle';
if ($status === 'open') {
    $sClass = 'badge-success';
    $sLabel = 'باز';
    $sIcon = 'fa-check-circle';
} elseif ($status === 'in_progress') {
    $sClass = 'badge-info';
    $sLabel = 'در حال بررسی';
    $sIcon = 'fa-spinner';
} elseif ($status === 'resolved') {
    $sClass = 'badge-warning';
    $sLabel = 'حل شده';
    $sIcon = 'fa-check-double';
} elseif ($status === 'closed') {
    $sClass = 'badge-secondary';
    $sLabel = 'بسته';
    $sIcon = 'fa-times-circle';
}
?>

<div class="card" style="margin-bottom:16px;">
    <div class="card-body">
        <!-- Title + Badges -->
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:20px;">
            <h3 style="margin:0;font-size:1.15rem;font-weight:700;color:#1f2937;">
                <?php echo e($ticket['subject'] ?? $ticket['title'] ?? ''); ?>
            </h3>
            <div style="display:flex;gap:8px;align-items:center;">
                <span class="badge <?php echo $pClass; ?>">
                    <i class="fas <?php echo $pIcon; ?>" style="margin-left:4px;font-size:0.65rem;"></i>
                    <?php echo $pLabel; ?>
                </span>
                <span class="badge <?php echo $sClass; ?>">
                    <i class="fas <?php echo $sIcon; ?>" style="margin-left:4px;font-size:0.65rem;"></i>
                    <?php echo $sLabel; ?>
                </span>
            </div>
        </div>

        <!-- Meta Info -->
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
            <div style="display:flex;align-items:center;gap:8px;">
                <i class="fas fa-clock" style="color:#6b7280;width:18px;text-align:center;"></i>
                <span style="font-size:0.85rem;color:#6b7280;">تاریخ ایجاد:</span>
                <span style="font-size:0.9rem;font-weight:500;color:#1f2937;"><?php echo formatDateTime($ticket['created_at']); ?></span>
            </div>
        </div>

        <!-- Message Body -->
        <div style="padding:16px;background:#F9FAFB;border-radius:8px;border:1px solid #E5E7EB;">
            <div style="font-size:0.8rem;color:#6b7280;margin-bottom:8px;font-weight:600;">
                <i class="fas fa-comment-dots" style="margin-left:4px;"></i>
                متن پیام
            </div>
            <div style="font-size:0.9rem;line-height:1.8;color:#1f2937;white-space:pre-wrap;"><?php echo e($ticket['description'] ?? $ticket['body'] ?? $ticket['message'] ?? ''); ?></div>
        </div>
    </div>
</div>

<!-- Replies -->
<div class="card">
    <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;">
        <h3 style="margin:0;font-size:0.95rem;font-weight:600;">
            <i class="fas fa-comments" style="margin-left:8px;color:#3B82F6;"></i>
            پاسخ‌ها
            <span class="badge badge-info" style="font-size:0.7rem;margin-right:8px;"><?php echo count($replies ?? []); ?></span>
        </h3>
    </div>
    <div class="card-body">
        <?php if (!empty($replies)): ?>
        <div style="display:flex;flex-direction:column;gap:12px;">
            <?php foreach ($replies as $reply): ?>
            <?php $isAdmin = !empty($reply['is_admin']); ?>
            <div style="display:flex;gap:12px;padding:16px;background:<?php echo $isAdmin ? '#F0F9FF' : '#F9FAFB'; ?>;border-radius:8px;border:1px solid #E5E7EB;<?php echo $isAdmin ? 'border-right:3px solid #3B82F6;' : ''; ?>">
                <div style="flex-shrink:0;width:40px;height:40px;border-radius:50%;background:<?php echo $isAdmin ? '#3B82F6' : '#8B5CF6'; ?>;display:flex;align-items:center;justify-content:center;">
                    <i class="fas <?php echo $isAdmin ? 'fa-user-shield' : 'fa-user'; ?>" style="color:#fff;font-size:14px;"></i>
                </div>
                <div style="flex:1;min-width:0;">
                    <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;margin-bottom:8px;flex-wrap:wrap;">
                        <div style="display:flex;align-items:center;gap:8px;">
                            <span style="font-weight:600;font-size:0.9rem;color:<?php echo $isAdmin ? '#3B82F6' : '#1f2937'; ?>;">
                                <?php echo e($reply['user_name'] ?? 'کاربر'); ?>
                            </span>
                            <?php if ($isAdmin): ?>
                            <span class="badge badge-info" style="font-size:0.65rem;">
                                <i class="fas fa-shield-alt" style="margin-left:3px;font-size:0.6rem;"></i>
                                مدیریت
                            </span>
                            <?php endif; ?>
                        </div>
                        <span style="font-size:0.8rem;color:#9CA3AF;">
                            <i class="fas fa-clock" style="margin-left:4px;font-size:0.7rem;"></i>
                            <?php echo formatDateTime($reply['created_at']); ?>
                        </span>
                    </div>
                    <div style="font-size:0.9rem;line-height:1.7;color:#374151;white-space:pre-wrap;">
                        <?php echo e($reply['message'] ?? $reply['body'] ?? ''); ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div style="text-align:center;padding:30px;color:#9CA3AF;">
            <i class="fas fa-comment-slash" style="font-size:36px;margin-bottom:12px;display:block;"></i>
            <p style="margin:0;">پاسخی ثبت نشده است.</p>
        </div>
        <?php endif; ?>

        <!-- Portal members cannot reply -->
        <?php if ($ticket['status'] !== 'closed' && $ticket['status'] !== 'resolved'): ?>
        <div style="margin-top:20px;padding:12px 16px;background:#F9FAFB;border-radius:8px;border:1px solid #E5E7EB;text-align:center;">
            <span style="font-size:0.85rem;color:#6B7A8D;">
                <i class="fas fa-info-circle" style="margin-left:6px;color:#3B82F6;"></i>
                برای ارسال پاسخ لطفاً با مدیریت باشگاه تماس بگیرید.
            </span>
        </div>
        <?php else: ?>
        <div style="margin-top:20px;padding:12px 16px;background:#F9FAFB;border-radius:8px;border:1px solid #E5E7EB;text-align:center;">
            <span style="font-size:0.85rem;color:#6B7A8D;">
                <i class="fas fa-lock" style="margin-left:6px;"></i>
                این تیکت بسته شده است.
            </span>
        </div>
        <?php endif; ?>
    </div>
</div>