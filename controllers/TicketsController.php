<?php
class TicketsController
{
    public function index()
    {
        $statusFilter = $_GET['status'] ?? '';

        $sql = "SELECT t.*, m.first_name, m.last_name, u.full_name as assigned_name 
                FROM tickets t 
                LEFT JOIN members m ON t.member_id = m.id 
                LEFT JOIN users u ON t.assigned_to = u.id 
                WHERE t.deleted_at IS NULL";

        $params = [];

        if (!empty($statusFilter)) {
            $sql .= " AND t.status = ?";
            $params[] = $statusFilter;
        }

        $sql .= " ORDER BY t.created_at DESC";

        $tickets = db()->getAll($sql, $params);

        render('tickets/index', ['tickets' => $tickets]);
    }

    public function show($id)
    {
        $ticket = db()->getOne(
            "SELECT t.*, m.first_name, m.last_name, u.full_name as assigned_name 
             FROM tickets t 
             LEFT JOIN members m ON t.member_id = m.id 
             LEFT JOIN users u ON t.assigned_to = u.id 
             WHERE t.id = ? AND t.deleted_at IS NULL",
            [$id]
        );

        if (!$ticket) {
            setFlash('error', 'تیکت مورد نظر یافت نشد.', 'error');
            redirect('admin/tickets');
        }

        $replies = db()->getAll(
            "SELECT tr.*, u.full_name as user_name 
             FROM ticket_replies tr 
             LEFT JOIN users u ON tr.user_id = u.id 
             WHERE tr.ticket_id = ? AND tr.deleted_at IS NULL 
             ORDER BY tr.created_at ASC",
            [$id]
        );

        // Mark ticket as in_progress if open
        if ($ticket['status'] === 'open') {
            db()->updateById('tickets', $id, [
                'status' => 'in_progress',
                'assigned_to' => auth()->id(),
            ]);
        }

        render('tickets/show', ['ticket' => $ticket, 'replies' => $replies]);
    }

    public function reply($id)
    {
        $ticket = db()->selectOne('tickets', ['id' => $id, 'deleted_at' => null]);
        if (!$ticket) {
            setFlash('error', 'تیکت مورد نظر یافت نشد.', 'error');
            redirect('admin/tickets');
        }

        $message = trim($_POST['message'] ?? '');
        if (empty($message)) {
            setFlash('error', 'متن پاسخ نمی‌تواند خالی باشد.', 'error');
            redirect('admin/tickets/' . $id);
        }

        try {
            db()->insert('ticket_replies', [
                'ticket_id' => $id,
                'user_id' => auth()->id(),
                'message' => $message,
                'is_admin' => 1,
            ]);

            logActivity('reply', 'tickets', $id, 'پاسخ به تیکت #' . $id);
            setFlash('success', 'پاسخ با موفقیت ثبت شد.');
        } catch (Exception $e) {
            setFlash('error', 'خطا در ثبت پاسخ.', 'error');
        }

        redirect('admin/tickets/' . $id);
    }
}