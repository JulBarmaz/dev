<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class siteControllerdefault extends Controller {
	
	public function __construct($name,$module) {
		parent::__construct($name, $module);
	}
	public function showMap() {
		$model=$this->getModel();
		$view=$this->getView();
		$links=$model->getLinksArr();
		$view->assign("links",$links);
	}
}

?>