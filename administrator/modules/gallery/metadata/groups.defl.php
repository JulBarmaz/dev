<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname	= 'gallery_groups';
$keystring	= 'gr_id';
$alias_field = 'gr_alias';
$deleted	= 'gr_deleted';
$nametabl	= "Gallery groups";
$namestring="gr_title";
$enabled="gr_published";
$ordering_field	= "gr_ordering";		// Поле порядка отображения

$buttons = array(
			"new"		=> array("show"=>1,"view"=>"groups","task"=>"modify"),
			"filter"	=> array("show"=>1,"view"=>"groups"),
			"modify"	=> array("show"=>1,"view"=>"groups","task"=>"modify"),
			"delete"	=> array("show"=>1,"view"=>"groups","task"=>"delete"),
			"undelete"	=> array("show"=>1,"view"=>"groups","task"=>"delete"),
			"reorder"	=> array("show"=>1,"view"=>"groups","task"=>"reorder"),
			"trash"		=> array("show"=>1,"link"=>false),
			"clean_trash"		=> array("show"=>1,"link"=>false)
);
$cur_table_arr = array(
			"field"		=> array(1=>'gr_id','gr_alias','gr_title','gr_thumb','gr_title_thm','gr_alt_thm','gr_comment','gr_ordering','gr_show_in_list','gr_published','gr_deleted',
								'gr_meta_title', 'gr_meta_description','gr_meta_keywords','gr_layout'),
			"name"		=> array(1=>'ID','Alias','Title','Thumb','Title image','Alt image','Comments','Ordering','Show in list','Published','Deleted',
								'Meta title', 'Meta description','Meta keywords','Layout'),
			"link"		=> array(3=>"index.php?module=gallery&view=items&psid=[0]"),
			"link_key"	=> array(3=>'id'),
			"link_type"	=> array(3=>''),
			"check_value"=>array(3=>1),		
			"val_type"	=> array(1=>'int','string','string','string','string','string','text','int','boolean','boolean','boolean','string','text','text','string'),
			"input_type"	=> array(1=>'hidden','text','text','image','text','text','texteditor','text','checkbox','checkbox','hidden','text','textarea','textarea','text'),
			"upload_path"=>array(4=>'groups/thumbs'),
);

?>