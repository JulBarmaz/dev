<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname	= 'redirect_links';
$keystring	= 'rl_id';
$deleted	= 'rl_deleted';
$enabled	= 'rl_published';
$ordering_field	= "rl_ordering";
$nametabl	= "Redirect links";
$buttons = array(
		"new"		=> array("show"=>1,"view"=>"redirectlinks","task"=>"modify"),
		"filter"	=> array("show"=>1,"view"=>"redirectlinks"),
		"modify"	=> array("show"=>1,"view"=>"redirectlinks","task"=>"modify"),
		"delete"	=> array("show"=>1,"view"=>"redirectlinks","task"=>"delete"),
		"undelete"	=> array("show"=>1,"view"=>"redirectlinks","task"=>"delete"),
		"clean_trash"=> array("show"=>1,"link"=>false),
		"trash"		=> array("show"=>1,"link"=>false)
);
$cur_table_arr = array(
		"field"				=> array(1=>'rl_id','rl_old_url','rl_new_url','rl_referer','rl_comment','rl_redirects','rl_substitution','rl_ordering','rl_published','rl_deleted','rl_create_date'),
		"name"				=> array(1=>'ID','Old url','New url','Referer','Comment','Redirects','Disable redirect','Ordering','Published','Deleted','Link created'),
		"input_type"		=> array(1=>'hidden','text','text','text','texteditor','label','checkbox','text','checkbox','hidden','label'),
		"default_value"		=> array(11=>"NOW"),
		"val_type"			=> array(1=>'int','string','string','string','text','int','boolean','int','boolean','boolean','datetime'),
		"view"				=> array(1=>0,1,1,1,1,1,1,1,1,0,1),
);
?>