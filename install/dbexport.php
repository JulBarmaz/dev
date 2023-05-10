<?php
//  BARMAZ erp system
//  Copyright (c) BARMAZ Group
//  Web: https://BARMAZ.ru/
//  Commercial license https://BARMAZ.ru/article/litsenzionnoe-soglashenie.html
//  THE SOFTWARE IS PROVIDED 'AS IS', WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
//  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
//  FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
//  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
//  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
//  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
//  Revision: 135 (2023-05-10 14:11:23)
// 


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru-ru" lang="ru-ru">
<head>
<meta http-equiv="PRAGMA" content="NO-CACHE">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="generator" content="Barmaz erp" />
<title>Database export</title>
</head>
<body>
<?php
define('_BARMAZ_VALID',1);
define('DS',DIRECTORY_SEPARATOR);
// Path constants
$pathSite = realpath(dirname(__FILE__).DS."..").DS;
defined('DEF_CP') or  define('DEF_CP',"UTF-8");
define('PATH_SITE'			,	$pathSite);
define('PATH_FRONT'			,	$pathSite);
define('PATH_CONFIG'		,	PATH_FRONT.'config'.DS);
define('PATH_INCLUDES'	,	PATH_FRONT.'includes'.DS);
define('PATH_PACKAGES'	,	PATH_FRONT.'packages'.DS);
define('PATH_LANGUAGE'	,	PATH_FRONT.'language'.DS);
define('PATH_MODULES'		,	PATH_FRONT.'modules'.DS);
define('PATH_WIDGETS'		,	PATH_FRONT.'widgets'.DS);
define('PATH_TEMPLATES'	,	PATH_FRONT.'templates'.DS);
define('LINK_TEMPLATES'	,	'templates');
define('PATH_JS'				,	PATH_FRONT.'js'.DS);
define('LINK_JS'				,	'js');
define('PATH_CSS'				,	PATH_FRONT.'css'.DS);
define('LINK_CSS'				,	'css');
define('PATH_IMAGES'		,	PATH_FRONT.'images'.DS);
define('PATH_TMP'				,	PATH_FRONT.'tmp'.DS);
define('PATH_CACHE'			,	PATH_FRONT.'cache'.DS);
define('PATH_PLUGINS'		,	PATH_FRONT.'plugins'.DS);
//------------------------------------------------
require_once PATH_CONFIG.DS.'database.php'; // Database configuration
require_once PATH_CONFIG.DS.'site.php'; // Site configuration
require_once PATH_CONFIG.DS.'seo.php'; // Site configuration
// Developer mode
if (isset(siteConfig::$developerMode)&&(siteConfig::$developerMode)) require_once PATH_CONFIG.DS.'developer.php';
include_once PATH_PACKAGES.DS.'loader.php'; // Include loader
require_once PATH_CONFIG.DS.'packages.php'; // CMS packages configuration
require_once PATH_CONFIG.DS.'catalog.php'; // Catalog configuration
//----------------------------- STARTUP ------------------------------------
// Set PHP interpreter parameters
ini_set('display_errors',1);		// Show errors
error_reporting(E_ALL);				// Report all errors
ini_set('memory_limit','128M');		// Limit memory to 64 megabytes

Debugger::createInstance(); // Initialize system debugger
Util::setupLocale(); // Setup locale (core config contains default language parameter)
Util::checkServerSettings(); // Check server settings

Debugger::getInstance()->dumpSystemInfo();

$exporter = Exporter::getInstance();

$task = Request::get('task','list');
$tlist = $exporter->getTablesList();
$settings = array();
$export=Request::getSafe('export',0);
$upload=Request::getSafe('upload',0);
if ($task == 'export' && $export) {
	$query_delimiter=Database::getInstance()->getDelimiter();
	$expName = Request::getSafe('expname',strval(time()));
	$drops = Request::get('drops','disabled');
	$expFileSys = PATH_FRONT.'install'.DS.'sql'.DS.'system.sql';
	$expPathData = PATH_FRONT.'install'.DS.'sql'.DS.'data'.DS;
	$expPathDemo = PATH_FRONT.'install'.DS.'sql'.DS.'demo'.DS;

	$data_files=Files::getFiles($expPathData,array(),false);
	if ($data_files){
		foreach($data_files as $file){
			Files::delete($expPathData.$file["filename"],true);
		}
	}
	$demo_files=Files::getFiles($expPathDemo,array(),false);
	if ($demo_files){
		foreach($demo_files as $file){
			Files::delete($expPathDemo.$file["filename"],true);
		}
	}

	$fpsys = fopen($expFileSys,"wt");

	$setnames = "SET NAMES 'UTF8'".$query_delimiter."\n";
	fputs($fpsys, $setnames, mb_strlen($setnames,DEF_CP));

	$cookieText = '';
	foreach ($tlist as $t) {
		$expMode = Request::getInt($t.'_mode',0);
		$settings[$t.'_mode']=$expMode;
		$demoData = true;
		$cookieText .= $expMode;
		switch ($expMode) {
			case 1:
				$expStruct = true;
				$expData = false;
				$demoData = false;
				break;

			case 2:
				$expStruct = true;
				$expData = true;
				$demoData = false;
				break;

			case 3:
				$expStruct = true;
				$expData = false;
				$demoData = true;
				break;

			case 0:
			default:
				$demoData = false;
				$expStruct = false;
				$expData = false;
				break;
		}
		if ($expStruct) {
			// Export structure
			$createTable = "";
			$createTable = "DROP TABLE IF EXISTS `$t`".$query_delimiter."\n";
			$createTable = str_replace('DROP TABLE IF EXISTS `'.Database::getInstance()->getPrefix(),'DROP TABLE IF EXISTS `#__',$createTable);
			$createTable .= $exporter->getCreateTable($t,$query_delimiter)."\n\n";
			fwrite ($fpsys, $createTable);
		}
		if ($expData) {
			// Export data
			$inserts = $exporter->getInserts($t, $query_delimiter);
			if($inserts) {
				$expFileData = $expPathData.$t.'.sql';
				$fpdemo = fopen($expFileData,"wt");
				fwrite ($fpdemo, $inserts);
				fclose($fpdemo);
				echo "Exported data ".$expFileData."<br />";
				} else {
				echo "Table ".$t." is empty. Skipped.<br />";
			}
		} elseif ($demoData) {
			// Export demo
			$inserts = $exporter->getInserts($t, $query_delimiter);
			if($inserts) {
				$inserts = "\n".$inserts."\n\n";
				$expFileDemo = $expPathDemo.$t.'.sql';
				$fpdemo = fopen($expFileDemo,"wt");
				fwrite ($fpdemo, $inserts);
				fclose($fpdemo);
				echo "Exported demo data".$expFileDemo."<br />";
			} else {
				echo "Table ".$t." is empty. Skipped.<br />";
			}
		}
	}
	fclose($fpsys);
	echo "<br />Exported ".$expFileSys."<br />";
	$fname=PATH_TMP."settings_".$expName.".txt";
	// $flink="/tmp/settings_".$expName.".txt";
	$fpsettings = fopen($fname,"wt");
	$str_settings=json_encode($settings);
	fputs($fpsettings, $str_settings, mb_strlen($str_settings));
	fclose($fpsettings);
	// echo "<p><a target=\"_blank\" href=\"".$flink."\">Download file with settings</a></p>";
	echo "<h2 style=\"color:".(Database::getInstance()->getDBName()=="BARMAZ_base" ? "blue" : "red")."\">".strtoupper(Database::getInstance()->getDBName())."</h2>";
	echo "<h4 style=\"color:blue;\">File with settings saved as ".$fname."</h4>";
	echo "<h3 style=\"color:green;\">Export complete</h3><br /><br /><br />";
} else {
	$settings=array();
	if(isset($_FILES["settings"]['tmp_name']) && $_FILES["settings"]['tmp_name']) {
		if ($buffer = file_get_contents($_FILES["settings"]['tmp_name'])) $settings=json_decode($buffer,true);
	}
?>
<form action="dbexport.php" method="post" enctype="multipart/form-data">
	<input type="hidden" name="task" value="export" />
	<table>
		<tr><th align="left">База данных:</th><th><h1 style="color:<?php echo Database::getInstance()->getDBName()=="BARMAZ_base" ? "blue" : "red" ?>"><?php echo strtoupper(Database::getInstance()->getDBName()); ?></h1></th></tr>
		<tr><th align="left">Найдено таблиц:</th><th><?php echo count($tlist); ?></th></tr>
		<tr><th align="left">Файл настроек:</th><th><input type="file" size="25" value="" name="settings"/><input type="submit" name="upload" value="Upload" /></th></tr>
		<tr><th colspan="2">&nbsp;</th></tr>
<?php
		$_arr=array("0"=>"не выгружать","1"=>"только структуру","2"=>"структуру и данные","3"=>"структуру и демоданные");
		foreach ($tlist as $t) { ?>
		<tr>
			<td><b><?php echo $t; ?></b></td>
			<td>
<?php
			$_name= $t."_mode";
			if (array_key_exists($_name, $settings)) $selection=$settings[$_name]; else $selection=1;
			echo HTMLControls::renderSelect($_name, $_name, "", "", $_arr, $selection,false);
?>
			</td>
		</tr>
<?php } ?>
	</table>
	<br />
	<input type="submit" name="export" value="Export" /></form>
<?php } ?>
</body>
</html>
