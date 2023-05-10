<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname='discounts';
$keystring='d_id';
$namestring='d_name';
$enabled='d_enabled';
$deleted='d_deleted';
$nametabl="Discounts and surcharges";
$ordering_field	= "d_ordering";
$buttons=array(
		"new"=>array("show"=>1,"view"=>"discounts","task"=>"modify"),
		"filter"=>array("show"=>1,"view"=>"discounts"),
		"modify"=>array("show"=>1,"view"=>"discounts","task"=>"modify"),
		"reorder"=> array("show"=>1,"view"=>"discounts","task"=>"reorder"),
		"delete"=>array("show"=>1,"view"=>"discounts","task"=>"delete"),
		"undelete"=>array("show"=>1,"view"=>"discounts","task"=>"delete"),
		"trash"=>array("show"=>1,"view"=>"discounts","link"=>false),
		"clean_trash"=>array("show"=>1,"view"=>"discounts","link"=>false)
);
$cur_table_arr=array(
		"field"=>array(1=>'d_id', 'd_name', 'd_sign','d_value','d_period_unlimited', 
						'd_start_date', 'd_end_date', 'd_stop', 'd_comment', 'd_ordering', 
						'd_enabled', 'd_deleted'),
		"name"=>array(1=>'ID', 'Discount name', 'Sign', 'Discount value','Unlimited period', 
						'Start date', 'End date', 'Stop with this discount', 'Comment', 'Ordering', 
						'Enabled', 'Deleted'),
		"input_type"=>array(1=>'hidden','text','select',"text",'checkbox',
							'datetime_ajax', 'datetime_ajax','checkbox','textarea','text',
							'checkbox','hidden'),
		"val_type"=>array(1=>'int','string','string','float','boolean',
							'datetime','datetime','boolean','text','int',
							'boolean','boolean'),
		"link"=>array(2=>'index.php?module=catalog&view=discounts&task=modify&psid=[0]'),
		"link_key"=>array(2=>'id'),
		"link_type"=>array(2=>''),
		"check_value"=>array(3=>1, 4=>1),
		"default_value"=>array(3=>"-",5=>1, 6=>"NOW", 7=>"NOW"),
		"ck_reestr" => array(3=>"sign_vals_full")
);
?>