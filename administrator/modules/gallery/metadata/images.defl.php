<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname	= 'gallery_images';
$keystring	= 'gi_id';
$deleted	= 'gi_deleted';
$nametabl	= "Images";
$multy_field='gi_gallery_id';
$parent_view="items";
$parent_code='g_id';
$ordering_field	= "gi_ordering";		// Поле порядка отображения
// $ordering_parent = "gi_gallery_id";		// Поле внутри которого существует порядок отображения
$enabled="gi_published";


$buttons = array(
			"go_up"=> array("show"=>1,"link"=>false),
			"new"		=> array("show"=>1,"view"=>"images","task"=>"modify"),
			"filter"	=> array("show"=>1,"view"=>"images"),
			"modify"	=> array("show"=>1,"view"=>"images","task"=>"modify"),
			"delete"	=> array("show"=>1,"view"=>"images","task"=>"delete"),
			"undelete"	=> array("show"=>1,"view"=>"images","task"=>"delete"),
			"reorder"	=> array("show"=>1,"view"=>"images","task"=>"reorder"),
			"trash"		=> array("show"=>1,"link"=>false),
			"clean_trash"		=> array("show"=>1,"link"=>false)
);
$cur_table_arr = array(
			"field"		=> array(1=>'gi_id','gi_gallery_id','gi_title',
					'gi_image','gi_title_img','gi_alt_img',
					'gi_thumb','gi_title_thm','gi_alt_thm',
					'gi_comment','gi_published','gi_ordering','gi_deleted',
					'gi_meta_title', 'gi_meta_description','gi_meta_keywords'),
			"name"		=> array(1=>'ID','Gallery','Title',
					'Image','Title image','Alt image',
					'Thumb','Title thumb','Alt thumb',
					'Comments','Published','Ordering','Delete mark',
					'Meta title', 'Meta description','Meta keywords'),
			"val_type"	=> array(1=>'int','int','string',
					'string','string','string',
					'string','string','string',
					'text','boolean','int','boolean',
					'string','text','text'),
			"input_type"	=> array(1=>'hidden','select','text',
					'image','text','text',
					'image','text','text',
					'texteditor','checkbox','text','hidden',
					'text','textarea','textarea'),
			"upload_path"=>array(4=>'i',7=>'i/thumbs'),
			"check_value"=>array(3=>1),
			"ch_table" => array(2=>"galleries"),
			"ch_field" => array(2=>"g_title"),
			"ch_id"    => array(2=>"g_id")

);

?>