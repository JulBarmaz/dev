<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class Controller extends BaseObject {

	private $_module		= null;
	private $_view			= null;
	private $_view_halted	= false;

	private $_redUrl	= '';
	private $_redMsg	= '';
	private $_redReferer= '';
	private $_redCode	= 302;
	
	public function haltView() {
		$this->_view_halted =true;
	}
	
	public function __construct($name,$module) {
		Debugger::getInstance()->milestone("Controller constructor : ".$name);
		$this->initObj($name);
		$this->_module = $module;
		$this->milestone("Controller created : ".$name, __FUNCTION__);
	}

	public function getPsid()	{
		$arr_psid	= Request::getSafe('cps_id', false);	// массив отмеченных галочкой элементов
		$psid		= Request::getInt('psid', 0);			// ид строки
		if(!$psid){
			if($arr_psid && is_array($arr_psid) && count($arr_psid)>0) $psid = $arr_psid[0];
			if(!$psid) {
				if(!defined("_ADMIN_MODE")) {
					$module_name=$this->get('module');
					$view_name=$this->get('view');
					if(Module::getInstance($module_name)->getDefaultView() == $view_name){
						$psid = $this->getConfigVal("Default_item_ID");
					}
				}
			}
		}
		return $psid;
	}

	protected function getModule() {
		if (is_object($this->_module)) return $this->_module;
		else return Module::getInstance();
	}

	protected function getModel($modelName='') {
		return $this->_module->getModel($modelName);
	}
	public function ajaxVote(){
		// здесь только по по комментам
		$element = Request::getSafe("element",false);
		if ($element=="comment") {
			$this->initComments(0);
			$psid = Request::getInt('psid',0);
			BaseComments::getInstance()->updateRating($psid);
		}
		return false;
	}
	public function getConfigVal($param_name, $subs_default_value=true, $custom_default_value=null) {
		return $this->getModule()->getParam($param_name, $subs_default_value, $custom_default_value);
	}
	/**
	* @deprecated From revision 1508. Use getConfigVal instead
	*/ 
	public function getConfigValue($key,$defaultValue='') {
		return $this->getModule()->getConfigValue($key,$defaultValue);
	}

	public function ajaxgetComments() {
		$psid		= Request::getInt('psid', 0);
		$parent_id	= Request::getInt('parent_id', 0);
		$start		= Request::getInt('start', 0); // это старт комментариев
		$this->initComments($psid);
		echo BaseComments::getInstance()->renderComments($parent_id, $start);
	}

	public function getComment() {
		$psid       = Request::getInt('psid', 0);
		$comm_id    = Request::getInt('comm_id', 0);
		$this->initComments($psid);
		echo BaseComments::getInstance()->renderComment($comm_id);
	}
	
	public function ajaxToggleCommentEnabled(){
		$psid       = Request::getSafe('psid', 0);
		$comm_id    = Request::getSafe('comm_id', 0);
		$published  = Request::getSafe('published', 0);
		$answer = array("status"=>0, "message"=>"");
		if ($psid && $comm_id){
			$this->initComments($psid);
			if (BaseComments::getInstance()->togglePublished($comm_id)){
				$answer["status"] = "OK";
				if ($published)	$answer["message"] = Text::_("Enable"); else $answer["message"] = Text::_("Disable");
			} else $answer["message"] = "Update error";
		} else $answer["message"] = "Data error";
		echo json_encode($answer);
	}

	public function ajaxToggleCommentDeleted(){
		$psid       = Request::getSafe('psid', 0);
		$comm_id    = Request::getSafe('comm_id', 0);
		$deleted  = Request::getSafe('deleted', 0);
		$answer = array("status"=>0, "message"=>"");
		if ($psid && $comm_id){
			$this->initComments($psid);
			if (BaseComments::getInstance()->toggleDeleted($comm_id)){
				$answer["status"] = "OK";
				if ($deleted) $answer["message"] = Text::_("Delete"); else $answer["message"] = Text::_("Undelete");
			} else $answer["message"] = "Update error";
		} else $answer["message"] = "Data error";
		echo json_encode($answer);
	}
	
	public function initComments($psid,$moduleName="",$viewName="",$layout="") {
		if (!$moduleName) $moduleName=$this->get('module');
		if (!$viewName) $viewName=$this->get('view');
		if (!$layout) $layout=$this->get('layout');
		BaseComments::getInstance()->init($moduleName,$viewName,$psid,$layout);
		return BaseComments::getInstance();	
	}
	
	public function saveComment() {
		$moduleName	= Module::getInstance()->getName();
		$viewName 	= $this->getView()->getName();
		$psid       = Request::getInt('psid');
		$multy_code	= Request::getInt('multy_code');
		$layout		= $this->get('layout');
		$alias		= Request::getSafe('alias');
		$page		= Request::getInt('page', 1);
		$sort		= Request::getSafe('sort');
		$orderby	= Request::getSafe('orderby');

		$parent_id	= Request::getInt("parent_id",0);
		$cm_cat		= Request::getInt('cm_cat',0);
		$cm_type	= Request::getInt('cm_type',0);
		$title		= Request::getSafe("comm_title","");
		if(User::getInstance()->isLoggedIn()){
			$email=User::getInstance()->getEmail();
			$nickname=User::getInstance()->getNickname();
		} else {
			$email=Request::getSafe("comm_email","");
			$nickname=Request::getSafe("comm_nickname",Text::_("Anonymous"));
		}
		$text=Request::getSafe("comm_text",""); 
		$url='index.php?module='.$moduleName."&view=".$viewName."&layout=".$layout."&psid=".$psid."&sort=".$sort.($page>1 ? "&page=".$page : "").($alias ? "&alias=".$alias : "")."&orderby=".$orderby.($multy_code ? "&multy_code=".$multy_code : "");
		$msg="";
		if(!$this->checkACL("commentsDisableCaptcha",false)) Event::raise("captcha.checkResult",array("module"=>$moduleName));
		if(isset($_SESSION['captcha_string'])&&($_SESSION['captcha_string']!="OK")) { 
			// $msg =Text::_("Wrong captcha");
			$msg = $_SESSION["captcha_string"];
			unset($_SESSION['captcha_string']); 
		} elseif(!$title && !$text) { $msg = Text::_('Empty message'); }
		elseif(!User::getInstance()->isLoggedIn()&&(!$nickname)) {$msg = Text::_('Wrong nickname'); }
		elseif(!User::getInstance()->isLoggedIn()&&(!Mailer::checkEmail($email))) {$msg = Text::_('Wrong email');}
		elseif(User::checkFloodPoint()) { $msg = Text::_('Flood is found'); }
		$this->initComments($psid);
		$text=BaseComments::getInstance()->cutCommentByLimit($text);
		if($msg) {
			$url.="&parent_id=".$parent_id."&comm_nickname=".urlencode($nickname)."&comm_email=".urlencode($email)."&parent_id=".$parent_id."&comm_title=".urlencode($title)."&comm_text=".urlencode($text);
			if($cm_cat) $url.="&cm_cat=".$cm_cat; 
			if($cm_type) $url.="&cm_type=".$cm_type; 
		} else {
			$this->initComments($psid);
			$cid=BaseComments::getInstance()->saveComment($parent_id,$title,$text,$nickname,$email,$cm_cat,$cm_type); 
			if($cid) {
				$msg=Text::_("Save successfull");
				if (BaseComments::getInstance()->premoderateEnabled()) $msg.="<br />".Text::_("Waiting moderation");
			}	else $msg=Text::_("Save unsuccessfull"); 
		}
		$this->setRedirect($url,$msg);
	}
	
	public function getView($vName="", $tech_override=false) {
		if (!is_object($this->_view) || ($vName && $tech_override)) { 	// Load view script
			$mName = $this->get('module');
			$mName=Module::getReplaceModule($mName);
			if(!$vName) $vName = $this->get('view');
			$path = PATH_MODULES.$mName.DS.'views'.DS.$vName.'.php';
			if (!is_file($path)){ 
				if($this->getModule()->getDefaultView() == $vName){
					$this->fatalError(Text::_("View not found")." => ".$mName.".".$vName, ($this->_debugger==0 ? "404" : "503")); // Realy fatal if default, else redirect already set in next lines.
				} else {
					if ((siteConfig::$debugMode && User::getInstance()->isAdmin()) || siteConfig::$debugMode>100) $this->fatalError(Text::_("View not found")." => ".$vName, defined("_ADMIN_MODE") ? "503" : "404"); // Render fatal for admin debug.
					else Util::redirect(Router::_("index.php"), Text::_("View not found")." => ".$vName, "404");
				}
			}
			else {
				Debugger::getInstance()->milestone("Including (require_once) ".$path );
				require_once $path;
				Debugger::getInstance()->milestone("Included (require_once) ".$path );
				$vClass = $this->getName().'View'.$vName;
				$this->_view = new $vClass($vName);
			}
		}
		return $this->_view;
	}

	public function getHelper($helperName,$moduleName='') {
		if ($moduleName == '') { $moduleName = $this->get('module');	}
		return Module::getHelper($helperName,$moduleName);
	}

	protected function setRedirect($url,$message='',$code=0,$referer="") {
		$this->_redUrl = Router::_($url,false,false);
		$this->_redMsg = $message;
		if ($code) $this->_redCode = $code;
		if ($referer) $this->_redReferer=$referer;
	}

	private function redirect($url) {
		Util::redirect($url,$this->_redMsg,$this->_redCode,$this->_redReferer);
	}

	protected function checkAuth($redirect2Login=false) {
		if (User::getInstance()->isLoggedIn() == false) {
			if($redirect2Login) $this->setRedirect('index.php?module=user&task=login');
			else $this->setRedirect('index.php?module=user&view=register');
			return false;
		} else return true;
	}

	protected function checkACL($aclObjectName, $critical=true) {
		$aclObject = ACLObject::getInstance($aclObjectName, false);
		$result = $aclObject->canAccess();
		if(!$result){
			if ($critical) {
				$description = $aclObject->getDescription();
				if (!defined("_ADMIN_MODE")) Text::parseModule("aclmgr");
				if ((siteConfig::$debugMode && User::getInstance()->isAdmin()) || siteConfig::$debugMode>100) 
					$this->fatalError(Text::_('Permission is absent for')." ".$aclObjectName."(".Text::_($description).")", "403"); // Redirect already set in next lines.
				else { 
					if (defined("_ADMIN_MODE")) $message=Text::_('Permission is absent for')." ".Text::_($description)." (1)";
					else $message=Text::_('Permission is absent');
					$this->setRedirect("index.php", $message, "403");
				}
			} else {
				if (defined("_ADMIN_MODE")) $message=Text::_('Permission is absent for')." ".$aclObjectName." (2)";
				else $message=Text::_('Permission is absent for')." ".$aclObjectName." (3)";
				$this->warning($message);
			}
		}
		return $result;
	}

	private function executeMethod($methodName) {
		if (method_exists($this, $methodName)) {
			$this->{$methodName}();
			$this->milestone("Module task executed => ".$this->get('module').".".$this->get('controller').".".$methodName, __FUNCTION__);
		}
		else {
			$this->error(Text::_("Undefined module task")." ".$methodName);
		}
	}

	//----------------- Interface methods ------------------
	public function executeTask() {
		$task = $this->get('task');
		Event::raise("controller.before_execute_task", array(), $this);
		$this->executeMethod($task);
		Event::raise("controller.after_execute_task", array(), $this);
		if ($this->_redUrl != '') $this->redirect($this->_redUrl);
	}

	public function executeAjax() {
		$task = $this->get('task');
		$task = 'ajax'.ucfirst($task);
		Event::raise("controller.before_execute_ajax", array(), $this);
		$this->executeMethod($task);
		Event::raise("controller.after_execute_ajax", array(), $this);
	}
	//------------------------------------------------------

	public function defaultTask() {
		// Just do nothing
	}

	public function search() {
		SearchMachine::getInstance()->parseRequest();
		Event::raise("search.renderForm");
		SearchMachine::getInstance()->renderForm();
		SearchMachine::getInstance()->renderResult();
	}
	
	public function show() {
		$view = $this->getView();
		$viewName = $view->getName();
		$methodName = 'show'.ucfirst($viewName);
		if (method_exists($this, $methodName)) {
			Event::raise("controller.before_show", array(), $this);
			$this->{$methodName}();
		}

		if ($this->_redUrl == '') {
			if(!$this->_view_halted) {
				Event::raise("controller.before_view_render", array(), $this);
				$view->render();
			}
		}
	}
	public function downloadFile(){
		// Just for override
	}
}
?>