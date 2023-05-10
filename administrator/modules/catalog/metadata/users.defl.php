<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname='users_vendors';
$keystring="uv_id";
$deleted="uv_deleted";
$enabled="uv_enabled";
$nametabl="Vendors links";
$buttons=array(
		"new"=>array("show"=>1,"view"=>"users","task"=>"modify"),
		"filter"=>array("show"=>1,"view"=>"users"),
		"modify"=>array("show"=>1,"view"=>"users","task"=>"modify"),
		"delete"=>array("show"=>1,"view"=>"users","task"=>"delete"),
		"undelete"=>array("show"=>1,"view"=>"users","task"=>"delete"),
		"trash"=>array("show"=>1,"link"=>false)
);
$cur_table_arr=array(
		"field"=>array(1=>"uv_id", "uv_uid", "uv_vid","uv_enabled","uv_deleted"),
		"name"=>array(1=>"ID","User", "Vendor","Enabled","Deleted"),
		"input_type"=>array(1=>"hidden","select","select","checkbox","hidden"),
		"val_type"=>array(1=>"int","int","int","boolean","boolean"),
		"ch_table" => array(2=>"users",3=>"vendors"),
		"ch_field" => array(2=>"u_login",3=>"v_name"),
		"ch_id"    => array(2=>"u_id",3=>"v_id"),
		"ch_deleted"  => array(2=>"u_deleted",3=>"v_deleted"),
		"ch_enabled"  => array(2=>"u_activated",3=>"v_enabled")
);
?>
