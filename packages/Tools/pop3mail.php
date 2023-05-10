<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class pop3mail extends BaseObject{
	private $mbox = NULL;        // Здесь храним указатель на поток скрипт<->сервер
	private $letterPatterns = array();
	private $processType = "messagesBySender";
	private $sender="";
	private $pop3Server = '127.0.0.1';
	private $pop3User = 'guest';
	private $pop3Pass = 'guest';
	private $pop3Port = 110;
	private $deleteAfterRead = false;
	
	private $successAuth = False;       // Если удалось авторизироваться на сервере
	private $letterCount = 0;           // Количество писем на сервере
	private $server_message = "";       // Текущий ответ сервера
	private $cr_lf="<br />";

	public function __construct($pop3Server='', $pop3User='', $pop3Pass='', $pop3Port=110) {
		if (defined('IS_CONSOLE')) $this->cr_lf=CR_LF;
		if (($pop3Server)&&($pop3User)&&($pop3Pass)&&($pop3Port)) {
			$this->pop3Server=$pop3Server;
			$this->pop3User=$pop3User;
			$this->pop3Pass=$pop3Pass;
			$this->pop3Port=$pop3Port;
		} else {
			$this->pop3Server=mailerConfig::$pop3Server;
			$this->pop3User=mailerConfig::$pop3User;
			$this->pop3Pass=mailerConfig::$pop3Password;
			$this->pop3Port=mailerConfig::$pop3Port;
		}
	}

	public function getLetterCount() {
		return $this->letterCount;
	}
	public function setDeleteAfterRead ($_flag=true) {
		$this->deleteAfterRead=$_flag;
	}
	public function setLetterPatterns($patterns) {
		$this->letterPatterns=$patterns;
	}
	public function setSender($sender) {
		$this->sender=$sender;
	}
	public function setProcessType($processType) {
		$this->processType=$processType;
	}
	
	public function checkMail() {
		if ($this->connectPop3Server()) {
			$this->srvMsg();
 			if ($this->startWork()) {
				$this->srvMsg();
				if ($this->letterCount>0) {					
					switch ($this->processType) {
						case "attachmentsBySender":
							$this->processAttachmentsBySender();
							break;
						case "messagesBySender":
							$this->processMailBySender();
							break;
						default:
							$this->server_message='Wrong processing type!';
							$this->srvMsg();
							break;
					}
				}
			}
			$this->disconnectPop3Server();
		}
		$this->srvMsg();
	}
	private function srvMsg() {
		echo $this->server_message.$this->cr_lf;
	}

	private function connectPop3Server() {
		$this->mbox = imap_open ("{".$this->pop3Server.":".$this->pop3Port."/pop3}INBOX", $this->pop3User, $this->pop3Pass);
	  if ($this->mbox) { $this->server_message='Successful connect!'; return true; } 
		else { 
			if (imap_last_error()) $this->server_message=imap_last_error();
      else $this->server_message='Failed to connect pop3-sever!';
      return false; 
		}
	}

	private function disconnectPop3Server() {
		imap_close($this->mbox);
		$this->server_message='Connection is closed';
	}

	private function startWork()	{
		$this->server_message='Receiving mail information from pop3-server...'; $this->srvMsg();
		$this->letterCount = imap_num_msg($this->mbox);
		$this->server_message='Found '. $this->letterCount . ' new letters.'; return true;
	}

	private function processMailBySender() {
		$this->server_message="Processing messages. Looking for text..."; $this->srvMsg();
		for ($message_number=1; $message_number <= $this->letterCount; $message_number++) {
			$structure=imap_fetchstructure($this->mbox,$message_number);
			$header=imap_headerinfo($this->mbox,$message_number);
			$body=imap_body($this->mbox,$message_number);
			if(preg_match("/".$this->sender."/", $header->fromaddress, $matches) > 0)	{
				if($structure->ifparameters) {
					if ($structure->parameters[0]->attribute=="CHARSET"){
						$charset=$structure->parameters[0]->value;
						$body=iconv($charset, "utf-8",$body);
					}
				}
				echo $body;	
				echo $this->cr_lf.$this->cr_lf.$this->cr_lf;
			  if ($this->deleteAfterRead) imap_delete($this->mbox, $message_number);
			}
		}
		imap_expunge($this->mbox);
	}
	
	private function processAttachmentsBySender() {
		$this->server_message="Processing messages. Looking for attachments..."; $this->srvMsg();
		for ($message_number=1; $message_number <= $this->letterCount; $message_number++) {
			$structure=imap_fetchstructure($this->mbox,$message_number);
			$header=imap_headerinfo($this->mbox,$message_number);
//			$body=imap_body($this->mbox,$message_number);
			if (preg_match("/".$this->sender."/", $header->fromaddress, $matches) > 0)	{
				$attachments = array();
				if (isset($structure->parts)) $sp_count=count($structure->parts); else $sp_count=0;
				if ($sp_count) {
					for ($i = 0; $i < $sp_count; $i++) {
						$attachments[$i] = array(	'is_attachment' => false,	'filename' => '',	'name' => '',	'attachment' => '');
						if($structure->parts[$i]->ifdparameters) {
							foreach($structure->parts[$i]->dparameters as $object) {
								if(strtolower($object->attribute) == 'filename') {
									$attachments[$i]['is_attachment'] = true;
									$attachments[$i]['filename'] = $object->value;
								}
							}
						}
						if($structure->parts[$i]->ifparameters) {
							foreach($structure->parts[$i]->parameters as $object) {
								if(strtolower($object->attribute) == 'name') {
									$attachments[$i]['is_attachment'] = true;
									$attachments[$i]['name'] = $object->value;
								}
							}
						}
						if($attachments[$i]['is_attachment']) {
							$attachments[$i]['attachment'] = imap_fetchbody($this->mbox, $message_number, $i+1);
							if($structure->parts[$i]->encoding == 3) { // 3 = BASE64
								$attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
							}	elseif($structure->parts[$i]->encoding == 4) { // 4 = QUOTED-PRINTABLE
								$attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
							}
						}
					}
					for($i = 0; $i < $sp_count; $i++) {
						if($attachments[$i]['is_attachment']) {
							$_filename=$attachments[$i]['name'];
							$ppos = strpos($_filename,'.'); $ext = substr($_filename, $ppos+1);
							$filename = date('Ymd_His',strtotime($header->date)).'.'.$ext;
							$filepath = PATH_TMP.DS.$filename;
							$body = $attachments[$i]['attachment'];
							$f = fopen($filepath,"wb");
							fwrite($f, $body); 	fclose($f);
							$this->server_message="Found file attachment.".$this->cr_lf."Written to ".$filepath; $this->srvMsg();
							if ($this->deleteAfterRead) imap_delete($this->mbox, $message_number);
						}
					}						
				}					
			}
		}
		imap_expunge($this->mbox);
	}
}
?>