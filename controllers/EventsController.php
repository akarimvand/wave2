<?php
class EventsController
{
    public function index()
    {
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $search = trim($_GET['search'] ?? '');
        $statusFilter = $_GET['status'] ?? '';

        $sql = "SELECT * FROM events WHERE deleted_at IS NULL";
        $countSql = "SELECT COUNT(*) as total FROM events WHERE deleted_at IS NULL";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND title LIKE ?";
            $countSql .= " AND title LIKE ?";
            $params[] = '%' . $search . '%';
        }

        if (!empty($statusFilter)) {
            $sql .= " AND is_active = ?";
            $countSql .= " AND is_active = ?";
            $params[] = (int) $statusFilter;
        }

        $countParams = $params;
        $totalRow = db()->getOne($countSql, $countParams);
        $total = (int) $totalRow['total'];

        $sql .= " ORDER BY event_date DESC LIMIT {$perPage} OFFSET {$offset}";
        $events = db()->getAll($sql, $params);

        $paginationHtml = pagination($page, $total, $perPage, 'admin/events');

        render('events/index', [
            'events' => $events,
            'pagination' => $paginationHtml,
            'page' => $page,
            'total' => $total,
            'perPage' => $perPage,
        ]);
    }

    public function create()
    {
        render('events/form', [
            'pageTitle' => 'ایجاد رویداد جدید',
        ], 'main');
    }

    public function store()
    {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $eventDate = $_POST['event_date'] ?? '';
        $eventTime = $_POST['event_time'] ?? '';
        $location = trim($_POST['location'] ?? '');
        $maxParticipants = (int) ($_POST['max_participants'] ?? 0);
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if (empty($title) || empty($eventDate)) {
            setFlash('error', 'عنوان و تاریخ رویداد الزامی است.', 'error');
            redirect('admin/events/create');
        }

        $eventDateGregorian = jalaliToGregorian($eventDate);

        try {
            db()->insert('events', [
                'title' => $title,
                'description' => $description ?: null,
                'event_date' => $eventDateGregorian,
                'event_time' => $eventTime ?: null,
                'location' => $location ?: null,
                'max_participants' => $maxParticipants,
                'is_active' => $isActive,
            ]);

            logActivity('create', 'events', null, 'ایجاد رویداد: ' . $title);
            setFlash('success', 'رویداد با موفقیت ایجاد شد.');
        } catch (Exception $e) {
            setFlash('error', 'خطا در ایجاد رویداد.', 'error');
        }

        redirect('admin/events');
    }

    public function edit($id)
    {
        $event = db()->selectOne('events', ['id' => $id, 'deleted_at' => null]);
        if (!$event) {
            setFlash('error', 'رویداد مورد نظر یافت نشد.', 'error');
            redirect('admin/events');
        }

        render('events/form', [
            'pageTitle' => 'ویرایش رویداد',
            'item' => $event,
            'isEdit' => true,
        ], 'main');
    }

    public function update($id)
    {
        $event = db()->selectOne('events', ['id' => $id, 'deleted_at' => null]);
        if (!$event) {
            setFlash('error', 'رویداد مورد نظر یافت نشد.', 'error');
            redirect('admin/events');
        }

        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $eventDate = $_POST['event_date'] ?? '';
        $eventTime = $_POST['event_time'] ?? '';
        $location = trim($_POST['location'] ?? '');
        $maxParticipants = (int) ($_POST['max_participants'] ?? 0);
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if (empty($title) || empty($eventDate)) {
            setFlash('error', 'عنوان و تاریخ رویداد الزامی است.', 'error');
            redirect('admin/events/' . $id . '/edit');
        }

        $eventDateGregorian = jalaliToGregorian($eventDate);

        try {
            db()->updateById('events', $id, [
                'title' => $title,
                'description' => $description ?: null,
                'event_date' => $eventDateGregorian,
                'event_time' => $eventTime ?: null,
                'location' => $location ?: null,
                'max_participants' => $maxParticipants,
                'is_active' => $isActive,
            ]);

            logActivity('update', 'events', $id, 'ویرایش رویداد: ' . $title);
            setFlash('success', 'رویداد با موفقیت بروزرسانی شد.');
        } catch (Exception $e) {
            setFlash('error', 'خطا در بروزرسانی رویداد.', 'error');
        }

        redirect('admin/events');
    }

    public function delete($id)
    {
        $event = db()->selectOne('events', ['id' => $id, 'deleted_at' => null]);
        if (!$event) {
            setFlash('error', 'رویداد مورد نظر یافت نشد.', 'error');
            redirect('admin/events');
        }

        try {
            softDelete('events', $id);
            logActivity('delete', 'events', $id, 'حذف رویداد: ' . $event['title']);
            setFlash('success', 'رویداد با موفقیت حذف شد.');
        } catch (Exception $e) {
            setFlash('error', 'خطا در حذف رویداد.', 'error');
        }

        redirect('admin/events');
    }
}