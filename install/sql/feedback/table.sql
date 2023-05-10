DROP TABLE IF EXISTS `#__feedback`###qb_delimiter###
CREATE TABLE `#__feedback` (
  `f_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `f_sender` varchar(255) NOT NULL,
  `f_uid` int(11) NOT NULL DEFAULT '0',
  `f_mail` varchar(50) NOT NULL,
  `f_ip` varchar(15) NOT NULL,
  `f_theme` varchar(255) NOT NULL,
  `f_text` text NOT NULL,
  `f_date` datetime NOT NULL,
  `f_sent` tinyint(1) NOT NULL DEFAULT '0',
  `f_read` tinyint(1) NOT NULL DEFAULT '0',
  `f_readdate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `f_comments` text,
  `f_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`f_id`)
) ENGINE=MyISAM AUTO_INCREMENT=46 DEFAULT CHARSET=utf8###qb_delimiter###


DROP TABLE IF EXISTS `#__feedback_data`###qb_delimiter###
CREATE TABLE `#__feedback_data` (
  `obj_id` int(11) NOT NULL COMMENT 'ид объекта',
  `field_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'ид поля свойства',
  `field_name` varchar(50) NOT NULL DEFAULT '' COMMENT 'название поля из метадаты',
  `field_value` text NOT NULL COMMENT 'поля свойства',
  PRIMARY KEY (`obj_id`,`field_name`),
  KEY `field_id` (`field_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8###qb_delimiter###
