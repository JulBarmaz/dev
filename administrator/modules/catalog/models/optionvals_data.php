<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class catalogModelOptionvals_data extends SpravModel {
	public function getOptionName($psid){
		$sql = "SELECT o_title FROM #__goods_options AS o, #__goods_options_data AS d";
		$sql.=" WHERE o.o_id=d.od_opt_id AND d.od_id=".(int)$psid;
		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}
	public function getOptValsParent($psid){
		$sql = "SELECT d.od_opt_id FROM #__goods_options_data AS d WHERE d.od_id=".(int)$psid;
		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}
	
	public function getGoodsName($psid){
		$sql = "SELECT g_name FROM #__goods AS g, #__goods_options_data AS d";
		$sql.=" WHERE g.g_id=d.od_obj_id AND d.od_id=".(int)$psid;
		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}
	
}
?>