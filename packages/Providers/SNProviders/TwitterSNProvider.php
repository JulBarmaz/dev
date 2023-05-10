<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class TwitterSNProvider extends OAuth {
	protected $auth_uri="https://api.twitter.com/oauth/authenticate";
	protected $profile_uri="https://api.twitter.com/1.1/users/show.json";
	protected $access_token_uri="https://api.twitter.com/oauth/access_token";
	protected $request_token_uri="https://api.twitter.com/oauth/request_token";
//	protected $api_version="";
	protected $useragent = "Twitter OAuth";
	private $url_separator="&";
	
	private $oauth_token_secret="";
	private $screen_name="";
	
	public function __construct($_key, $_secret, $callback_uri){
		parent::__construct($_key, $_secret);
		$this->callback_uri = $callback_uri;
	}
	public function getLoginURI($params=array()){
		if($this->getRequestToken()){
			Util::setArrParam($params, "oauth_token", Session::getVar("eauth_token"));
			return $this->auth_uri."?".http_build_query($params);
		} else {
			return false;
		}
	}
	protected function getRequestToken($params=array()){
		$oauth_nonce = md5(uniqid(rand(), true));
		$oauth_timestamp = time();
		$text_arr = array(
				'oauth_callback=' . urlencode($this->callback_uri) . $this->url_separator,
				'oauth_consumer_key=' . $this->_key . $this->url_separator,
				'oauth_nonce=' . $oauth_nonce . $this->url_separator,
				'oauth_signature_method=HMAC-SHA1' . $this->url_separator,
				'oauth_timestamp=' . $oauth_timestamp . $this->url_separator,
				'oauth_version=1.0'
		);
		$oauth_base_text = implode('', array_map('urlencode', $text_arr));
		$oauth_base_text = 'GET' . $this->url_separator . urlencode($this->request_token_uri) . $this->url_separator . $oauth_base_text;
		$oauth_signature = base64_encode(hash_hmac('sha1', $oauth_base_text, $this->_secret . $this->url_separator, true));
		$response=array();
		$text_arr = array(
				$this->url_separator . 'oauth_consumer_key=' . $this->_key,
				'oauth_nonce=' . $oauth_nonce,
				'oauth_signature=' . urlencode($oauth_signature),
				'oauth_signature_method=HMAC-SHA1',
				'oauth_timestamp=' . $oauth_timestamp,
				'oauth_version=1.0'
		);
		$url = $this->request_token_uri . '?oauth_callback=' . urlencode($this->callback_uri) . implode('&', $text_arr);
		$response_str = $this->httpRequest($url, "GET");
		parse_str($response_str, $response);
		if(isset($response["oauth_token"]) && isset($response["oauth_token_secret"])){
			Session::setVar("eauth_token", $response["oauth_token"]);
			Session::setVar("eauth_token_secret", $response["oauth_token_secret"]);
			return true;
		} else {
			$response=json_decode($response_str, true);
			if(isset($response["errors"][0])){
				$this->error_desc=Util::getArrParam($response["errors"][0], "message", "");
				$this->error_code=Util::getArrParam($response["errors"][0], "code", "");
			}
			return false;
		}
	}
	public function validateAuthResponse($params=array()){
		$params["oauth_token"] = Request::getSafe('oauth_token');
		$params["oauth_verifier"] = Request::getSafe('oauth_verifier');
		if($params["oauth_token"] && $params["oauth_verifier"]) {
			if($this->getAccessToken($params)) return true;
			else return false;
		} else {
			$this->error_desc="Tokens absent in request";
			$this->error_code="validateAuthResponse";
			return false; // тут бы сообщение об ошибке
		}
	}
	protected function getAccessToken($params=array()){
		$oauth_nonce = md5(uniqid(rand(), true));
		$oauth_timestamp = time();
		
		$oauth_base_text = "GET&";
		$oauth_base_text .= urlencode($this->access_token_uri)."&";
		
		$text_arr = array(
				'oauth_consumer_key=' . $this->_key . $this->url_separator,
				'oauth_nonce=' . $oauth_nonce . $this->url_separator,
				'oauth_signature_method=HMAC-SHA1' . $this->url_separator,
				'oauth_token=' . $params["oauth_token"] . $this->url_separator,
				'oauth_timestamp=' . $oauth_timestamp . $this->url_separator,
				'oauth_verifier=' . $params["oauth_verifier"] . $this->url_separator,
				'oauth_version=1.0'
		);
		
		$key = $this->_secret . $this->url_separator . Session::getVar("eauth_token_secret");
		$oauth_base_text = 'GET' . $this->url_separator . urlencode($this->access_token_uri) . $this->url_separator . implode('', array_map('urlencode', $text_arr));
		$oauth_signature = base64_encode(hash_hmac("sha1", $oauth_base_text, $key, true));
		
		$text_arr = array(
				'oauth_nonce=' . $oauth_nonce,
				'oauth_signature_method=HMAC-SHA1',
				'oauth_timestamp=' . $oauth_timestamp,
				'oauth_consumer_key=' . $this->_key,
				'oauth_token=' . urlencode($params["oauth_token"]),
				'oauth_verifier=' . urlencode($params["oauth_verifier"]),
				'oauth_signature=' . urlencode($oauth_signature),
				'oauth_version=1.0'
		);
		$url = $this->access_token_uri . '?' . implode('&', $text_arr);
		$response_str = $this->httpRequest($url, "GET");
		parse_str($response_str, $response);
		if(isset($response["oauth_token"]) && isset($response["oauth_token_secret"])){
			if(isset($response["user_id"])) $this->user_id=$response["user_id"];
			if(isset($response["oauth_token"])) $this->oauth_token=$response["oauth_token"];
			if(isset($response["oauth_token_secret"])) $this->oauth_token_secret=$response["oauth_token_secret"];
			if(isset($response["screen_name"])) $this->screen_name=$response["screen_name"];
			return true;
		} else {
			$this->error_desc=$response_str;
			$this->error_code="getAccessToken";
			return false;
		}
	}
	public function getProfile($params=array()){
		if ($this->user_id && $this->oauth_token && $this->oauth_token_secret && $this->screen_name) {
			$oauth_nonce = md5(uniqid(rand(), true));
			$oauth_timestamp = time();
			
			$text_arr = array(
					'oauth_consumer_key=' . $this->_key . $this->url_separator,
					'oauth_nonce=' . $oauth_nonce . $this->url_separator,
					'oauth_signature_method=HMAC-SHA1' . $this->url_separator,
					'oauth_timestamp=' . $oauth_timestamp . $this->url_separator,
					'oauth_token=' . $this->oauth_token . $this->url_separator,
					'oauth_version=1.0' . $this->url_separator,
					'screen_name=' . $this->screen_name
			);
			$oauth_base_text = 'GET' . $this->url_separator . urlencode($this->profile_uri) . $this->url_separator . implode('', array_map('urlencode', $text_arr));
			
			$key = $this->_secret . '&' . $this->oauth_token_secret;
			$signature = base64_encode(hash_hmac("sha1", $oauth_base_text, $key, true));
			
			// получаем данные о пользователе
			$text_arr = array(
					'oauth_consumer_key=' . $this->_key,
					'oauth_nonce=' . $oauth_nonce,
					'oauth_signature=' . urlencode($signature),
					'oauth_signature_method=HMAC-SHA1',
					'oauth_timestamp=' . $oauth_timestamp,
					'oauth_token=' . urlencode($this->oauth_token),
					'oauth_version=1.0',
					'screen_name=' . $this->screen_name
			);
			
			$url = $this->profile_uri . '?' . implode($this->url_separator, $text_arr);
			
			$response_str = $this->httpRequest($url, "GET");
			$response = json_decode($response_str, true);
			if (isset($response["id"]) && isset($response["name"]) && isset($response["screen_name"])) {
				$this->profile["uid"]=$response["id"];
				if($response["name"]) $this->profile["nickname"]=$response["name"];
				else $this->profile["nickname"]=$response["screen_name"];
			} else {
				$response=json_decode($response_str, true);
				if(isset($response["errors"][0])){
					$this->error_desc=Util::getArrParam($response["errors"][0], "message", "");
					$this->error_code=Util::getArrParam($response["errors"][0], "code", "");
				}
				return false;
			}
		} else {
			return false;
		}
		return $this->profile;
	}
}
?>