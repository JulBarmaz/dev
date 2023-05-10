<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname='fields_choices';
$use_view_rights="dopfields";
$keystring='fc_id';
$namestring='fc_value';
$enabled='fc_enabled';
$deleted='fc_deleted';
$nametabl="Field values";
$ordering_field	= "fc_ordering";
$parent_view="dopfields";
$parent_code='f_id';
//$parent_name='f_descr';
$multy_field='fc_field_id';
$buttons=array(
		"go_up"=> array("show"=>1,"link"=>false),
		"new"=>array("show"=>1,"view"=>"dopfields_choices","task"=>"modifyDopfields_choices"),
		"filter"=>array("show"=>1,"view"=>"dopfields_choices"),
		"modify"=>array("show"=>1,"view"=>"dopfields_choices","task"=>"modifyDopfields_choices"),
		"reorder"=> array("show"=>1,"view"=>"dopfields_choices","task"=>"reorder"),
		"delete"=>array("show"=>1,"view"=>"dopfields_choices","task"=>"delete"),
		"undelete"=>array("show"=>1,"view"=>"dopfields_choices","task"=>"delete"),
		"trash"=>array("show"=>1,"view"=>"dopfields_choices","link"=>false),
		"clean_trash"=>array("show"=>1,"view"=>"dopfields_choices","link"=>false)
);
$cur_table_arr=array(
		"field"	=>array(1=>'fc_id', 'fc_field_id', 'fc_value', 'fc_ordering', 'fc_enabled', 'fc_deleted'),
		"name"	=>array(1=>'ID','Field','Value','Ordering','Enabled','Deleted'),
		"input_type"=>array(1=>'hidden','label_sel','text','text','checkbox','hidden'),
		"view"=>array(1=>1,0,1,1,1,0),
		"val_type"=>array(1=>'int','int','string','int','boolean','boolean'),
		"link"=>array(3=>'index.php?module=conf&view=dopfields_choices&task=modifyDopfields_choices&psid=[0]&multy_code=[1]'),
		"link_key"=>array(3=>'id,multy_code'),
		"default_value"=>array(5=>1),
		"ch_table" => array(2=>"fields_list"),
		"ch_field" => array(2=>"f_descr"),
		"ch_id"    => array(2=>"f_id"),
);
?>