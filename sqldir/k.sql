
CREATE TABLE `user_setting` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT '0',
  `formaneme` varchar(20) DEFAULT NULL,
  `fieldname` varchar(20) DEFAULT NULL,
  `fieldvalue` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) 
