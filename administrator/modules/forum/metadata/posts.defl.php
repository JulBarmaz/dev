<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname	= 'forum_posts';
$keystring	= 'p_id';
$namestring	= 'p_theme';
$deleted	= 'p_deleted';
$enabled	= 'p_enabled';
$nametabl	= "Forum posts";
$multy_field='p_theme_id';
$parent_table="forum_themes";
$parent_name='t_theme';
$parent_view="themes";
$parent_code='t_id';

$buttons = array(
		"go_up"=> array("show"=>1,"link"=>false),
		"new"		=> array("show"=>1,"view"=>"posts","task"=>"modify"),
		"filter"	=> array("show"=>1,"view"=>"posts"),
		"modify"	=> array("show"=>1,"view"=>"posts","task"=>"modify"),
		"reorder"	=> array("show"=>1,"view"=>"posts","task"=>"reorder"),
		"modify_links"=>array("show"=>1,"view"=>"posts","task"=>"modifyLinks"),
		"delete"	=> array("show"=>1,"view"=>"posts","task"=>"delete"),
		"undelete"	=> array("show"=>1,"view"=>"posts","task"=>"delete"),
		"clean_trash"=>array("show"=>1,"link"=>false),
		"trash"		=> array("show"=>1,"link"=>false)
);

$cur_table_arr = array(
		"field"		=> array(1=>'p_id', 'p_author_id', 'p_theme_id', 'p_theme', 'p_text',
				'p_date', 'p_touch_date','p_ip', 'p_rating', 'p_enabled', 'p_deleted'),
		"name"		=> array(1=>'ID', 'Author', 'Theme', 'Sub theme', 'Text',
				'Date', 'Touch date', 'IP','Rating', 'Enabled', 'Deleted'),
		"default_value" =>array(2=>"AUTHOR",6=>"NOW",7=>"NOW",8=>"AUTHOR_IP"),
		"val_type"	=> array(1=>'int', 'int', 'int', 'string', 'text',
				'datetime', 'datetime', 'string', 'int', 'boolean', 'boolean'),
		"view"		=> array(1=>0,1,0,1,0,1,1,1,1,0,1),
		"input_type"	=> array(1=>'hidden', 'label_sel', 'select', 'text', 'textarea',
				'label', 'label', 'label', 'label', 'hidden', 'checkbox'),
		"ch_table" => array(1=>"", 2=>"users", 3=>"forum_themes"),
		"ch_field" => array(1=>"", 2=>"u_login", 3=>"t_theme"),
		"ch_id"    => array(1=>"", 2=>"u_id", 3=>"t_id")
);

?>