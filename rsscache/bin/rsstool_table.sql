-- phpMyAdmin SQL Dump
-- version 3.1.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 15, 2009 at 12:16 AM
-- Server version: 5.0.32
-- PHP Version: 5.2.0-8+etch13

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `jack`
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
  `rsstool_media_duration` mediumint(8) unsigned NOT NULL default '0',
  `tv2_category` varchar(32) NOT NULL,
  `tv2_moved` varchar(32) NOT NULL,
  `tv2_duration` mediumint(8) unsigned NOT NULL default '0',
  `tv2_active` enum('1','0') NOT NULL default '1',
  `tv2_keywords` varchar(512) NOT NULL,
  `tv2_votes` int(10) unsigned NOT NULL default '0',
  `tv2_score` float unsigned NOT NULL default '0',
  UNIQUE KEY `rsstool_url_crc32` (`rsstool_url_crc32`),
  KEY `tv2_duration` (`tv2_duration`),
  KEY `tv2_moved` (`tv2_moved`),
  KEY `tv2_score` (`tv2_score`),
  FULLTEXT KEY `tv2_keywords` (`tv2_keywords`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
