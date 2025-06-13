-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 11, 2025 at 10:42 AM
-- Server version: 8.2.0
-- PHP Version: 8.2.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `livenotes`
--

-- --------------------------------------------------------

--
-- Table structure for table `notes`
--

DROP TABLE IF EXISTS `notes`;
CREATE TABLE IF NOT EXISTS `notes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `content` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `pinned` tinyint(1) DEFAULT '0' COMMENT '0 = not pinned\r\n1 = pinned',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `notes`
--

INSERT INTO `notes` (`id`, `title`, `content`, `created_at`, `updated_at`, `pinned`) VALUES
(1, 'Welcome to Live Notes', 'This is your first note! You can edit or delete it.', '2025-06-10 11:36:14', '2025-06-10 11:36:14', 0),
(2, 'Shopping List', 'Milk\nEggs\nBread\nButter', '2025-06-10 11:36:14', '2025-06-10 11:36:14', 0),
(3, 'Project Ideas', '1. Build a notes app\r\n2. Learn PHP & AJAX\r\n3. Try a new CSS framework\r\n1. Build a notes app\r\n2. Learn PHP & AJAX\r\n3. Try a new CSS framework\r\n1. Build a notes app\r\n2. Learn PHP & AJAX\r\n3. Try a new CSS framework', '2025-06-10 11:36:14', '2025-06-10 12:15:50', 0),
(4, 'Meeting Notes', 'Discussed project milestones and deadlines. Next meeting: Friday.', '2025-06-10 11:36:14', '2025-06-10 11:36:14', 0),
(5, 'Quote', '“The secret of getting ahead is getting started.” – Mark Twain', '2025-06-10 11:36:14', '2025-06-10 11:36:14', 0),
(6, 'test 2', '1 2 3 5 6', '2025-06-10 12:27:48', '2025-06-11 07:10:46', 0),
(10, 'test 4', 'd\nd\nd\nd\ndl\nd\nd\nd\n\nd\nd\n\nd\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\ndddddddddddddddddddddd', '2025-06-11 07:18:37', '2025-06-11 08:56:57', 0),
(17, 'b', 'defd', '2025-06-11 09:13:22', '2025-06-11 09:13:22', 0),
(12, 'L', 'k\n', '2025-06-11 07:47:07', '2025-06-11 07:47:07', 0),
(13, 'A', 'ddd', '2025-06-11 07:56:43', '2025-06-11 07:56:43', 0),
(14, 'D', 'dd', '2025-06-11 07:56:56', '2025-06-11 07:56:56', 0),
(18, 'B', 'ds', '2025-06-11 09:16:51', '2025-06-11 09:16:51', 0),
(19, 'jk', 'mn', '2025-06-11 10:39:28', '2025-06-11 10:39:28', 0);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
