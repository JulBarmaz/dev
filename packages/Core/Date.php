<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

final class Date {
	public static $_delimiter=".";
	public static function initFormats() {
		defined( '__DATESTRING' ) or define( '__DATESTRING', '%d.%m.%Y' );
		defined( '__DATETIMESTRING' ) or define( '__DATETIMESTRING', '%d.%m.%Y %H:%M' );
		defined( '__DATETIMESHORTSTRING' ) or define( '__DATETIMESHORTSTRING', '%d.%m.%y %H:%M' );
		defined( '__DATETIMELONGSTRING' ) or define( '__DATETIMELONGSTRING', '%d.%m.%Y %H:%M:%S' );
		defined( '__MYSQLDATETIMESTRING' ) or define( '__MYSQLDATETIMESTRING', '%Y-%m-%d %H:%M:%S' );
	}

	public static function timestamp_to_mysqldatetime($timestamp = 0) {
		return strftime(__MYSQLDATETIMESTRING, $timestamp);
	}
	public static function mysqldatetime_to_timestamp($datetime = "") {
		// function is only applicable for valid MySQL DATETIME (19 characters) and DATE (10 characters)
		$l = strlen($datetime);
		if(!($l == 10 || $l == 19)) return 0;
		if(!strpos($datetime,'-')) return 0;
		$date = $datetime;
		$hours = 0;
		$minutes = 0;
		$seconds = 0;

		// DATETIME only
		if($l == 19) {
			list($date, $time) = explode(" ", $datetime);
			list($hours, $minutes, $seconds) = explode(":", $time);
		}
		if ($date=="0000-00-00") return 0;
		list($year, $month, $day) = explode("-", $date);
		return mktime($hours, $minutes, $seconds, $month, $day, $year);
	}
	public static function fromSQL($sqlDate, $notime=false, $noseconds=false) {
		$timestamp=self::mysqldatetime_to_timestamp($sqlDate);
		if(!$timestamp) return '';
		if($notime) $result=strftime(__DATESTRING, $timestamp);
		elseif($noseconds) $result=strftime(__DATETIMESTRING, $timestamp);
		else $result=strftime(__DATETIMELONGSTRING, $timestamp);
		return $result;
	}
	/*
	 *  How to check:
		$correct_dates=array("02.07.87", "02.07.1987", "02.07.87 15:30", "02.07.1987 15:46", "02.07.87 15:30:17", "02.07.1987 15:46:25");
		foreach($correct_dates as $correct_date) echo "strlen=".strlen($correct_date)." => ".$correct_date." => ".Date::toSQL($correct_date)."<br>";
		$incorrect_dates=array("02.07.8", "02.07.198", "02.07.8715:30");
		foreach($incorrect_dates as $incorrect_date) echo "strlen=".strlen($incorrect_date)." => ".$incorrect_date." => ".Date::toSQL($incorrect_date)."<br>";
	*/
	public static function toSQL($sqlDate="", $notime=false) {
		$result="0000-00-00 00:00:00";
		if ($sqlDate) {
			if(self::isSQLDate($sqlDate)) return $sqlDate;
			$date = explode(" ", $sqlDate);
			if(!isset($date[1])) $date[1] = "00:00:00";
			else{
				$time_arr = array();
				if(preg_match ("/^([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})$/", $date[1], $time_arr)){
					$date[1] = $time_arr[1].":".$time_arr[2].":".$time_arr[3];
				} elseif (preg_match ("/^([0-9]{1,2}):([0-9]{1,2})$/", $date[1], $time_arr)){
					$date[1] = $time_arr[1].":".$time_arr[2].":00";
				} else {
					$date[1] = "00:00:00";
				}
			}
			$date_arr = array();
			if(preg_match("/^([0-9]{1,2})".self::$_delimiter."([0-9]{1,2})".self::$_delimiter."([0-9]{4})$/", $date[0], $date_arr)){
				$date[0] = $date_arr[3]."-".$date_arr[2]."-".$date_arr[1];
			} elseif(preg_match("/^([0-9]{1,2})".self::$_delimiter."([0-9]{1,2})".self::$_delimiter."([0-9]{2})$/", $date[0], $date_arr)){
				$date[0] = DateTime::createFromFormat('y', $date_arr[3])->format('Y')."-".$date_arr[2]."-".$date_arr[1];
			} else {
				$date[0] = "0000-00-00";
				$date[1] = "00:00:00";
			}
			if($notime) $result = $date[0];
			else  $result = implode(" ", $date);
		}
		return $result;
	}
	public static function isSQLDate($date_str=""){
		return preg_match ("/^([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})$/", $date_str);
	}
	public static function toSQLWithDelimiter($sqlDate="", $delimiter=".", $notime=false) {
		$old_delimiter = self::$_delimiter;
		self::$_delimiter = $delimiter;
		$sqlDate = self::toSQL($sqlDate, $notime);
		self::$_delimiter = $old_delimiter;
		return $sqlDate;
	}
	/*
	public static function toSQL_old($sqlDate="", $delimiter=".", $notime=false, $noseconds=false) {
		if (!$sqlDate) {
			$result="0000-00-00 00:00:00";
		} else {
			if($notime) {
				preg_match("/([0-9]{1,2})".$delimiter."([0-9]{1,2})".$delimiter."([0-9]{4})/", $sqlDate, $datetime);
			} else {
				if($noseconds) { 
					preg_match ("/([0-9]{1,2})".$delimiter."([0-9]{1,2})".$delimiter."([0-9]{4}) ([0-9]{1,2}):([0-9]{1,2})/", $sqlDate, $datetime); 
					$datetime[6]='00';	
				} else { 
					preg_match ("/([0-9]{1,2})".$delimiter."([0-9]{1,2})".$delimiter."([0-9]{4}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})/", $sqlDate, $datetime); 
					// $datetime[4]=$datetime[5]=$datetime[6]='00';
				}
			}
			if(!isset($datetime[3])) { 
				$result=$sqlDate; 
			} else {
				$result = $datetime[3]."-".$datetime[2]."-".$datetime[1];
				if(!$notime) $result.=" ".$datetime[4].":".$datetime[5].":".$datetime[6];
			}
		}
		return $result;
	}
	*/
	public static function GetdateRus($timestamp,$varp=2,$notime=true) {
		if(empty($timestamp)) return '';
		$arrMont = array(
		'января','февраля','марта','апреля','мая','июня',
		'июля','августа','сентября','октября','ноября','декабря');
		if ($varp==1) { // передана временная метка
			$imes=intval(date("m",$timestamp))-1;
			$mesp=$arrMont[$imes];
			$metka_st = date("d",$timestamp)." ".$mesp."  ".date("Y",$timestamp)." года ";
			if(!$notime)$metka_st.=date("H:i:s",$timestamp);
		}	else	{
			if(preg_match ("/0000-00-00/",$timestamp)) return Text::_("Data absent");
			if(!$notime) preg_match ("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})/", $timestamp, $datetime);
			else preg_match ("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/", $timestamp, $datetime);
			$imes=intval($datetime[2])-1;
			$mesp=$arrMont[$imes];
			$metka_st = $datetime[3]." ".$mesp."  ".$datetime[1]." г.";
			if(!$notime)$metka_st.="  $datetime[4] ч. $datetime[5] м. $datetime[6] с.";
		}
		return($metka_st);
	}
	static function _monthToString($i)  {
		$arr=Date::getMonthArray();
		return $arr[$i]['title'];
	}
	static function getMonthArray()  {
		$monthArray[1]=array("id"=>1,"title"=>Text::_("January"));
		$monthArray[2]=array("id"=>2,"title"=>Text::_("February"));
		$monthArray[3]=array("id"=>3,"title"=>Text::_("March"));
		$monthArray[4]=array("id"=>4,"title"=>Text::_("April"));
		$monthArray[5]=array("id"=>5,"title"=>Text::_("May"));
		$monthArray[6]=array("id"=>6,"title"=>Text::_("June"));
		$monthArray[7]=array("id"=>7,"title"=>Text::_("July"));
		$monthArray[8]=array("id"=>8,"title"=>Text::_("August"));
		$monthArray[9]=array("id"=>9,"title"=>Text::_("September"));
		$monthArray[10]=array("id"=>10,"title"=>Text::_("October"));
		$monthArray[11]=array("id"=>11,"title"=>Text::_("November"));
		$monthArray[12]=array("id"=>12,"title"=>Text::_("December"));
		return $monthArray;
	}
	
	static function getMonthArrayStatic()  {
		$monthArray[1]=Text::_("January");
		$monthArray[2]=Text::_("February");
		$monthArray[3]=Text::_("March");
		$monthArray[4]=Text::_("April");
		$monthArray[5]=Text::_("May");
		$monthArray[6]=Text::_("June");
		$monthArray[7]=Text::_("July");
		$monthArray[8]=Text::_("August");
		$monthArray[9]=Text::_("September");
		$monthArray[10]=Text::_("October");
		$monthArray[11]=Text::_("November");
		$monthArray[12]=Text::_("December");
		return $monthArray;
	}

	static function getMonthShortArrayStatic()  {
		$monthArray[1]=Text::_("Jan");
		$monthArray[2]=Text::_("Feb");
		$monthArray[3]=Text::_("Mar");
		$monthArray[4]=Text::_("Apr");
		$monthArray[5]=Text::_("May");
		$monthArray[6]=Text::_("Jun");
		$monthArray[7]=Text::_("Jul");
		$monthArray[8]=Text::_("Aug");
		$monthArray[9]=Text::_("Sep");
		$monthArray[10]=Text::_("Oct");
		$monthArray[11]=Text::_("Nov");
		$monthArray[12]=Text::_("Dec");
		return $monthArray;
	}
	
	public static function getYearArray($offset=10)	{
		$res=array();
		$curYear=date("Y");
		for ($i = $curYear-$offset; $i <= $curYear; $i++) {
			$res[$i]=array("id"=>$i);
		}
		return $res;
	}
	public static function getYearArrayStatic($offset=10) {
		$res=array();
		$curYear=date("Y");
		for ($i = $curYear-$offset; $i <= $curYear; $i++) {
			$res[$i]=$i;
		}
		return $res;
	}
	public static function todaySQL() {
		$today = date('Y-m-d');
		return $today;
	}
	public static function nowSQL() {
		$now = date('Y-m-d H:i:s');
		return $now;
	}
	/**
	 * возвращает формат __DATESTRING , из строки вида ГГГГ-ММ-ДД
	 * при несоответствии формата возващает ложь
	 * @param $strdate
	 * @param $default_now
	 * @param $with_time
	 */
	public static function DateString($strdate, $default_now=false, $with_time=false) {
		$result = self::formatTimestamp(self::GetTimestamp($strdate), $with_time);
		if (($default_now)&&(strlen(trim($result))==0)) {
			if ($with_time) $result=date("d.m.Y H:M");
			else $result=date("d.m.Y");
		}
		return $result;
	}
	// возвращает дату формата __DATESTRING из метки времени
	public static function formatTimestamp($timestamp, $with_time=false, $with_seconds=false) {
		if ($timestamp && $with_time && $with_seconds) { return strftime(__DATETIMELONGSTRING, $timestamp); }
		elseif ($timestamp && $with_time ) { return strftime(__DATETIMESTRING, $timestamp); }
		elseif ($timestamp && !$with_time ) { return strftime(__DATESTRING, $timestamp); }
		else return "";
	}
	// возвращает временную метку , из строки вида ГГГГ-ММ-ДД ЧЧ:ММ:СС
	// при несоответствии формата возващает ложь
	public static function GetTimestamp($strdate, $with_time=0) {

		if ($with_time) {
			if(preg_match("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})/", $strdate, $datetime))
			{	$metka_st = mktime($datetime[4],$datetime[5],$datetime[6],$datetime[2],$datetime[3],$datetime[1]);	return($metka_st);	}
			else  return false;
		}	else	{
			if(preg_match ("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/", $strdate, $datetime)) {
				if (($datetime[2]=="00")||($datetime[3]=="00")||($datetime[1]=="0000")) return false;
				else $metka_st = mktime(0,0,0,$datetime[2],$datetime[3],$datetime[1]);	return($metka_st);	}
				else  return false;
		}
	}
	// возвращает временную метку , из строки вида ДД.ММ.ГГГГ ЧЧ:ММ:СС
	// при несоответствии формата возвращает ложь
	public static function GetTimestampDotes($strdate, $with_time=0) {
		if ($with_time) {
			if(preg_match("/([0-9]{1,2}).([0-9]{1,2}).([0-9]{4}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})/", $strdate, $datetime))
			{	$metka_st = mktime($datetime[4],$datetime[5],$datetime[6],$datetime[2],$datetime[1],$datetime[3]);	return($metka_st);	}
			else  return false;
		}	else	{
			if(preg_match ("/([0-9]{1,2})-([0-9]{1,2})-([0-9]{4})/", $strdate, $datetime)) {
				if (($datetime[2]=="00")||($datetime[3]=="00")||($datetime[1]=="0000")) return false;
				else $metka_st = mktime(0,0,0,$datetime[2],$datetime[1],$datetime[3]);	return($metka_st);	}
				else  return false;
		}
	}
	public static function AddHours($strdate,$hours) {
		if(preg_match("/([0-9]{1,2}).([0-9]{1,2}).([0-9]{4}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})/", $strdate, $datetime)){	
			$metka_st = mktime($datetime[4]+$hours,$datetime[5],$datetime[6],$datetime[2],$datetime[1],$datetime[3]);
			return(strftime(__DATETIMELONGSTRING, $metka_st)); 
		}elseif(preg_match("/([0-9]{1,2}).([0-9]{1,2}).([0-9]{4}) ([0-9]{1,2}):([0-9]{1,2})/", $strdate, $datetime)){	
			$metka_st = mktime($datetime[4]+$hours,$datetime[5],0,$datetime[2],$datetime[1],$datetime[3]);
			return(strftime(__DATETIMESTRING, $metka_st)); 
		}elseif(preg_match("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})/", $strdate, $datetime)){	
			$metka_st = mktime($datetime[4]+$hours,$datetime[5],$datetime[6],$datetime[2],$datetime[3],$datetime[1]);
			return(strftime(__MYSQLDATETIMESTRING, $metka_st)); 
		} else return false;
	}
	public static function AddSeconds($strdate,$seconds) {
		if(preg_match("/([0-9]{1,2}).([0-9]{1,2}).([0-9]{4}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})/", $strdate, $datetime)){
			$metka_st = mktime($datetime[4]+$hours,$datetime[5],$datetime[6],$datetime[2],$datetime[1],$datetime[3]);
			return(strftime(__DATETIMELONGSTRING, $metka_st));
		}elseif(preg_match("/([0-9]{1,2}).([0-9]{1,2}).([0-9]{4}) ([0-9]{1,2}):([0-9]{1,2})/", $strdate, $datetime)){
			$metka_st = mktime($datetime[4]+$hours,$datetime[5],0,$datetime[2],$datetime[1],$datetime[3]);
			return(strftime(__DATETIMESTRING, $metka_st));
		}elseif(preg_match("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})/", $strdate, $datetime)){
			$metka_st = mktime($datetime[4],$datetime[5],$datetime[6]+$seconds,$datetime[2],$datetime[3],$datetime[1]);
			return(strftime(__MYSQLDATETIMESTRING, $metka_st));
		} else return false;
	}	
	public static function SubtractDatesSQL($date1,$date2=false) {
		if(!$date2) $date2=self::nowSQL();
		$ts1 = self::GetTimestamp($date1,1);
		$ts2 = self::GetTimestamp($date2,1);
		$diff_sec=$ts1-$ts2;
		return self::ArrayFromSeconds($diff_sec);		
	}
	// вычитание дат в одном формате, возврат в том же формате
	public static function SubtractDates($date1,$date2,$no_second=false,$preg="/([0-9]{1,2}).([0-9]{1,2}).([0-9]{4}) ([0-9]{1,2}):([0-9]{1,2})/")	{
		$ts1 =0;
		$ts2 =0;
		if($no_second) {
			if(preg_match($preg, $date1, $datetime))
			$ts1 = mktime($datetime[4],$datetime[5],0,$datetime[2],$datetime[1],$datetime[3]);
		}	else {
			if(preg_match($preg, $date1, $datetime))
			$ts1 = mktime($datetime[4],$datetime[5],$datetime[6],$datetime[2],$datetime[1],$datetime[3]);
		}
		if($no_second) {
			if(preg_match($preg, $date2, $datetime))
			$ts2 = mktime($datetime[4],$datetime[5],0,$datetime[2],$datetime[1],$datetime[3]);
		}	else {
			if(preg_match($preg, $date2, $datetime))
			$ts2 = mktime($datetime[4],$datetime[5],$datetime[6],$datetime[2],$datetime[1],$datetime[3]);
		}
		$diff_sec=$ts1-$ts2;
		//echo "$diff_sec";
		return self::ArrayFromSeconds($diff_sec);
	}
	// преобразование количества секунд в массив
	public static function ArrayFromSeconds($seconds) {
		if($seconds<0) {
			$seconds=abs($seconds);
			$Massive['bef'] = 1;
		} else {
			$Massive['bef'] = 0;
		}
		$Year = floor($seconds/31536000);
		$Ost = ($seconds-($Year*31536000));
		$Day = floor($Ost/86400);
		$Ost = ($Ost-($Day*86400));
		$Hour = floor($Ost/3600);
		$Ost = ($Ost-($Hour*3600));
		$Minutes = floor($Ost/60);
		$Second = ($Ost-($Minutes*60));

		$Massive['y'] = $Year;
		$Massive['d'] = $Day;
		$Massive['h'] = $Hour;
		$Massive['m'] = $Minutes;
		$Massive['s'] = $Second;
		return $Massive;
	}
}
?>