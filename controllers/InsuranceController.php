<?php
class InsuranceController
{
    public function index()
    {
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $search = trim($_GET['search'] ?? '');

        $sql = "SELECT mi.*, m.first_name, m.last_name
                FROM member_insurance mi
                LEFT JOIN members m ON mi.member_id = m.id
                WHERE mi.deleted_at IS NULL";
        $countSql = "SELECT COUNT(*) as total
                     FROM member_insurance mi
                     LEFT JOIN members m ON mi.member_id = m.id
                     WHERE mi.deleted_at IS NULL";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (m.first_name LIKE ? OR m.last_name LIKE ? OR mi.policy_number LIKE ?)";
            $countSql .= " AND (m.first_name LIKE ? OR m.last_name LIKE ? OR mi.policy_number LIKE ?)";
            $searchLike = '%' . $search . '%';
            $params[] = $searchLike;
            $params[] = $searchLike;
            $params[] = $searchLike;
        }

        $countParams = $params;
        $totalRow = db()->getOne($countSql, $countParams);
        $total = (int) $totalRow['total'];

        $sql .= " ORDER BY mi.created_at DESC LIMIT {$perPage} OFFSET {$offset}";
        $memberInsurances = db()->getAll($sql, $params);

        $paginationHtml = pagination($page, $total, $perPage, 'admin/insurance');

        render('insurance/index', [
            'memberInsurances' => $memberInsurances,
            'pagination' => $paginationHtml,
            'page' => $page,
            'total' => $total,
            'perPage' => $perPage,
            'search' => $search,
            'activeMenu' => 'insurance',
        ]);
    }

    public function create()
    {
        $members = db()->select('members', ['deleted_at' => null], 'first_name ASC');

        render('insurance/form', [
            'pageTitle' => 'ثبت بیمه عضو',
            'members' => $members,
            'insurance' => null,
            'isEdit' => false,
            'activeMenu' => 'insurance',
        ], 'main');
    }

    public function store()
    {
        $memberId = $_POST['member_id'] ?? '';
        $policyNumber = trim($_POST['policy_number'] ?? '');
        $insuranceType = trim($_POST['insurance_type'] ?? '');
        $startDate = $_POST['start_date'] ?? '';
        $endDate = $_POST['end_date'] ?? '';
        $premiumAmount = (float) ($_POST['premium_amount'] ?? 0);
        $status = trim($_POST['status'] ?? 'active');
        $documentPath = trim($_POST['document_path'] ?? '');
        $notes = trim($_POST['notes'] ?? '');

        if (empty($memberId)) {
            setFlash('error', 'انتخاب عضو الزامی است.', 'error');
            flashOldInput();
            redirect('admin/insurance/create');
        }

        if (empty($startDate) || empty($endDate)) {
            setFlash('error', 'تاریخ شروع و پایان الزامی است.', 'error');
            flashOldInput();
            redirect('admin/insurance/create');
        }

        $startDateGregorian = jalaliToGregorian($startDate);
        $endDateGregorian = jalaliToGregorian($endDate);

        try {
            db()->insert('member_insurance', [
                'member_id' => (int) $memberId,
                'policy_number' => $policyNumber ?: null,
                'insurance_type' => $insuranceType ?: null,
                'start_date' => $startDateGregorian,
                'end_date' => $endDateGregorian,
                'premium_amount' => $premiumAmount,
                'status' => $status,
                'document_path' => $documentPath ?: null,
                'notes' => $notes ?: null,
            ]);

            logActivity('create', 'member_insurance', null, 'ثبت بیمه برای عضو #' . $memberId);
            setFlash('success', 'بیمه با موفقیت ثبت شد.');
        } catch (Exception $e) {
            setFlash('error', 'خطا در ثبت بیمه.', 'error');
        }

        redirect('admin/insurance');
    }

    public function edit($id)
    {
        $insurance = db()->selectOne('member_insurance', ['id' => $id, 'deleted_at' => null]);
        if (!$insurance) {
            setFlash('error', 'بیمه مورد نظر یافت نشد.', 'error');
            redirect('admin/insurance');
        }

        $members = db()->select('members', ['deleted_at' => null], 'first_name ASC');

        render('insurance/form', [
            'pageTitle' => 'ویرایش بیمه',
            'members' => $members,
            'insurance' => $insurance,
            'isEdit' => true,
            'activeMenu' => 'insurance',
        ], 'main');
    }

    public function update($id)
    {
        $insurance = db()->selectOne('member_insurance', ['id' => $id, 'deleted_at' => null]);
        if (!$insurance) {
            setFlash('error', 'بیمه مورد نظر یافت نشد.', 'error');
            redirect('admin/insurance');
        }

        $memberId = $_POST['member_id'] ?? '';
        $policyNumber = trim($_POST['policy_number'] ?? '');
        $insuranceType = trim($_POST['insurance_type'] ?? '');
        $startDate = $_POST['start_date'] ?? '';
        $endDate = $_POST['end_date'] ?? '';
        $premiumAmount = (float) ($_POST['premium_amount'] ?? 0);
        $status = trim($_POST['status'] ?? 'active');
        $documentPath = trim($_POST['document_path'] ?? '');
        $notes = trim($_POST['notes'] ?? '');

        if (empty($memberId)) {
            setFlash('error', 'انتخاب عضو الزامی است.', 'error');
            flashOldInput();
            redirect('admin/insurance/' . $id . '/edit');
        }

        if (empty($startDate) || empty($endDate)) {
            setFlash('error', 'تاریخ شروع و پایان الزامی است.', 'error');
            flashOldInput();
            redirect('admin/insurance/' . $id . '/edit');
        }

        $startDateGregorian = jalaliToGregorian($startDate);
        $endDateGregorian = jalaliToGregorian($endDate);

        try {
            $data = [
                'member_id' => (int) $memberId,
                'policy_number' => $policyNumber ?: null,
                'insurance_type' => $insuranceType ?: null,
                'start_date' => $startDateGregorian,
                'end_date' => $endDateGregorian,
                'premium_amount' => $premiumAmount,
                'status' => $status,
                'document_path' => $documentPath ?: null,
                'notes' => $notes ?: null,
            ];

            // If document_path was cleared, keep existing one unless explicitly removed
            if (empty($documentPath) && !empty($insurance['document_path']) && !isset($_POST['remove_document'])) {
                $data['document_path'] = $insurance['document_path'];
            }

            db()->updateById('member_insurance', $id, $data);

            logActivity('update', 'member_insurance', $id, 'ویرایش بیمه #' . $id);
            setFlash('success', 'بیمه با موفقیت بروزرسانی شد.');
        } catch (Exception $e) {
            setFlash('error', 'خطا در بروزرسانی بیمه.', 'error');
        }

        redirect('admin/insurance');
    }

    public function delete($id)
    {
        $insurance = db()->selectOne('member_insurance', ['id' => $id, 'deleted_at' => null]);
        if (!$insurance) {
            setFlash('error', 'بیمه مورد نظر یافت نشد.', 'error');
            redirect('admin/insurance');
        }

        try {
            db()->softDelete('member_insurance', $id);
            logActivity('delete', 'member_insurance', $id, 'حذف بیمه #' . $id);
            setFlash('success', 'بیمه با موفقیت حذف شد.');
        } catch (Exception $e) {
            setFlash('error', 'خطا در حذف بیمه.', 'error');
        }

        redirect('admin/insurance');
    }

    public static function checkMemberInsurance($memberId)
    {
        $row = db()->getOne(
            "SELECT * FROM member_insurance
             WHERE member_id = ? AND status = 'active' AND deleted_at IS NULL
               AND end_date >= CURDATE()
             ORDER BY end_date DESC
             LIMIT 1",
            [(int) $memberId]
        );

        if ($row) {
            return [
                'valid' => true,
                'message' => 'بیمه فعال وجود دارد.',
                'insurance' => $row,
            ];
        }

        return [
            'valid' => false,
            'message' => 'بیمه فعالی برای این عضو یافت نشد.',
            'insurance' => null,
        ];
    }
}