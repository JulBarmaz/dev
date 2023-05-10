<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname='blacklist';
$keystring='bl_id';
$deleted='bl_deleted';
$enabled="bl_enabled";
$nametabl="Blacklist";

$buttons=array(
		"go_up"=> array("show"=>0,"link"=>false),
		"new"=>array("show"=>1,"view"=>"blacklist","task"=>"modifyBlacklist"),
		"filter"=>array("show"=>1,"view"=>"blacklist"),
		"modify"=>array("show"=>1,"view"=>"blacklist","task"=>"modifyBlacklist"),
		"undelete"=>array("show"=>1,"view"=>"blacklist","task"=>"delete"),
		"delete"=>array("show"=>1,"view"=>"blacklist","task"=>"delete"),
		"clean_trash"	=> array("show"=>1,"link"=>false),
		"trash"=>array("show"=>1,"link"=>false)
);
$cur_table_arr=array(
				"field"=>array(1=>'bl_id', 	'bl_val', 	'bl_type', 	'bl_enabled', 	'bl_deleted'), 
				"name"=>array(1=>'ID', 	'Value', 	'Type', 	'Enabled', 	'Deleted'), 
				"input_type"=>array(1=>'hidden','text','select','checkbox','hidden'),
				"val_type"=>array(1=>'int','string','string','boolean','boolean'),
				"view"=>array(1=>0,1,1,1,0),
				"ck_reestr"   => array(3=>"blacklist_type")

);
?>