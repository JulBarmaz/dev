<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class serviceModelupdater extends Model {
	private $_update_log = array();
	private $_url = "https://barmaz.ru/updates/";
	private $_delimiter=null;

	public function __construct($module) {
		parent::__construct($module);
		$this->_delimiter=$this->_db->getDelimiter();
		if(is_file(PATH_CONFIG."version.php")) require_once PATH_CONFIG."version.php";
		else $this->_url.=Portal::getVersionMajor()."/".Portal::getVersionMinor().(backofficeConfig::$updatesBetaChannel ? "-beta" : "")."/";
	}
	private function logOK($text){
		$this->_update_log[]=$text." - <span class=\"log_ok\">OK</span>";
	}
	private function logError($text){
		$this->_update_log[]=$text." - <span class=\"log_error\">".Text::_("Error")."</span>";
	}
	private function logWarning($text){
		$this->_update_log[]=$text." - <span class=\"log_warning\">".Text::_("Warning")."</span>";
	}
	private function logMessage($text){
		$this->_update_log[]=$text;
	}
	public function getLog(){
		return $this->_update_log;
	}
	public function getVersion(){
		return Portal::getInstance()->getVersionRevision();
		// return Portal::getInstance()->getVersionRevision().".".Portal::getInstance()->getVersionBuild();
	}
	public function checkNewVersion(){
		$url = $this->_url."distr.info";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url); // set url to post to
		curl_setopt($ch, CURLOPT_FAILONERROR, 1);
		curl_setopt($ch, CURLOPT_FRESH_CONNECT,1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);// allow redirects
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		curl_setopt($ch, CURLOPT_POST, 1); 	// set POST method
		curl_setopt($ch, CURLOPT_POSTFIELDS, ""); // add POST fields
		$result = curl_exec($ch); // run the whole process
		curl_close($ch);
		try {
			$ini=parse_ini_string($result);
		} catch (Exception $e) {
			
		}
		if (is_array($ini) && array_key_exists("Revision num", $ini) && array_key_exists("Build num", $ini)){
			$version=$ini["Revision num"];
			// $version=$ini["Revision num"].".".$ini["Build num"];
		} else return false;
		return $version;
	}
	public function processPackage(){
		$cur_update="update".time();
		$filename=PATH_TMP.DS.$cur_update.".zip";
		$_SESSION['update_folder']=$cur_update;
		if ($this->getUpdatePackage($cur_update,$filename)) {
			if ($this->unzipPackage($cur_update,$filename)) {
				if ($this->checkFiles($cur_update)) return true;
			}
		}
		return false;
	}
	private function unzipPackage($cur_update,$filename){
		$zip = new ZipArchive;
		if (!$zip) {
			$this->logError(Text::_("ZipArchive object error")); return false;
		}
		$res = $zip->open($filename);
		if ($res === TRUE) {
			$zip->extractTo(PATH_TMP.$cur_update);
			$zip->close();
			unlink($filename);
			$this->logOK(Text::_("Extracting package contents to temporary folder"));
		} else {
			$this->logError(Text::_("Package open error")." : ".$res); return false;
		}
		return true;
	}
	private function getUpdatePackage($cur_update,$filename){
		if (mkdir(PATH_TMP.$cur_update, 0755, false)) $this->logOK(Text::_("Creating temporary folder"));
		else { $this->logError(Text::_("Directory is not writable")." : ".PATH_TMP); return false;
		}
		$package = fopen($filename, 'wb');
		$url = $this->_url."download.php";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FAILONERROR, 1);
		curl_setopt($ch, CURLOPT_FRESH_CONNECT,1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);// allow redirects
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_FILE, $package);
		curl_setopt($ch, CURLOPT_HEADER, 0); // allow redirects
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		if(curl_exec($ch)) {
			$this->logOK(Text::_("File loading"));
		} else {
			$this->logError(Text::_("Error at file loading with code")." : ".curl_error($ch)); curl_close($ch); fclose($package);
			return false;
		}
		curl_close($ch); fclose($package);
		return true;
	}
	private function deleteUpdateFolder($src){
		if (!Files::removeFolder($src, 1)) {
			$this->logWarning(Text::_("Temporary folder can not be deleted"));
			$this->logWarning(Text::_("Remove it manually")." : ".$src);
		}
		if (isset($_SESSION['update_folder'])) unset($_SESSION['update_folder']);
	}
	public function processUpdates($new_version){
		if ((isset($_SESSION['update_folder']))&&($_SESSION['update_folder'])){
			$cur_update=$_SESSION['update_folder'];
			unset($_SESSION['update_folder']); // ВЕРНУТЬ !!!!!
			$src=PATH_TMP.$cur_update;
			if ($this->copyFiles($cur_update)) {
				$new_rev=floor($new_version);
				$my_rev=Portal::getInstance()->getVersionRevision();
				if ($this->proceedDbUpdate($cur_update,$my_rev,$new_rev)) {
					$this->logOK("<br />".Text::_("Update finished"));
					if ($this->proceedDbRestructure($new_rev)) {
						$this->logOK("<br />".Text::_("Database restructuring finished"));
						$this->logOK("<br />".Text::_("Update finished"));
					} else {
						$this->logWarning("<br />".Text::_("Database restructuring failed"));
						$this->logWarning(Text::_("Apply restructuring manually"));
					}
					$this->deleteUpdateFolder($src);
				} else {
					$this->logWarning(Text::_("Some SQL queries failed"));
					$this->logWarning(Text::_("Apply them manually"));
				}
			}
		} else Util::redirect("index.php",Text::_("Updates folder not found"));
		return $this->_update_log;
	}
	private function copyFiles($cur_update){
		$result=true;
		$src=PATH_TMP.$cur_update.DS."sources".DS;
		if (!is_dir($src)) {
			//			unset($_SESSION['update_folder']); // УБРАТЬ !!!!!
			Util::redirect("index.php",Text::_("Updates folder not found"));
		} else {
			$dest=PATH_FRONT;
			$this->logMessage(Text::_("Processing package"));
			if (is_writable($dest)) $this->logOK(Text::_("Is writeble")." : ".$dest);
			else { $this->logError(Text::_("Is not writeble")." : ".$dest); $result=false;
			}
			if (Files::copyFolder($src,$dest,true)) $this->logOK(Text::_("All files are copied"));
			else  {
				$this->logError(Text::_("Copying of files failed"));
				$this->logWarning(Text::_("It is strongly recommended to perform manual update"));
				$result=false;
			}
		}
		return $result;
	}
	private function checkFiles($cur_update){
		$result=true;
		$src=PATH_TMP.$cur_update.DS."sources".DS;
		$hiddenfiles=array(".","..");
		if (!is_dir($src)) {
			if (isset($_SESSION['update_folder'])) unset($_SESSION['update_folder']);
			Util::redirect("index.php",Text::_("Updates folder not found"));
		} else {
			$dest=PATH_FRONT;
			$fc=array(); $counter=0;
			$this->logMessage(Text::_("Processing package"));
			Files::getFolderContent($src, "", $hiddenfiles, 1, $fc, $counter);
			if (($fc)&&(count($fc))) {
				foreach($fc as $f){
					if ($f["subpath"]) $tmppath=$dest.$f["subpath"].DS.$f["filename"];
					else $tmppath=$dest.$f["filename"];
					if (!Files::canWrite($tmppath,$f["folder"])) {
						$this->logError(Text::_("Is not writeble")." : ".$tmppath);
						$result=false;
						// Lets delete updates
						$src=PATH_TMP.$cur_update;
						$this->deleteUpdateFolder($src);
					}
				}
			}
		}
		if($result)	$this->logOK(Text::_("All files and folders are writeble"));
		else { $this->logError("<br />".Text::_("Some files and folders are not writeble"));
		}
		return $result;
	}
	private function proceedDbUpdate($cur_update,$my_rev,$new_rev){
		$result=true;
		if ($my_rev<$new_rev) {
			for ($rev = $my_rev+1; $rev <= $new_rev; $rev++){
				$src=PATH_TMP.$cur_update.DS."updates".DS.$rev.".sql";
				if (is_file($src)) {
					if (!$this->populateDatabase($src)) $result=false;
				}
			}
		}
		return $result;
	}
	private function populateDatabase($sqlfile) {
		if(!($buffer = file_get_contents($sqlfile))) return false;
		//$queries = preg_split ("/[".$this->_delimiter."]+/", $buffer);
		$queries = preg_split ("/(".$this->_delimiter.")/", $buffer);
		if (count($queries)) {
			$this->logMessage(Text::_("Processing")." SQL : ".$sqlfile);
			foreach ($queries as $query) {
				$query = trim($query);
				if ($query != '' && $query[0] != '#') {
					$this->_db->setQuery($query);
					if (!$this->_db->query()) {
						$this->logError("SQL Error :".$this->_db->getLastError()."<br />".$this->_db->getQuery());
						return false;
					}
				}
			}
		}
		return true;
	}
	public function proceedDbRestructure($new_rev){
		$result=true;
		$my_rev=intval(Settings::getVar("restruct_version"));
		if ($my_rev < $new_rev) {
			for ($rev = $my_rev+1; $rev <= $new_rev; $rev++){
				$file=PATH_MODULES."service".DS."restructure".DS."restructure_".$rev.".php";
				$className="Restructure_".$rev;
				if (is_file($file)) {
					require_once $file;
					if(!class_exists($className, false)) {
						$this->logError(Text::_("Error applying restructure file").": ".$className);
						return false;
					}
					$messages=array(); $errors=array();
					if (!$className::proceed($messages, $errors)) {
						if(count($errors)){
							foreach($errors as $error) $this->logError($error);
						}
						return false;
					}
					if(count($messages)){
						foreach($messages as $message) $this->logOK($message);
					}
					Settings::setVar("restruct_version", $rev);
				}
			}
		}
		Settings::setVar("restruct_version", $new_rev);
		return $result;
	}
}
?>