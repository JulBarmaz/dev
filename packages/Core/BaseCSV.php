<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class BaseCSV{
	private static $breakVal    = ";";
	private static $breakLine   = "\r\n";
	private static $capsVal     = '"';

	public static function parseCSVFile($file){
		$data=array();
		ini_set("auto_detect_line_endings", true);
		if (($handle = fopen($file, "r")) !== FALSE) {
			while (($str = fgetcsv($handle, 0, self::$breakVal, self::$capsVal)) !== FALSE) $data[]=$str;
			fclose($handle);
		}
		return $data;
	}
	public static function parseCSV($data, $_separator="",$_capsule=""){
		if (!$_separator) $_separator=self::$breakVal;
		if (!$_capsule) $_capsule=self::$capsVal;
		foreach ($data as $key => $line) {
			$line_array = array();
			$hvost = trim($line);
			while ($hvost != '') {
				if (mb_substr($hvost,0,1,DEF_CP) == $_capsule) {
					$left = -1;	$right = -1;
					while($left < strlen($hvost) && $left == $right && $left !== false) {
						$left = mb_strpos($hvost, $_capsule, $right+2,DEF_CP);
						$right = mb_strpos($hvost, $_capsule.$_capsule, $right+2,DEF_CP);
					}
					$line_array[] = str_replace($_capsule.$_capsule,$_capsule,mb_substr($hvost,1,$left-1,DEF_CP));
					$left = mb_strpos($hvost, $_separator, $left,DEF_CP);
					$hvost = ($left !== false) ? mb_substr($hvost,$left+1,mb_strlen($hvost,DEF_CP) - 1,DEF_CP) : '';
				} else {
					$left = mb_strpos($hvost, $_separator, 0,DEF_CP);
					$line_array[] = ($left !== false) ? mb_substr($hvost,0,$left,DEF_CP) : $hvost;
					$hvost = ($left !== false) ? mb_substr($hvost,$left+1,mb_strlen($hvost,DEF_CP) - 1,DEF_CP) : '';
				}
			}
			$data[$key] = $line_array;
		}
		return $data;
	}
	public static function getHeaders($csv_base_fields, $theheaders_array){
		$spec_arr=array();
		if (count($csv_base_fields) && count($theheaders_array)){
			foreach ($theheaders_array as $key=>$val){
				if (!in_array($val,$csv_base_fields)){
					$str=explode("_",$val);
					$ind=$str[1];
					$spec_arr[$val]=$ind;
				}
			}
		}
		return $spec_arr;
	}
	public static function buildCSV($headers, $rows, $_separator="",$_capsule=""){
		$docLine = '';
		if (!$_separator) $_separator=self::$breakVal;
		if (!$_capsule) $_capsule=self::$capsVal;
		$hc=0;
		foreach($headers as $ind=>$text) {
			$headers[$ind] = $_capsule.str_replace($_capsule,$_capsule.$_capsule,$text).$_capsule;
			$hc++;
		}
		$docLine.=implode($_separator,$headers).self::$breakLine;
		foreach($rows as $k1=>$row){
			$fc=0;
			foreach($row as $k2=>$val){
				if($fc<$hc){
					$row[$k2] = $_capsule.str_replace($_capsule,$_capsule.$_capsule,$val).$_capsule;
					$fc++;
				}
			}
			$docLine.=implode($_separator,$row).self::$breakLine;
		}
		return $docLine;
	}
}