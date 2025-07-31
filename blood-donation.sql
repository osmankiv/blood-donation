-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 31 يوليو 2025 الساعة 19:08
-- إصدار الخادم: 10.4.6-MariaDB
-- PHP Version: 8.3.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `blood-donation`
--

-- --------------------------------------------------------

--
-- بنية الجدول `blood_requests`
--

CREATE TABLE `blood_requests` (
  `id` int(11) NOT NULL,
  `hospital_name` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `blood_type` varchar(5) DEFAULT NULL,
  `bags` int(11) DEFAULT NULL,
  `contact_number` varchar(15) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `urgency` varchar(255) NOT NULL,
  `request_date` timestamp NULL DEFAULT current_timestamp(),
  `Create_by` varchar(255) NOT NULL DEFAULT 'Anamosa '
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

--
-- إرجاع أو استيراد بيانات الجدول `blood_requests`
--

INSERT INTO `blood_requests` (`id`, `hospital_name`, `city`, `blood_type`, `bags`, `contact_number`, `notes`, `urgency`, `request_date`, `Create_by`) VALUES
(1, 'المنوره', 'الخرطوم', '+O', 1, '0999553493', 'شكرا', '', '2025-07-24 16:59:41', 'Anamosa '),
(3, 'التركي', 'الخرطوم', '-AB', 3, '091236574', '', 'عاديه', '2025-07-24 17:22:28', 'Anamosa '),
(4, 'jghjghjg', 'hgjhg', '+AB', 15, '097686756', 'ghj', 'طارئة', '2025-07-26 17:40:24', 'Anamosa '),
(7, 'بتلتلت', 'بنبن', '+B', 2, '095218732', 'بة', 'طارئة', '2025-07-30 07:56:58', '1'),
(6, 'النو', 'الخرطوم', '-O', 4, '095236423', 'creat_by', 'طارئة', '2025-07-30 05:53:31', '1'),
(8, 'ىتال', 'التا', '+O', 2, '096584235', 'باى', 'طارئة', '2025-07-30 17:28:30', '1'),
(9, 'T', 'Y', '+O', 1, '098574252', 'F', 'طارئة', '2025-07-31 17:12:23', '1');

-- --------------------------------------------------------

--
-- بنية الجدول `donations`
--

CREATE TABLE `donations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `donated_at` datetime DEFAULT current_timestamp(),
  `status` enum('pending','confirmed','completed') DEFAULT 'pending'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

--
-- إرجاع أو استيراد بيانات الجدول `donations`
--

INSERT INTO `donations` (`id`, `user_id`, `request_id`, `donated_at`, `status`) VALUES
(4, 1, 3, '2025-07-25 18:11:41', 'pending'),
(5, 1, 1, '2025-07-25 18:13:52', 'pending'),
(6, 1, 4, '2025-07-26 19:40:57', 'pending'),
(7, 1, 6, '2025-07-30 09:35:53', 'completed'),
(8, 1, 7, '2025-07-30 09:58:27', 'completed'),
(9, 1, 9, '2025-07-31 20:46:31', 'completed'),
(10, 2, 1, '2025-07-31 19:25:39', 'pending'),
(11, 2, 6, '2025-07-31 20:14:17', 'completed'),
(12, 2, 9, '2025-07-31 20:40:31', 'completed'),
(13, 2, 8, '2025-07-31 20:45:30', 'completed');

-- --------------------------------------------------------

--
-- بنية الجدول `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `hospital` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- إرجاع أو استيراد بيانات الجدول `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `message`, `hospital`, `is_read`, `created_at`) VALUES
(1, 1, ' حالة طارئة لفصيلة دمك +O في T', 'T', 0, '2025-07-31 17:12:15');

-- --------------------------------------------------------

--
-- بنية الجدول `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `city` varchar(100) DEFAULT NULL,
  `blood_type` varchar(5) DEFAULT NULL,
  `last_donation_date` date DEFAULT NULL,
  `points` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `latitude` varchar(50) DEFAULT NULL,
  `longitude` varchar(50) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

--
-- إرجاع أو استيراد بيانات الجدول `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `password`, `city`, `blood_type`, `last_donation_date`, `points`, `created_at`, `latitude`, `longitude`) VALUES
(1, 'osman', 'osman@gmail.com', '0999553493', '$2y$10$wSXwJraxnhlBQjisxC3eUeybng/uRBlJlQ8bGb/l6b9CNBFoA8dEa', 'khko', '+O', NULL, 290, '2025-07-23 13:25:36', '15.4710991', '32.4645924'),
(2, 'Os2', 'os2@gmail.com', '0987543234', '$2y$10$2IuOADC7.N5O/q1xdCvLHucVOneg46Qwcc.i.hmqA45Wc.6Y.n4Ha', NULL, NULL, NULL, 120, '2025-07-31 17:22:47', '15.4774241', '32.4691918');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `blood_requests`
--
ALTER TABLE `blood_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `donations`
--
ALTER TABLE `donations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `request_id` (`request_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone` (`phone`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `blood_requests`
--
ALTER TABLE `blood_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `donations`
--
ALTER TABLE `donations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
