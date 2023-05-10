<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname='goods_options';
$keystring='o_id';
$namestring='o_title';
$enabled='o_enabled';
$deleted='o_deleted';
$nametabl="Goods options";
/*
$multy_field='o_id';
$parent_table="goods_group";
$parent_name='ggr_name';
$parent_code='ggr_id';
$parent_subordination=false;
*/
$ordering_field	= "o_ordering";
$custom_sql="AND c.o_custom>10 AND c.o_custom<20";
$buttons=array(
		"new"=>array("show"=>1,"view"=>"options","task"=>"modifyOption"),
		"filter"=>array("show"=>1,"view"=>"options"),
		"modify"=>array("show"=>1,"view"=>"options","task"=>"modifyOption"),
		"reorder"=> array("show"=>1,"view"=>"options","task"=>"reorder"),
		"delete"=>array("show"=>1,"view"=>"options","task"=>"delete"),
		"undelete"=>array("show"=>1,"view"=>"options","task"=>"delete"),
		"trash"=>array("show"=>1,"view"=>"options","link"=>false),
		"clean_trash"=>array("show"=>1,"view"=>"options","link"=>false)
);
$cur_table_arr=array(
		"field"=>array(1=>'o_id','o_title','o_vals_count','o_default','o_type',
						'o_ordering','o_required','o_is_quantitative','o_deleted','o_custom',
						'o_enabled'	),
		"name"=>array(1=>'ID','Label <Description>',"Option values",'Default value','Field type',
						"Ordering",'Required','Is quantitative','Deleted',"Is custom field",
						"Enabled"),
		"descr"=>array(2=>"Comments in square brackets will not be displayed on the site"),
		"input_type"=>array(1=>'hidden','text',"label",'hidden','select',
							'text','checkbox','checkbox','hidden','hidden',
							'checkbox'),
		"val_type"=>array(1=>'int','string','string','string','int',
							'int','boolean','boolean','boolean','int',
							'boolean'),
		"fim"=>array(3=>"countOptionsVals"),
//		"link"=>array(3=>'index.php?module=catalog&view=optionvals&psid=[0]',''),
//		"link_key"=>array(3=>'id',''),
		"link_type"=>array(3=>''),
		"check_value"=>array(1=>0,1,0,0,1,0,0,0,1),
		"update_type"=>array(5=>0,10=>0),
		"default_value"=>array(10=>11,11=>1),
		"ch_table" => array(1=>"", 5=>"goods_opt_types"),
		"ch_field" => array(1=>"", 5=>"t_name"),
		"ch_id"    => array(1=>"", 5=>"t_id"),
		"ck_reestr" => array(10=>"goods_option_type")
);
?>