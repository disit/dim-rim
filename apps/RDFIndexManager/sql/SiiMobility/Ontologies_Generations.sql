-- --------------------------------------------------------
-- Host:                         150.217.15.64
-- Versione server:              5.6.19-0ubuntu0.14.04.1 - (Ubuntu)
-- S.O. server:                  debian-linux-gnu
-- HeidiSQL Versione:            8.0.0.4396
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Dump della struttura di tabella SiiMobility.Ontologies_Generations
CREATE TABLE IF NOT EXISTS `Ontologies_Generations` (
  `ID_Generation` int(10) NOT NULL,
  `ID_Ontology` varchar(255) CHARACTER SET latin1 NOT NULL,
  `TripleDate` datetime NOT NULL,
  PRIMARY KEY (`ID_Generation`,`ID_Ontology`),
  KEY `IDOntology1` (`ID_Ontology`),
  CONSTRAINT `IDOntology1` FOREIGN KEY (`ID_Ontology`) REFERENCES `ontologies` (`Name`),
  CONSTRAINT `IDGeneration1` FOREIGN KEY (`ID_Generation`) REFERENCES `Generations` (`ID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- L’esportazione dei dati non era selezionata.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
