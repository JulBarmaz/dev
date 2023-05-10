<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname='feedback';
$keystring='f_id';
$deleted='f_deleted';
$nametabl="Feedbacks list";
$cur_table_arr=array(
		"field"=>array(1=>'f_id', 'f_sender', 'f_mail', 'f_ip', 'f_theme',
				'f_text', 'f_date', 'f_sent','f_read', 'f_readdate',
				'f_comments', 'f_deleted'),
		"name" =>array(1=>'ID', 'Author', 'E-mail', 'IP', 'Theme',
				'Text', 'Date', 'Sent','Is read', 'Read date',
				'Comments', 'Delete mark')
);
?>