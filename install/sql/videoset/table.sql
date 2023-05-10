DROP TABLE IF EXISTS `#__videoset_galleries`###qb_delimiter###
CREATE TABLE `#__videoset_galleries` (
  `vg_id` int(11) NOT NULL AUTO_INCREMENT,
  `vg_alias` varchar(50) NOT NULL DEFAULT '',
  `vg_group_id` int(11) NOT NULL DEFAULT '0',
  `vg_title` varchar(250) NOT NULL DEFAULT '',
  `vg_thumb` varchar(64) NOT NULL DEFAULT '',
  `vg_comment` text,
  `vg_ordering` int(11) NOT NULL DEFAULT '0',
  `vg_show_in_list` tinyint(1) NOT NULL DEFAULT '1',
  `vg_published` tinyint(1) NOT NULL DEFAULT '1',
  `vg_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `vg_alt_thm` varchar(255) DEFAULT NULL,
  `vg_title_thm` varchar(255) DEFAULT NULL,
  `vg_meta_title` varchar(250) NOT NULL DEFAULT '',
  `vg_meta_description` varchar(250) NOT NULL DEFAULT '',
  `vg_meta_keywords` varchar(250) NOT NULL DEFAULT '',
  PRIMARY KEY (`vg_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8###qb_delimiter###


DROP TABLE IF EXISTS `#__videoset_groups`###qb_delimiter###
CREATE TABLE `#__videoset_groups` (
  `vgr_id` int(11) NOT NULL AUTO_INCREMENT,
  `vgr_alias` varchar(50) NOT NULL DEFAULT '',
  `vgr_title` varchar(250) NOT NULL DEFAULT '',
  `vgr_thumb` varchar(64) NOT NULL DEFAULT '',
  `vgr_comment` text,
  `vgr_ordering` int(11) NOT NULL DEFAULT '0',
  `vgr_show_in_list` tinyint(1) NOT NULL DEFAULT '1',
  `vgr_published` tinyint(1) NOT NULL DEFAULT '1',
  `vgr_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `vgr_title_thm` varchar(255) DEFAULT NULL,
  `vgr_alt_thm` varchar(255) DEFAULT NULL,
  `vgr_meta_title` varchar(250) NOT NULL DEFAULT '',
  `vgr_meta_description` varchar(250) NOT NULL DEFAULT '',
  `vgr_meta_keywords` varchar(250) NOT NULL DEFAULT '',
  PRIMARY KEY (`vgr_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8###qb_delimiter###


DROP TABLE IF EXISTS `#__videoset_videos`###qb_delimiter###
CREATE TABLE `#__videoset_videos` (
  `v_id` int(11) NOT NULL AUTO_INCREMENT,
  `v_gallery_id` int(4) DEFAULT NULL,
  `v_title` varchar(200) DEFAULT NULL,
  `v_image` varchar(64) DEFAULT NULL,
  `v_title_img` varchar(255) DEFAULT NULL,
  `v_meta_title` varchar(250) NOT NULL DEFAULT '',
  `v_meta_description` varchar(250) NOT NULL DEFAULT '',
  `v_meta_keywords` varchar(250) NOT NULL DEFAULT '',
  `v_alt_img` varchar(255) DEFAULT NULL,
  `v_video_youtube` varchar(250) NOT NULL DEFAULT '',
  `v_video_ogg` varchar(250) NOT NULL DEFAULT '',
  `v_video_mp4` varchar(250) NOT NULL DEFAULT '',
  `v_video_webm` varchar(250) NOT NULL DEFAULT '',
  `v_comment` text,
  `v_published` tinyint(1) DEFAULT '0',
  `v_ordering` int(11) NOT NULL DEFAULT '0',
  `v_deleted` tinyint(1) DEFAULT '0',
  `v_alias` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`v_id`)
) ENGINE=MyISAM AUTO_INCREMENT=313 DEFAULT CHARSET=utf8###qb_delimiter###

