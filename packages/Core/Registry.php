<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class Registry extends FieldSet {
	private static $_instance = null;
	private $_cfgKeys = array();
	public static function createInstance() {
		if (self::$_instance == null) self::$_instance = new self();
	}
	public static function getInstance() {
		self::createInstance();
		return self::$_instance;
	}
}
?>