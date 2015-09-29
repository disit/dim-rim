CREATE TABLE `enrichments_generations` (
  `ID_Generation` int(10) NOT NULL,
  `ID_Enrichment` varchar(255) CHARACTER SET latin1 NOT NULL,
  `Clone` int(1) NOT NULL DEFAULT '0',
  `Locked` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID_Generation`,`ID_Enrichment`),
  KEY `ID_Enrichment` (`ID_Enrichment`),
  CONSTRAINT `IDGeneration1` FOREIGN KEY (`ID_Generation`) REFERENCES `generations` (`ID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
