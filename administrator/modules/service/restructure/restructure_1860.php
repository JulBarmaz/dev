<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class Restructure_1860{
	public static function proceed(&$messages, &$errors) {
		// Let's make something with database
		$sql="ALTER TABLE `#__fields_list` ADD COLUMN `f_extcode` VARCHAR(36) DEFAULT '' NOT NULL".Database::getInstance()->getDelimiter();
		Database::getInstance()->setQuery($sql);
		if(!Database::getInstance()->query()){
			// Returning error if failed
			$errors[]=Text::_("Error applying restructure").": ".__CLASS__;
			return false;
		}
		$sql="ALTER TABLE `#__fields_choices` ADD COLUMN `fc_extcode` VARCHAR(36) DEFAULT '' NOT NULL".Database::getInstance()->getDelimiter();
		Database::getInstance()->setQuery($sql);
		if(!Database::getInstance()->query()){
			// Returning error if failed
			$errors[]=Text::_("Error applying restructure").": ".__CLASS__;
			return false;
		}
		$sql="ALTER TABLE `#__taxes` ADD COLUMN `t_extcode` VARCHAR(36) DEFAULT '' NOT NULL".Database::getInstance()->getDelimiter();
		Database::getInstance()->setQuery($sql);
		if(!Database::getInstance()->query()){
			// Returning error if failed
			$errors[]=Text::_("Error applying restructure").": ".__CLASS__;
			return false;
		}
		$sql="ALTER TABLE `#__measure` CHANGE `meas_short_name` `meas_short_name` VARCHAR(50) CHARSET utf8 COLLATE utf8_general_ci DEFAULT '' NOT NULL, CHANGE `meas_full_name` `meas_full_name` VARCHAR(100) CHARSET utf8 COLLATE utf8_general_ci DEFAULT '' NOT NULL, CHANGE `meas_comment` `meas_comment` VARCHAR(200) CHARSET utf8 COLLATE utf8_general_ci DEFAULT '' NOT NULL".Database::getInstance()->getDelimiter();
		Database::getInstance()->setQuery($sql);
		if(!Database::getInstance()->query()){
			// Returning error if failed
			$errors[]=Text::_("Error applying restructure").": ".__CLASS__;
			return false;
		}
		$sql="ALTER TABLE `#__metadata` CHANGE `m_field` `m_field` VARCHAR(50) CHARSET utf8 COLLATE utf8_general_ci NOT NULL".Database::getInstance()->getDelimiter();
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