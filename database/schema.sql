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

SET FOREIGN_KEY_CHECKS = 1;