<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname	= 'goods_img';
$use_view_rights="goods";
$keystring	= 'i_id';
$enabled	= 'i_enabled';
$deleted	= 'i_deleted';
$nametabl	= "Additional product images";
$multy_field='i_gid';
$parent_view="goods";
$parent_name='g_name';
$parent_code='g_id';
$ordering_field	= "i_ordering";	// Поле порядка отображения
//$ordering_parent = "i_gid";			// Поле внутри которого существует порядок отображения
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
		"field"		=> array(1=>'i_id', 'i_gid', 'i_title', 'i_image', 'i_title_img',
								'i_alt_img', 'i_thumb', 'i_title_thm', 'i_alt_thm', 'i_ordering',
								'i_enabled', 'i_deleted'),
		"name"		=> array(1=>'ID','Goods name', 'Title', 'Image', 'Title image',
								'Alt image', 'Thumb','Title thumb','Alt thumb', 'Ordering',
								'Published', 'Delete mark'),
		"val_type"	=> array(1=>'int','int','string', 'string', 'string',
								'string', 'string', 'string', 'string', 'int',
								'boolean', 'boolean'),
		"input_type"	=> array(1=>'hidden', 'label_sel', 'text', 'image', 'text',
									'text', 'image','text','text', 'text',
									'checkbox', 'hidden'),
		"link"=>array(1=>'index.php?module=catalog&view=images&task=modify&psid=[0]&multy_code=[1]'),
		"link_key"=>array(1=>'id,i_gid'),
		"link_type"=>array(1=>''),		
		"upload_path"=>array(4=>'i/fullsize', 7=>'i/thumbs'),
		"ch_table" => array(2=>"goods"),
		"ch_field" => array(2=>"g_name"),
		"ch_id"    => array(2=>"g_id")
);
?>