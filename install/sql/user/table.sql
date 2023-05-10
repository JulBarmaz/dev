DROP TABLE IF EXISTS `#__blacklist`###qb_delimiter###
CREATE TABLE `#__blacklist` (
  `bl_id` int(11) NOT NULL AUTO_INCREMENT,
  `bl_val` varchar(255) NOT NULL,
  `bl_type` varchar(20) NOT NULL,
  `bl_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `bl_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`bl_id`),
  UNIQUE KEY `uni` (`bl_val`,`bl_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8###qb_delimiter###
DROP TABLE IF EXISTS `#__bonus_list`###qb_delimiter###
CREATE TABLE `#__bonus_list` (
  `b_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `b_uid` int(11) DEFAULT NULL,
  `b_oper` int(11) DEFAULT NULL,
  `b_date` datetime DEFAULT NULL,
  `b_sum` int(11) DEFAULT NULL,
  PRIMARY KEY (`b_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8###qb_delimiter###
DROP TABLE IF EXISTS `#__bonus_type`###qb_delimiter###
CREATE TABLE `#__bonus_type` (
  `b_id` int(10) DEFAULT NULL,
  `b_name` varchar(100) DEFAULT NULL,
  `b_price` int(4) DEFAULT NULL,
  `b_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `b_deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8###qb_delimiter###
DROP TABLE IF EXISTS `#__profiles`###qb_delimiter###
CREATE TABLE `#__profiles` (
  `pf_id` int(11) unsigned NOT NULL,
  `pf_deleted` int(1) DEFAULT '0',
  `pf_pollkey` varchar(20) DEFAULT NULL,
  `pf_age` varchar(3) DEFAULT NULL,
  `pf_sex` tinyint(1) DEFAULT NULL,
  `pf_site` varchar(50) DEFAULT NULL,
  `pf_icq` varchar(12) DEFAULT NULL,
  `pf_skype` varchar(50) DEFAULT NULL,
  `pf_jabber` varchar(50) DEFAULT NULL,
  `pf_img` varchar(250) DEFAULT NULL,
  `pf_text` text,
  PRIMARY KEY (`pf_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8###qb_delimiter###


DROP TABLE IF EXISTS `#__profiles_data`###qb_delimiter###
CREATE TABLE `#__profiles_data` (
  `obj_id` varchar(50) NOT NULL COMMENT 'код товара',
  `field_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'ид поля свойства',
  `field_name` varchar(50) NOT NULL COMMENT 'название поля из метадаты',
  `field_value` text NOT NULL COMMENT 'поля свойства',
  PRIMARY KEY (`obj_id`,`field_name`),
  KEY `field_id` (`field_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8###qb_delimiter###

DROP TABLE IF EXISTS `#__users`###qb_delimiter###
CREATE TABLE `#__users` (
  `u_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `u_affiliate_code` varchar(12) NOT NULL DEFAULT '0',
  `u_referral` varchar(12) NOT NULL DEFAULT '0',
  `u_login` varchar(50) NOT NULL,
  `u_secret` varchar(255) DEFAULT NULL,
  `u_email` varchar(100) DEFAULT NULL,
  `u_reg_date` datetime DEFAULT NULL,
  `u_nickname` varchar(50) DEFAULT NULL,
  `u_account` float(19,4) DEFAULT '0.0000',
  `u_points` float(19,4) DEFAULT '0.0000',
  `u_discount` float(19,4) DEFAULT '0.0000',
  `u_pricetype` int(11) DEFAULT '1',
  `u_role` int(11) DEFAULT NULL,
  `u_rating` int(5) DEFAULT '0',
  `u_activated` int(1) DEFAULT '0',
  `u_deleted` int(1) DEFAULT '0',
  `u_validation` varchar(50) DEFAULT NULL,
  `u_source` varchar(10) NOT NULL DEFAULT 'system',
  `u_last_visit` datetime DEFAULT CURRENT_TIMESTAMP,
  `u_login_date` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`u_id`),
  UNIQUE KEY `Affiliate` (`u_affiliate_code`),
  UNIQUE KEY `u_login` (`u_login`),
  UNIQUE KEY `u_email` (`u_email`),
  UNIQUE KEY `u_nickname` (`u_nickname`)
) ENGINE=MyISAM AUTO_INCREMENT=37 DEFAULT CHARSET=utf8###qb_delimiter###


DROP TABLE IF EXISTS `#__users_addr`###qb_delimiter###
CREATE TABLE `#__users_addr` (
  `a_id` int(11) NOT NULL AUTO_INCREMENT,
  `a_uid` int(11) NOT NULL DEFAULT '0',
  `a_type` tinyint(1) NOT NULL DEFAULT '0',
  `a_default` tinyint(1) NOT NULL DEFAULT '0',
  `a_data` blob,
  PRIMARY KEY (`a_id`,`a_uid`),
  UNIQUE KEY `d_id` (`a_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC###qb_delimiter###


DROP TABLE IF EXISTS `#__users_bank`###qb_delimiter###
CREATE TABLE `#__users_bank` (
  `b_id` int(11) NOT NULL AUTO_INCREMENT,
  `b_uid` int(11) NOT NULL DEFAULT '0',
  `b_default` tinyint(1) NOT NULL DEFAULT '0',
  `b_data` blob,
  PRIMARY KEY (`b_id`,`b_uid`),
  UNIQUE KEY `d_id` (`b_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8###qb_delimiter###


DROP TABLE IF EXISTS `#__users_company`###qb_delimiter###
CREATE TABLE `#__users_company` (
  `c_id` int(11) NOT NULL,
  `c_data` blob,
  PRIMARY KEY (`c_id`),
  UNIQUE KEY `d_id` (`c_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8###qb_delimiter###


