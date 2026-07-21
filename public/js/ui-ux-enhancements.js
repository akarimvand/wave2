/**
 * Club Management System - UI/UX Enhancements
 * Professional QA/QC Improvements
 */

// ============================================================
// 1. DARK MODE TOGGLE
// ============================================================

(function() {
    'use strict';

    // Check for saved theme preference or default to light mode
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-theme', savedTheme);

    // Create theme toggle button
    function createThemeToggle() {
        const toggle = document.createElement('button');
        toggle.className = 'theme-toggle';
        toggle.setAttribute('aria-label', 'تغییر تم');
        toggle.setAttribute('title', 'تغییر به حالت شب/روز');
        
        updateToggleIcon(toggle, savedTheme);
        
        toggle.addEventListener('click', function() {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            
            updateToggleIcon(toggle, newTheme);
            
            // Add animation
            toggle.style.animation = 'none';
            toggle.offsetHeight; // Trigger reflow
            toggle.style.animation = 'bounce 0.5s ease';
        });

        document.body.appendChild(toggle);
    }

    function updateToggleIcon(toggle, theme) {
        if (theme === 'dark') {
            toggle.innerHTML = '<i class="fas fa-sun"></i>';
        } else {
            toggle.innerHTML = '<i class="fas fa-moon"></i>';
        }
    }

    // Initialize theme toggle on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', createThemeToggle);
    } else {
        createThemeToggle();
    }
})();

// ============================================================
// 2. RIPPLE EFFECT FOR BUTTONS
// ============================================================

(function() {
    'use strict';

    function createRipple(event) {
        const button = event.currentTarget;
        
        // Remove existing ripples
        const existingRipples = button.querySelectorAll('.ripple');
        existingRipples.forEach(ripple => ripple.remove());

        const circle = document.createElement('span');
        circle.className = 'ripple';
        
        const diameter = Math.max(button.clientWidth, button.clientHeight);
        const radius = diameter / 2;
        
        const rect = button.getBoundingClientRect();
        
        circle.style.width = circle.style.height = `${diameter}px`;
        circle.style.left = `${event.clientX - rect.left - radius}px`;
        circle.style.top = `${event.clientY - rect.top - radius}px`;
        
        button.appendChild(circle);
        
        // Remove ripple after animation
        setTimeout(() => {
            circle.remove();
        }, 600);
    }

    // Add ripple effect to all buttons with .btn class
    document.addEventListener('click', function(event) {
        const button = event.target.closest('.btn');
        if (button && !button.disabled) {
            createRipple(event);
        }
    });
})();

// ============================================================
// 3. SCROLL TO TOP BUTTON
// ============================================================

(function() {
    'use strict';

    function createScrollToTop() {
        const scrollBtn = document.createElement('button');
        scrollBtn.className = 'scroll-to-top';
        scrollBtn.setAttribute('aria-label', 'بازگشت به بالا');
        scrollBtn.innerHTML = '<i class="fas fa-chevron-up"></i>';
        
        scrollBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        document.body.appendChild(scrollBtn);

        // Show/hide based on scroll position
        function toggleScrollButton() {
            if (window.scrollY > 300) {
                scrollBtn.classList.add('visible');
            } else {
                scrollBtn.classList.remove('visible');
            }
        }

        window.addEventListener('scroll', toggleScrollButton, { passive: true });
        toggleScrollButton(); // Initial check
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', createScrollToTop);
    } else {
        createScrollToTop();
    }
})();

// ============================================================
// 4. PAGE LOAD ANIMATION
// ============================================================

(function() {
    'use strict';

    function initPageLoadAnimation() {
        document.body.classList.add('page-loading');
        
        window.addEventListener('load', function() {
            setTimeout(function() {
                document.body.classList.remove('page-loading');
                document.body.classList.add('page-loaded');
            }, 100);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPageLoadAnimation);
    } else {
        initPageLoadAnimation();
    }
})();

// ============================================================
// 5. ENHANCED TOAST NOTIFICATIONS
// ============================================================

window.showToast = function(message, type = 'info', title = '', duration = 5000) {
    'use strict';

    // Get or create toast container
    let container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
    }

    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    
    // Icon based on type
    let icon = '';
    switch(type) {
        case 'success':
            icon = '<i class="fas fa-check-circle"></i>';
            break;
        case 'danger':
        case 'error':
            icon = '<i class="fas fa-times-circle"></i>';
            break;
        case 'warning':
            icon = '<i class="fas fa-exclamation-triangle"></i>';
            break;
        default:
            icon = '<i class="fas fa-info-circle"></i>';
    }

    toast.innerHTML = `
        <div class="toast-icon">${icon}</div>
        <div class="toast-content">
            ${title ? `<div class="toast-title">${title}</div>` : ''}
            <div class="toast-message">${message}</div>
        </div>
        <button class="toast-close" aria-label="بستن">
            <i class="fas fa-times"></i>
        </button>
        <div class="toast-progress">
            <div class="toast-progress-bar" style="animation-duration: ${duration}ms;"></div>
        </div>
    `;

    container.appendChild(toast);

    // Close button functionality
    const closeBtn = toast.querySelector('.toast-close');
    closeBtn.addEventListener('click', function() {
        closeToast(toast);
    });

    // Auto close after duration
    const timeoutId = setTimeout(function() {
        closeToast(toast);
    }, duration);

    function closeToast(toastElement) {
        if (toastElement.classList.contains('closing')) return;
        
        toastElement.classList.add('closing');
        toastElement.style.animation = 'toastSlideOut 0.3s ease forwards';
        
        setTimeout(function() {
            toastElement.remove();
            
            // Remove container if empty
            if (container.children.length === 0) {
                container.remove();
            }
        }, 300);
    }

    return toast;
};

// ============================================================
// 6. CONFIRMATION DIALOG
// ============================================================

window.showConfirm = function(options = {}) {
    'use strict';

    const defaults = {
        title: 'آیا مطمئن هستید؟',
        text: '',
        type: 'warning', // warning, danger, success
        confirmText: 'تایید',
        cancelText: 'انصراف',
        confirmClass: 'btn-primary',
        cancelClass: 'btn-secondary'
    };

    const config = { ...defaults, ...options };

    return new Promise((resolve) => {
        // Create overlay
        const overlay = document.createElement('div');
        overlay.className = 'wc-modal-overlay active';
        
        // Create modal
        const modal = document.createElement('div');
        modal.className = `confirm-dialog confirm-dialog-${config.type}`;
        modal.setAttribute('role', 'dialog');
        modal.setAttribute('aria-modal', 'true');
        modal.setAttribute('aria-labelledby', 'confirm-title');

        // Icon based on type
        let iconHtml = '';
        switch(config.type) {
            case 'danger':
                iconHtml = '<i class="fas fa-exclamation-triangle"></i>';
                break;
            case 'success':
                iconHtml = '<i class="fas fa-check-circle"></i>';
                break;
            default:
                iconHtml = '<i class="fas fa-question-circle"></i>';
        }

        modal.innerHTML = `
            <div class="confirm-dialog-header">
                <div class="confirm-dialog-icon">${iconHtml}</div>
                <h3 id="confirm-title" class="confirm-dialog-title">${config.title}</h3>
            </div>
            <div class="confirm-dialog-body">
                ${config.text ? `<p class="confirm-dialog-text">${config.text}</p>` : ''}
            </div>
            <div class="confirm-dialog-footer">
                <button class="btn ${config.cancelClass}" id="confirm-cancel">${config.cancelText}</button>
                <button class="btn ${config.confirmClass}" id="confirm-ok">${config.confirmText}</button>
            </div>
        `;

        overlay.appendChild(modal);
        document.body.appendChild(overlay);

        // Handle clicks
        const cancelBtn = modal.querySelector('#confirm-cancel');
        const okBtn = modal.querySelector('#confirm-ok');

        function closeModal(result) {
            overlay.classList.add('closing');
            modal.style.animation = 'wcModalOut 0.2s ease forwards';
            
            setTimeout(() => {
                overlay.remove();
                resolve(result);
            }, 200);
        }

        cancelBtn.addEventListener('click', () => closeModal(false));
        okBtn.addEventListener('click', () => closeModal(true));
        
        // Close on overlay click
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) {
                closeModal(false);
            }
        });

        // Close on ESC key
        function handleEsc(e) {
            if (e.key === 'Escape') {
                closeModal(false);
                document.removeEventListener('keydown', handleEsc);
            }
        }
        document.addEventListener('keydown', handleEsc);
    });
};

// ============================================================
// 7. INTERACTIVE CARDS ANIMATION
// ============================================================

(function() {
    'use strict';

    function initCardAnimations() {
        const cards = document.querySelectorAll('.card, .stat-card');
        
        cards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.05}s`;
            card.classList.add('interactive-card');
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCardAnimations);
    } else {
        initCardAnimations();
    }
})();

// ============================================================
// 8. TABLE ROW STAGGER ANIMATION
// ============================================================

(function() {
    'use strict';

    function initTableAnimations() {
        const tables = document.querySelectorAll('.data-table');
        
        tables.forEach(table => {
            const rows = table.querySelectorAll('tbody tr');
            rows.forEach((row, index) => {
                row.style.animationDelay = `${Math.min(index * 0.05, 0.25)}s`;
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initTableAnimations);
    } else {
        initTableAnimations();
    }
})();

// ============================================================
// 9. ACCESSIBILITY ENHANCEMENTS
// ============================================================

(function() {
    'use strict';

    // Add skip link
    function addSkipLink() {
        const skipLink = document.createElement('a');
        skipLink.className = 'skip-link';
        skipLink.href = '#main-content';
        skipLink.textContent = 'پرش به محتوای اصلی';
        
        document.body.insertBefore(skipLink, document.body.firstChild);
    }

    // Add ARIA labels to interactive elements
    function enhanceAccessibility() {
        // Add aria-label to buttons with only icons
        const iconButtons = document.querySelectorAll('button:not([aria-label])');
        iconButtons.forEach(btn => {
            const text = btn.textContent.trim();
            const icon = btn.querySelector('i, svg');
            
            if (!text && icon) {
                const iconClass = icon.className || '';
                let label = 'دکمه';
                
                if (iconClass.includes('fa-edit') || iconClass.includes('fa-pencil')) {
                    label = 'ویرایش';
                } else if (iconClass.includes('fa-trash') || iconClass.includes('fa-delete')) {
                    label = 'حذف';
                } else if (iconClass.includes('fa-eye')) {
                    label = 'مشاهده';
                } else if (iconClass.includes('fa-check')) {
                    label = 'تایید';
                } else if (iconClass.includes('fa-times') || iconClass.includes('fa-close')) {
                    label = 'بستن';
                }
                
                btn.setAttribute('aria-label', label);
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            addSkipLink();
            enhanceAccessibility();
        });
    } else {
        addSkipLink();
        enhanceAccessibility();
    }
})();

// ============================================================
// 10. IMAGE LAZY LOADING
// ============================================================

(function() {
    'use strict';

    function initLazyLoading() {
        const images = document.querySelectorAll('img[data-src]');
        
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                    img.classList.add('loaded');
                    observer.unobserve(img);
                }
            });
        }, {
            rootMargin: '50px 0px'
        });

        images.forEach(img => imageObserver.observe(img));
    }

    if ('IntersectionObserver' in window) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initLazyLoading);
        } else {
            initLazyLoading();
        }
    }
})();

// ============================================================
// 11. FORM VALIDATION VISUAL FEEDBACK
// ============================================================

(function() {
    'use strict';

    function initFormValidation() {
        const forms = document.querySelectorAll('form[novalidate]');
        
        forms.forEach(form => {
            const inputs = form.querySelectorAll('.form-input, .form-select, .form-textarea');
            
            inputs.forEach(input => {
                input.addEventListener('blur', function() {
                    validateField(this);
                });
                
                input.addEventListener('input', function() {
                    // Remove error state on input
                    this.classList.remove('error');
                    const errorEl = this.parentElement.querySelector('.form-error');
                    if (errorEl) errorEl.remove();
                });
            });
            
            form.addEventListener('submit', function(e) {
                let isValid = true;
                
                inputs.forEach(input => {
                    if (!validateField(input)) {
                        isValid = false;
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                    
                    // Show error toast
                    showToast('لطفا خطاهای فرم را اصلاح کنید', 'danger', 'خطا در ارسال فرم');
                }
            });
        });
    }

    function validateField(input) {
        const value = input.value.trim();
        const isRequired = input.hasAttribute('required');
        
        // Remove existing error
        input.classList.remove('error');
        const errorEl = input.parentElement.querySelector('.form-error');
        if (errorEl) errorEl.remove();
        
        // Check required
        if (isRequired && !value) {
            showError(input, 'این فیلد الزامی است');
            return false;
        }
        
        // Check email
        if (input.type === 'email' && value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                showError(input, 'ایمیل معتبر نیست');
                return false;
            }
        }
        
        // Check min length
        const minLength = input.getAttribute('minlength');
        if (minLength && value.length < parseInt(minLength)) {
            showError(input, `حداقل ${minLength} کاراکتر لازم است`);
            return false;
        }
        
        return true;
    }

    function showError(input, message) {
        input.classList.add('error');
        
        const errorEl = document.createElement('div');
        errorEl.className = 'form-error';
        errorEl.innerHTML = `<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>${message}`;
        
        input.parentElement.appendChild(errorEl);
        
        // Add shake animation
        input.style.animation = 'shakeError 0.5s ease';
        setTimeout(() => {
            input.style.animation = '';
        }, 500);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initFormValidation);
    } else {
        initFormValidation();
    }
})();

// ============================================================
// END OF UI/UX ENHANCEMENTS
// ============================================================
