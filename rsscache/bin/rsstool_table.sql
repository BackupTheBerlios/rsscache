-- phpMyAdmin SQL Dump
-- version 3.3.8.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 12, 2011 at 09:20 PM
-- Server version: 5.0.91
-- PHP Version: 5.2.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `pwnoogle_emulive`
--

-- --------------------------------------------------------

--
-- Table structure for table `rsstool_table`
--

CREATE TABLE IF NOT EXISTS `rsstool_table` (
  `rsstool_url_md5` varchar(32) NOT NULL default '',
  `rsstool_url_crc32` int(10) unsigned NOT NULL default '0',
  `rsstool_site` varchar(255) NOT NULL,
  `rsstool_dl_url` varchar(255) NOT NULL,
  `rsstool_dl_url_md5` varchar(32) NOT NULL default '',
  `rsstool_dl_url_crc32` int(10) unsigned NOT NULL default '0',
  `rsstool_title` varchar(255) NOT NULL,
  `rsstool_title_md5` varchar(32) NOT NULL default '',
  `rsstool_title_crc32` int(10) unsigned NOT NULL default '0',
  `rsstool_url` varchar(255) NOT NULL,
  `rsstool_desc` text NOT NULL,
  `rsstool_date` bigint(20) unsigned NOT NULL default '0',
  `rsstool_dl_date` bigint(20) unsigned NOT NULL default '0',
  `rsstool_keywords` varchar(512) NOT NULL,
  `rsstool_related_id` int(10) unsigned NOT NULL,
  `rsstool_media_duration` mediumint(8) unsigned NOT NULL default '0',
  `rsstool_image` text NOT NULL,
  `tv2_category` varchar(32) NOT NULL,
  `tv2_moved` varchar(32) NOT NULL,
  `rsstool_event_start` bigint(20) NOT NULL,
  `rsstool_event_end` bigint(20) NOT NULL,
  `tv2_active` enum('1','0') NOT NULL default '1',
  `tv2_votes` int(10) unsigned NOT NULL default '0',
  `tv2_score` float unsigned NOT NULL default '0',
  PRIMARY KEY  (`rsstool_url_crc32`),
  KEY `tv2_moved` (`tv2_moved`),
  KEY `rsstool_date` (`rsstool_date`),
  KEY `tv2_score` (`tv2_score`),
  KEY `tv2_votes` (`tv2_votes`),
  KEY `rsstool_media_duration` (`rsstool_media_duration`),
  KEY `rsstool_dl_date` (`rsstool_dl_date`),
  KEY `tv2_active` (`tv2_active`),
  KEY `tv2_moved_2` (`tv2_moved`,`rsstool_media_duration`),
  KEY `tv2_moved_3` (`tv2_moved`,`rsstool_date`),
  KEY `tv2_moved_4` (`tv2_moved`,`rsstool_dl_date`),
  KEY `tv2_moved_5` (`tv2_moved`,`rsstool_media_duration`,`rsstool_date`),
  KEY `tv2_moved_6` (`tv2_moved`,`rsstool_media_duration`,`rsstool_dl_date`),
  KEY `rsstool_media_duration_2` (`rsstool_media_duration`,`rsstool_date`),
  KEY `rsstool_media_duration_3` (`rsstool_media_duration`,`rsstool_dl_date`),
  KEY `rsstool_related_id` (`rsstool_related_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
