<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class Request {
	private static function getFrom($arr, $key, $defVal='', $sef=false) {
		if ($sef || seoConfig::$sefMode) {
			$rval=Router::getInstance()->getRequest($key);
			if (!is_null($rval)) return $rval;
		}
		return Util::getArrParam($arr, $key, $defVal);
	}
	public static function get($key,$defVal='',$type='request') {
		switch(strtolower($type)) {
			case 'get':
				return self::getFrom($_GET,$key,$defVal,true);
				break;
			case 'post':
				return self::getFrom($_POST,$key,$defVal,true);
				break;
			case 'session':
				return self::getFrom($_SESSION,$key,$defVal);
				break;
			case 'cookie':
				return self::getFrom($_COOKIE,$key,$defVal);
				break;
			case 'server':
				return self::getFrom($_SERVER,$key,$defVal);
				break;
			case 'request':
			default:
				return self::getFrom($_REQUEST,$key,$defVal,true);
				break;
		}
	}
	public static function checkExists($key, $type='request') {
		switch(strtolower($type)) {
			case 'get':
				return array_key_exists($key, $_GET);
				break;
			case 'post':
				return array_key_exists($key, $_POST);
				break;
			case 'session':
				return array_key_exists($key, $_SESSION);
				break;
			case 'cookie':
				return array_key_exists($key, $_COOKIE);
				break;
			case 'server':
				return array_key_exists($key, $_SERVER);
				break;
			case 'request':
			default:
				return array_key_exists($key, $_REQUEST);
				break;
		}
	}
	public static function getBigInt($key,$defVal=0,$type='request') {
		return self::bigintval(self::get($key,intval($defVal),$type));
	}
	public static function getInt($key,$defVal=0,$type='request') {
		return intval(self::get($key,intval($defVal),$type));
	}
	public static function getFloat($key,$defVal=0,$type='request') {
		return floatval(str_replace(',', '.',self::get($key,$defVal,$type)));
	}
	public static function getBool($key,$defVal=false,$type='request') {
		return Util::toBool(self::get($key,$defVal,$type));
	}
	public static function getDateTime($key,$defVal=false,$type='request') {
		$result = self::getSafe($key,"",$type);
		if ($result) return Date::toSQL($result);
		else return $defVal;
	}
	public static function getDate($key,$defVal=false,$type='request') {
		$result = self::getSafe($key,"",$type);
		if ($result) return Date::toSQL($result, true);
		else return $defVal;
	}
	public static function makeSafe($result) {
		if (is_array($result)){
			if (count($result)){
				foreach($result as $key=>$val)	{
					$result[$key]=htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
				}
			}
			return $result;
		} else return htmlspecialchars($result, ENT_QUOTES, 'UTF-8');
	}
	public static function getSafe($key, $defVal='', $type='request') {
		$result=self::get($key,$defVal,$type);
		return self::makeSafe($result);
	}
	public static function getMethod(){
		return strtoupper(self::getSafe("REQUEST_METHOD","","server"));
	}
	public static function getHTML($key,$defVal='',$type='request') {
//		return addcslashes(self::get($key,$defVal,$type),"'");
		return addslashes(self::get($key,$defVal,$type));
	}
	public static function reductionOfTypeCustomField($field, $v_type) {
		switch ($v_type)	{
			case "int":
				$temp=self::getInt("p_".$field."_select",false);
				if ($temp==0){
					$temp=self::getInt($field);
				}
				break;
			case "boolean":
				$temp=self::getInt("p_".$field."_select",false);
				if ($temp==0){
					$temp=self::getInt($field);
				}
				if ($temp) $temp=1;
				break;
			case "string":
				$temp=self::get("p_".$field."_select",false);
				if ($temp=="") {
					$temp=self::getSafe($field);
				}
				break;
			case "text":
				$temp=self::getSafe($field);
				break;
			case "float":
				$temp_d=self::get("p_".$field."_select",false);
				if(!$temp_d){
					$temp_d=self::get($field);
				}
				$temp=floatval(str_replace(",",".",strval($temp_d)));
				break;
			case "currency":
				$temp_d=self::get("p_".$field."_select",false);
				if(!$temp_d){
					$temp_d=self::get($field);
				}
				$temp=floatval(str_replace(",",".",strval($temp_d)));
				break;
			case "date":
				$temp=self::get("p_".$field."_select",false);
				if ($temp==""){
					$temp=self::get($field,false);
				}
				if ($temp) $temp = Date::toSQL($temp, true);
				break;
			case "datetime":
				$temp=self::get("p_".$field."_select",false);
				if ($temp==""){
					$temp=self::get($field,false);
				}
				if ($temp) $temp = Date::toSQL($temp);
				break;
			case "timestamp":
				$temp=self::getInt("p_".$field."_select",false);
				if ($temp==""){
					$temp=self::get($field,false);
				}
				if ($temp)$temp = strval($temp);
				break;
//			case "blob":
				// данные перед помещением в запрос должны быть закодированы через base64
				// для сохранения целостности двоичной информации
//				$temp=self::getInt("p_".$field."_select",false);
//				if ($temp=="") { $temp=self::get($field,false); }
//				if ($temp)$temp = addslashes(base64_decode($temp)); // ?????????????? почему decode если должны быть закодированы через base64 
//				break;
			default:
				Debugger::getInstance()->warning("Undefined custom field value type : ".$v_type);
				$temp='';
		}
		return $temp;
	}
	public static function bigintval($val) {
		$val = trim($val);
		if (ctype_digit($val)) 	return $val;
		$val = preg_replace("/[^0-9](.*)$/", '', $val);
		if (ctype_digit($val)) return $val;
		return 0;
	}
}
?>