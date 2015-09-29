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
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `ontologies` (
  `Name` varchar(255) CHARACTER SET latin1 NOT NULL,
  `URIPrefix` text CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`Name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `ontologies_generations` (
  `ID_Generation` int(10) NOT NULL,
  `ID_Ontology` varchar(255) NOT NULL,
  `TripleDate` datetime NOT NULL,
  `Clone` INT(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID_Generation`,`ID_Ontology`),
   CONSTRAINT `IDOntology1` FOREIGN KEY (`ID_Ontology`) REFERENCES `ontologies` (`Name`),
  CONSTRAINT `OntologiesIDGeneration` FOREIGN KEY (`ID_Generation`) REFERENCES `generations` (`ID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `opendata_generations` (
  `ID_Generation` int(10) NOT NULL,
  `ID_OpenData` text COLLATE utf8_unicode_ci NOT NULL,
  `TripleStart` text COLLATE utf8_unicode_ci NOT NULL,
  `TripleEnd` text COLLATE utf8_unicode_ci NOT NULL,
  `Clone` INT(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID_Generation`,`ID_OpenData`(100)),
  CONSTRAINT `IDOpenData` FOREIGN KEY (`ID_OpenData`) REFERENCES `process_manager2` (`process`),
  CONSTRAINT `OpenDataIDGeneration` FOREIGN KEY (`ID_Generation`) REFERENCES `generations` (`ID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `reconciliations` (
  `Name` varchar(255) NOT NULL,
  `Macroclasses` longtext,
  `Triples` varchar(50) DEFAULT NULL,
  `Description` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`Name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `reconciliations_generations` (
  `ID_Generation` int(10) NOT NULL,
  `ID_Reconciliation` varchar(50) NOT NULL,
  `TripleDate` datetime NOT NULL,
  `Clone` INT(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID_Generation`,`ID_Reconciliation`),
  CONSTRAINT `IDReconciliation` FOREIGN KEY (`ID_Reconciliation`) REFERENCES `Reconciliations` (`Name`),
  CONSTRAINT `ReconciliationsIDGeneration` FOREIGN KEY (`ID_Generation`) REFERENCES `generations` (`ID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `Generations` 
ADD COLUMN `SecurityLevel` INT(1) NOT NULL DEFAULT 3 AFTER `Version`;
