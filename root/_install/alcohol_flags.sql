CREATE TABLE `alcohol_flags` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `alcoholID` int(11) unsigned NOT NULL,
  `userID` int(11) unsigned NOT NULL,
  `content` varchar(250) NOT NULL DEFAULT '',
  `time` char(19) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8