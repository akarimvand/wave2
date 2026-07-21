<div class="page-header-row">
    <div class="page-header">
        <h2><i class="fas fa-shield-alt" style="margin-left:8px;"></i> بیمه اعضا</h2>
        <p>مدیریت بیمه‌نامه‌های اعضا</p>
    </div>
    <div class="page-header-actions">
        <a href="<?php echo url('admin/insurance/create'); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            ثبت بیمه جدید
        </a>
    </div>
</div>

<!-- Search -->
<form method="GET" action="<?php echo url('admin/insurance'); ?>" class="search-bar">
    <div class="search-input-group">
        <i class="fas fa-search"></i>
        <input type="text" name="search" value="<?php echo e($search ?? ''); ?>" placeholder="جستجو بر اساس نام عضو یا شماره بیمه‌نامه..." class="form-input">
    </div>
    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter"></i> جستجو</button>
    <a href="<?php echo url('admin/insurance'); ?>" class="btn btn-secondary btn-sm"><i class="fas fa-times"></i> پاک کردن</a>
</form>

<!-- Table -->
<div class="card">
    <div class="card-body">
        <div class="table-wrapper">
            <?php if (!empty($memberInsurances)): ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ردیف</th>
                        <th>نام عضو</th>
                        <th>شماره بیمه‌نامه</th>
                        <th>نوع بیمه</th>
                        <th>تاریخ شروع</th>
                        <th>تاریخ پایان</th>
                        <th>وضعیت</th>
                        <th>فایل</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $rowNumber = (($page ?? 1) - 1) * ($perPage ?? 20) + 1; ?>
                    <?php foreach ($memberInsurances as $mi): ?>
                    <?php
                    $statusBadge = 'badge-success';
                    $statusLabel = 'فعال';
                    if (($mi['status'] ?? '') === 'expired') { $statusBadge = 'badge-secondary'; $statusLabel = 'منقضی'; }
                    elseif (($mi['status'] ?? '') === 'cancelled') { $statusBadge = 'badge-danger'; $statusLabel = 'لغو شده'; }
                    $memberName = trim(($mi['first_name'] ?? '') . ' ' . ($mi['last_name'] ?? ''));
                    ?>
                    <tr>
                        <td><?php echo $rowNumber++; ?></td>
                        <td><?php echo e($memberName) ?: '-'; ?></td>
                        <td><?php echo e($mi['policy_number'] ?? '-'); ?></td>
                        <td><?php echo e($mi['insurance_type'] ?? '-'); ?></td>
                        <td><?php echo formatDate($mi['start_date'] ?? ''); ?></td>
                        <td><?php echo formatDate($mi['end_date'] ?? ''); ?></td>
                        <td><span class="badge <?php echo $statusBadge; ?>"><?php echo $statusLabel; ?></span></td>
                        <td>
                            <?php if (!empty($mi['document_path'])): ?>
                            <a href="<?php echo url(ltrim($mi['document_path'], '/')); ?>" target="_blank" title="دانلود فایل">
                                <i class="fas fa-file-download" style="color:#2563eb;font-size:16px;"></i>
                            </a>
                            <?php else: ?>
                            <span style="color:#94a3b8;">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?php echo url('admin/insurance/' . $mi['id'] . '/edit'); ?>" class="btn btn-sm" style="color:#2563eb;" title="ویرایش">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="<?php echo url('admin/insurance/' . $mi['id'] . '/delete'); ?>" class="btn btn-sm" style="color:#dc2626;" title="حذف" onclick="return confirm('آیا از حذف این بیمه اطمینان دارید؟');">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-shield-alt" style="font-size:48px;color:#cbd5e1;margin-bottom:12px;"></i>
                <h3>موردی یافت نشد</h3>
                <p>هنوز بیمه‌ای ثبت نشده است.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php echo $pagination ?? ''; ?>