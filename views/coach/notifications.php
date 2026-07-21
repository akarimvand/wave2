<?php $activeMenu = 'notifications'; $pageTitle = 'اعلانات'; ?>

<div class="page-header-row">
    <div class="page-header">
        <h2 class="page-title">
            <i class="fas fa-bell" style="color:#3B82F6;margin-left:8px;"></i>
            اعلانات
        </h2>
        <p>اعلانات و پیام‌های دریافتی</p>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding:0;">
        <?php if (!empty($notifications)): ?>
        <?php foreach ($notifications as $notif): ?>
        <div style="padding:16px 24px;border-bottom:1px solid #F3F4F6;display:flex;align-items:start;gap:14px;transition:background 0.2s;" onmouseover="this.style.background='#F9FAFB'" onmouseout="this.style.background='transparent'">
            <div style="width:10px;height:10px;border-radius:50%;background:<?php echo !empty($notif['is_read']) ? '#D1D5DB' : '#3B82F6'; ?>;margin-top:7px;flex-shrink:0;"></div>
            <div style="flex:1;">
                <div style="display:flex;justify-content:space-between;align-items:start;gap:8px;flex-wrap:wrap;">
                    <h4 style="margin:0 0 4px 0;font-size:0.92rem;font-weight:600;color:#1f2937;">
                        <?php echo e($notif['title']); ?>
                    </h4>
                    <span style="font-size:0.75rem;color:#9CA3AF;flex-shrink:0;">
                        <?php echo formatDateTime($notif['created_at']); ?>
                    </span>
                </div>
                <p style="margin:0;font-size:0.85rem;color:#6B7A8D;line-height:1.6;">
                    <?php echo e($notif['message']); ?>
                </p>
            </div>
        </div>
        <?php endforeach; ?>
        <?php else: ?>
        <div class="table-empty">
            <div class="empty-state" style="padding:40px;">
                <i class="fas fa-bell-slash" style="font-size:48px;color:#D1D5DB;margin-bottom:16px;display:block;"></i>
                <h3>اعلانی وجود ندارد</h3>
                <p>در حال حاضر اعلان جدیدی ندارید.</p>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>