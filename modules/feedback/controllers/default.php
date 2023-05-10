<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class feedbackControllerdefault extends SpravController {
	public function showBackcall() {
		$view=$this->getView();
		if($this->getConfigVal('Feedback_useCaptchaOnBackcall')) $view->assign('disableCaptcha',$this->checkACL("feedbackDisableCaptcha",false));
		else $view->assign('disableCaptcha', true);
		$view->assign("is_ajax", 0);
	}
	public function ajaxBackcallForm() {
		Portal::getInstance()->disableTemplate();
		$view=$this->getView("backcall");
		if($this->getConfigVal('Feedback_useCaptchaOnBackcall')) $view->assign('disableCaptcha',$this->checkACL("feedbackDisableCaptcha",false));
		else $view->assign('disableCaptcha', true);
		$view->assign("is_ajax", 1);
		$view->render();
	}
	public function saveBackcall(){
		$model = $this->getModel("backcall");
		$answer=array();
		$f_sender = Request::getSafe('f_sender','');
		$f_mail = Request::getSafe('f_mail','');
		$f_phone = Request::getSafe('f_phone','');
		$is_ajax = Request::getInt('is_ajax',0);
		$f_ip = User::getInstance()->getIP();
		$answer["status"] = "FAILED";
		
		if (User::checkFloodPoint()) {
			$answer["message"] = Text::_('Flood is found');
		} elseif($f_sender && $f_phone){
			$can_send = true;
			if($this->getConfigVal('Feedback_useCaptchaOnBackcall')){
				Event::raise("captcha.checkResult",array("module"=>"feedback"));
				if(!$this->checkACL("feedbackDisableCaptcha", false) && isset($_SESSION['captcha_string']) && $_SESSION['captcha_string']!="OK" ) {
					// $answer["message"] =Text::_("Wrong captcha");
					$answer["message"] = $_SESSION["captcha_string"];
					unset($_SESSION['captcha_string']);
					$can_send = false;
				}
			}
			if($can_send) {
				if($model->send($this->getTheme(), $f_sender, $f_phone, $f_mail, $f_ip, $this->getCopyAddresses())){
					Event::raise("feedback.backcall.send", array('senderName'=>$f_sender, "senderMail"=>$f_mail, "senderPhone"=>$f_phone, "senderIP"=>$f_ip));
					$answer["status"] = "OK";
					$answer["message"] = Text::_('Feedback sent');
				} else {
					$answer["message"] = Text::_('Feedback not sent');
				}
			}
		} else {
			if(!$f_sender) $answer["message"] = Text::_('Please enter')." ".Text::_("Your name");
			elseif(!$f_phone) $answer["message"] = Text::_('Please enter')." ".Text::_("Your phone");
		}
		if($is_ajax) {
			Portal::getInstance()->disableTemplate();
			echo json_encode($answer);
			Util::halt();
		} else {
			if ($answer["status"] == "FAILED") {
				$script="$(document).ready(function(){ alert('".$answer["message"]."')});";
				Portal::getInstance()->addScriptDeclaration($script);
				$view=$this->getView();
				$view->assign("is_ajax", 0);
				$view->render();
			} else {
				$this->setRedirect('index.php?module=feedback&view=backcall',$answer["message"]);
			}
		}
	}
	public function showMessage($msg="") {
		Module::getInstance()->get('reestr')->set('task',"modify");
		if ($msg) {
			$script="$(document).ready(function(){ alert('".$msg."')});";
			Portal::getInstance()->addScriptDeclaration($script);
		}
		$view=$this->getView();
		$view->addBreadcrumb(Text::_("Main page"),Router::_("index.php"));
		$this->modify();
	}
	public function modify($ajaxModify=false) {
		$reestr = Module::getInstance()->get('reestr');
		$moduleName	= Module::getInstance()->getName();
		$viewname = $this->getView()->getName();
		$model = $this->getModel();
		$multy_code   = 0;
		$psid         = 0;
		$reestr->set('ajaxModify',$ajaxModify);
		$reestr->set('view',$viewname);
		$reestr->set('model',$model);
		$reestr->set('metadata',$model->meta);
		$reestr->set("multy_code",$multy_code);
		$reestr->set('psid',$psid);
		$layout		= Request::get('layout');
		$page		= Request::get('page', 1);
		$sort		= Request::get('sort');
		$orderby	= Request::get('orderby');
		$reestr->set('layout',$layout, true);
		$reestr->set('page', $page);
		$reestr->set('sort',$sort);
		$reestr->set('orderby',$orderby);
		$reestr->set('task',"save");
		$result = $model->getElement();
		if (!$result) $result = new stdClass();
		$view = $this->getView();
		if(User::getInstance()->isLoggedIn()) {
			$view->assign('authorized',true);
			$result->f_uid = User::getInstance()->getID();
			$result->f_sender = User::getInstance()->getNickname();
			$result->f_mail = User::getInstance()->getEmail();
		} else {
			$view->assign('authorized',false);
			$result->f_uid=0;
			$result->f_sender = Request::getSafe('f_sender','');
			$result->f_mail = Request::getSafe('f_mail','');
		}
		$afields=$model->meta->getListAdditionalField($model->meta->tablename);
		$result->f_theme = Request::getSafe('f_theme',$this->getTheme());
		$result->f_text = Request::getSafe('f_text','');
		if (count($afields)){
			foreach($afields as $af=>$obj){ $result->{$af} = Request::getSafe($af,''); }
		}
		$view->addBreadcrumb(Text::_("Feedback"),"#");
		$art_id = $this->getConfigVal("forewordArticle");
		if ($art_id) {
			$helper=$this->getHelper("article","article");
			$article=$helper->getArticle($art_id);
		} else $article=false;
		$view->assign('article',$article);
		if($this->getConfigVal('Feedback_useCaptchaOnFeedback')) $view->assign('disableCaptcha',$this->checkACL("feedbackDisableCaptcha",false));
		else $view->assign('disableCaptcha', true);
		$view->modify($result);
		$this->haltView();
	}
	
	public function save() {
		$is_err=1; $msg ="";
		$model = $this->getModel();
		if(User::getInstance()->isLoggedIn()) {
			$f_uid = User::getInstance()->getID();
			$f_sender = User::getInstance()->getNickname();
			$f_mail = User::getInstance()->getEmail();
		} else {
			$f_uid=0;
			$f_sender = Request::getSafe('f_sender','');
			$f_mail = Request::getSafe('f_mail','');
		}
		$f_theme = Request::getSafe('f_theme',"");
		$f_text = Request::getSafe('f_text','');
		$f_phone = Request::getSafe('f_phone','');
		if($this->getConfigVal('Feedback_useCaptchaOnFeedback')) {
			// First we must raise event
			Event::raise("captcha.checkResult",array("module"=>"feedback"));
		}
		if($this->getConfigVal('Feedback_useCaptchaOnFeedback') && ( !$this->checkACL("feedbackDisableCaptcha",false) && isset($_SESSION['captcha_string']) && $_SESSION['captcha_string']!="OK" )) {
			// Second we must check the captcha
			// $msg =Text::_("Wrong captcha"); 
			$msg = $_SESSION["captcha_string"];
			unset($_SESSION['captcha_string']);
		} 
		elseif (!$f_sender) $msg =Text::_("Enter name");
		elseif (!$f_mail) $msg =Text::_("Enter e-mail");
		elseif (!Mailer::checkEmail($f_mail)) $msg =Text::_("Wrong e-mail");
		elseif (!$f_theme) $msg =Text::_("Enter theme");
		elseif (!$f_text) $msg =Text::_("Enter text");
		elseif((!$this->checkACL("feedbackDisableFloodControl",false))&&(User::checkFloodPoint())) {$msg = Text::_('Flood is found');}
		else {
			$f_ip = User::getInstance()->getIP();
			$_REQUEST["f_uid"]=$f_uid;
			if ($f_uid) {
				$_REQUEST["f_sender"]=$f_sender;
				$_REQUEST["f_mail"]=$f_mail;
			}
			$_REQUEST["f_ip"]=$f_ip;
			$_REQUEST["f_date"]=Date::nowSQL();
			$msgid=$model->save();
			if ($msgid) {
				if (siteConfig::$sendFeedbacksByMail) { 
					$res=$model->send($msgid,$this->getTheme(),$f_sender,$f_mail,$f_ip,$f_theme,$f_text,$this->getCopyAddresses()); 
				}
				Event::raise("feedback.send", array('senderName'=>$f_sender, "senderMail"=>$f_mail, "senderPhone"=>$f_phone, "senderIP"=>$f_ip));
				$msg = Text::_('Feedback sent'); 
				$is_err=0;
			}	else {
				$msg = Text::_('Feedback not sent');
			}
		}
		if ($is_err) {
			$this->showMessage($msg);
		} else {
			$this->setRedirect('index.php?module=feedback&view=message',$msg);
		}
	}

	private function getTheme() {
		return $this->getConfigVal("Feedback_theme");
	}
	private function getCopyAddresses() {
		return $this->getConfigVal("Feedback_copyAdresses");
	}
	
	public function showMessages() {
		$model=$this->getModel();
		$view=$this->getView();
		$view->addBreadcrumb(Text::_('Cabinet'),"index.php?module=user&amp;view=panel");
		$view->addBreadcrumb(Text::_("My feedbacks"),"#");
		if(User::getInstance()->isLoggedIn())$listmessages=$model->getListMessages();
		else $listmessages=array();
		$view->assign('list',$listmessages);
	}
}

?>