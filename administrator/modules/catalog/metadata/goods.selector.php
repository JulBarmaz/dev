<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname='goods';
$keystring='g_id';
$namestring='g_name';
$selector_string='g_sku,g_name';
$deleted='g_deleted';
$enabled="g_enabled";
$nametabl="Goods list";
$selector=true;

$cur_table_arr=array(
		"field"=>array(1=>'g_id','g_sku','g_name','g_enabled','g_deleted'),
		"name"=>array(1=>'ID','SKU','Name', 'Enabled', 'Deleted'),
		"size"=>array(1=>"80","20%",''),
		"view"=>array(1=>1,1,1,0,0)
);
?>