<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class Session extends BaseObject {
	//---------- Singleton implementation ------------
	private $_coname = false;
	private static $_instance = null;
	public static function createInstance() {
		if (self::$_instance == null) {
			Debugger::getInstance()->milestone("Starting session");
			self::$_instance = new self();
			Debugger::getInstance()->milestone("Session started");
		}
	}
	public static function getInstance() {
		self::createInstance();
		return self::$_instance;
	}
	//------------------------------------------------
	private function __construct() {
		$this->initObj();
		$this->_coname=md5(Request::getSafe('SERVER_NAME','','server'));
		if(PHP_VERSION_ID<70300){
			ini_set('session.cookie_samesite', backofficeConfig::$sameSitecookie);
		}
		session_start();
		$this->processSessionData($this->getKey());
	}
	// Special for autologin check !!!
	public function recoverByUidAndRequest($uid=""){
		$sess_data = $this->getSessionByUid($uid);
		if ($sess_data && $sess_data->s_id) {
//			Util::logFile($sess_data->s_id, "Session=>recoverByUidAndRequest for ".$uid.", type=".backofficeConfig::$allowAutoExchange);
			if(
				backofficeConfig::$allowAutoExchange==1
				||	
				(backofficeConfig::$allowAutoExchange==2 && Request::getSafe("sess_id") && Request::getSafe("sess_id")==$sess_data->s_id)
				||	
				(backofficeConfig::$allowAutoExchange==3 && Request::checkExists($sess_data->s_id))
			){
				$_SESSION['sess_id'] = $sess_data->s_id;
//				Util::logFile($this->_coname, "_coname in Session=>recoverByUidAndRequest");
				$this->processSessionData($sess_data->s_id);
				return true;
			}
		}
		return false;
	}
	private function processSessionData($key=""){
		if ($key){
			$sess_data = $this->getSessionById($key);
			if ($sess_data && $sess_data->s_uid) {
				if ($sess_data->s_ip == $this->getIP() && $sess_data->s_agent == md5($this->getAgent())) {
					$this->set('user_id',$sess_data->s_uid, true);
					$user_vars=json_decode(base64_decode($sess_data->s_vars), true);
					$this->set('user_vars', (is_null($user_vars) ? array() : $user_vars), true);
					if(rand(1,3)==2) $this->update($key);
					$this->milestone("Session started", __FUNCTION__);
				} else {
					if (!Session::restoreToken(Request::getSafe("BARMAZ_TOKEN","")))	$this->delete($key);
				}
			}
		}
	}
	private function getSessionById($key=""){
		$sess_data=false;
		$db=Database::getInstance();
		$db->setQuery("SELECT * FROM #__sessions WHERE s_id='".$key."'");
		$db->loadObject($sess_data);
		return $sess_data;
	}
	private function getSessionByUid($uid=0){
		if(intval($uid)){
			$db=Database::getInstance();
			$db->setQuery("SELECT * FROM #__sessions WHERE s_uid=".intval($uid));
			$sess_data = $db->loadObjectList();
			if(count($sess_data)==1){
				return $sess_data[0];
			}
		}
		return false;
	}
	private function delete($key=""){
		if ($key){
			$db=Database::getInstance();
			$db->setQuery("DELETE FROM #__sessions WHERE s_id='".$key."'");
			$db->query();
		}
	}
	private function deleteByUID($uid=""){
		if ($uid){
			$db=Database::getInstance();
			$db->setQuery("DELETE FROM #__sessions WHERE s_uid=".intval($uid));
			$db->query();
		}
	}
	private function update($key=""){
		$db=Database::getInstance();
		if ($key) {
			$db->setQuery("UPDATE #__sessions SET s_last='".time()."' WHERE s_id='".$key."'");
			$db->query();
			$this->milestone("Session updated", __FUNCTION__);
		}
		$db->setQuery("DELETE FROM #__sessions WHERE s_last<".(time()-intval(60*siteConfig::$cookieLifeTime)));
		$db->query();
	}
	public function getKey(){
		return Request::getSafe("sess_id",Request::getSafe($this->_coname,"","cookie"),"session");
	}
	private function buildKey($uid,$ip,$agent,$time) {
		return md5($uid).md5($ip).$agent.md5($time);
	}
	public function create($uid, $remember=false, $autologin=false) { // Вызывается только во время логина
		$time = time(); 	
		$ip=$this->getIP();
		$agent=md5($this->getAgent());
		if($autologin) $this->deleteByUID($uid);
		$key = $this->buildKey($uid,$ip,$agent,md5($time));
		$sql="INSERT INTO #__sessions (s_id, s_uid, s_agent, s_ip, s_time, s_last) VALUES ('".$key."', '".$uid."', '".$agent."', '".$ip."', '".$time."', '".$time."')";
		Database::getInstance()->setQuery($sql);
		if(Database::getInstance()->query()) {
			if ($remember) $this->setcookie($this->_coname,$key,time()+60*siteConfig::$cookieLifeTime,"/");
			$_SESSION['sess_id'] = $key;
		}	else {
			$this->destroy();
		}
	}
	public function getIP() {
		return $_SERVER["REMOTE_ADDR"];
	}
	public function getAgent() {
		// заглушка из-за IE10
		return "HTTP_USER_AGENT";
		return $_SERVER['HTTP_USER_AGENT'];
	}
	public function destroy() {
		$this->delete($this->getKey());
		$this->setcookie($this->_coname,"",0,"/");
		$this->setcookie(session_name(),'',time()-42000,'/');
		unset($_SESSION['sess_id']);
		session_destroy();
		$this->milestone('Session destroyed', __FUNCTION__);
	}
	public static function setVar($var,$val) {
		if(isset($_SESSION[$var])) $old_val=$_SESSION[$var];
		else $old_val=null;
		$_SESSION[$var]=$val;
		return $old_val;
	}
	public static function unsetVar($var) {
		if(isset($_SESSION[$var])) unset($_SESSION[$var]);
	}
	public static function getVar($var) {
		if(isset($_SESSION[$var])) return $_SESSION[$var];
		else return null;
	}
	public static function restoreToken($code) {
		if (! $code) return 0;
		$crypt = new Crypta ();
		$proc = json_decode ( $crypt->xxtea_decrypt ( base64_decode ( $code ), backofficeConfig::$secretCode ), true );
		if (is_array ( $proc ) && isset ( $proc ['user'] ) && isset ( $proc ['time'] ) && ($proc ['time'] < time ()) && ($proc ['time'] > time () - 120)) {
			return intval ( $proc ['user'] );
		} else return 0;
	}
	public static function getToken() {
		$data_ppc ['user'] = User::getInstance ()->getID ();
		$data_ppc ['time'] = time ();
		$crypt = new Crypta ();
		return base64_encode ( $crypt->xxtea_encrypt ( json_encode ( $data_ppc ), backofficeConfig::$secretCode ) );
	}
	public function getUserVar($var){
		$user_vars=$this->get("user_vars");
		if(is_null($var)) return $user_vars;
		if(isset($user_vars[$var])) return $user_vars[$var];
		else return null;
	}
	public function saveUserVar($var, $val){
		$uid=$this->get('user_id');
		$key=$this->getKey();
		if($uid && $key && $var){
			$user_vars=$this->get("user_vars");
			$user_vars[$var] = $val;
			$this->set('user_vars', $user_vars, true);
			$str_vars = base64_encode(json_encode($user_vars));
			$db=Database::getInstance();
			$db->setQuery("UPDATE #__sessions SET s_vars='".$str_vars."' WHERE s_id='".$key."' AND  s_uid='".$uid."'");
			$db->query();
		}
	}
	
	/**
	 * установка куки в зависимости от версии
	 * @param string $name
	 * @param string $value
	 * @param int $expires
	 * @param string $path
	 * @param string $domain
	 * @param bool $secure
	 * @param bool $httponly
	 * @param string $samesite - none/lax/strict
	 */
	public function setcookie($name ,$value = "" ,$expires = 0 ,$path = "/",$domain = false,$secure = false ,$httponly = 0,$samesite=false)
	{
		if(!$name) return false;
		if(!$samesite) $samesite=backofficeConfig::$sameSitecookie;
		if(!$domain) $domain=siteConfig::$siteDomain;
		if($secure) $secure=backofficeConfig::$secureCoockie;
		
		
		
		if(PHP_VERSION_ID>=70300){
			$options=array(
					"expires"=>$expires,
					"path"=>$path,
					"domain"=>$domain,
					"secure"=>$secure,
					"httponly"=>$httponly,
					"samesite"=>$samesite
			);
			return  setcookie ($name,$value,$options);
		}else{
			/*$header_txt="Set-Cookie: ".$name."=".$value."; path=".$path."; domain=".$domain."; ";
			 if($httponly) $header_txt.=" HttpOnly;";
			 $header_txt.=" SameSite=".$samesite.";";
			 if($secure) $header_txt=" Secure;";
			 header($header_txt);
			 return true;
			 */
			if($httponly&&$secure){
				return setcookie($name,$value,$expires,$path,$domain,$secure,$httponly);
			}
			if($httponly)
				return setcookie($name,$value,$expires,$path,$domain,$httponly);
			if($secure)
				return  setcookie($name,$value,$expires,$path,$domain,true);
				return setcookie($name,$value,$expires,$path,$domain);
		}
	}
	
}
?>