<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname='banners';
$keystring='b_id';
$deleted='b_deleted';
$nametabl="ACRM items";
$multy_field='b_cat_id';
$parent_view="cats";
$parent_code='bc_id';
$ordering_field	= "b_ordering";		// Поле порядка отображения
$enabled="b_enabled";
$buttons=array(
		    "go_up"=> array("show"=>1,"link"=>false),
			"new"=>array("show"=>1,"view"=>"items","task"=>"modify"),
			"filter"=>array("show"=>1,"view"=>"items"),
			"modify"=>array("show"=>1,"view"=>"items","task"=>"modify"),
			"reorder"	=> array("show"=>1,"view"=>"items","task"=>"reorder"),
			"delete"=>array("show"=>1,"view"=>"items","task"=>"delete"),
			"undelete"=>array("show"=>1,"view"=>"items","task"=>"delete"),
			"trash"=>array("show"=>1,"link"=>false)
);
$cur_table_arr=array(
				"field"=>array(1=>'b_id', 'b_client_id', 'b_cat_id', 'b_name', 'b_alias', 
								'b_show_total', 'b_show_made', 'b_clicks', 'b_image', 'b_target', 
								'b_custom_code', 'b_descr', 'b_sticky', 'b_ordering', 'b_width', 
								'b_height', 'b_publish_up', 'b_publish_down', 'b_enabled', 'b_deleted'),
				"name"=>array(1=>'ID', 'Client', 'Category', 'Name', 'Alias', 
								'Show total', 'Show made', 'Clicks', 'Image', 'Link', 
								'Custom code', 'Description', 'Sticky', 'Ordering', 'Width', 
								'Height', 'Publish up', 'Publish down', 'Enabled', 'Deleted'),
				"input_type"=>array(1=>'hidden', 'select', 'label_sel', 'text', 'text', 
								'text', 'label', 'label', 'image', 'text', 
								'textarea', 'textarea', 'checkbox', 'text', 'text', 
								'text', 'datetime_ajax', 'datetime_ajax', 'checkbox', 'hidden'),
				"val_type"=>array(1=>'int', 'int', 'int', 'string', 'string', 
								'int', 'int', 'int', 'string', 'string', 
								'text', 'text', 'boolean', 'int', 'int', 
								'int', 'datetime', 'datetime', 'boolean', 'boolean'),
				"default_value"=>array(17=>"NOW",18=>Date::AddHours(Date::fromSQL(Date::nowSQL()), 8760)),
				"upload_path"=>array(9=>'i'),
				"ch_table" => array(1=>"", 2=>"banners_clients",3=>'banners_categories'),
				"ch_field" => array(1=>"", 2=>"bcl_name",3=>'bc_name'),
				"ch_id"    => array(1=>"", 2=>"bcl_id",3=>'bc_id')
);
?>