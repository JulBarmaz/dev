<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class acrmModelclients extends SpravModel {

	public function cleanClientsFromItems(){
		// обнуляем удаленных клиентов в баннерах
		$query = "UPDATE #__banners SET b_client_id=0 WHERE b_client_id NOT IN ( SELECT bcl_id FROM #__banners_clients)";
		$this->_db->setQuery($query); if(!$this->_db->query()) return false; 
		return true;
	}
	
	public function cleanRecords()	{
		if(parent::cleanRecords()) return $this->cleanClientsFromItems();	else return false;
	}
	
}
?>