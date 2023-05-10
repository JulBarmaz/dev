<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class userControllerdefault extends SpravController {

	public function ajaxcheckSpravField() {
		$moduleName	= Module::getInstance()->getName();
		$viewname = $this->getView()->getName();
		$layout = $this->getView()->getLayout();
		
		$model = $this->getModel();
		$model->loadMeta();
		$fld=Request::getSafe('fld');
		$val=Request::getSafe('val');
		$psid=Request::getInt('psid',0);
		switch($fld){
			case "sn_name":
				if (!preg_match("/^[a-zA-Z0-9_]+$/", $val)) {
					echo Text::_("Error");
					return;
				}
				break;
		}
		if($model->isUniqueFieldValue($fld, $val, $psid)) echo "OK";  else echo Text::_("Occupied");
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

	public function showUsers() {
		$this->showData();
	}

	public function showAuth_providers() {
		$this->showData();
	}
	
	public function modifyAuth_providers() {
		$mdl = Module::getInstance();
		$reestr = $mdl->get('reestr');
		$reestr->set('task','saveAuth_providers');
		parent::modify();
	}
	
	public function saveAuth_providers() {
		// We dont check sn_name here, it's admin mode, so it's user problem? if he try to hack his own site
		parent::save();
	}
	
	public function showBlacklist() {
		$this->showData();
	}
	public function modifyBlacklist() {
		$mdl = Module::getInstance();
		$reestr = $mdl->get('reestr');
		$reestr->set('task','saveBlacklist');
		parent::modify();
	}
	public function saveBlacklist() {
		parent::save();
	}

	public function showPanel(){
		$this->set('view','users',true);
		$this->showData();
	}

	public function save() {
		$mdl				= Module::getInstance();
		$moduleName	= $mdl->getName();
		$viewname 	= $this->getView()->getName();
		if (defined("_ADMIN_MODE")) $this->checkACL("view".ucfirst($mdl->getName()).ucfirst($viewname));
		else $this->checkACL("modify".ucfirst($mdl->getName()).ucfirst($viewname));

		$userId 		= Request::getInt('psid',0);
		$userLogin		= Request::get('u_login','');
		$referral		= Request::get('u_referral',0);
		$discount		= Request::get('u_discount',0);
		$priceType		= Request::get('u_pricetype',1);
		$userNickname	= Request::get('u_nickname','');
		$userEmail		= Request::get('u_email','');
		$userPassword	= Request::get('u_secret','');
		$userActivated	= Request::getInt('u_activated');
		$userRole		= Request::getInt('u_role',User::getDefaultRole());

		$is_apply		= Request::getSafe('apply');
		$multy_code		= Request::getSafe('multy_code', 0);
		$layout			= Request::getSafe('layout');
		$page			= Request::getInt('page', 1);
		$sort			= Request::getSafe('sort');
		$orderby		= Request::getSafe('orderby');
		if ($userLogin == '' || $userNickname == '' || $userEmail == '' || $userRole == 0) {
			$msg=Text::_('Not enough data');
		}	else {
			if ($userId) {
				// Edit user
				$res=User::saveUser($userId,$userLogin,$userNickname,$userEmail,$userRole,$userActivated,$discount,$priceType);
				if ($res) {
					if ($userPassword) User::setPassword($userId,$userPassword,$userLogin);
					$msg=Text::_('User saved');
				} else {
					$msg=Text::_('Save failed');
				}
			}	else {
				// New user
				if ($userPassword == '') {
					$msg=Text::_('Not enough data');
				} elseif (User::isUser($userLogin)) {
					$msg=Text::_('Login is reserved');
				}	else {
					$userId = User::addUser($userLogin,$userNickname,$userPassword,$userEmail,$userRole,"",$userActivated,$discount,$priceType);
					if($userId) {
						$reg_data = array("uid"=>$userId);
						Event::raise("register.proceedRegistration",array("module"=>"user", "source"=>"module.user", "action"=>"admin.save"), $reg_data);
						$msg = Text::_('User added');
					} else {
						$msg = Text::_('User add failed');
					}
				}
			}
		}
		if ($is_apply) $url='index.php?module='.$moduleName.'&view='.$viewname.'&layout='.$layout.'&task=modify&psid='.$userId.'&sort='.$sort.'&page='.$page.'&orderby='.$orderby.'&multy_code='.$multy_code;
		else $url='index.php?module='.$moduleName.'&view='.$viewname.'&layout='.$layout.'&sort='.$sort.'&page='.$page.'&orderby='.$orderby.'&multy_code='.$multy_code;
		$this->setRedirect($url,$msg);
	}

	public function modify($ajaxModify=false){
		$mdl		= Module::getInstance();
		$moduleName	= $mdl->getName();
		$viewname 	= $this->getView()->getName();
		$this->checkACL("modify".ucfirst($mdl->getName()).ucfirst($viewname));
		parent::modify($ajaxModify);
	}
	public function modifyProfile() {
		if($this->getPsid()){
			$mdl = Module::getInstance();
			$reestr = $mdl->get('reestr');
			$reestr->set('task','saveProfile');
			parent::modify();
		} else {
			$url="index.php?module=user";
			$this->setRedirect($url);
		}
	}

	public function saveProfile() {
		$mdl = Module::getInstance();
		$reestr = $mdl->get('reestr');
		$reestr->set('task','modifyProfile');
		parent::save();
	}

	public function activityUser()
	{
		$userId=$this->getPsid();
		$model=$this->getModel();
		$view=$this->getView();
		// активность в статьях


		// активность в голосованиях


		// активность в форумах
		$view->assign('$act_forums',$model->getForumInfo($userId));

		// активность в комментариях

		// активность в блогах

		// активность в ЖКХ



		echo $userId;
	}
}
?>