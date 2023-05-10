<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname	= 'articles';
$keystring	= 'a_id';
$namestring='a_title';
$alias_field="a_alias";
$deleted	= 'a_deleted';
$enabled	= 'a_published';
$multy_field='a_parent_id';
$nametabl	= "Articles";
$ordering_field	= "a_ordering";		// Поле порядка отображения
//$ordering_parent = "a_parent_id";		// Поле внутри которого существует порядок отображения

$buttons = array(
		"new"		=> array("show"=>1,"view"=>"items","task"=>"modify"),
		"filter"	=> array("show"=>1,"view"=>"items"),
		"modify"	=> array("show"=>1,"view"=>"items","task"=>"modify"),
		"delete"	=> array("show"=>1,"view"=>"items","task"=>"delete"),
		"undelete"	=> array("show"=>1,"view"=>"items","task"=>"delete"),
		"reorder"	=> array("show"=>1,"view"=>"items","task"=>"reorder"),
		"trash"		=> array("show"=>1,"link"=>false),
		"clean_trash"		=> array("show"=>1,"link"=>false)
);
/*
$uni_buttons=array(	// массив уникальных кнопок
		"change_date"=>array(
				"title"=>"Change publication date",
				"alt"=>'D',
				"reset_multy"=>"true",
				"view"=>'items',
				"task"=>'changeDateForm',
				"module"=>'article',
				"position"=>"right",
				"alert"=>"Elements are not chosen"
		),
);
*/
$cur_table_arr = array(
		"field"		=> array(1=>'a_id', 'a_parent_id','a_alias', 'a_author_id', 'a_date',
				'a_title',	'a_text', 'a_show_info', 'a_show_in_contents', 'a_show_childs',
				'a_show_title', 'a_show_breadcrumb', 	'a_meta_title', 	'a_meta_description', 	'a_meta_keywords',
				'a_rating', 'a_deleted','a_published','a_thumb','a_title_thm',
				'a_alt_thm','a_ordering','a_childs_order_by','a_childs_order_dir'),
		"name"		=> array(1=>'ID', 'Article parent', 'Alias', 'Author', 'Date',
				'Title', 'Text', 'Show info', 'Show in contents', 'Show childs',
				'Show title', 'Show breadcrumb', 	'Meta title', 'Meta description',	'Meta keywords',
				'Rating', 	'Delete mark','Enabled','Thumb','Title image',
				'Alt image','Ordering', 'Childs order by', 'Childs order dir'),
		"val_type"	=> array(1=>'int', 'int', 'string', 'int', 'datetime',
				'string', 'text', 'boolean', 'boolean', 'boolean',
				'boolean', 'boolean', 	'string', 	'text', 'text',
				'int', 	'boolean', 	'boolean','string', 'string',
				'string','int','string', 'string'),
		"input_type"	=> array(1=>'hidden', 'select', 'text', 'label_sel', 'datetime_ajax',
				'text',	'texteditor', 'checkbox', 'checkbox', 'checkbox',
				'checkbox', 'checkbox', 'text', 	'textarea', 	'textarea',
				'label', 	'hidden','checkbox','image', 'text',
				'text','text', 'select', 'select'),
		"default_value" =>array(4=>"AUTHOR",5=>"NOW",11=>1,18=>1),
		"check_value"=>array(1=>0, 0, 0, 0, 0,
								1, 0, 0, 0, 0,
								0, 0, 0, 0, 0,
								0, 0, 0, 0, 0,
								0,0,1,1),
		"link"=>array(6=>'index.php?module=article&view=items&task=modify&psid=[0]&multy_code=[1]&sort=[2]&page=[3]&orderby=[4]'),
		"link_key"=>array(6=>'id,multy_code,sort,page,orderby'),
		"link_type"=>array(6=>''),
		"ch_table" => array(1=>"", 2=>"articles",4=>"users"),
		"ch_field" => array(1=>"", 2=>"a_title",4=>"u_nickname"),
		"ch_id"    => array(1=>"", 2=>"a_id",4=>"u_id"),
		"ch_enabled"    => array(1=>"", 2=>"a_published"),
		"ch_deleted"    => array(1=>"", 2=>"a_deleted"),
		"ch_skip_deleted"=> array(2=>0),
		"ch_skip_disabled"=> array(2=>0),
		"ck_reestr"=> array(23=>'a_childs_sort', 24=>'order_direction'),
		"upload_path"=>array(19=>'thumbs')
	);
?>