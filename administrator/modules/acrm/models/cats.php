<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class acrmModelcats extends SpravModel {

	public function cleanCatsInItems(){
		// обнуляем удаленные категории в баннерах
		$query = "UPDATE #__banners SET b_cat_id=0 WHERE b_cat_id NOT IN ( SELECT bc_id FROM #__banners_categories)";
		$this->_db->setQuery($query); if(!$this->_db->query()) return false; 

		return true;
	}
	public function cleanRecords(){
		if(parent::cleanRecords()) return $this->cleanCatsInItems();	else return false;
	}
	
}
?>