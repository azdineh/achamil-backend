-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mer. 28 juin 2023 à 19:37
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
-- Structure de la table `lessons`
--

DROP TABLE IF EXISTS `lessons`;
CREATE TABLE IF NOT EXISTS `lessons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `url_pdf` varchar(255) DEFAULT NULL,
  `url_mp4` varchar(255) DEFAULT NULL,
  `id_unite` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_unite` (`id_unite`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `lessons`
--

INSERT INTO `lessons` (`id`, `title`, `subtitle`, `url_pdf`, `url_mp4`, `id_unite`) VALUES
(1, 'كان ة أخواتها', '', 'pdf/Oukp0oO6mxi75sKIZM3lSg7ByGxLEMZsFx9VMJ84.pdf', 'mp4/WgvxZNTFlmyMyjwiyj3pTiHc4t3s2XZMPgGFNDwH.mp4', 10),
(2, 'الأعداد العشرية النسبية', '', 'pdf/WqnJSRFRAnoHGaHBz9hHJn7xe6n6CnsrMDv9zkbm.pdf', 'mp4/sKrXoBPpH1VBp59pielvcbYBCx0xqE8m0I5gAeuD.mp4', 9),
(3, 'مبرهنة فيتاغورس', '', 'pdf/sIoTFKTvRXnt6L4upYzalDgcePyjYtZPMDBAw7Uv.pdf', 'mp4/4j0cLHvI7lhQYi31WL7RPuoCFOTJEu7YIUk7hHAA.mp4', 10),
(4, 'المعادلة من الدرجة الأولى', '', 'pdf/s8ms5izY5Ps611UvgTGCBeZ1QsLWq6HIJKPfw51k.pdf', 'mp4/Eg6v42dLphvn67wvy3948w0YWJZlNlj5JjQoe71v.mp4', 10),
(5, 'التركيب الضوئي', '', 'pdf/q6erKVeyVdmk5sq6fklvE0YOR3m2OoJTNwbeLHQd.pdf', 'mp4/GLY5aYBtMnewTV0fsNHoG0ouAGoOtzzmG7hiuaN3.mp4', 9);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
