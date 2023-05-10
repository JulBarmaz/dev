<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class Settings{
	public static function setVar($name, $val, $type="string", $module="system") {
		switch ($type){
			case "int":
				$_val=intval($val);
				break;
			case "float":
				$_val=floatval($val);
				break;
			case "boolean":
				if($val) $_val=1;
				else $_val=0;
				break;
			case "date":
				$_val = Date::toSQL($val, true);
				break;
			case "datetime":
				$_val = Date::toSQL($val);
				break;
			case "string":
			default:
				$_val = htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
				break;
		}
		$sql = "INSERT INTO #__settings (`s_id`, `s_module`, `s_name`, `s_value`, `s_type`)";
		$sql.= " VALUES (NULL, '".$module."', '".$name."', '".$_val."', '".$type."')";
		$sql.= "  ON DUPLICATE KEY UPDATE `s_value`='".$_val."'";
		Database::getInstance()->setQuery($sql);
		return Database::getInstance()->query();
	}
	public static function getVar($name, $module="system") {
		$result = ""; $obj = false;
		Database::getInstance()->setQuery("SELECT * FROM #__settings WHERE s_name='".$name."' AND s_module='".$module."' LIMIT 1");
		Database::getInstance()->loadObject($obj);
		if($obj){
			switch ($obj->s_type){
				case "int":
					$result = intval($obj->s_value);
					break;
				case "float":
					$result = floatval($obj->s_value);
					break;
				case "boolean":
					if($obj->s_value=="1") $result=true;
					else $result=false;
					break;
				case "date":
					$result = Date::fromSQL($obj->s_value, true, true);
					break;
				case "datetime":
					$result = Date::fromSQL($obj->s_value);
					break;
				case "string":
				default:
					$result = $obj->s_value;
					break;
			}
		}
		return $result;
	}
}
