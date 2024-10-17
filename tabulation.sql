-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 17, 2024 at 02:25 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tabulation`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `username`, `password`) VALUES
(1, 'admin', '$2y$10$ygxfHhEXKh.WQjLrcEZQh.vX0X47SFGpktqcW6oi32ccilDVt3lQi');

-- --------------------------------------------------------

--
-- Table structure for table `criteria`
--

CREATE TABLE `criteria` (
  `criteria_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `max_points` decimal(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `criteria`
--

INSERT INTO `criteria` (`criteria_id`, `event_id`, `name`, `max_points`) VALUES
(63, 21, 'best in production', 0.00),
(65, 21, 'best in costume', 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `criteria_details`
--

CREATE TABLE `criteria_details` (
  `detail_id` int(11) NOT NULL,
  `criteria_id` int(11) DEFAULT NULL,
  `sub_criteria_name` varchar(255) DEFAULT NULL,
  `points` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `event_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `criteria` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`event_id`, `name`, `criteria`) VALUES
(18, 'machooooogay', NULL),
(19, 'dog show', NULL),
(20, 'Eating contest', NULL),
(21, 'machogay', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `judges`
--

CREATE TABLE `judges` (
  `judge_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `judges`
--

INSERT INTO `judges` (`judge_id`, `event_id`, `name`, `password`) VALUES
(64, 21, 'denmark loreno', '$2y$10$HvC0zSZbS8hDk7JHlLDN5ORQJYgD7zCpHPPTlU91VjlyVNYVu/27a'),
(65, 21, 'ariel gabiandan', '$2y$10$R/IKAEHLNW25cBwt9Tbz4uXamslBNEpSFBYwxeKt.j5WbNZRVibbm'),
(66, 21, 'anna nicole gabiandan', '$2y$10$mGbe3AFqOALtYoE/ni3Ote7HMkcGiZ/NQ6gw/CSmwcCDn7LkXKkei');

-- --------------------------------------------------------

--
-- Table structure for table `participants`
--

CREATE TABLE `participants` (
  `participant_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `number` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `gender` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `participants`
--

INSERT INTO `participants` (`participant_id`, `event_id`, `number`, `name`, `gender`) VALUES
(72, 21, 1, 'joefel ', 'male'),
(73, 21, 2, 'grover', 'male'),
(74, 21, 3, 'loki', 'male'),
(75, 21, 4, 'anne', 'female');

-- --------------------------------------------------------

--
-- Table structure for table `scores`
--

CREATE TABLE `scores` (
  `score_id` int(11) NOT NULL,
  `participant_id` int(11) NOT NULL,
  `judge_id` int(11) NOT NULL,
  `criteria_id` int(11) NOT NULL,
  `sub_criteria_id` int(11) DEFAULT NULL,
  `score` decimal(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `scores`
--

INSERT INTO `scores` (`score_id`, `participant_id`, `judge_id`, `criteria_id`, `sub_criteria_id`, `score`) VALUES
(1, 72, 66, 63, 1, 25.00),
(2, 72, 66, 63, 2, 25.00),
(3, 72, 66, 63, 3, 50.00),
(4, 73, 66, 63, 1, 15.50),
(5, 73, 66, 63, 2, 20.00),
(6, 73, 66, 63, 3, 37.50),
(7, 74, 66, 63, 1, 14.00),
(8, 74, 66, 63, 2, 21.00),
(9, 74, 66, 63, 3, 36.00),
(10, 72, 66, 65, 4, 49.00),
(11, 72, 64, 63, 1, 18.00),
(12, 72, 64, 63, 2, 16.00),
(13, 72, 64, 63, 3, 47.50),
(14, 73, 64, 63, 1, 20.00),
(15, 73, 64, 63, 2, 16.00),
(16, 73, 64, 63, 3, 39.00),
(17, 74, 64, 63, 1, 20.30),
(18, 74, 64, 63, 2, 23.30),
(19, 74, 64, 63, 3, 39.90),
(20, 72, 64, 65, 4, 40.00),
(21, 72, 64, 65, 5, 15.00),
(22, 72, 64, 65, 6, 26.00),
(23, 73, 64, 65, 4, 37.00),
(24, 73, 64, 65, 5, 17.00),
(25, 73, 64, 65, 6, 26.60),
(26, 74, 64, 65, 4, 39.00),
(27, 74, 64, 65, 5, 17.40),
(28, 74, 64, 65, 6, 26.90),
(29, 72, 65, 63, 1, 20.00),
(30, 72, 65, 63, 2, 22.00),
(31, 72, 65, 63, 3, 28.00),
(32, 73, 65, 63, 1, 23.30),
(33, 73, 65, 63, 2, 23.00),
(34, 73, 65, 63, 3, 38.00),
(35, 74, 65, 63, 1, 20.00),
(36, 74, 65, 63, 2, 23.30),
(37, 74, 65, 63, 3, 30.50),
(38, 72, 65, 65, 4, 20.00),
(39, 72, 65, 65, 5, 10.00),
(40, 72, 65, 65, 6, 20.00),
(41, 73, 65, 65, 4, 38.00),
(42, 73, 65, 65, 6, 23.00),
(43, 74, 65, 65, 4, 38.00),
(44, 74, 65, 65, 6, 20.00),
(45, 73, 65, 65, 5, 20.00),
(46, 74, 65, 65, 5, 19.00);

-- --------------------------------------------------------

--
-- Table structure for table `sub_criteria`
--

CREATE TABLE `sub_criteria` (
  `sub_criteria_id` int(11) NOT NULL,
  `criteria_id` int(11) DEFAULT NULL,
  `sub_criteria_name` varchar(255) DEFAULT NULL,
  `points` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sub_criteria`
--

INSERT INTO `sub_criteria` (`sub_criteria_id`, `criteria_id`, `sub_criteria_name`, `points`) VALUES
(1, 63, 'stage presence', 25.00),
(2, 63, 'audience impact', 25.00),
(3, 63, 'mastery', 50.00),
(4, 65, 'uniqueness', 50.00),
(5, 65, 'stage presence', 20.00),
(6, 65, 'basta nami', 30.00);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `criteria`
--
ALTER TABLE `criteria`
  ADD PRIMARY KEY (`criteria_id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `criteria_details`
--
ALTER TABLE `criteria_details`
  ADD PRIMARY KEY (`detail_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`);

--
-- Indexes for table `judges`
--
ALTER TABLE `judges`
  ADD PRIMARY KEY (`judge_id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `participants`
--
ALTER TABLE `participants`
  ADD PRIMARY KEY (`participant_id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `scores`
--
ALTER TABLE `scores`
  ADD PRIMARY KEY (`score_id`),
  ADD KEY `participant_id` (`participant_id`),
  ADD KEY `judge_id` (`judge_id`),
  ADD KEY `criteria_id` (`criteria_id`);

--
-- Indexes for table `sub_criteria`
--
ALTER TABLE `sub_criteria`
  ADD PRIMARY KEY (`sub_criteria_id`),
  ADD KEY `criteria_id` (`criteria_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `criteria`
--
ALTER TABLE `criteria`
  MODIFY `criteria_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `criteria_details`
--
ALTER TABLE `criteria_details`
  MODIFY `detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `judges`
--
ALTER TABLE `judges`
  MODIFY `judge_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `participants`
--
ALTER TABLE `participants`
  MODIFY `participant_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `scores`
--
ALTER TABLE `scores`
  MODIFY `score_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `sub_criteria`
--
ALTER TABLE `sub_criteria`
  MODIFY `sub_criteria_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `criteria`
--
ALTER TABLE `criteria`
  ADD CONSTRAINT `criteria_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`);

--
-- Constraints for table `judges`
--
ALTER TABLE `judges`
  ADD CONSTRAINT `judges_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`);

--
-- Constraints for table `participants`
--
ALTER TABLE `participants`
  ADD CONSTRAINT `participants_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`);

--
-- Constraints for table `scores`
--
ALTER TABLE `scores`
  ADD CONSTRAINT `scores_ibfk_1` FOREIGN KEY (`participant_id`) REFERENCES `participants` (`participant_id`),
  ADD CONSTRAINT `scores_ibfk_2` FOREIGN KEY (`judge_id`) REFERENCES `judges` (`judge_id`),
  ADD CONSTRAINT `scores_ibfk_3` FOREIGN KEY (`criteria_id`) REFERENCES `criteria` (`criteria_id`);

--
-- Constraints for table `sub_criteria`
--
ALTER TABLE `sub_criteria`
  ADD CONSTRAINT `sub_criteria_ibfk_1` FOREIGN KEY (`criteria_id`) REFERENCES `criteria` (`criteria_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
