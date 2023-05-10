<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname='goods_opt_vals_data';
$use_view_rights="goods";
$keystring='ovd_id';
$namestring='ovd_id';
$nametabl="Goods option vals";
$ordering_field	= "ovd_ordering";
$enabled='ovd_enabled';
$parent_view="options_data";
$parent_table="goods_options_data";
$parent_code='od_id';
$parent_name='od_id';
$multy_field='ovd_od_id';
$opt_vals_parent = Module::getInstance()->get('reestr')->get('opt_vals_parent',0);
$buttons=array(
		"go_up"=> array("show"=>1,"link"=>false),
		"new"=>array("show"=>1,"view"=>"optionvals_data","task"=>"modifyOptionvals_data"),
		"filter"=>array("show"=>1,"view"=>"optionvals_data"),
		"modify"=>array("show"=>1,"view"=>"optionvals_data","task"=>"modifyOptionvals_data"),
		"reorder"=> array("show"=>1,"view"=>"optionvals_data","task"=>"reorder"),
		"delete"=>array("show"=>1,"view"=>"optionvals_data","task"=>"deleteNow"),
);
$cur_table_arr=array( 
		"field" =>array(1=>'ovd_id', 'ovd_od_id_name', 'ovd_val_id', 'ovd_price_sign', 'ovd_price_1', 
							'ovd_price_2', 'ovd_price_3', 'ovd_price_4', 'ovd_price_5', 'ovd_thumb', 
							'ovd_points_sign', 'ovd_points', 'ovd_weight_sign', 'ovd_weight', 'ovd_length_sign', 
							'ovd_length', 'ovd_width_sign', 'ovd_width', 'ovd_height_sign', 'ovd_height',  
							'ovd_check_stock', 'ovd_stock', 'ovd_ordering','ovd_od_id','ovd_enabled'),
		"name"	=>array(1=>'ID', 'Option name', 'Name', 'Price sign', 'Price 1', 
							'Price 2', 'Price 3', 'Price 4', 'Price 5', 'Thumb', 
							'Points sign', 'Points', 'Weight sign', 'Weight', 'Length sign', 
							'Length', 'Width_sign', 'Width', 'Height sign', 'Height', 
							'Check stock', 'Stock', 'Ordering','ovd_od_id','Enabled'),
		"input_type"=>array(1=>'hidden','label','select','select','text',
								'text','text','text','text', 'image',
								'select', 'text', 'select', 'text', 'select',
								'text', 'select', 'text', 'select', 'text', 
								'checkbox','text',	'text','hidden','checkbox'),
		"val_type"=>array(1=>'int','string','int','string','currency',
								'currency','currency','currency','currency', 'string',
								'string', 'int', 'string', 'float', 'string',
								'float', 'string', 'float', 'string', 'float', 
								'boolean','float', 'int', 'int','boolean'),
		"check_value"=> array(3=>1,4=>1,11=>1,13=>1,15=>1,17=>1,19=>1),
		"default_value"=>array(25=>1),
		"view" => array(24=>0),
		"fim"=>array(2=>"getOptionName"),
		"link_type"=>array(2=>''),
		"ch_table" => array(3=>"goods_opt_vals", 24=>"goods_options_data"),
		"ch_field" => array(3=>"ov_name", 24=>"od_id"),
		"ch_id"    => array(3=>"ov_id", 24=>"od_id"),
		"ch_sp_field" => array(3=>"ov_opt_id"),
		"ch_sp_field_val" => array(3=>$opt_vals_parent),
		"ck_reestr"=>array(4=>'sign_vals_ext',11=>'sign_vals_ext',13=>'sign_vals_ext',15=>'sign_vals_ext',17=>'sign_vals_ext',19=>'sign_vals_ext'),
		"upload_path"=>array(10=>'i/opt_vals_data/thumbs')
);
?>