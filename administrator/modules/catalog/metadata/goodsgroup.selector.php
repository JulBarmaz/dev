<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname='goods_group';
$keystring='ggr_id';
$namestring='ggr_name';
$selector=true;
$enabled="ggr_enabled";
$deleted='ggr_deleted';
$nametabl="Goods groups list";
$cur_table_arr=array(
		"field"=>array(1=>'ggr_id', 'ggr_name', 'ggr_id_parent', 'ggr_alias', 'ggr_enabled', 'ggr_deleted'),
		"name"=>array(1=>"Group ID","Group name", "Group parent", 'Alias', 'Enabled', 'Deleted'),
		"size"=>array(1=>"80","20%",'','','',''),
		"view"=>array(1=>1,1,1,1,0,0),
		"ch_table" => array(1=>"", 3=>"goods_group"),
		"ch_field" => array(1=>"", 3=>"ggr_name"),
		"ch_id"    => array(1=>"", 3=>"ggr_id")
);
?>