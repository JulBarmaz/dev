DROP TABLE IF EXISTS `#__galleries`###qb_delimiter###
CREATE TABLE `#__galleries` (
  `g_id` int(11) NOT NULL AUTO_INCREMENT,
  `g_alias` varchar(255) NOT NULL DEFAULT '',
  `g_group_id` int(11) NOT NULL DEFAULT '0',
  `g_title` varchar(250) NOT NULL DEFAULT '',
  `g_thumb` varchar(64) NOT NULL DEFAULT '',
  `g_comment` text,
  `g_ordering` int(11) NOT NULL DEFAULT '0',
  `g_show_in_list` tinyint(1) NOT NULL DEFAULT '1',
  `g_hide_image_titles` tinyint(1) NOT NULL DEFAULT '0',
  `g_published` tinyint(1) NOT NULL DEFAULT '1',
  `g_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `g_alt_thm` varchar(255) DEFAULT NULL,
  `g_title_thm` varchar(255) DEFAULT NULL,
  `g_meta_title` varchar(250) NOT NULL DEFAULT '',
  `g_meta_description` varchar(250) NOT NULL DEFAULT '',
  `g_meta_keywords` varchar(250) NOT NULL DEFAULT '',
  `g_layout` varchar(100) DEFAULT NULL,
  `g_images_by_row` int(3) NOT NULL,
  `g_show_parent_descr` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`g_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8###qb_delimiter###


DROP TABLE IF EXISTS `#__gallery_groups`###qb_delimiter###
CREATE TABLE `#__gallery_groups` (
  `gr_id` int(11) NOT NULL AUTO_INCREMENT,
  `gr_alias` varchar(255) NOT NULL DEFAULT '',
  `gr_title` varchar(250) NOT NULL DEFAULT '',
  `gr_thumb` varchar(64) NOT NULL DEFAULT '',
  `gr_comment` text,
  `gr_ordering` int(11) NOT NULL DEFAULT '0',
  `gr_show_in_list` tinyint(1) NOT NULL DEFAULT '1',
  `gr_published` tinyint(1) NOT NULL DEFAULT '1',
  `gr_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `gr_title_thm` varchar(255) DEFAULT NULL,
  `gr_alt_thm` varchar(255) DEFAULT NULL,
  `gr_meta_title` varchar(250) NOT NULL DEFAULT '',
  `gr_meta_description` varchar(250) NOT NULL DEFAULT '',
  `gr_meta_keywords` varchar(250) NOT NULL DEFAULT '',
  `gr_layout` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`gr_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8###qb_delimiter###


DROP TABLE IF EXISTS `#__gallery_images`###qb_delimiter###
CREATE TABLE `#__gallery_images` (
  `gi_id` int(11) NOT NULL AUTO_INCREMENT,
  `gi_gallery_id` int(4) DEFAULT NULL,
  `gi_title` varchar(200) DEFAULT NULL,
  `gi_image` varchar(64) DEFAULT NULL,
  `gi_thumb` varchar(64) DEFAULT NULL,
  `gi_comment` text,
  `gi_published` tinyint(1) DEFAULT '0',
  `gi_ordering` int(11) NOT NULL DEFAULT '0',
  `gi_deleted` tinyint(1) DEFAULT '0',
  `gi_title_img` varchar(255) DEFAULT NULL,
  `gi_alt_img` varchar(255) DEFAULT NULL,
  `gi_title_thm` varchar(255) DEFAULT NULL,
  `gi_meta_title` varchar(250) NOT NULL DEFAULT '',
  `gi_meta_description` varchar(250) NOT NULL DEFAULT '',
  `gi_meta_keywords` varchar(250) NOT NULL DEFAULT '',
  `gi_alt_thm` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`gi_id`)
) ENGINE=MyISAM AUTO_INCREMENT=309 DEFAULT CHARSET=utf8###qb_delimiter###
