CREATE TABLE `api_tokens` (
  `token` char(40) NOT NULL,
  `hits` int(11) NOT NULL,
  `today_hits` int(11) NOT NULL,
  `day` char(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8