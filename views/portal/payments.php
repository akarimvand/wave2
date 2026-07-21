<div class="page-header">
    <h2 class="page-title"><i class="fas fa-receipt" style="margin-left:8px;color:#10B981;"></i>پرداخت‌های من</h2>
    <p>سوابق مالی شما</p>
</div>

<!-- Payments Table -->
<div class="card">
    <div class="card-body">
        <?php if (!empty($payments)): ?>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="hide-mobile">ردیف</th>
                        <th>مبلغ</th>
                        <th>تاریخ</th>
                        <th class="hide-mobile">توضیحات</th>
                        <th>وضعیت</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $row = 1; ?>
                    <?php foreach ($payments as $payment): ?>
                    <tr>
                        <td class="hide-mobile" style="font-size:0.85rem;color:#6B7A8D;"><?php echo $row++; ?></td>
                        <td style="font-weight:600;direction:ltr;text-align:right;">
                            <i class="fas fa-rial-sign" style="margin-left:4px;font-size:0.75rem;color:#10B981;"></i>
                            <?php echo formatCurrency($payment['amount']); ?>
                        </td>
                        <td style="font-size:0.85rem;color:#6B7A8D;">
                            <i class="fas fa-calendar" style="margin-left:4px;font-size:0.75rem;"></i>
                            <?php echo formatDate($payment['payment_date'] ?? $payment['created_at'] ?? ''); ?>
                        </td>
                        <td class="hide-mobile" style="max-width:250px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:0.85rem;" title="<?php echo e($payment['description'] ?? ''); ?>">
                            <?php echo e($payment['description'] ?? $payment['plan_name'] ?? '-'); ?>
                        </td>
                        <td>
                            <?php
                            $status = $payment['status'] ?? '';
                            if ($status === 'paid') {
                                echo '<span class="badge badge-success"><i class="fas fa-check-circle" style="margin-left:4px;font-size:0.65rem;"></i>پرداخت شده</span>';
                            } elseif ($status === 'pending') {
                                echo '<span class="badge badge-warning"><i class="fas fa-clock" style="margin-left:4px;font-size:0.65rem;"></i>در انتظار</span>';
                            } elseif ($status === 'refunded') {
                                echo '<span class="badge badge-info"><i class="fas fa-undo" style="margin-left:4px;font-size:0.65rem;"></i>بازپرداخت شده</span>';
                            } elseif ($status === 'cancelled') {
                                echo '<span class="badge badge-danger"><i class="fas fa-times-circle" style="margin-left:4px;font-size:0.65rem;"></i>لغو شده</span>';
                            } else {
                                echo '<span class="badge badge-secondary">' . e($status) . '</span>';
                            }
                            ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="table-empty">
            <div class="empty-state">
                <i class="fas fa-receipt" style="font-size:48px;color:#D1D5DB;margin-bottom:16px;display:block;"></i>
                <h3>موردی یافت نشد</h3>
                <p>هنوز پرداختی ثبت نشده است.</p>
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