<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class confModelDopfields extends SpravModel {
	public function countFieldVals($field_id){
		$sql="SELECT COUNT(*) FROM #__fields_choices WHERE fc_field_id=".intval($field_id);
		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}
}
?>