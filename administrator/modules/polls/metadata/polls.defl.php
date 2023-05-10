<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname	= 'polls';
$keystring	= 'p_id';
$deleted	= 'p_deleted';
$enabled	= 'p_enabled';
$nametabl	= "Polls";
$namestring="p_title";

$buttons = array(
			"new"		=> array("show"=>1,"view"=>"polls","task"=>"modify"),
			"filter"	=> array("show"=>1,"view"=>"polls"),
			"modify"	=> array("show"=>1,"view"=>"polls","task"=>"modify"),
			"delete"	=> array("show"=>1,"view"=>"polls","task"=>"delete"),
			"undelete"	=> array("show"=>1,"view"=>"polls","task"=>"delete"),
			"trash"		=> array("show"=>1,"link"=>false),
			"clean_trash"	=> array("show"=>1,"link"=>false)
);
$cur_table_arr = array(
			"field"		=> array(1=>'p_id','p_title','p_alias','p_lag','p_startdate','p_enddate','p_comments','p_deleted','p_enabled'),
			"name"		=> array(1=>'ID','Title','Alias','Lag','Poll start date','Poll end date','Comments','Deleted','Published'),
			"link"		=> array(2=>"index.php?module=polls&view=items&psid=[0]"),
			"link_key"	=> array(2=>'id'),
			"link_type"	=> array(2=>''),
			"val_type"	=> array(1=>'int','string','string','int','datetime','datetime','text','boolean','boolean'),
			"input_type"	=> array(1=>'hidden','text','text','text','datetime_ajax','datetime_ajax','texteditor','hidden','checkbox'),
);
?>