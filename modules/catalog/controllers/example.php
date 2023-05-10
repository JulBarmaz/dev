<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

require_once realpath(dirname(__FILE__)).DS.'default.php';

class catalogControllerexample extends catalogControllerdefault {
	// CHANGE NAME TO DEFAULT MODE CONSTRUCTOR
	public function __construct($name,$module) {
		parent::__construct("default",$module);
	}
	/*
	// STANDART MODE CONSTRUCTOR
	public function __construct($name,$module) {
		parent::__construct($name,$module);
	}
	*/
	
	// override showGoods()
	public function showGoods() {
		echo "<h3 style=\"color:red !important;text-align:center;padding:15px;background:yellow;font-weight:bold;\">We are in example controller</h3>";
		parent::showGoods();
	}
}
?>