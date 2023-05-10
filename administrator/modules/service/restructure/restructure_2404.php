<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class Restructure_2404{
	public static function proceed(&$messages, &$errors) {
		// Let's make something with database
		$sql_arr = array();
		$sql_arr[] = "ALTER TABLE `#__menus` ADD mi_type integer(11) DEFAULT 0 NOT NULL".Database::getInstance()->getDelimiter();
		$sql_arr[] = "ALTER TABLE `#__menus` CHANGE mi_type mi_type integer(11) DEFAULT 0 NOT NULL AFTER mi_name".Database::getInstance()->getDelimiter();
		$sql_arr[] = "ALTER TABLE `#__menus` ADD mi_controller VARCHAR(100) DEFAULT '' NOT NULL".Database::getInstance()->getDelimiter();
		$sql_arr[] = "ALTER TABLE `#__menus` CHANGE mi_controller mi_controller VARCHAR(100) DEFAULT '' NOT NULL AFTER mi_psid".Database::getInstance()->getDelimiter();
		$sql_arr[] = "ALTER TABLE `#__menus` ADD mi_task VARCHAR(100) DEFAULT '' NOT NULL".Database::getInstance()->getDelimiter();
		$sql_arr[] = "ALTER TABLE `#__menus` CHANGE mi_task mi_task VARCHAR(100) DEFAULT '' NOT NULL AFTER mi_controller".Database::getInstance()->getDelimiter();
		$sql_arr[] = "ALTER TABLE `#__menus` ADD mi_canonical_id integer(11) DEFAULT 0 NOT NULL".Database::getInstance()->getDelimiter();
		$sql_arr[] = "ALTER TABLE `#__menus` CHANGE mi_canonical_id mi_canonical_id integer(11) DEFAULT 0 NOT NULL AFTER mi_link".Database::getInstance()->getDelimiter();
		$sql_arr[] = "ALTER TABLE `#__menus` MODIFY COLUMN mi_module varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' NOT NULL".Database::getInstance()->getDelimiter();
		$sql_arr[] = "ALTER TABLE `#__menus` MODIFY COLUMN mi_view varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' NOT NULL".Database::getInstance()->getDelimiter();
		$sql_arr[] = "ALTER TABLE `#__menus` MODIFY COLUMN mi_layout varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' NOT NULL".Database::getInstance()->getDelimiter();
		$sql_arr[] = "ALTER TABLE `#__menus` MODIFY COLUMN mi_alias varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' NOT NULL".Database::getInstance()->getDelimiter();
		$sql_arr[] = "UPDATE `#__menus` SET mi_type=1 WHERE mi_link <> ''".Database::getInstance()->getDelimiter();

		foreach ($sql_arr as $key=>$sql){
			Database::getInstance()->setQuery($sql);
			if(!Database::getInstance()->query()){
				// Returning error if failed
				$errors[]=Text::_("Error applying restructure").": ".__CLASS__;
				return false;
			}
		}
		
		// Returning message if successed
		$messages[]=__CLASS__;
		return true;
	}
}
?>