<?php
class PaymentsController
{
    public function index()
    {
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $statusFilter = $_GET['status'] ?? '';
        $search = trim($_GET['search'] ?? '');
        $dateFrom = $_GET['date_from'] ?? '';
        $dateTo = $_GET['date_to'] ?? '';

        $sql = "SELECT p.*, m.first_name, m.last_name, mp.name as plan_name 
                FROM payments p 
                LEFT JOIN members m ON p.member_id = m.id 
                LEFT JOIN member_memberships mm ON p.membership_id = mm.id 
                LEFT JOIN membership_plans mp ON mm.plan_id = mp.id 
                WHERE p.deleted_at IS NULL";
        $countSql = "SELECT COUNT(*) as total FROM payments p WHERE p.deleted_at IS NULL";
        $params = [];

        if (!empty($statusFilter)) {
            $sql .= " AND p.status = ?";
            $countSql .= " AND p.status = ?";
            $params[] = $statusFilter;
        }

        if (!empty($search)) {
            $sql .= " AND (m.first_name LIKE ? OR m.last_name LIKE ? OR p.reference_number LIKE ?)";
            $countSql .= " AND (m.first_name LIKE ? OR m.last_name LIKE ? OR p.reference_number LIKE ?)";
            $searchLike = '%' . $search . '%';
            $params[] = $searchLike;
            $params[] = $searchLike;
            $params[] = $searchLike;
        }

        if (!empty($dateFrom)) {
            $dateFromGregorian = jalaliToGregorian($dateFrom);
            if ($dateFromGregorian) {
                $sql .= " AND p.payment_date >= ?";
                $countSql .= " AND p.payment_date >= ?";
                $params[] = $dateFromGregorian . ' 00:00:00';
            }
        }

        if (!empty($dateTo)) {
            $dateToGregorian = jalaliToGregorian($dateTo);
            if ($dateToGregorian) {
                $sql .= " AND p.payment_date <= ?";
                $countSql .= " AND p.payment_date <= ?";
                $params[] = $dateToGregorian . ' 23:59:59';
            }
        }

        $countParams = $params;
        $totalRow = db()->getOne($countSql, $countParams);
        $total = (int) $totalRow['total'];

        $sql .= " ORDER BY p.payment_date DESC LIMIT {$perPage} OFFSET {$offset}";
        $payments = db()->getAll($sql, $params);

        $paginationHtml = pagination($page, $total, $perPage, 'admin/payments');

        $totalPaid = 0;
        $paidRow = db()->getOne(
            "SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE status = 'paid' AND deleted_at IS NULL"
        );
        if ($paidRow) {
            $totalPaid = (float) $paidRow['total'];
        }

        render('payments/index', [
            'payments' => $payments,
            'pagination' => $paginationHtml,
            'page' => $page,
            'total' => $total,
            'perPage' => $perPage,
            'totalPaid' => $totalPaid,
        ]);
    }

    public function create()
    {
        $members = db()->select('members', ['deleted_at' => null], 'first_name ASC');
        $memberships = db()->getAll(
            "SELECT mm.*, mp.name as plan_name, m.first_name, m.last_name 
             FROM member_memberships mm 
             JOIN membership_plans mp ON mm.plan_id = mp.id 
             LEFT JOIN members m ON mm.member_id = m.id 
             WHERE mm.status = 'active' AND mm.deleted_at IS NULL 
             ORDER BY mp.name ASC"
        );

        render('payments/form', [
            'pageTitle' => 'ثبت پرداخت جدید',
            'members' => $members,
            'memberships' => $memberships,
        ], 'main');
    }

    public function store()
    {
        $memberId = $_POST['member_id'] ?? '';
        $membershipId = $_POST['membership_id'] ?? '';
        $amount = (float) ($_POST['amount'] ?? 0);
        $paymentMethod = trim($_POST['payment_method'] ?? '');
        $paymentDate = $_POST['payment_date'] ?? '';
        $status = trim($_POST['status'] ?? 'pending');
        $description = trim($_POST['description'] ?? '');
        $referenceNumber = trim($_POST['reference_number'] ?? '');

        if (empty($memberId) || $amount <= 0) {
            setFlash('error', 'انتخاب عضو و مبلغ الزامی است.', 'error');
            redirect('admin/payments/create');
        }

        $paymentDateGregorian = jalaliToGregorian($paymentDate);

        try {
            db()->insert('payments', [
                'member_id' => (int) $memberId,
                'membership_id' => $membershipId !== '' ? (int) $membershipId : null,
                'amount' => $amount,
                'payment_method' => $paymentMethod ?: null,
                'payment_date' => $paymentDateGregorian ?: date('Y-m-d H:i:s'),
                'status' => $status,
                'description' => $description ?: null,
                'reference_number' => $referenceNumber ?: null,
            ]);

            logActivity('create', 'payments', null, 'ثبت پرداخت: ' . formatCurrency($amount));
            setFlash('success', 'پرداخت با موفقیت ثبت شد.');
        } catch (Exception $e) {
            setFlash('error', 'خطا در ثبت پرداخت.', 'error');
        }

        redirect('admin/payments');
    }

    public function edit($id)
    {
        $payment = db()->selectOne('payments', ['id' => $id, 'deleted_at' => null]);
        if (!$payment) {
            setFlash('error', 'پرداخت مورد نظر یافت نشد.', 'error');
            redirect('admin/payments');
        }

        $members = db()->select('members', ['deleted_at' => null], 'first_name ASC');
        $memberships = db()->getAll(
            "SELECT mm.*, mp.name as plan_name, m.first_name, m.last_name 
             FROM member_memberships mm 
             JOIN membership_plans mp ON mm.plan_id = mp.id 
             LEFT JOIN members m ON mm.member_id = m.id 
             WHERE mm.status = 'active' AND mm.deleted_at IS NULL 
             ORDER BY mp.name ASC"
        );

        render('payments/form', [
            'pageTitle' => 'ویرایش پرداخت',
            'item' => $payment,
            'members' => $members,
            'memberships' => $memberships,
            'isEdit' => true,
        ], 'main');
    }

    public function update($id)
    {
        $payment = db()->selectOne('payments', ['id' => $id, 'deleted_at' => null]);
        if (!$payment) {
            setFlash('error', 'پرداخت مورد نظر یافت نشد.', 'error');
            redirect('admin/payments');
        }

        $memberId = $_POST['member_id'] ?? '';
        $membershipId = $_POST['membership_id'] ?? '';
        $amount = (float) ($_POST['amount'] ?? 0);
        $paymentMethod = trim($_POST['payment_method'] ?? '');
        $paymentDate = $_POST['payment_date'] ?? '';
        $status = trim($_POST['status'] ?? 'pending');
        $description = trim($_POST['description'] ?? '');
        $referenceNumber = trim($_POST['reference_number'] ?? '');

        if (empty($memberId) || $amount <= 0) {
            setFlash('error', 'انتخاب عضو و مبلغ الزامی است.', 'error');
            redirect('admin/payments/' . $id . '/edit');
        }

        $paymentDateGregorian = jalaliToGregorian($paymentDate);

        try {
            db()->updateById('payments', $id, [
                'member_id' => (int) $memberId,
                'membership_id' => $membershipId !== '' ? (int) $membershipId : null,
                'amount' => $amount,
                'payment_method' => $paymentMethod ?: null,
                'payment_date' => $paymentDateGregorian ?: $payment['payment_date'],
                'status' => $status,
                'description' => $description ?: null,
                'reference_number' => $referenceNumber ?: null,
            ]);

            logActivity('update', 'payments', $id, 'ویرایش پرداخت: ' . formatCurrency($amount));
            setFlash('success', 'پرداخت با موفقیت بروزرسانی شد.');
        } catch (Exception $e) {
            setFlash('error', 'خطا در بروزرسانی پرداخت.', 'error');
        }

        redirect('admin/payments');
    }

    public function delete($id)
    {
        $payment = db()->selectOne('payments', ['id' => $id, 'deleted_at' => null]);
        if (!$payment) {
            setFlash('error', 'پرداخت مورد نظر یافت نشد.', 'error');
            redirect('admin/payments');
        }

        try {
            softDelete('payments', $id);
            logActivity('delete', 'payments', $id, 'حذف پرداخت #' . $id);
            setFlash('success', 'پرداخت با موفقیت حذف شد.');
        } catch (Exception $e) {
            setFlash('error', 'خطا در حذف پرداخت.', 'error');
        }

        redirect('admin/payments');
    }
}