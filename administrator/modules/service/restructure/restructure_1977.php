<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class Restructure_1977{
	public static function proceed(&$messages, &$errors) {
		// Let's make something with database
		$sql="INSERT INTO `#__config` (`cfg_section`, `cfg_key`, `cfg_value`) VALUES ('seo', 'tmplCSSBackCompatibility', '1') ON DUPLICATE KEY UPDATE `cfg_value`=1".Database::getInstance()->getDelimiter();
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