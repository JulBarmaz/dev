<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class galleryControllerdefault extends SpravController {
	public function showgroups() {
		$this->showData();
	}
	public function showitems() {
		$this->showData('gr_title','gallery_groups');
	}
	public function showimages() {
		$this->showData('g_title','galleries');
	}

}
?>