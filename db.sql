-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 15, 2019 at 10:51 PM
-- Server version: 10.1.38-MariaDB-0ubuntu0.18.04.1
-- PHP Version: 7.2.15-0ubuntu0.18.04.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sven_briefwahl`
--

-- --------------------------------------------------------

--
-- Table structure for table `municipalities_core`
--

CREATE TABLE `municipalities_core` (
  `key` varchar(20) NOT NULL,
  `parent_key` varchar(20) NULL,
  `name` text NOT NULL,
  `county` text NOT NULL,
  `state` enum('Baden-Württemberg','Bayern','Berlin','Brandenburg','Bremen','Hamburg','Hessen','Mecklenburg-Vorpommern','Niedersachsen','Nordrhein-Westfalen','Rheinland-Pfalz','Saarland','Sachsen','Sachsen-Anhalt','Schleswig-Holstein','Thüringen','') NOT NULL,
  `district` text NOT NULL,
  `type` enum('Markt','Kreisfreie Stadt','Stadtkreis','Stadt','Kreisangehörige Gemeinde','gemeindefreies Gebiet, bewohnt','gemeindefreies Gebiet, unbewohnt','große Kreisstadt','Landkreis','Kreis') NOT NULL,
  `type_code` int(2) NOT NULL,
  `type_category` int(2) NOT NULL,
  `population` int(11) NULL,
  `population_male` int(11) NULL,
  `population_female` int(11) NULL,
  `longitude` double NOT NULL,
  `latitude` double NOT NULL,
  `area` double NOT NULL,
  `address_recipient` text NULL DEFAULT NULL,
  `address_street` text NULL DEFAULT NULL,
  `address_zip` text NULL DEFAULT NULL,
  `address_city` text NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `web_info_crawler`
--

CREATE TABLE `web_info_crawler` (
  `key` varchar(20) NOT NULL,
  `email_default` text NULL DEFAULT NULL,
  `website_default` text NULL DEFAULT NULL,
  `email_poll` text NULL DEFAULT NULL,
  `website_poll` text NULL DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `web_info_human`
--

CREATE TABLE `web_info_human` (
  `key` varchar(20) NOT NULL,
  `email_default` text NULL DEFAULT NULL,
  `website_default` text NULL DEFAULT NULL,
  `email_poll` text NULL DEFAULT NULL,
  `website_poll` text NULL DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `polling_station_crawler`
--

CREATE TABLE `polling_station_crawler` (
  `key` varchar(20) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `name` text NULL DEFAULT NULL,
  `address_street` text NULL DEFAULT NULL,
  `address_zip` text NULL DEFAULT NULL,
  `address_city` text NULL DEFAULT NULL,
  `opening_hours` text NULL DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `polling_station_human`
--

CREATE TABLE `polling_station_human` (
  `key` varchar(20) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `name` text NULL DEFAULT NULL,
  `address_street` text NULL DEFAULT NULL,
  `address_zip` text NULL DEFAULT NULL,
  `address_city` text NULL DEFAULT NULL,
  `opening_hours` text NULL DEFAULT NULL,
  `valid` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `polling_station_web_queue`
--

CREATE TABLE `polling_station_web_queue` (
  `key` varchar(20) NOT NULL,
  `slug` text,
  `name` text NULL DEFAULT NULL,
  `address_street` text NULL DEFAULT NULL,
  `address_zip` text NULL DEFAULT NULL,
  `address_city` text NULL DEFAULT NULL,
  `opening_hours` text NULL DEFAULT NULL,
  `email_default` text NULL DEFAULT NULL,
  `website_default` text NULL DEFAULT NULL,
  `email_poll` text NULL DEFAULT NULL,
  `website_poll` text NULL DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `zip_codes`
--

CREATE TABLE `zip_codes` (
  `municipality_key` varchar(20) NOT NULL,
  `zip` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `municipalities_core`
--
ALTER TABLE `municipalities_core`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `municipalities_crawler`
--
ALTER TABLE `web_info_crawler`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `municipalities_human`
--
ALTER TABLE `web_info_human`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `polling_station_crawler`
--
ALTER TABLE `polling_station_crawler`
  ADD PRIMARY KEY (`key`,`slug`);

--
-- Indexes for table `polling_station_human`
--
ALTER TABLE `polling_station_human`
  ADD PRIMARY KEY (`key`,`slug`);

--
-- Indexes for table `zip_codes`
--
ALTER TABLE `zip_codes`
  ADD PRIMARY KEY (`municipality_key`, `zip`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `web_info_crawler`
--
ALTER TABLE `web_info_crawler`
  ADD CONSTRAINT `web_info_crawler_key` FOREIGN KEY (`key`) REFERENCES `municipalities_core` (`key`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `web_info_human`
--
ALTER TABLE `web_info_human`
  ADD CONSTRAINT `web_info_human_key` FOREIGN KEY (`key`) REFERENCES `municipalities_core` (`key`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `polling_station_crawler`
--
ALTER TABLE `polling_station_crawler`
  ADD CONSTRAINT `polling_station_crawler_key` FOREIGN KEY (`key`) REFERENCES `municipalities_core` (`key`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `polling_station_human`
--
ALTER TABLE `polling_station_human`
  ADD CONSTRAINT `polling_station_human_key` FOREIGN KEY (`key`) REFERENCES `municipalities_core` (`key`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `zip_codes`
--
ALTER TABLE `zip_codes`
  ADD CONSTRAINT `zip_codes_key` FOREIGN KEY (`municipality_key`) REFERENCES `municipalities_core` (`key`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `polling_station_web_queue`
--
ALTER TABLE `polling_station_web_queue`
  ADD CONSTRAINT `polling_station_web_queue_key` FOREIGN KEY (`key`) REFERENCES `municipalities_core` (`key`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

CREATE VIEW web_info AS
  SELECT `key`, `email_default`, `website_default`, `email_poll`, `website_poll` FROM web_info_human
  UNION
  SELECT `key`, `email_default`, `website_default`, `email_poll`, `website_poll` FROM web_info_crawler WHERE `key` NOT IN (SELECT `key` FROM web_info_human);

CREATE VIEW municipalities AS SELECT c.*, w.`email_default`, w.`website_default`, w.`email_poll`, w.`website_poll` FROM municipalities_core c LEFT JOIN web_info w ON c.`key`=w.`key`;

CREATE VIEW polling_stations AS
  SELECT `key`, `slug`, `name`, `address_street`, `address_zip`, `address_city`, `opening_hours` FROM polling_station_human
    WHERE `valid`=1
  UNION
  SELECT `key`, `slug`, `name`, `address_street`, `address_zip`, `address_city`, `opening_hours` FROM polling_station_crawler WHERE
    (`key`, `slug`) NOT IN (SELECT `key`, `slug` FROM polling_station_human WHERE `valid`=1);
