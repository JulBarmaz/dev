<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname	= 'goods_prices';
$use_view_rights="goods";
$keystring	= 'p_id';
$enabled	= 'p_enabled';
//$deleted	= 'p_deleted';
$nametabl	= "Advanced price management";
$multy_field='p_g_id';
$parent_view="goods";
$parent_name='g_name';
$parent_code='g_id';
$keysort	= "p_quantity";	
//$ordering_parent = "i_gid";			// Поле внутри которого существует порядок отображения
$buttons = array(
		"go_up"=> array("show"=>1,"link"=>false),
		"new"=>array("show"=>1,"view"=>"goodsprices","task"=>"modify"),
		"filter"=>array("show"=>0,"view"=>"goodsprices"),
		"modify"=>array("show"=>1,"view"=>"goodsprices","task"=>"modify"),
		"reorder"=> array("show"=>0,"view"=>"goodsprices","task"=>"reorder"),
		"delete"=>array("show"=>1,"view"=>"goodsprices","task"=>"deleteNow"),
);
$cur_table_arr = array(
		"field"		=> array(1=>'p_id', 'p_g_id', 'p_quantity', 'p_price_1', 'p_price_2', 
								'p_price_3', 'p_price_4', 'p_price_5', 'p_enabled', 'p_change_date', 
								'p_change_uid'),
		"name"		=> array(1=>'ID', 'Product', 'Quantity start', 'Price 1', 'Price 2', 
								'Price 3','Price 4','Price 5', 'Enabled', 'Last change date', 
								'Last changer'),
		"val_type"	=> array(1=>'int','int','float','currency','currency',
								'currency','currency','currency', 'boolean', 'datetime',
								'int'),
		"input_type"	=> array(1=>'hidden','label_sel','text','text','text',
								'text','text','text','checkbox', 'label',
								'label_sel'),
		"default_value"=> array(11=>"AUTHOR"),
		"ch_table" => array(2=>"goods", 11=>"users"),
		"ch_field" => array(2=>"g_name", 11=>'u_login'),
		"ch_id"    => array(2=>"g_id", 11=>'u_id' ),
);
?>