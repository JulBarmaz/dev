<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class videosetControllerdefault extends SpravController {
	public function showgroups() {
		$this->showData();
	}
	public function showitems() {
		$this->showData('vgr_title','videoset_groups');
	}
	public function showvideos() {
		$this->showData('vg_title','videoset_galleries');
	}

}
?>