<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class YandexSNProvider extends OAuth {
	protected $auth_uri = "https://oauth.yandex.ru/authorize";
	protected $access_token_uri = "https://oauth.yandex.ru/token";
	protected $profile_uri = "https://login.yandex.ru/info";
	// protected $api_version="5.102";
	protected $useragent = "Yandex OAuth";
	
	public function __construct($_key, $_secret, $callback_uri){
		parent::__construct($_key, $_secret);
		$this->callback_uri = $callback_uri;
	}
	public function getLoginURI($params=array()){
		Util::setArrParam($params, "client_id", $this->_key);
		Util::setArrParam($params, "redirect_uri", $this->callback_uri);
		Util::setArrParam($params, "response_type", "code");
		// Util::setArrParam($params, "v", $this->api_version);
		// Util::setArrParam($params, "state", "My own data. Will return from Yandex.");
		return $this->auth_uri."?".http_build_query($params);
	}
	public function validateAuthResponse($params=array()){
		$this->oauth_code=Request::getSafe("code","");
		// $this->error_desc=Request::getSafe("error_description"); // ?????????????????
		// $this->error_code=Request::getSafe("error"); // ?????????????????
		// if ($this->error_code) return false;
		if (!$this->oauth_code) return false;
		if (!$this->getAccessToken($params)) return false;
		return true;
	}
	protected function getAccessToken($params=array()){
		Util::setArrParam($params, "grant_type", "authorization_code");
		Util::setArrParam($params, "client_id", $this->_key);
		Util::setArrParam($params, "client_secret", $this->_secret);
		Util::setArrParam($params, "code", $this->oauth_code);
		$this->_httpheader = null;
		$this->_header = false;
		// Util::setArrParam($params, "v", $this->api_version);
		$response = $this->httpRequest($this->access_token_uri, "POST", $params);
		$response = json_decode($response, true);
		if (isset($response["access_token"])) {
			$this->oauth_token = $response["access_token"];
			Session::setVar("eauth_token", $this->oauth_token);
			return true;
		} else {
			$this->error_desc=Util::getArrParam($response, "error_description", "");
			$this->error_code=Util::getArrParam($response, "error", "");
			return false;
		}
	}
	
	public function getProfile($params=array()){
		if ($this->oauth_token) {
			Util::setArrParam($params, "format", "json");
			$this->_httpheader = array('Authorization: OAuth '.$this->oauth_token);
			$this->_header = false;
			// Util::setArrParam($params, "v", $this->api_version);
			$response = $this->httpRequest($this->profile_uri, "POST", $params);
			$response = json_decode($response, true);
			if (isset($response["first_name"]) && isset($response["last_name"]) && isset($response["id"]) && $response["id"]) {
				$this->profile["uid"]=$response["id"];
				$this->profile["nickname"]=$response["first_name"]." ".$response["last_name"];
				$this->profile["email"]=$response["emails"][0];
			} else {
				return false;
			}
		} else {
			return false;
		}
		return $this->profile;
	}
}
?>