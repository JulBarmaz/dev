<?php
//BARMAZ_COPYRIGHT_TEMPLATE
defined('_BARMAZ_VALID') or die("Access denied");

Class DeveloperConfig {
	private static $_protocol				= "";
	private static $_current_protocol		= "";
	private static $_uri 					= "/";
	
	public static	$siteID		= 1;
	public static	$settings = array(
		1=>array('template' => 'html5',		'db' => 'BARMAZ_base'),
		2=>array('template' => 'html5',		'db' => 'BARMAZ_test')
	);
	private static function setProtocol() {
		self::$_current_protocol=Util::getCurrentProtocol();
		switch(seoConfig::$alwaysAbsoluteLinks){
			case 1: // Without protocol
				self::$_protocol="//";
				break;
			case 2: // With current protocol
				self::$_protocol=self::$_current_protocol."://";
				break;
			default:
				self::$_protocol="";
				break;
		}
	}
	public static function getURI($force_fronte=0, $absolute_link=0, $force_protocol=0) {
		if($absolute_link || seoConfig::$alwaysAbsoluteLinks){
			switch($force_protocol){
				case 1: // Without protocol
					$_protocol="//";
					break;
				case 2: // With current protocol
					$_protocol=self::$_current_protocol."://";
					break;
				case 3:
					$_protocol="http://";
					break;
				case 4:
					$_protocol="https://";
					break;
				default:
					$_protocol=self::$_protocol;
					break;
			}
			if ($force_fronte) return $_protocol.self::$_uri;
			elseif(defined('_ADMIN_MODE')) return $_protocol.self::$_uri."administrator/";
			else return $_protocol.self::$_uri;
			
		} else {
			if ($force_fronte) return "/";
			elseif(defined('_ADMIN_MODE')) return "/administrator/";
			else return "/";
		}
	}
	public static function initTemplate(){
		define('_BARMAZ_DEVELOPER', 1);
//		define('_BARMAZ_TRANSLATE', 1);
		define('_BARMAZ_DEVELOPER_EXCHANGE1C', 1);
		define('_BARMAZ_DEVELOPER', 1);
		
		self::setProtocol();
		self::$_uri=siteConfig::$siteDomain.(siteConfig::$sitePort && siteConfig::$sitePort != 80 ? ":".siteConfig::$sitePort: "" )."/";
		if (siteConfig::$debugMode) {
			ini_set('display_errors',1);		// Show errors
			if (version_compare(phpversion(), '5.3.0', '<') == true) error_reporting(E_ALL|E_STRICT);
			else error_reporting(E_ALL|E_STRICT|E_DEPRECATED);					
		}
		siteConfig::$siteTemplate=DeveloperConfig::$settings[DeveloperConfig::$siteID]['template'];
		DEFINE('BARMAZ_UF',self::getURI(1).'userfiles/'.siteConfig::$siteTemplate);
		DEFINE('BARMAZ_UF_PATH',PATH_FRONT.'userfiles'.DS.SiteConfig::$siteTemplate.DS);
	}
}
DatabaseConfig::$dbName=DeveloperConfig::$settings[DeveloperConfig::$siteID]['db'];
?>