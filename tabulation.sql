-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 17, 2024 at 03:01 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

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
(1, 'arl', '$2y$10$J49r7n2.HA9zOQA1h7/jP.GBPnzrSofQjVhHKlkNrEUR15NMv00za');

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
(23, 14, 'Best in Diaper', 25.00),
(24, 13, 'best in gown', 20.00),
(25, 13, 'gwapa', 20.00),
(26, 13, 'pa macho2', 50.00);

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `event_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`event_id`, `name`) VALUES
(13, 'machogay'),
(14, 'Dog Show'),
(15, 'Grovers Dog Show Event');

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
(23, 13, 'anna ermeo', '$2y$10$PaDCLluVNGBJijBOXB4BueZujN4B.AHbCeynm4nitPkWzg2cgrhIW'),
(24, 14, 'Ariel Gabiandan', '$2y$10$jdtIs0IIfYZpLu7UBwIE.u3wj4uUJw9chiTSA6XuvdCkCRzWSgybq'),
(25, 14, 'Anna Nicole S. Ermeo', '$2y$10$ugUCtcM71dm7ICrHrvSq9.sGTBqmP62SbLbAadXIlNjYpCQSX8.6e'),
(26, 14, 'Anne Therese Ermeo', '$2y$10$I9MzsjNMAUzW6p008OCO7uiy60wd898zzglrxZRIF/BvG.Fe3phl6'),
(27, 13, 'anne ermeo', '$2y$10$t3qEJxFCgyj.ijHCtR6Rjee/tOtCjud.JNqVf1fM6E/hOCHK8wezm'),
(28, 13, 'dwayne serenio', '$2y$10$/zvJqTl0M513iZQmSv3o/OeilC.xqVZfdSn/7h7ufE0CGIyzxjul2'),
(29, 13, 'junjun', '$2y$10$BTYjGV37MeKiEa2xRspRz.vB3L5R/oSLE7jmKiaLyfw52NuoXEKqS'),
(30, 13, 'grover', '$2y$10$jqoJ7zEzRDdBaiSXJ2Fm0OLNVQ.9LqkDvj7vcJFp4wpz5VSHq9H9i'),
(32, 15, 'teresa ermeo', '$2y$10$UD71iwT66iwbESkY.5T69uiN7AIZ7lBCs68B3ae2/nS5LZ.k1VhTq'),
(33, 15, 'anna ermeo', '$2y$10$6smQtYD28OWsXAZ1wHYzYOptFK6JSp2Ay/l5awMZ.T3Gsj/7Qd4eu');

-- --------------------------------------------------------

--
-- Table structure for table `participants`
--

CREATE TABLE `participants` (
  `participant_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `number` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `participants`
--

INSERT INTO `participants` (`participant_id`, `event_id`, `number`, `name`) VALUES
(24, 14, 1, 'Loki'),
(25, 14, 2, 'Grover'),
(26, 13, 1, 'princess carlita'),
(27, 15, 1, 'loki'),
(28, 15, 2, 'valac');

-- --------------------------------------------------------

--
-- Table structure for table `scores`
--

CREATE TABLE `scores` (
  `score_id` int(11) NOT NULL,
  `participant_id` int(11) NOT NULL,
  `judge_id` int(11) NOT NULL,
  `criteria_id` int(11) NOT NULL,
  `score` decimal(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `scores`
--

INSERT INTO `scores` (`score_id`, `participant_id`, `judge_id`, `criteria_id`, `score`) VALUES
(31, 24, 25, 23, 20.00),
(32, 25, 25, 23, 25.00),
(33, 24, 24, 23, 22.00),
(34, 25, 24, 23, 23.00),
(42, 26, 23, 24, 15.00),
(43, 26, 23, 25, 11.00),
(44, 26, 23, 26, 2.00);

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
  MODIFY `criteria_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `judges`
--
ALTER TABLE `judges`
  MODIFY `judge_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `participants`
--
ALTER TABLE `participants`
  MODIFY `participant_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `scores`
--
ALTER TABLE `scores`
  MODIFY `score_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
