<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname='fields_list';
$keystring='f_id';
$namestring='f_descr';
$deleted='f_deleted';
$nametabl="Additional fields";
$custom_sql="AND c.f_custom<2";

$parent_view="dopfields_groups";
$parent_code='fg_id';
$multy_field='f_group';

$buttons=array(
		"go_up"=> array("show"=>1,"link"=>false),
		"new"=>array("show"=>1,"view"=>"dopfields","task"=>"modifyDopfield"),
		"filter"=>array("show"=>1,"view"=>"dopfields"),
		"modify"=>array("show"=>1,"view"=>"dopfields","task"=>"modifyDopfield"),
		"delete"=>array("show"=>1,"view"=>"dopfields","task"=>"delete"),
		"undelete"=>array("show"=>1,"view"=>"dopfields","task"=>"delete"),
		"trash"=>array("show"=>1,"view"=>"dopfields","link"=>false),
		"clean_trash"=>array("show"=>1,"view"=>"dopfields","link"=>false)
);
$cur_table_arr=array(
		"field"=>array(1=>'f_id','f_group','f_name','f_vals_count','f_descr',
						'f_default', 'f_type', 'f_writeable','f_required','f_deleted',
						'f_table', 'f_custom'),
		"name"=>array(1=>'ID','Group','Name in latin <together>','Field values','Label <Description>',
						'Default value', 'Field type', 'Writeable','Required','Deleted',
						'Table', "Is custom field"),
		"input_type"=>array(1=>'hidden','select','text','label','text',
						'text', 'select','hidden','checkbox','hidden',
						'select', 'select'),
		"input_size"=>array(1=>false,1,20,50,50,
							50,1,1,1,false,
							1,1),
		"view"=>array(1=>1,1,1,1,1,1,1,1,1,0,1,1),
		"val_type"=>array(1=>'int','int','string','string','string',
							'string','int','boolean','boolean','boolean',
							'string','int'),
		"fim"=>array(4=>"countFieldVals"),
		"field_on_change"=>array(7=>"dopFieldTypeOnChange(this)"),
		"check_value"=>array(1=>0,1,2,0,1,
								0,1,0,0,0,
								1,0),
		"link"=>array(5=>'index.php?module=conf&view=dopfields&task=modifyDopfield&psid=[0]&multy_code=[1]'),
		"link_key"=>array(5=>'id,multy_code'),
		
		"default_value"=>array(1=>0,8=>1,12=>0),
		"update_type"=>array(1=>0, 3=>0, 7=>0, 11=>0, 12=>0),
		"ch_table" => array(1=>"", 2=>"fields_groups", 7=>"fields_type"),
		"ch_field" => array(1=>"", 2=>"fg_name", 7=>"t_name"),
		"ch_id"    => array(1=>"", 2=>"fg_id", 7=>"t_id"),
		"ck_reestr" => array(11=>"extendable_tables", 12=>"add_field_type")
);
?>