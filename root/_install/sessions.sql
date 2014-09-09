CREATE TABLE `sessions` (
  `token`     CHAR(36)             NOT NULL,
  `userID`    INT(10) UNSIGNED     NOT NULL,
  `installID` INT(10) UNSIGNED     NOT NULL,
  `_id`       SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`_id`)
)
  ENGINE =InnoDB
  DEFAULT CHARSET =latin1