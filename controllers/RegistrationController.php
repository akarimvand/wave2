<?php
class RegistrationController
{
    public function index()
    {
        include BASE_PATH . '/views/registration/index.php';
    }

    public function store()
    {
        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $nationalCode = trim($_POST['national_code'] ?? '');
        $birthDate = $_POST['birth_date'] ?? '';
        $address = trim($_POST['address'] ?? '');

        if (empty($firstName) || empty($lastName) || empty($phone)) {
            setFlash('error', 'نام، نام خانوادگی و شماره تلفن الزامی است.', 'error');
            redirect('registration');
        }

        if (!empty($nationalCode)) {
            $existing = db()->selectOne('members', ['national_code' => $nationalCode, 'deleted_at' => null]);
            if ($existing) {
                setFlash('error', 'این کد ملی قبلاً ثبت شده است.', 'error');
                redirect('registration');
            }
        }

        try {
            $memberId = db()->insert('members', [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'phone' => $phone,
                'email' => $email ?: null,
                'national_code' => $nationalCode ?: null,
                'birth_date' => $birthDate ?: null,
                'address' => $address ?: null,
                'status' => 'active',
                'approval_status' => 'pending',
            ]);

            logActivity('register', 'members', $memberId, 'درخواست عضویت جدید: ' . $firstName . ' ' . $lastName);
            setFlash('success', 'درخواست عضویت شما با موفقیت ثبت شد. پس از بررسی نتیجه اعلام خواهد شد.');
        } catch (Exception $e) {
            setFlash('error', 'خطا در ثبت درخواست. لطفاً دوباره تلاش کنید.', 'error');
        }

        redirect('registration');
    }
}