<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO


defined('_BARMAZ_VALID') or die("Access denied");

class StreamTelecomSMSProvider extends SMSProvider {

	public function __construct($sender="",$senderName="") {
		parent::__construct($sender, $senderName);
		$this->_server = 'http://gateway.api.sc/rest/';
		$this->_time = 10;
	}
	public function send($recipient, // телефон получателя
						$text, 		// текст письма
						$senderName=false) {
		$this->_sms_text=$text;
		$result = false; $response_code=""; $response_text="";
		if(mailerConfig::$smsEnabled){
			$response=$this->GetSessionId();
			if(is_array($response)){
				if(isset($response['Code'])) $response_code=$response['Code'];
				if(isset($response['Desc'])) $response_text=$response['Desc'];
				$this->logSMS($recipient, $response_code, $response_text);
			} else {
				$this->_session=$response;
				$response=$this->SendSms($senderName, $recipient);
				if(is_array($response) && count($response)>1){
					if(isset($response['Code'])) $response_code=$response['Code'];
					if(isset($response['Desc'])) $response_text=$response['Desc'];
					if(!$response_text) $response_text=json_encode($response);
					$this->logSMS($recipient, $response_code, $response_text);
				} elseif(is_array($response) && count($response)==1) {
					$response_code=0;
					$response_text="Sent with id=".$response[0];
					$this->logSMS($recipient, $response_code, $response_text);
					$result=true;
				} else {
					$response_code="";
					$response_text="Unknown error";
					$this->logSMS($recipient, $response_code, $response_text);
				}
			}
		}
		return $result;					
	}
	private function GetSessionId($_method="POST"){
		if($_method=="POST"){
			$href = $this->_server.'Session/session.php';
			$src = 'login='.$this->_username.'&password='.$this->_password;
			return json_decode($this->PostConnect($src, $href), true);
		} else {
			$href = $this->_server.'Session/?login='.$this->_username.'&password='.$this->_password;
			return json_decode($this->GetConnect($href), true);
		}
	}
	private function GetState($messageId, $_method="POST"){
		if($_method=="POST"){
			$href = $this->_server.'State/state.php';
			$src ='sessionId='.$this->_session.'&messageId='.$messageId;
			$result = $this->PostConnect($src, $href);
			$result = $this->ChangeFormateDate(json_decode($result, true));
		} else {
			$href = $this->_server.'State/?sessionId='.$this->_session.'&messageId='.$messageId;
			$result = $this->GetConnect($href);
			$result = $this->ChangeFormateDate(json_decode($result, true));
		}
		return $result;
	}
	private function SendSms($sourceAddress, $destinationAddress, $validity=1440, $sendDate = ''){
		$href = $this->_server.'Send/SendSms/';
		if($sendDate) $sendDate = '&sendDate='.$sendDate; 
		$src = 'sessionId='.$this->_session.'&sourceAddress='.$sourceAddress.'&destinationAddress='.
		$destinationAddress.'&data='.urlencode($this->_sms_text).'&validity='.$validity.$sendDate;
		$result = $this->PostConnect($src, $href);
		return json_decode($result,true);
	}
	private function GetConnect($href){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$href);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}
	private function PostConnect($src, $href){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/x-www-form-urlencoded'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CRLF, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $src);
		curl_setopt($ch, CURLOPT_URL, $href);
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}
}
?>