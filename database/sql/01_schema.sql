-- Sellora Database Schema for MySQL
-- Generated on 2025-09-26 12:13:49

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
SET time_zone = '+00:00';

CREATE TABLE `approvals` (
  "id" INT primary key AUTO_INCREMENT not null,
  "entity_type" VARCHAR(255) check ("entity_type" in ('order',
  'bill',
  'budget')) not null,
  "entity_id" INT not null,
  "from_role" VARCHAR(255),
  "to_role" VARCHAR(255),
  "action" VARCHAR(255) check ("action" in ('approve',
  'reject',
  'forward')) not null,
  "remarks" TEXT,
  "acted_by" INT,
  "created_at" TIMESTAMP,
  "updated_at" TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `assessment_attempts` (
  "id" INT primary key AUTO_INCREMENT not null,
  "assessment_id" INT not null,
  "user_id" INT not null,
  "answers" TEXT,
  "score" numeric,
  "status" VARCHAR(255) check ("status" in ('in_progress',
  'completed',
  'abandoned')) not null default 'in_progress',
  "started_at" TIMESTAMP,
  "completed_at" TIMESTAMP,
  "duration" INT,
  "ip_address" VARCHAR(255),
  "user_agent" TEXT,
  "notes" TEXT,
  "created_at" TIMESTAMP,
  "updated_at" TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `assessment_results` (
  "id" INT primary key AUTO_INCREMENT not null,
  "assessment_attempt_id" INT not null,
  "question_index" INT not null,
  "question_text" TEXT not null,
  "user_answer" TEXT,
  "correct_answer" TEXT,
  "is_correct" tinyint(1) not null default '0',
  "points_earned" numeric not null default '0',
  "max_points" numeric not null default '0',
  "time_spent" INT,
  "feedback" TEXT,
  "created_at" TIMESTAMP,
  "updated_at" TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `assessments` (
  "id" INT primary key AUTO_INCREMENT not null,
  "user_id" INT not null,
  "title" VARCHAR(255) not null,
  "description" TEXT,
  "category" VARCHAR(255),
  "type" VARCHAR(255) check ("type" in ('quiz',
  'survey',
  'exam')) not null default 'quiz',
  "questions" TEXT,
  "scoring_method" VARCHAR(255) check ("scoring_method" in ('points',
  'percentage')) not null default 'points',
  "max_score" INT not null default '100',
  "passing_score" INT not null default '60',
  "time_limit" INT,
  "attempts_allowed" INT not null default '1',
  "is_active" tinyint(1) not null default '1',
  "start_date" TIMESTAMP,
  "end_date" TIMESTAMP,
  "instructions" TEXT,
  "tags" TEXT,
  "difficulty_level" VARCHAR(255) check ("difficulty_level" in ('easy',
  'medium',
  'hard')) not null default 'medium',
  "estimated_duration" INT,
  "auto_grade" tinyint(1) not null default '1',
  "show_results_immediately" tinyint(1) not null default '1',
  "randomize_questions" tinyint(1) not null default '0',
  "allow_review" tinyint(1) not null default '1',
  "certificate_template" VARCHAR(255),
  "completion_message" TEXT,
  "created_at" TIMESTAMP,
  "updated_at" TIMESTAMP,
  "deleted_at" TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `audit_logs` (
  "id" INT primary key AUTO_INCREMENT not null,
  "actor_id" INT,
  "action" VARCHAR(255) not null,
  "entity_type" VARCHAR(255) not null,
  "entity_id" INT,
  "metadata" TEXT,
  "ip_address" VARCHAR(255),
  "user_agent" TEXT,
  "created_at" TIMESTAMP,
  "updated_at" TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `bill_files` (
  "id" INT primary key AUTO_INCREMENT not null,
  "bill_id" INT not null,
  "original_name" VARCHAR(255) not null,
  "file_path" VARCHAR(255) not null,
  "file_type" VARCHAR(255),
  "file_size" INT,
  "created_at" TIMESTAMP,
  "updated_at" TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `bills` (
  "id" INT primary key AUTO_INCREMENT not null,
  "user_id" INT not null,
  "amount" numeric not null,
  "purpose" VARCHAR(255) not null,
  "description" TEXT,
  "vendor" VARCHAR(255),
  "receipt_number" VARCHAR(255),
  "expense_date" date,
  "category" VARCHAR(255),
  "payment_method" VARCHAR(255),
  "priority" VARCHAR(255) check ("priority" in ('Low',
  'Medium',
  'High',
  'Urgent')) not null default 'Medium',
  "notes" TEXT,
  "status" VARCHAR(255) check ("status" in ('Pending',
  'Approved',
  'Forwarded',
  'Paid',
  'Rejected')) not null default 'Pending',
  "approved_by" INT,
  "approved_at" TIMESTAMP,
  "rejected_reason" TEXT,
  "created_at" TIMESTAMP,
  "updated_at" TIMESTAMP,
  "deleted_at" TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `budget_expenses` (
  "id" INT primary key AUTO_INCREMENT not null,
  "budget_id" INT not null,
  "bill_id" INT,
  "expense_id" INT,
  "amount" numeric not null,
  "category" VARCHAR(255),
  "description" TEXT,
  "allocated_at" TIMESTAMP not null default CURRENT_TIMESTAMP,
  "created_at" TIMESTAMP,
  "updated_at" TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `budget_items` (
  "id" INT primary key AUTO_INCREMENT not null,
  "budget_id" INT not null,
  "name" VARCHAR(255) not null,
  "description" TEXT,
  "category" VARCHAR(255) not null,
  "allocated_amount" numeric not null,
  "spent_amount" numeric not null default '0',
  "remaining_amount" numeric not null default '0',
  "sort_order" INT not null default '0',
  "is_active" tinyint(1) not null default '1',
  "created_at" TIMESTAMP,
  "updated_at" TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `budgets` (
  "id" INT primary key AUTO_INCREMENT not null,
  "name" VARCHAR(255) not null,
  "description" TEXT,
  "period_type" VARCHAR(255) check ("period_type" in ('monthly',
  'quarterly',
  'half_yearly',
  'yearly',
  'custom')) not null,
  "start_date" date not null,
  "end_date" date not null,
  "total_amount" numeric not null,
  "allocated_amount" numeric not null default '0',
  "spent_amount" numeric not null default '0',
  "remaining_amount" numeric not null default '0',
  "status" VARCHAR(255) check ("status" in ('draft',
  'pending',
  'approved',
  'active',
  'completed',
  'cancelled',
  'exceeded')) not null default 'draft',
  "created_by" INT not null,
  "approved_by" INT,
  "approved_at" TIMESTAMP,
  "notes" TEXT,
  "categories" TEXT,
  "currency" VARCHAR(255) not null default 'USD',
  "auto_approve_limit" numeric,
  "notification_threshold" numeric not null default '80',
  "is_recurring" tinyint(1) not null default '0',
  "recurring_frequency" VARCHAR(255) check ("recurring_frequency" in ('monthly',
  'quarterly',
  'half_yearly',
  'yearly')),
  "created_at" TIMESTAMP,
  "updated_at" TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cache` (
  "key" VARCHAR(255) not null,
  "value" TEXT not null,
  "expiration" INT not null,
  `primary` key ("key"
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cache_locks` (
  "key" VARCHAR(255) not null,
  "owner" VARCHAR(255) not null,
  "expiration" INT not null,
  `primary` key ("key"
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `customers` (
  "id" INT primary key AUTO_INCREMENT not null,
  "name" VARCHAR(255) not null,
  "shop_name" VARCHAR(255),
  "full_address" TEXT,
  "phone" VARCHAR(255) not null,
  "email" VARCHAR(255),
  "notes" TEXT,
  "created_at" TIMESTAMP,
  "updated_at" TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `email_queue` (
  "id" INT primary key AUTO_INCREMENT not null,
  "to_email" VARCHAR(255) not null,
  "to_user_id" INT,
  "subject" VARCHAR(255) not null,
  "body" TEXT not null,
  "template_slug" VARCHAR(255),
  "data_json" TEXT,
  "scheduled_at" TIMESTAMP not null,
  "sent_at" TIMESTAMP,
  "status" VARCHAR(255) check ("status" in ('queued',
  'sent',
  'failed')) not null default 'queued',
  "error" TEXT,
  "attempts" INT not null default '0',
  "created_at" TIMESTAMP,
  "updated_at" TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `email_templates` (
  "id" INT primary key AUTO_INCREMENT not null,
  "slug" VARCHAR(255) not null,
  "subject_tpl" VARCHAR(255) not null,
  "body_tpl" TEXT not null,
  "description" VARCHAR(255),
  "enabled" tinyint(1) not null default '1',
  "created_at" TIMESTAMP,
  "updated_at" TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `events` (
  "id" INT primary key AUTO_INCREMENT not null,
  "title" VARCHAR(255) not null,
  "description" TEXT,
  "event_type" VARCHAR(255) check ("event_type" in ('meeting',
  'appointment',
  'deadline',
  'reminder',
  'personal',
  'holiday',
  'other')) not null default 'meeting',
  "start_date" date not null,
  "end_date" date not null,
  "start_time" TIMESTAMP,
  "end_time" TIMESTAMP,
  "location" VARCHAR(255),
  "is_all_day" tinyint(1) not null default '0',
  "priority" VARCHAR(255) check ("priority" in ('low',
  'medium',
  'high',
  'urgent')) not null default 'medium',
  "status" VARCHAR(255) check ("status" in ('scheduled',
  'in_progress',
  'completed',
  'cancelled',
  'postponed')) not null default 'scheduled',
  "color" VARCHAR(255),
  "reminder_minutes" INT,
  "attendees" TEXT,
  "notes" TEXT,
  "created_by" INT not null,
  "recurring_type" VARCHAR(255) check ("recurring_type" in ('none',
  'daily',
  'weekly',
  'monthly',
  'yearly')) not null default 'none',
  "recurring_end_date" date,
  "recurring_days" TEXT,
  "attachments" TEXT,
  "created_at" TIMESTAMP,
  "updated_at" TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `expenses` (
  "id" INT primary key AUTO_INCREMENT not null,
  "user_id" INT not null,
  "title" VARCHAR(255) not null,
  "description" TEXT,
  "category" VARCHAR(255) not null,
  "amount" numeric not null,
  "currency" VARCHAR(255) not null default 'USD',
  "expense_date" date not null,
  "receipt_number" VARCHAR(255),
  "vendor" VARCHAR(255),
  "status" VARCHAR(255) check ("status" in ('pending',
  'approved',
  'rejected',
  'paid')) not null default 'pending',
  "priority" VARCHAR(255) check ("priority" in ('low',
  'medium',
  'high')) not null default 'medium',
  "approved_by" INT,
  "approved_at" TIMESTAMP,
  "approval_notes" TEXT,
  "rejection_reason" TEXT,
  "attachments" TEXT,
  "notes" TEXT,
  "is_reimbursable" tinyint(1) not null default '1',
  "tax_amount" numeric,
  "payment_method" VARCHAR(255),
  "reference_number" VARCHAR(255),
  "created_at" TIMESTAMP,
  "updated_at" TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `export_jobs` (
  "id" INT primary key AUTO_INCREMENT not null,
  "scope" VARCHAR(255) not null,
  "modules" TEXT not null,
  "format" VARCHAR(255) not null,
  "filters" TEXT,
  "include_dependencies" tinyint(1) not null default '0',
  "status" VARCHAR(255) check ("status" in ('pending',
  'processing',
  'completed',
  'failed',
  'cancelled')) not null default 'pending',
  "created_by" INT not null,
  "stats" TEXT,
  "file_path" TEXT,
  "error_message" TEXT,
  "started_at" TIMESTAMP,
  "completed_at" TIMESTAMP,
  "created_at" TIMESTAMP,
  "updated_at" TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `external_maps` (
  "id" INT primary key AUTO_INCREMENT not null,
  "module" VARCHAR(255) not null,
  "external_id" VARCHAR(255) not null,
  "local_id" INT not null,
  "source" VARCHAR(255) not null,
  "metadata" TEXT,
  "created_at" TIMESTAMP,
  "updated_at" TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `external_product_map` (
  "id" INT primary key AUTO_INCREMENT not null,
  "product_id" INT not null,
  "external_system" VARCHAR(255) not null,
  "external_id" VARCHAR(255) not null,
  "external_sku" VARCHAR(255),
  "external_url" VARCHAR(255),
  "external_data" TEXT,
  "field_mapping" TEXT,
  "sync_direction" VARCHAR(255) check ("sync_direction" in ('import',
  'export',
  'bidirectional')) not null default 'import',
  "auto_sync" tinyint(1) not null default '1',
  "last_synced_at" TIMESTAMP,
  "last_sync_attempt_at" TIMESTAMP,
  "sync_status" VARCHAR(255) check ("sync_status" in ('pending',
  'syncing',
  'success',
  'failed',
  'disabled')) not null default 'pending',
  "sync_error" TEXT,
  "sync_attempts" INT not null default '0',
  "is_active" tinyint(1) not null default '1',
  "notes" TEXT,
  "created_at" TIMESTAMP,
  "updated_at" TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `failed_jobs` (
  "id" INT primary key AUTO_INCREMENT not null,
  "uuid" VARCHAR(255) not null,
  "connection" TEXT not null,
  "queue" TEXT not null,
  "payload" TEXT not null,
  "exception" TEXT not null,
  "failed_at" TIMESTAMP not null default CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `import_items` (
  "id" INT primary key AUTO_INCREMENT not null,
  "job_id" INT not null,
  "module" VARCHAR(255) not null,
  "source_row_no" INT,
  "payload" TEXT not null,
  "status" VARCHAR(255) check ("status" in ('pending',
  'processing',
  'completed',
  'failed',
  'skipped')) not null default 'pending',
  "error_message" TEXT,
  "entity_id" INT,
  "created_at" TIMESTAMP,
  "updated_at" TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `import_jobs` (
  "id" INT primary key AUTO_INCREMENT not null,
  "source_type" VARCHAR(255) not null,
  "modules" TEXT not null,
  "status" VARCHAR(255) check ("status" in ('pending',
  'processing',
  'completed',
  'failed',
  'cancelled')) not null default 'pending',
  "created_by" INT not null,
  "stats" TEXT,
  "file_path" TEXT,
  "config" TEXT,
  "error_message" TEXT,
  "started_at" TIMESTAMP,
  "completed_at" TIMESTAMP,
  "created_at" TIMESTAMP,
  "updated_at" TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `import_presets` (
  "id" INT primary key AUTO_INCREMENT not null,
  "name" VARCHAR(255) not null,
  "source_type" VARCHAR(255) not null,
  "module" VARCHAR(255) not null,
  "column_map" TEXT not null,
  "options" TEXT,
  "created_by" INT not null,
  "created_at" TIMESTAMP,
  "updated_at" TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `job_batches` (
  "id" VARCHAR(255) not null,
  "name" VARCHAR(255) not null,
  "total_jobs" INT not null,
  "pending_jobs" INT not null,
  "failed_jobs" INT not null,
  "failed_job_ids" TEXT not null,
  "options" TEXT,
  "cancelled_at" INT,
  "created_at" INT not null,
  "finished_at" INT,
  `primary` key ("id"
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `jobs` (
  "id" INT primary key AUTO_INCREMENT not null,
  "queue" VARCHAR(255) not null,
  "payload" TEXT not null,
  "attempts" INT not null,
  "reserved_at" INT,
  "available_at" INT not null,
  "created_at" INT not null
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `location_tracking` (
  "id" INT primary key AUTO_INCREMENT not null,
  "user_id" INT not null,
  "latitude" numeric not null,
  "longitude" numeric not null,
  "accuracy" numeric,
  "captured_at" TIMESTAMP not null default CURRENT_TIMESTAMP,
  "created_at" TIMESTAMP,
  "updated_at" TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `location_visits` (
  "id" INT primary key AUTO_INCREMENT not null,
  "location_id" INT not null,
  "user_id" INT not null,
  "visited_at" TIMESTAMP not null,
  "left_at" TIMESTAMP,
  "duration_minutes" INT,
  "purpose" VARCHAR(255),
  "notes" TEXT,
  "check_in_method" VARCHAR(255) check ("check_in_method" in ('manual',
  'gps',
  'qr_code',
  'nfc',
  'beacon')) not null default 'manual',
  "check_out_method" VARCHAR(255) check ("check_out_method" in ('manual',
  'gps',
  'auto',
  'timeout')),
  "latitude" numeric,
  "longitude" numeric,
  "accuracy" numeric,
  "weather" VARCHAR(255),
  "temperature" numeric,
  "mood_rating" INT,
  "productivity_rating" INT,
  "photos" TEXT,
  "tags" TEXT,
  "created_at" TIMESTAMP,
  "updated_at" TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `locations` (
  "id" INT primary key AUTO_INCREMENT not null,
  "user_id" INT not null,
  "name" VARCHAR(255) not null,
  "description" TEXT,
  "latitude" numeric not null,
  "longitude" numeric not null,
  "address" VARCHAR(255),
  "city" VARCHAR(255),
  "state" VARCHAR(255),
  "country" VARCHAR(255),
  "postal_code" VARCHAR(255),
  "type" VARCHAR(255),
  "status" VARCHAR(255) not null default 'active',
  "accuracy" numeric,
  "altitude" numeric,
  "speed" numeric,
  "heading" numeric,
  "timestamp" TIMESTAMP,
  "ip_address" VARCHAR(255),
  "user_agent" TEXT,
  "notes" TEXT,
  "is_favorite" tinyint(1) not null default '0',
  "visit_count" INT not null default '0',
  "last_visited_at" TIMESTAMP,
  "created_at" TIMESTAMP,
  "updated_at" TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `media` (
  "id" INT primary key AUTO_INCREMENT not null,
  "mediable_type" VARCHAR(255) not null,
  "mediable_id" INT not null,
  "collection_name" VARCHAR(255) not null default 'default',
  "name" VARCHAR(255) not null,
  "file_name" VARCHAR(255) not null,
  "mime_type" VARCHAR(255),
  "disk" VARCHAR(255) not null default 'public',
  "conversions_disk" VARCHAR(255) not null default 'public',
  "size" INT not null,
  "manipulations" TEXT not null,
  "custom_properties" TEXT not null,
  "generated_conversions" TEXT not null,
  "responsive_images" TEXT not null,
  "order_column" INT,
  "alt_text" VARCHAR(255),
  "description" TEXT,
  "caption" VARCHAR(255),
  "is_primary" tinyint(1) not null default '0',
  "is_active" tinyint(1) not null default '1',
  "created_at" TIMESTAMP,
  "updated_at" TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `migrations` (
  "id" INT primary key AUTO_INCREMENT not null,
  "migration" VARCHAR(255) not null,
  "batch" INT not null
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `notifications` (
  "id" VARCHAR(255) not null,
  "type" VARCHAR(255) not null,
  "notifiable_type" VARCHAR(255) not null,
  "notifiable_id" INT not null,
  "data" TEXT not null,
  "read_at" TIMESTAMP,
  "created_at" TIMESTAMP,
  "updated_at" TIMESTAMP,
  `primary` key ("id"
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `order_files` (
  "id" INT primary key AUTO_INCREMENT not null,
  "order_id" INT not null,
  "original_name" VARCHAR(255) not null,
  "file_path" VARCHAR(255) not null,
  "file_type" VARCHAR(255) not null,
  "file_size" INT not null,
  "created_at" TIMESTAMP,
  "updated_at" TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `order_items` (
  "id" INT primary key AUTO_INCREMENT not null,
  "order_id" INT not null,
  "product_id" INT not null,
  "quantity" INT not null,
  "unit_price" numeric not null,
  "total_price" numeric not null,
  "notes" TEXT,
  "created_at" TIMESTAMP,
  "updated_at" TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `order_tax_lines` (
  "id" INT primary key AUTO_INCREMENT not null,
  "order_id" INT not null,
  "tax_head_id" INT not null,
  "base_amount" numeric not null,
  "rate" numeric not null,
  "calculated_amount" numeric not null,
  "payer" VARCHAR(255) check ("payer" in ('client',
  'company')) not null,
  "visible" tinyint(1) not null default '1',
  "created_at" TIMESTAMP,
  "updated_at" TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `orders` (
  "id" INT primary key AUTO_INCREMENT not null,
  "user_id" INT not null,
  "customer_name" VARCHAR(255) not null,
  "amount" numeric not null,
  "status" VARCHAR(255) check ("status" in ('Pending',
  'Approved',
  'Forwarded',
  'Completed',
  'Cancelled')) not null default 'Pending',
  "notes" TEXT,
  "created_at" TIMESTAMP,
  "updated_at" TIMESTAMP,
  "tax_breakdown" TEXT,
  "total_amount" numeric,
  "order_number" VARCHAR(255),
  "vat_condition" VARCHAR(255) check ("vat_condition" in ('client_bears',
  'company_bears')) not null default 'client_bears',
  "tax_condition" VARCHAR(255) check ("tax_condition" in ('client_bears',
  'company_bears')) not null default 'client_bears',
  "vat_amount" numeric not null default '0',
  "tax_amount" numeric not null default '0',
  "net_revenue" numeric
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `password_reset_tokens` (
  "email" VARCHAR(255) not null,
  "token" VARCHAR(255) not null,
  "created_at" TIMESTAMP,
  `primary` key ("email"
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `presentation_comments` (
  "id" INT primary key AUTO_INCREMENT not null,
  "presentation_id" INT not null,
  "user_id" INT not null,
  "content" TEXT not null,
  "created_at" TIMESTAMP,
  "updated_at" TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `presentation_downloads` (
  "id" INT primary key AUTO_INCREMENT not null,
  "presentation_id" INT not null,
  "user_id" INT,
  "ip_address" VARCHAR(255),
  "user_agent" VARCHAR(255),
  "created_at" TIMESTAMP,
  "updated_at" TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `presentation_shares` (
  "id" INT primary key AUTO_INCREMENT not null,
  "presentation_id" INT not null,
  "user_id" INT not null,
  "permission" VARCHAR(255) check ("permission" in ('view',
  'edit')) not null default 'view',
  "created_at" TIMESTAMP,
  "updated_at" TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `presentation_views` (
  "id" INT primary key AUTO_INCREMENT not null,
  "presentation_id" INT not null,
  "user_id" INT,
  "ip_address" VARCHAR(255),
  "user_agent" VARCHAR(255),
  "created_at" TIMESTAMP,
  "updated_at" TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `presentations` (
  "id" INT primary key AUTO_INCREMENT not null,
  "user_id" INT not null,
  "title" VARCHAR(255) not null,
  "description" TEXT,
  "file_path" VARCHAR(255) not null,
  "file_name" VARCHAR(255) not null,
  "file_size" INT not null,
  "file_type" VARCHAR(255) not null,
  "category" VARCHAR(255),
  "tags" TEXT,
  "status" VARCHAR(255) check ("status" in ('draft',
  'published',
  'archived')) not null default 'draft',
  "privacy_level" VARCHAR(255) check ("privacy_level" in ('private',
  'public',
  'shared')) not null default 'private',
  "is_template" tinyint(1) not null default '0',
  "view_count" INT not null default '0',
  "download_count" INT not null default '0',
  "version" VARCHAR(255) not null default '1.0',
  "original_presentation_id" INT,
  "created_at" TIMESTAMP,
  "updated_at" TIMESTAMP,
  "deleted_at" TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `product_batches` (
  "id" INT primary key AUTO_INCREMENT not null,
  "product_id" INT not null,
  "batch_no" VARCHAR(255) not null,
  "mfg_date" date,
  "exp_date" date,
  "mrp" numeric,
  "purchase_price" numeric,
  "barcode" VARCHAR(255),
  "created_at" TIMESTAMP,
  "updated_at" TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `product_brands` (
  "id" INT primary key AUTO_INCREMENT not null,
  "name" VARCHAR(255) not null,
  "description" VARCHAR(255),
  "status" tinyint(1) not null default '1',
  "created_at" TIMESTAMP,
  "updated_at" TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `product_categories` (
  "id" INT primary key AUTO_INCREMENT not null,
  "name" VARCHAR(255) not null,
  "description" VARCHAR(255),
  "status" tinyint(1) not null default '1',
  "created_at" TIMESTAMP,
  "updated_at" TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `product_files` (
  "id" INT primary key AUTO_INCREMENT not null,
  "product_id" INT not null,
  "file_path" VARCHAR(255) not null,
  "file_type" VARCHAR(255) not null,
  "original_name" VARCHAR(255) not null,
  "file_size" INT,
  "created_at" TIMESTAMP,
  "updated_at" TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `product_prices` (
  "id" INT primary key AUTO_INCREMENT not null,
  "product_id" INT not null,
  "price_type" VARCHAR(255) not null,
  "price" numeric not null,
  "cost_price" numeric,
  "min_quantity" INT not null default '1',
  "max_quantity" INT,
  "currency" VARCHAR(255) not null default 'USD',
  "customer_id" INT,
  "customer_group" VARCHAR(255),
  "valid_from" date,
  "valid_to" date,
  "is_active" tinyint(1) not null default '1',
  "notes" TEXT,
  "created_at" TIMESTAMP,
  "updated_at" TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `product_units` (
  "id" INT primary key AUTO_INCREMENT not null,
  "name" VARCHAR(255) not null,
  "symbol" VARCHAR(255) not null,
  "status" tinyint(1) not null default '1',
  "created_at" TIMESTAMP,
  "updated_at" TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `products` (
  "id" INT primary key AUTO_INCREMENT not null,
  "name" VARCHAR(255) not null,
  "generic_name" VARCHAR(255),
  "composition" VARCHAR(255),
  "dosage_form" VARCHAR(255),
  "strength" VARCHAR(255),
  "sku" VARCHAR(255) not null,
  "barcode" VARCHAR(255),
  "hsn" VARCHAR(255),
  "schedule" VARCHAR(255),
  "storage_conditions" VARCHAR(255),
  "category_id" INT,
  "brand_id" INT,
  "unit_id" INT,
  "pack_size" VARCHAR(255),
  "purchase_price" numeric not null default ('0'),
  "selling_price" numeric not null default ('0'),
  "tax_rate" numeric not null default ('0'),
  "reorder_level" INT not null default ('0'),
  "reorder_qty" INT not null default ('0'),
  "allow_negative" tinyint(1) not null default ('0'),
  "status" tinyint(1) not null default ('1'),
  "created_at" TIMESTAMP,
  "updated_at" TIMESTAMP,
  "warehouse_id" INT,
  "description" TEXT,
  "weight" numeric,
  "dimensions" VARCHAR(255),
  "tax_code" VARCHAR(255),
  "is_taxable" tinyint(1) not null default ('0'),
  "meta_data" TEXT,
  "price" numeric,
  "stock" INT not null default ('0'),
  "expiration_date" date
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `reports` (
  "id" INT primary key AUTO_INCREMENT not null,
  "name" VARCHAR(255) not null,
  "type" VARCHAR(255) check ("type" in ('sales',
  'expenses',
  'visits',
  'budgets',
  'custom')) not null,
  "user_id" INT not null,
  "filters" TEXT,
  "format" VARCHAR(255) check ("format" in ('pdf',
  'excel',
  'csv')) not null,
  "file_path" VARCHAR(255),
  "file_size" INT,
  "generated_at" TIMESTAMP not null,
  "expires_at" TIMESTAMP,
  "status" VARCHAR(255) check ("status" in ('pending',
  'processing',
  'completed',
  'failed')) not null default 'pending',
  "error_message" TEXT,
  "created_at" TIMESTAMP,
  "updated_at" TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `roles` (
  "id" INT primary key AUTO_INCREMENT not null,
  "name" VARCHAR(255) not null,
  "description" TEXT,
  "created_at" TIMESTAMP,
  "updated_at" TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `sales_targets` (
  "id" INT primary key AUTO_INCREMENT not null,
  "assigned_by_user_id" INT not null,
  "assigned_to_user_id" INT not null,
  "target_year" INT not null,
  "week_1_target" numeric not null default '0',
  "week_2_target" numeric not null default '0',
  "week_3_target" numeric not null default '0',
  "week_4_target" numeric not null default '0',
  "january_target" numeric not null default '0',
  "february_target" numeric not null default '0',
  "march_target" numeric not null default '0',
  "april_target" numeric not null default '0',
  "may_target" numeric not null default '0',
  "june_target" numeric not null default '0',
  "july_target" numeric not null default '0',
  "august_target" numeric not null default '0',
  "september_target" numeric not null default '0',
  "october_target" numeric not null default '0',
  "november_target" numeric not null default '0',
  "december_target" numeric not null default '0',
  "total_yearly_target" numeric not null default '0',
  "status" VARCHAR(255) check ("status" in ('active',
  'inactive',
  'completed')) not null default 'active',
  "notes" TEXT,
  "created_at" TIMESTAMP,
  "updated_at" TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `self_assessments` (
  "id" INT primary key AUTO_INCREMENT not null,
  "user_id" INT not null,
  "period" VARCHAR(255) not null,
  "targets" TEXT not null,
  "achievements" TEXT not null,
  "problems" TEXT,
  "solutions" TEXT,
  "market_analysis" TEXT,
  "status" VARCHAR(255) check ("status" in ('draft',
  'submitted',
  'reviewed')) not null default 'draft',
  "submitted_at" TIMESTAMP,
  "reviewed_at" TIMESTAMP,
  "reviewed_by" INT,
  "reviewer_comments" TEXT,
  "created_at" TIMESTAMP,
  "updated_at" TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `sessions` (
  "id" VARCHAR(255) not null,
  "user_id" INT,
  "ip_address" VARCHAR(255),
  "user_agent" TEXT,
  "payload" TEXT not null,
  "last_activity" INT not null,
  `primary` key ("id"
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `settings` (
  "id" INT primary key AUTO_INCREMENT not null,
  "type" VARCHAR(255) not null,
  "key_name" VARCHAR(255) not null,
  "value" TEXT,
  "created_at" TIMESTAMP,
  "updated_at" TIMESTAMP,
  "is_locked" tinyint(1) not null default '0',
  "locked_by_role" VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `stock_balances` (
  "id" INT primary key AUTO_INCREMENT not null,
  "product_id" INT not null,
  "warehouse_id" INT not null,
  "batch_id" INT,
  "qty_on_hand" INT not null default '0',
  "qty_reserved" INT not null default '0',
  "created_at" TIMESTAMP,
  "updated_at" TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `stock_transactions` (
  "id" INT primary key AUTO_INCREMENT not null,
  "product_id" INT not null,
  "warehouse_id" INT not null,
  "batch_id" INT,
  "qty" INT not null,
  "type" VARCHAR(255) check ("type" in ('opening',
  'purchase_in',
  'transfer_in',
  'transfer_out',
  'sale_reserve',
  'release_reserve',
  'sale_dispatch',
  'sale_return',
  'adjustment_in',
  'adjustment_out')) not null,
  "ref_type" VARCHAR(255),
  "ref_id" INT,
  "note" TEXT,
  "created_at" TIMESTAMP,
  "updated_at" TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `suppliers` (
  "id" INT primary key AUTO_INCREMENT not null,
  "name" VARCHAR(255) not null,
  "contact_person" VARCHAR(255),
  "email" VARCHAR(255),
  "phone" VARCHAR(255),
  "address" TEXT,
  "city" VARCHAR(255),
  "state" VARCHAR(255),
  "country" VARCHAR(255),
  "postal_code" VARCHAR(255),
  "tax_number" VARCHAR(255),
  "status" tinyint(1) not null default '1',
  "notes" TEXT,
  "created_at" TIMESTAMP,
  "updated_at" TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `sync_log` (
  "id" INT primary key AUTO_INCREMENT not null,
  "sync_type" VARCHAR(255) not null,
  "external_system" VARCHAR(255) not null,
  "operation" VARCHAR(255) not null,
  "syncable_type" VARCHAR(255) not null,
  "syncable_id" INT not null,
  "external_id" VARCHAR(255),
  "status" VARCHAR(255) check ("status" in ('pending',
  'processing',
  'success',
  'failed',
  'partial')) not null default 'pending',
  "started_at" TIMESTAMP,
  "completed_at" TIMESTAMP,
  "duration_ms" INT,
  "request_data" TEXT,
  "response_data" TEXT,
  "changes_made" TEXT,
  "error_message" TEXT,
  "error_details" TEXT,
  "retry_count" INT not null default '0',
  "next_retry_at" TIMESTAMP,
  "batch_id" VARCHAR(255),
  "user_id" VARCHAR(255),
  "ip_address" VARCHAR(255),
  "notes" TEXT,
  "created_at" TIMESTAMP,
  "updated_at" TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tax_codes` (
  "id" INT primary key AUTO_INCREMENT not null,
  "name" VARCHAR(255) not null,
  "code" VARCHAR(255) not null,
  "description" TEXT,
  "is_active" tinyint(1) not null default '1',
  "created_at" TIMESTAMP,
  "updated_at" TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tax_heads` (
  "id" INT primary key AUTO_INCREMENT not null,
  "name" VARCHAR(255) not null,
  "code" VARCHAR(255) not null,
  "kind" VARCHAR(255) check ("kind" in ('VAT',
  'AIT',
  'OTHER')) not null,
  "percentage" numeric not null,
  "visible_to_client" tinyint(1) not null default '1',
  "created_by" INT not null,
  "created_at" TIMESTAMP,
  "updated_at" TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tax_rates` (
  "id" INT primary key AUTO_INCREMENT not null,
  "tax_code_id" INT not null,
  "label" VARCHAR(255) not null,
  "percent" numeric not null,
  "country" VARCHAR(255),
  "region" VARCHAR(255),
  "effective_from" date not null,
  "effective_to" date,
  "is_default" tinyint(1) not null default '0',
  "is_active" tinyint(1) not null default '1',
  "created_at" TIMESTAMP,
  "updated_at" TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tax_rules` (
  "id" INT primary key AUTO_INCREMENT not null,
  "name" VARCHAR(255) not null,
  "description" TEXT,
  "tax_rate_id" INT not null,
  "applies_to" VARCHAR(255) check ("applies_to" in ('all',
  'category',
  'product',
  'shipping',
  'fees')) not null default 'all',
  "category_id" INT,
  "product_id" INT,
  "price_mode" VARCHAR(255) check ("price_mode" in ('INCLUSIVE',
  'EXCLUSIVE')) not null default 'EXCLUSIVE',
  "bearer" VARCHAR(255) check ("bearer" in ('CUSTOMER',
  'COMPANY')) not null default 'CUSTOMER',
  "reverse_charge" tinyint(1) not null default '0',
  "zero_rated" tinyint(1) not null default '0',
  "exempt" tinyint(1) not null default '0',
  "withholding" tinyint(1) not null default '0',
  "withholding_percent" numeric,
  "taxable_discounts" VARCHAR(255) check ("taxable_discounts" in ('NONE',
  'BEFORE_TAX',
  'AFTER_TAX')) not null default 'NONE',
  "taxable_shipping" tinyint(1) not null default '1',
  "place_of_supply" VARCHAR(255) check ("place_of_supply" in ('ORIGIN',
  'DESTINATION',
  'AUTO')) not null default 'ORIGIN',
  "rounding" VARCHAR(255) check ("rounding" in ('LINE',
  'SUBTOTAL',
  'INVOICE')) not null default 'LINE',
  "priority" INT not null default '0',
  "is_active" tinyint(1) not null default '1',
  "comments" TEXT,
  "created_at" TIMESTAMP,
  "updated_at" TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `users` (
  "id" INT primary key AUTO_INCREMENT not null,
  "name" VARCHAR(255) not null,
  "email" VARCHAR(255) not null,
  "email_verified_at" TIMESTAMP,
  "password" VARCHAR(255) not null,
  "role_id" INT,
  "designation" VARCHAR(255),
  "photo" VARCHAR(255),
  "remember_token" VARCHAR(255),
  "created_at" TIMESTAMP,
  "updated_at" TIMESTAMP,
  "notify_email" tinyint(1) not null default '1',
  "employee_id" VARCHAR(255),
  "bio" TEXT,
  "timezone" VARCHAR(255) not null default 'UTC',
  "email_notifications" tinyint(1) not null default '1',
  "sms_notifications" tinyint(1) not null default '0',
  "marketing_emails" tinyint(1) not null default '0',
  "security_alerts" tinyint(1) not null default '1',
  "last_login_at" TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `visits` (
  "id" INT primary key AUTO_INCREMENT not null,
  "user_id" INT not null,
  "customer_name" VARCHAR(255) not null,
  "customer_phone" VARCHAR(255),
  "customer_email" VARCHAR(255),
  "customer_address" TEXT not null,
  "visit_type" VARCHAR(255) not null default 'sales',
  "purpose" TEXT,
  "scheduled_at" TIMESTAMP not null,
  "actual_start_time" TIMESTAMP,
  "actual_end_time" TIMESTAMP,
  "status" VARCHAR(255) check ("status" in ('scheduled',
  'in_progress',
  'completed',
  'cancelled',
  'rescheduled')) not null default 'scheduled',
  "priority" VARCHAR(255) check ("priority" in ('low',
  'medium',
  'high',
  'urgent')) not null default 'medium',
  "notes" TEXT,
  "outcome" TEXT,
  "latitude" numeric,
  "longitude" numeric,
  "location_address" VARCHAR(255),
  "attachments" TEXT,
  "estimated_duration" numeric not null default '1',
  "actual_duration" numeric,
  "requires_follow_up" tinyint(1) not null default '0',
  "follow_up_date" TIMESTAMP,
  "cancellation_reason" TEXT,
  "rescheduled_from" TIMESTAMP,
  "created_at" TIMESTAMP,
  "updated_at" TIMESTAMP,
  "rescheduled_to" TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `warehouses` (
  "id" INT primary key AUTO_INCREMENT not null,
  "name" VARCHAR(255) not null,
  "code" VARCHAR(255) not null,
  "address" TEXT,
  "status" tinyint(1) not null default '1',
  "created_at" TIMESTAMP,
  "updated_at" TIMESTAMP,
  "is_main" tinyint(1) not null default '0',
  "phone" VARCHAR(255),
  "email" VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
