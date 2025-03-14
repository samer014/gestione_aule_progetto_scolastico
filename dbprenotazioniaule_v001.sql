-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versione server:              10.4.28-MariaDB - mariadb.org binary distribution
-- S.O. server:                  Win64
-- HeidiSQL Versione:            12.10.0.7000
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dump della struttura del database dbprenotazioniaule
CREATE DATABASE IF NOT EXISTS `dbprenotazioniaule` /*!40100 DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci */;
USE `dbprenotazioniaule`;

-- Dump della struttura di tabella dbprenotazioniaule.aule
CREATE TABLE IF NOT EXISTS `aule` (
  `nome` char(4) NOT NULL,
  `note` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`nome`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Dump dei dati della tabella dbprenotazioniaule.aule: ~101 rows (circa)
INSERT INTO `aule` (`nome`, `note`) VALUES
	('A010', NULL),
	('A017', NULL),
	('A061', NULL),
	('A062', NULL),
	('A063', NULL),
	('A064', NULL),
	('A105', NULL),
	('A106', NULL),
	('A107', NULL),
	('A108', NULL),
	('A109', NULL),
	('A110', NULL),
	('A116', NULL),
	('A118', NULL),
	('A120', NULL),
	('A121', NULL),
	('A122', NULL),
	('A123', NULL),
	('A161', NULL),
	('A162', NULL),
	('A163', NULL),
	('A164', NULL),
	('A201', NULL),
	('A202', NULL),
	('A203', NULL),
	('A205', NULL),
	('A207', NULL),
	('A208', NULL),
	('A209', NULL),
	('A210', NULL),
	('A212', NULL),
	('A214', NULL),
	('A215', NULL),
	('A216', NULL),
	('A217', NULL),
	('A218', NULL),
	('A219', NULL),
	('A220', NULL),
	('A221', NULL),
	('A222', NULL),
	('A223', NULL),
	('A241', NULL),
	('A243', NULL),
	('A261', NULL),
	('A262', NULL),
	('A263', NULL),
	('A264', NULL),
	('A301', NULL),
	('A303', NULL),
	('A305', NULL),
	('A307', NULL),
	('A308', NULL),
	('A309', NULL),
	('A310', NULL),
	('A312', NULL),
	('A314', NULL),
	('A315', NULL),
	('A316', NULL),
	('A317', NULL),
	('A318', NULL),
	('A319', NULL),
	('A320', NULL),
	('A321', NULL),
	('A322', NULL),
	('A323', NULL),
	('A345', NULL),
	('COR1', NULL),
	('COR2', NULL),
	('L005', NULL),
	('L006', NULL),
	('L007', NULL),
	('L008', NULL),
	('L009', NULL),
	('L015', NULL),
	('L019', NULL),
	('L042', NULL),
	('L043', NULL),
	('L044', NULL),
	('L045', NULL),
	('L046', NULL),
	('L048', NULL),
	('L141', NULL),
	('L142', NULL),
	('L143', NULL),
	('L144', NULL),
	('L145', NULL),
	('L146', NULL),
	('L204', NULL),
	('L206', NULL),
	('L242', NULL),
	('L244', NULL),
	('L245', NULL),
	('L246', NULL),
	('L247', NULL),
	('L341', NULL),
	('L342', NULL),
	('L346', NULL),
	('L347', NULL),
	('P041', NULL),
	('P053', NULL),
	('P072', NULL);

-- Dump della struttura di tabella dbprenotazioniaule.orari
CREATE TABLE IF NOT EXISTS `orari` (
  `ora` int(11) NOT NULL,
  `oraInizio` varchar(5) NOT NULL,
  PRIMARY KEY (`ora`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Dump dei dati della tabella dbprenotazioniaule.orari: ~8 rows (circa)
INSERT INTO `orari` (`ora`, `oraInizio`) VALUES
	(1, '08:00'),
	(2, '08:50'),
	(3, '09:50'),
	(4, '10:50'),
	(5, '12:00'),
	(6, '12:50'),
	(7, '13:40'),
	(8, '14:30');

-- Dump della struttura di tabella dbprenotazioniaule.prenotazioni
CREATE TABLE IF NOT EXISTS `prenotazioni` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dataPrenotazione` datetime NOT NULL,
  `accettata` tinyint(1) DEFAULT NULL,
  `oraInizio` datetime NOT NULL,
  `oraFine` datetime NOT NULL CHECK (`oraFine` > `oraInizio`),
  `dataEsito` datetime DEFAULT NULL,
  `aula` char(4) DEFAULT NULL,
  `IdUtente` int(11) DEFAULT NULL,
  `IdAmministratore` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `aula` (`aula`),
  KEY `IdUtente` (`IdUtente`),
  KEY `IdAmministratore` (`IdAmministratore`),
  CONSTRAINT `prenotazioni_ibfk_1` FOREIGN KEY (`aula`) REFERENCES `aule` (`nome`),
  CONSTRAINT `prenotazioni_ibfk_2` FOREIGN KEY (`IdUtente`) REFERENCES `utenti` (`id`),
  CONSTRAINT `prenotazioni_ibfk_3` FOREIGN KEY (`IdAmministratore`) REFERENCES `utenti` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Dump dei dati della tabella dbprenotazioniaule.prenotazioni: ~0 rows (circa)

-- Dump della struttura di tabella dbprenotazioniaule.utenti
CREATE TABLE IF NOT EXISTS `utenti` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(10) NOT NULL,
  `password` varchar(30) NOT NULL,
  `amministratore` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Dump dei dati della tabella dbprenotazioniaule.utenti: ~4 rows (circa)
INSERT INTO `utenti` (`id`, `username`, `password`, `amministratore`) VALUES
	(1, 'adm', 'adm', 1),
	(3, 'utente', 'utente', 0),
	(4, '19926', '19926', 0),
	(5, 'tezza', 'tezza', 0);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
