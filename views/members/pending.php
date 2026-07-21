<div class="page-header-row">
    <div class="page-header">
        <h2 class="page-title">
            <i class="fas fa-clock" style="color:#f59e0b;margin-left:6px;"></i>
            درخواست‌های تأیید نشده
        </h2>
        <p>لیست اعضایی که منتظر تأیید مدیر هستند</p>
    </div>
    <div class="page-header-actions">
        <a href="<?php echo url('admin/members'); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i>
            بازگشت به لیست اعضا
        </a>
    </div>
</div>

<?php if (!empty($members)): ?>
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
                        <th class="hide-mobile">ایمیل</th>
                        <th class="hide-mobile">گروه خونی</th>
                        <th class="hide-mobile">بیمه</th>
                        <th class="hide-mobile">تاریخ ثبت‌نام</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $rowNumber = ($page - 1) * $perPage; foreach ($members as $member): $rowNumber++; ?>
                    <tr>
                        <td class="hide-mobile"><?php echo $rowNumber; ?></td>
                        <td>
                            <strong><?php echo e($member['first_name'] . ' ' . $member['last_name']); ?></strong>
                        </td>
                        <td class="hide-mobile" style="direction:ltr;text-align:right;font-size:0.85rem;"><?php echo e($member['national_code'] ?? '-'); ?></td>
                        <td style="direction:ltr;text-align:right;"><?php echo e($member['phone']); ?></td>
                        <td class="hide-mobile" style="font-size:0.85rem;"><?php echo e($member['email'] ?? '-'); ?></td>
                        <td class="hide-mobile">
                            <?php if (!empty($member['blood_type'])): ?>
                            <span class="badge badge-danger" style="font-size:0.78rem;"><?php echo e($member['blood_type']); ?></span>
                            <?php else: ?>
                            <span style="color:#9ca3af;font-size:0.82rem;">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="hide-mobile">
                            <?php if (!empty($member['has_insurance'])): ?>
                            <span class="badge badge-info"><i class="fas fa-shield-alt" style="margin-left:3px;"></i> دارد</span>
                            <?php else: ?>
                            <span style="color:#9ca3af;font-size:0.82rem;">ندارد</span>
                            <?php endif; ?>
                        </td>
                        <td class="hide-mobile" style="font-size:0.82rem;color:#6b7280;"><?php echo e(formatDate($member['created_at'])); ?></td>
                        <td>
                            <div class="table-actions">
                                <button type="button" class="btn btn-info btn-xs" onclick="loadMemberDetail(<?php echo $member['id']; ?>)" title="مشاهده">
                                    <i class="fas fa-eye"></i>
                                    مشاهده
                                </button>
                                <form method="POST" action="<?php echo url('admin/members/' . $member['id'] . '/approve'); ?>" style="display:inline;">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="btn btn-success btn-xs" title="تأیید" onclick="return confirm('آیا از تأیید این عضو اطمینان دارید؟ حساب کاربری فعال خواهد شد.')">
                                        <i class="fas fa-check"></i>
                                        تأیید
                                    </button>
                                </form>
                                <form method="POST" action="<?php echo url('admin/members/' . $member['id'] . '/reject'); ?>" style="display:inline;">
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
    </div>
</div>

<?php echo pagination($page, $total, $perPage, 'admin/members/pending'); ?>

<?php else: ?>
<div class="card">
    <div class="card-body">
        <div class="table-empty">
            <div class="empty-state">
                <i class="fas fa-check-circle" style="font-size:48px;color:#10b981;margin-bottom:16px;display:block;"></i>
                <h3>درخواست تأیید نشده‌ای وجود ندارد</h3>
                <p>تمام درخواست‌های عضویت بررسی شده‌اند.</p>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

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
            if (html.indexOf('nav-tabs') === -1 && html.indexOf('tab-pane') === -1) {
                console.error('Invalid modal response:', html.substring(0, 200));
                throw new Error('پاسخ نامعتبر از سرور');
            }

            modalBody.innerHTML = html;

            if (!memberModalInstance) {
                memberModalInstance = new bootstrap.Modal(memberModalEl);
            }
            memberModalInstance.show();
        })
        .catch(function(error) {
            modalBody.innerHTML = '<div class="text-center py-5 text-danger"><i class="fas fa-exclamation-triangle" style="font-size:48px;"></i><p class="mt-3">' + error.message + '</p></div>';
            if (!memberModalInstance) {
                memberModalInstance = new bootstrap.Modal(memberModalEl);
            }
            memberModalInstance.show();
        });
}

memberModalEl.addEventListener('hidden.bs.modal', function() {
    document.getElementById('memberModalBody').innerHTML = '';
    if (memberModalInstance) {
        memberModalInstance.dispose();
        memberModalInstance = null;
    }
});
</script>