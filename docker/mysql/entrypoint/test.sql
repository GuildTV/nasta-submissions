SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE DATABASE IF NOT EXISTS `Test_Nasta_Submissions` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `Test_Nasta_Submissions`;

CREATE TABLE `categories` (
  `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `compact_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `closing_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `opening_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `categories` (`id`, `name`, `compact_name`, `description`, `closing_at`, `opening_at`, `created_at`, `updated_at`) VALUES
('already-closed', 'Already closed', 'already-closed', '', '2016-12-21 00:00:00', NULL, '2017-01-18 21:21:31', '2017-01-18 21:21:31'),
('animation', 'Animation', 'Male', 'A single animation programme (or a shortened edit from an episode or series), or an original piece of animation of any type, which has been produced by your station.', '2017-02-20 19:00:00', NULL, '2016-11-26 16:57:31', '2016-11-26 16:57:31'),
('no-constraints', 'no file constraints!', 'no-constraints', '', '2028-04-17 00:00:00', NULL, '2017-01-18 21:15:43', '2017-01-18 21:15:43'),
('something', 'Something', 'Something', 'fbsgsdd', '2016-02-20 19:00:00', NULL, '2016-11-26 16:57:31', '2016-11-26 16:57:31');

CREATE TABLE `category_file_constraint` (
  `id` int(10) UNSIGNED NOT NULL,
  `category_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `file_constraint_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `category_file_constraint` (`id`, `category_id`, `file_constraint_id`, `created_at`, `updated_at`) VALUES
(1, 'animation', 1, NULL, NULL),
(2, 'animation', 2, NULL, NULL),
(3, 'something', 3, '2017-01-06 18:32:55', '2017-01-06 18:32:55'),
(4, 'already-closed', 3, '2017-01-18 21:25:59', '2017-01-18 21:25:59');

CREATE TABLE `dropbox_accounts` (
  `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  `used_space` bigint(20) NOT NULL DEFAULT '0',
  `total_space` bigint(20) NOT NULL DEFAULT '0',
  `access_token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `dropbox_accounts` (`id`, `enabled`, `used_space`, `total_space`, `access_token`, `created_at`, `updated_at`) VALUES
('test', 0, 27272822, 2147483648, 'oupaw3hcijAAAAAAAAAADNEqcrJR5JKstlbCBrnFTPhQ0WaSNkC_CQAUB9YdfG0z', '2016-12-03 16:16:36', '2017-01-15 22:20:55');

CREATE TABLE `entries` (
  `id` int(10) UNSIGNED NOT NULL,
  `category_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `station_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `description` text COLLATE utf8_unicode_ci,
  `rules` tinyint(1) NOT NULL DEFAULT '0',
  `submitted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `entries` (`id`, `category_id`, `station_id`, `name`, `description`, `rules`, `submitted`, `created_at`, `updated_at`) VALUES
(29, 'animation', 3, 'trsetestse', '', 1, 1, '2016-12-14 22:14:49', '2016-12-14 22:15:58'),
(72, 'something', 3, 'Test submission', 'Something exciting about pillows.', 1, 1, '2017-01-06 23:49:53', '2017-01-06 23:49:53');

CREATE TABLE `entries_folders` (
  `id` int(10) UNSIGNED NOT NULL,
  `entry_id` int(10) UNSIGNED NOT NULL,
  `folder_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `file_constraints` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `mimetypes` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `video_duration` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `file_constraints` (`id`, `name`, `description`, `mimetypes`, `video_duration`, `created_at`, `updated_at`) VALUES
(1, '10 Minute Video', 'A 10 minute video entry', 'video/mp4', NULL, '2016-11-26 16:57:31', '2016-11-26 16:57:31'),
(2, '500 Words', 'A 500 word document', 'pdf;doc;docx;odf', NULL, '2016-11-26 16:57:31', '2016-11-26 16:57:31'),
(3, '2 Words', 'A 2 word document', 'pdf;doc;docx;odf', NULL, '2016-11-26 16:57:31', '2016-11-26 16:57:31');

CREATE TABLE `google_accounts` (
  `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  `used_space` bigint(20) NOT NULL DEFAULT '0',
  `total_space` bigint(20) NOT NULL DEFAULT '0',
  `target_dir` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8_unicode_ci NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(16, '2014_10_12_000000_create_users_table', 1),
(17, '2014_10_12_100000_create_password_resets_table', 1),
(18, '2016_11_22_215053_create_categories_table', 1),
(19, '2016_11_22_215734_create_file_constraint_table', 1),
(20, '2016_11_22_220038_create_category_file_constraint_pivot', 2),
(21, '2016_11_23_213723_create_entries_table', 3),
(26, '2016_11_27_130603_create_google_account_table', 4),
(29, '2016_11_27_171746_create_file_upload_table', 5),
(30, '2016_11_27_185538_alter_file_constraints_add_video_duration', 5),
(31, '2016_11_27_233951_create_file_upload_log_table', 6),
(32, '2016_11_28_001806_create_jobs_table', 7),
(37, '2013_04_09_062329_create_revisions_table', 8),
(38, '2016_12_01_224616_create_entries_folder_table', 8),
(40, '2016_12_03_153818_create_dropbox_account', 9),
(41, '2016_12_03_161959_create_station_folder', 10),
(44, '2016_12_03_174632_create_uploaded_files_table', 11),
(45, '2016_12_03_194216_alter_category_add_compact', 12),
(46, '2016_12_03_203551_create_uploaded_file_log_table', 13),
(47, '2017_01_08_170342_alter_uploaded_files_add_size', 14),
(48, '2017_01_11_191252_alter_uploaded_files_add_hash', 15),
(49, '2017_01_15_140523_alter_uploaded_files_add_local_path', 16),
(50, '2017_01_18_002345_alter_users_add_compact_name', 17);

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `revisions` (
  `id` int(10) UNSIGNED NOT NULL,
  `revisionable_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `revisionable_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `old_value` text COLLATE utf8_unicode_ci,
  `new_value` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `station_folders` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `account_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `request_url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `folder_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `station_folders` (`id`, `user_id`, `account_id`, `request_url`, `folder_name`, `created_at`, `updated_at`) VALUES
(1, 3, 'test', 'https://www.dropbox.com/request/1FSUznsCcBN83Tzj7F56', '/File requests/Test Station Submissions', '2016-12-03 16:58:31', '2016-12-03 16:58:31');

CREATE TABLE `uploaded_files` (
  `id` int(10) UNSIGNED NOT NULL,
  `station_id` int(10) UNSIGNED NOT NULL,
  `category_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `account_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `path` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `size` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `hash` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `path_local` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `uploaded_files` (`id`, `station_id`, `category_id`, `account_id`, `path`, `name`, `size`, `hash`, `path_local`, `uploaded_at`, `created_at`, `updated_at`) VALUES
(20, 3, 'animation', 'test', '/Imported/Test Station/Julian Waller - LSTV_Male_DennisTheMenace22.mp4', 'Julian Waller - LSTV_Male_DennisTheMenace22.mp4', '', '', NULL, '2016-12-03 19:49:44', '2016-12-03 19:49:44', '2016-12-03 19:49:44'),
(21, 3, NULL, 'test', '/Imported/Test Station/test  - fgf - LSTV_Male_DennisTheMenace22.mp4', 'test  - fgf - LSTV_Male_DennisTheMenace22.mp4', '', '', NULL, '2017-04-28 19:49:45', '2016-12-03 19:49:46', '2016-12-03 19:49:46'),
(22, 3, 'something', 'test', 'Nope', 'Fake file', '', '', NULL, '2017-04-28 19:49:45', '2016-12-03 19:49:46', '2016-12-03 19:49:46'),
(131, 3, 'something', 'test', 'Nope', 'Fake file', '', '', 'Nope', '2017-04-28 19:49:45', '2016-12-03 19:49:46', '2016-12-03 19:49:46'),
(190, 3, 'already-closed', 'test', '/no/no/no', 'not a file!', '400', 'fsdfsdf', NULL, '2017-02-24 00:00:00', '2017-01-18 21:31:53', '2017-01-18 21:31:53'),
(191, 3, NULL, 'test', '/no/no/no', 'not a file!', '400', 'fsdfsdf', NULL, '2017-02-24 00:00:00', '2017-01-18 21:31:53', '2017-01-18 21:31:53'),
(192, 3, 'something', 'test', '/no/no/no', 'not a file!', '400', 'fsdfsdf', NULL, '2017-02-24 00:00:00', '2017-01-18 21:31:53', '2017-01-18 21:31:53'),
(193, 3, 'animation', 'test', '/no/no/no', 'not a file!', '400', 'fsdfsdf', NULL, '2017-02-24 00:00:00', '2017-01-18 21:31:53', '2017-01-18 21:31:53');

CREATE TABLE `uploaded_file_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `station_id` int(10) UNSIGNED NOT NULL,
  `category_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `level` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `message` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `compact_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` enum('station','judge','support','admin') COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `users` (`id`, `name`, `compact_name`, `username`, `email`, `password`, `remember_token`, `type`, `created_at`, `updated_at`) VALUES
(1, 'Test Admin', 'TestAdmin', 'test_admin', 'test@email.com', '$2y$10$EVf9rQNjszZywYF/7/opyOXuzo6heEfn8G4TD6Py6hUTPfMhKGDmO', '5WzQAVQSUiwxaDHippU7tgxHwtobQ2b7WuitkkxjQvJzpBKWU5EXE4SIndNS', 'admin', '2016-11-26 16:57:31', '2016-12-03 16:45:37'),
(2, 'Test Judge', 'TestJudge', 'test_judge', 'judge@email.com', '$2y$10$J82DBmvgsH59vsKxrMDCHe1BVu9ufIV/w44ldggAM6HCDpMvqxhvK', NULL, 'judge', '2016-11-26 16:57:31', '2016-11-26 16:57:31'),
(3, 'Test Station', 'TestStation', 'test_station', 'station@email.com', '$2y$10$qBA1pYoTPOLNMgi14B8P2u4GIf9kNu.XDSzOfQPn9XNr5Mo5Lvocm', 'QvY1MEsiYXWZbcwJvxroEOq8hd7nAZfv6PrsBYf2jThn4vtqCXcesnwP3Z75', 'station', '2016-11-26 16:57:31', '2016-12-03 15:51:24'),
(4, 'Station no submissions', 'Stationnosubmissions', 'no-subs', 'no@subs.com', '', NULL, 'station', '2017-01-17 09:09:57', '2017-01-17 09:09:57');


ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `category_file_constraint`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_file_constraint_category_id_foreign` (`category_id`),
  ADD KEY `category_file_constraint_file_constraint_id_foreign` (`file_constraint_id`);

ALTER TABLE `dropbox_accounts`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `entries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `entries_category_id_station_id_deleted_at_unique` (`category_id`,`station_id`),
  ADD KEY `entries_station_id_foreign` (`station_id`);

ALTER TABLE `entries_folders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `entries_folders_entry_id_unique` (`entry_id`);

ALTER TABLE `file_constraints`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `google_accounts`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_reserved_at_index` (`queue`,`reserved_at`);

ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`),
  ADD KEY `password_resets_token_index` (`token`);

ALTER TABLE `revisions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `revisions_revisionable_id_revisionable_type_index` (`revisionable_id`,`revisionable_type`);

ALTER TABLE `station_folders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `station_folders_user_id_unique` (`user_id`),
  ADD KEY `station_folders_account_id_foreign` (`account_id`);

ALTER TABLE `uploaded_files`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uploaded_files_path_local_unique` (`path_local`),
  ADD KEY `uploaded_files_account_id_foreign` (`account_id`),
  ADD KEY `uploaded_files_station_id_foreign` (`station_id`),
  ADD KEY `uploaded_files_category_id_foreign` (`category_id`);

ALTER TABLE `uploaded_file_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uploaded_file_logs_category_id_foreign` (`category_id`),
  ADD KEY `uploaded_file_logs_station_id_foreign` (`station_id`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_username_unique` (`username`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_compact_name_unique` (`compact_name`);


ALTER TABLE `category_file_constraint`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
ALTER TABLE `entries`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;
ALTER TABLE `entries_folders`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `file_constraints`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;
ALTER TABLE `revisions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;
ALTER TABLE `station_folders`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
ALTER TABLE `uploaded_files`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=194;
ALTER TABLE `uploaded_file_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `category_file_constraint`
  ADD CONSTRAINT `category_file_constraint_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `category_file_constraint_file_constraint_id_foreign` FOREIGN KEY (`file_constraint_id`) REFERENCES `file_constraints` (`id`) ON DELETE CASCADE;

ALTER TABLE `entries`
  ADD CONSTRAINT `entries_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `entries_station_id_foreign` FOREIGN KEY (`station_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `entries_folders`
  ADD CONSTRAINT `entries_folders_entry_id_foreign` FOREIGN KEY (`entry_id`) REFERENCES `entries` (`id`) ON DELETE CASCADE;

ALTER TABLE `station_folders`
  ADD CONSTRAINT `station_folders_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `dropbox_accounts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `station_folders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `uploaded_files`
  ADD CONSTRAINT `uploaded_files_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `dropbox_accounts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `uploaded_files_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `uploaded_files_station_id_foreign` FOREIGN KEY (`station_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `uploaded_file_logs`
  ADD CONSTRAINT `uploaded_file_logs_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `uploaded_file_logs_station_id_foreign` FOREIGN KEY (`station_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
