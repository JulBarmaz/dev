<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class Measure extends BaseObject{
	private $_loadedMeasures = array();

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
	public function getMeasure($id){
		if (!array_key_exists($id,$this->_loadedMeasures)) $this->loadMeasure($id);
		return $this->_loadedMeasures[$id];
	}
	private function loadMeasure($id){
		$res=false;
		$db=Database::getInstance();
		$db->setQuery("SELECT * FROM #__measure WHERE meas_id='".$id."'");
		$db->loadObject($res);
		$this->_loadedMeasures[$id]=$res;
	}
	public function getCode($id){
		$meas = $this->getMeasure($id);
		if ($meas) return $meas->meas_code; else return "";
	}
	public function getTitle($id){
		$meas = $this->getMeasure($id);
		if ($meas) return $meas->meas_full_name; else return "";
	}
	public function getShortName($id){
		$meas = $this->getMeasure($id);
		if ($meas) return $meas->meas_short_name; else return "";
	}
	public function getType($id){
		$meas = $this->getMeasure($id);
		if ($meas) return $meas->meas_type; else return 0;
	}
	public function getKoeff($id){
		$meas = $this->getMeasure($id);
		if ($meas) return $meas->meas_kf; else return 0;
	}
	public function convert($quantity, $in_id, $out_id){
		$meas_in=$this->getMeasure($in_id);
		$meas_out=$this->getMeasure($out_id);
		if ($meas_in && $meas_out && ($meas_in->meas_type == $meas_out->meas_type)){
			if (!$meas_out->meas_kf) return 0;
			return $quantity * $meas_in->meas_kf / $meas_out->meas_kf;
		} else return 0;
	}
}
