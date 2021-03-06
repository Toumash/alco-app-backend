CREATE TABLE `users` (
  `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `LOGIN` varchar(255) NOT NULL,
  `EMAIL` text NOT NULL,
  `PASSWORD` varchar(255) NOT NULL,
  `PERMISSIONS` int(11) NOT NULL DEFAULT '1',
  `ACTIVATION` varchar(40) NOT NULL,
  `SEX` tinyint(1) NOT NULL DEFAULT '-1',
  `WEIGHT` double NOT NULL DEFAULT '-1',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8