<?php $activeMenu = 'assignments'; ?>
<div class="page-header-row">
    <div class="page-header">
        <h2 class="page-title">
            <i class="fas fa-user-plus" style="color:#059669;margin-left:8px;"></i>
            تخصیص اعضا به کلاس‌ها
        </h2>
        <p>مدیریت تخصیص ورزشکاران دارای اشتراک فعال به کلاس‌ها و مربیان</p>
    </div>
</div>

<!-- Info Banner -->
<div style="padding:14px 20px;background:#F0FDF4;border:1px solid #BBF7D0;border-radius:10px;margin-bottom:20px;display:flex;align-items:center;gap:10px;">
    <i class="fas fa-info-circle" style="color:#059669;font-size:1.1rem;"></i>
    <span style="font-size:0.88rem;color:#065F46;">
        فقط اعضایی که <strong>اشتراک فعال</strong> دارند در اینجا قابل تخصیص هستند. روی هر کلاس کلیک کنید تا اعضای آن را مدیریت کنید.
    </span>
</div>

<!-- Classes Grid -->
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(340px,1fr));gap:16px;">
    <?php foreach ($classes as $cls): ?>
    <?php
    $coachName = trim(($cls['coach_first_name'] ?? '') . ' ' . ($cls['coach_last_name'] ?? ''));
    $currentCount = (int) ($cls['registered_count'] ?? 0);
    $maxPart = (int) ($cls['max_participants'] ?? 0);
    $percent = $maxPart > 0 ? min(($currentCount / $maxPart) * 100, 100) : 0;
    $barColor = $percent >= 90 ? '#EF4444' : ($percent >= 70 ? '#F59E0B' : '#059669');
    ?>
    <div class="card" style="margin-bottom:0;overflow:hidden;">
        <div style="background:linear-gradient(135deg,#065F46,#059669);padding:14px 18px;">
            <div style="display:flex;justify-content:space-between;align-items:start;">
                <div>
                    <h4 style="margin:0 0 4px 0;color:#fff;font-size:0.95rem;font-weight:600;">
                        <?php echo e($cls['name']); ?>
                    </h4>
                    <div style="font-size:0.78rem;color:rgba(255,255,255,0.8);">
                        <?php echo e($coachName) ?: 'بدون مربی'; ?>
                    </div>
                </div>
                <div style="background:rgba(255,255,255,0.2);color:#fff;padding:4px 10px;border-radius:12px;font-size:0.8rem;">
                    <?php echo $currentCount; ?>/<?php echo $maxPart ?: '∞'; ?>
                </div>
            </div>
            <div style="font-size:0.78rem;color:rgba(255,255,255,0.7);margin-top:6px;">
                <i class="fas fa-calendar" style="margin-left:4px;"></i>
                <?php echo e($cls['schedule_days'] ?? $cls['schedule_day'] ?? '-'); ?>
                &nbsp;|&nbsp;
                <i class="fas fa-clock" style="margin-left:4px;"></i>
                <span style="direction:ltr;display:inline-block;"><?php echo e($cls['start_time'] ?? substr($cls['schedule_time'], 0, 5)); ?></span>
            </div>
        </div>
        <?php if ($maxPart > 0): ?>
        <div style="height:4px;background:#F3F4F6;">
            <div style="height:100%;width:<?php echo $percent; ?>%;background:<?php echo $barColor; ?>;transition:width 0.5s;"></div>
        </div>
        <?php endif; ?>
        <div style="padding:14px 18px;">
            <div id="class-members-<?php echo $cls['id']; ?>">
                <div style="text-align:center;padding:8px;color:#9CA3AF;font-size:0.85rem;">
                    <i class="fas fa-arrow-up" style="margin-left:4px;"></i>
                    برای مدیریت اعضا کلیک کنید
                </div>
            </div>
            <div style="display:flex;gap:8px;margin-top:12px;">
                <button type="button" class="btn btn-sm" style="flex:1;background:#059669;color:#fff;border:none;border-radius:8px;" onclick="loadClassMembers(<?php echo $cls['id']; ?>)">
                    <i class="fas fa-users" style="margin-left:4px;"></i>
                    مشاهده/مدیریت اعضا
                </button>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Assignment Modal -->
<div class="modal fade" id="assignmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header" style="background:#F0FDF4;border-bottom:2px solid #BBF7D0;">
                <h5 class="modal-title" id="assignmentModalTitle">
                    <i class="fas fa-user-plus" style="color:#059669;margin-left:8px;"></i>
                    مدیریت اعضای کلاس
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="assignmentModalBody">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary"></div>
                    <p class="mt-2 text-muted">در حال بارگذاری...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
var currentClassId = null;

function loadClassMembers(classId) {
    currentClassId = classId;
    var modalBody = document.getElementById('assignmentModalBody');
    var modalTitle = document.getElementById('assignmentModalTitle');

    modalBody.innerHTML = '<div class="text-center py-4"><div class="spinner-border" style="color:#059669;"></div><p class="mt-2 text-muted">در حال بارگذاری...</p></div>';

    var modal = new bootstrap.Modal(document.getElementById('assignmentModal'));
    modal.show();

    fetch('<?php echo url("admin/assignments/members?class_id="); ?>' + classId)
        .then(function(r) { return r.json(); })
        .then(function(data) {
            renderModalContent(classId, data.members || []);
        })
        .catch(function(err) {
            modalBody.innerHTML = '<div class="text-center py-4 text-danger"><i class="fas fa-exclamation-triangle" style="font-size:36px;"></i><p class="mt-2">خطا در دریافت اطلاعات</p></div>';
        });
}

function renderModalContent(classId, members) {
    var modalBody = document.getElementById('assignmentModalBody');
    var html = '';

    // Search + Add Section
    html += '<div style="margin-bottom:16px;padding:12px;background:#F8FAFC;border-radius:8px;">';
    html += '  <div style="display:flex;gap:8px;margin-bottom:10px;">';
    html += '    <input type="text" id="memberSearchInput" placeholder="جستجوی عضو (نام، تلفن، کد ملی)..." style="flex:1;padding:8px 12px;border:1px solid #D1D5DB;border-radius:8px;font-family:Vazirmatn;font-size:0.88rem;" onkeyup="searchMembers()">';
    html += '    <button type="button" class="btn btn-sm" style="background:#059669;color:#fff;border:none;border-radius:8px;white-space:nowrap;" onclick="searchMembers()"><i class="fas fa-search"></i> جستجو</button>';
    html += '  </div>';
    html += '  <div id="searchResults" style="max-height:160px;overflow-y:auto;"></div>';
    html += '</div>';

    // Current Members
    html += '<h6 style="font-size:0.9rem;font-weight:600;margin-bottom:10px;color:#334155;">';
    html += '  <i class="fas fa-users" style="color:#059669;margin-left:6px;"></i> اعضای ثبت‌نام شده (' + members.length + ' نفر)';
    html += '</h6>';

    if (members.length === 0) {
        html += '<div style="text-align:center;padding:20px;color:#9CA3AF;">';
        html += '  <i class="fas fa-user-slash" style="font-size:32px;"></i>';
        html += '  <p style="margin-top:8px;">هنوز عضوی تخصیص داده نشده است.</p>';
        html += '</div>';
    } else {
        html += '<div style="max-height:300px;overflow-y:auto;">';
        html += '<table style="width:100%;border-collapse:collapse;font-size:0.85rem;">';
        html += '<thead><tr style="background:#F1F5F9;">';
        html += '<th style="padding:8px 10px;text-align:right;">نام</th>';
        html += '<th style="padding:8px 10px;text-align:right;">تلفن</th>';
        html += '<th style="padding:8px 10px;text-align:right;">اشتراک</th>';
        html += '<th style="padding:8px 10px;text-align:right;">عملیات</th>';
        html += '</tr></thead><tbody>';
        members.forEach(function(m) {
            html += '<tr style="border-bottom:1px solid #F3F4F6;">';
            html += '<td style="padding:8px 10px;font-weight:500;">' + escapeHtml(m.first_name + ' ' + m.last_name) + '</td>';
            html += '<td style="padding:8px 10px;direction:ltr;text-align:right;">' + escapeHtml(m.phone || '-') + '</td>';
            html += '<td style="padding:8px 10px;"><span style="background:#05966915;color:#059669;padding:2px 8px;border-radius:8px;font-size:0.78rem;">' + escapeHtml(m.plan_name || 'فعال') + '</span></td>';
            html += '<td style="padding:8px 10px;"><button type="button" class="btn btn-danger btn-xs" onclick="removeMember(' + m.id + ', ' + classId + ', this)"><i class="fas fa-times"></i> حذف</button></td>';
            html += '</tr>';
        });
        html += '</tbody></table></div>';
    }

    modalBody.innerHTML = html;
}

function searchMembers() {
    var search = document.getElementById('memberSearchInput').value;
    var resultsDiv = document.getElementById('searchResults');

    if (search.length < 2) {
        resultsDiv.innerHTML = '<div style="padding:8px;color:#9CA3AF;font-size:0.82rem;text-align:center;">حداقل ۲ حرف وارد کنید...</div>';
        return;
    }

    resultsDiv.innerHTML = '<div style="text-align:center;padding:8px;"><div class="spinner-border spinner-border-sm" style="color:#059669;"></div></div>';

    fetch('<?php echo url("admin/assignments/members?class_id="); ?>' + currentClassId + '&search=' + encodeURIComponent(search))
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (!data.members || data.members.length === 0) {
                resultsDiv.innerHTML = '<div style="padding:8px;color:#9CA3AF;font-size:0.82rem;text-align:center;">عضوی یافت نشد.</div>';
                return;
            }
            var html = '';
            data.members.forEach(function(m) {
                html += '<div style="display:flex;justify-content:space-between;align-items:center;padding:8px 10px;border-bottom:1px solid #F3F4F6;transition:background 0.2s;" onmouseover="this.style.background=\'#F8FAFC\'" onmouseout="this.style.background=\'transparent\'">';
                html += '  <div><strong>' + escapeHtml(m.first_name + ' ' + m.last_name) + '</strong>';
                html += '  <br><small style="color:#6B7A8D;">' + escapeHtml(m.plan_name || '') + ' | ' + escapeHtml(m.phone || '') + '</small></div>';
                html += '  <button type="button" class="btn btn-sm" style="background:#059669;color:#fff;border:none;border-radius:6px;font-size:0.78rem;" onclick="assignMember(' + m.id + ', ' + currentClassId + ', this)"><i class="fas fa-plus" style="margin-left:3px;"></i> تخصیص</button>';
                html += '</div>';
            });
            resultsDiv.innerHTML = html;
        });
}

function assignMember(memberId, classId, btn) {
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

    var formData = new FormData();
    formData.append('member_id', memberId);
    formData.append('class_id', classId);
    formData.append('_token', '<?php echo csrf_token(); ?>');

    fetch('<?php echo url("admin/assignments/assign"); ?>', { method: 'POST', body: formData })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                loadClassMembers(classId);
            } else {
                alert(data.message);
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-plus" style="margin-left:3px;"></i> تخصیص';
            }
        })
        .catch(function() {
            alert('خطا در ارتباط با سرور');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-plus" style="margin-left:3px;"></i> تخصیص';
        });
}

function removeMember(memberId, classId, btn) {
    if (!confirm('آیا از حذف این عضو از کلاس اطمینان دارید؟')) return;
    btn.disabled = true;

    var formData = new FormData();
    formData.append('member_id', memberId);
    formData.append('class_id', classId);
    formData.append('_token', '<?php echo csrf_token(); ?>');

    fetch('<?php echo url("admin/assignments/remove"); ?>', { method: 'POST', body: formData })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                loadClassMembers(classId);
            } else {
                alert(data.message);
                btn.disabled = false;
            }
        });
}

function escapeHtml(str) {
    if (!str) return '';
    var div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}
</script>