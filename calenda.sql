-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 31, 2026 at 01:18 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `calenda`
--

-- --------------------------------------------------------

--
-- Table structure for table `attachments`
--

CREATE TABLE `attachments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `file_url` text NOT NULL,
  `file_type` varchar(50) DEFAULT NULL,
  `note_id` int(11) DEFAULT NULL,
  `event_id` int(11) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attachments`
--

INSERT INTO `attachments` (`id`, `user_id`, `file_url`, `file_type`, `note_id`, `event_id`, `uploaded_at`) VALUES
(23, NULL, 'uploads/1774955294_0_ระบบรับคำร้องแก้แบบฟอร์ม.pdf', 'pdf', 16, 16, '2026-03-31 11:08:14'),
(24, NULL, 'uploads/1774955294_1_template โครงงานสหกิจศึกษาปรับ 1 คอลัมน์(1).pdf', 'pdf', 16, 16, '2026-03-31 11:08:14'),
(25, NULL, 'uploads/1774955294_2_บทความสหกิจศึกษา นส.ธนิสร มโนรมย์.pdf', 'pdf', 16, 16, '2026-03-31 11:08:14'),
(26, NULL, 'uploads/1774955489_0_ระบบรับคำร้องแก้แบบฟอร์ม.pdf', 'pdf', 17, 17, '2026-03-31 11:11:29'),
(27, NULL, 'uploads/1774955489_1_template โครงงานสหกิจศึกษาปรับ 1 คอลัมน์(1).pdf', 'pdf', 17, 17, '2026-03-31 11:11:29'),
(28, NULL, 'uploads/1774955489_2_บทความสหกิจศึกษา นส.ธนิสร มโนรมย์.pdf', 'pdf', 17, 17, '2026-03-31 11:11:29'),
(29, NULL, 'uploads/1774955489_3_ข้อแนะนำในบทความสหกิจศึกษา.pdf', 'pdf', 17, 17, '2026-03-31 11:11:29'),
(31, NULL, 'uploads/1774955587_0_ระบบรับคำร้องแก้แบบฟอร์ม.pdf', 'pdf', 19, 19, '2026-03-31 11:13:07'),
(32, NULL, 'uploads/1774955765_0_edit_ระบบรับคำร้องแก้แบบฟอร์ม.pdf', 'pdf', 16, 16, '2026-03-31 11:16:05');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `note_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `user_id`, `title`, `start_time`, `end_time`, `note_id`) VALUES
(16, NULL, 'ฟฟฟฟ', '2026-04-01 00:00:00', '2026-04-01 23:59:59', 16),
(17, NULL, 'กกก', '2035-10-25 00:00:00', '2035-10-25 23:59:59', 17),
(18, NULL, 'A', '2026-04-09 00:00:00', '2026-04-09 23:59:59', 18),
(19, NULL, 'AAAA', '2026-04-12 00:00:00', '2026-04-12 23:59:59', 19);

-- --------------------------------------------------------

--
-- Table structure for table `notes`
--

CREATE TABLE `notes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `content` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notes`
--

INSERT INTO `notes` (`id`, `user_id`, `title`, `content`, `updated_at`) VALUES
(16, NULL, 'ฟฟฟฟ', 'ฟฟฟฟ', '2026-03-31 11:08:14'),
(17, NULL, 'กกก', 'กกก', '2026-03-31 11:11:29'),
(18, NULL, 'A', 'A', '2026-03-31 11:12:37'),
(19, NULL, 'AAAA', 'AA', '2026-03-31 11:13:07');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attachments`
--
ALTER TABLE `attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `note_id` (`note_id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `note_id` (`note_id`);

--
-- Indexes for table `notes`
--
ALTER TABLE `notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attachments`
--
ALTER TABLE `attachments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `notes`
--
ALTER TABLE `notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attachments`
--
ALTER TABLE `attachments`
  ADD CONSTRAINT `attachments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attachments_ibfk_2` FOREIGN KEY (`note_id`) REFERENCES `notes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attachments_ibfk_3` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `events_ibfk_2` FOREIGN KEY (`note_id`) REFERENCES `notes` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `notes`
--
ALTER TABLE `notes`
  ADD CONSTRAINT `notes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
