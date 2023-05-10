<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class siteModule extends Module {
	public function prepare() {
		/* Not need here if is set in module settings */ $this->setDefaultView('map');
	}
}
?>