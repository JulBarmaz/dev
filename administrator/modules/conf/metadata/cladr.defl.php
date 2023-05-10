<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname	= "addr_countries";
$keystring	= "c_id";
$namestring	= "c_name";
$deleted	= "c_deleted";
$enabled	= "c_enabled";
$nametabl	= "Countries";
$ordering_field	= "c_ordering";		// Поле порядка отображения
$buttons = array(
	"new"			=> array("show"=>1,"view"=>"cladr","task"=>"modify"),
	"filter"		=> array("show"=>1,"view"=>"cladr"),
	"modify"		=> array("show"=>1,"view"=>"cladr","task"=>"modify"),
	"delete"		=> array("show"=>1,"view"=>"cladr","task"=>"delete"),
	"reorder"		=> array("show"=>1,"view"=>"cladr","task"=>"reorder"),
	"trash"			=> array("show"=>1,"link"=>false),
	"clean_trash"	=> array("show"=>1,"link"=>false)
);
$cur_table_arr = array(
	"field"				=> array(1=>"c_id", "c_name","c_alpha_2","c_alpha_3","c_descr","c_ordering","c_enabled","c_deleted"),
	"name"				=> array(1=>"ID","Title","Alpha 2 code","Alpha 3 code","Description","Ordering","Enabled","Deleted"),
	"input_type"		=> array(1=>"hidden", "text", "text", "text", "textarea", "text","checkbox","hidden"),
	"val_type"			=> array(1=>"int","string","string","string", "text","int","boolean","boolean"),
	"link"				=> array(2=>"index.php?module=conf&view=cladr&layout=regions&psid=[0]"),
	"link_key"			=> array(2=>"id"),
	"link_type"			=> array(2=>""),
	"view"				=> array(1=>0,1,1,1,1,1,1,0)
);
?>