DROP TABLE IF EXISTS `#__blogs`###qb_delimiter###
CREATE TABLE `#__blogs` (
  `b_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `b_name` varchar(255) NOT NULL,
  `b_alias` varchar(255) NOT NULL DEFAULT '',
  `b_description` text,
  `b_meta_title` varchar(250) NOT NULL,
  `b_meta_description` varchar(250) NOT NULL,
  `b_meta_keywords` varchar(250) NOT NULL,
  `b_thumb` varchar(250) DEFAULT NULL,
  `b_title_thm` varchar(255) DEFAULT NULL,
  `b_alt_thm` varchar(255) DEFAULT NULL,
  `b_porder_by` varchar(25) NOT NULL DEFAULT 'p_date',
  `b_porder_dir` varchar(4) NOT NULL DEFAULT 'DESC',
  `b_corder_dir` varchar(4) NOT NULL DEFAULT 'ASC',
  `b_layout` varchar(20) NOT NULL DEFAULT 'default',
  `b_show_in_list` tinyint(1) NOT NULL DEFAULT '0',
  `b_post_rating` tinyint(1) NOT NULL DEFAULT '1',
  `b_comments_rating` tinyint(1) NOT NULL DEFAULT '1',
  `b_premoderated` tinyint(1) NOT NULL DEFAULT '0',
  `b_guieditor` tinyint(1) NOT NULL DEFAULT '1',
  `b_hide_properties` tinyint(1) NOT NULL DEFAULT '0',
  `b_hide_comments` tinyint(1) NOT NULL DEFAULT '0',
  `b_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `b_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`b_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8###qb_delimiter###


DROP TABLE IF EXISTS `#__blogs_cats`###qb_delimiter###
CREATE TABLE `#__blogs_cats` (
  `bc_id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ид группы',
  `bc_id_parent` int(10) NOT NULL DEFAULT '0' COMMENT 'ид родителя',
  `bc_name` varchar(50) NOT NULL DEFAULT '0' COMMENT 'наименование группы',
  `bc_alias` varchar(255) NOT NULL DEFAULT '',
  `bc_comment` text COMMENT 'описание группы',
  `bc_meta_title` varchar(250) NOT NULL,
  `bc_meta_description` varchar(250) NOT NULL,
  `bc_meta_keywords` varchar(250) NOT NULL,
  `bc_ordering` tinyint(5) NOT NULL DEFAULT '0',
  `bc_enabled` smallint(1) NOT NULL DEFAULT '1',
  `bc_deleted` smallint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`bc_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8###qb_delimiter###


DROP TABLE IF EXISTS `#__blogs_comments`###qb_delimiter###
CREATE TABLE `#__blogs_comments` (
  `cm_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cm_post_id` int(11) DEFAULT NULL,
  `cm_parent_id` int(11) DEFAULT '0',
  `cm_author_id` int(11) DEFAULT NULL,
  `cm_date` datetime DEFAULT NULL,
  `cm_text` text,
  `cm_rating` int(5) DEFAULT '0',
  `cm_deleted` int(1) DEFAULT '0',
  PRIMARY KEY (`cm_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8###qb_delimiter###


DROP TABLE IF EXISTS `#__blogs_links`###qb_delimiter###
CREATE TABLE `#__blogs_links` (
  `b_id` varchar(50) NOT NULL COMMENT 'код элемента',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `parent_id` int(10) NOT NULL DEFAULT '0' COMMENT 'ид группы',
  PRIMARY KEY (`b_id`,`parent_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8###qb_delimiter###


DROP TABLE IF EXISTS `#__blogs_posts`###qb_delimiter###
CREATE TABLE `#__blogs_posts` (
  `p_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `p_author_id` int(11) DEFAULT NULL,
  `p_blog_id` int(11) DEFAULT '0',
  `p_theme` varchar(250) DEFAULT NULL,
  `p_alias` varchar(255) NOT NULL DEFAULT '',
  `p_text` text,
  `p_date` datetime DEFAULT NULL,
  `p_touch_date` datetime DEFAULT NULL,
  `p_comments` int(8) DEFAULT '0',
  `p_rating` int(5) DEFAULT '0',
  `p_tags` tinytext,
  `p_enabled` tinyint(1) DEFAULT '0',
  `p_deleted` tinyint(1) DEFAULT '0',
  `p_closed` tinyint(1) DEFAULT '0',
  `p_thumb` varchar(250) DEFAULT NULL,
  `p_title_thm` varchar(250) DEFAULT NULL,
  `p_alt_thm` varchar(250) DEFAULT NULL,
  `p_meta_title` varchar(250) DEFAULT NULL,
  `p_meta_description` varchar(250) DEFAULT NULL,
  `p_meta_keywords` varchar(250) DEFAULT NULL,
  `p_ordering` int(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`p_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8###qb_delimiter###


DROP TABLE IF EXISTS `#__blogs_rights`###qb_delimiter###
CREATE TABLE `#__blogs_rights` (
  `b_id` int(11) DEFAULT NULL,
  `u_id` int(11) DEFAULT '0',
  `r_id` int(11) DEFAULT '0',
  `action` varchar(55) DEFAULT NULL,
  `flag` int(1) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8###qb_delimiter###
