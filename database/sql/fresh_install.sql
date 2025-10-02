-- Sellora Clean MySQL Schema (Production Ready)
-- Generated on 2025-09-26 12:15:23
-- This file contains only the structure, no sample data

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
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

-- Essential data

-- Roles (Essential)
INSERT INTO `roles` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Author', 'Full system access (undeletable)', NULL, NULL),
(2, 'Admin', 'User management, role assignment, full settings', NULL, NULL),
(3, 'Chairman', 'Org KPIs & summary reports', NULL, NULL),
(4, 'Director', 'Org KPIs & summary reports', NULL, NULL),
(5, 'ED', 'Org KPIs & summary reports', NULL, NULL),
(6, 'GM', 'Final approvals; org-wide dashboard', NULL, NULL),
(7, 'DGM', 'High-level approvals; consolidated reporting', NULL, NULL),
(8, 'AGM', 'Set company targets; approve NSM data', NULL, NULL),
(9, 'NSM', 'Assign teams to ZSM/RSM/ASM; approve orders/bills; presentations', NULL, NULL),
(10, 'ZSM', 'Regional view; budgets & expenses approvals', NULL, NULL),
(11, 'RSM', 'Self + ASM + MR, approve ASM/MR, assign ASM targets', NULL, NULL),
(12, 'ASM', 'Self + MR team, approve MR, assign MR targets', NULL, NULL),
(13, 'MPO', 'Self data only (sales/bills/visits/budgets/assessments/reports)', NULL, NULL),
(14, 'MR', 'Self data only (sales/bills/visits/budgets/assessments/reports)', NULL, NULL),
(15, 'Trainee', 'Self data only (sales/bills/visits/budgets/assessments/reports)', NULL, NULL);

SET FOREIGN_KEY_CHECKS = 1;
