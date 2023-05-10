DROP TABLE IF EXISTS `#__currency`###qb_delimiter###
CREATE TABLE `#__currency` (
  `c_id` int(11) NOT NULL AUTO_INCREMENT,
  `c_code` char(3) NOT NULL,
  `c_name` varchar(64) NOT NULL,
  `c_short_name` varchar(10) NOT NULL,
  `c_enabled` smallint(1) NOT NULL DEFAULT '0',
  `c_deleted` smallint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`c_id`),
  UNIQUE KEY `c_code` (`c_code`),
  KEY `idx_currency_name` (`c_name`)
) ENGINE=MyISAM AUTO_INCREMENT=303 DEFAULT CHARSET=utf8###qb_delimiter###


DROP TABLE IF EXISTS `#__currency_rate`###qb_delimiter###
CREATE TABLE `#__currency_rate` (
  `cr_id` int(11) NOT NULL AUTO_INCREMENT,
  `c_id` int(11) NOT NULL,
  `c_datetime` datetime NOT NULL,
  `c_value` decimal(17,4) NOT NULL DEFAULT '1.0000',
  `c_deleted` smallint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`cr_id`),
  KEY `idx_currency_name` (`c_datetime`)
) ENGINE=MyISAM AUTO_INCREMENT=147 DEFAULT CHARSET=utf8###qb_delimiter###


DROP TABLE IF EXISTS `#__discounts`###qb_delimiter###
CREATE TABLE `#__discounts` (
  `d_id` int(11) NOT NULL AUTO_INCREMENT,
  `d_name` varchar(50) NOT NULL,
  `d_sign` varchar(1) NOT NULL DEFAULT '+',
  `d_value` decimal(17,2) NOT NULL DEFAULT '0.00',
  `d_period_unlimited` tinyint(1) NOT NULL DEFAULT '1',
  `d_start_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `d_end_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `d_stop` tinyint(1) NOT NULL DEFAULT '0',
  `d_comment` varchar(255) NOT NULL DEFAULT '',
  `d_ordering` int(11) NOT NULL DEFAULT '0',
  `d_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `d_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`d_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8###qb_delimiter###
DROP TABLE IF EXISTS `#__goods`###qb_delimiter###
CREATE TABLE `#__goods` (
  `g_id` int(11) NOT NULL AUTO_INCREMENT,
  `g_main_grp` int(11) NOT NULL,
  `g_type` int(3) NOT NULL DEFAULT '1',
  `g_sku` varchar(50) NOT NULL COMMENT 'артикул товара',
  `g_name` varchar(150) NOT NULL DEFAULT '' COMMENT 'наименование',
  `g_alias` varchar(255) NOT NULL DEFAULT '',
  `g_fullname` varchar(255) NOT NULL DEFAULT '' COMMENT 'полное наименование',
  `g_stock` decimal(17,4) NOT NULL DEFAULT '0.0000' COMMENT 'остаток',
  `g_measure` int(11) NOT NULL DEFAULT '0' COMMENT 'ед.измерения',
  `g_pack_measure` int(11) NOT NULL DEFAULT '0' COMMENT 'ед измерения упаковки',
  `g_pack_koeff` decimal(17,4) NOT NULL DEFAULT '1.0000' COMMENT 'коэффициент ед.измерения в ед.упаковки',
  `g_size_measure` int(11) NOT NULL DEFAULT '0' COMMENT 'ед измерения размеров',
  `g_height` decimal(17,3) NOT NULL DEFAULT '0.000' COMMENT 'высота',
  `g_width` decimal(17,3) NOT NULL DEFAULT '0.000' COMMENT 'ширина',
  `g_length` decimal(17,3) NOT NULL DEFAULT '0.000' COMMENT 'длина',
  `g_vmeasure` int(11) NOT NULL DEFAULT '0' COMMENT 'ед измерения объема',
  `g_weight` decimal(17,3) NOT NULL DEFAULT '0.000' COMMENT 'вес',
  `g_wmeasure` int(11) NOT NULL DEFAULT '0' COMMENT 'ед измерения веса',
  `g_points` int(10) NOT NULL DEFAULT '0' COMMENT 'баллы',
  `g_currency` int(11) NOT NULL,
  `g_selltype` int(1) NOT NULL DEFAULT '0' COMMENT 'отпуск в единицах измерения',
  `g_price_1` decimal(19,2) NOT NULL DEFAULT '0.00' COMMENT 'Цена',
  `g_price_2` decimal(19,2) NOT NULL DEFAULT '0.00' COMMENT 'цена',
  `g_price_3` decimal(19,2) NOT NULL DEFAULT '0.00' COMMENT 'цена',
  `g_price_4` decimal(19,2) NOT NULL DEFAULT '0.00' COMMENT 'цена',
  `g_price_5` decimal(19,2) NOT NULL DEFAULT '0.00' COMMENT 'цена',
  `g_tax` int(11) NOT NULL DEFAULT '0' COMMENT 'ставка налога',
  `g_image` varchar(64) NOT NULL DEFAULT '' COMMENT 'изображение',
  `g_medium_image` varchar(64) NOT NULL DEFAULT '' COMMENT 'уменьшенное изображение',
  `g_thumb` varchar(64) NOT NULL DEFAULT '' COMMENT 'мини изображение',
  `g_comments` text COMMENT 'комментарий',
  `g_meta_title` varchar(250) NOT NULL DEFAULT '',
  `g_meta_description` varchar(250) NOT NULL DEFAULT '',
  `g_meta_keywords` varchar(250) NOT NULL DEFAULT '',
  `g_enabled` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'включен',
  `g_deleted` smallint(1) NOT NULL DEFAULT '0' COMMENT 'помечен для удаления',
  `g_flypage` varchar(20) NOT NULL DEFAULT 'info' COMMENT 'шаблон страницы товара',
  `g_order_tmpl` varchar(30) NOT NULL DEFAULT '' COMMENT 'шаблон заказа товара',
  `g_is_single` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'один в заказе',
  `g_vendor` int(11) NOT NULL DEFAULT '0' COMMENT 'продавец товара',
  `g_manufacturer` int(11) NOT NULL DEFAULT '0' COMMENT 'товара',
  `g_file_demo` varchar(200) NOT NULL DEFAULT '' COMMENT 'демо файл для скачивания',
  `g_file` varchar(200) NOT NULL DEFAULT '' COMMENT 'файл для скачивания',
  `g_new` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'новинка',
  `g_hit` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'хит продаж',
  `g_change_date` datetime NOT NULL,
  `g_change_uid` int(11) NOT NULL DEFAULT '0',
  `g_title_img` varchar(255) DEFAULT NULL COMMENT 'тайтл основной картинки',
  `g_title_med` varchar(255) DEFAULT NULL COMMENT 'тайтл уменьшенного изображения',
  `g_title_thm` varchar(255) DEFAULT NULL COMMENT 'тайтл мини изображения',
  `g_alt_img` varchar(255) DEFAULT NULL COMMENT 'алт основной картинки',
  `g_alt_med` varchar(255) DEFAULT NULL COMMENT 'алт уменьшенного изображения картинки',
  `g_alt_thm` varchar(255) DEFAULT NULL COMMENT 'алт мини изображения картинки',
  `g_extcode` varchar(36) NOT NULL DEFAULT '' COMMENT 'Exchange GUID',
  PRIMARY KEY (`g_id`),
  UNIQUE KEY `goods_sku` (`g_sku`),
  KEY `goods_deleted` (`g_deleted`),
  KEY `goods_enabled` (`g_enabled`)
) ENGINE=MyISAM AUTO_INCREMENT=75 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC###qb_delimiter###


DROP TABLE IF EXISTS `#__goods_additionals`###qb_delimiter###
CREATE TABLE `#__goods_additionals` (
  `g_id` int(11) NOT NULL DEFAULT '0',
  `ad_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`g_id`,`ad_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8###qb_delimiter###


DROP TABLE IF EXISTS `#__goods_analogs`###qb_delimiter###
CREATE TABLE `#__goods_analogs` (
  `g_id` int(11) NOT NULL DEFAULT '0',
  `a_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`g_id`,`a_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8###qb_delimiter###


DROP TABLE IF EXISTS `#__goods_basket`###qb_delimiter###
CREATE TABLE `#__goods_basket` (
  `basket_id` varchar(128) NOT NULL,
  `basket_touch` int(11) NOT NULL,
  `basket_data` text NOT NULL,
  PRIMARY KEY (`basket_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8###qb_delimiter###


DROP TABLE IF EXISTS `#__goods_compare`###qb_delimiter###
CREATE TABLE `#__goods_compare` (
  `compare_id` varchar(128) NOT NULL,
  `compare_touch` int(11) NOT NULL,
  `compare_data` text NOT NULL,
  PRIMARY KEY (`compare_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8###qb_delimiter###


DROP TABLE IF EXISTS `#__goods_data`###qb_delimiter###
CREATE TABLE `#__goods_data` (
  `obj_id` int(11) NOT NULL COMMENT 'ид объекта',
  `field_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'ид поля свойства',
  `field_name` varchar(50) NOT NULL DEFAULT '' COMMENT 'название поля из метадаты',
  `field_value` text NOT NULL COMMENT 'поля свойства',
  PRIMARY KEY (`obj_id`,`field_name`),
  KEY `field_id` (`field_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8###qb_delimiter###


DROP TABLE IF EXISTS `#__goods_discounts`###qb_delimiter###
CREATE TABLE `#__goods_discounts` (
  `g_id` int(11) NOT NULL DEFAULT '0',
  `d_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`g_id`,`d_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8###qb_delimiter###


DROP TABLE IF EXISTS `#__goods_dts`###qb_delimiter###
CREATE TABLE `#__goods_dts` (
  `dt_id` int(11) NOT NULL AUTO_INCREMENT,
  `dt_name` varchar(150) NOT NULL,
  `dt_price` decimal(19,2) NOT NULL DEFAULT '0.00',
  `dt_weight_limit` decimal(19,2) NOT NULL DEFAULT '0.00',
  `dt_min_sum` decimal(19,2) NOT NULL DEFAULT '0.00',
  `dt_max_sum` decimal(19,2) NOT NULL DEFAULT '0.00',
  `dt_tax` int(11) NOT NULL DEFAULT '0',
  `dt_currency` int(11) NOT NULL,
  `dt_file` varchar(50) NOT NULL DEFAULT 'default',
  `dt_logo` varchar(128) NOT NULL DEFAULT '',
  `dt_params` text NOT NULL,
  `dt_comments` text,
  `dt_ordering` int(11) NOT NULL DEFAULT '0',
  `dt_debug` tinyint(1) NOT NULL DEFAULT '0',
  `dt_admin_only` tinyint(1) NOT NULL DEFAULT '0',
  `dt_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `dt_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`dt_id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8###qb_delimiter###


DROP TABLE IF EXISTS `#__goods_dts_links`###qb_delimiter###
CREATE TABLE `#__goods_dts_links` (
  `dt_id` varchar(50) NOT NULL COMMENT 'код товара',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `parent_id` int(10) NOT NULL DEFAULT '0' COMMENT 'ид группы',
  PRIMARY KEY (`dt_id`,`parent_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC###qb_delimiter###


DROP TABLE IF EXISTS `#__goods_favourites`###qb_delimiter###
CREATE TABLE `#__goods_favourites` (
  `favourites_id` varchar(128) NOT NULL,
  `favourites_touch` int(11) NOT NULL,
  `favourites_data` text NOT NULL,
  PRIMARY KEY (`favourites_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8###qb_delimiter###


DROP TABLE IF EXISTS `#__goods_feedbacks`###qb_delimiter###
CREATE TABLE `#__goods_feedbacks` (
  `gf_id` int(11) NOT NULL AUTO_INCREMENT,
  `gf_date` datetime NOT NULL,
  `gf_goods_id` int(11) NOT NULL COMMENT 'код товара',
  `gf_userid` int(11) NOT NULL DEFAULT '0',
  `gf_username` varchar(50) NOT NULL,
  `gf_usermail` varchar(50) NOT NULL,
  `gf_userip` varchar(15) NOT NULL,
  `gf_rating` int(11) NOT NULL DEFAULT '3',
  `gf_merits` text NOT NULL,
  `gf_demerits` text NOT NULL,
  `gf_published` int(1) NOT NULL DEFAULT '0',
  `gf_deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`gf_id`),
  UNIQUE KEY `mail_goods` (`gf_goods_id`,`gf_usermail`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8###qb_delimiter###


DROP TABLE IF EXISTS `#__goods_group`###qb_delimiter###
CREATE TABLE `#__goods_group` (
  `ggr_id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ид группы',
  `ggr_id_parent` int(10) NOT NULL DEFAULT '0' COMMENT 'ид родителя',
  `ggr_name` varchar(150) NOT NULL DEFAULT '' COMMENT 'наименование группы',
  `ggr_alias` varchar(255) NOT NULL,
  `ggr_thumb` varchar(255) NOT NULL DEFAULT '' COMMENT 'мини изображение',
  `ggr_title_thm` varchar(255) DEFAULT NULL,
  `ggr_alt_thm` varchar(255) DEFAULT NULL,
  `ggr_image` varchar(255) NOT NULL DEFAULT '',
  `ggr_title_img` varchar(255) NOT NULL DEFAULT '',
  `ggr_alt_img` varchar(255) NOT NULL DEFAULT '',
  `ggr_image_inherit` smallint(1) NOT NULL DEFAULT '0',
  `ggr_comment` text COMMENT 'описание группы',
  `ggr_meta_title` varchar(250) NOT NULL DEFAULT '',
  `ggr_meta_description` varchar(250) NOT NULL DEFAULT '',
  `ggr_meta_keywords` varchar(250) NOT NULL DEFAULT '',
  `ggr_ordering` int(11) NOT NULL DEFAULT '0' COMMENT 'порядок сортировки',
  `ggr_default_sorting` varchar(50) NOT NULL DEFAULT '',
  `ggr_list_tmpl` varchar(50) NOT NULL COMMENT 'темплейт категории',
  `ggr_enabled` smallint(1) NOT NULL DEFAULT '1' COMMENT 'включен',
  `ggr_deleted` smallint(1) NOT NULL DEFAULT '0' COMMENT 'помечен для удаления',
  `ggr_extcode` varchar(36) DEFAULT NULL COMMENT 'код группы во внешней программе',
  `ggr_change_date` datetime DEFAULT NULL,
  `ggr_change_uid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ggr_id`)
) ENGINE=MyISAM AUTO_INCREMENT=163 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC###qb_delimiter###


DROP TABLE IF EXISTS `#__goods_group_data`###qb_delimiter###
CREATE TABLE `#__goods_group_data` (
  `obj_id` int(11) NOT NULL COMMENT 'ид объекта',
  `field_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'ид поля свойства',
  `field_name` varchar(50) NOT NULL COMMENT 'название поля из метадаты',
  `field_value` text NOT NULL COMMENT 'поля свойства',
  PRIMARY KEY (`obj_id`,`field_name`),
  KEY `field_id` (`field_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8###qb_delimiter###


DROP TABLE IF EXISTS `#__goods_group_fields`###qb_delimiter###
CREATE TABLE `#__goods_group_fields` (
  `f_id` int(11) NOT NULL DEFAULT '0',
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `ordering` int(11) DEFAULT '0',
  PRIMARY KEY (`f_id`,`parent_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8###qb_delimiter###


DROP TABLE IF EXISTS `#__goods_img`###qb_delimiter###
CREATE TABLE `#__goods_img` (
  `i_id` int(11) NOT NULL AUTO_INCREMENT,
  `i_gid` int(11) NOT NULL,
  `i_type` tinyint(3) NOT NULL,
  `i_link` varchar(250) NOT NULL,
  `i_title` varchar(250) NOT NULL,
  `i_image` varchar(64) NOT NULL,
  `i_thumb` varchar(64) NOT NULL,
  `i_ordering` int(11) NOT NULL,
  `i_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `i_deleted` smallint(1) NOT NULL DEFAULT '0',
  `i_alt_img` varchar(255) DEFAULT NULL COMMENT 'алт картинки',
  `i_alt_thm` varchar(255) DEFAULT NULL COMMENT 'алт мини картинки',
  `i_title_thm` varchar(225) DEFAULT NULL COMMENT 'тайтл мини картинки',
  `i_title_img` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`i_id`),
  KEY `goods_deleted` (`i_deleted`),
  KEY `goods_enabled` (`i_enabled`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8###qb_delimiter###


DROP TABLE IF EXISTS `#__goods_import_tmp`###qb_delimiter###
CREATE TABLE `#__goods_import_tmp` (
  `id` int(11) NOT NULL DEFAULT '0',
  `f1` text NOT NULL,
  `f2` text NOT NULL,
  `f3` text NOT NULL,
  `f4` text NOT NULL,
  `f5` text NOT NULL,
  `f6` text NOT NULL,
  `f7` text NOT NULL,
  `f8` text NOT NULL,
  `f9` text NOT NULL,
  `f10` text NOT NULL,
  `f11` text NOT NULL,
  `f12` text NOT NULL,
  `f13` text NOT NULL,
  `f14` text NOT NULL,
  `f15` text NOT NULL,
  `f16` text NOT NULL,
  `f17` text NOT NULL,
  `f18` text NOT NULL,
  `f19` text NOT NULL,
  `f20` text NOT NULL,
  `f21` text NOT NULL,
  `f22` text NOT NULL,
  `f23` text NOT NULL,
  `f24` text NOT NULL,
  `f25` text NOT NULL,
  `f26` text NOT NULL,
  `f27` text NOT NULL,
  `f28` text NOT NULL,
  `f29` text NOT NULL,
  `f30` text NOT NULL,
  `f31` text NOT NULL,
  `f32` text NOT NULL,
  `f33` text NOT NULL,
  `f34` text NOT NULL,
  `f35` text NOT NULL,
  `f36` text NOT NULL,
  `f37` text NOT NULL,
  `f38` text NOT NULL,
  `f39` text NOT NULL,
  `f40` text NOT NULL,
  `f41` text NOT NULL,
  `f42` text NOT NULL,
  `f43` text NOT NULL,
  `f44` text NOT NULL,
  `f45` text NOT NULL,
  `f46` text NOT NULL,
  `f47` text NOT NULL,
  `f48` text NOT NULL,
  `f49` text NOT NULL,
  `f50` text NOT NULL,
  `f51` text NOT NULL,
  `f52` text NOT NULL,
  `f53` text NOT NULL,
  `f54` text NOT NULL,
  `f55` text NOT NULL,
  `f56` text NOT NULL,
  `f57` text NOT NULL,
  `f58` text NOT NULL,
  `f59` text NOT NULL,
  `f60` text NOT NULL,
  `f61` text NOT NULL,
  `f62` text NOT NULL,
  `f63` text NOT NULL,
  `f64` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8###qb_delimiter###


DROP TABLE IF EXISTS `#__goods_links`###qb_delimiter###
CREATE TABLE `#__goods_links` (
  `g_id` int(11) NOT NULL COMMENT 'код товара',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `parent_id` int(10) NOT NULL DEFAULT '0' COMMENT 'ид группы',
  PRIMARY KEY (`g_id`,`parent_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8###qb_delimiter###


DROP TABLE IF EXISTS `#__goods_opt_types`###qb_delimiter###
CREATE TABLE `#__goods_opt_types` (
  `t_id` int(10) NOT NULL AUTO_INCREMENT,
  `t_val_type` varchar(50) NOT NULL,
  `t_input_type` varchar(50) NOT NULL,
  `t_name` varchar(50) NOT NULL,
  `t_mb_quantitative` tinyint(1) NOT NULL DEFAULT '1',
  `t_have_values` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`t_id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8###qb_delimiter###


DROP TABLE IF EXISTS `#__goods_opt_vals`###qb_delimiter###
CREATE TABLE `#__goods_opt_vals` (
  `ov_id` int(11) NOT NULL AUTO_INCREMENT,
  `ov_opt_id` int(11) NOT NULL DEFAULT '0',
  `ov_name` varchar(255) NOT NULL DEFAULT '',
  `ov_thumb` varchar(64) NOT NULL DEFAULT '',
  `ov_ordering` int(11) NOT NULL,
  `ov_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `ov_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `ov_extcode` varchar(36) NOT NULL DEFAULT '',
  PRIMARY KEY (`ov_id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8###qb_delimiter###


DROP TABLE IF EXISTS `#__goods_opt_vals_data`###qb_delimiter###
CREATE TABLE `#__goods_opt_vals_data` (
  `ovd_id` int(11) NOT NULL AUTO_INCREMENT,
  `ovd_od_id` int(11) NOT NULL,
  `ovd_val_id` int(11) NOT NULL COMMENT 'ид значения опции',
  `ovd_price_sign` varchar(1) NOT NULL DEFAULT '+',
  `ovd_price_1` decimal(19,2) NOT NULL DEFAULT '0.00',
  `ovd_price_2` decimal(19,2) NOT NULL DEFAULT '0.00',
  `ovd_price_3` decimal(19,2) NOT NULL DEFAULT '0.00',
  `ovd_price_4` decimal(19,2) NOT NULL DEFAULT '0.00',
  `ovd_price_5` decimal(19,2) NOT NULL DEFAULT '0.00',
  `ovd_weight_sign` varchar(1) NOT NULL DEFAULT '+',
  `ovd_weight` decimal(17,3) NOT NULL DEFAULT '0.000',
  `ovd_points_sign` varchar(1) NOT NULL DEFAULT '+',
  `ovd_points` decimal(17,0) NOT NULL DEFAULT '0',
  `ovd_length_sign` varchar(1) NOT NULL DEFAULT '+',
  `ovd_length` decimal(17,3) NOT NULL DEFAULT '0.000',
  `ovd_width_sign` varchar(1) NOT NULL DEFAULT '+',
  `ovd_width` decimal(17,3) NOT NULL DEFAULT '0.000',
  `ovd_height_sign` varchar(1) NOT NULL DEFAULT '+',
  `ovd_height` decimal(17,3) NOT NULL DEFAULT '0.000',
  `ovd_check_stock` tinyint(1) NOT NULL DEFAULT '0',
  `ovd_stock` decimal(17,4) NOT NULL DEFAULT '0.0000',
  `ovd_thumb` varchar(64) NOT NULL DEFAULT '',
  `ovd_ordering` int(11) NOT NULL DEFAULT '0',
  `ovd_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `ovd_extcode` varchar(36) NOT NULL DEFAULT '',
  PRIMARY KEY (`ovd_id`),
  UNIQUE KEY `od_go_id_od_val_id` (`ovd_od_id`,`ovd_val_id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8###qb_delimiter###


DROP TABLE IF EXISTS `#__goods_options`###qb_delimiter###
CREATE TABLE `#__goods_options` (
  `o_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ид',
  `o_title` varchar(255) DEFAULT NULL COMMENT 'Название поля',
  `o_default` text NOT NULL COMMENT 'значение по умолчанию',
  `o_type` mediumint(10) NOT NULL COMMENT 'тип параметра(связь)',
  `o_required` smallint(1) NOT NULL DEFAULT '0',
  `o_ordering` int(11) NOT NULL DEFAULT '0',
  `o_deleted` smallint(1) NOT NULL DEFAULT '0' COMMENT 'признак выключения',
  `o_is_quantitative` tinyint(1) NOT NULL DEFAULT '0',
  `o_custom` smallint(1) NOT NULL DEFAULT '0',
  `o_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `o_extcode` varchar(36) NOT NULL DEFAULT '',
  PRIMARY KEY (`o_id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 PACK_KEYS=0###qb_delimiter###


DROP TABLE IF EXISTS `#__goods_options_data`###qb_delimiter###
CREATE TABLE `#__goods_options_data` (
  `od_id` int(11) NOT NULL AUTO_INCREMENT,
  `od_obj_id` int(11) NOT NULL COMMENT 'ид объекта',
  `od_opt_id` int(11) NOT NULL DEFAULT '0' COMMENT 'ид опции',
  `od_ordering` int(11) NOT NULL DEFAULT '0',
  `od_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `od_extcode` varchar(36) NOT NULL DEFAULT '',
  PRIMARY KEY (`od_id`),
  UNIQUE KEY `field_id` (`od_opt_id`,`od_obj_id`)
) ENGINE=MyISAM AUTO_INCREMENT=117 DEFAULT CHARSET=utf8###qb_delimiter###


DROP TABLE IF EXISTS `#__goods_prices`###qb_delimiter###
CREATE TABLE `#__goods_prices` (
  `p_id` int(11) NOT NULL AUTO_INCREMENT,
  `p_g_id` int(11) NOT NULL DEFAULT '0',
  `p_quantity` decimal(17,4) unsigned NOT NULL DEFAULT '0.0000',
  `p_price_1` decimal(19,2) NOT NULL DEFAULT '0.00',
  `p_price_2` decimal(19,2) NOT NULL DEFAULT '0.00',
  `p_price_3` decimal(19,2) NOT NULL DEFAULT '0.00',
  `p_price_4` decimal(19,2) NOT NULL DEFAULT '0.00',
  `p_price_5` decimal(19,2) NOT NULL DEFAULT '0.00',
  `p_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `p_deleted` smallint(1) NOT NULL DEFAULT '0',
  `p_change_date` datetime NOT NULL,
  `p_change_uid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`p_id`),
  UNIQUE KEY `quantity_start` (`p_g_id`,`p_quantity`)
) ENGINE=MyISAM AUTO_INCREMENT=71 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC###qb_delimiter###


DROP TABLE IF EXISTS `#__goods_pts`###qb_delimiter###
CREATE TABLE `#__goods_pts` (
  `pt_id` int(11) NOT NULL AUTO_INCREMENT,
  `pt_name` varchar(150) NOT NULL,
  `pt_file` varchar(25) NOT NULL DEFAULT 'default',
  `pt_logo` varchar(128) NOT NULL DEFAULT '',
  `pt_price` decimal(19,2) NOT NULL DEFAULT '0.00',
  `pt_currency` int(11) NOT NULL,
  `pt_set_status` int(11) NOT NULL DEFAULT '0',
  `pt_params` text NOT NULL,
  `pt_comments` text,
  `pt_ordering` int(11) NOT NULL DEFAULT '0',
  `pt_debug` tinyint(1) NOT NULL DEFAULT '0',
  `pt_admin_only` tinyint(1) NOT NULL DEFAULT '0',
  `pt_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `pt_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`pt_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8###qb_delimiter###


DROP TABLE IF EXISTS `#__goods_sets`###qb_delimiter###
CREATE TABLE `#__goods_sets` (
  `g_id` int(11) NOT NULL,
  `s_id` int(11) NOT NULL,
  `s_quantity` float(17,4) NOT NULL DEFAULT '0.0000',
  PRIMARY KEY (`g_id`,`s_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8###qb_delimiter###


DROP TABLE IF EXISTS `#__goods_stat`###qb_delimiter###
CREATE TABLE `#__goods_stat` (
  `gs_id` int(11) NOT NULL AUTO_INCREMENT,
  `gs_remote_url` varchar(100) DEFAULT NULL,
  `gs_goods_id` int(11) DEFAULT '0',
  `gs_count` int(11) DEFAULT '0',
  `gs_enabled` tinyint(1) DEFAULT '1',
  `gs_deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`gs_id`),
  UNIQUE KEY `Uniq` (`gs_remote_url`,`gs_goods_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8###qb_delimiter###


DROP TABLE IF EXISTS `#__goods_videos`###qb_delimiter###
CREATE TABLE `#__goods_videos` (
  `v_id` int(11) NOT NULL AUTO_INCREMENT,
  `v_gid` int(4) DEFAULT NULL,
  `v_title` varchar(200) DEFAULT NULL,
  `v_image` varchar(64) DEFAULT NULL,
  `v_title_img` varchar(255) DEFAULT NULL,
  `v_alt_img` varchar(255) DEFAULT NULL,
  `v_video_youtube` varchar(250) DEFAULT NULL,
  `v_video_ogg` varchar(250) DEFAULT NULL,
  `v_video_mp4` varchar(250) DEFAULT NULL,
  `v_video_webm` varchar(250) DEFAULT NULL,
  `v_comment` text,
  `v_published` tinyint(1) DEFAULT '0',
  `v_ordering` int(11) NOT NULL DEFAULT '0',
  `v_deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`v_id`)
) ENGINE=MyISAM AUTO_INCREMENT=310 DEFAULT CHARSET=utf8###qb_delimiter###



DROP TABLE IF EXISTS `#__manufacturer_categories`###qb_delimiter###
CREATE TABLE `#__manufacturer_categories` (
  `mfc_id` int(11) NOT NULL AUTO_INCREMENT,
  `mfc_name` varchar(64) NOT NULL,
  `mfc_desc` text,
  `mfc_deleted` smallint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`mfc_id`),
  KEY `idx_manufacturer_category_category_name` (`mfc_name`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='Manufacturers are assigned to these categories'###qb_delimiter###


DROP TABLE IF EXISTS `#__manufacturers`###qb_delimiter###
CREATE TABLE `#__manufacturers` (
  `mf_id` int(11) NOT NULL AUTO_INCREMENT,
  `mf_cat_id` int(11) NOT NULL,
  `mf_name` varchar(150) NOT NULL,
  `mf_logo` varchar(64) NOT NULL DEFAULT '',
  `mf_email` varchar(255) NOT NULL DEFAULT '',
  `mf_url` varchar(255) NOT NULL DEFAULT '',
  `mf_desc` text NOT NULL,
  `mf_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `mf_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `mf_extcode` varchar(36) NOT NULL DEFAULT '',
  `mf_alias` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`mf_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8###qb_delimiter###

DROP TABLE IF EXISTS `#__measure`###qb_delimiter###
CREATE TABLE `#__measure` (
  `meas_id` int(11) NOT NULL AUTO_INCREMENT,
  `meas_code` varchar(10) NOT NULL DEFAULT '',
  `meas_short_name` varchar(100) NOT NULL DEFAULT '',
  `meas_full_name` varchar(100) NOT NULL DEFAULT '',
  `meas_kf` float(19,4) NOT NULL DEFAULT '1.0000',
  `meas_type` tinyint(2) NOT NULL DEFAULT '0',
  `meas_comment` varchar(200) NOT NULL DEFAULT '',
  `meas_enabled` smallint(1) NOT NULL DEFAULT '1',
  `meas_deleted` smallint(1) NOT NULL DEFAULT '0',
  `meas_extcode` varchar(36) NOT NULL DEFAULT '',
  PRIMARY KEY (`meas_id`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8###qb_delimiter###

DROP TABLE IF EXISTS `#__orders`###qb_delimiter###
CREATE TABLE `#__orders` (
  `o_id` int(11) NOT NULL AUTO_INCREMENT,
  `o_hash` varchar(100) NOT NULL DEFAULT '',
  `o_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `o_uid` int(11) NOT NULL,
  `o_vendor` int(11) NOT NULL,
  `o_pt_id` int(11) NOT NULL,
  `o_pt_name` varchar(200) NOT NULL,
  `o_pt_data` text NOT NULL,
  `o_pt_sum` decimal(19,2) NOT NULL DEFAULT '0.00',
  `o_pt_result` text NOT NULL,
  `o_userdata` text NOT NULL,
  `o_dt_id` int(11) NOT NULL,
  `o_dt_name` varchar(200) NOT NULL,
  `o_dt_data` text NOT NULL,
  `o_dt_sum` decimal(19,2) NOT NULL DEFAULT '0.00',
  `o_dt_tax_id` int(11) NOT NULL DEFAULT '0',
  `o_dt_tax_name` varchar(150) NOT NULL DEFAULT '',
  `o_dt_tax_val` decimal(19,2) NOT NULL DEFAULT '0.00',
  `o_dt_tax_sum` decimal(19,2) NOT NULL DEFAULT '0.00',
  `o_discount_sum` decimal(19,2) NOT NULL DEFAULT '0.00',
  `o_taxes_sum` decimal(19,2) NOT NULL DEFAULT '0.00',
  `o_total_sum` decimal(19,2) NOT NULL DEFAULT '0.00',
  `o_points` decimal(19,4) NOT NULL DEFAULT '0.0000',
  `o_currency` int(11) NOT NULL,
  `o_quantity` decimal(19,4) NOT NULL DEFAULT '0.0000',
  `o_measure` int(11) NOT NULL,
  `o_weight` decimal(19,4) NOT NULL DEFAULT '0.0000',
  `o_wmeasure` int(11) NOT NULL,
  `o_status` int(11) NOT NULL,
  `o_paid` smallint(1) NOT NULL DEFAULT '0',
  `o_ip_address` varchar(15) NOT NULL DEFAULT '',
  `o_comments` text NOT NULL,
  `o_deleted` smallint(1) NOT NULL DEFAULT '0',
  `o_extcode` varchar(36) NOT NULL DEFAULT '',
  PRIMARY KEY (`o_id`)
) ENGINE=MyISAM AUTO_INCREMENT=158 DEFAULT CHARSET=utf8###qb_delimiter###


DROP TABLE IF EXISTS `#__orders_files`###qb_delimiter###
CREATE TABLE `#__orders_files` (
  `f_id` int(11) NOT NULL AUTO_INCREMENT,
  `f_order_id` int(11) NOT NULL,
  `f_item_id` int(11) NOT NULL,
  `f_g_id` int(11) NOT NULL,
  `f_opt_id` int(11) NOT NULL,
  `f_opt_title` varchar(100) NOT NULL,
  `f_opt_file` varchar(64) NOT NULL,
  `f_extcode` varchar(36) NOT NULL DEFAULT '',
  PRIMARY KEY (`f_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8###qb_delimiter###


DROP TABLE IF EXISTS `#__orders_items`###qb_delimiter###
CREATE TABLE `#__orders_items` (
  `i_id` int(11) NOT NULL AUTO_INCREMENT,
  `i_order_id` int(11) NOT NULL,
  `i_g_id` int(11) DEFAULT NULL,
  `i_g_type` int(3) NOT NULL DEFAULT '1',
  `i_g_sku` varchar(50) NOT NULL,
  `i_g_name` varchar(250) NOT NULL,
  `i_g_extcode` varchar(36) NOT NULL DEFAULT '',
  `i_g_options` text NOT NULL,
  `i_g_options_text` text NOT NULL,
  `i_g_quantity` decimal(19,4) NOT NULL DEFAULT '0.0000',
  `i_g_measure` int(11) NOT NULL,
  `i_g_price` decimal(19,2) NOT NULL DEFAULT '0.00',
  `i_g_weight` decimal(19,2) NOT NULL DEFAULT '0.00',
  `i_g_wmeasure` int(11) NOT NULL,
  `i_g_tax_id` int(11) NOT NULL DEFAULT '0',
  `i_g_tax_val` decimal(19,2) NOT NULL DEFAULT '0.00',
  `i_g_tax` decimal(19,2) NOT NULL DEFAULT '0.00',
  `i_g_tax_name` varchar(150) NOT NULL,
  `i_g_sum` decimal(19,2) NOT NULL DEFAULT '0.00',
  `i_extcode` varchar(36) NOT NULL DEFAULT '',
  PRIMARY KEY (`i_id`)
) ENGINE=MyISAM AUTO_INCREMENT=162 DEFAULT CHARSET=utf8###qb_delimiter###


DROP TABLE IF EXISTS `#__orders_payments`###qb_delimiter###
CREATE TABLE `#__orders_payments` (
  `payment_id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_order_id` int(11) NOT NULL DEFAULT '0',
  `payment_method_id` int(11) DEFAULT NULL,
  `payment_code` varchar(30) NOT NULL DEFAULT '',
  `payment_number` blob,
  `payment_expire` int(11) DEFAULT NULL,
  `payment_name` varchar(255) DEFAULT NULL,
  `payment_log` text,
  `payment_trans_id` text NOT NULL,
  `payment_extcode` varchar(36) NOT NULL DEFAULT '',
  PRIMARY KEY (`payment_id`),
  KEY `idx_order_payment_order_id` (`payment_order_id`),
  KEY `idx_order_payment_method_id` (`payment_method_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='The payment method that was chosen for a specific order'###qb_delimiter###


DROP TABLE IF EXISTS `#__orders_status`###qb_delimiter###
CREATE TABLE `#__orders_status` (
  `os_id` int(11) NOT NULL AUTO_INCREMENT,
  `os_name` varchar(50) NOT NULL,
  `os_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `os_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`os_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8###qb_delimiter###

DROP TABLE IF EXISTS `#__taxes`###qb_delimiter###
CREATE TABLE `#__taxes` (
  `t_id` int(11) NOT NULL AUTO_INCREMENT,
  `t_name` varchar(50) NOT NULL,
  `t_value` decimal(5,2) NOT NULL DEFAULT '0.00',
  `t_fixed` tinyint(1) NOT NULL DEFAULT '0',
  `t_comment` varchar(255) NOT NULL DEFAULT '',
  `t_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `t_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `t_extcode` varchar(36) NOT NULL DEFAULT '',
  PRIMARY KEY (`t_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8###qb_delimiter###

DROP TABLE IF EXISTS `#__users_vendors`###qb_delimiter###
CREATE TABLE `#__users_vendors` (
  `uv_id` int(11) NOT NULL AUTO_INCREMENT,
  `uv_uid` int(11) NOT NULL,
  `uv_vid` int(11) NOT NULL,
  `uv_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `uv_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uv_id`),
  UNIQUE KEY `vendor` (`uv_vid`),
  UNIQUE KEY `user` (`uv_uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8###qb_delimiter###


DROP TABLE IF EXISTS `#__vendor_categories`###qb_delimiter###
CREATE TABLE `#__vendor_categories` (
  `vc_id` int(11) NOT NULL AUTO_INCREMENT,
  `vc_name` varchar(150) NOT NULL DEFAULT '',
  `vc_desc` text,
  `vc_deleted` smallint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`vc_id`),
  KEY `idx_vendor_category_category_name` (`vc_name`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='The categories that vendors are assigned to'###qb_delimiter###


DROP TABLE IF EXISTS `#__vendors`###qb_delimiter###
CREATE TABLE `#__vendors` (
  `v_id` int(11) NOT NULL AUTO_INCREMENT,
  `v_cat_id` int(11) NOT NULL,
  `v_name` varchar(150) NOT NULL,
  `v_store_name` varchar(150) NOT NULL DEFAULT '',
  `v_minimum_basket` decimal(19,2) NOT NULL DEFAULT '0.00',
  `v_store_desc` text,
  `v_contact_name` varchar(255) NOT NULL DEFAULT '',
  `v_contact_phone` varchar(32) NOT NULL DEFAULT '',
  `v_contact_email` varchar(255) NOT NULL DEFAULT '',
  `v_ogrn` varchar(15) NOT NULL DEFAULT '',
  `v_inn` varchar(12) NOT NULL DEFAULT '',
  `v_kpp` varchar(9) NOT NULL DEFAULT '',
  `v_boss` varchar(75) NOT NULL DEFAULT '',
  `v_ca_name` varchar(75) NOT NULL DEFAULT '',
  `v_bank` varchar(255) NOT NULL DEFAULT '',
  `v_sett_acc` varchar(20) NOT NULL DEFAULT '',
  `v_bik` varchar(9) NOT NULL DEFAULT '',
  `v_bank_acc` varchar(20) NOT NULL DEFAULT '',
  `v_address_p` text NOT NULL,
  `v_address_u` text NOT NULL,
  `v_phone` varchar(32) NOT NULL DEFAULT '',
  `v_fax` varchar(32) NOT NULL DEFAULT '',
  `v_url` varchar(255) NOT NULL DEFAULT '',
  `v_logo` varchar(64) NOT NULL DEFAULT '',
  `v_terms_of_service` text NOT NULL,
  `v_pechat` varchar(64) NOT NULL DEFAULT '',
  `v_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `v_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `v_extcode` varchar(36) NOT NULL DEFAULT '',
  `v_alias` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`v_id`),
  KEY `idx_vendor_name` (`v_name`),
  KEY `idx_vendor_category_id` (`v_cat_id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8###qb_delimiter###




