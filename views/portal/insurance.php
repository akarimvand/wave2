<div class="page-header">
    <h2 class="page-title"><i class="fas fa-shield-alt" style="margin-left:8px;color:#3B82F6;"></i>بیمه</h2>
    <p>اطلاعات بیمه شما</p>
</div>

<?php
// Support both $insurance (singular) and $insurances (plural) variable names
$insuranceItems = [];
if (!empty($insurance)) {
    $insuranceItems = [$insurance];
} elseif (!empty($insurances)) {
    $insuranceItems = $insurances;
}
?>

<?php if (!empty($insuranceItems)): ?>
<?php foreach ($insuranceItems as $insurance): ?>
<?php
$insStatus = $insurance['status'] ?? '';
$statusBadge = 'badge-secondary';
$statusLabel = 'نامشخص';
$statusIcon = 'fa-question-circle';
if ($insStatus === 'active') {
    $statusBadge = 'badge-success';
    $statusLabel = 'فعال';
    $statusIcon = 'fa-check-circle';
} elseif ($insStatus === 'expired') {
    $statusBadge = 'badge-secondary';
    $statusLabel = 'منقضی';
    $statusIcon = 'fa-clock';
} elseif ($insStatus === 'cancelled') {
    $statusBadge = 'badge-danger';
    $statusLabel = 'لغو شده';
    $statusIcon = 'fa-times-circle';
}
?>
<div class="card" style="margin-bottom:16px;">
    <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;">
        <div style="display:flex;align-items:center;gap:12px;">
            <div style="width:44px;height:44px;border-radius:12px;background:<?php echo $insStatus === 'active' ? '#10B98115' : '#E5E7EB'; ?>;display:flex;align-items:center;justify-content:center;">
                <i class="fas fa-shield-alt" style="font-size:1.1rem;color:<?php echo $insStatus === 'active' ? '#10B981' : '#6B7A8D'; ?>;"></i>
            </div>
            <div>
                <h3 style="margin:0;font-size:1rem;font-weight:600;"><?php echo e($insurance['insurance_type'] ?? 'بیمه ورزشی'); ?></h3>
            </div>
        </div>
        <span class="badge <?php echo $statusBadge; ?>">
            <i class="fas <?php echo $statusIcon; ?>" style="margin-left:4px;font-size:0.7rem;"></i>
            <?php echo $statusLabel; ?>
        </span>
    </div>
    <div class="card-body">
        <div class="profile-grid">
            <div class="profile-field">
                <span class="profile-field-label">
                    <i class="fas fa-hashtag" style="margin-left:4px;font-size:0.75rem;"></i>
                    شماره بیمه‌نامه
                </span>
                <span class="profile-field-value" style="direction:ltr;text-align:right;font-weight:500;"><?php echo e($insurance['policy_number'] ?? '-'); ?></span>
            </div>
            <div class="profile-field">
                <span class="profile-field-label">
                    <i class="fas fa-money-bill-wave" style="margin-left:4px;font-size:0.75rem;"></i>
                    مبلغ پوشش
                </span>
                <span class="profile-field-value" style="font-weight:600;color:#10B981;"><?php echo formatCurrency($insurance['coverage_amount'] ?? $insurance['premium_amount'] ?? 0); ?></span>
            </div>
            <div class="profile-field">
                <span class="profile-field-label">
                    <i class="fas fa-calendar-plus" style="margin-left:4px;font-size:0.75rem;"></i>
                    تاریخ شروع
                </span>
                <span class="profile-field-value"><?php echo formatDate($insurance['start_date'] ?? ''); ?></span>
            </div>
            <div class="profile-field">
                <span class="profile-field-label">
                    <i class="fas fa-calendar-minus" style="margin-left:4px;font-size:0.75rem;"></i>
                    تاریخ پایان
                </span>
                <span class="profile-field-value"><?php echo formatDate($insurance['end_date'] ?? ''); ?></span>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>
<?php else: ?>
<div class="card">
    <div class="card-body">
        <div class="empty-state" style="padding:48px 20px;">
            <i class="fas fa-shield-alt" style="font-size:48px;color:#D1D5DB;margin-bottom:16px;display:block;"></i>
            <h3>بیمه‌ای ثبت نشده است</h3>
            <p>در حال حاضر بیمه‌ای برای شما ثبت نشده است.</p>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Insurance Upload Form -->
<div class="card" style="margin-top:20px;">
    <div class="card-header">
        <h3 style="margin:0;font-size:0.95rem;font-weight:600;">
            <i class="fas fa-upload" style="margin-left:8px;color:#3B82F6;"></i>
            آپلود بیمه‌نامه جدید
        </h3>
    </div>
    <div class="card-body">
        <form method="POST" action="<?php echo url('portal/insurance/upload'); ?>" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:16px;margin-bottom:16px;">
                <div>
                    <label style="display:block;font-size:0.82rem;color:#6B7A8D;margin-bottom:4px;">نوع بیمه</label>
                    <input type="text" name="insurance_type" value="بیمه ورزشی" class="form-control" required>
                </div>
                <div>
                    <label style="display:block;font-size:0.82rem;color:#6B7A8D;margin-bottom:4px;">شماره بیمه‌نامه <span style="color:#EF4444;">*</span></label>
                    <input type="text" name="policy_number" class="form-control" required placeholder="شماره بیمه‌نامه">
                </div>
                <div>
                    <label style="display:block;font-size:0.82rem;color:#6B7A8D;margin-bottom:4px;">تاریخ شروع <span style="color:#EF4444;">*</span></label>
                    <input type="date" name="start_date" class="form-control" required>
                </div>
                <div>
                    <label style="display:block;font-size:0.82rem;color:#6B7A8D;margin-bottom:4px;">تاریخ پایان <span style="color:#EF4444;">*</span></label>
                    <input type="date" name="end_date" class="form-control" required>
                </div>
            </div>
            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:0.82rem;color:#6B7A8D;margin-bottom:4px;">فایل بیمه‌نامه (تصویر یا PDF - حداکثر ۵ مگابایت)</label>
                <input type="file" name="document" accept="image/jpeg,image/png,image/gif,application/pdf" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-upload" style="margin-left:4px;"></i>
                آپلود
            </button>
        </form>
        <p class="mt-2 text-muted" style="font-size:0.82rem;margin-top:12px;color:#9CA3AF;">
            <i class="fas fa-info-circle" style="margin-left:4px;"></i>
            فایل آپلود شده پس از تأیید مدیر فعال خواهد شد.
        </p>
    </div>
</div>