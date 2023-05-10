<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname='goods_pts';
$keystring='pt_id';
$namestring='pt_name';
$enabled='pt_enabled';
$deleted='pt_deleted';
$nametabl="Payment types";
$selector=1;
$cur_table_arr=array(
		"field"=>array(1=> 'pt_name','pt_id','pt_enabled','pt_deleted'),
		"name"=>array(1=> 'Name', 'Id', 'Enabled', 'Deleted'),
		"view"=>array(1=>1,1,0,0)
);
?>