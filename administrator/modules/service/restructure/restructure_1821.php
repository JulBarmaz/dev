<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class Restructure_1821{
	public static function proceed(&$messages, &$errors) {
		// Let's make something with database
		$sql="ALTER TABLE `#__goods_options` ADD COLUMN `o_extcode` VARCHAR(36) DEFAULT '' NOT NULL".Database::getInstance()->getDelimiter(); 
		Database::getInstance()->setQuery($sql);
		if(!Database::getInstance()->query()){
			// Returning error if failed
			$errors[]=Text::_("Error applying restructure").": ".__CLASS__;
			return false;
		}
		
		// Let's make something with files
		// For example old facebook folder in redistributions
		$old_facebook_path=PATH_FRONT."redistribution".DS."facebook";
		if(is_dir($old_facebook_path)){
			if (!Files::removeFolder($old_facebook_path, 1)) {
				// Returning error if failed
				$errors[]=Text::_("Error applying restructure").": ".__CLASS__." [Facebook]";
				return false;
			}
		}
		// For example old twitter folder in redistributions
		$old_twitter_path=PATH_FRONT."redistribution".DS."twitter";
		if(is_dir($old_twitter_path)){
			if (!Files::removeFolder($old_twitter_path, 1)) {
				// Returning error if failed
				$errors[]=Text::_("Error applying restructure").": ".__CLASS__." [Twitter]";
				return false;
			}
		}
		// For example old vk folder in redistributions
		$old_vk_path=PATH_FRONT."redistribution".DS."vk";
		if(is_dir($old_vk_path)){
			if (!Files::removeFolder($old_vk_path, 1)) {
				// Returning error if failed
				$errors[]=Text::_("Error applying restructure").": ".__CLASS__." [VK]";
				return false;
			}
		}
		
		// Returning message if successed
		$messages[]=__CLASS__;
		return true;
	}
}
?>