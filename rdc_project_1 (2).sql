-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 26, 2025 at 06:33 AM
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
-- Database: `rdc_project_1`
--

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

DROP TABLE IF EXISTS `courses`;
CREATE TABLE IF NOT EXISTS `courses` (
  `id` int NOT NULL AUTO_INCREMENT,
  `course_code` varchar(20) NOT NULL,
  `course_name` varchar(100) NOT NULL,
  `description` text,
  `credits` int DEFAULT '3',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `course_code` (`course_code`),
  UNIQUE KEY `course_name` (`course_name`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `course_code`, `course_name`, `description`, `credits`, `created_at`, `updated_at`) VALUES
(12, 'CSC443', 'Web Development', 'hjbmn', 5, '2025-05-21 13:26:04', '2025-05-23 09:25:40'),
(13, 'MTH301', 'Linear Algebra', 'feefjhgeriwuoqejnjvf', 5, '2025-05-22 10:49:13', '2025-05-22 10:49:13'),
(30, 'CSC433', 'Web Dev', 'Covers frontend and backend web technologies including HTML, CSS, JavaScript, and server-side frameworks.', 5, '2025-05-23 15:15:50', '2025-05-23 15:15:50'),
(31, 'CSC310', 'Data Structures', 'Focuses on implementation and analysis of data structures such as lists, stacks, queues, trees, and graphs.', 3, '2025-05-23 15:15:50', '2025-05-23 15:15:50'),
(32, 'CSC375', 'Database Systems', 'Introduction to relational database systems, SQL, normalization, and transaction management.', 4, '2025-05-23 15:15:50', '2025-05-23 15:15:50'),
(33, 'MTH201', 'Calculus I', 'Differential and integral calculus with applications to science and engineering.', 3, '2025-05-23 15:15:50', '2025-05-23 15:15:50'),
(34, 'ENG102', 'English Communication Skills', 'Enhances academic writing, speaking, and critical thinking skills in English.', 2, '2025-05-23 15:15:50', '2025-05-23 15:15:50'),
(35, 'BUS210', 'Principles of Marketing', 'Overview of marketing principles, strategies, consumer behavior, and market research.', 3, '2025-05-23 15:15:50', '2025-05-23 15:15:50'),
(39, 'nut201', 'Fundamentals of Nutrition ', 'NA', 3, '2025-05-26 09:24:49', '2025-05-26 09:24:49');

-- --------------------------------------------------------

--
-- Table structure for table `registrations`
--

DROP TABLE IF EXISTS `registrations`;
CREATE TABLE IF NOT EXISTS `registrations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `student_id` int NOT NULL,
  `course_id` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `student_id` (`student_id`,`course_id`),
  KEY `fk_registration_course` (`course_id`)
) ENGINE=InnoDB AUTO_INCREMENT=102 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `registrations`
--

INSERT INTO `registrations` (`id`, `student_id`, `course_id`) VALUES
(83, 1, 13),
(88, 1, 30),
(81, 9, 12),
(44, 9, 13),
(89, 9, 30),
(76, 10, 13),
(90, 10, 30),
(97, 21, 30),
(98, 24, 12),
(100, 24, 13),
(101, 24, 34);

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

DROP TABLE IF EXISTS `schedules`;
CREATE TABLE IF NOT EXISTS `schedules` (
  `id` int NOT NULL AUTO_INCREMENT,
  `course_id` int NOT NULL,
  `day_of_week` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `location` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `course_id` (`course_id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `schedules`
--

INSERT INTO `schedules` (`id`, `course_id`, `day_of_week`, `start_time`, `end_time`, `location`) VALUES
(35, 38, 'Monday', '09:15:00', '09:19:00', ''),
(36, 17, 'Monday', '16:05:00', '22:16:00', ''),
(37, 12, 'Monday', '08:00:00', '09:50:00', 'NH304');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

DROP TABLE IF EXISTS `students`;
CREATE TABLE IF NOT EXISTS `students` (
  `id` int NOT NULL AUTO_INCREMENT,
  `student_id` varchar(20) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(191) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `gender` enum('Male','Female') DEFAULT NULL,
  `major` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `student_id` (`student_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `student_id`, `first_name`, `last_name`, `email`, `phone`, `dob`, `gender`, `major`, `created_at`, `updated_at`) VALUES
(1, '202201070', 'Nour', 'Jalloul', 'nour.jalloul01@lau.edu', '70166557', '2007-01-05', 'Female', 'Computer Science', '2025-05-15 11:43:33', '2025-05-22 13:01:42'),
(9, '20250009', 'Nour', 'Jalloul', 'sidein.come232@gmail.com', '70166557', '2005-01-05', 'Female', 'Computer Science', '2025-05-20 09:17:15', '2025-05-26 06:26:23'),
(10, '20250010', 'FARAH', 'bestie ^_^', 'foufi111@gmai.com', '70166557', '2025-04-29', 'Female', 'Computer Science 97', '2025-05-22 07:38:08', '2025-05-23 10:32:42'),
(15, '20250015', 'frgbh', 'fiybh', 'rftgv@gmail.com', '123454', '0000-00-00', '', 'vgbhn', '2025-05-23 12:10:57', '2025-05-23 12:10:57'),
(16, '202201071', 'Ali', 'Hassan', 'ali.hassan@lau.edu', '70311223', '2006-11-15', 'Male', 'Computer Science', '2025-05-15 11:50:12', '2025-05-22 13:02:10'),
(17, '202201072', 'Maya', 'Zein', 'maya.zein@lau.edu', '76544321', '2005-06-10', 'Female', 'Graphic Design', '2025-05-15 11:51:45', '2025-05-22 13:02:11'),
(18, '202201073', 'Omar', 'Khatib', 'omar.khatib@lau.edu', '71123456', '2004-09-23', 'Male', 'Mechanical Engineering', '2025-05-15 11:53:00', '2025-05-22 13:02:12'),
(19, '202201074', 'Layla', 'Tamer', 'layla.tamer@lau.edu', '78123456', '2007-03-18', 'Female', 'Business Administration', '2025-05-15 11:54:30', '2025-05-22 13:02:13'),
(20, '202201075', 'Jad', 'Kassem', 'jad.kassem@lau.edu', '71654321', '2006-12-02', 'Male', 'Computer Science', '2025-05-15 11:55:50', '2025-05-22 13:02:14'),
(21, '20250021', 'dfgvbn', 'jfgvbn', 'drfghb@gmail.com', '6666666', '0000-00-00', 'Female', 'drfvgbhn fcvghbn', '2025-05-26 05:45:39', '2025-05-26 06:15:14'),
(22, '20250022', 'drfkg', 'fkgvbhj', 'nour.jallodfghbjul01@lau.edu', '4657788', '0000-00-00', 'Female', 'dcgfv', '2025-05-26 06:17:22', '2025-05-26 06:17:22'),
(23, '20250023', 'djfgvkhbjn', 'fghkbjn', 'nour.jallftghboul01@lau.edu', '576878', '0000-00-00', '', 'ghbjhm', '2025-05-26 06:17:40', '2025-05-26 06:17:40'),
(24, '20250024', 'Huda', 'Badr', 'huda.badr01@lau.edu', '71859465', '2005-04-18', 'Female', 'ITM', '2025-05-26 06:18:52', '2025-05-26 06:19:20');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `registrations`
--
ALTER TABLE `registrations`
  ADD CONSTRAINT `fk_registration_course` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_registration_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
