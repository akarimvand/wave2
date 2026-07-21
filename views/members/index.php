<!-- Pending Members Alert -->
<?php if (!empty($pendingMembers)): ?>
<div class="card" style="border-color: #f59e0b; border-width: 2px; margin-bottom: 1.25rem;">
    <div class="card-header" style="background: rgba(245, 158, 11, 0.06); border-bottom: 1px solid rgba(245, 158, 11, 0.15); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
        <div style="display:flex;align-items:center;gap:8px;">
            <i class="fas fa-clock" style="color:#f59e0b;font-size:1.1rem;"></i>
            <strong style="color:#92400e;">درخواست‌های تأیید نشد</strong>
            <span class="badge badge-warning" style="font-size:0.75rem;"><?php echo count($pendingMembers); ?></span>
        </div>
        <a href="<?php echo url('admin/members/pending'); ?>" class="btn btn-warning btn-sm">
            <i class="fas fa-list-check"></i>
            مشاهده همه درخواست‌ها
        </a>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>نام و نام خانوادگی</th>
                        <th class="hide-mobile">کد ملی</th>
                        <th>تلفن</th>
                        <th class="hide-mobile">بیمه</th>
                        <th class="hide-mobile">تاریخ ثبت‌نام</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (array_slice($pendingMembers, 0, 5) as $pm): ?>
                    <tr style="background: rgba(245, 158, 11, 0.04);">
                        <td>
                            <strong><?php echo e($pm['first_name'] . ' ' . $pm['last_name']); ?></strong>
                        </td>
                        <td class="hide-mobile" style="direction:ltr;text-align:right;font-size:0.85rem;"><?php echo e($pm['national_code'] ?? '-'); ?></td>
                        <td style="direction:ltr;text-align:right;"><?php echo e($pm['phone']); ?></td>
                        <td class="hide-mobile">
                            <?php if (!empty($pm['has_insurance'])): ?>
                            <span class="badge badge-info"><i class="fas fa-shield-alt" style="margin-left:3px;"></i> دارد</span>
                            <?php else: ?>
                            <span style="color:#9ca3af;font-size:0.82rem;">ندارد</span>
                            <?php endif; ?>
                        </td>
                        <td class="hide-mobile" style="font-size:0.82rem;color:#6b7280;"><?php echo e(formatDate($pm['created_at'])); ?></td>
                        <td>
                            <div class="table-actions">
                                <form method="POST" action="<?php echo url('admin/members/' . $pm['id'] . '/approve'); ?>" style="display:inline;">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="btn btn-success btn-xs" title="تأیید" onclick="return confirm('آیا از تأیید این عضو اطمینان دارید؟')">
                                        <i class="fas fa-check"></i>
                                        تأیید
                                    </button>
                                </form>
                                <form method="POST" action="<?php echo url('admin/members/' . $pm['id'] . '/reject'); ?>" style="display:inline;">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="btn btn-danger btn-xs" title="رد" onclick="return confirm('آیا از رد درخواست این عضو اطمینان دارید؟')">
                                        <i class="fas fa-times"></i>
                                        رد
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if (count($pendingMembers) > 5): ?>
        <div style="text-align:center;padding:0.75rem;border-top:1px solid rgba(245,158,11,0.1);">
            <a href="<?php echo url('admin/members/pending'); ?>" style="color:#d97706;font-size:0.85rem;font-weight:600;">
                و <?php echo count($pendingMembers) - 5; ?> درخواست دیگر...
                <i class="fas fa-arrow-left" style="font-size:0.7rem;margin-right:4px;"></i>
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<div class="page-header-row">
    <div class="page-header">
        <h2 class="page-title">مدیریت اعضا</h2>
        <p>لیست تمام اعضای باشگاه</p>
    </div>
    <div class="page-header-actions">
        <a href="<?php echo url('admin/members/create'); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            عضو جدید
        </a>
    </div>
</div>

<!-- Search & Filter -->
<form method="GET" action="<?php echo url('admin/members'); ?>" class="search-bar">
    <div class="search-input-group">
        <i class="fas fa-search"></i>
        <input type="text" name="search" value="<?php echo e($search ?? ''); ?>" placeholder="جستجو بر اساس نام یا کد ملی..." class="form-input">
    </div>
    <select name="status" class="search-filter">
        <option value="">همه وضعیت‌ها</option>
        <option value="active" <?php echo (($filters['status'] ?? '') === 'active') ? 'selected' : ''; ?>>فعال</option>
        <option value="inactive" <?php echo (($filters['status'] ?? '') === 'inactive') ? 'selected' : ''; ?>>غیرفعال</option>
        <option value="pending" <?php echo (($filters['status'] ?? '') === 'pending') ? 'selected' : ''; ?>>در انتظار تأیید</option>
    </select>
    <button type="submit" class="btn btn-primary btn-sm">جستجو</button>
    <a href="<?php echo url('admin/members'); ?>" class="btn btn-secondary btn-sm">پاک کردن</a>
</form>

<!-- Table -->
<div class="card">
    <div class="card-body">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="hide-mobile">ردیف</th>
                        <th>نام و نام خانوادگی</th>
                        <th class="hide-mobile">کد ملی</th>
                        <th>تلفن</th>
                        <th>وضعیت</th>
                        <th class="hide-mobile">اشتراک</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($members)): ?>
                    <?php $rowNumber = ($page - 1) * $perPage; foreach ($members as $member): $rowNumber++; ?>
                    <tr>
                        <td class="hide-mobile"><?php echo $rowNumber; ?></td>
                        <td><?php echo e($member['first_name'] . ' ' . $member['last_name']); ?></td>
                        <td class="hide-mobile" style="direction:ltr;text-align:right;font-size:0.85rem;"><?php echo e($member['national_code'] ?? '-'); ?></td>
                        <td style="direction:ltr;text-align:right;"><?php echo e($member['phone']); ?></td>
                        <td>
                            <?php if ($member['status'] === 'active' && $member['approval_status'] === 'approved'): ?>
                            <span class="badge badge-success">فعال</span>
                            <?php elseif ($member['status'] === 'pending' || $member['approval_status'] === 'pending'): ?>
                            <span class="badge badge-warning">در انتظار تأیید</span>
                            <?php elseif ($member['approval_status'] === 'rejected'): ?>
                            <span class="badge badge-danger">رد شده</span>
                            <?php else: ?>
                            <span class="badge badge-warning">غیرفعال</span>
                            <?php endif; ?>
                        </td>
                        <td class="hide-mobile"><?php echo e($member['membership_name'] ?? '-'); ?></td>
                        <td>
                            <div class="table-actions">
                                <button type="button" class="btn btn-info btn-xs" onclick="loadMemberDetail(<?php echo $member['id']; ?>)" title="مشاهده">
                                    <i class="fas fa-eye"></i>
                                    مشاهده
                                </button>
                                <a href="<?php echo url('admin/members/' . $member['id'] . '/edit'); ?>" class="btn btn-warning btn-xs" title="ویرایش">
                                    <i class="fas fa-pen-to-square"></i>
                                    ویرایش
                                </a>
                                <form method="POST" action="<?php echo url('admin/members/' . $member['id'] . '/delete'); ?>" style="display:inline;" onclick="return confirmDelete('آیا از حذف این عضو اطمینان دارید؟')">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="btn btn-danger btn-xs" title="حذف">
                                        <i class="fas fa-trash"></i>
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
            <?php if (empty($members)): ?>
            <div class="table-empty">
                <div class="empty-state">
                    <i class="fas fa-users" style="font-size:48px;color:#cbd5e1;margin-bottom:16px;display:block;"></i>
                    <h3>موردی یافت نشد</h3>
                    <p>هنوز عضو‌ای ثبت نشده است.</p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php echo pagination($page, $total, $perPage, 'admin/members'); ?>

<!-- Member Detail Modal -->
<div class="modal fade" id="memberModal" tabindex="-1" aria-labelledby="memberModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="memberModalLabel">
                    <i class="fas fa-user-circle" style="color:#3b82f6;margin-left:6px;"></i>
                    جزئیات عضو
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="بستن"></button>
            </div>
            <div class="modal-body" id="memberModalBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">در حال بارگذاری...</span>
                    </div>
                    <p class="mt-3 text-muted">در حال بارگذاری اطلاعات...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
var memberModalEl = document.getElementById('memberModal');
var memberModalInstance = null;

function loadMemberDetail(memberId) {
    var modalBody = document.getElementById('memberModalBody');
    modalBody.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">در حال بارگذاری...</span></div><p class="mt-3 text-muted">در حال بارگذاری اطلاعات...</p></div>';

    var detailUrl = '<?php echo url("admin/members"); ?>/' + memberId + '/detail';

    fetch(detailUrl)
        .then(function(response) {
            if (!response.ok) {
                throw new Error('خطای سرور: ' + response.status);
            }
            return response.text();
        })
        .then(function(html) {
            // Validate response contains expected tab structure
            if (html.indexOf('nav-tabs') === -1 && html.indexOf('tab-pane') === -1) {
                // Response is probably a redirect/error page, not modal content
                console.error('Invalid modal response:', html.substring(0, 200));
                throw new Error('پاسخ نامعتبر از سرور');
            }

            modalBody.innerHTML = html;

            // Show modal
            if (!memberModalInstance) {
                memberModalInstance = new bootstrap.Modal(memberModalEl);
            }
            memberModalInstance.show();
        })
        .catch(function(error) {
            modalBody.innerHTML = '<div class="text-center py-5 text-danger"><i class="fas fa-exclamation-triangle" style="font-size:48px;"></i><p class="mt-3">' + error.message + '</p><button type="button" class="btn btn-sm btn-outline-secondary mt-3" onclick="memberModalInstance.hide();">بستن</button></div>';
            if (!memberModalInstance) {
                memberModalInstance = new bootstrap.Modal(memberModalEl);
            }
            memberModalInstance.show();
        });
}

// Clean up modal instance when hidden to prevent stale state
memberModalEl.addEventListener('hidden.bs.modal', function() {
    document.getElementById('memberModalBody').innerHTML = '';
    if (memberModalInstance) {
        memberModalInstance.dispose();
        memberModalInstance = null;
    }
});
</script>