<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class confModelcladr extends SpravModel {
	public function cleanRT(){
		$query = "DELETE FROM #__addr_regions WHERE r_parent_id NOT IN (SELECT c_id FROM #__addr_countries)";
		$this->_db->setQuery($query); if(!$this->_db->query()) return false; 
		$query = "DELETE FROM #__addr_districts WHERE d_parent_id NOT IN (SELECT r_id FROM #__addr_regions)";
		$this->_db->setQuery($query); if(!$this->_db->query()) return false; 
		$query = "DELETE FROM #__addr_localities WHERE l_parent_id NOT IN (SELECT d_id FROM #__addr_districts)";
		$this->_db->setQuery($query); if(!$this->_db->query()) return false;
		return true;
	}
	public function cleanRecords()	{
		if(parent::cleanRecords()) return $this->cleanRT();	else return false;
	}
	
}
?>