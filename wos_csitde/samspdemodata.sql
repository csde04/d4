-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Mar 05, 2012 at 06:39 PM
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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `access`
--

INSERT INTO `access` (`id`, `card_id`, `time`, `accesstype_id`, `referred_as`, `accessstatus_id`, `venue_id`) VALUES
(1, 6, '2012-03-05 09:33:29', 1, '', 1, 4),
(2, 6, '2012-03-05 13:21:36', 2, '', 1, 4),
(3, 7, '2012-03-05 09:12:18', 1, '', 1, 1),
(4, 7, '2012-03-05 14:34:25', 2, '', 1, 1);

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
  `description` varchar(40) NOT NULL,
  `address` varchar(40) NOT NULL,
  `city` varchar(20) NOT NULL,
  `county` varchar(20) NOT NULL,
  `country` varchar(20) NOT NULL,
  `postcode` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `agency`
--

INSERT INTO `agency` (`id`, `referred_as`, `description`, `address`, `city`, `county`, `country`, `postcode`) VALUES
(1, 'Brenny Cola', 'Catering', '1 Cola Lane', 'York', 'Yorkshire', 'England', 'YK6 8LG'),
(2, 'Micro Zoft', 'Electronics', '123 Soft Street', 'Hatfield', 'Hertfordshire', 'England', 'AL1 7HY'),
(3, 'Food Derivatives', 'Catering', '321 Hungryville', 'Paris', '', 'France', 'PA5 1IS'),
(4, 'Zibadir', 'Pharmiceutical and Medical', '99 Medicine Lane', 'Swansea', '', 'Wales', 'SW4 5EA'),
(5, 'Muscletone', 'Gym and Athletics Furniture', '432 Excersice Close', 'Berlin', '', 'Germany', 'BE6 1IN'),
(6, 'Uframed', 'Electronics', '76 Uframed Close', 'London', 'Greater London', 'England', 'LO12ON');

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
(1, '', 1, '2012-07-25', '2012-07-25', 1),
(2, '', 2, '2012-07-25', '2012-07-30', 1),
(3, '', 3, '2012-07-25', '2012-07-30', 1),
(4, '', 4, '2012-07-26', '2012-07-28', 1),
(5, '', 5, '2012-07-26', '2012-07-28', 1),
(6, '', 2, '2012-02-28', '2012-02-28', 2),
(7, '', 3, '2012-02-27', '2012-02-27', 2);

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
(1, 1),
(1, 2),
(1, 3),
(2, 1),
(2, 2),
(2, 3),
(2, 4),
(2, 5),
(3, 1),
(3, 2),
(3, 3),
(3, 4),
(3, 5),
(4, 9),
(4, 6),
(5, 9),
(5, 6),
(7, 1),
(6, 4);

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
(1, 'auser', '5f4dcc3b5aa765d61d8327deb882cf99');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE IF NOT EXISTS `staff` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `referred_as` varchar(40) NOT NULL,
  `role` varchar(40) NOT NULL,
  `agency_id` int(11) NOT NULL,
  `title_id` int(11) NOT NULL,
  `stafftype_id` int(11) NOT NULL,
  `address` varchar(40) NOT NULL,
  `city` varchar(20) NOT NULL,
  `county` varchar(20) NOT NULL,
  `country` varchar(20) NOT NULL,
  `postcode` varchar(8) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`id`, `referred_as`, `role`, `agency_id`, `title_id`, `stafftype_id`, `address`, `city`, `county`, `country`, `postcode`) VALUES
(1, 'John Johnson', 'Deliverer', 1, 1, 3, '1 Johnson Street', 'Johnsonville', 'JohnShire', 'France', 'JO65HN'),
(2, 'Luise Luisselle', 'IT - Manager', 2, 4, 3, '12 Luise Gardens', 'Hull', 'Yorkshire', 'England', 'HU11IS'),
(3, 'Gary Garynard', 'IT - Manager''s Assistant', 2, 1, 3, '123 Gary Range', 'Hull', 'Yorkshire', '', 'HU11IS'),
(4, 'Tony Vivaro', 'Deliverer', 3, 1, 3, '12 Van Drive', 'Stoke', 'Staffordshire', 'England', 'OR12CS'),
(5, 'Maria Master', 'Chef', 3, 4, 3, '65 Askchef Court', 'Stoke', 'Staffordshire', 'England', 'OR43DS');

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `venue`
--

INSERT INTO `venue` (`id`, `referred_as`, `address`, `city`, `county`, `country`, `postcode`) VALUES
(1, 'Judo Court 1', '12 Olympic Lane', 'London', '', 'England', 'LO12ON'),
(2, 'Judo Court 2', '12 Olympic Lane', 'London', '', 'England', 'LO12ON'),
(3, 'Judo Court 3', '14 Olympic Lane', 'London', '', 'England', 'LO12ON'),
(4, 'Tennis Court 1', '7 Olympic Lane', 'London', '', 'England', 'LO12ON'),
(5, 'Tennis Court 2', '8 Olympic Lane', 'London', '', 'England', 'LO12ON'),
(6, 'Football Stadium', 'Wembley', 'London', '', 'England', 'WE36EY'),
(7, 'Swimming Pool 1', '1 Olympic Lane', 'London', '', 'England', 'LO12ON'),
(8, 'Swimming Pool 2', '100 Olympic Lane', 'London', '', 'England', 'LO12ON'),
(9, 'Athletics Stadium', '2 Olympic Lane', 'London', '', 'England', 'LO12ON');
