<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname	= 'blogs_posts';
$keystring	= 'p_id';
$namestring	= 'p_theme';
$deleted	= 'p_deleted';
$enabled	= 'p_enabled';
$ordering_field	= 'p_ordering';
$nametabl	= "Blog posts";
$multy_field='p_blog_id';
$parent_table="blogs";
$parent_name='b_name';
$parent_view="list";
$parent_code='b_id';
$alias_field = 'p_alias';

$buttons = array(
		  "go_up"=> array("show"=>1,"link"=>false),
			"new"		=> array("show"=>1,"view"=>"post","task"=>"modify"),
			"filter"	=> array("show"=>1,"view"=>"post"),
			"modify"	=> array("show"=>1,"view"=>"post","task"=>"modify"),
			"reorder"	=> array("show"=>1,"view"=>"post","task"=>"reorder"),
			"modify_links"=>array("show"=>1,"view"=>"post","task"=>"modifyLinks"),
			"delete"	=> array("show"=>1,"view"=>"post","task"=>"delete"),
			"undelete"	=> array("show"=>1,"view"=>"post","task"=>"delete"),
			"clean_trash"=>array("show"=>1,"link"=>false),
			"trash"		=> array("show"=>1,"link"=>false)
);

$cur_table_arr = array(
			"field"		=> array(1=>'p_id','p_author_id','p_blog_id','p_theme','p_alias',
									'p_text','p_date','p_touch_date','p_comments',
									'p_rating','p_tags','p_enabled','p_deleted','p_closed',
									'p_thumb','p_title_thm','p_alt_thm','p_meta_title','p_meta_description','p_meta_keywords','p_ordering'),
			"name"		=> array(1=>"ID","Author","Blog",	'Theme', 'Alias',
									'Text','Date',"Touch date","Comments count",
									"Rating", "Tags", "Published","Deleted","Disable comments",
									"Thumb","SEO title","SEO alt",'Meta title','Meta description','Meta keywords','Ordering'),
			//"link"		=> array(2=>"index.php?module=blog&view=rights&blogid=[0]"),
			//"link_key"	=> array(2=>'id'),
			//"link_type"	=> array(2=>''),
			"val_type"	=> array(1=>"int","int","int",'string','string',
									'text','datetime','datetime','int',
									'int', 'string', 'boolean', 'boolean', 'boolean',
									"string","string","string",'string','text','text','int'),
			"input_type"=> array(1=>'hidden','label_sel','label_sel','text','text',
									'texteditor',	'datetime_ajax','datetime_ajax', 'label',
									'label', 'text', 'checkbox','hidden','checkbox',
									'image','text','text','text','textarea','textarea','text'),
		//	"view"		=> array(1=>0,1,1,1,1,1,1,1,1,1,1,1,1,0),
		"default_value"=>array(2=>"AUTHOR",7=>"NOW",8=>"NOW",13=>0),
	   	"ch_table" => array(1=>"", 2=>"users",3=>"blogs"),
	    "ch_field" => array(1=>"", 2=>"u_nickname",3=>"b_name"),
	    "ch_id"    => array(1=>"", 2=>"u_id",3=>"b_id"),
		"upload_path"=>array(15=>'thumbs')


			//"ck_reestr"=> array(7=>'bp_sort', 8=>'order_direction', 9=>'order_direction')
);

?>