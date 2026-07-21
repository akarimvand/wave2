<div class="page-header">
    <h2 class="page-title"><i class="fas fa-headset" style="margin-left:8px;color:#3B82F6;"></i>تیکت‌های من</h2>
    <p>لیست تیکت‌های پشتیبانی</p>
</div>

<div class="page-actions" style="margin-bottom:20px;display:flex;justify-content:flex-end;">
    <a href="<?php echo url('portal/tickets/create'); ?>" class="btn btn-primary">
        <i class="fas fa-plus" style="margin-left:6px;"></i>
        تیکت جدید
    </a>
</div>

<div class="card">
    <div class="card-body">
        <?php if (!empty($tickets)): ?>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ردیف</th>
                        <th>عنوان</th>
                        <th>اولویت</th>
                        <th>وضعیت</th>
                        <th>تاریخ</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $row = 1; ?>
                    <?php foreach ($tickets as $ticket): ?>
                    <?php
                    $priority = $ticket['priority'] ?? 'medium';
                    $pClass = 'badge-info';
                    $pLabel = 'متوسط';
                    $pIcon = 'fa-minus-circle';
                    if ($priority === 'high') {
                        $pClass = 'badge-danger';
                        $pLabel = 'بالا';
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
                    } elseif ($status === 'closed') {
                        $sClass = 'badge-secondary';
                        $sLabel = 'بسته';
                        $sIcon = 'fa-times-circle';
                    } elseif ($status === 'pending') {
                        $sClass = 'badge-warning';
                        $sLabel = 'در انتظار';
                        $sIcon = 'fa-clock';
                    }
                    ?>
                    <tr>
                        <td style="font-size:0.85rem;color:#6B7A8D;"><?php echo $row++; ?></td>
                        <td>
                            <a href="<?php echo url('portal/tickets/' . $ticket['id']); ?>" style="color:#1E293B;text-decoration:none;font-weight:500;">
                                <?php echo e($ticket['subject'] ?? $ticket['title'] ?? ''); ?>
                            </a>
                        </td>
                        <td>
                            <span class="badge <?php echo $pClass; ?>">
                                <i class="fas <?php echo $pIcon; ?>" style="margin-left:4px;font-size:0.65rem;"></i>
                                <?php echo $pLabel; ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge <?php echo $sClass; ?>">
                                <i class="fas <?php echo $sIcon; ?>" style="margin-left:4px;font-size:0.65rem;"></i>
                                <?php echo $sLabel; ?>
                            </span>
                        </td>
                        <td style="font-size:0.85rem;color:#6B7A8D;">
                            <i class="fas fa-calendar" style="margin-left:4px;font-size:0.7rem;"></i>
                            <?php echo formatDateTime($ticket['created_at']); ?>
                        </td>
                        <td>
                            <div class="table-actions">
                                <a href="<?php echo url('portal/tickets/' . $ticket['id']); ?>" class="btn btn-primary btn-sm" title="مشاهده">
                                    <i class="fas fa-eye" style="margin-left:4px;"></i>
                                    مشاهده
                                </a>
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
                <i class="fas fa-headset" style="font-size:48px;color:#D1D5DB;margin-bottom:16px;display:block;"></i>
                <h3>تیکتی یافت نشد</h3>
                <p>هنوز تیکتی ثبت نکرده‌اید.</p>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php if (!empty($pagination)): ?>
<div class="pagination">
    <?php echo $pagination; ?>
</div>
<?php endif; ?>