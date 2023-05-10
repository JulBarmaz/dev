<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname='goods_options_data';
$use_view_rights="goods";
$keystring='od_id';
$namestring='od_id';
$enabled='od_enabled';
$nametabl="Goods options";
$multy_field='od_obj_id';
$parent_table="goods";
$parent_name='g_name';
$parent_code='g_id';
$parent_view="goods";
$ordering_field	= "od_ordering";
$buttons=array(
		"go_up"=> array("show"=>1,"link"=>false),
		"new"=>array("show"=>1,"view"=>"options_data","task"=>"modifyOption_data"),
		"filter"=>array("show"=>1,"view"=>"options_data"),
		"modify"=>array("show"=>1,"view"=>"options_data","task"=>"modifyOption_data"),
		"reorder"=> array("show"=>1,"view"=>"options_data","task"=>"reorder"),
		"delete"=>array("show"=>1,"view"=>"options_data","task"=>"deleteNow"),
);
$cur_table_arr=array(
		"field"=>array(1=>'od_id','od_obj_id','od_opt_id','od_vals_count','od_ordering','od_enabled'),
		"name"=>array(1=>'ID','Product',"Label <Description>","Option values","Ordering","Enabled"),
		"input_type"=>array(1=>'hidden','label_sel',"select","label",'text','checkbox'),
		"val_type"=>array(1=>'int','int','int','string','int','boolean'),
		"input_view"=>array(4=>0),
		"fim"=>array(4=>"countOptionsVals"),
		"default_value"=>array(6=>1),
		"link_type"=>array(4=>''),
		"check_value"=>array(1=>0,1,3,0),
		"update_type"=>array(3=>0),
		"ch_table" => array(2=>"goods",3=>"goods_options"),
		"ch_field" => array(2=>"g_name", 3=>"o_title"),
		"ch_id"    => array(2=>"g_id",3=>"o_id"),
);
?>