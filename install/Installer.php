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

defined('_BARMAZ_VALID') or die("Access denied");

class Installer extends BaseObject {

	//---------- Singleton implementation ------------
	private static $_instance = null;
	private $_delimiter=null;
	
	private static $_ver_revision			= 0;
	private static $_ver_build				= 0;
	private static $_ver_build_date			= "";
	
	public static function createInstance() {
		if (self::$_instance == null) {
			self::$_instance = new self();
		}
	}

	public static function getInstance() {
		self::createInstance();
		return self::$_instance;
	}
	//------------------------------------------------

	private $_existedCfg = "";

	private function __construct() {
		$this->initObj();
		$this->initVersion();
		$this->initializeRegistry();
		$this->prepare();
		
	}

	private function initVersion(){
		// Шаблоны переменных заменяются при создании дистрибутива
		self::$_ver_revision=intval("135");
		self::$_ver_build=intval("135");
		self::$_ver_build_date="2023-05-10 14:11:49";
	}
	
	private function initializeRegistry() {
		$returnUrl	= Request::get('return_url','');
		if ($returnUrl != '') $returnUrl = base64_decode($returnUrl);
		else $returnUrl = 'index.php';
		$step = Request::getInt('step',0);
		$this->set('returnUrl',$returnUrl,true);
		$this->set('step',$step,true);
	}

	private function prepare() {
		//
	}

	private function initDB() {
		DatabaseConfig::$dbHost = Request::get('dbHost','');
		DatabaseConfig::$dbPort = Request::get('dbPort','');
		DatabaseConfig::$dbUser = Request::get('dbUser','');
		DatabaseConfig::$dbPassword = Request::get('dbPassword','');
		DatabaseConfig::$dbName = Request::get('dbName','');
		DatabaseConfig::$dbPrefix = Request::get('dbPrefix','');
		

		$db = Database::getInstance(false);
		
		if ($db->isConnected() == false) {
			Util::redirect("install.php?step=2&fail=1");
		}
		$this->_delimiter=$db->getDelimiter();
		return $db;
	}

	private function populateDatabase($db,$sqlfile,&$err_array) {
		if(!($buffer = file_get_contents($sqlfile))) return false; 
//		$queries = preg_split ("/[".self::$query_delimiter."]+/", $buffer);
		$queries = preg_split ("/".$this->_delimiter."/", $buffer);
		foreach ($queries as $query) {
			$query = trim($query);
			if ($query != '' && $query[0] != '#') {
				$db->setQuery($query);
				if (!$db->query()) {
					$err_array[]="<p class=\"sql_error\">"."SQL Error :".$db->getLastError()."</p>".$db->getQuery();
					return false;
				}
			}
		}
		return true;
	}

	private function getLicense() {
		$licPath = PATH_FRONT.'administrator'.DS."modules".DS.'help'.DS.'views'.DS.'template'.DS.'default'.DS.'license.php';
		if(file_exists($licPath)) require_once $licPath;
	}

	private function writeConfigToDatabase($db) {
		$sql="INSERT INTO #__config VALUES ('site','siteDomain','".Request::get('siteDomain','')."')";
		$db->setQuery($sql); if(!$db->query()) return false;
		$sql="INSERT INTO #__config VALUES ('site','sitePort','".Request::getInt('sitePort','')."')";
		$db->setQuery($sql); if(!$db->query()) return false;
		$sql="INSERT INTO #__config VALUES ('site','siteSSLPort','".Request::getInt('siteSSLPort','')."')";
		$db->setQuery($sql); if(!$db->query()) return false;
		$sql="INSERT INTO #__config VALUES ('site','metaTitle','".stripcslashes(Request::getSafe('siteTitle',''))."')";
		$db->setQuery($sql); if(!$db->query()) return false;
		$sql="INSERT INTO #__config VALUES ('site','siteTemplate','".Request::getSafe('siteTemplate','')."')";
		$db->setQuery($sql); if(!$db->query()) return false;
		$sql="INSERT INTO #__config VALUES ('admin','adminTemplate','".Request::getSafe('adminTemplate','space')."')";
		$db->setQuery($sql); if(!$db->query()) return false;		
		$sql="INSERT INTO #__config VALUES ('site','debugMode',0')";
		$db->setQuery($sql); if(!$db->query()) return false;
		
		return true;
	}
	private function writeConfig() {
		// database.php
		$siteCfgPath				= PATH_CONFIG.'site.php';
		$databaseCfgPath		= PATH_CONFIG.'database.php';
		$buffer = '<?php defined("_BARMAZ_VALID") or die("Access denied");
class DatabaseConfig {
	// Database configuration
	public static	'.'$'.'dbHost		= "'.DatabaseConfig::$dbHost.'";
	public static	'.'$'.'dbPort		= "'.DatabaseConfig::$dbPort.'";
	public static	'.'$'.'dbName		= "'.DatabaseConfig::$dbName.'";
	public static	'.'$'.'dbUser		= "'.DatabaseConfig::$dbUser.'";
	public static	'.'$'.'dbPassword	= "'.DatabaseConfig::$dbPassword.'";
	public static	'.'$'.'dbPrefix		= "'.DatabaseConfig::$dbPrefix.'";
	public static	'.'$'.'dbSecret		= "'.DatabaseConfig::$dbSecret.'";
	}
?>';
		$f = fopen($databaseCfgPath,"wt");
		if ($f) { fwrite($f,$buffer); fclose($f);	} else return false;

		// site.php
		$buffer = file_get_contents($siteCfgPath);
		$buffer = str_replace('###SITE_DOMAIN###',Request::get('siteDomain',''),$buffer);
		$buffer = str_replace('###SITE_PORT###',Request::getInt('sitePort',''),$buffer);
		$buffer = str_replace('###SITE_SSL_PORT###',Request::getInt('siteSSLPort',''),$buffer);
		$buffer = str_replace('###SITE_TITLE###',stripcslashes(Request::getSafe('siteTitle','')),$buffer);
		$buffer = str_replace('###SITE_TEMPLATE###',Request::getSafe('siteTemplate','html5'),$buffer);
		$f = fopen($siteCfgPath,"wt");
		if ($f) { fwrite($f,$buffer); fclose($f);	} else return false;
		return true;
	}
	/*
	public function cleanUpUserfiles(){
		$result = false;
		$userdata = Files::getFolders(BARMAZ_UF_PATH, array(".", ".."));
		if(is_array($userdata) && count($userdata)){
			foreach($userdata as $uf_key=>$uf_folder){
				$current_folder=BARMAZ_UF_PATH.$uf_folder["filename"].DS;
				$result=Files::removeFolder($current_folder, 1);
				if(!$result) return $result;
			}
		}
		return $result;
	}
	*/
	
	/**
	 * состав дистрибутива при установке
	 */
	public function getDistributionKit($echoon=false)
	{
	    $html='';
	  // считываем описание сборки
	    $kit_file = PATH_CONFIG.DS.'kit_barmas.json';
	  if(file_exists($kit_file))
	  // описание  
	  {
		$kit_data=file_get_contents($kit_file ,true);
		$formdata=json_decode($kit_data);
		//Util::showArray($formdata);
		// состав ядра - информационно - версия - данные таблиц ставим
		$html.='<div class="kit-table">';
			$html.='<div class="table-row">';
				$html.='<div class="kit-cell">'.Text::_("Name of complect").'</div>';
				$html.='<div class="kit-cell kit-2">'.$formdata->name."(v.)".$formdata->core->version.'</div>';		
			$html.='</div>';
			$html.='<div class="table-row">';
				$html.='<div class="kit-cell">'.Text::_("Set all demo data").'</div>';
				$html.='<div class="kit-cell kit-2"><input type="checkbox" checked="checked" value="1" name="installAllDemo" onchange="toggleDemoData(this);" /></div>';
			$html.='</div>';

		// модули относящиеся к ядру всегда устанавливаются вместе с данными
		// остальные по желанию пользователя
		// по сути у нас тут база данных только , еще раз
		$html.='<div class="table-row">';
			$html.='<div class="kit-cell table-head kit-3">'.Text::_("Core elements").'</div>';
		$html.='</div>';

		$html.='<div class="table-row table-head">';
			$html.='<div class="kit-cell">'.Text::_("Modules name").'</div>';
			$html.='<div class="kit-cell">'.Text::_("Set module").'</div>';
			$html.='<div class="kit-cell">'.Text::_("Set demo data modules").'</div>';
		$html.='</div>';
		$modcorename='';	
		foreach($formdata->core->modules as $modname){
		$modcorename.='<div class="table-row">';
			$modcorename.='<div class="kit-cell">'.Text::_($modname);
			$modcorename.='<input type="hidden" name="mod_inst[]" value="'.$modname.'" id="i_'.$modname.'" />';
			$modcorename.='</div>';
			$modcorename.='<div class="kit-cell">'.Text::_("Setted default").'</div>';
			$modcorename.='<div class="kit-cell">'.Text::_("Setted").'</div>';
		$modcorename.='</div>';
		}
		$html.=$modcorename;
		$html.='<div class="table-row">';
		$html.='<div class="kit-cell table-head kit-3">'.Text::_("Available modules of complect").'</div>';
	  $html.='</div>';

		$modaddname='';	
		foreach($formdata->modules as $modname){
			$modaddname.='<div class="table-row">';
			$modaddname.='<div class="kit-cell">'.Text::_($modname).'</div>';
			$modaddname.='<div class="kit-cell"><input type="checkbox" checked="checked" value="'.$modname.'" id="i_'.$modname.'" name="mod_inst[]" onchange="toggleDemoDataMod(this);"/></div>';
			$modaddname.='<div class="kit-cell"><input type="checkbox" checked="checked" value="'.$modname.'" id="id_'.$modname.'" name="mod_inst_demo[]" /></div>';
			$modaddname.='</div>';
		}
		$html.=$modaddname;

		

		// плагины относим к ядру
		
		// состав модулей - название версия установить(взведено), демо данные
		// перечень виджетов установить(взведено) ( возможно стоит тут зависимость от модулей учесть)
		 
		
		
		$html.='</div>';

	  }
	  if($echoon) echo $html; else return $html;
	    
	}
	
	public function render() {
		$step = $this->get('step');
		ob_start();
		include_once PATH_INSTALL.'wizard'.DS.'step'.$step.'.php';
		$stepHTML = ob_get_contents();
		ob_end_clean();

		include_once PATH_INSTALL.'template.php';
	}
	
	public static function getVersionBuild() {
		return self::$_ver_build;
	}
	
	public static function getVersionBuildDate() {
		return self::$_ver_build_date;
	}
	
	public static function getVersionRevision() {
		return self::$_ver_revision;
	}
}

?>