<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname	= 'template_zones';
$keystring	= 'tz_id';
$deleted	= 'tz_deleted';
$enabled	= 'tz_enabled';
$ordering_field	= 'tz_ordering';
$nametabl	= "Template zones";
$buttons = array(
		"new"		=> array("show"=>1,"view"=>"tmplzones","task"=>"modify"),
		"filter"	=> array("show"=>1,"view"=>"tmplzones"),
		"modify"	=> array("show"=>1,"view"=>"tmplzones","task"=>"modify"),
		"delete"	=> array("show"=>1,"view"=>"tmplzones","task"=>"delete"),
		"reorder"	=> array("show"=>1,"view"=>"tmplzones","task"=>"reorder"),
		"undelete"=> array("show"=>1,"view"=>"tmplzones","task"=>"delete"),
		"clean_trash"		=> array("show"=>1,"link"=>false),
		"trash"		=> array("show"=>1,"link"=>false)
);
$cur_table_arr = array(
		"field"				=> array(1=>'tz_id','tz_name','tz_descr','tz_ordering','tz_enabled','tz_deleted'),
		"name"				=> array(1=>'ID','Name','Description','Ordering','Enabled','Deleted'),
		"input_type"		=> array(1=>'hidden','text','text','text','checkbox','hidden'),
		"default_value"		=> array(9=>"NOW"),
		"val_type"			=> array(1=>'int','string','string','int','boolean','boolean'),
		"view"				=> array(1=>0,1,1,1,1,0),
);
?>