<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname	= 'acl_roles';
$keystring	= 'ar_id';
$deleted	= 'ar_deleted';
$nametabl	= "ACL roles list";

$buttons = array(
			"new"		=> array("show"=>1,"view"=>"roles","task"=>"modify"),
			"filter"	=> array("show"=>1,"view"=>"roles"),
			"modify"	=> array("show"=>1,"view"=>"roles","task"=>"modify"),
			"delete"	=> array("show"=>1,"view"=>"roles","task"=>"delete"),
			"undelete"	=> array("show"=>1,"view"=>"roles","task"=>"delete"),
			"trash"		=> array("show"=>1,"link"=>false),
			"clean_trash"		=> array("show"=>1,"link"=>false)
);

$cur_table_arr = array(
			"field"		=> array(1=>"ar_id","ar_name","ar_title","ar_admin","ar_active","ar_system","ar_deleted"),
			"name"		=> array(1=>"Role ID","Role name","Title","Role can admin","Role is active","Role is system","Deleted"),
			"val_type"	=> array(1=>"int","string","string","boolean","boolean","boolean","int"),
			"view"		=> array(1=>0,1,1,1,0),
			"link"		=> array(2=>"index.php?module=aclmgr&view=rules&roleid=[0]"),
			"link_key"	=> array(2=>'id'),
			"link_type"	=> array(2=>''),
			"input_type"=> array(1=>'hidden','text','text','checkbox','checkbox','hidden','hidden'),
			"update_type"=> array(2=>0)
);

?>