<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");


/**
 * For getting number of restructure run:
 * gulp last_version
 * then add one to the number of "Building revision"
 *
 */
class Restructure_2573{
	public static function proceed(&$messages, &$errors) {
		// Let's make something with database
		$sql_arr = array();
		$sql_arr[] = "ALTER TABLE #__goods_dts MODIFY COLUMN dt_logo varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' NOT NULL".Database::getInstance()->getDelimiter();
		$sql_arr[] = "ALTER TABLE #__goods_pts MODIFY COLUMN pt_logo varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' NOT NULL".Database::getInstance()->getDelimiter();
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