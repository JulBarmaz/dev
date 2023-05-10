<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class pollsModelpolls extends SpravModel {

	public function cleanPollStats(){
		// удаленные элементы
		$query = "DELETE FROM #__poll_items WHERE pi_deleted=1";
		$this->_db->setQuery($query); if(!$this->_db->query()) return false; 
		// элементы удаленных голосований
		$query = "DELETE FROM #__poll_items WHERE pi_poll_id IN (SELECT p_id FROM #__polls WHERE p_deleted=1)";
		$this->_db->setQuery($query); if(!$this->_db->query()) return false; 
		// статистика
		$query = "DELETE FROM #__poll_stats WHERE ps_item_id NOT IN (SELECT pi_id FROM #__poll_items)";
		$this->_db->setQuery($query); if(!$this->_db->query()) return false; 
		return true;
	}
	public function cleanRecords()	{
		if(parent::cleanRecords()) return $this->cleanPollStats();	else return false;
	}
	
}

?>