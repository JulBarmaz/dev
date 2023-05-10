<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class Vendor extends BaseObject{
	private $_loadedVendors = array();

	//---------- Singleton implementation ------------
	private static $_instance = null;

	public static function createInstance() {
		if (self::$_instance == null) {
			self::$_instance = new self();
		}
	}

	public static function getInstance() {
		self::createInstance();
		return self::$_instance;
	}
	//------------------------------------------------

	private function __construct() {

	}
	public function getPostZipcode($id){
		$v=$this->getAddress($id, false);
		if (is_array($v) && isset($v['zipcode'])) return $v['zipcode']; else return "";
	}
	public function getPostAddress($id){
		$v=$this->getAddress($id, false);
		if (is_array($v) && isset($v['fullinfo'])) return $v['fullinfo']; else return "";
	}
	
	public function getAddress($id, $addr_type){
		$v=$this->getVendor($id);
		if ($v) {
			if ($addr_type) return json_decode(base64_decode($v->v_address_u),true);
			else return json_decode(base64_decode($v->v_address_p),true);
		} else return Address::getTmpl();
	}
	
	public function getVendor($id){
		if (!array_key_exists($id,$this->_loadedVendors)) $this->loadVendor($id);
		return $this->_loadedVendors[$id];
	}
	
	private function loadVendor($id){
		$res=false;
		$db=Database::getInstance();
		$db->setQuery("SELECT * FROM #__vendors WHERE v_enabled=1 AND v_deleted=0 AND v_id='".$id."'");
		$db->loadObject($res);
		$this->_loadedVendors[$id]=$res;
	}
	
}

?>