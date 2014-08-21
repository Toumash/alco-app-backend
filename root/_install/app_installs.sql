CREATE TABLE `app_installs` (
  `id` char(38) NOT NULL COMMENT 'install id 36 UUID java',
  `updates` smallint(6) NOT NULL DEFAULT '0',
  `date` char(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8