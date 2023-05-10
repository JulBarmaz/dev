<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname	= 'plugins';
$keystring	= 'p_id';
$deleted	= 'p_deleted';
$enabled	= 'p_enabled';
$ordering_field	= "p_ordering";		// Поле порядка отображения
// $ordering_parent = "p_path";		// Поле внутри которого существует порядок отображения
$nametabl	= "List of plugins";

$buttons = array(
	"new"		=> array("show"=>0,"view"=>"plugins","task"=>""),
	"filter"	=> array("show"=>0),
	"modify"	=> array("show"=>1,"view"=>"plugins","task"=>"modifyPlugin"),
	"delete"	=> array("show"=>0),
	"reorder"	=> array("show"=>1,"view"=>"plugins","task"=>"reorder"),
	"trash"		=> array("show"=>0,"link"=>false)
);

$cur_table_arr = array(
	"field"				=> array(1=>'p_id','p_path','p_name','p_params','p_ordering','p_enabled','p_deleted'),
	"view"				=> array(1=>0,1,1,0,1,0),
	"link"				=> array(3=>"index.php?module=conf&view=plugins&task=modifyPlugin&psid=[0]&page=[1]&sort=[2]&orderby=[3]"),
	"link_key"			=> array(3=>'id, page, sort, orderby'),
	"name"				=> array(1=>'ID','Path','Name','Params','Ordering','Enabled','Deleted'),
	"input_type"	=> array(1=>'hidden','label','label','text','text','checkbox','hidden'),
	"val_type"		=> array(1=>'int','string','string','string','int','boolean','boolean')
);
?>