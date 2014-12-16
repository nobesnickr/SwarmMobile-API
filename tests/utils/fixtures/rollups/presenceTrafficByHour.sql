DROP TABLE IF EXISTS `presenceTrafficByHour`;
CREATE TABLE `presenceTrafficByHour` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `location_id` int(10) unsigned NOT NULL,
  `total_open` int(10) unsigned NOT NULL DEFAULT '0',
  `total_close` int(10) unsigned NOT NULL DEFAULT '0',
  `total_total` int(10) unsigned NOT NULL DEFAULT '0',
  `h00` int(10) unsigned NOT NULL DEFAULT '0',
  `h01` int(10) unsigned NOT NULL DEFAULT '0',
  `h02` int(10) unsigned NOT NULL DEFAULT '0',
  `h03` int(10) unsigned NOT NULL DEFAULT '0',
  `h04` int(10) unsigned NOT NULL DEFAULT '0',
  `h05` int(10) unsigned NOT NULL DEFAULT '0',
  `h06` int(10) unsigned NOT NULL DEFAULT '0',
  `h07` int(10) unsigned NOT NULL DEFAULT '0',
  `h08` int(10) unsigned NOT NULL DEFAULT '0',
  `h09` int(10) unsigned NOT NULL DEFAULT '0',
  `h10` int(10) unsigned NOT NULL DEFAULT '0',
  `h11` int(10) unsigned NOT NULL DEFAULT '0',
  `h12` int(10) unsigned NOT NULL DEFAULT '0',
  `h13` int(10) unsigned NOT NULL DEFAULT '0',
  `h14` int(10) unsigned NOT NULL DEFAULT '0',
  `h15` int(10) unsigned NOT NULL DEFAULT '0',
  `h16` int(10) unsigned NOT NULL DEFAULT '0',
  `h17` int(10) unsigned NOT NULL DEFAULT '0',
  `h18` int(10) unsigned NOT NULL DEFAULT '0',
  `h19` int(10) unsigned NOT NULL DEFAULT '0',
  `h20` int(10) unsigned NOT NULL DEFAULT '0',
  `h21` int(10) unsigned NOT NULL DEFAULT '0',
  `h22` int(10) unsigned NOT NULL DEFAULT '0',
  `h23` int(10) unsigned NOT NULL DEFAULT '0',
  `ts_creation` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ts_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `location_date` (`location_id`,`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;