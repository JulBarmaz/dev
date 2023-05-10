<?php
//BARMAZ_COPYRIGHT_TEMPLATE
defined('_BARMAZ_VALID') or  define('_BARMAZ_VALID',1);

mb_internal_encoding("UTF-8");
defined('DEF_CP') or  define('DEF_CP',"UTF-8");
defined('DS') or define('DS',DIRECTORY_SEPARATOR);
defined("CR_LF") or DEFINE("CR_LF", chr(13).chr(10));
/* Path constants */
$pathSite = realpath(dirname(__FILE__)).DS;
defined('PATH_SITE') or define('PATH_SITE'			,	$pathSite);
defined('PATH_FRONT') or define('PATH_FRONT'			,	$pathSite);
defined('PATH_CONFIG') or define('PATH_CONFIG'		,	PATH_FRONT.'config'.DS);
defined('PATH_INCLUDES') or define('PATH_INCLUDES'	,	PATH_FRONT.'includes'.DS);
defined('PATH_PACKAGES') or define('PATH_PACKAGES'	,	PATH_FRONT.'packages'.DS);
defined('PATH_LANGUAGE') or define('PATH_LANGUAGE'	,	PATH_FRONT.'language'.DS);
defined('PATH_MODULES') or define('PATH_MODULES'		,	PATH_FRONT.'modules'.DS);
defined('PATH_WIDGETS') or define('PATH_WIDGETS'		,	PATH_FRONT.'widgets'.DS);
defined('PATH_TEMPLATES') or define('PATH_TEMPLATES'	,	PATH_FRONT.'templates'.DS);
defined('PATH_ADMIN_TEMPLATES') or define('PATH_ADMIN_TEMPLATES'	,	PATH_FRONT.DS.'administrator'.DS.'templates'.DS);
defined('LINK_TEMPLATES') or define('LINK_TEMPLATES'	,	'templates');
defined('LINK_ADMIN_TEMPLATES') or define('LINK_ADMIN_TEMPLATES'	,	'adminstrator/templates');
defined('PATH_JS') or define('PATH_JS'				,	PATH_FRONT.'js'.DS);
defined('LINK_JS') or define('LINK_JS'				,	'js');
defined('PATH_CSS') or define('PATH_CSS'				,	PATH_FRONT.'css'.DS);
defined('LINK_CSS') or define('LINK_CSS'				,	'css');
defined('PATH_IMAGES') or define('PATH_IMAGES'		,	PATH_FRONT.'images'.DS);
defined('PATH_TMP') or define('PATH_TMP'				,	PATH_FRONT.DS.'tmp'.DS);
defined('PATH_CACHE') or define('PATH_CACHE'			,	PATH_FRONT.'cache'.DS);
defined('PATH_PLUGINS') or define('PATH_PLUGINS'		,	PATH_FRONT.'plugins'.DS);
defined('PATH_INSTALL') or define('PATH_INSTALL'	,	PATH_FRONT.'install'.DS);
defined('BARMAZ_UF') or define('BARMAZ_UF', '/userfiles');
defined('BARMAZ_UF_PATH') or define('BARMAZ_UF_PATH', PATH_FRONT.'userfiles'.DS);
if (file_exists(PATH_INSTALL.'install.php')) {
	include_once PATH_INSTALL.'install.php';
} else {	die("No installer found!");
}
?>