<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class InstallerXML extends BaseObject {

	private $_path 		= "";						// путь до файла установщика
	private $_isLoaded	= false;			// флаг удачной загрузки
	private $_xml		= null;						// собстовенно сам объект xml

	public $type				= "";					// тип (module,widget,plugin)
	public $name				= "";					// имя модуля, виджета, плагина
	public $subname				= "";			  // имя модуля, виджета, плагина
	public $description	= "";					// описание 
	public $version			= "";					// версия
	public $author			= "";					// автор
	public $email				= "";					// email автора
	public $site				= "";					// сайт автора
	public $license			= "";					// лицензия 
	
	public $dependsOn	= array();			// depends on modules
	
	public $addons		= array();			// addon files on modules

	public $feRoot		= "frontend";		// front root
	public $feDirs		= array();			// front dirs
	public $feFiles		= array();			// front files
	
	public $beRoot		= "backend";		// admin root
	public $beDirs		= array();			// admin dirs
	public $beFiles		= array();			// admin files
	
	public $feMenus		= array();			// front menus
	public $beMenus		= array();			// admin menus
	
	public $feACL			= array(); 				// front ACL
	public $beACL			= array();				// admin ACL
	
	public $language	= array();			// language files

	public $cssRoot		= "css";			  // css root
	public $cssFiles	= array();			// css files
	public $cssDirs		= array();			// css dirs

	public $jsRoot		= "js";			  	// javascript root
	public $jsFiles		= array();			// javascript files
	public $jsDirs		= array();			// javascript dirs

	public $redistributionRoot		= "redistribution";			  	// redistribution root
	public $redistributionDirs= array();			// redistribution dirs
	public $redistributionFiles		= array();			// redistribution files

	public $packagesRoot		= "packages";			  	// packages root
	public $packagesDirs		= array();			// packages dirs
	public $packagesFiles		= array();			// packages files
	
	
	public $params		= array();			// параметры
	public $queries		= array();				// SQL queries for install
	public $unqueries	= array();			// SQL queries for uninstall
	
	public function __construct($path) {
		$this->initObj();

		$this->_path = $path;
		$this->_isLoaded = $this->load();
	}

	private function getValueXML($node,$subnodeName,$defaultValue="") {
		if (isset($node->{$subnodeName})) {
			return $node->{$subnodeName};
		}
		else return $defaultValue;
	}

	private function getAttrXML($node,$attrName,$defaultValue="") {
		if (isset($node[$attrName])) {
			return $node[$attrName];
		}
		else return $defaultValue;
	}

	private function getDependences() {
		$mArr = array();
		if (isset($this->_xml->dependences)) {
			if (isset($this->_xml->dependences->module)) {
				foreach ($this->_xml->dependences->module as $module) {
					if (strlen($module) > 1) {
						$mArr[] = strval($module);
					}
				}
			}
		}
		return $mArr;
	}
	
	private function getAddonDependences() {
		$mAddon = array ();
		if (isset ( $this->_xml->addons )) {
			if (isset ( $this->_xml->addons->addon )) {
				foreach ( $this->_xml->addons->addon as $addonfile ) {
					if (isset ( $this->_xml->addons->addon->directory )) {
						foreach ( $this->_xml->addons->addon->directory as $directory ) {
							if (strlen ( $directory ) > 1) {
								$addon = new stdClass ();
								$addon->module = strval($addonfile['name']);
								$addon->side = strval($addonfile['side']);
								$addon->directory = strval($directory['name']);
								$addon->uninstall= strval($addonfile['uninstall']);
								$ind=1;
								$arrFiles = array ();
								if (isset ( $directory->file )) {
									foreach ( $directory->file as $file ) {
										if (strlen ( $file ) > 1) {
											$arrFiles [$ind]['file'] = strval ( $file );
											$arrFiles [$ind]['version']= strval ( $file['version'] );
											$ind++;
										}
									}
									$addon->files = $arrFiles;
								}
								$mAddon[]=$addon;
							}
						}
					}
				}
			}
		}
		return $mAddon;
	}
	
	private function getQueries() {
		$qArr = array();
		if (isset($this->_xml->queries)) {
			if (isset($this->_xml->queries->query)) {
				foreach ($this->_xml->queries->query as $query) {
					if (strlen($query) > 1) {
						$queryObj = new stdClass();
						$queryObj->max_version=strval($this->getAttrXML($query,"max_version"));
						$queryObj->sql=strval($query);
						$qArr[] = $queryObj;
					}
				}
			}
		}
		return $qArr;
	}
	
	private function getUninstallQueries() {
		$qArr = array();
		if (isset($this->_xml->unqueries)) {
			if (isset($this->_xml->unqueries->query)) {
				foreach ($this->_xml->unqueries->query as $query) {
					if (strlen($query) > 1) {
						$qArr[] = strval($query);
					}
				}
			}
		}
		return $qArr;
	}
	
	private function getMenus($node) {
		$mArr = array();
		if (isset($node->menu)) {
			foreach ($node->menu as $grp) {
				$cvarName = strval($grp['name']);
				if (strlen($cvarName) > 1) {
					$mArr[$cvarName] = $this->getMenusItems($grp);
				}
			}
		}
		return $mArr;
	}
	private function getMenusItems($node) {
		$mArr = array();
			if (isset($node->item)) {
			foreach ($node->item as $item) {
				$cvarName = strval($item['name']);
				if (strlen($cvarName) > 1) {
					$mArr[$cvarName] = strval($item);
				}
			}
		}
		return $mArr;
	}
	
	private function getDirectories($node) {
		$dirArr = array();
		if (isset($node->directory)) {
			foreach ($node->directory as $dir) {
				$subdirs = $this->getDirectories($dir);
				$dirName = strval($this->getAttrXML($dir,"name",""));
				if (strlen($dirName) > 0) {
					$dirData = new stdclass();
					$dirData->dirs = $subdirs;
					$dirData->files = $this->getFiles($dir);
					$dirArr[$dirName] = $dirData;
				}
			}
		}

		return $dirArr;
	}

	private function getFiles($node) {
		$filesArr = array();
		if (isset($node->file)) {
			foreach ($node->file as $_fileName) {
				$fileSrc = strval($_fileName);
				$fileDst = $this->getAttrXML($_fileName,"dst",$fileSrc);
				if (strlen($fileSrc) > 3) {
					$filesArr[$fileSrc] = $fileDst;
				}
			}
		}
		return $filesArr;
	}

	private function getACL($node) {
		$aclData = array();
		if (isset($node->acl)) {
			if (isset($node->acl->object)) {
				foreach ($node->acl->object as $aclobj) {
					$aclName = strval($aclobj['name']);
					$aclDescr = strval($aclobj);
					if (strlen($aclName) > 1) {
						$aclData[$aclName] = $aclDescr;
					}
				}
			}
		}
		return $aclData;
	}

	private function load() {
		if (file_exists($this->_path)) {
			// Load
			$this->_xml = simplexml_load_file($this->_path);
			if ($this->_xml) {
				// Type
				$this->type = $this->getAttrXML($this->_xml,"type");
				$this->update = ((string)$this->getAttrXML($this->_xml,"update","false")==="true" ? true : false);
				// Name, version
				$this->subname = strtolower($this->getValueXML($this->_xml,"subname"));
				$this->name = strtolower($this->getValueXML($this->_xml,"name"));
				$this->version = (string)$this->getValueXML($this->_xml,"version");
				$this->description = (string)$this->getValueXML($this->_xml,"description");
				$this->author = (string)$this->getValueXML($this->_xml,"author");
				$this->email = (string)$this->getValueXML($this->_xml,"email");
				$this->site = (string)$this->getValueXML($this->_xml,"site");
				$this->license = (string)$this->getValueXML($this->_xml,"license");
				
				$this->dependsOn = $this->getDependences();
				$this->addons	=	$this->getAddonDependences();
				
				// Check for frontent descriptor
				if ((!isset($this->_xml->frontend))&&(!isset($this->_xml->backend))) {
					$this->error(Text::_('Nothing to install')); return false;
				}
				$this->feRoot = $this->getAttrXML($this->_xml->frontend,"root","frontend");
				$this->beRoot = $this->getAttrXML($this->_xml->backend,"root","backend");
				// get style files

				if (isset($this->_xml->css)){
					$this->cssRoot = $this->getAttrXML($this->_xml->css,"root","css");
					$this->cssFiles = $this->getFiles($this->_xml->css);
					$this->cssDirs = $this->getDirectories($this->_xml->css);
				}
				// get js files
				if (isset($this->_xml->js)){
					$this->jsRoot = $this->getAttrXML($this->_xml->js,"root","js");
					$this->jsFiles = $this->getFiles($this->_xml->js);
					$this->jsDirs = $this->getDirectories($this->_xml->js);
				}
				
				if (isset($this->_xml->redistribution)){
					$this->redistributionRoot = $this->getAttrXML($this->_xml->redistribution,"root","redistribution");
					$this->redistributionFiles = $this->getFiles($this->_xml->redistribution);
					$this->redistributionDirs = $this->getDirectories($this->_xml->redistribution);
				}
				if (isset($this->_xml->packages)){
					$this->packagesRoot = $this->getAttrXML($this->_xml->packages,"root","packages");
					$this->packagesFiles = $this->getFiles($this->_xml->packages);
					$this->packagesDirs = $this->getDirectories($this->_xml->packages);
				}
				
				// Get ACL
				$this->feACL = $this->getACL($this->_xml->frontend);
				$this->beACL = $this->getACL($this->_xml->backend);


				// Get files
				$this->feFiles = $this->getFiles($this->_xml->frontend);
				$this->beFiles = $this->getFiles($this->_xml->backend);

				// Get directories
				$this->feDirs = $this->getDirectories($this->_xml->frontend);
				$this->beDirs = $this->getDirectories($this->_xml->backend);

				// Get menus
				$this->feMenus = $this->getMenus($this->_xml->frontend);
				$this->beMenus = $this->getMenus($this->_xml->backend);
				
				// Get config
				if (isset($this->_xml->params)) {
					if (isset($this->_xml->params->param)) {
						foreach ($this->_xml->params->param as $cvar) {
							$cvarName = strval($cvar['name']);
							if (strlen($cvarName) > 1) {
								$this->params[$cvarName] = strval($cvar);
							}
						}
					}
				}
				$this->queries = $this->getQueries();
				$this->unqueries = $this->getUninstallQueries();
				
				// Get language
				if (isset($this->_xml->language)) {
					foreach ($this->_xml->language as $language) {
						$langName = strval($language['name']);
						if (strlen($langName) == 2) {
							$this->language[$langName] = strval($language);
						}
					}
				}
				return true;
			}
			else return false;
		}
		else return false;
	}

	//-------------------------------------------------------

	public function isLoaded() {
		return $this->_isLoaded;
	}
	
	public function xmlData() {
		return $this->_xml->asXML();
	}
	
}

?>