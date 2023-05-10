DROP TABLE IF EXISTS `#__articles`###qb_delimiter###
CREATE TABLE `#__articles` (
  `a_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `a_parent_id` int(11) DEFAULT '0',
  `a_author_id` int(11) DEFAULT NULL,
  `a_date` datetime DEFAULT NULL,
  `a_title` varchar(200) DEFAULT NULL,
  `a_alias` varchar(255) DEFAULT NULL,
  `a_thumb` varchar(255) DEFAULT NULL,
  `a_text` longtext,
  `a_show_info` tinyint(1) NOT NULL DEFAULT '1',
  `a_show_in_contents` tinyint(1) NOT NULL DEFAULT '1',
  `a_show_childs` tinyint(1) NOT NULL DEFAULT '1',
  `a_show_title` tinyint(1) NOT NULL DEFAULT '1',
  `a_show_breadcrumb` tinyint(1) NOT NULL DEFAULT '1',
  `a_meta_title` varchar(250) NOT NULL DEFAULT '',
  `a_meta_description` varchar(250) NOT NULL DEFAULT '',
  `a_meta_keywords` varchar(250) NOT NULL DEFAULT '',
  `a_rating` int(11) NOT NULL DEFAULT '0',
  `a_ordering` int(11) NOT NULL DEFAULT '0',
  `a_published` tinyint(1) NOT NULL DEFAULT '1',
  `a_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `a_title_thm` varchar(255) NOT NULL DEFAULT '',
  `a_alt_thm` varchar(255) NOT NULL DEFAULT '',
  `a_childs_order_by` varchar(25) NOT NULL DEFAULT 'a_ordering',
  `a_childs_order_dir` varchar(4) NOT NULL DEFAULT 'ASC',
  PRIMARY KEY (`a_id`)
) ENGINE=MyISAM AUTO_INCREMENT=67 DEFAULT CHARSET=utf8###qb_delimiter###

