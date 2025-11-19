-- MSR Database Schema
-- Movie & Series Review Website

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- Database: `if0_39024958_MR`

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','editor') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'editor',
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `remember_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `status`, `remember_token`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'MSR', 'msa.masum.bd@gmail.com', '$2y$12$YourHashedPasswordHere', 'admin', 'active', NULL, NULL, NOW(), NOW());

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_bn` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icon` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '#3B82F6',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `name_bn`, `slug`, `description`, `icon`, `color`, `sort_order`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Action', 'অ্যাকশন', 'action', 'Action movies and series', 'fas fa-fist-raised', '#EF4444', 1, 'active', NOW(), NOW()),
(2, 'Comedy', 'কমেডি', 'comedy', 'Comedy movies and series', 'fas fa-laugh', '#F59E0B', 2, 'active', NOW(), NOW()),
(3, 'Drama', 'ড্রামা', 'drama', 'Drama movies and series', 'fas fa-theater-masks', '#8B5CF6', 3, 'active', NOW(), NOW()),
(4, 'Thriller', 'থ্রিলার', 'thriller', 'Thriller movies and series', 'fas fa-user-secret', '#1F2937', 4, 'active', NOW(), NOW()),
(5, 'Romance', 'রোমান্স', 'romance', 'Romance movies and series', 'fas fa-heart', '#EC4899', 5, 'active', NOW(), NOW()),
(6, 'Horror', 'হরর', 'horror', 'Horror movies and series', 'fas fa-ghost', '#6B7280', 6, 'active', NOW(), NOW()),
(7, 'Korean Drama', 'কোরিয়ান ড্রামা', 'korean-drama', 'Korean drama series', 'fas fa-globe-asia', '#10B981', 7, 'active', NOW(), NOW()),
(8, 'Bangla', 'বাংলা', 'bangla', 'Bangla movies and series', 'fas fa-flag', '#059669', 8, 'active', NOW(), NOW());

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reviewer_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reviewer_email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `year` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('movie','series') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'movie',
  `language` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `director` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cast` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rating` decimal(2,1) NOT NULL DEFAULT 0.0,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `excerpt` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `poster_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trailer_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `featured` tinyint(1) NOT NULL DEFAULT 0,
  `view_count` int(11) NOT NULL DEFAULT 0,
  `status` enum('published','pending','draft') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_by` enum('admin','user') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'admin',
  `meta_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_keywords` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `title`, `slug`, `reviewer_name`, `reviewer_email`, `year`, `type`, `language`, `director`, `cast`, `rating`, `content`, `excerpt`, `poster_image`, `trailer_url`, `featured`, `view_count`, `status`, `created_by`, `meta_title`, `meta_description`, `meta_keywords`, `created_at`, `updated_at`) VALUES
(1, 'দ্য উইচার', 'the-witcher', 'MSR Team', NULL, '2023', 'series', 'ইংরেজি', 'Lauren Schmidt Hissrich', 'Henry Cavill, Anya Chalotra, Freya Allan', 4.5, '<p>নেটফ্লিক্সের এই ফ্যান্টাসি সিরিজটি একটি অভূতপূর্ব অভিজ্ঞতা। গেরাল্ট অফ রিভিয়ার চরিত্রে হেনরি ক্যাভিলের অভিনয় এবং জটিল কাহিনী বিন্যাস দর্শকদের মুগ্ধ করে রাখে। প্রতিটি এপিসোডে নতুন রহস্য ও রোমাঞ্চের অপেক্ষা।</p>', 'নেটফ্লিক্সের এই ফ্যান্টাসি সিরিজটি একটি অভূতপূর্ব অভিজ্ঞতা।', NULL, NULL, 1, 125, 'published', 'admin', NULL, NULL, NULL, NOW(), NOW()),
(2, 'প্যারাসাইট', 'parasite', 'MSR Team', NULL, '2019', 'movie', 'কোরিয়ান', 'বং জুন-হো', 'Song Kang-ho, Lee Sun-kyun, Cho Yeo-jeong', 5.0, '<p>বং জুন-হো পরিচালিত এই কোরিয়ান মাস্টারপিসটি সামাজিক বৈষম্য নিয়ে একটি অসাধারণ কাহিনী। একটি দরিদ্র পরিবারের জীবন সংগ্রাম এবং ধনী পরিবারের সাথে তাদের জটিল সম্পর্ক এই সিনেমার মূল বিষয়বস্তু।</p>', 'বং জুন-হো পরিচালিত এই কোরিয়ান মাস্টারপিসটি সামাজিক বৈষম্য নিয়ে একটি অসাধারণ কাহিনী।', NULL, NULL, 1, 89, 'published', 'admin', NULL, NULL, NULL, NOW(), NOW()),
(3, 'স্কুইড গেম', 'squid-game', 'MSR Team', NULL, '2021', 'series', 'কোরিয়ান', 'Hwang Dong-hyuk', 'Lee Jung-jae, Park Hae-soo, Wi Ha-joon', 4.5, '<p>নেটফ্লিক্সের এই কোরিয়ান সিরিজটি বিশ্বব্যাপী ঝড় তুলেছে। শৈশবের খেলার আড়ালে লুকিয়ে থাকা নির্মম বাস্তবতা এবং মানুষের লোভ ও হতাশার চিত্র অসাধারণভাবে ফুটিয়ে তোলা হয়েছে।</p>', 'নেটফ্লিক্সের এই কোরিয়ান সিরিজটি বিশ্বব্যাপী ঝড় তুলেছে।', NULL, NULL, 1, 156, 'published', 'admin', NULL, NULL, NULL, NOW(), NOW());

-- --------------------------------------------------------

--
-- Table structure for table `review_categories`
--

CREATE TABLE `review_categories` (
  `id` int(11) NOT NULL,
  `review_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `review_categories`
--

INSERT INTO `review_categories` (`id`, `review_id`, `category_id`) VALUES
(1, 1, 3),
(2, 1, 1),
(3, 2, 3),
(4, 2, 4),
(5, 3, 3),
(6, 3, 4);

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE `site_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_value` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `site_settings`
--

INSERT INTO `site_settings` (`id`, `setting_key`, `setting_value`, `created_at`, `updated_at`) VALUES
(1, 'site_name', 'MSR - Movie & Series Review', NOW(), NOW()),
(2, 'site_description', 'বাংলা ভাষায় সেরা সিনেমা ও সিরিয়াল রিভিউ', NOW(), NOW()),
(3, 'site_logo', '', NOW(), NOW()),
(4, 'favicon', '', NOW(), NOW()),
(5, 'theme_color', '#3B82F6', NOW(), NOW()),
(6, 'footer_text', '© ২০২৫ MSR - Movie & Series Review. সকল অধিকার সংরক্ষিত।', NOW(), NOW());

-- --------------------------------------------------------

--
-- Table structure for table `seo_settings`
--

CREATE TABLE `seo_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_value` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `seo_settings`
--

INSERT INTO `seo_settings` (`id`, `setting_key`, `setting_value`, `created_at`, `updated_at`) VALUES
(1, 'meta_description', 'বাংলা ও বিদেশি মুভি, ওয়েব সিরিজ এবং ড্রামার নির্ভরযোগ্য রিভিউ ও রেটিং', NOW(), NOW()),
(2, 'meta_keywords', 'movie review, বাংলা মুভি, web series, drama review, film analysis, রিভিউ', NOW(), NOW()),
(3, 'google_analytics', '', NOW(), NOW()),
(4, 'google_console_verification', '', NOW(), NOW()),
(5, 'facebook_pixel', '', NOW(), NOW()),
(6, 'bing_webmaster_verification', '', NOW(), NOW()),
(7, 'og_image', '', NOW(), NOW());

-- --------------------------------------------------------

--
-- Table structure for table `security_logs`
--

CREATE TABLE `security_logs` (
  `id` int(11) NOT NULL,
  `event_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `details` json DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `status` (`status`),
  ADD KEY `featured` (`featured`),
  ADD KEY `type` (`type`),
  ADD KEY `created_at` (`created_at`);

--
-- Indexes for table `review_categories`
--
ALTER TABLE `review_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `review_id` (`review_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `seo_settings`
--
ALTER TABLE `seo_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `security_logs`
--
ALTER TABLE `security_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `event_type` (`event_type`),
  ADD KEY `created_at` (`created_at`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `review_categories`
--
ALTER TABLE `review_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `site_settings`
--
ALTER TABLE `site_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `seo_settings`
--
ALTER TABLE `seo_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `security_logs`
--
ALTER TABLE `security_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;