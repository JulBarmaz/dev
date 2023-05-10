<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class Event {
	private static $_events	= array();
	public static function register($eventName,$pluginName) {
 		if (array_key_exists($eventName,self::$_events) && array_key_exists($pluginName,self::$_events[$eventName])) {
			Debugger::getInstance()->warning("Event reregistration");
		}
		else {
			self::$_events[$eventName][$pluginName] = $pluginName;
			Debugger::getInstance()->message("Event registered: ".$pluginName."=&gt;".$eventName);
		}
	}

	public static function raise($eventName, $params=array(), &$data=null) {
		$result=null;
		if (array_key_exists($eventName, self::$_events)) {
			Debugger::getInstance()->message("Event raised : ".$eventName." [Handler found]");
			foreach (self::$_events[$eventName] as $plugin=>$val) {
				$res=Plugin::getInstance($plugin)->executeEvent($eventName, $params, $data);
				// возващаем пока только текст
				if(!is_bool($res) && !is_null($res)) $result.=$res;
			}
		} else {
			Debugger::getInstance()->warning("Event raised : ".$eventName." [Handler absent]");
		}
		return $result;
	}
	public static function getEvents(){
		return self::$_events;
	}
}
?>