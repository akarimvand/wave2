<?php
class NotificationsController
{
    public function index()
    {
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $filter = $_GET['filter'] ?? 'all';

        $sql = "SELECT n.*, u.full_name as user_name 
                FROM notifications n 
                LEFT JOIN users u ON n.user_id = u.id 
                WHERE n.deleted_at IS NULL";
        $countSql = "SELECT COUNT(*) as total FROM notifications n WHERE n.deleted_at IS NULL";
        $params = [];

        if ($filter === 'unread') {
            $sql .= " AND n.is_read = 0";
            $countSql .= " AND n.is_read = 0";
        }

        // Mark all as read
        db()->query(
            "UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0",
            [auth()->id()]
        );

        $countParams = $params;
        $totalRow = db()->getOne($countSql, $countParams);
        $total = (int) $totalRow['total'];

        $sql .= " ORDER BY n.created_at DESC LIMIT {$perPage} OFFSET {$offset}";
        $notifications = db()->getAll($sql, $params);

        $paginationHtml = pagination($page, $total, $perPage, 'admin/notifications');

        render('notifications/index', [
            'notifications' => $notifications,
            'pagination' => $paginationHtml,
            'page' => $page,
            'total' => $total,
            'perPage' => $perPage,
        ]);
    }

    public function create()
    {
        render('notifications/form', [
            'pageTitle' => 'ارسال اعلان جدید',
        ], 'main');
    }

    public function store()
    {
        $title = trim($_POST['title'] ?? '');
        $message = trim($_POST['message'] ?? '');
        $type = trim($_POST['type'] ?? 'info');
        $targetType = trim($_POST['target_type'] ?? 'all');

        if (empty($title) || empty($message)) {
            setFlash('error', 'عنوان و متن اعلان الزامی است.', 'error');
            redirect('admin/notifications/create');
        }

        try {
            db()->insert('notifications', [
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'related_module' => $targetType,
                'user_id' => auth()->id(),
                'is_read' => 0,
            ]);

            logActivity('create', 'notifications', null, 'ارسال اعلان: ' . $title);
            setFlash('success', 'اعلان با موفقیت ارسال شد.');
        } catch (Exception $e) {
            setFlash('error', 'خطا در ارسال اعلان.', 'error');
        }

        redirect('admin/notifications');
    }
}