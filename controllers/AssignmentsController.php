<?php
class AssignmentsController
{
    /**
     * Show assignment management page
     */
    public function index()
    {
        $classes = db()->getAll(
            "SELECT c.*, co.first_name as coach_first_name, co.last_name as coach_last_name,
                    (SELECT COUNT(*) FROM class_registrations cr WHERE cr.class_id = c.id AND cr.status = 'active' AND cr.deleted_at IS NULL) as registered_count
             FROM classes c
             LEFT JOIN coaches co ON c.coach_id = co.id
             WHERE c.is_active = 1 AND c.deleted_at IS NULL
             ORDER BY c.schedule_day ASC, c.schedule_time ASC"
        );

        $coaches = db()->select('coaches', ['is_active' => 1, 'deleted_at' => null], 'first_name ASC');

        render('assignments/index', [
            'classes' => $classes,
            'coaches' => $coaches,
        ]);
    }

    /**
     * Get members with active subscriptions (for AJAX)
     */
    public function getActiveMembers()
    {
        header('Content-Type: application/json; charset=utf-8');

        $search = trim($_GET['search'] ?? '');
        $classId = (int) ($_GET['class_id'] ?? 0);

        // First, get currently registered members in this class
        $registeredMemberIds = [];
        if ($classId > 0) {
            $registered = db()->getAll(
                "SELECT member_id FROM class_registrations 
                 WHERE class_id = ? AND status = 'active' AND deleted_at IS NULL",
                [$classId]
            );
            foreach ($registered as $r) {
                $registeredMemberIds[] = $r['member_id'];
            }
        }

        $sql = "SELECT m.id, m.first_name, m.last_name, m.phone, m.national_code,
                       mp.name as plan_name, mm.end_date as membership_end
                FROM members m
                INNER JOIN member_memberships mm ON mm.member_id = m.id
                    AND mm.status = 'active' AND mm.end_date >= CURDATE() AND mm.deleted_at IS NULL
                INNER JOIN membership_plans mp ON mm.plan_id = mp.id
                WHERE m.deleted_at IS NULL AND m.status = 'active'
                  AND m.approval_status = 'approved'";

        $params = [];

        // Exclude members already registered in this class
        if (!empty($registeredMemberIds)) {
            $placeholders = implode(',', array_fill(0, count($registeredMemberIds), '?'));
            $sql .= " AND m.id NOT IN ($placeholders)";
            $params = array_merge($params, $registeredMemberIds);
        }

        if (!empty($search)) {
            $sql .= " AND (m.first_name LIKE ? OR m.last_name LIKE ? OR m.phone LIKE ? OR m.national_code LIKE ?)";
            $searchLike = '%' . $search . '%';
            $params = array_merge($params, [$searchLike, $searchLike, $searchLike, $searchLike]);
        }

        $sql .= " ORDER BY m.first_name ASC LIMIT 30";

        $members = db()->getAll($sql, $params);
        echo json_encode(['members' => $members]);
        exit;
    }

    /**
     * Assign member to a class
     */
    public function assignClass()
    {
        $memberId = (int) ($_POST['member_id'] ?? 0);
        $classId = (int) ($_POST['class_id'] ?? 0);

        if (empty($memberId) || empty($classId)) {
            echo json_encode(['success' => false, 'message' => 'اطلاعات ناقص است.']);
            exit;
        }

        // Verify member has active subscription
        $membership = db()->getOne(
            "SELECT mm.* FROM member_memberships mm
             WHERE mm.member_id = ? AND mm.status = 'active' AND mm.end_date >= CURDATE() AND mm.deleted_at IS NULL
             ORDER BY mm.created_at DESC LIMIT 1",
            [$memberId]
        );

        if (!$membership) {
            echo json_encode(['success' => false, 'message' => 'این عضو اشتراک فعالی ندارد.']);
            exit;
        }

        // Check if already registered
        $existing = db()->selectOne('class_registrations', [
            'member_id' => $memberId,
            'class_id' => $classId,
            'status' => 'active',
            'deleted_at' => null,
        ]);

        if ($existing) {
            echo json_encode(['success' => false, 'message' => 'این عضو قبلاً در این کلاس ثبت‌نام کرده است.']);
            exit;
        }

        // Check class capacity
        $class = db()->selectOne('classes', ['id' => $classId, 'is_active' => 1, 'deleted_at' => null]);
        if ($class) {
            $registeredCount = db()->count('class_registrations', [
                'class_id' => $classId,
                'status' => 'active',
                'deleted_at' => null,
            ]);
            $maxParticipants = (int) ($class['max_participants'] ?? 0);
            if ($maxParticipants > 0 && $registeredCount >= $maxParticipants) {
                echo json_encode(['success' => false, 'message' => 'ظرفیت کلاس تکمیل شده است.']);
                exit;
            }
        }

        // Check insurance
        $insurance = db()->getOne(
            "SELECT * FROM member_insurance
             WHERE member_id = ? AND status = 'active' AND end_date >= CURDATE() AND deleted_at IS NULL
             ORDER BY created_at DESC LIMIT 1",
            [$memberId]
        );

        try {
            db()->insert('class_registrations', [
                'member_id' => $memberId,
                'class_id' => $classId,
                'status' => 'active',
            ]);

            // Notify coach if they have a user account
            if ($class && !empty($class['coach_id'])) {
                $coachUser = db()->getOne(
                    "SELECT u.id FROM users u INNER JOIN coaches c ON c.user_id = u.id WHERE c.id = ?",
                    [$class['coach_id']]
                );
                if ($coachUser) {
                    $member = db()->selectOne('members', ['id' => $memberId]);
                    db()->insert('notifications', [
                        'user_id' => $coachUser['id'],
                        'title' => 'عضو جدید در کلاس',
                        'message' => sprintf('%s %s به کلاس "%s" اضافه شد.',
                            $member['first_name'] ?? '', $member['last_name'] ?? '', $class['name'] ?? ''),
                        'related_module' => 'classes',
                        'related_id' => $classId,
                        'is_read' => 0,
                    ]);
                }
            }

            logActivity('assign', 'classes', $classId, 'تخصیص عضو #' . $memberId . ' به کلاس');
            echo json_encode(['success' => true, 'message' => 'عضو با موفقیت به کلاس تخصیص داده شد.']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'خطا: ' . $e->getMessage()]);
        }
        exit;
    }

    /**
     * Remove member from a class
     */
    public function removeClass()
    {
        $memberId = (int) ($_POST['member_id'] ?? 0);
        $classId = (int) ($_POST['class_id'] ?? 0);

        if (empty($memberId) || empty($classId)) {
            echo json_encode(['success' => false, 'message' => 'اطلاعات ناقص است.']);
            exit;
        }

        try {
            $reg = db()->selectOne('class_registrations', [
                'member_id' => $memberId,
                'class_id' => $classId,
                'status' => 'active',
                'deleted_at' => null,
            ]);

            if ($reg) {
                db()->updateById('class_registrations', $reg['id'], ['status' => 'cancelled']);
                logActivity('unassign', 'classes', $classId, 'حذف عضو #' . $memberId . ' از کلاس');
                echo json_encode(['success' => true, 'message' => 'عضو از کلاس حذف شد.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'ثبت‌نام یافت نشد.']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'خطا: ' . $e->getMessage()]);
        }
        exit;
    }
}