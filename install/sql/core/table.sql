SET NAMES 'UTF8'###qb_delimiter###
DROP TABLE IF EXISTS `#__acl_objects`###qb_delimiter###
CREATE TABLE `#__acl_objects` (
  `ao_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ao_name` varchar(50) NOT NULL,
  `ao_module_name` varchar(25) NOT NULL DEFAULT 'common_rules',
  `ao_description` varchar(255) NOT NULL,
  `ao_ordering` int(11) NOT NULL DEFAULT '0',
  `ao_is_admin` int(1) DEFAULT '0',
  PRIMARY KEY (`ao_id`),
  UNIQUE KEY `module_name` (`ao_name`,`ao_module_name`,`ao_is_admin`)
) ENGINE=MyISAM AUTO_INCREMENT=321 DEFAULT CHARSET=utf8###qb_delimiter###
DROP TABLE IF EXISTS `#__acl_roles`###qb_delimiter###
CREATE TABLE `#__acl_roles` (
  `ar_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ar_name` varchar(50) NOT NULL,
  `ar_title` varchar(50) NOT NULL,
  `ar_admin` int(1) DEFAULT '0',
  `ar_active` int(1) NOT NULL DEFAULT '1',
  `ar_system` int(1) NOT NULL DEFAULT '1',
  `ar_deleted` int(1) DEFAULT '0',
  PRIMARY KEY (`ar_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8###qb_delimiter###
DROP TABLE IF EXISTS `#__acl_rules`###qb_delimiter###
CREATE TABLE `#__acl_rules` (
  `acl_object_id` int(11) NOT NULL,
  `acl_role_id` int(11) NOT NULL,
  `acl_access` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`acl_object_id`,`acl_role_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8###qb_delimiter###

DROP TABLE IF EXISTS `#__addr_countries`###qb_delimiter###
CREATE TABLE `#__addr_countries` (
  `c_id` int(11) NOT NULL AUTO_INCREMENT,
  `c_name` varchar(64) NOT NULL,
  `c_alpha_2` varchar(2) NOT NULL,
  `c_alpha_3` varchar(3) NOT NULL,
  `c_descr` varchar(255) NOT NULL,
  `c_ordering` int(11) NOT NULL DEFAULT '100',
  `c_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `c_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`c_id`),
  UNIQUE KEY `alpha_2` (`c_alpha_2`),
  UNIQUE KEY `alpha_3` (`c_alpha_3`),
  KEY `idx_country_name` (`c_name`)
) ENGINE=MyISAM AUTO_INCREMENT=895 DEFAULT CHARSET=utf8###qb_delimiter###


DROP TABLE IF EXISTS `#__addr_districts`###qb_delimiter###
CREATE TABLE `#__addr_districts` (
  `d_id` int(11) NOT NULL AUTO_INCREMENT,
  `d_parent_id` int(11) NOT NULL DEFAULT '0',
  `d_name` varchar(64) NOT NULL,
  `d_long` float(14,7) NOT NULL DEFAULT '0.0000000',
  `d_lat` float(14,7) NOT NULL DEFAULT '0.0000000',
  `d_show_on_map` int(1) NOT NULL DEFAULT '0',
  `d_ordering` int(11) NOT NULL DEFAULT '0',
  `d_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `d_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`d_id`)
) ENGINE=MyISAM AUTO_INCREMENT=179 DEFAULT CHARSET=utf8###qb_delimiter###


DROP TABLE IF EXISTS `#__addr_localities`###qb_delimiter###
CREATE TABLE `#__addr_localities` (
  `l_id` int(11) NOT NULL AUTO_INCREMENT,
  `l_parent_id` int(11) NOT NULL DEFAULT '0',
  `l_name` varchar(64) NOT NULL,
  `l_long` float(14,7) NOT NULL DEFAULT '0.0000000',
  `l_lat` float(14,7) NOT NULL DEFAULT '0.0000000',
  `l_show_on_map` int(1) NOT NULL DEFAULT '0',
  `l_ordering` int(11) NOT NULL DEFAULT '0',
  `l_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `l_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`l_id`)
) ENGINE=MyISAM AUTO_INCREMENT=183 DEFAULT CHARSET=utf8###qb_delimiter###


DROP TABLE IF EXISTS `#__addr_regions`###qb_delimiter###
CREATE TABLE `#__addr_regions` (
  `r_id` int(11) NOT NULL AUTO_INCREMENT,
  `r_parent_id` int(11) NOT NULL DEFAULT '0',
  `r_name` varchar(64) NOT NULL,
  `r_ordering` int(11) NOT NULL DEFAULT '0',
  `r_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `r_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`r_id`),
  KEY `idx_country_id` (`r_parent_id`)
) ENGINE=MyISAM AUTO_INCREMENT=286 DEFAULT CHARSET=utf8###qb_delimiter###

DROP TABLE IF EXISTS `#__admin_menus`###qb_delimiter###
CREATE TABLE `#__admin_menus` (
  `mnu_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mnu_parent_id` int(11) NOT NULL DEFAULT '0',
  `mnu_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `mnu_link` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `mnu_module` varchar(50) NOT NULL,
  `mnu_order` int(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`mnu_id`)
) ENGINE=MyISAM AUTO_INCREMENT=129 DEFAULT CHARSET=utf8###qb_delimiter###

DROP TABLE IF EXISTS `#__auth_providers`###qb_delimiter###
CREATE TABLE `#__auth_providers` (
  `sn_id` int(11) NOT NULL AUTO_INCREMENT,
  `sn_name` varchar(30) NOT NULL DEFAULT '',
  `sn_key` varchar(255) NOT NULL DEFAULT '',
  `sn_secret` varchar(255) NOT NULL DEFAULT '',
  `sn_ordering` int(11) NOT NULL DEFAULT '0',
  `sn_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `sn_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`sn_id`),
  UNIQUE KEY `c_sn_providers_UN` (`sn_name`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8###qb_delimiter###

DROP TABLE IF EXISTS `#__config`###qb_delimiter###
CREATE TABLE `#__config` (
  `cfg_section` varchar(25) NOT NULL DEFAULT '',
  `cfg_key` varchar(255) NOT NULL,
  `cfg_value` text,
  PRIMARY KEY (`cfg_section`,`cfg_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8###qb_delimiter###

DROP TABLE IF EXISTS `#__fields_choices`###qb_delimiter###
CREATE TABLE `#__fields_choices` (
  `fc_id` int(11) NOT NULL AUTO_INCREMENT,
  `fc_field_id` int(11) NOT NULL DEFAULT '0',
  `fc_value` varchar(255) NOT NULL DEFAULT '',
  `fc_ordering` int(11) NOT NULL DEFAULT '0',
  `fc_enabled` int(1) NOT NULL DEFAULT '1',
  `fc_deleted` int(1) NOT NULL DEFAULT '0',
  `fc_extcode` varchar(36) NOT NULL DEFAULT '',
  PRIMARY KEY (`fc_id`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8###qb_delimiter###


DROP TABLE IF EXISTS `#__fields_groups`###qb_delimiter###
CREATE TABLE `#__fields_groups` (
  `fg_id` int(11) NOT NULL AUTO_INCREMENT,
  `fg_name` varchar(100) NOT NULL DEFAULT '',
  `fg_enabled` int(1) NOT NULL DEFAULT '1',
  `fg_deleted` int(1) NOT NULL DEFAULT '0',
  `fg_comment` text NOT NULL,
  PRIMARY KEY (`fg_id`),
  UNIQUE KEY `c_fields_groups_UN` (`fg_name`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8###qb_delimiter###


DROP TABLE IF EXISTS `#__fields_list`###qb_delimiter###
CREATE TABLE `#__fields_list` (
  `f_id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ид',
  `f_group` int(11) NOT NULL DEFAULT '0',
  `f_name` varchar(255) NOT NULL DEFAULT '' COMMENT 'название параметра',
  `f_descr` varchar(255) DEFAULT NULL COMMENT 'Название поля',
  `f_default` text NOT NULL COMMENT 'значение по умолчанию',
  `f_type` mediumint(10) NOT NULL COMMENT 'тип параметра(связь)',
  `f_writeable` smallint(5) NOT NULL DEFAULT '90' COMMENT 'возможность изменять для роли',
  `f_required` smallint(1) NOT NULL DEFAULT '0',
  `f_deleted` smallint(1) NOT NULL DEFAULT '0' COMMENT 'признак выключения',
  `f_table` varchar(50) DEFAULT 'goods' COMMENT 'имя модуля в котором используем',
  `f_custom` smallint(1) NOT NULL DEFAULT '0',
  `f_extcode` varchar(36) NOT NULL DEFAULT '',
  PRIMARY KEY (`f_id`),
  UNIQUE KEY `f_fieldname` (`f_name`,`f_table`),
  KEY `name` (`f_name`),
  KEY `disabled` (`f_deleted`),
  KEY `f_table` (`f_table`)
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=utf8 PACK_KEYS=0###qb_delimiter###


DROP TABLE IF EXISTS `#__fields_type`###qb_delimiter###
CREATE TABLE `#__fields_type` (
  `t_id` int(10) NOT NULL,
  `t_val_type` varchar(50) NOT NULL,
  `t_input_type` varchar(50) NOT NULL,
  `t_name` varchar(50) NOT NULL,
  PRIMARY KEY (`t_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8###qb_delimiter###


DROP TABLE IF EXISTS `#__filters`###qb_delimiter###
CREATE TABLE `#__filters` (
  `f_time` int(11) NOT NULL,
  `f_uid` varchar(50) NOT NULL DEFAULT '0' COMMENT 'ид пользователя',
  `f_module` varchar(30) NOT NULL DEFAULT '' COMMENT 'модуль',
  `f_view` varchar(30) NOT NULL DEFAULT '' COMMENT 'view(таблица)',
  `f_layout` varchar(30) NOT NULL DEFAULT '',
  `f_side` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-front,1-admin',
  `f_key` varchar(20) NOT NULL DEFAULT '' COMMENT 'поле фильтра',
  `f_val` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`f_uid`,`f_module`,`f_view`,`f_layout`,`f_side`,`f_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC###qb_delimiter###

DROP TABLE IF EXISTS `#__install`###qb_delimiter###
CREATE TABLE `#__install` (
  `c_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `c_type` enum('module','widget','plugin','template','langpack') DEFAULT NULL,
  `c_name` varchar(255) DEFAULT NULL,
  `c_version` varchar(25) DEFAULT NULL,
  `c_description` varchar(255) DEFAULT NULL,
  `c_author` varchar(100) DEFAULT NULL,
  `c_email` varchar(100) DEFAULT NULL,
  `c_site` varchar(100) DEFAULT NULL,
  `c_license` text,
  `c_data` text,
  PRIMARY KEY (`c_id`),
  UNIQUE KEY `tn` (`c_type`,`c_name`)
) ENGINE=MyISAM AUTO_INCREMENT=36 DEFAULT CHARSET=utf8###qb_delimiter###

DROP TABLE IF EXISTS `#__md_btns`###qb_delimiter###
CREATE TABLE `#__md_btns` (
  `b_id` int(11) NOT NULL AUTO_INCREMENT,
  `b_hid` int(11) NOT NULL,
  `b_unique` tinyint(1) NOT NULL DEFAULT '0',
  `b_name` varchar(50) NOT NULL,
  `b_title` varchar(100) NOT NULL,
  `b_show` tinyint(1) NOT NULL DEFAULT '0',
  `b_module` varchar(50) NOT NULL,
  `b_view` varchar(50) NOT NULL,
  `b_layout` varchar(50) NOT NULL,
  `b_task` varchar(50) NOT NULL,
  `b_link` varchar(50) NOT NULL,
  `b_img` varchar(50) NOT NULL,
  PRIMARY KEY (`b_id`),
  UNIQUE KEY `btn_name` (`b_hid`,`b_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8###qb_delimiter###


DROP TABLE IF EXISTS `#__md_flds`###qb_delimiter###
CREATE TABLE `#__md_flds` (
  `f_id` int(11) NOT NULL AUTO_INCREMENT,
  `f_hid` int(11) NOT NULL DEFAULT '0',
  `f_name` varchar(25) NOT NULL COMMENT 'field',
  `f_title` varchar(50) NOT NULL COMMENT 'field_title',
  `f_orderby` varchar(4) NOT NULL DEFAULT 'NONE' COMMENT 'field_orderby',
  `f_val_type` varchar(20) NOT NULL COMMENT 'val_type',
  `f_input_type` varchar(20) NOT NULL COMMENT 'input_type',
  `f_default_value` varchar(50) NOT NULL COMMENT 'default_value',
  `f_validate` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'check_value',
  `f_onchange_js` varchar(50) NOT NULL COMMENT 'field_on_change',
  `f_translate` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'translate_value',
  `f_no_update` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'field_no_update',
  `f_is_method` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'field_is_method',
  `f_ck_array` varchar(50) NOT NULL COMMENT 'ck_reestr',
  `f_ch_tablename` varchar(50) NOT NULL COMMENT 'ch_table',
  `f_ch_keystring` varchar(25) NOT NULL COMMENT 'ch_id',
  `f_ch_namestring` varchar(25) NOT NULL COMMENT 'ch_field',
  `f_ch_deleted` varchar(25) NOT NULL COMMENT 'ch_deleted',
  `f_ch_keysort` varchar(25) NOT NULL COMMENT 'ch_sort',
  `f_link` varchar(150) NOT NULL COMMENT 'link',
  `f_link_vars` varchar(100) NOT NULL COMMENT 'link_vars',
  `f_link_type` varchar(50) NOT NULL COMMENT 'link_types',
  `f_link_img` varchar(50) NOT NULL COMMENT 'link_picture',
  `f_upload_path` varchar(150) NOT NULL COMMENT 'upload_path',
  `f_ordering` tinyint(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`f_id`),
  UNIQUE KEY `fld_name` (`f_hid`,`f_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8###qb_delimiter###


DROP TABLE IF EXISTS `#__md_hdrs`###qb_delimiter###
CREATE TABLE `#__md_hdrs` (
  `h_id` int(11) NOT NULL AUTO_INCREMENT,
  `h_side` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-admin,1-front',
  `h_module` varchar(50) NOT NULL,
  `h_view` varchar(50) NOT NULL,
  `h_layout` varchar(50) NOT NULL,
  `h_title` varbinary(255) NOT NULL COMMENT 'title',
  `h_table` varchar(50) NOT NULL COMMENT 'tablename',
  `h_is_tree` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'is_tree',
  `h_keystring` varchar(50) NOT NULL COMMENT 'keystring',
  `h_namestring` varchar(50) NOT NULL COMMENT 'namestring',
  `h_keycurrency` varchar(50) NOT NULL COMMENT 'keycurrency',
  `h_enabled` varchar(50) NOT NULL COMMENT 'enabled',
  `h_deleted` varchar(50) NOT NULL COMMENT 'deleted',
  `h_keysort` varchar(50) NOT NULL COMMENT 'keysort',
  `h_ordering_fld` varchar(50) NOT NULL COMMENT 'ordering_field',
  `h_ordering_parent` varchar(50) NOT NULL COMMENT 'ordering_parent',
  `h_show_cb` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'checkbox',
  `h_selector` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'selector',
  `h_multy_field` varchar(50) NOT NULL COMMENT 'multy_field',
  `h_l_tablename` varchar(50) NOT NULL COMMENT 'linktable',
  `h_p_tablename` varchar(50) NOT NULL COMMENT 'parent_table',
  `h_p_keystring` varchar(50) NOT NULL COMMENT 'parent_code',
  `h_p_namestring` varchar(50) NOT NULL COMMENT 'parent_name',
  `h_p_view` varbinary(50) DEFAULT NULL COMMENT 'parent_view',
  `h_tmpl_new` varchar(50) NOT NULL COMMENT 'templates[new]',
  `h_tmpl_modify` varchar(50) NOT NULL COMMENT 'templates[modify]',
  `h_custom_sql` text NOT NULL COMMENT 'custom_sql',
  PRIMARY KEY (`h_id`),
  UNIQUE KEY `smvl` (`h_side`,`h_module`,`h_view`,`h_layout`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8###qb_delimiter###

DROP TABLE IF EXISTS `#__metadata`###qb_delimiter###
CREATE TABLE `#__metadata` (
  `m_id` int(11) NOT NULL AUTO_INCREMENT,
  `m_module` varchar(30) NOT NULL,
  `m_view` varchar(30) NOT NULL,
  `m_field` varchar(50) NOT NULL,
  `m_layout` varchar(50) NOT NULL,
  `m_show` tinyint(1) DEFAULT '1',
  `m_width` varchar(10) DEFAULT NULL,
  `m_input_view` tinyint(1) DEFAULT '1',
  `m_input_size` varchar(10) DEFAULT NULL,
  `m_input_page` tinyint(2) NOT NULL DEFAULT '0',
  `m_show_in_filter` tinyint(1) DEFAULT '1',
  `m_show_in_filter_ext` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0',
  `m_strict_filter` tinyint(1) NOT NULL DEFAULT '0',
  `m_admin_side` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - фронтенд, 1 - админка',
  `m_field_loaded` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'флаг для перечитки метадаты',
  `m_field_order` int(11) NOT NULL DEFAULT '0',
  `m_translate_value` tinyint(1) DEFAULT '0' COMMENT 'need value translation',
  PRIMARY KEY (`m_module`,`m_view`,`m_field`,`m_layout`,`m_admin_side`),
  UNIQUE KEY `m_id` (`m_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1966 DEFAULT CHARSET=utf8###qb_delimiter###


DROP TABLE IF EXISTS `#__modules`###qb_delimiter###
CREATE TABLE `#__modules` (                                                                      
  `m_id` int(11) unsigned NOT NULL AUTO_INCREMENT,                                              
	`m_name` varchar(255) DEFAULT NULL,                                                           
	`m_show_breadcrumb` int(1) NOT NULL DEFAULT '1',                                              
  `m_config` text,                                                                              
  `m_incl_map` tinyint(1) NOT NULL DEFAULT '1',                                                 
  `m_enabled` tinyint(1) NOT NULL DEFAULT '1',                                                  
  `m_deleted` tinyint(1) NOT NULL DEFAULT '0',                                                  
  `m_translated` tinyint(1) DEFAULT '0' COMMENT 'need translation',                             
  `m_replace_name` varchar(255) DEFAULT NULL,                                                   
  `m_cur_version` varchar(10) DEFAULT NULL COMMENT 'текущая версия модуля',  
  `m_author` varchar(100) DEFAULT 'barmaz erp' COMMENT 'автор модуля',               
  `m_date` date DEFAULT NULL COMMENT 'дата версии модуля ',                     
  	PRIMARY KEY (`m_id`)                                                                          
  ) ENGINE=MyISAM AUTO_INCREMENT=37 DEFAULT CHARSET=utf8###qb_delimiter###

DROP TABLE IF EXISTS `#__notifications`###qb_delimiter###
CREATE TABLE `#__notifications` (
  `n_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `n_from` varchar(50) NOT NULL,
  `n_fromname` varchar(50) NOT NULL,
  `n_to` varchar(50) NOT NULL,
  `n_phone` varchar(12) NOT NULL,
  `n_time` int(11) NOT NULL,
  `n_title` varchar(150) NOT NULL,
  `n_text` text NOT NULL,
  `n_format` varchar(5) NOT NULL,
  `n_type` tinyint(1) NOT NULL,
  PRIMARY KEY (`n_id`)
) ENGINE=MyISAM AUTO_INCREMENT=321 DEFAULT CHARSET=utf8###qb_delimiter###

DROP TABLE IF EXISTS `#__plugins`###qb_delimiter###
CREATE TABLE `#__plugins` (
  `p_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `p_path` varchar(100) NOT NULL,
  `p_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `p_params` text NOT NULL,
  `p_ordering` int(3) NOT NULL DEFAULT '0',
  `p_enabled` int(1) NOT NULL DEFAULT '1',
  `p_deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`p_id`),
  UNIQUE KEY `path_and_name` (`p_path`,`p_name`)
) ENGINE=MyISAM AUTO_INCREMENT=66 DEFAULT CHARSET=utf8###qb_delimiter###
CREATE TABLE `#__plugins` (                                                          
	`p_id` int(11) unsigned NOT NULL AUTO_INCREMENT,                                  
	`p_path` varchar(100) NOT NULL,                                                   
	`p_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,        
	`p_params` text NOT NULL,                                                         
	`p_ordering` int(3) NOT NULL DEFAULT '0',                                         
	`p_enabled` int(1) NOT NULL DEFAULT '1',                                          
	`p_deleted` int(1) NOT NULL DEFAULT '0',                                          
  `p_version` varchar(10) DEFAULT NULL COMMENT 'версия плагина',       
  `p_author` varchar(50) DEFAULT 'barmaz erp' COMMENT 'автор плагина',  
  `p_date` date DEFAULT NULL COMMENT 'дата версии плагина ',       
  PRIMARY KEY (`p_id`),                                                             
  UNIQUE KEY `path_and_name` (`p_path`,`p_name`)                                    
  ) ENGINE=MyISAM AUTO_INCREMENT=66 DEFAULT CHARSET=utf8###qb_delimiter###

DROP TABLE IF EXISTS `#__redirect_links`###qb_delimiter###
CREATE TABLE `#__redirect_links` (
  `rl_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `rl_old_url` varchar(255) NOT NULL,
  `rl_new_url` varchar(255) NOT NULL,
  `rl_referer` varchar(150) NOT NULL,
  `rl_comment` varchar(255) NOT NULL,
  `rl_redirects` int(11) unsigned NOT NULL DEFAULT '0',
  `rl_substitution` tinyint(1) NOT NULL DEFAULT '0',
  `rl_ordering` int(11) NOT NULL DEFAULT '0',
  `rl_published` tinyint(1) NOT NULL DEFAULT '1',
  `rl_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `rl_create_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`rl_id`),
  UNIQUE KEY `idx_link_old` (`rl_old_url`)
) ENGINE=MyISAM AUTO_INCREMENT=6540 DEFAULT CHARSET=utf8###qb_delimiter###


DROP TABLE IF EXISTS `#__sessions`###qb_delimiter###
CREATE TABLE `#__sessions` (
  `s_id` varchar(128) NOT NULL,
  `s_uid` int(11) NOT NULL,
  `s_agent` varchar(32) NOT NULL,
  `s_ip` varchar(15) NOT NULL,
  `s_time` int(11) NOT NULL,
  `s_last` int(11) NOT NULL,
  `s_vars` text,
  PRIMARY KEY (`s_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8###qb_delimiter###


DROP TABLE IF EXISTS `#__settings`###qb_delimiter###
CREATE TABLE `#__settings` (
  `s_id` int(11) NOT NULL AUTO_INCREMENT,
  `s_module` varchar(64) NOT NULL DEFAULT 'system',
  `s_name` varchar(64) NOT NULL,
  `s_value` text,
  `s_type` varchar(64) NOT NULL DEFAULT 'string',
  PRIMARY KEY (`s_id`),
  UNIQUE KEY `s_module` (`s_module`,`s_name`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8###qb_delimiter###

DROP TABLE IF EXISTS `#__sms_sender_log`###qb_delimiter###
CREATE TABLE `#__sms_sender_log` (
  `phone` varchar(50) NOT NULL DEFAULT '',
  `err_code` varchar(5) NOT NULL DEFAULT '',
  `err_text` varchar(100) NOT NULL DEFAULT '',
  `err_time` datetime DEFAULT NULL,
  `m_text` varchar(250) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8###qb_delimiter###


DROP TABLE IF EXISTS `#__tags`###qb_delimiter###
CREATE TABLE `#__tags` (
  `t_module_name` varchar(32) NOT NULL DEFAULT '',
  `t_object_id` int(11) NOT NULL DEFAULT '0',
  `t_tag_name` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`t_module_name`,`t_object_id`,`t_tag_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8###qb_delimiter###

DROP TABLE IF EXISTS `#__template_zones`###qb_delimiter###
CREATE TABLE `#__template_zones` (
  `tz_id` int(11) NOT NULL AUTO_INCREMENT,
  `tz_name` varchar(50) NOT NULL DEFAULT '',
  `tz_descr` varchar(250) NOT NULL DEFAULT '',
  `tz_ordering` int(5) NOT NULL DEFAULT '0',
  `tz_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `tz_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`tz_id`),
  UNIQUE KEY `UNIQ_NAME` (`tz_name`)
) ENGINE=MyISAM AUTO_INCREMENT=43 DEFAULT CHARSET=utf8###qb_delimiter###
DROP TABLE IF EXISTS `#__widgets`###qb_delimiter###
CREATE TABLE `#__widgets` (
	`w_id` int(11) unsigned NOT NULL AUTO_INCREMENT,                                  
  `w_name` varchar(55) NOT NULL,                                                    
  `w_side` tinyint(1) NOT NULL DEFAULT '1',                                         
  `w_version` varchar(10) DEFAULT NULL COMMENT 'версия плагина',       
  `w_author` varchar(50) DEFAULT 'barmaz erp' COMMENT 'автор плагина',  
  `w_date` date DEFAULT NULL COMMENT 'версии плагина',                 
  PRIMARY KEY (`w_id`),
  UNIQUE KEY `w_name` (`w_name`)
) ENGINE=MyISAM AUTO_INCREMENT=64 DEFAULT CHARSET=utf8###qb_delimiter###
DROP TABLE IF EXISTS `#__widgets_active`###qb_delimiter###
CREATE TABLE `#__widgets_active` (
  `aw_id` int(11) NOT NULL AUTO_INCREMENT,
  `aw_name` varchar(255) NOT NULL,
  `aw_title` varchar(100) NOT NULL,
  `aw_show_title` tinyint(1) NOT NULL DEFAULT '0',
  `aw_title_link` varchar(150) NOT NULL,
  `aw_zone` varchar(255) NOT NULL,
  `aw_class` varchar(50) NOT NULL,
  `aw_config` text NOT NULL,
  `aw_access` text NOT NULL,
  `aw_content` text NOT NULL,
  `aw_ordering` int(5) NOT NULL DEFAULT '0',
  `aw_visible_in` text COMMENT 'Menu IDs',
  `aw_cache` int(7) NOT NULL DEFAULT '0',
  `aw_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `aw_deleted` int(1) NOT NULL DEFAULT '0',
  `aw_forlang` varchar(255) DEFAULT 'all' COMMENT 'for which language show',
  PRIMARY KEY (`aw_id`)
) ENGINE=MyISAM AUTO_INCREMENT=89 DEFAULT CHARSET=utf8###qb_delimiter###


