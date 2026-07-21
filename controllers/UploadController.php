<?php
class UploadController
{
    public function upload()
    {
        header('Content-Type: application/json; charset=utf-8');

        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'message' => 'فایل ارسال نشده است.']);
            return;
        }

        $file = $_FILES['file'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'application/pdf'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        if (!in_array($file['type'], $allowedTypes)) {
            echo json_encode(['success' => false, 'message' => 'نوع فایل مجاز نیست.']);
            return;
        }

        if ($file['size'] > $maxSize) {
            echo json_encode(['success' => false, 'message' => 'حجم فایل نباید بیشتر از ۵ مگابایت باشد.']);
            return;
        }

        $uploadDir = BASE_PATH . '/public/uploads/' . date('Y/m') . '/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = uniqid('file_', true) . '.' . $ext;
        $filePath = $uploadDir . $fileName;

        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            $relativePath = '/public/uploads/' . date('Y/m') . '/' . $fileName;
            echo json_encode([
                'success' => true,
                'message' => 'فایل با موفقیت آپلود شد.',
                'file_path' => $relativePath,
                'file_name' => $fileName,
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'خطا در ذخیره فایل.']);
        }
    }
}