<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname='discounts';
$keystring='d_id';
$namestring='d_name';
$enabled='d_enabled';
$deleted='d_deleted';
$nametabl="Discounts and surcharges";
$selector=true;

$cur_table_arr=array(
		"field"=>array(1=>'d_id', 'd_name','d_enabled','d_deleted'),
		"name"=>array(1=>'ID', 'Discount name', 'Enabled', 'Deleted'),
		"view"=>array(1=>1,1,0,0),
);
?>