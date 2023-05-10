<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname	= 'poll_items';
$keystring	= 'pi_id';
$deleted	= 'pi_deleted';
$nametabl	= "Poll items";
$multy_field='pi_poll_id';
$parent_view="polls";
$parent_code='p_id';
$ordering_field	= "pi_ordering";		// Поле порядка отображения
//$ordering_parent = "pi_poll_id";		// Поле внутри которого существует порядок отображения

$buttons = array(
			"go_up"=> array("show"=>1,"link"=>false),
			"new"		=> array("show"=>1,"view"=>"items","task"=>"modify"),
			"filter"	=> array("show"=>0,"view"=>"items"),
			"modify"	=> array("show"=>1,"view"=>"items","task"=>"modify"),
			"delete"	=> array("show"=>1,"view"=>"items","task"=>"delete"),
			"undelete"	=> array("show"=>1,"view"=>"items","task"=>"delete"),
			"reorder"	=> array("show"=>1,"view"=>"items","task"=>"reorder"),
			"trash"		=> array("show"=>1,"link"=>false)
);
$cur_table_arr = array(
			"field"		=> array(1=>'pi_id','pi_poll_id','pi_text','pi_hits','pi_ordering','pi_deleted'),
			"name"		=> array(1=>'ID','Poll','Title','Hits','Ordering','Delete mark'),
			"val_type"	=> array(1=>'int','int','string','int','int','boolean'),
			"input_type"	=> array(1=>'hidden','label_sel','text','label','text','hidden'),
			"ch_table" => array(2=>"polls"),
			"ch_field" => array(2=>"p_title"),
			"ch_id"    => array(2=>"p_id")
);
?>