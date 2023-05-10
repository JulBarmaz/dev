<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class catalogModelsales extends SpravModel {
	public function getStatuses(){
		$sql="SELECT * FROM #__orders_status WHERE os_enabled=1 AND os_deleted=0 ORDER BY os_id ASC";
		$this->_db->setQuery($sql);
		$res=$this->_db->loadObjectList('os_id');
		return $res;
	}
	public function getOrders(){
		$user_id = User::getInstance()->getId();
		if($user_id) {
			$sql="SELECT * FROM #__orders as o, #__users_vendors as v WHERE o.o_vendor=v.uv_vid AND v.uv_uid>0 AND v.uv_uid=".$user_id." ORDER BY o_date DESC,o_id DESC";
			$this->_db->setQuery($sql);
			return $this->_db->loadObjectList();
		} else {
			return array();
		}
	}
}
?>