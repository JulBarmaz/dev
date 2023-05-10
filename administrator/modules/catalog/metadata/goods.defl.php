<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname='goods';
$keystring='g_id';
$namestring='g_name';
$alias_field='g_alias';
$deleted='g_deleted';
$enabled="g_enabled";
$nametabl="Goods list";
$multy_field='g_id';
$keycurrency='g_currency';
$parent_table="goods_group";
$parent_name='ggr_name';
/*это для апкей */
$parent_view="goodsgroup";
$parent_code='ggr_id';

$buttons=array(
		"go_up"=> array("show"=>1,"link"=>false),
		"new"=>array("show"=>1,"view"=>"goods","task"=>"modify"),
		"clone"=>array("show"=>1,"view"=>"goods","task"=>"make_clone"),
		"filter"=>array("show"=>1,"view"=>"goods"),
		"modify"=>array("show"=>1,"view"=>"goods","task"=>"modify"),
		"reorder"	=> array("show"=>1,"view"=>"goods","task"=>"reorder"),
		"modify_links"=>array("show"=>1,"view"=>"goods","task"=>"modifyLinks"),
		"delete"=>array("show"=>1,"view"=>"goods","task"=>"delete"),
		"undelete"=>array("show"=>1,"view"=>"goods","task"=>"delete"),
		"trash"=>array("show"=>1,"link"=>false),
		"clean_trash"=>array("show"=>1,"link"=>false)
);
$uni_buttons=array(
		"list_videos"=>array(
			"title"=>"List videos for selected goods",
			"alt"=>'V',
			"reset_multy"=>"true",
			"view"=>'videos',
			"module"=>'catalog',
			"position"=>"right",
			"alert"=>"Elements are not chosen"
		),
		"list_images"=>array(
			"title"=>"List images for selected goods",
			"alt"=>'I',
			"reset_multy"=>"true",
			"view"=>'images',
			"module"=>'catalog',
			"position"=>"right",
			"alert"=>"Elements are not chosen"
		),
		"list_options"=>array(
			"title"=>"List options for selected goods",
			"alt"=>'I',
			"reset_multy"=>"true",
			"view"=>'options_data',
			"module"=>'catalog',
			"position"=>"right",
			"alert"=>"Elements are not chosen"
		),
		"list_prices"=>array(
			"title"=>"Advanced price management",
			"alt"=>'APM',
			"reset_multy"=>"true",
			"view"=>'goodsprices',
			"module"=>'catalog',
			"position"=>"right",
			"alert"=>"Elements are not chosen"
		)
);

$cur_table_arr=array(
		"field"=>array(1=>'g_id','g_type','g_sku','g_name', 'g_alias',
				'g_fullname', 'g_stock', 'g_measure', 'g_pack_measure','g_pack_koeff',
				'g_height', 'g_width', 'g_length','g_vmeasure','g_weight',
				'g_wmeasure', 'g_points', 'g_currency', 'g_selltype', 'g_price_1', 
				'g_price_2', 'g_price_3', 'g_price_4', 'g_price_5', 'g_tax',
				'g_image','g_title_img','g_alt_img', 'g_medium_image','g_title_med',
				'g_alt_med', 'g_thumb','g_title_thm','g_alt_thm', 'g_comments', 
				'g_meta_title', 'g_meta_description', 'g_meta_keywords', 'g_enabled', 'g_deleted', 
				'g_flypage', 'g_order_tmpl', 'g_is_single', 'g_vendor', 'g_manufacturer', 
				'g_file_demo', 'g_file', 'g_new', 'g_hit', 'g_change_date', 
				'g_change_uid','g_size_measure', 'g_main_grp' 					
				),
		"name"=>array(1=>'ID','Goods type','SKU','Name','Alias',
				'Full name','Stock', 'Measure','Pack measure','Pack coefficient',
				'Height', 'Width','Length','Volume measure', 'Weight',
				'Weight measure', 'Points','Currency', 'Sell type',	'Price 1',
				'Price 2', 'Price 3','Price 4','Price 5','Tax',
				'Image','Title image','Alt image', 'Medium image','Title medium image',
				'Alt medium images','Thumb','Title thumb','Alt thumb', 'Description',
				'Meta title', 'Meta description', 'Meta keywords', 'Enabled', 'Delete mark',
				'Flypage','Order template','Is single',	'Vendor','Manufacturer',
				'Demo filename','Filename',	'Is new','Is hit','Last change date', 
				'Last changer', 'Size measure', 'Main group'
		),
		"descr"=>array(43=>'Redirect to basket after press buy'),
		"input_type"=>array(1=>'hidden','select','text','text','text',
				'text',	'text',	'select', 'select','text',
				'text', 'text','text','select',	'text',
				'select', 'text', 'select', 'select', 'text',
				'text', 'text','text', 'text', 'select',
				'image','text','text', 'image','text',
				'text', 'image','text','text', 'texteditor',
				'text','textarea', 'textarea', 'checkbox', 'hidden',
				'select', 'select', 'checkbox',	'select', 'select',
				'filepath','filepath', 'checkbox','checkbox','label',
				'label_sel', 'select', 'text'
		),
		"val_type"=>array(1=>'int','int','string','string','string',
				'string','float','int','int','float',
				'float','float','float','int','float',
				'int', 'int','int',	'int','currency',
				'currency',	'currency','currency', 'currency','int',
				'string','string','string',	'string','string',
				'string', 'string','string','string', 'text',
				'string','string', 'string','boolean','boolean',
				'string','string', 'boolean','int','int',
				'string','string', 'boolean','boolean','datetime',
				'int','int','int'
		),
		"field_on_change"=>array(2=>"goodsComplectSetVisible(this,7);"),
		"val_size"=>array(37=>250,38=>250),
		"check_value"=>array(2=>1,3=>(Module::getInstance("catalog")->getParam("generate_sku_automaticly") ? 3 : 2),4=>1,5=>3,8=>1,9=>1,14=>1,16=>1,41=>1,42=>1,44=>1,45=>1,52=>1),
		// "link"=>array(1=>'index.php?module=catalog&view=goods&task=modify&psid=[0]&multy_code=[1]'),
		// "link_key"=>array(1=>'id,multy_code'),
		"link"=>array(1=>'index.php?module=catalog&view=goods&task=modify&psid=[0]&multy_code=[1]&sort=[2]&page=[3]&orderby=[4]'),
		"link_key"=>array(1=>'id,multy_code,sort,page,orderby'),
		"link_type"=>array(1=>''),		
		"default_value"=>array(2=>1,8=>catalogConfig::$default_measure,9=>catalogConfig::$default_measure,10=>1,14=>catalogConfig::$default_vol_measure,
				16=>catalogConfig::$default_wmeasure,18=>Module::getInstance("catalog")->getParam("default_goods_currency"),19=>0,25=>catalogConfig::$default_order_taxes,
				44=>catalogConfig::$default_vendor,45=>catalogConfig::$default_manufacturer,50=>'NOW',51=>'AUTHOR',52=>catalogConfig::$default_size_measure),
		"upload_path"=>array(26=>'i/fullsize',29=>'i/medium',32=>'i/thumbs'),
		"ck_reestr"=>array(2=>'goods_type',19=>'sell_type',41=>'goods_flypage_tmpl',42=>'goods_order_tmpl'),
		"ch_table" => array(8=>'measure',9=>'measure',14=>'measure',16=>'measure',18=>'currency',25=>'taxes',44=>'vendors',45=>'manufacturers',51=>"users",52=>'measure',53=>"goods_group"),
		"ch_field" => array(8=>'meas_full_name',9=>'meas_full_name',14=>'meas_full_name',16=>'meas_full_name',18=>'c_name',25=>'t_name',44=>'v_name',45=>'mf_name',51=>"u_login",52=>'meas_full_name',53=>"ggr_name"),
		"ch_id"    => array(8=>'meas_id',9=>'meas_id',14=>'meas_id',16=>'meas_id',18=>'c_id',25=>'t_id',44=>'v_id',45=>'mf_id',51=>"u_id",52=>'meas_id',53=>"ggr_id"),
		"ch_deleted"=> array(8=>'meas_deleted',9=>'meas_deleted',14=>'meas_deleted',16=>'meas_deleted',18=>"c_deleted",53=>"ggr_deleted"),
		"ch_enabled"=> array(18=>"c_enabled"),
		"ch_sp_field"=> array(14=>"meas_type",16=>"meas_type",52=>"meas_type"),
		"ch_sp_field_val"=> array(14=>"1",16=>"4",52=>"2")
		
);
?>
