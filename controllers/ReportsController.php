<?php
class ReportsController
{
    public function index()
    {
        $reportType = $_GET['report_type'] ?? 'financial';
        $fromDate = $_GET['from_date'] ?? '';
        $toDate = $_GET['to_date'] ?? '';

        $reportData = [];

        // Total revenue
        $row = db()->getOne("SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE status = 'paid' AND deleted_at IS NULL");
        $reportData['totalRevenue'] = (float) $row['total'];

        // This month revenue
        $firstOfMonth = date('Y-m-01');
        $lastOfMonth = date('Y-m-t');
        $row = db()->getOne(
            "SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE status = 'paid' AND deleted_at IS NULL AND payment_date >= ? AND payment_date <= ?",
            [$firstOfMonth . ' 00:00:00', $lastOfMonth . ' 23:59:59']
        );
        $reportData['monthlyRevenue'] = (float) $row['total'];

        // Last month revenue
        $lastMonthFirst = date('Y-m-01', strtotime('-1 month'));
        $lastMonthLast = date('Y-m-t', strtotime('-1 month'));
        $row = db()->getOne(
            "SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE status = 'paid' AND deleted_at IS NULL AND payment_date >= ? AND payment_date <= ?",
            [$lastMonthFirst . ' 00:00:00', $lastMonthLast . ' 23:59:59']
        );
        $reportData['lastMonthRevenue'] = (float) $row['total'];

        // Pending payments
        $row = db()->getOne("SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE status = 'pending' AND deleted_at IS NULL");
        $reportData['pendingPayments'] = (float) $row['total'];

        // Refunded
        $row = db()->getOne("SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE status = 'refunded' AND deleted_at IS NULL");
        $reportData['refundedTotal'] = (float) $row['total'];

        // Membership stats
        $reportData['totalMembers'] = db()->count('members', ['deleted_at' => null]);
        $reportData['activeMembers'] = db()->count('members', ['status' => 'active', 'deleted_at' => null]);
        $reportData['inactiveMembers'] = db()->count('members', ['status' => 'inactive', 'deleted_at' => null]);
        $reportData['expiredMembers'] = db()->count('members', ['status' => 'expired', 'deleted_at' => null]);
        $reportData['suspendedMembers'] = db()->count('members', ['status' => 'suspended', 'deleted_at' => null]);
        $reportData['pendingApprovals'] = db()->count('members', ['approval_status' => 'pending', 'deleted_at' => null]);

        // Active memberships by plan
        $reportData['membershipsByPlan'] = db()->getAll(
            "SELECT mp.name, COUNT(mm.id) as count, SUM(mm.price_paid) as revenue 
             FROM member_memberships mm 
             JOIN membership_plans mp ON mm.plan_id = mp.id 
             WHERE mm.status = 'active' AND mm.deleted_at IS NULL 
             GROUP BY mp.id, mp.name 
             ORDER BY count DESC"
        );

        // Monthly revenue trend (last 12 months) - with date filter
        $monthlySql = "SELECT DATE_FORMAT(payment_date, '%Y-%m') as month, SUM(amount) as total, COUNT(*) as count 
                       FROM payments 
                       WHERE status = 'paid' AND deleted_at IS NULL";
        $monthlyParams = [];

        if (!empty($fromDate)) {
            $fromGregorian = jalaliToGregorian($fromDate);
            if ($fromGregorian) {
                $monthlySql .= " AND payment_date >= ?";
                $monthlyParams[] = $fromGregorian . ' 00:00:00';
            }
        }

        if (!empty($toDate)) {
            $toGregorian = jalaliToGregorian($toDate);
            if ($toGregorian) {
                $monthlySql .= " AND payment_date <= ?";
                $monthlyParams[] = $toGregorian . ' 23:59:59';
            }
        }

        if (empty($fromDate) && empty($toDate)) {
            $monthlySql .= " AND payment_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)";
        }

        $monthlySql .= " GROUP BY DATE_FORMAT(payment_date, '%Y-%m') ORDER BY month ASC";

        $reportData['revenueTrend'] = db()->getAll($monthlySql, $monthlyParams);

        // Payments by method
        $reportData['paymentsByMethod'] = db()->getAll(
            "SELECT payment_method, COUNT(*) as count, SUM(amount) as total 
             FROM payments 
             WHERE status = 'paid' AND deleted_at IS NULL 
             GROUP BY payment_method"
        );

        // Top paying members
        $reportData['topMembers'] = db()->getAll(
            "SELECT m.first_name, m.last_name, SUM(p.amount) as total_paid 
             FROM payments p 
             JOIN members m ON p.member_id = m.id 
             WHERE p.status = 'paid' AND p.deleted_at IS NULL 
             GROUP BY p.member_id 
             ORDER BY total_paid DESC 
             LIMIT 10"
        );

        // Member membership reports
        if ($reportType === 'membership') {
            $reportData['membershipStats'] = db()->getAll(
                "SELECT mp.name as plan_name, 
                        COUNT(mm.id) as total,
                        SUM(CASE WHEN mm.status = 'active' THEN 1 ELSE 0 END) as active_count,
                        SUM(CASE WHEN mm.status = 'expired' THEN 1 ELSE 0 END) as expired_count,
                        SUM(mm.price_paid) as total_revenue
                 FROM member_memberships mm 
                 JOIN membership_plans mp ON mm.plan_id = mp.id 
                 WHERE mm.deleted_at IS NULL 
                 GROUP BY mp.id, mp.name 
                 ORDER BY total DESC"
            );
        }

        $data = [
            'stats' => [
                'total_revenue' => $reportData['totalRevenue'],
                'month_revenue' => $reportData['monthlyRevenue'],
                'today_revenue' => 0,
                'new_members_month' => 0,
                'active_memberships' => $reportData['membershipsByPlan'] ? array_sum(array_column($reportData['membershipsByPlan'], 'count')) : 0,
                'expired_memberships' => 0,
                'pending_payments' => $reportData['pendingPayments'],
                'refunded_total' => $reportData['refundedTotal'],
                'total_members' => $reportData['totalMembers'],
                'active_members' => $reportData['activeMembers'],
            ],
            'monthlyData' => $reportData['revenueTrend'],
            'reportType' => $reportType,
            'membershipsByPlan' => $reportData['membershipsByPlan'],
            'paymentsByMethod' => $reportData['paymentsByMethod'],
            'topMembers' => $reportData['topMembers'],
            'membershipStats' => $reportData['membershipStats'] ?? null,
        ];

        render('reports/index', $data);
    }
}