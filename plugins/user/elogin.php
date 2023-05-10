<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_PLUGIN_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class userPluginelogin extends Plugin {
	private $_key = "";
	private $_secret = "";
	private $_callback_uri = "index.php?option=elogin"; // Router makes /default.html?option=elogin
	private $_providerId = 0;
	private $_providerName = "";
//	private $_providerClassName = ""; // Seems to be not actual
	private $_provider = null;
	private $_debug_messages = 0;
	private $_eauth_return = "index.php";
	protected $_events = array("user.login_form", "user.before_elogin", "user.after_elogin");
	
	protected function setParamsMask(){
		parent::setParamsMask();
		$this->addParam("debug_messages", "boolean", 0);
	}
	/**
	 * Social network provider initialization
	 * @param $name
	 * @param $reset_mode 0-none, 1-partial, 2-full
	 */
	private function initProvider($name, $reset_mode=0){
		if($reset_mode == 1) User::resetEAuth(false);
		elseif($reset_mode == 2) User::resetEAuth();
		$provider = $this->getProvider($name);
		if(is_object($provider)){
			$this->_providerId = $provider->sn_id;
			$this->_providerName = ucfirst($provider->sn_name);
			$crypt = new Crypta ();
			$this->_key  = $crypt->xxtea_decrypt(base64_decode($provider->sn_key), backofficeConfig::$secretCode);
			$this->_secret = $crypt->xxtea_decrypt(base64_decode($provider->sn_secret), backofficeConfig::$secretCode);
			$this->_callback_uri = Router::_($this->_callback_uri);
			$this->_debug_messages = $this->getParam("debug_messages");
			$_providerClassName = ucfirst($this->_providerName)."SNProvider";
			if ($this->_key && $this->_secret) {
				$this->_provider = new $_providerClassName($this->_key, $this->_secret, $this->_callback_uri);
			}
		}
	}
	private function resetAuth($url, $message="", $message_code=""){
		User::resetEAuth();
		$this->redirect($url, $message, $message_code);
	}
	private function redirect($url, $message="", $message_code=""){
		if($this->_debug_messages){
			if($this->_providerName) $message.= " ".$this->_providerName;
			if($message_code) $message.= " ".$message_code;
			if(!is_null($this->_provider)){
				if($this->_provider->error_desc) $message.= "<br />Provider message: ".$this->_provider->error_desc;
				if($this->_provider->error_code) $message.= "<br />Code: ".$this->_provider->error_code;
			}
		}
		Util::redirect($url, $message);
	}
	protected function onRaise($event, &$data) {
		switch($event){
			case "user.login_form":
				$providers = $this->getProviders();
				if(is_array($providers)){
					foreach($providers as $pk=>$provider){
						$data[$provider->sn_name] = "<a class=\"sn_button\" href=\"".Router::_("index.php?module=user&task=elogin&use=".$provider->sn_name)."\">".HTMLControls::renderImage("/images/sn/64/".$provider->sn_name.".png",false,0,0,Text::_("Login with")." ".ucfirst($provider->sn_name))."</a>";
					}
				}
				break;
			case "user.before_elogin":
				// echo Util::traceStack(true, false, false);
				if (!is_null(Session::getVar("eauth_return"))) $this->_eauth_return = Session::getVar("eauth_return");
				if (!backofficeConfig::$allowSNLogin) $this->resetAuth($this->_eauth_return, Text::_('Error signing in'), "x001");
				$eauth_type = Session::getVar("eauth_type");
				if(!(User::checkFloodPoint())) {
					$this->initProvider($eauth_type, 1);
					if(is_null($this->_provider)){
						$this->resetAuth($this->_eauth_return, Text::_('Error signing in'), "x003");
					} else {
						$this->authProvider();
					}
				} else $this->resetAuth($this->_eauth_return, Text::_('Flood found'), "x004");
				break;
			case "user.after_elogin":
				// echo Util::traceStack(true, false, false);
				if (!is_null(Session::getVar("eauth_return"))) $this->_eauth_return = Session::getVar("eauth_return");
				if (!backofficeConfig::$allowSNLogin) $this->resetAuth($this->_eauth_return, Text::_('Error signing in'), "x011");
				$eauth_type = Session::getVar("eauth_type");
//				if(!(User::checkFloodPoint(1))) {  // Seems to be not actual
					$this->initProvider($eauth_type, 1);
					if(is_null($this->_provider)){
						$this->resetAuth($this->_eauth_return, Text::_('Error signing in'), "x013");
					} else {
						$this->proceedAuth();
					}
//				} else $this->resetAuth($this->_eauth_return, Text::_('Flood found'), "x014");
				break;
		}
	}
	private function tryAuth($profile){
		// !!!!!!!!!!!!!!!!!!!! ДЛЯ ОТЛАДКИ БЕЗ ЗАПИСИ В БАЗУ !!!!!!!!!!!!!!!!!!!!!!!!!
		// Util::showArray($profile, "tryAuth"); die(); // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		$provider = strtolower($this->_providerName);
		if(isset($profile["uid"]) && $profile["uid"]){
			$login = "sn-".$provider."-".$profile["uid"];
			if(isset($profile["nickname"]) && $profile["nickname"]) $nickname = $profile["nickname"];
			else $nickname = "";
			$email = $profile["email"];
			$password = "";
			$role = backofficeConfig::$defaultUserRole;
			$referral = User::checkReferral();
			$activated = 1;
			$discount = 0;
			$price_type = 1;
			if(User::isUser($login)){
				User::getInstance()->elogin($login, $provider, $email);
			} else {
				if(!$nickname || User::isNickName($nickname)) $nickname = $provider."-".$profile["uid"];
				if(User::isEmail($email)) $email = "";
				if (!backofficeConfig::$noRegistration) {
					$userId = User::addUser($login, $nickname, $password, $email, $role, $referral, $activated, $discount, $price_type, $provider);
					if($userId){
						$reg_data = array("uid"=>$userId);
						Event::raise("register.proceedRegistration",array("module"=>false, "source"=>"plugin.elogin", "action"=>"elogin.auth"), $reg_data);
						User::getInstance()->elogin($login, $provider, $email);
					} else {
						$this->resetAuth($this->_eauth_return, Text::_('Error signing in'), "x041");
					}
				} else {
					$this->resetAuth($this->_eauth_return, Text::_('Error signing in'), "x042");
				}
			}
		} else {
			$this->resetAuth($this->_eauth_return, Text::_('Error signing in'), "x043");
		}
	}
	private function authProvider(){
		$params = array();
//		$params["scope"] = "email"; // Seems to be not actual
		$loginUrl = $this->_provider->getLoginURI($params);
		if($loginUrl) Util::redirect($loginUrl);
		else $this->resetAuth($this->_eauth_return, Text::_('Error signing in'), "x021");
	}
	private function proceedAuth(){
		$params = array();
//		$params["scope"] = "email"; // Seems to be not actual
		if ($this->_provider->validateAuthResponse($params)){
			User::resetEAuth(false);
			$params = array(); // Reinit params for profile
//			$params["fields"] = "name,email"; // Seems to be not actual
			$profile = $this->_provider->getProfile($params);
			if ($profile && is_array($profile) && isset($profile["uid"]) && isset($profile["nickname"])){
				$this->tryAuth($profile);
			} else $this->resetAuth($this->_eauth_return, Text::_('Error signing in'), "x031");
		} else $this->resetAuth($this->_eauth_return, Text::_('Error signing in'), "x032");
	}
	private function getProvider($name){
		$sql = "SELECT * FROM #__auth_providers WHERE sn_enabled=1 AND sn_deleted=0 AND sn_name='".$name."'";
		Database::getInstance()->setQuery($sql);
		Database::getInstance()->loadObject($provider);
		return $provider;
	}
	private function getProviders(){
		$sql = "SELECT sn_id, sn_name FROM #__auth_providers WHERE sn_enabled=1 AND sn_deleted=0 ORDER BY sn_ordering";
		Database::getInstance()->setQuery($sql);
		return Database::getInstance()->loadObjectList();
	}
}
?>