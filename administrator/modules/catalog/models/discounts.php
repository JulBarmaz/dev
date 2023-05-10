<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class catalogModelDiscounts extends SpravModel {
	public function getGoods($psid){
		$sql="SELECT DISTINCT d.g_id AS id, CONCAT(g.g_sku,' : ', g.g_name) AS title FROM #__goods_discounts AS d, #__goods AS g WHERE g.g_id=d.g_id AND d.d_id=".$psid;
		$this->_db->setQuery($sql);
		return $this->_db->loadAssocList();
	}
	public function saveGoods($psid, $goods){
		$sql="DELETE FROM #__goods_discounts WHERE d_id=".$psid.$this->_db->getDelimiter();
		$res=true;
		if(count($goods)){
			foreach($goods as $gid){
				$sql.="INSERT INTO #__goods_discounts VALUES(".$gid.",".$psid.")".$this->_db->getDelimiter();
			}
		}
		$this->_db->setQuery($sql);
		return $this->_db->query_batch(true,true);
	}
}
?>