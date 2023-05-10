<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

// Поле сортировки отрабатывается специально в модели путем оверрайда updateOrdering()
$tablname='goods_dts';
$keystring='dt_id';
$deleted='dt_deleted';
$enabled="dt_enabled";
$ordering_field	= "dt_ordering";
$parent_view="paymenttypes";
$parent_table="goods_pts";
$parent_code='pt_id';
$parent_name='pt_name';
$multy_field="dt_id";

$nametabl="Delivery types";
$keycurrency='dt_currency';
$buttons=array(
		"new"=>array("show"=>1,"view"=>"deliverytypes","task"=>"newDTS"),
		"clone"=>array("show"=>1,"view"=>"deliverytypes","task"=>"cloneDTS"),
		"filter"=>array("show"=>1,"view"=>"deliverytypes"),
		"modify"=>array("show"=>1,"view"=>"deliverytypes","task"=>"modifyDTS"),
		"delete"=>array("show"=>1,"view"=>"deliverytypes","task"=>"delete"),
		"undelete"=>array("show"=>1,"view"=>"deliverytypes","task"=>"delete"),
		"clean_trash"=>array("show"=>1,"link"=>false),
		"trash"=>array("show"=>1,"link"=>false)
);
$cur_table_arr=array(
		"field"=>array(1=>"dt_id", "dt_name", "dt_price","dt_currency", "dt_tax", 
						"dt_weight_limit","dt_min_sum","dt_max_sum","dt_file", "dt_logo",
						"dt_params","dt_comments","dt_ordering","dt_debug","dt_admin_only",
						"dt_enabled", "dt_deleted"),
		"descr"=>array(3=>"Fixed price",6=>"Delivery max weight",7=>"Delivery min sum",8=>"Delivery max sum"),
		"name"=>array(1=>"Id", "Name", "Price","Currency","Tax",
						"Max weight","Min sum","Max sum", "Template","Logo",
						"Params","Comments","Ordering",	"Debug mode on","Admin only",
						"Enabled", "Deleted"),
		"input_type"=>array(1=>"hidden","text","text","select", "select",
						"text","text","text","label","image",
						"text","textarea","text","checkbox","checkbox",
						"checkbox","hidden"),
		"val_type"=>array(1=>"int","string","currency","int", "int",
						"float","currency","currency","string","string",
						"string","text","int","boolean","boolean",
						"boolean","boolean"),
		"update_type"	=> array(11=>0),
		"upload_path"=>array(10=>"dts"),
		"check_value"=>array(4=>1),
		"default_value"=>array(4=>DEFAULT_CURRENCY),
		"ch_table" => array(4=>"currency", 5=>"taxes"),
		"ch_field" => array(4=>"c_name", 5=>"t_name"),
		"ch_deleted"=> array(4=>"c_deleted"),
		"ch_enabled"=> array(4=>"c_enabled"),
		"ch_id"    => array(4=>"c_id", 5=>"t_id")
);
?>
