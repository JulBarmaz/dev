<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$tablname='orders';
$keystring='o_id';
$deleted='o_deleted';
$nametabl="Orders";
$keysort="o_id";
$keysort_dest="DESC";
$buttons=array(
		"go_up"=> array("show"=>0,"link"=>false),
		"new"=>array("show"=>0,"view"=>"orders","task"=>"modify"),
		"filter"=>array("show"=>1,"view"=>"orders", "layout"=>"default"),
		"modify"=>array("show"=>1,"view"=>"orders","task"=>"modify"),
		"print"=>array("show"=>1,"view"=>"orders","task"=>"printOrders"),
		"delete"=>array("show"=>0,"view"=>"orders","task"=>"delete"),
		"undelete"=>array("show"=>0,"view"=>"orders","task"=>"delete"),
		"trash"=>array("show"=>0,"link"=>false)
);
$uni_buttons=array(	// массив уникальных кнопок
		"list_goods"=>array(
				"title"=>"Goods list",
				"alt"=>'G',
				"reset_multy"=>"true",
				"layout"=>'order',
				"view"=>'orders',
				"module"=>'catalog',
				"position"=>"right",
				"alert"=>"Elements are not chosen"
		),
);

$cur_table_arr=array(
		"field"=>array(1=>'o_id','o_date','o_uid','o_pt_id','o_pt_name',
						'o_pt_data','o_pt_sum','o_pt_result','o_userdata','o_dt_id',
						'o_dt_name','o_dt_data','o_dt_sum','o_discount_sum','o_taxes_sum',
						'o_total_sum','o_points','o_currency','o_status','o_paid',
						'o_ip_address','o_comments','o_deleted'),
		"name"=>array(1=>'Order number','Order date','Username','Payment ID','Payment type',
						'Payment data','Payment commission','Payment result',"User data",'Delivery ID',
						'Delivery type','Delivery data','Delivery sum','Discount','Taxes',
						'Total','Points','Currency','Status','Is paid',
						'IP address','Comments','Deleted'),
		"input_type"=>array(1=>'label','label','label_sel','hidden','label',
						'hidden','label','hidden','hidden','hidden',
						'label','hidden','hidden','hidden','hidden',
						'label','label','label','select','hidden',
						'label','label','hidden'),
		"val_type"=>array(1=>'int','date','int','int','string',
						'string','currency','string','string','int',
						'string','string','currency','currency','currency',
						'currency','float','int','int','boolean',
						'string','string','boolean'),
		"link"=>array(2=>'index.php?module=catalog&view=orders&task=viewOrder&option=ajax&psid=[0]'),
		"link_key"=>array(2=>'id'),
		"link_type"=>array(2=>'popup'),
		"view"=>array(4=>0,9=>0,10=>0),
		"check_value"=>array(19=>1),
		"ch_table" => array(1=>"", 3=>"users",19=>'orders_status'),
		"ch_field" => array(1=>"", 3=>"u_nickname",19=>'os_name'),
		"ch_id"    => array(1=>"", 3=>"u_id",19=>'os_id')
);
?>