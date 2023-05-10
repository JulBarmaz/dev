<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_PLUGIN_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class systemPlugingoogle_analytics extends Plugin {
	protected $_events=array("system.executeModuleBefore");
	protected function setParamsMask(){
		parent::setParamsMask();
		$this->addParam("using_plugin", "ro_string", Text::_("system_google_analytics_description"));
		$this->addParam("Site_ID", "string", "UA-XXXXXX-X");
		$this->addParam("deferred_loading_timeout", "integer", 0);
	}
	protected function onRaise($event, &$data) {
		if(defined("_ADMIN_MODE")) return;
		switch($event){
			case "system.executeModuleBefore":
				$Site_ID=$this->getParam("Site_ID");
				if(!$Site_ID || $Site_ID=="UA-XXXXXX-X") return;
				$deferred_loading_timeout = (int)$this->getParam("deferred_loading_timeout");
				$script = "
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', '".$Site_ID."');
				";
				if($deferred_loading_timeout){
					$script.= '
$(window).on(\'load\',function(){
	setTimeout(function(){ jQuery.getScript("https://www.googletagmanager.com/gtag/js?id='.$Site_ID.'"); },'.($deferred_loading_timeout*1000).');
});
';
				} else {
					Portal::getInstance()->addScript("https://www.googletagmanager.com/gtag/js?id=".$Site_ID, false, true);
				}
				Portal::getInstance()->addScriptDeclaration($script);
				break;
			default: break;
		}
	}
}

?>