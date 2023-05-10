<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class pollsControllerdefault extends SpravController {
	public function showpolls() {
		$this->showData();
	}
	
	public function showItems() {
		$this->showData('p_title','polls');
	}
}
?>