-- ============================================================
-- Schema Update Script
-- Wave Club Management System (باشگاه ویو کنگان)
-- Date: 2025
-- ============================================================
-- NOTES:
--   - These ALTER TABLE statements are safe to run on an
--     existing database created from schema.sql.
--   - Columns that already exist in the current schema are
--     noted in comments and skipped to avoid errors.
--   - No notifications_types table was found in the base
--     schema, so no INSERT statements are included.
-- ============================================================

USE `wave_club`;

-- ============================================================
-- 1. classes — add multi-day schedule support
--    Existing columns schedule_day and schedule_time are kept
--    intact (they may contain existing data).
-- ============================================================
ALTER TABLE `classes`
    ADD COLUMN `schedule_days` VARCHAR(255) NULL
        COMMENT 'روزهای برگزاری: شنبه,دوشنبه,چهارشنبه' AFTER `schedule_day`,
    ADD COLUMN `start_time`    TIME       NULL
        COMMENT 'ساعت شروع' AFTER `schedule_days`,
    ADD COLUMN `end_time`      TIME       NULL
        COMMENT 'ساعت پایان' AFTER `start_time`;

-- ============================================================
-- 2. members — add medical & referral fields
-- ============================================================
ALTER TABLE `members`
    ADD COLUMN `blood_type`      VARCHAR(10) NULL
        COMMENT 'گروه خونی'         AFTER `emergency_phone`,
    ADD COLUMN `allergies`       TEXT        NULL
        COMMENT 'حساسیت‌ها'         AFTER `blood_type`,
    ADD COLUMN `medical_history` TEXT        NULL
        COMMENT 'سوابق پزشکی'       AFTER `allergies`,
    ADD COLUMN `referral_number` VARCHAR(50) NULL
        COMMENT 'شماره معرف'        AFTER `medical_history`;

-- ============================================================
-- 3. member_insurance — add document, type, notes; relax company_id
--    The foreign key fk_mi_company is dropped before making
--    company_id nullable, then re-created so that NULL values
--    are allowed (ON DELETE CASCADE still applies when a
--    company IS referenced).
-- ============================================================
ALTER TABLE `member_insurance`
    DROP FOREIGN KEY `fk_mi_company`;

ALTER TABLE `member_insurance`
    MODIFY COLUMN `company_id`   INT UNSIGNED  NULL
        COMMENT 'شرکت بیمه',
    ADD COLUMN    `document_path` VARCHAR(500) NULL
        COMMENT 'مسیر فایل اسکن بیمه‌نامه' AFTER `policy_number`,
    ADD COLUMN    `insurance_type` VARCHAR(100) NULL
        COMMENT 'نوع بیمه'                 AFTER `document_path`,
    ADD COLUMN    `notes`          TEXT        NULL
        COMMENT 'توضیحات'                  AFTER `insurance_type`;

ALTER TABLE `member_insurance`
    ADD CONSTRAINT `fk_mi_company`
        FOREIGN KEY (`company_id`) REFERENCES `insurance_companies` (`id`)
        ON DELETE CASCADE;

-- ============================================================
-- 4. notifications — add relation / read-tracking columns
--    ⚠ The current schema ALREADY contains the following
--    columns that match the requested changes:
--        • is_read        TINYINT(1) NOT NULL DEFAULT 0
--        • related_module VARCHAR(100) NULL   (equivalent to requested related_type)
--        • related_id     INT UNSIGNED  NULL
--    No ALTER is required for this table.
-- ============================================================
-- ALTER TABLE `notifications`
--     ADD COLUMN `is_read`      TINYINT(1)   NOT NULL DEFAULT 0,
--     ADD COLUMN `related_type` VARCHAR(50)  NULL COMMENT 'member, insurance, class',
--     ADD COLUMN `related_id`   INT UNSIGNED NULL;

-- ============================================================
-- 5. notifications_types — INSERT new notification types
--    ⚠ No notifications_types table exists in the base schema.
--    If this table is created in a future migration, insert
--    records here.  Example (commented out):
-- ============================================================
-- INSERT INTO `notifications_types` (`name`, `slug`, `description`) VALUES
--     ('عضویت جدید',             'new_membership',  'عضو جدید در باشگاه ثبت‌نام کرد'),
--     ('تمدید بیمه',             'insurance_renew', 'بیمه‌نامه نزدیک به انقضا است'),
--     ('سر کلاس',                'class_reminder',  'یادآوری جلسه کلاس'),
--     ('پرداخت موفق',            'payment_success', 'پرداخت با موفقیت انجام شد');

-- ============================================================
-- 6. roles — add coach role
-- ============================================================
INSERT IGNORE INTO `roles` (`id`, `name`, `display_name`, `description`) VALUES
(6, 'coach', 'مربی', 'پنل مربیان باشگاه - مدیریت کلاس‌ها و حضور و غیاب');

-- ============================================================
-- 7. coaches — add user_id for login linkage
-- ============================================================
ALTER TABLE `coaches`
    ADD COLUMN `user_id` INT UNSIGNED NULL
        COMMENT 'آیدی کاربر مرتبط با مربی برای ورود به پنل' AFTER `avatar_path`,
    ADD INDEX `idx_coaches_user_id` (`user_id`);

-- ============================================================
-- 8. class_attendance — attendance tracking table
-- ============================================================
CREATE TABLE IF NOT EXISTS `class_attendance` (
    `id`              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `class_id`        INT UNSIGNED NOT NULL,
    `registration_id` INT UNSIGNED NOT NULL,
    `member_id`       INT UNSIGNED NOT NULL,
    `coach_id`        INT UNSIGNED NOT NULL,
    `attendance_date` DATE        NOT NULL,
    `status`          VARCHAR(20) NOT NULL DEFAULT 'present' COMMENT 'present, absent, late, excused',
    `notes`           TEXT        NULL,
    `created_at`      DATETIME    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_attendance_class_date` (`class_id`, `attendance_date`),
    INDEX `idx_attendance_member` (`member_id`),
    INDEX `idx_attendance_coach` (`coach_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;