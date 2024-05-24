-- phpMyAdmin SQL Dump
-- version 3.4.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 07, 2012 at 09:48 AM
-- Server version: 5.5.16
-- PHP Version: 5.3.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `surewaves_easyro`
--

-- --------------------------------------------------------

--
-- Table structure for table `ro_admin`
--

CREATE TABLE IF NOT EXISTS `ro_admin` (
  `admin_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `pass` varchar(45) NOT NULL,
  PRIMARY KEY (`admin_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `ro_admin`
--

INSERT INTO `ro_admin` (`admin_id`, `email`, `pass`) VALUES
(4, 'admin@surewaves.com', 'sadmin123');

-- --------------------------------------------------------

--
-- Table structure for table `ro_email_copy`
--

CREATE TABLE IF NOT EXISTS `ro_email_copy` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `to_email` varchar(30) DEFAULT NULL,
  `cc_email` varchar(30) DEFAULT NULL,
  `subject` text,
  `message` text,
  `date_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4040 ;

-- --------------------------------------------------------

--
-- Table structure for table `ro_ext_ro`
--

CREATE TABLE IF NOT EXISTS `ro_ext_ro` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ref_order_id` int(11) NOT NULL,
  `ro_description` varchar(100) DEFAULT NULL,
  `internal_RO_no` varchar(50) NOT NULL,
  `total_cost` int(11) NOT NULL,
  `activity_start_date` datetime DEFAULT NULL,
  `activity_end_date` datetime DEFAULT NULL,
  `ro_date` datetime DEFAULT NULL,
  `ext_ro_number` varchar(50) DEFAULT NULL,
  `ro_instructions` text,
  PRIMARY KEY (`id`),
  KEY `ref_order_id` (`ref_order_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=137 ;

--
-- Dumping data for table `ro_ext_ro`
--

INSERT INTO `ro_ext_ro` (`id`, `ref_order_id`, `ro_description`, `internal_RO_no`, `total_cost`, `activity_start_date`, `activity_end_date`, `ro_date`, `ext_ro_number`, `ro_instructions`) VALUES
(133, 136, NULL, 'SW/c/ab/Sep-2012-1', 0, NULL, NULL, '0000-00-00 00:00:00', 'aaa', NULL),
(134, 137, NULL, 'SW/Default/a/Sep-2012-1', 0, NULL, NULL, '0000-00-00 00:00:00', 'abc111', NULL),
(135, 138, NULL, 'SW/abc/a/Sep-2012-1', 0, NULL, NULL, '0000-00-00 00:00:00', 'abc111', NULL),
(136, 139, NULL, 'SW/abc/a/Sep-2012-1', 0, NULL, NULL, '2012-09-04 00:00:00', 'abc111', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ro_order`
--

CREATE TABLE IF NOT EXISTS `ro_order` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `internal_RO_no` varchar(50) NOT NULL,
  `contact_email` varchar(100) DEFAULT NULL,
  `contact_phone` varchar(20) DEFAULT NULL,
  `client_name` varchar(45) DEFAULT NULL,
  `agency_name` varchar(45) DEFAULT NULL,
  `campaign_name` varchar(85) DEFAULT NULL,
  `brand_name` varchar(45) DEFAULT NULL,
  `brand_owner` varchar(45) DEFAULT NULL,
  `product` varchar(45) DEFAULT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `approval_requested` tinyint(1) unsigned DEFAULT '0',
  `fill_completed` tinyint(1) unsigned DEFAULT '0',
  `approval_req_datetime` datetime DEFAULT NULL,
  `campaign_start_date` datetime DEFAULT NULL,
  `campaign_end_date` datetime DEFAULT NULL,
  `creation_datetime` datetime DEFAULT NULL,
  `ref_ro_id` int(11) DEFAULT NULL,
  `special_instructions` text,
  PRIMARY KEY (`order_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=140 ;

--
-- Dumping data for table `ro_order`
--

INSERT INTO `ro_order` (`order_id`, `user_id`, `internal_RO_no`, `contact_email`, `contact_phone`, `client_name`, `agency_name`, `campaign_name`, `brand_name`, `brand_owner`, `product`, `status`, `approval_requested`, `fill_completed`, `approval_req_datetime`, `campaign_start_date`, `campaign_end_date`, `creation_datetime`, `ref_ro_id`, `special_instructions`) VALUES
(133, 135, 'SW/abc/a/Sep-2012-1', '0', '', 'abc', 'a', 'test', '', NULL, '', 0, 0, 0, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2012-09-06 17:04:55', NULL, ''),
(134, 135, 'SW/abc/a/Sep-2012-1', '0', '', 'abc', 'a', 'test', '', NULL, '', 0, 0, 0, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2012-09-06 17:07:51', NULL, ''),
(135, 135, 'SW/abc/a/Sep-2012-1', '0', '', 'abc', 'a', 'test', '', NULL, '', 0, 0, 0, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2012-09-06 17:08:46', NULL, ''),
(136, 135, 'SW/c/ab/Sep-2012-1', '0', '', 'c', 'ab', 'aa', '', NULL, '', 1, 0, 0, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2012-09-06 17:16:31', NULL, ''),
(137, 135, 'SW/Default/a/Sep-2012-1', '0', '', 'Default', 'a', 'TestCamapign', '', NULL, '', 1, 0, 0, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2012-09-06 18:00:33', NULL, ''),
(138, 135, 'SW/abc/a/Sep-2012-1', '0', '', 'abc', 'a', 'test', '', NULL, '', 1, 0, 0, NULL, '1970-01-01 05:30:00', '1970-01-01 05:30:00', '2012-09-06 18:03:12', NULL, ''),
(139, 135, 'SW/abc/a/Sep-2012-1', '0', '', 'abc', 'a', 'test', '', NULL, '', 1, 0, 0, NULL, '2012-09-12 00:00:00', '2012-09-20 00:00:00', '2012-09-06 18:08:22', NULL, '');

-- --------------------------------------------------------

--
-- Table structure for table `ro_user`
--

CREATE TABLE IF NOT EXISTS `ro_user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `agency_id` int(11) NOT NULL DEFAULT '1',
  `user_email` varchar(100) NOT NULL,
  `user_phone` varchar(20) DEFAULT NULL,
  `user_name` varchar(45) NOT NULL,
  `user_password` varchar(45) NOT NULL,
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `plain_text_password` varchar(45) DEFAULT NULL,
  `profile_id` int(11) NOT NULL,
  `creation_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  KEY `profile_id` (`profile_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=139 ;

--
-- Dumping data for table `ro_user`
--

INSERT INTO `ro_user` (`user_id`, `agency_id`, `user_email`, `user_phone`, `user_name`, `user_password`, `active`, `plain_text_password`, `profile_id`, `creation_datetime`) VALUES
(135, 2, 'kiran@surewaves.com', '9999999990', 'Kiran', '6a056c8edf28cd1177f892d8046ea5b0', 1, 'surewaves', 1, '2012-08-30 17:51:22'),
(136, 2, 'kiran1@surewaves.com', '9999999990', 'Akshay', 'dd379fb4c2e79db5e5f3f57d068cbc12', 1, '9999999990', 1, '2012-08-30 17:52:05'),
(137, 2, 'kiran11@surewaves.com', '8910191011', 'test', 'f67ba3d2b1948cd0918878a57cbdbebb', 1, '8910191011', 1, '2012-08-30 17:54:37'),
(138, 2, 'ops@gmail.com', '9999099991', 'KiranOPs', '075dcbcc5cf757dde35dcc506eb7eea2', 1, 'opshead1', 4, '2012-08-31 10:21:30');

-- --------------------------------------------------------

--
-- Table structure for table `ro_user_audit_trail`
--

CREATE TABLE IF NOT EXISTS `ro_user_audit_trail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message` varchar(400) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1639 ;

-- --------------------------------------------------------

--
-- Table structure for table `ro_user_profile`
--

CREATE TABLE IF NOT EXISTS `ro_user_profile` (
  `profile_id` int(11) NOT NULL AUTO_INCREMENT,
  `profile_name` varchar(40) DEFAULT NULL,
  `profile_desc` varchar(300) DEFAULT NULL,
  PRIMARY KEY (`profile_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `ro_user_profile`
--

INSERT INTO `ro_user_profile` (`profile_id`, `profile_name`, `profile_desc`) VALUES
(1, 'Business Head', 'Allow to perform all actions'),
(2, 'COO', 'Approve and view all properties'),
(3, 'Scheduler', 'Scheduling User'),
(4, 'Operations', 'View Campaign except Pricing Information'),
(5, 'Accounts Executive', 'Can view all properties and Pricing Information');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ro_ext_ro`
--
ALTER TABLE `ro_ext_ro`
  ADD CONSTRAINT `ro_ext_ro_ibfk_1` FOREIGN KEY (`ref_order_id`) REFERENCES `ro_order` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ro_order`
--
ALTER TABLE `ro_order`
  ADD CONSTRAINT `ro_order_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `ro_user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ro_user`
--
ALTER TABLE `ro_user`
  ADD CONSTRAINT `ro_user_ibfk_1` FOREIGN KEY (`profile_id`) REFERENCES `ro_user_profile` (`profile_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ro_user_audit_trail`
--
ALTER TABLE `ro_user_audit_trail`
  ADD CONSTRAINT `ro_user_audit_trail_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `ro_user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

  
 CREATE TABLE `sv_production`.`ro_amount_confirmation` (
  `confirmation_id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` VARCHAR(75) NOT NULL,
  `apa` TEXT,
  `post_channel_avg_rate` TEXT,
  `post_channel_amount` TEXT,
  `hid_total_seconds` TEXT,
  `hid_client_name` TEXT,
  `hid_internal_ro` TEXT,
  `hid_cust_id` TEXT,
  `total_rows` TEXT,
  `post_network_share` TEXT,
  `post_final_amount` TEXT,
  PRIMARY KEY (`confirmation_id`)
)

CREATE TABLE IF NOT EXISTS `ro_external_ro_report_details` (
  `customer_ro_number` varchar(100) CHARACTER SET latin1 NOT NULL,
  `internal_ro_number` varchar(100) CHARACTER SET latin1 NOT NULL,
  `client_name` varchar(100) CHARACTER SET latin1 NOT NULL,
  `agency_name` varchar(100) CHARACTER SET latin1 NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `gross_ro_amount` decimal(40,2) unsigned NOT NULL,
  `agency_commission_amount` decimal(40,2) unsigned NOT NULL,
  `agency_rebate` decimal(40,2) unsigned NOT NULL,
  `other_expenses` decimal(40,2) unsigned NOT NULL,
  `total_seconds_scheduled` decimal(40,2) unsigned NOT NULL,
  `total_network_payout` decimal(40,2) unsigned NOT NULL,
  `net_contribution_amount` decimal(40,2) unsigned NOT NULL,
  `net_contribution_amount_per` float(10,2) unsigned NOT NULL,
  `net_revenue` decimal(40,2) unsigned DEFAULT NULL,
  PRIMARY KEY (`customer_ro_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ro_network_ro_report_details`
--

CREATE TABLE IF NOT EXISTS `ro_network_ro_report_details` (
  `customer_ro_number` varchar(100) CHARACTER SET latin1 NOT NULL,
  `internal_ro_number` varchar(100) CHARACTER SET latin1 NOT NULL,
  `network_ro_number` varchar(100) CHARACTER SET latin1 NOT NULL,
  `customer_name` varchar(100) CHARACTER SET latin1 NOT NULL,
  `client_name` varchar(100) CHARACTER SET latin1 NOT NULL,
  `agency_name` varchar(100) CHARACTER SET latin1 NOT NULL,
  `market` varchar(100) CHARACTER SET latin1 NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `activity_months` varchar(256) CHARACTER SET latin1 NOT NULL,
  `gross_network_ro_amount` decimal(45,0) unsigned NOT NULL,
  `customer_share` float unsigned NOT NULL,
  `net_amount_payable` decimal(45,0) unsigned NOT NULL,
  `release_date` datetime NOT NULL,
  PRIMARY KEY (`network_ro_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `ro_amount_confirmation` (
  `confirmation_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` varchar(255) NOT NULL,
  `apa` text,
  `post_channel_avg_rate` text,
  `post_channel_amount` text,
  `hid_total_seconds` text,
  `hid_client_name` text,
  `hid_internal_ro` text,
  `hid_cust_id` text,
  `total_rows` text,
  `post_network_share` text,
  `post_final_amount` text,
  PRIMARY KEY (`confirmation_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

ALTER TABLE `sv_customer` ADD `billing_name` VARCHAR( 255 ) NULL DEFAULT NULL ;
ALTER TABLE `ro_approved_networks` ADD `billing_name` VARCHAR( 255 ) NULL DEFAULT NULL ;
ALTER TABLE `ro_external_ro_report_details` ADD `net_revenue` DECIMAL( 40, 2 ) NULL DEFAULT NULL;


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
