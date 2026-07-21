-- ============================================================
-- Wave Club Management System
-- Database Updates #3 — Slider & Help Guide System
-- ============================================================

SET NAMES utf8mb4;
USE `wave_club`;

-- 1. Create sliders table for student page slider
DROP TABLE IF EXISTS `sliders`;
CREATE TABLE `sliders` (
    `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title`        VARCHAR(255)  NOT NULL COMMENT 'عنوان اسلاید',
    `description`  TEXT          NULL     COMMENT 'توضیحات اسلاید',
    `image_path`   VARCHAR(500)  NOT NULL COMMENT 'مسیر تصویر',
    `link_url`     VARCHAR(500)  NULL     COMMENT 'لینک اختیاری',
    `is_active`    TINYINT(1)    NOT NULL DEFAULT 1,
    `sort_order`   INT           NOT NULL DEFAULT 0,
    `created_by`   INT UNSIGNED  NULL     COMMENT 'مدیر ایجاد کننده',
    `created_at`   DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`   DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at`   DATETIME      NULL,
    KEY `idx_sliders_active` (`is_active`, `sort_order`),
    CONSTRAINT `fk_sliders_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='اسلایدرهای صفحه اعضا';

-- 2. Create role_help_guides table for role-based help guides
DROP TABLE IF EXISTS `role_help_guides`;
CREATE TABLE `role_help_guides` (
    `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `role_name`     VARCHAR(50)   NOT NULL COMMENT 'admin, coach, member, receptionist, accountant',
    `page_key`      VARCHAR(100)  NOT NULL COMMENT 'کلید صفحه مانند: dashboard, members, classes, attendance',
    `title`         VARCHAR(255)  NOT NULL COMMENT 'عنوان راهنما',
    `content`       TEXT          NOT NULL COMMENT 'محتوای راهنما (HTML مجاز)',
    `video_url`     VARCHAR(500)  NULL     COMMENT 'لینک ویدیوی آموزشی',
    `tips`          JSON          NULL     COMMENT 'نکات کلیدی به صورت آرایه',
    `is_active`     TINYINT(1)    NOT NULL DEFAULT 1,
    `sort_order`    INT           NOT NULL DEFAULT 0,
    `created_at`    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_role_page` (`role_name`, `page_key`),
    KEY `idx_role_help_active` (`role_name`, `is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='راهنماهای نقش‌محور';

-- 3. Insert sample slider data
INSERT INTO `sliders` (`title`, `description`, `image_path`, `link_url`, `is_active`, `sort_order`) VALUES
('ورزش منظم، زندگی سالم', 'با برنامه‌های ورزشی متنوع ما، سلامتی و تناسب اندام خود را تضمین کنید.', '/public/uploads/sliders/slider1.jpg', NULL, 1, 1),
('کلاس‌های گروهی پرانرژی', 'در کلاس‌های گروهی ما شرکت کنید و از ورزش لذت ببرید.', '/public/uploads/sliders/slider2.jpg', NULL, 1, 2),
('مربیان مجرب و حرفه‌ای', 'تیم مربیان ما آماده راهنمایی شما در رسیدن به اهداف ورزشی‌تان هستند.', '/public/uploads/sliders/slider3.jpg', NULL, 1, 3),
('تجهیزات مدرن و به‌روز', 'باشگاه ما مجهز به آخرین تجهیزات ورزشی است.', '/public/uploads/sliders/slider4.jpg', NULL, 1, 4);

-- 4. Insert help guide data for different roles
INSERT INTO `role_help_guides` (`role_name`, `page_key`, `title`, `content`, `tips`) VALUES
('admin', 'dashboard', 'راهنمای داشبورد مدیریت', '<p>داشبورد مدیریت نمای کلی از وضعیت باشگاه را نمایش می‌دهد.</p><ul><li>آمار اعضا، کلاس‌ها و پرداخت‌ها</li><li>اعلان‌های مهم</li><li>دسترسی سریع به بخش‌های مختلف</li></ul>', '["برای مشاهده جزئیات بیشتر روی هر کارت کلیک کنید", "اعلان‌های قرمز رنگ نیاز به توجه فوری دارند"]'),
('admin', 'members', 'راهنمای مدیریت اعضا', '<p>در این بخش می‌توانید اطلاعات اعضا را مدیریت کنید.</p><ul><li>افزودن عضو جدید</li><li>ویرایش اطلاعات اعضا</li><li>تأیید یا رد درخواست‌های عضویت</li><li>مشاهده سوابق عضو</li></ul>', ["برای جستجو از فیلترها استفاده کنید", "وضعیت تأیید عضو را قبل از فعال‌سازی بررسی کنید"]),
('admin', 'assignments', 'راهنمای تخصیص اعضا به کلاس‌ها', '<p>در این صفحه می‌توانید اعضا را به کلاس‌های مختلف تخصیص دهید.</p><ul><li>فقط اعضایی که اشتراک فعال دارند قابل تخصیص هستند</li><li>ظرفیت کلاس‌ها رعایت می‌شود</li><li>بیمه عضو باید معتبر باشد</li></ul>', ["قبل از تخصیص، وضعیت بیمه عضو را بررسی کنید", "ظرفیت کلاس را در نظر بگیرید"]),
('admin', 'insurance', 'راهنمای مدیریت بیمه', '<p>ثبت و مدیریت بیمه‌نامه اعضای باشگاه.</p><ul><li>ثبت بیمه جدید برای اعضا</li><li>ویرایش یا تمدید بیمه‌های موجود</li><li>بارگذاری مدارک بیمه</li></ul>', ["تاریخ شروع و پایان بیمه را دقیق وارد کنید", "مدارک بیمه را اسکن و بارگذاری کنید"]),
('coach', 'dashboard', 'راهنمای داشبورد مربی', '<p>داشبورد مربی اطلاعات کلاس‌های شما را نمایش می‌دهد.</p><ul><li>کلاس‌های امروز</li><li>تعداد ورزشکاران</li><li>حضور و غیاب ثبت شده</li><li>اعلان‌ها</li></ul>', ['برای مشاهده لیست ورزشکاران هر کلاس روی آن کلیک کنید', 'حضور و غیاب را در همان روز کلاس ثبت کنید']),
('coach', 'classes', 'راهنمای کلاس‌های من', '<p>لیست تمام کلاس‌هایی که به شما اختصاص داده شده است.</p><ul><li>مشاهده برنامه هفتگی</li><li>دسترسی به لیست ورزشکاران هر کلاس</li><li>ثبت حضور و غیاب</li></ul>', ['برای ثبت حضور و غیاب روی دکمه مربوطه کلیک کنید', 'لیست ورزشکاران شامل اطلاعات تماس و وضعیت بیمه است']),
('coach', 'attendance', 'راهنمای حضور و غیاب', '<p>ثبت حضور و غیاب ورزشکاران در کلاس‌ها.</p><ul><li>انتخاب تاریخ مورد نظر</li><li>انتخاب کلاس</li><li>ثبت وضعیت هر ورزشکار (حاضر، غایب، تأخیر، موجه)</li><li>افزودن توضیحات در صورت نیاز</li></ul>', ['می‌توانید با دکمه "همه حاضر" همه را یکجا حاضر کنید', 'وضعیت بیمه و اشتراک ورزشکاران را بررسی کنید']),
('receptionist', 'members', 'راهنمای پذیرش - مدیریت اعضا', '<p>ثبت‌نام اعضای جدید و مدیریت اطلاعات آن‌ها.</p><ul><li>ثبت عضو جدید</li><li>بارگذاری مدارک</li><li>ارسال درخواست تأیید به مدیریت</li></ul>', ['تمام فیلدهای الزامی را تکمیل کنید', 'مدارک شناسایی را اسکن و بارگذاری کنید']),
('accountant', 'payments', 'راهنمای مدیریت پرداخت‌ها', '<p>ثبت و مدیریت پرداخت‌های اعضا.</p><ul><li>ثبت پرداخت جدید</li><li>صدور فاکتور</li><li>گزارش‌گیری مالی</li></ul>', ['نوع پرداخت را دقیق انتخاب کنید', 'رسید پرداخت را اسکن کنید']);

-- 5. Add tips column to class_registrations for coach notes
ALTER TABLE `class_registrations`
    ADD COLUMN `coach_notes` TEXT NULL COMMENT 'یادداشت‌های مربی درباره این ثبت‌نام' AFTER `status`;

-- 6. Ensure insurance check is properly integrated
-- (No schema change needed, logic is in controller)

