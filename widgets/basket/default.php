<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_WIDGET_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class basketWidget extends Widget {
	protected $_requiredModules = array("catalog");
	
	protected function setParamsMask(){
		parent::setParamsMask();
		$this->addParam("Basket_ID", "string", "ajax_basket");
	}
	public function prepare() {
		$Basket_ID = $this->getParam('Basket_ID');
		Portal::getInstance()->addScriptDeclaration("basketWidgetID.push('".$Basket_ID."')");
	}
	public function render() {
		if (catalogConfig::$ordersDisabled) {
			$this->set('show_title',0);
			return "";
		}
		$html = $this->get('content','');
		if ($html) $html=$html; else $html="";
		$Basket_ID = $this->get('Basket_ID','ajax_basket');
		$html.="<div id=\"".$Basket_ID."\">".Basket::getInstance()->showMini()."</div>";
		return $html;
	}
}
?>