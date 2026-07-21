-- ============================================================
-- Wave Club Management System (باشگاه ویو کنگان)
-- Database Schema
-- Engine: InnoDB | Charset: utf8mb4 | Collation: utf8mb4_unicode_ci
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_ENGINE_SUBSTITUTION';

CREATE DATABASE IF NOT EXISTS `wave_club`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `wave_club`;

-- ============================================================
-- 1. users — سیستم کاربران
-- ============================================================
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
    `id`             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `username`       VARCHAR(100)  NOT NULL,
    `password_hash`  VARCHAR(255)  NOT NULL,
    `full_name`      VARCHAR(200)  NOT NULL,
    `email`          VARCHAR(200)  NULL,
    `phone`          VARCHAR(30)   NULL,
    `avatar_path`    VARCHAR(500)  NULL,
    `is_active`      TINYINT(1)    NOT NULL DEFAULT 1,
    `created_at`     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at`     DATETIME      NULL,
    UNIQUE KEY `uk_users_username` (`username`),
    UNIQUE KEY `uk_users_email`    (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 2. roles — نقش‌ها
-- ============================================================
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
    `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name`        VARCHAR(100) NOT NULL,
    `display_name` VARCHAR(200) NOT NULL,
    `description` TEXT         NULL,
    `created_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at`  DATETIME     NULL,
    UNIQUE KEY `uk_roles_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 3. permissions — مجوزها
-- ============================================================
DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
    `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name`         VARCHAR(100) NOT NULL,
    `display_name` VARCHAR(200) NOT NULL,
    `module`       VARCHAR(100) NOT NULL,
    `created_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at`   DATETIME     NULL,
    UNIQUE KEY `uk_permissions_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 4. role_permissions — ارتباط نقش و مجوز
-- ============================================================
DROP TABLE IF EXISTS `role_permissions`;
CREATE TABLE `role_permissions` (
    `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `role_id`       INT UNSIGNED NOT NULL,
    `permission_id` INT UNSIGNED NOT NULL,
    `created_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at`    DATETIME     NULL,
    UNIQUE KEY `uk_role_permission` (`role_id`, `permission_id`),
    CONSTRAINT `fk_rp_role`       FOREIGN KEY (`role_id`)       REFERENCES `roles`       (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_rp_permission` FOREIGN KEY (`permission_id`)  REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 5. user_roles — ارتباط کاربر و نقش
-- ============================================================
DROP TABLE IF EXISTS `user_roles`;
CREATE TABLE `user_roles` (
    `id`       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id`  INT UNSIGNED NOT NULL,
    `role_id`  INT UNSIGNED NOT NULL,
    `created_at` DATETIME   NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME   NULL,
    UNIQUE KEY `uk_user_role` (`user_id`, `role_id`),
    CONSTRAINT `fk_ur_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_ur_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 6. members — اعضای باشگاه
-- ============================================================
DROP TABLE IF EXISTS `members`;
CREATE TABLE `members` (
    `id`               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `first_name`       VARCHAR(100)  NOT NULL,
    `last_name`        VARCHAR(100)  NOT NULL,
    `national_code`    VARCHAR(20)   NULL,
    `phone`            VARCHAR(30)   NOT NULL,
    `email`            VARCHAR(200)  NULL,
    `birth_date`       DATE          NULL,
    `address`          TEXT          NULL,
    `emergency_contact` VARCHAR(200) NULL,
    `emergency_phone`  VARCHAR(30)   NULL,
    `blood_type`       VARCHAR(10)   NULL,
    `allergies`        TEXT          NULL,
    `medications`      TEXT          NULL,
    `medical_history`  TEXT          NULL,
    `status`           ENUM('active','inactive','expired','suspended','pending') NOT NULL DEFAULT 'active',
    `approval_status`  ENUM('pending','approved','rejected')          NOT NULL DEFAULT 'pending',
    `notes`            TEXT          NULL,
    `referred_by`      VARCHAR(200)  NULL,
    `referral_number`  VARCHAR(50)   NULL,
    `approval_date`    DATETIME      NULL,
    `approved_by`      INT UNSIGNED  NULL,
    `created_at`       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at`       DATETIME      NULL,
    UNIQUE KEY `uk_members_national_code` (`national_code`),
    KEY `idx_members_status`          (`status`),
    KEY `idx_members_approval_status` (`approval_status`),
    KEY `idx_members_phone`           (`phone`),
    CONSTRAINT `fk_members_approved_by` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 7. member_documents — مدارک اعضا
-- ============================================================
DROP TABLE IF EXISTS `member_documents`;
CREATE TABLE `member_documents` (
    `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `member_id`     INT UNSIGNED NOT NULL,
    `title`         VARCHAR(200) NOT NULL,
    `file_path`     VARCHAR(500) NOT NULL,
    `document_type` VARCHAR(100) NULL,
    `created_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at`    DATETIME     NULL,
    KEY `idx_md_member_id` (`member_id`),
    CONSTRAINT `fk_md_member` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 8. membership_plans — طرح‌های عضویت
-- ============================================================
DROP TABLE IF EXISTS `membership_plans`;
CREATE TABLE `membership_plans` (
    `id`             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name`           VARCHAR(200)  NOT NULL,
    `description`    TEXT          NULL,
    `duration_days`  INT           NOT NULL,
    `price`          DECIMAL(12,0) NOT NULL DEFAULT 0,
    `max_classes`    INT           NULL,
    `is_active`      TINYINT(1)    NOT NULL DEFAULT 1,
    `created_at`     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at`     DATETIME      NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 9. member_memberships — اشتراک‌های اعضا
-- ============================================================
DROP TABLE IF EXISTS `member_memberships`;
CREATE TABLE `member_memberships` (
    `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `member_id`    INT UNSIGNED NOT NULL,
    `plan_id`      INT UNSIGNED NOT NULL,
    `start_date`   DATE         NOT NULL,
    `end_date`     DATE         NOT NULL,
    `price_paid`   DECIMAL(12,0) NOT NULL DEFAULT 0,
    `status`       ENUM('active','expired','cancelled') NOT NULL DEFAULT 'active',
    `payment_id`   INT UNSIGNED  NULL,
    `created_at`   DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`   DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at`   DATETIME      NULL,
    KEY `idx_mm_member_id` (`member_id`),
    KEY `idx_mm_plan_id`   (`plan_id`),
    KEY `idx_mm_status`    (`status`),
    CONSTRAINT `fk_mm_member`  FOREIGN KEY (`member_id`)  REFERENCES `members`          (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_mm_plan`    FOREIGN KEY (`plan_id`)    REFERENCES `membership_plans`  (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_mm_payment` FOREIGN KEY (`payment_id`) REFERENCES `payments`          (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 10. membership_discounts — تخفیف‌های طرح عضویت
-- ============================================================
DROP TABLE IF EXISTS `membership_discounts`;
CREATE TABLE `membership_discounts` (
    `id`              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `plan_id`         INT UNSIGNED  NOT NULL,
    `discount_percent` DECIMAL(5,2) NOT NULL DEFAULT 0,
    `start_date`      DATE          NOT NULL,
    `end_date`        DATE          NOT NULL,
    `is_active`       TINYINT(1)    NOT NULL DEFAULT 1,
    `created_at`      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at`      DATETIME      NULL,
    KEY `idx_mdisc_plan_id` (`plan_id`),
    CONSTRAINT `fk_mdisc_plan` FOREIGN KEY (`plan_id`) REFERENCES `membership_plans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 11. classes — کلاس‌های باشگاه
-- ============================================================
DROP TABLE IF EXISTS `classes`;
CREATE TABLE `classes` (
    `id`               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name`             VARCHAR(200) NOT NULL,
    `description`      TEXT         NULL,
    `coach_id`         INT UNSIGNED NULL,
    `schedule_time`    TIME         NOT NULL,
    `schedule_day`     VARCHAR(50)  NOT NULL COMMENT 'e.g. Saturday, Sunday or multiple days',
    `max_participants`  INT          NOT NULL DEFAULT 0,
    `is_active`        TINYINT(1)   NOT NULL DEFAULT 1,
    `created_at`       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at`       DATETIME     NULL,
    KEY `idx_classes_coach_id` (`coach_id`),
    CONSTRAINT `fk_classes_coach` FOREIGN KEY (`coach_id`) REFERENCES `coaches` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 12. class_registrations — ثبت‌نام در کلاس‌ها
-- ============================================================
DROP TABLE IF EXISTS `class_registrations`;
CREATE TABLE `class_registrations` (
    `id`                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `member_id`         INT UNSIGNED NOT NULL,
    `class_id`          INT UNSIGNED NOT NULL,
    `registration_date` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `status`            ENUM('active','cancelled') NOT NULL DEFAULT 'active',
    `created_at`        DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`        DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at`        DATETIME     NULL,
    UNIQUE KEY `uk_class_reg_member_class` (`member_id`, `class_id`),
    KEY `idx_cr_class_id` (`class_id`),
    CONSTRAINT `fk_cr_member` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_cr_class`  FOREIGN KEY (`class_id`)  REFERENCES `classes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 12-b. class_attendance — حضور و غیاب کلاس‌ها
-- ============================================================
DROP TABLE IF EXISTS `class_attendance`;
CREATE TABLE `class_attendance` (
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

-- ============================================================
-- 13. coaches — مربیان
-- ============================================================
DROP TABLE IF EXISTS `coaches`;
CREATE TABLE `coaches` (
    `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `first_name`   VARCHAR(100)  NOT NULL,
    `last_name`    VARCHAR(100)  NOT NULL,
    `phone`        VARCHAR(30)   NULL,
    `email`        VARCHAR(200)  NULL,
    `specialty`    VARCHAR(200)  NULL,
    `hire_date`    DATE          NULL,
    `salary`       DECIMAL(12,0) NOT NULL DEFAULT 0,
    `is_active`    TINYINT(1)    NOT NULL DEFAULT 1,
    `bio`          TEXT          NULL,
    `avatar_path`  VARCHAR(500)  NULL,
    `user_id`      INT UNSIGNED  NULL COMMENT 'آیدی کاربر مرتبط با مربی برای ورود به پنل',
    `created_at`   DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`   DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at`   DATETIME      NULL,
    INDEX `idx_coaches_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 14. payments — پرداخت‌ها
-- ============================================================
DROP TABLE IF EXISTS `payments`;
CREATE TABLE `payments` (
    `id`              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `member_id`       INT UNSIGNED  NULL,
    `membership_id`   INT UNSIGNED  NULL,
    `amount`          DECIMAL(12,0) NOT NULL DEFAULT 0,
    `payment_method`  ENUM('cash','card','transfer','online') NOT NULL DEFAULT 'cash',
    `payment_date`    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `status`          ENUM('paid','pending','refunded','cancelled') NOT NULL DEFAULT 'pending',
    `description`     TEXT          NULL,
    `reference_number` VARCHAR(200) NULL,
    `created_at`      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at`      DATETIME      NULL,
    KEY `idx_payments_member_id`     (`member_id`),
    KEY `idx_payments_membership_id` (`membership_id`),
    KEY `idx_payments_status`        (`status`),
    KEY `idx_payments_date`          (`payment_date`),
    CONSTRAINT `fk_pay_member`    FOREIGN KEY (`member_id`)    REFERENCES `members`           (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_pay_membership` FOREIGN KEY (`membership_id`) REFERENCES `member_memberships` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 15. events — رویدادها
-- ============================================================
DROP TABLE IF EXISTS `events`;
CREATE TABLE `events` (
    `id`               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title`            VARCHAR(255) NOT NULL,
    `description`      TEXT         NULL,
    `event_date`       DATE         NOT NULL,
    `event_time`       TIME         NULL,
    `location`         VARCHAR(300) NULL,
    `max_participants`  INT          NOT NULL DEFAULT 0,
    `is_active`        TINYINT(1)   NOT NULL DEFAULT 1,
    `created_at`       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at`       DATETIME     NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 16. event_registrations — ثبت‌نام در رویدادها
-- ============================================================
DROP TABLE IF EXISTS `event_registrations`;
CREATE TABLE `event_registrations` (
    `id`                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `member_id`         INT UNSIGNED NOT NULL,
    `event_id`          INT UNSIGNED NOT NULL,
    `registration_date` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `status`            ENUM('registered','cancelled') NOT NULL DEFAULT 'registered',
    `created_at`        DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`        DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at`        DATETIME     NULL,
    UNIQUE KEY `uk_event_reg_member_event` (`member_id`, `event_id`),
    KEY `idx_er_event_id` (`event_id`),
    CONSTRAINT `fk_er_member` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_er_event`  FOREIGN KEY (`event_id`)  REFERENCES `events`  (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 17. equipment — تجهیزات باشگاه
-- ============================================================
DROP TABLE IF EXISTS `equipment`;
CREATE TABLE `equipment` (
    `id`               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name`             VARCHAR(200)  NOT NULL,
    `description`      TEXT          NULL,
    `purchase_date`    DATE          NULL,
    `purchase_price`   DECIMAL(12,0) NOT NULL DEFAULT 0,
    `condition_status` ENUM('new','good','fair','poor','broken') NOT NULL DEFAULT 'new',
    `last_maintenance` DATE          NULL,
    `next_maintenance` DATE          NULL,
    `created_at`       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at`       DATETIME      NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 18. insurance_companies — شرکت‌های بیمه
-- ============================================================
DROP TABLE IF EXISTS `insurance_companies`;
CREATE TABLE `insurance_companies` (
    `id`              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name`            VARCHAR(200)  NOT NULL,
    `phone`           VARCHAR(30)   NULL,
    `address`         TEXT          NULL,
    `contact_person`  VARCHAR(200)  NULL,
    `discount_percent` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    `is_active`       TINYINT(1)    NOT NULL DEFAULT 1,
    `created_at`      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at`      DATETIME      NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 19. member_insurance — بیمه اعضا
-- ============================================================
DROP TABLE IF EXISTS `member_insurance`;
CREATE TABLE `member_insurance` (
    `id`              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `member_id`       INT UNSIGNED   NOT NULL,
    `company_id`      INT UNSIGNED   NULL,
    `insurance_type`  VARCHAR(100)   NULL,
    `policy_number`   VARCHAR(200)   NULL,
    `document_path`   VARCHAR(500)   NULL,
    `start_date`      DATE           NULL,
    `end_date`        DATE           NULL,
    `premium_amount`  DECIMAL(12,0)  NOT NULL DEFAULT 0,
    `status`          ENUM('active','expired','cancelled','pending_approval') NOT NULL DEFAULT 'active',
    `created_at`      DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at`      DATETIME       NULL,
    KEY `idx_mi_member_id`  (`member_id`),
    KEY `idx_mi_company_id` (`company_id`),
    CONSTRAINT `fk_mi_member`  FOREIGN KEY (`member_id`)  REFERENCES `members`             (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_mi_company` FOREIGN KEY (`company_id`) REFERENCES `insurance_companies` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 20. tickets — تیکت‌های پشتیبانی
-- ============================================================
DROP TABLE IF EXISTS `tickets`;
CREATE TABLE `tickets` (
    `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `member_id`    INT UNSIGNED  NULL,
    `subject`      VARCHAR(300) NOT NULL,
    `description`  TEXT         NOT NULL,
    `priority`     ENUM('low','medium','high','urgent') NOT NULL DEFAULT 'medium',
    `status`       ENUM('open','in_progress','resolved','closed') NOT NULL DEFAULT 'open',
    `assigned_to`  INT UNSIGNED  NULL,
    `created_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at`   DATETIME     NULL,
    KEY `idx_tickets_member_id`    (`member_id`),
    KEY `idx_tickets_status`       (`status`),
    KEY `idx_tickets_priority`     (`priority`),
    KEY `idx_tickets_assigned_to`  (`assigned_to`),
    CONSTRAINT `fk_tickets_member`   FOREIGN KEY (`member_id`)   REFERENCES `members` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_tickets_assigned` FOREIGN KEY (`assigned_to`) REFERENCES `users`   (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 21. ticket_replies — پاسخ‌های تیکت
-- ============================================================
DROP TABLE IF EXISTS `ticket_replies`;
CREATE TABLE `ticket_replies` (
    `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `ticket_id`  INT UNSIGNED NOT NULL,
    `user_id`    INT UNSIGNED NULL,
    `message`    TEXT        NOT NULL,
    `is_admin`   TINYINT(1)  NOT NULL DEFAULT 0,
    `created_at` DATETIME    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME    NULL,
    KEY `idx_tr_ticket_id` (`ticket_id`),
    CONSTRAINT `fk_tr_ticket` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 22. notifications — اعلان‌ها
-- ============================================================
DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
    `id`             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id`        INT UNSIGNED  NULL,
    `title`          VARCHAR(300)  NOT NULL,
    `message`        TEXT          NOT NULL,
    `type`           ENUM('info','warning','success','error') NOT NULL DEFAULT 'info',
    `is_read`        TINYINT(1)    NOT NULL DEFAULT 0,
    `related_module` VARCHAR(100)  NULL,
    `related_id`     INT UNSIGNED   NULL,
    `created_at`     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at`     DATETIME      NULL,
    KEY `idx_notif_user_id` (`user_id`),
    KEY `idx_notif_is_read` (`is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 23. settings — تنظیمات سیستم
-- ============================================================
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
    `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `key`        VARCHAR(100) NOT NULL,
    `value`      TEXT         NULL,
    `updated_by` INT UNSIGNED NULL,
    `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME     NULL,
    UNIQUE KEY `uk_settings_key` (`key`),
    CONSTRAINT `fk_settings_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 24. activity_logs — لاگ فعالیت‌ها
-- ============================================================
DROP TABLE IF EXISTS `activity_logs`;
CREATE TABLE `activity_logs` (
    `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id`     INT UNSIGNED  NULL,
    `action`      VARCHAR(100)  NOT NULL,
    `module`      VARCHAR(100)  NOT NULL,
    `record_id`   INT UNSIGNED   NULL,
    `description` TEXT          NULL,
    `ip_address`  VARCHAR(45)   NULL,
    `created_at`  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at`  DATETIME      NULL,
    KEY `idx_al_user_id` (`user_id`),
    KEY `idx_al_module`  (`module`),
    KEY `idx_al_action`  (`action`),
    KEY `idx_al_date`    (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 25. menus — منوی سیستم
-- ============================================================
DROP TABLE IF EXISTS `menus`;
CREATE TABLE `menus` (
    `id`             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `parent_id`      INT UNSIGNED  NULL,
    `title`          VARCHAR(200)  NOT NULL,
    `icon`           VARCHAR(100)  NULL COMMENT 'e.g. fas fa-home, bi bi-people',
    `url`            VARCHAR(500)  NULL,
    `sort_order`     INT           NOT NULL DEFAULT 0,
    `is_active`      TINYINT(1)    NOT NULL DEFAULT 1,
    `permission_name` VARCHAR(100) NULL,
    `created_at`     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at`     DATETIME      NULL,
    KEY `idx_menus_parent_id` (`parent_id`),
    KEY `idx_menus_sort_order` (`sort_order`),
    CONSTRAINT `fk_menus_parent` FOREIGN KEY (`parent_id`) REFERENCES `menus` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


--##############################################################
-- SEED DATA
--##############################################################

-- ============================================================
-- Roles
-- ============================================================
INSERT INTO `roles` (`id`, `name`, `display_name`, `description`) VALUES
(1, 'admin',        'مدیر سیستم',       'دسترسی کامل به تمام بخش‌های سیستم'),
(2, 'manager',      'مدیر باشگاه',      'مدیریت عملیات روزانه باشگاه'),
(3, 'receptionist', 'پذیرش',           'ثبت‌نام اعضا و پاسخگویی'),
(4, 'accountant',   'حسابدار',         'مدیریت مالی و پرداخت‌ها'),
(5, 'member',       'عضو باشگاه',      'دسترسی محدود به پنل کاربری'),
(6, 'coach',        'مربی',             'پنل مربیان باشگاه - مدیریت کلاس‌ها و حضور و غیاب');

-- ============================================================
-- Admin User
-- NOTE: The password_hash below is a PLACEHOLDER for 'admin123'.
--       Generate the real hash by running in PHP:
--         echo password_hash('admin123', PASSWORD_DEFAULT);
--       Then replace the value below before deploying to production.
-- ============================================================
INSERT INTO `users` (`id`, `username`, `password_hash`, `full_name`, `is_active`) VALUES
(1, 'admin', '$2y$10$PLACEHOLDER_GENERATE_WITH_password_hash_admin123', 'مدیر سیستم', 1);

-- ============================================================
-- Assign admin role to admin user
-- ============================================================
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES (1, 1);

-- ============================================================
-- Permissions — grouped by module
-- ============================================================
INSERT INTO `permissions` (`name`, `display_name`, `module`) VALUES
-- Dashboard
('dashboard.view',    'مشاهده داشبورد',    'dashboard'),
-- Members
('members.view',      'مشاهده اعضا',      'members'),
('members.create',    'ثبت عضو جدید',     'members'),
('members.edit',      'ویرایش عضو',       'members'),
('members.delete',    'حذف عضو',          'members'),
('members.approve',   'تأیید عضو',        'members'),
-- Membership Plans
('plans.view',        'مشاهده طرح‌ها',     'plans'),
('plans.create',      'ایجاد طرح',        'plans'),
('plans.edit',        'ویرایش طرح',       'plans'),
('plans.delete',      'حذف طرح',          'plans'),
-- Payments
('payments.view',     'مشاهده پرداخت‌ها',  'payments'),
('payments.create',   'ثبت پرداخت',       'payments'),
('payments.edit',     'ویرایش پرداخت',    'payments'),
('payments.refund',   'بازگشت وجه',       'payments'),
-- Classes
('classes.view',      'مشاهده کلاس‌ها',    'classes'),
('classes.create',    'ایجاد کلاس',       'classes'),
('classes.edit',      'ویرایش کلاس',      'classes'),
('classes.delete',    'حذف کلاس',         'classes'),
-- Coaches
('coaches.view',      'مشاهده مربیان',    'coaches'),
('coaches.create',    'ثبت مربی',         'coaches'),
('coaches.edit',      'ویرایش مربی',      'coaches'),
('coaches.delete',    'حذف مربی',         'coaches'),
-- Reports
('reports.view',      'مشاهده گزارش‌ها',   'reports'),
('reports.export',    'خروجی گزارش',      'reports'),
-- Users & Roles
('users.view',        'مشاهده کاربران',   'users'),
('users.create',      'ایجاد کاربر',      'users'),
('users.edit',        'ویرایش کاربر',     'users'),
('users.delete',      'حذف کاربر',        'users'),
-- Settings
('settings.view',     'مشاهده تنظیمات',   'settings'),
('settings.edit',     'ویرایش تنظیمات',   'settings'),
-- Equipment
('equipment.view',    'مشاهده تجهیزات',   'equipment'),
('equipment.create',  'ثبت تجهیزات',     'equipment'),
('equipment.edit',    'ویرایش تجهیزات',  'equipment'),
('equipment.delete',  'حذف تجهیزات',     'equipment'),
-- Events
('events.view',       'مشاهده رویدادها',  'events'),
('events.create',     'ایجاد رویداد',     'events'),
('events.edit',       'ویرایش رویداد',    'events'),
('events.delete',     'حذف رویداد',       'events'),
-- Tickets
('tickets.view',      'مشاهده تیکت‌ها',   'tickets'),
('tickets.reply',     'پاسخ به تیکت',    'tickets'),
('tickets.close',     'بستن تیکت',       'tickets'),
-- Insurance
('insurance.view',    'مشاهده بیمه',      'insurance'),
('insurance.create',  'ثبت بیمه',        'insurance'),
('insurance.edit',    'ویرایش بیمه',     'insurance'),
-- Menus
('menus.view',        'مشاهده منوها',     'menus'),
('menus.edit',        'ویرایش منوها',    'menus');

-- ============================================================
-- Role-Permission mapping
-- Admin gets ALL permissions
-- ============================================================
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT 1, `id` FROM `permissions`;

-- Manager permissions
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(2, 1),   -- dashboard.view
(2, 2),   -- members.view
(2, 3),   -- members.create
(2, 4),   -- members.edit
(2, 6),   -- members.approve
(2, 7),   -- plans.view
(2, 8),   -- plans.create
(2, 9),   -- plans.edit
(2, 11),  -- payments.view
(2, 12),  -- payments.create
(2, 13),  -- payments.edit
(2, 15),  -- classes.view
(2, 16),  -- classes.create
(2, 17),  -- classes.edit
(2, 19),  -- coaches.view
(2, 20),  -- coaches.create
(2, 21),  -- coaches.edit
(2, 23),  -- reports.view
(2, 24),  -- reports.export
(2, 36),  -- equipment.view
(2, 37),  -- equipment.create
(2, 38),  -- equipment.edit
(2, 40),  -- events.view
(2, 41),  -- events.create
(2, 42),  -- events.edit
(2, 44),  -- tickets.view
(2, 45),  -- tickets.reply
(2, 46),  -- tickets.close
(2, 47),  -- insurance.view
(2, 48),  -- insurance.create
(2, 49);  -- insurance.edit

-- Receptionist permissions
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(3, 1),   -- dashboard.view
(3, 2),   -- members.view
(3, 3),   -- members.create
(3, 4),   -- members.edit
(3, 7),   -- plans.view
(3, 11),  -- payments.view
(3, 12),  -- payments.create
(3, 15),  -- classes.view
(3, 40),  -- events.view
(3, 41),  -- events.create
(3, 47),  -- insurance.view
(3, 48);  -- insurance.create

-- Accountant permissions
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(4, 1),   -- dashboard.view
(4, 2),   -- members.view
(4, 7),   -- plans.view
(4, 11),  -- payments.view
(4, 12),  -- payments.create
(4, 13),  -- payments.edit
(4, 14),  -- payments.refund
(4, 23),  -- reports.view
(4, 24);  -- reports.export

-- ============================================================
-- Sidebar Menus
-- ============================================================
INSERT INTO `menus` (`id`, `parent_id`, `title`, `icon`, `url`, `sort_order`, `is_active`, `permission_name`) VALUES
(1,  NULL, 'داشبورد',            'fas fa-tachometer-alt', '/dashboard',                1, 1, 'dashboard.view'),
(2,  NULL, 'مدیریت اعضا',        'fas fa-users',          '/members',                  2, 1, 'members.view'),
(3,  2,    'لیست اعضا',          NULL,                     '/members',                  1, 1, 'members.view'),
(4,  2,    'درخواست‌های عضویت',  NULL,                     '/members/pending',          2, 1, 'members.approve'),
(5,  2,    'مدارک اعضا',         NULL,                     '/members/documents',        3, 1, 'members.view'),
(6,  NULL, 'طرح‌های عضویت',     'fas fa-id-card',        '/plans',                    3, 1, 'plans.view'),
(7,  NULL, 'کلاس‌ها',            'fas fa-dumbbell',       '/classes',                  4, 1, 'classes.view'),
(8,  NULL, 'مربیان',             'fas fa-chalkboard-teacher', '/coaches',               5, 1, 'coaches.view'),
(9,  NULL, 'پرداخت‌ها',          'fas fa-money-bill-wave','/payments',                 6, 1, 'payments.view'),
(10, NULL, 'تجهیزات',            'fas fa-tools',          '/equipment',                7, 1, 'equipment.view'),
(11, NULL, 'بیمه',               'fas fa-shield-alt',     '/insurance',                8, 1, 'insurance.view'),
(12, NULL, 'رویدادها',           'fas fa-calendar-alt',   '/events',                   9, 1, 'events.view'),
(13, NULL, 'تیکت‌های پشتیبانی',  'fas fa-headset',        '/tickets',                  10,1, 'tickets.view'),
(14, NULL, 'گزارش‌ها',           'fas fa-chart-bar',      '/reports',                  11,1, 'reports.view'),
(15, NULL, 'کاربران و نقش‌ها',   'fas fa-user-shield',    '/users',                    12,1, 'users.view'),
(16, NULL, 'تنظیمات',            'fas fa-cog',            '/settings',                 13,1, 'settings.view'),
(17, NULL, 'لاگ فعالیت‌ها',      'fas fa-history',        '/activity-logs',            14,1, 'activity_logs.view');

-- ============================================================
-- System Settings
-- ============================================================
INSERT INTO `settings` (`key`, `value`, `updated_by`) VALUES
('club_name',          'ویو کلاب کنگان',                    1),
('club_address',       'کنگان، بلوار ساحلی',                1),
('club_phone',         '۰۷۶۳۳۲۲۲۲۲۲',                       1),
('club_email',         'info@waveclub.ir',                   1),
('currency',           'تومان',                              1),
('timezone',           'Asia/Tehran',                        1),
('date_format',        'Y/m/d',                              1),
('membership_auto_expire', '1',                              1),
('default_approval',   'pending',                            1),
('logo_path',          '/assets/images/logo.png',            1),
('sms_enabled',        '0',                                  1);

SET FOREIGN_KEY_CHECKS = 1;-- ============================================================
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;-- ============================================================
-- Wave Club Management System
-- Database Updates #2 — Registration Overhaul
-- ============================================================

SET NAMES utf8mb4;

-- 1. Add 'pending' to members status enum
ALTER TABLE `members`
    MODIFY COLUMN `status` ENUM('active','inactive','expired','suspended','pending') NOT NULL DEFAULT 'active';

-- 2. Add medical/health columns to members table
ALTER TABLE `members`
    ADD COLUMN `blood_type`      VARCHAR(10)  NULL AFTER `emergency_phone`,
    ADD COLUMN `allergies`       TEXT         NULL AFTER `blood_type`,
    ADD COLUMN `medications`     TEXT         NULL AFTER `allergies`,
    ADD COLUMN `medical_history` TEXT         NULL AFTER `medications`,
    ADD COLUMN `referral_number` VARCHAR(50)  NULL AFTER `referred_by`;

-- 3. Update member_insurance table for registration flow
ALTER TABLE `member_insurance`
    MODIFY COLUMN `company_id`   INT UNSIGNED   NULL,
    ADD COLUMN `insurance_type`  VARCHAR(100)   NULL AFTER `company_id`,
    ADD COLUMN `document_path`   VARCHAR(500)   NULL AFTER `policy_number`,
    MODIFY COLUMN `start_date`   DATE           NULL,
    MODIFY COLUMN `end_date`     DATE           NULL,
    MODIFY COLUMN `status` ENUM('active','expired','cancelled','pending_approval') NOT NULL DEFAULT 'active';

-- 4. Drop and recreate foreign key for company_id (now nullable, ON DELETE SET NULL)
ALTER TABLE `member_insurance` DROP FOREIGN KEY `fk_mi_company`;
ALTER TABLE `member_insurance`
    ADD CONSTRAINT `fk_mi_company` FOREIGN KEY (`company_id`) REFERENCES `insurance_companies` (`id`) ON DELETE SET NULL;-- ============================================================
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

-- ============================================================
-- Wave Club Management System (باشگاه ویو کنگان)
-- Demo / Test Data
-- ============================================================
-- این فایل شامل داده‌های نمونه برای تست و نمایش سیستم است.
-- پس از اجرای schema.sql و updates.sql، این فایل را اجرا کنید
-- یا از طریق setup.php با گزینه «داده‌های دمو» اضافه شود.
-- ============================================================
-- رمز عبور تمام کاربران تستی: 123456
-- ============================================================

USE `wave_club`;
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================
-- 1. کاربران اضافی (مدیر باشگاه، پذیرش، حسابدار، اعضا)
--    id=1 (admin) از قبل در schema.sql وجود دارد
-- ============================================================
INSERT INTO `users` (`id`, `username`, `password_hash`, `full_name`, `email`, `phone`, `is_active`) VALUES
-- password: 123456 → $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
(2, 'manager',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'علی رضایی',       'ali@waveclub.ir',       '09121234567', 1),
(3, 'reception',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'سارا احمدی',      'sara@waveclub.ir',      '09131234567', 1),
(4, 'accountant',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'رضا محمدی',       'reza@waveclub.ir',      '09141234567', 1),
-- اعضا
(5, 'member1',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'محمد حسینی',     'm.hosseini@email.com',  '09351234567', 1),
(6, 'member2',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'فاطمه کریمی',    'f.karimi@email.com',    '09361234567', 1),
(7, 'member3',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'امیر نوری',      'a.noori@email.com',     '09371234567', 1),
(8, 'member4',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'زهرا صادقی',     'z.sadeghi@email.com',   '09381234567', 1),
(9, 'member5',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'حسین رحیمی',    'h.rahimi@email.com',    '09391234567', 1),
(10,'member6',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مریم جعفری',     'm.jafari@email.com',    '09151234567', 1),
(11,'member7',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'سعید عباسی',     's.abbasi@email.com',    '09161234567', 1),
(12,'member8',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'نرگس حیدری',     'n.heydari@email.com',   '09171234567', 1),
-- مربیان
(13,'coach1',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مهدی پورمحمدی',   'm.pourmohammadi@waveclub.ir',  '09181111111', 1),
(14,'coach2',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'لیلا کاظمی',      'l.kazemi@waveclub.ir',         '09182222222', 1),
(15,'coach3',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'احمد شریفی',     'a.sharifi@waveclub.ir',        '09183333333', 1),
(16,'coach4',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'نسرین موسوی',     'n.mousavi@waveclub.ir',        '09184444444', 1),
(17,'coach5',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'کامران بهزادی',    'k.behzadi@waveclub.ir',        '09185555555', 1);

-- ============================================================
-- 2. تخصیص نقش به کاربران
-- ============================================================
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES
(2, 2),   -- manager → manager
(3, 3),   -- reception → receptionist
(4, 4),   -- accountant → accountant
(5, 5),   -- member1 → member
(6, 5),   -- member2 → member
(7, 5),   -- member3 → member
(8, 5),   -- member4 → member
(9, 5),   -- member5 → member
(10, 5),  -- member6 → member
(11, 5),  -- member7 → member
(12, 5),  -- member8 → member
(13, 6),  -- coach1 → coach
(14, 6),  -- coach2 → coach
(15, 6),  -- coach3 → coach
(16, 6),  -- coach4 → coach
(17, 6);  -- coach5 → coach

-- ============================================================
-- 3. اعضای باشگاه (members)
-- ============================================================
INSERT INTO `members` (`id`, `first_name`, `last_name`, `national_code`, `phone`, `email`, `birth_date`, `address`, `emergency_contact`, `emergency_phone`, `status`, `approval_status`, `approval_date`, `approved_by`, `notes`, `referred_by`) VALUES
(1, 'محمد',    'حسینی',    '2870456789', '09351234567', 'm.hosseini@email.com',  '1990-03-15', 'کنگان، خیابان دریا، کوچه ۱۲', 'علی حسینی',   '09121111111', 'active',   'approved', '2025-01-10 08:30:00', 1, 'عضو قدیمی باشگاه',          NULL),
(2, 'فاطمه',   'کریمی',    '3890123456', '09361234567', 'f.karimi@email.com',    '1995-07-22', 'کنگان، بلوار ساحلی، پلاک ۵',  'احمد کریمی',  '09122222222', 'active',   'approved', '2025-01-15 09:00:00', 1, NULL,                        'محمد حسینی'),
(3, 'امیر',     'نوری',     '4900567890', '09371234567', 'a.noori@email.com',     '1988-11-03', 'بوشهر، خیابان سعدی',              'زهرا نوری',   '09123333333', 'active',   'approved', '2025-02-01 10:15:00', 2, NULL,                        NULL),
(4, 'زهرا',    'صادقی',    '5810234567', '09381234567', 'z.sadeghi@email.com',   '1992-05-18', 'کنگان، محله قدیمی',                'محمد صادقی', '09124444444', 'active',   'approved', '2025-02-10 11:00:00', 2, NULL,                        'فاطمه کریمی'),
(5, 'حسین',    'رحیمی',    '6720345678', '09391234567', 'h.rahimi@email.com',    '1985-09-30', 'کنگان، خیابان انقلاب',              'فاطمه رحیمی', '09125555555', 'inactive', 'approved', '2025-01-20 08:00:00', 1, 'عضویت منقضی شده',          NULL),
(6, 'مریم',    'جعفری',    '7830456789', '09151234567', 'm.jafari@email.com',    '1998-01-12', 'کنگان، فاز ۲',                     'رضا جعفری',   '09126666666', 'active',   'approved', '2025-03-01 09:30:00', 3, NULL,                        NULL),
(7, 'سعید',    'عباسی',    '8940567890', '09161234567', 's.abbasi@email.com',    '1991-06-25', 'کنگان، خیابان ولیعصر',             'مهدی عباسی',  '09127777777', 'active',   'approved', '2025-03-05 10:00:00', 2, NULL,                        'امیر نوری'),
(8, 'نرگس',    'حیدری',    '9050678901', '09171234567', 'n.heydari@email.com',   '1993-12-08', 'بوشهر، خیابان خلیج فارس',          'حمید حیدری',  '09128888888', 'pending',  'pending',  NULL,                          NULL, 'درخواست عضویت جدید',       NULL);

-- ============================================================
-- 4. طرح‌های عضویت (membership_plans)
-- ============================================================
INSERT INTO `membership_plans` (`id`, `name`, `description`, `duration_days`, `price`, `max_classes`, `is_active`) VALUES
(1, 'یک ماهه معمولی',   'اشتراک یک ماهه با دسترسی به تمام فضای باشگاه',                    30,  800000,   NULL, 1),
(2, 'سه ماهه طلایی',    'اشتراک سه ماهه با تخفیف ویژه و دسترسی به کلاس‌ها',                90,  2100000,  12,   1),
(3, 'شش ماهه نقره‌ای',  'اشتراک شش ماهه با تخفیف بیشتر و ویژگی‌های اضافی',               180, 3800000,  30,   1),
(4, 'یک ساله الماسی',   'اشتراک یک ساله با تمام امکانات باشگاه و اولویت ثبت‌نام کلاس',    365, 6500000,  60,   1),
(5, 'یک ماهه ویژه آبی', 'اشتراک ماهانه مخصوص کلاس‌های آبی و استخر',                       30,  1200000,  20,   1),
(6, 'پاس تمرینی روزانه', 'دسترسی یک روزه به تمام امکانات باشگاه',                         1,   80000,    1,   1);

-- ============================================================
-- 5. اشتراک فعال اعضا (member_memberships)
-- ============================================================
INSERT INTO `member_memberships` (`id`, `member_id`, `plan_id`, `start_date`, `end_date`, `price_paid`, `status`) VALUES
(1, 1, 4, '2025-01-10', '2026-01-10', 6500000, 'active'),
(2, 2, 2, '2025-01-15', '2025-04-15', 2100000, 'active'),
(3, 3, 3, '2025-02-01', '2025-08-01', 3800000, 'active'),
(4, 4, 1, '2025-03-01', '2025-04-01', 800000,  'active'),
(5, 5, 1, '2025-01-20', '2025-02-20', 800000,  'expired'),
(6, 6, 2, '2025-03-01', '2025-06-01', 2100000, 'active'),
(7, 7, 1, '2025-03-05', '2025-04-05', 800000,  'active');

-- ============================================================
-- 6. تخفیف‌های طرح عضویت (membership_discounts)
-- ============================================================
INSERT INTO `membership_discounts` (`plan_id`, `discount_percent`, `start_date`, `end_date`, `is_active`) VALUES
(2, 10.00, '2025-01-01', '2025-03-31', 1),
(3, 15.00, '2025-01-01', '2025-06-30', 1),
(4, 20.00, '2025-03-20', '2025-04-20', 1);

-- ============================================================
-- 7. مربیان (coaches)
-- ============================================================
INSERT INTO `coaches` (`id`, `first_name`, `last_name`, `phone`, `email`, `specialty`, `hire_date`, `salary`, `is_active`, `bio`, `user_id`) VALUES
(1, 'مهدی',  'پورمحمدی',  '09181111111', 'm.pourmohammadi@waveclub.ir',  'فیتنس و بدنسازی',       '2023-03-15', 25000000, 1, 'مربی رسمی فدراسیون بدنسازی با ۸ سال سابقه. دارای مدرک بین‌المللی ACSM.', 13),
(2, 'لیلا',  'کاظمی',     '09182222222', 'l.kazemi@waveclub.ir',         'یوگا و مدیتیشن',        '2023-06-01', 20000000, 1, 'مربی یوگا با ۵ سال سابقه. تخصص در یوگا تراپی و پرانایاما.', 14),
(3, 'احمد',  'شریفی',     '09183333333', 'a.sharifi@waveclub.ir',        'شنا و ورزش‌های آبی',    '2022-09-20', 22000000, 1, 'قهرمان سابق شنا کشور. مربی تیم ملی نوجوانان شنا.', 15),
(4, 'نسرین', 'موسوی',     '09184444444', 'n.mousavi@waveclub.ir',        'ایروبیک و زومبا',       '2024-01-10', 18000000, 1, 'مربی licensed زومبا با ۳ سال سابقه تدریس گروهی.', 16),
(5, 'کامران', 'بهزادی',    '09185555555', 'k.behzadi@waveclub.ir',        'رزمی و کنگ‌فو',         '2023-11-01', 23000000, 1, 'دارای کمربند مشکی کنگ‌فو و ۱۲ سال سابقه آموزش رزمی.', 17);

-- ============================================================
-- 8. کلاس‌های باشگاه (classes)
-- ============================================================
INSERT INTO `classes` (`id`, `name`, `description`, `coach_id`, `schedule_time`, `schedule_day`, `max_participants`, `is_active`) VALUES
(1, 'فیتنس صبحگاهی',       'کلاس فیتنس و تمرینات قدرتی صبح زود',                     1, '07:00:00', 'شنبه,دوشنبه,چهارشنبه',       20, 1),
(2, 'یوگا آرامش‌بخش',     'کلاس یوگا برای رفع استرس و افزایش تمرکز',                  2, '08:30:00', 'یکشنبه,سه‌شنبه,پنجشنبه',   15, 1),
(3, 'شنا مبتدی',           'آموزش شنا برای افراد مبتدی',                               3, '10:00:00', 'شنبه,یکشنبه',               10, 1),
(4, 'شنا پیشرفته',         'تکنیک‌های پیشرفته شنا و استقامت',                           3, '16:00:00', 'دوشنبه,چهارشنبه',             10, 1),
(5, 'زومبا انرژی‌بخش',     'کلاس رقص زومبا برای چربی‌سوزی و سرحالی',                   4, '17:30:00', 'شنبه,سه‌شنبه,پنجشنبه',       25, 1),
(6, 'کنگ‌فو و دفاع شخصی',  'آموزش کنگ‌فو و تکنیک‌های دفاع شخصی',                       5, '19:00:00', 'یکشنبه,چهارشنبه',             18, 1),
(7, 'فیتنس عصرگاهی',       'کلاس فیتنس برای افراد شاغل بعد از کار',                    1, '18:00:00', 'شنبه,دوشنبه,چهارشنبه',       20, 1),
(8, 'ایروبیک صبحگاهی',     'کلاس ایروبیک سبک برای شروع روز',                           4, '06:30:00', 'شنبه,دوشنبه,چهارشنبه,پنجشنبه', 22, 1);

-- ============================================================
-- 9. ثبت‌نام در کلاس‌ها (class_registrations)
-- ============================================================
INSERT INTO `class_registrations` (`member_id`, `class_id`, `registration_date`, `status`) VALUES
(1, 1, '2025-01-12 07:00:00', 'active'),
(1, 7, '2025-01-15 18:00:00', 'active'),
(2, 2, '2025-01-16 08:30:00', 'active'),
(2, 5, '2025-02-01 17:30:00', 'active'),
(3, 6, '2025-02-03 19:00:00', 'active'),
(3, 4, '2025-02-05 16:00:00', 'active'),
(4, 8, '2025-03-02 06:30:00', 'active'),
(4, 2, '2025-03-03 08:30:00', 'active'),
(6, 1, '2025-03-05 07:00:00', 'active'),
(6, 3, '2025-03-08 10:00:00', 'active'),
(7, 5, '2025-03-06 17:30:00', 'active'),
(7, 7, '2025-03-07 18:00:00', 'active');

-- ============================================================
-- 10. تجهیزات باشگاه (equipment)
-- ============================================================
INSERT INTO `equipment` (`id`, `name`, `description`, `purchase_date`, `purchase_price`, `condition_status`, `last_maintenance`, `next_maintenance`) VALUES
(1, 'تردمیل پروفشنال',       'تردمیل حرفه‌ای برند لایف فیتنس مدل XF50',            '2023-06-15', 45000000, 'good',   '2025-01-10', '2025-07-10'),
(2, 'دوچرخه ثابت اسپینینگ',   'دوچرخه اسپینینگ برند کتلر با مانیتور',               '2023-08-20', 28000000, 'good',   '2025-02-01', '2025-08-01'),
(3, 'ست وزنه کامل',           'ست کامل دمبل و هالتر از ۲ تا ۵۰ کیلوگرم',             '2022-12-01', 35000000, 'fair',   '2024-12-15', '2025-06-15'),
(4, 'دستگاه پرس سینه',        'دستگاه پرس سینه هامر استرنث با تنظیم کامل',           '2023-03-10', 52000000, 'good',   '2025-01-20', '2025-07-20'),
(5, 'دستگاه لگ پرس',          'دستگاه لگ پرس برند نایتیلوس ۴ ایستگاه',                '2023-04-25', 48000000, 'good',   '2024-11-30', '2025-05-30'),
(6, 'استپ آیرobic',           'تخت استپ آیروبیک قابل تنظیم ارتفاع',                  '2024-01-05', 3500000,  'new',    '2025-03-01', '2025-09-01'),
(7, 'میز بیلیارد',            'میز بیلیارد استاندارد ۷ فوت با توپ و میله',            '2022-09-15', 65000000, 'fair',   '2024-10-20', '2025-04-20'),
(8, 'تشک یوگا (۱۰ عدد)',      'تشک یوگا ضد لغزش ضخامت ۱۰ میلی‌متر - بسته ۱۰ عددی',   '2024-02-10', 8000000,  'new',    NULL,        '2025-08-10'),
(9, 'طناب کششی مجموعه',      'مجموعه طناب کششی و بند مقاومتی در ۵ سطح',              '2024-03-01', 2500000,  'new',    NULL,        NULL),
(10,'اسکی ارگ',              'دستگاه اسکی ارگ حرفه‌ای برند کانسپت ۲',               '2023-11-20', 38000000, 'good',   '2025-02-15', '2025-08-15');

-- ============================================================
-- 11. شرکت‌های بیمه (insurance_companies)
-- ============================================================
INSERT INTO `insurance_companies` (`id`, `name`, `phone`, `address`, `contact_person`, `discount_percent`, `is_active`) VALUES
(1, 'بیمه ایران',           '۰۲۱۲۲۲۲۱۱۱۱', 'تهران، خیابان آزادی',                  'مرتضی احمدی',   5.00, 1),
(2, 'بیمه آسیا',            '۰۲۱۸۸۷۷۶۶۵۵', 'تهران، سعادت آباد',                    'شقایق نوری',    10.00, 1),
(3, 'بیمه دانا',            '۰۷۶۳۳۴۴۴۵۵۵', 'بوشهر، خیابان سعدی',                    'علی موسوی',     8.00, 1),
(4, 'بیمه پارسیان',         '۰۲۱۲۲۰۰۱۱۲۲', 'تهران، ونک',                            'نازنین رضایی',  12.00, 1),
(5, 'بیمه کوثر',            '۰۷۶۳۳۵۵۵۶۶۶', 'کنگان، بلوار ساحلی',                    'حسن قاسمی',    15.00, 1);

-- ============================================================
-- 12. بیمه اعضا (member_insurance)
-- ============================================================
INSERT INTO `member_insurance` (`id`, `member_id`, `company_id`, `policy_number`, `start_date`, `end_date`, `premium_amount`, `status`) VALUES
(1, 1, 3, 'DAN-1403-45678',  '2025-01-10', '2026-01-10', 350000,  'active'),
(2, 2, 5, 'KOS-1403-78901',  '2025-01-15', '2026-01-15', 280000,  'active'),
(3, 3, 2, 'ASI-1403-23456',  '2025-02-01', '2026-02-01', 420000,  'active'),
(4, 4, 1, 'IRA-1403-67890',  '2025-03-01', '2026-03-01', 500000,  'active'),
(5, 6, 4, 'PAR-1403-34567',  '2025-03-01', '2026-03-01', 380000,  'active'),
(6, 5, 3, 'DAN-1402-11111',  '2024-01-20', '2025-01-20', 320000,  'expired'),
(7, 7, 5, 'KOS-1403-99999',  '2025-03-05', '2026-03-05', 280000,  'active');

-- ============================================================
-- 13. پرداخت‌ها (payments)
-- ============================================================
INSERT INTO `payments` (`id`, `member_id`, `membership_id`, `amount`, `payment_method`, `payment_date`, `status`, `description`, `reference_number`) VALUES
-- پرداخت عضویت‌ها
(1, 1, 1, 6500000, 'card',     '2025-01-10 08:35:00', 'paid',     'پرداخت اشتراک یک ساله الماسی',       'TXN-14030110-001'),
(2, 2, 2, 2100000, 'card',     '2025-01-15 09:05:00', 'paid',     'پرداخت اشتراک سه ماهه طلایی',        'TXN-14030115-002'),
(3, 3, 3, 3800000, 'transfer', '2025-02-01 10:20:00', 'paid',     'پرداخت اشتراک شش ماهه نقره‌ای',      'TRF-14030201-003'),
(4, 4, 4, 800000,  'cash',     '2025-03-01 11:05:00', 'paid',     'پرداخت اشتراک یک ماهه معمولی',       NULL),
(5, 5, 5, 800000,  'cash',     '2025-01-20 08:10:00', 'paid',     'پرداخت اشتراک یک ماهه معمولی',       NULL),
(6, 6, 6, 2100000, 'card',     '2025-03-01 09:35:00', 'paid',     'پرداخت اشتراک سه ماهه طلایی',        'TXN-14030301-006'),
(7, 7, 7, 800000,  'online',   '2025-03-05 10:10:00', 'paid',     'پرداخت اشتراک یک ماهه معمولی',       'ONL-14030305-007'),
-- پرداخت بیمه
(8, 1, NULL, 350000,  'card',     '2025-01-10 08:40:00', 'paid',     'پرداخت حق بیمه سالانه - بیمه دانا',  'TXN-14030110-008'),
(9, 2, NULL, 280000,  'cash',     '2025-01-15 09:15:00', 'paid',     'پرداخت حق بیمه - بیمه کوثر',          NULL),
(10, 3, NULL, 420000, 'transfer', '2025-02-01 10:30:00', 'paid',     'پرداخت حق بیمه - بیمه آسیا',          'TRF-14030201-010'),
(11, 4, NULL, 500000, 'card',     '2025-03-01 11:15:00', 'paid',     'پرداخت حق بیمه - بیمه ایران',         'TXN-14030301-011'),
(12, 6, NULL, 380000, 'online',   '2025-03-01 09:40:00', 'paid',     'پرداخت حق بیمه - بیمه پارسیان',       'ONL-14030301-012'),
-- پرداخت در انتظار
(13, 8, NULL, 800000, 'online',   '2025-04-01 10:00:00', 'pending',  'پرداخت اشتراک یک ماهه - در انتظار تایید', 'ONL-14030401-013');

-- ============================================================
-- 14. رویدادها (events)
-- ============================================================
INSERT INTO `events` (`id`, `title`, `description`, `event_date`, `event_time`, `location`, `max_participants`, `is_active`) VALUES
(1, 'مسابقه شنا جام ویو کلاب',    'مسابقه شنا سرعت و استقامت بین اعضای باشگاه با اهدای جوایز نفیس',  '2025-04-15', '09:00:00', 'استخر باشگاه ویو',           30, 1),
(2, 'کارگاه تغذیه ورزشی',         'کارگاه آموزشی رایگان تغذیه صحیح و مکمل‌های ورزشی',                '2025-04-20', '16:00:00', 'سالن همایش‌های باشگاه',      50, 1),
(3, 'جشن سالانه باشگاه',          'جشن سالگرد تاسیس باشگاه ویو با برنامه‌های شاد و قرعه‌کشی',       '2025-05-01', '18:00:00', 'سالن اصلی باشگاه',            100, 1),
(4, 'اردوی صبحگاهی ساحلی',        'پیاده‌روی ساحلی و تمرینات ورزشی در فضای باز',                      '2025-04-25', '06:30:00', 'ساحل کنگان',                  40, 1),
(5, 'کلاس رایگان زومبا',          'جلسه معارفه رایگان زومبا برای اعضای جدید',                        '2025-04-10', '17:00:00', 'سالن ایروبیک',                25, 1);

-- ============================================================
-- 15. ثبت‌نام در رویدادها (event_registrations)
-- ============================================================
INSERT INTO `event_registrations` (`member_id`, `event_id`, `registration_date`, `status`) VALUES
(1, 1, '2025-03-20 10:00:00', 'registered'),
(1, 3, '2025-03-25 12:00:00', 'registered'),
(2, 2, '2025-03-18 09:00:00', 'registered'),
(2, 5, '2025-03-15 14:00:00', 'registered'),
(3, 1, '2025-03-22 11:00:00', 'registered'),
(3, 4, '2025-03-28 08:00:00', 'registered'),
(4, 2, '2025-03-19 15:00:00', 'registered'),
(6, 5, '2025-03-16 10:00:00', 'registered'),
(6, 1, '2025-03-21 09:30:00', 'registered'),
(7, 3, '2025-03-26 14:00:00', 'registered'),
(7, 4, '2025-03-29 07:00:00', 'registered');

-- ============================================================
-- 16. تیکت‌های پشتیبانی (tickets)
-- ============================================================
INSERT INTO `tickets` (`id`, `member_id`, `subject`, `description`, `priority`, `status`, `assigned_to`, `created_at`) VALUES
(1, 1, 'سوال درباره تمدید اشتراک',        'سلام، اشتراک الماسی من تا دی ماه اعتبار دارد. آیا امکان تمدید زودتر با تخفیف وجود دارد؟', 'low',      'resolved',  3, '2025-03-10 09:00:00'),
(2, 2, 'مشکل در ثبت‌نام کلاس یوگا',        'من نمی‌توانم در کلاس یوگای روز سه‌شنبه ثبت‌نام کنم. پیام ظرفیت تکمیل شده می‌آید.',       'medium',   'in_progress', 3, '2025-03-12 14:30:00'),
(3, 3, 'درخواست تغییر ساعتی کلاس شنا',     'آیا امکان تغییر ساعت کلاس شنا پیشرفته از ۴ عصر به ۵ عصر وجود دارد؟',               'medium',   'open',      NULL, '2025-03-15 10:15:00'),
(4, 4, 'گزارش خرابی تردمیل شماره ۲',      'تردمیل شماره ۲ هنگام تمرین صدا می‌دهد و نوار آن ساییده شده است.',                   'high',     'in_progress', 2, '2025-03-18 08:45:00'),
(5, 6, 'استعلام مانده بیمه‌نامه',          'لطفاً اعتبار بیمه‌نامه من را چک کنید. می‌خواهم مطمئن شوم هنوز فعال است.',             'low',      'resolved',  3, '2025-03-05 11:00:00'),
(6, 7, 'پیشنهاد اضافه کردن کلاس پیلاتس',  'با سلام، آیا امکان اضافه کردن کلاس پیلاتس به برنامه باشگاه وجود دارد؟',               'low',      'open',      NULL, '2025-03-20 16:00:00');

-- ============================================================
-- 17. پاسخ‌های تیکت (ticket_replies)
-- ============================================================
INSERT INTO `ticket_replies` (`ticket_id`, `user_id`, `message`, `is_admin`, `created_at`) VALUES
-- تیکت 1
(1, 5, 'سلام، اشتراک الماسی من تا دی ماه اعتبار دارد. آیا امکان تمدید زودتر با تخفیف وجود دارد؟', 0, '2025-03-10 09:00:00'),
(1, 3, 'سلام جناب حسینی. بله، اگر یک ماه قبل از انقضا تمدید کنید ۵٪ تخفیف ویژه دارید.', 1, '2025-03-10 10:30:00'),
(1, 5, 'ممنون از اطلاع‌رسانی. لطفاً فاکتور تمدید را آماده کنید.', 0, '2025-03-10 11:00:00'),
(1, 3, 'فاکتور صادر شد و در بخش پرداخت‌ها قابل مشاهده است. موفق باشید.', 1, '2025-03-10 14:00:00'),
-- تیکت 2
(2, 6, 'من نمی‌توانم در کلاس یوگا ثبت‌نام کنم. پیام ظرفیت تکمیل شده می‌آید.', 0, '2025-03-12 14:30:00'),
(2, 3, 'سلام خانم کریمی. ظرفیت کلاس یوگای سه‌شنبه تکمیل شده است. لیست انتظار اضافه شدید.', 1, '2025-03-12 15:30:00'),
-- تیکت 4
(4, 8, 'تردمیل شماره ۲ هنگام تمرین صدا می‌دهد و نوار آن ساییده شده است.', 0, '2025-03-18 08:45:00'),
(4, 2, 'ممنون از گزارش. تکنسین تعمیرات فردا بررسی می‌کند. تا آن زمان از تردمیل ۱ استفاده کنید.', 1, '2025-03-18 09:30:00'),
-- تیکت 5
(5, 9, 'لطفاً اعتبار بیمه‌نامه من را چک کنید.', 0, '2025-03-05 11:00:00'),
(5, 3, 'سلام خانم جعفری. بیمه‌نامه شما تا ۱۴۰۵/۰۱/۱۰ اعتبار دارد و فعال است.', 1, '2025-03-05 12:00:00'),
(5, 9, 'ممنون بابت پیگیری سریع.', 0, '2025-03-05 12:15:00');

-- ============================================================
-- 18. اعلان‌ها (notifications)
-- ============================================================
INSERT INTO `notifications` (`user_id`, `title`, `message`, `type`, `is_read`, `related_module`, `related_id`, `created_at`) VALUES
-- اعلان برای عضو 1
(5, 'خوش آمدید!',                    'به باشگاه ویو کنگان خوش آمدید. امیدواریم تجربه خوبی داشته باشید.',           'success', 1, NULL,          NULL, '2025-01-10 08:35:00'),
(5, 'تمدید اشتراک',                  'اشتراک یک ساله الماسی شما تا ۱۴۰۵/۰۱/۱۰ اعتبار دارد. برای تمدید زودتر تخفیف دارید.', 'info', 0, 'membership',    1,    '2025-03-01 08:00:00'),
(5, 'تیکت شما پاسخ داده شد',         'تیکت «سوال درباره تمدید اشتراک» توسط پذیرش پاسخ داده شد.',                  'info', 1, 'ticket',       1,    '2025-03-10 10:30:00'),
-- اعلان برای عضو 2
(6, 'ثبت‌نام در رویداد موفق',         'شما با موفقیت در مسابقه شنا جام ویو کلاب ثبت‌نام شدید.',                    'success', 1, 'event',        1,    '2025-03-20 10:00:00'),
(6, 'کلاس یوگا - لیست انتظار',       'شما در لیست انتظار کلاس یوگا قرار گرفتید. در صورت آزاد شدن ظرفیت اطلاع رسانی می‌شود.', 'warning', 0, 'class', 2, '2025-03-12 15:30:00'),
-- اعلان برای عضو 3
(7, 'اشتراک فعال',                   'اشتراک شش ماهه نقره‌ای شما با موفقیت فعال شد.',                           'success', 1, 'membership',    3,    '2025-02-01 10:20:00'),
(7, 'یادآوری پرداخت بیمه',           'بیمه‌نامه شما تا ۱۴۰۴/۱۱/۱۲ اعتبار دارد. لطفاً قبل از انقضا تمدید کنید.',     'warning', 0, 'insurance',    3,    '2025-04-15 09:00:00'),
-- اعلان برای عضو 4
(8, 'ثبت‌نام کلاس موفق',             'شما با موفقیت در کلاس‌های ایروبیک و یوگا ثبت‌نام شدید.',                    'success', 1, 'class',        NULL, '2025-03-02 07:00:00'),
(8, 'تیکت شما در دست بررسی است',     'تیکت «گزارش خرابی تردمیل» به مدیریت ارجاع شد.',                          'info', 0, 'ticket',       4,    '2025-03-18 09:30:00'),
-- اعلان عمومی
(5, 'رویداد جدید: مسابقه شنا',       'مسابقه شنا جام ویو کلاب در ۲۵ فروردین برگزار می‌شود. ثبت‌نام آغاز شد!',     'info', 0, 'event',        1,    '2025-03-15 08:00:00'),
(6, 'رویداد جدید: مسابقه شنا',       'مسابقه شنا جام ویو کلاب در ۲۵ فروردین برگزار می‌شود. ثبت‌نام آغاز شد!',     'info', 0, 'event',        1,    '2025-03-15 08:00:00'),
(7, 'رویداد جدید: مسابقه شنا',       'مسابقه شنا جام ویو کلاب در ۲۵ فروردین برگزار می‌شود. ثبت‌نام آغاز شد!',     'info', 0, 'event',        1,    '2025-03-15 08:00:00'),
(8, 'رویداد جدید: مسابقه شنا',       'مسابقه شنا جام ویو کلاب در ۲۵ فروردین برگزار می‌شود. ثبت‌نام آغاز شد!',     'info', 0, 'event',        1,    '2025-03-15 08:00:00'),
(9, 'رویداد جدید: مسابقه شنا',       'مسابقه شنا جام ویو کلاب در ۲۵ فروردین برگزار می‌شود. ثبت‌نام آغاز شد!',     'info', 0, 'event',        1,    '2025-03-15 08:00:00'),
(10,'رویداد جدید: مسابقه شنا',       'مسابقه شنا جام ویو کلاب در ۲۵ فروردین برگزار می‌شود. ثبت‌نام آغاز شد!',     'info', 0, 'event',        1,    '2025-03-15 08:00:00'),
(11,'رویداد جدید: مسابقه شنا',       'مسابقه شنا جام ویو کلاب در ۲۵ فروردین برگزار می‌شود. ثبت‌نام آغاز شد!',     'info', 0, 'event',        1,    '2025-03-15 08:00:00'),
(12,'رویداد جدید: مسابقه شنا',       'مسابقه شنا جام ویو کلاب در ۲۵ فروردین برگزار می‌شود. ثبت‌نام آغاز شد!',     'info', 0, 'event',        1,    '2025-03-15 08:00:00');

-- ============================================================
-- 19. لاگ فعالیت‌ها (activity_logs)
-- ============================================================
INSERT INTO `activity_logs` (`user_id`, `action`, `module`, `record_id`, `description`, `ip_address`, `created_at`) VALUES
(1, 'login',    'auth',       NULL, 'ورود مدیر سیستم به پنل',                     '127.0.0.1', '2025-03-20 08:00:00'),
(2, 'login',    'auth',       NULL, 'ورود مدیر باشگاه به پنل',                    '127.0.0.1', '2025-03-20 08:15:00'),
(3, 'login',    'auth',       NULL, 'ورود پذیرش به پنل',                           '127.0.0.1', '2025-03-20 08:30:00'),
(1, 'create',   'members',    8,    'ثبت عضو جدید: نرگس حیدری (در انتظار تأیید)', '127.0.0.1', '2025-03-20 08:45:00'),
(2, 'create',   'classes',    8,    'ایجاد کلاس جدید: ایروبیک صبحگاهی',           '127.0.0.1', '2025-03-19 14:00:00'),
(3, 'create',   'payments',   12,   'ثبت پرداخت حق بیمه - بیمه پارسیان',          '127.0.0.1', '2025-03-01 09:40:00'),
(1, 'update',   'settings',   1,    'بروزرسانی تنظیمات سیستم: نام باشگاه',        '127.0.0.1', '2025-03-18 16:00:00'),
(4, 'login',    'auth',       NULL, 'ورود حسابدار به پنل',                        '127.0.0.1', '2025-03-20 09:00:00'),
(4, 'view',     'reports',    NULL, 'مشاهده گزارش مالی ماهانه',                   '127.0.0.1', '2025-03-20 09:10:00'),
(2, 'reply',    'tickets',    4,    'پاسخ به تیکت: گزارش خرابی تردمیل',           '127.0.0.1', '2025-03-18 09:30:00'),
(1, 'create',   'coaches',    5,    'ثبت مربی جدید: کامران بهزادی',               '127.0.0.1', '2025-02-28 10:00:00'),
(3, 'reply',    'tickets',    1,    'پاسخ به تیکت: سوال درباره تمدید اشتراک',     '127.0.0.1', '2025-03-10 10:30:00'),
(2, 'create',   'events',     5,    'ایجاد رویداد: کلاس رایگان زومبا',            '127.0.0.1', '2025-03-15 11:00:00'),
(1, 'approve',  'members',    6,    'تأیید عضویت: مریم جعفری',                    '127.0.0.1', '2025-03-01 09:25:00'),
(3, 'create',   'insurance',  7,    'ثبت بیمه‌نامه: سعید عباسی - بیمه کوثر',      '127.0.0.1', '2025-03-05 10:15:00');

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- پایان داده‌های دمو
-- ============================================================
-- حساب‌های کاربری تستی:
--   مدیر سیستم:   admin / admin123
--   مدیر باشگاه:  manager / 123456
--   پذیرش:        reception / 123456
--   حسابدار:      accountant / 123456
--   عضو ۱:        member1 / 123456
--   عضو ۲:        member2 / 123456
--   عضو ۳:        member3 / 123456
--   عضو ۴:        member4 / 123456
--   عضو ۵:        member5 / 123456
--   عضو ۶:        member6 / 123456
--   عضو ۷:        member7 / 123456
--   عضو ۸:        member8 / 123456
-- ============================================================