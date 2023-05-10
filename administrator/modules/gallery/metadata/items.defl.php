<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname	= 'galleries';
$keystring	= 'g_id';
$deleted	= 'g_deleted';
$nametabl	= "Galleries";
$alias_field = 'g_alias';
$multy_field='g_group_id';
$parent_view="groups";
$namestring="g_title";
$parent_code='gr_id';
$ordering_field	= "g_ordering";		// Поле порядка отображения
// $ordering_parent = "g_group_id";		// Поле внутри которого существует порядок отображения
$enabled="g_published";

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
			"field"		=> array(1=>'g_id','g_alias','g_group_id','g_title','g_thumb',
									'g_title_thm','g_alt_thm','g_comment','g_ordering','g_show_in_list',
									'g_hide_image_titles','g_published','g_deleted','g_meta_title','g_meta_description',
					'g_meta_keywords','g_layout','g_images_by_row', 'g_show_parent_descr'),
			"name"		=> array(1=>'ID','Alias','Group','Title','Thumb',
									'Title image','Alt image','Comments','Ordering','Show in list',
									'Hide images titles','Published','Delete mark',	'Meta title', 'Meta description',
									'Meta keywords','Layout', 'Images by row', 'Show parent description'),
			"link"		=> array(4=>"index.php?module=gallery&view=images&psid=[0]"),
			"link_key"	=> array(4=>'id'),
			"link_type"	=> array(4=>''),
			"val_type"	=> array(1=>'int','string','int','string','string',
									'string','string','text','int','boolean',
									'boolean','boolean','boolean', 'string','text',
									'text','string', 'int','boolean'),
			"input_type"	=> array(1=>'hidden','text','select','text','image',
										'text','text','texteditor','text','checkbox',
										'checkbox','checkbox','hidden',	'text','textarea',
										'textarea','text', 'select','checkbox'),
			"upload_path"=>array(5=>'items/thumbs'),
			"check_value"=>array(3=>1),
			"ch_table" => array(3=>"gallery_groups"),
			"ch_field" => array(3=>"gr_title"),
			"ch_id"    => array(3=>"gr_id"),
			"ck_reestr"=>array(18=>"quadro_by_row")
);
?>