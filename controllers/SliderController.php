<?php
class SliderController
{
    /**
     * Show slider management page (admin only)
     */
    public function index()
    {
        $sliders = db()->getAll(
            "SELECT s.*, u.full_name as creator_name
             FROM sliders s
             LEFT JOIN users u ON s.created_by = u.id
             WHERE s.deleted_at IS NULL
             ORDER BY s.sort_order ASC, s.created_at DESC"
        );

        render('sliders/index', [
            'sliders' => $sliders,
            'activeMenu' => 'settings',
        ], 'main');
    }

    /**
     * Create new slider form
     */
    public function create()
    {
        render('sliders/form', [
            'pageTitle' => 'افزودن اسلاید جدید',
            'slider' => null,
            'isEdit' => false,
            'activeMenu' => 'settings',
        ], 'main');
    }

    /**
     * Store new slider
     */
    public function store()
    {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $linkUrl = trim($_POST['link_url'] ?? '');
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        $sortOrder = (int) ($_POST['sort_order'] ?? 0);

        if (empty($title)) {
            setFlash('error', 'عنوان اسلاید الزامی است.', 'error');
            flashOldInput();
            redirect('admin/sliders/create');
        }

        // Handle image upload
        $imagePath = null;
        if (!empty($_FILES['image_file']['name'])) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
            $maxSize = 5 * 1024 * 1024; // 5MB

            if (!in_array($_FILES['image_file']['type'], $allowedTypes)) {
                setFlash('error', 'فرمت تصویر مجاز نیست. فقط JPG, PNG, GIF, WebP, SVG مجاز است.', 'error');
                flashOldInput();
                redirect('admin/sliders/create');
            }

            if ($_FILES['image_file']['size'] > $maxSize) {
                setFlash('error', 'حجم تصویر نباید بیشتر از ۵ مگابایت باشد.', 'error');
                flashOldInput();
                redirect('admin/sliders/create');
            }

            $uploadDir = __DIR__ . '/../public/uploads/sliders/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $extension = pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION);
            $filename = 'slider_' . time() . '_' . uniqid() . '.' . $extension;
            $targetPath = $uploadDir . $filename;

            if (move_uploaded_file($_FILES['image_file']['tmp_name'], $targetPath)) {
                $imagePath = '/public/uploads/sliders/' . $filename;
            } else {
                setFlash('error', 'خطا در بارگذاری تصویر.', 'error');
                flashOldInput();
                redirect('admin/sliders/create');
            }
        }

        try {
            db()->insert('sliders', [
                'title' => $title,
                'description' => $description ?: null,
                'image_path' => $imagePath ?: '/public/uploads/sliders/default.svg',
                'link_url' => $linkUrl ?: null,
                'is_active' => $isActive,
                'sort_order' => $sortOrder,
                'created_by' => auth()->id(),
            ]);

            logActivity('create', 'sliders', null, 'افزودن اسلاید جدید: ' . $title);
            setFlash('success', 'اسلاید با موفقیت افزوده شد.');
        } catch (Exception $e) {
            setFlash('error', 'خطا در افزودن اسلاید.', 'error');
        }

        redirect('admin/sliders');
    }

    /**
     * Edit slider form
     */
    public function edit($id)
    {
        $slider = db()->selectOne('sliders', ['id' => $id, 'deleted_at' => null]);
        if (!$slider) {
            setFlash('error', 'اسلاید مورد نظر یافت نشد.', 'error');
            redirect('admin/sliders');
        }

        render('sliders/form', [
            'pageTitle' => 'ویرایش اسلاید',
            'slider' => $slider,
            'isEdit' => true,
            'activeMenu' => 'settings',
        ], 'main');
    }

    /**
     * Update slider
     */
    public function update($id)
    {
        $slider = db()->selectOne('sliders', ['id' => $id, 'deleted_at' => null]);
        if (!$slider) {
            setFlash('error', 'اسلاید مورد نظر یافت نشد.', 'error');
            redirect('admin/sliders');
        }

        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $linkUrl = trim($_POST['link_url'] ?? '');
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        $sortOrder = (int) ($_POST['sort_order'] ?? 0);
        $removeImage = isset($_POST['remove_image']);

        if (empty($title)) {
            setFlash('error', 'عنوان اسلاید الزامی است.', 'error');
            flashOldInput();
            redirect('admin/sliders/' . $id . '/edit');
        }

        $data = [
            'title' => $title,
            'description' => $description ?: null,
            'link_url' => $linkUrl ?: null,
            'is_active' => $isActive,
            'sort_order' => $sortOrder,
        ];

        // Handle image upload
        if (!empty($_FILES['image_file']['name'])) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];

            if (!in_array($_FILES['image_file']['type'], $allowedTypes)) {
                setFlash('error', 'فرمت تصویر مجاز نیست. فقط JPG, PNG, GIF, WebP, SVG مجاز است.', 'error');
                flashOldInput();
                redirect('admin/sliders/' . $id . '/edit');
            }

            if ($_FILES['image_file']['size'] > $maxSize) {
                setFlash('error', 'حجم تصویر نباید بیشتر از ۵ مگابایت باشد.', 'error');
                flashOldInput();
                redirect('admin/sliders/' . $id . '/edit');
            }

            $uploadDir = __DIR__ . '/../public/uploads/sliders/';
            $extension = pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION);
            $filename = 'slider_' . time() . '_' . uniqid() . '.' . $extension;
            $targetPath = $uploadDir . $filename;

            if (move_uploaded_file($_FILES['image_file']['tmp_name'], $targetPath)) {
                // Delete old image
                if (!empty($slider['image_path']) && file_exists(__DIR__ . '/../' . $slider['image_path']) && basename($slider['image_path']) !== 'default.svg') {
                    unlink(__DIR__ . '/../' . $slider['image_path']);
                }
                $data['image_path'] = '/public/uploads/sliders/' . $filename;
            }
        } elseif ($removeImage) {
            // Remove existing image
            if (!empty($slider['image_path']) && file_exists(__DIR__ . '/../' . $slider['image_path']) && basename($slider['image_path']) !== 'default.svg') {
                unlink(__DIR__ . '/../' . $slider['image_path']);
            }
            $data['image_path'] = '/public/uploads/sliders/default.svg';
        }

        try {
            db()->updateById('sliders', $id, $data);
            logActivity('update', 'sliders', $id, 'ویرایش اسلاید: ' . $title);
            setFlash('success', 'اسلاید با موفقیت بروزرسانی شد.');
        } catch (Exception $e) {
            setFlash('error', 'خطا در بروزرسانی اسلاید.', 'error');
        }

        redirect('admin/sliders');
    }

    /**
     * Delete slider
     */
    public function delete($id)
    {
        $slider = db()->selectOne('sliders', ['id' => $id, 'deleted_at' => null]);
        if (!$slider) {
            setFlash('error', 'اسلاید مورد نظر یافت نشد.', 'error');
            redirect('admin/sliders');
        }

        try {
            // Delete image file
            if (!empty($slider['image_path']) && file_exists(__DIR__ . '/../' . $slider['image_path'])) {
                unlink(__DIR__ . '/../' . $slider['image_path']);
            }

            db()->softDelete('sliders', $id);
            logActivity('delete', 'sliders', $id, 'حذف اسلاید: ' . $slider['title']);
            setFlash('success', 'اسلاید با موفقیت حذف شد.');
        } catch (Exception $e) {
            setFlash('error', 'خطا در حذف اسلاید.', 'error');
        }

        redirect('admin/sliders');
    }

    /**
     * Get active sliders for AJAX (used in members page)
     */
    public function getActiveSliders()
    {
        header('Content-Type: application/json; charset=utf-8');

        $sliders = db()->getAll(
            "SELECT * FROM sliders
             WHERE is_active = 1 AND deleted_at IS NULL
             ORDER BY sort_order ASC, created_at DESC"
        );

        echo json_encode(['sliders' => $sliders]);
        exit;
    }

    /**
     * Toggle slider active status
     */
    public function toggleStatus($id)
    {
        $slider = db()->selectOne('sliders', ['id' => $id, 'deleted_at' => null]);
        if (!$slider) {
            echo json_encode(['success' => false, 'message' => 'اسلاید یافت نشد.']);
            exit;
        }

        $newStatus = $slider['is_active'] ? 0 : 1;

        try {
            db()->updateById('sliders', $id, ['is_active' => $newStatus]);
            logActivity('toggle', 'sliders', $id, 'تغییر وضعیت اسلاید به ' . ($newStatus ? 'فعال' : 'غیرفعال'));
            echo json_encode(['success' => true, 'is_active' => $newStatus]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'خطا در تغییر وضعیت.']);
        }
        exit;
    }
}
