<?php
class ActivityLogsController
{
    public function index()
    {
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $perPage = 30;
        $offset = ($page - 1) * $perPage;

        $moduleFilter = $_GET['module'] ?? '';
        $userFilter = $_GET['user_id'] ?? '';

        $where = ['deleted_at' => null];
        $params = [];

        $sql = "SELECT al.*, u.full_name as user_name 
                FROM activity_logs al 
                LEFT JOIN users u ON al.user_id = u.id 
                WHERE al.deleted_at IS NULL";

        $countSql = "SELECT COUNT(*) as total FROM activity_logs al WHERE al.deleted_at IS NULL";

        if (!empty($moduleFilter)) {
            $sql .= " AND al.module = ?";
            $countSql .= " AND al.module = ?";
            $params[] = $moduleFilter;
        }

        if (!empty($userFilter)) {
            $sql .= " AND al.user_id = ?";
            $countSql .= " AND al.user_id = ?";
            $params[] = (int) $userFilter;
        }

        $countRow = db()->getOne($countSql, $params);
        $total = (int) $countRow['total'];

        $sql .= " ORDER BY al.created_at DESC LIMIT {$perPage} OFFSET {$offset}";
        $logs = db()->getAll($sql, $params);

        $paginationHtml = pagination($page, $total, $perPage, 'admin/activity-logs');

        // Get distinct modules for filter
        $modules = db()->getAll(
            "SELECT DISTINCT module FROM activity_logs WHERE deleted_at IS NULL ORDER BY module ASC"
        );
        $modules = array_column($modules, 'module');

        render('activity-logs/index', [
            'logs' => $logs,
            'paginationHtml' => $paginationHtml,
            'modules' => $modules,
        ]);
    }
}