<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class catalogUserdata extends BaseObject{
	protected $data="";
	protected $items=null;
	protected $order=null;
	protected $params=null;
	protected $inc_required=array();	// что из отсутствующего в шаблоне добавить в обязательные
	protected $exc_required=array();	// что из имеющегося в шаблоне исключить из обязательных
	protected $is_error=0;
	protected $error_text="";
	
	public function __construct($_type,$_data=false) {
		$this->initObj();
	}
	public function getParamsMask(){
		$params = array();
		return $params;
	}
	public function getConfigValue($key, $defaultValue="") {
		if (isset($this->params[$key])) return $this->params[$key]; else return $defaultValue;
	}
	public function assignOrder(&$order, &$items){
		$this->order=$order;
		$this->items=$items;
	}
	// для одномерных массивов
	protected function intersectRequired($arr=array()){
		return array_merge(array_diff($arr,$this->exc_required),$this->inc_required);
	}
	public function getData() {
		return $this->data;
	}
	public function getEncodedData() {
		return $this->getEncodedArray($this->data);
	}
	public function getDecodedData($data=array()) {
		if(backofficeConfig::$cryptoUserData) return Userdata::getInstance(User::getInstance()->u_id)->decode($data);
		else return json_decode(base64_decode($data), true);
	}
	public function getEncodedArray($data=array()) {
		if(backofficeConfig::$cryptoUserData) return Userdata::getInstance(User::getInstance()->u_id)->encode($data);
		else return base64_encode(json_encode($data));
	}
	public function renderInfo($data="") {
		/* JUST FOR OVERRIDE */
		return "";
	}
	public function renderForm() {
		return "";
	}
	public function checkData(&$err_message) {
		return true;
	}
	public function save() {
		return array();
	}
	public function getErrorText() {
		return $this->error_text;
	}
	public function isError() {
		return $this->is_error;
	}
}
?>