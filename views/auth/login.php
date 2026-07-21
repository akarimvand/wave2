<!-- Login Form -->
<form method="POST" action="<?php echo url('auth/login'); ?>" id="loginForm" novalidate>
    <?php echo csrf_field(); ?>

    <!-- Username -->
    <div class="form-group">
        <label for="username">نام کاربری</label>
        <div class="login-input-wrapper">
            <input
                type="text"
                class="form-control"
                id="username"
                name="username"
                placeholder="نام کاربری خود را وارد کنید"
                required
                autocomplete="username"
                autofocus
                value="<?php echo e(old('username') ?? ''); ?>"
            >
            <i class="fas fa-user input-icon"></i>
        </div>
    </div>

    <!-- Password -->
    <div class="form-group">
        <label for="login-password">رمز عبور</label>
        <div class="login-input-wrapper">
            <input
                type="password"
                class="form-control"
                id="login-password"
                name="password"
                placeholder="رمز عبور خود را وارد کنید"
                required
                autocomplete="current-password"
            >
            <i class="fas fa-lock input-icon"></i>
            <button type="button" class="password-toggle" id="passwordToggle" tabindex="-1" aria-label="نمایش رمز عبور">
                <i class="fas fa-eye"></i>
            </button>
        </div>
    </div>

    <!-- Remember Me -->
    <div class="login-remember">
        <input type="checkbox" id="remember" name="remember">
        <label for="remember">مرا به خاطر بسپار</label>
    </div>

    <!-- Submit Button -->
    <button type="submit" class="login-btn" id="loginBtn">
        <span id="loginBtnText">ورود به سیستم</span>
        <i class="fas fa-arrow-left" id="loginBtnIcon" style="font-size:0.85rem;"></i>
        <span id="loginBtnSpinner" class="btn-spinner"></span>
    </button>
</form>

<!-- Register Link -->
<div class="auth-footer">
    <span>حساب کاربری ندارید؟</span>
    <a href="<?php echo url('auth/register'); ?>">
        <i class="fas fa-user-plus" style="font-size: 0.75rem; margin-left: 0.25rem;"></i>
        ثبت‌نام کنید
    </a>
</div>

<script>
    // Password visibility toggle
    const passwordToggle = document.getElementById('passwordToggle');
    const passwordInput = document.getElementById('login-password');

    if (passwordToggle && passwordInput) {
        passwordToggle.addEventListener('click', function() {
            const icon = this.querySelector('i');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
                this.setAttribute('aria-label', 'پنهان کردن رمز عبور');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
                this.setAttribute('aria-label', 'نمایش رمز عبور');
            }
        });
    }

    // Form submission - show loading state
    const loginForm = document.getElementById('loginForm');
    const loginBtn = document.getElementById('loginBtn');
    const loginBtnText = document.getElementById('loginBtnText');
    const loginBtnIcon = document.getElementById('loginBtnIcon');
    const loginBtnSpinner = document.getElementById('loginBtnSpinner');

    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            loginBtn.disabled = true;
            loginBtnText.textContent = 'در حال ورود...';
            if (loginBtnIcon) loginBtnIcon.style.display = 'none';
            if (loginBtnSpinner) loginBtnSpinner.style.display = 'inline-block';
        });
    }

    // Auto-focus
    document.addEventListener('DOMContentLoaded', function() {
        const usernameField = document.getElementById('username');
        if (usernameField && !usernameField.value) {
            usernameField.focus();
        } else if (passwordInput) {
            passwordInput.focus();
        }
    });
</script>