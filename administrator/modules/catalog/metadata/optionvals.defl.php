<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname='goods_opt_vals';
$use_view_rights="options";
$keystring='ov_id';
$namestring='ov_name';
$enabled='ov_enabled';
$deleted='ov_deleted';
$nametabl="Goods option vals";
$ordering_field	= "ov_ordering";
$parent_view="options";
$parent_code='o_id';
$parent_name='o_title';
$multy_field='ov_opt_id';
$buttons=array(
		"go_up"=> array("show"=>1,"link"=>false),
		"new"=>array("show"=>1,"view"=>"optionvals","task"=>"modifyOptionval"),
		"filter"=>array("show"=>1,"view"=>"optionvals"),
		"modify"=>array("show"=>1,"view"=>"optionvals","task"=>"modifyOptionval"),
		"reorder"=> array("show"=>1,"view"=>"optionvals","task"=>"reorder"),
		"delete"=>array("show"=>1,"view"=>"optionvals","task"=>"delete"),
		"undelete"=>array("show"=>1,"view"=>"optionvals","task"=>"delete"),
		"trash"=>array("show"=>1,"view"=>"optionvals","link"=>false),
		"clean_trash"=>array("show"=>1,"view"=>"optionvals","link"=>false)
);
$cur_table_arr=array(
		"field"	=>array(1=>'ov_id','ov_opt_id','ov_name','ov_thumb','ov_ordering','ov_enabled','ov_deleted'),
		"name"	=>array(1=>'ID','Option name','Title','Image','Ordering','Enabled','Deleted'),
		"input_type"=>array(1=>'hidden','label_sel','text','image','text','checkbox','hidden'),
		"val_type"=>array(1=>'int','int','string','string','int','boolean','boolean'),
		"default_value"=>array(6=>1),
		"ch_table" => array(2=>"goods_options"),
		"ch_field" => array(2=>"o_title"),
		"ch_id"    => array(2=>"o_id"),
		"upload_path"=>array(4=>'i/opt_vals/thumbs')
);
?>