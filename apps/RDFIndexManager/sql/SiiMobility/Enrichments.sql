CREATE TABLE `enrichments` (
  `Name` varchar(255) NOT NULL,
  `Query` longtext,
  `Description` text DEFAULT NULL,
  PRIMARY KEY (`Name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
