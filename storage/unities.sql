-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mer. 28 juin 2023 à 19:23
-- Version du serveur : 5.7.36
-- Version de PHP : 7.4.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `achamil`
--

-- --------------------------------------------------------

--
-- Structure de la table `unities`
--

DROP TABLE IF EXISTS `unities`;
CREATE TABLE IF NOT EXISTS `unities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8 NOT NULL,
  `id_subunite` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_subunite` (`id_subunite`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `unities`
--

INSERT INTO `unities` (`id`, `title`, `id_subunite`) VALUES
(1, 'الإستعداد للمبارة', 0),
(2, 'المعارف المدرسة بالسلك الإبتدائي', 0),
(3, 'الديداكتيك', 0),
(4, 'علوم التربية', 0),
(5, 'مستجدات المنهاج', 0),
(6, 'الإمتحانات السابقة', 0),
(7, 'يوم الإمتحان', 0),
(8, 'المقابلة الشفهية', 0),
(11, 'الرسالة التحفيزية', 1),
(10, 'الترشح للمبارة', 1),
(9, 'توصيف مبارة', 1);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
