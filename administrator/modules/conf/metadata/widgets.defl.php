<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");
$tablname	= 'widgets_active';
$keystring	= 'aw_id';
$deleted	= 'aw_deleted';
$enabled	= 'aw_enabled';
$nametabl	= "Widgets";
$ordering_field	= "aw_ordering";		// Поле порядка отображения
$keysort = "aw_name";
// $ordering_parent = "aw_zone";		// Поле внутри которого существует порядок отображения
$buttons = array(
		"new"		=> array("show"=>1,"view"=>"widgets","task"=>"newWidget"),
		"filter"	=> array("show"=>1,"view"=>"widgets"),
		"modify"	=> array("show"=>1,"view"=>"widgets","task"=>"modifyWidget"),
		"delete"	=> array("show"=>1,"view"=>"widgets","task"=>"delete"),
		"reorder"	=> array("show"=>1,"view"=>"widgets","task"=>"reorder"),
		"undelete"=> array("show"=>1,"view"=>"widgets","task"=>"delete"),
		"trash"		=> array("show"=>1,"view"=>"widgets","link"=>false),
		"clean_trash"=>array("show"=>1,"view"=>"widgets","link"=>false)
);
$cur_table_arr = array(
		"field"				=> array(1=>'aw_id', 'aw_name', 'aw_title', 'aw_show_title', 'aw_title_link', 'aw_zone', 'aw_class', 'aw_config','aw_access','aw_content','aw_ordering','aw_visible_in','aw_enabled','aw_cache','aw_deleted','aw_forlang'),
		"name"				=> array(1=>'ID','Widget type','Title','Show title', 'Title link', 'Zone','Class','Params','Roles that can access','Content','Ordering','Visibility upon selected menu item','Enabled',"Cache time",'Deleted','Show for language'),
		"input_type"		=> array(1=>'hidden', 'label_sel', 'text', 'checkbox', 'text', 'select', 'text', 'text', 'text', 'texteditor', 'text', 'text', 'checkbox', 'select','hidden',(defined("_BARMAZ_TRANSLATE") ? 'multiselect' : "hidden")),
		"val_type"			=> array(1=>'int','string','string','boolean','string','string','string','string','string','text','int','string','boolean','int','boolean','string'),
		"view"				=> array(1=>0,1,1,1,1,1,1,1,1,1,1,1,1,1,0),
		"check_value"		=> array(6=>1,16=>defined("_BARMAZ_TRANSLATE")),
		"update_type"		=> array(8=>0, 9=>0, 12=>0),
		"link"				=> array(3=>"index.php?module=conf&view=widgets&task=modifyWidget&psid=[0]&page=[1]&sort=[2]&orderby=[3]"),
		"link_key"			=> array(3=>'id, page, sort, orderby'),
		"ch_table" 			=> array(1=>"", 2=>"widgets",6=>"template_zones"),
		"ch_field" 			=> array(1=>"", 2=>"w_name",6=>"tz_name"),
		"ch_id"    			=> array(1=>"", 2=>"w_name",6=>"tz_name"),
		"ch_sort"			=> array(6=>"tz_ordering"),
		"ck_reestr" 		=> array(14=>"time_interval",16=>'list_lang')
);
?>