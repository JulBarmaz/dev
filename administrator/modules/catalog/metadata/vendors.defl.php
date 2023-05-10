<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname='vendors';
$keystring='v_id';
$namestring='v_name';
$alias_field='v_alias';
$deleted='v_deleted';
$enabled='v_enabled';
$nametabl="Vendors";
$multy_field='v_cat_id';
$parent_view="vendor_cats";
$parent_code='vc_id';
$parent_name='vc_name';
$keysort="v_name";
$buttons=array(
		"go_up"=> array("show"=>1,"link"=>false),
		"new"=>array("show"=>1,"view"=>"vendors","task"=>"modify"),
		"filter"=>array("show"=>1,"view"=>"vendors"),
		"modify"=>array("show"=>1,"view"=>"vendors","task"=>"modify"),
		"delete"=>array("show"=>1,"view"=>"vendors","task"=>"delete"),
		"undelete"=>array("show"=>1,"view"=>"vendors","task"=>"delete"),
		"trash"=>array("show"=>1,"link"=>false),
		"clean_trash"=>array("show"=>1,"link"=>false)
);
$cur_table_arr=array(
		"field"=>array(1=>'v_id', 'v_cat_id', 'v_name', 'v_store_name', 'v_minimum_basket', 
				'v_store_desc', 'v_contact_name', 'v_contact_phone', 'v_contact_email', 'v_ogrn', 
				'v_inn', 'v_kpp', 'v_boss', 'v_ca_name', 'v_bank', 
				'v_sett_acc', 'v_bik', 'v_bank_acc', 'v_address_p', 'v_address_u', 
				'v_phone', 'v_fax', 'v_url', 'v_logo', 'v_terms_of_service', 
				'v_pechat',	'v_enabled', 'v_deleted', 'v_alias'),
		"name"=>array( 1=>'ID', 'Category', 'Name', 'Store name', 'Minimum basket sum', 
				'Description', 'Contact name', 'Contact phone','Contact email', 'OGRN', 
				'INN', 'KPP', 'Boss', 'Chief account', 'Bank', 
				'Sett account', 'BIC', 'Bank account', 'Post address', 'Address registration',
				'Phone', 'Fax', 'Site', 'Logo', 'Terms of service', 
				'Stamp', 'Enabled', 'Deleted', 'Alias'),
		"input_type"=>array(1=>'hidden','select','text','text','text',
				'texteditor','text', 'text', 'text', 'text', 
				'text', 'text', 'text', 'text', 'text', 
				'text', 'text', 'text', 'address', 'address',
				'text', 'text', 'text', 'image', 'texteditor', 
				'image', 'checkbox', 'hidden', 'text'),
		"input_size"=>array(10=>15,11=>12,12=>9,16=>20,17=>9,18=>20),
		"val_type"=>array(1=>'int','int','string','string', 'currency',
				'text', 'string', 'string', 'string', 'string',
				'string', 'string', 'string', 'string', 'string',
				'string', 'string', 'string', 'string', 'string',
				'string', 'string', 'string', 'string', 'text',
				'string', 'boolean', 'boolean', 'string'),
		"check_value"=>array(2=>1, 3=>1, 29=>3),
		"upload_path"=>array(24=>'vendors/logo',26=>'vendors/stamp'),
		"link"=>array(3=>'index.php?module=catalog&view=vendors&task=modify&psid=[0]&multy_code=[1]&sort=[2]&page=[3]&orderby=[4]'),
		"link_key"=>array(3=>'id,multy_code,sort,page,orderby'),
		"link_type"=>array(3=>''),
		"ch_table" => array(2=>"vendor_categories"),
		"ch_field" => array(2=>"vc_name"),
		"ch_id"    => array(2=>"vc_id")
);
?>
