-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 16, 2025 at 06:17 AM
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
) ENGINE=MyISAM AUTO_INCREMENT=68 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `notes`
--

INSERT INTO `notes` (`id`, `title`, `content`, `created_at`, `updated_at`, `pinned`) VALUES
(1, 'Welcome to Live Notes', 'This is your first note! You can edit or delete it.', '2025-06-10 11:36:14', '2025-06-13 08:40:17', 0),
(2, 'Shopping List', 'Milk\nEggs\nBread\nButter', '2025-06-10 11:36:14', '2025-06-13 08:40:24', 0),
(3, 'Project Ideas', '1. Build a notes app\n\n2. Learn PHP &amp; AJAX\n\n3. Try a new CSS framework\n\n1. Build a notes app\n\n2. Learn PHP &amp; AJAX\n\n3. Try a new CSS framework\n\n1. Build a notes app\n\n2. Learn PHP &amp; AJAX\n\n3. Try a new CSS framework\n\n1. Build a notes app\n\n2. Learn PHP &amp; AJAX\n\n3. Try a new CSS framework\n\n1. Build a notes app\n\n2. Learn PHP &amp; AJAX\n\n3. Try a new CSS framework\n\n1. Build a notes app\n\n2. Learn PHP &amp; AJAX\n\n3. Try a new CSS framework\n\n1. Build a notes app\n\n2. Learn PHP &amp; AJAX\n\n3. Try a new CSS framework\n\n1. Build a notes app\n\n2. Learn PHP &amp; AJAX\n\n3. Try a new CSS framework\n\n1. Build a notes app\n\n2. Learn PHP &amp; AJAX\n\n3. Try a new CSS framework\n\n1. Build a notes app\n\n2. Learn PHP &amp; AJAX\n\n3. Try a new CSS framework\n\n1. Build a notes app\n\n2. Learn PHP &amp; AJAX\n\n3. Try a new CSS framework\n\n1. Build a notes app\n\n2. Learn PHP &amp; AJAX\n\n3. Try a new CSS framework\n\n1. Build a notes app\n\n2. Learn PHP &amp; AJAX\n\n3. Try a new CSS framework\n\n1. Build a notes app\n\n2. Learn PHP &amp; AJAX\n\n3. Try a new CSS framework\n\n1. Build a notes app\n\n2. Learn PHP &amp; AJAX\n\n3. Try a new CSS framework\n\n1. Build a notes app\n\n2. Learn PHP &amp; AJAX\n\n3. Try a new CSS framework\n\n1. Build a notes app\n\n2. Learn PHP &amp; AJAX\n\n3. Try a new CSS framework\n\n1. Build a notes app\n\n2. Learn PHP &amp; AJAX\n\n3. Try a new CSS framework\n\n1. Build a notes app\n\n2. Learn PHP &amp; AJAX\n\n3. Try a new CSS framework\n\n1. Build a notes app\n\n2. Learn PHP &amp; AJAX\n\n3. Try a new CSS framework\n\n1. Build a notes app\n\n2. Learn PHP &amp; AJAX\n\n3. Try a new CSS framework\n\n1. Build a notes app\n\n2. Learn PHP &amp; AJAX\n\n3. Try a new CSS framework\n\n1. Build a notes app\n\n2. Learn PHP &amp; AJAX\n\n3. Try a new CSS framework\n\n1. Build a notes app\n\n2. Learn PHP &amp; AJAX\n\n3. Try a new CSS framework\n\n1. Build a notes app\n\n2. Learn PHP &amp; AJAX\n\n3. Try a new CSS framework\n\n1. Build a notes app\n\n2. Learn PHP &amp; AJAX\n\n3. Try a new CSS framework', '2025-06-10 11:36:14', '2025-06-13 11:13:51', 0),
(4, 'Meeting Notes', 'Discussed project milestones and deadlines. Next meeting: Friday.', '2025-06-10 11:36:14', '2025-06-13 08:40:27', 0),
(5, 'Quote', '“The secret of getting ahead is getting started.” – Mark Twain', '2025-06-10 11:36:14', '2025-06-13 08:40:27', 0),
(26, 'test', 'test', '2025-06-13 11:51:09', '2025-06-16 04:54:12', 0),
(62, 'Travel Plans', 'Visit Istanbul in September. Budget: $1000.', '2025-06-16 05:13:52', '2025-06-16 05:13:52', 1),
(61, 'Meeting Notes', 'Discussed the Q3 marketing strategy. Tasks assigned.', '2025-06-16 05:13:52', '2025-06-16 05:13:52', 0),
(28, 'xxs', 'literally why i had to use escapeHTML\n\n<script>alert(\'Hacked!\');</script>', '2025-06-13 13:10:32', '2025-06-15 17:37:07', 0),
(27, 'xxs 3', '<script>alert(\'Hacked!\')</script>', '2025-06-13 13:09:50', '2025-06-15 21:10:02', 0),
(25, 'farahfinal', 'final final', '2025-06-13 11:48:45', '2025-06-13 11:48:50', 1),
(60, 'Book Quotes', '“Not all those who wander are lost.” – Tolkien', '2025-06-16 05:13:52', '2025-06-16 05:13:52', 0),
(58, 'Grocery List', 'Buy milk, eggs, bread, and coffee.', '2025-06-16 05:13:52', '2025-06-16 05:13:52', 0),
(59, 'Project Ideas', '1. AI-based note app\n2. Smart fridge tracker\n3. Mood journal app', '2025-06-16 05:13:52', '2025-06-16 05:13:52', 1),
(63, 'Fitness Goals', 'Run 3 times a week. Track progress in app.', '2025-06-16 05:13:52', '2025-06-16 05:13:52', 0),
(64, 'Study Topics', 'Review chapters 3–5 for Networks exam.', '2025-06-16 05:13:52', '2025-06-16 05:13:52', 0),
(65, 'To-Do Today', '1. Respond to emails\n2. Submit timesheet\n3. Call plumber', '2025-06-16 05:13:52', '2025-06-16 05:13:52', 1),
(66, 'Gift Ideas', 'Personalized mug, book, wireless earbuds.', '2025-06-16 05:13:52', '2025-06-16 05:13:52', 0),
(67, 'Random Thoughts', 'What if memories could be downloaded?', '2025-06-16 05:13:52', '2025-06-16 05:13:52', 0);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
