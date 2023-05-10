<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class userModelusers extends SpravModel {

	public function cleanRecords()	{
		if (parent::cleanRecords()){
			$query = "DELETE FROM #__users_addr WHERE a_uid NOT IN (SELECT u_id FROM #__users)".$this->_db->getDelimiter();
			$query.= "DELETE FROM #__users_bank WHERE b_uid NOT IN (SELECT u_id FROM #__users)".$this->_db->getDelimiter();
			$query.= "DELETE FROM #__users_company WHERE c_id NOT IN (SELECT u_id FROM #__users)".$this->_db->getDelimiter();
			$query.= "DELETE FROM #__profiles WHERE pf_id NOT IN (SELECT u_id FROM #__users)".$this->_db->getDelimiter();
			$query.= "UPDATE #__users_vendors SET uv_uid=0 WHERE uv_uid NOT IN (SELECT u_id FROM #__users)".$this->_db->getDelimiter();
			$this->_db->setQuery($query); if(!$this->_db->query_batch(true,true)) return false;
			return true;
		} else  return false;
	}
}
?>