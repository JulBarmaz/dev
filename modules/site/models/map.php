<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class siteModelmap extends Model {

	function getLinksArr() {
		$_arr=array();
		$i=0;
		$arr_name=Module::getInstalledModules(true, true);
		foreach($arr_name as $module) { // на всякий случай проверяем существует ли модуль на фронте
			if (file_exists(PATH_MODULES.$module.DS.'module.php')) {
				if (ACLObject::getInstance($module.'Module',false)->canAccess()){
					$_arr[$module]=Module::getInstance($module)->getSitemapHTML();
				}
			}
		}
		return $_arr;
	}
	
}
?>