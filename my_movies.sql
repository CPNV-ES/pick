-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Mar 15 Décembre 2015 à 14:14
-- Version du serveur :  5.6.17
-- Version de PHP :  5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `my_movies`
--
CREATE DATABASE IF NOT EXISTS `my_movies` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
USE `my_movies`;

-- --------------------------------------------------------
 
--
-- Structure de la table `actors`
--

CREATE TABLE IF NOT EXISTS `actors` (
  `id_actor` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  PRIMARY KEY (`id_actor`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1846 ;

-- --------------------------------------------------------

--
-- Structure de la table `corrects`
--

CREATE TABLE IF NOT EXISTS `corrects` (
  `id_correct` int(11) NOT NULL AUTO_INCREMENT,
  `filename_source` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `filename_correct` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id_correct`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=41 ;

-- --------------------------------------------------------

--
-- Structure de la table `directors`
--

CREATE TABLE IF NOT EXISTS `directors` (
  `id_director` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `firstname` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_director`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=41 ;

-- --------------------------------------------------------

--
-- Structure de la table `genres`
--

CREATE TABLE IF NOT EXISTS `genres` (
  `id_genre` int(11) NOT NULL AUTO_INCREMENT,
  `genre` varchar(50) NOT NULL,
  PRIMARY KEY (`id_genre`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=110 ;

-- --------------------------------------------------------

--
-- Structure de la table `movies`
--

CREATE TABLE IF NOT EXISTS `movies` (
  `id_movie` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `release_date` date DEFAULT NULL,
  `note` decimal(2,1) DEFAULT NULL,
  `name_file` varchar(255) NOT NULL,
  `original_title` varchar(45) DEFAULT NULL,
  `runtime` int(11) DEFAULT NULL,
  `synopsis` longtext,
  `poster_path` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  PRIMARY KEY (`id_movie`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=96 ;

-- --------------------------------------------------------

--
-- Structure de la table `movie_actors`
--

CREATE TABLE IF NOT EXISTS `movie_actors` (
  `id_movie` int(11) NOT NULL,
  `id_actor` int(11) NOT NULL,
  `role` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_movie`,`id_actor`),
  KEY `fk_tblMovie_has_tblActor_tblActor1_idx` (`id_actor`),
  KEY `fk_tblMovie_has_tblActor_tblMovie1_idx` (`id_movie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `movie_directors`
--

CREATE TABLE IF NOT EXISTS `movie_directors` (
  `id_movie` int(11) NOT NULL,
  `id_director` int(11) NOT NULL,
  PRIMARY KEY (`id_movie`,`id_director`),
  KEY `fk_tblMovie_has_tblDirector_tblDirector1_idx` (`id_director`),
  KEY `fk_tblMovie_has_tblDirector_tblMovie1_idx` (`id_movie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `movie_genres`
--

CREATE TABLE IF NOT EXISTS `movie_genres` (
  `id_movie` int(11) NOT NULL,
  `id_genre` int(11) NOT NULL,
  PRIMARY KEY (`id_movie`,`id_genre`),
  KEY `fk_tblMovie_has_tblGenre_tblGenre1_idx` (`id_genre`),
  KEY `fk_tblMovie_has_tblGenre_tblMovie1_idx` (`id_movie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `movie_producers`
--

CREATE TABLE IF NOT EXISTS `movie_producers` (
  `id_movie` int(11) NOT NULL,
  `id_producer` int(11) NOT NULL,
  PRIMARY KEY (`id_movie`,`id_producer`),
  KEY `fk_tblMovie_has_tblProducer_tblProducer1_idx` (`id_producer`),
  KEY `fk_tblMovie_has_tblProducer_tblMovie1_idx` (`id_movie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `producers`
--

CREATE TABLE IF NOT EXISTS `producers` (
  `id_producer` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `firstname` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_producer`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=77 ;

--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `movie_actors`
--
ALTER TABLE `movie_actors`
  ADD CONSTRAINT `fk_tblMovie_has_tblActor_tblActor1` FOREIGN KEY (`id_actor`) REFERENCES `actors` (`id_actor`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_tblMovie_has_tblActor_tblMovie1` FOREIGN KEY (`id_movie`) REFERENCES `movies` (`id_movie`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `movie_directors`
--
ALTER TABLE `movie_directors`
  ADD CONSTRAINT `fk_tblMovie_has_tblDirector_tblDirector1` FOREIGN KEY (`id_director`) REFERENCES `directors` (`id_director`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_tblMovie_has_tblDirector_tblMovie1` FOREIGN KEY (`id_movie`) REFERENCES `movies` (`id_movie`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `movie_genres`
--
ALTER TABLE `movie_genres`
  ADD CONSTRAINT `fk_tblMovie_has_tblGenre_tblGenre1` FOREIGN KEY (`id_genre`) REFERENCES `genres` (`id_genre`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_tblMovie_has_tblGenre_tblMovie1` FOREIGN KEY (`id_movie`) REFERENCES `movies` (`id_movie`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `movie_producers`
--
ALTER TABLE `movie_producers`
  ADD CONSTRAINT `fk_tblMovie_has_tblProducer_tblMovie1` FOREIGN KEY (`id_movie`) REFERENCES `movies` (`id_movie`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_tblMovie_has_tblProducer_tblProducer1` FOREIGN KEY (`id_producer`) REFERENCES `producers` (`id_producer`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
