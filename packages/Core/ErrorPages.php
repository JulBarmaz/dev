<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

final class ErrorPages {
	public static $_err_codes_4xx=array(403=>"Forbidden", 404=>"Not Found");
	
	public static function renderDump(){
		if (siteConfig::$debugMode>100 || (siteConfig::$debugMode && User::getInstance()->isAdmin())) return Debugger::getInstance()->dump();
	}
	
	private static function getCustomTemplatePath($filename=""){
		$custom_path=PATH_TEMPLATES.Portal::getInstance()->getTemplate().DS.$filename;
		//die($custom_path." in ");
		if(file_exists($custom_path) && is_file($custom_path)) {
			return $custom_path;
		} else {
			$custom_path=PATH_SITE.$filename;
			if(file_exists($custom_path) && is_file($custom_path)) {
				return $custom_path;
			} else {
				return false;
			}
		}
	}
	
	public static function renderRedirect($url, $message, $code){
		//die($url);
		Session::setVar('BARMAZ_message', "");
		Session::setVar('BARMAZ_message_code', false);
		
		$custom_path = false;// пока тут почему то редирект self::getCustomTemplatePath("redirect_page.php");
		
		if($custom_path) {
			require_once($custom_path);
			exit();
		} else {
			$_stylesheets=array("/css/debug.css","/css/errors.css");
			$errorHTML = HTMLControls::renderStaticHeader(Text::_("Debug mode"),"","",$_stylesheets);
			$errorHTML .= "<div id=\"error_header\"><div id=\"logo\" class=\"redirect\"></div><div id=\"headLine\"></div></div>";
			$errorHTML .= "<div class=\"fatal-error-text\">".Text::_("Redirect")." ".$code.": ".$message."</div>";
			$errorHTML .= "<div class=\"fatal-error-link\"><a class=\"go_back\" href=\"".($url ? $url : "/")."\">".($url ? Text::_('Continue') : Text::_('Go main page'))."</a></div>";
			$errorHTML .= self::renderDump();
			$errorHTML .= HTMLControls::renderStaticFooter();
			exit($errorHTML);
		}
	}
	
	public static function render404($url, $message=""){
		Session::setVar('BARMAZ_message', "");
		Session::setVar('BARMAZ_message_code', false);
		$custom_path = self::getCustomTemplatePath("503.php");
		if($custom_path) {
			require_once($custom_path);
			exit();
		} else {
			$_stylesheets=array("/css/debug.css","/css/errors.css");
			$errorHTML = HTMLControls::renderStaticHeader(Text::_("Page not found"),"","",$_stylesheets);
			$errorHTML .= "<div id=\"error_header\"><div id=\"logo\" class=\"logo404\"></div><div id=\"headLine\"></div></div>";
			$errorHTML .= "<div class=\"fatal-error-text\">".Text::_("Error 404").": ".(siteConfig::$debugMode ? $message : Text::_("Page not found"))."</div>";
			$errorHTML .= "<div class=\"fatal-error-link\"><a class=\"go_back\" href=\"".($url ? $url : "/")."\">".($url ? Text::_('Continue') : Text::_('Go main page'))."</a></div>";
			$errorHTML .= self::renderDump();
			$errorHTML .= HTMLControls::renderStaticFooter();
			exit($errorHTML);
		}
	}
	
	public static function render503($message=""){
		Session::setVar('BARMAZ_message', "");
		Session::setVar('BARMAZ_message_code', false);
		$custom_path = self::getCustomTemplatePath("404.php");
		if($custom_path) {
			require_once($custom_path);
			exit();
		} else {
			Session::setVar('BARMAZ_message', "");
			Session::setVar('BARMAZ_message_code', false);
			$_stylesheets=array("/css/debug.css","/css/errors.css");
			$errorHTML = HTMLControls::renderStaticHeader(Text::_("Service Temporarily Unavailable"),"","",$_stylesheets);
			$errorHTML .= "<div id=\"error_header\"><div id=\"logo\" class=\"logo503\"></div><div id=\"headLine\"></div></div>";
			$errorHTML .= "<div class=\"fatal-error-text\">".Text::_("Service Temporarily Unavailable").(siteConfig::$debugMode ? ": ".$message : "")."</div>";
			$errorHTML .= self::renderDump();
			$errorHTML .= HTMLControls::renderStaticFooter();
			exit($errorHTML);
		}
	}
}
?>