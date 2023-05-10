<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname='profiles';
$keystring='pf_id';
$deleted='pf_deleted';
$nametabl="Public info";
$cur_table_arr=array(
		"field"=>array(1=>'pf_id','pf_deleted','pf_pollkey','pf_age','pf_sex', //1-5
				'pf_site','pf_icq','pf_skype','pf_jabber','pf_img','pf_text'),//6-10
		"name"=>array(1=>'ID','Deleted','Poll key', 'Age','Sex',
				'Personal web page','icq','Skype','Jabber','User image','About self'),

		"input_type"=>array(1=>'hidden','hidden','label','text','select',
				'text','text','text','text','image','textarea'),

		"val_type"=>array(1=>'int','int','string','string','string',
				'string','string','string','string','string','text'),
		"upload_path"=>array(10=>'i/avatars'),
		"ck_reestr"   => array(5=>"sex")
);
?>