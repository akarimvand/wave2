<div class="page-header-row">
    <div class="page-header">
        <h2><i class="fas fa-headset" style="margin-left:8px;color:#3B82F6;"></i>جزئیات تیکت</h2>
        <p>مشاهده و پاسخ به تیکت</p>
    </div>
    <div class="page-header-actions">
        <a href="<?php echo url('admin/tickets'); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i>
            بازگشت به لیست
        </a>
    </div>
</div>

<!-- Ticket Info -->
<div class="card" style="margin-bottom:16px;">
    <div class="card-body">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:20px;">
            <h3 style="margin:0;font-size:1.15rem;font-weight:700;color:#1f2937;">
                <?php echo e($ticket['title'] ?? $ticket['subject'] ?? ''); ?>
            </h3>
            <div style="display:flex;gap:8px;align-items:center;">
                <?php
                $priority = $ticket['priority'] ?? 'low';
                $pClass = 'badge-success';
                $pLabel = 'پایین';
                if ($priority === 'high') { $pClass = 'badge-danger'; $pLabel = 'بالا'; }
                elseif ($priority === 'medium') { $pClass = 'badge-warning'; $pLabel = 'متوسط'; }
                ?>
                <span class="badge <?php echo $pClass; ?>"><?php echo $pLabel; ?></span>
                <?php
                $status = $ticket['status'] ?? '';
                if ($status === 'open') echo '<span class="badge badge-info">باز</span>';
                elseif ($status === 'in_progress') echo '<span class="badge badge-warning">در حال بررسی</span>';
                elseif ($status === 'closed') echo '<span class="badge badge-secondary">بسته</span>';
                else echo '<span class="badge badge-info">' . e($status) . '</span>';
                ?>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
            <div style="display:flex;align-items:center;gap:8px;">
                <i class="fas fa-user" style="color:#6b7280;width:18px;text-align:center;"></i>
                <span style="font-size:0.85rem;color:#6b7280;">فرستنده:</span>
                <span style="font-size:0.9rem;font-weight:500;color:#1f2937;"><?php echo e($ticket['member_name'] ?? ''); ?></span>
            </div>
            <div style="display:flex;align-items:center;gap:8px;">
                <i class="fas fa-clock" style="color:#6b7280;width:18px;text-align:center;"></i>
                <span style="font-size:0.85rem;color:#6b7280;">تاریخ:</span>
                <span style="font-size:0.9rem;font-weight:500;color:#1f2937;"><?php echo formatDateTime($ticket['created_at']); ?></span>
            </div>
        </div>

        <div style="padding:16px;background:#F9FAFB;border-radius:8px;border:1px solid #E5E7EB;">
            <div style="font-size:13px;color:#6b7280;margin-bottom:8px;font-weight:600;">
                <i class="fas fa-comment-dots" style="margin-left:4px;"></i>متن پیام
            </div>
            <div style="font-size:14px;line-height:1.8;color:#1f2937;white-space:pre-wrap;"><?php echo e($ticket['body'] ?? $ticket['message'] ?? ''); ?></div>
        </div>
    </div>
</div>

<!-- Status Update -->
<?php if ($ticket['status'] !== 'closed'): ?>
<div class="card" style="margin-bottom:16px;">
    <div class="card-body">
        <form method="POST" action="<?php echo url('admin/tickets/' . $ticket['id'] . '/status'); ?>">
            <?php echo csrf_field(); ?>
            <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                <label class="form-label" style="margin-bottom:0;white-space:nowrap;">
                    <i class="fas fa-exchange-alt" style="margin-left:4px;"></i>تغییر وضعیت:
                </label>
                <select name="status" class="form-input" style="max-width:200px;">
                    <option value="open" <?php echo ($ticket['status'] ?? '') === 'open' ? 'selected' : ''; ?>>باز</option>
                    <option value="in_progress" <?php echo ($ticket['status'] ?? '') === 'in_progress' ? 'selected' : ''; ?>>در حال بررسی</option>
                    <option value="closed" <?php echo ($ticket['status'] ?? '') === 'closed' ? 'selected' : ''; ?>>بسته</option>
                </select>
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-sync-alt"></i> بروزرسانی وضعیت
                </button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- Replies -->
<div class="card">
    <div class="card-header">
        <h3 style="margin:0;font-size:0.95rem;font-weight:600;">
            <i class="fas fa-comments" style="margin-left:8px;color:#3B82F6;"></i>
            پاسخ‌ها (<?php echo count($replies ?? []); ?>)
        </h3>
    </div>
    <div class="card-body">
        <!-- Replies List -->
        <?php if (!empty($replies)): ?>
        <div style="display:flex;flex-direction:column;gap:12px;margin-bottom:24px;">
            <?php foreach ($replies as $reply): ?>
            <div style="display:flex;gap:12px;padding:16px;background:#F9FAFB;border-radius:8px;border:1px solid #E5E7EB;">
                <div style="flex-shrink:0;width:40px;height:40px;border-radius:50%;background:#3B82F6;display:flex;align-items:center;justify-content:center;">
                    <i class="fas fa-user" style="color:#fff;font-size:14px;"></i>
                </div>
                <div style="flex:1;min-width:0;">
                    <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;margin-bottom:8px;flex-wrap:wrap;">
                        <span style="font-weight:600;font-size:0.9rem;color:#1f2937;"><?php echo e($reply['user_name'] ?? 'کاربر'); ?></span>
                        <span style="font-size:0.8rem;color:#9CA3AF;">
                            <i class="fas fa-clock" style="margin-left:4px;"></i>
                            <?php echo formatDateTime($reply['created_at']); ?>
                        </span>
                    </div>
                    <div style="font-size:0.9rem;line-height:1.7;color:#374151;white-space:pre-wrap;"><?php echo e($reply['body'] ?? $reply['message'] ?? ''); ?></div>
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

        <!-- Reply Form -->
        <?php if ($ticket['status'] !== 'closed'): ?>
        <div style="border-top:1px solid #E5E7EB;padding-top:20px;">
            <h4 style="margin:0 0 16px;font-size:0.95rem;font-weight:600;color:#374151;">
                <i class="fas fa-reply" style="margin-left:6px;color:#3B82F6;"></i>
                ارسال پاسخ
            </h4>
            <form method="POST" action="<?php echo url('admin/tickets/' . $ticket['id'] . '/reply'); ?>">
                <?php echo csrf_field(); ?>
                <div class="form-group">
                    <label class="form-label">پاسخ شما <span class="required">*</span></label>
                    <textarea name="body" class="form-input" rows="4" required placeholder="پاسخ خود را بنویسید..." style="min-height:100px;"></textarea>
                </div>
                <div style="display:flex;gap:8px;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane" style="margin-left:6px;"></i>
                        ارسال پاسخ
                    </button>
                </div>
            </form>
        </div>
        <?php else: ?>
        <div style="border-top:1px solid #E5E7EB;padding-top:20px;text-align:center;">
            <div style="display:inline-flex;align-items:center;gap:8px;padding:12px 20px;background:#F9FAFB;border-radius:8px;border:1px solid #E5E7EB;color:#6b7280;font-size:0.9rem;">
                <i class="fas fa-lock"></i>
                این تیکت بسته شده و امکان پاسخ‌دهی وجود ندارد.
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>