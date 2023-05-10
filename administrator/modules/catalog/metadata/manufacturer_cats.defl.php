<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname='manufacturer_categories';
$keystring='mfc_id';
$namestring='mfc_name';
$deleted='mfc_deleted';
$nametabl="Manufacturers categories";
$buttons=array(
		"new"=>array("show"=>1,"view"=>"manufacturer_cats","task"=>"modify"),
		"filter"=>array("show"=>1,"view"=>"manufacturer_cats"),
		"modify"=>array("show"=>1,"view"=>"manufacturer_cats","task"=>"modify"),
		"delete"=>array("show"=>1,"view"=>"manufacturer_cats","task"=>"delete"),
		"undelete"=>array("show"=>1,"view"=>"manufacturer_cats","task"=>"delete"),
		"trash"=>array("show"=>1,"link"=>false),
		"clean_trash"=>array("show"=>1,"link"=>false)
);
$cur_table_arr=array(
		"field"=>array(1=>'mfc_id','mfc_name','mfc_desc','mfc_deleted'),
		"name"=>array(1=>'Code','Name','Description','Deleted'),
		"input_type"=>array(1=>'hidden','text','textarea',"hidden"),
		"val_type"=>array(1=>'int','string','string','boolean'),
		"link"=>array(1=>'','index.php?module=catalog&view=manufacturers&psid=[0]'),
		"link_key"=>array(1=>'','id'),
		"link_type"=>array(1=>'','')
);
?>
