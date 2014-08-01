CREATE TABLE `alcohol_subtypes` (
  `_id` tinyint(3) unsigned NOT NULL,
  `id` smallint(5) unsigned NOT NULL,
  `typeID` tinyint(1) NOT NULL,
  `name` char(20) NOT NULL,
  PRIMARY KEY (`_id`),
  KEY `_id` (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8