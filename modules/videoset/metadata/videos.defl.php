<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname	= 'videoset_videos';
$keystring	= 'v_id';
$deleted	= 'v_deleted';
$nametabl	= "Videos";
$multy_field='v_gallery_id';
$parent_view="items";
$parent_code='vg_id';
$ordering_field	= "v_ordering";		// Поле порядка отображения
// $ordering_parent = "v_gallery_id";		// Поле внутри которого существует порядок отображения
$enabled="v_published";


$buttons = array(
			"go_up"=> array("show"=>1,"link"=>false),
			"new"		=> array("show"=>1,"view"=>"videos","task"=>"modify"),
			"filter"	=> array("show"=>1,"view"=>"videos"),
			"modify"	=> array("show"=>1,"view"=>"videos","task"=>"modify"),
			"delete"	=> array("show"=>1,"view"=>"videos","task"=>"delete"),
			"undelete"	=> array("show"=>1,"view"=>"videos","task"=>"delete"),
			"reorder"	=> array("show"=>1,"view"=>"videos","task"=>"reorder"),
			"trash"		=> array("show"=>1,"link"=>false),
			"clean_trash"	=> array("show"=>1,"link"=>false)
);
$cur_table_arr = array(
		"field"		=> array(1=>'v_id','v_gallery_id','v_title','v_image','v_alias',
				'v_video_youtube','v_video_ogg', 'v_video_mp4', 'v_video_webm', 'v_comment',
				'v_published', 'v_ordering','v_deleted',
				'v_meta_title','v_meta_description','v_meta_keywords'),
		"name"		=> array(1=>'ID','Gallery','Title','Image','Alias',
				'Video YouTube', 'Video OGG', 'Video MP4', 'Video WEBM','Comments',
				'Published', 'Ordering','Delete mark',
				'Meta title','Meta description','Meta keywords'),
		"val_type"	=> array(1=>'int','int','string','string','string',
				'string', 'string', 'string', 'string', 'text',
				'boolean',	'int','boolean'
				,'string','text','text'),
		"input_type"	=> array(1=>'hidden','select','text','image','text',
				'text', 'filepath', 'filepath', 'filepath',	'texteditor',
				'checkbox',	'text','hidden',
				'text','textarea','textarea'),
		"upload_path"=>array(4=>'i'),
			"ch_table" => array(2=>"videoset_galleries"),
			"ch_field" => array(2=>"vg_title"),
			"ch_id"    => array(2=>"vg_id")
);
?>