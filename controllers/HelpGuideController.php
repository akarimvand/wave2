<?php
class HelpGuideController
{
    /**
     * Show help guide modal content for current page and role
     */
    public function getGuide()
    {
        header('Content-Type: application/json; charset=utf-8');

        $roleName = $_GET['role'] ?? 'admin';
        $pageKey = $_GET['page'] ?? 'dashboard';

        $guide = db()->getOne(
            "SELECT * FROM role_help_guides
             WHERE role_name = ? AND page_key = ? AND is_active = 1
             LIMIT 1",
            [$roleName, $pageKey]
        );

        if ($guide) {
            echo json_encode([
                'success' => true,
                'guide' => $guide
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'راهنمایی برای این صفحه یافت نشد.'
            ]);
        }
        exit;
    }

    /**
     * Admin management of help guides
     */
    public function index()
    {
        $guides = db()->getAll(
            "SELECT * FROM role_help_guides
             WHERE deleted_at IS NULL
             ORDER BY role_name, sort_order"
        );

        render('help-guides/index', [
            'guides' => $guides,
            'activeMenu' => 'settings',
        ], 'main');
    }

    /**
     * Create new guide form
     */
    public function create()
    {
        $roles = ['admin', 'coach', 'receptionist', 'accountant', 'member'];
        
        render('help-guides/form', [
            'pageTitle' => 'افزودن راهنمای جدید',
            'guide' => null,
            'roles' => $roles,
            'isEdit' => false,
            'activeMenu' => 'settings',
        ], 'main');
    }

    /**
     * Store new guide
     */
    public function store()
    {
        $roleName = trim($_POST['role_name'] ?? '');
        $pageKey = trim($_POST['page_key'] ?? '');
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $videoUrl = trim($_POST['video_url'] ?? '');
        $tips = $_POST['tips'] ?? [];
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        $sortOrder = (int) ($_POST['sort_order'] ?? 0);

        if (empty($roleName) || empty($pageKey) || empty($title)) {
            setFlash('error', 'فیلدهای نقش، کلید صفحه و عنوان الزامی هستند.', 'error');
            flashOldInput();
            redirect('admin/help-guides/create');
        }

        // Check for duplicate
        $existing = db()->selectOne('role_help_guides', [
            'role_name' => $roleName,
            'page_key' => $pageKey,
            'deleted_at' => null,
        ]);

        if ($existing) {
            setFlash('error', 'راهنما برای این نقش و صفحه از قبل وجود دارد.', 'error');
            flashOldInput();
            redirect('admin/help-guides/create');
        }

        try {
            db()->insert('role_help_guides', [
                'role_name' => $roleName,
                'page_key' => $pageKey,
                'title' => $title,
                'content' => $content,
                'video_url' => $videoUrl ?: null,
                'tips' => !empty($tips) ? json_encode($tips) : null,
                'is_active' => $isActive,
                'sort_order' => $sortOrder,
            ]);

            logActivity('create', 'help_guides', null, 'افزودن راهنما: ' . $title);
            setFlash('success', 'راهنما با موفقیت افزوده شد.');
        } catch (Exception $e) {
            setFlash('error', 'خطا در افزودن راهنما.', 'error');
        }

        redirect('admin/help-guides');
    }

    /**
     * Edit guide form
     */
    public function edit($id)
    {
        $guide = db()->selectOne('role_help_guides', ['id' => $id, 'deleted_at' => null]);
        if (!$guide) {
            setFlash('error', 'راهنما مورد نظر یافت نشد.', 'error');
            redirect('admin/help-guides');
        }

        $roles = ['admin', 'coach', 'receptionist', 'accountant', 'member'];
        $tipsArray = !empty($guide['tips']) ? json_decode($guide['tips'], true) : [];

        render('help-guides/form', [
            'pageTitle' => 'ویرایش راهنما',
            'guide' => $guide,
            'roles' => $roles,
            'tipsArray' => $tipsArray,
            'isEdit' => true,
            'activeMenu' => 'settings',
        ], 'main');
    }

    /**
     * Update guide
     */
    public function update($id)
    {
        $guide = db()->selectOne('role_help_guides', ['id' => $id, 'deleted_at' => null]);
        if (!$guide) {
            setFlash('error', 'راهنما مورد نظر یافت نشد.', 'error');
            redirect('admin/help-guides');
        }

        $roleName = trim($_POST['role_name'] ?? '');
        $pageKey = trim($_POST['page_key'] ?? '');
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $videoUrl = trim($_POST['video_url'] ?? '');
        $tips = $_POST['tips'] ?? [];
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        $sortOrder = (int) ($_POST['sort_order'] ?? 0);

        if (empty($roleName) || empty($pageKey) || empty($title)) {
            setFlash('error', 'فیلدهای نقش، کلید صفحه و عنوان الزامی هستند.', 'error');
            flashOldInput();
            redirect('admin/help-guides/' . $id . '/edit');
        }

        try {
            db()->updateById('role_help_guides', $id, [
                'role_name' => $roleName,
                'page_key' => $pageKey,
                'title' => $title,
                'content' => $content,
                'video_url' => $videoUrl ?: null,
                'tips' => !empty($tips) ? json_encode($tips) : null,
                'is_active' => $isActive,
                'sort_order' => $sortOrder,
            ]);

            logActivity('update', 'help_guides', $id, 'ویرایش راهنما: ' . $title);
            setFlash('success', 'راهنما با موفقیت بروزرسانی شد.');
        } catch (Exception $e) {
            setFlash('error', 'خطا در بروزرسانی راهنما.', 'error');
        }

        redirect('admin/help-guides');
    }

    /**
     * Delete guide
     */
    public function delete($id)
    {
        $guide = db()->selectOne('role_help_guides', ['id' => $id, 'deleted_at' => null]);
        if (!$guide) {
            setFlash('error', 'راهنما مورد نظر یافت نشد.', 'error');
            redirect('admin/help-guides');
        }

        try {
            db()->softDelete('role_help_guides', $id);
            logActivity('delete', 'help_guides', $id, 'حذف راهنما: ' . $guide['title']);
            setFlash('success', 'راهنما با موفقیت حذف شد.');
        } catch (Exception $e) {
            setFlash('error', 'خطا در حذف راهنما.', 'error');
        }

        redirect('admin/help-guides');
    }
}
