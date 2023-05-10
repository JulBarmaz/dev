DROP TABLE IF EXISTS `#__banners`###qb_delimiter###
CREATE TABLE `#__banners` (
  `b_id` int(11) NOT NULL AUTO_INCREMENT,
  `b_client_id` int(11) NOT NULL DEFAULT '0',
  `b_cat_id` int(10) unsigned NOT NULL DEFAULT '0',
  `b_name` varchar(255) NOT NULL DEFAULT '',
  `b_alias` varchar(255) NOT NULL DEFAULT '',
  `b_show_total` int(11) NOT NULL DEFAULT '0',
  `b_show_made` int(11) NOT NULL DEFAULT '0',
  `b_clicks` int(11) NOT NULL DEFAULT '0',
  `b_image` varchar(100) NOT NULL DEFAULT '',
  `b_target` varchar(200) NOT NULL DEFAULT '',
  `b_custom_code` text NOT NULL,
  `b_descr` text NOT NULL,
  `b_sticky` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `b_ordering` int(11) NOT NULL DEFAULT '0',
  `b_width` int(4) NOT NULL DEFAULT '0',
  `b_height` int(4) NOT NULL DEFAULT '0',
  `b_publish_up` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `b_publish_down` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `b_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `b_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`b_id`),
  KEY `viewbanner` (`b_deleted`),
  KEY `idx_banner_catid` (`b_cat_id`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8###qb_delimiter###


DROP TABLE IF EXISTS `#__banners_categories`###qb_delimiter###
CREATE TABLE `#__banners_categories` (
  `bc_id` int(11) NOT NULL AUTO_INCREMENT,
  `bc_name` varchar(255) NOT NULL DEFAULT '',
  `bc_descr` text NOT NULL,
  `bc_published` tinyint(1) NOT NULL DEFAULT '1',
  `bc_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`bc_id`),
  KEY `cat_idx` (`bc_published`)
) ENGINE=MyISAM AUTO_INCREMENT=40 DEFAULT CHARSET=utf8###qb_delimiter###


DROP TABLE IF EXISTS `#__banners_clients`###qb_delimiter###
CREATE TABLE `#__banners_clients` (
  `bcl_id` int(11) NOT NULL AUTO_INCREMENT,
  `bcl_name` varchar(255) NOT NULL DEFAULT '',
  `bcl_contact` varchar(255) NOT NULL DEFAULT '',
  `bcl_email` varchar(255) NOT NULL DEFAULT '',
  `bcl_info` text NOT NULL,
  `bcl_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `bcl_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`bcl_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8###qb_delimiter###
