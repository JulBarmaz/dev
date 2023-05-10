<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname='orders_items';
$keystring='i_id';
$nametabl="Order items";
$multy_field='i_order_id';

$parent_table="orders";
$parent_name='o_id';
$parent_code='o_id';
$parent_view="orders";


$buttons=array(
		"go_up"=> array("show"=>1,"link"=>false),
		"new"=>array("show"=>0,"view"=>"orders","layout"=>"order","task"=>"modify"),
		"modify"=>array("show"=>0,"view"=>"orders","layout"=>"order","task"=>"modify"),
		"delete"=>array("show"=>1,"view"=>"orders","layout"=>"order","task"=>"deleteNow"),
		"undelete"=>array("show"=>0,"view"=>"orders","layout"=>"order","task"=>"delete"),
		"trash"=>array("show"=>0,"link"=>false)
);
$cur_table_arr=array(
		"field"=>array(1=>'i_id', 'i_order_id', 'i_g_id', 'i_g_name', 'i_g_options_text', 'i_g_options_files', 'i_g_quantity', 'i_g_measure', 'i_g_price', 'i_g_sum'),
		"name"=>array(1=> 'ID', 'Order number', 'Goods id', 'Name', 'Options', 'Files', 'Quantity', 'Measure', 'Price', 'Sum'),
		"sort_order"=>array(1=>'NONE','NONE','NONE','NONE','NONE','NONE','NONE','NONE','NONE','NONE'),
		"input_type"=>array(1=>'label','label','label','label','label','label','label','label','label','label'),
		"val_type"=>array(1=>'int','int','int',	'string','string','string','float','string','currency','currency'),
		"fim"=>array(6=>"getOrderFiles"),
		"ch_table" => array(8=>'measure'),
		"ch_field" => array(8=>'meas_short_name'),
		"ch_id"    => array(8=>'meas_id'),
		
);
?>