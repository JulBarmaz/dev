<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname='comms';
$keystring='cm_id';
$namestring='cm_title';
$nametabl="Comments";
$enabled="cm_published";
$deleted='cm_deleted';
$parent_view="groups";
$parent_table="comms_grp";
$parent_code='cg_id';
$parent_name='cg_title';
$multy_field="cm_grp_id";
$buttons=array(
			"go_up"=> array("show"=>1,"link"=>false),
			"new"=>array("show"=>0,"view"=>"comments","task"=>"modify"),
			"filter"=>array("show"=>1,"view"=>"comments"),
			"modify"=>array("show"=>1,"view"=>"comments","task"=>"modify"),
			"delete"=>array("show"=>1,"view"=>"comments","task"=>"delete"),
			"undelete"=>array("show"=>1,"view"=>"comments","task"=>"delete"),
			"trash"=>array("show"=>1,"link"=>false),
			"clean_trash"	=> array("show"=>1,"link"=>false)
);
$cur_table_arr=array(
				"field"=>array(1=>'cm_id','cm_grp_id','cm_obj_id','cm_parent_id', 'cm_uid',
								'cm_nickname','cm_email','cm_date','cm_ip','cm_title', 
								'cm_text','cm_rating','cm_cat','cm_type','cm_published',
								'cm_deleted'),
				"name"=>array(1=>'ID','Group','Objject ID','Parent', 'User id',
								'User nickname','User email','Date','IP','Title',
								'Text','Raiting','Category','Type','Published',
								'Deleted'),
				"input_type"=>array(1=>'label','label_sel','label','label', 'label_sel',
								'label','label','label','label','label', 
								'textarea','text','select','select','checkbox',
								'hidden'),
				"val_type"=>array(1=>'int','int','int','int', 'int',
								'string','string','datetime','string','string', 
								'text','int','int','int','boolean',
								'boolean'),
				"ch_table" => array(1=>"", 2=>"comms_grp",5=>"users",13=>"comms_cat",14=>"comms_types"),
				"ch_field" => array(1=>"", 2=>"cg_title",5=>"u_nickname",13=>"cc_title",14=>"ct_title"),
				"ch_id"    => array(1=>"", 2=>"cg_id",5=>"u_id",13=>"cc_id",14=>"ct_id"),
				"ch_enabled"    => array(1=>"", 2=>"cg_enabled",13=>"cc_enabled",14=>"ct_enabled"),
				"ch_deleted"    => array(1=>"", 2=>"cg_deleted",5=>"u_deleted",13=>"cc_deleted",14=>"ct_deleted")
);
?>