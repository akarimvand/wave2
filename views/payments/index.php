<div class="page-header">
    <div class="page-title">
        <h2><i class="fas fa-credit-card"></i> پرداخت‌ها</h2>
    </div>
    <div class="page-actions">
        <a href="<?php echo url('admin/payments/create'); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            پرداخت جدید
        </a>
    </div>
</div>

<!-- Search & Filter -->
<form method="GET" action="<?php echo url('admin/payments'); ?>" class="search-bar">
    <div class="search-input-group">
        <i class="fas fa-search"></i>
        <input type="text" name="search" value="<?php echo e($search ?? ''); ?>" placeholder="جستجو بر اساس نام عضو..." class="form-input">
    </div>
    <select name="status" class="search-filter">
        <option value="">همه وضعیت‌ها</option>
        <option value="paid" <?php echo (($filters['status'] ?? '') === 'paid') ? 'selected' : ''; ?>>پرداخت شده</option>
        <option value="pending" <?php echo (($filters['status'] ?? '') === 'pending') ? 'selected' : ''; ?>>در انتظار</option>
        <option value="failed" <?php echo (($filters['status'] ?? '') === 'failed') ? 'selected' : ''; ?>>ناموفق</option>
    </select>
    <button type="submit" class="btn btn-primary btn-sm">جستجو</button>
    <a href="<?php echo url('admin/payments'); ?>" class="btn btn-secondary btn-sm">پاک کردن</a>
</form>

<!-- Table -->
<div class="card">
    <div class="card-body">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ردیف</th>
                        <th>عضو</th>
                        <th>مبلغ</th>
                        <th>نوع پرداخت</th>
                        <th>تاریخ</th>
                        <th>وضعیت</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($payments)): ?>
                    <?php $rowNumber = 1; ?>
                    <?php foreach ($payments as $payment): ?>
                    <?php
                    $status = $payment['status'] ?? '';
                    $method = $payment['payment_method'] ?? '';
                    if ($method === 'cash') $methodLabel = 'نقدی';
                    elseif ($method === 'card') $methodLabel = 'کارت';
                    elseif ($method === 'transfer') $methodLabel = 'Transfer';
                    else $methodLabel = e($method ?: '-');
                    ?>
                    <tr>
                        <td><?php echo $rowNumber++; ?></td>
                        <td><?php echo e($payment['member_name'] ?? ''); ?></td>
                        <td><?php echo formatCurrency($payment['amount']); ?></td>
                        <td><?php echo $methodLabel; ?></td>
                        <td><?php echo formatDate($payment['payment_date'] ?? $payment['created_at'] ?? ''); ?></td>
                        <td>
                            <?php if ($status === 'paid'): ?>
                                <span class="badge badge-success">پرداخت شده</span>
                            <?php elseif ($status === 'pending'): ?>
                                <span class="badge badge-warning">در انتظار</span>
                            <?php elseif ($status === 'failed'): ?>
                                <span class="badge badge-danger">ناموفق</span>
                            <?php else: ?>
                                <span class="badge badge-secondary"><?php echo e($status); ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="table-actions">
                                <a href="<?php echo url('admin/payments/' . $payment['id'] . '/edit'); ?>" class="btn btn-sm btn-warning" title="ویرایش">
                                    <i class="fas fa-edit"></i>
                                    ویرایش
                                </a>
                                <form method="POST" action="<?php echo url('admin/payments/' . $payment['id'] . '/delete'); ?>" style="display:inline;">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirmDelete('آیا از حذف این پرداخت مطمئن هستید؟')" title="حذف">
                                        <i class="fas fa-trash-alt"></i>
                                        حذف
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <?php endif; ?>
                </tbody>
            </table>
            <?php if (empty($payments)): ?>
            <div class="table-empty">
                <div class="empty-state">
                    <i class="fas fa-credit-card"></i>
                    <h3>موردی یافت نشد</h3>
                    <p>هنوز پرداختی ثبت نشده است.</p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php echo $pagination ?? ''; ?>