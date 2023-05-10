<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname	= 'videoset_groups';
$keystring	= 'vgr_id';
$alias_field = 'vgr_alias';
$deleted	= 'vgr_deleted';
$nametabl	= "Video gallery groups";
$namestring="vgr_title";
$enabled="vgr_published";
$ordering_field	= "vgr_ordering";		// Поле порядка отображения

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
			"field"		=> array(1=>'vgr_id','vgr_alias','vgr_title','vgr_thumb','vgr_title_thm','vgr_alt_thm','vgr_comment','vgr_ordering','vgr_show_in_list','vgr_published','vgr_deleted','vgr_meta_title', 'vgr_meta_description','vgr_meta_keywords'),
			"name"		=> array(1=>'ID','Alias','Title','Thumb','Title image','Alt image','Comments','Ordering','Show in list','Published','Deleted','Meta title','Meta description','Meta keywords'),
			"link"		=> array(3=>"index.php?module=videoset&view=items&psid=[0]"),
			"link_key"	=> array(3=>'id'),
			"link_type"	=> array(3=>''),
			"check_value"=>array(3=>1),
			"val_type"	=> array(1=>'int','string','string','string','string','string','text','int','boolean','boolean','boolean','string','text','text'),
			"input_type"	=> array(1=>'hidden','text','text','image','text','text','texteditor','text','checkbox','checkbox','hidden','text','textarea','textarea'),
			"upload_path"=>array(4=>'groups/thumbs'),
);

?>