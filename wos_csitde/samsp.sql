-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Mar 05, 2012 at 04:09 PM
-- Server version: 5.5.8
-- PHP Version: 5.3.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `samsp`
--

-- --------------------------------------------------------

--
-- Table structure for table `access`
--

CREATE TABLE IF NOT EXISTS `access` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `card_id` int(11) NOT NULL,
  `time` datetime NOT NULL,
  `accesstype_id` int(11) NOT NULL,
  `referred_as` varchar(40) NOT NULL,
  `accessstatus_id` int(11) NOT NULL,
  `venue_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=47 ;

--
-- Dumping data for table `access`
--

INSERT INTO `access` (`id`, `card_id`, `time`, `accesstype_id`, `referred_as`, `accessstatus_id`, `venue_id`) VALUES
(21, 1, '2012-02-27 15:51:44', 2, '', 1, 3),
(22, 12, '1970-01-01 01:00:00', 1, '', 4, 1),
(23, 1, '2012-02-27 16:04:03', 1, '', 1, 1),
(24, 1, '2012-02-27 16:10:38', 1, '', 1, 1),
(25, 5, '1970-01-01 01:00:00', 1, '', 4, 1),
(26, 5, '2012-02-27 16:12:00', 1, '', 3, 3),
(27, 5, '2012-02-27 16:13:05', 1, '', 3, 3),
(28, 5, '2012-02-27 16:17:45', 1, '', 3, 3),
(29, 5, '2012-02-27 16:18:18', 1, '', 1, 1),
(30, 1, '2012-02-27 18:30:37', 1, '', 1, 1),
(31, 1, '2012-02-29 17:37:30', 1, '', 3, 1),
(32, 1, '2012-02-29 17:54:02', 1, '', 3, 1),
(33, 1, '2012-03-05 13:30:38', 1, '', 3, 1),
(34, 7, '1970-01-01 01:00:00', 1, '', 4, 1),
(35, 7, '2012-03-05 13:35:15', 1, '', 3, 3),
(36, 7, '2012-03-05 13:38:40', 1, '', 3, 3),
(37, 7, '2012-03-05 13:44:27', 1, '', 3, 3),
(38, 1, '2012-03-05 13:44:40', 1, '', 3, 1),
(39, 2, '1970-01-01 01:00:00', 1, '', 4, 1),
(40, 2, '2012-03-05 13:45:56', 1, '', 1, 2),
(41, 7, '2012-03-05 00:00:00', 1, '', 3, 3),
(42, 7, '2012-03-05 00:00:00', 1, '', 3, 3),
(43, 7, '2012-03-05 00:00:00', 1, '', 3, 3),
(44, 7, '2012-03-05 00:00:00', 1, '', 3, 3),
(45, 7, '2012-03-05 00:00:00', 1, '', 1, 3),
(46, 7, '2012-03-05 13:53:44', 1, '', 1, 3);

-- --------------------------------------------------------

--
-- Table structure for table `accessstatus`
--

CREATE TABLE IF NOT EXISTS `accessstatus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `referred_as` varchar(40) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `accessstatus`
--

INSERT INTO `accessstatus` (`id`, `referred_as`) VALUES
(1, 'Ok'),
(2, 'Denied Cancelled'),
(3, 'Denied Expired'),
(4, 'Denied');

-- --------------------------------------------------------

--
-- Table structure for table `accesstype`
--

CREATE TABLE IF NOT EXISTS `accesstype` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `referred_as` varchar(40) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `accesstype`
--

INSERT INTO `accesstype` (`id`, `referred_as`) VALUES
(1, 'Entry'),
(2, 'Exit');

-- --------------------------------------------------------

--
-- Table structure for table `agency`
--

CREATE TABLE IF NOT EXISTS `agency` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `referred_as` varchar(40) NOT NULL,
  `address` varchar(40) NOT NULL,
  `city` varchar(20) NOT NULL,
  `county` varchar(20) NOT NULL,
  `country` varchar(20) NOT NULL,
  `postcode` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `agency`
--


-- --------------------------------------------------------

--
-- Table structure for table `card`
--

CREATE TABLE IF NOT EXISTS `card` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `referred_as` varchar(40) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `startdate` date NOT NULL,
  `expirydate` date NOT NULL,
  `status_id` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `card`
--

INSERT INTO `card` (`id`, `referred_as`, `staff_id`, `startdate`, `expirydate`, `status_id`) VALUES
(1, '', 1, '2012-02-01', '2012-02-29', 1),
(2, '', 0, '2012-02-21', '2012-03-15', 1),
(3, '', 0, '2012-02-25', '2012-02-29', 1),
(4, '', 0, '2012-02-01', '2012-02-02', 1),
(5, '', 0, '2012-02-21', '2012-02-29', 1),
(6, '', 0, '2012-02-27', '2012-02-29', 2),
(7, '', 0, '2012-03-05', '2012-03-05', 1);

-- --------------------------------------------------------

--
-- Table structure for table `card_venue`
--

CREATE TABLE IF NOT EXISTS `card_venue` (
  `card_id` int(11) NOT NULL,
  `venue_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `card_venue`
--

INSERT INTO `card_venue` (`card_id`, `venue_id`) VALUES
(4, 3),
(4, 2),
(4, 1),
(5, 3),
(5, 2),
(5, 1),
(6, 3),
(6, 2),
(6, 1),
(3, 1),
(7, 3),
(2, 2),
(1, 3),
(1, 2),
(1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

CREATE TABLE IF NOT EXISTS `login` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `referred_as` varchar(40) NOT NULL,
  `password` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `login`
--

INSERT INTO `login` (`id`, `referred_as`, `password`) VALUES
(1, 'frank', '4107c0a4db3fa48dd9aac2701b32b143');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE IF NOT EXISTS `staff` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `referred_as` varchar(40) NOT NULL,
  `agency_id` int(11) NOT NULL,
  `title_id` int(11) NOT NULL,
  `stafftype_id` int(11) NOT NULL,
  `address` varchar(40) NOT NULL,
  `city` varchar(20) NOT NULL,
  `county` varchar(20) NOT NULL,
  `country` varchar(20) NOT NULL,
  `postcode` varchar(8) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`id`, `referred_as`, `agency_id`, `title_id`, `stafftype_id`, `address`, `city`, `county`, `country`, `postcode`) VALUES
(1, 'Frank the Tank', 0, 6, 1, 'ohsdgfjds', 'qohdsfghjdsgfh', 'oihuifdiufda', 'ihdfhjfdjh', 'hjchjs');

-- --------------------------------------------------------

--
-- Table structure for table `stafftype`
--

CREATE TABLE IF NOT EXISTS `stafftype` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `referred_as` varchar(40) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `stafftype`
--

INSERT INTO `stafftype` (`id`, `referred_as`) VALUES
(1, 'In-house'),
(2, 'Volunteer'),
(3, 'Third Party');

-- --------------------------------------------------------

--
-- Table structure for table `status`
--

CREATE TABLE IF NOT EXISTS `status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `referred_as` varchar(40) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `status`
--

INSERT INTO `status` (`id`, `referred_as`) VALUES
(1, 'Valid'),
(2, 'Expired'),
(3, 'Cancelled');

-- --------------------------------------------------------

--
-- Table structure for table `title`
--

CREATE TABLE IF NOT EXISTS `title` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `referred_as` varchar(6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

--
-- Dumping data for table `title`
--

INSERT INTO `title` (`id`, `referred_as`) VALUES
(1, 'Mr'),
(2, 'Mrs'),
(3, 'Miss'),
(4, 'Ms'),
(5, 'Master'),
(6, 'Dr'),
(7, 'Rev'),
(8, 'Prof'),
(9, 'Fr'),
(10, 'Hon'),
(11, 'Gov');

-- --------------------------------------------------------

--
-- Table structure for table `venue`
--

CREATE TABLE IF NOT EXISTS `venue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `referred_as` varchar(40) NOT NULL,
  `address` varchar(40) NOT NULL,
  `city` varchar(20) NOT NULL,
  `county` varchar(20) NOT NULL,
  `country` varchar(20) NOT NULL,
  `postcode` varchar(8) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `venue`
--

INSERT INTO `venue` (`id`, `referred_as`, `address`, `city`, `county`, `country`, `postcode`) VALUES
(1, 'Track', 'blah', 'blah', 'more blah', 'yet more blah', 'blah'),
(2, 'Pool', 'sdfsd', 'ugghu', 'hghg', 'hghj', 'hhhj'),
(3, 'Pitch1', '123', 'London', 'Middlesex', 'England', 'LO12ON');
