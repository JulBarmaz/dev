DROP TABLE IF EXISTS `#__forum_posts`###qb_delimiter###
CREATE TABLE `#__forum_posts` (
  `p_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `p_author_id` int(11) NOT NULL,
  `p_theme_id` int(11) NOT NULL DEFAULT '0',
  `p_theme` varchar(250) NOT NULL,
  `p_text` text NOT NULL,
  `p_date` datetime NOT NULL,
  `p_touch_date` datetime NOT NULL,
  `p_ip` varchar(15) NOT NULL,
  `p_rating` int(11) NOT NULL DEFAULT '0',
  `p_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `p_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`p_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8###qb_delimiter###


DROP TABLE IF EXISTS `#__forum_rights`###qb_delimiter###
CREATE TABLE `#__forum_rights` (
  `f_id` int(11) DEFAULT NULL,
  `u_id` int(11) DEFAULT '0',
  `r_id` int(11) DEFAULT '0',
  `action` varchar(55) DEFAULT NULL,
  `flag` int(1) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8###qb_delimiter###


DROP TABLE IF EXISTS `#__forum_sections`###qb_delimiter###
CREATE TABLE `#__forum_sections` (
  `f_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `f_parent_id` int(11) NOT NULL,
  `f_name` varchar(255) NOT NULL,
  `f_description` text,
  `f_meta_title` varchar(250) NOT NULL,
  `f_meta_description` varchar(250) NOT NULL,
  `f_meta_keywords` varchar(250) NOT NULL,
  `f_layout` varchar(20) NOT NULL DEFAULT 'default',
  `f_show_in_list` tinyint(1) NOT NULL DEFAULT '0',
  `f_post_rating` tinyint(1) NOT NULL DEFAULT '1',
  `f_premoderated` tinyint(1) NOT NULL DEFAULT '0',
  `f_ordering` int(3) NOT NULL DEFAULT '0',
  `f_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `f_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `f_alias` varchar(250) NOT NULL DEFAULT '',
	`f_thumb` varchar(250) NOT NULL DEFAULT '',
  PRIMARY KEY (`f_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8###qb_delimiter###


DROP TABLE IF EXISTS `#__forum_subscribers`###qb_delimiter###
CREATE TABLE `#__forum_subscribers` (
  `t_id` int(11) NOT NULL,
  `u_id` int(11) NOT NULL,
  PRIMARY KEY (`t_id`,`u_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8###qb_delimiter###


DROP TABLE IF EXISTS `#__forum_themes`###qb_delimiter###
CREATE TABLE `#__forum_themes` (
  `t_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `t_author_id` int(11) NOT NULL,
  `t_forum_id` int(11) NOT NULL DEFAULT '0',
  `t_theme` varchar(250) NOT NULL,
  `t_text` text NOT NULL,
  `t_date` datetime NOT NULL,
  `t_touch_date` datetime NOT NULL,
  `t_rating` int(11) NOT NULL DEFAULT '0',
  `t_tags` tinytext NOT NULL,
  `t_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `t_ip` varchar(15) NOT NULL,
  `t_views` int(11) NOT NULL DEFAULT '0',
  `t_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `t_fixed` tinyint(1) NOT NULL DEFAULT '0',
  `t_closed` tinyint(1) NOT NULL DEFAULT '0',
  `t_alias` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`t_id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8###qb_delimiter###
