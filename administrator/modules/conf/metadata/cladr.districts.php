<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname	= "addr_districts";
$keystring	= "d_id";
$namestring	= "d_name";
$deleted	= "d_deleted";
$enabled	= "d_enabled";
$nametabl	= "Districts/Towns";
$ordering_field	= "d_ordering";		// Поле порядка отображения
$multy_field="d_parent_id";
/*это для апкей и родительской метадаты */
$parent_view="cladr.regions";
$buttons = array(
		"go_up"		=> array("show"=>1,"link"=>false),
		"new"		=> array("show"=>1,"view"=>"cladr","layout"=>"districts","task"=>"modify"),
		"filter"	=> array("show"=>1,"view"=>"cladr","layout"=>"districts"),
		"modify"	=> array("show"=>1,"view"=>"cladr","layout"=>"districts","task"=>"modify"),
		"delete"	=> array("show"=>1,"view"=>"cladr","layout"=>"districts","task"=>"delete"),
		"reorder"	=> array("show"=>1,"view"=>"cladr","layout"=>"districts","task"=>"reorder"),
		"trash"		=> array("show"=>1,"link"=>false)
);
$cur_table_arr = array(
		"field"			=> array(1=>"d_id", "d_parent_id", "d_name", "d_long", "d_lat", "d_show_on_map", "d_ordering","d_enabled", "d_deleted"),
		"name"			=> array(1=>"ID","Region","Title","Longitude","Latitude","Show on map","Ordering","Enabled","Deleted"),
		"input_type"	=> array(1=>"hidden", "select","text","text","text","checkbox", "text","checkbox","hidden"),
		"val_type"		=> array(1=>"int","int","string","float","float","boolean","int","boolean","boolean"),
		"link"			=>array(3=>"index.php?module=conf&view=cladr&layout=localities&psid=[0]"),
		"link_key"		=>array(3=>"id"),
		"link_type"		=>array(3=>""),
		"view"			=> array(1=>0,0,1,1,1,1,1,1,0),
		"ch_table" 		=> array(1=>"", 2=>"addr_regions",),
		"ch_field" 		=> array(1=>"", 2=>"r_name",),
		"ch_id"    		=> array(1=>"", 2=>"r_id",),
);
?>