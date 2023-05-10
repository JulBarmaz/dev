<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname	= 'blogs';
$keystring	= 'b_id';
$namestring	= 'b_name';
$alias_field = 'b_alias';
$deleted	= 'b_deleted';
$enabled	= 'b_enabled';
$nametabl	= "Blogs";
$multy_field='b_id';
$parent_table="blogs_cats";
$parent_name='bc_name';
$parent_view="categories";
$parent_code='bc_id';

$buttons = array(
			"go_up"=> array("show"=>1,"link"=>false),
			"new"		=> array("show"=>1,"view"=>"list","task"=>"modify"),
			"filter"	=> array("show"=>1,"view"=>"list"),
			"modify"	=> array("show"=>1,"view"=>"list","task"=>"modify"),
			"reorder"	=> array("show"=>1,"view"=>"list","task"=>"reorder"),
			"modify_links"=>array("show"=>0,"view"=>"list","task"=>"modifyLinks"),
			"delete"	=> array("show"=>1,"view"=>"list","task"=>"delete"),
			"undelete"	=> array("show"=>1,"view"=>"list","task"=>"delete"),
			"clean_trash"=>array("show"=>1,"link"=>false),	
			"trash"		=> array("show"=>1,"link"=>false)
);
$uni_buttons=array(	// массив уникальных кнопок
		"rights"=>array(
				"title"=>"Modify rights",
				"alt"=>'R',
				"module"=>"blog",
				"view"=>"rights",
				"position"=>"left",
				"alert"=>"Please select element from list"
		)
);

$cur_table_arr = array(
			"field"		=> array(1=>"b_id",	"b_name","b_alias",	"b_description","b_meta_title", 
									"b_meta_description", "b_meta_keywords","b_thumb",	"b_title_thm",	"b_alt_thm", 
									"b_porder_by", "b_porder_dir",	"b_corder_dir",	"b_layout",	"b_show_in_list", 
									"b_post_rating", "b_comments_rating",	"b_premoderated",	"b_guieditor",	"b_hide_properties", 
									"b_hide_comments", 	"b_enabled","b_deleted"),
			"name"		=> array(1=>"Blog ID","Blog name","Alias","Description","Meta title", 
									"Meta description",	"Meta keywords","Thumb","SEO title","SEO alt",
									"Posts order by", "Posts order direction", "Comments order direction", "Layout", "Show in list",
									"Enable post rating","Enable comments rating","Premoderate","Enable gui editor","Hide properties",
									"Hide comments","Published","Deleted"),
			"val_type"	=> array(1=>"int","string","string","text","string",
									"text", "text", "string", "string", "string",
									"string", "string", "string", "string", "boolean", 
									"boolean", "boolean", "boolean", "boolean", "boolean", 
									"boolean", "boolean", "boolean"),
			"input_type"=> array(1=>"hidden","text","text","texteditor","text", 
									"textarea", "textarea","image","text","text",
									"select", "select", "select", "text", "checkbox", 
									"checkbox", "checkbox", "checkbox", "checkbox", "checkbox", 
									"checkbox", "checkbox","hidden"),
			"link"		=> array(2=>"index.php?module=blog&view=post&psid=[0]"),
			"link_key"	=> array(2=>'id'),
			"link_type"	=> array(2=>''),
			"view"		=> array(1=>0,1,1,1,1,
									1,1,1,1,1,
									1,1,1,1,1,
									1,1,1,1,1,
									1,1,0),
			"check_value"=>array(1=>0, 1, 0, 0, 0,
									0, 0, 0, 0, 0,
									1, 1, 1, 0, 0,
									0, 0, 0, 0, 0,
									0,0,0),
			"upload_path"=>array(8=>'blog_thumbs'),
			"ck_reestr"=> array(11=>'bp_sort', 12=>'order_direction', 13=>'order_direction')
);

?>