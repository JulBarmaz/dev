<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class blogModuleParams {
	public static function _proceed(&$module){
		$module->addParam("short_theme_length", "integer", 50);
		$module->addParam("exclude_cats_from_map", "string", "0");
		$module->addParam("exclude_blogs_from_map", "string", "0");
	}
}
?>