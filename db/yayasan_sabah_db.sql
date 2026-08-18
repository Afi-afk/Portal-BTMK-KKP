-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 12, 2026 at 04:09 AM
-- Server version: 10.4.18-MariaDB
-- PHP Version: 8.0.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `yayasan_sabah_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `hazard_reports`
--

CREATE TABLE `hazard_reports` (
  `id` int(11) NOT NULL,
  `incident_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `attachment_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Pending','In Progress','Resolved') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hazard_reports`
--

INSERT INTO `hazard_reports` (`id`, `incident_type`, `location`, `description`, `attachment_path`, `status`, `created_at`) VALUES
(1, 'near_miss', 'Basement - HR &#38; Admin (Vehicle Pool)', 'jdkasjdjasjdas', NULL, 'Pending', '2026-08-05 16:27:38'),
(2, 'unsafe_condition', 'Level 4 - Education Development (Higher Education Loan)', 'khsdjahskhash', 'uploads/hazards/hazard_20260805_102838_6a72f4368921a.jpg', 'Pending', '2026-08-05 16:28:38'),
(3, 'unsafe_condition', 'Level 4 - Education Development (Higher Education Loan)', 'dgsds', NULL, 'Pending', '2026-08-10 08:59:01'),
(4, 'unsafe_condition', 'Other - Parking Area', 'kasjdasiodhis', NULL, 'Pending', '2026-08-10 09:00:33'),
(5, 'unsafe_act', 'Podium - Multivision Room', 'kshdjahsihdi', NULL, 'Pending', '2026-08-10 09:01:49'),
(6, 'near_miss', 'Podium - Security Counter', 'cbfbvncvn', NULL, 'Pending', '2026-08-10 09:05:38'),
(7, 'near_miss', 'Level 8 - ICT &#38; OSH Division', 'jadkjahdhaodh', NULL, 'Pending', '2026-08-10 09:07:38'),
(8, 'unsafe_condition', 'Level 23 - Internal Audit Division', 'kzkdhasdhoasdh', NULL, 'Pending', '2026-08-10 09:08:01'),
(9, 'unsafe_act', 'Level 27 - Accounts &#38; Financial Division', 'kashdiasid', NULL, 'Pending', '2026-08-10 09:10:40');

-- --------------------------------------------------------

--
-- Table structure for table `ict_reports`
--

CREATE TABLE `ict_reports` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ticket_no` varchar(20) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `status` enum('pending','in_progress','completed','cancelled') DEFAULT 'pending',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `organization_info`
--

CREATE TABLE `organization_info` (
  `id` int(11) NOT NULL DEFAULT 1,
  `org_name` varchar(255) NOT NULL,
  `org_desc` text DEFAULT NULL,
  `vision` text DEFAULT NULL,
  `mission` text DEFAULT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `org_chart_image` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `organization_info`
--

INSERT INTO `organization_info` (`id`, `org_name`, `org_desc`, `vision`, `mission`, `address`, `phone`, `email`, `org_chart_image`, `updated_at`) VALUES
(1, 'Nama Organisasi / Jabatan', 'Penerangan ringkas organisasi.', 'Visi organisasi.', 'Misi organisasi.', 'Alamat Pejabat', '03-12345678', 'admin@org.gov.my', NULL, '2026-08-10 03:37:32');

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE `site_settings` (
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `site_settings`
--

INSERT INTO `site_settings` (`setting_key`, `setting_value`) VALUES
('btmk_mission_bm', 'Menyediakan perkhidmatan ICT yang cekap, infrastruktur rangkaian moden, serta perlindungan keselamatan siber.'),
('btmk_vision_bm', 'Menjadi pemangkin transformasi digital yang cemerlang, selamat, dan berdaya saing bagi Kumpulan Yayasan Sabah.'),
('kkp_mission_bm', 'Memastikan pematuhan KKP, melaksanakan penilaian HIRARC, serta membudayakan amalan keselamatan.'),
('kkp_vision_bm', 'Mewujudkan persekitaran kerja yang selamat, sihat, dan bebas daripada kemalangan.');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `department` varchar(100) DEFAULT 'Kumpulan Yayasan Sabah',
  `role` enum('admin','btmk','kkp','staff') DEFAULT 'staff',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `phone` varchar(20) DEFAULT NULL,
  `reset_token` varchar(64) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `email`, `department`, `role`, `status`, `created_at`, `updated_at`, `phone`, `reset_token`, `reset_expires`) VALUES
(1, 'admin', '$2y$10$mqi6C1xaHiLiwB.YiiXguOgv2VdH5M.cDtAtKzhZONJT9890paSWy', 'Administrator BTMK & KKP', 'admin_btmk@ys.sabah.gov.my', 'BTMK & KKP', 'admin', 'active', '2026-08-04 03:52:11', '2026-08-04 04:02:04', NULL, NULL, NULL),
(2, 'staf1', '$2y$10$iNmEir9oX4qAPPB0cCIRL.78OIdjpTv6XPCs/6E6B8O/XI2K2P032', 'Ahmad Bin Razak', 'ahmad@ys.sabah.gov.my', 'Bahagian Sumber Manusia', '', 'active', '2026-08-04 03:52:11', '2026-08-10 02:38:51', NULL, '32d7cd5a3805e94261ffcec4bdd8d7c3452c6a2b2131a2306d2a139fd423a0c8', '2026-08-10 05:38:51');

-- --------------------------------------------------------

--
-- Table structure for table `visitor_logs`
--

CREATE TABLE `visitor_logs` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(64) NOT NULL,
  `visit_date` date NOT NULL,
  `last_visit` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `visitor_logs`
--

INSERT INTO `visitor_logs` (`id`, `ip_address`, `visit_date`, `last_visit`) VALUES
(1, 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', '2026-08-05', '2026-08-05 16:24:40'),
(6, 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', '2026-08-10', '2026-08-10 12:30:21'),
(32, 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', '2026-08-11', '2026-08-11 16:57:36');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `hazard_reports`
--
ALTER TABLE `hazard_reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ict_reports`
--
ALTER TABLE `ict_reports`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ticket_no` (`ticket_no`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `organization_info`
--
ALTER TABLE `organization_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`setting_key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `visitor_logs`
--
ALTER TABLE `visitor_logs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_visit` (`ip_address`,`visit_date`),
  ADD UNIQUE KEY `uk_ip_date` (`ip_address`,`visit_date`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `hazard_reports`
--
ALTER TABLE `hazard_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `ict_reports`
--
ALTER TABLE `ict_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `visitor_logs`
--
ALTER TABLE `visitor_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ict_reports`
--
ALTER TABLE `ict_reports`
  ADD CONSTRAINT `ict_reports_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
