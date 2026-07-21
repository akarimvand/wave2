<?php
class CoachController
{
    /**
     * Find the coach record linked to the currently logged-in user
     */
    private function getCoach()
    {
        $userId = auth()->id();
        return db()->getOne(
            "SELECT c.* FROM coaches c
             WHERE c.user_id = ? AND c.is_active = 1 AND c.deleted_at IS NULL
             LIMIT 1",
            [$userId]
        );
    }

    /**
     * Get today's day name in Persian
     */
    private function getTodayDayName()
    {
        $days = ['یکشنبه', 'دوشنبه', 'سه‌شنبه', 'چهارشنبه', 'پنجشنبه', 'جمعه', 'شنبه'];
        $englishDays = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $today = date('l');
        $index = array_search($today, $englishDays);
        return $index !== false ? $days[$index] : '';
    }

    // ==================== Dashboard ====================
    public function dashboard()
    {
        $coach = $this->getCoach();
        if (!$coach) {
            setFlash('error', 'اطلاعات مربی یافت نشد. با مدیریت تماس بگیرید.', 'error');
            auth()->logout();
            redirect('auth/login');
        }

        $data['coach'] = $coach;
        $todayName = $this->getTodayDayName();
        $data['todayName'] = $todayName;

        // My classes
        $data['myClasses'] = db()->getAll(
            "SELECT c.*,
                    (SELECT COUNT(*) FROM class_registrations cr
                     WHERE cr.class_id = c.id AND cr.status = 'active' AND cr.deleted_at IS NULL) as student_count
             FROM classes c
             WHERE c.coach_id = ? AND c.is_active = 1 AND c.deleted_at IS NULL
             ORDER BY c.schedule_day ASC, c.schedule_time ASC",
            [$coach['id']]
        );

        // Today's classes
        $data['todayClasses'] = db()->getAll(
            "SELECT c.*,
                    (SELECT COUNT(*) FROM class_registrations cr
                     WHERE cr.class_id = c.id AND cr.status = 'active' AND cr.deleted_at IS NULL) as student_count
             FROM classes c
             WHERE c.coach_id = ? AND c.is_active = 1 AND c.deleted_at IS NULL
               AND (c.schedule_day LIKE ? OR c.schedule_days LIKE ?)
             ORDER BY c.schedule_time ASC",
            [$coach['id'], "%$todayName%", "%$todayName%"]
        );

        // Total students (unique)
        $data['totalStudents'] = db()->getOne(
            "SELECT COUNT(DISTINCT cr.member_id) as cnt
             FROM class_registrations cr
             JOIN classes c ON cr.class_id = c.id
             WHERE c.coach_id = ? AND cr.status = 'active' AND cr.deleted_at IS NULL AND c.deleted_at IS NULL",
            [$coach['id']]
        )['cnt'] ?? 0;

        // Today's attendance count
        $data['todayAttendance'] = db()->getOne(
            "SELECT COUNT(*) as cnt FROM class_attendance
             WHERE coach_id = ? AND attendance_date = CURDATE()",
            [$coach['id']]
        )['cnt'] ?? 0;

        // Unread notifications
        $data['unreadNotifications'] = db()->count('notifications', [
            'user_id' => auth()->id(),
            'is_read' => 0,
        ]);

        // Recent notifications
        $data['recentNotifications'] = db()->getAll(
            "SELECT * FROM notifications WHERE user_id = ?
             ORDER BY created_at DESC LIMIT 5",
            [auth()->id()]
        );

        render('coach/dashboard', $data, 'coach');
    }

    // ==================== My Classes ====================
    public function classes()
    {
        $coach = $this->getCoach();

        $myClasses = db()->getAll(
            "SELECT c.*,
                    (SELECT COUNT(*) FROM class_registrations cr
                     WHERE cr.class_id = c.id AND cr.status = 'active' AND cr.deleted_at IS NULL) as student_count
             FROM classes c
             WHERE c.coach_id = ? AND c.is_active = 1 AND c.deleted_at IS NULL
             ORDER BY c.schedule_day ASC, c.schedule_time ASC",
            [$coach['id']]
        );

        render('coach/classes', ['classes' => $myClasses, 'coach' => $coach], 'coach');
    }

    /**
     * View students of a specific class (AJAX or full page)
     */
    public function classStudents($classId)
    {
        $coach = $this->getCoach();

        // Verify this class belongs to this coach
        $class = db()->selectOne('classes', [
            'id' => $classId,
            'coach_id' => $coach['id'],
            'is_active' => 1,
            'deleted_at' => null,
        ]);

        if (!$class) {
            setFlash('error', 'کلاس مورد نظر یافت نشد.', 'error');
            redirect('coach/classes');
        }

        $students = db()->getAll(
            "SELECT cr.id as registration_id, cr.status as reg_status, cr.registration_date,
                    m.id as member_id, m.first_name, m.last_name, m.phone, m.national_code,
                    mi.status as insurance_status,
                    mm.status as membership_status, mm.start_date as membership_start, mm.end_date as membership_end,
                    mp.name as plan_name
             FROM class_registrations cr
             JOIN members m ON cr.member_id = m.id
             LEFT JOIN member_insurance mi ON mi.member_id = m.id AND mi.status = 'active'
                 AND mi.end_date >= CURDATE() AND mi.deleted_at IS NULL
             LEFT JOIN member_memberships mm ON mm.member_id = m.id AND mm.status = 'active'
                 AND mm.end_date >= CURDATE() AND mm.start_date <= CURDATE() AND mm.deleted_at IS NULL
             LEFT JOIN membership_plans mp ON mm.plan_id = mp.id
             WHERE cr.class_id = ? AND cr.status = 'active' AND cr.deleted_at IS NULL
             ORDER BY m.first_name ASC, m.last_name ASC",
            [$classId]
        );

        render('coach/class-students', [
            'class' => $class,
            'students' => $students,
            'coach' => $coach,
        ], 'coach');
    }

    // ==================== Attendance ====================
    public function attendance()
    {
        $coach = $this->getCoach();
        $todayName = $this->getTodayDayName();
        $selectedDate = $_GET['date'] ?? date('Y-m-d');

        // Get today's classes for this coach
        $classes = db()->getAll(
            "SELECT c.*,
                    (SELECT COUNT(*) FROM class_registrations cr
                     WHERE cr.class_id = c.id AND cr.status = 'active' AND cr.deleted_at IS NULL) as student_count
             FROM classes c
             WHERE c.coach_id = ? AND c.is_active = 1 AND c.deleted_at IS NULL
             ORDER BY c.schedule_day ASC, c.schedule_time ASC",
            [$coach['id']]
        );

        // Get attendance data for selected date
        $attendanceRecords = [];
        if (!empty($classes)) {
            $classIds = array_column($classes, 'id');
            $placeholders = implode(',', array_fill(0, count($classIds), '?'));
            $attendanceRecords = db()->getAll(
                "SELECT ca.*, m.first_name, m.last_name, c.name as class_name
                 FROM class_attendance ca
                 JOIN members m ON ca.member_id = m.id
                 JOIN classes c ON ca.class_id = c.id
                 WHERE ca.coach_id = ? AND ca.attendance_date = ?
                   AND ca.class_id IN ($placeholders)
                 ORDER BY c.schedule_time ASC, m.first_name ASC",
                array_merge([$coach['id'], $selectedDate], $classIds)
            );
        }

        render('coach/attendance', [
            'classes' => $classes,
            'coach' => $coach,
            'selectedDate' => $selectedDate,
            'todayName' => $todayName,
            'attendanceRecords' => $attendanceRecords,
        ], 'coach');
    }

    /**
     * Show attendance form for a specific class and date
     */
    public function attendanceForm()
    {
        $coach = $this->getCoach();
        $classId = (int) ($_GET['class_id'] ?? 0);
        $date = $_GET['date'] ?? date('Y-m-d');

        $class = db()->selectOne('classes', [
            'id' => $classId,
            'coach_id' => $coach['id'],
            'is_active' => 1,
            'deleted_at' => null,
        ]);

        if (!$class) {
            setFlash('error', 'کلاس مورد نظر یافت نشد.', 'error');
            redirect('coach/attendance');
        }

        // Get active registrations for this class with subscription check
        $students = db()->getAll(
            "SELECT cr.id as registration_id, m.id as member_id,
                    m.first_name, m.last_name, m.phone,
                    ca.status as attendance_status, ca.id as attendance_id, ca.notes as attendance_notes,
                    mm.status as membership_status, mm.start_date as membership_start,
                    mm.end_date as membership_end, mp.name as plan_name,
                    mi.status as insurance_status
             FROM class_registrations cr
             JOIN members m ON cr.member_id = m.id
             LEFT JOIN class_attendance ca ON ca.registration_id = cr.id
                 AND ca.class_id = cr.class_id AND ca.member_id = m.id
                 AND ca.coach_id = ? AND ca.attendance_date = ?
             LEFT JOIN member_memberships mm ON mm.member_id = m.id AND mm.status = 'active'
                 AND mm.end_date >= ? AND mm.start_date <= ? AND mm.deleted_at IS NULL
             LEFT JOIN membership_plans mp ON mm.plan_id = mp.id
             LEFT JOIN member_insurance mi ON mi.member_id = m.id AND mi.status = 'active'
                 AND mi.end_date >= ? AND mi.deleted_at IS NULL
             WHERE cr.class_id = ? AND cr.status = 'active' AND cr.deleted_at IS NULL
             ORDER BY m.first_name ASC",
            [$coach['id'], $date, $date, $date, $date, $classId]
        );

        render('coach/attendance-form', [
            'class' => $class,
            'students' => $students,
            'coach' => $coach,
            'selectedDate' => $date,
        ], 'coach');
    }

    /**
     * Save attendance for a class session
     */
    public function saveAttendance()
    {
        $coach = $this->getCoach();
        $classId = (int) ($_POST['class_id'] ?? 0);
        $date = $_POST['date'] ?? date('Y-m-d');
        $statuses = $_POST['status'] ?? [];
        $notes = $_POST['notes'] ?? [];
        $attendanceIds = $_POST['attendance_id'] ?? [];

        $class = db()->selectOne('classes', [
            'id' => $classId,
            'coach_id' => $coach['id'],
            'is_active' => 1,
            'deleted_at' => null,
        ]);

        if (!$class) {
            setFlash('error', 'کلاس مورد نظر یافت نشد.', 'error');
            redirect('coach/attendance');
        }

        try {
            db()->beginTransaction();

            foreach ($statuses as $regId => $status) {
                $regId = (int) $regId;
                $existingId = !empty($attendanceIds[$regId]) ? (int) $attendanceIds[$regId] : null;

                // Get member_id from registration
                $reg = db()->selectOne('class_registrations', ['id' => $regId]);
                if (!$reg) continue;

                $note = trim($notes[$regId] ?? '');

                if ($existingId) {
                    // Update existing
                    db()->updateById('class_attendance', $existingId, [
                        'status' => $status,
                        'notes' => $note ?: null,
                    ]);
                } else {
                    // Insert new
                    db()->insert('class_attendance', [
                        'class_id' => $classId,
                        'registration_id' => $regId,
                        'member_id' => $reg['member_id'],
                        'coach_id' => $coach['id'],
                        'attendance_date' => $date,
                        'status' => $status,
                        'notes' => $note ?: null,
                    ]);
                }
            }

            db()->commit();
            logActivity('attendance', 'classes', $classId, 'ثبت حضور و غیاب کلاس ' . $class['name']);
            setFlash('success', 'حضور و غیاب با موفقیت ثبت شد.');
        } catch (Exception $e) {
            db()->rollback();
            setFlash('error', 'خطا در ثبت حضور و غیاب.', 'error');
        }

        redirect('coach/attendance?date=' . urlencode($date));
    }

    // ==================== Profile ====================
    public function profile()
    {
        $coach = $this->getCoach();
        render('coach/profile', ['coach' => $coach], 'coach');
    }

    public function profileUpdate()
    {
        $coach = $this->getCoach();

        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $bio = trim($_POST['bio'] ?? '');

        try {
            db()->updateById('coaches', $coach['id'], [
                'phone' => $phone ?: null,
                'email' => $email ?: null,
                'bio' => $bio ?: null,
            ]);

            logActivity('update', 'coaches', $coach['id'], 'بروزرسانی پروفایل مربی');
            setFlash('success', 'پروفایل با موفقیت بروزرسانی شد.');
        } catch (Exception $e) {
            setFlash('error', 'خطا در بروزرسانی پروفایل.', 'error');
        }

        redirect('coach/profile');
    }

    /**
     * Change password for coach
     */
    public function changePassword()
    {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($currentPassword) || empty($newPassword)) {
            setFlash('error', 'تمامی فیلدها الزامی است.', 'error');
            redirect('coach/profile');
        }

        if ($newPassword !== $confirmPassword) {
            setFlash('error', 'رمز عبور جدید و تکرار آن مطابقت ندارند.', 'error');
            redirect('coach/profile');
        }

        if (strlen($newPassword) < 6) {
            setFlash('error', 'رمز عبور باید حداقل ۶ کاراکتر باشد.', 'error');
            redirect('coach/profile');
        }

        // Verify current password
        $user = db()->selectOne('users', ['id' => auth()->id(), 'deleted_at' => null]);
        if (!$user || !password_verify($currentPassword, $user['password_hash'])) {
            setFlash('error', 'رمز عبور فعلی اشتباه است.', 'error');
            redirect('coach/profile');
        }

        try {
            db()->updateById('users', auth()->id(), [
                'password_hash' => password_hash($newPassword, PASSWORD_DEFAULT),
            ]);
            setFlash('success', 'رمز عبور با موفقیت تغییر کرد.');
        } catch (Exception $e) {
            setFlash('error', 'خطا در تغییر رمز عبور.', 'error');
        }

        redirect('coach/profile');
    }

    // ==================== Notifications ====================
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

        render('coach/notifications', ['notifications' => $notifications], 'coach');
    }
}