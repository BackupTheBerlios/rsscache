-- phpMyAdmin SQL Dump
-- version 3.3.8.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 12, 2011 at 09:21 PM
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
-- Table structure for table `keyword_table`
--

CREATE TABLE IF NOT EXISTS `keyword_table` (
  `rsstool_url_md5` varchar(32) NOT NULL,
  `rsstool_url_crc32` int(10) unsigned NOT NULL,
  `rsstool_keyword_crc32` int(10) unsigned NOT NULL,
  `rsstool_keyword_crc24` int(10) unsigned NOT NULL,
  `rsstool_keyword_crc16` smallint(5) unsigned NOT NULL,
  PRIMARY KEY  (`rsstool_url_crc32`,`rsstool_keyword_crc16`),
  KEY `rsstool_keyword_20bit` (`rsstool_keyword_crc24`),
  KEY `rsstool_keyword_16bit` (`rsstool_keyword_crc16`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
