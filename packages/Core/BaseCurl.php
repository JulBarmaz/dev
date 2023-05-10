<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class BaseCurl extends BaseObject {
	/***********************************************/
	protected $response_format = "text";
	protected $response_json_decode = false;
	/***********************************************/
	protected $curl_useragent = "";
	protected $curl_connecttimeout = 3;
	protected $curl_timeout = 3;
	protected $curl_returntransfer = true;
	protected $curl_httpheader = array("Expect:");
	protected $curl_ssl_verifypeer = false;
	protected $curl_ssl_verifyhost = false;
	protected $curl_json_encode_postfields = false;
	protected $curl_header = null;
	/***********************************************/
	protected $curl_error_code = "";
	protected $curl_error_desc = "";
	protected $curl_http_code = "";
	/***********************************************/
	public function __construct() {
		$this->initObj();
	}
	/***********************************************/
	public function setResponseFormat($str){
		$this->response_format = $str;
		return $this;
	}
	public function setResponseJsonDecode($flag){
		if($flag) $this->response_format = "json";
		$this->response_json_decode = $flag;
		return $this;
	}
	/***********************************************/
	public function setUseragent($str){
		$this->curl_useragent = $str;
		return $this;
	}
	public function setConnTimeout($num){
		$this->curl_connecttimeout = $num;
		return $this;
	}
	public function setTimeout($num){
		$this->curl_timeout = $num;
		return $this;
	}
	public function setReturnTransfer($flag){
		$this->curl_returntransfer = $flag;
		return $this;
	}
	public function setHttpHeader($arr){
		$this->curl_httpheader = is_array($arr) ? $arr : array($arr);
		return $this;
	}
	public function addHttpHeader($str){
		$this->curl_httpheader[] = $str;
		return $this;
	}
	public function setVerifyPeer($flag){
		$this->curl_ssl_verifypeer = $flag;
		return $this;
	}
	public function setVerifyHost($flag){
		$this->curl_ssl_verifyhost = $flag;
		return $this;
	}
	public function setJsonEncodeParams($flag){
		$this->curl_json_encode_postfields = $flag;
		return $this;
	}
	public function setHeaderInclude($flag){
		$this->curl_header = $flag;
		return $this;
	}
	/***********************************************/
	public function getErrorCode(){
		return $this->curl_error_code;
	}
	public function getErrorText(){
		return $this->curl_error_desc;
	}
	public function getHttpCode(){
		return $this->curl_http_code;
	}
	/***********************************************/
	public function getResponse($url, $parameters = array()) {
		$response = $this->httpRequest($url, $parameters, "GET");
		return $response;
	}
	public function httpRequest($url, $postfields = NULL, $method = "POST") {
		if(!$url) return false;
		// $this->http_info = array();
		$ch = curl_init();
		/* Curl settings */
		//		curl_setopt($ch, CURLOPT_FAILONERROR, 1);
		//		curl_setopt($ch, CURLOPT_FRESH_CONNECT,1);
		//		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);// allow redirects
		curl_setopt($ch, CURLOPT_USERAGENT, $this->curl_useragent);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->curl_connecttimeout);
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->curl_timeout);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, $this->curl_returntransfer);
		if(!is_null($this->curl_httpheader) && is_array($this->curl_httpheader)) curl_setopt($ch, CURLOPT_HTTPHEADER, $this->curl_httpheader);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->curl_ssl_verifypeer);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $this->curl_ssl_verifyhost);
		if(!is_null($this->curl_header)) curl_setopt($ch, CURLOPT_HEADER, $this->curl_header);
		switch ($method) {
			case "GET":
				break;
			case "POST":
				curl_setopt($ch, CURLOPT_POST, true);
				if (is_array($postfields) && count($postfields)) {
					if($this->curl_json_encode_postfields){
						$payload = json_encode($postfields, JSON_PRESERVE_ZERO_FRACTION);
						// Util::pre($url, $payload);
						curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
					} else {
						curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
					}
				}
				break;
		}
		curl_setopt($ch, CURLOPT_URL, $url);
		$response = curl_exec($ch);
		if($response===false){
			$this->curl_error_code = curl_errno($ch);
			$this->curl_error_desc = curl_error($ch);
		} else {
			$this->curl_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		}
		curl_close($ch);
		
		if ($this->response_format === "json" && $this->response_json_decode) return json_decode($response, true);
		return $response;
	}
}
?>