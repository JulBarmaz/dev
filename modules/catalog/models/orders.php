<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class catalogModelorders extends SpravModel {
	
	public function getDeliveryType($dt_id) {
		$sql="SELECT * FROM #__goods_dts WHERE dt_enabled=1 AND dt_deleted=0 AND dt_id=".$dt_id;
		if(!User::getInstance()->isAdmin()) $sql.=" AND dt_admin_only=0";
		$this->_db->setQuery($sql);
		$this->_db->loadObject($res);
		return $res;
	}
	public function getDeliveryTypes($pt_id=0,$order_weight=0,$order_total=-1) {
		if($pt_id){
			$sql="SELECT d.* FROM #__goods_dts as d, #__goods_dts_links as l
					WHERE l.dt_id=d.dt_id AND d.dt_enabled=1 AND d.dt_deleted=0 AND l.parent_id=".(int)$pt_id;
			if(!User::getInstance()->isAdmin()) $sql.=" AND d.dt_admin_only=0";
			if ($order_weight) $sql.=" AND (d.dt_weight_limit=0 || d.dt_weight_limit>".$order_weight.")";
			$sql.=" ORDER BY d.dt_ordering, d.dt_name";
		} else {
			$sql="SELECT * FROM #__goods_dts WHERE dt_enabled=1 AND dt_deleted=0";
			if(!User::getInstance()->isAdmin()) $sql.=" AND dt_admin_only=0";
			$sql.=" ORDER BY dt_ordering, dt_name";
		}
		$this->_db->setQuery($sql);
		$dt_list = $this->_db->loadObjectList();
		if($order_total != -1){
			$new_dts=array();
			foreach ($dt_list as $a=>$dt){
				$dt_min_sum = Currency::getInstance()->convert($dt->dt_min_sum, $dt->dt_currency, DEFAULT_CURRENCY);
				$dt_max_sum = Currency::getInstance()->convert($dt->dt_max_sum, $dt->dt_currency, DEFAULT_CURRENCY);
				if($dt_min_sum==0 && $dt_max_sum==0) $new_dts[]=$dt;
				elseif($dt_min_sum>0 && $dt_max_sum>0){
					if($order_total>$dt_min_sum && $order_total<=$dt_max_sum) $new_dts[]=$dt;
				} elseif($dt_min_sum>0){
					if($order_total>$dt_min_sum) $new_dts[]=$dt;
				} elseif($dt_max_sum>0){
					if($order_total<=$dt_max_sum) $new_dts[]=$dt;
				}
			}
			$dt_list=$new_dts;
		}
		return $dt_list;
	}
	public function getPaymentType($pt_id) {
		$sql="SELECT * FROM #__goods_pts WHERE pt_enabled=1 AND pt_deleted=0 AND pt_id=".$pt_id;
		if(!User::getInstance()->isAdmin()) $sql.=" AND pt_admin_only=0";
		$this->_db->setQuery($sql);
		$this->_db->loadObject($res);
		return $res;
	}
	public function getPaymentTypes() {
		$sql="SELECT * FROM #__goods_pts WHERE pt_enabled=1 AND pt_deleted=0";
		if(!User::getInstance()->isAdmin()) $sql.=" AND pt_admin_only=0";
		$sql.=" ORDER BY pt_ordering, pt_name";
		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}
	public function getStatuses(){
		$sql="SELECT * FROM #__orders_status WHERE os_enabled=1 AND os_deleted=0 ORDER BY os_id ASC";
		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList('os_id');
	}
	public function getOrders(){
		$user_id = User::getInstance()->getId();
		if($user_id) {
			$sql="SELECT * FROM #__orders WHERE o_uid=".$user_id." ORDER BY o_date DESC,o_id DESC";
			$this->_db->setQuery($sql);
			return $this->_db->loadObjectList();
		} else {
			return array();
		}
	}
	public function getAbstractOrder($order_id){
		$sql="SELECT * FROM #__orders WHERE o_id=".$order_id;
		$this->_db->setQuery($sql);
		$this->_db->loadObject($res);
		return $res;
	}
	public function getOrder($order_id, $order_hash){
		$user_id=User::getInstance()->getID();
		if ($user_id) {
			$sql="SELECT * FROM #__orders WHERE o_uid=".$user_id." AND o_id=".$order_id;
			$this->_db->setQuery($sql);
			$this->_db->loadObject($res);
			return $res;
		} elseif ($order_hash) {
			$sql="SELECT * FROM #__orders WHERE o_hash='".$order_hash."' AND o_id=".$order_id;
			$this->_db->setQuery($sql);
			$this->_db->loadObject($res);
			return $res;
		} else {
			return null;
		}
	}
	public function getOrderItems($order_id){
		$sql="SELECT * FROM #__orders_items WHERE i_order_id=".$order_id;
		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}
	public function saveOrder($pt, $dt, $userdata, $hash) {
		$calculation_error=true;
		$user_id = User::getInstance()->getId();
		$basket=Basket::getInstance();
		$_goods = $basket->calculateOrder($pt, $dt, 0, $userdata);
		$calculation_error=!(!$pt->isError() && !$dt->isError());
		$order_comments=Request::getSafe("order_comments","");
		$currency = DEFAULT_CURRENCY;
		$measure=catalogConfig::$default_measure;
		$w_measure=catalogConfig::$default_wmeasure;
		if (!$calculation_error && $_goods && count($_goods)>0) {
			$ip_address = User::getInstance()->getIP();
			if (catalogConfig::$multy_vendor) {
				$vendor=Session::getVar("basket_vendor");
			} else {
				$vendor=catalogConfig::$default_vendor;
			}
			$order_sql = "INSERT INTO #__orders
					(o_id, o_hash, o_date, o_uid, o_vendor,	o_pt_id, o_pt_name, o_pt_data, o_pt_sum, o_pt_result, o_userdata,
					o_dt_id, o_dt_name, o_dt_data, o_dt_sum, o_dt_tax_id, o_dt_tax_name, o_dt_tax_val, o_dt_tax_sum, o_discount_sum, o_taxes_sum,
					o_total_sum, o_points, o_currency, o_quantity, o_measure, o_weight, o_wmeasure,
					o_status, o_paid, o_ip_address,o_comments)
					VALUES
					(NULL,
					'".$hash."',
					'".Date::nowSQL()."',
					'".$user_id."',
					'".$vendor."',
					'".$pt->getId()."',
					'".$pt->getTitle()."',
					'".$pt->getEncodedData()."',
					'".$basket->paymentSum."',
					'".$pt->getEncodedArray()."',
					'".$pt->getEncodedArray($userdata)."',
					'".$dt->getId()."',
					'".$dt->getTitle()."',
					'".$dt->getEncodedData()."',
					'".$basket->deliverySum."',
					'".$dt->getTaxId()."',
					'".$basket->getTaxName($dt->getTaxId())."',
					'".$basket->getTaxValue($dt->getTaxId())."',
					'".$basket->getTaxSum($dt->getTaxId(), $basket->deliverySum)."',
					'".$basket->discountSum."',
					'".$basket->taxesSum."',
					'".$basket->total."',
					'".$basket->points."',
					'".$currency."',
					'".$basket->quantity."',
					'".$measure."',
					'".$basket->weight."',
					'".$w_measure."',
					'".catalogConfig::$default_order_status."',
					'0',
					'".$ip_address."',
					'".$order_comments."'
					)";
			$this->_db->setQuery($order_sql);
			if ($this->_db->query()) {
				$order_id=$this->_db->insertid();
				foreach ($_goods as $key=>$val) {
					$items_sql="INSERT INTO #__orders_items
							(
							i_id,
							i_order_id,
							i_g_id,
							i_g_sku,
							i_g_name,
							i_g_extcode,
							i_g_options,
							i_g_options_text,
							i_g_quantity,
							i_g_measure,
							i_g_price,
							i_g_weight,
							i_g_wmeasure,
							i_g_tax_id,
							i_g_tax_val,
							i_g_tax,
							i_g_tax_name,
							i_g_sum
							)
							VALUES
							(
							NULL,
							'".$order_id."',
							'".$val->g_id."',
							'".$val->g_sku."',
							'".$val->g_name."',
							'".$val->g_extcode."',
							'".addslashes(json_encode($val->options_data))."',
							'".implode("; ", $val->options_text)."',		
							'".$val->g_quantity."',
							'".$val->g_sell_measure."',
							'".$val->g_price."',
							'".$val->g_weight."',
							'".$val->g_wmeasure."',
							'".$val->g_tax_id."',
							'".$val->g_tax_val."',
							'".$val->g_tax."',
							'".$val->g_tax_name."',
							'".$val->g_sum."'
							)";
					$this->_db->setQuery($items_sql);
					if (!$this->_db->query()) {
						$basket->order_message=Text::_("Order items add failed"); return false;
					} else {
						$basket->reduceStocks($key);
						$item_id=$this->_db->insertid();
						if(count($val->options_files)){
							foreach($val->options_files as $fk=>$fv){
								$upload_dir=Files::checkUserDir("orders".DS.Files::getAppendix($fv[0]['value']), "catalog");
								if(copy(Basket::getInstance()->getTempFile($fv[0]['value']), $upload_dir.DS.$fv[0]['value'])) {
									$files_sql="INSERT INTO `#__orders_files` (`f_id`, `f_order_id`, `f_item_id`, `f_g_id`, `f_opt_id`, `f_opt_title`, `f_opt_file` )
												VALUES (NULL, '".$order_id."', '".$item_id."', '".$val->g_id."', '".$fk."', '".$fv[0]['title']."', '".$fv[0]['value']."');";
									$this->_db->setQuery($files_sql);
									if (!$this->_db->query()) {
										$basket->order_message=Text::_("Order items add failed")." (2). ".Text::_("Copying files failed"); return false;
									}		
								} else {
									$basket->order_message=Text::_("Order items add failed")."  (1). ".Text::_("Copying files failed"); return false;
								}
							}
						}
					}
				}
			} else {
				$basket->order_message=Text::_("Order add failed"); 
				return false;
			}
			$basket->order_complete = 1;
			$basket->order_id=$order_id;
			$basket->order_vendor=$vendor;
			$basket->order_message=Text::_("Your order accepted");
			return $order_id;
		} elseif($calculation_error) {
			$basket->order_message=Text::_("Order calculation error"); return false;
		} else { 
			$basket->order_message=Text::_("Basket is empty"); return false;
		}
	}
}
?>