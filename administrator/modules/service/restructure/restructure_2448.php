<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class Restructure_2448{
	public static function proceed(&$messages, &$errors) {
		// Let's make something with database
		$sql_arr = array();
		$sql_arr[] = "ALTER TABLE `#__forum_sections` ADD f_thumb varchar(250) NULL".Database::getInstance()->getDelimiter();
		$sql_arr[] = "ALTER TABLE `#__forum_themes` ADD t_thumb varchar(250) NULL".Database::getInstance()->getDelimiter();
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