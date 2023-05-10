<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname='banners_categories';
$keystring='bc_id';
$namestring='bc_name';
$enabled='bc_published';
$deleted='bc_deleted';
$nametabl="ACRM categories";
$enabled="bc_published";
$buttons=array(
			"new"=>array("show"=>1,"view"=>"cats","task"=>"modify"),
			"filter"=>array("show"=>1,"view"=>"cats"),
			"modify"=>array("show"=>1,"view"=>"cats","task"=>"modify"),
			"delete"=>array("show"=>1,"view"=>"cats","task"=>"delete"),
			"undelete"=>array("show"=>1,"view"=>"cats","task"=>"delete"),
			"trash"=>array("show"=>1,"link"=>false),
			"clean_trash"	=> array("show"=>1,"link"=>false)
);
$cur_table_arr=array(
				"field"=>array(1=>'bc_id', 'bc_name', 'bc_descr', 'bc_published', 'bc_deleted'),
				"name" =>array(1=>'ID', 'Name', 'Description', 'Published', 'Deleted'),
				"input_type"=>array(1=>'hidden','text','textarea','checkbox','hidden'),
				"val_type"=>array(1=>'int','string','text','boolean','boolean'),
				"link"=>array(2=>'index.php?module=acrm&view=items&psid=[0]'),
				"link_key"=>array(2=>'id'),
				"link_type"=>array(2=>'')
);
?>