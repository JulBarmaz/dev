<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname='blogs_cats';
$keystring='bc_id';
$namestring='bc_name';
$alias_field = 'bc_alias';
$multy_field='bc_id_parent';
$enabled='bc_enabled';
$deleted='bc_deleted';
$nametabl="Blogs categories";
$ordering_field	= "bc_ordering";		// Поле порядка отображения
//$ordering_parent = "bc_id_parent";		// Поле внутри которого существует порядок отображения
$buttons=array(
			"new"=>array("show"=>1,"view"=>"categories","task"=>"modify"),
			"filter"=>array("show"=>1,"view"=>"categories"),
			"modify"=>array("show"=>1,"view"=>"categories","task"=>"modify"),
			"delete"=>array("show"=>1,"view"=>"categories","task"=>"delete"),
			"reorder"	=> array("show"=>1,"view"=>"categories","task"=>"reorder"),
			"undelete"=>array("show"=>1,"view"=>"categories","task"=>"delete"),
			"clean_trash"=>array("show"=>1,"link"=>false),			
			"trash"=>array("show"=>1,"link"=>false)
);
$uni_buttons=array(	// массив уникальных кнопок
				"blogs"=>array(
				"title"=>"List blogs in opened category",
				"alt"=>'B',
				"link"=>'index.php?module=blog&view=list&psid=%s',
				"position"=>"right"
				)
);
$cur_table_arr=array(
				"field"=>array(1=>'bc_id', 'bc_id_parent', 'bc_name', 'bc_alias','bc_comment', 'bc_meta_title', 'bc_meta_description', 'bc_meta_keywords', 'bc_ordering', 'bc_enabled','bc_deleted'),
				"name"=>array(1=>"Category ID", "Parent category","Category name",'Alias',"Description",'Meta title', 'Meta description', 'Meta keywords',"Ordering","Enabled","Delete mark"),
				"input_type"=>array(1=>'hidden','select','text','text',"texteditor",'text','textarea','textarea','text',"checkbox",'hidden'),
				"val_type"=>array(1=>'int','int','string','string',"text",'string',"text","text",'int','boolean','boolean'),
				"link"=>array(1=>'','','index.php?module=blog&view=list&psid=[0]',''),
				"link_key"=>array(1=>'','','id',''),
				"link_type"=>array(1=>'','','',''),
				"check_value"=>array(3=>1),
				"ch_table" => array(1=>"", 2=>"blogs_cats"),
		    	"ch_field" => array(1=>"", 2=>"bc_name"),
		    	"ch_id"    => array(1=>"", 2=>"bc_id")
);
?>