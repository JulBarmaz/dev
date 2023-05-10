<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname='currency';
$keystring='c_id';
$namestring='c_name';
$deleted='c_deleted';
$enabled="c_enabled";
$nametabl="List of currencies";
$buttons=array(
		"new"=>array("show"=>1,"view"=>"currency","task"=>"modify"),
		"filter"=>array("show"=>1,"view"=>"currency"),
		"modify"=>array("show"=>1,"view"=>"currency","task"=>"modify"),
		"delete"=>array("show"=>1,"view"=>"currency","task"=>"delete"),
		"undelete"=>array("show"=>1,"view"=>"currency","task"=>"delete"),
		"trash"=>array("show"=>1,"link"=>false)
);
$cur_table_arr=array(
		"field"=>array(1=>'c_id','c_code','c_name','c_short_name','c_enabled','c_deleted'),
		"name"=>array(1=>'ID','Code','Name','Short name','Enabled','Deleted'),
		"input_type"=>array(1=>'hidden','text','text','text','checkbox','hidden'),
		"val_type"=>array(1=>'int','string','string','string','boolean','boolean'),
		"check_value"=>array(2=>3),
		"link"=>array(1=>'','','index.php?module=catalog&view=currency_rate&psid=[0]'),
		"link_key"=>array(1=>'','','$id'),
		"link_type"=>array(1=>'','','')
);
?>