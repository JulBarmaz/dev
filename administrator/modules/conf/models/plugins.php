<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class confModelplugins extends SpravModel {
	public function saveParams($id,$def_params,$params) {
		$p_params = Params::intersect($params, $def_params);
		$query = "UPDATE #__plugins SET p_params='".$p_params."' WHERE p_id=".$id;
		$this->_db->setQuery($query);
		return $this->_db->query();
	}
}

?>