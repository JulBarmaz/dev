<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class confModelDopfields_groups extends SpravModel {
	public function countFieldVals($field_id){
		$sql="SELECT COUNT(*) FROM #__fields_list WHERE f_group=".intval($field_id);
		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}
}
?>