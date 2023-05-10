<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class Restructure_1911{
	public static function proceed(&$messages, &$errors) {
		// Let's make something with database
		$sql="ALTER TABLE `#__orders` ADD COLUMN `o_extcode` VARCHAR(36) DEFAULT '' NOT NULL".Database::getInstance()->getDelimiter();
		Database::getInstance()->setQuery($sql);
		if(!Database::getInstance()->query()){
			// Returning error if failed
			$errors[]=Text::_("Error applying restructure").": ".__CLASS__;
			return false;
		}
		
		$sql="ALTER TABLE `#__orders_items` ADD COLUMN `i_extcode` VARCHAR(36) DEFAULT '' NOT NULL".Database::getInstance()->getDelimiter();
		Database::getInstance()->setQuery($sql);
		if(!Database::getInstance()->query()){
			// Returning error if failed
			$errors[]=Text::_("Error applying restructure").": ".__CLASS__;
			return false;
		}
		$sql="ALTER TABLE `#__orders_items` ADD COLUMN `i_g_extcode` VARCHAR(36) DEFAULT '' NOT NULL AFTER `i_g_name`".Database::getInstance()->getDelimiter();
		Database::getInstance()->setQuery($sql);
		if(!Database::getInstance()->query()){
			// Returning error if failed
			$errors[]=Text::_("Error applying restructure").": ".__CLASS__;
			return false;
		}
		
		$sql="ALTER TABLE `#__orders_files` ADD COLUMN `f_extcode` VARCHAR(36) DEFAULT '' NOT NULL".Database::getInstance()->getDelimiter();
		Database::getInstance()->setQuery($sql);
		if(!Database::getInstance()->query()){
			// Returning error if failed
			$errors[]=Text::_("Error applying restructure").": ".__CLASS__;
			return false;
		}
		
		$sql="ALTER TABLE `#__orders_payments` ADD COLUMN `payment_extcode` VARCHAR(36) DEFAULT '' NOT NULL".Database::getInstance()->getDelimiter();
		Database::getInstance()->setQuery($sql);
		if(!Database::getInstance()->query()){
			// Returning error if failed
			$errors[]=Text::_("Error applying restructure").": ".__CLASS__;
			return false;
		}
		
		$sql="ALTER TABLE `#__orders` CHANGE `o_date` `o_date` DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL".Database::getInstance()->getDelimiter();
		Database::getInstance()->setQuery($sql);
		if(!Database::getInstance()->query()){
			// Returning error if failed
			$errors[]=Text::_("Error applying restructure").": ".__CLASS__;
			return false;
		}
		
		$sql="ALTER TABLE `#__orders_items` ADD COLUMN `i_g_type` INT(3) DEFAULT 1 NOT NULL AFTER `i_g_id`".Database::getInstance()->getDelimiter();
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