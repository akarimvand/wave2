# راهنمای نصب و راه‌اندازی ویژگی‌های جدید

## ویژگی‌های اضافه شده:

### ۱. اسلایدر صفحه اعضا (Slider)
- مدیر می‌تواند اسلایدرهایی با تصویر، عنوان و توضیحات ایجاد کند
- اسلایدرها به صورت خودکار در صفحه اعضا نمایش داده می‌شوند
- پشتیبانی از تاچ برای موبایل
- طراحی مدرن و ریسپانسیو

### ۲. راهنماهای نقش‌محور (Help Guides)
- راهنماهای کمکی برای هر نقش (مدیر، مربی، پذیرش، حسابدار، عضو)
- نمایش راهنما در هر صفحه به صورت پاپ‌آپ
- امکان افزودن ویدیو آموزشی و نکات کلیدی

### ۳. حضور و غیاب توسط مربی
- مربیان می‌توانند در کارتکس شاگردهایشان حضور و غیاب ثبت کنند
- وضعیت‌های مختلف: حاضر، غایب، تأخیر، موجه
- نمایش وضعیت بیمه و اشتراک ورزشکاران

### ۴. تخصیص اعضا به کلاس‌ها و بیمه
- بررسی وضعیت بیمه قبل از تخصیص
- بررسی ظرفیت کلاس‌ها
- بررسی اشتراک فعال عضو

---

## مراحل نصب:

### مرحله ۱: اجرای آپدیت دیتابیس

```bash
mysql -u username -p wave_club < /workspace/database/updates3.sql
```

یا از طریق phpMyAdmin فایل `updates3.sql` را ایمپورت کنید.

### مرحله ۲: افزودن مسیرها به routes.php

محتویات فایل `routes.php.patch` را به فایل `routes.php` اصلی خود اضافه کنید.

### مرحله ۳: تنظیم دسترسی‌ها

مطمئن شوید پوشه `public/uploads/sliders` قابل نوشتن است:

```bash
chmod -R 755 /workspace/public/uploads/sliders
```

### مرحله ۴: افزودن اسلایدر به صفحه اعضا

در کنترلر MembersController، متد index را پیدا کرده و اسلایدرها را لود کنید:

```php
public function index()
{
    // ... existing code ...
    
    // Load active sliders
    $sliders = db()->getAll(
        "SELECT * FROM sliders WHERE is_active = 1 AND deleted_at IS NULL ORDER BY sort_order"
    );
    
    render('members/index', [
        // ... existing params ...
        'sliders' => $sliders,
    ], 'main');
}
```

سپس در فایل `views/members/index.php`، بعد از هدر صفحه، کامپوننت اسلایدر را اضافه کنید:

```php
<?php include __DIR__ . '/slider-component.php'; ?>
```

### مرحله ۵: افزودن دکمه راهنما به صفحات

برای نمایش دکمه راهنما در هر صفحه، این کد را به فایل layout یا صفحات مورد نظر اضافه کنید:

```php
<!-- Help Button -->
<button type="button" onclick="showHelpGuide()" 
        style="position:fixed;bottom:24px;left:24px;width:56px;height:56px;border-radius:50%;background:#F59E0B;color:#fff;border:none;box-shadow:0 4px 20px rgba(245,158,11,0.4);cursor:pointer;z-index:1000;display:flex;align-items:center;justify-content:center;font-size:1.5rem;transition:all 0.3s;">
    <i class="fas fa-question"></i>
</button>

<script>
function showHelpGuide() {
    const roleName = '<?php echo auth()->role() ?? "admin"; ?>';
    const pageKey = '<?php echo $activeMenu ?? "dashboard"; ?>';
    
    fetch('<?php echo url("api/help-guide"); ?>?role=' + roleName + '&page=' + pageKey)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                // Show modal with guide content
                alert(data.guide.title + '\n\n' + data.guide.content);
            } else {
                alert('راهنمایی برای این صفحه یافت نشد.');
            }
        });
}
</script>
```

---

## نکات مهم:

### برای تخصیص اعضا به کلاس‌ها:
- فقط اعضایی که اشتراک فعال دارند قابل تخصیص هستند
- بیمه عضو باید معتبر باشد
- ظرفیت کلاس نباید پر باشد

### برای ثبت بیمه:
- تاریخ شروع و پایان را دقیق وارد کنید
- مدارک بیمه را اسکن و بارگذاری کنید
- وضعیت بیمه به صورت خودکار بر اساس تاریخ‌ها بروزرسانی می‌شود

### برای حضور و غیاب:
- مربیان فقط می‌توانند برای کلاس‌های خودشان حضور و غیاب ثبت کنند
- وضعیت هر ورزشکار به صورت جداگانه قابل ثبت است
- امکان ثبت توضیحات برای هر ورزشکار وجود دارد

---

## تست ویژگی‌ها:

۱. **اسلایدر**: به منوی تنظیمات > اسلایدرها بروید و یک اسلاید جدید ایجاد کنید
۲. **راهنما**: به منوی تنظیمات > راهنماها بروید و یک راهنما برای نقش مدیر ایجاد کنید
۳. **حضور و غیاب**: با حساب مربی وارد شوید و از بخش کلاس‌های من، حضور و غیاب را ثبت کنید
۴. **تخصیص**: به منوی کلاس‌ها > تخصیص اعضا بروید و یک عضو را به کلاس اختصاص دهید

---

## پشتیبانی:

در صورت بروز مشکل، لاگ‌های سیستم را بررسی کنید و از صحت اتصال به دیتابیس اطمینان حاصل نمایید.
