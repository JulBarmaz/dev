<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class installerModelinstall extends Model {
	public function getInstalledPackages(){
		$sql="SELECT * FROM #__install ORDER BY c_type,c_name";
		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}
	public function getXMLData($psid){
		$sql="SELECT c_data FROM #__install WHERE c_id=".$psid;
		$this->_db->setQuery($sql);
		return stripslashes($this->_db->loadResult());
	}
	public function getLicense($psid){
		$sql="SELECT c_license FROM #__install WHERE c_id=".$psid;
		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}
	
}
?>