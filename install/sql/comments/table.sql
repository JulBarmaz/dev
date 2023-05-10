DROP TABLE IF EXISTS `#__comms`###qb_delimiter###
CREATE TABLE `#__comms` (
  `cm_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cm_grp_id` varchar(32) NOT NULL,
  `cm_obj_id` int(11) NOT NULL DEFAULT '0',
  `cm_parent_id` int(11) NOT NULL DEFAULT '0',
  `cm_uid` int(11) NOT NULL DEFAULT '0',
  `cm_nickname` varchar(32) NOT NULL,
  `cm_email` varchar(50) NOT NULL,
  `cm_date` datetime NOT NULL,
  `cm_ip` varchar(15) NOT NULL,
  `cm_title` varchar(250) NOT NULL,
  `cm_text` text NOT NULL,
  `cm_rating` int(5) NOT NULL DEFAULT '0',
  `cm_cat` int(5) DEFAULT NULL,
  `cm_type` int(11) NOT NULL DEFAULT '0',
  `cm_published` tinyint(1) NOT NULL DEFAULT '0',
  `cm_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`cm_id`),
  KEY `mod_view_obj` (`cm_grp_id`,`cm_obj_id`),
  KEY `uid` (`cm_uid`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8###qb_delimiter###


DROP TABLE IF EXISTS `#__comms_acl`###qb_delimiter###
CREATE TABLE `#__comms_acl` (
  `ca_grp_id` int(11) NOT NULL,
  `ca_r_id` int(11) NOT NULL DEFAULT '0',
  `ca_action` varchar(32) NOT NULL,
  `ca_flag` int(1) DEFAULT '0',
  PRIMARY KEY (`ca_grp_id`,`ca_r_id`,`ca_action`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8###qb_delimiter###


DROP TABLE IF EXISTS `#__comms_cat`###qb_delimiter###
CREATE TABLE `#__comms_cat` (
  `cc_id` int(11) NOT NULL AUTO_INCREMENT,
  `cc_cgrp_id` int(11) DEFAULT NULL,
  `cc_title` varchar(150) NOT NULL,
  `cc_marker` int(5) DEFAULT NULL,
  `cc_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `cc_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`cc_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8###qb_delimiter###


DROP TABLE IF EXISTS `#__comms_grp`###qb_delimiter###
CREATE TABLE `#__comms_grp` (
  `cg_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cg_title` varchar(150) NOT NULL,
  `cg_module` varchar(32) NOT NULL,
  `cg_view` varchar(32) NOT NULL,
  `cg_tablename` varchar(32) NOT NULL,
  `cg_list_limit` tinyint(3) NOT NULL DEFAULT '20',
  `cg_text_limit` int(3) NOT NULL DEFAULT '1000',
  `cg_bbcode` tinyint(1) NOT NULL DEFAULT '1',
  `cg_premoderate` tinyint(1) NOT NULL DEFAULT '0',
  `cg_mailmoder` tinyint(1) NOT NULL DEFAULT '0',
  `cg_vote_obj` tinyint(1) NOT NULL DEFAULT '0',
  `cg_vote_comms` tinyint(1) NOT NULL DEFAULT '0',
  `cg_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `cg_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`cg_id`),
  UNIQUE KEY `module_view` (`cg_module`,`cg_view`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8###qb_delimiter###


DROP TABLE IF EXISTS `#__comms_types`###qb_delimiter###
CREATE TABLE `#__comms_types` (
  `ct_id` int(11) NOT NULL AUTO_INCREMENT,
  `ct_cgrp_id` int(11) DEFAULT NULL,
  `ct_title` varchar(150) NOT NULL,
  `ct_class` tinyint(2) NOT NULL DEFAULT '1',
  `ct_marker` varchar(50) DEFAULT NULL,
  `ct_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `ct_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ct_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8###qb_delimiter###
