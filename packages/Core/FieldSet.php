<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class FieldSet {
	private $_defs	= array();
	public function get($key, $defaultValue='', $emty2default=false) {
		if (array_key_exists($key,$this->_defs)) {
			if($emty2default && !$this->_defs[$key]) return $defaultValue;
			else return $this->_defs[$key];
		}	else {
			return $defaultValue;
		}
	}
	public function set($key, $value='') {
		if (array_key_exists($key,$this->_defs)) {
			$oldValue = $this->_defs[$key];
		} else $oldValue = null;
		$this->_defs[$key] = $value;
		return $oldValue;
	}
	public function toArray() {
		return $this->_defs;
	}
}
?>