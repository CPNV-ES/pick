-- phpMyAdmin SQL Dump
-- version 4.4.14
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Ven 13 Novembre 2015 à 11:04
-- Version du serveur :  5.6.26
-- Version de PHP :  5.6.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

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
  `id_actor` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `firstname` varchar(100) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=1846 DEFAULT CHARSET=utf8;

--
-- Contenu de la table `actors`
--

INSERT INTO `actors` (`id_actor`, `name`, `firstname`) VALUES
(1731, 'Haig', 'Sid'),
(1732, 'Moseley', 'Bill'),
(1733, 'Moon', 'Sheri'),
(1734, 'Black', 'Karen'),
(1735, 'Daniels', 'Erin'),
(1736, 'Hardwick', 'Chris'),
(1737, 'Wilson', 'Rainn'),
(1738, 'Jostyn', 'Jennifer'),
(1739, 'Towles', 'Tom'),
(1740, 'Goggins', 'Walton'),
(1741, 'McGrory', 'Matthew'),
(1742, 'Allen', 'Robert'),
(1743, 'Fimple', 'Dennis'),
(1744, 'McKinnon', 'Jake'),
(1745, 'Young', 'Harrison'),
(1746, 'Keyes', 'Irwin'),
(1747, 'J.', 'Michael'),
(1748, 'Bannon', 'Chad'),
(1749, 'Bassett', 'William'),
(1750, 'Reynolds', 'David'),
(1751, 'Karmann', 'Sam'),
(1752, 'Chabat', 'Alain'),
(1753, 'Bouvet', 'Jean-Christophe'),
(1754, 'Lauby', 'Chantal'),
(1755, 'Farrugia', 'Dominique'),
(1756, 'Darmon', 'GÃ©rard'),
(1757, 'de', 'HÃ©lÃ¨ne'),
(1758, 'Arquette', 'Rosanna'),
(1759, 'Karyo', 'TchÃ©ky'),
(1760, 'Bacri', 'Jean-Pierre'),
(1761, 'GÃ©lin', 'Daniel'),
(1762, 'Mitchell', 'Eddy'),
(1763, 'Lescure', 'Pierre'),
(1764, 'Lizana', 'Patrick'),
(1765, 'Prat', 'Eric'),
(1766, 'de', 'Marc'),
(1767, 'Lemercier', 'ValÃ©rie'),
(1768, '', 'Dave'),
(1769, 'Laffont', 'Patrice'),
(1770, 'de', 'Artus'),
(1771, 'Hazanavicius', 'Michel'),
(1772, 'Toscan', 'Daniel'),
(1773, 'Cameron', 'James'),
(1774, 'Doran', 'Olivier'),
(1775, 'Gazio', 'Christian'),
(1776, 'Viala', 'Florence'),
(1777, 'Chany', 'Philippe'),
(1778, 'Amzallag', 'Pierre'),
(1779, 'Carette', 'Bruno'),
(1780, 'Hammond', 'Claire'),
(1781, 'Moro', 'Christian'),
(1782, 'Joubert', 'Florence'),
(1783, 'Rodier', 'HÃ©lÃ¨ne'),
(1784, 'Bonnet-GuÃ©rin', 'GÃ©raldine'),
(1785, 'Mounicot', 'Sophie'),
(1786, 'Lanvin', 'GÃ©rard'),
(1787, 'Besnehard', 'Dominique'),
(1788, 'HÃ©ros', 'Pierre'),
(1789, 'de', 'Henri'),
(1790, 'Driscoll', 'Bobby'),
(1791, 'Beaumont', 'Kathryn'),
(1792, 'Conried', 'Hans'),
(1793, 'Thompson', 'Bill'),
(1794, 'Angel', 'Heather'),
(1795, 'Collins', 'Paul'),
(1796, 'Luske', 'Tommy'),
(1797, 'Candido', 'Candy'),
(1798, 'Conway', 'Tom'),
(1799, 'Taylor', 'Veronica'),
(1800, 'Lillis', 'Rachael'),
(1801, 'Blaustein', 'Maddie'),
(1802, 'ÅŒtani', 'Ikue'),
(1803, 'Electra', 'Carmen'),
(1804, 'Sheridan', 'Dave'),
(1805, 'Faris', 'Anna'),
(1806, 'Abrahams', 'Jon'),
(1807, 'Hall', 'Regina'),
(1808, 'Wayans', 'Marlon'),
(1809, 'Elizabeth', 'Shannon'),
(1810, 'Oteri', 'Cheri'),
(1811, 'Wayans', 'Shawn'),
(1812, 'Ivory', 'Keenen'),
(1813, 'Jaret', 'Marissa'),
(1814, 'Van', 'James'),
(1815, 'Fuller', 'Kurt'),
(1816, 'Munro', 'Lochlyn'),
(1817, 'McKellen', 'Ian'),
(1818, 'Freeman', 'Martin'),
(1819, 'Armitage', 'Richard'),
(1820, 'Serkis', 'Andy'),
(1821, 'Blanchett', 'Cate'),
(1822, 'Lee', 'Christopher'),
(1823, 'McCoy', 'Sylvester'),
(1824, 'Holm', 'Ian'),
(1825, 'Wood', 'Elijah'),
(1826, 'Weaving', 'Hugo'),
(1827, 'Pace', 'Lee'),
(1828, 'Stevens', 'Conan'),
(1829, 'McKenzie', 'Bret'),
(1830, 'Turner', 'Aidan'),
(1831, 'Nesbitt', 'James'),
(1832, 'McTavish', 'Graham'),
(1833, 'Humphries', 'Barry'),
(1834, 'Stott', 'Ken'),
(1835, 'Thomas', 'Jeffrey'),
(1836, 'Hadlow', 'Mark'),
(1837, 'Callen', 'John'),
(1838, 'Kircher', 'William'),
(1839, 'Bell', 'John'),
(1840, 'Cumberbatch', 'Benedict'),
(1841, 'O''Gorman', 'Dean'),
(1842, 'Bennett', 'Manu'),
(1843, 'Brophy', 'Jed'),
(1844, 'Brown', 'Adam'),
(1845, 'Hunter', 'Stephen');

-- --------------------------------------------------------

--
-- Structure de la table `corrects`
--

CREATE TABLE IF NOT EXISTS `corrects` (
  `id_correct` int(11) NOT NULL,
  `filename_source` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `filename_correct` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Contenu de la table `corrects`
--

INSERT INTO `corrects` (`id_correct`, `filename_source`, `filename_correct`) VALUES
(37, '26 Les Aventures de Bernard et Bianca.Zone-Telechargement.com.avi', '26 Les Aventures de Bernard et Bianca'),
(38, 'Hary potter.txt', 'Hary potter'),
(39, 'Pinocchio.PLATiNUM.EDiTiON.French.DvDrip.XviD.zone-telechargement.com.avi', 'Pinocchio PLATiNUM EDiTiON'),
(40, 'PoKeMoN Le FiLM - MeWTWo Vs MeW.avi', 'PoKeMoN Le FiLM   MeWTWo Vs MeW');

-- --------------------------------------------------------

--
-- Structure de la table `directors`
--

CREATE TABLE IF NOT EXISTS `directors` (
  `id_director` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `firstname` varchar(50) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8;

--
-- Contenu de la table `directors`
--

INSERT INTO `directors` (`id_director`, `name`, `firstname`) VALUES
(33, 'Zombie', 'Rob'),
(34, 'BerbÃ©rian', 'Alain'),
(35, 'Geronimi', 'Clyde'),
(36, 'Jackson', 'Wilfred'),
(37, 'Haigney', 'Michael'),
(38, 'Yuyama', 'Kunihiko'),
(39, 'Ivory', 'Keenen'),
(40, 'Jackson', 'Peter');

-- --------------------------------------------------------

--
-- Structure de la table `genres`
--

CREATE TABLE IF NOT EXISTS `genres` (
  `id_genre` int(11) NOT NULL,
  `genre` varchar(50) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=110 DEFAULT CHARSET=utf8;

--
-- Contenu de la table `genres`
--

INSERT INTO `genres` (`id_genre`, `genre`) VALUES
(94, 'Horror'),
(95, 'Comedy'),
(96, 'Animation'),
(97, 'Music'),
(98, 'Family'),
(99, 'Adventure'),
(100, 'Fantasy'),
(101, 'Adventure'),
(102, 'Fantasy'),
(103, 'Animation'),
(104, 'Science Fiction'),
(105, 'Family'),
(106, 'Comedy'),
(107, 'Adventure'),
(108, 'Fantasy'),
(109, 'Action');

-- --------------------------------------------------------

--
-- Structure de la table `movies`
--

CREATE TABLE IF NOT EXISTS `movies` (
  `id_movie` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `release_date` date DEFAULT NULL,
  `note` decimal(2,1) DEFAULT NULL,
  `name_file` varchar(255) NOT NULL,
  `original_title` varchar(45) DEFAULT NULL,
  `runtime` int(11) DEFAULT NULL,
  `synopsis` longtext,
  `poster_path` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=96 DEFAULT CHARSET=utf8;

--
-- Contenu de la table `movies`
--

INSERT INTO `movies` (`id_movie`, `title`, `release_date`, `note`, `name_file`, `original_title`, `runtime`, `synopsis`, `poster_path`, `file_path`) VALUES
(90, 'House of 1000 Corpses', '2003-03-13', '5.7', 'La Maison Des 1000 Morts FRENCH DVDRiP XViD AC3-FwD.avi', 'House of 1000 Corpses', 89, 'Two teenage couples traveling across the backwoods of Texas searching for urban legends of serial killers end up as prisoners of a bizarre and sadistic backwater family of serial killers.', '/8l9wpDtIXcVxZPp2IMtCj6I1iCx.jpg', '/xampp/htdocs/sweg/Video//La Maison Des 1000 Morts FRENCH DVDRiP XViD AC3-FwD.avi'),
(91, 'Fear City: A Family-Style Comedy', '1994-03-09', '8.0', 'La.Cite.De.La.Peur.FRENCH.DVDRiP.DivX-www.Zone-Telechargement.com.avi', 'La citÃ© de la peur', 93, 'A second-class horror movie has to be shown at Cannes Film Festival, but, before each screening, the projectionist is killed by a mysterious fellow, with hammer and sickle, just as it happens in the film to be shown.', '/l43KgZb2ICqLcYE5YDgwLWxlzIx.jpg', '/xampp/htdocs/sweg/Video//La.Cite.De.La.Peur.FRENCH.DVDRiP.DivX-www.Zone-Telechargement.com.avi'),
(92, 'Peter Pan', '1953-02-05', '6.6', 'Peter.Pan.Truefrench.Subforced.DVDRip.XviD-LiberTeam.avi', 'Peter Pan', 77, 'Leaving the safety of their nursery behind, Wendy, Michael and John follow Peter Pan to a magical world where childhood lasts forever. But while in Neverland, the kids must face Captain Hook and foil his attempts to get rid of Peter for good.', '/aWLgUzpkVFk8OVcg5jJte5I0Ces.jpg', '/xampp/htdocs/sweg/Video//Peter.Pan.Truefrench.Subforced.DVDRip.XviD-LiberTeam.avi'),
(93, 'PokÃ©mon: The Movie 2000', '1999-07-17', '6.4', 'PoKéMoN 2.avi', 'åŠ‡å ´ç‰ˆãƒã‚±ãƒƒãƒˆãƒ¢ãƒ³ã‚¹ã‚¿ãƒ¼ å¹»ã®ãƒ', 84, 'Ash Ketchum must put his skill to the test when he attempts to save the world from destruction. The Greedy Pokemon collector Lawrence III throws the universe into chaos after disrupting the balance of nature by capturing one of the Pokemon birds that rule the elements of fire, lightning and ice. Will Ash have what it takes to save the world?', '/jrwTeoEHCmFEr0J9HENdPAR3dEq.jpg', '/xampp/htdocs/sweg/Video//PoKÃ©MoN 2.avi'),
(94, 'Scary Movie', '2000-07-07', '5.9', 'Scary.Movie.avi', 'Scary Movie', 88, 'Following on the heels of popular teen-scream horror movies, with uproarious comedy and biting satire. Marlon and Shawn Wayans, Shannon Elizabeth and Carmen Electra pitch in to skewer some of Hollywood''s biggest blockbusters, including Scream, I Know What You Did Last Summer, The Matrix, American Pie and The Blair Witch Project.', '/bvVmVFBVQLytK1H4TJTFdnhvf4T.jpg', '/xampp/htdocs/sweg/Video//Scary.Movie.avi'),
(95, 'The Hobbit: An Unexpected Journey', '2012-12-14', '6.8', 'The.Hobbit 1.An.Unexpected.Journey..mp4', 'The Hobbit: An Unexpected Journey', 169, 'Bilbo Baggins, a hobbit enjoying his quiet life, is swept into an epic quest by Gandalf the Grey and thirteen dwarves who seek to reclaim their mountain home from Smaug, the dragon.', '/w29Guo6FX6fxzH86f8iAbEhQEFC.jpg', '/xampp/htdocs/sweg/Video//The.Hobbit 1.An.Unexpected.Journey..mp4');

-- --------------------------------------------------------

--
-- Structure de la table `movie_actors`
--

CREATE TABLE IF NOT EXISTS `movie_actors` (
  `id_movie` int(11) NOT NULL,
  `id_actor` int(11) NOT NULL,
  `role` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `movie_actors`
--

INSERT INTO `movie_actors` (`id_movie`, `id_actor`, `role`) VALUES
(90, 1731, 'Captain Spaulding'),
(90, 1732, 'Otis B. Driftwood'),
(90, 1733, 'Baby Firefly'),
(90, 1734, 'Mother Firefly'),
(90, 1735, 'Denise Willis'),
(90, 1736, 'Jerry Goldsmith'),
(90, 1737, 'Bill Hudley'),
(90, 1738, 'Mary Knowles'),
(90, 1739, 'Lieutenant George Wydell'),
(90, 1740, 'Deputy Steve Naish'),
(90, 1741, 'Tiny Firefly'),
(90, 1742, 'Rufus ''RJ'' Firefly Jr.'),
(90, 1743, 'Grandpa Hugo Firefly'),
(90, 1744, 'Rufus ''Earl'' Firefly Sr.'),
(90, 1745, 'Don Willis'),
(90, 1746, 'Ravelli'),
(90, 1747, 'Stucky'),
(90, 1748, 'Killer Karl'),
(90, 1749, 'Sheriff Frank Huston'),
(90, 1750, 'Richard ''Little Dick'' Wick'),
(91, 1751, 'Ã‰mile Gravier'),
(91, 1752, 'Serge Karamazov'),
(91, 1753, 'Jean-Paul Martoni'),
(91, 1754, 'Odile Deray'),
(91, 1755, 'Simon JÃ©rÃ©mi'),
(91, 1756, 'Commissaire Patrick BialÃ¨s'),
(91, 1757, 'Sandy'),
(91, 1758, 'Elle-mÃªme'),
(91, 1759, 'La premiÃ¨re victime'),
(91, 1760, 'Une victime'),
(91, 1761, 'Une victime'),
(91, 1762, 'Une victime'),
(91, 1763, 'Lui-mÃªme'),
(91, 1764, 'Grimaldi'),
(91, 1765, 'Garcia'),
(91, 1766, 'Le patron de Serge Karamazov'),
(91, 1767, 'La veuve joyeuse'),
(91, 1768, 'Lui-mÃªme'),
(91, 1769, 'Lui-mÃªme'),
(91, 1770, 'Sens'),
(91, 1771, 'RÃ©gis'),
(91, 1772, 'Himself'),
(91, 1773, 'Lui-mÃªme'),
(91, 1774, 'Jean'),
(91, 1775, 'L''infirmier dans l''ambulance'),
(91, 1776, 'Martine'),
(91, 1777, 'Le voisin du crÃ©tin dans la foule'),
(91, 1778, 'Bestel'),
(91, 1779, 'Mizou Mizou (images d''archives)'),
(91, 1780, 'La journaliste pipelette 2'),
(91, 1781, 'Un assistant d''Odile'),
(91, 1782, 'Susan dans "Red is Dead"'),
(91, 1783, 'Janine'),
(91, 1784, 'Tiffany'),
(91, 1785, 'La journaliste pipelette 1'),
(91, 1786, '(scÃ¨ne coupÃ©e)'),
(91, 1787, 'Le journaliste au camÃ©o'),
(91, 1788, 'Lui-mÃªme'),
(91, 1789, 'Le rÃ©citant du documentaire tissu (voix)'),
(92, 1790, 'Peter Pan (voice)'),
(92, 1791, 'Wendy Darling (voice)'),
(92, 1792, 'Captain Hook / Mr. Darling (voice)'),
(92, 1793, 'Mr. Smee (voice)'),
(92, 1794, 'Mrs. Darling (voice)'),
(92, 1795, 'John Darling (voice)'),
(92, 1796, 'Michael Darling (voice)'),
(92, 1797, 'Indian Chief (voice)'),
(92, 1798, 'Narrator (voice)'),
(93, 1799, 'Ash Ketchum (voice)'),
(93, 1800, 'Misty (voice)'),
(93, 1801, 'Meowth (voice)'),
(93, 1802, 'Pikachu (voice)'),
(94, 1803, 'Drew Decker'),
(94, 1804, 'The Killer/Doofy Gilmore'),
(94, 1805, 'Cindy Campbell'),
(94, 1806, 'Bobby Prinze'),
(94, 1807, 'Brenda Meeks'),
(94, 1808, 'Shorty Meeks'),
(94, 1809, 'Buffy Gilmore'),
(94, 1810, 'Gail Hailstorm'),
(94, 1811, 'Ray Wilkins'),
(94, 1812, 'Slave'),
(94, 1813, 'Garage Victim'),
(94, 1814, 'Dawson Leery'),
(94, 1815, 'The Sheriff'),
(94, 1816, 'Greg Phillippe'),
(95, 1817, 'Gandalf'),
(95, 1818, 'Bilbo'),
(95, 1819, 'Thorin'),
(95, 1820, 'Gollum'),
(95, 1821, 'Galadriel'),
(95, 1822, 'Saruman'),
(95, 1823, 'Radagast'),
(95, 1824, 'Older Bilbo'),
(95, 1825, 'Frodo'),
(95, 1826, 'Elrond'),
(95, 1827, 'Thranduil'),
(95, 1828, 'Bolg'),
(95, 1829, 'Lindir'),
(95, 1830, 'Kili'),
(95, 1831, 'Bofur'),
(95, 1832, 'Dwalin'),
(95, 1833, 'Great Goblin'),
(95, 1834, 'Balin'),
(95, 1835, 'King Thror'),
(95, 1836, 'Dori'),
(95, 1837, 'Oin'),
(95, 1838, 'Bifur/Tom Troll'),
(95, 1839, 'Bain'),
(95, 1840, 'Necromancer'),
(95, 1841, 'Fili'),
(95, 1842, 'Azog'),
(95, 1843, 'Nori'),
(95, 1844, 'Ori'),
(95, 1845, 'Bombur');

-- --------------------------------------------------------

--
-- Structure de la table `movie_directors`
--

CREATE TABLE IF NOT EXISTS `movie_directors` (
  `id_movie` int(11) NOT NULL,
  `id_director` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `movie_directors`
--

INSERT INTO `movie_directors` (`id_movie`, `id_director`) VALUES
(90, 33),
(91, 34),
(92, 35),
(92, 36),
(93, 37),
(93, 38),
(94, 39),
(95, 40);

-- --------------------------------------------------------

--
-- Structure de la table `movie_genres`
--

CREATE TABLE IF NOT EXISTS `movie_genres` (
  `id_movie` int(11) NOT NULL,
  `id_genre` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `movie_genres`
--

INSERT INTO `movie_genres` (`id_movie`, `id_genre`) VALUES
(90, 94),
(91, 95),
(92, 96),
(92, 97),
(92, 98),
(92, 99),
(92, 100),
(93, 101),
(93, 102),
(93, 103),
(93, 104),
(93, 105),
(94, 106),
(95, 107),
(95, 108),
(95, 109);

-- --------------------------------------------------------

--
-- Structure de la table `movie_producers`
--

CREATE TABLE IF NOT EXISTS `movie_producers` (
  `id_movie` int(11) NOT NULL,
  `id_producer` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `movie_producers`
--

INSERT INTO `movie_producers` (`id_movie`, `id_producer`) VALUES
(90, 61),
(91, 62),
(91, 63),
(92, 64),
(93, 65),
(93, 66),
(93, 67),
(94, 68),
(94, 69),
(94, 70),
(94, 71),
(94, 72),
(95, 73),
(95, 74),
(95, 75),
(95, 76);

-- --------------------------------------------------------

--
-- Structure de la table `producers`
--

CREATE TABLE IF NOT EXISTS `producers` (
  `id_producer` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `firstname` varchar(50) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=77 DEFAULT CHARSET=utf8;

--
-- Contenu de la table `producers`
--

INSERT INTO `producers` (`id_producer`, `name`, `firstname`) VALUES
(61, 'Gould', 'Andy'),
(62, 'Gassot', 'Charles'),
(63, 'Brunner', 'Dominique'),
(64, 'Disney', 'Walt'),
(65, 'Matsusako', 'Yukako'),
(66, 'Mori', 'Takemoto'),
(67, 'Yoshikawa', 'Choji'),
(68, 'L.', 'Eric'),
(69, 'Ivory', 'Keenen'),
(70, 'Wayans', 'Marlon'),
(71, 'Wayans', 'Shawn'),
(72, 'R.', 'Lee'),
(73, 'Cunningham', 'Carolynne'),
(74, 'Jackson', 'Peter'),
(75, 'Walsh', 'Fran'),
(76, 'Weiner', 'Zane');

--
-- Index pour les tables exportées
--

--
-- Index pour la table `actors`
--
ALTER TABLE `actors`
  ADD PRIMARY KEY (`id_actor`);

--
-- Index pour la table `corrects`
--
ALTER TABLE `corrects`
  ADD PRIMARY KEY (`id_correct`);

--
-- Index pour la table `directors`
--
ALTER TABLE `directors`
  ADD PRIMARY KEY (`id_director`);

--
-- Index pour la table `genres`
--
ALTER TABLE `genres`
  ADD PRIMARY KEY (`id_genre`);

--
-- Index pour la table `movies`
--
ALTER TABLE `movies`
  ADD PRIMARY KEY (`id_movie`);

--
-- Index pour la table `movie_actors`
--
ALTER TABLE `movie_actors`
  ADD PRIMARY KEY (`id_movie`,`id_actor`),
  ADD KEY `fk_tblMovie_has_tblActor_tblActor1_idx` (`id_actor`),
  ADD KEY `fk_tblMovie_has_tblActor_tblMovie1_idx` (`id_movie`);

--
-- Index pour la table `movie_directors`
--
ALTER TABLE `movie_directors`
  ADD PRIMARY KEY (`id_movie`,`id_director`),
  ADD KEY `fk_tblMovie_has_tblDirector_tblDirector1_idx` (`id_director`),
  ADD KEY `fk_tblMovie_has_tblDirector_tblMovie1_idx` (`id_movie`);

--
-- Index pour la table `movie_genres`
--
ALTER TABLE `movie_genres`
  ADD PRIMARY KEY (`id_movie`,`id_genre`),
  ADD KEY `fk_tblMovie_has_tblGenre_tblGenre1_idx` (`id_genre`),
  ADD KEY `fk_tblMovie_has_tblGenre_tblMovie1_idx` (`id_movie`);

--
-- Index pour la table `movie_producers`
--
ALTER TABLE `movie_producers`
  ADD PRIMARY KEY (`id_movie`,`id_producer`),
  ADD KEY `fk_tblMovie_has_tblProducer_tblProducer1_idx` (`id_producer`),
  ADD KEY `fk_tblMovie_has_tblProducer_tblMovie1_idx` (`id_movie`);

--
-- Index pour la table `producers`
--
ALTER TABLE `producers`
  ADD PRIMARY KEY (`id_producer`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `actors`
--
ALTER TABLE `actors`
  MODIFY `id_actor` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1846;
--
-- AUTO_INCREMENT pour la table `corrects`
--
ALTER TABLE `corrects`
  MODIFY `id_correct` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=41;
--
-- AUTO_INCREMENT pour la table `directors`
--
ALTER TABLE `directors`
  MODIFY `id_director` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=41;
--
-- AUTO_INCREMENT pour la table `genres`
--
ALTER TABLE `genres`
  MODIFY `id_genre` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=110;
--
-- AUTO_INCREMENT pour la table `movies`
--
ALTER TABLE `movies`
  MODIFY `id_movie` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=96;
--
-- AUTO_INCREMENT pour la table `producers`
--
ALTER TABLE `producers`
  MODIFY `id_producer` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=77;
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
