<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname='comms_types';
$keystring='ct_id';
$namestring='ct_title';
$nametabl="Comments types";
$enabled="ct_enabled";
$deleted='ct_deleted';
$parent_view="groups";
$parent_table="comms_grp";
$parent_code='cg_id';
$parent_name='cg_name';
$multy_field="ct_cgrp_id";
$buttons=array(
			"go_up"=> array("show"=>1,"link"=>false),
			"new"=>array("show"=>1,"view"=>"comtypes","task"=>"modify"),
			"filter"=>array("show"=>1,"view"=>"comtypes"),
			"modify"=>array("show"=>1,"view"=>"comtypes","task"=>"modify"),
			"delete"=>array("show"=>1,"view"=>"comtypes","task"=>"delete"),
			"undelete"=>array("show"=>1,"view"=>"comtypes","task"=>"delete"),
			"trash"=>array("show"=>1,"link"=>false),
			"clean_trash"	=> array("show"=>1,"link"=>false)
);
$cur_table_arr=array(
				"field"=>array(1=>'ct_id','ct_cgrp_id','ct_title','ct_marker','ct_enabled','ct_deleted'),
				"name"=>array(1=>'ID','Group','Title','Marker','Enabled','Deleted'),
				"input_type"=>array(1=>'hidden','select','text','select', 'checkbox', 'hidden'),
				"val_type"=>array(1=>'int','int','string','int','boolean', 'boolean'),
				"ch_table" => array(1=>"", 2=>"comms_grp"),
				"ch_field" => array(1=>"", 2=>"cg_title"),
				"ch_id"    => array(1=>"", 2=>"cg_id"),
				"ch_enabled"    => array(1=>"", 2=>"cg_enabled"),
				"ch_deleted"    => array(1=>"", 2=>"cg_deleted"),
				"ck_reestr"=> array(4=>'ym_markers')
);
?>