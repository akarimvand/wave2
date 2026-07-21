<?php
class CoachesController
{
    public function index()
    {
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $search = trim($_GET['search'] ?? '');
        $statusFilter = $_GET['status'] ?? '';

        $sql = "SELECT c.*, u.username as user_username, u.is_active as user_active FROM coaches c LEFT JOIN users u ON c.user_id = u.id WHERE c.deleted_at IS NULL";
        $countSql = "SELECT COUNT(*) as total FROM coaches WHERE deleted_at IS NULL";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (c.first_name LIKE ? OR c.last_name LIKE ? OR c.specialty LIKE ?)";
            $countSql .= " AND (first_name LIKE ? OR last_name LIKE ? OR specialty LIKE ?)";
            $searchLike = '%' . $search . '%';
            $params[] = $searchLike;
            $params[] = $searchLike;
            $params[] = $searchLike;
        }

        if ($statusFilter === '1' || $statusFilter === 'active') {
            $sql .= " AND c.is_active = 1";
            $countSql .= " AND is_active = 1";
        } elseif ($statusFilter === '0' || $statusFilter === 'inactive') {
            $sql .= " AND c.is_active = 0";
            $countSql .= " AND is_active = 0";
        }

        $countParams = $params;
        $totalRow = db()->getOne($countSql, $countParams);
        $total = (int) $totalRow['total'];

        $sql .= " ORDER BY id DESC LIMIT {$perPage} OFFSET {$offset}";
        $coaches = db()->getAll($sql, $params);

        $paginationHtml = pagination($page, $total, $perPage, 'admin/coaches');

        render('coaches/index', [
            'coaches' => $coaches,
            'pagination' => $paginationHtml,
            'page' => $page,
            'total' => $total,
            'perPage' => $perPage,
        ]);
    }

    public function create()
    {
        render('coaches/form', [
            'pageTitle' => 'ثبت مربی جدید',
        ], 'main');
    }

    public function store()
    {
        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $specialty = trim($_POST['specialty'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $hireDate = $_POST['hire_date'] ?? '';
        $salary = (float) ($_POST['salary'] ?? 0);
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        $bio = trim($_POST['bio'] ?? '');

        if (empty($firstName) || empty($lastName)) {
            setFlash('error', 'نام و نام خانوادگی مربی الزامی است.', 'error');
            redirect('admin/coaches/create');
        }

        $hireDateGregorian = jalaliToGregorian($hireDate);

        try {
            // Optionally create a user account for the coach
            $createUserAccount = !empty($_POST['create_user_account']);
            $coachUserId = null;

            if ($createUserAccount) {
                $username = trim($_POST['coach_username'] ?? '');
                $password = $_POST['coach_password'] ?? '123456';
                if (!empty($username)) {
                    $existingUser = db()->selectOne('users', ['username' => $username, 'deleted_at' => null]);
                    if (!$existingUser) {
                        $coachUserId = db()->insert('users', [
                            'username' => $username,
                            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                            'full_name' => $firstName . ' ' . $lastName,
                            'phone' => $phone ?: null,
                            'email' => $email ?: null,
                            'is_active' => 1,
                        ]);
                        // Assign coach role
                        $coachRole = db()->selectOne('roles', ['name' => 'coach', 'deleted_at' => null]);
                        if ($coachRole && $coachUserId) {
                            db()->insert('user_roles', ['user_id' => $coachUserId, 'role_id' => $coachRole['id']]);
                        }
                    }
                }
            }

            db()->insert('coaches', [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'specialty' => $specialty ?: null,
                'phone' => $phone ?: null,
                'email' => $email ?: null,
                'hire_date' => $hireDateGregorian,
                'salary' => $salary,
                'is_active' => $isActive,
                'bio' => $bio ?: null,
                'user_id' => $coachUserId ?: null,
            ]);

            logActivity('create', 'coaches', null, 'ثبت مربی جدید: ' . $firstName . ' ' . $lastName);
            setFlash('success', 'مربی با موفقیت ثبت شد.');
        } catch (Exception $e) {
            setFlash('error', 'خطا در ثبت مربی.', 'error');
        }

        redirect('admin/coaches');
    }

    public function edit($id)
    {
        $coach = db()->selectOne('coaches', ['id' => $id, 'deleted_at' => null]);
        if (!$coach) {
            setFlash('error', 'مربی مورد نظر یافت نشد.', 'error');
            redirect('admin/coaches');
        }

        render('coaches/form', [
            'pageTitle' => 'ویرایش مربی',
            'item' => $coach,
            'isEdit' => true,
        ], 'main');
    }

    public function update($id)
    {
        $coach = db()->selectOne('coaches', ['id' => $id, 'deleted_at' => null]);
        if (!$coach) {
            setFlash('error', 'مربی مورد نظر یافت نشد.', 'error');
            redirect('admin/coaches');
        }

        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $specialty = trim($_POST['specialty'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $hireDate = $_POST['hire_date'] ?? '';
        $salary = (float) ($_POST['salary'] ?? 0);
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        $bio = trim($_POST['bio'] ?? '');

        if (empty($firstName) || empty($lastName)) {
            setFlash('error', 'نام و نام خانوادگی مربی الزامی است.', 'error');
            redirect('admin/coaches/' . $id . '/edit');
        }

        $hireDateGregorian = jalaliToGregorian($hireDate);

        try {
            db()->updateById('coaches', $id, [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'specialty' => $specialty ?: null,
                'phone' => $phone ?: null,
                'email' => $email ?: null,
                'hire_date' => $hireDateGregorian,
                'salary' => $salary,
                'is_active' => $isActive,
                'bio' => $bio ?: null,
            ]);

            logActivity('update', 'coaches', $id, 'ویرایش مربی: ' . $firstName . ' ' . $lastName);
            setFlash('success', 'اطلاعات مربی با موفقیت بروزرسانی شد.');
        } catch (Exception $e) {
            setFlash('error', 'خطا در بروزرسانی مربی.', 'error');
        }

        redirect('admin/coaches');
    }

    public function delete($id)
    {
        $coach = db()->selectOne('coaches', ['id' => $id, 'deleted_at' => null]);
        if (!$coach) {
            setFlash('error', 'مربی مورد نظر یافت نشد.', 'error');
            redirect('admin/coaches');
        }

        try {
            db()->softDelete('coaches', $id);
            logActivity('delete', 'coaches', $id, 'حذف مربی: ' . $coach['first_name'] . ' ' . $coach['last_name']);
            setFlash('success', 'مربی با موفقیت حذف شد.');
        } catch (Exception $e) {
            setFlash('error', 'خطا در حذف مربی.', 'error');
        }

        redirect('admin/coaches');
    }
}