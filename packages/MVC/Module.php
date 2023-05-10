<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class Module extends BaseObject {
	//--------------- Factory implementation --------------------
	private static $_loadedModules	= array();
	private static $_loadMode		= 'main';
	private static $_path_name= '';
	private static $_replace_name= array();
	private static $coreModules		= array('aclmgr','conf','user');
	protected $_paramsMask	= array(); // vtype, vdefault, fill_default, source, descr
	// private static $_config		= null;
	private $_config		= array();
	private static $_helpers	= array();
	
	public function addParam($name, $vtype, $vdefault, $fill_default=false, $source=null, $description=null){
		$this->_paramsMask[$name]["vtype"]=$vtype;
		$this->_paramsMask[$name]["vdefault"]=$vdefault;
		$this->_paramsMask[$name]["fill_default"]=$fill_default;
		if(!is_null($description)) $this->_paramsMask[$name]["descr"]=$description;
		if(!is_null($source)) $this->_paramsMask[$name]["source"]=$source;
	}
	protected function setParamsMask(){
		if (array_key_exists($this->getName(),self::$_replace_name) == false) {
			self::$_replace_name[$this->getName()]=self::getReplaceModule($this->getName());
		}
		$patr=self::$_replace_name[$this->getName()];
		//$patr=  self::$_path_name ? self::$_path_name :$this->getName();
		$views_front = Files::getFolders((defined("_ADMIN_MODE") ? PATH_FRONT_MODULES : PATH_MODULES).DS.$patr.DS."views".DS."template", array(".svn",".git",".",".."), false);
		if(is_array($views_front)){
			$views_front_keys = array_keys($views_front); 
			$views_front_arr = array_combine($views_front_keys, $views_front_keys);
		} else {
			// $views_front_arr = array(""=>" --- ".Text::_("Not selected")." --- ");  // With default value
			$views_front_arr = array(); // Without default value
		}
		$views_admin = Files::getFolders((defined("_ADMIN_MODE") ? PATH_MODULES : PATH_ADMIN_MODULES).DS.$patr.DS."views".DS."template", array(".svn",".git",".",".."), false);
		if(is_array($views_admin)){
			$views_admin_keys = array_keys($views_admin);
			$views_admin_arr = array_combine($views_admin_keys, $views_admin_keys);
		} else {
			// $views_admin_arr = array(""=>" --- ".Text::_("Not selected")." --- "); // With default value
			$views_admin_arr = array(); // Without default value
		}
		$this->addParam("Page_size", "integer", siteConfig::$recordsPerPage, true);
		// $this->addParam("Default_view", "select", array_values($views_front_arr)[0], true, $views_front_arr); // With default value
		$this->addParam("Default_view", "select", "", true, $views_front_arr); // Without default value
		$this->addParam("Default_item_ID", "integer", 0, true);
		$this->addParam("Admin_page_size", "integer", adminConfig::$adminRecordsPerPage, true);
		// $this->addParam("Admin_default_view", "select", array_values($views_admin_arr)[0], true, $views_admin_arr); // With default value
		$this->addParam("Admin_default_view", "select", "", true, $views_admin_arr); // Without default value
//		$this->addParam("Admin_default_item_ID", "integer", 0, true);
		$this->addParam("Default module meta title", "string", "");
		$this->addParam("Default module meta description", "string", "");
		$this->addParam("Default module meta keywords", "string", "");
		if(defined("_ADMIN_MODE")) $params_file = PATH_MODULES.$patr.DS.'params.php';
		else $params_file = PATH_ADMIN_MODULES.$patr.DS.'params.php';
		if(is_file($params_file)) {
			$className=$this->getName()."ModuleParams";
			require_once $params_file;
			$className::_proceed($this);
		}
	}
	public function getParamsMask(){
		//if(!count($this->_paramsMask)) $this->setParamsMask();
		return $this->_paramsMask;
	}
	
	public function getACLTemplate($is_admin=true) {
		return array();
	}
	
	public function getLinksArray(&$counter,&$arr) {
		$counter++;
		$module=$this->getName();
		$arr[$module][$counter]['link']=Router::_("index.php?module=".$module, true);
		$arr[$module][$counter]['name']=Text::_($module);
		$arr[$module][$counter]['fullname']=Text::_($module);
		return true;	
	}
	public function getSitemapHTML() { return array("html"=>"", "title_link"=>false, "links"=>array()); }
	
	public static function getInstance($name='', $force_api = false) {
		if (!$name) $name = Registry::getInstance()->get('module');
		// name must be equal to [a-zA-Z0-9_]
		if(!preg_match('/^[\w]+$/', $name)) Util::redirect(Router::_("index.php"), Text::_("Page not found").(defined("_ADMIN_MODE")||siteConfig::$debugMode ? " => ".$name : ""), "404");
		// Check and load
		if (!array_key_exists($name, self::$_loadedModules)) {
			// Check in database and get it
			$db=Database::getInstance();
			$db->setQuery("SELECT * FROM #__modules WHERE m_name='".$name."' AND m_deleted=0");
			$moduleData=$db->loadObjectList();
			if (count($moduleData)!=1) {
				if($name==siteConfig::$defaultModule) $this->fatalError(Text::_("Module not installed")." => ".$name, defined("_ADMIN_MODE") ? "503" : "404"); // Realy fatal if default, else redirect already set in next lines.
				else Util::redirect(Router::_("index.php"), Text::_("Module not installed")." => ".$name, "404");
			}
			if(!$moduleData[0]->m_enabled && !defined("_ADMIN_MODE") && !$force_api && self::$_loadMode=='main' && !self::isCoreModule($name)) Util::redirect("/",Text::_("Module disabled")." => ".$name." (".self::$_loadMode.")", "404");
			// Module main script path
			/* @TODO вот тут - самое место для замены пути к модулям
			* логика : у нас другой код другой модуль - имеет свою папку хранения
			* заменяющий базовую поставку подставляем физический путь другой
			* при этом ссылки сотаются прежними
			*/
			//$replace_name=$moduleData[0]->m_replace_name;
			//self::$_path_name=$moduleData[0]->m_replace_name;
			//$this->_path_name=$moduleData[0]->m_replace_name;
			if (array_key_exists($name,self::$_replace_name) == false) {
				self::$_replace_name[$name]=self::getReplaceModule($name);
			}
			$path_name=self::$_replace_name[$name];
			$moduleScriptPath = PATH_MODULES.$path_name.DS.'module.php';
			if (!file_exists($moduleScriptPath)) {
				$moduleScriptPath = PATH_MODULES.$path_name.DS.$name.'.php';
				if (!file_exists($moduleScriptPath)) {
					if (defined("PATH_FRONT_MODULES")) {
						$moduleScriptPath = PATH_FRONT_MODULES.$path_name.DS.'module.php';
						if (!file_exists($moduleScriptPath)) {
							$moduleScriptPath = PATH_FRONT_MODULES.$path_name.DS.'module.php';
							if (!file_exists($moduleScriptPath)) {
								if($name==siteConfig::$defaultModule){
									$bas= new parent;
									$bas->fatalError(Text::_("Module not installed")." => ".$name, defined("_ADMIN_MODE") ? "503" : "404"); // Realy fatal if default, else redirect already set in next lines.
								}
								else Util::redirect(Router::_("index.php"), Text::_("Module front not found")." => ".$moduleScriptPath.' => '.$name, "404");
							}
						}
					} else {
						if($name==siteConfig::$defaultModule) $this->fatalError(Text::_("Module not found")." => ".$moduleScriptPath.' => '.$name, "404"); // Realy fatal if default, else redirect already set in next lines.
						else Util::redirect(Router::_("index.php"), Text::_("Module not found")." => ".$moduleScriptPath.'=>'.$name, "404");
					}
				}
			}
			// Load language file
			if (!defined("_ADMIN_MODE")) Text::parseModule($path_name);
			Debugger::getInstance()->milestone("Including (require_once) ".$moduleScriptPath);
			require_once $moduleScriptPath;
			Debugger::getInstance()->milestone("Included (require_once) ".$moduleScriptPath);
			$moduleClass = $name.'Module';
			self::$_loadedModules[$name] = new $moduleClass($name, ($force_api ? "api" : self::$_loadMode), $moduleData[0]);
			if(!$force_api) self::$_loadMode = 'api';
		}
		return self::$_loadedModules[$name];
	}

	//-----------------------------------------------------------
	public static function isCoreModule($moduleName) {
		return in_array($moduleName, self::$coreModules);
	}
	public static function isInstalled($moduleName) {
		$db=Database::getInstance();
		$db->setQuery("SELECT COUNT(m_id) FROM #__modules WHERE m_name='".$moduleName."'");
		if ($db->loadResult()!=1) return false;
		return true;
	}

	public static function getInstalledModules($forMap=false, $enabledOnly=false) {
		// Check in database
		$db=Database::getInstance();
		$sql="SELECT DISTINCT m_name FROM #__modules WHERE m_deleted=0";
		if ($forMap) $sql.=" AND m_incl_map=1";
		if($enabledOnly) $sql.=" AND m_enabled=1";
		$db->setQuery($sql);
		return $db->loadResultArray();
	}


	public static function getHelper($helperName, $moduleName="", $forceFront=false) {
		if(!$moduleName) $moduleName=Module::getInstance()->getName();
		if (array_key_exists($helperName,self::$_helpers) == false) {
			if (array_key_exists($moduleName,self::$_replace_name) == false) {
				self::$_replace_name[$moduleName]=self::getReplaceModule($moduleName);
			}
			$path_name=self::$_replace_name[$moduleName];
			
			//$path_name=self::getReplaceModule($moduleName);	
			//if(self::$_path_name!='') $path_name=self::$_path_name; 
			if(defined("_ADMIN_MODE") && $forceFront) $helperScriptPath = PATH_FRONT_MODULES.$path_name.DS.'helpers'.DS.$helperName.'.php';
			else $helperScriptPath = PATH_MODULES.$path_name.DS.'helpers'.DS.$helperName.'.php';
			if (!file_exists($helperScriptPath)) {
				Util::fatalError(Text::_("Helper not found")." => ".$path_name.".".$helperName."(".$helperScriptPath.")", 404); // May be realy fatal. Method is static.
			}

			require_once $helperScriptPath;

			$helperClass = $moduleName.'Helper'.ucfirst($helperName);
			self::$_helpers[$helperName] = new $helperClass();
		}

		return self::$_helpers[$helperName];
	}
	/**
	 * название заменяемого штатного модуля, для определения пути к файлам
	 * @param string $module_name - имя штатного модуля
	 * @return NULL|string - пусто или имя заменяемого модуля
	 */
	
	public static function getReplaceModule($module_name)
	{
			if($module_name){
				Database::getInstance()->setQuery("select m_replace_name from #__modules where m_name='".$module_name."'");
				$pathl=Database::getInstance()->loadResult();
				if($pathl) return $pathl;
			}
			return $module_name;
	}

	public static function initModuleClass($className,$moduleName,$forceFront=false,$fatal=true) {
		  if(defined("_ADMIN_MODE") && $forceFront) $scriptPath = PATH_FRONT_MODULES.$moduleName.DS.'classes'.DS.$className.'.php';
		  else $scriptPath = PATH_MODULES.$moduleName.DS.'classes'.DS.$className.'.php';
			if (!file_exists($scriptPath)) {
				if($fatal) Util::fatalError(Text::_("Module class not found")." => ".$moduleName.".".$className."(".$scriptPath.")", 404); // May be realy fatal, if critical flag true. Method is static.
				else return false;
			}
   		require_once $scriptPath;
      return true; 
	}
	
	//-----------------------------------------------------------

	protected $_controller		= null;
	private $_controllerName	= '';
	private $_defaultViewName	= 'default';
	private $_module_id			= 0;
	private $_models			= array();
	private $_showBreadcrumb	= false;
	private $_disabled			= '';

	public function __construct($name,$mode,$data) {
		Debugger::getInstance()->milestone("Module constructor start => ".$name);
		$this->initObj($name);
		$_aclObject = ACLObject::getInstance($name.'Module'); // canAccess() will be later
		if (!$_aclObject->canAccess()) { // DON'T move down and check only for "main", not for api. Both must be checked. Or it would be a hole.
			if ($name==siteConfig::$defaultModule) $this->fatalError(Text::_('Permission is absent for')." ".$_aclObject->getDescription()); // Realy fatal. Access to default module absent.
			else {
				if ((siteConfig::$debugMode && User::getInstance()->isAdmin()) || siteConfig::$debugMode>100) $this->fatalError(Text::_('Permission is absent for')." ".$_aclObject->getDescription(), defined("_ADMIN_MODE") ? "503" : "404"); // Render fatal for admin debug.
				else Util::redirect(Router::_("index.php"), Text::_('Permission is absent for')." ".$_aclObject->getDescription(), "404");
			}
		}	else {
			$config = $data->m_config;
			$this->_module_id = $data->m_id;
			$this->_showBreadcrumb = $data->m_show_breadcrumb;
			if ($mode == 'main') {
				$reestr = new FieldSet();
				$this->set('reestr',$reestr);
				$this->loadConfig($config);
				$this->prepare();
				$this->setDefaults();
				$this->initData();
				$this->getController();
			}	else {
				$this->loadConfig($config);
			}
			$this->milestone("Module loaded => ".$name, __FUNCTION__);
		}
	}

	private function initData() {
		$controller	= Request::getSafe('controller', 'default');
		$task		= Request::getSafe('task', 'show');
		$view		= Request::getSafe('view', $this->_defaultViewName);
		$layout		= Request::getSafe('layout', 'default');

		$this->set('controller', $controller, true);
		$this->set('task', $task, true);
		$this->set('view', $view, true);
		$this->set('layout', $layout, true);
	}

	private function loadConfig($config) {
		$this->setParamsMask();
		$params=Params::parse($config);
		if(is_array($params)) $this->_config = $params; else $this->_config = array();
	}

	protected function setDefaults() {
		if(defined("_ADMIN_MODE")) $default_view = $this->getParam("Admin_default_view");
		else $default_view = $this->getParam("Default_view");
		if($default_view) $this->_defaultViewName = $default_view;
	}
	
	public function getController() {
		// Load controller script
		$controllerName = $this->get('controller');
		if ($controllerName != $this->_controllerName) {
			if (array_key_exists($this->getName(),self::$_replace_name) == false) {
				self::$_replace_name[$this->getName()]=self::getReplaceModule($this->getName());
			}
			$module_name=self::$_replace_name[$this->getName()];
			
			//$module_name=self::getReplaceModule($this->getName());
			$path = PATH_MODULES.$module_name.DS.'controllers'.DS.$controllerName.'.php';
			if (!file_exists($path)) {
				if($controllerName=="default") {
					$this->fatalError(Text::_("Controller not found")." => ".$this->getName().".".$controllerName); // Realy fatal if default, else redirect already set in next lines.
				} else {
					if ((siteConfig::$debugMode && User::getInstance()->isAdmin()) || siteConfig::$debugMode>100) $this->fatalError(Text::_("Controller not found")." => ".$this->getName().".".$controllerName, defined("_ADMIN_MODE") ? "503" : "404"); // Render fatal for admin debug.
					else Util::redirect(Router::_("index.php"), Text::_("Controller not found")." => ".$this->getName().".".$controllerName, "404");
				}
			}
			Debugger::getInstance()->milestone("Including (require_once) ".$path );
			require_once $path;
			Debugger::getInstance()->milestone("Included (require_once) ".$path );
			$this->_controllerName = $controllerName;
			$controllerClass = $this->getName().'Controller'.$controllerName;
			$this->_controller = new $controllerClass($controllerName,$this);
		}		
		return $this->_controller;
	}
	public function getPathName()
	{
		return $this->_path_name;
	}
	
	public function getModel($modelName="") {
		$moduleName = $this->getName();
		$viewName = $this->get('view');
		if (!$modelName) $modelName = $viewName;
		if (array_key_exists($moduleName,self::$_replace_name) == false) {
			self::$_replace_name[$moduleName]=self::getReplaceModule($moduleName);
		}
		$path_name=self::$_replace_name[$moduleName];
		//$path_name=self::getReplaceModule($moduleName);
		if (array_key_exists($modelName,$this->_models) == false) {
			$this->milestone("Model load attempt => MODULE=&quot;".$moduleName."&quot; MODEL=&quot;".$modelName."&quot; VIEW=&quot;".$viewName."&quot;", __FUNCTION__);
			$path = PATH_MODULES.$path_name.DS.'models'.DS.$modelName.'.php';
			if (!file_exists($path)) {
				// FIXME убрать поиск моделей и т.д на фронтенде
				if (defined("PATH_FRONT_MODULES"))	{
					$path = PATH_FRONT_MODULES.$path_name.DS.'models'.DS.$modelName.'.php';
					if (!file_exists($path)) $this->fatalError(Text::_("Model front not found")." => ".$path_name.'--'.$modelName); // Realy fatal, but this is "_ADMIN_MODE".
				} else {
					if($this->getDefaultView() == $viewName){
						$this->fatalError(Text::_("Model not found")." => ".$moduleName."(".$path_name.").".$modelName); // Realy fatal if default, else redirect already set in next lines.
					} else {
						if ((siteConfig::$debugMode && User::getInstance()->isAdmin()) || siteConfig::$debugMode>100) $this->fatalError(Text::_("Model not found")." => ".$moduleName."(".$path_name.").".$modelName, defined("_ADMIN_MODE") ? "503" : "404"); // Render fatal for admin debug.
						else Util::redirect(Router::_("index.php"), Text::_("Model not found")." => ".$moduleName."(".$path_name.").".$modelName, "404");
					}
				}
			}
			require_once $path;
			$modelClass = $moduleName.'Model'.$modelName;
			$this->_models[$modelName] = new $modelClass($this);
		}
		return $this->_models[$modelName];
	}

	public function getConfig() {
		return $this->_config;
	}
	
	public function getID() {
		return $this->_module_id;
	}
	public function getParam($param_name, $subs_default_value=true, $custom_default_value=null) {
		if (array_key_exists($param_name, $this->_config) && isset($this->_paramsMask[$param_name])) {
			switch($this->_paramsMask[$param_name]["vtype"]){
				case "boolean": // тут возвращаем сразу
					if($this->_config[$param_name]) return 1;
					else return 0;
					break;
				case "integer":
					$val = intval($this->_config[$param_name]);
					break;
				case "float":
					$val = floatval($this->_config[$param_name]);
					break;
				case "table_select":
				case "select":
				case "string":
				case "text":
				case "ro_string":
				case "title":
				default: // это все считаем строками
					$val = $this->_config[$param_name];
					break;
			}
			if(!$val && isset($this->_paramsMask[$param_name]["fill_default"]) && $this->_paramsMask[$param_name]["fill_default"]){
				if(!is_null($custom_default_value)) return $custom_default_value;
				elseif ($subs_default_value && isset($this->_paramsMask[$param_name]["vdefault"])) return $this->_paramsMask[$param_name]["vdefault"];
			}
			return $val;
		} else {
			if ($subs_default_value && isset($this->_paramsMask[$param_name]["vdefault"])) return $this->_paramsMask[$param_name]["vdefault"];
			elseif(!is_null($custom_default_value)) return $custom_default_value;
			else return "";
		}
	}
	/**
	* @deprecated From revision 1508. Use getParam instead
	*/
	public function getConfigValue($key, $userDefaultValue='', $emty2default=false) {
		if (array_key_exists($key, $this->_config)) {
			if (!$this->_config[$key] && $emty2default) return $userDefaultValue;
			else return $this->_config[$key];
		}	else {
			return $userDefaultValue;
		}
	}
	
	public function setBreadCrumbVisibility($status=1) {
		$this->_showBreadcrumb=$status;
	}

	public function showBreadcrumb() {
		return $this->_showBreadcrumb;
	}

	//-------------------- Interface methods --------------------
	public function prepare() {
		// Just for overriding
	}

	public function render() {
		// Javascript for module
		if (!Portal::getInstance()->isDisabled() && !Portal::getInstance()->inPrintMode()) { Portal::getInstance()->addScript("modules/".$this->getName().".js"); }
		if($this->_disabled) echo $this->_disabled;
		else $this->_controller->executeTask();
	}

	public function ajax() {
		if($this->_disabled) echo $this->_disabled;
		else $this->_controller->executeAjax();
	}

	public function getBreadCrumbArray() {
		return $this->_controller->getView()->getBreadCrumbArray();
	}

	public function renderBreadCrumb($force=false) {
		if ($this->showBreadcrumb()||$force) return $this->_controller->getView()->renderBreadCrumb();
		else return "";
	}
	
	public function setDefaultView($viewName) {
		$this->_defaultViewName = $viewName;
	}

	public function getDefaultView() {
		return $this->_defaultViewName;
	}

	public function _disable($msg="Module disabled"){
		if (!$this->_disabled) $this->_disabled="<div id=\"disabled-message\">".$msg."</div>";
	}
}

?>