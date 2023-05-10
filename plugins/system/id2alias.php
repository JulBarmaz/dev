<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_PLUGIN_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class systemPluginid2alias extends Plugin {
	protected $_events = array("system.renderPortalBefore");
	protected function setParamsMask(){
		parent::setParamsMask();
	}
	protected function onRaise($event, &$data) {
		if(defined("_ADMIN_MODE")) return;
		if(!seoConfig::$sefMode) return;
		switch($event){
			case "system.renderPortalBefore":
				$router_vars=Router::getInstance()->getVarsArr();
				if(!array_key_exists('alias', $router_vars) && array_key_exists('psid', $router_vars) && array_key_exists('module', $router_vars)){
					$router_vars["alias"]=Router::getInstance($router_vars["module"])->getAlias($router_vars);
					if($router_vars["alias"]){
						$link=Router::getInstance($router_vars["module"])->buildRoute($router_vars);
						Util::redirect($link, "", 301);
					}
				}
			break;
		}
	}
}
?>