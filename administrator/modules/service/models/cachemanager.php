<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class serviceModelcachemanager extends Model {
	public function getFilterCount()
	{
		$sql="select COUNT(*) as cnt FROM #__filters";
		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}
  public function clearUserFilter($uid=0)
  {
  	$sql="DELETE FROM #__filters";
  	if($uid) $sql.=" WHERE f_uid=".(int)$uid;
		$this->_db->setQuery($sql);
		return $this->_db->query();
  	
  }
}
?>