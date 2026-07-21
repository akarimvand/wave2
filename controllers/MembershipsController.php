<?php
class MembershipsController
{
    public function index()
    {
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $search = trim($_GET['search'] ?? '');
        $statusFilter = $_GET['status'] ?? '';

        $where = ['deleted_at' => null];
        $params = [];
        $sql = "SELECT * FROM membership_plans WHERE deleted_at IS NULL";
        $countSql = "SELECT COUNT(*) as total FROM membership_plans WHERE deleted_at IS NULL";

        if (!empty($search)) {
            $sql .= " AND name LIKE ?";
            $countSql .= " AND name LIKE ?";
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

        $sql .= " ORDER BY id DESC LIMIT {$perPage} OFFSET {$offset}";
        $memberships = db()->getAll($sql, $params);

        // Attach member count for each plan
        foreach ($memberships as &$m) {
            $count = db()->getOne(
                "SELECT COUNT(*) as cnt FROM member_memberships WHERE plan_id = ? AND deleted_at IS NULL AND status = 'active'",
                [$m['id']]
            );
            $m['member_count'] = (int) ($count['cnt'] ?? 0);
        }
        unset($m);

        render('memberships/index', [
            'memberships' => $memberships,
            'page' => $page,
            'total' => $total,
            'perPage' => $perPage,
        ]);
    }

    public function create()
    {
        render('memberships/form', [
            'pageTitle' => 'ایجاد طرح عضویت جدید',
        ]);
    }

    public function store()
    {
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $durationDays = (int) ($_POST['duration_days'] ?? 0);
        $price = (float) ($_POST['price'] ?? 0);
        $maxClasses = $_POST['max_classes'] ?? '';
        $isActive = isset($_POST['is_active']) ? 1 : 1;

        if (empty($name) || $durationDays <= 0) {
            setFlash('error', 'نام طرح و مدت زمان الزامی است.', 'error');
            flashOldInput();
            redirect('admin/memberships/create');
        }

        try {
            db()->insert('membership_plans', [
                'name' => $name,
                'description' => $description ?: null,
                'duration_days' => $durationDays,
                'price' => $price,
                'max_classes' => $maxClasses !== '' ? (int) $maxClasses : null,
                'is_active' => $isActive,
            ]);

            logActivity('create', 'membership_plans', null, 'ایجاد طرح عضویت: ' . $name);
            setFlash('success', 'طرح عضویت با موفقیت ایجاد شد.');
        } catch (Exception $e) {
            setFlash('error', 'خطا در ایجاد طرح عضویت.', 'error');
        }

        redirect('admin/memberships');
    }

    public function edit($id)
    {
        $plan = db()->selectOne('membership_plans', ['id' => $id, 'deleted_at' => null]);
        if (!$plan) {
            setFlash('error', 'طرح عضویت مورد نظر یافت نشد.', 'error');
            redirect('admin/memberships');
        }

        render('memberships/form', [
            'pageTitle' => 'ویرایش طرح عضویت',
            'membership' => $plan,
        ]);
    }

    public function update($id)
    {
        $plan = db()->selectOne('membership_plans', ['id' => $id, 'deleted_at' => null]);
        if (!$plan) {
            setFlash('error', 'طرح عضویت مورد نظر یافت نشد.', 'error');
            redirect('admin/memberships');
        }

        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $durationDays = (int) ($_POST['duration_days'] ?? 0);
        $price = (float) ($_POST['price'] ?? 0);
        $maxClasses = $_POST['max_classes'] ?? '';
        $isActive = isset($_POST['is_active']) ? 1 : 1;

        if (empty($name) || $durationDays <= 0) {
            setFlash('error', 'نام طرح و مدت زمان الزامی است.', 'error');
            flashOldInput();
            redirect('admin/memberships/' . $id . '/edit');
        }

        try {
            db()->updateById('membership_plans', $id, [
                'name' => $name,
                'description' => $description ?: null,
                'duration_days' => $durationDays,
                'price' => $price,
                'max_classes' => $maxClasses !== '' ? (int) $maxClasses : null,
                'is_active' => $isActive,
            ]);

            logActivity('update', 'membership_plans', $id, 'ویرایش طرح عضویت: ' . $name);
            setFlash('success', 'طرح عضویت با موفقیت بروزرسانی شد.');
        } catch (Exception $e) {
            setFlash('error', 'خطا در بروزرسانی طرح عضویت.', 'error');
        }

        redirect('admin/memberships');
    }

    public function delete($id)
    {
        $plan = db()->selectOne('membership_plans', ['id' => $id, 'deleted_at' => null]);
        if (!$plan) {
            setFlash('error', 'طرح عضویت مورد نظر یافت نشد.', 'error');
            redirect('admin/memberships');
        }

        try {
            softDelete('membership_plans', $id);
            logActivity('delete', 'membership_plans', $id, 'حذف طرح عضویت: ' . $plan['name']);
            setFlash('success', 'طرح عضویت با موفقیت حذف شد.');
        } catch (Exception $e) {
            setFlash('error', 'خطا در حذف طرح عضویت.', 'error');
        }

        redirect('admin/memberships');
    }
}