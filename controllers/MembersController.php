<?php
class MembersController
{
    public function index()
    {
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $search = trim($_GET['search'] ?? '');
        $statusFilter = $_GET['status'] ?? '';
        $approvalFilter = $_GET['approval'] ?? '';

        $where = ['deleted_at' => null];

        if (!empty($statusFilter)) {
            $where['status'] = $statusFilter;
        }
        if (!empty($approvalFilter)) {
            $where['approval_status'] = $approvalFilter;
        }

        if (!empty($search)) {
            $members = db()->getAll(
                "SELECT m.* FROM members m WHERE m.deleted_at IS NULL AND (m.first_name LIKE ? OR m.last_name LIKE ? OR m.phone LIKE ? OR m.national_code LIKE ?) ORDER BY m.id DESC LIMIT {$perPage} OFFSET {$offset}",
                ['%' . $search . '%', '%' . $search . '%', '%' . $search . '%', '%' . $search . '%']
            );
            $totalRow = db()->getOne(
                "SELECT COUNT(*) as total FROM members WHERE deleted_at IS NULL AND (first_name LIKE ? OR last_name LIKE ? OR phone LIKE ? OR national_code LIKE ?)",
                ['%' . $search . '%', '%' . $search . '%', '%' . $search . '%', '%' . $search . '%']
            );
            $total = (int) $totalRow['total'];
        } else {
            $members = db()->select('members', $where, 'id DESC', $perPage, $offset);
            $total = db()->count('members', $where);
        }

        // Attach latest active membership name for each member
        foreach ($members as &$m) {
            $mm = db()->getOne(
                "SELECT mp.name FROM member_memberships mm 
                 INNER JOIN membership_plans mp ON mm.plan_id = mp.id 
                 WHERE mm.member_id = ? AND mm.status = 'active' AND mm.deleted_at IS NULL AND mm.end_date >= CURDATE()
                 ORDER BY mm.created_at DESC LIMIT 1",
                [$m['id']]
            );
            $m['membership_name'] = $mm ? $mm['name'] : null;
        }
        unset($m);

        // Get pending members for the alert at top
        $pendingMembers = [];
        try {
            $pendingMembers = db()->getAll(
                "SELECT m.*, (SELECT COUNT(*) FROM member_insurance mi WHERE mi.member_id = m.id AND mi.status = 'pending_approval' AND mi.deleted_at IS NULL) as has_insurance FROM members m WHERE m.deleted_at IS NULL AND m.approval_status = 'pending' ORDER BY m.id DESC",
                []
            );
        } catch (Exception $e) {}

        render('members/index', [
            'members' => $members,
            'pendingMembers' => $pendingMembers,
            'page' => $page,
            'total' => $total,
            'perPage' => $perPage,
            'search' => $search,
            'filters' => ['status' => $statusFilter],
        ]);
    }

    public function create()
    {
        $memberships = db()->select('membership_plans', ['deleted_at' => null, 'is_active' => 1], 'name ASC');
        render('members/form', ['memberships' => $memberships]);
    }

    public function store()
    {
        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $nationalCode = trim($_POST['national_code'] ?? '');
        $birthDate = $_POST['birth_date'] ?? '';
        $address = trim($_POST['address'] ?? '');
        $emergencyContact = trim($_POST['emergency_contact'] ?? '');
        $emergencyPhone = trim($_POST['emergency_phone'] ?? '');
        $membershipId = !empty($_POST['membership_id']) ? (int) $_POST['membership_id'] : null;
        $status = $_POST['status'] ?? 'active';
        $notes = trim($_POST['notes'] ?? '');
        $referredBy = trim($_POST['referred_by'] ?? '');
        $bloodType = trim($_POST['blood_type'] ?? '');
        $allergies = trim($_POST['allergies'] ?? '');
        $medicalHistory = trim($_POST['medical_history'] ?? '');
        $referralNumber = trim($_POST['referral_number'] ?? '');

        if (empty($firstName) || empty($lastName) || empty($phone)) {
            setFlash('error', 'نام، نام خانوادگی و شماره تلفن الزامی است.', 'error');
            flashOldInput();
            redirect('admin/members/create');
        }

        if (!empty($nationalCode)) {
            $existing = db()->selectOne('members', ['national_code' => $nationalCode, 'deleted_at' => null]);
            if ($existing) {
                setFlash('error', 'کد ملی قبلاً ثبت شده است.', 'error');
                flashOldInput();
                redirect('admin/members/create');
            }
        }

        try {
            $memberId = db()->insert('members', [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'phone' => $phone,
                'email' => $email ?: null,
                'national_code' => $nationalCode ?: null,
                'birth_date' => !empty($birthDate) ? jalaliToGregorian($birthDate) : null,
                'address' => $address ?: null,
                'emergency_contact' => $emergencyContact ?: null,
                'emergency_phone' => $emergencyPhone ?: null,
                'status' => $status,
                'notes' => $notes ?: null,
                'referred_by' => $referredBy ?: null,
                'blood_type' => $bloodType ?: null,
                'allergies' => $allergies ?: null,
                'medical_history' => $medicalHistory ?: null,
                'referral_number' => $referralNumber ?: null,
                'approval_status' => 'approved',
                'approval_date' => date('Y-m-d H:i:s'),
                'approved_by' => auth()->id(),
            ]);

            // If a membership plan was selected, create a member_membership record
            if ($membershipId) {
                $plan = db()->selectOne('membership_plans', ['id' => $membershipId, 'deleted_at' => null]);
                if ($plan) {
                    $startDate = date('Y-m-d');
                    $endDate = date('Y-m-d', strtotime("+{$plan['duration_days']} days"));
                    db()->insert('member_memberships', [
                        'member_id' => $memberId,
                        'plan_id' => $membershipId,
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'price_paid' => $plan['price'] ?? 0,
                        'status' => 'active',
                    ]);
                }
            }

            logActivity('create', 'members', null, 'ثبت عضو جدید: ' . $firstName . ' ' . $lastName);
            setFlash('success', 'عضو جدید با موفقیت ثبت شد.');
        } catch (Exception $e) {
            setFlash('error', 'خطا در ثبت عضو: ' . $e->getMessage(), 'error');
        }

        redirect('admin/members');
    }

    public function edit($id)
    {
        $member = db()->selectOne('members', ['id' => $id, 'deleted_at' => null]);
        if (!$member) {
            setFlash('error', 'عضو مورد نظر یافت نشد.', 'error');
            redirect('admin/members');
        }
        $memberships = db()->select('membership_plans', ['deleted_at' => null, 'is_active' => 1], 'name ASC');

        // Get current active membership plan_id
        $activeMM = db()->getOne(
            "SELECT plan_id FROM member_memberships WHERE member_id = ? AND status = 'active' AND deleted_at IS NULL ORDER BY created_at DESC LIMIT 1",
            [$id]
        );
        $member['membership_id'] = $activeMM ? $activeMM['plan_id'] : null;

        render('members/form', ['member' => $member, 'memberships' => $memberships]);
    }

    public function update($id)
    {
        $member = db()->selectOne('members', ['id' => $id, 'deleted_at' => null]);
        if (!$member) {
            setFlash('error', 'عضو مورد نظر یافت نشد.', 'error');
            redirect('admin/members');
        }

        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $nationalCode = trim($_POST['national_code'] ?? '');
        $birthDate = $_POST['birth_date'] ?? '';
        $address = trim($_POST['address'] ?? '');
        $emergencyContact = trim($_POST['emergency_contact'] ?? '');
        $emergencyPhone = trim($_POST['emergency_phone'] ?? '');
        $membershipId = !empty($_POST['membership_id']) ? (int) $_POST['membership_id'] : null;
        $status = $_POST['status'] ?? 'active';
        $notes = trim($_POST['notes'] ?? '');
        $referredBy = trim($_POST['referred_by'] ?? '');
        $bloodType = trim($_POST['blood_type'] ?? '');
        $allergies = trim($_POST['allergies'] ?? '');
        $medicalHistory = trim($_POST['medical_history'] ?? '');
        $referralNumber = trim($_POST['referral_number'] ?? '');

        if (empty($firstName) || empty($lastName) || empty($phone)) {
            setFlash('error', 'نام، نام خانوادگی و شماره تلفن الزامی است.', 'error');
            flashOldInput();
            redirect('admin/members/' . $id . '/edit');
        }

        if (!empty($nationalCode)) {
            $existing = db()->selectOne('members', ['national_code' => $nationalCode, 'deleted_at' => null]);
            if ($existing && (int) $existing['id'] !== (int) $id) {
                setFlash('error', 'کد ملی قبلاً ثبت شده است.', 'error');
                flashOldInput();
                redirect('admin/members/' . $id . '/edit');
            }
        }

        try {
            db()->updateById('members', $id, [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'phone' => $phone,
                'email' => $email ?: null,
                'national_code' => $nationalCode ?: null,
                'birth_date' => !empty($birthDate) ? jalaliToGregorian($birthDate) : null,
                'address' => $address ?: null,
                'emergency_contact' => $emergencyContact ?: null,
                'emergency_phone' => $emergencyPhone ?: null,
                'status' => $status,
                'notes' => $notes ?: null,
                'referred_by' => $referredBy ?: null,
                'blood_type' => $bloodType ?: null,
                'allergies' => $allergies ?: null,
                'medical_history' => $medicalHistory ?: null,
                'referral_number' => $referralNumber ?: null,
            ]);

            logActivity('update', 'members', $id, 'ویرایش عضو: ' . $firstName . ' ' . $lastName);
            setFlash('success', 'اطلاعات عضو با موفقیت بروزرسانی شد.');
        } catch (Exception $e) {
            setFlash('error', 'خطا در بروزرسانی عضو: ' . $e->getMessage(), 'error');
        }

        redirect('admin/members');
    }

    public function delete($id)
    {
        $member = db()->selectOne('members', ['id' => $id, 'deleted_at' => null]);
        if (!$member) {
            setFlash('error', 'عضو مورد نظر یافت نشد.', 'error');
            redirect('admin/members');
        }

        db()->softDelete('members', $id);
        logActivity('delete', 'members', $id, 'حذف عضو: ' . $member['first_name'] . ' ' . $member['last_name']);
        setFlash('success', 'عضو با موفقیت حذف شد.');
        redirect('admin/members');
    }

    public function detail($id)
    {
        try {
            $member = db()->selectOne('members', ['id' => $id, 'deleted_at' => null]);
            if (!$member) {
                http_response_code(404);
                echo '<div class="text-center py-5"><i class="fas fa-exclamation-triangle" style="font-size:48px;color:#ef4444;"></i><p class="mt-3">عضو مورد نظر یافت نشد.</p></div>';
                return;
            }

            // Active insurance
            $insurance = null;
            try {
                $insurance = db()->getOne(
                    "SELECT mi.*
                     FROM member_insurance mi
                     WHERE mi.member_id = ? AND mi.status = 'active' AND mi.end_date >= CURDATE() AND mi.deleted_at IS NULL
                     ORDER BY mi.created_at DESC LIMIT 1",
                    [$id]
                );
            } catch (Exception $e) { /* table might not exist yet */ }

            // Payment history
            $payments = [];
            try {
                $payments = db()->getAll(
                    "SELECT * FROM payments WHERE member_id = ? AND deleted_at IS NULL ORDER BY payment_date DESC",
                    [$id]
                );
            } catch (Exception $e) {}

            // Calculate balance
            $balance = 0;
            foreach ($payments as $p) {
                if (($p['type'] ?? '') === 'credit' || ($p['payment_type'] ?? '') === 'income') {
                    $balance += (float)$p['amount'];
                } else {
                    $balance -= (float)$p['amount'];
                }
            }

            // Class registrations
            $classRegistrations = [];
            try {
                $classRegistrations = db()->getAll(
                    "SELECT cr.*, c.name as class_name,
                            COALESCE(NULLIF(c.schedule_days, ''), c.schedule_day) as schedule_day,
                            c.start_time, c.end_time,
                            COALESCE(c.start_time, c.schedule_time) as schedule_time,
                            co.first_name as coach_first_name, co.last_name as coach_last_name
                     FROM class_registrations cr
                     INNER JOIN classes c ON cr.class_id = c.id
                     LEFT JOIN coaches co ON c.coach_id = co.id
                     WHERE cr.member_id = ? AND cr.status = 'active' AND cr.deleted_at IS NULL
                     ORDER BY cr.registration_date DESC",
                    [$id]
                );
            } catch (Exception $e) {}

            // Latest active membership
            $activeMembership = null;
            try {
                $activeMembership = db()->getOne(
                    "SELECT mm.*, mp.name as plan_name
                     FROM member_memberships mm
                     INNER JOIN membership_plans mp ON mm.plan_id = mp.id
                     WHERE mm.member_id = ? AND mm.status = 'active' AND mm.deleted_at IS NULL AND mm.end_date >= CURDATE()
                     ORDER BY mm.created_at DESC LIMIT 1",
                    [$id]
                );
            } catch (Exception $e) {}

            include BASE_PATH . '/views/members/detail-modal-content.php';
            exit;
        } catch (Exception $e) {
            echo '<div class="text-center py-5 text-danger">';
            echo '<i class="fas fa-exclamation-triangle" style="font-size:48px;"></i>';
            echo '<p class="mt-3">خطا در بارگذاری اطلاعات: ' . e($e->getMessage()) . '</p>';
            echo '</div>';
        }
    }

    public function approve($id)
    {
        $member = db()->selectOne('members', ['id' => $id, 'deleted_at' => null]);
        if (!$member) {
            setFlash('error', 'عضو مورد نظر یافت نشد.', 'error');
            redirect('admin/members');
        }

        try {
            db()->beginTransaction();

            // Update member status
            db()->updateById('members', $id, [
                'approval_status' => 'approved',
                'status' => 'active',
                'approval_date' => date('Y-m-d H:i:s'),
                'approved_by' => auth()->id(),
            ]);

            // Activate user account if linked
            try {
                db()->query(
                    "UPDATE users u INNER JOIN user_roles ur ON u.id = ur.user_id INNER JOIN roles r ON ur.role_id = r.id SET u.is_active = 1 WHERE r.name = 'member' AND u.username = ?",
                    [$member['national_code'] ?? '']
                );
            } catch (Exception $e) {}

            // Activate pending insurance if exists
            try {
                db()->query(
                    "UPDATE member_insurance SET status = 'active' WHERE member_id = ? AND status = 'pending_approval' AND deleted_at IS NULL",
                    [$id]
                );
            } catch (Exception $e) {}

            db()->commit();

            logActivity('approve', 'members', $id, 'تأیید عضو: ' . $member['first_name'] . ' ' . $member['last_name']);
            setFlash('success', 'عضو با موفقیت تأیید شد و حساب کاربری فعال گردید.');
        } catch (Exception $e) {
            db()->rollback();
            setFlash('error', 'خطا در تأیید عضو: ' . $e->getMessage(), 'error');
        }

        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        if (strpos($referer, 'pending') !== false) {
            redirect('admin/members/pending');
        }
        redirect('admin/members');
    }

    public function reject($id)
    {
        $member = db()->selectOne('members', ['id' => $id, 'deleted_at' => null]);
        if (!$member) {
            setFlash('error', 'عضو مورد نظر یافت نشد.', 'error');
            redirect('admin/members');
        }

        try {
            db()->beginTransaction();

            db()->updateById('members', $id, [
                'approval_status' => 'rejected',
                'status' => 'inactive',
                'approval_date' => date('Y-m-d H:i:s'),
                'approved_by' => auth()->id(),
            ]);

            // Reject pending insurance
            try {
                db()->query(
                    "UPDATE member_insurance SET status = 'cancelled' WHERE member_id = ? AND status = 'pending_approval' AND deleted_at IS NULL",
                    [$id]
                );
            } catch (Exception $e) {}

            db()->commit();

            logActivity('reject', 'members', $id, 'رد عضو: ' . $member['first_name'] . ' ' . $member['last_name']);
            setFlash('success', 'درخواست عضویت رد شد.');
        } catch (Exception $e) {
            db()->rollback();
            setFlash('error', 'خطا در رد عضو: ' . $e->getMessage(), 'error');
        }

        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        if (strpos($referer, 'pending') !== false) {
            redirect('admin/members/pending');
        }
        redirect('admin/members');
    }

    public function pendingMembers()
    {
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $pendingMembers = db()->getAll(
            "SELECT m.*, (SELECT COUNT(*) FROM member_insurance mi WHERE mi.member_id = m.id AND mi.status = 'pending_approval' AND mi.deleted_at IS NULL) as has_insurance FROM members m WHERE m.deleted_at IS NULL AND m.approval_status = 'pending' ORDER BY m.id DESC LIMIT {$perPage} OFFSET {$offset}",
            []
        );
        $total = db()->count('members', ['deleted_at' => null, 'approval_status' => 'pending']);

        render('members/pending', [
            'members' => $pendingMembers,
            'page' => $page,
            'total' => $total,
            'perPage' => $perPage,
        ]);
    }
}