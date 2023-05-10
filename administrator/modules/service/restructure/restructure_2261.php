<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class Restructure_2261{
	public static function proceed(&$messages, &$errors) {
		// Let's make something with files
		// For example cladr.towns.php in conf module
		$old_file=PATH_JS."plugins".DS."system".DS."recaptcha.js";
		if(is_file($old_file)){
			if (!Files::delete($old_file, 1)) {
				// Returning error if failed
				$errors[]=Text::_("Error applying restructure").": ".__CLASS__." [Old conf metadata]";
				return false;
			}
		}
		
		// Returning message if successed
		$messages[]=__CLASS__;
		return true;
	}
}
?>