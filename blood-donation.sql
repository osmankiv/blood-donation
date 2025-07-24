-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: 24 يوليو 2025 الساعة 17:38
-- إصدار الخادم: 8.0.31
-- PHP Version: 8.2.0

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

DROP TABLE IF EXISTS `blood_requests`;
CREATE TABLE IF NOT EXISTS `blood_requests` (
  `id` int NOT NULL AUTO_INCREMENT,
  `hospital_name` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `blood_type` varchar(5) DEFAULT NULL,
  `bags` int DEFAULT NULL,
  `contact_number` varchar(15) DEFAULT NULL,
  `notes` text,
  `urgency` varchar(255) NOT NULL,
  `request_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- إرجاع أو استيراد بيانات الجدول `blood_requests`
--

INSERT INTO `blood_requests` (`id`, `hospital_name`, `city`, `blood_type`, `bags`, `contact_number`, `notes`, `urgency`, `request_date`) VALUES
(1, 'المنوره', 'الخرطوم', '+O', 1, '0999553493', 'شكرا', '', '2025-07-24 16:59:41'),
(3, 'التركي', 'الخرطوم', '-AB', 3, '091236574', '', 'عاديه', '2025-07-24 17:22:28');

-- --------------------------------------------------------

--
-- بنية الجدول `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `city` varchar(100) DEFAULT NULL,
  `blood_type` varchar(5) DEFAULT NULL,
  `last_donation_date` date DEFAULT NULL,
  `points` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `phone` (`phone`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- إرجاع أو استيراد بيانات الجدول `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `password`, `city`, `blood_type`, `last_donation_date`, `points`, `created_at`) VALUES
(1, 'osman', 'osman@gmail.com', '0999553493', '$2y$10$F2n9.G1Q1LElc2d4wlRsWuIxlBnAMAEBsa6rdu3jsP6F7VHoZyThu', 'khko', '-A', NULL, 270, '2025-07-23 13:25:36');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
