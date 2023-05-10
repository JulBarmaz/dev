<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_WIDGET_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class customHTMLWidget extends Widget {
	protected function setParamsMask(){
		parent::setParamsMask();
		$this->addParam("Translate", "boolean", 0);
	}
	public function render() {
		if ($this->getParam('Translate')) return Text::_($this->getParam('content'));
		else return $this->getParam('content');
	}
}
?>