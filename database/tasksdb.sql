-- phpMyAdmin SQL Dump
-- version 4.9.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 15, 2021 at 08:01 AM
-- Server version: 5.7.24
-- PHP Version: 7.4.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tasksdb`
--
CREATE DATABASE IF NOT EXISTS `tasksdb` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `tasksdb`;

-- --------------------------------------------------------

--
-- Table structure for table `tbltasks`
--

CREATE TABLE `tbltasks` (
  `id` bigint(20) NOT NULL COMMENT 'Task ID - Primary Key',
  `title` varchar(255) NOT NULL COMMENT 'Task Title',
  `description` mediumtext COMMENT 'Task Description',
  `deadline` datetime DEFAULT NULL COMMENT 'Task Deadline Date',
  `completed` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT 'Task Completion Status'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Tasks table';

--
-- Dumping data for table `tbltasks`
--

INSERT DELAYED INTO `tbltasks` (`id`, `title`, `description`, `deadline`, `completed`) VALUES
(2, 'Task 2', 'description 2', '2021-07-16 13:30:13', 'N'),
(3, 'Task 3', 'description 3', '2021-06-10 18:01:55', 'Y'),
(4, 'Task 4', 'description 4', '2021-06-19 18:01:55', 'N'),
(5, 'Task 5', 'description 5', NULL, 'Y'),
(6, 'Task 6', 'description 6', '2021-06-19 10:02:52', 'N');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbltasks`
--
ALTER TABLE `tbltasks`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbltasks`
--
ALTER TABLE `tbltasks`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'Task ID - Primary Key', AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
