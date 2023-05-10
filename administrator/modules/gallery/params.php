<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class galleryModuleParams{
	public static function _proceed(&$module){
		$module->addParam("images_by_row", "select", "4", false, array("2"=>"2", "3"=>"3", "4"=>"4", "6"=>"6"));
		$module->addParam("exclude_cats_from_map", "multiselect", "0", false, self::getGroups());
		$module->addParam("exclude_galleries_from_map", "multiselect", "0", false, self::getItems());
	}
	public static function getGroups() {
		$sql="SELECT gr_id AS `id`, gr_title AS `name` FROM #__gallery_groups WHERE gr_deleted=0 ORDER BY gr_ordering";
		Database::getInstance()->setQuery($sql);
		return Database::getInstance()->loadAssocList();
	}
	public static function getItems($psid=0) {
		$sql="SELECT g_id AS `id`, g_title AS `name` FROM #__galleries WHERE g_deleted=0 ORDER BY g_ordering";
		Database::getInstance()->setQuery($sql);
		return Database::getInstance()->loadAssocList();
	}
}
?>