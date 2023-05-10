<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class mailModule extends Module {
	public function prepare() {
		/* Not need here if is set in module settings */ $this->setDefaultView('contents');
	}
	public function writeLetter($recieverId,$theme,$text) {
		$model = $this->getModel('letter');
		$model->writeLetter(intval($recieverId),$theme,$text);
	}
}
?>