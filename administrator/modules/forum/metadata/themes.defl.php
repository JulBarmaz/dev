<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname	= 'forum_themes';
$keystring	= 't_id';
$namestring	= 't_theme';
$alias_field = 't_alias';
$deleted	= 't_deleted';
$enabled	= 't_enabled';
$nametabl	= "Forum themes";
$multy_field='t_forum_id';
$parent_table="forum_sections";
$parent_name='f_name';
$parent_view="sections";
$parent_code='f_id';

$buttons = array(
		"go_up"=> array("show"=>1,"link"=>false),
		"new"		=> array("show"=>1,"view"=>"themes","task"=>"modify"),
		"filter"	=> array("show"=>1,"view"=>"themes"),
		"modify"	=> array("show"=>1,"view"=>"themes","task"=>"modify"),
		"reorder"	=> array("show"=>1,"view"=>"themes","task"=>"reorder"),
		"modify_links"=>array("show"=>1,"view"=>"themes","task"=>"modifyLinks"),
		"delete"	=> array("show"=>1,"view"=>"themes","task"=>"delete"),
		"undelete"	=> array("show"=>1,"view"=>"themes","task"=>"delete"),
		"clean_trash"=>array("show"=>1,"link"=>false),
		"trash"		=> array("show"=>1,"link"=>false)
);
$cur_table_arr = array(
		"field"	=> array(1=>'t_id', 't_author_id', 't_forum_id', 't_theme', 't_alias',
				't_text', 't_date', 't_touch_date', 't_rating', 't_tags',
				't_enabled', 't_ip', 't_views', 't_deleted','t_thumb','t_fixed',
				't_closed'),
		"name"	=> array(1=>'ID', 'Author', 'Forum', 'Theme', 'Alias',
				'Text', 'Date', 'Touch date', 'Rating', 'Tags',
				'Enabled', 'IP','Views','Deleted','Thumb','Fixed theme',
				'Closed theme'),
		"link"		=> array(4=>"index.php?module=forum&view=posts&psid=[0]"),
		"link_key"	=> array(4=>'id'),
		"link_type"	=> array(4=>''),
		"default_value" =>array(2=>"AUTHOR",7=>"NOW",8=>"NOW",12=>"AUTHOR_IP"),
		"val_type"	=> array(1=>'int', 'int', 'int', 'string', 'string',
				'text',	'datetime', 'datetime', 'int', 'string',
				'boolean', 'string', 'int','boolean','string','boolean',
				'boolean'),
		"view"	=> array(1=>0,1,0,1,1,0,1,1,1,1,1,1,1,0,1,1),
		"input_type"	=> array(1=>'hidden', 'label_sel', 'select', 'text', 'text',
				'textarea', 'label', 'label', 'label', 'text',
				'checkbox', 'label','label','hidden','image','checkbox',
				'checkbox'),
		"upload_path"=>array(15=>'themes'),
		"ch_table" => array(1=>"", 2=>"users", 3=>"forum_sections"),
		"ch_field" => array(1=>"", 2=>"u_login", 3=>"f_name"),
		"ch_id"    => array(1=>"", 2=>"u_id", 3=>"f_id")
);
?>