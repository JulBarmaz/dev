<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname='goods_group';
$keystring='ggr_id';
$namestring='ggr_name';
$enabled="ggr_enabled";
$deleted='ggr_deleted';
$nametabl="Goods groups list";
$multy_field='ggr_id_parent';
$ordering_field	= "ggr_ordering";		// Поле порядка отображения
$cur_table_arr=array(
		"field"=>array(1=>'ggr_id', 'ggr_id_parent', 'ggr_name', 'ggr_alias', 'ggr_thumb',
				'ggr_title_thm','ggr_alt_thm', 'ggr_image', 'ggr_title_img','ggr_alt_img',
				'ggr_image_inherit', 'ggr_comment', 'ggr_meta_title', 'ggr_meta_description', 'ggr_meta_keywords', 
				'ggr_list_tmpl', 'ggr_ordering', 'ggr_enabled','ggr_deleted','ggr_change_date',
				'ggr_change_uid', 'ggr_default_sorting'),
		"name"=>array(1=>"Group ID", "Group parent","Group name","Alias","Thumb",
				'Title thumb','Alt thumb', 'Image','Title image','Alt image',
				"Inherit images","Description",'Meta title', 'Meta description', 'Meta keywords',
				'List template', "Ordering","Enabled","Delete mark",'Last change date',
				'Last changer', 'default goods sorting'),
		"input_type"=>array(1=>'hidden','select','text','text','image',
				'text','text', 'image', 'string','text',
				"checkbox","texteditor",'text','textarea','textarea',
				'select','text',"checkbox",'hidden','label',
				'label_sel', 'select'),
		"val_type"=>array(1=>'int','int','string','string','string',
				'string','string', 'string', 'string', 'string',
				'boolean',"text",'string',"text","text",
				'string', 'int','boolean','boolean','datetime',
				'int', 'string'),
		"upload_path"=>array(5=>'ggr',8=>'ggr/i'),
		"default_value"=>array(20=>'NOW',21=>'AUTHOR'),
		"ck_reestr"=>array(16=>'goods_list_tmpl', 22=>'goods_default_sorting'),
		"ch_table" => array(1=>"", 2=>"goods_group",21=>'users'),
		"ch_field" => array(1=>"", 2=>"ggr_name",21=>'u_nickname'),
		"ch_id"    => array(1=>"", 2=>"ggr_id",21=>'u_id')
);
?>