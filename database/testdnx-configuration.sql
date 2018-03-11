-- phpMyAdmin SQL Dump
-- version 4.1.8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 30, 2014 at 03:42 PM
-- Server version: 5.1.73-cll
-- PHP Version: 5.4.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `t3stdnxn_realtysi_database`
--

-- --------------------------------------------------------

DROP TABLE `configuration`;

--
-- Table structure for table `configuration`
--

CREATE TABLE IF NOT EXISTS `configuration` (
  `configuration_id` int(11) NOT NULL AUTO_INCREMENT,
  `configuration_group_id` int(11) NOT NULL DEFAULT '0',
  `key_name` varchar(64) NOT NULL DEFAULT '',
  `value` varchar(64) NOT NULL DEFAULT '',
  `select_type` varchar(128) NOT NULL DEFAULT '',
  PRIMARY KEY (`configuration_id`),
  KEY `configuration_group_id` (`configuration_group_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=34 ;

--
-- Dumping data for table `configuration`
--

INSERT INTO `configuration` (`configuration_id`, `configuration_group_id`, `key_name`, `value`, `select_type`) VALUES
(1, 0, 'SEND_EMAILS', 'false', 'true,false'),
(2, 0, 'EMAIL_USE_HTML', 'true', 'true,false'),
(3, 0, 'EMAIL_FROM_ADDRESS', 'orders@realtysignpost.com', 'input'),
(4, 0, 'EMAIL_FROM_NAME', 'Realty SignPost', 'input'),
(5, 0, 'EMAIL_DEFAULT_SUBJECT', 'Email from Realty Signpost', 'input'),
(6, 0, 'EMAIL_TRANSPORT', 'sendmail', 'smtp,sendmail'),
(7, 0, 'SEND_EXTRA_EMAIL', 'true', 'true,false'),
(8, 0, 'SEND_EXTRA_EMAIL_TO', 'orders@realtysignpost.com', 'input'),
(9, 0, 'USE_GZIP', 'true', 'true,false'),
(10, 0, 'MAILER_NAME', 'X-Mailer: RSPC Mailer', 'input'),
(12, 0, 'LOG_PAGE_DATA', 'false', 'true,false'),
(13, 0, 'AUTOMATIC_REMOVAL_TIME', '125', 'input'),
(14, 0, 'TRACK_USERS', 'true', 'true,false'),
(15, 0, 'SEND_CONTACT_EMAILS_TO', 'realtysp@yahoo.com', 'input'),
(16, 1, 'DEFAULT_DEPOSIT_COUNT', '0', 'input'),
(17, 0, 'REQUIRE_NEW_AGENT_DEPOSIT', 'false', 'true,false'),
(18, 0, 'SHOW_PROMO_CODE_AREA', 'false', 'true,false'),
(19, 0, 'REQUIRE_DEPOSIT_AMOUNT', '60', 'input'),
(20, 1, 'MAX_FREE_REMOVAL_TIME', '240', 'input'),
(21, 1, 'EXTENDED_HIRE_MONTHLY_COST', '10', 'input'),
(22, 0, 'INSTALLER_MARK_SCHEDUALED_TIME', '17:00', 'input'),
(23, 0, 'INSTALLER_PAYMENT_SCHEDUALE', '12:00-2|17:00-1', 'input'),
(24, 0, 'DEFAULT_INSTALL_POST_TYPE', '37', 'equipment_type,1'),
(25, 0, 'PASSWORD_REMINDER_DAYS', '270', 'input'),
(26, 0, 'INFO_EMAIL', 'info@realtysignpost.com', 'input'),
(27, 0, 'ADMIN_EMAIL', 'realtysp@yahoo.com', 'input'),
(28, 0, 'USPS_BEGIN', 'You entered:', ''),
(29, 0, 'USPS_END', 'Show ', ''),
(32, 0, 'MISS_UTILITY_DELAY', '3', 'input'),
(33, 0, 'SERVICE_STATS_EMAILS', 'john.pelster@gmail.com', 'input');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
