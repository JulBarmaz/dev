<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class commentsModelgroups extends SpravModel {

	public function cleanRules(){
		// подчищаем правила
		$query = "DELETE FROM #__comms_acl WHERE ca_grp_id NOT IN (SELECT cg_id FROM #__comms_grp)";
		$this->_db->setQuery($query); if(!$this->_db->query()) return false; 
		return true;
	}
	
	public function cleanRecords(){
		if(parent::cleanRecords()) return $this->cleanRules();	else return false;
	}
}

?>