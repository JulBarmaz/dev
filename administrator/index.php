<?php
//BARMAZ_COPYRIGHT_TEMPLATE

define('_BARMAZ_VALID',1);
define('DS',DIRECTORY_SEPARATOR);
define("CR_LF", chr(13).chr(10));
define('_ADMIN_MODE'	,	1);

$pathSite = realpath(dirname(__FILE__)).DS;
$pathFront = realpath(dirname(__FILE__).DS.'..').DS;
define('PATH_SITE'			,	$pathSite);
define('PATH_FRONT'			,	$pathFront);

define('PATH_FRONT_MODULES'	,	PATH_FRONT.'modules'.DS);
define('PATH_FRONT_WIDGETS'	,	PATH_FRONT.'widgets'.DS);
define('PATH_FRONT_TEMPLATES',	PATH_FRONT.'templates'.DS);

define('PATH_CACHE'			,	PATH_FRONT.'cache'.DS);
define('PATH_CONFIG'		,	PATH_FRONT.'config'.DS);
define('PATH_CSS'				,	PATH_FRONT.'css'.DS);
define('LINK_CSS'				,	'/css');
define('PATH_IMAGES'		,	PATH_FRONT.'images'.DS);
define('PATH_INCLUDES'	,	PATH_FRONT.'includes'.DS);
define('PATH_JS'				,	PATH_FRONT.'js'.DS);
define('LINK_JS'				,	'/js');
define('PATH_LOGS'	,	PATH_FRONT.'logs'.DS);
define('PATH_LANGUAGE'	,	PATH_FRONT.'language'.DS);
define('PATH_MODULES'		,	PATH_SITE.'modules'.DS);
define('PATH_PACKAGES'	,	PATH_FRONT.'packages'.DS);
define('PATH_PLUGINS'		,	PATH_FRONT.'plugins'.DS);
define('PATH_TEMPLATES'	,	PATH_SITE.'templates'.DS);
define('LINK_TEMPLATES'	,	'templates');
define('PATH_TMP'				,	PATH_FRONT.'tmp'.DS);
define('PATH_WIDGETS'		,	PATH_SITE.'widgets'.DS);

if (!file_exists(PATH_CONFIG.'database.php')) die("Not installed !!!");
else include_once PATH_PACKAGES.'startup.php';
?>