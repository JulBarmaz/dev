<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class SMSProvider extends BaseObject {

	protected $_username = "";
	protected $_password = "";
	protected $_server = "";
	protected $_session = "";
	protected $_time = 10;
	protected $_senderName ="";
	protected $_sms_text="";
	protected $data_charset  = "utf-8";
	protected $send_charset  = "utf-8";
	protected $_end  = "\r\n";

	public function __construct($sender="",$senderName="") {
		$this->initObj('SMSProvider');
		if ($sender == '') {
			$this->_senderName=soConfig::$firmName;
		} else { 
			$this->_senderName=$senderName;
		}
		$this->_username=mailerConfig::$smsUser;
		$this->_password=mailerConfig::$smsPassword;
	}
	public function send($phone, // телефон получателя
						$text, 	 // текст письма
						$senderName=false) {
		
		return false;					
	}
	protected function logSMS($recipient, $response_code, $response_text)	{
		$sql = "INSERT INTO #__sms_sender_log (phone, err_code, err_text, err_time, m_text)
				VALUES('".$recipient."','".$response_code."', '".$response_text."',NOW(),'".$this->_sms_text."')";
		Database::getInstance()->setQuery($sql);
		return Database::getInstance()->query();
	}
	public function formatPhoneAsNumber($phone, $length=11){
		$phone = preg_replace ("/[^0-9]/","",$phone);
		if(strlen($phone)!=$length) $phone = "";
		return $phone;
	}
	/* для проверки номеров без самого провайдера смс */
	public static function checkPhoneNumber($phone, $length=11){
		$phone = preg_replace ("/[^0-9]/","",$phone);
		if(strlen($phone)!=$length) return false;
		else return true;
	}
}
?>