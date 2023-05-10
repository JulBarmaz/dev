<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class userControllerdefault extends Controller {

	public function ajaxcheckfield()  {
		$fld=Request::getSafe('fld');
		$val=Request::getSafe('val');
		$psid=Request::getInt('psid',0);
		switch($fld){
			case "login":
				if(!User::isUser($val,$psid))  echo "OK";  else  echo Text::_("Occupied");
				break;
			case "nickname":
				if(!User::isNickName($val,$psid))  echo "OK";  else  echo Text::_("Occupied");
				break;
			case "email":
				if(!User::isEmail($val,$psid))  echo "OK";  else  echo Text::_("Occupied");
				break;
		}
	}

	public function ajaxgetRegionSelector() {
		$psid=Request::getInt("psid");
		$ctrl_prefix=Request::getSafe("ctrl_pref","");
		echo Address::renderRegionSelector($psid,$ctrl_prefix);
	}

	public function ajaxgetDistrictSelector() {
		$psid=Request::getInt("psid");
		$ctrl_prefix=Request::getSafe("ctrl_pref","");
		echo Address::renderDistrictSelector($psid,$ctrl_prefix);
	}
	
	public function ajaxgetLocalitySelector() {
		$psid=Request::getInt("psid");
		$ctrl_prefix=Request::getSafe("ctrl_pref","");
		echo Address::renderLocalitySelector($psid,$ctrl_prefix);
	}
	
	public function registerUser() {
		$returnUrl=$this->get('returnUrl');
		$returnUrlEncoded	= Request::getSafe("return_url","");
		if ($returnUrlEncoded) $register_url="index.php?module=user&view=register&return_url=".$returnUrlEncoded;
		else $register_url="index.php?module=user&view=register";
		$agree		= Request::getInt('rAgree',0);
		if (!backofficeConfig::$allowEmailLogin) $login	= Request::getSafe('rLogin','');
		else $login	= Request::getSafe('rNickname','');
		$nickname	= Request::getSafe('rNickname','');
		$password	= Request::getSafe('rPassword','');
		$password2	= Request::getSafe('rPasswordRetype','');
		$email		= Request::getSafe('rEmail','');
		$referral	= Request::getSafe('codeassign','');
		if (backofficeConfig::$noRegistration) Util::redirect("index.php",Text::_("Registration disabled"));
		Event::raise("captcha.checkResult",array("module"=>"user"));
		if(isset($_SESSION['captcha_string']) && $_SESSION['captcha_string']!="OK") {
			// $msg = Text::_("Wrong captcha");
			$msg = $_SESSION["captcha_string"];
			unset($_SESSION['captcha_string']);
			$this->setRedirect($register_url, $msg);
		}	elseif(!Mailer::checkEmail($email)){
			$this->setRedirect($register_url,Text::_('Wrong e-mail'));
		}	elseif(User::checkFloodPoint()){
			$this->setRedirect($register_url,Text::_('Flood is found'));
		}	else {
			if ((!$agree || $login == '' || $password == '' || $password2 == '' || $email == '') ||($password != $password2)) {
				$this->setRedirect($register_url,Text::_('Some fields not filled 1'));
			}	else {
				if (substr($login,0,5)=="user-"||substr($login,0,3)=="sn-"||User::isUser($login)) {
					$this->setRedirect($register_url,Text::_('Login is reserved'));
				}	elseif (User::isEmail($email)) {
					$this->setRedirect($register_url,Text::_('Email is reserved'));
				}	elseif (User::isNickName($nickname)) {
					$this->setRedirect($register_url,Text::_('NickName is reserved'));
				}	else {
					$msgs=User::getInstance()->inBlackList($login,$nickname,User::getInstance()->getIP(),$email);
					if (count($msgs)) {
						$this->setRedirect($register_url, implode(",", $msgs));
					} else {
						$referral=User::checkReferral($referral);
						$role=User::getDefaultRole();
						if(!backofficeConfig::$regConfirmation)	$userActivated=1; else $userActivated=0; 
						if (!$role) $this->setRedirect($returnUrl,Text::_('Registration restricted'));
						elseif (($uid = User::addUser($login,$nickname,$password,$email,$role,$referral,$userActivated)) > 0) {
							$model = $this->getModel('user');
							$reg_data = array("uid"=>$uid);
							Event::raise("register.proceedRegistration", array("module"=>"user", "source"=>"module.user", "action"=>"user.register"), $reg_data);
							if (backofficeConfig::$regConfirmation==1) {
								$link = Router::_("index.php?module=user&task=activate&code=".$model->getAffiliateCode($uid)."&return_url=".$returnUrlEncoded, false, false, 1, 2);
								$letter = sprintf(Text::_('confirmation mail text'), $nickname, siteConfig::$metaTitle, $login, $link, $model->getAffiliateCode($uid))."\n";
								$letter .= Text::_('Follow this link to activate your account');
								$letter .= $link;
								if(aNotifier::addToMailQueue($email,Text::_('Registration confirmation'),$letter)) {
									if (isset($_COOKIE['referral'])){
										Session::getInstance()->setcookie("referral", "", time() - 3600,"/");
									}
									$this->setRedirect($returnUrl,Text::_('Registration succeeded')." ".Text::_('Registration email send'));
								}	else $this->setRedirect($returnUrl,Text::_('Post sending error'));
							} elseif(backofficeConfig::$regConfirmation==2) {
								$letter = sprintf(Text::_('registration admin mail text'),siteConfig::$metaTitle,$login)."\n";
								aNotifier::addToQueue(soConfig::$siteEmail,Text::_('Registration confirmation'),$letter);
								if (isset($_COOKIE['referral'])) {
									Session::getInstance()->setcookie("referral", "", time() - 3600,"/");
								}
								$this->setRedirect($returnUrl,Text::_('Registration succeeded')." ".Text::_('Registration admin wait'));
							} else {
								$this->setRedirect($returnUrl,Text::_('Registration succeeded'));
							}
						} else $this->setRedirect($returnUrl,Text::_('Registration failed'));
					}
				}
			}
		}
	}
	public function showLogin() {
		if (User::getInstance()->isLoggedIn()) {
			$this->setRedirect("index.php?module=user&view=panel");
		}
		Portal::getInstance()->disableTemplate();
	}
	public function login() {
		if (User::getInstance()->isLoggedIn()) {
			$this->setRedirect("index.php?module=user&view=panel");
		}
		$this->set('view','login', true);
		$view = $this->getView();
		$returnUrl=$this->get('returnUrl');
		$view->assign("return_url",$returnUrl);
		$view->render();
	}
	public function elogin() {
		$returnTo=Util::getRefererUrl(false);
		$sn	= Request::getSafe('use',"");
		if ($sn) {
			Session::setVar("eauth_type",$sn);
			Session::setVar("eauth_return",$returnTo);
			Event::raise("user.before_elogin",array("module"=>"user","sn"=>$sn,"returnTo"=>$returnTo));
		}	else Util::redirect($returnTo);
	}
	public function showRegister() {
		if (User::getInstance()->isLoggedIn()) $this->setRedirect("index.php?module=user&view=panel");
		if (backofficeConfig::$noRegistration) $this->setRedirect("index.php",Text::_("Registration disabled"));
		$view = $this->getView();
		$return_url	= Request::getSafe('return_url',Util::getRefererUrl());
		$view->assign("return_url",$return_url);
		$view->addBreadcrumb(Text::_('Register'),"#");
	}

	public function showConfirm() {
		if (User::getInstance()->isLoggedIn()) {
			$this->setRedirect("index.php?module=user&view=panel");
		} else {
			Portal::getInstance()->disableTemplate();
		}
	}

	public function showStructure() {
		$model=$this->getModel();
		$view=$this->getView();
		$tree=$model->getStructure(User::getInstance()->u_affiliate_code);
		$view->assign('tree',$tree);
	}

	public function activate() {
		$affCode = Request::getSafe('code','');
		if ($affCode == '') {
			$this->setRedirect($this->get("returnUrl"));
		}	else {
			$model = $this->getModel('user');
			$model->activateUser($affCode);
			$this->setRedirect($this->get("returnUrl"),Text::_('Account activated successfully'));
		}
	}

	public function reactivate()	{
		$email = Request::getSafe('r_email','');
		$url=Portal::getInstance()->getURI(1);
		if(User::checkFloodPoint()){
			$msg=Text::_('Flood is found');
		} else {
			if ($email)	{
				$model = $this->getModel('user');
				$u = $model->getUserByEmail($email);
				if ($u) {
					if ($u->u_activated == '0') {
						$link = Router::_("index.php?module=user&task=activate&code=".$u->u_affiliate_code, false, false, 1, 2);
						$letter = sprintf(Text::_('confirmation mail text'),	$u->u_nickname,	siteConfig::$metaTitle,	$u->u_login, $link, $u->u_affiliate_code)."\n";
						$letter .= Text::_('Follow this link to activate your account')." ";
						$letter .= $link;
						if (aNotifier::addToMailQueue($email,Text::_('Reactivation'),$letter)) $msg=Text::_('Reactivation sent');
						else $msg=Text::_('Post sending error');
					} else $msg=Text::_('Account is allready activated');
				}	else $msg=Text::_('e-mail not found');
			}	else $msg=Text::_('e-mail is empty or invalid');
		}
		$this->setRedirect($url,$msg);
	}

	public function showReset()	{
		$view = $this->getView();
		switch($this->get('layout')) {
			case "email":
				if (User::getInstance()->isLoggedIn()) {
					$view->addBreadcrumb(Text::_('Cabinet'),"index.php?module=user&amp;view=panel");
					$view->addBreadcrumb(Text::_('Change e-mail'),"#");
					//$return_url	= Request::getSafe('return_url',"index.php");
					$email=User::getInstance()->getEmail();
					$view->assign("email",$email);
					//$view->assign("return_url",$return_url);
				} else $this->setRedirect("index.php");
				break;
			default:
				if (User::getInstance()->isLoggedIn()) { // фэйковый код если залогинен
					$vcode="logged in";
				} else {
					$vcode = trim(Request::getSafe("code",""));
				}
				if ($vcode) {
					$view->assign("vcode",$vcode);
					$view->addBreadcrumb(Text::_('Cabinet'),"index.php?module=user&amp;view=panel");
					$view->addBreadcrumb(Text::_('Change password'),"#");
				} else $this->setRedirect("index.php");
				break;
		}
	}

	public function resetPassword()	{
		$vcode	= Request::getSafe("code","");
		if ($vcode) $url="index.php?module=user&view=reset&code=".$vcode;
		else $url="index.php?module=user&view=register";
		$password	= Request::getSafe("rPassword","");
		$password2	= Request::getSafe("rPasswordRetype","");
		if(User::getInstance()->isLoggedIn()) {
			$login = User::getInstance()->getLogin();
		} else {
			$login = Request::getSafe("rLogin","");
			Event::raise("captcha.checkResult",array("module"=>"user"));
		}
		if(!User::getInstance()->isLoggedIn() && isset($_SESSION["captcha_string"]) && ($_SESSION["captcha_string"]!="OK")) {
			// $msg = Text::_("Wrong captcha");
			$msg = $_SESSION["captcha_string"];
			unset($_SESSION["captcha_string"]);
		}	elseif(User::checkFloodPoint()){
			$msg = Text::_("Flood is found");
		}	else {
			if (($login == "" || $password == "" || $password2 == "" || $vcode == "") || ($password != $password2)) {
				$msg = Text::_("Not enough data");
			}	else {
				if (User::getInstance()->resetPassword($login, $password, $vcode)) {
					if (User::getInstance()->isLoggedIn()) {
						$url="index.php?module=user&view=panel";
					} else {
						$url="index.php";
					}
					$msg = Text::_("Password changed");
				}
				else $msg = Text::_("Password change failed");
			}
		}
		$this->setRedirect($url,$msg);
	}
	public function resetEmail(){
		$email = Request::getSafe('rEmail','');
		$url = "index.php?module=user&view=reset&layout=email";
		if (User::getInstance()->isLoggedIn()){
			if(!$email || !Mailer::checkEmail($email)){
				$msg = Text::_("Wrong e-mail");
			} elseif(User::checkFloodPoint()){
				$msg = Text::_("Flood is found");
			} else {
				if (User::getInstance()->setEmail($email, User::getInstance()->getID())) {
					$url = "index.php?module=user&at=tab_2";
					$msg = Text::_("E-mail changed");
				} else {
					$msg = Text::_("Error changing e-mail");
				}
			}
		} else {
			$url = "index.php?module=user&task=login";
			$msg = Text::_("You are not authorized");
		}
		$this->setRedirect($url,$msg);
	}
	public function remindPassword()	{
		$email = Request::getSafe('r_email','');
		$url="index.php";
		if(User::checkFloodPoint()){
			$msg=Text::_('Flood is found');
		} else {
			if ($email)	{
				$model = $this->getModel('user');
				$u = $model->getUserByEmail($email);
				if ($u) {
					if ($u->u_activated) {
						$code=User::genValidationCode($u->u_id);
						$link = Router::_("index.php?module=user&view=reset&code=".$code, false, false, 1, 2);
						$letter = sprintf(Text::_('change password mail text'),
								$u->u_nickname,
								siteConfig::$metaTitle,
								$u->u_nickname,
								$link)."\n";
						$letter .= Text::_('Follow this link to change your password')." ";
						$letter .= $link;
						if (aNotifier::addToMailQueue($email,Text::_("Change password request"),$letter)) $msg=Text::_("Password change link sent");
						else $msg=Text::_("Post sending error");
					} else $msg=Text::_("Account is not activated");
				}	else $msg=Text::_("e-mail not found");
			}	else $msg=Text::_("e-mail is empty or invalid");
		}
		$this->setRedirect($url,$msg);
	}

	public function showInfo() {
		$this->checkACL("Profileuser");
		$userid=Request::getInt('psid');
		$view=$this->getView();
		$view->assign("canWriteToUser", $this->checkACL("mailModule", false));
		if (!$userid) {
			// а может affiliate код пришел
			$affiliate=Request::getSafe('affiliate');
			$userid=User::getIDByAffiliate($affiliate);
		}
		if ($userid) {
			$view->assign("psid",$userid);
			$view->assign("profile",$this->getProfile($userid));
			$view->assign("nickname",User::getNicknameFor($userid));
		} else {
			$this->setRedirect("index.php",Text::_("Data absent"));
		}
	}

	public function showPanel()	{
		if($this->checkAuth(true)){
			$view = $this->getView();			
			$view->addBreadcrumb(Text::_('Cabinet'),"index.php?module=user&amp;view=panel");
			$tabid=Request::get('at','tab_1');
			$view->addBreadcrumb(Text::_('User panel'),"#");
			$userdata=Userdata::getInstance(User::getInstance()->getID());
			$view->assign('profile',$this->getProfile(User::getInstance()->getId()));
			$view->assign('addresses',$userdata->getAddresses());
			$view->assign('company',$userdata->getCompany());
			$view->assign('banks',$userdata->getBanks());
			$view->assign('vendors',$userdata->getVendors());
			$view->assign("hide_personal_tab", $this->getConfigVal("hide_personal_tab"));
			$view->assign("hide_profile_tab_base", $this->getConfigVal("hide_profile_tab_base"));
			$view->assign("hide_profile_tab_public", $this->getConfigVal("hide_profile_tab_public"));
		}
	}
	public function getProfile($userid) {
		$profile=Profile::getInstance($userid)->getProfile();
		return $profile;
	}

	public function modifyProfile(){
		$this->checkAuth();
//		Portal::getInstance()->disableTemplate();
		$mdl = Module::getInstance();
		$moduleName	= $mdl->getName();
		$reestr = $mdl->get('reestr');
		$viewname = $this->getView()->getName();
		$model = $this->getModel();
//		$reestr->set('ajaxModify',false);
		$reestr->set('task',"saveProfile");
		$reestr->set('view',$viewname);
		$reestr->set('model',$model);
		$reestr->set('psid',User::getInstance()->getId());
		$layout			= Request::get('layout');
		$reestr->set('layout', $layout, true);
		$result = $model->getElement();
		$view = $this->getView();
		$view->modify($result);
	}
	public function saveProfile (){
		$this->checkAuth();
		$mdl				= Module::getInstance();
		$reestr 		= $mdl->get('reestr');
		$viewname 	= $this->getView()->getName();
		$layout			= Request::getSafe('layout');
		$reestr->set('view',$viewname);
		$reestr->set('psid',User::getInstance()->getId());
		$model 			= $this->getModel();
		if ($model->save()) $msg = Text::_("Save successfull");
		else $msg=Text::_("Save unsuccessfull");
		$url="index.php?module=user&view=panel";
		$this->setRedirect($url,$msg);
	}
	public function modifyAddress(){
		Portal::getInstance()->disableTemplate();
		$view=$this->getView();
		$this->set("layout","address",true);
		$userdata=Userdata::getInstance(User::getInstance()->getID());
		$psid = Request::getInt("psid",0);
		$view->assign('address',$userdata->getAddress($psid));
		$view->render();
	}
	public function saveAddress(){
		$userdata=Userdata::getInstance(User::getInstance()->getID());
		if ($userdata->saveAddress()) $msg = Text::_("Save successfull");	else $msg=Text::_("Save unsuccessfull");
		$url="index.php?module=user&view=panel&at=tab_3";
		$this->setRedirect($url,$msg);
	}
	public function deleteAddress(){
		$userdata=Userdata::getInstance(User::getInstance()->getID());
		if ($userdata->deleteAddress()) $msg = Text::_("Operation complete");	else $msg=Text::_("Operation failed");
		$url="index.php?module=user&view=panel&at=tab_3";
		$this->setRedirect($url,$msg);
	}

	public function modifyBank(){
		Portal::getInstance()->disableTemplate();
		$view=$this->getView();
		$this->set("layout","bank",true);
		$userdata=Userdata::getInstance(User::getInstance()->getID());
		$psid = Request::getInt("psid",0);
		$view->assign('bank',$userdata->getBank($psid));
		$view->render();
	}
	public function saveBank(){
		$userdata=Userdata::getInstance(User::getInstance()->getID());
		if ($userdata->saveBank()) $msg = Text::_("Save successfull");	else $msg=Text::_("Save unsuccessfull");
		$url="index.php?module=user&view=panel&at=tab_3";
		$this->setRedirect($url,$msg);
	}
	public function deleteBank(){
		$userdata=Userdata::getInstance(User::getInstance()->getID());
		if ($userdata->deleteBank()) $msg = Text::_("Operation complete");	else $msg=Text::_("Operation failed");
		$url="index.php?module=user&view=panel&at=tab_3";
		$this->setRedirect($url,$msg);
	}

	public function modifyCompany(){
		Portal::getInstance()->disableTemplate();
		$view=$this->getView();
		$this->set("layout","company",true);
		$userdata=Userdata::getInstance(User::getInstance()->getID());
		$view->assign('company',$userdata->getCompany());
		$view->render();
	}
	public function saveCompany(){
		$userdata=Userdata::getInstance(User::getInstance()->getID());
		if ($userdata->saveCompany()) $msg = Text::_("Save successfull");	else $msg=Text::_("Save unsuccessfull");
		$url="index.php?module=user&view=panel&at=tab_3";
		$this->setRedirect($url,$msg);
	}
}
?>