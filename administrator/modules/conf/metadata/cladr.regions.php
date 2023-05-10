<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname	= "addr_regions";
$keystring	= "r_id";
$namestring	= "r_name";
$deleted	= "r_deleted";
$enabled	= "r_enabled";
$nametabl	= "Regions";
$ordering_field	= "r_ordering";		// Поле порядка отображения
$multy_field="r_parent_id";
/*это для апкей и родительской метадаты */
$parent_view="cladr";
$buttons = array(
		"go_up"=> array("show"=>1,"link"=>false),
		"new"		=> array("show"=>1,"view"=>"cladr","task"=>"modify"),
		"filter"	=> array("show"=>1,"view"=>"cladr","layout"=>"regions"),
		"modify"	=> array("show"=>1,"view"=>"cladr","task"=>"modify"),
		"delete"	=> array("show"=>1,"view"=>"cladr","task"=>"delete"),
		"reorder"	=> array("show"=>1,"view"=>"cladr","task"=>"reorder"),
		"trash"		=> array("show"=>1,"link"=>false)
);
$cur_table_arr = array(
		"field"			=> array(1=>"r_id", "r_parent_id", "r_name","r_ordering","r_enabled","r_deleted"),
		"name"			=> array(1=>"ID","Country","Title","Ordering","Enabled","Deleted"),
		"input_type"	=> array(1=>"hidden", "select","text", "text","checkbox","hidden"),
		"val_type"		=> array(1=>"int","int","string","int","boolean","boolean"),
		"link"			=>array(3=>"index.php?module=conf&view=cladr&layout=districts&psid=[0]"),
		"link_key"		=>array(3=>"id"),
		"link_type"		=>array(3=>""),
		"view"			=> array(1=>0,0,1,1,1,0),
		"ch_table" 		=> array(1=>"", 	2=>"addr_countries",),
		"ch_field" 		=> array(1=>"", 	2=>"c_name",),
		"ch_id"    		=> array(1=>"", 	2=>"c_id",),
);
?>