<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname='vendor_categories';
$keystring='vc_id';
$namestring='vc_name';
$deleted='vc_deleted';
$nametabl="Vendors categories";
$buttons=array(
				"new"=>array("show"=>1,"view"=>"vendor_cats","task"=>"modify"),
				"filter"=>array("show"=>1,"view"=>"vendor_cats"),
				"modify"=>array("show"=>1,"view"=>"vendor_cats","task"=>"modify"),
				"delete"=>array("show"=>1,"view"=>"vendor_cats","task"=>"delete"),
				"undelete"=>array("show"=>1,"view"=>"vendor_cats","task"=>"delete"),
				"trash"=>array("show"=>1,"link"=>false),
				"clean_trash"=>array("show"=>1,"link"=>false)
);
$cur_table_arr=array(
				"field"=>array(1=>'vc_id','vc_name','vc_desc','vc_deleted'),
				"name"=>array(1=>'Code','Name','Description','Deleted'),
				"input_type"=>array(1=>'hidden','text','textarea',"hidden"),
				"val_type"=>array(1=>'int','string','string','boolean'),
				"check_value"=>array(2=>1),
				"link"=>array(1=>'','index.php?module=catalog&view=vendors&psid=[0]'),
				"link_key"=>array(1=>'','id'),
				"link_type"=>array(1=>'','')
);
?>