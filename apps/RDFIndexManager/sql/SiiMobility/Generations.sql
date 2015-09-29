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

-- Dump della struttura di tabella SiiMobility.Generations
CREATE TABLE `Generations` (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `SessionStart` datetime NOT NULL,
  `SessionEnd` datetime NOT NULL,
  `SessionIP` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `GenerationStart` datetime NOT NULL,
  `GenerationEnd` datetime NOT NULL,
  `ScriptPath` text COLLATE utf8_unicode_ci NOT NULL,
  `RepositoryID` text COLLATE utf8_unicode_ci NOT NULL,
  `Description` text COLLATE utf8_unicode_ci NOT NULL,
  `ParentID` text COLLATE utf8_unicode_ci NOT NULL,
  `Type` text COLLATE utf8_unicode_ci NOT NULL,
  `Version` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- L’esportazione dei dati non era selezionata.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
