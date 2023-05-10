<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname	= "mailer_log";
$keystring	= "err_time";
$namestring	= "m_theme";
//$deleted	= "c_deleted";
//$enabled	= "c_enabled";
$checkbox=false;
$nametabl	= "Mailer log";
//$ordering_field	= "err_time";		// Поле порядка отображения
$uni_buttons=array(	
	"clean_trash"=>array(
	"title"=>"Clean mailer log",
	"alt"=>'X',
	"reset_multy"=>"true",
	"task"=>'cleanMailerlog',
	"view"=>'mailerlog',
	"module"=>'service',
	"position"=>"right",
	"confirm"=>"Clean mailer log"
	),
);
$buttons = array(
	"new"			=> array("show"=>0,"view"=>"mailerlog","task"=>"modify"),
	"filter"		=> array("show"=>1,"view"=>"mailerlog"),
	"modify"		=> array("show"=>0,"view"=>"mailerlog","task"=>"modify"),
	"delete"		=> array("show"=>0,"view"=>"mailerlog","task"=>"delete"),
	"reorder"		=> array("show"=>0,"view"=>"mailerlog","task"=>"reorder"),
	"trash"			=> array("show"=>0,"link"=>false),
	"clean_trash"	=> array("show"=>0,"link"=>false)
);
$cur_table_arr = array(
	"field"				=> array(1=>"email", "err_code", "err_text", "err_response", "err_time","m_theme"),
	"name"				=> array(1=>"Email","Error code","Error text","Server response","Time","Theme"),
	"input_type"		=> array(1=>"text", "text", "text", "text", "text", "text"),
	"val_type"			=> array(1=>"string","string","string","string","string","string"),
	"sort_order"		=> array(5=>"DESC"),
);
?>