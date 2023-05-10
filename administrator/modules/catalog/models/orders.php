<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class catalogModelorders extends SpravModel {
	public function decodeOrdersData($orders){
		if (count($orders)) {
			foreach($orders as $order){
				$datafields = array("o_dt_data", "o_pt_data", "o_userdata", "o_pt_result");
				foreach($datafields as $fld){
					if(backofficeConfig::$cryptoUserData) $order->{$fld}=Userdata::getInstance($order->o_uid, true)->decode($order->{$fld});
					else $order->{$fld}=json_decode(base64_decode($order->{$fld}), true);
				}
			}
		}
		return $orders;
	}
	public function getStatusName($psid){
		$status = $this->getStatus($psid);
		if(isset($status->os_name)) return $status->os_name;
		else return "";
	}
	public function setStatus($psid, $status){
		if(!$status || !$psid) return false;
		$sql="UPDATE `#__orders` SET `o_status`=".$status." WHERE `o_id`=".$psid;
		$this->_db->setQuery($sql);
		return $this->_db->query();
	}
	public function getStatus($psid){
		$object = null;
		$sql="SELECT * FROM #__orders_status WHERE os_enabled=1 AND os_deleted=0 AND os_id=".$psid;
		$this->_db->setQuery($sql);
		$this->_db->loadObject($object);
		return $object;
	}
	public function getStatuses(){
		$sql="SELECT * FROM #__orders_status WHERE os_enabled=1 AND os_deleted=0 ORDER BY os_id ASC";
		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList('os_id');
	}
	public function getOrders(){
		$reestr = Module::getInstance()->get('reestr');
		$psids = $reestr->get('arr_psid');
		if (is_array($psids)) $arr_psid="(".implode(",",$psids).")"; else $arr_psid="(".$psids.")";
		$sql="SELECT * FROM #__orders WHERE o_id IN ".$arr_psid." ORDER BY o_date DESC";
		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}
	public function getOrdersByDate($start_date=false, $end_date=false, $skip_deleted=false, $statuses=array()){
		$sql = "SELECT * FROM #__orders";
		$where = array();
		if($start_date && Date::isSQLDate($start_date)) $where[] = " o_date>='".$start_date."'";
		if($end_date && Date::isSQLDate($end_date)) $where[] = " o_date<='".$end_date."'";
		if(count($statuses)) $where[] = " o_status IN (".implode(",", $statuses).")";
		if($skip_deleted) $where[] = " o_deleted=0";
		if(count($where)) $sql.= " WHERE ".implode(" AND ", $where);
		$sql.= " ORDER BY o_date";
		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}
	public function getOrdersItems(){
		$reestr = Module::getInstance()->get('reestr');
		$arr_psid = $reestr->get('arr_psid');
		if (is_array($arr_psid)) $psids="(".implode(",",$arr_psid).")"; else $psids="(".$arr_psid.")";
		$sql="SELECT * FROM #__orders_items WHERE i_order_id IN ".$psids." ORDER BY i_order_id DESC";
		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}
	public function getOrderItems($psid){
		$sql="SELECT * FROM #__orders_items WHERE i_order_id =".$psid;
		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}
	/**
	 * список картинок для набора товаров
	 */
	public function getImageForItems($id_array) {
		$sql="select g_id,g_thumb,g_image,g_name from #__goods WHERE g_id IN ('".implode("','",$id_array)."')";
		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList('g_id');
	}
}
?>