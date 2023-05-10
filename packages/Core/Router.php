<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class Router extends BaseObject {
	//---------- Singleton implementation ------------
	private static $_sefMode = false;
	private static $_instance = null;
	private static $_routers = array();
	private static $_vars = array();
	private static $_reserved = array("search");
	private $_request = "";
	private $_module = "";

	private static function createInstance() {
		if (self::$_instance == null) {
			self::$_instance = new self();
			Debugger::getInstance()->milestone("Created master router");
		}
		return self::$_instance;
	}
	public static function getInstance($moduleName="") {
		if ($moduleName) {
			if (array_key_exists($moduleName, self::$_routers)) { // уже обращались за роутером
				if (self::$_routers[$moduleName]) { // и нашли
					return self::$_routers[$moduleName];
				}	else {	// не существует отдельного роутера
					return self::createInstance();
				}
			} else { // первое обращение к customRouter
				if (self::initCustomRouter($moduleName)) { // создали отдельный роутер
					return self::$_routers[$moduleName];
				}	else { // не существует отдельного роутера
					return self::createInstance();
				}
			}
		} else { // нет имени используем базовый
			return self::createInstance();
		}
	}
	//------------------------------------------------
	private function __construct() {
		if(defined('_ADMIN_MODE')) self::$_sefMode=false;
		else self::$_sefMode=seoConfig::$sefMode;
	}
	private static function initCustomRouter($moduleName) {
		if(defined('_ADMIN_MODE')) $_custom_router_file=PATH_FRONT_MODULES.$moduleName.DS."router.php";
		else $_custom_router_file=PATH_MODULES.$moduleName.DS."router.php";
		if (file_exists($_custom_router_file)) {
			require_once($_custom_router_file);
			$_custom_router_class=$moduleName.'CustomRouter';
			if (class_exists($_custom_router_class,false)) {
				self::$_routers[$moduleName]=new $_custom_router_class;
				Debugger::getInstance()->milestone("Created router for ".$moduleName);
			} else self::$_routers[$moduleName]=false;
		} else self::$_routers[$moduleName]=false;
		return self::$_routers[$moduleName];
	}
	private function checkIntrusion() {
		foreach(self::$_vars as $key=>$value)	{
			$value=Util::checkIntrusion($value);
			$_REQUEST[$key]=$value;
			self::$_vars[$key]=$value;
		}
	}
	private function checkErrorPages($_last_chance){
		if (array_key_exists($_last_chance, ErrorPages::$_err_codes_4xx)) Util::fatalError(Text::_(ErrorPages::$_err_codes_4xx[$_last_chance]), $_last_chance);
		if(isset($_SERVER['REDIRECT_URL']) && $_SERVER['REDIRECT_URL']){
			$_redirect_url= $_SERVER['REDIRECT_URL'];
			$redirect=preg_split('/[\?]/', $_redirect_url);
			$main_redirect=preg_split('/[\/]/',$redirect[0]);
			if (count($main_redirect)>0) {
				if (!$main_redirect[0]) {
					$last_chance=$main_redirect[1];
				} else {
					$last_chance=$main_redirect[0];
				}
				$_last_chance=preg_split('/[.]/',$last_chance);
				if (array_key_exists($_last_chance[0], ErrorPages::$_err_codes_4xx)) Util::fatalError(Text::_(ErrorPages::$_err_codes_4xx[$_last_chance[0]]), $_last_chance[0]);
			}
		}
	}
	public function init()  {
		defined('_ADMIN_MODE') or self::checkManualRedirect();
		if (self::$_sefMode) {
			if(isset($_SERVER['REQUEST_URI'])){
				if(!preg_match("/ref_(.*)\.html/", $_SERVER['REQUEST_URI'], $result)) {
					$_request_uri=$_SERVER['REQUEST_URI'];
				} else {
					$_request_uri="/index.php?referral=".$result[1];
				}
				$request=preg_split('/[\?]/',$_request_uri);
				$this->_request=$request[0];
				$main_request=preg_split('/[\/]/',$request[0]);
				if (count($main_request)>0) { //getting module
					if (!$main_request[0]) {
						$last_chance=$main_request[1];
					} else {
						$last_chance=$main_request[0];
					}
					$_last_chance=preg_split('/[.]/',$last_chance);
					$this->checkErrorPages($_last_chance[0]);
					$this->_module=$_last_chance[0];
				}
				self::$_vars=Router::getInstance($this->_module)->parseRoute($this->_request);
				$this->checkIntrusion();
			}
		} else {
			$this->_module=Request::getSafe("module", "");
			if ($this->_module) {
				$this->setVarsVal("module", $this->_module);
				$view=Request::getSafe("view","");
				if ($view) {
					$this->setVarsVal("view", $view);
					$layout=Request::getSafe("layout","");
					if ($layout) $this->setVarsVal("layout", $layout);
				}
				$psid=Request::getInt("psid",0); if ($psid) $this->setVarsVal("psid", $psid);
			} elseif(Request::getSafe("task") && !in_array(Request::getSafe("task"), self::$_reserved) && !defined('_ADMIN_MODE')) {
				if(!seoConfig::$stop404) Util::redirect(Portal::getURI(1), Text::_("Page not found"), 404);
			}
		}
		Debugger::getInstance()->milestone("Router initialized");
	}
	private function checkManualRedirect(){
		$old_url1=Request::getSafe("REQUEST_URI","","server");
		$cnt=0;
		$old_url1=Util::replaceDouble($_SERVER['REQUEST_URI'], $cnt);
		if($cnt) Util::redirect($old_url1, "", 301);
		$old_url=htmlspecialchars_decode($old_url1,ENT_NOQUOTES);
		$poz=intval(mb_strpos($old_url1,"?"));
		if($poz>0)$old_url2="or rl_old_url = '".mb_substr($old_url1,0,$poz)."'";	
		else $old_url2="";	
		$sql="SELECT * FROM #__redirect_links WHERE (rl_old_url = '".$old_url."' or rl_old_url = '".$old_url1."' ".$old_url2.") AND rl_published=1 AND rl_deleted=0 ORDER BY rl_ordering LIMIT 1";
		Database::getInstance()->setQuery($sql);
		$new_url=Database::getInstance()->loadObject($object);
		if (is_object($object) && $object->rl_new_url) {
			$upd_count="update #__redirect_links set rl_redirects=rl_redirects+1 where rl_id=".(int)$object->rl_id;
			Database::getInstance()->setQuery($upd_count);
			Database::getInstance()->query();
			
			if(!$object->rl_substitution) Util::redirect(self::_($object->rl_new_url, false, false),"",301,Portal::getURI(1, 1));
			else {
				$url_arr = parse_url($object->rl_new_url);
				// ===============================================================================================
				// @FIXME Only $_REQUEST, NOT $_GET or $_POST
				// if($url_arr["query"]) $url_vars = parse_str(html_entity_decode($url_arr["query"]), $_REQUEST);
				if($url_arr["query"]) {
					$url_vars = parse_str(html_entity_decode($url_arr["query"]), $_REQUEST_MY);
					$_REQUEST = array_merge($_REQUEST, $_REQUEST_MY);
				}
				// ===============================================================================================
				$_SERVER["REDIRECT_URL"] = @$url_arr["path"];
				$_SERVER["REDIRECT_QUERY_STRING"] = @$url_arr["query"];
				$_SERVER["REQUEST_URI"]=self::_($object->rl_new_url, false, false);
				// Util::showArray($_REQUEST); // Util::showArray($_SERVER); die(); // @FIXME УБРАТЬ !!! !!!
			}
		}
	}
	public function getVarsArr() {
		return self::$_vars;
	}
	public function unsetVar($name) {
		if(isset(self::$_vars[$name])){
			unset(self::$_vars[$name]);
		}
		if(isset($_REQUEST[$name])){
			unset($_REQUEST[$name]);
		}
	}
	public function setVarsVal($name, $val) {
		self::$_vars[$name]=$val;
	}
	public function getRequest($varname) {
		if (array_key_exists($varname, self::$_vars)) return self::$_vars[$varname];
		else return null;
	}
	public function buildRoute($options, $mode="sitelink", $force_fronte=0, $absolute_link=0, $force_protocol=0) {
		if (array_key_exists('alias', $options)) unset($options['alias']);
		switch ($mode) {
			case "nomodule":
				if (array_key_exists('module', $options))  unset($options['module']);
				if (array_key_exists('view', $options))  unset($options['view']);
				if (array_key_exists('layout', $options)) unset($options['layout']);
				if(isset($options["task"]) && $options["task"]=="search"){
					$url = SearchMachine::getInstance()->buildRoute($options, $force_fronte, $absolute_link, $force_protocol);
				} else {
					$url = "";
					if(count($options)>0) {
						$appendix = "";
						foreach($options as $key=>$val) {
							$appendix.= "&".$key."=".$val;
						}
						if($appendix) $url = $url."?".substr($appendix,1);
					}
					$url = Portal::getURI($force_fronte, $absolute_link, $force_protocol).$url;
				}
				break;
			case "sitelink":
			default:
				$url = "";
				if (array_key_exists('module', $options)) {
					$url = $options['module']; unset($options['module']);
					if (array_key_exists('view', $options)&&$options['view']) {
						$url.= "/".$options['view']; unset($options['view']);
						if (array_key_exists('layout', $options) && $options['layout']) {
							$url.= "/".$options['layout']; unset($options['layout']);
							if (array_key_exists('psid', $options) && $options['psid']) {
								$url.= "/".$options['psid']; unset($options['psid']);
							}
						} elseif (array_key_exists('psid', $options) && $options['psid']) {
							$url.= "/".$options['psid']; unset($options['psid']);
						}
					}
				}
				
				if($url) $url.=".html";
				if(count($options)>0) {
					$appendix = "";
					foreach($options as $key=>$val) {
						$appendix.= "&".$key."=".$val;
					}
					if($appendix) $url = $url."?".substr($appendix, 1);
				}
				$url = Portal::getURI($force_fronte, $absolute_link, $force_protocol).$url;
				break;
		}
		return $url;
	}
	/*
	public function buildRoute($options, $mode="sitelink", $force_fronte=0, $absolute_link=0, $force_protocol=0) {
		if (array_key_exists('alias', $options)) unset($options['alias']);
		switch ($mode) {
			case "nomodule":
				if (array_key_exists('module', $options))  unset($options['module']);
				if (array_key_exists('view', $options))  unset($options['view']);
				if (array_key_exists('layout', $options)) unset($options['layout']);
				if(isset($options["task"]) && $options["task"]=="search"){
					$url = SearchMachine::getInstance()->buildRoute($options, $force_fronte, $absolute_link, $force_protocol);
				} else {
					$url="default.html"; $appendix="";
					if (count($options)>0) {
						foreach($options as $key=>$val) {
							$appendix.="&".$key."=".$val;
						}
					}
					$url=Portal::getURI($force_fronte, $absolute_link, $force_protocol).$url;
					if ($appendix) $url=$url."?".substr($appendix,1);
				}
				break;
			case "sitelink":
			default:
				if (array_key_exists('module', $options)) {
					$url = $options['module']; unset($options['module']);
					if (array_key_exists('view', $options)&&$options['view']) {
						$url.= "/".$options['view']; unset($options['view']);
						if (array_key_exists('layout', $options) && $options['layout']) {
							$url.= "/".$options['layout']; unset($options['layout']);
							if (array_key_exists('psid', $options) && $options['psid']) {
								$url.= "/".$options['psid']; unset($options['psid']);
							} else $url.= "/default";
						} elseif (array_key_exists('psid', $options) && $options['psid']) {
							$url.= "/".$options['psid']; unset($options['psid']);
						} else $url.= "/default";
					} else $url.= "/default";
				} else $url.= "default";

				$url.=".html"; $appendix="";
				if (count($options)>0) {
					foreach($options as $key=>$val) {
						$appendix.="&".$key."=".$val;
					}
				}
				$url=Portal::getURI($force_fronte, $absolute_link, $force_protocol).$url;
				if ($appendix) $url=$url."?".substr($appendix,1);
				break;
		}
		return $url;
	}
	*/
	public function parseRoute($request) {
		Debugger::getInstance()->milestone("Router (master) parsed URI ".$this->_request);
		$_result=preg_split('/[\/]/',$request);
		$module=""; $view=""; $layout=""; $psid="";
		$last_element = preg_split('/(\.html)/',$_result[count($_result)-1]);
		if (preg_match('/(index.php)/',$last_element[0])) $last_element[0]="";
		if (preg_match('/(default)/',$last_element[0])) $last_element[0]="";
		$_result[count($_result)-1] = $last_element[0];
		switch (count($_result)) {
			case 2:
				$module = $_result[1];
				break;
			case 3:
				$module = $_result[1]; $view   = $_result[2];
				break;
			case 4:
				$psid = intval($_result[3]);
				$layout = $_result[3];
				if ($psid) {
					if (strval($psid)===$layout){ // это число
						$layout = "";
					} else {
						$psid = "";
					}
				} else $psid = "";
				$module = $_result[1]; $view   = $_result[2];
				break;
			case 5:
				$module = $_result[1]; $view = $_result[2];
				$layout = $_result[3]; $psid = intval($_result[4]);
				break;
			default:
				Util::redirect(Portal::getInstance()->getURI(),'',404);
				break;
		}
		$_vars = array();
		if ($module) {
			if(in_array($module, self::$_reserved)) return $_vars;
			$_vars['module']=$module;
			if ($view) $_vars['view']=$view;
			if ($layout) $_vars['layout']=$layout;
			if ($psid) $_vars['psid']=$psid;
		}
		return $_vars;
	}
	public function getTreeUp($view, $layout, $psid, $alias){
		return array($view=>array($psid=>$alias));
	}
	public static function isJavaScript($_url)  {
		if (preg_match('/(javascript:)/',$_url)) return true;
		else return false;
	}
	public static function isFullLink($_url)  {
		if (preg_match('/(http:\/\/)/',$_url)) return true;
		elseif (preg_match('/(https:\/\/)/',$_url)) return true;
		elseif (preg_match('/(ftp:\/\/)/',$_url)) return true;
		else return false;
	}
	public static function isAnchor($_url)  {
		if (substr($_url, 0,1)=="#") return true;
		else return false;
	}
	public static function getSeparator($href){
		if(strpos($href, "?")===false) return "?";
		elseif(strrpos($href, "?")==0) return "";
		else return "&amp;";
		
	}
	public static function isAbsoluteLink($url = ""){
		return (preg_match('/(http:\/\/)/', $url)) || (preg_match('/(https:\/\/)/', $url)) || (substr($url, 0, 2)=="//");
	}
	public static function _($_url, $sefModeOverride=false, $HtmlSpecialChars=true, $absolute_link=0, $force_protocol=0, $force_fronte=0) {
		if (self::isFullLink($_url)) return $_url;
		if (self::isJavaScript($_url)) return $_url;
		if (self::isAnchor($_url)) return $_url;
		$url = str_replace("&amp;", "&", $_url);
		if ($url=="index.php") return Portal::getURI($force_fronte);
		if (($sefModeOverride)||(self::$_sefMode)) { // sefMode=true
			if(!$force_fronte) $force_fronte=1;
			$options=array();
			$parts=preg_split('/(\?)/',$url);
			if ($parts[0]!="index.php") { // no index.php in URI
				if (!self::isAbsoluteLink($url)) {
					if(substr($url, 0, 1)=="/") $url=substr($url, 1);
					//$url = Portal::getURI().$url;
					$url=Portal::getURI($force_fronte, $absolute_link, $force_protocol).$url;
				}
			} else { // begin parsing URI
				$subparts=preg_split('/(\&)/',$parts[1]);
				if (count($subparts)>0) {
					foreach ($subparts as $subpart) {
						if ($subpart) {
							$option=preg_split('/(\=)/',$subpart);
							if(isset($option[1])) $options[$option[0]]=$option[1];							
						}
					}
				}
				if (array_key_exists('module',$options)) $module_name=$options['module']; // found module
				else $module_name="nomodule";
				$url=Router::getInstance($module_name)->buildRoute($options, $module_name, $force_fronte, $absolute_link, $force_protocol);
			}
		} else { 
			if (!self::isAbsoluteLink($url)) {
				if(substr($url, 0, 1)=="/") $url=substr($url, 1);
				$url=Portal::getURI($force_fronte, $absolute_link, $force_protocol).$url;
			}
		}
		if ($HtmlSpecialChars) $url = str_replace("&", "&amp;", $url);
		return $url;
	}
	public function getAlias($_vars){
		return "";
	}
}
?>