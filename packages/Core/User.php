<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

final class User extends BaseObject {

	//---------- Singleton implementation ------------
	private static $_instance = null;
	private static $guest_role = 3; // 3 это гость

	public static function createInstance() {
		if (self::$_instance == null) {
			self::$_instance = new self();
			self::$_instance->adjustACL();
			Debugger::getInstance()->milestone("User created");
		}
	}

	public static function getInstance() {
		self::createInstance();
		return self::$_instance;
	}

	//------------------------------------------------

	private $_loggedIn	= false;

	public $u_id		= 0;
	public $u_login		= '';
	public $u_nickname	= '';
	public $u_email		= '';
	public $u_status	= 0;
	public $u_lastvisit	= '';
	public $u_role		= 3; // 3 это гость
	public $u_referral	= "";
	public $u_account	= 0;
	public $u_points	= 0;
	public $u_discount	= 0;
	public $u_pricetype	= 1;
	public $u_rating	= 0;
	public $u_source	= "";
	public $u_last_visit = "0000-00-00 00:00:00";
	
	private $_isAdmin				= false;
	private $_canMaintenanceLogin	= false;

	private	$_friends	= null;

	private function __construct() {
		$this->initObj();
		$db = Database::getInstance();
		$uid = intval($this->get('user_id',0));
		if ($uid != 0) {
			// Load user data
			$query = "SELECT u.*, ar.ar_admin FROM #__users AS u, #__acl_roles AS ar WHERE ar.ar_id=u.u_role AND u.u_id=".$uid;
			$db->setQuery($query);
			$db->loadObject($this);
			if($this->u_id){
				$this->_isAdmin = (1 == intval($this->ar_admin));
				$this->_loggedIn = true;
				if (!$this->u_email){ // If not in cabinet and not logout, then constantly redirect to filling email
					// Checking BARMAZ_REDIRECT_CYCLED, make sure we don't get stuck.
					if (!defined("_BARMAZ_REDIRECT_CYCLED") && Request::getSafe("option")!="logout" && Request::getSafe("module")!="user") Util::redirect(Router::_("index.php?module=user&view=reset&layout=email"));
				}
			}
		} else {
			$referral = strval(Request::get('referral',''));
			if ($referral) {
				if (isset($_COOKIE['referral'])) {
					$old_referral=$_COOKIE['referral'];
					if (!$old_referral) {
						Session::getInstance()->setcookie('referral', $referral, time()+7776000,"/");
					}
				} else {
					Session::getInstance()->setcookie('referral', $referral, time()+7776000,"/");
				}
			}
			$query = "SELECT `ar_id` FROM `#__acl_roles` WHERE `ar_name`='guest'";
			$db->setQuery($query);
			$this->_isAdmin = false;
			$this->u_role = intval($db->loadResult());
		}
		// Event::raise('user.authenticate');
		Event::raise('user.authenticate', array(), $this);
	}

	private function adjustACL() {
		if (!defined('_ADMIN_MODE')) {
			$this->_canMaintenanceLogin = ACLObject::getInstance('maintenanceLogin',false)->canAccess();
		}	else {
			$this->_canMaintenanceLogin = true;
		}
		if (siteConfig::$siteDisabled && !$this->_canMaintenanceLogin && $this->_loggedIn) { $this->logout(); }
	}

	public function getIP() {
		return Session::getInstance()->getIP();
	}

	public function getAgent() {
		return Session::getInstance()->getAgent();
	}

	public function autoLogin($check_only=false){
		if(!backofficeConfig::$allowAutoExchange) return false;
		$BARMAZ_xkey_name="BARMAZxkey";
//		Util::logFile(intval($check_only), "check_only");
		$result=false;
		$secret=false;
		if($check_only) {
			$login=backofficeConfig::$autoExchangeLogin;
			$password=false;
		} else {
			$login=Request::getSafe("PHP_AUTH_USER","","server");
			$password=Request::getSafe("PHP_AUTH_PW","","server");
		}
		$remember=true;
		if(!(User::checkFloodPoint())) {
			$db = Database::getInstance();
			if($login) {
				if($check_only) {
					$query = "SELECT * FROM #__users WHERE u_source='system' AND u_deleted=0 AND u_login='".$login."'";
				} else {
					$secret=$this->encodePassword($login, $password);
					$query = "SELECT * FROM #__users WHERE u_source='system' AND u_deleted=0 AND u_login='".$login."' AND u_secret='".$secret."'";
				}
				$usr = new stdClass();
				$usr->u_id=0; // пока поставим в 0
				$db->setQuery($query);
				if ($db->loadObject($usr)) {
					if($check_only) {
						$xkey_cookie=Request::getSafe($BARMAZ_xkey_name,"","cookie");
						$BARMAZ_xkey_val=$this->encodePassword($usr->u_secret, $login);  // меняем местами, чтоб врагов запутать и друзьям было чем заняться
//						Util::logFile($xkey_cookie, "xkey_cookie"); Util::logFile($BARMAZ_xkey_val, "BARMAZ_xkey_val");
						if($xkey_cookie!=$BARMAZ_xkey_val){
							$cont =false;
						} else {
							$cont = true;
						}
					} else {
						$cont = true;
					}
					if($cont){
						$ip=$this->getIP();
						$msgs=$this->inBlackList($login, $usr->u_nickname, $ip, $usr->u_email);
						if (!count($msgs)) {
							if (siteConfig::$siteDisabled) {
								$this->u_id = $usr->u_id;
								$this->u_role = $usr->u_role;
								ACLObject::clearACL();
								if (ACLObject::getInstance("maintenanceLogin", false)->canAccess()) {
									$result=true;
								}
							} else {
								$result=true;
							}
						}
					}
				}
			}
		}
		if($result){
			$query = "SELECT u.*, ar.ar_admin FROM #__users AS u, #__acl_roles AS ar WHERE ar.ar_id=u.u_role AND u.u_id=".$usr->u_id;
			$db->setQuery($query);
			$db->loadObject($this);
			if($this->u_id) {
				$this->_loggedIn = true;
				$this->_isAdmin = (1 == intval($this->ar_admin));
//				Session::getInstance()->create($this->u_id, $remember);
//				$this->updateLastVisit($this->u_id);
				if(!$check_only){
					if($secret){
						Session::getInstance()->create($this->u_id, $remember, true);
						$BARMAZ_xkey_val=$this->encodePassword($secret, $login);  // меняем местами, чтоб врагов запутать и друзьям было чем заняться
						Session::getInstance()->setcookie($BARMAZ_xkey_name, $BARMAZ_xkey_val, time()+1800,"/");
						echo "success\n";
						echo $BARMAZ_xkey_name."\n";
						echo $BARMAZ_xkey_val."\n";
						if(backofficeConfig::$allowAutoExchange==2) echo "sess_id=".Session::getInstance()->getKey()."\n";
						elseif(backofficeConfig::$allowAutoExchange==3) echo Session::getInstance()->getKey()."\n";
						echo date('c', Date::mysqldatetime_to_timestamp(Date::todaySQL()))."\n";
					} else {
						echo "failure\n";
						echo "auth failed\n";
						$result=false;
						if ($this->_loggedIn) {
							Session::getInstance()->destroy();
						}
					}
				}
				if($result){
					if( !$check_only || ( $check_only && Session::getInstance()->recoverByUidAndRequest($this->u_id) ) ){
						$this->updateLastVisit($this->u_id);
//						Util::logFile("Auto auth OK:".$this->u_id.", check_only=".intval($check_only));
					} else {
						echo "failure\n";
						echo "auth failed\n";
						$result=false;
						if ($this->_loggedIn) {
							Session::getInstance()->destroy();
						}
//						Util::logFile("Epic fail");
					}
				} else {
//					Util::logFile("Epic fail x03");
				}
			} else {
//				Util::logFile("Epic fail x02");
				if(!$check_only){
					echo "failure\n";
					echo "auth failed\n";
					$result=false;
					if ($this->_loggedIn) {
						Session::getInstance()->destroy();
					}
				}
			}
		} else {
//			Util::logFile("Epic fail x01");
			if(!$check_only){
				echo "failure\n";
				echo "auth failed\n";
				if ($this->_loggedIn) {
					Session::getInstance()->destroy();
				}
			}
		}
//		Util::logFile(Session::getInstance()->getKey(), "Session::getInstance()->getKey()");
//		Util::logFile($_SESSION, "_SESSION in autologin"); Util::logFile($_COOKIE, "_COOKIE in autologin"); Util::logFile($_REQUEST, "_REQUEST in autologin");
		return $result;
	}

	public function login($login, $password, $remember) {
		$source = "system";
		Event::raise('user.before_login', array("login_from"=>$source));
		
		if(User::checkFloodPoint()) Util::redirect($this->get('returnUrl'), Text::_("Flood found"));
		if($login == '' || $password == '') Util::redirect($this->get('returnUrl'), Text::_('Wrong username or password'));
		
		if(backofficeConfig::$allowEmailLogin) $login = self::getLoginForEmail($login);
		if(!$login) Util::redirect($this->get('returnUrl'), Text::_('Login error')." (x001)");
		
		$secret = $this->encodePassword($login, $password);

		$query = "SELECT * FROM #__users WHERE u_source='".$source."' AND u_deleted=0 AND u_login='".$login."' AND u_secret='".$secret."'";

		/*****************************************************************************************/
		Database::getInstance()->setQuery($query);
		Database::getInstance()->loadObject($usr);
		$this->proceedLogin($usr, $remember);
		/*****************************************************************************************/

		Event::raise('user.after_login', array("usr"=>$usr, "login_from"=>$source));
		Util::redirect($this->get('returnUrl'));
	}

	public function elogin($login, $source, $email="") {
		$remember = 0;
		Event::raise('user.before_login', array("login_from"=>$source));
		
		if ($login == '' || $source == '' || $source == 'system') Util::redirect($this->get('returnUrl'), Text::_('Login error')." (x001)");
		
		$query = "SELECT * FROM #__users WHERE u_source='".$source."' AND u_deleted=0 AND u_login='".$login."'";
		
		/*****************************************************************************************/
		Database::getInstance()->setQuery($query);
		Database::getInstance()->loadObject($usr);
		$this->proceedLogin($usr, $remember);
		/*****************************************************************************************/
		
		// Lets update email if empty
		if (!$usr->u_email && $email && !self::isEmail($email, $usr->u_id) && $this->setEmail($email, $usr->u_id)){
			$usr->u_email = $email;
		}
		self::resetEAuth();
		
		/*****************************************************************************************/
		
		Event::raise('user.after_login', array("usr"=>$usr, "login_from"=>$source));
		Util::redirect($this->get('returnUrl'));
	}
	
	private function proceedLogin($usr, $remember){
		// Check if user loaded
		if (!is_object($usr)) {
			$server_uri = Portal::getURI(1, 1);
			if (preg_match("/administrator/", $this->get('returnUrl'))) $this->set('returnUrl', $server_uri."index.php?module=user&task=login&return_url=".base64_encode($server_uri."administrator/index.php"), true);
			Util::redirect($this->get('returnUrl'), Text::_('Login error')." (x002)");
		}
		
		// Check if activated
		if (intval($usr->u_activated) != 1 || intval($usr->u_role) == 0) {
			Util::redirect($this->get('returnUrl'), Text::_('User not activated or role not present'));
		}
		
		// Check blacklist
		$ip = User::getInstance()->getIP();
		$msgs=$this->inBlackList($usr->u_login, $usr->u_nickname, $ip, $usr->u_email);
		if (count($msgs)) {
			Util::redirect($this->get('returnUrl'), implode(",", $msgs));
		}
		
		// Check maintenance login
		if (siteConfig::$siteDisabled) {
			$this->u_id = $usr->u_id;
			$this->u_role = $usr->u_role;
			ACLObject::clearACL();
			if (!ACLObject::getInstance("maintenanceLogin", false)->canAccess()) {
				Util::redirect('index.php', Text::_('Site is disabled'));
			}
		}
		
		// Check if we come from logout page
		if (preg_match("/logout/", $this->get('returnUrl'))) {
			$this->set('returnUrl', Router::_("index.php"), true);
		}
		
		// Check if user email is not empty
		if (!$usr->u_email){
			$this->set('returnUrl', Router::_("index.php?module=user&view=reset&layout=email&return_url=".base64_encode($this->get('returnUrl'))), true);
		}
		// Start session
		Session::getInstance()->create($usr->u_id, $remember);
		$this->updateLastVisit($usr->u_id);
	}

/*
	public function login($login,$password,$remember) {
		Event::raise('user.before_login',array());
		if ($login == '' || $password == '') Util::redirect($this->get('returnUrl'),Text::_('Login error'));
		if((User::checkFloodPoint())) Util::redirect($this->get('returnUrl'),Text::_("Flood found"));
		$db = Database::getInstance();
		if(backofficeConfig::$allowEmailLogin) $login=self::getLoginForEmail($login);
		$return_url=$this->get('returnUrl');
		if(!$login) Util::redirect($return_url,Text::_('Wrong Username or password'));
		$secret=$this->encodePassword($login,$password);
		$query = "SELECT * FROM #__users WHERE u_source='system' AND u_deleted=0 AND u_login='".$login."' AND u_secret='".$secret."'";
		$db->setQuery($query);

		$usr = new stdClass();
		if (!$db->loadObject($usr)) {
//			$return_url=$this->get('returnUrl');
			$server_uri = Portal::getURI(1, 1);
			if (preg_match("/administrator/", $return_url)) $return_url=$server_uri."index.php?module=user&task=login&return_url=".base64_encode($server_uri."administrator/index.php");
			Util::redirect($return_url,Text::_('Wrong Username or password'));
		}
		if ((intval($usr->u_activated) != 1)||(intval($usr->u_role) == 0)) {
			Util::redirect($this->get('returnUrl'),Text::_('User not activated or role not present'));
		}
		$ip=User::getInstance()->getIP();
		$msgs=$this->inBlackList($login,$usr->u_nickname,$ip,$usr->u_email);
		if (count($msgs)) {
			Util::redirect($this->get('returnUrl'),implode(",", $msgs));
		}
		if (siteConfig::$siteDisabled) {
			$this->u_id = $usr->u_id;
			$this->u_role = $usr->u_role;
			ACLObject::clearACL();
			if (!ACLObject::getInstance("maintenanceLogin", false)->canAccess()) {
				Util::redirect('index.php',Text::_('Site is disabled'));
			}
		}
		Session::getInstance()->create($usr->u_id, $remember);
		$this->updateLastVisit($usr->u_id);
		Event::raise('user.after_login',array("usr"=>$usr));
		$return_to=$this->get('returnUrl');
		if (preg_match("/logout/",$return_to)) Util::redirect("index.php");
		else Util::redirect($this->get('returnUrl'));
	}

	public function elogin($login, $source, $email="") {
		Event::raise('user.before_login',array("login_from"=>$source));
		if ($login == '' || $source == '' || $source == 'system') Util::redirect($this->get('returnUrl'),Text::_('Login error'));
		$db = Database::getInstance();
		$query = "SELECT * FROM #__users WHERE u_source='".$source."' AND u_deleted=0 AND u_login='".$login."'";
		$db->setQuery($query);

		$usr = new stdClass();
		if (!$db->loadObject($usr)) {
			$return_url=$this->get('returnUrl');
			$server_uri = Portal::getURI(1, 1);
			if (preg_match("/administrator/", $return_url)) $return_url=$server_uri."index.php?module=user&task=login&return_url=".base64_encode($server_uri."administrator/index.php");
			Util::redirect($return_url,Text::_('Wrong Username or password'));
		}
		// Lets update email if empty
		if (!$usr->u_email && $email){
			$query = "SELECT count(u_id) FROM #__users WHERE u_email='".$email."'";
			$db->setQuery($query);
			if($db->loadResult()==0){
				$query = "UPDATE #__users SET u_email='".$email."' WHERE u_id=".$usr->u_id;
				$db->setQuery($query);
				if ($db->query()) $usr->u_email = $email;
			}
		}
		if ((intval($usr->u_activated) != 1)||(intval($usr->u_role) == 0)) {
			Util::redirect($this->get('returnUrl'),Text::_('User not activated or role not present'));
		}
		$ip=User::getInstance()->getIP();
		$msgs=$this->inBlackList($login,$usr->u_nickname,$ip,$usr->u_email);
		if (count($msgs)) {
			Util::redirect($this->get('returnUrl'),implode(",", $msgs));
		}
		if (siteConfig::$siteDisabled) {
			$this->u_id = $usr->u_id;
			$this->u_role = $usr->u_role;
			ACLObject::clearACL();
			if (!ACLObject::getInstance("maintenanceLogin", false)->canAccess()) {
				Util::redirect('index.php',Text::_('Site is disabled'));
			}
		}
		Session::getInstance()->create($usr->u_id, 0);
		$this->updateLastVisit($usr->u_id);
		Event::raise('user.after_login',array("usr"=>$usr, "login_from"=>$source));
		// Lets ask for email if empty
		self::resetEAuth();
		if (!$usr->u_email){
			Util::redirect(Router::_("index.php?module=user&view=reset&layout=email&return_url=".base64_encode($this->get('returnUrl'))));
		}	else Util::redirect($this->get('returnUrl'));
	}
*/
	public function inBlackList($login,$nickname,$ip,$email){
		$msg=array();
		$db=Database::getInstance();
		$sql="SELECT * FROM #__blacklist";
		$sql.=" WHERE bl_enabled=1 AND bl_deleted=0";
		$sql.=" AND((bl_val='".$login."' AND bl_type='login')";
		$sql.=" OR (bl_val='".$ip."' AND bl_type='ip')";
		$sql.=" OR (bl_val='".$nickname."' AND bl_type='nickname')";
		$sql.=" OR (bl_val='".$email."' AND bl_type='email'))";
		$db->setQuery($sql);
		$res=$db->loadObjectList();
		if (count($res)){
			foreach($res as $ibl){
				$msg[]=Text::_($ibl->bl_type." in blacklist");
			}
		}
		return $msg;
	}

	public function logout() {
		if ($this->_loggedIn) {
			Session::getInstance()->destroy();
		}
		Event::raise('user.logout');
		Util::redirect($this->get('returnUrl'));
	}

	public function isLoggedIn() {
		return $this->_loggedIn;
	}

	public function getID($session_id_4_guests=false) {
		if(!$this->u_id && $session_id_4_guests) return session_id();
		return $this->u_id;
	}

	public function getStatusID() {
		return $this->u_status;
	}
	public function getLogin() {
		return $this->u_login;
	}
	
	public function getNickname() {
		return $this->u_nickname;
	}

	public function getEmail() {
		return $this->u_email;
	}

	public function getProvider() {
		return $this->u_source;
	}

	public function isAdmin() {
		return $this->_isAdmin;
	}

	public static function getNicknameFor($uid) {
		$db = Database::getInstance();
		$query = "SELECT u_nickname FROM #__users WHERE u_id=".(int)$uid;
		$db->setQuery($query);
		return $db->loadResult();
	}

	public static function getEmailFor($uid) {
		$db = Database::getInstance();
		$query = "SELECT u_email FROM #__users WHERE u_id=".(int)$uid;
		$db->setQuery($query);
		return $db->loadResult();
	}

	public function getIdFor($nickname) {
		$db = Database::getInstance();
		$query = "SELECT u_id FROM #__users WHERE u_nickname='".$nickname."'";
		$db->setQuery($query);
		return intval($db->loadResult());
	}

	public function getInfoUserFor($nickname) {
		$db = Database::getInstance();
		$query = "SELECT u_id, u_email FROM #__users WHERE u_nickname='".$nickname."'";
		$db->setQuery($query);
		$res=false;
		$db->loadobject($res);
		return $res;
	}

	public static function getUserId($login) {
		$db = Database::getInstance();
		$query = "SELECT u_id FROM #__users WHERE u_login='".$login."'";
		$db->setQuery($query);
		return intval($db->loadResult());
	}

	public static function getUserIdByMail($email) {
		$db = Database::getInstance();
		$query = "SELECT u_id FROM #__users WHERE u_email='".$email."'";
		$db->setQuery($query);
		return intval($db->loadResult());
	}

	public static function getLoginFor($uid) {
		$db = Database::getInstance();
		$query = "SELECT u_login FROM #__users WHERE u_id=".(int)$uid;
		$db->setQuery($query);
		return $db->loadResult();
	}

	private static function getLoginForEmail($email) {
		if(!$email) return "";
		$db = Database::getInstance();
		$query = "SELECT u_login FROM #__users WHERE u_email='".$email."'";
		$db->setQuery($query);
		return $db->loadResult();
	}

	public static function getIDByAffiliate($affiliate){
		$db = Database::getInstance();
		$query = "SELECT u_id FROM #__users WHERE u_affiliate_code='".$affiliate."'";
		$db->setQuery($query);
		return $db->loadResult();
	}

	public static function getAffiliateByID($id){
		$db = Database::getInstance();
		$query = "SELECT u_affiliate_code FROM #__users WHERE u_id=".$id;
		$db->setQuery($query);
		return $db->loadResult();
	}	

	public function getRole() {
		return $this->u_role;
	}

	public function getLastVisit() {
		return $this->u_last_visit;
	}

	public function getRoleName() {
		$db = Database::getInstance();
		$query = "SELECT ar_name FROM #__acl_roles WHERE ar_id=".$this->u_role;
		$db->setQuery($query);
		$role_name = $db->loadResult();
		return $role_name;
	}

	public static function isUser($login,$psid=0) {
		$db = Database::getInstance();
		$query = "SELECT COUNT(*) FROM `#__users` WHERE LOWER(`u_login`)='".mb_strtolower($login)."'";
		if ($psid) $query.=" AND u_id<>".$psid;
		$db->setQuery($query);
		$cnt = $db->loadResult();
		return (intval($cnt) != 0);
	}

	public static function isEmail($mail,$psid=0) {
		$db = Database::getInstance();
		$query = "SELECT COUNT(*) FROM `#__users` WHERE LOWER(`u_email`)='".mb_strtolower($mail)."'";
		if ($psid) $query.=" AND u_id<>".$psid;
		$db->setQuery($query);
		$cnt = $db->loadResult();
		return (intval($cnt) != 0);
	}

	public static function isNickName($nickname,$psid=0) {
		$db = Database::getInstance();
		$query = "SELECT COUNT(*) FROM `#__users` WHERE LOWER(`u_nickname`)='".mb_strtolower($nickname)."'";
		if ($psid) $query.=" AND u_id<>".$psid;
		$db->setQuery($query);
		$cnt = $db->loadResult();
		return (intval($cnt) != 0);
	}

	public static function getReferral($affcode) {
		$db = Database::getInstance();
		$query = "SELECT `u_id` FROM `#__users` WHERE `u_affiliate_code`='".$affcode."'";
		$db->setQuery($query);
		return intval($db->loadResult());
	}

	public static function getNicknameByAffCode($affcode) {
		$db = Database::getInstance();
		$query = "SELECT `u_nickname` FROM `#__users` WHERE `u_affiliate_code`='".$affcode."'";
		$db->setQuery($query);
		return strval($db->loadResult());
	}

	public static function genAffiliateCode($uid, $length=12) {
		$length -= strlen(strval($uid));
		return strtoupper(Util::generateRandomString($length, "", 1).strval($uid));
	}

	public static function genValidationCode($uid, $length=25) {
		$vcode = strtoupper(Util::generateRandomString($length, "", 1).strval($uid));
		$db= Database::getInstance();
		$sql="UPDATE #__users set u_validation='".$vcode."' WHERE u_deleted=0 AND u_id=".(int)$uid;
		$db->setQuery($sql);
		$db->query();
		return $vcode;
	}

	public static function addUser($login, $nickname, $password, $email, $role, $referral="", $activated, $Discount=0, $priceType=1, $provider="system") {
		$db = Database::getInstance();
		$secret=User::getInstance()->encodePassword($login,$password);
		$affiliateCode = self::genAffiliateCode(0);
		$accountBonus=(int)siteConfig::$def_account_bonus;
		$query="INSERT INTO #__users 
							(u_id, u_affiliate_code, u_referral, u_login,
							u_secret, u_email, u_reg_date, u_nickname,
							u_account, u_points, u_discount, u_pricetype,
							u_role, u_rating, u_activated, u_deleted, u_source)
						VALUES(
							NULL, '".$affiliateCode."', '".$referral."', ".DBUtil::quote($login).", '"
							.$secret."', ".DBUtil::quote($email).", NOW(), ".DBUtil::quote($nickname).", "
							.$accountBonus.", 0, ".$Discount.", ".$priceType.", "
							.$role.", 0, ".(intval($activated) > 0 ? 1 : 0).", 0, '".$provider."')";
		
		$db->setQuery($query);
		if (!$db->query()) return false;
		$uid = $db->insertid();
		Profile::addProfile($uid);
		/*
		$affiliateCode = self::genAffiliateCode($uid);
		$query = "UPDATE `#__users` SET `u_affiliate_code`='".$affiliateCode."',`u_activated`='".$activated."' WHERE `u_id`=".$uid;
		$db->setQuery($query);
		if (!$db->query()) return false;
		*/
		Event::raise("user.register",array("userLogin"=>$login,"userEmail"=>$email));
		return $uid;
	}

	public static function saveUser($uid,$login,$nickname,$email,$role,$activated,$Discount=0,$priceType=1) {
		$db = Database::getInstance();
		$query = "UPDATE #__users SET u_login=".DBUtil::quote($login);
		$query.= ", u_nickname=".DBUtil::quote($nickname);
		if (defined("_ADMIN_MODE")) $query.= ", u_discount=".$Discount.", u_pricetype=".intval($priceType);
		$query.= ", u_email=".DBUtil::quote($email).", u_role=".intval($role);
		$query.= ", u_activated=".$activated."  WHERE u_id=".intval($uid);

		$db->setQuery($query);
		return $db->query();
	}

	public static function setPassword($uid,$password,$login) {
		$db = Database::getInstance();
		$secret=User::getInstance()->encodePassword($login,$password);
		$query = "UPDATE #__users SET u_secret='".$secret."' WHERE u_id=".intval($uid);
		$db->setQuery($query);
		$db->query();
	}

	public function setEmail($email, $uid){
		$db = Database::getInstance();
		if ($uid && $email) {
			$query = "UPDATE #__users SET u_email='".$email."' WHERE u_id=".intval($uid);
			$db->setQuery($query);
			return $db->query();
		} else return false;
	}

	/*
	 public function resetEmail($email){
		$db = Database::getInstance();
		$uid = User::getInstance()->getID();
		if ($uid && !User::isEmail($email)) {
			$query = "UPDATE #__users SET u_email='".$email."' WHERE u_id=".intval($uid);
			$db->setQuery($query);
			return $db->query();
		} else return false;
	}
	*/

	public function resetPassword($login,$password,$vcode) {
		$db = Database::getInstance();
		// возможно что смена пароля идет прямо из кабинета
		if (User::getInstance()->isLoggedIn()) $uid=User::getInstance()->getID();
		else { // а возможно не авторизовался но пришел по ссылке
			$sql_test = "SELECT u_id FROM #__users WHERE u_deleted=0 AND u_login='".$login."' AND u_validation='".$vcode."'";
			$db->setQuery($sql_test);
			$uid=$db->loadResult();
		}
		if ($uid) {
			$secret=$this->encodePassword($login,$password);
			$query = "UPDATE #__users SET u_validation='', u_secret='".$secret."' WHERE u_id=".intval($uid);
			$db->setQuery($query);
			return $db->query();
		} else return false;
	}
	
	public static function setActivation($uid,$activated) {
		$db = Database::getInstance();
		$query = "UPDATE `#__users` SET `u_activated`=".intval($activated)." WHERE `u_id`=".intval($uid);
		$db->setQuery($query);
		$db->query();
	}

	public static function deleteUser($uid) {
		$db = Database::getInstance();
		$query = "UPDATE `#__users` SET `u_deleted`=1 WHERE `u_id`=".intval($uid);
		$db->setQuery($query);
		return $db->query();
	}

	public static function getDefaultRole($name='') {
		$db = Database::getInstance();
		if($name){
			$query = "SELECT ar_id FROM #__acl_roles WHERE ar_name='".$name."'";
			$db->setQuery($query);
			return intval($db->loadResult());
		} else {
			$query="SELECT ar_id FROM #__acl_roles WHERE ar_id=".(int)backofficeConfig::$defaultUserRole;
			$db->setQuery($query);
			if($db->LoadResult()) return (int)backofficeConfig::$defaultUserRole;
			else return 0;
		}
	}

	public static function checkReferral($affcode=0) {
		if(!$affcode && isset($_COOKIE['referral'])) $affcode=$_COOKIE['referral'];
		if ($affcode) {
			$db = Database::getInstance();
			$query = "SELECT count(u_id) FROM `#__users` WHERE `u_affiliate_code`='".$affcode."'";
			$db->setQuery($query);
			if($db->LoadResult()>0) return 	$affcode;
			else return 0;
		} else return 0;
	}

	/** возвращает true если флуд **/
	public static function checkFloodPoint($delay=0){
		if(!$delay) $delay=backofficeConfig::$floodDelay;
		if (isset($_SESSION['flood_point'])) {
			if (($_SESSION['flood_point']+$delay) > microtime(1)) {
				return true;
			} else {
				self::setFloodPoint();
				return false;
			}
		} else {
			self::setFloodPoint();
			return false;
		}
	}

	public static function setFloodPoint(){
		$_SESSION['flood_point']=microtime(1);
	}

	public function encodePassword($login,$secret){
		return md5($secret.$login.DatabaseConfig::$dbSecret);
	}

	public static function resetEAuth($all=true){
		if ($all) {
			Session::unsetVar("eauth_type");
			Session::unsetVar("eauth_return");
		}
		Session::unsetVar("eauth_token");
		Session::unsetVar("eauth_token_secret");
	}

	public function increaseAccount($sum,$znak="+")	{
		$sql="update #__users set u_account=u_account".$znak.$sum ." WHERE u_id=".$this->u_id;
		Database::getInstance()->setQuery($sql);
		return  Database::getInstance()->query();
	}

	public function decreaseAccount($sum)	{
		return $this->increaseAccount($sum,"-");
	}

	public function increasePoints($sum,$znak="+") {
		$sql="update #__users set u_points=u_points".$znak.$sum ." WHERE u_id=".$this->u_id;
		Database::getInstance()->setQuery($sql);
		return  Database::getInstance()->query();
	}

	public function decreasePoints($sum) {
		return $this->increasePoints($sum,"-");
	}

	public function trasferAccount2Points($sum) {
		$sql="update #__users set u_points=u_points+".$sum.",u_account=u_account-".$sum." WHERE u_id=".$this->u_id;
		Database::getInstance()->setQuery($sql);
		return  Database::getInstance()->query();
	}

	public function updateLastVisit($uid=0){
		$sql="update #__users set u_last_visit=u_login_date, u_login_date=NOW() WHERE u_id=".$uid;
		Database::getInstance()->setQuery($sql);
		return  Database::getInstance()->query();
	}

	public static function getGuestRole() {
		return self::$guest_role;
	}
}
?>