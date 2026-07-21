<div class="page-header">
    <h2 class="page-title"><i class="fas fa-bell" style="margin-left:8px;color:#F59E0B;"></i>اعلانات</h2>
    <p>اعلانات و اطلاعیه‌ها</p>
</div>

<?php if (!empty($notifications)): ?>
<div class="card">
    <div class="card-body" style="padding:0;">
        <div style="display:flex;flex-direction:column;">
            <?php foreach ($notifications as $notif): ?>
            <?php
            $type = $notif['type'] ?? 'info';
            $iconColor = '#3B82F6';
            $iconBg = '#3B82F615';
            $icon = 'fa-info-circle';
            $borderColor = '#3B82F6';

            if ($type === 'success') {
                $iconColor = '#10B981';
                $iconBg = '#10B98115';
                $icon = 'fa-check-circle';
                $borderColor = '#10B981';
            } elseif ($type === 'warning') {
                $iconColor = '#F59E0B';
                $iconBg = '#F59E0B15';
                $icon = 'fa-exclamation-triangle';
                $borderColor = '#F59E0B';
            } elseif ($type === 'error') {
                $iconColor = '#EF4444';
                $iconBg = '#EF444415';
                $icon = 'fa-times-circle';
                $borderColor = '#EF4444';
            }

            $isUnread = empty($notif['is_read']) || $notif['is_read'] == 0;
            $messageText = $notif['message'] ?? $notif['body'] ?? '';
            $truncated = mb_strlen($messageText) > 120 ? mb_substr($messageText, 0, 120) . '...' : $messageText;
            ?>
            <div style="display:flex;align-items:flex-start;gap:14px;padding:18px 20px;background:#fff;<?php echo $isUnread ? 'border-right:3px solid ' . $borderColor . ';' : ''; ?>border-bottom:1px solid #F3F4F6;">
                <div style="flex-shrink:0;width:40px;height:40px;border-radius:50%;background:<?php echo $iconBg; ?>;display:flex;align-items:center;justify-content:center;">
                    <i class="fas <?php echo $icon; ?>" style="font-size:1rem;color:<?php echo $iconColor; ?>;"></i>
                </div>
                <div style="flex:1;min-width:0;">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;flex-wrap:wrap;">
                        <strong style="font-size:0.9rem;color:#1f2937;">
                            <?php echo e($notif['title']); ?>
                        </strong>
                        <?php if ($isUnread): ?>
                        <span class="badge badge-primary" style="font-size:0.6rem;padding:2px 8px;">جدید</span>
                        <?php endif; ?>
                    </div>
                    <p style="font-size:0.85rem;color:#6B7A8D;margin:0;line-height:1.6;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                        <?php echo e($truncated); ?>
                    </p>
                    <span style="font-size:0.75rem;color:#9CA3AF;margin-top:8px;display:block;">
                        <i class="fas fa-calendar" style="margin-left:4px;font-size:0.7rem;"></i>
                        <?php echo formatDateTime($notif['created_at']); ?>
                    </span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php else: ?>
<div class="card">
    <div class="card-body">
        <div class="empty-state" style="padding:48px 20px;">
            <i class="fas fa-bell-slash" style="font-size:48px;color:#D1D5DB;margin-bottom:16px;display:block;"></i>
            <h3>اعلانی یافت نشد</h3>
            <p>هنوز اعلانی برای شما ثبت نشده است.</p>
        </div>
    </div>
</div>
<?php endif; ?>