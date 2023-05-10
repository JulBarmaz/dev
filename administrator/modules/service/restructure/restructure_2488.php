<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class Restructure_2488{
	public static function proceed(&$messages, &$errors) {
		// Let's make something with database
		$sql_arr = array();
		$sql_arr[] = "CREATE TABLE #__auth_providers (sn_id INT(11) auto_increment NOT NULL, sn_name varchar(30) DEFAULT '' NOT NULL, sn_key varchar(255) DEFAULT '' NOT NULL, sn_secret varchar(255) DEFAULT '' NOT NULL, sn_enabled TINYINT(1) DEFAULT 1 NOT NULL, sn_deleted TINYINT(1) DEFAULT 0 NOT NULL, CONSTRAINT c_sn_providers_PK PRIMARY KEY (sn_id), CONSTRAINT c_sn_providers_UN UNIQUE KEY (sn_name) ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci".Database::getInstance()->getDelimiter();
		$sql_arr[] = "INSERT INTO #__auth_providers (sn_name, sn_enabled) VALUES ('twitter', 0)".Database::getInstance()->getDelimiter();
		$sql_arr[] = "INSERT INTO #__auth_providers (sn_name, sn_enabled) VALUES ('facebook', 0)".Database::getInstance()->getDelimiter();
		$sql_arr[] = "INSERT INTO #__auth_providers (sn_name, sn_enabled) VALUES ('vkontakte', 0)".Database::getInstance()->getDelimiter();
		$sql_arr[] = "INSERT INTO #__auth_providers (sn_name, sn_enabled) VALUES ('yandex', 0)".Database::getInstance()->getDelimiter();
		$sql_arr[] = "ALTER TABLE #__auth_providers ADD sn_ordering INT(11) DEFAULT 0 NOT NULL".Database::getInstance()->getDelimiter();
		$sql_arr[] = "ALTER TABLE #__auth_providers CHANGE sn_ordering sn_ordering INT(11) DEFAULT 0 NOT NULL AFTER sn_secret".Database::getInstance()->getDelimiter();
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