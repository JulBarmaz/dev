<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname='currency_rate';
$keystring='cr_id';
$nametabl="List of currency rates";
$multy_field='c_id';
$parent_view="currency";
$parent_code='c_id';
$keysort="c_datetime";
$buttons=array(
		"go_up"=> array("show"=>1,"link"=>false),
		"new"=>array("show"=>1,"view"=>"currency_rate","task"=>"modify"),
		"filter"=>array("show"=>1,"view"=>"currency_rate"),
		"modify"=>array("show"=>1,"view"=>"currency_rate","task"=>"modify"),
		"delete"=>array("show"=>1,"view"=>"currency_rate","task"=>"deleteNow"),
		"undelete"=>array("show"=>0,"view"=>"currency_rate","task"=>"delete"),
		"trash"=>array("show"=>0,"link"=>false)
);
$cur_table_arr=array(
		"field"=>array(1=>'cr_id','c_id','c_datetime','c_value'),
		"sort_order"=>array(3=>"DESC"),
		"name"=>array(1=>'ID','Currency','Starts from','Rate in base currency'),
		"input_type"=>array(1=>'hidden','label_sel','datetime_ajax','text'),
		"val_type"=>array(1=>'int','int','datetime','string'),
		"default_value"=>array(3=>"NOW"),
		"ch_table" => array(2=>"currency"),
		"ch_field" => array(2=>"c_name"),
		"ch_id"    => array(2=>"c_id")
);
?>