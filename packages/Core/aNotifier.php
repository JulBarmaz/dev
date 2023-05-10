<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

final class aNotifier {

	public static function addToQueue($to, $title, $text, $format="plain", $phone="", $sms_text="", $fromname="", $from=""){
//		if(!self::addToMailQueue($to, $title, $text, $format, $fromname, $from) && !$phone) return false;
		if(!$sms_text) $sms_text=$title;
		$res1=self::addToMailQueue($to, $title, $text, $format, $fromname, $from);
		$res2=self::addToSMSQueue($phone, $sms_text);
		return ($res1 && $res2);
	}
	public static function addToMailQueue($to, $title, $text, $format="plain", $fromname="", $from=""){
		if($to) {
			if (mailerConfig::$robotNotifier) {
				if (!$from) $from=mailerConfig::$robotEmail; 
				if (!$fromname) $fromname=soConfig::$firmName;
				if (!$fromname) $fromname=$from;
				$now=time();
				$title = Database::getInstance()->getEscaped($title);
				$text = Database::getInstance()->getEscaped($text);
				$sql = "INSERT INTO #__notifications (n_id, n_from, n_fromname, n_to, n_phone, n_time, n_title, n_text, n_format, n_type)
						VALUES (NULL,	'".$from."','".$fromname."', '".$to."','', '".$now."', '".$title."', '".$text."', '".$format."', 1)";
				Database::getInstance()->setQuery($sql);
				return Database::getInstance()->query();
			} else {
				$_mailer= new Mailer();
				return $_mailer->send($to, $title, $text, $format, $from, $fromname);
			}
		}
		return false;
	}
	public static function addToSMSQueue($phone, $text){
		if($phone && $text && mailerConfig::$smsEnabled) {
			$prov=new SMSProvider();
			$phone=$prov->formatPhoneAsNumber($phone);
			if($phone){
				if (mailerConfig::$robotNotifier) {
					if(mailerConfig::$smsSender) $fromname=mailerConfig::$smsSender;
					else  $fromname=soConfig::$firmName;
					if (!$fromname) return false;
					$now=time();
					$sql = "INSERT INTO #__notifications (n_id, n_from, n_fromname, n_to, n_phone, n_time, n_title, n_text, n_format, n_type)
							VALUES (NULL,	'','".$fromname."', '', '".$phone."', '".$now."', '', '".$text."', 'plain', 2)";
					Database::getInstance()->setQuery($sql);
					Database::getInstance()->query();
					return true;
				} else {
					if(mailerConfig::$smsProvider){
						$smsProviderName=mailerConfig::$smsProvider;
						$_sms_sender= new $smsProviderName();
						return $_sms_sender->send($phone, $text, $fromname);
					}
				}
			}
		}
		return false;
	}
	public static function sendMailQueue($limit){
		if (!$limit) return;
		$queue=self::getMailQueue($limit);
		$queue_sent=array();
		if (count($queue)){
			$_mailer= new Mailer();
			foreach ($queue as $row){
				if ($row->n_from) {
					$n_from=$row->n_from; 
					if ($row->n_fromname) $n_fromname=$row->n_fromname; else $n_fromname=$n_from;
				} else {
					$n_from=false;
					$n_fromname=false;
				}
				if ($_mailer->send($row->n_to,$row->n_title, $row->n_text,$row->n_format,$n_from,$n_fromname)) $queue_sent[]=$row->n_id;
			}
			self::cleanMailQueue($queue_sent);
		}
		self::resetCounter();
	}
	public static function sendSMSQueue($limit){
		if (!$limit || !mailerConfig::$smsEnabled || !mailerConfig::$smsProvider) return;
		$queue=self::getSMSQueue($limit);
		$queue_sent=array();
		if (count($queue)){
			$smsProviderName=mailerConfig::$smsProvider;
			$_sms_sender= new $smsProviderName();
			foreach ($queue as $row){
				if ($row->n_fromname) $n_fromname=$row->n_fromname;
				elseif(mailerConfig::$smsSender) $n_fromname=mailerConfig::$smsSender;
				else $n_fromname=soConfig::$firmName;
				if ($_sms_sender->send($row->n_phone, $row->n_text, $n_fromname)) $queue_sent[]=$row->n_id;
			}
			self::cleanSMSQueue($queue_sent);
		} 
		self::resetCounter();
	}
	private static function resetCounter(){
		$sql="SELECT count(n_id) FROM #__notifications";
		Database::getInstance()->setQuery($sql);
		if(!Database::getInstance()->loadResult()) DBUtil::resetCounter("notifications");
	}
	private static function getMailQueue($limit){
		$sql="SELECT * FROM #__notifications WHERE n_type=1 ORDER BY n_time ASC LIMIT 0,".$limit;
		Database::getInstance()->setQuery($sql);
		return 	Database::getInstance()->loadObjectList();
	}
	private static function getSMSQueue($limit){
		$sql="SELECT * FROM #__notifications WHERE n_type=2 ORDER BY n_time ASC LIMIT 0,".$limit;
		Database::getInstance()->setQuery($sql);
		return 	Database::getInstance()->loadObjectList();
	}
	private static function cleanMailQueue($idents){
		if (count($idents)){
			$ids=implode("','",$idents);
			$sql="DELETE FROM #__notifications WHERE n_id IN('".$ids."') AND n_type=1";
			Database::getInstance()->setQuery($sql);
			return Database::getInstance()->query();
		} else return false;
	}
	private static function cleanSMSQueue($idents){
		if (count($idents)){
			$ids=implode("','",$idents);
			$sql="DELETE FROM #__notifications WHERE n_id IN('".$ids."') AND n_type=2";
			Database::getInstance()->setQuery($sql);
			return Database::getInstance()->query();
		} else return false;
	}
	public static function proceedNotifications(){
		ini_set('display_errors',0);	error_reporting(0);
		$limit=mailerConfig::$robotNotifierMessages;
		self::sendMailQueue($limit);
		if(mailerConfig::$smsEnabled) self::sendSMSQueue($limit);
	}
}
?>