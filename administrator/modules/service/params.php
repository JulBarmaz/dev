<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class serviceModuleParams {
	public static function _proceed(&$module){
		$module->addParam("process_records_per_pass","integer", 20);
	}
}
?>