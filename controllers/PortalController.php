<?php
class PortalController
{
    private function getMember()
    {
        $userId = auth()->id();
        // Link: users.username = members.national_code, users.id = user_roles.user_id
        $member = db()->getOne(
            "SELECT m.* FROM members m 
             INNER JOIN users u ON m.national_code = u.username AND u.id = ?
             WHERE m.deleted_at IS NULL
             LIMIT 1",
            [$userId]
        );

        return $member;
    }

    public function dashboard()
    {
        $member = $this->getMember();
        $data = [];

        if ($member) {
            // Active membership
            $data['activeMembership'] = db()->getOne(
                "SELECT mm.*, mp.name as plan_name, mp.duration_days 
                 FROM member_memberships mm 
                 JOIN membership_plans mp ON mm.plan_id = mp.id 
                 WHERE mm.member_id = ? AND mm.status = 'active' AND mm.deleted_at IS NULL 
                 ORDER BY mm.end_date DESC LIMIT 1",
                [$member['id']]
            );

            // Upcoming classes
            $data['upcomingClasses'] = db()->getAll(
                "SELECT c.*, co.first_name as coach_first_name, co.last_name as coach_last_name,
                        (SELECT COUNT(*) FROM class_registrations cr WHERE cr.class_id = c.id AND cr.status = 'active' AND cr.deleted_at IS NULL) as registered_count
                 FROM classes c 
                 LEFT JOIN coaches co ON c.coach_id = co.id 
                 WHERE c.is_active = 1 AND c.deleted_at IS NULL 
                 ORDER BY c.schedule_day ASC, c.schedule_time ASC 
                 LIMIT 10"
            );

            // Registered classes
            $data['myClasses'] = db()->getAll(
                "SELECT c.*, co.first_name as coach_first_name, co.last_name as coach_last_name, cr.status as reg_status
                 FROM class_registrations cr 
                 JOIN classes c ON cr.class_id = c.id 
                 LEFT JOIN coaches co ON c.coach_id = co.id 
                 WHERE cr.member_id = ? AND cr.status = 'active' AND cr.deleted_at IS NULL 
                 ORDER BY c.schedule_day ASC, c.schedule_time ASC",
                [$member['id']]
            );

            // Open tickets
            $data['openTickets'] = db()->count('tickets', [
                'member_id' => $member['id'],
                'status' => ['open', 'in_progress'],
                'deleted_at' => null,
            ]);

            // Unread notifications
            $data['unreadNotifications'] = db()->count('notifications', [
                'user_id' => auth()->id(),
                'is_read' => 0,
            ]);

            // Recent payments
            $data['recentPayments'] = db()->getAll(
                "SELECT p.*, mp.name as plan_name 
                 FROM payments p 
                 LEFT JOIN member_memberships mm ON p.membership_id = mm.id 
                 LEFT JOIN membership_plans mp ON mm.plan_id = mp.id 
                 WHERE p.member_id = ? AND p.deleted_at IS NULL 
                 ORDER BY p.payment_date DESC 
                 LIMIT 5",
                [$member['id']]
            );

            // Recent attendance records
            $data['recentAttendance'] = db()->getAll(
                "SELECT ca.*, c.name as class_name, ca.status as attendance_status,
                       ca.attendance_date, ca.notes
                 FROM class_attendance ca
                 JOIN class_registrations cr ON ca.registration_id = cr.id
                 JOIN classes c ON ca.class_id = c.id
                 WHERE cr.member_id = ? AND cr.status = 'active' AND cr.deleted_at IS NULL
                 ORDER BY ca.attendance_date DESC LIMIT 20",
                [$member['id']]
            );

            // Insurance status for dashboard card
            $activeInsurance = db()->getOne(
                "SELECT * FROM member_insurance 
                 WHERE member_id = ? AND status = 'active' AND end_date >= CURDATE() AND deleted_at IS NULL 
                 ORDER BY created_at DESC LIMIT 1",
                [$member['id']]
            );
            $data['insuranceStatus'] = $activeInsurance ? 'active' : null;

            // Stats: total registered classes
            $data['totalClassesRegistered'] = count($data['myClasses'] ?? []);

            // Stats: attendance this month (present status)
            $data['monthlyAttendanceCount'] = (int) db()->getOne(
                "SELECT COUNT(*) as cnt FROM class_attendance ca
                 JOIN class_registrations cr ON ca.registration_id = cr.id
                 WHERE cr.member_id = ? AND cr.status = 'active' AND cr.deleted_at IS NULL
                 AND ca.status = 'present'
                 AND YEAR(ca.attendance_date) = YEAR(CURDATE())
                 AND MONTH(ca.attendance_date) = MONTH(CURDATE())",
                [$member['id']]
            )['cnt'];
        }

        $data['member'] = $member;

        // Map to view-expected variable names
        $data['membership'] = $data['activeMembership'] ?? null;

        render('portal/dashboard', $data, 'portal');
    }

    public function profile()
    {
        $member = $this->getMember();
        if (!$member) {
            setFlash('error', 'اطلاعات عضو یافت نشد.', 'error');
            redirect('portal/dashboard');
        }
        render('portal/profile', ['member' => $member], 'portal');
    }

    public function profileUpdate()
    {
        $member = $this->getMember();
        if (!$member) {
            setFlash('error', 'اطلاعات عضو یافت نشد.', 'error');
            redirect('portal/dashboard');
        }

        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $emergencyContact = trim($_POST['emergency_contact'] ?? '');
        $address = trim($_POST['address'] ?? '');

        if (empty($firstName) || empty($lastName)) {
            setFlash('error', 'نام و نام خانوادگی الزامی است.', 'error');
            redirect('portal/profile');
        }

        try {
            db()->updateById('members', $member['id'], [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'phone' => $phone ?: null,
                'email' => $email ?: null,
                'emergency_contact' => $emergencyContact ?: null,
                'address' => $address ?: null,
            ]);

            logActivity('update', 'members', $member['id'], 'بروزرسانی پروفایل پورتال');
            setFlash('success', 'پروفایل با موفقیت بروزرسانی شد.');
        } catch (Exception $e) {
            setFlash('error', 'خطا در بروزرسانی پروفایل.', 'error');
        }

        redirect('portal/profile');
    }

    public function classes()
    {
        $member = $this->getMember();

        $availableClasses = db()->getAll(
            "SELECT c.*, co.first_name as coach_first_name, co.last_name as coach_last_name,
                    (SELECT COUNT(*) FROM class_registrations cr WHERE cr.class_id = c.id AND cr.status = 'active' AND cr.deleted_at IS NULL) as registered_count
             FROM classes c 
             LEFT JOIN coaches co ON c.coach_id = co.id 
             WHERE c.is_active = 1 AND c.deleted_at IS NULL 
             ORDER BY c.schedule_day ASC, c.schedule_time ASC"
        );

        $myRegisteredIds = [];
        if ($member) {
            $myRegs = db()->getAll(
                "SELECT class_id FROM class_registrations WHERE member_id = ? AND status = 'active' AND deleted_at IS NULL",
                [$member['id']]
            );
            $myRegisteredIds = array_column($myRegs, 'class_id');
        }

        render('portal/classes', [
            'availableClasses' => $availableClasses,
            'myRegisteredIds' => $myRegisteredIds,
        ], 'portal');
    }

    public function classRegister($id)
    {
        $member = $this->getMember();
        if (!$member) {
            setFlash('error', 'اطلاعات عضو یافت نشد.', 'error');
            redirect('portal/classes');
        }

        $class = db()->selectOne('classes', ['id' => $id, 'is_active' => 1, 'deleted_at' => null]);
        if (!$class) {
            setFlash('error', 'کلاس مورد نظر یافت نشد.', 'error');
            redirect('portal/classes');
        }

        // Check if already registered
        $existing = db()->selectOne('class_registrations', [
            'member_id' => $member['id'],
            'class_id' => $id,
            'status' => 'active',
            'deleted_at' => null,
        ]);
        if ($existing) {
            setFlash('error', 'شما قبلاً در این کلاس ثبت‌نام کرده‌اید.', 'error');
            redirect('portal/classes');
        }

        // Check capacity
        $registeredCount = db()->count('class_registrations', [
            'class_id' => $id,
            'status' => 'active',
            'deleted_at' => null,
        ]);
        $maxParticipants = (int) ($class['max_participants'] ?? 0);
        if ($maxParticipants > 0 && $registeredCount >= $maxParticipants) {
            setFlash('error', 'ظرفیت کلاس تکمیل شده است.', 'error');
            redirect('portal/classes');
        }

        // Check insurance - member must have active valid insurance
        $insurance = db()->getOne(
            "SELECT * FROM member_insurance 
             WHERE member_id = ? AND status = 'active' AND end_date >= CURDATE() AND deleted_at IS NULL 
             ORDER BY created_at DESC LIMIT 1",
            [$member['id']]
        );

        if (!$insurance) {
            setFlash('error', 'ثبت‌نام در کلاس امکان‌پذیر نیست. شما بیمه فعال و معتبری ندارید. لطفاً ابتدا بیمه خود را فعال کنید.', 'error');
            redirect('portal/classes');
        }

        // Check insurance coverage days vs class schedule
        $classDays = !empty($class['schedule_days']) ? explode(',', $class['schedule_days']) : [];
        if (!empty($classDays) && $insurance) {
            $daysUntilExpiry = (strtotime($insurance['end_date']) - strtotime(date('Y-m-d'))) / 86400;
            $classDaysPerWeek = count($classDays);
            // Estimate: if class runs weekly, need at least 4 weeks of coverage
            $minRequiredDays = max($classDaysPerWeek * 4, 7);
            if ($daysUntilExpiry < $minRequiredDays) {
                // Notify admin about low insurance coverage
                $adminUsers = db()->getAll(
                    "SELECT u.id FROM users u 
                     INNER JOIN user_roles ur ON u.id = ur.user_id 
                     INNER JOIN roles r ON ur.role_id = r.id AND r.name IN ('admin', 'manager')
                     WHERE u.is_active = 1 AND u.deleted_at IS NULL"
                );
                foreach ($adminUsers as $admin) {
                    db()->insert('notifications', [
                        'user_id' => $admin['id'],
                        'title' => 'هشدار پوشش بیمه ناکافی',
                        'message' => sprintf(
                            'بیمه %s %s تا %s روز دیگر منقضی می‌شود که برای کلاس "%s" کافی نیست.',
                            $member['first_name'], $member['last_name'],
                            (int)$daysUntilExpiry, $class['name']
                        ),
                        'related_module' => 'insurance',
                        'related_id' => $insurance['id'],
                        'is_read' => 0,
                    ]);
                }
                // Also notify the member
                db()->insert('notifications', [
                    'user_id' => auth()->id(),
                    'title' => 'هشدار بیمه',
                    'message' => sprintf(
                        'بیمه شما تا %s روز دیگر منقضی می‌شود. لطفاً نسبت به تمدید آن اقدام کنید.',
                        (int)$daysUntilExpiry
                    ),
                    'related_module' => 'insurance',
                    'related_id' => $insurance['id'],
                    'is_read' => 0,
                ]);
                setFlash('warning', 'توجه: پوشش بیمه شما کمتر از مدت مورد نیاز کلاس است. به مدیر اطلاع داده شد. لطفاً بیمه خود را تمدید کنید.', 'warning');
            }
        }

        try {
            db()->insert('class_registrations', [
                'member_id' => $member['id'],
                'class_id' => (int) $id,
                'status' => 'active',
            ]);

            logActivity('register', 'classes', $id, 'ثبت‌نام در کلاس: ' . $class['name']);
            setFlash('success', 'با موفقیت در کلاس ثبت‌نام شدید.');
        } catch (Exception $e) {
            setFlash('error', 'خطا در ثبت‌نام در کلاس.', 'error');
        }

        redirect('portal/classes');
    }

    public function classUnregister($id)
    {
        $member = $this->getMember();
        if (!$member) {
            setFlash('error', 'اطلاعات عضو یافت نشد.', 'error');
            redirect('portal/classes');
        }

        $registration = db()->selectOne('class_registrations', [
            'member_id' => $member['id'],
            'class_id' => $id,
            'status' => 'active',
            'deleted_at' => null,
        ]);

        if (!$registration) {
            setFlash('error', 'ثبت‌نام شما در این کلاس یافت نشد.', 'error');
            redirect('portal/classes');
        }

        try {
            db()->updateById('class_registrations', $registration['id'], [
                'status' => 'cancelled',
            ]);

            logActivity('unregister', 'classes', $id, 'لغو ثبت‌نام از کلاس #' . $id);
            setFlash('success', 'ثبت‌نام شما در کلاس لغو شد.');
        } catch (Exception $e) {
            setFlash('error', 'خطا در لغو ثبت‌نام.', 'error');
        }

        redirect('portal/classes');
    }

    public function payments()
    {
        $member = $this->getMember();
        if (!$member) {
            redirect('portal/dashboard');
        }

        $payments = db()->getAll(
            "SELECT p.*, mp.name as plan_name 
             FROM payments p 
             LEFT JOIN member_memberships mm ON p.membership_id = mm.id 
             LEFT JOIN membership_plans mp ON mm.plan_id = mp.id 
             WHERE p.member_id = ? AND p.deleted_at IS NULL 
             ORDER BY p.payment_date DESC",
            [$member['id']]
        );

        render('portal/payments', ['payments' => $payments], 'portal');
    }

    public function insurance()
    {
        $member = $this->getMember();
        if (!$member) {
            redirect('portal/dashboard');
        }

        $insurances = db()->getAll(
            "SELECT mi.* 
             FROM member_insurance mi 
             WHERE mi.member_id = ? AND mi.deleted_at IS NULL 
             ORDER BY mi.created_at DESC",
            [$member['id']]
        );

        render('portal/insurance', ['insurances' => $insurances], 'portal');
    }

    public function tickets()
    {
        $member = $this->getMember();
        if (!$member) {
            redirect('portal/dashboard');
        }

        $tickets = db()->select('tickets', [
            'member_id' => $member['id'],
            'deleted_at' => null,
        ], 'created_at DESC');

        render('portal/tickets', ['tickets' => $tickets], 'portal');
    }

    public function ticketForm()
    {
        render('portal/ticket-form', [], 'portal');
    }

    public function ticketStore()
    {
        $member = $this->getMember();
        if (!$member) {
            setFlash('error', 'اطلاعات عضو یافت نشد.', 'error');
            redirect('portal/tickets');
        }

        $subject = trim($_POST['subject'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $priority = $_POST['priority'] ?? 'medium';

        if (empty($subject) || empty($description)) {
            setFlash('error', 'موضوع و متن تیکت الزامی است.', 'error');
            redirect('portal/tickets/create');
        }

        try {
            $ticketId = db()->insert('tickets', [
                'member_id' => $member['id'],
                'subject' => $subject,
                'description' => $description,
                'priority' => $priority,
                'status' => 'open',
            ]);

            logActivity('create', 'tickets', $ticketId, 'ایجاد تیکت جدید: ' . $subject);
            setFlash('success', 'تیکت با موفقیت ثبت شد.');
        } catch (Exception $e) {
            setFlash('error', 'خطا در ثبت تیکت.', 'error');
        }

        redirect('portal/tickets');
    }

    public function ticketDetail($id)
    {
        $member = $this->getMember();
        if (!$member) {
            redirect('portal/dashboard');
        }

        $ticket = db()->selectOne('tickets', [
            'id' => $id,
            'member_id' => $member['id'],
            'deleted_at' => null,
        ]);

        if (!$ticket) {
            setFlash('error', 'تیکت مورد نظر یافت نشد.', 'error');
            redirect('portal/tickets');
        }

        $replies = db()->getAll(
            "SELECT tr.*, u.full_name as user_name 
             FROM ticket_replies tr 
             LEFT JOIN users u ON tr.user_id = u.id 
             WHERE tr.ticket_id = ? AND tr.deleted_at IS NULL 
             ORDER BY tr.created_at ASC",
            [$id]
        );

        render('portal/ticket-detail', ['ticket' => $ticket, 'replies' => $replies], 'portal');
    }

    public function attendance()
    {
        $member = $this->getMember();
        if (!$member) {
            redirect('portal/dashboard');
        }

        // Get member's registered classes for filter dropdown
        $data['registeredClasses'] = db()->getAll(
            "SELECT c.id, c.name FROM class_registrations cr 
             JOIN classes c ON cr.class_id = c.id 
             WHERE cr.member_id = ? AND cr.status = 'active' AND cr.deleted_at IS NULL 
             ORDER BY c.name ASC",
            [$member['id']]
        );

        // Build query with optional filters
        $params = [$member['id']];
        $where = "cr.member_id = ? AND cr.status = 'active' AND cr.deleted_at IS NULL";

        // Date filter
        $dateFilter = trim($_GET['date'] ?? '');
        $data['dateFilter'] = $dateFilter;
        if (!empty($dateFilter)) {
            $where .= " AND ca.attendance_date = ?";
            $params[] = $dateFilter;
        }

        // Class filter
        $classFilter = (int) ($_GET['class_id'] ?? 0);
        $data['classFilter'] = $classFilter;
        if ($classFilter > 0) {
            $where .= " AND ca.class_id = ?";
            $params[] = $classFilter;
        }

        // Fetch filtered attendance
        $data['attendanceRecords'] = db()->getAll(
            "SELECT ca.*, c.name as class_name, ca.status as attendance_status,
                   ca.attendance_date, ca.notes
             FROM class_attendance ca
             JOIN class_registrations cr ON ca.registration_id = cr.id
             JOIN classes c ON ca.class_id = c.id
             WHERE $where
             ORDER BY ca.attendance_date DESC LIMIT 100",
            $params
        );

        // Summary stats (all time for this member)
        $statsBase = "SELECT ca.status, COUNT(*) as cnt
                      FROM class_attendance ca
                      JOIN class_registrations cr ON ca.registration_id = cr.id
                      WHERE cr.member_id = ? AND cr.status = 'active' AND cr.deleted_at IS NULL
                      GROUP BY ca.status";
        $statsRows = db()->getAll($statsBase, [$member['id']]);
        $data['totalPresent'] = 0;
        $data['totalAbsent'] = 0;
        $data['totalLate'] = 0;
        foreach ($statsRows as $row) {
            if ($row['status'] === 'present') $data['totalPresent'] = (int) $row['cnt'];
            elseif ($row['status'] === 'absent') $data['totalAbsent'] = (int) $row['cnt'];
            elseif ($row['status'] === 'late') $data['totalLate'] = (int) $row['cnt'];
        }

        render('portal/attendance', $data, 'portal');
    }

    public function uploadInsurance()
    {
        $member = $this->getMember();
        if (!$member) {
            setFlash('error', 'اطلاعات عضو یافت نشد.', 'error');
            redirect('portal/insurance');
        }

        $insuranceType = trim($_POST['insurance_type'] ?? 'بیمه ورزشی');
        $policyNumber = trim($_POST['policy_number'] ?? '');
        $startDate = trim($_POST['start_date'] ?? '');
        $endDate = trim($_POST['end_date'] ?? '');

        if (empty($policyNumber) || empty($startDate) || empty($endDate)) {
            setFlash('error', 'شماره بیمه‌نامه، تاریخ شروع و تاریخ پایان الزامی است.', 'error');
            redirect('portal/insurance');
        }

        // Handle file upload
        $documentPath = null;
        if (isset($_FILES['document']) && $_FILES['document']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['document'];
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
            $maxSize = 5 * 1024 * 1024; // 5MB

            if (!in_array($file['type'], $allowedTypes)) {
                setFlash('error', 'فرمت فایل مجاز نیست. فقط تصاویر و PDF قابل قبول است.', 'error');
                redirect('portal/insurance');
            }

            if ($file['size'] > $maxSize) {
                setFlash('error', 'حجم فایل نباید بیشتر از ۵ مگابایت باشد.', 'error');
                redirect('portal/insurance');
            }

            $uploadDir = BASE_PATH . '/public/uploads/insurance/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $fileName = 'insurance_' . $member['id'] . '_' . time() . '.' . $ext;
            $documentPath = 'uploads/insurance/' . $fileName;

            if (!move_uploaded_file($file['tmp_name'], $uploadDir . $fileName)) {
                setFlash('error', 'خطا در آپلود فایل.', 'error');
                redirect('portal/insurance');
            }
        }

        try {
            db()->insert('member_insurance', [
                'member_id' => $member['id'],
                'insurance_type' => $insuranceType,
                'policy_number' => $policyNumber,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'document_path' => $documentPath,
                'status' => 'pending_approval',
            ]);

            logActivity('create', 'member_insurance', null, 'آپلود بیمه‌نامه جدید: ' . $policyNumber);
            setFlash('success', 'بیمه‌نامه با موفقیت آپلود شد و پس از تأیید مدیر فعال خواهد شد.');
        } catch (Exception $e) {
            setFlash('error', 'خطا در ثبت بیمه‌نامه.', 'error');
        }

        redirect('portal/insurance');
    }

    public function notifications()
    {
        $userId = auth()->id();

        db()->query(
            "UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0",
            [$userId]
        );

        $notifications = db()->getAll(
            "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 50",
            [$userId]
        );

        render('portal/notifications', ['notifications' => $notifications], 'portal');
    }
}