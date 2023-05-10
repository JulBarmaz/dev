<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname='goods_stat';
$keystring='gs_id';
$namestring='gs_id';
$deleted='gs_deleted';
$enabled="gs_enabled";
$nametabl="Transition statistics from other sites";

$buttons=array(
		"go_up"=> array("show"=>0,"link"=>false),
		"new"=>array("show"=>0,"view"=>"goods_stat","task"=>"modify"),
		"clone"=>array("show"=>0,"view"=>"goods_stat","task"=>"make_clone"),
		"filter"=>array("show"=>1,"view"=>"goods_stat"),
		"modify"=>array("show"=>0,"view"=>"goods_stat","task"=>"modify"),
		"reorder"	=> array("show"=>0,"view"=>"goods_stat","task"=>"reorder"),
		"modify_links"=>array("show"=>0,"view"=>"goods_stat","task"=>"modifyLinks"),
		"delete"=>array("show"=>1,"view"=>"goods_stat","task"=>"delete"),
		"undelete"=>array("show"=>1,"view"=>"goods_stat","task"=>"delete"),
		"trash"=>array("show"=>1,"link"=>false),
		"clean_trash"=>array("show"=>1,"link"=>false)
);

$cur_table_arr=array(
		"field"=>array(1=>'gs_id',	'gs_remote_url', 'gs_goods_id',	'gs_count',	'gs_enabled', 'gs_deleted'),
		"name"=>array(1=>'ID',	'Remote URL', 'Goods name',	'Transitions',	'Enabled', 'Deleted'),
		"input_type"=>array(1=>'hidden','text','label_sel','text','checkbox','hidden'),
		"val_type"=>array(1=>'int','string','int','string','boolean','boolean'),
		"ch_table" => array(3=>'goods'),
		"ch_field" => array(3=>'g_name'),
		"ch_id"    => array(3=>'g_id'),
);
?>
