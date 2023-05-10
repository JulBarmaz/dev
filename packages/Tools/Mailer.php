<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class Mailer extends BaseObject {

	private $_sender	= "";
	private $_senderName ="";
	private $_replyTo	= "";
	private $_replyToName ="";
	private $_subject="";
	private $data_charset  = "utf-8";
	private $send_charset  = "utf-8";
	private $_end = "\r\n";

	public function __construct($sender="",$senderName="",$replyTo="",$replyToName="") {
		$this->initObj('Mailer');
		if ($sender == '') {
			$this->_sender = mailerConfig::$robotEmail; 
			$this->_senderName=soConfig::$firmName;
		} else { 
			$this->_sender = $sender; 
			$this->_senderName=$senderName;
		}
		if (!$this->_senderName) $this->_senderName=$this->_sender;
		if ($replyTo == '') {
			$this->_replyTo = $this->_sender;
			$this->_replyToName = $this->_senderName;
		} else {
			$this->_replyTo = $replyTo;
			$this->_replyToName = $replyToName;
		}
		if (!$this->_replyToName) $this->_replyToName=$this->_replyTo;
	}

	public function mime_header_encode($str) {
		if($this->data_charset != $this->send_charset) {
			$str = iconv($this->data_charset, $this->send_charset, $str);
		}
		return "=?" . $this->send_charset . "?B?" . base64_encode($str) . "?=";
	}

	public function setDataCharset($cs) {
		$this->data_charset=$cs;
	}
	public function setSendCharset($cs) {
		$this->send_charset=$cs;
	}
	public function getSender() {
		return $this->_sender;
	}
	public function getReplyTo() {
		return $this->_replyTo;
	}
	public function send($email_to, // email получателя
			$subject, 	// тема письма
			$body, 		// текст письма
			$type="plain", // формат письма
			$sender	= false, // для подмены отправителя
			$senderName=false,
			$replyTo	= false,
			$replyToName=false
			) {
		// проверяем на пустой емайл
		if (!$email_to) $email_to=$this->_senderName;
		if ($sender){
			$this->_sender = $sender;
			if($senderName)	$this->_senderName=$senderName;
			else $this->_senderName=$sender;
		}
		// а вдруг кому копии нужны
		$addresses=explode(",", $email_to);
		if (!count($addresses)) return false;
		$email_to=$addresses[0];
		//		if (count($addresses)>0) {unset($addresses[0]);	}

		if ($this->_senderName)	$from =  $this->mime_header_encode(htmlspecialchars_decode($this->_senderName))." <" . $this->_sender . ">";
		else $from=$this->_sender;
		$this->_subject=$subject;
		if (mailerConfig::$useSMTP) {
			// мульти отсылка только здесь
			return $this->sendSMTP($addresses, $subject, $body, $type, $from);
		}	else {
			$to = $this->mime_header_encode($email_to). ' <' . $email_to . '>';
			$subject = $this->mime_header_encode($subject);
			if($this->data_charset != $this->send_charset) {
				$body = iconv($this->data_charset, $this->send_charset, $body);
			}
			/*
			$headers  = "From: ".$from.$this->_end;
			$headers .= "Content-type: text/".$type."; charset=".$this->send_charset.$this->_end;
			$headers .= "Mime-Version: 1.0".$this->_end;
			*/
			$headers  = "From: ".$from.$this->_end;
			$headers .= "Return-Path:".$this->_sender.$this->_end;
			$headers .= "Reply-To:".$this->_replyTo.$this->_end;
			$headers .= "Content-type: text/".$type."; charset=".$this->send_charset.$this->_end;
			$headers .= "Mime-Version: 1.0".$this->_end;
			$additional_parameters="-f".$this->_sender;
			return @mail($to, $subject, $body, $headers, $additional_parameters);
		}
	}
	private function sendSMTP($addresses, $subject, $body, $type, $from){
		if( !$socket = fsockopen(mailerConfig::$smtpServer, mailerConfig::$smtpPort, $errno, $errstr, 30) ) {
			if (siteConfig::$debugMode) Debugger::getInstance()->error($errno." <=> ".$errstr);
			return false;
		}
		if (!$this->parseServerResponse($socket, "220", __LINE__,"")) return false;

		//fputs($socket, "HELO " . mailerConfig::$smtpServer . $this->_end);
		fputs($socket, "HELO " . str_replace( array( 'ssl://', 'tls://' ), '', mailerConfig::$smtpServer ) . $this->_end);
		if (!$this->parseServerResponse($socket, "250", __LINE__,"Unable to send HELO!")) {
			fclose($socket); return false;
		}

		fputs($socket, "AUTH LOGIN".$this->_end);
		if (!$this->parseServerResponse($socket, "334", __LINE__, "Unable to find response for authorization request")) {
			fclose($socket); return false;
		}

		fputs($socket, base64_encode(mailerConfig::$smtpUser) . $this->_end);
		if (!$this->parseServerResponse($socket, "334", __LINE__, "Login rejected")) {
			fclose($socket); return false;
		}

		fputs($socket, base64_encode(mailerConfig::$smtpPassword) . $this->_end);
		if (!$this->parseServerResponse($socket, "235", __LINE__,"Password rejected")) {
			fclose($socket); return false;
		}

		//		fputs($socket, "RCPT TO: <" . $email_to . ">" . $this->_end);
		//		if (!$this->parseServerResponse($socket, "250", __LINE__,"Unable to send command RCPT TO for main address")) { fclose($socket); return false; }

		if (isset($addresses) && is_array($addresses) && count($addresses)) {
			foreach($addresses as $adr)	{
				$data = "Date: ".date("D, d M Y H:i:s") . " UT".$this->_end;
				$data.= "Subject: ".$this->mime_header_encode($subject).$this->_end;
				$data.= "Reply-To: ".$this->_replyTo.$this->_end;
				$data.= "MIME-Version: 1.0".$this->_end;
				$data.= "Content-Type: text/".$type."; charset=\"".$this->send_charset."\"".$this->_end;
				$data.= "Content-Transfer-Encoding: 8bit".$this->_end;
				$data.= "From: ".$from.$this->_end;
				$data.= "To: $adr <$adr>".$this->_end;
				$data.= "X-Priority: 3".$this->_end.$this->_end;
				if($this->data_charset != $this->send_charset) $body = iconv($this->data_charset, $this->send_charset, $body);
				$data.=  $body.$this->_end;

				fputs($socket, "MAIL FROM: <" . $this->_sender . ">" . $this->_end);
				if (!$this->parseServerResponse($socket, "250", __LINE__,"Unable to send command MAIL FROM", $adr)) {
					fclose($socket); return false;
				}

				fputs($socket, "RCPT TO: <" . trim($adr) . ">" . $this->_end);
				if (!$this->parseServerResponse($socket, "250", __LINE__,"Unable to send command RCPT TO extended addresses : ".$adr, $adr)) {
					fclose($socket); return false;
				}

				fputs($socket, "DATA" . $this->_end);
				if (!$this->parseServerResponse($socket, "354", __LINE__, "Unable to send command DATA", $adr)) {
					fclose($socket); return false;
				}
				fputs($socket, $data . $this->_end . "." . $this->_end);
				if (!$this->parseServerResponse($socket, "250", __LINE__, "Unable to send message body", $adr)) {
					fclose($socket); return false;
				}
			}
		}

		fputs($socket, "QUIT".$this->_end);  fclose($socket);
		if (siteConfig::$debugMode) Debugger::getInstance()->message("Mailer: Message sent via SMTP");

		return true;
	}
	private function parseServerResponse(&$socket, $response, $line = __LINE__, $msg="",$recipient='common commands' ) {
		$server_response = "";
		while (substr($server_response, 3, 1) != " ") {
			if (!($server_response = fgets($socket, 256))) {
				$err_text = "Line: ".$line.". Response code: NORES => ".$msg;
				if (siteConfig::$debugMode) Debugger::getInstance()->error("Mailer: Get response error! Waited: ".$response.", ".$err_text);
				if(mailerConfig::$logErrors) $this->logMail($recipient, 'NORES', $err_text, $server_response);
				return false;
			}
		}
		if (!(substr($server_response, 0, 3) == $response)) {
			$err_text = "Line: ".$line.". Response code: ".substr($server_response, 0, 3)." => ".$msg;
			if (siteConfig::$debugMode) Debugger::getInstance()->error("Mailer: POST send error! Waited: ".$response.", ".$err_text);
			if(mailerConfig::$logErrors) $this->logMail($recipient, substr($server_response, 0, 3), $err_text, $server_response);
			return false;
		}
		return true;
	}
	private function logMail($recipient, $response_code, $err_text, $server_response)	{
		$sql = "INSERT INTO #__mailer_log (email, err_code, err_text, err_response, err_time, m_theme)
				VALUES('".$recipient."','".$response_code."','".$err_text."','".$server_response."' ,NOW(),'".$this->_subject."')";
		Database::getInstance()->setQuery($sql);
		return Database::getInstance()->query();
	}
	public static function checkEmail($email) {
		if(preg_match("/localhost/", $email)) return false;
		$_pattern = "/^[\w\._-]+@[\w\._-]+\.[\w]{2,6}$/";
		if(preg_match($_pattern, $email)) return true;
		else return false;
	}
}
?>