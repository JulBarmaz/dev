<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

final class Util {
	private static $locales = array('en'=>array('en_US.UTF-8', 'C'), 'ru'=>array('ru_RU.UTF-8', 'C')); // PHP locales
	private static $sortField='';
	private static $sortDir=true;

	/******************** Core ********************/
	public static function lastModifiedHeader($datetime){
		if($datetime && !is_null($datetime)){
			$IfModifiedSince = false;
			$LastModified_unix = Date::mysqldatetime_to_timestamp($datetime);
			
			if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) $IfModifiedSince = strtotime(substr($_SERVER['HTTP_IF_MODIFIED_SINCE'], 5));
			elseif (isset($_ENV['HTTP_IF_MODIFIED_SINCE'])) $IfModifiedSince = strtotime(substr($_ENV['HTTP_IF_MODIFIED_SINCE'], 5));
			else {
				$LastModified = gmdate("D, d M Y H:i:s \G\M\T", $LastModified_unix);
				header('Last-Modified: '. $LastModified);
			}
			if ($IfModifiedSince && $IfModifiedSince >= $LastModified_unix) {
				header($_SERVER['SERVER_PROTOCOL'] . ' 304 Not Modified');
				self::halt();
			}
		}
	}
	public static function sendHeader($code=0){
		switch($code){
			case 301:
				header('HTTP/1.1 301 Moved Permanently');
				break;
			case 403:
				header('HTTP/1.1 403 Forbidden');
				break;
			case 404:
				header('HTTP/1.0 404 Not Found');
				break;
			case 500:
				header('HTTP/1.1 500 Internal Server Error');
				break;
			case 503:
				header("HTTP/1.1 503 Service Temporarily Unavailable");
				break;
			default:
				break;
		}
	}
	public static function getLocale($language='en') {
		$locale = self::$locales['en'];
		if (array_key_exists($language,self::$locales))
			$locale = self::$locales[$language];
		return $locale;
	}
	public static function setupLocale() {
		setlocale( LC_ALL, self::getLocale(Text::getLanguage()));
		setlocale( LC_NUMERIC, self::getLocale("en"));
	}
	public static function halt($msg="") {
		die($msg); // это для замены die() в коде
	}
	public static function fatalError($message, $codeerror="503") {
		if(siteConfig::$debugMode) Util::logFile($message.CR_LF.$code);
		if(siteConfig::$debugMode) Util::logFile(Util::traceStack());
		
		@ob_end_clean();
		switch($codeerror){
			case '503':
				self::sendHeader(503);
				ErrorPages::render503($message);
				break;
			case '404':
				self::sendHeader(404);
				ErrorPages::render404("/",$message);
				break;
			default:
				self::sendHeader($codeerror);
				ErrorPages::renderRedirect("/", $message, $codeerror);
				break;
		}
	}
	public static function redirect($url, $message='', $code=301, $referer="") {
		// Debugger редиректов. НЕ УДАЛЯТЬ !!!
		// if(siteConfig::$debugMode) Util::logFile($url.CR_LF.$message.CR_LF.$code.CR_LF.$referer);
		// if(siteConfig::$debugMode) Util::logFile(Util::traceStack());
		if ($message) {
			Session::setVar('BARMAZ_message', $message);
			Session::setVar('BARMAZ_message_code', $code);
		} else {
			Session::setVar('BARMAZ_message', "");
			Session::setVar('BARMAZ_message_code', false);
		}
		$_sent=headers_sent();
		
		/************************************************************/
		/******************* Let's remove cycling *******************/
		/************************************************************/
		if(defined("_BARMAZ_REDIRECT_CYCLED")){
			if (siteConfig::$debugMode > 100){
				echo "<h3>Redirect 3 cycled</h3>";
				echo "<pre>First REQUEST: "._BARMAZ_REDIRECT_CYCLED."</pre>";
				self::pre($url, $message, $code, $referer);
				echo self::traceStack(true, false, false);
			} else {
				@ob_end_clean(); // Maybe we should not ?
				self::sendHeader(503); // Maybe we should not ?
				echo Text::_("Service Temporarily Unavailable");
			}
			self::halt();
		} else {
			define("_BARMAZ_REDIRECT_CYCLED", print_r($_REQUEST, true));
		}
		/************************************************************/
		if(siteConfig::$debugMode>100 || (siteConfig::$debugMode && User::getInstance()->isAdmin())){
			ErrorPages::renderRedirect($url, $message, $code);
		} elseif(Request::getSafe("option") == "ajax") {
			echo json_encode(array("code" => $code, "message" => $message, "redirect_url" => $url));
			Session::setVar('BARMAZ_message', "");
			Session::setVar('BARMAZ_message_code', false);
		} else {
			@ob_end_clean(); // clear output buffer
			if ($_sent) {
				echo "<script>document.location.href='$url';</script>\n";
			} else {
				$_SESSION['BARMAZ_redirect_code']=$code;
				switch($code){
					case 404:
						self::sendHeader(404);
						if (seoConfig::$stop404) ErrorPages::render404($url, $message);
						else echo "<script>document.location.href='$url';</script>\n";
						break;
					case 301:
						self::sendHeader(301);
						header("Location: ".$url,true, 301);
						break;
					case 302:
					default:
						header("Location: ".$url);
						break;
				}
			}
		}
		self::halt();
	}
	public static function download($filepath, $filename, $redirect="index.php", $msg="") {
		if (headers_sent()) {
			Util::redirect($redirect,$msg);
		}	elseif(!is_file($filepath.$filename)) {
			Util::redirect($redirect,Text::_("File absent"));
		}	else {
			while (ob_get_level()) { ob_end_clean(); }	// clear output buffer
			header ("Content-Type: application/octet-stream");
			header ("Accept-Ranges: bytes");
			header ("Content-Length: ".filesize($filepath.$filename));
			header ("Content-Disposition: attachment; filename=".basename($filename));
			@readfile($filepath.$filename);
			self::halt();
		}
	}
	public static function getCurrentProtocol() {
		$scheme	=	isset($_SERVER['HTTP_SCHEME'])
		? $_SERVER['HTTP_SCHEME']
		: (
				(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') || 443 == $_SERVER['SERVER_PORT']
				? 'https'
				: 'http'
				);
		return $scheme;
	}
	public static function getReturnUrl($encode=true) {
		if(isset($_SERVER['REQUEST_URI'])) $returnUrl = $_SERVER['REQUEST_URI'];
		else $returnUrl="index.php";
		if ($encode) $returnUrl = base64_encode($returnUrl);
		return $returnUrl;
	}
	public static function getRefererUrl($encode=true) {
		if(isset($_SERVER['HTTP_REFERER'])) $returnUrl = $_SERVER['HTTP_REFERER'];
		else $returnUrl="index.php";
		if ($encode) $returnUrl = base64_encode($returnUrl);
		return $returnUrl;
	}
	public static function checkServerSettings($critical=false) {
		$settings_ok = true; $msg="";
		if (intval(ini_get('register_globals')) == 1) { $settings_ok = false; $msg="CMS required register_globals to be disabled.<br />"; }
		if (version_compare(phpversion(), '5.3.0', '<') == true) { $settings_ok = false; $msg="CMS required PHP version >=5.3<br />"; }
		if (!$settings_ok && $critical) Util::halt("Invalid server settings.<br />".$msg);
		return $settings_ok;
	}
	public static function checkIntrusion($queryString=false) {
		// проверяем входящую строку или то что пришло в запросе после знака вопроса
		if (!$queryString) {
			/*
			 * ============================================= * 
			 * ПОКА ЗАРЕМИМ, ИБО ПОИСКОВИКИ, СОЦИАЛЬНЫЕ СЕТИ *
			 * ============================================= *
			 * 
			// запрещаем ссылки вида http://сайт/?
			if(preg_match("/^\/\?(.*)/",$_SERVER['REQUEST_URI'])){ self::fatalError(Text::_("Page absent"),"404"); }
			*/
			// забираем весь QUERY_STRING для дальнейшей проверки	
			$queryString = strtolower($_SERVER['QUERY_STRING']);
		}
		/*
		 * ===================================================== *
		 * ПОКА ОСТАВИМ, НО НАСКОЛЬКО АКТУАЛЬНО НЕ РАЗБИРАЛИ ЕЩЕ *
		 * ===================================================== *
		*/ 
		// проверяем встречаемость 5 подряд символов
		if (preg_match("/([OdWo5NIbpuU4V2iJT0n]{5}) /", rawurldecode($loc=$queryString), $matches)) { self::fatalError("", "403"); }
		if ((stristr($queryString,'%20union%20'))
		||(stristr($queryString,'/*'))
		||(stristr($queryString,'*/union/*'))
		||(stristr($queryString,'c2nyaxb0'))
		||(stristr($queryString,'+union+'))
		||(stristr($queryString,'http://'))
		||((stristr($queryString,'cmd=')) && (!stristr($queryString,'&cmd')))
		||((stristr($queryString,'exec')) && (!stristr($queryString,'execu')))
		||(stristr($queryString,'concat'))) {
//			self::fatalError(Text::_("Page absent"),"404");
			header("Location: index.php");
		}
		return $queryString;
	}
	/******************** Array management ********************/
	public static function setArrParam(&$arr, $name, $val, $overwrite=false){
		if($overwrite) $arr[$name]=$val;
		elseif(!array_key_exists($name, $arr)) $arr[$name]=$val;
	}
	public static function getArrParam($arr, $name, $def=null) {
		$return = null;
		if (is_array($arr)) {
			if (isset($arr[$name])) $return = $arr[$name];
			else return $def;
		} else if(is_object($arr)) {
			if (property_exists($arr, $name)) $return = $arr->{$name};
			else return $def;
		} else return $def;
		if(empty($return) && $return!=="0" && $return!==0 && $return!==false && !is_null($def)) return $def;
		if (is_string($return)) $return = trim($return);
		return $return;
	}
	// @TODO REMOVE NEXT
	/* OLD */
	/*
	public static function getArrParam($arr, $name, $def=null) {
		$return = null;
		if (is_array($arr)) {
			if (isset($arr[$name])) $return = $arr[$name];
		} else if(is_object($arr)) {
			if (property_exists($arr, $name)) $return = $arr->{$name};
		} else return $def;
		if($return == null) return $def;
		if (is_string($return)) $return = trim($return);
		return $return;
	}
	*/
	// @TODO REMOVE NEXT
	/* Barma */
	/*
	public static function getArrParam($arr, $name, $def=null,$strict=false) {
		$return = null;
		if (is_array($arr)) {
			if (isset($arr[$name])) $return = $arr[$name];
		} else if(is_object($arr)) {
			if (property_exists($arr, $name)) $return = $arr->{$name};
		} else return $def;
		if($strict){ 
			if($return === null) return $def;
		} else {
			if($return == null) return $def;
		}
		if (is_string($return)) $return = trim($return);
		return $return;
	}
	*/
	// Native function available in PHP 7 ( >= 7.3.0 )
	public static function array_key_first($arr) {
		if(function_exists("array_key_first")){
			return array_key_first($arr);
		} else {
			if(count($arr)){
				reset($arr);
				return key($arr);
			}
		}
		return null;
	}
	public static function getArrayOfNumbers($param) {
		$arr=array();
		if($param) {
			for ($i = 1; $i <=$param; $i++) {
				$arr[$i]=$i;
			}
		}
		return $arr;
	}
	public static function sortStdClassArray(&$array,$fieldname,$asc=true) 	{
		self::$sortField=$fieldname;
		self::$sortDir=$asc;
		return uasort($array,array("Util","stdSort"));
	}
	private static function stdSort($f1,$f2) {
		$fieldname=Util::$sortField;
		if($f1->{$fieldname} < $f2->{$fieldname}) return (self::$sortDir? -1: 1);
		elseif($f1->{$fieldname} > $f2->{$fieldname}) return (self::$sortDir? 1: -1);
		else return 0;
	}
	// @TODO бета версия функции чистки массива
	public static function getSafeArray($_data) {
		if (is_array($_data)) {
			foreach ($_data as $_ind => $_val) {
				$_data[$_ind] = self::getSafeArray($_val);
			}
			return $_data;
		}
		return htmlspecialchars($_data, ENT_QUOTES, 'UTF-8');
	}
	// получаем максимальную глубину массива
	public static function deepArray($array, $deep=0) {
		if(!is_array($array)||!count($array)) return $deep;
		if(!$deep) $deep=1;
		foreach ($array as $key => $val) {
			if (is_array($val)) {
				$deep+=self::deepArray($val, $deep);
			}
		}
		return $deep;
	}
	/******************** Logs management ********************/
	public static function writeLog($message, $file="common.log", $with_date=true, $start_with_clear=false) {
		if($start_with_clear){
			$f=fopen(PATH_LOGS.$file, "w+");
		} else {
			$f=fopen(PATH_LOGS.$file, "a");
		}
		if(is_array($message) || is_object($message)) $message=print_r($message, true);
		$logmessage = ($with_date ? Date::nowSQL()." => " : "").$message.CR_LF;
		fwrite($f, $logmessage);
		fclose($f);
	}
	public static function logFile($message, $title="", $filterByIP=false, $trace_call = false) {
		if($trace_call) {
			$trace = self::traceFunctionCallFrom(1);
			if($trace) $title.= ($title ? " " : "")."[".$trace."]";
		}
		if(!$filterByIP || !property_exists("siteConfig","debugIP") || ($filterByIP && siteConfig::$debugIP && Request::get("REMOTE_ADDR", "", "server")==siteConfig::$debugIP)) {
			$f=fopen(PATH_TMP."debug.log","a");
			if(is_array($message) || is_object($message)) $message=print_r($message, true);
			$logmessage = Date::nowSQL().($title ? " => ".$title.CR_LF: " => ").$message.CR_LF."-------".CR_LF;
			fwrite($f,$logmessage);
			fclose($f);
		}
	}
	/******************** Debug management ********************/
	public static function ddump($var, $label="",$fe=false)	{
		echo "<br />---start dump----- $label -----start dump---<br />";
		if($fe)	{
			if(is_array($var)||is_object($var)) {
				foreach($var as $key=>$value) {
					echo "$key=>$value<br />";
					if(is_array($var)||is_object($var))
					{
						ddump($var," ".Text::_("level")." ");
					}
				}
			}	else {
				var_dump($var);
			}
		} else {
			var_dump($var);
		}
		echo "<br />---end dump------- $label -------end dump---<br />";
	}
	public static function pre() {
		$all_args=func_get_args();
		$i=0;
		foreach($all_args as $arg){
			$i++;
			echo "<pre>Param #".$i."=>";
			var_dump($arg);
			echo "</pre>";
		}
	}
	public static function traceFunctionCallFrom($level=1, $abs_path=false) {
		$e = new Exception();
		$result = "";
		$e_arr = $e->getTrace();
		if(isset($e_arr[$level])){
			$frame = $e_arr[$level];
			if(!$abs_path) $frame['file'] = str_replace(PATH_FRONT, '', $frame['file']);
			if(isset($e_arr[$level + 1])){
				$frame_2 = $e_arr[$level + 1];
				$result = sprintf( "%s%s%s in %s(%s)", (isset($frame_2['class']) ? $frame_2['class'] : "Unknown"), (isset($frame_2['type']) ? $frame_2['type'] : " "), $frame_2['function'], $frame['file'], $frame['line']);
			} else {
				$result = sprintf( "%s(%s)", $frame['file'], $frame['line']);
			}
		}
		return $result;
	}
	public static function traceStack($pre=true, $abs_path=false, $cutString=false) {
		$e = new Exception();
		$html="";
		if($pre) $html.="<pre>";
		if($cutString){
			if($abs_path) $html.=$e->getTraceAsString();
			else $html.=(str_replace(PATH_FRONT, '', $e->getTraceAsString()));
		} else {
			if($abs_path) $html.=self::getExceptionTraceAsString($e);
			else $html.=(str_replace(PATH_FRONT, '', self::getExceptionTraceAsString($e)));
		}
		if($pre) $html.="</pre>";
		return $html;
	}
	public static function getExceptionTraceAsString($exception) {
		$rtn = "";
		$count = 0;
		foreach ($exception->getTrace() as $frame) {
			$args = "";
			if (isset($frame['args'])) {
				$args = array();
				foreach ($frame['args'] as $arg) {
					if (is_string($arg)) {
						$args[] = "'" . $arg . "'";
					} elseif (is_array($arg)) {
						$args[] = "Array";
					} elseif (is_null($arg)) {
						$args[] = 'NULL';
					} elseif (is_bool($arg)) {
						$args[] = ($arg) ? "true" : "false";
					} elseif (is_object($arg)) {
						$args[] = get_class($arg);
					} elseif (is_resource($arg)) {
						$args[] = get_resource_type($arg);
					} else {
						$args[] = $arg;
					}
				}
				$args = join(", ", $args);
			}
			$rtn .= sprintf( "#%s %s(%s): %s%s%s(%s)\n",
					$count,
					$frame['file'],
					$frame['line'],
					(isset($frame['class']) ? $frame['class'] : ""),
					(isset($frame['type']) ? $frame['type'] : ""),
					$frame['function'],
					$args );
			$count++;
		}
		return $rtn;
	}
	public static function showCollapsedArray(&$array, $name='', $direct_output=true, $force_object=false, &$iter=0, $flag_head=true,$as_text=false) {
		if($as_text) return self::showArray($array, $name, $direct_output, $force_object, $iter, $flag_head, true);
		else echo self::showArray($array, $name, $direct_output, $force_object, $iter, $flag_head, true);
	}
	public static function showArray(&$array, $title="", $direct_output=true, $force_object=false, &$iter=0, $flag_head=true, $collapsed=false) {
		$text="";
		if (is_object($array)) $name=get_class($array)."_".rand(0,1000);
		elseif (is_array($array)) $name="_name_array_".rand(0,1000);
		else $name = "_name_".rand(0,1000);
		if(!$title) $title = $name;
		if($flag_head){
			if(is_array($array)) $title.= " (values: ".count($array).")";
			elseif(is_object($array)) $title.= " (properties: ".count((array)$array).")";
		}
		if (!is_array($array) && !is_object($array)) {
			$text.="<div id=\"".$iter."_head_".$name."\" class=\"arr_dbg_head\">".$array." is not valid by by this debug function for <span class=\"underline\">".$title."</span></div>";
			if($direct_output) {
				echo $text; return "";
			} else return $text;
		}
		$iter++;
		if ($flag_head) $text.="<div id=\"head_".$iter."_".$name."\" class=\"arr_dbg_head\" onclick=\"$('#".$name."_".$iter."').toggle();\">".$title." : ".gettype($array)."</div>";
		if ($collapsed) $text.= "<div id=\"".$name."_".$iter."\" class=\"arr_dbg\" style=\"display:none;\">";
		else $text.= "<div id=\"".$name."_".$iter."\" class=\"arr_dbg\">";
		if (is_object($array)) {
			if (get_class($array) == 'FieldSet' || is_subclass_of($array,'FieldSet')) {
				$array_vars = $array->toArray();
			}	else {
				if (method_exists($array, "getObjectVars")) $array_vars=$array->getObjectVars();
				else $array_vars = get_object_vars($array);
			}
		} else $array_vars=$array;
		foreach ($array_vars as $index=>$value) {
			$text.= "<table>";
			if (is_array($value) || is_object($value)) {
				// $_iter = $iter + 1;
				$text.= "<tr><td colspan=\"3\"><fieldset><legend onclick=\"$(this).siblings('.arr_dbg').toggle();\">".$index." : ".gettype($value)."</legend>";
				if($direct_output) { echo $text; $text =""; }
				$text.=self::showArray($value, "sub_".$index, $direct_output, $force_object, $iter, false, $collapsed);
				$text.= "</fieldset></td></tr>";
			} else $text.= "<tr><td width=\"20%\">[".$index."]</td><td width=\"3%\"> ====> </td><td width=\"77%\"><b>".(is_bool($value) ? ($value ? "True" : "False") : $value)."</b> <i><small>(typeof ".gettype($value).")</small></i></td></tr>";
			$text.= "</table>";
		}
		$text.= "</div>";
		if($direct_output) {
			echo $text;	return "";
		} else return $text;
	}
	/**
	 * Выводит ассоциативный массив свойств и значений объекта
	 */
	public static function getProperties($obj=false){
		if($obj) {
			$class_methods = get_class_methods(get_class($obj));
			self::showArray($class_methods," ".Text::_("Object").":  ".get_class($obj) );
		}
	}
	/******************** Text and conversion management ********************/
	/*
	 // Just for testing
	 Util::pre(Util::toBool(false));
	 Util::pre(Util::toBool(true));
	 Util::pre(Util::toBool("0"));
	 Util::pre(Util::toBool("1"));
	 Util::pre(Util::toBool("false"));
	 Util::pre(Util::toBool("true"));
	 Util::pre(Util::toBool(0));
	 Util::pre(Util::toBool(1));
	 Util::pre(Util::toBool(8));
	 Util::pre(Util::toBool("privet"));
	 */
	public static function toBool($var){
		if(is_string($var)){
			if($var==='true' || $var==='1') return true;
			if($var==='false' || $var==='0') return false;
		} elseif(is_bool($var)){
			return $var;
		} elseif(is_int($var)){
			if($var > 0) return true;
			return false;
		}
		return null;
	}
	/**
	 *
	 * Преобразование строк вида %D0%BF%D1%80 в 'пр'
	 * @param $text - входящий текст
	 * return  преобразованный текст
	 */
	public static function getTrasserText($text) {
		while (ereg('%([0-9A-F]{2})',$text)) {
			$val=ereg_replace('.*%([0-9A-F]{2}).*','\1',$text);
			$newval=chr(hexdec($val)); // получаем сивол с номером,
			$text=str_replace('%'.$val,$newval,$text);
		}
		return $text;
	}
	/*проврека есть ли в строке не ASCII символы */
	public static function checkNonASCIISymbols($txt)	{
		$check=array();
		$mmn=mb_strlen($txt, DEF_CP);
		for($b=0; $b <= $mmn; $b++) {
			$pb=mb_substr($txt, $b, 1, DEF_CP);
			if(ord($pb)>128) {
				$check[]=Text::_("symbol")." ".$pb." ".Text::_("code").ord($pb)." ".Text::_("position")." ".$b;
			}
		}
		return $check;
	}
	// замена символов в строке (применялась когда надо было убрать например двойные слеши в адресе)
	// убирает запущенные случаи ///////////////
	// возхвращает в параметре число произведенных замен
	// нужно например чтобы подменить url в строке
	public static function replaceDouble($str, &$cnt=0, $search="//", $needle="/") {
		if(strpos($str,$search)===false) return $str;
		while(true) {
			$nw_str=str_replace($search, $needle, $str, $cnt_z);
			$cnt+=$cnt_z;
			if(strlen($nw_str)==strlen($str)) {
				return $nw_str; break;
			} else {
				$str=$nw_str;
			}
		}
	}
	public static function dsPath($path, $prefix=false) {
		$path=str_replace("/", DS, $path);
		if ($prefix) return str_replace(DS.DS,DS,$prefix.$path);
		return $path;
	}
	public static function generateRandomString($length=10, $keyspace="", $mode=3) {
		$str = ""; $min_keyspace_length = 3;
		if($keyspace && mb_strlen($keyspace, DEF_CP) < $min_keyspace_length){
			$keyspace = "";
			$mode = 3;
		}
		if(!$keyspace){
			$keyspace = '0123456789';
			if($mode>0) $keyspace.= 'abcdefghijklmnopqrstuvwxyz';
			if($mode>1) $keyspace.= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
			// if($mode>2) $keyspace.= '!@#$%^&*()_-+=~'; // Old variant, not for passwords.
			if($mode>2) $keyspace.= '!@#^*_-+=~'; // @TODO Need testing
			if($mode>3) $keyspace.= '$%&()'; // @TODO Need testing
		}
		$max = mb_strlen($keyspace, DEF_CP) - 1;
		for ($i = 0; $i < $length; $i++) {
			if(function_exists("random_int")) $str.= $keyspace[random_int(0, $max)];
			else $str.= $keyspace[rand(0, $max)];			
		}
		// Util::pre($str, $length, $keyspace, $mode);
		return $str;
	}
	/************************************************************/
	/******************* OTHERS (TEMPORARY) *********************/
	/************************************************************/
	/**
	 * Используется , если формируем дополнительные колнтроллеры, в которых надо сформировать дополнительные права
	 * вызывается в конструкторах контроллера , но может быть вызван и в хелпере
	 * @param string $module - модуль для котрого проверяем , добавляем список прав
	 * @param array $listAcl - новый список прав в формате  $i++;$listAcl[$i]['ao_name']='viewCatalogClients'; $listAcl[$i]['ao_description']='viewCatalogClients';
	 * @param number $is_admin - где проверяем в админке или на фронте (1, 0)
	 * @return array $messages - массив сообщений о результате работы который можно вывести
	 */
	public static function checkAddAcl($module, $listAcl, $is_admin=1) {
		$counter = 0;
		$message=array();
		$db = Database::getInstance ();
		$sql = "SELECT ao_name FROM #__acl_objects WHERE ao_module_name='" . $module . "' AND ao_is_admin=".$is_admin;
		$db->setQuery ( $sql );
		$res_db = $db->LoadResultArray ();
		$res_core = $listAcl;
		if (count ( $res_core )) {
			foreach ( $res_core as $acl ) {
				if (! in_array ( $acl ['ao_name'], $res_db )) {
					$counter = $counter + 10;
					$message [] = sprintf ( Text::_ ( 'Admin rule %s for module' ), $acl ['ao_name'] ) . " " . $module . " " . Text::_ ( "is absent" );
					$sql = "INSERT INTO `#__acl_objects` (`ao_name`,`ao_module_name`,`ao_description`,`ao_ordering`,`ao_is_admin`)
							VALUES ('" . $acl ['ao_name'] . "','" . $module . "','" . $acl ['ao_description'] . "'," . $counter . ",1)";
					$db->setQuery ( $sql );
					if (! $db->query ()) {
						$message [] = sprintf ( Text::_ ( 'Adding admin rule %s for module' ), $acl ['ao_name'] ) . " " . $module . " " . Text::_ ( "failed" );
					} else {
						$message [] = sprintf ( Text::_ ( 'Admin rule %s for module' ), $acl ['ao_name'] ) . " " . $module . " " . Text::_ ( "added" );
					}
				}
			}
		}
		return $message;
	}
	//--------------------------------------------------------------------
	// Функция проверки принадлежит ли браузер к мобильным устройствам
	// Возвращает 0 - браузер стационарный или определить его не удалось
	//            1-4 - браузер запущен на мобильном устройстве
	//--------------------------------------------------------------------
	public static function is_mobile() {
		$user_agent=strtolower(getenv('HTTP_USER_AGENT'));
		$accept=strtolower(getenv('HTTP_ACCEPT'));
		
		if ((strpos($accept,'text/vnd.wap.wml')!==false) ||
				(strpos($accept,'application/vnd.wap.xhtml+xml')!==false)) {
					return 1; // Мобильный браузер обнаружен по HTTP-заголовкам
				}
				if (isset($_SERVER['HTTP_X_WAP_PROFILE']) ||
						isset($_SERVER['HTTP_PROFILE'])) {
							return 2; // Мобильный браузер обнаружен по установкам сервера
						}
						if (preg_match('/(mini 9.5|vx1000|lge |m800|e860|u940|ux840|compal|'.
								'wireless| mobi|ahong|lg380|lgku|lgu900|lg210|lg47|lg920|lg840|'.
								'lg370|sam-r|mg50|s55|g83|t66|vx400|mk99|d615|d763|el370|sl900|'.
								'mp500|samu3|samu4|vx10|xda_|samu5|samu6|samu7|samu9|a615|b832|'.
								'm881|s920|n210|s700|c-810|_h797|mob-x|sk16d|848b|mowser|s580|'.
								'r800|471x|v120|rim8|c500foma:|160x|x160|480x|x640|t503|w839|'.
								'i250|sprint|w398samr810|m5252|c7100|mt126|x225|s5330|s820|'.
								'htil-g1|fly v71|s302|-x113|novarra|k610i|-three|8325rc|8352rc|'.
								'sanyo|vx54|c888|nx250|n120|mtk |c5588|s710|t880|c5005|i;458x|'.
								'p404i|s210|c5100|teleca|s940|c500|s590|foma|samsu|vx8|vx9|a1000|'.
								'_mms|myx|a700|gu1100|bc831|e300|ems100|me701|me702m-three|sd588|'.
								's800|8325rc|ac831|mw200|brew |d88|htc\/|htc_touch|355x|m50|km100|'.
								'd736|p-9521|telco|sl74|ktouch|m4u\/|me702|8325rc|kddi|phone|lg |'.
								'sonyericsson|samsung|240x|x320vx10|nokia|sony cmd|motorola|'.
								'up.browser|up.link|mmp|symbian|smartphone|midp|wap|vodafone|o2|'.
								'pocket|kindle|mobile|psp|treo|android|iphone|ipod|webos|wp7|wp8|'.
								'fennec|blackberry|htc_|opera m|windowsphone)/', $user_agent)) {
								return 3; // Мобильный браузер обнаружен по сигнатуре User Agent
						}
						if (in_array(substr($user_agent,0,4),
								Array("1207", "3gso", "4thp", "501i", "502i", "503i", "504i", "505i", "506i",
										"6310", "6590", "770s", "802s", "a wa", "abac", "acer", "acoo", "acs-",
										"aiko", "airn", "alav", "alca", "alco", "amoi", "anex", "anny", "anyw",
										"aptu", "arch", "argo", "aste", "asus", "attw", "au-m", "audi", "aur ",
										"aus ", "avan", "beck", "bell", "benq", "bilb", "bird", "blac", "blaz",
										"brew", "brvw", "bumb", "bw-n", "bw-u", "c55/", "capi", "ccwa", "cdm-",
										"cell", "chtm", "cldc", "cmd-", "cond", "craw", "dait", "dall", "dang",
										"dbte", "dc-s", "devi", "dica", "dmob", "doco", "dopo", "ds-d", "ds12",
										"el49", "elai", "eml2", "emul", "eric", "erk0", "esl8", "ez40", "ez60",
										"ez70", "ezos", "ezwa", "ezze", "fake", "fetc", "fly-", "fly_", "g-mo",
										"g1 u", "g560", "gene", "gf-5", "go.w", "good", "grad", "grun", "haie",
										"hcit", "hd-m", "hd-p", "hd-t", "hei-", "hiba", "hipt", "hita", "hp i",
										"hpip", "hs-c", "htc ", "htc-", "htc_", "htca", "htcg", "htcp", "htcs",
										"htct", "http", "huaw", "hutc", "i-20", "i-go", "i-ma", "i230", "iac",
										"iac-", "iac/", "ibro", "idea", "ig01", "ikom", "im1k", "inno", "ipaq",
										"iris", "jata", "java", "jbro", "jemu", "jigs", "kddi", "keji", "kgt",
										"kgt/", "klon", "kpt ", "kwc-", "kyoc", "kyok", "leno", "lexi", "lg g",
										"lg-a", "lg-b", "lg-c", "lg-d", "lg-f", "lg-g", "lg-k", "lg-l", "lg-m",
										"lg-o", "lg-p", "lg-s", "lg-t", "lg-u", "lg-w", "lg/k", "lg/l", "lg/u",
										"lg50", "lg54", "lge-", "lge/", "libw", "lynx", "m-cr", "m1-w", "m3ga",
										"m50/", "mate", "maui", "maxo", "mc01", "mc21", "mcca", "medi", "merc",
										"meri", "midp", "mio8", "mioa", "mits", "mmef", "mo01", "mo02", "mobi",
										"mode", "modo", "mot ", "mot-", "moto", "motv", "mozz", "mt50", "mtp1",
										"mtv ", "mwbp", "mywa", "n100", "n101", "n102", "n202", "n203", "n300",
										"n302", "n500", "n502", "n505", "n700", "n701", "n710", "nec-", "nem-",
										"neon", "netf", "newg", "newt", "nok6", "noki", "nzph", "o2 x", "o2-x",
										"o2im", "opti", "opwv", "oran", "owg1", "p800", "palm", "pana", "pand",
										"pant", "pdxg", "pg-1", "pg-2", "pg-3", "pg-6", "pg-8", "pg-c", "pg13",
										"phil", "pire", "play", "pluc", "pn-2", "pock", "port", "pose", "prox",
										"psio", "pt-g", "qa-a", "qc-2", "qc-3", "qc-5", "qc-7", "qc07", "qc12",
										"qc21", "qc32", "qc60", "qci-", "qtek", "qwap", "r380", "r600", "raks",
										"rim9", "rove", "rozo", "s55/", "sage", "sama", "samm", "sams", "sany",
										"sava", "sc01", "sch-", "scoo", "scp-", "sdk/", "se47", "sec-", "sec0",
										"sec1", "semc", "send", "seri", "sgh-", "shar", "sie-", "siem", "sk-0",
										"sl45", "slid", "smal", "smar", "smb3", "smit", "smt5", "soft", "sony",
										"sp01", "sph-", "spv ", "spv-", "sy01", "symb", "t-mo", "t218", "t250",
										"t600", "t610", "t618", "tagt", "talk", "tcl-", "tdg-", "teli", "telm",
										"tim-", "topl", "tosh", "treo", "ts70", "tsm-", "tsm3", "tsm5", "tx-9",
										"up.b", "upg1", "upsi", "utst", "v400", "v750", "veri", "virg", "vite",
										"vk-v", "vk40", "vk50", "vk52", "vk53", "vm40", "voda", "vulc", "vx52",
										"vx53", "vx60", "vx61", "vx70", "vx80", "vx81", "vx83", "vx85", "vx98",
										"w3c ", "w3c-", "wap-", "wapa", "wapi", "wapj", "wapm", "wapp", "wapr",
										"waps", "wapt", "wapu", "wapv", "wapy", "webc", "whit", "wig ", "winc",
										"winw", "wmlb", "wonu", "x700", "xda-", "xda2", "xdag", "yas-", "your",
										"zeto", "zte-"))) {
										return 4; // Мобильный браузер обнаружен по сигнатуре User Agent
								}
								return false; // Мобильный браузер не обнаружен
	}
	public static function getIP() {
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}
	public static function getLetter($int)
	{
		$arr_letter[0]="A";
		$arr_letter[1]="B";
		$arr_letter[2]="C";
		$arr_letter[3]="D";
		$arr_letter[4]="E";
		$arr_letter[5]="F";
		$arr_letter[6]="G";
		$arr_letter[7]="H";
		$arr_letter[8]="I";
		$arr_letter[9]="J";
		$arr_letter[10]="K";
		$arr_letter[11]="L";
		$arr_letter[12]="M";
		$arr_letter[13]="N";
		$arr_letter[14]="O";
		$arr_letter[15]="P";
		$arr_letter[16]="Q";
		$arr_letter[17]="R";
		$arr_letter[18]="S";
		$arr_letter[19]="T";
		$arr_letter[20]="U";
		$arr_letter[21]="V";
		$arr_letter[22]="W";
		$arr_letter[23]="X";
		$arr_letter[24]="Y";
		$arr_letter[25]="Z";
		if($int>=0&&$int<26)	return $arr_letter[$int];
		else return "";
		
	}
}
?>