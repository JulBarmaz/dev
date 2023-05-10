DROP TABLE IF EXISTS `#__poll_items`###qb_delimiter###
CREATE TABLE `#__poll_items` (
  `pi_id` int(11) NOT NULL AUTO_INCREMENT,
  `pi_poll_id` int(11) NOT NULL DEFAULT '0',
  `pi_text` varchar(255) NOT NULL,
  `pi_hits` int(11) NOT NULL DEFAULT '0',
  `pi_ordering` tinyint(3) NOT NULL DEFAULT '0',
  `pi_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`pi_id`),
  KEY `pollid` (`pi_poll_id`,`pi_text`(1))
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8###qb_delimiter###


DROP TABLE IF EXISTS `#__poll_stats`###qb_delimiter###
CREATE TABLE `#__poll_stats` (
  `ps_voter` varchar(20) NOT NULL COMMENT 'Unique id of user(not u_id)',
  `ps_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ps_item_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ps_voter`,`ps_item_id`)
) ENGINE=MyISAM AUTO_INCREMENT=52 DEFAULT CHARSET=utf8###qb_delimiter###


DROP TABLE IF EXISTS `#__polls`###qb_delimiter###
CREATE TABLE `#__polls` (
  `p_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `p_title` varchar(255) NOT NULL,
  `p_alias` varchar(255) NOT NULL,
  `p_lag` int(11) NOT NULL DEFAULT '86400',
  `p_startdate` datetime NOT NULL,
  `p_enddate` datetime NOT NULL,
  `p_comments` text NOT NULL,
  `p_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `p_enabled` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`p_id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8###qb_delimiter###
DROP TABLE IF EXISTS `#__votes`###qb_delimiter###
CREATE TABLE `#__votes` (
  `v_uid` int(11) NOT NULL COMMENT 'id юзера',
  `v_type` tinyint(2) NOT NULL COMMENT '1-users,2-articles,3-blog posts,4-blog comments,',
  `v_eid` int(11) NOT NULL COMMENT 'id элемента',
  PRIMARY KEY (`v_uid`,`v_type`,`v_eid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8###qb_delimiter###
