<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class userModuleParams {
	public static function _proceed(&$module){
		$module->addParam("cabinet_title", "title", Text::_("CABINET"));
		$module->addParam("hide_personal_tab","boolean", 0);
		$module->addParam("hide_profile_tab_base","boolean", 0);
		$module->addParam("hide_profile_tab_public","boolean", 0);
	}
}
?>