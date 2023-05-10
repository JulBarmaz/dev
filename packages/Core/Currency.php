<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class Currency extends BaseObject {
	//---------- Factory implementation ------------
	private static $_instances = Array();
	private $_default_currency = null;
	private $_dtc = null;
	private $_rate = Array();
	
	private static $_short_names = Array();
	private static $_codes = Array();

	private function __construct($dtc) {
		$this->initObj();
		$this->_dtc=$dtc;
		$this->_default_currency=DEFAULT_CURRENCY;//catalogConfig::$default_currency;
	}

	public static function getInstance($datetime_c='') {
		if ($datetime_c) $dtc=date('Y-m-d H:i:s', $datetime_c); else $dtc=DTC;
		if (array_key_exists($dtc,self::$_instances) == false) { self::$_instances[$dtc] = new self($dtc); }
		return self::$_instances[$dtc];
	}
	//------------------------------------------------
	public static function getCode($id=0) {
		if (!$id) return "";
		if(isset(self::$_codes[$id])) return self::$_codes[$id];
		$db=Database::getInstance();
		$sql="SELECT c_code FROM #__currency WHERE c_id=".(int)$id;
		$db->setQuery($sql);
		$result=$db->loadResult();
		if (!$result) $result=Text::_($id);
		self::$_codes[$id]=$result;
		return $result;
	}
	public static function getIdByCode($code="") {
		if (!$code) return 0;
		$id = array_search($code, self::$_codes);
		if(!$id){
			$db=Database::getInstance();
			$sql="SELECT c_id FROM #__currency WHERE c_code='".$code."' LIMIT 1";
			$db->setQuery($sql);
			$id=intval($db->loadResult());
			if($id) self::$_codes[$id]=$code;
		}
		return $id;
	}
	public static function getList($all=false) {
		$sql = "SELECT c_id AS id, c_name AS name FROM #__currency";
		if(!$all) $sql.= " WHERE c_enabled=1 AND c_deleted=0";
		$sql.=" ORDER BY c_name";
		Database::getInstance()->setQuery($sql);
		return Database::getInstance()->loadAssocList();
	}
	public static function getShortName($id=0) {
		if (!$id) return "";
		if(isset(self::$_short_names[$id])) return self::$_short_names[$id];
		$db=Database::getInstance();
		$sql="SELECT c_short_name FROM #__currency WHERE c_id=".(int)$id;
		$db->setQuery($sql);
		$result=$db->loadResult();
		if (!$result){
			$result=self::getCode($id);
		}
		self::$_short_names[$id]=$result;
		return $result;
	}
	private function getRate($cid) {
		$db=Database::getInstance();
		$sql="SELECT c_value FROM #__currency_rate WHERE c_id=".(int)$cid." AND c_datetime<='".$this->_dtc."' ORDER BY c_datetime DESC LIMIT 0,1";
		$db->setQuery($sql);
		if (!$result=$db->loadResult()) $result=0;
		$this->_rate[$cid]=$result;
	}
	private function convertFromDefault($val, $currency){
		if (array_key_exists($currency,$this->_rate) == false) {
			$this->getRate($currency);
		}
		if(!$this->_rate[$currency]) return 0;
		$_val = round($val/$this->_rate[$currency], catalogConfig::$price_digits);
		return $_val;
	}
	private function convertToDefault($val, $currency){
		if (array_key_exists($currency,$this->_rate) == false) { $this->getRate($currency); }
		$_val = round($val * $this->_rate[$currency], catalogConfig::$price_digits);
		return $_val;
	}
	public function convert($val,$from_currency='', $to_currency='') {
		if (!$from_currency || $from_currency==$to_currency) return round($val, catalogConfig::$price_digits);
		if (!$to_currency) $to_currency=$this->_default_currency;
		if ($from_currency !=$this->_default_currency) $_val = $this->convertToDefault($val, $from_currency);
		else $_val = $val;
		if ($to_currency==$this->_default_currency) return round($_val, catalogConfig::$price_digits);
		$_val = $this->convertFromDefault($_val, $to_currency);
		return round($_val, catalogConfig::$price_digits);
	}
}