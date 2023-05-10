<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class videosetModuleParams{
	public static function _proceed(&$module){
		$module->addParam("ggr_thumbAutoResize", "boolean", "1");
		$module->addParam("ggr_thumbWidth", "integer", "200");
		$module->addParam("ggr_thumbHeight", "integer", "200");
		$module->addParam("gal_thumbAutoResize", "boolean", "1");
		$module->addParam("gal_thumbWidth", "integer", "200");
		$module->addParam("gal_thumbHeight", "integer", "200");
	}
}
?>