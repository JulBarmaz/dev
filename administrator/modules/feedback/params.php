<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class feedbackModuleParams{
	public static function _proceed(&$module){
		$module->addParam("forewordArticle", "table_select", "", false, "SELECT a_id AS fld_id, a_title AS fld_name FROM #__articles ORDER BY fld_name");
		$module->addParam("Feedback_theme", "string", Text::_('Message for site administration'), true);
		$module->addParam("Feedback_copyAdresses", "string", "");
		$module->addParam("Feedback_useCaptchaOnFeedback", "boolean", "1");
		$module->addParam("Feedback_useCaptchaOnBackcall", "boolean", "0");
	}
}
?>