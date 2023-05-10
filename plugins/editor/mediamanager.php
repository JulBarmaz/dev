<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_PLUGIN_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class editorPluginmediamanager extends Plugin {
	protected $_events=array("editor.ckeditor_params");
	protected function setParamsMask(){
		parent::setParamsMask();
		$this->addParam("use_absolute_links", "boolean", 1);
	}
	protected function onRaise($event, &$data) {
		switch($event){
			case "editor.ckeditor_params":
				if(defined("_ADMIN_MODE")) {
					Portal::getInstance()->addScript("modules/service.js");
					$data[]="'showFileBrowserButton':'1'";
					$data[]="'useAbsoluteLinks':'".intval($this->getParam("use_absolute_links"))."'";
				}
			break;
			default:
			break;
		}
	}
}

?>