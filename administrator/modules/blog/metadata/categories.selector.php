<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname='blogs_cats';
$keystring='bc_id';
$namestring='bc_name';
$alias_field = 'bc_alias';
$selector=true;
$enabled='bc_enabled';
$deleted='bc_deleted';
$nametabl="Blogs categories list";
$cur_table_arr=array(
		"field"=>array(1=>'bc_id', 'bc_id_parent', 'bc_name','bc_enabled','bc_deleted'),
		"name"=>array(1=>"Category ID", "Parent category","Category name", 'Enabled', 'Deleted'),
		"size"=>array(1=>"80"),
		"view"=>array(1=>1,1,1,0,0),
		"ch_table" => array(1=>"", 2=>"blogs_cats"),
		"ch_field" => array(1=>"", 2=>"bc_name"),
		"ch_id"    => array(1=>"", 2=>"bc_id")
);
?>