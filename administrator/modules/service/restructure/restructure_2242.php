<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class Restructure_2242{
	public static function proceed(&$messages, &$errors) {
		// Let's make something with database
		$sql="INSERT INTO `#__widgets` (`w_id`,`w_name`,`w_side`) VALUES (NULL,'catalogsearch', 1)".Database::getInstance()->getDelimiter();
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