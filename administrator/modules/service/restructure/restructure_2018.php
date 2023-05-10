<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class Restructure_2018{
	public static function proceed(&$messages, &$errors) {
		// Let's make something with database
		$sql="ALTER TABLE `#__galleries` ADD COLUMN `g_show_parent_descr` TINYINT(1) DEFAULT 0 NOT NULL AFTER `g_images_by_row`".Database::getInstance()->getDelimiter();
		Database::getInstance()->setQuery($sql);
		if(!Database::getInstance()->query()){
			// Returning error if failed
			$errors[]=Text::_("Error applying restructure").": ".__CLASS__;
			return false;
		}
		
		$sql="UPDATE `#__galleries` SET `g_show_parent_descr`=".intval(galleryConfig::$showParentDescr).Database::getInstance()->getDelimiter();
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