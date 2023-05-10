<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname='measure';
$keystring='meas_id';
$enabled='meas_enabled';
$deleted='meas_deleted';
$nametabl="List of measures";
$buttons=array(
		"new"=>array("show"=>1,"view"=>"measures","task"=>"modify"),
		"filter"=>array("show"=>1,"view"=>"measures"),
		"modify"=>array("show"=>1,"view"=>"measures","task"=>"modify"),
		"delete"=>array("show"=>1,"view"=>"measures","task"=>"delete"),
		"undelete"=>array("show"=>1,"view"=>"measures","task"=>"delete"),
		"trash"=>array("show"=>1,"link"=>false),
		"clean_trash"=>array("show"=>1,"link"=>false)

);
$cur_table_arr=array(
		"field"=>array(1=>'meas_id', 'meas_code', 'meas_short_name', 'meas_full_name', 	'meas_comment','meas_kf','meas_type', 'meas_enabled','meas_deleted'),
		"descr"=>array(6=>"Conversion coefficient must be equals to amount of main measure in selected measures"),
		"name"=>array(1=>'Id','Code','Short name', "Full name", "Comments",'Conversion coefficient','Type', "Enabled","Deleted"),
		"input_type"=>array(1=>'hidden','text','text','text','text','text','select','checkbox','hidden'),
		"val_type"=>array(1=>'int','string','string','string','string','float','int','boolean','boolean'),
		"check_value"=>array(2=>3),
		"ck_reestr"=>array(7=>"measure_type")
);
?>
