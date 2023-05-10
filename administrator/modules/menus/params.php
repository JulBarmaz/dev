<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class menusModuleParams{
	public static function _proceed(&$module){
		$module->addParam("thumbWidth", "integer", 50);
		$module->addParam("thumbHeight", "integer", 50);
		$module->addParam("core_listmenu", "string", "");
	}
}
?>