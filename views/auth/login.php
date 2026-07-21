<!-- Login Form - Facebook Style -->
<div class="facebook-login-container">
    <div class="facebook-login-left">
        <div class="facebook-brand-section">
            <h1 class="facebook-logo">wave club</h1>
            <p class="facebook-tagline">سیستم مدیریت هوشمند باشگاه ویو کنگان</p>
        </div>
        <div class="facebook-illustration">
            <div class="illustration-wave">
                <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                    <path fill="#1877F2" d="M44.7,-76.4C58.9,-69.2,71.8,-59.1,79.6,-46.9C87.4,-34.7,90.1,-20.4,89.5,-6.4C88.9,7.6,85,21.3,77.6,33.2C70.2,45.1,59.3,55.2,47.4,63.1C35.5,71,22.6,76.7,9.3,79.3C-4,81.9,-17.7,81.4,-30.5,75.6C-43.3,69.8,-55.2,58.7,-64.4,46.2C-73.6,33.7,-80.1,19.8,-79.8,5.8C-79.5,-8.2,-72.4,-22.3,-63.2,-34.3C-54,-46.3,-42.7,-56.2,-30.5,-63.5C-18.3,-70.8,-5.2,-75.5,6.4,-76.8C18,-78.1,30.5,-76,44.7,-76.4Z" transform="translate(100 100)" />
                </svg>
            </div>
        </div>
    </div>
    
    <div class="facebook-login-right">
        <div class="login-form-card">
            <form method="POST" action="<?php echo url('auth/login'); ?>" id="loginForm" novalidate>
                <?php echo csrf_field(); ?>
                
                <!-- Username -->
                <div class="form-group facebook-form-group">
                    <input
                        type="text"
                        class="form-control facebook-input"
                        id="username"
                        name="username"
                        placeholder="شماره موبایل یا ایمیل"
                        required
                        autocomplete="username"
                        autofocus
                        value="<?php echo e(old('username') ?? ''); ?>"
                    >
                </div>
                
                <!-- Password -->
                <div class="form-group facebook-form-group">
                    <input
                        type="password"
                        class="form-control facebook-input"
                        id="login-password"
                        name="password"
                        placeholder="رمز عبور"
                        required
                        autocomplete="current-password"
                    >
                </div>
                
                <!-- Submit Button -->
                <button type="submit" class="login-btn facebook-login-btn" id="loginBtn">
                    <span id="loginBtnText">ورود</span>
                    <span id="loginBtnSpinner" class="btn-spinner"></span>
                </button>
                
                <!-- Forgot Password -->
                <div class="forgot-password-section">
                    <a href="#" class="forgot-password-link">رمز عبور را فراموش کرده‌اید؟</a>
                </div>
                
                <!-- Divider -->
                <div class="login-divider">
                    <span></span>
                    <span>یا</span>
                    <span></span>
                </div>
                
                <!-- Create Account Button -->
                <a href="<?php echo url('auth/register'); ?>" class="create-account-btn">
                    <i class="fas fa-user-plus"></i>
                    <span>ایجاد حساب کاربری جدید</span>
                </a>
            </form>
        </div>
        
        <!-- Mobile App Section (Optional) -->
        <div class="mobile-app-section">
            <p>دانلود اپلیکیشن موبایل</p>
            <div class="app-buttons">
                <button class="app-store-btn">
                    <i class="fab fa-google-play"></i>
                    <span>Google Play</span>
                </button>
                <button class="app-store-btn">
                    <i class="fab fa-apple"></i>
                    <span>App Store</span>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* ===== Facebook-Style Login Page ===== */
.facebook-login-container {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 2rem;
    max-width: 980px;
    margin: 0 auto;
    padding: 2rem 1rem;
    min-height: calc(100vh - 200px);
}

/* Left Side - Branding */
.facebook-login-left {
    flex: 1;
    max-width: 500px;
    text-align: left;
    padding: 2rem;
}

.facebook-brand-section {
    margin-bottom: 1.5rem;
}

.facebook-logo {
    font-size: 3.5rem;
    font-weight: 800;
    background: linear-gradient(135deg, #1877F2 0%, #0A3178 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    letter-spacing: -1px;
    margin: 0 0 0.5rem 0;
    direction: ltr;
    text-align: left;
}

.facebook-tagline {
    font-size: 1.5rem;
    color: #1c1e21;
    font-weight: 400;
    line-height: 1.4;
    margin: 0;
}

.facebook-illustration {
    margin-top: 2rem;
    opacity: 0.9;
}

.illustration-wave svg {
    width: 280px;
    height: 280px;
    animation: waveFloat 3s ease-in-out infinite;
}

@keyframes waveFloat {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-15px); }
}

/* Right Side - Form Card */
.facebook-login-right {
    flex: 0 0 396px;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.login-form-card {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1), 0 8px 16px rgba(0, 0, 0, 0.1);
    padding: 1.25rem;
    width: 100%;
    box-sizing: border-box;
}

/* Form Inputs */
.facebook-form-group {
    margin-bottom: 1rem;
}

.facebook-input {
    width: 100%;
    padding: 14px 16px;
    font-size: 17px;
    border: 1px solid #dddfe2;
    border-radius: 6px;
    background: #f5f6f7;
    color: #1c1e21;
    transition: all 0.2s ease;
    box-sizing: border-box;
    font-family: 'Vazirmatn', 'Vazir', sans-serif;
}

.facebook-input:focus {
    outline: none;
    border-color: #1877F2;
    background: #fff;
    box-shadow: 0 0 0 2px rgba(24, 119, 242, 0.2);
}

.facebook-input::placeholder {
    color: #65676b;
}

/* Login Button */
.facebook-login-btn {
    width: 100%;
    padding: 12px;
    font-size: 20px;
    font-weight: 700;
    color: #fff;
    background: linear-gradient(135deg, #1877F2 0%, #0A3178 100%);
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    margin-top: 0.5rem;
    font-family: 'Vazirmatn', 'Vazir', sans-serif;
}

.facebook-login-btn:hover:not(:disabled) {
    background: linear-gradient(135deg, #166fe5 0%, #092860 100%);
    transform: translateY(-1px);
}

.facebook-login-btn:active:not(:disabled) {
    transform: translateY(0);
}

.facebook-login-btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

/* Forgot Password Link */
.forgot-password-section {
    text-align: center;
    margin-top: 1rem;
}

.forgot-password-link {
    color: #1877F2;
    font-size: 14px;
    text-decoration: none;
    transition: color 0.2s ease;
}

.forgot-password-link:hover {
    color: #0A3178;
    text-decoration: underline;
}

/* Divider */
.login-divider {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin: 1.5rem 0;
}

.login-divider span:first-child,
.login-divider span:last-child {
    flex: 1;
    height: 1px;
    background: #dadde1;
}

.login-divider span:nth-child(2) {
    color: #65676b;
    font-size: 13px;
    font-weight: 600;
}

/* Create Account Button */
.create-account-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 12px 16px;
    background: #42b72a;
    color: #fff;
    text-decoration: none;
    border-radius: 6px;
    font-size: 17px;
    font-weight: 700;
    transition: all 0.2s ease;
    border: none;
    cursor: pointer;
    font-family: 'Vazirmatn', 'Vazir', sans-serif;
    width: 100%;
    box-sizing: border-box;
}

.create-account-btn:hover {
    background: #36a420;
    transform: translateY(-1px);
}

.create-account-btn i {
    font-size: 18px;
}

/* Mobile App Section */
.mobile-app-section {
    margin-top: 2rem;
    text-align: center;
}

.mobile-app-section p {
    color: #65676b;
    font-size: 14px;
    margin-bottom: 1rem;
}

.app-buttons {
    display: flex;
    gap: 0.75rem;
    justify-content: center;
}

.app-store-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 8px 12px;
    background: #f5f6f7;
    border: 1px solid #dddfe2;
    border-radius: 6px;
    color: #1c1e21;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s ease;
    font-family: 'Vazirmatn', 'Vazir', sans-serif;
}

.app-store-btn:hover {
    background: #e4e6eb;
}

.app-store-btn i {
    font-size: 20px;
}

/* Spinner */
.btn-spinner {
    width: 20px;
    height: 20px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-top-color: #fff;
    border-radius: 50%;
    animation: spin 0.7s linear infinite;
    display: none;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* ===== Responsive Design ===== */
@media (max-width: 900px) {
    .facebook-login-container {
        flex-direction: column;
        gap: 1.5rem;
        padding: 1.5rem 1rem;
    }
    
    .facebook-login-left {
        max-width: 100%;
        text-align: center;
        padding: 1rem;
    }
    
    .facebook-brand-section {
        margin-bottom: 1rem;
    }
    
    .facebook-logo {
        font-size: 2.5rem;
        text-align: center;
    }
    
    .facebook-tagline {
        font-size: 1.1rem;
        text-align: center;
    }
    
    .facebook-illustration {
        margin-top: 1rem;
    }
    
    .illustration-wave svg {
        width: 180px;
        height: 180px;
    }
    
    .facebook-login-right {
        flex: 0 0 auto;
        width: 100%;
        max-width: 396px;
    }
}

@media (max-width: 480px) {
    .facebook-login-container {
        padding: 1rem;
        min-height: calc(100vh - 150px);
    }
    
    .facebook-logo {
        font-size: 2rem;
    }
    
    .facebook-tagline {
        font-size: 0.95rem;
    }
    
    .illustration-wave svg {
        width: 140px;
        height: 140px;
    }
    
    .login-form-card {
        padding: 1rem;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1), 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    
    .facebook-input {
        padding: 12px 14px;
        font-size: 16px;
    }
    
    .facebook-login-btn {
        padding: 11px;
        font-size: 18px;
    }
    
    .create-account-btn {
        padding: 11px 14px;
        font-size: 16px;
    }
    
    .app-buttons {
        flex-direction: column;
    }
    
    .app-store-btn {
        width: 100%;
        justify-content: center;
    }
}

/* Landscape Mode */
@media (max-height: 500px) and (orientation: landscape) {
    .facebook-login-container {
        align-items: flex-start;
        padding-top: 1rem;
    }
    
    .facebook-login-left {
        display: none;
    }
    
    .facebook-login-right {
        flex: 1;
        max-width: 400px;
    }
}

/* Dark Mode Support */
@media (prefers-color-scheme: dark) {
    .facebook-tagline {
        color: #e4e6eb;
    }
    
    .login-form-card {
        background: #242526;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2), 0 8px 16px rgba(0, 0, 0, 0.2);
    }
    
    .facebook-input {
        background: #3a3b3c;
        border-color: #4e4f50;
        color: #e4e6eb;
    }
    
    .facebook-input:focus {
        background: #4e4f50;
        border-color: #1877F2;
    }
    
    .facebook-input::placeholder {
        color: #b0b3b8;
    }
    
    .login-divider span:first-child,
    .login-divider span:last-child {
        background: #4e4f50;
    }
    
    .login-divider span:nth-child(2) {
        color: #b0b3b8;
    }
    
    .mobile-app-section p {
        color: #b0b3b8;
    }
    
    .app-store-btn {
        background: #3a3b3c;
        border-color: #4e4f50;
        color: #e4e6eb;
    }
    
    .app-store-btn:hover {
        background: #4e4f50;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const loginBtn = document.getElementById('loginBtn');
    const loginBtnText = document.getElementById('loginBtnText');
    const loginBtnSpinner = document.getElementById('loginBtnSpinner');
    const usernameInput = document.getElementById('username');
    const passwordInput = document.getElementById('login-password');

    // Form submission with loading state
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            if (loginBtn && !loginBtn.disabled) {
                loginBtn.disabled = true;
                if (loginBtnText) loginBtnText.textContent = 'در حال ورود...';
                if (loginBtnSpinner) loginBtnSpinner.style.display = 'inline-block';
            }
        });
    }

    // Auto-focus first empty field
    if (usernameInput && !usernameInput.value) {
        setTimeout(() => usernameInput.focus(), 300);
    } else if (passwordInput) {
        setTimeout(() => passwordInput.focus(), 300);
    }

    // Input validation feedback
    const inputs = [usernameInput, passwordInput].filter(Boolean);
    inputs.forEach(input => {
        if (input) {
            input.addEventListener('input', function() {
                this.classList.remove('is-invalid');
            });
            
            input.addEventListener('blur', function() {
                if (this.value.trim() === '' && this.hasAttribute('required')) {
                    this.classList.add('is-invalid');
                }
            });
        }
    });

    // Touch-friendly enhancements
    if ('ontouchstart' in window) {
        document.body.classList.add('touch-device');
        
        const buttons = document.querySelectorAll('.facebook-login-btn, .create-account-btn, .app-store-btn');
        buttons.forEach(btn => {
            btn.addEventListener('touchstart', function() {
                this.style.transform = 'scale(0.98)';
            }, { passive: true });
            
            btn.addEventListener('touchend', function() {
                this.style.transform = '';
            }, { passive: true });
        });
    }

    // Prevent zoom on double-tap for iOS
    const metaViewport = document.querySelector('meta[name="viewport"]');
    if (metaViewport && /iPad|iPhone|iPod/.test(navigator.userAgent)) {
        metaViewport.setAttribute('content', 'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no');
    }
});
</script>