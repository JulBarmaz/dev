<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class Restructure_2251{
	public static function proceed(&$messages, &$errors) {
		// Let's make something with database
		$sql="CREATE TABLE `#__goods_favourites` ( `favourites_id` varchar(128) NOT NULL, `favourites_touch` int(11) NOT NULL, `favourites_data` text NOT NULL, PRIMARY KEY (`favourites_id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8".Database::getInstance()->getDelimiter();
		Database::getInstance()->setQuery($sql);
		if(!Database::getInstance()->query()){
			// Returning error if failed
			$errors[]=Text::_("Error applying restructure").": ".__CLASS__;
			return false;
		}
		
		$sql="CREATE TABLE `#__goods_compare` ( `compare_id` varchar(128) NOT NULL, `compare_touch` int(11) NOT NULL, `compare_data` text NOT NULL, PRIMARY KEY (`compare_id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8".Database::getInstance()->getDelimiter();
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