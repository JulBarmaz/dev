<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class Restructure_2249{
	public static function proceed(&$messages, &$errors) {
		// Let's make something with database
		$sql="CREATE TABLE `#__fields_groups` (`fg_id` int(11) NOT NULL AUTO_INCREMENT,`fg_name` varchar(100) NOT NULL DEFAULT '',`fg_enabled` int(1) NOT NULL DEFAULT '1',`fg_deleted` int(1) NOT NULL DEFAULT '0',`fg_comment` text NOT NULL,PRIMARY KEY (`fg_id`),UNIQUE KEY `c_fields_groups_UN` (`fg_name`)) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8".Database::getInstance()->getDelimiter();
		Database::getInstance()->setQuery($sql);
		if(!Database::getInstance()->query()){
			// Returning error if failed
			$errors[]=Text::_("Error applying restructure").": ".__CLASS__;
			return false;
		}
		
		$sql="INSERT INTO `#__fields_groups` (fg_id,fg_name,fg_comment) VALUES (1,'Common','')".Database::getInstance()->getDelimiter();
		Database::getInstance()->setQuery($sql);
		if(!Database::getInstance()->query()){
			// Returning error if failed
			$errors[]=Text::_("Error applying restructure").": ".__CLASS__;
			return false;
		}
		
		$sql="ALTER TABLE `#__fields_list` ADD f_group INT(11) DEFAULT 0 NOT NULL AFTER `f_id`".Database::getInstance()->getDelimiter();
		Database::getInstance()->setQuery($sql);
		if(!Database::getInstance()->query()){
			// Returning error if failed
			$errors[]=Text::_("Error applying restructure").": ".__CLASS__;
			return false;
		}
		
		$sql="UPDATE `#__fields_list` SET f_group=1".Database::getInstance()->getDelimiter();
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