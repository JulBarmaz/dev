<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class Restructure_2335{
	public static function proceed(&$messages, &$errors) {
		// Let's make something with database
		$sql="UPDATE `#__widgets_active` SET `aw_config`=REPLACE(`aw_config`, '{;}Collapse_menu{=}', '{;}Render_type{=}') WHERE `aw_name`='menu'".Database::getInstance()->getDelimiter();
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