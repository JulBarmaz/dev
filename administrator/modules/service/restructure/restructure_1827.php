<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class Restructure_1827{
	public static function proceed(&$messages, &$errors) {
		// Let's make something with files
		// For example BARMAZ.accordion.js
		$old_file=PATH_JS."BARMAZ.accordion.js";
		if(is_file($old_file)){
			if (!Files::delete($old_file, 1)) {
				// Returning error if failed
				$errors[]=Text::_("Error applying restructure").": ".__CLASS__." [Old accordion]";
				return false;
			}
		}
		// For example BARMAZ.accordion-unpacked.js
		$old_file=PATH_JS."BARMAZ.accordion-unpacked.js";
		if(is_file($old_file)){
			if (!Files::delete($old_file, 1)) {
				// Returning error if failed
				$errors[]=Text::_("Error applying restructure").": ".__CLASS__." [Old accordion]";
				return false;
			}
		}
		
		// Returning message if successed
		$messages[]=__CLASS__;
		return true;
	}
}
?>