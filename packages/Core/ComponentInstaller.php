<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class ComponentInstaller extends BaseObject {

	//---------- Singleton implementation ------------
	private static $_instance = null;

	public static function createInstance($critFail=true) {
		if (self::$_instance == null) {
			self::$_instance = new self($critFail);
		}
	}

	public static function getInstance($critFail=true) {
		self::createInstance($critFail);
		return self::$_instance;
	}
	//------------------------------------------------

	private $_db = null;
	private $_dirsCreated = array();
	private $_filesCopied = array();
	private $_distribPath = "";
	private $_log_counter = 0;
	private $_log = array();

	private function __construct($critFail) {
		$this->initObj();
		$this->_db = Database::getInstance();
	}
	private function _log($msg,$type) {
		$this->_log_counter++;
		$this->_log[$this->_log_counter]["type"]=$type;
		$this->_log[$this->_log_counter]["text"]=$msg;
	}
	private function log_message($msg) {
		$this->_log($msg,"message");
	}

	private function log_error($msg) {
		$this->_log($msg,"error");
	}

	private function log_warning($msg) {
		$this->_log($msg,"warning");
	}
	public function getLog()	{
		return $this->_log;
	}
	public function install($distribDir) {
		$success = false;
		$xmlPath = $distribDir.DS."install.xml";
		$xml = new InstallerXML($xmlPath);
		if ($xml->isLoaded()) {
			$this->_distribPath = $distribDir;
			switch ($xml->type) {
				case "module":
					$is_installed=Module::isInstalled($xml->name);
					$installed_version = $this->getPackageVersion($xml->name, "module");
					$success = $this->installModule($xml, $is_installed, $installed_version); break;
				case "widget":
					$is_installed=Widget::isInstalled($xml->name);
					$installed_version = $this->getPackageVersion($xml->name, "widget");
					$success = $this->installWidget($xml, $is_installed, $installed_version); break;
				case "plugin":
					$is_installed=Plugin::isInstalled($xml->subname,$xml->name);
					$installed_version = $this->getPackageVersion($xml->subname.".".$xml->name, "plugin");
					$success = $this->installPlugin($xml, $is_installed, $installed_version); break;
				case "package":
					$success = $this->installPackage($xml); break;
				default:	break;
			}
			if ($success) return true;
			else {
				if(!$xml->update || !$is_installed){
					$this->rollbackFiles();
					$this->rollbackDirs();
				}
				return false;
			}
		}	else {
			$this->log_error(Text::_("Can not load")." ". $xmlPath);
			return false;
		}
	}
	private function compareVersion($type, $name, $version, $operator="<="){
		// check if xml version > installed version
		$current_version = $this->getPackageVersion($name, $type);
		if(!is_null($current_version)){
			return version_compare($current_version, $version, $operator);
		}
		return false;
	}
	private function getPackageVersion($name, $type){
		$sql="SELECT c_version FROM #__install WHERE c_type='".$type."' AND c_name='".$name."'";
		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}
	private function createDirectory($path, $rollback=true) {
		if (!Files::checkFolder($path, 0)) {
			if (Files::checkFolder($path,1)) {
				$this->log_message(Text::_("Directory created").": ".$path);
				if($rollback) $this->_dirsCreated []= $path;
				return true;
			} else {
				$this->log_error(Text::_("Unable to create directory").": ".$path);
				return false;
			}
		} elseif(Files::checkFolder($path, 0)) {
			return true;
		} else {
			$this->log_error(Text::_("Unable to create directory").": ".$path);
			return false;
		}
	}
	
	private function createDirectories($root,$dirs) {
		$success = true;
		foreach ($dirs as $dirName=>$dirData) {
			$path = $root.DS.$dirName;
			if (!$this->createDirectory($path)) {
				$success = false;
				break;
			}
			$success = $this->createDirectories($path,$dirData->dirs);
			if (!$success) break;
		}
		return $success;
	}

	private function unInstallFiles($dstRoot,$dirs) {
		$success = true;
		foreach ($dirs as $dirName=>$dirData) {
			$dstDir = $dstRoot.DS.$dirName;
			if (!$this->deleteFiles($dstDir,$dirData->files)) {
				return false;
			}
			$success = $this->unInstallFiles($dstDir,$dirData->dirs);
			if (!$success) return false;
		}
		return $success;
	}
	private function deleteFiles($dstDir,$files) {
		foreach ($files as $fileSrc=>$fileDst) {
			$dstPath = $dstDir.DS.$fileDst;
			$this->log_message(Text::_("Deleting file ")." ".$dstPath);
			if (!@unlink($dstPath)) return false;
		}
		return true;
	}

	private function installFiles($srcRoot,$dstRoot,$dirs) {
		$success = true;
		foreach ($dirs as $dirName=>$dirData) {
			$srcDir = $srcRoot.DS.$dirName;
			$dstDir = $dstRoot.DS.$dirName;
			if (!$this->copyFiles($srcDir,$dstDir,$dirData->files)) {
				return false;
			}
			$success = $this->installFiles($srcDir,$dstDir,$dirData->dirs);
			if (!$success) return false;
		}
		return $success;
	}

	private function copyFiles($srcDir,$dstDir,$files) {
		foreach ($files as $fileSrc=>$fileDst) {
			$srcPath = $srcDir.DS.$fileSrc;
			$dstPath = $dstDir.DS.$fileDst;
			if (!$this->installFile($srcPath,$dstPath)) return false;
		}
		return true;
	}

	private function rollbackDirs() {
		$dirs = array_reverse($this->_dirsCreated);
		foreach ($dirs as $path) {
			if (@rmdir($path)) {
				$this->log_message(Text::_("Directory removed").": ".$path);
			} else  $this->log_error(Text::_("Failed to remove directory").": ".$path);
		}
	}

	private function rollbackFiles() {
		$files = array_reverse($this->_filesCopied);
		foreach ($files as $path) {
			if (@unlink($path))	$this->log_message(Text::_("File removed").": ".$path);
			else $this->log_error(Text::_("Failed to remove file").": ".$path);
		}
	}

	private function installFile($srcPath,$dstPath) {
		if (!@copy($srcPath,$dstPath)) {
			$this->log_error(Text::_("File copy failed").": ".$srcPath." => ".$dstPath);
			return false;
		}	else {
			$this->_filesCopied []= $dstPath;
			$this->log_message(Text::_("File copied").": ".$dstPath);

			return true;
		}
	}

	private function createMenus($arr_menus,$name){
		// Это наверное вообще не надо, оставил как заглушку
		return true;
	}

	private function createAdminMenus($arr_menus,$module){
		if (count($arr_menus)){
			$this->log_message(Text::_("Creating admin menus"));
			foreach($arr_menus as $key=>$mnugrp) {
				$sql="SELECT mnu_id FROM #__admin_menus WHERE mnu_parent_id=0 AND mnu_name='".$key."' AND mnu_module='".$module."' LIMIT 1";
				$this->_db->setQuery($sql);
				$parent_id=(int)$this->_db->loadResult();
				if(!$parent_id){
					$sql="INSERT INTO #__admin_menus (mnu_id, mnu_parent_id, mnu_name, mnu_link, mnu_module) VALUES(0, 0, '".$key."', '','".$module."')";
					$this->_db->setQuery($sql);
					if ($this->_db->query()) $parent_id = $this->_db->insertid();
				}
				if($parent_id) {
					if (count($mnugrp)){
						foreach($mnugrp as $name=>$link){
							$sql="SELECT COUNT(mnu_id) FROM #__admin_menus WHERE mnu_parent_id='".$parent_id."' AND mnu_name='".$name."' AND mnu_module='".$module."'";
							$this->_db->setQuery($sql);
							if($this->_db->loadResult()==0){
								$sql="INSERT INTO #__admin_menus (mnu_id, mnu_parent_id, mnu_name, mnu_link, mnu_module) VALUES('0', '".$parent_id."', '".$name."', '".$link."','".$module."')";
								$this->_db->setQuery($sql);
								if (!$this->_db->query()) {
									$this->log_error(Text::_("Failed to add admin menus"));
									return false;
								}
							} else {
								$sql="UPDATE #__admin_menus SET mnu_link='".$link."'WHERE mnu_parent_id='".$parent_id."' AND mnu_name='".$name."' AND mnu_module='".$module."'";
								$this->_db->setQuery($sql);
								if (!$this->_db->query()) {
									$this->log_error(Text::_("Failed to update admin menus"));
									return false;
								}
							}
						}
					}
				} else {
					$this->log_error(Text::_("Failed to add admin menus"));
					return false;
				}
			}
		}
		return true;
	}

	private function installPackage(&$xml) {
		// FIXME Доделать установку пакета
		$this->log_error(Text::_('Under construction'));
		return false;
	}
	private function installModule(&$xml, $is_installed, $installed_version) {
		$this->log_message(Text::_("Installing module")." ".$xml->name);
		$result=true;
		if (($xml->name=="system")||($is_installed && !$xml->update)) {
			$this->log_error(Text::_('Module is allready installed'));
			return false;
		} elseif ($is_installed && $xml->update && !$this->compareVersion("module", $xml->name, $xml->version)) {
			$this->log_error(Text::_('Module version is less then allready installed'));
			return false;
		}	else {
			// check dependences
			if (count($xml->dependsOn)){
				foreach($xml->dependsOn as $dName){
					if (!Module::isInstalled($dName)){
						$this->log_error(Text::_('Module needs other module to be installed first')." ".$dName);
						return false;
					}
				}
			}
			// Create root directories
			if (count($xml->feDirs)||count($xml->feFiles)) {
				$moduleFeRootDir = PATH_FRONT_MODULES.$xml->name;
				$this->log_message(Text::_("Frontend root dir")."=".$moduleFeRootDir);
				if (!$this->createDirectory($moduleFeRootDir)) return false;
				// root files copy
				if (!$this->copyFiles($this->_distribPath.DS.$xml->feRoot,$moduleFeRootDir,$xml->feFiles)) return false;
				// Create frontend FS
				if (!$this->createDirectories($moduleFeRootDir,$xml->feDirs)) return false;
				if (!$this->installFiles($this->_distribPath.DS.$xml->feRoot,$moduleFeRootDir,$xml->feDirs)) return false;
			}
			if (count($xml->beDirs)||count($xml->beFiles)) {
				$moduleBeRootDir = PATH_MODULES.$xml->name;
				$this->log_message(Text::_("Backend root dir")."=".$moduleBeRootDir);
				if (!$this->createDirectory($moduleBeRootDir)) return false;
				// root files copy
				if (!$this->copyFiles($this->_distribPath.DS.$xml->beRoot,$moduleBeRootDir,$xml->beFiles)) return false;
				// Create backend FS
				if (!$this->createDirectories($moduleBeRootDir,$xml->beDirs)) return false;
				if (!$this->installFiles($this->_distribPath.DS.$xml->beRoot,$moduleBeRootDir,$xml->beDirs)) return false;
			}

			if (count($xml->cssDirs)||count($xml->cssFiles)) {
				$cssRoot=PATH_CSS."modules";
				$this->log_message(Text::_("Copying css files to")." ".$cssRoot);
//				if (!$this->createDirectory($cssRoot)) return false;
				if (!$this->copyFiles($this->_distribPath.DS.$xml->cssRoot,$cssRoot,$xml->cssFiles)) return false;
				if (!$this->createDirectories($cssRoot,$xml->cssDirs)) return false;
				if (!$this->installFiles($this->_distribPath.DS.$xml->cssRoot,$cssRoot,$xml->cssDirs)) return false;
			}
			// JavaScript
			if (count($xml->jsDirs)||count($xml->jsFiles)) {
				$jsRoot=PATH_JS."modules";
				$this->log_message(Text::_("Copying javascript files to")." ".$jsRoot);
//				if (!$this->createDirectory($jsRoot)) return false;
				if (!$this->copyFiles($this->_distribPath.DS.$xml->jsRoot,$jsRoot,$xml->jsFiles)) return false;
				if (!$this->createDirectories($jsRoot,$xml->jsDirs)) return false;
				if (!$this->installFiles($this->_distribPath.DS.$xml->jsRoot,$jsRoot,$xml->jsDirs)) return false;
			}

			// redistribution
			if (count($xml->redistributionDirs)||count($xml->redistributionFiles)) {
				$jsRoot=PATH_FRONT."redistribution";
				$this->log_message(Text::_("Copying redistribution files to")." ".$jsRoot);
				if (!$this->createDirectory($jsRoot)) return false;
				if (!$this->copyFiles($this->_distribPath.DS.$xml->redistributionRoot,$jsRoot,$xml->redistributionFiles)) return false;
				if (!$this->createDirectories($jsRoot,$xml->redistributionDirs)) return false;
				if (!$this->installFiles($this->_distribPath.DS.$xml->redistributionRoot,$jsRoot,$xml->redistributionDirs)) return false;
			}
			// packages 
			if (count($xml->packagesDirs)||count($xml->packagesFiles)) {
				$jsRoot=PATH_FRONT."packages";
				$this->log_message(Text::_("Copying packages files to")." ".$jsRoot);
				if (!$this->createDirectory($jsRoot)) return false;
				if (!$this->copyFiles($this->_distribPath.DS.$xml->packagesRoot,$jsRoot,$xml->packagesFiles)) return false;
				if (!$this->createDirectories($jsRoot,$xml->packagesDirs)) return false;
				if (!$this->installFiles($this->_distribPath.DS.$xml->packagesRoot,$jsRoot,$xml->packagesDirs)) return false;
			}
			

			// Localization
			$this->log_message(Text::_("Installing language files"));
			foreach ($xml->language as $languageName=>$language_file) {
				$langSrcPath = $this->_distribPath.DS.$language_file;
				$langDstPath = PATH_LANGUAGE."common".DS.$languageName.DS."modules".DS.$xml->name.".ini";
				if (file_exists($langSrcPath)) {
					if (!$this->installFile($langSrcPath,$langDstPath)) {
						return false;
					}
				}
			}
			// apply queries
			if (count($xml->queries)) {
				$this->log_message(Text::_("Applying install queries to database"));
				foreach ($xml->queries as $query) {
					if($query->max_version=="" || $this->compareVersion("module", $xml->name, $query->max_version, "<")){
						$this->_db->setQuery($query->sql);
						if (!$this->_db->query()) {
							if($xml->update){
								$this->log_error("SQL Error :".$this->_db->getLastError()."<br />".$this->_db->getQuery());
								$result=false;
							} else {
								$this->log_error(Text::_("Failed to apply queries to database"));
								return false;
							}
						}
					}
				}
				if(!$result) $this->log_error(Text::_("Failed to apply queries to database"));
			}
			// Build config
			$moduleConfig = "";
			foreach ($xml->params as $cfgKey=>$cfgValue) {	$moduleConfig .= "$cfgKey=$cfgValue;"; }
			$moduleConfig = substr($moduleConfig,0,strlen($moduleConfig) - 1);
			if(!$is_installed) {
				// Install into database
				$query = "INSERT INTO #__modules (m_id, m_name,m_show_breadcrumb,m_config,m_incl_map,m_deleted) VALUES (NULL,'".$xml->name."',1,'".$moduleConfig."',0,0)";
				$this->_db->setQuery($query);
				if (!$this->_db->query()) {
					$this->log_error(Text::_("Failed to install module into database"));
					return false;
				}
				// Copy installer XML to database
				$installdata = $xml->xmlData();
				$query = "INSERT INTO #__install (c_id, c_type, c_name,	c_version, c_description, c_author, c_email, c_site, c_license, c_data)
				VALUES(NULL,'module','".$xml->name."',	'".$xml->version."', '".$xml->description."', '".$xml->author."', '".$xml->email."', '".$xml->site."', '".addslashes($xml->license)."', '".addslashes($installdata)."')";
				$this->_db->setQuery($query);
				if (!$this->_db->query()) {
					$this->log_error(Text::_("Failed to write install info into database"));
					return false;
				}
			} elseif($xml->update) {
				// Update installer XML in database
				$installdata = $xml->xmlData();
				$query = "UPDATE #__install SET c_version='".$xml->version."', c_description='".$xml->description."', c_author='".$xml->author."', c_email='".$xml->email."', c_site='".$xml->site."', c_license='".addslashes($xml->license)."', c_data='".addslashes($installdata)."'";
				$query.= " WHERE c_type='module' AND c_name='".$xml->name."'";
				$this->_db->setQuery($query);
				if (!$this->_db->query()) {
					$this->log_error(Text::_("Failed to write update info into database"));
					$this->log_error("SQL Error :".$this->_db->getLastError()."<br />".$this->_db->getQuery());
					$result=false;
				}
			}
			// Module access ACL
			ACLObject::createACLObject($xml->name,$xml->name."Module","Module access",0);
			ACLObject::createACLObject($xml->name,$xml->name."Module","Module access",1);

			// Developer-defined ACL
			foreach ($xml->feACL as $aclName=>$aclDescr) {
				ACLObject::createACLObject($xml->name,$aclName,$aclDescr,0);
			}
			foreach ($xml->beACL as $aclName=>$aclDescr) {
				ACLObject::createACLObject($xml->name,$aclName,$aclDescr,1);
			}
			// Finally create menus
			$this->createAdminMenus($xml->beMenus,$xml->name);
			$this->createMenus($xml->feMenus,$xml->name);

			return $result;
		}
	}

	// if this return true, need update  
	private function checkInstalledVersionFile($file,$input_version)
	{
		if(is_file($file)){
			$handle = @fopen($file, "r");
			if ($handle) {
				while (($buffer = fgets($handle)) !== false) {
					if(strpos($buffer,'Revision:')){
						$res=preg_match('/\s+([\d.]*)\s+/',$buffer,$match);
						if($res===1){
							$cur_revision=$match[1];
							if( version_compare ( $input_version, $cur_revision, ">")){
								fclose($handle);
								return true;
							}
						}
					}
				}
				if (!feof($handle)) {
					echo "Error : fgets() crashed\n";
				}
				fclose($handle);
			}
			return false;
		}
		return true;
	}
	
	private function installWidget(&$xml, $is_installed, $installed_version) {
		$this->log_message(Text::_("Installing widget")." ".$xml->name);
		$result=true;
		// Is it allready installed?
		if ($is_installed && !$xml->update) {
			$this->log_error(Text::_('Widget is allready installed'));
			return false;
		} elseif ($is_installed && $xml->update && !$this->compareVersion("widget", $xml->name, $xml->version)) {
			$this->log_error(Text::_('Widget version is less then allready installed'));
			return false;
		}	else {
			// check dependences
			if (count($xml->dependsOn)){
				foreach($xml->dependsOn as $dName){
					if (!Module::isInstalled($dName)){
						$this->log_error(Text::_('Widget needs module to be installed first')." ".$dName);
						return false;
					}
				}
				// check addons
				if (count($xml->addons)){
					foreach($xml->addons as $addfiles){
						if($addfiles->side=='backend'){
							$widgetAddonDir=PATH_ADMIN;
							$widgetAddonDirSrc=$this->_distribPath.DS.$xml->beRoot;
						}else{
							$widgetAddonDir=PATH_FRONT;
							$widgetAddonDirSrc=$this->_distribPath.DS.$xml->feRoot;
						}
						if($addfiles->directory)
						{
							$widgetAddonDir.=str_replace("/",DS,$addfiles->directory);
						}	
						$this->log_message(Text::_("Addon files dir")."=".$widgetAddonDir);
						if (!$this->createDirectory($widgetAddonDir)) return false;
						foreach($addfiles->files as $keyf=>$arfile)
						{	
							$file=$arfile['file'];
							$version=$arfile['version'];
							if( $this->checkInstalledVersionFile($widgetAddonDir.DS.$file,$version)){							
								$this->installFile($widgetAddonDirSrc.DS.$file,$widgetAddonDir.DS.$file);
							}else{
								$this->log_message(Text::_("the version of the incoming file is older than the installed").": ".$widgetAddonDir.DS.$file);
							}
						}
					}
				}
			}
			// Create root directories
			if (count($xml->feDirs)||count($xml->feFiles)) {
				$widgetFeRootDir = PATH_FRONT_WIDGETS.$xml->name;
				$this->log_message(Text::_("Frontend root dir")."=".$widgetFeRootDir);
				if (!$this->createDirectory($widgetFeRootDir)) return false;
				// root files copy
				if (!$this->copyFiles($this->_distribPath.DS.$xml->feRoot,$widgetFeRootDir,$xml->feFiles)) return false;
				// Create frontend FS
				if (!$this->createDirectories($widgetFeRootDir,$xml->feDirs)) return false;
				if (!$this->installFiles($this->_distribPath.DS.$xml->feRoot,$widgetFeRootDir,$xml->feDirs)) return false;
			} elseif (count($xml->beDirs)||count($xml->beFiles)) {
				$widgetBeRootDir = PATH_WIDGETS.$xml->name;
				$this->log_message(Text::_("Backend root dir")."=".$widgetBeRootDir);
				if (!$this->createDirectory($widgetBeRootDir)) return false;
				// root files copy
				if (!$this->copyFiles($this->_distribPath.DS.$xml->beRoot,$widgetBeRootDir,$xml->beFiles)) return false;
				// Create backend FS
				if (!$this->createDirectories($widgetBeRootDir,$xml->beDirs)) return false;
				if (!$this->installFiles($this->_distribPath.DS.$xml->beRoot,$widgetBeRootDir,$xml->beDirs)) return false;
			} else {
				$this->log_error(Text::_("Files list empty"));
				return false;
			}

			// CSS
			if (count($xml->cssDirs)||count($xml->cssFiles)) {
				$cssRoot=PATH_CSS."widgets";
				$this->log_message(Text::_("Copying css files to")." ".$cssRoot);
//				if (!$this->createDirectory($cssRoot)) return false;
				if (!$this->copyFiles($this->_distribPath.DS.$xml->cssRoot,$cssRoot,$xml->cssFiles)) return false;
				if (!$this->createDirectories($cssRoot,$xml->cssDirs)) return false;
				if (!$this->installFiles($this->_distribPath.DS.$xml->cssRoot,$cssRoot,$xml->cssDirs)) return false;
			}
			// JavaScript
			if (count($xml->jsDirs)||count($xml->jsFiles)) {
				$jsRoot=PATH_JS."widgets";
				$this->log_message(Text::_("Copying javascript files to")." ".$jsRoot);
//				if (!$this->createDirectory($jsRoot)) return false;
				if (!$this->copyFiles($this->_distribPath.DS.$xml->jsRoot,$jsRoot,$xml->jsFiles)) return false;
				if (!$this->createDirectories($jsRoot,$xml->jsDirs)) return false;
				if (!$this->installFiles($this->_distribPath.DS.$xml->jsRoot,$jsRoot,$xml->jsDirs)) return false;
			}

			// redistribution
			if (count($xml->redistributionDirs)||count($xml->redistributionFiles)) {
				$jsRoot=PATH_FRONT."redistribution";
				$this->log_message(Text::_("Copying redistribution files to")." ".$jsRoot);
				if (!$this->createDirectory($jsRoot)) return false;
				if (!$this->copyFiles($this->_distribPath.DS.$xml->redistributionRoot,$jsRoot,$xml->redistributionFiles)) return false;
				if (!$this->createDirectories($jsRoot,$xml->redistributionDirs)) return false;
				if (!$this->installFiles($this->_distribPath.DS.$xml->redistributionRoot,$jsRoot,$xml->redistributionDirs)) return false;
			}
			
			// packages
			if (count($xml->packagesDirs)||count($xml->packagesFiles)) {
				$jsRoot=PATH_FRONT."packages";
				$this->log_message(Text::_("Copying packages files to")." ".$jsRoot);
				if (!$this->createDirectory($jsRoot)) return false;
				if (!$this->copyFiles($this->_distribPath.DS.$xml->packagesRoot,$jsRoot,$xml->packagesFiles)) return false;
				if (!$this->createDirectories($jsRoot,$xml->packagesDirs)) return false;
				if (!$this->installFiles($this->_distribPath.DS.$xml->packagesRoot,$jsRoot,$xml->packagesDirs)) return false;
			}
			
			// Localization
			$this->log_message(Text::_("Installing language files"));
			foreach ($xml->language as $languageName=>$language_file) {
				$langSrcPath = $this->_distribPath.DS.$language_file;
				$langDstPath = PATH_LANGUAGE."common".DS.$languageName.DS."widgets".DS.$xml->name.".ini";
				if (file_exists($langSrcPath)) {
					if (!$this->installFile($langSrcPath,$langDstPath)) {
						return false;
					}
				}
			}
			// apply queries
			if (count($xml->queries)) {
				$this->log_message(Text::_("Applying install queries to database"));
				foreach ($xml->queries as $query) {
					if($query->max_version=="" || $this->compareVersion("widget", $xml->name, $query->max_version, "<")){
						$this->_db->setQuery($query->sql);
						if (!$this->_db->query()) {
							if($xml->update){
								$this->log_error("SQL Error :".$this->_db->getLastError()."<br />".$this->_db->getQuery());
								$result=false;
							} else {
								$this->log_error(Text::_("Failed to apply queries to database"));
								return false;
							}
						}
					}
				}
				if(!$result) $this->log_error(Text::_("Failed to apply queries to database"));
			}
			if(!$is_installed) {
				// Install into database
				if (count($xml->feDirs)||count($xml->feFiles)) {
					$query = "INSERT INTO #__widgets (w_id, w_name,w_side) VALUES (NULL,'".$xml->name."',1)";
					$this->_db->setQuery($query);
					if (!$this->_db->query()) {
						$this->log_error(Text::_("Failed to install widget into database"));
						return false;
					}
				} elseif (count($xml->beDirs)||count($xml->beFiles)) {
					$query = "INSERT INTO #__widgets (w_id, w_name,w_side) VALUES (NULL,'".$xml->name."',0)";
					$this->_db->setQuery($query);
					if (!$this->_db->query()) {
						$this->log_error(Text::_("Failed to install widget into database"));
						return false;
					}
				} else {
					$this->log_error(Text::_("Failed to install widget into database"));
					return false;
				}
				// Copy installer XML то database
				$installdata = $xml->xmlData();
				$query = "INSERT INTO #__install (c_id, c_type, c_name,	c_version, c_description, c_author, c_email, c_site, c_license, c_data)
							VALUES(NULL,'widget','".$xml->name."',	'".$xml->version."', '".$xml->description."', '".$xml->author."', '".$xml->email."', '".$xml->site."', '".addslashes($xml->license)."', '".addslashes($installdata)."')";
				$this->_db->setQuery($query);
				if (!$this->_db->query()) {
					$this->log_error(Text::_("Failed to write install info into database"));
					return false;
				}
			} elseif($xml->update) {
				// Update installer XML in database
				$installdata = $xml->xmlData();
				$query = "UPDATE #__install SET c_version='".$xml->version."', c_description='".$xml->description."', c_author='".$xml->author."', c_email='".$xml->email."', c_site='".$xml->site."', c_license='".addslashes($xml->license)."', c_data='".addslashes($installdata)."'";
				$query.= " WHERE c_type='widget' AND c_name='".$xml->name."'";
				$this->_db->setQuery($query);
				if (!$this->_db->query()) {
					$this->log_error(Text::_("Failed to write update info into database"));
					$this->log_error("SQL Error :".$this->_db->getLastError()."<br />".$this->_db->getQuery());
					$result=false;
				}
			}
			return $result;
		}
	}

	private function installPlugin(&$xml, $is_installed, $installed_version) {
		$this->log_message(Text::_("Installing plugin")." ".$xml->name);
		$result=true;
		if ((!$xml->subname)||(!$xml->name)) {
			$this->log_error(Text::_('Wrong name or path'));
			return false;
		}
		// Is it allready installed?
		if (Plugin::isCorrupted($xml->subname,$xml->name)) {
			$this->log_error(Text::_('Plugin installation corrupted'));
			return false;
		} elseif ($is_installed && !$xml->update) {
			$this->log_error(Text::_('Plugin is allready installed'));
			return false;
		} elseif ($is_installed && $xml->update && !$this->compareVersion("plugin", $xml->subname.".".$xml->name, $xml->version)) {
			$this->log_error(Text::_('Plugin version is less then allready installed'));
			return false;
		}	else {
			// Create root directories
			if (count($xml->feDirs)||count($xml->feFiles)) {
				$pluginFeRootDir = PATH_PLUGINS.$xml->subname;
				$this->log_message(Text::_("Frontend root dir")."=".$pluginFeRootDir);
				if (!$this->createDirectory($pluginFeRootDir, false)) return false;
				// root files copy
				if (!$this->copyFiles($this->_distribPath.DS.$xml->feRoot,$pluginFeRootDir,$xml->feFiles)) return false;
				// Create frontend FS
				if (!$this->createDirectories($pluginFeRootDir,$xml->feDirs)) return false;
				if (!$this->installFiles($this->_distribPath.DS.$xml->feRoot,$pluginFeRootDir,$xml->feDirs)) return false;
			}
			
			// CSS
			if (count($xml->cssDirs)||count($xml->cssFiles)) {
				$cssRoot=PATH_CSS."plugins".DS.$xml->subname;
				$this->log_message(Text::_("Copying css files to")." ".$cssRoot);
				if (!$this->createDirectory($cssRoot, false)) return false;
				if (!$this->copyFiles($this->_distribPath.DS.$xml->cssRoot,$cssRoot,$xml->cssFiles)) return false;
				if (!$this->createDirectories($cssRoot,$xml->cssDirs)) return false;
				if (!$this->installFiles($this->_distribPath.DS.$xml->cssRoot,$cssRoot,$xml->cssDirs)) return false;
			}
			// JavaScript
			if (count($xml->jsDirs)||count($xml->jsFiles)) {
				$jsRoot=PATH_JS."plugins".DS.$xml->subname;
				$this->log_message(Text::_("Copying javascript files to")." ".$jsRoot);
				if (!$this->createDirectory($jsRoot, false)) return false;
				if (!$this->copyFiles($this->_distribPath.DS.$xml->jsRoot,$jsRoot,$xml->jsFiles)) return false;
				if (!$this->createDirectories($jsRoot,$xml->jsDirs)) return false;
				if (!$this->installFiles($this->_distribPath.DS.$xml->jsRoot,$jsRoot,$xml->jsDirs)) return false;
			}
			
			// redistribution
			if (count($xml->redistributionDirs)||count($xml->redistributionFiles)) {
				$jsRoot=PATH_FRONT."redistribution";
				$this->log_message(Text::_("Copying redistribution files to")." ".$jsRoot);
				if (!$this->createDirectory($jsRoot)) return false;
				if (!$this->copyFiles($this->_distribPath.DS.$xml->redistributionRoot,$jsRoot,$xml->redistributionFiles)) return false;
				if (!$this->createDirectories($jsRoot,$xml->redistributionDirs)) return false;
				if (!$this->installFiles($this->_distribPath.DS.$xml->redistributionRoot,$jsRoot,$xml->redistributionDirs)) return false;
			}
			
			// packages
			if (count($xml->packagesDirs)||count($xml->packagesFiles)) {
				$jsRoot=PATH_FRONT."packages";
				$this->log_message(Text::_("Copying packages files to")." ".$jsRoot);
				if (!$this->createDirectory($jsRoot)) return false;
				if (!$this->copyFiles($this->_distribPath.DS.$xml->packagesRoot,$jsRoot,$xml->packagesFiles)) return false;
				if (!$this->createDirectories($jsRoot,$xml->packagesDirs)) return false;
				if (!$this->installFiles($this->_distribPath.DS.$xml->packagesRoot,$jsRoot,$xml->packagesDirs)) return false;
			}
			
			// Localization
			$this->log_message(Text::_("Installing language files"));
			foreach ($xml->language as $languageName=>$language_file) {
				$langSrcPath = $this->_distribPath.DS.$language_file;
				$langDstPath = PATH_LANGUAGE."common".DS.$languageName.DS."plugins".DS.$xml->subname.".".$xml->name.".ini";
				if (file_exists($langSrcPath)) {
					if (!$this->installFile($langSrcPath,$langDstPath)) {
						return false;
					}
				}
			}
			
			// apply queries
			if (count($xml->queries)) {
				$this->log_message(Text::_("Applying install queries to database"));
				foreach ($xml->queries as $query) {
					if($query->max_version=="" || $this->compareVersion("plugin", $xml->subname.".".$xml->name, $query->max_version, "<")){
						$this->_db->setQuery($query->sql);
						if (!$this->_db->query()) {
							if($xml->update){
								$this->log_error("SQL Error :".$this->_db->getLastError()."<br />".$this->_db->getQuery());
								$result=false;
							} else {
								$this->log_error(Text::_("Failed to apply queries to database"));
								return false;
							}
						}
					}
				}
				if(!$result) $this->log_error(Text::_("Failed to apply queries to database"));
			}
			if(!$is_installed) { 
				// Install into database
				$query = "INSERT INTO #__plugins (p_id, p_path, p_name, p_params, p_ordering, p_enabled, p_deleted)";
				$query .= "VALUES (0,'".$xml->subname."','".$xml->name."','',0,0,0)";
				$this->_db->setQuery($query);
				if (!$this->_db->query()) {
					$this->log_error(Text::_("Failed to install plugin into database"));
					return false;
				}
				// Copy installer XML to database
				$installdata = $xml->xmlData();
				$query = "INSERT INTO #__install (c_id, c_type, c_name,	c_version, c_description, c_author, c_email, c_site, c_license, c_data)";
				$query.= " VALUES(0,'plugin','".$xml->subname.".".$xml->name."',	'".$xml->version."', '".$xml->description."', '".$xml->author."', '".$xml->email."', '".$xml->site."', '".addslashes($xml->license)."', '".addslashes($installdata)."')";
				$this->_db->setQuery($query);
				if (!$this->_db->query()) {
					$this->log_error(Text::_("Failed to write install info into database"));
					return false;
				}
			} elseif($xml->update) {
				// Update installer XML in database
				$installdata = $xml->xmlData();
				$query = "UPDATE #__install SET c_version='".$xml->version."', c_description='".$xml->description."', c_author='".$xml->author."', c_email='".$xml->email."', c_site='".$xml->site."', c_license='".addslashes($xml->license)."', c_data='".addslashes($installdata)."'";
				$query.= " WHERE c_type='plugin' AND c_name='".$xml->subname.".".$xml->name."'";
				$this->_db->setQuery($query);
				if (!$this->_db->query()) {
					$this->log_error(Text::_("Failed to write update info into database"));
					$this->log_error("SQL Error :".$this->_db->getLastError()."<br />".$this->_db->getQuery());
					$result=false;
				}
			}
			return $result;
		}
	}
	
	public function uninstall($xmlFile,$psid) {
		$success = false;
		$xml = new InstallerXML($xmlFile);
		if ($xml->isLoaded()) {
			switch ($xml->type) {
				case "module":
					$success = $this->uninstallModule($xml,$psid); break;
				case "widget":
					$success = $this->uninstallWidget($xml,$psid); break;
				case "plugin":
					$success = $this->uninstallPlugin($xml,$psid); break;
				case "package":
					$success = $this->uninstallPackage($xml,$psid); break;
				default:	break;
			}
			return $success;
		}	else {
			$this->log_error(Text::_("Can not load")." ". $xmlFile);
			return false;
		}
	}

	public function uninstallPackage(&$xml) {
		// FIXME Доделать удаление пакета
		$this->log_error(Text::_('Under construction'));
		return false;
	}
	public function uninstallModule($xml,$psid) {
		$this->log_message(Text::_("Uninstalling module")." ".$xml->name);
		if (!$xml->name) return false;
		if (count($xml->feDirs)||count($xml->feFiles)) {
			$moduleFeRootDir = PATH_FRONT_MODULES.$xml->name;
			$this->log_message(Text::_("Frontend root dir")."=".$moduleFeRootDir);
			$result=Files::removeFolder($moduleFeRootDir,1);
			if ($result) $this->log_message(Text::_("Frontend root folder removed"));
			else { $this->log_error(Text::_("Failed removing Frontend root folder")); 	return false;	}
		}
		if (count($xml->beDirs)||count($xml->beFiles)) {
			$moduleBeRootDir = PATH_MODULES.$xml->name;
			$this->log_message(Text::_("Backend root dir")."=".$moduleBeRootDir);
			$result=Files::removeFolder($moduleBeRootDir,1);
			if ($result) $this->log_message(Text::_("Backend root folder removed"));
			else { $this->log_error(Text::_("Failed removing Backend root folder")); 	return false;	}
		}
		// Removing localization
		foreach ($xml->language as $languageName=>$language) {
			$langDstPath = PATH_LANGUAGE."common".DS.$languageName.DS."modules".DS.$xml->name.".ini";
			if (file_exists($langDstPath)) {
				$this->log_message(Text::_("Removing language file").": ".$langDstPath);
				if (!unlink($langDstPath)) {
					$this->log_error(Text::_("Failed removing language file"));	return false;
				}
			}
		}

		// Removing CSS
		if (count($xml->cssFiles)) {
			foreach($xml->cssFiles as $file){
				$cssPath = PATH_CSS."modules".DS.$file;
				if (file_exists($cssPath)) {
					$this->log_message(Text::_("Removing css file").": ".$cssPath);
					if (!unlink($cssPath)) {
						$this->log_error(Text::_("Failed removing css file"));	return false;
					}
				} else $this->log_error(Text::_("File absent").": ".$cssPath);
			}
		}
		// Removing Javascript
		if (count($xml->jsFiles)) {
			foreach($xml->jsFiles as $file){
				$jsPath = PATH_JS."modules".DS.$file;
				if (file_exists($jsPath)) {
					$this->log_message(Text::_("Removing javascript file").": ".$jsPath);
					if (!unlink($jsPath)) {
						$this->log_error(Text::_("Failed removing javascript file"));	return false;
					}
				} else $this->log_error(Text::_("File absent").": ".$jsPath);
			}
		}
		// apply uninstall queries
		if (count($xml->unqueries)) {
			$this->log_message(Text::_("Applying uninstall queries to database"));
			foreach ($xml->unqueries as $sql) {
				$this->_db->setQuery($sql);
				if (!$this->_db->query()) {
					$this->log_error(Text::_("Failed to apply uninstall queries to database"));
					return false;
				}
			}
		}
		// Cleaning table
		$query = "DELETE FROM #__modules WHERE m_name='".$xml->name."'";
		$this->_db->setQuery($query);
		if ($this->_db->query()) $this->log_message(Text::_("Cleaned data from database"));
		else $this->log_error(Text::_("Failed to clean data from database"));
		// Clean installer XML то database
		$query = "DELETE FROM #__install WHERE c_id=".$psid;
		$this->_db->setQuery($query);
		if ($this->_db->query()) $this->log_message(Text::_("Cleaned data from install table"));
		else $this->log_error(Text::_("Failed to clean data from install table"));
		// Clean menu table
		$query = "DELETE FROM #__admin_menus WHERE mnu_module='".$xml->name."'";
		$this->_db->setQuery($query);
		if ($this->_db->query()) $this->log_message(Text::_("Cleaned admin menu"));
		else $this->log_error(Text::_("Failed to clean admin menu"));
		// Cleaning ACL
		$query = "DELETE FROM #__acl_objects WHERE ao_module_name='".$xml->name."'";
		$this->_db->setQuery($query);
		if ($this->_db->query()) $this->log_message(Text::_("Cleaned data from database"));
		else $this->log_error(Text::_("Failed to clean data from database"));

		return true;
	}

	public function uninstallWidget($xml,$psid) {
		$this->log_message(Text::_("Uninstalling widget")." ".$xml->name);
		if (!$xml->name) return false;
		
		// Removing Addons
		// check addons
		if (count($xml->addons)){
			foreach($xml->addons as $addfiles){
				if($addfiles->side=='backend'){
					$widgetAddonDir=PATH_ADMIN;
					$widgetAddonDirSrc=$this->_distribPath.DS.$xml->beRoot;
				}else{
					$widgetAddonDir=PATH_FRONT;
					$widgetAddonDirSrc=$this->_distribPath.DS.$xml->feRoot;
				}
				if($addfiles->directory)
				{
					$widgetAddonDir.=str_replace("/",DS,$addfiles->directory);
				}
				//$this->log_message(Text::_("Addon files dir")."=".$widgetAddonDir);
				foreach($addfiles->files as $keyf=>$arfile)
				{
					$file=$arfile['file'];
					$version=$arfile['version'];
					if (file_exists($widgetAddonDir.DS.$file)) {
						if($addfiles->uninstall=='manual'){
							$this->log_message(Text::_("File can be deleted manually").": ".$widgetAddonDir.DS.$file);
						}else{
							$this->log_message(Text::_("Removing addon file").": ".$widgetAddonDir.DS.$file);
							if (!unlink($widgetAddonDir.DS.$file)) {
								$this->log_error(Text::_("Failed removing addon file"));	return false;
							}
						}
					} else $this->log_error(Text::_("File absent").": ".$widgetAddonDir.DS.$file);
				}
			}
		}
		if (count($xml->feDirs)||count($xml->feFiles)) {
			$packageFeRootDir = PATH_FRONT_WIDGETS.$xml->name;
			$this->log_message(Text::_("Frontend root dir")."=".$packageFeRootDir);
			$result=Files::removeFolder($packageFeRootDir,1);
			if ($result) $this->log_message(Text::_("Frontend root folder removed"));
			else { $this->log_error(Text::_("Failed removing Frontend root folder")); 	return false;	}
		}
		if (count($xml->beDirs)||count($xml->beFiles)) {
			$packageBeRootDir = PATH_WIDGETS.$xml->name;
			$this->log_message(Text::_("Backend root dir")."=".$packageBeRootDir);
			$result=Files::removeFolder($packageBeRootDir,1);
			if ($result) $this->log_message(Text::_("Backend root folder removed"));
			else { $this->log_error(Text::_("Failed removing Backend root folder")); 	return false;	}
		}
		// Removing CSS
		if (count($xml->cssFiles)) {
			foreach($xml->cssFiles as $file){
				$cssPath = PATH_CSS."widgets".DS.$file;
				if (file_exists($cssPath)) {
					$this->log_message(Text::_("Removing css file").": ".$cssPath);
					if (!unlink($cssPath)) {
						$this->log_error(Text::_("Failed removing css file"));	return false;
					}
				} else $this->log_error(Text::_("File absent").": ".$cssPath);
			}
		}
		// Removing Javascript
		if (count($xml->jsFiles)) {
			foreach($xml->jsFiles as $file){
				$jsPath = PATH_JS."widgets".DS.$file;
				if (file_exists($jsPath)) {
					$this->log_message(Text::_("Removing javascript file").": ".$jsPath);
					if (!unlink($jsPath)) {
						$this->log_error(Text::_("Failed removing javascript file"));	return false;
					}
				} else $this->log_error(Text::_("File absent").": ".$jsPath);
			}
		}
		// Removing localization
		foreach ($xml->language as $languageName=>$language) {
			$langDstPath = PATH_LANGUAGE."common".DS.$languageName.DS."widgets".DS.$xml->name.".ini";
			if (file_exists($langDstPath)) {
				$this->log_message(Text::_("Removing language file").": ".$langDstPath);
				if (!unlink($langDstPath)) {
					$this->log_error(Text::_("Failed removing language file"));	return false;
				}
			}
		}
		// apply uninstall queries
		if (count($xml->unqueries)) {
			$this->log_message(Text::_("Applying uninstall queries to database"));
			foreach ($xml->unqueries as $sql) {
				$this->_db->setQuery($sql);
				if (!$this->_db->query()) {
					$this->log_error(Text::_("Failed to apply uninstall queries to database"));
					return false;
				}
			}
		}
		// Cleaning table
		$query = "DELETE FROM #__widgets WHERE w_name='".$xml->name."'";
		$this->_db->setQuery($query);
		if ($this->_db->query()) $this->log_message(Text::_("Cleaned data from database"));
		else $this->log_error(Text::_("Failed to clean data from database"));
		// Cleaning table
		$query = "DELETE FROM #__widgets_active WHERE aw_name='".$xml->name."'";
		$this->_db->setQuery($query);
		if ($this->_db->query()) $this->log_message(Text::_("Cleaned data from database"));
		else $this->log_error(Text::_("Failed to clean data from database"));
		// Clean installer XML то database
		$query = "DELETE FROM #__install WHERE c_id=".$psid;
		$this->_db->setQuery($query);
		if (!$this->_db->query()) $this->log_error(Text::_("Failed to clean data from install table"));
		else $this->log_message(Text::_("Cleaned data from install table"));

		return true;
	}

	public function uninstallPlugin($xml,$psid) {
		$this->log_message(Text::_("Uninstalling plugin")." ".$xml->subname."/".$xml->name);
		if ((!$xml->subname)||(!$xml->name)) return false;
		if (count($xml->feDirs)||count($xml->feFiles)) {
			$pluginFeRootDir = PATH_PLUGINS.$xml->subname;
			$this->log_message(Text::_("Frontend root dir")."=".$pluginFeRootDir);
			if (!$this->unInstallFiles($pluginFeRootDir,$xml->feDirs)) {
				$this->log_error(Text::_("Failed to remove file"));
				return false;
			}

			foreach($xml->feFiles as $file) {
				$path=$pluginFeRootDir.DS.$file;
				if (@unlink($path))	$this->log_message(Text::_("File removed").": ".$path);
				else $this->log_error(Text::_("Failed to remove file").": ".$path);
			}
		}

		// Removing localization
		foreach ($xml->language as $languageName=>$language) {
			$langDstPath = PATH_LANGUAGE."common".DS.$languageName.DS."plugins".DS.$xml->subname.".".$xml->name.".ini";
			if (file_exists($langDstPath)) {
				$this->log_message(Text::_("Removing language file").": ".$langDstPath);
				if (!unlink($langDstPath)) {
					$this->log_error(Text::_("Failed removing language file"));	return false;
				}
			}
		}
		// Removing CSS
		if (count($xml->cssFiles)) {
			foreach($xml->cssFiles as $file){
				$cssPath = PATH_CSS."plugins".DS.$xml->subname.DS.$file;
				if (file_exists($cssPath)) {
					$this->log_message(Text::_("Removing css file").": ".$cssPath);
					if (!unlink($cssPath)) {
						$this->log_error(Text::_("Failed removing css file"));	return false;
					}
				} else $this->log_error(Text::_("File absent").": ".$cssPath);
			}
		}
		// Removing Javascript
		if (count($xml->jsFiles)) {
			foreach($xml->jsFiles as $file){
				$jsPath = PATH_JS."plugins".DS.$xml->subname.DS.$file;
				if (file_exists($jsPath)) {
					$this->log_message(Text::_("Removing javascript file").": ".$jsPath);
					if (!unlink($jsPath)) {
						$this->log_error(Text::_("Failed removing javascript file"));	return false;
					}
				} else $this->log_error(Text::_("File absent").": ".$jsPath);
			}
		}
		// apply uninstall queries
		if (count($xml->unqueries)) {
			$this->log_message(Text::_("Applying uninstall queries to database"));
			foreach ($xml->unqueries as $sql) {
				$this->_db->setQuery($sql);
				if (!$this->_db->query()) {
					$this->log_error(Text::_("Failed to apply uninstall queries to database"));
					return false;
				}
			}
		}
		// Cleaning table
		$query = "DELETE FROM #__plugins WHERE p_name='".$xml->name."' AND p_path='".$xml->subname."'";
		$this->_db->setQuery($query);
		if ($this->_db->query()) $this->log_message(Text::_("Cleaned data from database"));
		else $this->log_error(Text::_("Failed to clean data from database"));
		// Clean installer XML то database
		$query = "DELETE FROM #__install WHERE c_id=".$psid;
		$this->_db->setQuery($query);
		if ($this->_db->query()) $this->log_message(Text::_("Cleaned data from install table"));
		else $this->log_error(Text::_("Failed to clean data from install table"));

		return true;
	}
}
?>