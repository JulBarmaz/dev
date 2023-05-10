<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname='manufacturers';
$keystring='mf_id';
$enabled='mf_enabled';
$deleted='mf_deleted';
$namestring='mf_name';
$alias_field='mf_alias';
$nametabl="Manufacturers";
$multy_field='mf_cat_id';
$parent_view="manufacturer_cats";
$parent_code='mfc_id';
$keysort="mf_name";
$buttons=array(
		"go_up"=> array("show"=>1,"link"=>false),
		"new"=>array("show"=>1,"view"=>"manufacturers","task"=>"modify"),
		"filter"=>array("show"=>1,"view"=>"manufacturers"),
		"modify"=>array("show"=>1,"view"=>"manufacturers","task"=>"modify"),
		"delete"=>array("show"=>1,"view"=>"manufacturers","task"=>"delete"),
		"undelete"=>array("show"=>1,"view"=>"manufacturers","task"=>"delete"),
		"trash"=>array("show"=>1,"link"=>false),
		"clean_trash"=>array("show"=>1,"link"=>false)
);
$cur_table_arr=array(
		"field"=>array(1=>'mf_id', 'mf_cat_id', 'mf_name', 'mf_alias', 'mf_logo', 'mf_email', 'mf_url', 'mf_desc', 'mf_enabled','mf_deleted'),
		"name"=>array(1=>'ID', 'Category', 'Name', 'Alias', 'Logo', 'email', 'url', 'Description', 'Enabled', 'Deleted'),
		"input_type"=>array(1=>'hidden','select','text','text','image','text','text','texteditor','checkbox',"hidden"),
		"val_type"=>array(1=>'int','int','string','string','string','string','string','text','boolean','boolean'),
		"upload_path"=>array(5=>'manufacturers/logo'),
		"link"=>array(3=>'index.php?module=catalog&view=manufacturers&task=modify&psid=[0]&multy_code=[1]&sort=[2]&page=[3]&orderby=[4]'),
		"link_key"=>array(3=>'id,multy_code,sort,page,orderby'),
		"link_type"=>array(3=>''),
		"check_value"=>array(4=>3),
		"ch_table" => array(2=>"manufacturer_categories"),
		"ch_field" => array(2=>"mfc_name"),
		"ch_id"    => array(2=>"mfc_id")
);
?>