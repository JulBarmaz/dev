<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class VkontakteSNProvider extends OAuth {
	protected $auth_uri="https://oauth.vk.com/authorize";
	protected $profile_uri="https://api.vk.com/method/users.get";
	protected $access_token_uri="https://oauth.vk.com/access_token";
	protected $api_version="5.102";
	protected $useragent = "Vkontakte OAuth";
	
	public function __construct($_key, $_secret, $callback_uri){
		parent::__construct($_key, $_secret);
		$this->callback_uri = $callback_uri;
	}
	public function getLoginURI($params=array()){
		Util::setArrParam($params, "redirect_uri", $this->callback_uri);
		Util::setArrParam($params, "response_type", "code");
		Util::setArrParam($params, "client_id", $this->_key);
		Util::setArrParam($params, "scope", "email");
		Util::setArrParam($params, "auth_type", "rerequest");
		Util::setArrParam($params, "v", $this->api_version);
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
		if ($this->user_id && $this->oauth_token) {
			Util::setArrParam($params, "uid", $this->user_id);
			Util::setArrParam($params, "access_token", $this->oauth_token);
			Util::setArrParam($params, "v", $this->api_version);
			$url=$this->profile_uri."?".http_build_query($params);
			$response = $this->getUrl($url);
			if (isset($response["response"][0]["first_name"])&&isset($response["response"][0]["last_name"])&&isset($response["response"][0]["id"])) {
				$this->profile["uid"]=$response["response"][0]["id"];
				$this->profile["nickname"]=$response["response"][0]["first_name"]." ".$response["response"][0]["last_name"];
				$this->profile["email"]=$this->user_email;
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
		Util::setArrParam($params, "v", $this->api_version);
		$response=$this->getUrl($this->access_token_uri."?".http_build_query($params));
		if (isset($response["access_token"]) && isset($response["user_id"]) && $response["user_id"]) {
			$this->oauth_token=$response["access_token"];
			Session::setVar("eauth_token", $this->oauth_token);
			$this->user_id=$response["user_id"];
			if(isset($response["email"])) $this->user_email = $response["email"];
			return true;
		} else {
			$this->error_desc=Util::getArrParam($response, "error_description", "");
			$this->error_code=Util::getArrParam($response, "error", "");
			return false;
		}
	}
}
?>