<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class confModelmodules extends SpravModel {
	public function saveParams($id,$def_params,$params) {
		$_params = Params::intersect($params, $def_params);
		$query = "UPDATE #__modules SET m_config='".$_params."' WHERE m_id=".$id;
		$this->_db->setQuery($query);
		return $this->_db->query();
	}
	public function saveModule($id, $show_breadcrumb, $incl_map, $enabled) {
		$query = "UPDATE #__modules SET m_show_breadcrumb=".$show_breadcrumb.", m_incl_map=".$incl_map.", m_enabled=".$enabled." WHERE m_id=".$id;
		$this->_db->setQuery($query);
		return $this->_db->query();
	}
	
}
?>