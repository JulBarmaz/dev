<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname='feedback';
$keystring='f_id';
$deleted='f_deleted';
$keysort="f_date";
$nametabl="Feedbacks list";
$buttons=array(
		"go_up"=> array("show"=>0,"link"=>false),
		"new"=>array("show"=>0,"view"=>"messages","task"=>"modify"),
		"filter"=>array("show"=>1,"view"=>"messages"),
		"modify"=>array("show"=>1,"view"=>"messages","task"=>"modify"),
		"delete"=>array("show"=>1,"view"=>"messages","task"=>"delete"),
		"undelete"=>array("show"=>1,"view"=>"messages","task"=>"delete"),
		"trash"=>array("show"=>1,"link"=>false),
		"clean_trash"	=> array("show"=>1,"link"=>false)
);
$cur_table_arr=array(
		"field"=>array(1=>'f_id', 'f_sender', 'f_mail', 'f_ip', 'f_theme',
				'f_text', 'f_date', 'f_sent','f_read', 'f_readdate',
				'f_comments', 'f_deleted'),
		"name" =>array(1=>'ID', 'Author', 'E-mail', 'IP', 'Theme',
				'Text', 'Date', 'Sent','Is read', 'Read date',
				'Comments', 'Delete mark'),
		"input_type"=>array(1=>'hidden', 'label', 'label', 'label', 'label',
				'formated', 'label', 'label', 'label', 'label',
				'texteditor', 'hidden'),
		"val_type"=>array(1=>'int', 'string', 'string', 'string', 'string',
				'text', 'datetime', 'boolean', 'boolean', 'datetime',
				'text', 'boolean'),
		"sort_order"=>array(7=>"DESC"),
		"update_type"=>array(1=>0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0),
		"view"=>array(1=>0, 1, 1, 1, 1, 0, 1, 1, 1, 1, 0, 0),
		"link"=>array(5=>'index.php?module=feedback&view=messages&layout=single&psid=[0]'),
		"link_key"=>array(5=>'id'),
		"link_type"=>array(5=>'popup')
);
?>