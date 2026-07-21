<?php
class ClassesController
{
    public function index()
    {
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $search = trim($_GET['search'] ?? '');
        $scheduleDay = $_GET['schedule_day'] ?? '';
        $statusFilter = $_GET['status'] ?? '';

        $sql = "SELECT c.*, co.first_name as coach_first_name, co.last_name as coach_last_name
                FROM classes c
                LEFT JOIN coaches co ON c.coach_id = co.id
                WHERE c.deleted_at IS NULL";
        $countSql = "SELECT COUNT(*) as total
                     FROM classes c
                     WHERE c.deleted_at IS NULL";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND c.name LIKE ?";
            $countSql .= " AND c.name LIKE ?";
            $params[] = '%' . $search . '%';
        }

        if (!empty($scheduleDay)) {
            $sql .= " AND c.schedule_days LIKE ?";
            $countSql .= " AND c.schedule_days LIKE ?";
            $params[] = '%' . $scheduleDay . '%';
        }

        if ($statusFilter !== '') {
            $sql .= " AND c.is_active = ?";
            $countSql .= " AND c.is_active = ?";
            $params[] = (int) $statusFilter;
        }

        $countParams = $params;
        $totalRow = db()->getOne($countSql, $countParams);
        $total = (int) $totalRow['total'];

        $sql .= " ORDER BY c.id DESC LIMIT {$perPage} OFFSET {$offset}";
        $classes = db()->getAll($sql, $params);

        $paginationHtml = pagination($page, $total, $perPage, 'admin/classes');

        render('classes/index', [
            'activeMenu' => 'classes',
            'classes' => $classes,
            'pagination' => $paginationHtml,
            'search' => $search,
            'scheduleDay' => $scheduleDay,
            'page' => $page,
            'total' => $total,
            'perPage' => $perPage,
        ]);
    }

    public function create()
    {
        $coaches = db()->select('coaches', ['is_active' => 1, 'deleted_at' => null], 'first_name ASC');
        render('classes/form', [
            'activeMenu' => 'classes',
            'pageTitle' => 'ایجاد کلاس جدید',
            'coaches' => $coaches,
        ], 'main');
    }

    public function store()
    {
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $coachId = $_POST['coach_id'] ?? '';
        $scheduleDays = trim($_POST['schedule_days'] ?? '');
        $startTime = $_POST['start_time'] ?? '';
        $endTime = $_POST['end_time'] ?? '';
        $maxParticipants = (int) ($_POST['max_participants'] ?? 0);
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if (empty($name) || empty($scheduleDays)) {
            setFlash('error', 'نام کلاس و روزهای برگزاری الزامی است.', 'error');
            redirect('admin/classes/create');
        }

        try {
            db()->insert('classes', [
                'name' => $name,
                'description' => $description ?: null,
                'coach_id' => $coachId !== '' ? (int) $coachId : null,
                'schedule_days' => $scheduleDays,
                'start_time' => $startTime ?: null,
                'end_time' => $endTime ?: null,
                'max_participants' => $maxParticipants,
                'is_active' => $isActive,
            ]);

            logActivity('create', 'classes', null, 'ایجاد کلاس: ' . $name);
            setFlash('success', 'کلاس با موفقیت ایجاد شد.');
        } catch (Exception $e) {
            setFlash('error', 'خطا در ایجاد کلاس.', 'error');
        }

        redirect('admin/classes');
    }

    public function edit($id)
    {
        $class = db()->selectOne('classes', ['id' => $id, 'deleted_at' => null]);
        if (!$class) {
            setFlash('error', 'کلاس مورد نظر یافت نشد.', 'error');
            redirect('admin/classes');
        }

        $coaches = db()->select('coaches', ['is_active' => 1, 'deleted_at' => null], 'first_name ASC');

        render('classes/form', [
            'activeMenu' => 'classes',
            'pageTitle' => 'ویرایش کلاس',
            'class' => $class,
            'coaches' => $coaches,
            'isEdit' => true,
        ], 'main');
    }

    public function update($id)
    {
        $class = db()->selectOne('classes', ['id' => $id, 'deleted_at' => null]);
        if (!$class) {
            setFlash('error', 'کلاس مورد نظر یافت نشد.', 'error');
            redirect('admin/classes');
        }

        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $coachId = $_POST['coach_id'] ?? '';
        $scheduleDays = trim($_POST['schedule_days'] ?? '');
        $startTime = $_POST['start_time'] ?? '';
        $endTime = $_POST['end_time'] ?? '';
        $maxParticipants = (int) ($_POST['max_participants'] ?? 0);
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if (empty($name) || empty($scheduleDays)) {
            setFlash('error', 'نام کلاس و روزهای برگزاری الزامی است.', 'error');
            redirect('admin/classes/' . $id . '/edit');
        }

        try {
            db()->updateById('classes', $id, [
                'name' => $name,
                'description' => $description ?: null,
                'coach_id' => $coachId !== '' ? (int) $coachId : null,
                'schedule_days' => $scheduleDays,
                'start_time' => $startTime ?: null,
                'end_time' => $endTime ?: null,
                'max_participants' => $maxParticipants,
                'is_active' => $isActive,
            ]);

            logActivity('update', 'classes', $id, 'ویرایش کلاس: ' . $name);
            setFlash('success', 'کلاس با موفقیت بروزرسانی شد.');
        } catch (Exception $e) {
            setFlash('error', 'خطا در بروزرسانی کلاس.', 'error');
        }

        redirect('admin/classes');
    }

    public function delete($id)
    {
        $class = db()->selectOne('classes', ['id' => $id, 'deleted_at' => null]);
        if (!$class) {
            setFlash('error', 'کلاس مورد نظر یافت نشد.', 'error');
            redirect('admin/classes');
        }

        try {
            softDelete('classes', $id);
            logActivity('delete', 'classes', $id, 'حذف کلاس: ' . $class['name']);
            setFlash('success', 'کلاس با موفقیت حذف شد.');
        } catch (Exception $e) {
            setFlash('error', 'خطا در حذف کلاس.', 'error');
        }

        redirect('admin/classes');
    }
}