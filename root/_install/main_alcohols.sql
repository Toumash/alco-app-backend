CREATE TABLE `main_alcohols` (
  `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `NAME` varchar(255) NOT NULL DEFAULT '',
  `TYPE` int(3) unsigned NOT NULL DEFAULT '1',
  `SUBTYPE` int(3) unsigned NOT NULL DEFAULT '1',
  `PRICE` double unsigned DEFAULT NULL,
  `VOLUME` int(4) unsigned NOT NULL DEFAULT '0',
  `PERCENT` double unsigned DEFAULT NULL,
  `DEPOSIT` int(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8