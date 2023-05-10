<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname	= 'modules';
$keystring	= 'm_id';
//$enabled	= 'm_enabled'; // заремлено чтобы не было переключателя в списке
$deleted	= 'm_deleted';
$nametabl	= "List of modules";

$buttons = array(
	"new"		=> array("show"=>0,"view"=>"modules","task"=>""),
	"filter"	=> array("show"=>0),
	"modify"	=> array("show"=>1,"view"=>"modules","task"=>"modifyModule"),
	"delete"	=> array("show"=>0),
	"trash"		=> array("show"=>0,"link"=>false)
);

$cur_table_arr = array(
		"field"				=> array(1=>'m_id','m_name','m_replace_name','m_show_breadcrumb','m_config','m_incl_map','m_enabled','m_deleted','m_translated'),
		"view"				=> array(1=>0,0,0,1,1,1,1,0,0),
		"name"				=> array(1=>'ID','m_name','Replace name','m_show_breadcrumb','m_config','Include for sitemap', 'Enabled on frontend','Deleted','Demand translate'),
		"input_type"		=> array(1=>'hidden','label',(defined("_BARMAZ_TRANSLATE") ? 'text' : "hidden"),'checkbox','text','checkbox','checkbox','hidden',(defined("_BARMAZ_TRANSLATE") ? 'checkbox' : "hidden")),
		"val_type"			=> array(1=>'int','string','string','boolean','string','boolean','boolean','int','boolean'),
		"link"				=> array(5=>"index.php?module=conf&view=modules&task=modifyModule&psid=[0]&page=[1]&sort=[2]&orderby=[3]"),
		"link_key"			=> array(5=>'id, page, sort, orderby'),
		"link_picture"		=> array(5=>"/administrator/templates/".adminConfig::$adminTemplate."/images/buttons/modify.png"),
		"translate_value"	=> array(2=>true)
);
?>