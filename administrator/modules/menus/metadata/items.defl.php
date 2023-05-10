<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");
$tablname = 'menus';
$keystring = 'mi_id';
$namestring = 'mi_name';
$keysort = 'mi_ordering';
$enabled = 'mi_enabled';
$deleted = 'mi_deleted';
$nametabl = "Menus items list";
$multy_field = 'mi_parent_id';
$ordering_field = "mi_ordering";		// Поле порядка отображения
$buttons = array(
		"go_up" => array("show"=>0, "link"=>false),
		"new" => array("show"=>1, "view"=>"items", "task"=>"modify"),
		"filter" => array("show"=>0, "view"=>"items"),
		"modify" => array("show"=>1, "view"=>"items", "task"=>"modify"),
		"modify_links" => array("show"=>0, "view"=>"items", "task"=>"modify"),
		"delete" => array("show"=>1, "view"=>"items", "task"=>"delete"),
		"reorder" => array("show"=>1, "view"=>"items", "task"=>"reorder"),
		"undelete" => array("show"=>1, "view"=>"items", "task"=>"delete"),
		"trash" => array("show"=>1, "link"=>false),
		"clean_trash" => array("show"=>1, "link"=>false)
);
$cur_table_arr = array(
		"field" => array(1=>'mi_id', 'mi_parent_id', 'mi_name', 'mi_type', 'mi_target',
				'mi_nofollow', 'mi_link', 'mi_module', 'mi_view', 'mi_layout',
				'mi_alias', 'mi_psid', 'mi_controller', 'mi_task', 'mi_canonical_id',
				'mi_ordering', 'mi_thumb', 'mi_image', 'mi_descr', 'mi_access',
				'mi_custom_template', 'mi_enabled', 'mi_deleted','mi_forlang'),
		"name" => array(1=>'ID', 'Parent item', 'Name', 'Menu type', 'Target',
				'Link nofollow', 'Link', 'Module', 'View', 'Layout', 
				'Item alias', 'Item ID', 'Controller', 'Task', 'Canonical ID',
				'Order position', 'Thumb', 'Image', 'Description', 'Roles that can access',
				'Custom site template', 'Enabled', 'Deleted', 'Show for language'),
		"input_type" => array(1=>'hidden', 'select', 'text', 'select', 'select',
				'checkbox', 'text', 'text', 'text', 'text',
				'text', 'text', 'text', 'text', 'text',
				'text', 'image', 'image', 'texteditor', 'text',
				'folder_select', 'checkbox', 'hidden', (defined("_BARMAZ_TRANSLATE") ? 'multiselect' : "hidden")),
		"val_type" => array(1=>'int', 'int', 'string', 'int', 'string',
				'boolean', 'string', 'string', 'string', 'string',
				'string', 'int', 'string', 'string', 'int',
				'int', 'string', 'string', 'text', 'string',
				'string', 'boolean', 'boolean', 'string'),
		"default_value" => array(4=>0, 5 => "window"),
		"link"=>array(3=>'index.php?module=menus&view=items&task=modify&psid=[0]&multy_code=[1]&sort=[2]&page=[3]&orderby=[4]'),
		"link_key"=>array(3=>'id,multy_code,sort,page,orderby'),
		"link_type"=>array(3=>''),
		"sort_order" => array(2 => "NONE", 20 => "NONE"),
		"upload_path" => array(17 => 'thumbs', 18 => 'i', 21 => '/templates'),
		"check_value" => array(24 => defined("_BARMAZ_TRANSLATE")),
		"view" => array(1 => 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 1, 1, 1, 0),
		"ch_table" => array(1 => "", 2 => 'menus'),
		"ch_field" => array(1 => "", 2 => 'mi_name'),
		"ch_id" => array(1 => "", 2 => 'mi_id'),
		"ch_sort" => array(1 => "", 2 => 'mi_ordering'),
		"ch_deleted" => array(1 => "", 2 => 'mi_deleted'),
		"ck_reestr" => array(4 => "menu_type", 5 => "link_target", 24 => 'list_lang')
);
?>