<?php
class DashboardController
{
    public function index()
    {
        $data = [];

        $data['totalMembers'] = db()->count('members', ['deleted_at' => null]);
        $data['activeMembers'] = db()->count('members', ['status' => 'active', 'deleted_at' => null]);
        $data['pendingMembers'] = db()->count('members', ['approval_status' => 'pending', 'deleted_at' => null]);

        $data['activeMemberships'] = db()->count('member_memberships', ['status' => 'active', 'deleted_at' => null]);

        $today = date('Y-m-d');
        $data['todayPayments'] = db()->count('payments', [
            'status' => 'paid',
            'deleted_at' => null,
        ]);
        $data['todayPaymentsTotal'] = 0;
        $todayPaymentsRows = db()->getAll(
            "SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE status = 'paid' AND deleted_at IS NULL AND DATE(payment_date) = ?",
            [$today]
        );
        if ($todayPaymentsRows) {
            $data['todayPaymentsTotal'] = (float) $todayPaymentsRows[0]['total'];
        }

        $data['upcomingEvents'] = db()->getAll(
            "SELECT * FROM events WHERE event_date >= ? AND deleted_at IS NULL ORDER BY event_date ASC LIMIT 5",
            [$today]
        );

        $data['openTickets'] = db()->count('tickets', [
            'status' => ['open', 'in_progress'],
            'deleted_at' => null,
        ]);

        $data['recentTickets'] = db()->getAll(
            "SELECT t.*, m.first_name, m.last_name FROM tickets t LEFT JOIN members m ON t.member_id = m.id WHERE t.deleted_at IS NULL ORDER BY t.created_at DESC LIMIT 5"
        );

        $data['totalCoaches'] = db()->count('coaches', ['is_active' => 1, 'deleted_at' => null]);
        $data['activeClasses'] = db()->count('classes', ['is_active' => 1, 'deleted_at' => null]);

        $data['monthlyRevenue'] = 0;
        $firstOfMonth = date('Y-m-01');
        $lastOfMonth = date('Y-m-t');
        $monthlyRows = db()->getAll(
            "SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE status = 'paid' AND deleted_at IS NULL AND payment_date >= ? AND payment_date <= ?",
            [$firstOfMonth . ' 00:00:00', $lastOfMonth . ' 23:59:59']
        );
        if ($monthlyRows) {
            $data['monthlyRevenue'] = (float) $monthlyRows[0]['total'];
        }

        // Upcoming classes with coach info
        $data['upcomingClasses'] = db()->getAll(
            "SELECT c.*, CONCAT(co.first_name, ' ', co.last_name) as coach_name,
                    (SELECT COUNT(*) FROM class_registrations cr WHERE cr.class_id = c.id AND cr.status = 'active' AND cr.deleted_at IS NULL) as registered_count
             FROM classes c 
             LEFT JOIN coaches co ON c.coach_id = co.id 
             WHERE c.is_active = 1 AND c.deleted_at IS NULL 
             ORDER BY c.schedule_day ASC, c.schedule_time ASC 
             LIMIT 10"
        );

        // Recent tickets with member info (already have recentTickets, add member info)
        $data['recentTickets'] = db()->getAll(
            "SELECT t.*, CONCAT(m.first_name, ' ', m.last_name) as member_name 
             FROM tickets t 
             LEFT JOIN members m ON t.member_id = m.id 
             WHERE t.deleted_at IS NULL 
             ORDER BY t.created_at DESC 
             LIMIT 5"
        );

        $data['stats'] = [
            'total_members' => $data['totalMembers'],
            'active_members' => $data['activeMembers'],
            'pending_members' => $data['pendingMembers'],
            'active_memberships' => $data['activeMemberships'],
            'today_revenue' => $data['todayPaymentsTotal'],
            'open_tickets' => $data['openTickets'],
            'total_coaches' => $data['totalCoaches'],
            'active_classes' => $data['activeClasses'],
            'monthly_revenue' => $data['monthlyRevenue'],
        ];

        render('dashboard/index', $data);
    }
}