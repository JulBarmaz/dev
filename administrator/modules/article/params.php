<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class articleModuleParams{
	public static function _proceed(&$module){
		$module->addParam("use_rating", "boolean", 0);
		$module->addParam("breadcrumb_start", "string", "Articles");
		$module->addParam("breadcrumb_start_link", "string", "index.php?module=article");
		$module->addParam("breadcrumb_lenght", "integer", 0);
	}
}
?>