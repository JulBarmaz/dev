<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname='taxes';
$keystring='t_id';
$enabled='t_enabled';
$deleted='t_deleted';
$nametabl="List of taxes";
$buttons=array(
				"new"=>array("show"=>1,"view"=>"taxes","task"=>"modify"),
				"filter"=>array("show"=>1,"view"=>"taxes"),
				"modify"=>array("show"=>1,"view"=>"taxes","task"=>"modify"),
				"delete"=>array("show"=>1,"view"=>"taxes","task"=>"deleteTax"),
				"undelete"=>array("show"=>1,"view"=>"taxes","task"=>"deleteTax"),
				"trash"=>array("show"=>1,"link"=>false)
);
$cur_table_arr=array(
				"field"=>array(1=>'t_id','t_name','t_value','t_fixed', 't_comment', 't_enabled', 't_deleted'),
				"name"=>array(1=>'Code','Name', "Tax value", "Is fixed", "Comments", "Enabled", "Deleted"),
				"input_type"=>array(1=>'hidden','text','text','checkbox','textarea','checkbox','hidden'),
				"val_type"=>array(1=>'int','string','float','boolean','text','boolean','boolean'),
);
?>