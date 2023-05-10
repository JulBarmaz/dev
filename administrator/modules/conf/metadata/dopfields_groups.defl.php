<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname='fields_groups';
$use_view_rights="dopfields";
$keystring='fg_id';
$namestring='fg_name';
$deleted='fg_deleted';
$enabled='fg_enabled';
$nametabl="Additional fields groups";

$buttons=array(
		// "go_up"=> array("view"=>1,"link"=>false),
		"new"=>array("show"=>1,"view"=>"dopfields_groups","task"=>"modify"),
		"filter"=>array("show"=>1,"view"=>"dopfields_groups"),
		"modify"=>array("show"=>1,"view"=>"dopfields_groups","task"=>"modify"),
		"delete"=>array("show"=>1,"view"=>"dopfields_groups","task"=>"delete"),
		"undelete"=>array("show"=>1,"view"=>"dopfields_groups","task"=>"delete"),
		"trash"=>array("show"=>1,"view"=>"dopfields_groups","link"=>false),
		"clean_trash"=>array("show"=>0,"view"=>"dopfields_groups","link"=>false)
);
$cur_table_arr=array(
		"field"=>array(1=>'fg_id','fg_name','fg_vals_count','fg_comment','fg_deleted','fg_enabled'),
		"name"=>array(1=>'ID','Title','Fields','Comment','Deleted','Enabled'),
		"input_type"=>array(1=>'hidden','text','label','textarea','hidden','checkbox'),
		"input_size"=>array(1=>false),
		"view"=>array(1=>1,1,1,0,0,0),
		"fim"=>array(3=>"countFieldVals"),
		"val_type"=>array(1=>'int','string','string','text','boolean','boolean'),
		"link"=>array(1=>'index.php?module=conf&view=dopfields_groups&task=modify&psid=[0]&multy_code=[1]',
					2=>'index.php?module=conf&view=dopfields&psid=[0]'),
		"link_key"=>array(1=>'id,multy_code',2=>'id'),
		"check_value"=>array(1=>0,1,0,0,0,0)
);
?>