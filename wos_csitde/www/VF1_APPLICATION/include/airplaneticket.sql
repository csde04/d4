-- phpMyAdmin SQL Dump
-- version 2.11.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 26, 2011 at 08:36 AM
-- Server version: 5.0.67
-- PHP Version: 5.2.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `airplaneticket`
--

-- --------------------------------------------------------

--
-- Table structure for table `airline`
--

CREATE TABLE IF NOT EXISTS `airline` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(40) default NULL,
  `referred_as` varchar(40) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `airline`
--

INSERT INTO `airline` (`id`, `name`, `referred_as`) VALUES
(1, 'Ryanair', 'FR'),
(2, 'Alitalia', 'AZ'),
(3, 'KLM', 'KL'),
(4, 'British Airways', 'BA');

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE IF NOT EXISTS `customer` (
  `id` int(11) NOT NULL auto_increment,
  `title_id` int(11) NOT NULL,
  `name` varchar(40) NOT NULL,
  `surname` varchar(40) NOT NULL,
  `dob` date default NULL,
  `address` varchar(40) default NULL,
  `town` varchar(40) default NULL,
  `country` varchar(40) default NULL,
  `postcode` varchar(40) default NULL,
  `document_id` int(11) NOT NULL,
  `document_no` varchar(40) NOT NULL,
  `referred_as` varchar(40) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`id`, `title_id`, `name`, `surname`, `dob`, `address`, `town`, `country`, `postcode`, `document_id`, `document_no`, `referred_as`) VALUES
(1, 1, 'John', 'Smith', '1959-03-30', '', '', '', '', 1, '5345342', 'Smith John'),
(2, 2, 'Helen', 'Galli', '1918-11-22', '', '', '', '', 2, '32543A32', 'Galli Helen');

-- --------------------------------------------------------

--
-- Table structure for table `document`
--

CREATE TABLE IF NOT EXISTS `document` (
  `id` int(11) NOT NULL auto_increment,
  `referred_as` varchar(40) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `document`
--

INSERT INTO `document` (`id`, `referred_as`) VALUES
(1, 'passport'),
(2, 'identity card');

-- --------------------------------------------------------

--
-- Table structure for table `facility`
--

CREATE TABLE IF NOT EXISTS `facility` (
  `id` int(11) NOT NULL auto_increment,
  `referred_as` varchar(40) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `facility`
--

INSERT INTO `facility` (`id`, `referred_as`) VALUES
(1, 'hotel'),
(2, 'restaurant'),
(3, 'hospital'),
(4, 'shopping hall'),
(5, 'sea resort'),
(6, 'golf pitch');

-- --------------------------------------------------------

--
-- Table structure for table `facility_location`
--

CREATE TABLE IF NOT EXISTS `facility_location` (
  `facility_id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `facility_location`
--

INSERT INTO `facility_location` (`facility_id`, `location_id`) VALUES
(2, 2),
(5, 2),
(4, 2),
(3, 5),
(2, 5),
(1, 8),
(2, 8),
(4, 8),
(3, 9),
(1, 9),
(2, 9),
(4, 9),
(5, 4),
(2, 7),
(5, 7),
(6, 13),
(1, 13),
(2, 13),
(6, 12),
(3, 12),
(5, 12),
(4, 12);

-- --------------------------------------------------------

--
-- Table structure for table `flight`
--

CREATE TABLE IF NOT EXISTS `flight` (
  `id` int(11) NOT NULL auto_increment,
  `code` varchar(40) NOT NULL,
  `airline_id` int(11) NOT NULL,
  `from_location_id` int(11) NOT NULL,
  `to_location_id` int(11) NOT NULL,
  `referred_as` varchar(40) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `flight`
--

INSERT INTO `flight` (`id`, `code`, `airline_id`, `from_location_id`, `to_location_id`, `referred_as`) VALUES
(1, 'FR 3423', 1, 1, 2, 'FR 3423'),
(2, 'AZ 4323', 2, 3, 2, 'AZ 4323'),
(3, 'FR 5331', 1, 3, 2, 'FR 5331'),
(4, 'FR 6443', 1, 1, 3, 'FR 6443'),
(5, 'AZ 4111', 2, 2, 4, 'AZ 4111'),
(6, '7766', 3, 3, 10, 'KL 7766');

-- --------------------------------------------------------

--
-- Table structure for table `location`
--

CREATE TABLE IF NOT EXISTS `location` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(40) NOT NULL,
  `country` varchar(40) default NULL,
  `airport` varchar(40) default NULL,
  `referred_as` varchar(40) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;

--
-- Dumping data for table `location`
--

INSERT INTO `location` (`id`, `name`, `country`, `airport`, `referred_as`) VALUES
(1, 'London', 'UK', 'Stansted', 'London STN'),
(2, 'Rome', 'Italy', 'Fiumicino', 'Rome FCO'),
(3, 'Paris', 'France', 'Charles De Gaulle', 'Paris XDT'),
(4, 'London', 'UK', 'Heathrow', 'London LHR'),
(5, 'Madrid', 'Spain', 'Barajas', 'Madrid MAD'),
(6, 'Barcelona', 'Spain', 'El Prat', 'Barcelona BCN'),
(7, 'New York', 'USA', 'JFK', 'New York JFK'),
(8, 'New York', 'USA', 'LaGuardia', 'New York LGA'),
(9, 'New York', 'USA', 'Newark', 'New York EWR'),
(10, 'Beijing', 'China', 'Beijing Capital Int.', 'Beijing PEK'),
(11, 'Dubai', 'UAE', 'Dubai International', 'Dubai DXB'),
(12, 'Abuja', 'Nigeria', 'Nnamdi Azikiwe International', 'Abuja ABV'),
(13, 'Amsterdam', 'Netherlands', 'Schiphol', 'Amsterdam AMS');

-- --------------------------------------------------------

--
-- Table structure for table `status`
--

CREATE TABLE IF NOT EXISTS `status` (
  `id` int(11) NOT NULL auto_increment,
  `referred_as` varchar(40) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `status`
--

INSERT INTO `status` (`id`, `referred_as`) VALUES
(1, 'booked'),
(2, 'cancelled'),
(3, 'checked-in'),
(4, 'expired');

-- --------------------------------------------------------

--
-- Table structure for table `ticket`
--

CREATE TABLE IF NOT EXISTS `ticket` (
  `id` int(11) NOT NULL auto_increment,
  `date` date NOT NULL,
  `flight_id` int(11) NOT NULL,
  `status_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `passengers_no` int(11) NOT NULL,
  `referred_as` varchar(40) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `ticket`
--

INSERT INTO `ticket` (`id`, `date`, `flight_id`, `status_id`, `customer_id`, `passengers_no`, `referred_as`) VALUES
(1, '2011-01-23', 1, 1, 2, 6, ''),
(2, '2011-01-19', 1, 1, 1, 4, ''),
(3, '2011-01-21', 2, 1, 2, 3, ''),
(4, '2011-01-22', 5, 2, 2, 1, ''),
(5, '2011-01-19', 6, 1, 1, 2, '');

-- --------------------------------------------------------

--
-- Table structure for table `title`
--

CREATE TABLE IF NOT EXISTS `title` (
  `id` int(11) NOT NULL auto_increment,
  `referred_as` varchar(40) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `title`
--

INSERT INTO `title` (`id`, `referred_as`) VALUES
(1, 'Mr'),
(2, 'Mrs'),
(3, 'Ms'),
(4, 'Dr');
