<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname	= "sitemap_man";
$keystring	= "m_id";
$namestring	= "m_loc";
$deleted	= "m_deleted";
$enabled	= "m_enabled";
$nametabl	= "Elements of xml sitemap";
$buttons = array(
	"new"			=> array("show"=>1,"view"=>"mansitemap","task"=>"modify"),
	"filter"		=> array("show"=>1,"view"=>"mansitemap"),
	"modify"		=> array("show"=>1,"view"=>"mansitemap","task"=>"modify"),
	"delete"		=> array("show"=>1,"view"=>"mansitemap","task"=>"delete"),
	"reorder"		=> array("show"=>1,"view"=>"mansitemap","task"=>"reorder"),
	"trash"			=> array("show"=>1,"link"=>false),
	"clean_trash"	=> array("show"=>1,"link"=>false)
);
$cur_table_arr = array(
		"field"				=> array(1=>'m_id','m_loc','m_lastmod','m_changefreq','m_priority','m_deleted','m_enabled','m_type','m_module','m_title'),
		"name"				=> array(1=>"ID","loc","lastmod","changefreq","priority","Deleted","Enabled",'Operation','Module','Title'),
		"input_type"		=> array(1=>"hidden", "text", "date_ajax", "text", "text","hidden","checkbox",'select','string','string'),
		"val_type"			=> array(1=>"int","string","date","string", "string","boolean","boolean",'string','string','string'),
		"check_value"=>array(2=>1),
		"ck_reestr"=>array(8=>'inc_switcher'),
		"default_value"=>array(3=>"TODAY",4=>"weekly",5=>"0.5",7=>true,8=>'1',9=>'main')
);
?>