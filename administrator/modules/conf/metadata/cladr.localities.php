<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname	= "addr_localities";
$keystring	= "l_id";
$namestring	= "l_name";
$deleted	= "l_deleted";
$enabled	= "l_enabled";
$nametabl	= "Localities";
$ordering_field	= "l_ordering";		// Поле порядка отображения
$multy_field="l_parent_id";
/*это для апкей и родительской метадаты */
$parent_view="cladr.districts";
$buttons = array(
		"go_up"		=> array("show"=>1,"link"=>false),
		"new"		=> array("show"=>1,"view"=>"cladr","layout"=>"localities","task"=>"modify"),
		"filter"	=> array("show"=>1,"view"=>"cladr","layout"=>"localities",),
		"modify"	=> array("show"=>1,"view"=>"cladr","layout"=>"localities","task"=>"modify"),
		"delete"	=> array("show"=>1,"view"=>"cladr","layout"=>"localities","task"=>"delete"),
		"reorder"	=> array("show"=>1,"view"=>"cladr","layout"=>"localities","task"=>"reorder"),
		"trash"		=> array("show"=>1,"link"=>false)
);
$cur_table_arr = array(
		"field"				=> array(1=>"l_id", "l_parent_id", "l_name", "l_long", "l_lat", "l_show_on_map", "l_ordering","l_enabled", "l_deleted"),
		"name"				=> array(1=>"ID","District/Town","Title","Longitude","Latitude","Show on map","Ordering","Enabled","Deleted"),
		"input_type"		=> array(1=>"hidden", "select","text","text","text","checkbox", "text","checkbox","hidden"),
		"val_type"			=> array(1=>"int","int","string","float","float","boolean","int","boolean","boolean"),
		"view"				=> array(1=>0,0,1,1,1,1,1,1,0),
		"ch_table" 			=> array(1=>"", 2=>"addr_districts"),
		"ch_field" 			=> array(1=>"", 2=>"d_name"),
		"ch_id"				=> array(1=>"", 2=>"d_id"),
);
?>