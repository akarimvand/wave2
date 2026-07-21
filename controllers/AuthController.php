<?php
class AuthController
{
    public function loginForm()
    {
        if (auth()->check()) {
            redirect('admin/dashboard');
        }
        render('auth/login', [], 'auth');
    }

    public function login()
    {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            setFlash('error', 'نام کاربری و رمز عبور الزامی است.', 'error');
            redirect('auth/login');
        }

        if (auth()->login($username, $password)) {
            // Store redirect target in session for loading screen
            if (auth()->hasRole('member')) {
                $_SESSION['login_redirect'] = 'portal/dashboard';
            } elseif (auth()->hasRole('coach')) {
                $_SESSION['login_redirect'] = 'coach/dashboard';
            } else {
                $_SESSION['login_redirect'] = 'admin/dashboard';
            }
            redirect('auth/loading');
        }

        setFlash('error', 'نام کاربری یا رمز عبور اشتباه است.', 'error');
        redirect('auth/login');
    }

    public function loading()
    {
        if (!auth()->check()) {
            redirect('auth/login');
        }
        $redirectUrl = $_SESSION['login_redirect'] ?? 'admin/dashboard';
        unset($_SESSION['login_redirect']);
        render('auth/loading', ['redirectUrl' => $redirectUrl], 'auth-loading');
    }

    public function logout()
    {
        auth()->logout();
        redirect('auth/login');
    }

    public function registerForm()
    {
        render('auth/register', [], 'auth');
    }

    public function register()
    {
        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $nationalCode = trim($_POST['national_code'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $birthDate = trim($_POST['birth_date'] ?? '');

        // Health fields
        $bloodType = trim($_POST['blood_type'] ?? '');
        $allergies = trim($_POST['allergies'] ?? '');
        $medications = trim($_POST['medications'] ?? '');
        $medicalHistory = trim($_POST['medical_history'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $emergencyContact = trim($_POST['emergency_contact'] ?? '');
        $emergencyPhone = trim($_POST['emergency_phone'] ?? '');

        // Insurance fields
        $insuranceType = trim($_POST['insurance_type'] ?? '');
        $policyNumber = trim($_POST['policy_number'] ?? '');
        $startDate = trim($_POST['start_date'] ?? '');
        $endDate = trim($_POST['end_date'] ?? '');

        // Username = national code, password = national code
        $username = $nationalCode;
        $password = $nationalCode;

        // Validation
        if (empty($firstName) || empty($lastName) || empty($nationalCode) || empty($phone)) {
            setFlash('error', 'نام، نام خانوادگی، کد ملی و شماره تلفن الزامی است.', 'error');
            flashOldInput();
            redirect('auth/register');
        }

        if (!preg_match('/^\d{10}$/', $nationalCode)) {
            setFlash('error', 'کد ملی باید دقیقاً ۱۰ رقم باشد.', 'error');
            flashOldInput();
            redirect('auth/register');
        }

        $existing = db()->selectOne('users', ['username' => $username, 'deleted_at' => null]);
        if ($existing) {
            setFlash('error', 'این کد ملی قبلاً ثبت‌نام شده است.', 'error');
            flashOldInput();
            redirect('auth/register');
        }

        // Handle file upload
        $documentPath = null;
        if (isset($_FILES['document']) && $_FILES['document']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['document'];
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'application/pdf'];
            $maxSize = 5 * 1024 * 1024; // 5MB

            if (!in_array($file['type'], $allowedTypes)) {
                setFlash('error', 'فرمت فایل مجاز نیست. فقط تصاویر و PDF قابل قبول است.', 'error');
                flashOldInput();
                redirect('auth/register');
            }

            if ($file['size'] > $maxSize) {
                setFlash('error', 'حجم فایل نباید بیشتر از ۵ مگابایت باشد.', 'error');
                flashOldInput();
                redirect('auth/register');
            }

            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $newName = 'ins_' . $nationalCode . '_' . time() . '.' . $ext;
            $uploadDir = BASE_PATH . '/public/uploads/insurance/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            if (move_uploaded_file($file['tmp_name'], $uploadDir . $newName)) {
                $documentPath = 'uploads/insurance/' . $newName;
            }
        }

        try {
            db()->beginTransaction();

            $fullName = $firstName . ($lastName ? ' ' . $lastName : '');

            // Convert jalali dates to gregorian
            $birthDateGreg = !empty($birthDate) ? jalaliToGregorian($birthDate) : null;
            $startDateGreg = !empty($startDate) ? jalaliToGregorian($startDate) : null;
            $endDateGreg = !empty($endDate) ? jalaliToGregorian($endDate) : null;

            // Create member (pending approval)
            $memberId = db()->insert('members', [
                'first_name'       => $firstName,
                'last_name'        => $lastName,
                'national_code'    => $nationalCode,
                'phone'            => $phone,
                'email'            => $email ?: null,
                'birth_date'       => $birthDateGreg,
                'address'          => $address ?: null,
                'emergency_contact' => $emergencyContact ?: null,
                'emergency_phone'  => $emergencyPhone ?: null,
                'blood_type'       => $bloodType ?: null,
                'allergies'        => $allergies ?: null,
                'medications'      => $medications ?: null,
                'medical_history'  => $medicalHistory ?: null,
                'status'           => 'pending',
                'approval_status'  => 'pending',
            ]);

            // Create user account (inactive until approved)
            $userId = db()->insert('users', [
                'username'      => $username,
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                'full_name'     => $fullName,
                'phone'         => $phone,
                'is_active'     => 0,
            ]);

            $memberRole = db()->selectOne('roles', ['name' => 'member', 'deleted_at' => null]);
            if ($memberRole) {
                db()->insert('user_roles', [
                    'user_id' => $userId,
                    'role_id' => $memberRole['id'],
                ]);
            }

            // Create insurance record if insurance info provided
            if (!empty($insuranceType) || !empty($policyNumber) || !empty($documentPath)) {
                db()->insert('member_insurance', [
                    'member_id'      => $memberId,
                    'insurance_type' => $insuranceType ?: null,
                    'policy_number'  => $policyNumber ?: null,
                    'document_path'  => $documentPath,
                    'start_date'     => $startDateGreg,
                    'end_date'       => $endDateGreg,
                    'premium_amount' => 0,
                    'status'         => 'pending_approval',
                ]);
            }

            db()->commit();

            setFlash('success', 'ثبت‌نام شما با موفقیت انجام شد. پس از تأیید مدیر، می‌توانید وارد سیستم شوید.');
            redirect('auth/login');
        } catch (Exception $e) {
            db()->rollback();
            setFlash('error', 'خطا در ثبت‌نام. لطفاً دوباره تلاش کنید.', 'error');
            redirect('auth/register');
        }
    }
}