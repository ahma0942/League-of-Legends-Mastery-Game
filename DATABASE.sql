-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: May 10, 2016 at 01:42 PM
-- Server version: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `league`
--

-- --------------------------------------------------------

--
-- Table structure for table `answers`
--

CREATE TABLE IF NOT EXISTS `answers` (
  `aid` int(11) NOT NULL AUTO_INCREMENT,
  `gid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `a1` int(2) NOT NULL DEFAULT '0',
  `a2` int(2) NOT NULL DEFAULT '0',
  `a3` int(2) NOT NULL DEFAULT '0',
  `a4` int(2) NOT NULL DEFAULT '0',
  `a5` int(2) NOT NULL DEFAULT '0',
  `t1` int(10) NOT NULL DEFAULT '0',
  `t2` int(10) NOT NULL DEFAULT '0',
  `t3` int(10) NOT NULL DEFAULT '0',
  `t4` int(10) NOT NULL DEFAULT '0',
  `t5` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`aid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=18 ;

-- --------------------------------------------------------

--
-- Table structure for table `game`
--

CREATE TABLE IF NOT EXISTS `game` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `p1` int(11) NOT NULL,
  `p2` int(11) NOT NULL,
  `ff` int(11) NOT NULL DEFAULT '0',
  `questions` text NOT NULL,
  `timestamp` int(10) NOT NULL,
  `game_end` int(10) NOT NULL DEFAULT '0',
  `winner` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Table structure for table `league_mastery_game`
--

CREATE TABLE IF NOT EXISTS `league_mastery_game` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `summoner_id` int(10) NOT NULL,
  `summoner_name` varchar(30) NOT NULL,
  `server_id` int(2) NOT NULL,
  `mastery` int(4) NOT NULL,
  `last_on` int(10) NOT NULL DEFAULT '0',
  `timestamp` int(10) NOT NULL,
  `validation` varchar(10) NOT NULL,
  `rank` int(6) NOT NULL DEFAULT '100',
  `queue` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `match_found`
--

CREATE TABLE IF NOT EXISTS `match_found` (
  `player1` int(11) NOT NULL,
  `player2` int(11) NOT NULL,
  `p1a` int(1) NOT NULL DEFAULT '0',
  `p2a` int(1) NOT NULL DEFAULT '0',
  `timestamp` int(10) NOT NULL,
  UNIQUE KEY `player1` (`player1`),
  UNIQUE KEY `player2` (`player2`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE IF NOT EXISTS `questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `json` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

INSERT INTO `questions` (`id`, `json`) VALUES
(1, '{"Q":"Which champion has the following quote, <quote>Gems? Gems are truly outrageous. They are truly, truly, truly outrageous.</quote>","C":{"1":"Skarner","2":"Teemo","3":"Master Yi","4":"Taric"},"A":"4"}'),
(2, '{"Q":"Which champion has the following quote, <quote>Rock solid!</quote>","C":{"1":"Nautilus","2":"Sion","3":"Taric","4":"Malphite"},"A":"4"}'),
(3, '{"Q":"Which champion has the following quote, <quote>By the blood of my father, I will end them.</quote>","C":{"1":"Yorick","2":"Shyvana","3":"Olaf","4":"Nocturne"},"A":"2"}'),
(4, '{"Q":"Which champion has the following quote, <quote>I never skip breakfast.</quote>","C":{"1":"Gragas","2":"Zac","3":"Tryndamere","4":"Gentlemen Chou2019Gath"},"A":"1"}'),
(5, '{"Q":"Which champion has the following quote, <quote>Allegrissimo.</quote>","C":{"1":"Alistar","2":"Thresh","3":"Sona","4":"Singed"},"A":"3"}'),
(6, '{"Q":"Which champion has the following quote, <quote>Stop being a bonehead!</quote>","C":{"1":"Karthus","2":"Mundo","3":"Yorick","4":"Sion"},"A":"4"}'),
(7, '{"Q":"Which champion has the following quote, <quote>They will know serenity.</quote>","C":{"1":"Soraka","2":"Leona","3":"Diana","4":"Sona"},"A":"1"}'),
(8, '{"Q":"Which champion has the following quote, <quote>My aim is steady.</quote>","C":{"1":"Quinn","2":"Caitlyn","3":"Ashe","4":"Miss Fortune"},"A":"3"}'),
(9, '{"Q":"Which champion has the following quote, <quote>Feeding time!</quote>","C":{"1":"Twitch","2":"Chou2019Gath","3":"Kogu2019Maw","4":"Gragas"},"A":"1"}'),
(10, '{"Q":"Which champion has the following quote, <quote>The unseen blade is the deadliest.</quote>","C":{"1":"Shen","2":"Shaco","3":"akali","4":"zed"},"A":"4"}');

-- --------------------------------------------------------

--
-- Table structure for table `servers`
--

CREATE TABLE IF NOT EXISTS `servers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `server` varchar(4) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
