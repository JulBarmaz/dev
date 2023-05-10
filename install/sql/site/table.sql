DROP TABLE IF EXISTS `#__sitemap_man`###qb_delimiter###
CREATE TABLE `#__sitemap_man` (
  `m_id` int(11) NOT NULL AUTO_INCREMENT,
  `m_loc` varchar(250) DEFAULT NULL,
  `m_lastmod` date DEFAULT NULL,
  `m_changefreq` varchar(50) DEFAULT NULL,
  `m_priority` varchar(5) DEFAULT NULL,
  `m_deleted` tinyint(1) DEFAULT '0',
  `m_enabled` tinyint(1) DEFAULT '1',
  `m_parentid` int(11) DEFAULT NULL COMMENT 'parent tag from this table',
  `m_container` varchar(20) DEFAULT 'url' COMMENT 'url, image:image',
  `m_tag` varchar(50) DEFAULT NULL COMMENT 'image:loc',
  `m_title` varchar(250) DEFAULT NULL COMMENT 'title',
  `m_type` tinyint(1) DEFAULT '1' COMMENT '1 - include, 2 - exclude',
  `m_module` varchar(50) DEFAULT 'main' COMMENT 'module',
  PRIMARY KEY (`m_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8###qb_delimiter###
