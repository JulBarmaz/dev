<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

final class CachingMachine extends BaseObject {
	private static $_pattern="<?php die(); ?>";
	public static function getHTMLCache($type,$widgetName,$cacheId,$backtime){
		if (defined("_ADMIN_MODE")) return;
		$backtime=$backtime*60; // in seconds
		switch($type){
			case "widgets":
				if (!$backtime) return;
				$path=PATH_CACHE.$type.DS;
				$role=User::getInstance()->getRole();
				$lang=Text::getLanguage();
				$file=$widgetName."_".$cacheId."_".$role."_".$lang.".php";
				break;
			default:
				if (!$backtime) $backtime=intval(siteConfig::$cacheLife); 
				$path=false;
				break;
		}
		if ($path && self::actualFile($path.$file,$backtime)) return self::readFile($path.$file);
		else return false;
	}
	public static function setHTMLCache($type,$name,$cacheId,$data,$backtime=0){
		if (defined("_ADMIN_MODE")||!intval(siteConfig::$cacheLife)) return;
		$backtime=$backtime*60; // in seconds
		switch($type){
			case "widgets":
				if (!$backtime) return;
				$path=PATH_CACHE.$type.DS;
				$role=User::getInstance()->getRole();
				$lang=Text::getLanguage();
				$file=$name."_".$cacheId."_".$role."_".$lang.".php";
				break;
			default:
				if (!$backtime) $backtime=intval(siteConfig::$cacheLife); 
				$path=false;
				break;
		}
		if ($path && (!self::actualFile($path.$file,$backtime))) self::writeFile($path,$file,$data);
	}	
	private static function actualFile($file,$backtime){
		if (is_file($file)){
			if (filemtime($file)<(time()-$backtime)){
				unlink($file);
				return false;
			} else return true;
		} else return false;
	}
	private static function writeFile($path,$file,$data){
		Files::checkFolder($path, true);
		$handle=fopen($path.$file,"w");
		if($handle) {
			fwrite($handle, self::$_pattern.$data);
			fclose($handle);
			Debugger::getInstance()->message("Write cache for ".$path.$file);
		}
	}
	private static function readFile($file){//return false;
		if (is_file($file)){
			$data=file_get_contents($file,false,null,mb_strlen(self::$_pattern,DEF_CP));
			Debugger::getInstance()->message("Read cache for ".$file);
			return $data;
		} else return false;
	}
}

?>