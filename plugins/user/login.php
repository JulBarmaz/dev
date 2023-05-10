<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_PLUGIN_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class userPluginlogin extends Plugin {
	protected $_events = array("user.login_form","user.before_login","user.after_login","user.logout");
	protected function setParamsMask(){
		parent::setParamsMask();
	}
	protected function onRaise($event, &$data) {
		switch($event){
			case "user.login_form":
				break;
			case "user.before_login":
				break;
			case "user.after_login":
				break;
			case "user.logout":
				break;
		}
	}
}
?>