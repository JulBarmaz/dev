<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname='users';
$keystring='u_id';
$deleted='u_deleted';
$enabled="u_activated";
$nametabl="Users";

$buttons=array(
		"go_up"=> array("show"=>0,"link"=>false),
		"new"=>array("show"=>1,"view"=>"users","task"=>"modify"),
		"filter"=>array("show"=>1,"view"=>"users"),
		"clone"=>array("show"=>0,"view"=>"users","task"=>"make_clone"),
		"modify"=>array("show"=>1,"view"=>"users","task"=>"modify"),
		"undelete"=>array("show"=>1,"view"=>"users","task"=>"delete"),
		"delete"=>array("show"=>1,"view"=>"users","task"=>"delete"),
		"clean_trash"	=> array("show"=>1,"link"=>false),
		"trash"=>array("show"=>1,"link"=>false)
);
$uni_buttons=array(	// массив уникальных кнопок
		"vcard"=>array(
				"title"=>"VCard",
				"alt"=>'VC',
				"module"=>'user',
				"view"=>'panel',
				"task"=>'modifyProfile',
				"position"=>"right",
				"alert"=>"Elements are not chosen"
		)

);
$cur_table_arr=array(
		"field"=>array(1=>'u_id','u_affiliate_code','u_referral','u_login', // 1-4
				'u_secret','u_email','u_reg_date','u_nickname', // 5-8
				'u_account','u_points','u_discount','u_pricetype', // 9-12
				'u_role','u_rating','u_activated','u_deleted','u_last_visit','u_login_date'), //13-16

		"name"=>array(1=>'id','affiliate code','referral','User login',
				'password','User email','User registration date','User nickname',
				'Account status','Points','Personal discount','Type of price',
				'User role','rating','activated','Deleted','last visit','login date'),

		"input_type"=>array(1=>'label','label','text','text',
				'password','text','label','text',
				'label','label','text','select',
				'select','text','checkbox','hidden','label','label'),
		"val_type"=>array(1=>'int','string','string','string',
				'string','string','date','string',
				'float','float','float','int',
				'int','string','boolean','int','datetime','datetime'),
		"update_type"=>array(2=>0,4=>0,7=>0),
		"check_value"=>array(4=>2,6=>2,8=>2,12=>1,13=>1),
		"default_value"=>array(7=>"NOW",13=>backofficeConfig::$defaultUserRole),
		"ch_table" => array( 13=>"acl_roles"),
		"ch_field" => array( 13=>"ar_title"),
		"ch_id"    => array( 13=>"ar_id"),
		"ck_reestr"   => array(12=>"price_type")

);
?>