<?php
class EquipmentController
{
    public function index()
    {
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $search = trim($_GET['search'] ?? '');

        $sql = "SELECT * FROM equipment WHERE deleted_at IS NULL";
        $countSql = "SELECT COUNT(*) as total FROM equipment WHERE deleted_at IS NULL";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (name LIKE ? OR description LIKE ?)";
            $countSql .= " AND (name LIKE ? OR description LIKE ?)";
            $searchLike = '%' . $search . '%';
            $params[] = $searchLike;
            $params[] = $searchLike;
        }

        $conditionFilter = $_GET['condition_status'] ?? '';
        if (!empty($conditionFilter)) {
            $sql .= " AND condition_status = ?";
            $countSql .= " AND condition_status = ?";
            $params[] = $conditionFilter;
        }

        $countParams = $params;
        $totalRow = db()->getOne($countSql, $countParams);
        $total = (int) $totalRow['total'];

        $sql .= " ORDER BY id DESC LIMIT {$perPage} OFFSET {$offset}";
        $equipment = db()->getAll($sql, $params);

        $paginationHtml = pagination($page, $total, $perPage, 'admin/equipment');

        render('equipment/index', [
            'equipment' => $equipment,
            'pagination' => $paginationHtml,
            'page' => $page,
            'total' => $total,
            'perPage' => $perPage,
        ]);
    }

    public function create()
    {
        render('equipment/form', [
            'pageTitle' => 'ثبت تجهیز جدید',
        ], 'main');
    }

    public function store()
    {
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $purchaseDate = $_POST['purchase_date'] ?? '';
        $purchasePrice = (float) ($_POST['purchase_price'] ?? 0);
        $conditionStatus = trim($_POST['condition_status'] ?? 'new');
        $lastMaintenance = $_POST['last_maintenance'] ?? '';
        $nextMaintenance = $_POST['next_maintenance'] ?? '';

        if (empty($name)) {
            setFlash('error', 'نام تجهیز الزامی است.', 'error');
            redirect('admin/equipment/create');
        }

        $purchaseDateGregorian = jalaliToGregorian($purchaseDate);
        $lastMaintGregorian = jalaliToGregorian($lastMaintenance);
        $nextMaintGregorian = jalaliToGregorian($nextMaintenance);

        try {
            db()->insert('equipment', [
                'name' => $name,
                'description' => $description ?: null,
                'purchase_date' => $purchaseDateGregorian,
                'purchase_price' => $purchasePrice,
                'condition_status' => $conditionStatus,
                'last_maintenance' => $lastMaintGregorian,
                'next_maintenance' => $nextMaintGregorian,
            ]);

            logActivity('create', 'equipment', null, 'ثبت تجهیز جدید: ' . $name);
            setFlash('success', 'تجهیز با موفقیت ثبت شد.');
        } catch (Exception $e) {
            setFlash('error', 'خطا در ثبت تجهیز.', 'error');
        }

        redirect('admin/equipment');
    }

    public function edit($id)
    {
        $item = db()->selectOne('equipment', ['id' => $id, 'deleted_at' => null]);
        if (!$item) {
            setFlash('error', 'تجهیز مورد نظر یافت نشد.', 'error');
            redirect('admin/equipment');
        }

        render('equipment/form', [
            'pageTitle' => 'ویرایش تجهیز',
            'item' => $item,
            'isEdit' => true,
        ], 'main');
    }

    public function update($id)
    {
        $item = db()->selectOne('equipment', ['id' => $id, 'deleted_at' => null]);
        if (!$item) {
            setFlash('error', 'تجهیز مورد نظر یافت نشد.', 'error');
            redirect('admin/equipment');
        }

        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $purchaseDate = $_POST['purchase_date'] ?? '';
        $purchasePrice = (float) ($_POST['purchase_price'] ?? 0);
        $conditionStatus = trim($_POST['condition_status'] ?? 'new');
        $lastMaintenance = $_POST['last_maintenance'] ?? '';
        $nextMaintenance = $_POST['next_maintenance'] ?? '';

        if (empty($name)) {
            setFlash('error', 'نام تجهیز الزامی است.', 'error');
            redirect('admin/equipment/' . $id . '/edit');
        }

        $purchaseDateGregorian = jalaliToGregorian($purchaseDate);
        $lastMaintGregorian = jalaliToGregorian($lastMaintenance);
        $nextMaintGregorian = jalaliToGregorian($nextMaintenance);

        try {
            db()->updateById('equipment', $id, [
                'name' => $name,
                'description' => $description ?: null,
                'purchase_date' => $purchaseDateGregorian,
                'purchase_price' => $purchasePrice,
                'condition_status' => $conditionStatus,
                'last_maintenance' => $lastMaintGregorian,
                'next_maintenance' => $nextMaintGregorian,
            ]);

            logActivity('update', 'equipment', $id, 'ویرایش تجهیز: ' . $name);
            setFlash('success', 'تجهیز با موفقیت بروزرسانی شد.');
        } catch (Exception $e) {
            setFlash('error', 'خطا در بروزرسانی تجهیز.', 'error');
        }

        redirect('admin/equipment');
    }

    public function delete($id)
    {
        $item = db()->selectOne('equipment', ['id' => $id, 'deleted_at' => null]);
        if (!$item) {
            setFlash('error', 'تجهیز مورد نظر یافت نشد.', 'error');
            redirect('admin/equipment');
        }

        try {
            softDelete('equipment', $id);
            logActivity('delete', 'equipment', $id, 'حذف تجهیز: ' . $item['name']);
            setFlash('success', 'تجهیز با موفقیت حذف شد.');
        } catch (Exception $e) {
            setFlash('error', 'خطا در حذف تجهیز.', 'error');
        }

        redirect('admin/equipment');
    }
}