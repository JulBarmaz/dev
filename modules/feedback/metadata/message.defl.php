<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname='feedback';
$keystring='f_id';
$deleted='f_deleted';
$keysort="f_date";
$nametabl="Feedbacks list";
$buttons1=array(
		"go_up"=> array("show"=>0,"link"=>false),
		"new"=>array("show"=>0,"view"=>"messages","task"=>"modify"),
		"filter"=>array("show"=>0,"view"=>"messages"),
		"modify"=>array("show"=>0,"view"=>"messages","task"=>"modify"),
		"delete"=>array("show"=>0,"view"=>"messages","task"=>"delete"),
		"undelete"=>array("show"=>0,"view"=>"messages","task"=>"delete"),
		"trash"=>array("show"=>0,"link"=>false),
		"clean_trash"	=> array("show"=>0,"link"=>false)
);
$cur_table_arr=array(
		"field"=>array(1=>'f_id','f_uid', 'f_sender', 'f_mail', 'f_ip', 'f_theme',
						'f_text', 'f_date', 'f_sent'),
		"name" =>array(1=>'ID', 'Author ID', 'Your nick name', 'Your e-mail', 'IP', 'Feedback theme',
						'Feedback text', 'Date','Sent'),
		"input_type"=>array(1=>'hidden','hidden', 'text', 'text', 'hidden', 'text',
						'textarea', 'hidden', 'hidden'),
		"val_type"=>array(1=>'int','int', 'string', 'string', 'string', 'string',
						'text', 'datetime', 'boolean'),
		"view"=>array(1=>0, 0, 1, 1, 1, 1, 0, 1, 1),
		"check_value"=>array(1=>0, 0, 1, 1, 0, 1, 1, 0, 0),
);
?>