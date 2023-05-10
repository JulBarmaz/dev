<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class Plugin extends BaseObject {
	private static $_loadedPlugins	= array();
	private static $_css			= array();
	private static $_js			= array();
	
	private static $_installedPlugins	= array();
	private static $_corruptedPlugins	= array();

	protected $_events=array();
//	protected $_loadedConfig		= array();
	protected $_paramsMask	= array(); // vtype, vdefault, fill_default, source, descr

	protected function addParam($name, $vtype, $vdefault, $fill_default=false, $source=null, $description=null){
		$this->_paramsMask[$name]["vtype"]=$vtype;
		$this->_paramsMask[$name]["vdefault"]=$vdefault;
		$this->_paramsMask[$name]["fill_default"]=$fill_default;
		if(!is_null($description)) $this->_paramsMask[$name]["descr"]=$description;
		if(!is_null($source)) $this->_paramsMask[$name]["source"]=$source;
	}
	protected function setParamsMask(){
		// Just for overriding
	}
	public function getParam($param_name, $subs_default_value=true, $custom_default_value=null) {
		if(!count($this->_paramsMask)) $this->setParamsMask();
		// true - something
		if($subs_default_value && !is_null($custom_default_value)) return $this->get($param_name, $custom_default_value, (isset($this->_paramsMask[$param_name]["fill_default"]) && $this->_paramsMask[$param_name]["fill_default"]));
		// false - something
		if(!$subs_default_value && !is_null($custom_default_value)) return $this->get($param_name, $custom_default_value, true);
		// true - null
		// isset($this->_paramsMask[$param_name]["vdefault"]) is nesessary because in Plugin executeEvent function added some $params without vdefault
		if($subs_default_value && isset($this->_paramsMask[$param_name]["vdefault"])) return $this->get($param_name, $this->_paramsMask[$param_name]["vdefault"], (isset($this->_paramsMask[$param_name]["fill_default"]) && $this->_paramsMask[$param_name]["fill_default"]));
		// false - null
		return $this->get($param_name);
	}
/*
	public function getParam($param_name, $subs_default_value=true, $custom_default_value=null) {
		if(!count($this->_paramsMask)) $this->setParamsMask();
		if($subs_default_value && isset($this->_paramsMask[$param_name]["vdefault"])) return $this->get($param_name, $this->_paramsMask[$param_name]["vdefault"], (isset($this->_paramsMask[$param_name]["fill_default"]) && $this->_paramsMask[$param_name]["fill_default"]));
		if(!$subs_default_value && !is_null($custom_default_value)) return $this->get($param_name, $custom_default_value, (isset($this->_paramsMask[$param_name]["fill_default"]) && $this->_paramsMask[$param_name]["fill_default"]));
		return $this->get($param_name);
	}
*/
	public static function initialize() {
		Debugger::getInstance()->milestone("Loading plugins");
		$db = Database::getInstance();
		$db->setQuery("SELECT * FROM #__plugins WHERE p_deleted=0 ORDER by p_ordering");
		$_installedPlugins = $db->loadObjectList();
		if (count($_installedPlugins)) {
			foreach ($_installedPlugins as $plg) {
				self::initializePlugin($plg);
			}
			Debugger::getInstance()->milestone("Plugins loaded");
		}
	}
	private static function initializePlugin($plg=null) {
		if($plg===null) return false;
		$name=$plg->p_path.".".$plg->p_name;
		$pluginScriptPath = PATH_PLUGINS.$plg->p_path.DS.$plg->p_name.'.php';
		if (file_exists($pluginScriptPath)) {
			require_once $pluginScriptPath;
			$pluginClass = $plg->p_path.'Plugin'.$plg->p_name;
			if (class_exists($pluginClass,0)) {
				if ($plg->p_enabled){
					Text::parsePlugin($name);
					self::$_loadedPlugins[$name] = new $pluginClass($name,$plg->p_params);
					self::$_css[$name]="plugins/".$plg->p_path."/".$plg->p_name.".css";
					self::$_js[$name]="plugins/".$plg->p_path."/".$plg->p_name.".js";
				} else {
					Text::parsePlugin($name);
					self::$_installedPlugins[$name] = new $pluginClass($name,$plg->p_params, 0);
				}
			} else {
				self::$_corruptedPlugins[$name] = true;
				Debugger::getInstance()->warning("Plugin class not loaded : ".$pluginClass);
				return false;
			}
		} else {
			self::$_corruptedPlugins[$name] = true;
			Debugger::getInstance()->warning("Plugin not found => ".$pluginScriptPath);
			return false;
		}
		return true;
	}
	public static function isInstalled($path,$name) {
		$pname=$path.".".$name;
		return (array_key_exists($pname,self::$_installedPlugins) || self::isLoaded($pname));
	}
	public static function isLoaded($name) {
		return array_key_exists($name,self::$_loadedPlugins);
	}
	public static function loadAssets() {
		foreach(self::$_css as $css){
			Portal::getInstance()->addStyleSheet($css, !seoConfig::$tmplCSSBackCompatibility);
		}
		foreach(self::$_js as $js){
			Portal::getInstance()->addScript($js);
		}
		
	}
	public static function isCorrupted($path,$name) {
		$pname=$path.".".$name;
		return array_key_exists($pname,self::$_corruptedPlugins);
	}
	public static function getInstance($name, $check_all=0) {
		if (self::isLoaded($name)) {
			return self::$_loadedPlugins[$name];
		}	else {
			if ($check_all) {
				if (array_key_exists($name,self::$_installedPlugins)) {
					return self::$_installedPlugins[$name];
				}	else {
					Util::fatalError("Plugin not installed => ".$name); // Realy fatal. Method is static.
				}
			} else Util::fatalError("Plugin not loaded => ".$name); // Realy fatal. Method is static.
		}
	}
	private function __construct($pluginName, $params="", $force_events=1) {
		$this->initObj($pluginName);
		if ($force_events) $this->registerEvents();
//		$this->setDefaultParams($params);
		$this->loadConfig($params);
	}
	private function loadConfig($params){
		$paramsMask=$this->getParamsMask();
		$configArray=Params::parse($params);
		if (is_array($configArray) && count($configArray)) {
			foreach ($configArray as $configKey=>$configVal) {
				if (array_key_exists($configKey, $paramsMask)) {
					$this->set($configKey, $configVal);
				}
			}
		}
	}
/*
	private function setDefaultParams($params){
		// @TODO возможно нужно переделать под новое получение параметров
		$paramsMask=$this->getParamsMask();
		if (count($paramsMask)){
			foreach($paramsMask as $key=>$val) {
				$this->set($key,$val['vdefault']);
				$this->_loadedConfig[$key] = $val['vdefault'];
			}
		}
		$configArray=Params::parse($params);
		if (count($configArray)) {
			foreach ($configArray as $configKey=>$configVal) {
				if (array_key_exists($configKey,$paramsMask)) {
					$this->set($configKey,$configVal);
					$this->_loadedConfig[$configKey] = $configVal;
				}
			}
		}
	}
*/
	public function executeEvent($event, $params=array(), &$data) {
		// @TODO Возможно здесь это уже излишне
		/*
		if (count($this->_loadedConfig)) {
			foreach($this->_loadedConfig as $k=>$v){
				$this->set($k,$v);
			}
		}
		*/
		// А это нужно оставить
		foreach ($params as $key=>$value) {
			$this->set($key, $value);
		}
		return $this->onRaise($event, $data);
	}
	protected function registerEvents(){
		foreach($this->_events as $_event){
			Event::register($_event,$this->getName());
		}
	}
	public function getParamsMask(){
		if(!count($this->_paramsMask)) $this->setParamsMask();
		return $this->_paramsMask;
	}
	protected function onRaise($event, &$data) {
		// Just for overriding
	}
}
?>