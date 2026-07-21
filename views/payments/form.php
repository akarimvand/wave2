<?php $isEdit = !empty($payment); ?>

<div class="page-header">
    <div class="page-title">
        <h2><i class="fas fa-credit-card"></i> <?php echo $isEdit ? 'ویرایش پرداخت' : 'افزودن پرداخت جدید'; ?></h2>
    </div>
    <div class="page-actions">
        <a href="<?php echo url('admin/payments'); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i>
            بازگشت
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?php echo $isEdit ? url('admin/payments/' . $payment['id'] . '/update') : url('admin/payments/store'); ?>">
            <?php echo csrf_field(); ?>

            <div class="form-group">
                <label class="form-label">عضو <span class="required">*</span></label>
                <select name="member_id" class="form-input" required>
                    <option value="">انتخاب عضو...</option>
                    <?php foreach ($members as $m): ?>
                        <option value="<?php echo e($m['id']); ?>" <?php echo (isset($payment) && $payment['member_id'] == $m['id']) ? 'selected' : ''; echo old('member_id') == $m['id'] ? 'selected' : ''; ?>>
                            <?php echo e($m['first_name'] . ' ' . $m['last_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">مبلغ <span class="required">*</span></label>
                <input type="number" name="amount" class="form-input" required min="0" value="<?php echo e($payment['amount'] ?? old('amount') ?? ''); ?>">
            </div>

            <div class="form-group">
                <label class="form-label">روش پرداخت <span class="required">*</span></label>
                <select name="payment_method" class="form-input" required>
                    <option value="">انتخاب روش...</option>
                    <option value="cash" <?php echo (isset($payment) && $payment['payment_method'] === 'cash') ? 'selected' : ''; ?><?php echo old('payment_method') === 'cash' ? 'selected' : ''; ?>>نقدی</option>
                    <option value="card" <?php echo (isset($payment) && $payment['payment_method'] === 'card') ? 'selected' : ''; ?><?php echo old('payment_method') === 'card' ? 'selected' : ''; ?>>کارت</option>
                    <option value="transfer" <?php echo (isset($payment) && $payment['payment_method'] === 'transfer') ? 'selected' : ''; ?><?php echo old('payment_method') === 'transfer' ? 'selected' : ''; ?>>Transfer</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">تاریخ پرداخت <span class="required">*</span></label>
                <input type="text" name="payment_date" class="form-input jalali-date" data-datepicker required value="<?php echo e($payment['payment_date'] ?? old('payment_date') ?? ''); ?>" placeholder="انتخاب تاریخ">
            </div>

            <div class="form-group">
                <label class="form-label">وضعیت <span class="required">*</span></label>
                <select name="status" class="form-input" required>
                    <option value="paid" <?php echo (isset($payment) && $payment['status'] === 'paid') ? 'selected' : ''; ?><?php echo old('status') === 'paid' ? 'selected' : ''; ?>>پرداخت شده</option>
                    <option value="pending" <?php echo (isset($payment) && $payment['status'] === 'pending') ? 'selected' : ''; ?><?php echo old('status') === 'pending' ? 'selected' : ''; ?>>در انتظار</option>
                    <option value="failed" <?php echo (isset($payment) && $payment['status'] === 'failed') ? 'selected' : ''; ?><?php echo old('status') === 'failed' ? 'selected' : ''; ?>>ناموفق</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">توضیحات</label>
                <textarea name="description" class="form-input" rows="3"><?php echo e($payment['description'] ?? old('description') ?? ''); ?></textarea>
            </div>

            <div class="form-actions" style="display:flex;gap:8px;margin-top:24px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    ذخیره
                </button>
                <a href="<?php echo url('admin/payments'); ?>" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    انصراف
                </a>
            </div>
        </form>
    </div>
</div>