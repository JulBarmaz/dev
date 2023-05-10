<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname	= 'forum_sections';
$keystring	= 'f_id';
$namestring	= 'f_name';
$alias_field = 'f_alias';
$deleted	= 'f_deleted';
$enabled	= 'f_enabled';
$nametabl	= "Forum sections";
$multy_field='f_parent_id';
$ordering_field	= "f_ordering";		// Поле порядка отображения
// $ordering_parent = "f_parent_id";		// Поле внутри которого существует порядок отображения

$buttons = array(
		"go_up"=> array("show"=>0,"link"=>false),
		"new"		=> array("show"=>1,"view"=>"sections","task"=>"modify"),
		"filter"	=> array("show"=>1,"view"=>"sections"),
		"modify"	=> array("show"=>1,"view"=>"sections","task"=>"modify"),
		"reorder"	=> array("show"=>1,"view"=>"sections","task"=>"reorder"),
		"modify_links"=>array("show"=>1,"view"=>"sections","task"=>"modifyLinks"),
		"delete"	=> array("show"=>1,"view"=>"sections","task"=>"delete"),
		"undelete"	=> array("show"=>1,"view"=>"sections","task"=>"delete"),
		"clean_trash"=>array("show"=>1,"link"=>false),
		"trash"		=> array("show"=>1,"link"=>false)
);
$uni_buttons=array(	// массив уникальных кнопок
		"rights"=>array(
				"title"=>"Modify rights",
				"alt"=>'R',
				"module"=>"forum",
				"view"=>"rights",
				"position"=>"left",
				"alert"=>"Please select element from list"
		),
		"list"=>array(
				"title"=>"List themes in opened section",
				"alt"=>'T',
				"link"=>'index.php?module=forum&view=themes&psid=%s',
				"position"=>"right"
		)
);
$cur_table_arr = array(
		"field"		=> array(1=>"f_id","f_parent_id","f_name","f_alias","f_description","f_meta_title", "f_meta_description", "f_meta_keywords", "f_layout", "f_show_in_list","f_post_rating","f_premoderated","f_ordering","f_thumb","f_enabled","f_deleted"),
		"name"		=> array(1=>"ID","Forum parent","Forum name", "Alias", "Description", 'Meta title', 'Meta description',	'Meta keywords',"Layout", "Show in list","Enable post rating","Premoderate","Ordering","Thumb","Published","Deleted"),
		"link"		=> array(3=>"index.php?module=forum&view=themes&psid=[0]"),
		"link_key"	=> array(3=>'id'),
		"link_type"	=> array(3=>''),
		"val_type"	=> array(1=>"int","int","string","string","text",'string','text','text',  'string','boolean', 'boolean', 'boolean','int','string' ,'boolean', 'boolean'),
		"view"		=> array(1=>0,1,1,1,0,0,0,0,1,1,1,1,1,1,0),
		"input_type"=> array(1=>'hidden','select','text','text','texteditor','text', 'textarea',	'textarea', 'text', 'checkbox', 'checkbox', 'checkbox','string','image' ,'checkbox','hidden'),
		"upload_path"=>array(14=>'sections'),
		"ch_table" => array(1=>"", 2=>"forum_sections"),
		"ch_field" => array(1=>"", 2=>"f_name"),
		"ch_id"    => array(1=>"", 2=>"f_id")
);
?>