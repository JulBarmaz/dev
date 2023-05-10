<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class Restructure_2388{
	public static function proceed(&$messages, &$errors) {
		// Let's make something with database
		$sql="ALTER TABLE `#__manufacturers` ADD COLUMN `mf_alias` VARCHAR(255) DEFAULT '' NOT NULL".Database::getInstance()->getDelimiter(); 
		Database::getInstance()->setQuery($sql);
		if(!Database::getInstance()->query()){
			// Returning error if failed
			$errors[]=Text::_("Error applying restructure").": ".__CLASS__;
			return false;
		}

		$sql="ALTER TABLE `#__vendors` ADD COLUMN `v_alias` VARCHAR(255) DEFAULT '' NOT NULL".Database::getInstance()->getDelimiter();
		Database::getInstance()->setQuery($sql);
		if(!Database::getInstance()->query()){
			// Returning error if failed
			$errors[]=Text::_("Error applying restructure").": ".__CLASS__;
			return false;
		}

		// Let's make something with files
		$old_catalogfilter_plugin_path=PATH_PLUGINS."content".DS."catalogfilter.php";
		if(is_dir($old_catalogfilter_plugin_path)){
			if (!Files::delete($old_catalogfilter_plugin_path, 1)) {
				// Returning error if failed
				$errors[]=Text::_("Error applying restructure").": ".__CLASS__." [Facebook]";
				return false;
			}
		}
		
		// Returning message if successed
		$messages[]=__CLASS__;
		return true;
	}
}
?>