DROP TABLE IF EXISTS `#__mail`###qb_delimiter###
CREATE TABLE `#__mail` (
  `l_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `l_sender_id` int(11) DEFAULT NULL,
  `l_reciever_id` int(11) DEFAULT NULL,
  `l_theme` varchar(155) DEFAULT NULL,
  `l_text` text,
  `l_date` datetime DEFAULT NULL,
  `l_read` int(1) DEFAULT '0',
  `l_deleted_s` int(1) DEFAULT '0',
  `l_deleted_r` int(1) DEFAULT '0',
  PRIMARY KEY (`l_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8###qb_delimiter###


DROP TABLE IF EXISTS `#__mailer_log`###qb_delimiter###
CREATE TABLE `#__mailer_log` (
  `email` varchar(50) DEFAULT NULL,
  `err_code` varchar(5) DEFAULT NULL,
  `err_text` varchar(250) DEFAULT NULL,
  `err_response` varchar(250) DEFAULT NULL,
  `err_time` datetime DEFAULT NULL,
  `m_theme` varchar(250) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8###qb_delimiter###
