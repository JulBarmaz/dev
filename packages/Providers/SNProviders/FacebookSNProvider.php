<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class FacebookSNProvider extends OAuth {
	protected $auth_uri="https://www.facebook.com/dialog/oauth";
	protected $profile_uri="https://graph.facebook.com/me";
	protected $access_token_uri="https://graph.facebook.com/oauth/access_token";
	protected $api_version="v4.0";
	protected $useragent = "Facebook OAuth";
	
	public function __construct($_key, $_secret, $callback_uri){
		parent::__construct($_key, $_secret);
		$this->callback_uri = $callback_uri;
	}
	public function getLoginURI($params=array()){
		Util::setArrParam($params, "redirect_uri", $this->callback_uri);
		Util::setArrParam($params, "client_id", $this->_key);
		Util::setArrParam($params, "scope", "email");
		Util::setArrParam($params, "auth_type", "rerequest");
		return $this->auth_uri."?".http_build_query($params);
	}
	public function validateAuthResponse($params=array()){
		$this->error_desc=Request::getSafe("error_description");
		$this->error_code=Request::getSafe("error");
		$this->oauth_code=Request::getSafe("code","");
		if ($this->error_code) return false;
		if (!$this->oauth_code) return false;
		if (!$this->getAccessToken($params)) return false;
		return true;
	}
	public function getProfile($params=array()){
		if ($this->oauth_token) {
			Util::setArrParam($params, "client_id", $this->_key);
			Util::setArrParam($params, "redirect_uri", $this->callback_uri);
			Util::setArrParam($params, "access_token", $this->oauth_token);
			Util::setArrParam($params, "default_graph_version", $this->api_version);
			Util::setArrParam($params, "fields", "name,email");
			$url=$this->profile_uri."?".http_build_query($params);
			$response = $this->getUrl($url);
			if (isset($response["name"]) && isset($response["id"])) {
				$this->profile["uid"]=$response["id"];
				$this->profile["nickname"]=$response["name"];
				if(isset($response["email"])) $this->profile["email"]=$response["email"];
			} else {
				return false;
			}
		} else {
			return false;
		}
		return $this->profile;
	}
	protected function getAccessToken($params=array()){
		Util::setArrParam($params, "redirect_uri", $this->callback_uri);
		Util::setArrParam($params, "client_id", $this->_key);
		Util::setArrParam($params, "client_secret", $this->_secret);
		Util::setArrParam($params, "code", $this->oauth_code);
		Util::setArrParam($params, "scope", "email");
		$response=$this->getUrl($this->access_token_uri."?".http_build_query($params));
		if (isset($response["access_token"])) {
			$this->oauth_token=$response["access_token"];
			Session::setVar("eauth_token", $this->oauth_token);
			return true;
		} else {
			if(isset($response["error"])){
				$this->error_desc=Util::getArrParam($response["error"], "message", "");
				$this->error_code=Util::getArrParam($response["error"], "code", "");
			}
			return false;
		}
	}
}
?>