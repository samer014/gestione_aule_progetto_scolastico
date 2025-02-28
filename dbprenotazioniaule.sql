-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Feb 28, 2025 alle 10:26
-- Versione del server: 10.4.28-MariaDB
-- Versione PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dbprenotazioniaule`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `aule`
--

CREATE TABLE `aule` (
  `nome` char(4) NOT NULL,
  `note` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dump dei dati per la tabella `aule`
--

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

-- --------------------------------------------------------

--
-- Struttura della tabella `prenotazioni`
--

CREATE TABLE `prenotazioni` (
  `id` int(11) NOT NULL,
  `dataPrenotazione` datetime NOT NULL,
  `dataScelta` datetime NOT NULL,
  `accettata` tinyint(1) DEFAULT NULL,
  `oraInizio` datetime NOT NULL,
  `oraFine` datetime NOT NULL CHECK (`oraFine` > `oraInizio`),
  `dataEsito` datetime DEFAULT NULL,
  `aula` char(4) DEFAULT NULL,
  `IdUtente` int(11) DEFAULT NULL,
  `IdAmministratore` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `utenti`
--

CREATE TABLE `utenti` (
  `id` int(11) NOT NULL,
  `username` varchar(10) NOT NULL,
  `password` varchar(30) NOT NULL,
  `amministratore` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `aule`
--
ALTER TABLE `aule`
  ADD PRIMARY KEY (`nome`);

--
-- Indici per le tabelle `prenotazioni`
--
ALTER TABLE `prenotazioni`
  ADD PRIMARY KEY (`id`),
  ADD KEY `aula` (`aula`),
  ADD KEY `IdUtente` (`IdUtente`),
  ADD KEY `IdAmministratore` (`IdAmministratore`);

--
-- Indici per le tabelle `utenti`
--
ALTER TABLE `utenti`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `prenotazioni`
--
ALTER TABLE `prenotazioni`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `utenti`
--
ALTER TABLE `utenti`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `prenotazioni`
--
ALTER TABLE `prenotazioni`
  ADD CONSTRAINT `prenotazioni_ibfk_1` FOREIGN KEY (`aula`) REFERENCES `aule` (`nome`),
  ADD CONSTRAINT `prenotazioni_ibfk_2` FOREIGN KEY (`IdUtente`) REFERENCES `utenti` (`id`),
  ADD CONSTRAINT `prenotazioni_ibfk_3` FOREIGN KEY (`IdAmministratore`) REFERENCES `utenti` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
