<?php
//BARMAZ_COPYRIGHT_TEMPLATE

define('_BARMAZ_EXCHANGE',1);

/**************************************************************************************/
/*
log_exchange1c($_REQUEST, "_REQUEST");
// log_exchange1c($_COOKIE, "_COOKIE");
// log_exchange1c($_SESSION, "_SESSION");
function log_exchange1c($message, $title) {
	$pathFront = realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..').DIRECTORY_SEPARATOR;
	$f=fopen($pathFront."tmp".DIRECTORY_SEPARATOR."exchange1c_request.log","a");
	if(is_array($message) || is_object($message)) $message=print_r($message, true);
	$message = chr(13).chr(10).str_repeat(">", 20)." ".$title." start ".str_repeat(">", 20).chr(13).chr(10).$message.chr(13).chr(10).str_repeat("<", 20)." ".$title."  end  ".str_repeat("<", 20).chr(13).chr(10);
	fwrite($f, $message);
	fclose($f);
}
*/
/**************************************************************************************/
if (array_key_exists("mode", $_REQUEST) && array_key_exists("type", $_REQUEST)){
	$_REQUEST["notmpl"]="1";
	$_REQUEST["module"]="catalog";
	$_REQUEST["task"]="autoexchange1c";
	require_once 'index.php';
}
/**************************************************************************************/
?>