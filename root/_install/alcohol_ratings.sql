CREATE TABLE `alcohol_ratings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `alcoholID` int(11) unsigned NOT NULL,
  `userID` int(11) unsigned NOT NULL,
  `content` varchar(250) NOT NULL DEFAULT '',
  `time` char(19) DEFAULT NULL,
  `rate` tinyint(4) NOT NULL DEFAULT '5',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=utf8