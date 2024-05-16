-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 09, 2024 at 08:06 AM
-- Server version: 8.2.0
-- PHP Version: 8.3.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dbhofin`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_property`
--

DROP TABLE IF EXISTS `tbl_property`;
CREATE TABLE IF NOT EXISTS `tbl_property` (
  `property_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `id_no` varchar(255) DEFAULT NULL,
  `blk_no` int DEFAULT NULL,
  `lot_no` int DEFAULT NULL,
  `homelot_area` int DEFAULT NULL,
  `open_space` int DEFAULT NULL,
  `sharein_loan` int DEFAULT NULL,
  `principal_interest` int DEFAULT NULL,
  `MRI` int DEFAULT NULL,
  `total` int DEFAULT NULL,
  PRIMARY KEY (`property_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_property`
--

INSERT INTO `tbl_property` (`property_id`, `user_id`, `id_no`, `blk_no`, `lot_no`, `homelot_area`, `open_space`, `sharein_loan`, `principal_interest`, `MRI`, `total`) VALUES
(1, 2, '1', 2, 3, 4, 5, 6, 7, 8, 9),
(2, 3, '20203', 9, 65, 97569, 57, 65, 756, 87, 6587),
(3, 4, '6', 546, 546, 5464, 654, 646, 46, 5465, 654),
(4, 5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, 7, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_transaction`
--

DROP TABLE IF EXISTS `tbl_transaction`;
CREATE TABLE IF NOT EXISTS `tbl_transaction` (
  `transac_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `balance_debt` int DEFAULT NULL,
  `transc_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` int DEFAULT NULL,
  `date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `is_verified` varchar(10) NOT NULL DEFAULT 'no',
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `proof` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`transac_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_transaction`
--

INSERT INTO `tbl_transaction` (`transac_id`, `user_id`, `balance_debt`, `transc_type`, `amount`, `date`, `due_date`, `is_verified`, `code`, `proof`) VALUES
(1, 2, 3000, 'Gcash', 3000, '2024-04-10', '2024-04-09', 'yes', NULL, './proof/1.jpg'),
(2, 3, 6, 'Cash', 6, '2024-04-03', '2024-04-18', 'yes', '8PD1S', NULL),
(3, 2, 9, 'arrangement', NULL, NULL, '2024-06-20', 'yes', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_useracc`
--

DROP TABLE IF EXISTS `tbl_useracc`;
CREATE TABLE IF NOT EXISTS `tbl_useracc` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `is_admin` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'no',
  `is_deleted` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'no',
  `is_verified` varchar(10) NOT NULL DEFAULT 'no',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_useracc`
--

INSERT INTO `tbl_useracc` (`user_id`, `username`, `password`, `email`, `is_admin`, `is_deleted`, `is_verified`) VALUES
(1, 'admin', '$2b$12$lTwEqKy98rZhefz/lYFO7OFORY7F/NtPyTnnolKfInFsTojgl2ugO', '', 'yes', 'no', 'yes'),
(2, '20242', '$2b$12$92vMhEjqOTN/9wM92jsmgeTi9HRSs5Jo5TxYcbvAUzESCR2HyN/C2', 'jetsebastian4@gmail.com', 'no', 'no', 'yes'),
(3, '20243', '$2b$12$tB9bEdIZlyt08mTJmyI1gOmTYR72q/sA0C9N7nGTvjykBAtG4n8MK', 'agajdbualal@gmail.com', 'no', 'no', 'yes'),
(4, '20244', '$2b$12$Qm6FwOLLAjh8gSzm0d3/Vu8vpvWZxyD8d8kO8ARjpJtNl2ZKUjBfi', 'asdasd@asdasd.asdasd', 'no', 'no', 'yes'),
(5, '20245', '$2b$12$lRIEglLVoRb6RAa7.DbfwOSxtbmx4Qaor54I7vuFmakHkJ.RByua.', 'asdasd@asdasd.com', 'no', 'no', 'yes'),
(6, '20246', '$2b$12$1j5jtFYItWfcyHisOi5k1ex45T8434jo33Q0HZZolRhXJ7T4Jx1uC', 'j@j.com', 'no', 'no', 'no'),
(7, '20247', '$2b$12$TVWPV1ma1E9nEVn4Omh0beBk7BxOfy7SiodhZHZ8vATQBxvLik57e', 'j@a.com', 'no', 'no', 'yes');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_userinfo`
--

DROP TABLE IF EXISTS `tbl_userinfo`;
CREATE TABLE IF NOT EXISTS `tbl_userinfo` (
  `userinfo_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `given_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `middle_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `gender` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `bday` date NOT NULL,
  PRIMARY KEY (`userinfo_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_userinfo`
--

INSERT INTO `tbl_userinfo` (`userinfo_id`, `user_id`, `given_name`, `middle_name`, `last_name`, `gender`, `bday`) VALUES
(1, 1, 'Admin', '', '', '', '0000-00-00'),
(2, 2, 'Jet', 'Dela cruz', 'Sebastian', 'Male', '0000-00-00'),
(3, 3, 'John andrei', 'Samonte', 'Canlas', 'Male', '2024-05-15'),
(4, 4, 'A', 'B', 'C', 'Male', '0000-00-00'),
(5, 5, 'Asd', 'Dsa', 'Sasd', 'Female', '0000-00-00'),
(6, 6, 'J', 'D', 'S', 'Male', '2002-02-01'),
(7, 7, 'J', 'D', 'S', 'Male', '2002-02-01');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_property`
--
ALTER TABLE `tbl_property`
  ADD CONSTRAINT `tbl_property_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tbl_useracc` (`user_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `tbl_transaction`
--
ALTER TABLE `tbl_transaction`
  ADD CONSTRAINT `tbl_transaction_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tbl_useracc` (`user_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `tbl_userinfo`
--
ALTER TABLE `tbl_userinfo`
  ADD CONSTRAINT `tbl_userinfo_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tbl_useracc` (`user_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
