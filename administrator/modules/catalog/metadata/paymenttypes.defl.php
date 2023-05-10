<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname='goods_pts';
$keystring='pt_id';
$deleted='pt_deleted';
$enabled="pt_enabled";
$ordering_field	= "pt_ordering";
$keycurrency='pt_currency';
$nametabl="Payment types";
$buttons=array(
				"new"=>array("show"=>1,"view"=>"paymenttypes","task"=>"newPTS"),
				"filter"=>array("show"=>1,"view"=>"paymenttypes"),
				"modify"=>array("show"=>1,"view"=>"paymenttypes","task"=>"modifyPTS"),
				"delete"=>array("show"=>1,"view"=>"paymenttypes","task"=>"delete"),
				"undelete"=>array("show"=>1,"view"=>"paymenttypes","task"=>"delete"),
				"trash"=>array("show"=>1,"view"=>"paymenttypes","link"=>false),
				"clean_trash"=>array("show"=>1,"view"=>"paymenttypes","link"=>false)

);
$cur_table_arr=array(
					"field"=>array(1=>"pt_id", "pt_name", "pt_price","pt_currency", "pt_file", 
							"pt_set_status", "pt_logo","pt_params","pt_comments", "pt_ordering",
							"pt_debug","pt_admin_only","pt_enabled", "pt_deleted"),
					"name"=>array(1=>"Id", "Name", "Price","Currency", "Template", 
							"Set status after complete","Logo","Params","Comments","Ordering",
							"Debug mode on","Admin only","Enabled", "Deleted"),
					"input_type"=>array(1=>"hidden","text","text","select","label",
							"select","image","text","textarea","text",
							"checkbox","checkbox","checkbox","hidden"),
					"val_type"=>array(1=>"int","string","currency","int","string",
							"int","string","string","text","int",
							"boolean","boolean","boolean","boolean"),
					"default_value"=>array(4=>DEFAULT_CURRENCY),
					"update_type"	=> array(8=>0),
					"upload_path"=>array(7=>"pts"),
					"check_value"=>array(4=>1),
					"ch_table" => array(4=>"currency",6=>"orders_status"),
					"ch_field" => array(4=>"c_name",6=>"os_name"),
					"ch_id"    => array(4=>"c_id",6=>"os_id"),
					"ch_enabled" => array(4=>"c_enabled",6=>"os_enabled"),
					"ch_deleted" => array(4=>"c_deleted",6=>"os_deleted")
);
?>