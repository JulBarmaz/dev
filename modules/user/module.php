<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class userModule extends Module {
	public function prepare() {
		/* Not need here if is set in module settings */ $this->setDefaultView('panel');
		Portal::getInstance()->addStyleSheet("sprav.css", true);
	}
}
?>