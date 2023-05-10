<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class Widget extends BaseObject {

	private $_models		= array();
	private $_data			= null;
	protected $_requiredModules = array();
	protected $_requiredDisabledModules = array();
	protected $_paramsMask	= array(); // vtype, vdefault, fill_default, source, descr
	protected $_hide_content_param = false; // Hide content field while modifying 
	
	private static $_installedWidgets	= array();
	private static $_widgets			= array();
	
	public function getRequiredDisabledModules(){
		return $this->_requiredDisabledModules;
	}
	public function hideContentParam(){
		return $this->_hide_content_param;
	}
	protected function setRequiredDisabledModules(){
		if(count($this->_requiredModules)){
			$this->_requiredDisabledModules = array_intersect($this->_requiredModules, Portal::getInstance()->getDisabledModules());
		}
	}
	protected function addParam($name, $vtype, $vdefault, $fill_default=false, $source=null, $description=null){
		$this->_paramsMask[$name]["vtype"]=$vtype;
		$this->_paramsMask[$name]["vdefault"]=$vdefault;
		$this->_paramsMask[$name]["fill_default"]=$fill_default;
		if(!is_null($description)) $this->_paramsMask[$name]["descr"]=$description;
		if(!is_null($source)) $this->_paramsMask[$name]["source"]=$source;
	}
	protected function setParamsMask(){
		$this->addParam("Widget_ID", "string", "");
		$this->addParam("Title_tag", "select", "div", true, SpravStatic::getCKArray("title_tags"));
	}
	public function getParamsMask(){ 
//		Debugger::getInstance()->milestone("Widget ".$this->getName()." getParamsMask (start)");
		if(!count($this->_paramsMask)) $this->setParamsMask();
		Event::raise("widget.getParamsMask", array("widgetName"=>$this->getName()), $this->_paramsMask);
//		Debugger::getInstance()->milestone("Widget ".$this->getName()." getParamsMask (stop)");
		return $this->_paramsMask;
	}
	public static function initialize() {
		$db = Database::getInstance();
		if (defined("_ADMIN_MODE")) $db->setQuery("SELECT * FROM #__widgets ORDER BY w_name");
		else $db->setQuery("SELECT * FROM #__widgets WHERE w_side=1 ORDER BY w_name");
		self::$_installedWidgets = $db->loadObjectList("w_name");
	}
	public static function getInstance($widgetName, $base_widget=false) {
		if (array_key_exists($widgetName,self::$_widgets) == false) {
			if (array_key_exists($widgetName,self::$_installedWidgets)) {
				// $widgetTemplateScriptPath = Portal::getInstance()->getTemplatePath().'widgets'.DS.$widgetName.DS.'template.php';
				if(defined("_ADMIN_MODE") && !$base_widget && self::$_installedWidgets[$widgetName]->w_side == 1){
					/*
					$templateName = siteConfig::$siteTemplate;
					$templateDir = PATH_FRONT_TEMPLATES.$templateName.DS;
					$widgetTemplateScriptPath = $templateDir.'widgets'.DS.$widgetName.DS.'template.php';
					*/
					$widgetTemplateScriptPath = PATH_FRONT_TEMPLATES.siteConfig::$siteTemplate.DS.'widgets'.DS.$widgetName.DS.'template.php';
				} else {
					$widgetTemplateScriptPath = Portal::getInstance()->getTemplatePath().'widgets'.DS.$widgetName.DS.'template.php';
				}
				Debugger::getInstance()->milestone("Widget template load attempt (template) - ".$widgetName." - &gt; ".$widgetTemplateScriptPath);
				if (!$base_widget && file_exists($widgetTemplateScriptPath)) {
					// If we loaded template.php, then we don't need to search widget default.php in template. 
					$widgetScriptPath = PATH_WIDGETS.$widgetName.DS.'default.php';
					Debugger::getInstance()->milestone("Base widget load attempt - ".$widgetName." - &gt; ".$widgetScriptPath);
					if (!file_exists($widgetScriptPath) && DEFINED('PATH_FRONT_WIDGETS')) { // искали виджет в админке и не нашли
						$widgetScriptPath = PATH_FRONT_WIDGETS.$widgetName.DS.'default.php';
						Debugger::getInstance()->message("Base widget load attempt (front) - ".$widgetName." - &gt; ".$widgetScriptPath);
					}
					if (!file_exists($widgetScriptPath)) {
						Util::fatalError(Text::_("Widget not found")." => ".$widgetName); // Realy fatal. Method is static.
					}
					require_once $widgetScriptPath;
					require_once $widgetTemplateScriptPath;
					$widgetClass = $widgetName.'WidgetTmpl';
				} else {
					if(!$base_widget){
						//$widgetScriptPath = Portal::getInstance()->getTemplatePath().'widgets'.DS.$widgetName.DS.'default.php';
						if(defined("_ADMIN_MODE") && !$base_widget && self::$_installedWidgets[$widgetName]->w_side == 1){
							/*
							$templateName = siteConfig::$siteTemplate;
							$templateDir = PATH_FRONT_TEMPLATES.$templateName.DS;
							$widgetScriptPath = $templateDir.'widgets'.DS.$widgetName.DS.'default.php';
							*/
							/*************************************************************************/
							/*** USE WITH CAUTION !!! FULL OVERRIDES ALL OPTIONS AND FUNCTIONS !!! ***/
							/*************************************************************************/
							$widgetScriptPath = PATH_FRONT_TEMPLATES.siteConfig::$siteTemplate.DS.'widgets'.DS.$widgetName.DS.'default.php';
						} else {
							$widgetScriptPath = Portal::getInstance()->getTemplatePath().'widgets'.DS.$widgetName.DS.'default.php';
						}
						Debugger::getInstance()->message("Widget load attempt (template) - ".$widgetName." - &gt; ".$widgetScriptPath);
					}
					if ($base_widget || !file_exists($widgetScriptPath)) {
						$widgetScriptPath = PATH_WIDGETS.$widgetName.DS.'default.php';
						Debugger::getInstance()->milestone("Widget load attempt - ".$widgetName." - &gt; ".$widgetScriptPath);
						if (!file_exists($widgetScriptPath) && DEFINED('PATH_FRONT_WIDGETS')) { // искали виджет в админке и не нашли
							$widgetScriptPath = PATH_FRONT_WIDGETS.$widgetName.DS.'default.php';
							Debugger::getInstance()->message("Widget load attempt (front) - ".$widgetName." - &gt; ".$widgetScriptPath);
						}
						if (!file_exists($widgetScriptPath)) {
							Util::fatalError(Text::_("Widget not found")." => ".$widgetName); // Realy fatal. Method is static.
						}
					}
					require_once $widgetScriptPath;
					$widgetClass = $widgetName.'Widget';
				}
				self::$_widgets[$widgetName] = new $widgetClass($widgetName,self::$_installedWidgets[$widgetName]);
			}	else Util::fatalError(Text::_('Widget is not installed').": $widgetName"); // Realy fatal. Method is static.
		}
		return self::$_widgets[$widgetName];
	}
	public static function isInstalled($widgetName) {
		return array_key_exists($widgetName,self::$_installedWidgets);
	}
	private function __construct($name,$data) {
		$this->initObj('widget'.$name);
		$this->setRequiredDisabledModules();
		// Copy data from DB
		$this->_data = $data;
		// Try to load CSS
		$cssPath = "widgets/".$name.".css";
		Portal::getInstance()->addStyleSheet($cssPath, !seoConfig::$tmplCSSBackCompatibility);
		// Try to load JS
		$jsPath = "widgets/".$name.".js";
		Portal::getInstance()->addScript($jsPath);
		// Localization
		Text::parseWidget($name);
	}
	protected function getModel($moduleName,$modelName='') {
		$widgetName = $this->getName();

		if ($modelName == '') {
			$modelName = $widgetName;
		}

		$module = Module::getInstance($moduleName);

		$modelKey = $moduleName."_".$modelName;
		if (array_key_exists($modelKey,$this->_models) == false) {
			// Load model script
			$modelScriptPath = PATH_MODULES.DS.$moduleName.DS.'models'.DS.$modelName.'.php';
			if (!file_exists($modelScriptPath)) {
				$this->fatalError(Text::_("Model not found")." => ".$moduleName.".".$modelName); // Realy fatal, but called from widgets only.
			}

			require_once $modelScriptPath;

			$modelClass = $moduleName.'Model'.$modelName;
			$this->_models[$modelKey] = new $modelClass($module);

			$this->message("Model loaded from widget => ".$moduleName.".".$this->getName().".".$modelName, __FUNCTION__);
		}

		return $this->_models[$modelKey];
	}
	public function getHelper($helperName,$moduleName) {
		return Module::getHelper($helperName,$moduleName);
	}
	public function checkSelf($haltOnFail=false) {
		return true;
	}
	public function ajax() {
		// Just for overriding
	}
	public function render() {
		// Just for overriding
	}
	public function prepare() {
		// Just for overriding
	}
	public function curve() {
		$method=$this->get("method","render_default");
		$title = $this->get('title','');
		$show_title = $this->get('show_title',0);
		if (!$show_title)	$title="";
		$body=$this->render();
		if (!$body) return "";
		if (class_exists("widgetTMPL",false)&& method_exists("widgetTMPL", $method)) return widgetTMPL::$method($title,$body,$this); else return $body;
	}
	public static function getInstalled() {
		return self::$_installedWidgets;
	}
	public static function getInstalledForFront() {
		$db = Database::getInstance();
		$db->setQuery("SELECT * FROM #__widgets WHERE w_side=1 ORDER BY w_name");
		return $db->loadObjectList("w_name");
	}
	public function getParam($param_name, $subs_default_value=true, $custom_default_value=null) {
		if(!count($this->_paramsMask)) $this->setParamsMask();
		// true - something
		if($subs_default_value && !is_null($custom_default_value)) return $this->get($param_name, $custom_default_value, (isset($this->_paramsMask[$param_name]["fill_default"]) && $this->_paramsMask[$param_name]["fill_default"]));
		// false - something
		if(!$subs_default_value && !is_null($custom_default_value)) return $this->get($param_name, $custom_default_value, true);
		// true - null
		// isset($this->_paramsMask[$param_name]["vdefault"]) is nesessary because in widgwtZone prepare function added some $params without vdefault
		if($subs_default_value && isset($this->_paramsMask[$param_name]["vdefault"])) return $this->get($param_name, $this->_paramsMask[$param_name]["vdefault"], (isset($this->_paramsMask[$param_name]["fill_default"]) && $this->_paramsMask[$param_name]["fill_default"]));
		// false - null
		return $this->get($param_name);
	}
	public static function cleanWidgetCache($widget_id, $widget_name){
		if (!$widget_id || !$widget_name) return;
		$files=Files::getFiles(PATH_CACHE."widgets".DS);
		$cache_mask="/".$widget_name."_".$widget_id."_(.*).php/";
		if ($files){
			foreach ($files as $file){
				if (preg_match($cache_mask, $file["filename"])) Files::delete(PATH_CACHE."widgets".DS.$file["filename"], true);
			}
		}
	}
	/**************************************************************************************/
	public function checkAccess(&$aw_access){
		if(trim($aw_access)!="all") {
			$roles = preg_split("/(\;)/", trim($aw_access));
			$roles = array_flip($roles);
			if (!array_key_exists(User::getInstance()->getRole(), $roles)) return false;
		}
		return true;
	}
	public function checkLanguage(&$aw_forlang){
		if(defined("_BARMAZ_TRANSLATE")){
			if ($aw_forlang != "all") {
				$lang = preg_split("/(\;)/", trim($aw_forlang));
				$lang = array_flip($lang);
				if (!array_key_exists(Text::getLanguage(), $lang)) return false;
			}
		}
		return true;
	}
	public function checkVisibilityByMenu(&$aw_visible_in){
		if(trim($aw_visible_in)!="all") {
			$menuitems = preg_split("/(\;)/", trim($aw_visible_in));
			$visible_except = (isset($menuitems[0]) && $menuitems[0]=="except"); // $visible_except=1; else $visible_except=0;
			$menuitems = array_flip($menuitems);
			$activemenu = $_SESSION['active_menu_id'];
			if($visible_except) {
				if (array_key_exists($activemenu, $menuitems)) return false;
			} else {
				if (!array_key_exists($activemenu, $menuitems)) return false;
			}
		}
		return true;
	}
	public function intersectParams(&$aw){
		$params = array("aw_id"=>$aw->aw_id, "aw_name"=>$aw->aw_name, "class"=>$aw->aw_class, "title"=>$aw->aw_title, "title_link"=>$aw->aw_title_link, "show_title"=>$aw->aw_show_title, "content"=>$aw->aw_content, "use_cache"=>$aw->aw_cache);
		$configArray = Params::parse($aw->aw_config);
		$defParams = $this->getParamsMask();
//		Util::showArray($defParams);
		if (count($defParams)) {
			foreach ($defParams as $param_name=>$param_data){
				if (isset($configArray[$param_name])) $params[$param_name]=$configArray[$param_name];
				else {
					$params[$param_name]=$param_data["vdefault"];
				}
			}
		}
//		Util::showArray($params);
		return $params;
	}
	public function getActiveWidgetData($aw_id, $enabled_only=1){
		$aw = false;
		$query = "SELECT aw.* FROM `#__widgets_active` as aw WHERE aw.aw_id=".$aw_id." AND aw.aw_deleted=0";
		if($enabled_only) $query.=" AND aw.aw_enabled=1";
		$db = Database::getInstance();
		$db->setQuery($query);
		$db->loadObject($aw);
		return $aw;
	}
}
?>