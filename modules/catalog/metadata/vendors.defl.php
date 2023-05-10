<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname='vendors';
$classTable="vendor-wrapper";
$keystring='v_id';
$namestring='v_name';
$alias_field='v_alias';
$deleted='v_deleted';
$enabled='v_enabled';
$nametabl="Goods vendors";
//$multy_field='v_cat_id';
//$parent_view="vendor_cats";
//$parent_code='vc_id';
//$parent_name='vc_name';
$checkbox=false;
$keysort="v_name";
$buttons=array(
		"go_up"=> array("show"=>0,"link"=>false),
//				"new"=>array("show"=>0,"view"=>"vendors","task"=>"modify"),
				"filter"=>array("show"=>1,"view"=>"vendors"),
//				"modify"=>array("show"=>0,"view"=>"vendors","task"=>"modify"),
				"delete"=>array("show"=>0,"view"=>"vendors","task"=>"delete"),
				"undelete"=>array("show"=>0,"view"=>"vendors","task"=>"delete"),
				"trash"=>array("show"=>0,"link"=>false)
);
$cur_table_arr=array(
		"field"=>array(
				1=>'v_id', 'v_cat_id', 'v_name', 'v_store_name', 'v_store_desc', 
				'v_contact_name', 'v_contact_phone', 'v_contact_email', 'v_ogrn', 'v_inn', 
				'v_kpp', 'v_boss', 'v_ca_name', 'v_bank', 'v_sett_acc', 
				'v_bik', 'v_bank_acc', 'v_address_p', 'v_address_u', 'v_phone', 
				'v_fax', 'v_url', 'v_logo', 'v_terms_of_service', 'v_pechat', 
				'v_enabled', 'v_deleted', 'v_alias'),
		"name"=>array(
				1=>'ID', 'Category', 'Name', 'Store name', 'Description', 
				'Contact name', 'Contact phone','Contact email', 'OGRN', 'INN', 
				'KPP', 'Boss', 'Chief account', 'Bank', 'Sett account', 
				'BIC', 'Bank account', 'Post address', 'Address registration', 	'Phone',
				'Fax', 'Site', 'Logo', 'Terms of service', 	'Stamp', 
				'Enabled', 'Deleted', 'Alias'),
		"input_type"=>array(
				1=>'hidden','select','text','text','texteditor',
				'text', 'text', 'text', 'text', 'text', 
				'text', 'text', 'text', 'text', 'text', 
				'text', 'text', 'address', 'address', 'text', 
				'text', 'text', 'image', 'texteditor', 'image', 
				'checkbox', 'hidden','text'),
		"input_size"=>array(9=>15,10=>12,11=>9,15=>20,16=>9,17=>20),
		"sort_order"=>array(18=>"NONE",19=>"NONE"),							
		"val_type"=>array(1=>'int','int','string','string','text',
						'string', 'string', 'string', 'string', 'string', 
						'string', 'string', 'string', 'string', 'string', 
						'string', 'string', 'string', 'string', 'string', 
						'string', 'string', 'string', 'text', 'string', 
						'boolean', 'boolean'),
		"link"=>array(4=>'index.php?module=catalog&view=vendors&layout=info&psid=[0]&alias=[1]'),
		"link_key"=>array(4=>'id,v_alias'),
		"link_type"=>array(4=>''),
		"upload_path"=>array(23=>'vendors/logo',25=>'vendors/stamp'),
		"ch_table" => array(2=>"vendor_categories"),
		"ch_field" => array(2=>"vc_name"),
		"ch_id"    => array(2=>"vc_id")
);
?>
