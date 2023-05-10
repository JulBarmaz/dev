<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

final class Params {
	private static $ravno="{=}";
	private static $semicolon="{;}";
	private static $hvost=3;
	
	public static function parse($str){
		$res=null;
		$configArray = explode(self::$semicolon,$str);
		if (count($configArray)) {
			foreach($configArray as $config)	{
				$cfgPair = explode(self::$ravno,$config);
				if($cfgPair[0]&&isset($cfgPair[1])) $res[$cfgPair[0]]=$cfgPair[1];
			}
		}
		return $res;
	}
	public static function parse64($data){
		return json_decode(base64_decode($data));
	}
	public static function intersect($params, $def_params, $return_array=false){
		$param_arr=array();
		foreach ($def_params as $prmName=>$prmType) {
			if($prmType["vtype"]=="ro_string" || $prmType["vtype"]=="title" || $prmType["vtype"]=="tab") continue;
			$normalVal = "";
			switch ($prmType["vtype"]) {
				case "boolean":
					if (array_key_exists($prmName,$params)) $normalVal = "1"; else $normalVal = "0";
					break;
				case "integer":
					if (array_key_exists($prmName,$params)) {
						$normalVal = intval($params[$prmName]);
					}
					if(($normalVal=="" || $normalVal==0) && isset($prmType["fill_default"]) && $prmType["fill_default"]) $normalVal = $prmType["vdefault"];
					break;
				case "float":
					if (array_key_exists($prmName,$params)) {
						$normalVal = floatval(str_replace(',', '.',$params[$prmName]));
					}
					if(($normalVal=="" || $normalVal==0) && isset($prmType["fill_default"]) && $prmType["fill_default"]) $normalVal = $prmType["vdefault"];
					break;
				case "string":
				case "text":
					if (array_key_exists($prmName,$params)) {
						$normalVal = htmlspecialchars(htmlspecialchars_decode($params[$prmName],ENT_QUOTES),ENT_QUOTES,DEF_CP);
					}
					if($normalVal=="" && isset($prmType["fill_default"]) && $prmType["fill_default"]) $normalVal = $prmType["vdefault"];
					break;
				case "multiselect":
				case "table_multiselect":
				case "multiselect_method":
					if (array_key_exists($prmName,$params)) {
						if(is_array($params[$prmName])){
							$normalVal=implode(";", $params[$prmName]);
						}
					}
					break;
				default:
					if (array_key_exists($prmName,$params)) {
						$normalVal = $params[$prmName];
					}
					if($normalVal=="" && isset($prmType["fill_default"]) && $prmType["fill_default"]) $normalVal = $prmType["vdefault"];
					break;
			}
			if($return_array) $param_arr[$prmName] = $normalVal;
			else $param_arr[] = $prmName.self::$ravno.$normalVal;
		}
		if($return_array) return $param_arr;
		
		return implode(self::$semicolon, $param_arr);
	}
	
	/* Usage example: $_arr = array('3' => '10%','4' => '18%') */
	public static function transformParamsSource($_arr){
		$data=array();
		if(count($_arr)){
			foreach($_arr as $k=>$v){
				$data[]=array('id'=>$k, 'name'=>$v);
			}
		}
		return $data;
	}
}

?>