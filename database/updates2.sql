-- ============================================================
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
    ADD CONSTRAINT `fk_mi_company` FOREIGN KEY (`company_id`) REFERENCES `insurance_companies` (`id`) ON DELETE SET NULL;