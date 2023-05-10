<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname='comms_grp';
$keystring='cg_id';
$namestring='cg_module';
$nametabl="Comments groups";
$enabled="cg_enabled";
$deleted='cg_deleted';
$buttons=array(
			"new"=>array("show"=>1,"view"=>"groups","task"=>"modify"),
			"filter"=>array("show"=>1,"view"=>"groups"),
			"modify"=>array("show"=>1,"view"=>"groups","task"=>"modify"),
			"delete"=>array("show"=>1,"view"=>"groups","task"=>"delete"),
			"undelete"=>array("show"=>1,"view"=>"groups","task"=>"delete"),
			"trash"=>array("show"=>1,"link"=>false),
			"clean_trash"	=> array("show"=>1,"link"=>false)
);
$uni_buttons=array(	// массив уникальных кнопок
			"list_types"=>array(
				"title"=>"Comments types",
				"alt"=>'F',
				"view"=>'comtypes',
				"module"=>'comments',
				"position"=>"right",
				"alert"=>"Elements are not chosen"
		    ),							    
			"list_cats"=>array(
				"title"=>"Comments categories",
				"alt"=>'F',
				"view"=>'comcat',
				"module"=>'comments',
				"position"=>"right",
				"alert"=>"Elements are not chosen"
			)
);

$cur_table_arr=array(
				"field"=>array(1=>'cg_id', 'cg_title', 'cg_module', 'cg_view', 'cg_tablename', 'cg_list_limit', 'cg_text_limit','cg_bbcode', 'cg_premoderate','cg_mailmoder','cg_vote_obj','cg_vote_comms', 'cg_enabled', 'cg_deleted'),
				"name"=>array(1=>'ID', 'Title','Module', 'View', 'Database table','List limit', 'Text limit','Use BBCode', 'Premoderate', 'Mail moderators','Vote objects','Vote comments', 'Enabled','Deleted'),
				"input_type"=>array(1=>'hidden', 'text', 'select', 'text', 'text', 'text', 'text', 'checkbox', 'checkbox', 'checkbox', 'checkbox', 'checkbox',  'checkbox', 'hidden'),
				"val_type"=>array(1=>'int', 'string', 'string', 'string', 'string', 'int', 'int','boolean','boolean', 'boolean', 'boolean', 'boolean', 'boolean', 'boolean'),
				"link"=>array(2=>'index.php?module=comments&view=comments&psid=[0]'),
				"link_key"=>array(2=>'id'),
				"link_type"=>array(2=>''),
				"ch_table" => array(1=>"", 3=>"modules"),
				"ch_field" => array(1=>"", 3=>"m_name"),
				"ch_id"    => array(1=>"", 3=>"m_name")
);
?>