<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class mailControllerdefault extends Controller {
	
	private $boxes=array();
	
	public function __construct($name,$module) {
		parent::__construct($name, $module);
		$this->boxes[0]=Text::_("Outgoing");
		$this->boxes[1]=Text::_("Inbox");
	}
	public function showContents() {
		$this->checkAuth();

		$mode = Request::get('mode','all');
		$inbox = Request::getInt('inbox',1);
		$view = $this->getView();
		$view->assign("boxes",$this->boxes);
		$view->assign("inbox",$inbox);
		$model = $this->getModel('letter');

		$view->addBreadcrumb(Text::_('Mail'),'index.php?module=mail');
		$unreadOnly = ($mode != 'all');
		// Paging
		$letterCount = $model->getLetterCount(($inbox == 1),$unreadOnly);
		$paginator = $model->createPaginator($view,$letterCount);
		
		$letters = $model->getMail(($inbox == 1), $unreadOnly);
		if ($inbox == 1) {
			$view->inbox = true;
			$view->addBreadcrumb(Text::_('Inbox'),'index.php?module=mail&amp;view=contents');
			$pageLinkTail = "&amp;view=contents";
		}	else {
			$view->inbox = false;
			$view->addBreadcrumb(Text::_('Outgoing'),'index.php?module=mail&amp;view=contents&amp;inbox=0');
			$pageLinkTail = "&amp;view=contents&amp;inbox=0";
		}
		$paginator->buildPagePanel("index.php?module=mail", $pageLinkTail);
		$view->assign("mode", $mode);
		$view->assign("letters", $letters);
	}

	public function showRead() {
		$this->checkAuth();

		$letterId = Request::getInt('ltrid',0);
		$view = $this->getView();

		$view->addBreadcrumb(Text::_('Mail'),'index.php?module=mail');
		$view->assign("boxes",$this->boxes);
		
		$model = $this->getModel('letter');
		$letter = $model->getLetter($letterId);
		if ($letter === false) {
			$this->setRedirect("index.php?module=mail",Text::_("Letter absent"));
		}	else {

			if (User::getInstance()->getId() == $letter->l_sender_id) {
				$view->assign("inbox",0);
				$view->addBreadcrumb(Text::_('Outgoing'),Router::_("index.php?module=mail&amp;view=contents&amp;inbox=0"));
			}	else {
				$view->assign("inbox",1);
				$view->addBreadcrumb(Text::_('Inbox'),Router::_("index.php?module=mail&amp;view=contents"));
			}
			
			$userType = Text::_('Sender');
			$user = User::getInstance()->getNicknameFor($letter->l_sender_id);
			if (User::getInstance()->getId() == $letter->l_sender_id) {
				$userType = Text::_('Reciever');
				$user = User::getInstance()->getNicknameFor($letter->l_reciever_id);
			}
			$view->assign('letter', $letter);
			$view->assign('userType',$userType);
			$view->assign('user', $user);

			if (User::getInstance()->getId() == $letter->l_reciever_id) {
				$model->setRead($letterId);
			}
		}
	}

	public function showWrite() {
		$this->checkAuth();
		$letterText		= Request::getSafe('letterText','');
		
		$theme = Request::get('theme','');
		if ($theme) $theme = urldecode($theme);	

		$recvId = Request::getInt('recvuid',0);
		$recvUser		= Request::getSafe('recvUser','');
		
		$nickname = '';
		if ($recvId) {
			$nickname = User::getInstance()->getNicknameFor($recvId);
		} else $nickname=$recvUser;

		$view = $this->getView();

		$view->addBreadcrumb(Text::_('Mail'),'index.php?module=mail');
		$view->addBreadcrumb(Text::_('New letter'),'#');
		$view->assign('letterText',$letterText);
		$view->assign('recvNickname',$nickname);
		$view->assign('theme',$theme);
	}

	public function send() {
		$this->checkAuth();
		$recvUser		= Request::getSafe('recvUser','');
		$letterTheme	= Request::getSafe('letterTheme','');
		$letterText		= Request::getSafe('letterText','');
		Event::raise("captcha.checkResult",array("module"=>"mail"));
		$redirectUrl="index.php?module=mail&view=write&recvUser=".urlencode($recvUser);
		if ($letterTheme == '' || $letterText == '' || $recvUser == '') {
			$msg=Text::_('Form incomplete');
		} else if(isset($_SESSION['captcha_string'])&&($_SESSION['captcha_string']!="OK")) { 
				//$msg =Text::_("Wrong captcha");
				$msg = $_SESSION["captcha_string"];
				unset($_SESSION['captcha_string']); 
				$redirectUrl.="&theme=".urlencode($letterTheme)."&letterText=".urlencode($letterText);
		} elseif (User::checkFloodPoint()){
			$msg=Text::_('Flood is found');
		}	else {
			// получаем все данные по пользователю
			$infoUserRecv=User::getInstance()->getInfoUserFor($recvUser);
			if($infoUserRecv ) {
				$recvId = $infoUserRecv->u_id;
				$recvEmail =$infoUserRecv->u_email;
			} else {
				$recvId =0;
				$recvEmail ='';				
			}
			if ($recvId == 0) {
				$msg=Text::_('Absent nickname');
			} else {
				$model = $this->getModel('letter');
				if($model->writeLetter($recvId,$letterTheme,$letterText))	{
					$link = "index.php?module=mail&view=contents&mode=unread&inbox=1";
					$letter = sprintf(Text::_('new mail text'),$recvUser,siteConfig::$metaTitle, Router::_($link, false, true, 1, 2))."\n";
					if(aNotifier::addToMailQueue($recvEmail, sprintf(Text::_('New post portal'),siteConfig::$metaTitle),$letter)) {
						$msg=Text::_('Letter sent');
					}	else {
						$msg=Text::_('Letter saved butnot sent');
					}
					$redirectUrl="index.php?module=mail&view=contents&inbox=0";
				}	else {$msg=Text::_('Operation failed');}
			}
		}
		$this->setRedirect($redirectUrl,$msg);
	}

	public function delete() {
		$this->checkAuth();

		$letterId	= Request::getInt('letterid',0);
		$inbox=Request::getInt('inbox',1);

		$model = $this->getModel('letter');
		$letter = $model->getLetter($letterId);
		if ($letter == false) {
			$this->setRedirect("index.php?module=mail");
		}
		else {
			$uid = User::getInstance()->getId();
			if ($uid == $letter->l_sender_id) {
				$model->deleteLetter($letter->l_id,"s");
				$this->setRedirect("index.php?module=mail",Text::_('Letter deleted'));
			}
			else if ($uid == $letter->l_reciever_id) {
				$model->deleteLetter($letter->l_id,"r");
				$this->setRedirect("index.php?module=mail",Text::_('Letter deleted'));
			}
			else {
				$this->setRedirect("index.php?module=mail&inbox=".$inbox);
			}
		}
	}

}

?>