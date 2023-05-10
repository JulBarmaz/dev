<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

final class Text {
	private static $_language					= 'ru';
	private static $_languageE				= 'ru_RU';
	private static $_defs 						= array();
	private static $_parsedIni				= array();
	private static $_available_langs	= array();
	public static function setLanguage($lang='ru') {
		$list=Files::getFolders(PATH_FRONT.DS."language".DS."common",array(".svn",".",".."),false);
		foreach($list as $l) self::$_available_langs[$l['filename']]=1;
		if(defined('_ADMIN_MODE')) {
			self::$_language = $lang;
		} else {
			$new_lang=Request::getSafe('lang','');
			if ($new_lang) {
				Session::getInstance()->setcookie('BARMAZ_language', $new_lang, time()+60*60*24*365,"/");
			}
			if ((!$new_lang)&&((!isset($_COOKIE['BARMAZ_language'])||(!$_COOKIE['BARMAZ_language'])))) {
				self::$_language = $lang;
			} else {
				if ($new_lang) $stored_lang=$new_lang; else	$stored_lang=$_COOKIE['BARMAZ_language'];
				if (array_key_exists($stored_lang,self::$_available_langs)) self::$_language = $stored_lang;
				else self::$_language = $lang;
			}
		}
		self::$_languageE = self::$_language."_".strtoupper(self::$_language);
	}
	
	public static function getAllLanguages() {
		return self::$_available_langs;
	}
	
	public static function getLanguage($short=true) {
		if ($short) return self::$_language;
		else return self::$_languageE;
	}
	
	public function checkIfKeyExists($key) {
		return isset(self::$_defs[$key]);
	}

	public static function dump() {
		foreach (self::$_defs as $key=>$def) {
			echo $key.'='.$def.'<br />';
		}
	}

	public static function _($key) {
		$value = self::get($key);
		return $value;
	}

	private static function get($key) {
		$ukey=strtoupper($key);
		if (isset(self::$_defs[$key])) {
			return self::$_defs[$key];
		}	elseif (isset(self::$_defs[$ukey])) {
			return self::$_defs[$ukey];
		}	elseif(!$key) {
			Debugger::getInstance()->translation("Empty value");
			if (siteConfig::$debugMode) return "Empty value"; else return $key;
		} else {
			Debugger::getInstance()->translation($key);
			return $key;
		}
	}

	public static function parseCustom($_name) {
		self::parse($_name, "custom");
	}
	public static function parseAllModules() {
		if (defined("_ADMIN_MODE")) {
			$modules = Module::getInstalledModules();
			foreach ($modules as $moduleName) self::parseModule($moduleName);
		}
	}

	public static function parseModule($moduleName) {
		self::parse($moduleName, "modules");
	}

	public static function parseWidget($widgetName) {
		self::parse($widgetName, "widgets");
	}

	public static function parsePlugin($_name) {
		self::parse($_name, "plugins");
	}

	public static function parse($iniName, $folder="") {
		self::parseBaseIni($iniName, $folder);
		self::parseTemplateIni($iniName, $folder);
	}

	public static function parseBaseIni($iniName, $folder="") {
		$iniPath = PATH_LANGUAGE.'common'.DS.self::$_language.DS.$folder.($folder ? DS : "").$iniName.'.ini';
		self::parseIni($iniPath);
	}

	public static function parseTemplateIni($iniName, $folder="") {
		$iniPath = PATH_LANGUAGE.Portal::getInstance()->getTemplate().DS.self::$_language.DS.$folder.($folder ? DS : "").$iniName.'.ini';
		self::parseIni($iniPath, 1);
	}
	
	public static function showTranslations() {
		Util::showArray(self::$_defs,'List translate value');
	}

	private static function parseIni($iniPath, $tmpl_file=0) {
		if (isset(self::$_parsedIni[$iniPath]) && self::$_parsedIni[$iniPath] == $iniPath) {
			// Nothing to do
		} else {
			if (is_file($iniPath)) {
				$newDefs = parse_ini_file($iniPath,false);
				foreach ($newDefs as $key=>$value) {
					self::$_defs[$key] = $value;
				}
				self::$_parsedIni[$iniPath] = $iniPath;
				if(siteConfig::$debugMode>1) Debugger::getInstance()->milestone("Loaded language : ".$iniPath);
			} else	if(siteConfig::$debugMode>1 && !$tmpl_file) Debugger::getInstance()->warning("Language file absent : ".$iniPath);
		}
	}
	
	public static function cutHtml($text, $cutLength=0, $cutTextSuffix="..."){
		$result = self::toHtml(self::fromHtml($text, $cutLength, $cutTextSuffix));
		// $result = preg_replace("/(<br \/>){2,}/", "<br />", $result);
		$result = preg_replace("/(<br\s*\/?>){2,}/", "<br />", $result);
		$result = preg_replace('/^\s*(?:<br\s*\/?>\s*)*/i', '', $result);
		$result = preg_replace('/\s*(?:<br\s*\/?>\s*)*$/i', '', $result);
		return $result;
	}
	
	public static function toHtml($text){
		// Newlines ONLY !!! Don't put anything else !!! 
		return str_replace(array("\r\n", "\n", "\r"), "<br />", $text);
	}
	
	public static function fromHtml($text, $cutLength=0, $cutTextSuffix=""){
		$pattern = array("/\n/", "/(<p\s*>\s*)/", "/(<\/p>)/", "/(<br\s*\/?>\s*)/");
		$replace = array("", "", "\n", "\n");
		$text = trim(strip_tags(preg_replace($pattern, $replace, $text)), "\n");
		if ($cutLength) $text = mb_substr($text, 0, $cutLength, DEF_CP).$cutTextSuffix;
		return $text;
	}
	
	public static function cr_lf_replace($text) {
		$text_arr = preg_split('#([\n\r]+)#Usi',$text);
		if(is_array($text_arr)){
			$text_arr_1 = array_map('trim', $text_arr);
			$text_arr_2 = array_diff($text_arr_1, array(''));
			$text = implode(CR_LF, $text_arr_2);
		} else $text = trim($text);
		$text =  preg_replace('/ {2,}/',' ',$text);
		return $text;
	}
	
	public static function mapjsAddSlashes($str)  {
		$pattern = array( "/\\\\/", "/\n/", "/\r/", "/\"/", "/\'/", "/&/", "/</", "/>/" );
		$replace = array( "\\\\\\\\", "\\n", "\\r", "\\\"", "\\'", "\\x26", "\\x3C", "\\x3E" );
		return preg_replace($pattern, $replace, $str);
	}
	
	public static function replaceUnicodeMatches($matches) {
		return html_entity_decode("&#x".(is_array($matches)&&count($matches) ? $matches[1] : $matches).";", ENT_COMPAT, "UTF-8");
	}
	
	public static function utf8_unicode($str, $as_arr=false){
		$uni_arr = array();
		$values = array();
		$lookingFor = 1;
		for ($i = 0; $i < strlen( $str ); $i++ ) {
			$thisValue = ord( $str[ $i ] );
			if ( $thisValue < 128 ) $uni_arr[] = $thisValue;
			else {
				if ( count( $values ) == 0 ) $lookingFor = ( $thisValue < 224 ) ? 2 : 3;
				$values[] = $thisValue;
				if ( count( $values ) == $lookingFor ) {
					$number = ( $lookingFor == 3 ) 
					? ( ( $values[0] % 16 ) * 4096 ) + ( ( $values[1] % 64 ) * 64 ) + ( $values[2] % 64 ) 
					: ( ( $values[0] % 32 ) * 64 ) + ( $values[1] % 64 );
					$uni_arr[] = $number;
					$values = array();
					$lookingFor = 1;
				}
			}
		}
		if ($as_arr) return $uni_arr;
		return '&#' . implode(";&#",$uni_arr) . ';';
	}
	public static function ucfirst($string) {
		$string = trim($string);
		$strlen = mb_strlen($string, DEF_CP);
		$firstChar = mb_substr($string, 0, 1, DEF_CP);
		$then = mb_substr($string, 1, $strlen - 1, DEF_CP);
		return mb_strtoupper($firstChar, DEF_CP) . $then;
	}
}

final class RusText {
	public static function SumAsCurrency($l) {
		$rub=floor($l);
		$kop=ceil(($l-$rub)*100);
		return $rub."руб.".($kop<10 ? "0".$kop : $kop)  ."коп.";
	}

	public static function SumInRus($L){
		$namerub[1]="рубль ";			$namerub[2]="рубля ";			$namerub[3]="рублей ";
		$nametho[1]="тысяча ";		$nametho[2]="тысячи ";		$nametho[3]="тысяч ";
		$namemil[1]="миллион ";		$namemil[2]="миллиона ";	$namemil[3]="миллионов ";
		$namemrd[1]="миллиард ";	$namemrd[2]="миллиарда ";	$namemrd[3]="миллиардов ";
		$kopeek[1]="копейка ";		$kopeek[2]="копейки ";		$kopeek[3]="копеек ";
		$s=" ";   $s1=" ";   $s2=" ";
		$kop=intval( ( $L*100 - intval( $L )*100 ));   $L=intval($L);
		if($L>=1000000000) {
			$many=0;	self::semantic(intval($L / 1000000000),$s1,$many,3);	$s.=$s1.$namemrd[$many];	$L%=1000000000;
		}
		if($L >= 1000000)	{
			$many=0;	self::semantic(intval($L / 1000000),$s1,$many,2);	$s.=$s1.$namemil[$many]; $L%=1000000;
			if($L==0)	$s.="рублей ";
		}
		if($L >= 1000){
			$many=0;	self::semantic(intval($L / 1000),$s1,$many,1);	$s.=$s1.$nametho[$many];	$L%=1000;
			if($L==0)	$s.="рублей ";
		}
		if($L != 0)	{
			$many=0;	self::semantic($L,$s1,$many,0);	$s.=$s1.$namerub[$many];
		}
		if($kop > 0) 	{
			$many=0;	self::semantic($kop,$s1,$many,1);	$s.=$s1.$kopeek[$many];
		}	else $s.=" 00 копеек";
		return $s;
	}
	
	public static function semantic($i,&$words,&$fem,$f){
		$_1_2[1]="одна ";	$_1_2[2]="две ";
		$_1_19[1]="один ";	$_1_19[2]="два ";	$_1_19[3]="три ";	$_1_19[4]="четыре ";	$_1_19[5]="пять ";
		$_1_19[6]="шесть ";	$_1_19[7]="семь ";	$_1_19[8]="восемь ";	$_1_19[9]="девять ";	$_1_19[10]="десять ";
		$_1_19[11]="одиннацать ";	$_1_19[12]="двенадцать ";	$_1_19[13]="тринадцать ";
		$_1_19[14]="четырнадцать ";	$_1_19[15]="пятнадцать ";	$_1_19[16]="шестнадцать ";
		$_1_19[17]="семнадцать ";	$_1_19[18]="восемнадцать ";	$_1_19[19]="девятнадцать ";
		$des[2]="двадцать ";	$des[3]="тридцать ";	$des[4]="сорок ";
		$des[5]="пятьдесят ";	$des[6]="шестьдесят ";	$des[7]="семьдесят ";
		$des[8]="восемдесят ";	$des[9]="девяносто ";
		$hang[1]="сто ";	$hang[2]="двести ";	$hang[3]="триста ";	$hang[4]="четыреста ";
		$hang[5]="пятьсот ";	$hang[6]="шестьсот ";	$hang[7]="семьсот ";	$hang[8]="восемьсот ";
		$hang[9]="девятьсот ";

		$words="";  $fl=0;
		if($i >= 100)	{
			$jkl = intval($i / 100);  $words.=$hang[$jkl];  $i%=100;
		}
		if($i >= 20)	{
			$jkl = intval($i / 10);   $words.=$des[$jkl];   $i%=10;   $fl=1;
		}
		switch($i){
			case 1: $fem=1; break;
			case 2:
			case 3:
			case 4: $fem=2; break;
			default: $fem=3; break;
		}
		if( $i ){
			if( $i < 3 && $f > 0 ){
				if ( $f >= 2 ) {
					$words.=$_1_19[$i];
				}  else { $words.=$_1_2[$i];
				}
			}
			else { $words.=$_1_19[$i];
			}
		}
	}
}
?>