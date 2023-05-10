<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class Restructure_1900{
	public static function proceed(&$messages, &$errors) {
		// Let's make something with database
		$sql="ALTER TABLE `#__measure` CHANGE `meas_id` `meas_id` INT(11) NOT NULL AUTO_INCREMENT, CHANGE `meas_code` `meas_code` VARCHAR(10) CHARSET utf8 COLLATE utf8_general_ci DEFAULT '' NOT NULL, CHANGE `meas_short_name` `meas_short_name` VARCHAR(100) CHARSET utf8 COLLATE utf8_general_ci DEFAULT '' NOT NULL".Database::getInstance()->getDelimiter();
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