<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname	= 'videoset_galleries';
$keystring	= 'vg_id';
$deleted	= 'vg_deleted';
$nametabl	= "Video galleries";
$multy_field='vg_group_id';
$parent_view="groups";
$namestring="vg_title";
$parent_code='vgr_id';
$ordering_field	= "vg_ordering";		// Поле порядка отображения
// $ordering_parent = "vg_group_id";		// Поле внутри которого существует порядок отображения
$enabled="vg_published";

$buttons = array(
			"go_up"=> array("show"=>1,"link"=>false),
			"new"		=> array("show"=>1,"view"=>"items","task"=>"modify"),
			"filter"	=> array("show"=>1,"view"=>"items"),
			"modify"	=> array("show"=>1,"view"=>"items","task"=>"modify"),
			"delete"	=> array("show"=>1,"view"=>"items","task"=>"delete"),
			"undelete"	=> array("show"=>1,"view"=>"items","task"=>"delete"),
			"reorder"	=> array("show"=>1,"view"=>"items","task"=>"reorder"),
			"trash"		=> array("show"=>1,"link"=>false),
			"clean_trash"		=> array("show"=>1,"link"=>false)
);
$cur_table_arr = array(
			"field"		=> array(1=>'vg_id','vg_alias','vg_group_id','vg_title','vg_thumb','vg_title_thm','vg_alt_thm','vg_comment','vg_ordering','vg_show_in_list','vg_published','vg_deleted'),
			"name"		=> array(1=>'ID','Alias','Group','Title','Thumb','Title image','Alt image','Comments','Ordering','Show in list','Published','Delete mark'),
			"link"		=> array(4=>"index.php?module=videoset&view=videos&psid=[0]"),
			"link_key"	=> array(4=>'id'),
			"link_type"	=> array(4=>''),
			"val_type"	=> array(1=>'int','string','int','string','string','string','string','text','int','boolean','boolean','boolean'),
			"input_type"	=> array(1=>'hidden','text','select','text','image','text','text','texteditor','text','checkbox','checkbox','hidden'),
			"upload_path"=>array(5=>'items/thumbs'),
			"ch_table" => array(3=>"videoset_groups"),
			"ch_field" => array(3=>"vgr_title"),
			"ch_id"    => array(3=>"vgr_id")
);


?>