<?php
class SettingsController
{
    public function index()
    {
        $rows = db()->select('settings', ['deleted_at' => null]);
        $settings = [];
        foreach ($rows as $row) {
            $settings[$row['key']] = $row['value'];
        }

        render('settings/index', ['settings' => $settings]);
    }

    public function update()
    {
        $input = isset($_POST['settings']) && is_array($_POST['settings'])
            ? $_POST['settings']
            : $_POST;

        $fields = [
            'club_name', 'club_address', 'club_phone', 'club_email',
            'currency', 'timezone', 'date_format', 'logo_path',
            'membership_auto_expire', 'default_approval', 'sms_enabled',
            'working_hours', 'primary_color', 'secondary_color',
            'about_club', 'tax_rate', 'items_per_page', 'session_timeout',
        ];

        // Handle logo upload
        if (isset($_FILES['logo_file']) && $_FILES['logo_file']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['logo_file'];
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
            $maxSize = 2 * 1024 * 1024; // 2MB

            if (in_array($file['type'], $allowedTypes) && $file['size'] <= $maxSize) {
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $newName = 'logo_' . time() . '.' . $ext;
                $uploadDir = BASE_PATH . '/public/uploads/settings/';

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                if (move_uploaded_file($file['tmp_name'], $uploadDir . $newName)) {
                    $input['logo_path'] = 'uploads/settings/' . $newName;
                }
            }
        }

        foreach ($fields as $key) {
            $value = isset($input[$key]) ? trim($input[$key]) : '';
            $existing = db()->selectOne('settings', ['key' => $key, 'deleted_at' => null]);
            if ($existing) {
                db()->update('settings', ['value' => $value, 'updated_by' => auth()->id()], ['id' => $existing['id']]);
            } else {
                db()->insert('settings', [
                    'key' => $key,
                    'value' => $value,
                    'updated_by' => auth()->id(),
                ]);
            }
        }

        logActivity('update', 'settings', null, 'بروزرسانی تنظیمات سیستم');
        setFlash('success', 'تنظیمات با موفقیت ذخیره شد.');
        redirect('admin/settings');
    }
}