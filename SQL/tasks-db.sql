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
  `id` bigint(20) NOT NULL COMMENT 'Task ID Number - Primary Key',
  `title` varchar(255) NOT NULL COMMENT 'Task Title',
  `description` mediumtext COMMENT 'Task Description',
  `deadline` datetime DEFAULT NULL COMMENT 'Task Deadline',
  `completed` enum('N','Y') NOT NULL DEFAULT 'N' COMMENT 'Task Complete'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table to store to do tasks';

-- --------------------------------------------------------

--
-- Indexes for table `tbltasks`
--
ALTER TABLE `tbltasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `completed` (`completed`);

--
-- AUTO_INCREMENT for table `tbltasks`
--
ALTER TABLE `tbltasks`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'Task ID Number - Primary Key';

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
