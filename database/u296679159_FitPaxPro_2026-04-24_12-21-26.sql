# ************************************************************
# Antares - SQL Client
# Version 0.7.35
# 
# https://antares-sql.app/
# https://github.com/antares-sql/antares
# 
# Host: srv1498.hstgr.io (MariaDB Server 11.8.6)
# Database: u296679159_FitPaxPro
# Generation time: 2026-04-24T12:21:30+05:30
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
SET NAMES utf8mb4;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table admin_warnings
# ------------------------------------------------------------

CREATE TABLE `admin_warnings` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL COMMENT 'The user receiving the warning',
  `admin_id` char(36) NOT NULL COMMENT 'The admin who issued the warning',
  `reason` text NOT NULL COMMENT 'Description of the unethical content or behavior',
  `content_type` enum('daily_snap','recipe','comment','profile_picture','forum_post') NOT NULL,
  `content_id` char(36) DEFAULT NULL COMMENT 'ID of the specific record being warned (e.g., health_logs.id)',
  `severity_level` enum('low','medium','high','final_notice') DEFAULT 'medium',
  `action_taken` enum('none','content_deleted','account_restricted','temporary_ban') DEFAULT 'content_deleted',
  `is_resolved` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1 if the user has acknowledged or corrected the issue',
  `admin_notes` text DEFAULT NULL COMMENT 'Internal notes for other admins',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_admin_warnings_user` (`user_id`),
  KEY `fk_admin_warnings_admin` (`admin_id`),
  CONSTRAINT `fk_admin_warnings_admin` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_admin_warnings_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table attendance_logs
# ------------------------------------------------------------

CREATE TABLE `attendance_logs` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL COMMENT 'The member attending the gym',
  `gym_id` char(36) NOT NULL COMMENT 'The specific gym location',
  `check_in_time` datetime NOT NULL COMMENT 'Requirement: user reached gym',
  `check_out_time` datetime DEFAULT NULL,
  `duration_minutes` int(5) GENERATED ALWAYS AS (timestampdiff(MINUTE,`check_in_time`,`check_out_time`)) VIRTUAL,
  `method` enum('manual','qr_code','automatic_geofence','biometric') DEFAULT 'manual',
  `device_id` varchar(255) DEFAULT NULL COMMENT 'ID of the phone/device used for automatic check-in',
  `latitude_at_checkin` decimal(10,8) DEFAULT NULL,
  `longitude_at_checkin` decimal(11,8) DEFAULT NULL,
  `status` enum('present','completed','flagged') NOT NULL DEFAULT 'present',
  `is_verified` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Verified by Gym Admin/Trainer',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_attendance_user_date` (`user_id`,`check_in_time`),
  KEY `fk_attendance_gym_id` (`gym_id`),
  CONSTRAINT `fk_attendance_gym_id` FOREIGN KEY (`gym_id`) REFERENCES `gyms` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_attendance_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table blog_comments
# ------------------------------------------------------------

CREATE TABLE `blog_comments` (
  `id` char(36) NOT NULL,
  `blog_id` char(36) NOT NULL,
  `user_id` char(36) DEFAULT NULL COMMENT 'NULL for guest comments if allowed',
  `parent_id` char(36) DEFAULT NULL COMMENT 'For nested replies',
  `name` varchar(255) DEFAULT NULL COMMENT 'Guest name',
  `email` varchar(255) DEFAULT NULL COMMENT 'Guest email',
  `comment` text NOT NULL,
  `status` enum('pending','approved','spam','trashed') NOT NULL DEFAULT 'pending',
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_comments_blog` (`blog_id`),
  KEY `fk_comments_user` (`user_id`),
  KEY `fk_comments_parent` (`parent_id`),
  CONSTRAINT `fk_comments_blog` FOREIGN KEY (`blog_id`) REFERENCES `blogs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_comments_parent` FOREIGN KEY (`parent_id`) REFERENCES `blog_comments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_comments_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table blogs
# ------------------------------------------------------------

CREATE TABLE `blogs` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL COMMENT 'Author from users table',
  `category_id` char(36) DEFAULT NULL COMMENT 'Main category from categories table',
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` varchar(255) DEFAULT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT 0,
  `view_count` int(11) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `blogs_slug_unique` (`slug`),
  KEY `fk_blogs_author` (`user_id`),
  KEY `fk_blogs_category` (`category_id`),
  CONSTRAINT `fk_blogs_author` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_blogs_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table cache
# ------------------------------------------------------------

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table cache_locks
# ------------------------------------------------------------

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table categories
# ------------------------------------------------------------

CREATE TABLE `categories` (
  `id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `icon_class` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table custom_field_values
# ------------------------------------------------------------

CREATE TABLE `custom_field_values` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `custom_field_id` bigint(20) unsigned NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  `value` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `custom_field_values_custom_field_id_foreign` (`custom_field_id`),
  KEY `custom_field_values_model_id_custom_field_id_index` (`model_id`,`custom_field_id`),
  CONSTRAINT `custom_field_values_custom_field_id_foreign` FOREIGN KEY (`custom_field_id`) REFERENCES `custom_fields` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table custom_fields
# ------------------------------------------------------------

CREATE TABLE `custom_fields` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `label` varchar(255) NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'text',
  `validation_rules` text DEFAULT NULL,
  `options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`options`)),
  `placeholder` varchar(255) DEFAULT NULL,
  `help_text` varchar(255) DEFAULT NULL,
  `default_value` varchar(255) DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_required` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table diet_plan_items
# ------------------------------------------------------------

CREATE TABLE `diet_plan_items` (
  `id` char(36) NOT NULL,
  `diet_plan_id` char(36) NOT NULL,
  `meal_time` enum('breakfast','morning_snack','lunch','evening_snack','dinner','pre_workout','post_workout') NOT NULL,
  `food_name` varchar(255) NOT NULL,
  `portion_size` varchar(100) DEFAULT NULL COMMENT 'e.g., 200g, 2 eggs, 1 scoop',
  `calories_estimate` int(5) DEFAULT NULL,
  `day_of_week` enum('monday','tuesday','wednesday','thursday','friday','saturday','sunday','all_days') DEFAULT 'all_days',
  `order_index` int(2) DEFAULT 0 COMMENT 'To sort meals correctly in the app',
  PRIMARY KEY (`id`),
  KEY `fk_diet_items_plan` (`diet_plan_id`),
  CONSTRAINT `fk_diet_items_plan` FOREIGN KEY (`diet_plan_id`) REFERENCES `diet_plans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table diet_plans
# ------------------------------------------------------------

CREATE TABLE `diet_plans` (
  `id` char(36) NOT NULL,
  `creator_id` char(36) NOT NULL COMMENT 'Link to users table (Trainer/Gym Owner/Admin)',
  `user_id` char(36) DEFAULT NULL COMMENT 'NULL if this is a general template, or linked to a specific member',
  `name` varchar(255) NOT NULL COMMENT 'e.g., Summer Shredding, Muscle Bulk 3000',
  `description` text DEFAULT NULL,
  `diet_category` enum('veg','non_veg','eggitarian','vegan','keto','paleo') NOT NULL DEFAULT 'veg',
  `total_calories_target` int(5) unsigned DEFAULT NULL,
  `protein_grams` int(4) DEFAULT NULL,
  `carbs_grams` int(4) DEFAULT NULL,
  `fats_grams` int(4) DEFAULT NULL,
  `is_template` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1=Available for all gym members, 0=Custom for one user',
  `status` enum('active','inactive','draft') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_diet_plans_creator` (`creator_id`),
  KEY `fk_diet_plans_user` (`user_id`),
  CONSTRAINT `fk_diet_plans_creator` FOREIGN KEY (`creator_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_diet_plans_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table exercise_plan_items
# ------------------------------------------------------------

CREATE TABLE `exercise_plan_items` (
  `id` char(36) NOT NULL,
  `exercise_plan_id` char(36) NOT NULL,
  `exercise_name` varchar(255) NOT NULL COMMENT 'e.g., Bench Press, Squats',
  `target_muscle_group` enum('chest','back','legs','shoulders','arms','core','full_body') NOT NULL,
  `sets` int(2) DEFAULT 3,
  `reps` varchar(50) DEFAULT '10-12',
  `rest_period_seconds` int(4) DEFAULT 60,
  `instruction_video_url` varchar(255) DEFAULT NULL COMMENT 'Link to exercise tutorial',
  `tips` text DEFAULT NULL,
  `day_number` int(2) DEFAULT 1 COMMENT 'Day 1, Day 2, etc.',
  `order_index` int(2) DEFAULT 0 COMMENT 'Order of exercises within the day',
  PRIMARY KEY (`id`),
  KEY `fk_exercise_items_plan` (`exercise_plan_id`),
  CONSTRAINT `fk_exercise_items_plan` FOREIGN KEY (`exercise_plan_id`) REFERENCES `exercise_plans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table exercise_plans
# ------------------------------------------------------------

CREATE TABLE `exercise_plans` (
  `id` char(36) NOT NULL,
  `creator_id` char(36) NOT NULL COMMENT 'Link to users table (Trainer/Gym Owner/Admin)',
  `user_id` char(36) DEFAULT NULL COMMENT 'NULL for general templates, or linked to a specific member',
  `title` varchar(255) NOT NULL COMMENT 'e.g., 5-Day Split, Fat Burner, Leg Day',
  `description` text DEFAULT NULL,
  `difficulty_level` enum('beginner','intermediate','advanced','athlete') DEFAULT 'beginner',
  `body_type_target` enum('ectomorph','endomorph','mesomorph','general') DEFAULT 'general' COMMENT 'Requirement: body',
  `goal_type` enum('mass_gain','weight_loss','strength','flexibility') NOT NULL COMMENT 'Requirement: type',
  `is_template` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=Public template, 0=Private assigned plan',
  `estimated_duration_minutes` int(3) DEFAULT NULL,
  `status` enum('active','inactive','draft') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_exercise_plans_creator` (`creator_id`),
  KEY `fk_exercise_plans_user` (`user_id`),
  CONSTRAINT `fk_exercise_plans_creator` FOREIGN KEY (`creator_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_exercise_plans_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table failed_jobs
# ------------------------------------------------------------

CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table fcm_notification_logs
# ------------------------------------------------------------

CREATE TABLE `fcm_notification_logs` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL COMMENT 'The recipient of the notification',
  `fcm_token_id` char(36) DEFAULT NULL COMMENT 'Link to the specific device token used',
  `title` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `notification_type` enum('water_intake','attendance_alert','fee_reminder','gym_offer','admin_warning','live_feedback_reply','diet_plan_update') NOT NULL,
  `source_id` char(36) DEFAULT NULL COMMENT 'ID of the related record (e.g., fee_plan_id or attendance_id)',
  `status` enum('sent','delivered','failed','read') NOT NULL DEFAULT 'sent',
  `error_message` text DEFAULT NULL COMMENT 'Stores failure reason from FCM API',
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `read_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_fcm_logs_user` (`user_id`),
  KEY `idx_notification_type` (`notification_type`),
  KEY `fk_fcm_logs_token` (`fcm_token_id`),
  CONSTRAINT `fk_fcm_logs_token` FOREIGN KEY (`fcm_token_id`) REFERENCES `fcm_tokens` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_fcm_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table fcm_tokens
# ------------------------------------------------------------

CREATE TABLE `fcm_tokens` (
  `id` char(36) NOT NULL,
  `user_id` varchar(36) DEFAULT NULL,
  `token` text NOT NULL,
  `device_name` varchar(255) DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`) USING HASH,
  KEY `fcm_tokens_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table forum_replies
# ------------------------------------------------------------

CREATE TABLE `forum_replies` (
  `id` char(36) NOT NULL,
  `thread_id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `parent_id` char(36) DEFAULT NULL COMMENT 'For nested replies',
  `content` longtext NOT NULL,
  `is_best_answer` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_replies_thread` (`thread_id`),
  KEY `fk_replies_user` (`user_id`),
  CONSTRAINT `fk_replies_thread` FOREIGN KEY (`thread_id`) REFERENCES `forum_threads` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_replies_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table forum_threads
# ------------------------------------------------------------

CREATE TABLE `forum_threads` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL COMMENT 'Author from users table',
  `category_id` char(36) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `is_sticky` tinyint(1) NOT NULL DEFAULT 0,
  `is_locked` tinyint(1) NOT NULL DEFAULT 0,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Official NCS Posts',
  `view_count` int(11) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `forum_slug_unique` (`slug`),
  KEY `fk_forum_author` (`user_id`),
  KEY `fk_forum_category` (`category_id`),
  CONSTRAINT `fk_forum_author` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_forum_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table gym_enquiries
# ------------------------------------------------------------

CREATE TABLE `gym_enquiries` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL COMMENT 'The potential member asking the question',
  `gym_id` char(36) NOT NULL COMMENT 'The target gym for the enquiry',
  `subject` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `enquiry_type` enum('membership_plans','facilities','personal_training','trial_request','other') DEFAULT 'membership_plans',
  `status` enum('pending','in_progress','responded','closed') NOT NULL DEFAULT 'pending',
  `priority` enum('low','medium','high') DEFAULT 'low',
  `admin_response` text DEFAULT NULL COMMENT 'The reply sent by the gym owner or staff',
  `responded_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_enquiries_user` (`user_id`),
  KEY `fk_enquiries_gym` (`gym_id`),
  CONSTRAINT `fk_enquiries_gym` FOREIGN KEY (`gym_id`) REFERENCES `gyms` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_enquiries_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table gym_fee_plans
# ------------------------------------------------------------

CREATE TABLE `gym_fee_plans` (
  `id` char(36) NOT NULL,
  `gym_id` char(36) NOT NULL COMMENT 'Link to the gyms table',
  `name` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`features`)),
  `tagline` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL COMMENT 'Base price of the plan',
  `offer_price` decimal(10,2) DEFAULT NULL COMMENT 'Requirement: Send Offer / Discounted price',
  `duration_months` int(2) NOT NULL DEFAULT 1,
  `billing_cycle` enum('one_time','monthly','quarterly','yearly') DEFAULT 'monthly',
  `includes_diet_plan` tinyint(1) NOT NULL DEFAULT 0,
  `includes_trainer` tinyint(1) NOT NULL DEFAULT 0,
  `max_gym_visits_per_week` int(2) DEFAULT NULL COMMENT 'NULL for unlimited access',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_fee_plans_gym_id` (`gym_id`),
  CONSTRAINT `fk_fee_plans_gym_id` FOREIGN KEY (`gym_id`) REFERENCES `gyms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table gym_gallery_media
# ------------------------------------------------------------

CREATE TABLE `gym_gallery_media` (
  `id` char(36) NOT NULL,
  `gym_id` char(36) NOT NULL COMMENT 'Link to the gyms table',
  `file_type` enum('image','video','360_view','document') NOT NULL DEFAULT 'image',
  `collection_name` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `mime_type` varchar(255) DEFAULT NULL,
  `file_size` bigint(20) unsigned DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `order_index` int(3) DEFAULT 0 COMMENT 'To sort the gallery display in the app',
  `is_main_video` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Requirement: Intro Video',
  `status` varchar(255) NOT NULL DEFAULT 'approved',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_gallery_gym_id` (`gym_id`),
  KEY `idx_media_moderation` (`status`),
  CONSTRAINT `fk_gallery_gym_id` FOREIGN KEY (`gym_id`) REFERENCES `gyms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table gym_otp_verifications
# ------------------------------------------------------------

CREATE TABLE `gym_otp_verifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `gym_id` char(36) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `otp` varchar(255) NOT NULL,
  `expires_at` timestamp NOT NULL,
  `is_used` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `gym_otp_verifications_gym_id_foreign` (`gym_id`),
  CONSTRAINT `gym_otp_verifications_gym_id_foreign` FOREIGN KEY (`gym_id`) REFERENCES `gyms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table gym_reviews
# ------------------------------------------------------------

CREATE TABLE `gym_reviews` (
  `id` char(36) NOT NULL,
  `gym_id` char(36) NOT NULL COMMENT 'The gym being reviewed',
  `user_id` char(36) NOT NULL COMMENT 'The member providing feedback',
  `rating` tinyint(1) unsigned NOT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `anonymous_review` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Requirement: privacy depend',
  `status` enum('pending','published','hidden','flagged') NOT NULL DEFAULT 'published',
  `is_featured` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Admin can highlight good reviews',
  `owner_reply` text DEFAULT NULL,
  `replied_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_gym_review` (`user_id`,`gym_id`),
  KEY `fk_reviews_gym_id` (`gym_id`),
  CONSTRAINT `fk_reviews_gym_id` FOREIGN KEY (`gym_id`) REFERENCES `gyms` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_reviews_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table gym_subscriptions
# ------------------------------------------------------------

CREATE TABLE `gym_subscriptions` (
  `id` char(36) NOT NULL,
  `gym_id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `gym_fee_plan_id` char(36) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `amount_paid` decimal(10,2) NOT NULL,
  `payment_status` varchar(255) NOT NULL DEFAULT 'pending',
  `payment_method` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `gym_subscriptions_gym_id_foreign` (`gym_id`),
  KEY `gym_subscriptions_user_id_foreign` (`user_id`),
  KEY `gym_subscriptions_gym_fee_plan_id_foreign` (`gym_fee_plan_id`),
  CONSTRAINT `gym_subscriptions_gym_fee_plan_id_foreign` FOREIGN KEY (`gym_fee_plan_id`) REFERENCES `gym_fee_plans` (`id`) ON DELETE CASCADE,
  CONSTRAINT `gym_subscriptions_gym_id_foreign` FOREIGN KEY (`gym_id`) REFERENCES `gyms` (`id`) ON DELETE CASCADE,
  CONSTRAINT `gym_subscriptions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table gyms
# ------------------------------------------------------------

CREATE TABLE `gyms` (
  `id` char(36) NOT NULL,
  `owner_id` char(36) NOT NULL COMMENT 'Link to users table (Admin/Gym Owner)',
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `brand_name` varchar(255) DEFAULT NULL,
  `logo_path` varchar(255) DEFAULT NULL,
  `intro_video_url` varchar(255) DEFAULT NULL COMMENT 'Requirement: Intro Video for Gym Profile',
  `address` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `search_radius_km` int(3) DEFAULT 10 COMMENT 'Default radius for user discovery',
  `is_sponsored` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Requirement: Highlighted for Advertising',
  `member_count_limit` int(11) DEFAULT NULL COMMENT 'Requirement: Member Count tracking',
  `rating_avg` decimal(3,2) DEFAULT 0.00,
  `status` enum('pending','active','suspended','rejected','inactive') NOT NULL DEFAULT 'pending',
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `platform_plan_id` char(36) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `gyms_slug_unique` (`slug`),
  KEY `idx_gym_location` (`latitude`,`longitude`),
  KEY `fk_gyms_owner_id` (`owner_id`),
  KEY `gyms_platform_plan_id_foreign` (`platform_plan_id`),
  CONSTRAINT `fk_gyms_owner_id` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `gyms_platform_plan_id_foreign` FOREIGN KEY (`platform_plan_id`) REFERENCES `platform_subscription_plans` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table health_logs
# ------------------------------------------------------------

CREATE TABLE `health_logs` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL COMMENT 'Link to the user being tracked',
  `weight` decimal(5,2) NOT NULL COMMENT 'Current weight in kg for graph plotting',
  `bmi` decimal(4,2) DEFAULT NULL COMMENT 'Calculated Body Mass Index',
  `body_fat_percentage` decimal(4,2) DEFAULT NULL,
  `snap_path` varchar(255) DEFAULT NULL COMMENT 'File path for the "Today Snap"',
  `snap_type` enum('front','side','back','other') DEFAULT 'front',
  `log_date` date NOT NULL COMMENT 'The specific day this record represents',
  `water_intake_ml` int(5) DEFAULT 0 COMMENT 'Requirement: Water intake notification tracking',
  `notes` text DEFAULT NULL COMMENT 'User comments on their physical feeling or progress',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_daily_log` (`user_id`,`log_date`),
  CONSTRAINT `fk_health_logs_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table job_batches
# ------------------------------------------------------------

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table jobs
# ------------------------------------------------------------

CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table likes
# ------------------------------------------------------------

CREATE TABLE `likes` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `likeable_id` char(36) NOT NULL,
  `likeable_type` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_likeable` (`user_id`,`likeable_id`,`likeable_type`),
  KEY `idx_likeable_lookup` (`likeable_id`,`likeable_type`),
  CONSTRAINT `fk_likes_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table media
# ------------------------------------------------------------

CREATE TABLE `media` (
  `id` char(36) NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` char(36) NOT NULL,
  `uuid` char(36) DEFAULT NULL,
  `collection_name` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `mime_type` varchar(255) DEFAULT NULL,
  `disk` varchar(255) NOT NULL,
  `conversions_disk` varchar(255) DEFAULT NULL,
  `size` bigint(20) unsigned NOT NULL,
  `manipulations` longtext DEFAULT NULL,
  `custom_properties` longtext DEFAULT NULL,
  `generated_conversions` longtext DEFAULT NULL,
  `responsive_images` longtext DEFAULT NULL,
  `order_column` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `media_uuid_unique` (`uuid`),
  KEY `media_model_type_model_id_index` (`model_type`,`model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table media_galleries
# ------------------------------------------------------------

CREATE TABLE `media_galleries` (
  `id` char(36) NOT NULL,
  `title` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `extension` varchar(50) NOT NULL,
  `mime_type` varchar(100) NOT NULL,
  `file_type` varchar(50) NOT NULL,
  `category_id` char(36) DEFAULT NULL,
  `file_size` bigint(20) unsigned NOT NULL,
  `uploaded_by` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_media_uploaded_by` (`uploaded_by`),
  KEY `fk_media_category` (`category_id`),
  CONSTRAINT `fk_media_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_media_uploaded_by` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table membership_plan_templates
# ------------------------------------------------------------

CREATE TABLE `membership_plan_templates` (
  `id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `tagline` varchar(255) DEFAULT NULL,
  `color_code` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `offer_price` decimal(10,2) DEFAULT NULL,
  `duration_months` int(11) NOT NULL,
  `features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`features`)),
  `includes_diet_plan` tinyint(1) NOT NULL DEFAULT 0,
  `includes_trainer` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table migrations
# ------------------------------------------------------------

CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table model_has_permissions
# ------------------------------------------------------------

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` char(36) NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table model_has_roles
# ------------------------------------------------------------

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` char(36) NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table notifications
# ------------------------------------------------------------

CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` char(36) NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table pages
# ------------------------------------------------------------

CREATE TABLE `pages` (
  `id` char(100) NOT NULL,
  `title` text DEFAULT NULL,
  `type` enum('privacy','terms','aboutus','refund','pricing','disclaimer','home') DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `isActive` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



# Dump of table password_reset_tokens
# ------------------------------------------------------------

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table permissions
# ------------------------------------------------------------

CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `group_name` varchar(255) DEFAULT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table personal_access_tokens
# ------------------------------------------------------------

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` varchar(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table platform_subscription_plans
# ------------------------------------------------------------

CREATE TABLE `platform_subscription_plans` (
  `id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `monthly_price` decimal(10,2) NOT NULL,
  `yearly_price` decimal(10,2) DEFAULT NULL,
  `max_gyms` int(11) NOT NULL DEFAULT 1,
  `max_members` int(11) DEFAULT NULL,
  `has_analytics` tinyint(1) NOT NULL DEFAULT 0,
  `has_mobile_app` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table recipes
# ------------------------------------------------------------

CREATE TABLE `recipes` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL COMMENT 'The author of the recipe',
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `ingredients` text NOT NULL,
  `instructions` longtext NOT NULL,
  `cooking_time_minutes` int(4) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `calories_per_serving` int(5) DEFAULT NULL,
  `diet_type` enum('veg','non_veg','eggitarian','vegan') NOT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT 1,
  `view_count` int(11) unsigned DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `recipes_slug_unique` (`slug`),
  KEY `fk_recipes_user` (`user_id`),
  CONSTRAINT `fk_recipes_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table role_has_permissions
# ------------------------------------------------------------

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table roles
# ------------------------------------------------------------

CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table sessions
# ------------------------------------------------------------

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table settings
# ------------------------------------------------------------

CREATE TABLE `settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `key` longtext DEFAULT NULL,
  `value` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table user_profiles
# ------------------------------------------------------------

CREATE TABLE `user_profiles` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL COMMENT 'One-to-one link to the users table',
  `alternative_contact` varchar(20) DEFAULT NULL COMMENT 'Secondary contact number for emergencies',
  `gender` enum('male','female','other','prefer_not_to_say') DEFAULT NULL,
  `age` int(3) unsigned DEFAULT NULL,
  `current_weight` decimal(5,2) DEFAULT NULL COMMENT 'Stored in kg (e.g., 75.50) for progress tracking',
  `height` decimal(5,2) DEFAULT NULL COMMENT 'Stored in cm for BMI calculations',
  `goal_type` enum('weight_gain','weight_loss','maintenance','muscle_building') DEFAULT 'maintenance',
  `activity_level` enum('sedentary','lightly_active','moderately_active','very_active','extra_active') DEFAULT 'sedentary',
  `diet_type` enum('veg','non_veg','eggitarian','vegan','keto','paleo') NOT NULL DEFAULT 'veg',
  `medical_conditions` text DEFAULT NULL COMMENT 'Pre-existing conditions like Diabetes or Hypertension',
  `allergies` text DEFAULT NULL COMMENT 'Food or environmental allergies',
  `physical_limitations` text DEFAULT NULL COMMENT 'Injuries or disabilities affecting exercise plans',
  `is_public` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0=Private, 1=Public (Controls "Find Friend" visibility)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_profiles_user_id_unique` (`user_id`),
  CONSTRAINT `fk_user_profiles_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table users
# ------------------------------------------------------------

CREATE TABLE `users` (
  `id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `fcm` text DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `password` varchar(255) NOT NULL,
  `user_type` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0=super-admin1=admin,2=coach,3=seekers',
  `otp_expires_at` timestamp NULL DEFAULT NULL,
  `otp` int(10) DEFAULT NULL,
  `profile_image` text DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `phone` (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of views
# ------------------------------------------------------------

# Creating temporary tables to overcome VIEW dependency errors


/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

# Dump completed on 2026-04-24T12:21:33+05:30
