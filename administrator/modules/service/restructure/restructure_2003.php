<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class Restructure_2003{
	public static function proceed(&$messages, &$errors) {
		// Let's make something with database
		$sql="UPDATE `#__acl_objects` SET `ao_name` = 'deleteCatalogPaymenttypes' WHERE `ao_name` = 'deletePaymenttypes' AND `ao_module_name`='catalog' AND `ao_is_admin`=1".Database::getInstance()->getDelimiter();
		Database::getInstance()->setQuery($sql);
		if(!Database::getInstance()->query()){
			// Returning error if failed
			$errors[]=Text::_("Error applying restructure").": ".__CLASS__;
			return false;
		}
		
		$sql="ALTER TABLE `#__galleries` CHANGE `g_layout` `g_layout` VARCHAR(100) CHARSET utf8 COLLATE utf8_general_ci NULL".Database::getInstance()->getDelimiter();
		Database::getInstance()->setQuery($sql);
		if(!Database::getInstance()->query()){
			// Returning error if failed
			$errors[]=Text::_("Error applying restructure").": ".__CLASS__;
			return false;
		}
		
		// Returning message if successed
		$messages[]=__CLASS__;
		return true;
	}
}
?>