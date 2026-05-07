# ************************************************************
# Antares - SQL Client
# Version 0.7.35
# 
# https://antares-sql.app/
# https://github.com/antares-sql/antares
# 
# Host: srv1498.hstgr.io (MariaDB Server 11.8.6)
# Database: u296679159_FitPaxPro
# Generation time: 2026-05-07T11:41:12+05:30
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

DROP TABLE IF EXISTS `admin_warnings`;

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

DROP TABLE IF EXISTS `attendance_logs`;

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





# Dump of table banners
# ------------------------------------------------------------

DROP TABLE IF EXISTS `banners`;

CREATE TABLE `banners` (
  `id` char(36) NOT NULL,
  `badge_text` varchar(255) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `background_color_hex` varchar(255) DEFAULT NULL,
  `image_url` varchar(255) NOT NULL,
  `target_link` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `banners` WRITE;
/*!40000 ALTER TABLE `banners` DISABLE KEYS */;

INSERT INTO `banners` (`id`, `badge_text`, `title`, `background_color_hex`, `image_url`, `target_link`, `is_active`, `order`, `created_at`, `updated_at`) VALUES
	("a19e8c91-7e12-41f0-840e-5c2554136d49", "OFFER", "Save 50% on Annual Plans!", "#004085", "banners/gbQdcfjeSj5TJwPjp0dsHqovFZQj3ptTmj1OqyK3.jpg", "plan_123", 1, 1, "2026-04-24 09:35:53", "2026-04-24 12:16:48"),
	("a19e8c93-8e91-408c-9560-d9f38f21744b", "NEW", "Try CrossFit Training", "#2e0007", "banners/uNtXNIJBTEuIQsvf6xmKxAxOgwJyamAuJ9JLHQ64.png", "category_crossfit", 1, 2, "2026-04-24 09:35:53", "2026-04-24 12:15:43"),
	("a19ec29e-737c-44b0-9195-c6cba86afdc0", "Latest", "80% Discount for female", "#008a02", "banners/QnBlFtpvAde4EOKwfbbeoRH73wYbQ3dCvgybsZsE.png", "New-Plan", 1, 2, "2026-04-24 12:07:00", "2026-04-24 12:16:34");

/*!40000 ALTER TABLE `banners` ENABLE KEYS */;
UNLOCK TABLES;



# Dump of table blog_comments
# ------------------------------------------------------------

DROP TABLE IF EXISTS `blog_comments`;

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

DROP TABLE IF EXISTS `blogs`;

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

DROP TABLE IF EXISTS `cache`;

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;





# Dump of table cache_locks
# ------------------------------------------------------------

DROP TABLE IF EXISTS `cache_locks`;

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;





# Dump of table categories
# ------------------------------------------------------------

DROP TABLE IF EXISTS `categories`;

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

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;

INSERT INTO `categories` (`id`, `name`, `slug`, `icon_class`, `is_active`, `created_at`, `updated_at`) VALUES
	("a19eb58d-7776-4c42-9686-459e80d56eb6", "strength", "strength", "mdi mdi-dumbbell", 1, "2026-04-24 11:30:28", "2026-04-24 11:30:28"),
	("a19eb5bf-8449-460c-9053-81d0aff249cd", "Home Workout", "home-workout", "mdi mdi-home", 1, "2026-04-24 11:31:01", "2026-04-24 11:31:01");

/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;



# Dump of table comments
# ------------------------------------------------------------

DROP TABLE IF EXISTS `comments`;

CREATE TABLE `comments` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `commentable_id` char(36) NOT NULL,
  `commentable_type` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `parent_id` char(36) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'approved',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `comments_user_id_foreign` (`user_id`),
  KEY `comments_parent_id_foreign` (`parent_id`),
  KEY `comments_commentable_id_commentable_type_index` (`commentable_id`,`commentable_type`),
  CONSTRAINT `comments_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;





# Dump of table custom_field_values
# ------------------------------------------------------------

DROP TABLE IF EXISTS `custom_field_values`;

CREATE TABLE `custom_field_values` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `custom_field_id` bigint(20) unsigned NOT NULL,
  `model_id` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `custom_field_values_custom_field_id_foreign` (`custom_field_id`),
  KEY `custom_field_values_model_id_custom_field_id_index` (`model_id`,`custom_field_id`),
  CONSTRAINT `custom_field_values_custom_field_id_foreign` FOREIGN KEY (`custom_field_id`) REFERENCES `custom_fields` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `custom_field_values` WRITE;
/*!40000 ALTER TABLE `custom_field_values` DISABLE KEYS */;

INSERT INTO `custom_field_values` (`id`, `custom_field_id`, `model_id`, `value`, `created_at`, `updated_at`) VALUES
	(1, 1, "a19c8e84-2c38-4612-ad5e-356e87c7cf19", "56", "2026-04-24 12:29:34", "2026-04-24 12:29:34"),
	(2, 2, "a19c8e84-2c38-4612-ad5e-356e87c7cf19", "male", "2026-04-24 12:29:34", "2026-04-24 12:29:34");

/*!40000 ALTER TABLE `custom_field_values` ENABLE KEYS */;
UNLOCK TABLES;



# Dump of table custom_fields
# ------------------------------------------------------------

DROP TABLE IF EXISTS `custom_fields`;

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

LOCK TABLES `custom_fields` WRITE;
/*!40000 ALTER TABLE `custom_fields` DISABLE KEYS */;

INSERT INTO `custom_fields` (`id`, `name`, `label`, `model_type`, `type`, `validation_rules`, `options`, `placeholder`, `help_text`, `default_value`, `order`, `is_active`, `is_required`, `created_at`, `updated_at`) VALUES
	(1, "age", "Age", "App\\Models\\Gym", "text", NULL, NULL, "Enter Your Age", "Your Age not greater then the 80", NULL, 1, 1, 1, "2026-04-17 10:01:26", "2026-04-17 10:04:32"),
	(2, "gender", "Gender", "App\\Models\\Gym", "radio", NULL, "[\"male\",\"female\",\"others\"]", NULL, "Enter your gender", NULL, 1, 1, 1, "2026-04-17 10:25:45", "2026-04-17 10:26:19");

/*!40000 ALTER TABLE `custom_fields` ENABLE KEYS */;
UNLOCK TABLES;



# Dump of table diet_plan_items
# ------------------------------------------------------------

DROP TABLE IF EXISTS `diet_plan_items`;

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

DROP TABLE IF EXISTS `diet_plans`;

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

DROP TABLE IF EXISTS `exercise_plan_items`;

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

DROP TABLE IF EXISTS `exercise_plans`;

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

DROP TABLE IF EXISTS `failed_jobs`;

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

DROP TABLE IF EXISTS `fcm_notification_logs`;

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

DROP TABLE IF EXISTS `fcm_tokens`;

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

DROP TABLE IF EXISTS `forum_replies`;

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

DROP TABLE IF EXISTS `forum_threads`;

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





# Dump of table gym_category
# ------------------------------------------------------------

DROP TABLE IF EXISTS `gym_category`;

CREATE TABLE `gym_category` (
  `gym_id` char(36) NOT NULL,
  `category_id` char(36) NOT NULL,
  PRIMARY KEY (`gym_id`,`category_id`),
  KEY `gym_category_category_id_foreign` (`category_id`),
  CONSTRAINT `gym_category_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `gym_category_gym_id_foreign` FOREIGN KEY (`gym_id`) REFERENCES `gyms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `gym_category` WRITE;
/*!40000 ALTER TABLE `gym_category` DISABLE KEYS */;

INSERT INTO `gym_category` (`gym_id`, `category_id`) VALUES
	("a19c8e84-2c38-4612-ad5e-356e87c7cf19", "a19eb58d-7776-4c42-9686-459e80d56eb6"),
	("a19c8e84-2c38-4612-ad5e-356e87c7cf19", "a19eb5bf-8449-460c-9053-81d0aff249cd");

/*!40000 ALTER TABLE `gym_category` ENABLE KEYS */;
UNLOCK TABLES;



# Dump of table gym_enquiries
# ------------------------------------------------------------

DROP TABLE IF EXISTS `gym_enquiries`;

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

DROP TABLE IF EXISTS `gym_fee_plans`;

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

LOCK TABLES `gym_fee_plans` WRITE;
/*!40000 ALTER TABLE `gym_fee_plans` DISABLE KEYS */;

INSERT INTO `gym_fee_plans` (`id`, `gym_id`, `name`, `image`, `title`, `description`, `features`, `tagline`, `price`, `offer_price`, `duration_months`, `billing_cycle`, `includes_diet_plan`, `includes_trainer`, `max_gym_visits_per_week`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
	("a1969a39-ad96-43e5-ae02-93430e9c9f9a", "a1969981-16ea-49fd-bf3f-0c7ca493d326", "Elite operative", NULL, NULL, NULL, NULL, "Advanced Tactical Training", 2999, 2499, 12, "monthly", 1, 1, NULL, 1, "2026-04-20 10:47:26", "2026-04-20 10:47:26", NULL),
	("a19a53f8-494b-46d1-bf61-d1a16315e32a", "a19a53f8-48a6-4999-a6aa-f2314ca9ddf7", "new gym plan", NULL, NULL, NULL, NULL, "new force gym", 648, 254, 12, "monthly", 1, 1, NULL, 1, "2026-04-22 07:14:18", "2026-04-22 07:14:18", NULL),
	("a19a5be4-4f5e-49b3-ac53-81a14146e735", "a19a5bb7-c4be-403a-b708-13da64adb288", "elite gym", NULL, NULL, NULL, NULL, "gzb", 3618, 94, 12, "monthly", 1, 1, NULL, 1, "2026-04-22 07:36:27", "2026-04-22 07:36:27", NULL),
	("a19a5d64-16ca-4ca2-87f7-55b05f5f1b7b", "a19a5d64-164a-47b1-a7cb-44832c11e475", "jdjs", NULL, NULL, NULL, NULL, "hs", 9464, 949, 12, "monthly", 1, 1, NULL, 1, "2026-04-22 07:40:38", "2026-04-22 07:40:38", NULL),
	("a19a67fe-d3e7-4e86-b737-48f3a8f14632", "a19a67fe-d358-4b65-9eed-0d386213dfb8", "Elite pack for males", "gyms/plans/VTUKq2BpBj6KRBqHpX5XnMqJLYV17c6b9XGm0ICq.png", NULL, "full access on all membership to access all resources", NULL, "go to fitness you win", 5236, 5368, 12, "monthly", 1, 1, NULL, 1, "2026-04-22 08:10:17", "2026-04-22 10:20:46", NULL),
	("a19c6f95-b0de-485f-ac38-6ae77237fa2e", "a19c6f95-b024-4025-af34-497782842ace", "plan name is bew", NULL, NULL, NULL, NULL, "Advance technical xourse", 3628, 299, 12, "monthly", 1, 1, NULL, 1, "2026-04-23 08:23:10", "2026-04-23 08:23:10", NULL),
	("a19c8833-5f04-4736-a610-e142754e6b56", "a19c8833-5beb-47b4-8ffa-be7293ba9d1e", "ohwdbowhxohw", NULL, NULL, NULL, NULL, "hdehex", 0, 681, 12, "monthly", 1, 1, NULL, 1, "2026-04-23 09:32:00", "2026-04-23 09:32:00", NULL),
	("a19c8d46-697b-4586-92d7-61e6d972e7a7", "a19c8833-5beb-47b4-8ffa-be7293ba9d1e", "ohwdbowhxohw", NULL, NULL, NULL, NULL, "hdehex", 0, 681, 12, "monthly", 1, 1, NULL, 1, "2026-04-23 09:46:11", "2026-04-23 09:46:11", NULL),
	("a19c8e84-2ca9-47bb-8516-26b1d072bbe7", "a19c8e84-2c38-4612-ad5e-356e87c7cf19", "Ajdjdn", NULL, NULL, NULL, NULL, "hsjssns", 64, 94, 12, "monthly", 1, 1, NULL, 1, "2026-04-23 09:49:39", "2026-04-23 09:49:39", NULL);

/*!40000 ALTER TABLE `gym_fee_plans` ENABLE KEYS */;
UNLOCK TABLES;



# Dump of table gym_gallery_media
# ------------------------------------------------------------

DROP TABLE IF EXISTS `gym_gallery_media`;

CREATE TABLE `gym_gallery_media` (
  `id` char(36) NOT NULL,
  `gym_id` char(36) NOT NULL COMMENT 'Link to the gyms table',
  `file_type` varchar(50) NOT NULL DEFAULT 'image',
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

LOCK TABLES `gym_gallery_media` WRITE;
/*!40000 ALTER TABLE `gym_gallery_media` DISABLE KEYS */;

INSERT INTO `gym_gallery_media` (`id`, `gym_id`, `file_type`, `collection_name`, `file_path`, `file_name`, `mime_type`, `file_size`, `title`, `description`, `order_index`, `is_main_video`, `status`, `created_at`, `updated_at`) VALUES
	("a19a5bf4-c6d1-4798-896d-c2e3945e3274", "a19a5bb7-c4be-403a-b708-13da64adb288", "image", NULL, "gyms/gallery/uCY0D5eDRhChuFR67qMAo7Dz85ZsAD53tzxPSzXp.jpg", "IMG-20260421-WA0000.jpg", "application/octet-stream", 251291, NULL, NULL, 0, 0, "approved", "2026-04-22 07:36:38", "2026-04-22 07:36:38"),
	("a19a694e-2572-456e-afd0-2dc209c6eb36", "a19a67fe-d358-4b65-9eed-0d386213dfb8", "image", NULL, "gyms/gallery/3xGHOQhoOJt0IwGolCohxAWkcxCusYRV8uBwX5nx.png", "Screenshot_20260421-175649.LinkedIn.png", "image/png", 118047, NULL, NULL, 0, 0, "approved", "2026-04-22 08:13:57", "2026-04-22 08:13:57"),
	("a19c8d5f-706a-4c9b-aa9c-bc4018835615", "a19c8833-5beb-47b4-8ffa-be7293ba9d1e", "image", NULL, "gyms/gallery/QkqXEK9VKiLqzJvpviFnIy5qDlX3tDYIYAJCtveM.png", "Screenshot_20260423-101635.IRCTC Rail Connect.png", "image/png", 117746, NULL, NULL, 0, 0, "approved", "2026-04-23 09:46:28", "2026-04-23 09:46:28"),
	("a19c8d5f-713e-4a88-bc9b-50340771a809", "a19c8833-5beb-47b4-8ffa-be7293ba9d1e", "image", NULL, "gyms/gallery/5xKqS2FOezrbqDTLWj7Vn52W0FWVr87uM8xCvYlQ.png", "Screenshot_20260421-175649.LinkedIn.png", "image/png", 118047, NULL, NULL, 0, 0, "approved", "2026-04-23 09:46:28", "2026-04-23 09:46:28"),
	("a1a4876d-2959-4ac5-aa56-cd734757d51c", "a19c8e84-2c38-4612-ad5e-356e87c7cf19", "youtube", NULL, "https://youtu.be/9nN9FJxK1wQ?si=qheuwLJ7n8Kym6lR", "YouTube Video", "text/url", NULL, NULL, NULL, 0, 0, "approved", "2026-04-27 08:56:27", "2026-04-27 08:56:27"),
	("a1a4876d-2a39-4b4f-a972-7790e5e47d01", "a19c8e84-2c38-4612-ad5e-356e87c7cf19", "youtube", NULL, "https://youtu.be/dyzgXkWqN6A?si=l1_wPnUZnYkxFScB", "YouTube Video", "text/url", NULL, NULL, NULL, 0, 0, "approved", "2026-04-27 08:56:27", "2026-04-27 08:56:27");

/*!40000 ALTER TABLE `gym_gallery_media` ENABLE KEYS */;
UNLOCK TABLES;



# Dump of table gym_otp_verifications
# ------------------------------------------------------------

DROP TABLE IF EXISTS `gym_otp_verifications`;

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
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `gym_otp_verifications` WRITE;
/*!40000 ALTER TABLE `gym_otp_verifications` DISABLE KEYS */;

INSERT INTO `gym_otp_verifications` (`id`, `gym_id`, `email`, `otp`, `expires_at`, `is_used`, `created_at`, `updated_at`) VALUES
	(43, NULL, "ak2sh@gmail.com", "556660", "2026-05-01 06:49:38", 0, "2026-05-01 06:34:38", NULL),
	(44, NULL, "arvindverma630635@gmail.com", "354562", "2026-05-05 12:25:11", 0, "2026-05-05 12:10:11", NULL);

/*!40000 ALTER TABLE `gym_otp_verifications` ENABLE KEYS */;
UNLOCK TABLES;



# Dump of table gym_reviews
# ------------------------------------------------------------

DROP TABLE IF EXISTS `gym_reviews`;

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

DROP TABLE IF EXISTS `gym_subscriptions`;

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

DROP TABLE IF EXISTS `gyms`;

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

LOCK TABLES `gyms` WRITE;
/*!40000 ALTER TABLE `gyms` DISABLE KEYS */;

INSERT INTO `gyms` (`id`, `owner_id`, `name`, `email`, `phone`, `slug`, `description`, `brand_name`, `logo_path`, `intro_video_url`, `address`, `image`, `city`, `latitude`, `longitude`, `search_radius_km`, `is_sponsored`, `member_count_limit`, `rating_avg`, `status`, `is_verified`, `created_at`, `updated_at`, `deleted_at`, `platform_plan_id`) VALUES
	("a19048d0-f35f-4359-a5da-355858cd9c34", "a19041fb-a4de-4c78-a340-f09a6752a53c", "team backendcoders", "teambackendcoders@gmail.com", "1232423423", "team-backendcoders", NULL, NULL, NULL, NULL, "63, 11/42, Krishna Nagar Road", "gyms/NozInT5M8EWk5c7ODJf7QCuzpT3Btb0frZz83Czl.png", NULL, NULL, NULL, 10, 1, 23, 0, "active", 1, "2026-04-17 07:24:50", "2026-04-24 11:44:28", NULL, "a190474f-7371-4658-b622-22109622dd37"),
	("a1969981-16ea-49fd-bf3f-0c7ca493d326", "a1969733-b372-4350-bb85-a8f7c26bd2b0", "Iron Force Elite", "john@fitpaxpro.com", "+911234567890", "iron-force-elite", NULL, NULL, NULL, NULL, "string", "gyms/1yWeikQrTH1RVkPEVR6P2mq3b9Yz4v5q9E5KxGCH.png", "string", 0, 0, 10, 0, NULL, 0, "pending", 0, "2026-04-20 10:45:25", "2026-04-22 12:08:02", NULL, NULL),
	("a19a53f8-48a6-4999-a6aa-f2314ca9ddf7", "a19a53ad-e902-4ad7-b420-d51ec5fd070c", "Iron force", "ops@fitpaxpro.com", "2356894175", "iron-force-2", NULL, NULL, NULL, NULL, "mode", "gyms/JV3r8uooDWfgzgjSaDjvq7TjXbB3sA3XonGMesZ5.webp", "kanpur nagar", 61.494, 64.4646, 10, 0, NULL, 0, "active", 0, "2026-04-22 07:14:18", "2026-04-24 11:47:42", NULL, NULL),
	("a19a5bb7-c4be-403a-b708-13da64adb288", "a19a5b84-65be-4bc0-bc04-0786d2380533", "Gyms and open", "owner@gnail.xom", "3126459780", "gyms-and-open", NULL, NULL, NULL, NULL, NULL, "gyms/G1RwLbHNjBBDDaknLnscGiZuQQbm9ktaafFUND5k.jpg", NULL, NULL, NULL, 10, 0, NULL, 0, "pending", 0, "2026-04-22 07:35:58", "2026-04-22 07:36:38", NULL, NULL),
	("a19a5d64-164a-47b1-a7cb-44832c11e475", "a19a5d32-84e5-4f77-a004-25b4d9561ce3", "gym owner", "gym@gmail.cok", "64371827840", "gym-owner", NULL, NULL, NULL, NULL, NULL, "gyms/1DHBoWGD0Wv6U2s27xz3jP6Lr4NQucJpVbhl4pb6.jpg", NULL, NULL, NULL, 10, 0, NULL, 0, "pending", 0, "2026-04-22 07:40:38", "2026-04-22 07:40:54", NULL, NULL),
	("a19a67fe-d358-4b65-9eed-0d386213dfb8", "a19a64fe-1da2-4a87-bf9f-9fcf550dfab2", "lol", "hshs@jsj.sjsj", "6466494944", "lol", NULL, NULL, NULL, NULL, "11/42/66, Krishna Nagar, 208007", "gyms/ckdHn0zBbDtmWf3H8g1xnihJfNunTYQrvlVBrd82.jpg", "Kanpur", 26.420571, 80.372209, 10, 0, NULL, 0, "pending", 0, "2026-04-22 08:10:17", "2026-04-23 09:54:00", NULL, NULL),
	("a19c6f95-b024-4025-af34-497782842ace", "a19c6f42-85a2-48e4-a4ff-4e4d54e3ccff", "Elite force", "akash1@gmail.com", "31254697854", "elite-force", NULL, NULL, NULL, NULL, NULL, "gyms/w91HaTxgLnzr3WciUrozJ0DzD93XFnR8zYExa3rf.png", NULL, NULL, NULL, 10, 0, NULL, 0, "pending", 0, "2026-04-23 08:23:10", "2026-04-23 08:32:30", NULL, NULL),
	("a19c8833-5beb-47b4-8ffa-be7293ba9d1e", "a19c7493-0900-49f2-a920-5a965b8481b7", "giibibob", "hshsh@hwh.xj", "6464646444", "giibibob", NULL, NULL, NULL, NULL, NULL, "gyms/t0KqfO34zj3k3qePZYBbyLsP2ArtqqZNmKgNIbMy.png", NULL, NULL, NULL, 10, 0, NULL, 0, "pending", 0, "2026-04-23 09:32:00", "2026-04-23 09:46:28", NULL, NULL),
	("a19c8e84-2c38-4612-ad5e-356e87c7cf19", "a19c8e46-5364-4f67-b0be-98d268e96c69", "gym 1", "gym12@gmail.co", "643494646", "gym-1", NULL, NULL, NULL, NULL, "11/42/66, Krishna Nagar, 208007", "gyms/h5Q9kYQHwbKEoc7VAncVLKYZPX9xQESsndYVtQRh.avif", "Kanpur", 26.420568, 80.3722, 10, 0, NULL, 0, "active", 0, "2026-04-23 09:49:39", "2026-04-24 11:48:35", NULL, NULL);

/*!40000 ALTER TABLE `gyms` ENABLE KEYS */;
UNLOCK TABLES;



# Dump of table health_logs
# ------------------------------------------------------------

DROP TABLE IF EXISTS `health_logs`;

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

DROP TABLE IF EXISTS `job_batches`;

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

DROP TABLE IF EXISTS `jobs`;

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

DROP TABLE IF EXISTS `likes`;

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

DROP TABLE IF EXISTS `media`;

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

DROP TABLE IF EXISTS `media_galleries`;

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

DROP TABLE IF EXISTS `membership_plan_templates`;

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

LOCK TABLES `membership_plan_templates` WRITE;
/*!40000 ALTER TABLE `membership_plan_templates` DISABLE KEYS */;

INSERT INTO `membership_plan_templates` (`id`, `name`, `image`, `tagline`, `color_code`, `description`, `price`, `offer_price`, `duration_months`, `features`, `includes_diet_plan`, `includes_trainer`, `is_active`, `sort_order`, `created_at`, `updated_at`, `deleted_at`) VALUES
	("a190421b-f77e-4a63-8a58-87373b93a094", "1 monthly plan", NULL, "Building", NULL, NULL, 12332, NULL, 1, NULL, 1, 1, 1, 0, "2026-04-17 07:06:04", "2026-04-23 06:53:06", NULL);

/*!40000 ALTER TABLE `membership_plan_templates` ENABLE KEYS */;
UNLOCK TABLES;



# Dump of table migrations
# ------------------------------------------------------------

DROP TABLE IF EXISTS `migrations`;

CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(6, "2019_12_14_000001_create_personal_access_tokens_table", 1),
	(7, "2026_04_17_000000_create_gym_subscriptions_table", 1),
	(8, "2026_04_17_000001_create_membership_plan_templates_table", 1),
	(9, "2026_04_17_000002_add_extra_fields_to_plans", 1),
	(10, "2026_04_17_000003_create_platform_subscription_plans_table", 1),
	(11, "2026_04_17_094141_create_custom_fields_table", 2),
	(12, "2026_04_17_094146_create_custom_fields_table", 3),
	(13, "2026_04_17_094153_create_custom_field_values_table", 3),
	(14, "2026_04_20_000000_fix_gym_gallery_media_table", 4),
	(15, "2026_04_20_000001_make_gym_gallery_media_columns_nullable", 5),
	(16, "2026_04_20_000002_add_rich_fields_to_gym_fee_plans", 6),
	(17, "2026_04_20_000003_add_image_to_membership_plan_templates", 7),
	(18, "2026_04_20_000004_update_gym_status_enum", 8),
	(19, "2026_04_20_000005_update_personal_access_tokens_for_uuids", 9),
	(20, "2026_04_20_000006_create_gym_otp_verifications_table", 10),
	(21, "2026_04_20_000007_make_gym_id_nullable_in_otp", 11),
	(22, "2026_04_20_000008_make_gym_fields_nullable_for_registration", 12),
	(23, "2026_04_20_000009_make_title_nullable_in_gym_fee_plans", 13),
	(24, "2026_04_24_065648_add_extra_fields_to_user_profiles_table", 14),
	(25, "2026_04_24_065649_create_user_body_measurements_table", 14),
	(26, "2026_04_24_093409_create_banners_table", 15),
	(27, "2026_04_24_120855_create_gym_category_table", 16),
	(28, "2026_04_24_122911_change_model_id_to_string_in_custom_field_values_table", 17),
	(29, "2026_04_27_070000_add_youtube_to_gym_gallery_media_file_type", 18),
	(30, "2026_04_27_081738_create_comments_table", 19);

/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;



# Dump of table model_has_permissions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `model_has_permissions`;

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

DROP TABLE IF EXISTS `model_has_roles`;

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

DROP TABLE IF EXISTS `notifications`;

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

DROP TABLE IF EXISTS `pages`;

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

DROP TABLE IF EXISTS `password_reset_tokens`;

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;





# Dump of table permissions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `permissions`;

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

DROP TABLE IF EXISTS `personal_access_tokens`;

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
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
	(1, "App\\Models\\User", "a1968d88-bcef-4083-a8d0-70fefc5bc1c8", "iPhone 15", "c0ac48ff4f1531a22db300fe7048fe075c5e1ff31c0d11624baa15bbcc3cc94b", "[\"*\"]", NULL, NULL, "2026-04-20 10:14:33", "2026-04-20 10:14:33"),
	(2, "App\\Models\\User", "a1969733-b372-4350-bb85-a8f7c26bd2b0", "gym-reg", "7e08e937a9e9f5062ccf54793d84c3ac5020667843cf1eb948c1ab66ce4fd998", "[\"*\"]", "2026-04-20 10:50:20", NULL, "2026-04-20 10:39:21", "2026-04-20 10:50:20"),
	(3, "App\\Models\\User", "a1969733-b372-4350-bb85-a8f7c26bd2b0", "mobile-app", "4e4639380cf25caaae09001545a3fb569504290d7c10e08ddb9261288cae96e4", "[\"*\"]", NULL, NULL, "2026-04-20 11:07:58", "2026-04-20 11:07:58"),
	(4, "App\\Models\\User", "a19a4d52-818e-40c7-9361-704388d3b6ac", "mobile-app", "9bd76ed86a8ec3a8ca3005d329cd01b441ab530c3f9b3dfff90d91d97d8e4a6c", "[\"*\"]", NULL, NULL, "2026-04-22 07:01:21", "2026-04-22 07:01:21"),
	(5, "App\\Models\\User", "a19a4f91-1884-40d6-8115-4e349538367c", "gym-reg", "41b68759a80c8faee825784f8822cf87155eb00cc5629be9485588e164ebb57c", "[\"*\"]", NULL, NULL, "2026-04-22 07:02:26", "2026-04-22 07:02:26"),
	(6, "App\\Models\\User", "a19a53ad-e902-4ad7-b420-d51ec5fd070c", "gym-reg", "7001e3c0ec79835c8b13857acb15b9c9c99a32dcc833ca6ea4aa1f90b2d5486d", "[\"*\"]", "2026-04-22 07:15:45", NULL, "2026-04-22 07:13:40", "2026-04-22 07:15:45"),
	(7, "App\\Models\\User", "a19a5b84-65be-4bc0-bc04-0786d2380533", "gym-reg", "9c6284d85c67d6b31e98c49932ff3e54023918271202729b85e8bcc296bfb6df", "[\"*\"]", "2026-04-22 07:36:38", NULL, "2026-04-22 07:35:33", "2026-04-22 07:36:38"),
	(8, "App\\Models\\User", "a19a5d32-84e5-4f77-a004-25b4d9561ce3", "gym-reg", "57cf6eacc367bae52bb00a8eb4baaf7316c11c2aad0f00ece66315fa5807fb2b", "[\"*\"]", "2026-04-22 07:40:54", NULL, "2026-04-22 07:40:12", "2026-04-22 07:40:54"),
	(9, "App\\Models\\User", "a19a641f-4ee3-42f1-928b-2ef14d41b663", "gym-reg", "381d1cc4a6ff8c58944412e53771bd73f4b070cb04fc408e4652ec1117fc4c0a", "[\"*\"]", NULL, NULL, "2026-04-22 07:59:35", "2026-04-22 07:59:35"),
	(10, "App\\Models\\User", "a19a64fe-1da2-4a87-bf9f-9fcf550dfab2", "gym-reg", "9e9ffab3cc1a3d61e8a90a2bb559d15775508acc118a5fb106e1f4eecc0786fb", "[\"*\"]", NULL, NULL, "2026-04-22 08:01:59", "2026-04-22 08:01:59"),
	(11, "App\\Models\\User", "a19a64fe-1da2-4a87-bf9f-9fcf550dfab2", "gym-reg", "acdd474633f74472a6a3a0c6903eefda623a1859a31fa0861974b193bbb3ecba", "[\"*\"]", "2026-04-22 08:13:57", NULL, "2026-04-22 08:09:49", "2026-04-22 08:13:57"),
	(12, "App\\Models\\User", "a19a64fe-1da2-4a87-bf9f-9fcf550dfab2", "mobile-app", "e863213ee0a8d60204fdd4cf3d049eb8e53afe151abd93fa7d6d012a077a419b", "[\"*\"]", "2026-04-22 10:33:50", NULL, "2026-04-22 09:13:27", "2026-04-22 10:33:50"),
	(13, "App\\Models\\User", "a19a64fe-1da2-4a87-bf9f-9fcf550dfab2", "mobile-app", "c4cb3cdae3a4c88c411b17a3ca0d2ea1c37daad4468449f931dc65a1f0ba8e43", "[\"*\"]", "2026-04-22 09:56:55", NULL, "2026-04-22 09:15:39", "2026-04-22 09:56:55"),
	(14, "App\\Models\\User", "a19c5bcc-3e49-4c60-90be-ffafe2c2169c", "gym-reg", "7d98081619181a496b36ae8745d4b258de23aff577c13784fd0f34127e8a4959", "[\"*\"]", NULL, NULL, "2026-04-23 07:27:59", "2026-04-23 07:27:59"),
	(15, "App\\Models\\User", "a19c60b9-7219-42be-ba85-5cf76754adc5", "gym-reg", "eaeeabca54f418c4e4035c4fb2b0b10bae4a321641da0702f3e93c97bd003829", "[\"*\"]", NULL, NULL, "2026-04-23 07:41:43", "2026-04-23 07:41:43"),
	(16, "App\\Models\\User", "a19c60b9-7219-42be-ba85-5cf76754adc5", "gym-reg", "fc6a7211457c517cc3548bd00b91af0a711de8690d3b2326f5f1419a3ffa9fd4", "[\"*\"]", NULL, NULL, "2026-04-23 07:48:29", "2026-04-23 07:48:29"),
	(17, "App\\Models\\User", "a19c6f42-85a2-48e4-a4ff-4e4d54e3ccff", "gym-reg", "a297a074aff5dae5664e80b8fccd85857ae533c8b88660bda11bca5458d3fbfc", "[\"*\"]", "2026-04-23 08:32:30", NULL, "2026-04-23 08:22:22", "2026-04-23 08:32:30"),
	(18, "App\\Models\\User", "a19c7493-0900-49f2-a920-5a965b8481b7", "mobile-app", "c45d293aadb6584c869770ad3ef2c423fbf90b368cd75a84dfe1b931b6961cd8", "[\"*\"]", NULL, NULL, "2026-04-23 08:49:33", "2026-04-23 08:49:33"),
	(19, "App\\Models\\User", "a19a53ad-e902-4ad7-b420-d51ec5fd070c", "mobile-app", "e7c3061d36dbfae3d26f6fee967e7c5276a7965bbf0801c59211e43b84a64f53", "[\"*\"]", NULL, NULL, "2026-04-23 09:02:51", "2026-04-23 09:02:51"),
	(20, "App\\Models\\User", "a19c7493-0900-49f2-a920-5a965b8481b7", "mobile-app", "73c92da574c1780b18929f48f5f5ed362450c179dc41c1c864b9cc59e415e20e", "[\"*\"]", NULL, NULL, "2026-04-23 09:03:31", "2026-04-23 09:03:31"),
	(21, "App\\Models\\User", "a19c7493-0900-49f2-a920-5a965b8481b7", "mobile-app", "cb78948806315a85cce5c8412a220b30fe9ed747d2088867860a66fb0e289574", "[\"*\"]", "2026-04-23 09:32:00", NULL, "2026-04-23 09:31:39", "2026-04-23 09:32:00"),
	(22, "App\\Models\\User", "a19c7493-0900-49f2-a920-5a965b8481b7", "gym-reg", "216b7aa8195fa6d570244033c65da0af3bc86cfbcae1a8c000fe796e15245da1", "[\"*\"]", "2026-04-23 09:46:28", NULL, "2026-04-23 09:46:10", "2026-04-23 09:46:28"),
	(23, "App\\Models\\User", "a19c8e46-5364-4f67-b0be-98d268e96c69", "gym-reg", "5e4402ce127c2b3b8fed20973c1e3764494327c9a438e50f835c31720268fcf0", "[\"*\"]", "2026-04-23 09:50:09", NULL, "2026-04-23 09:49:07", "2026-04-23 09:50:09"),
	(24, "App\\Models\\User", "a19a53ad-e902-4ad7-b420-d51ec5fd070c", "mobile-app", "804ac6e71c4d6b302023077994848d8cc78ccc6b0fc2a7b638410579b537ddb5", "[\"*\"]", "2026-04-23 09:50:59", NULL, "2026-04-23 09:50:27", "2026-04-23 09:50:59"),
	(25, "App\\Models\\User", "a19a64fe-1da2-4a87-bf9f-9fcf550dfab2", "mobile-app", "f42d343553d6ea7f36d3e39de56ba82a7ea96fbc9364bb9a6e7be29ac44cd91b", "[\"*\"]", NULL, NULL, "2026-04-23 09:53:03", "2026-04-23 09:53:03"),
	(26, "App\\Models\\User", "a19a64fe-1da2-4a87-bf9f-9fcf550dfab2", "mobile-app", "82e5103242cc289ff7dc53e220ba662fd71313dfe084d4e5e5c17dc5ab24656c", "[\"*\"]", "2026-04-27 06:34:31", NULL, "2026-04-23 09:53:31", "2026-04-27 06:34:31"),
	(27, "App\\Models\\User", "a19e7dc9-c489-4eef-8dfa-4d815dbac8c7", "user-app", "851281a0ce31d41ba461ba8f8fb3ef966b45d05917b9dcee1804310f2a188bb0", "[\"*\"]", NULL, NULL, "2026-04-24 08:55:10", "2026-04-24 08:55:10"),
	(28, "App\\Models\\User", "a1a49c0c-6ab8-4a6d-b45a-d61bb419a60d", "user-app", "494969157ad5854263800be70c5d08f77768fba59301a9a0cb37e9e5e7438be8", "[\"*\"]", NULL, NULL, "2026-04-27 09:54:27", "2026-04-27 09:54:27"),
	(29, "App\\Models\\User", "a1a4a1d3-4d97-4a57-b2cb-f18cb6d13be3", "user-app", "3c32f47a02432548794d7929f25b6e593045b0e201558b62ccae02d96b044e3e", "[\"*\"]", NULL, NULL, "2026-04-27 10:10:40", "2026-04-27 10:10:40"),
	(30, "App\\Models\\User", "a1a49c0c-6ab8-4a6d-b45a-d61bb419a60d", "user-app", "39e0e775d2383b93384d8d36183dbafd73a0c93b37b78d24f01e0388f09e61c9", "[\"*\"]", "2026-04-27 10:12:16", NULL, "2026-04-27 10:11:56", "2026-04-27 10:12:16"),
	(31, "App\\Models\\User", "a1a4c09c-203a-44b0-973d-1e92c09c25eb", "user-app", "3ee34e0f3b3d5e97b1bb45e5f39d6e4596a40e81bdf168e67048aa2c35d7dad4", "[\"*\"]", NULL, NULL, "2026-04-27 11:36:34", "2026-04-27 11:36:34"),
	(32, "App\\Models\\User", "a1ac4c63-59c6-4cb2-bdfd-624bd97db5be", "user-app", "1dc30fdd0ec0eecadbbdbf6c3fa9886345b55fb21a534d0915c3d45459eeb4c4", "[\"*\"]", NULL, NULL, "2026-05-01 05:38:20", "2026-05-01 05:38:20"),
	(33, "App\\Models\\User", "a1ac4c63-59c6-4cb2-bdfd-624bd97db5be", "user-app", "c59c886e44af72e0283e3e05154cbdd9f7b9dba08026a82c6882bb86cbd721bc", "[\"*\"]", NULL, NULL, "2026-05-01 05:38:53", "2026-05-01 05:38:53"),
	(34, "App\\Models\\User", "a1ac4c63-59c6-4cb2-bdfd-624bd97db5be", "user-app", "ab50a04aac087d570edcb82b0142798c9bd86576edb6a1770a7cb32b745a8a94", "[\"*\"]", NULL, NULL, "2026-05-01 05:39:13", "2026-05-01 05:39:13"),
	(35, "App\\Models\\User", "a1ac4c63-59c6-4cb2-bdfd-624bd97db5be", "user-app", "e29f4a16de8b9b78bd95aa797071b4528de0934fd7e265fea0e26467d570e6e4", "[\"*\"]", NULL, NULL, "2026-05-01 05:40:00", "2026-05-01 05:40:00"),
	(36, "App\\Models\\User", "a1ac4c63-59c6-4cb2-bdfd-624bd97db5be", "user-app", "2797bf6cc4741500b28e828777c52445cd8671db4d0b97ebb7bcf88e58d9921f", "[\"*\"]", NULL, NULL, "2026-05-01 05:40:10", "2026-05-01 05:40:10"),
	(37, "App\\Models\\User", "a1ac4c63-59c6-4cb2-bdfd-624bd97db5be", "user-app", "d5cd7e0910e160d743ef94815a6a79b8e58997a59aa72d76411e1dd2402dde98", "[\"*\"]", NULL, NULL, "2026-05-01 05:40:20", "2026-05-01 05:40:20"),
	(38, "App\\Models\\User", "a1ac4c63-59c6-4cb2-bdfd-624bd97db5be", "user-app", "2de2b1d9aaa16a5cfb285e7f04a9bd38abedcf1bcef086b9e066709e914effca", "[\"*\"]", NULL, NULL, "2026-05-01 05:40:28", "2026-05-01 05:40:28"),
	(39, "App\\Models\\User", "a1ac4c63-59c6-4cb2-bdfd-624bd97db5be", "user-app", "d5de2208b714c53d4451a7a54fb6387921fa515fa63ae5c09ab792213af4e9ba", "[\"*\"]", "2026-05-01 05:42:59", NULL, "2026-05-01 05:42:16", "2026-05-01 05:42:59"),
	(40, "App\\Models\\User", "a1ac4c63-59c6-4cb2-bdfd-624bd97db5be", "user-app", "cae4aad8d20576b643b570169250b25778f414b53fa6b370270c559f01f305af", "[\"*\"]", NULL, NULL, "2026-05-01 05:49:58", "2026-05-01 05:49:58"),
	(41, "App\\Models\\User", "a1ac4c63-59c6-4cb2-bdfd-624bd97db5be", "user-app", "e6c5afe363546ef4fac733c3dd691b7f7eb7fb4bdf3284669f32d1b96bd1624a", "[\"*\"]", NULL, NULL, "2026-05-01 05:50:10", "2026-05-01 05:50:10"),
	(42, "App\\Models\\User", "a1ac4c63-59c6-4cb2-bdfd-624bd97db5be", "user-app", "5040a7658d115bbc000fe2c9012c8d98fd86fad65a77c1c71b2f6293297ba37a", "[\"*\"]", NULL, NULL, "2026-05-01 05:50:34", "2026-05-01 05:50:34"),
	(43, "App\\Models\\User", "a1ac4c63-59c6-4cb2-bdfd-624bd97db5be", "user-app", "41130735e9532a61e9e48f12c4e15c214feacd7629d0e94b63d334de29d4d0bc", "[\"*\"]", "2026-05-01 05:53:30", NULL, "2026-05-01 05:52:26", "2026-05-01 05:53:30"),
	(44, "App\\Models\\User", "a1ac4c63-59c6-4cb2-bdfd-624bd97db5be", "user-app", "d0c71fd1237db3843609eb29608f36b91c75ea572fefe1c1b33252e1b256163d", "[\"*\"]", NULL, NULL, "2026-05-01 06:08:13", "2026-05-01 06:08:13"),
	(45, "App\\Models\\User", "a1ac4c63-59c6-4cb2-bdfd-624bd97db5be", "user-app", "6c4b0de562557c772bb115c1a01092fd77d697844a6477bca1cb0dc1693d583a", "[\"*\"]", "2026-05-01 06:14:02", NULL, "2026-05-01 06:13:52", "2026-05-01 06:14:02"),
	(46, "App\\Models\\User", "a1ac4c63-59c6-4cb2-bdfd-624bd97db5be", "user-app", "560fd1095ab0455a1ab21cc61a8a9e98293ed0d5b6c8f2ce4f0daae0456cdd47", "[\"*\"]", "2026-05-01 06:34:50", NULL, "2026-05-01 06:34:47", "2026-05-01 06:34:50"),
	(47, "App\\Models\\User", "a19e7dc9-c489-4eef-8dfa-4d815dbac8c7", "user-app", "a2a72b18dca8549055309d034401bc118ccd6eef94f785437341df14ae413497", "[\"*\"]", "2026-05-05 12:11:29", NULL, "2026-05-05 12:10:45", "2026-05-05 12:11:29");

/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;



# Dump of table platform_subscription_plans
# ------------------------------------------------------------

DROP TABLE IF EXISTS `platform_subscription_plans`;

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

LOCK TABLES `platform_subscription_plans` WRITE;
/*!40000 ALTER TABLE `platform_subscription_plans` DISABLE KEYS */;

INSERT INTO `platform_subscription_plans` (`id`, `name`, `monthly_price`, `yearly_price`, `max_gyms`, `max_members`, `has_analytics`, `has_mobile_app`, `created_at`, `updated_at`) VALUES
	("a190474f-7371-4658-b622-22109622dd37", "Trial Plan for Gym\'s Users", 0, 0, 23, 234, 1, 1, "2026-04-17 07:20:37", "2026-04-27 06:24:36");

/*!40000 ALTER TABLE `platform_subscription_plans` ENABLE KEYS */;
UNLOCK TABLES;



# Dump of table recipes
# ------------------------------------------------------------

DROP TABLE IF EXISTS `recipes`;

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

DROP TABLE IF EXISTS `role_has_permissions`;

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

DROP TABLE IF EXISTS `roles`;

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

DROP TABLE IF EXISTS `sessions`;

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

DROP TABLE IF EXISTS `settings`;

CREATE TABLE `settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `key` longtext DEFAULT NULL,
  `value` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;

INSERT INTO `settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES
	(67, "site_title", "FitPaxPro", "2026-04-17 08:05:21", "2026-04-17 08:05:21"),
	(68, "admin_email", "admin@fitpaxpro.com", "2026-04-17 08:05:21", "2026-04-17 08:05:21"),
	(69, "currency", "INR", "2026-04-17 08:05:21", "2026-04-17 09:31:57"),
	(70, "currency_symbol", "$", "2026-04-17 08:05:21", "2026-04-17 08:05:21"),
	(71, "contact_number", "+1 234 567 890", "2026-04-17 08:05:22", "2026-04-17 08:05:22"),
	(72, "logo", "assets/images/logo_1776413143.png", "2026-04-17 08:05:43", "2026-04-17 08:05:43"),
	(73, "favicon", "assets/images/favicon_1776666877.png", "2026-04-17 08:08:18", "2026-04-20 06:34:37");

/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;



# Dump of table user_body_measurements
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_body_measurements`;

CREATE TABLE `user_body_measurements` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL COMMENT 'Link to users table',
  `recorded_at` date NOT NULL,
  `weight` decimal(5,2) DEFAULT NULL,
  `chest` decimal(5,2) DEFAULT NULL COMMENT 'in cm',
  `waist` decimal(5,2) DEFAULT NULL COMMENT 'in cm',
  `hips` decimal(5,2) DEFAULT NULL COMMENT 'in cm',
  `biceps` decimal(5,2) DEFAULT NULL COMMENT 'in cm',
  `thighs` decimal(5,2) DEFAULT NULL COMMENT 'in cm',
  `body_fat_percentage` decimal(5,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_body_measurements_user_id_foreign` (`user_id`),
  CONSTRAINT `user_body_measurements_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;





# Dump of table user_profiles
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_profiles`;

CREATE TABLE `user_profiles` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL COMMENT 'One-to-one link to the users table',
  `alternative_contact` varchar(20) DEFAULT NULL COMMENT 'Secondary contact number for emergencies',
  `gender` enum('male','female','other','prefer_not_to_say') DEFAULT NULL,
  `age` int(3) unsigned DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `current_weight` decimal(5,2) DEFAULT NULL COMMENT 'Stored in kg (e.g., 75.50) for progress tracking',
  `target_weight` decimal(5,2) DEFAULT NULL,
  `body_fat_percentage` decimal(5,2) DEFAULT NULL,
  `height` decimal(5,2) DEFAULT NULL COMMENT 'Stored in cm for BMI calculations',
  `goal_type` enum('weight_gain','weight_loss','maintenance','muscle_building') DEFAULT 'maintenance',
  `workout_frequency_goal` int(11) DEFAULT NULL,
  `activity_level` enum('sedentary','lightly_active','moderately_active','very_active','extra_active') DEFAULT 'sedentary',
  `fitness_level` enum('beginner','intermediate','advanced','athlete') DEFAULT NULL,
  `preferred_workout_time` enum('morning','afternoon','evening','late_night','flexible') DEFAULT NULL,
  `blood_group` varchar(5) DEFAULT NULL,
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

LOCK TABLES `user_profiles` WRITE;
/*!40000 ALTER TABLE `user_profiles` DISABLE KEYS */;

INSERT INTO `user_profiles` (`id`, `user_id`, `alternative_contact`, `gender`, `age`, `date_of_birth`, `current_weight`, `target_weight`, `body_fat_percentage`, `height`, `goal_type`, `workout_frequency_goal`, `activity_level`, `fitness_level`, `preferred_workout_time`, `blood_group`, `diet_type`, `medical_conditions`, `allergies`, `physical_limitations`, `is_public`, `created_at`, `updated_at`) VALUES
	("a1a4a28a-6888-40d3-9b97-949367a127a2", "a1a49c0c-6ab8-4a6d-b45a-d61bb419a60d", NULL, "male", NULL, "1995-05-15", 75, 70, NULL, 180.5, "maintenance", NULL, "sedentary", NULL, NULL, "O+", "veg", NULL, NULL, NULL, 1, "2026-04-27 10:12:16", "2026-04-27 10:12:16"),
	("a1ac4e05-895b-4ebc-ac2c-0afa6c087ef1", "a1ac4c63-59c6-4cb2-bdfd-624bd97db5be", NULL, "male", NULL, "1995-05-15", 69, 80, NULL, 256, "weight_loss", 4, "moderately_active", "intermediate", "morning", "O+", "non_veg", "None", "None", "None", 1, "2026-05-01 05:42:34", "2026-05-01 05:42:43");

/*!40000 ALTER TABLE `user_profiles` ENABLE KEYS */;
UNLOCK TABLES;



# Dump of table users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `fcm` text DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `password` varchar(255) DEFAULT NULL,
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

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `email_verified_at`, `fcm`, `status`, `password`, `user_type`, `otp_expires_at`, `otp`, `profile_image`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
	("a19041fb-a4de-4c78-a340-f09a6752a53c", "admin", "admin@fitpaxpro.com", NULL, NULL, NULL, 1, "$2y$12$73gUniOl7jUz65OKyvnNKue8rrAcu2rs6nCYbVsDoaH04Lbxhb/lm", 1, NULL, NULL, NULL, NULL, "2026-04-17 07:05:43", "2026-04-17 07:05:43", NULL),
	("a1969733-b372-4350-bb85-a8f7c26bd2b0", "John Operative", "john@fitpaxpro.com", "+911234567890", "2026-04-20 10:39:21", NULL, 1, "$2y$12$3dOo7WPlLVlrbnohmbU5M.Lldh30fyVrrMgdy7lHt.SClRHB5sHJu", 2, NULL, NULL, NULL, NULL, "2026-04-20 10:38:59", "2026-04-20 10:39:21", NULL),
	("a19a4d52-818e-40c7-9361-704388d3b6ac", "Arvind verma", "aman@gmail.com", "3164997676494", NULL, NULL, 1, "$2y$12$ajiWSIOhCH6Z8pIkGGRDJ.pjlltqf8F.p49CMYn/20dJtRJZRSehe", 2, NULL, NULL, NULL, NULL, "2026-04-22 06:55:42", "2026-04-22 07:01:21", NULL),
	("a19a4dd9-447a-4790-80ba-713af6147007", "string", "arvind@gmail.com", "123123123123", NULL, NULL, 0, "$2y$12$dCL4XhyGDAwA1m48Zz7iWe7lt32xLNhzIPSopWPyvgtDKWwRdRUAq", 2, NULL, NULL, NULL, NULL, "2026-04-22 06:57:11", "2026-04-22 06:57:11", NULL),
	("a19a4f91-1884-40d6-8115-4e349538367c", "ja", "bsbsb@hsh.ck", "6494994444", "2026-04-22 07:02:26", NULL, 1, "$2y$12$hnbsdp5rCk3UYmjcI0emIe6OMO.Ekh93tMWB977oTmOh/5jLPcdG.", 2, NULL, NULL, NULL, NULL, "2026-04-22 07:01:59", "2026-04-22 07:02:26", NULL),
	("a19a53ad-e902-4ad7-b420-d51ec5fd070c", "Arc8", "ops@fitpaxpro.com", "2356894175", "2026-04-22 07:13:40", NULL, 1, "$2y$12$EvZ4lv5TeyO3g/VD3u8p5OXE9.6gX0E2EBjnkeyGZufQe4fTepp1m", 2, NULL, NULL, NULL, NULL, "2026-04-22 07:13:29", "2026-04-22 07:13:40", NULL),
	("a19a5b84-65be-4bc0-bc04-0786d2380533", "new ower", "owner@gnail.xom", "3126459780", "2026-04-22 07:35:33", NULL, 1, "$2y$12$1Vjx8TqZsBfGYmDefAMvru0EkW6VoiA.hS4RoH3OdhiNtFAVIBm5G", 2, NULL, NULL, NULL, NULL, "2026-04-22 07:35:24", "2026-04-22 07:35:33", NULL),
	("a19a5d32-84e5-4f77-a004-25b4d9561ce3", "over gym", "gym@gmail.cok", "64371827840", "2026-04-22 07:40:12", NULL, 1, "$2y$12$iPt.E1fYFlXYt5s/rBAFKOLeqQGzUQ4Py0UclqoWUSbxQv1x0Vm7y", 2, NULL, NULL, NULL, NULL, "2026-04-22 07:40:06", "2026-04-22 07:40:12", NULL),
	("a19a641f-4ee3-42f1-928b-2ef14d41b663", "akash", "akash@gmail.com", "31254697845", "2026-04-22 07:59:35", NULL, 1, "$2y$12$6/Cws5YrFexQtgoUMNTmneuIbnc3klUOCzCE4u60WgksLhGmvdZoi", 2, NULL, NULL, NULL, NULL, "2026-04-22 07:59:28", "2026-04-22 07:59:35", NULL),
	("a19a64fe-1da2-4a87-bf9f-9fcf550dfab2", "ksks", "hshs@jsj.sjsj", "6466494944", "2026-04-22 08:09:49", NULL, 1, "$2y$12$fQUrHKaaWrJdqH4QYRaMIeia19r7llxrox9F5hew.HPvZGen1JZaO", 2, NULL, NULL, NULL, NULL, "2026-04-22 08:01:54", "2026-04-22 08:09:49", NULL),
	("a19c5bcc-3e49-4c60-90be-ffafe2c2169c", "New gym", "gym1@gmail.cok", "3629454846", "2026-04-23 07:27:59", NULL, 1, "$2y$12$cv01CpN7BobJ0gt3FmxraekVerHWEB6aTug1Y9BtbFJeiFBF1y9Qa", 2, NULL, NULL, NULL, NULL, "2026-04-23 07:27:50", "2026-04-23 07:27:59", NULL),
	("a19c60b9-7219-42be-ba85-5cf76754adc5", "aman", "gym2@gmail.cok", "316494846", "2026-04-23 07:48:29", NULL, 1, "$2y$12$XHU0tAbhbSIHS5KTmqq30./BOPIxQ6a8il/o3KtNHK/jkq7VM5Ici", 2, NULL, NULL, NULL, NULL, "2026-04-23 07:41:37", "2026-04-23 07:48:29", NULL),
	("a19c6f42-85a2-48e4-a4ff-4e4d54e3ccff", "akash", "akash1@gmail.com", "31254697854", "2026-04-23 08:22:22", NULL, 1, "$2y$12$6K0JED2p8dBzjjHdCSDGpOa1zoxJ03oicDBFzzVmG79guuDlOKqni", 2, NULL, NULL, NULL, NULL, "2026-04-23 08:22:16", "2026-04-23 08:22:22", NULL),
	("a19c7493-0900-49f2-a920-5a965b8481b7", "kaks", "hshsh@hwh.xj", "6464646444", "2026-04-23 09:46:10", NULL, 1, "$2y$12$Owdnqed7r5nVfldWV./zkuS5n0Oxh2R.DnGDjhAuYpHLQe3DWoqT6", 2, NULL, NULL, NULL, NULL, "2026-04-23 08:37:07", "2026-04-23 09:46:10", NULL),
	("a19c8e46-5364-4f67-b0be-98d268e96c69", "vsj", "gym12@gmail.co", "643494646", "2026-04-23 09:49:07", NULL, 1, "$2y$12$5/DKc8xkbWfMH8oZL.H6IOOU6B6qsHlXrFYUlGTA.Hf7LUbHLnfHG", 2, NULL, NULL, NULL, NULL, "2026-04-23 09:48:59", "2026-04-23 09:49:07", NULL),
	("a19e6f8a-682d-4ccf-9bf4-1394c82aa9f9", "John Doe", "john@example.com", "+1234567890", NULL, NULL, 0, NULL, 1, NULL, NULL, NULL, NULL, "2026-04-24 08:14:42", "2026-04-24 08:14:42", NULL),
	("a19e7dc9-c489-4eef-8dfa-4d815dbac8c7", "ARVIND Verma", "arvindverma630635@gmail.com", "388686868686", NULL, NULL, 0, NULL, 1, NULL, NULL, NULL, NULL, "2026-04-24 08:54:32", "2026-04-24 08:54:32", NULL),
	("a1a49c0c-6ab8-4a6d-b45a-d61bb419a60d", "user", "user@gmail.com", "9311728446", NULL, NULL, 0, NULL, 1, NULL, NULL, NULL, NULL, "2026-04-27 09:54:07", "2026-04-27 09:54:07", NULL),
	("a1a4a1d3-4d97-4a57-b2cb-f18cb6d13be3", "user2", "user2@gmail.com", "3690852147", NULL, NULL, 0, NULL, 1, NULL, NULL, NULL, NULL, "2026-04-27 10:10:16", "2026-04-27 10:10:16", NULL),
	("a1a4c09c-203a-44b0-973d-1e92c09c25eb", "user23", "user3@gmail.com", "4946494946", NULL, NULL, 0, NULL, 1, NULL, NULL, NULL, NULL, "2026-04-27 11:36:21", "2026-04-27 11:36:21", NULL),
	("a1ac4c63-59c6-4cb2-bdfd-624bd97db5be", "akash", "ak2sh@gmail.com", "3690852146", NULL, NULL, 1, NULL, 1, NULL, NULL, NULL, NULL, "2026-05-01 05:38:00", "2026-05-01 05:42:59", NULL);

/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;



# Dump of views
# ------------------------------------------------------------

# Creating temporary tables to overcome VIEW dependency errors


/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

# Dump completed on 2026-05-07T11:41:20+05:30
