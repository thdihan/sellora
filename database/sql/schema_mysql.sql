-- Sellora MySQL Database Schema
-- Generated on 2025-09-26 12:15:23
-- Compatible with MySQL 5.7+ and MariaDB 10.3+

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = '+00:00';

-- Table: approvals
DROP TABLE IF EXISTS `approvals`;
CREATE TABLE `approvals` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `entity_type` VARCHAR(255) NOT NULL,
  `entity_id` BIGINT UNSIGNED NOT NULL,
  `from_role` VARCHAR(255),
  `to_role` VARCHAR(255),
  `action` VARCHAR(255) NOT NULL,
  `remarks` VARCHAR(255),
  `acted_by` INT,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: assessment_attempts
DROP TABLE IF EXISTS `assessment_attempts`;
CREATE TABLE `assessment_attempts` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `assessment_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `answers` VARCHAR(255),
  `score` DECIMAL(10,2),
  `status` VARCHAR(255) DEFAULT 'in_progress',
  `started_at` VARCHAR(255),
  `completed_at` VARCHAR(255),
  `duration` INT,
  `ip_address` VARCHAR(255),
  `user_agent` VARCHAR(255),
  `notes` VARCHAR(255),
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: assessment_results
DROP TABLE IF EXISTS `assessment_results`;
CREATE TABLE `assessment_results` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `assessment_attempt_id` BIGINT UNSIGNED NOT NULL,
  `question_index` INT NOT NULL,
  `question_text` VARCHAR(255) NOT NULL,
  `user_answer` VARCHAR(255),
  `correct_answer` VARCHAR(255),
  `is_correct` VARCHAR(255) DEFAULT '0',
  `points_earned` DECIMAL(10,2) DEFAULT 0,
  `max_points` DECIMAL(10,2) DEFAULT 0,
  `time_spent` INT,
  `feedback` VARCHAR(255),
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: assessments
DROP TABLE IF EXISTS `assessments`;
CREATE TABLE `assessments` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `category` VARCHAR(255),
  `type` VARCHAR(255) DEFAULT 'quiz',
  `questions` VARCHAR(255),
  `scoring_method` VARCHAR(255) DEFAULT 'points',
  `max_score` INT DEFAULT 100,
  `passing_score` INT DEFAULT 60,
  `time_limit` INT,
  `attempts_allowed` INT DEFAULT 1,
  `is_active` VARCHAR(255) DEFAULT '1',
  `start_date` VARCHAR(255),
  `end_date` VARCHAR(255),
  `instructions` VARCHAR(255),
  `tags` VARCHAR(255),
  `difficulty_level` VARCHAR(255) DEFAULT 'medium',
  `estimated_duration` INT,
  `auto_grade` VARCHAR(255) DEFAULT '1',
  `show_results_immediately` VARCHAR(255) DEFAULT '1',
  `randomize_questions` VARCHAR(255) DEFAULT '0',
  `allow_review` VARCHAR(255) DEFAULT '1',
  `certificate_template` VARCHAR(255),
  `completion_message` VARCHAR(255),
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  `deleted_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: audit_logs
DROP TABLE IF EXISTS `audit_logs`;
CREATE TABLE `audit_logs` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `actor_id` BIGINT UNSIGNED,
  `action` VARCHAR(255) NOT NULL,
  `entity_type` VARCHAR(255) NOT NULL,
  `entity_id` BIGINT UNSIGNED,
  `metadata` VARCHAR(255),
  `ip_address` VARCHAR(255),
  `user_agent` VARCHAR(255),
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: bill_files
DROP TABLE IF EXISTS `bill_files`;
CREATE TABLE `bill_files` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `bill_id` BIGINT UNSIGNED NOT NULL,
  `original_name` VARCHAR(255) NOT NULL,
  `file_path` VARCHAR(255) NOT NULL,
  `file_type` VARCHAR(255),
  `file_size` INT,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: bills
DROP TABLE IF EXISTS `bills`;
CREATE TABLE `bills` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `purpose` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `vendor` VARCHAR(255),
  `receipt_number` VARCHAR(255),
  `expense_date` VARCHAR(255),
  `category` VARCHAR(255),
  `payment_method` VARCHAR(255),
  `priority` VARCHAR(255) DEFAULT 'Medium',
  `notes` VARCHAR(255),
  `status` VARCHAR(255) DEFAULT 'Pending',
  `approved_by` INT,
  `approved_at` VARCHAR(255),
  `rejected_reason` VARCHAR(255),
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  `deleted_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: budget_expenses
DROP TABLE IF EXISTS `budget_expenses`;
CREATE TABLE `budget_expenses` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `budget_id` BIGINT UNSIGNED NOT NULL,
  `bill_id` BIGINT UNSIGNED,
  `expense_id` BIGINT UNSIGNED,
  `amount` DECIMAL(10,2) NOT NULL,
  `category` VARCHAR(255),
  `description` TEXT,
  `allocated_at` VARCHAR(255) DEFAULT CURRENT_TIMESTAMP,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: budget_items
DROP TABLE IF EXISTS `budget_items`;
CREATE TABLE `budget_items` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `budget_id` BIGINT UNSIGNED NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `category` VARCHAR(255) NOT NULL,
  `allocated_amount` DECIMAL(10,2) NOT NULL,
  `spent_amount` DECIMAL(10,2) DEFAULT 0,
  `remaining_amount` DECIMAL(10,2) DEFAULT 0,
  `sort_order` INT DEFAULT 0,
  `is_active` VARCHAR(255) DEFAULT '1',
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: budgets
DROP TABLE IF EXISTS `budgets`;
CREATE TABLE `budgets` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `period_type` VARCHAR(255) NOT NULL,
  `start_date` VARCHAR(255) NOT NULL,
  `end_date` VARCHAR(255) NOT NULL,
  `total_amount` DECIMAL(10,2) NOT NULL,
  `allocated_amount` DECIMAL(10,2) DEFAULT 0,
  `spent_amount` DECIMAL(10,2) DEFAULT 0,
  `remaining_amount` DECIMAL(10,2) DEFAULT 0,
  `status` VARCHAR(255) DEFAULT 'draft',
  `created_by` INT NOT NULL,
  `approved_by` INT,
  `approved_at` VARCHAR(255),
  `notes` VARCHAR(255),
  `categories` VARCHAR(255),
  `currency` VARCHAR(255) DEFAULT 'USD',
  `auto_approve_limit` DECIMAL(10,2),
  `notification_threshold` DECIMAL(10,2) DEFAULT 80,
  `is_recurring` VARCHAR(255) DEFAULT '0',
  `recurring_frequency` VARCHAR(255),
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: cache
DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache` (
  `key` VARCHAR(255),
  `value` VARCHAR(255) NOT NULL,
  `expiration` INT NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: cache_locks
DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks` (
  `key` VARCHAR(255),
  `owner` VARCHAR(255) NOT NULL,
  `expiration` INT NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: customers
DROP TABLE IF EXISTS `customers`;
CREATE TABLE `customers` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `shop_name` VARCHAR(255),
  `full_address` VARCHAR(255),
  `phone` VARCHAR(255) NOT NULL,
  `email` VARCHAR(191),
  `notes` VARCHAR(255),
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: email_queue
DROP TABLE IF EXISTS `email_queue`;
CREATE TABLE `email_queue` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `to_email` VARCHAR(191) NOT NULL,
  `to_user_id` BIGINT UNSIGNED,
  `subject` VARCHAR(255) NOT NULL,
  `body` TEXT NOT NULL,
  `template_slug` VARCHAR(255),
  `data_json` VARCHAR(255),
  `scheduled_at` VARCHAR(255) NOT NULL,
  `sent_at` VARCHAR(255),
  `status` VARCHAR(255) DEFAULT 'queued',
  `error` VARCHAR(255),
  `attempts` INT DEFAULT 0,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: email_templates
DROP TABLE IF EXISTS `email_templates`;
CREATE TABLE `email_templates` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `slug` VARCHAR(255) NOT NULL,
  `subject_tpl` VARCHAR(255) NOT NULL,
  `body_tpl` TEXT NOT NULL,
  `description` VARCHAR(255),
  `enabled` VARCHAR(255) DEFAULT '1',
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: events
DROP TABLE IF EXISTS `events`;
CREATE TABLE `events` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `event_type` VARCHAR(255) DEFAULT 'meeting',
  `start_date` VARCHAR(255) NOT NULL,
  `end_date` VARCHAR(255) NOT NULL,
  `start_time` VARCHAR(255),
  `end_time` VARCHAR(255),
  `location` VARCHAR(255),
  `is_all_day` VARCHAR(255) DEFAULT '0',
  `priority` VARCHAR(255) DEFAULT 'medium',
  `status` VARCHAR(255) DEFAULT 'scheduled',
  `color` VARCHAR(255),
  `reminder_minutes` INT,
  `attendees` VARCHAR(255),
  `notes` VARCHAR(255),
  `created_by` INT NOT NULL,
  `recurring_type` VARCHAR(255) DEFAULT 'none',
  `recurring_end_date` VARCHAR(255),
  `recurring_days` VARCHAR(255),
  `attachments` VARCHAR(255),
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: expenses
DROP TABLE IF EXISTS `expenses`;
CREATE TABLE `expenses` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `category` VARCHAR(255) NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `currency` VARCHAR(255) DEFAULT 'USD',
  `expense_date` VARCHAR(255) NOT NULL,
  `receipt_number` VARCHAR(255),
  `vendor` VARCHAR(255),
  `status` VARCHAR(255) DEFAULT 'pending',
  `priority` VARCHAR(255) DEFAULT 'medium',
  `approved_by` INT,
  `approved_at` VARCHAR(255),
  `approval_notes` VARCHAR(255),
  `rejection_reason` VARCHAR(255),
  `attachments` VARCHAR(255),
  `notes` VARCHAR(255),
  `is_reimbursable` VARCHAR(255) DEFAULT '1',
  `tax_amount` DECIMAL(10,2),
  `payment_method` VARCHAR(255),
  `reference_number` VARCHAR(255),
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: export_jobs
DROP TABLE IF EXISTS `export_jobs`;
CREATE TABLE `export_jobs` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `scope` VARCHAR(255) NOT NULL,
  `modules` VARCHAR(255) NOT NULL,
  `format` VARCHAR(255) NOT NULL,
  `filters` VARCHAR(255),
  `include_dependencies` VARCHAR(255) DEFAULT '0',
  `status` VARCHAR(255) DEFAULT 'pending',
  `created_by` INT NOT NULL,
  `stats` VARCHAR(255),
  `file_path` VARCHAR(255),
  `error_message` VARCHAR(255),
  `started_at` VARCHAR(255),
  `completed_at` VARCHAR(255),
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: external_maps
DROP TABLE IF EXISTS `external_maps`;
CREATE TABLE `external_maps` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `module` VARCHAR(255) NOT NULL,
  `external_id` VARCHAR(255) NOT NULL,
  `local_id` BIGINT UNSIGNED NOT NULL,
  `source` VARCHAR(255) NOT NULL,
  `metadata` VARCHAR(255),
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: external_product_map
DROP TABLE IF EXISTS `external_product_map`;
CREATE TABLE `external_product_map` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `product_id` BIGINT UNSIGNED NOT NULL,
  `external_system` VARCHAR(255) NOT NULL,
  `external_id` VARCHAR(255) NOT NULL,
  `external_sku` VARCHAR(255),
  `external_url` VARCHAR(255),
  `external_data` VARCHAR(255),
  `field_mapping` VARCHAR(255),
  `sync_direction` VARCHAR(255) DEFAULT 'import',
  `auto_sync` VARCHAR(255) DEFAULT '1',
  `last_synced_at` VARCHAR(255),
  `last_sync_attempt_at` VARCHAR(255),
  `sync_status` VARCHAR(255) DEFAULT 'pending',
  `sync_error` VARCHAR(255),
  `sync_attempts` INT DEFAULT 0,
  `is_active` VARCHAR(255) DEFAULT '1',
  `notes` VARCHAR(255),
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: failed_jobs
DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `uuid` VARCHAR(255) NOT NULL,
  `connection` VARCHAR(255) NOT NULL,
  `queue` VARCHAR(255) NOT NULL,
  `payload` VARCHAR(255) NOT NULL,
  `exception` VARCHAR(255) NOT NULL,
  `failed_at` VARCHAR(255) DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: import_items
DROP TABLE IF EXISTS `import_items`;
CREATE TABLE `import_items` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `job_id` BIGINT UNSIGNED NOT NULL,
  `module` VARCHAR(255) NOT NULL,
  `source_row_no` INT,
  `payload` VARCHAR(255) NOT NULL,
  `status` VARCHAR(255) DEFAULT 'pending',
  `error_message` VARCHAR(255),
  `entity_id` BIGINT UNSIGNED,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: import_jobs
DROP TABLE IF EXISTS `import_jobs`;
CREATE TABLE `import_jobs` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `source_type` VARCHAR(255) NOT NULL,
  `modules` VARCHAR(255) NOT NULL,
  `status` VARCHAR(255) DEFAULT 'pending',
  `created_by` INT NOT NULL,
  `stats` VARCHAR(255),
  `file_path` VARCHAR(255),
  `config` VARCHAR(255),
  `error_message` VARCHAR(255),
  `started_at` VARCHAR(255),
  `completed_at` VARCHAR(255),
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: import_presets
DROP TABLE IF EXISTS `import_presets`;
CREATE TABLE `import_presets` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `source_type` VARCHAR(255) NOT NULL,
  `module` VARCHAR(255) NOT NULL,
  `column_map` VARCHAR(255) NOT NULL,
  `options` VARCHAR(255),
  `created_by` INT NOT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: job_batches
DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE `job_batches` (
  `id` VARCHAR(255),
  `name` VARCHAR(255) NOT NULL,
  `total_jobs` INT NOT NULL,
  `pending_jobs` INT NOT NULL,
  `failed_jobs` INT NOT NULL,
  `failed_job_ids` VARCHAR(255) NOT NULL,
  `options` VARCHAR(255),
  `cancelled_at` INT,
  `created_at` TIMESTAMP NULL NOT NULL,
  `finished_at` INT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: jobs
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `queue` VARCHAR(255) NOT NULL,
  `payload` VARCHAR(255) NOT NULL,
  `attempts` INT NOT NULL,
  `reserved_at` INT,
  `available_at` INT NOT NULL,
  `created_at` TIMESTAMP NULL NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: location_tracking
DROP TABLE IF EXISTS `location_tracking`;
CREATE TABLE `location_tracking` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `latitude` DECIMAL(10,2) NOT NULL,
  `longitude` DECIMAL(10,2) NOT NULL,
  `accuracy` DECIMAL(10,2),
  `captured_at` VARCHAR(255) DEFAULT CURRENT_TIMESTAMP,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: location_visits
DROP TABLE IF EXISTS `location_visits`;
CREATE TABLE `location_visits` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `location_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `visited_at` VARCHAR(255) NOT NULL,
  `left_at` VARCHAR(255),
  `duration_minutes` INT,
  `purpose` VARCHAR(255),
  `notes` VARCHAR(255),
  `check_in_method` VARCHAR(255) DEFAULT 'manual',
  `check_out_method` VARCHAR(255),
  `latitude` DECIMAL(10,2),
  `longitude` DECIMAL(10,2),
  `accuracy` DECIMAL(10,2),
  `weather` VARCHAR(255),
  `temperature` DECIMAL(10,2),
  `mood_rating` INT,
  `productivity_rating` INT,
  `photos` VARCHAR(255),
  `tags` VARCHAR(255),
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: locations
DROP TABLE IF EXISTS `locations`;
CREATE TABLE `locations` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `latitude` DECIMAL(10,2) NOT NULL,
  `longitude` DECIMAL(10,2) NOT NULL,
  `address` VARCHAR(255),
  `city` VARCHAR(255),
  `state` VARCHAR(255),
  `country` VARCHAR(255),
  `postal_code` VARCHAR(255),
  `type` VARCHAR(255),
  `status` VARCHAR(255) DEFAULT 'active',
  `accuracy` DECIMAL(10,2),
  `altitude` DECIMAL(10,2),
  `speed` DECIMAL(10,2),
  `heading` DECIMAL(10,2),
  `timestamp` VARCHAR(255),
  `ip_address` VARCHAR(255),
  `user_agent` VARCHAR(255),
  `notes` VARCHAR(255),
  `is_favorite` VARCHAR(255) DEFAULT '0',
  `visit_count` INT DEFAULT 0,
  `last_visited_at` VARCHAR(255),
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: media
DROP TABLE IF EXISTS `media`;
CREATE TABLE `media` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `mediable_type` VARCHAR(255) NOT NULL,
  `mediable_id` BIGINT UNSIGNED NOT NULL,
  `collection_name` VARCHAR(255) DEFAULT 'default',
  `name` VARCHAR(255) NOT NULL,
  `file_name` VARCHAR(255) NOT NULL,
  `mime_type` VARCHAR(255),
  `disk` VARCHAR(255) DEFAULT 'public',
  `conversions_disk` VARCHAR(255) DEFAULT 'public',
  `size` INT NOT NULL,
  `manipulations` VARCHAR(255) NOT NULL,
  `custom_properties` VARCHAR(255) NOT NULL,
  `generated_conversions` VARCHAR(255) NOT NULL,
  `responsive_images` VARCHAR(255) NOT NULL,
  `order_column` INT,
  `alt_text` VARCHAR(255),
  `description` TEXT,
  `caption` VARCHAR(255),
  `is_primary` VARCHAR(255) DEFAULT '0',
  `is_active` VARCHAR(255) DEFAULT '1',
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: migrations
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `migration` VARCHAR(255) NOT NULL,
  `batch` INT NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: notifications
DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `id` VARCHAR(255),
  `type` VARCHAR(255) NOT NULL,
  `notifiable_type` VARCHAR(255) NOT NULL,
  `notifiable_id` BIGINT UNSIGNED NOT NULL,
  `data` VARCHAR(255) NOT NULL,
  `read_at` VARCHAR(255),
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: order_files
DROP TABLE IF EXISTS `order_files`;
CREATE TABLE `order_files` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `order_id` BIGINT UNSIGNED NOT NULL,
  `original_name` VARCHAR(255) NOT NULL,
  `file_path` VARCHAR(255) NOT NULL,
  `file_type` VARCHAR(255) NOT NULL,
  `file_size` INT NOT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: order_items
DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `order_id` BIGINT UNSIGNED NOT NULL,
  `product_id` BIGINT UNSIGNED NOT NULL,
  `quantity` INT NOT NULL,
  `unit_price` DECIMAL(10,2) NOT NULL,
  `total_price` DECIMAL(10,2) NOT NULL,
  `notes` VARCHAR(255),
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: order_tax_lines
DROP TABLE IF EXISTS `order_tax_lines`;
CREATE TABLE `order_tax_lines` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `order_id` BIGINT UNSIGNED NOT NULL,
  `tax_head_id` BIGINT UNSIGNED NOT NULL,
  `base_amount` DECIMAL(10,2) NOT NULL,
  `rate` DECIMAL(10,2) NOT NULL,
  `calculated_amount` DECIMAL(10,2) NOT NULL,
  `payer` VARCHAR(255) NOT NULL,
  `visible` VARCHAR(255) DEFAULT '1',
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: orders
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `customer_name` VARCHAR(255) NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `status` VARCHAR(255) DEFAULT 'Pending',
  `notes` VARCHAR(255),
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  `tax_breakdown` VARCHAR(255),
  `total_amount` DECIMAL(10,2),
  `order_number` VARCHAR(255),
  `vat_condition` VARCHAR(255) DEFAULT 'client_bears',
  `tax_condition` VARCHAR(255) DEFAULT 'client_bears',
  `vat_amount` DECIMAL(10,2) DEFAULT 0,
  `tax_amount` DECIMAL(10,2) DEFAULT 0,
  `net_revenue` DECIMAL(10,2),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: password_reset_tokens
DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens` (
  `email` VARCHAR(191),
  `token` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: presentation_comments
DROP TABLE IF EXISTS `presentation_comments`;
CREATE TABLE `presentation_comments` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `presentation_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `content` TEXT NOT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: presentation_downloads
DROP TABLE IF EXISTS `presentation_downloads`;
CREATE TABLE `presentation_downloads` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `presentation_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED,
  `ip_address` VARCHAR(255),
  `user_agent` VARCHAR(255),
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: presentation_shares
DROP TABLE IF EXISTS `presentation_shares`;
CREATE TABLE `presentation_shares` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `presentation_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `permission` VARCHAR(255) DEFAULT 'view',
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: presentation_views
DROP TABLE IF EXISTS `presentation_views`;
CREATE TABLE `presentation_views` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `presentation_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED,
  `ip_address` VARCHAR(255),
  `user_agent` VARCHAR(255),
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: presentations
DROP TABLE IF EXISTS `presentations`;
CREATE TABLE `presentations` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `file_path` VARCHAR(255) NOT NULL,
  `file_name` VARCHAR(255) NOT NULL,
  `file_size` INT NOT NULL,
  `file_type` VARCHAR(255) NOT NULL,
  `category` VARCHAR(255),
  `tags` VARCHAR(255),
  `status` VARCHAR(255) DEFAULT 'draft',
  `privacy_level` VARCHAR(255) DEFAULT 'private',
  `is_template` VARCHAR(255) DEFAULT '0',
  `view_count` INT DEFAULT 0,
  `download_count` INT DEFAULT 0,
  `version` VARCHAR(255) DEFAULT '1.0',
  `original_presentation_id` BIGINT UNSIGNED,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  `deleted_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: product_batches
DROP TABLE IF EXISTS `product_batches`;
CREATE TABLE `product_batches` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `product_id` BIGINT UNSIGNED NOT NULL,
  `batch_no` VARCHAR(255) NOT NULL,
  `mfg_date` VARCHAR(255),
  `exp_date` VARCHAR(255),
  `mrp` DECIMAL(10,2),
  `purchase_price` DECIMAL(10,2),
  `barcode` VARCHAR(255),
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: product_brands
DROP TABLE IF EXISTS `product_brands`;
CREATE TABLE `product_brands` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `description` VARCHAR(255),
  `status` VARCHAR(255) DEFAULT '1',
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: product_categories
DROP TABLE IF EXISTS `product_categories`;
CREATE TABLE `product_categories` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `description` VARCHAR(255),
  `status` VARCHAR(255) DEFAULT '1',
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: product_files
DROP TABLE IF EXISTS `product_files`;
CREATE TABLE `product_files` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `product_id` BIGINT UNSIGNED NOT NULL,
  `file_path` VARCHAR(255) NOT NULL,
  `file_type` VARCHAR(255) NOT NULL,
  `original_name` VARCHAR(255) NOT NULL,
  `file_size` INT,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: product_prices
DROP TABLE IF EXISTS `product_prices`;
CREATE TABLE `product_prices` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `product_id` BIGINT UNSIGNED NOT NULL,
  `price_type` VARCHAR(255) NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `cost_price` DECIMAL(10,2),
  `min_quantity` INT DEFAULT 1,
  `max_quantity` INT,
  `currency` VARCHAR(255) DEFAULT 'USD',
  `customer_id` BIGINT UNSIGNED,
  `customer_group` VARCHAR(255),
  `valid_from` VARCHAR(255),
  `valid_to` VARCHAR(255),
  `is_active` VARCHAR(255) DEFAULT '1',
  `notes` VARCHAR(255),
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: product_units
DROP TABLE IF EXISTS `product_units`;
CREATE TABLE `product_units` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `symbol` VARCHAR(255) NOT NULL,
  `status` VARCHAR(255) DEFAULT '1',
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: products
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `generic_name` VARCHAR(255),
  `composition` VARCHAR(255),
  `dosage_form` VARCHAR(255),
  `strength` VARCHAR(255),
  `sku` VARCHAR(255) NOT NULL,
  `barcode` VARCHAR(255),
  `hsn` VARCHAR(255),
  `schedule` VARCHAR(255),
  `storage_conditions` VARCHAR(255),
  `category_id` BIGINT UNSIGNED,
  `brand_id` BIGINT UNSIGNED,
  `unit_id` BIGINT UNSIGNED,
  `pack_size` VARCHAR(255),
  `purchase_price` DECIMAL(10,2) DEFAULT 0,
  `selling_price` DECIMAL(10,2) DEFAULT 0,
  `tax_rate` DECIMAL(10,2) DEFAULT 0,
  `reorder_level` INT DEFAULT 0,
  `reorder_qty` INT DEFAULT 0,
  `allow_negative` VARCHAR(255) DEFAULT '0',
  `status` VARCHAR(255) DEFAULT '1',
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  `warehouse_id` BIGINT UNSIGNED,
  `description` TEXT,
  `weight` DECIMAL(10,2),
  `dimensions` VARCHAR(255),
  `tax_code` VARCHAR(255),
  `is_taxable` VARCHAR(255) DEFAULT '0',
  `meta_data` VARCHAR(255),
  `price` DECIMAL(10,2),
  `stock` INT DEFAULT 0,
  `expiration_date` VARCHAR(255),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: reports
DROP TABLE IF EXISTS `reports`;
CREATE TABLE `reports` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `type` VARCHAR(255) NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `filters` VARCHAR(255),
  `format` VARCHAR(255) NOT NULL,
  `file_path` VARCHAR(255),
  `file_size` INT,
  `generated_at` VARCHAR(255) NOT NULL,
  `expires_at` VARCHAR(255),
  `status` VARCHAR(255) DEFAULT 'pending',
  `error_message` VARCHAR(255),
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: roles
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: sales_targets
DROP TABLE IF EXISTS `sales_targets`;
CREATE TABLE `sales_targets` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `assigned_by_user_id` BIGINT UNSIGNED NOT NULL,
  `assigned_to_user_id` BIGINT UNSIGNED NOT NULL,
  `target_year` INT NOT NULL,
  `week_1_target` DECIMAL(10,2) DEFAULT 0,
  `week_2_target` DECIMAL(10,2) DEFAULT 0,
  `week_3_target` DECIMAL(10,2) DEFAULT 0,
  `week_4_target` DECIMAL(10,2) DEFAULT 0,
  `january_target` DECIMAL(10,2) DEFAULT 0,
  `february_target` DECIMAL(10,2) DEFAULT 0,
  `march_target` DECIMAL(10,2) DEFAULT 0,
  `april_target` DECIMAL(10,2) DEFAULT 0,
  `may_target` DECIMAL(10,2) DEFAULT 0,
  `june_target` DECIMAL(10,2) DEFAULT 0,
  `july_target` DECIMAL(10,2) DEFAULT 0,
  `august_target` DECIMAL(10,2) DEFAULT 0,
  `september_target` DECIMAL(10,2) DEFAULT 0,
  `october_target` DECIMAL(10,2) DEFAULT 0,
  `november_target` DECIMAL(10,2) DEFAULT 0,
  `december_target` DECIMAL(10,2) DEFAULT 0,
  `total_yearly_target` DECIMAL(10,2) DEFAULT 0,
  `status` VARCHAR(255) DEFAULT 'active',
  `notes` VARCHAR(255),
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: self_assessments
DROP TABLE IF EXISTS `self_assessments`;
CREATE TABLE `self_assessments` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `period` VARCHAR(255) NOT NULL,
  `targets` VARCHAR(255) NOT NULL,
  `achievements` VARCHAR(255) NOT NULL,
  `problems` VARCHAR(255),
  `solutions` VARCHAR(255),
  `market_analysis` VARCHAR(255),
  `status` VARCHAR(255) DEFAULT 'draft',
  `submitted_at` VARCHAR(255),
  `reviewed_at` VARCHAR(255),
  `reviewed_by` INT,
  `reviewer_comments` VARCHAR(255),
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: sessions
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` VARCHAR(255),
  `user_id` BIGINT UNSIGNED,
  `ip_address` VARCHAR(255),
  `user_agent` VARCHAR(255),
  `payload` VARCHAR(255) NOT NULL,
  `last_activity` INT NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: settings
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `type` VARCHAR(255) NOT NULL,
  `key_name` VARCHAR(255) NOT NULL,
  `value` VARCHAR(255),
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  `is_locked` VARCHAR(255) DEFAULT '0',
  `locked_by_role` VARCHAR(255),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: stock_balances
DROP TABLE IF EXISTS `stock_balances`;
CREATE TABLE `stock_balances` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `product_id` BIGINT UNSIGNED NOT NULL,
  `warehouse_id` BIGINT UNSIGNED NOT NULL,
  `batch_id` BIGINT UNSIGNED,
  `qty_on_hand` INT DEFAULT 0,
  `qty_reserved` INT DEFAULT 0,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: stock_transactions
DROP TABLE IF EXISTS `stock_transactions`;
CREATE TABLE `stock_transactions` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `product_id` BIGINT UNSIGNED NOT NULL,
  `warehouse_id` BIGINT UNSIGNED NOT NULL,
  `batch_id` BIGINT UNSIGNED,
  `qty` INT NOT NULL,
  `type` VARCHAR(255) NOT NULL,
  `ref_type` VARCHAR(255),
  `ref_id` BIGINT UNSIGNED,
  `note` VARCHAR(255),
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: suppliers
DROP TABLE IF EXISTS `suppliers`;
CREATE TABLE `suppliers` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `contact_person` VARCHAR(255),
  `email` VARCHAR(191),
  `phone` VARCHAR(255),
  `address` VARCHAR(255),
  `city` VARCHAR(255),
  `state` VARCHAR(255),
  `country` VARCHAR(255),
  `postal_code` VARCHAR(255),
  `tax_number` VARCHAR(255),
  `status` VARCHAR(255) DEFAULT '1',
  `notes` VARCHAR(255),
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: sync_log
DROP TABLE IF EXISTS `sync_log`;
CREATE TABLE `sync_log` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `sync_type` VARCHAR(255) NOT NULL,
  `external_system` VARCHAR(255) NOT NULL,
  `operation` VARCHAR(255) NOT NULL,
  `syncable_type` VARCHAR(255) NOT NULL,
  `syncable_id` BIGINT UNSIGNED NOT NULL,
  `external_id` VARCHAR(255),
  `status` VARCHAR(255) DEFAULT 'pending',
  `started_at` VARCHAR(255),
  `completed_at` VARCHAR(255),
  `duration_ms` INT,
  `request_data` VARCHAR(255),
  `response_data` VARCHAR(255),
  `changes_made` VARCHAR(255),
  `error_message` VARCHAR(255),
  `error_details` VARCHAR(255),
  `retry_count` INT DEFAULT 0,
  `next_retry_at` VARCHAR(255),
  `batch_id` VARCHAR(255),
  `user_id` VARCHAR(255),
  `ip_address` VARCHAR(255),
  `notes` VARCHAR(255),
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: tax_codes
DROP TABLE IF EXISTS `tax_codes`;
CREATE TABLE `tax_codes` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `code` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `is_active` VARCHAR(255) DEFAULT '1',
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: tax_heads
DROP TABLE IF EXISTS `tax_heads`;
CREATE TABLE `tax_heads` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `code` VARCHAR(255) NOT NULL,
  `kind` VARCHAR(255) NOT NULL,
  `percentage` DECIMAL(10,2) NOT NULL,
  `visible_to_client` VARCHAR(255) DEFAULT '1',
  `created_by` INT NOT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: tax_rates
DROP TABLE IF EXISTS `tax_rates`;
CREATE TABLE `tax_rates` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `tax_code_id` BIGINT UNSIGNED NOT NULL,
  `label` VARCHAR(255) NOT NULL,
  `percent` DECIMAL(10,2) NOT NULL,
  `country` VARCHAR(255),
  `region` VARCHAR(255),
  `effective_from` VARCHAR(255) NOT NULL,
  `effective_to` VARCHAR(255),
  `is_default` VARCHAR(255) DEFAULT '0',
  `is_active` VARCHAR(255) DEFAULT '1',
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: tax_rules
DROP TABLE IF EXISTS `tax_rules`;
CREATE TABLE `tax_rules` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `tax_rate_id` BIGINT UNSIGNED NOT NULL,
  `applies_to` VARCHAR(255) DEFAULT 'all',
  `category_id` BIGINT UNSIGNED,
  `product_id` BIGINT UNSIGNED,
  `price_mode` VARCHAR(255) DEFAULT 'EXCLUSIVE',
  `bearer` VARCHAR(255) DEFAULT 'CUSTOMER',
  `reverse_charge` VARCHAR(255) DEFAULT '0',
  `zero_rated` VARCHAR(255) DEFAULT '0',
  `exempt` VARCHAR(255) DEFAULT '0',
  `withholding` VARCHAR(255) DEFAULT '0',
  `withholding_percent` DECIMAL(10,2),
  `taxable_discounts` VARCHAR(255) DEFAULT 'NONE',
  `taxable_shipping` VARCHAR(255) DEFAULT '1',
  `place_of_supply` VARCHAR(255) DEFAULT 'ORIGIN',
  `rounding` VARCHAR(255) DEFAULT 'LINE',
  `priority` INT DEFAULT 0,
  `is_active` VARCHAR(255) DEFAULT '1',
  `comments` VARCHAR(255),
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: users
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(191) NOT NULL,
  `email_verified_at` VARCHAR(191),
  `password` VARCHAR(255) NOT NULL,
  `role_id` BIGINT UNSIGNED,
  `designation` VARCHAR(255),
  `photo` VARCHAR(255),
  `remember_token` VARCHAR(255),
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  `notify_email` VARCHAR(191) DEFAULT '1',
  `employee_id` VARCHAR(255),
  `bio` VARCHAR(255),
  `timezone` VARCHAR(255) DEFAULT 'UTC',
  `email_notifications` VARCHAR(191) DEFAULT '1',
  `sms_notifications` VARCHAR(255) DEFAULT '0',
  `marketing_emails` VARCHAR(191) DEFAULT '0',
  `security_alerts` VARCHAR(255) DEFAULT '1',
  `last_login_at` VARCHAR(255),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: visits
DROP TABLE IF EXISTS `visits`;
CREATE TABLE `visits` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `customer_name` VARCHAR(255) NOT NULL,
  `customer_phone` VARCHAR(255),
  `customer_email` VARCHAR(191),
  `customer_address` VARCHAR(255) NOT NULL,
  `visit_type` VARCHAR(255) DEFAULT 'sales',
  `purpose` VARCHAR(255),
  `scheduled_at` VARCHAR(255) NOT NULL,
  `actual_start_time` VARCHAR(255),
  `actual_end_time` VARCHAR(255),
  `status` VARCHAR(255) DEFAULT 'scheduled',
  `priority` VARCHAR(255) DEFAULT 'medium',
  `notes` VARCHAR(255),
  `outcome` VARCHAR(255),
  `latitude` DECIMAL(10,2),
  `longitude` DECIMAL(10,2),
  `location_address` VARCHAR(255),
  `attachments` VARCHAR(255),
  `estimated_duration` DECIMAL(10,2) DEFAULT 1,
  `actual_duration` DECIMAL(10,2),
  `requires_follow_up` VARCHAR(255) DEFAULT '0',
  `follow_up_date` VARCHAR(255),
  `cancellation_reason` VARCHAR(255),
  `rescheduled_from` VARCHAR(255),
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  `rescheduled_to` VARCHAR(255),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: warehouses
DROP TABLE IF EXISTS `warehouses`;
CREATE TABLE `warehouses` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `code` VARCHAR(255) NOT NULL,
  `address` VARCHAR(255),
  `status` VARCHAR(255) DEFAULT '1',
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  `is_main` VARCHAR(255) DEFAULT '0',
  `phone` VARCHAR(255),
  `email` VARCHAR(191),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Indexes and Foreign Keys
CREATE INDEX `approvals_acted_by_index` ON `approvals` (`acted_by`);
CREATE INDEX `approvals_entity_type_action_index` ON `approvals` (`entity_type`, `action`);
CREATE INDEX `approvals_entity_type_entity_id_index` ON `approvals` (`entity_type`, `entity_id`);
CREATE INDEX `assessment_attempts_score_index` ON `assessment_attempts` (`score`);
CREATE INDEX `assessment_attempts_status_started_at_index` ON `assessment_attempts` (`status`, `started_at`);
CREATE INDEX `assessment_attempts_assessment_id_user_id_index` ON `assessment_attempts` (`assessment_id`, `user_id`);
CREATE INDEX `assessment_results_is_correct_index` ON `assessment_results` (`is_correct`);
CREATE INDEX `assessment_results_assessment_attempt_id_question_index_index` ON `assessment_results` (`assessment_attempt_id`, `question_index`);
CREATE INDEX `audit_logs_action_index` ON `audit_logs` (`action`);
CREATE INDEX `audit_logs_actor_id_created_at_index` ON `audit_logs` (`actor_id`, `created_at`);
CREATE INDEX `audit_logs_entity_type_entity_id_index` ON `audit_logs` (`entity_type`, `entity_id`);
CREATE INDEX `bill_files_bill_id_index` ON `bill_files` (`bill_id`);
CREATE INDEX `budget_expenses_expense_id_index` ON `budget_expenses` (`expense_id`);
CREATE INDEX `budget_expenses_bill_id_index` ON `budget_expenses` (`bill_id`);
CREATE INDEX `budget_expenses_budget_id_allocated_at_index` ON `budget_expenses` (`budget_id`, `allocated_at`);
CREATE INDEX `budget_items_budget_id_sort_order_index` ON `budget_items` (`budget_id`, `sort_order`);
CREATE INDEX `budget_items_budget_id_category_index` ON `budget_items` (`budget_id`, `category`);
CREATE INDEX `budgets_period_type_start_date_index` ON `budgets` (`period_type`, `start_date`);
CREATE INDEX `budgets_status_created_at_index` ON `budgets` (`status`, `created_at`);
CREATE INDEX `customers_email_index` ON `customers` (`email`);
CREATE INDEX `customers_phone_index` ON `customers` (`phone`);
CREATE INDEX `customers_shop_name_index` ON `customers` (`shop_name`);
CREATE INDEX `customers_name_index` ON `customers` (`name`);
CREATE INDEX `email_queue_to_user_id_index` ON `email_queue` (`to_user_id`);
CREATE INDEX `email_queue_status_scheduled_at_index` ON `email_queue` (`status`, `scheduled_at`);
CREATE UNIQUE INDEX `email_templates_slug_unique` ON `email_templates` (`slug`);
CREATE INDEX `export_jobs_created_by_index` ON `export_jobs` (`created_by`);
CREATE INDEX `export_jobs_status_created_at_index` ON `export_jobs` (`status`, `created_at`);
CREATE INDEX `external_maps_source_index` ON `external_maps` (`source`);
CREATE INDEX `external_maps_module_local_id_index` ON `external_maps` (`module`, `local_id`);
CREATE UNIQUE INDEX `external_maps_module_external_id_source_unique` ON `external_maps` (`module`, `external_id`, `source`);
CREATE INDEX `external_product_map_last_synced_at_is_active_index` ON `external_product_map` (`last_synced_at`, `is_active`);
CREATE INDEX `external_product_map_sync_status_auto_sync_index` ON `external_product_map` (`sync_status`, `auto_sync`);
CREATE INDEX `external_product_map_external_system_external_id_index` ON `external_product_map` (`external_system`, `external_id`);
CREATE UNIQUE INDEX `external_product_map_product_id_external_system_external_id_unique` ON `external_product_map` (`product_id`, `external_system`, `external_id`);
CREATE UNIQUE INDEX `failed_jobs_uuid_unique` ON `failed_jobs` (`uuid`);
CREATE INDEX `import_items_module_index` ON `import_items` (`module`);
CREATE INDEX `import_items_job_id_status_index` ON `import_items` (`job_id`, `status`);
CREATE INDEX `import_jobs_created_by_index` ON `import_jobs` (`created_by`);
CREATE INDEX `import_jobs_status_created_at_index` ON `import_jobs` (`status`, `created_at`);
CREATE INDEX `import_presets_source_type_module_index` ON `import_presets` (`source_type`, `module`);
CREATE INDEX `import_presets_created_by_index` ON `import_presets` (`created_by`);
CREATE INDEX `jobs_queue_index` ON `jobs` (`queue`);
CREATE INDEX `location_tracking_user_id_captured_at_index` ON `location_tracking` (`user_id`, `captured_at`);
CREATE INDEX `location_tracking_captured_at_index` ON `location_tracking` (`captured_at`);
CREATE INDEX `location_tracking_user_id_index` ON `location_tracking` (`user_id`);
CREATE INDEX `location_visits_visited_at_left_at_index` ON `location_visits` (`visited_at`, `left_at`);
CREATE INDEX `location_visits_visited_at_index` ON `location_visits` (`visited_at`);
CREATE INDEX `location_visits_user_id_visited_at_index` ON `location_visits` (`user_id`, `visited_at`);
CREATE INDEX `location_visits_location_id_visited_at_index` ON `location_visits` (`location_id`, `visited_at`);
CREATE INDEX `locations_type_index` ON `locations` (`type`);
CREATE INDEX `locations_latitude_longitude_index` ON `locations` (`latitude`, `longitude`);
CREATE INDEX `locations_user_id_status_index` ON `locations` (`user_id`, `status`);
CREATE INDEX `media_is_primary_order_column_index` ON `media` (`is_primary`, `order_column`);
CREATE INDEX `media_collection_name_is_active_index` ON `media` (`collection_name`, `is_active`);
CREATE INDEX `media_mediable_type_mediable_id_index` ON `media` (`mediable_type`, `mediable_id`);
CREATE INDEX `notifications_notifiable_type_notifiable_id_index` ON `notifications` (`notifiable_type`, `notifiable_id`);
CREATE INDEX `order_files_order_id_index` ON `order_files` (`order_id`);
CREATE INDEX `order_items_order_id_product_id_index` ON `order_items` (`order_id`, `product_id`);
CREATE UNIQUE INDEX `orders_order_number_unique` ON `orders` (`order_number`);
CREATE INDEX `presentation_comments_user_id_created_at_index` ON `presentation_comments` (`user_id`, `created_at`);
CREATE INDEX `presentation_comments_presentation_id_created_at_index` ON `presentation_comments` (`presentation_id`, `created_at`);
CREATE INDEX `presentation_downloads_user_id_created_at_index` ON `presentation_downloads` (`user_id`, `created_at`);
CREATE INDEX `presentation_downloads_presentation_id_created_at_index` ON `presentation_downloads` (`presentation_id`, `created_at`);
CREATE INDEX `presentation_shares_user_id_created_at_index` ON `presentation_shares` (`user_id`, `created_at`);
CREATE UNIQUE INDEX `presentation_shares_presentation_id_user_id_unique` ON `presentation_shares` (`presentation_id`, `user_id`);
CREATE INDEX `presentation_views_user_id_created_at_index` ON `presentation_views` (`user_id`, `created_at`);
CREATE INDEX `presentation_views_presentation_id_created_at_index` ON `presentation_views` (`presentation_id`, `created_at`);
CREATE INDEX `presentations_is_template_index` ON `presentations` (`is_template`);
CREATE INDEX `presentations_category_status_index` ON `presentations` (`category`, `status`);
CREATE INDEX `presentations_user_id_status_index` ON `presentations` (`user_id`, `status`);
CREATE INDEX `product_batches_exp_date_product_id_index` ON `product_batches` (`exp_date`, `product_id`);
CREATE UNIQUE INDEX `product_batches_product_id_batch_no_unique` ON `product_batches` (`product_id`, `batch_no`);
CREATE UNIQUE INDEX `product_brands_name_unique` ON `product_brands` (`name`);
CREATE INDEX `product_brands_status_name_index` ON `product_brands` (`status`, `name`);
CREATE UNIQUE INDEX `product_categories_name_unique` ON `product_categories` (`name`);
CREATE INDEX `product_categories_status_name_index` ON `product_categories` (`status`, `name`);
CREATE INDEX `product_files_product_id_file_type_index` ON `product_files` (`product_id`, `file_type`);
CREATE INDEX `product_prices_min_quantity_max_quantity_index` ON `product_prices` (`min_quantity`, `max_quantity`);
CREATE INDEX `product_prices_valid_from_valid_to_index` ON `product_prices` (`valid_from`, `valid_to`);
CREATE INDEX `product_prices_customer_id_is_active_index` ON `product_prices` (`customer_id`, `is_active`);
CREATE INDEX `product_prices_product_id_price_type_is_active_index` ON `product_prices` (`product_id`, `price_type`, `is_active`);
CREATE INDEX `product_units_status_index` ON `product_units` (`status`);
CREATE UNIQUE INDEX `product_units_name_symbol_unique` ON `product_units` (`name`, `symbol`);
CREATE INDEX `products_status_category_id_index` ON `products` (`status`, `category_id`);
CREATE UNIQUE INDEX `products_sku_unique` ON `products` (`sku`);
CREATE INDEX `products_sku_barcode_index` ON `products` (`sku`, `barcode`);
CREATE INDEX `products_reorder_level_index` ON `products` (`reorder_level`);
CREATE INDEX `reports_status_generated_at_index` ON `reports` (`status`, `generated_at`);
CREATE INDEX `reports_user_id_type_index` ON `reports` (`user_id`, `type`);
CREATE UNIQUE INDEX `roles_name_unique` ON `roles` (`name`);
CREATE UNIQUE INDEX `sales_targets_assigned_to_user_id_target_year_unique` ON `sales_targets` (`assigned_to_user_id`, `target_year`);
CREATE INDEX `sales_targets_status_target_year_index` ON `sales_targets` (`status`, `target_year`);
CREATE INDEX `sales_targets_assigned_by_user_id_target_year_index` ON `sales_targets` (`assigned_by_user_id`, `target_year`);
CREATE INDEX `sales_targets_assigned_to_user_id_target_year_index` ON `sales_targets` (`assigned_to_user_id`, `target_year`);
CREATE UNIQUE INDEX `self_assessments_user_id_period_unique` ON `self_assessments` (`user_id`, `period`);
CREATE INDEX `self_assessments_status_submitted_at_index` ON `self_assessments` (`status`, `submitted_at`);
CREATE INDEX `self_assessments_user_id_period_index` ON `self_assessments` (`user_id`, `period`);
CREATE INDEX `sessions_last_activity_index` ON `sessions` (`last_activity`);
CREATE INDEX `sessions_user_id_index` ON `sessions` (`user_id`);
CREATE INDEX `settings_type_index` ON `settings` (`type`);
CREATE INDEX `settings_type_key_name_index` ON `settings` (`type`, `key_name`);
CREATE INDEX `stock_balances_qty_on_hand_index` ON `stock_balances` (`qty_on_hand`);
CREATE INDEX `stock_balances_product_id_warehouse_id_index` ON `stock_balances` (`product_id`, `warehouse_id`);
CREATE UNIQUE INDEX `uniq_stock_balance` ON `stock_balances` (`product_id`, `warehouse_id`, `batch_id`);
CREATE INDEX `stock_transactions_created_at_index` ON `stock_transactions` (`created_at`);
CREATE INDEX `stock_transactions_ref_type_ref_id_index` ON `stock_transactions` (`ref_type`, `ref_id`);
CREATE INDEX `stock_transactions_product_id_type_created_at_index` ON `stock_transactions` (`product_id`, `type`, `created_at`);
CREATE INDEX `suppliers_status_name_index` ON `suppliers` (`status`, `name`);
CREATE INDEX `sync_log_status_next_retry_at_index` ON `sync_log` (`status`, `next_retry_at`);
CREATE INDEX `sync_log_started_at_completed_at_index` ON `sync_log` (`started_at`, `completed_at`);
CREATE INDEX `sync_log_batch_id_status_index` ON `sync_log` (`batch_id`, `status`);
CREATE INDEX `sync_log_external_system_operation_index` ON `sync_log` (`external_system`, `operation`);
CREATE INDEX `sync_log_sync_type_status_index` ON `sync_log` (`sync_type`, `status`);
CREATE INDEX `sync_log_syncable_type_syncable_id_index` ON `sync_log` (`syncable_type`, `syncable_id`);
CREATE UNIQUE INDEX `tax_codes_code_unique` ON `tax_codes` (`code`);
CREATE INDEX `tax_codes_is_active_code_index` ON `tax_codes` (`is_active`, `code`);
CREATE UNIQUE INDEX `tax_heads_code_unique` ON `tax_heads` (`code`);
CREATE INDEX `tax_rates_country_region_index` ON `tax_rates` (`country`, `region`);
CREATE INDEX `tax_rates_effective_from_effective_to_index` ON `tax_rates` (`effective_from`, `effective_to`);
CREATE INDEX `tax_rates_tax_code_id_is_active_index` ON `tax_rates` (`tax_code_id`, `is_active`);
CREATE INDEX `tax_rules_category_id_product_id_index` ON `tax_rules` (`category_id`, `product_id`);
CREATE INDEX `tax_rules_priority_is_active_index` ON `tax_rules` (`priority`, `is_active`);
CREATE INDEX `tax_rules_applies_to_is_active_index` ON `tax_rules` (`applies_to`, `is_active`);
CREATE UNIQUE INDEX `users_email_unique` ON `users` (`email`);
CREATE INDEX `visits_visit_type_index` ON `visits` (`visit_type`);
CREATE INDEX `visits_status_scheduled_at_index` ON `visits` (`status`, `scheduled_at`);
CREATE INDEX `visits_user_id_scheduled_at_index` ON `visits` (`user_id`, `scheduled_at`);
CREATE INDEX `warehouses_is_main_index` ON `warehouses` (`is_main`);
CREATE UNIQUE INDEX `warehouses_code_unique` ON `warehouses` (`code`);
CREATE INDEX `warehouses_status_code_index` ON `warehouses` (`status`, `code`);

COMMIT;
SET FOREIGN_KEY_CHECKS = 1;
