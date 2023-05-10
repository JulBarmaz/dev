<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname='banners_clients';
$keystring='bcl_id';
$enabled='bcl_enabled';
$deleted='bcl_deleted';
$nametabl="ACRM clients";
$buttons=array(
			"new"=>array("show"=>1,"view"=>"clients","task"=>"modify"),
			"filter"=>array("show"=>1,"view"=>"clients"),
			"modify"=>array("show"=>1,"view"=>"clients","task"=>"modify"),
			"delete"=>array("show"=>1,"view"=>"clients","task"=>"delete"),
			"undelete"=>array("show"=>1,"view"=>"clients","task"=>"delete"),
			"trash"=>array("show"=>1,"link"=>false),
			"clean_trash"	=> array("show"=>1,"link"=>false)
);
$cur_table_arr=array(
				"field"=>array(1=>'bcl_id', 'bcl_name', 'bcl_contact', 'bcl_email', 'bcl_info', 'bcl_enabled','bcl_deleted'),
				"name" =>array(1=>'ID', 'Name', 'Contact name','Contact e-mail','Description','Enabled', 'Deleted'),
				"input_type"=>array(1=>'hidden','text','text','text','textarea','checkbox','hidden'),
				"val_type"=>array(1=>'int','string','string','string','text','boolean','boolean')
);
?>