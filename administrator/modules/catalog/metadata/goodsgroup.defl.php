<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname='goods_group';
$keystring='ggr_id';
$namestring='ggr_name';
$alias_field='ggr_alias';
$enabled="ggr_enabled";
$deleted='ggr_deleted';
$tree_skip_deleted=Module::getInstance("catalog")->getParam("tree_skip_deleted");
$nametabl="Goods groups list";
$multy_field='ggr_id_parent';
$ordering_field	= "ggr_ordering";		// Поле порядка отображения
//$ordering_parent = "ggr_id_parent";		// Поле внутри которого существует порядок отображения
$buttons=array(
		"new"=>array("show"=>1,"view"=>"goodsgroup","task"=>"modify"),
		"filter"=>array("show"=>1,"view"=>"goodsgroup"),
		"clone"=>array("show"=>1,"view"=>"goodsgroup","task"=>"make_clone"),
		"modify"=>array("show"=>1,"view"=>"goodsgroup","task"=>"modify"),
		"delete"=>array("show"=>1,"view"=>"goodsgroup","task"=>"delete"),
		"reorder"	=> array("show"=>1,"view"=>"goodsgroup","task"=>"reorder"),
		"undelete"=>array("show"=>1,"view"=>"goodsgroup","task"=>"delete"),
		"clean_trash"=>array("show"=>1,"link"=>false),
		"trash"=>array("show"=>1,"link"=>false)
);
$uni_buttons=array(	// массив уникальных кнопок
		"list_goods"=>array(
				"title"=>"List goods in opened category",
				"alt"=>'',
				"link"=>'index.php?module=catalog&view=goods&psid=%s',
				"position"=>"right"
		),
		"filter_goods"=>array(
				"title"=>"Filter goods",
				"alt"=>'Filter goods',
				"view"=>'goods',
				"task"=>'',
				"layout"=>"",
				"module"=>'catalog',
				"position"=>"left",
				"link"=>"javascript:showFilter('catalog','goods','','','".Text::_("Filter goods")."'); return false;"
		)
);
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
		"check_value"=>array(4=>3),
		"upload_path"=>array(5=>'ggr',8=>'ggr/i'),
		"default_value"=>array(20=>'NOW',21=>'AUTHOR'),
		"link"=>array(	1=>'index.php?module=catalog&view=goodsgroup&task=modify&psid=[0]&multy_code=[1]',
						3=>'index.php?module=catalog&view=goods&psid=[0]',''),
		"link_key"=>array(1=>'id,multy_code',3=>'id',''),
		"link_type"=>array(1=>'',3=>''),
		"ck_reestr"=>array(16=>'goods_list_tmpl', 22=>'goods_default_sorting'),
		"ch_table" => array(1=>"", 2=>"goods_group",21=>'users'),
		"ch_field" => array(1=>"", 2=>"ggr_name",21=>'u_nickname'),
		"ch_id"    => array(1=>"", 2=>"ggr_id",21=>'u_id'),
		"ch_enabled"    => array(1=>"", 2=>"ggr_enabled", 21=>""),
		"ch_deleted"    => array(1=>"", 2=>"ggr_deleted", 21=>""),
		"ch_skip_deleted"=> array(2=>0),
		"ch_skip_disabled"=> array(2=>0)
);
?>