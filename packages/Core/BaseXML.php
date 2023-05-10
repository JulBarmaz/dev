<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class BaseXML{
	public static function array2xml($data, &$xml) {
		foreach($data as $key => $value) {
			if (is_array($value)) {
				if (!is_numeric($key)) {
					if(isset($value["@attributes"])){
						$subnode = $xml->addChild( preg_replace('/\d/', '', $key), (isset($value["@value"]) ? $value["@value"] : "") );
						foreach($value["@attributes"] as $attr_key=>$attr_value){
							$subnode->addAttribute($attr_key, $attr_value);
						}
					} else {
						$subnode = $xml->addChild(preg_replace('/\d/', '', $key));
						self::array2xml($value, $subnode);
					}
				}
			} else {
				$xml->addChild($key, $value);
			}
		}
		return $xml;
	}
	public static function nodeHasChilds($node, $subnodeName) {
		if(self::checkNode($node, $subnodeName)){
			return !is_string(self::checkNode($node, $subnodeName));
		}
		return false;
	}
	public static function checkNode($node, $subnodeName) {
		if (isset($node->{$subnodeName})) return true;
		return false;
	}
	public static function getNode($node, $subnodeName, $defaultValue="") {
		if (isset($node->{$subnodeName})) return $node->{$subnodeName};
		return $defaultValue;
	}
	public static function dropNode($node, $subnodeName) {
		if (isset($node->{$subnodeName})) {
			unset($node->{$subnodeName});
			return true;
		}
		return false;
	}
	public static function checkAttr($node, $attrName) {
		if (isset($node[$attrName])) return true;
		return false;
	}
	public static function getAttr($node, $attrName, $defaultValue="") {
		if (isset($node[$attrName])) return trim($node[$attrName]);
		return $defaultValue;
	}
	public static function xmlData($xml, $format=true) {
		if($format){
			$doc = new DOMDocument();
			$doc->preserveWhiteSpace = false;
			$doc->formatOutput = true;
			$doc->loadXML($xml->asXML());
			return $doc->saveXML();
		} else {
			return $xml->asXML();
		}
	}
}