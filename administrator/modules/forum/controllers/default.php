<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class forumControllerdefault extends SpravController {
	public $actions = array("read","write","moderate","postvote");

	public function showsections() {
		$this->showData();
	}

	public function showthemes() {
		$this->showData("f_name","forum_sections");
	}
	
	public function showposts() {
		$this->showData("t_name","forum_themes");
	}
	
	public function ajaxupdateRoleRule(){
		$forum_id	= Request::getInt("psid");
		$role_id	= Request::getInt("role");
		$action 	= Request::getSafe("act");
		$state			= Request::getInt("flag");
		if (!$forum_id || !$role_id || !in_array($action, $this->actions)) echo "ERROR DATA";
		$rmodel 	= $this->getModel("rights");
		if ($rmodel->setRuleForRole($forum_id,$role_id,$action,$state)) {
			echo "OK";
		} else {
			echo "ERROR UPDATE";
		}
	}
	public function ajaxupdateUserRule(){
		$forum_id	= Request::getInt("psid");
		$user_id	= Request::getInt("user");
		$action 	= Request::getSafe("act");
		$state		= Request::getInt("flag");
		if (!$forum_id || !$user_id || !in_array($action, $this->actions)) echo "ERROR DATA";
		$rmodel 	= $this->getModel("rights");
		if ($rmodel->setRuleForUser($forum_id,$user_id,$action,$state)) {
			echo "OK";
		} else {
			echo "ERROR UPDATE";
		}
	}
	public function viewRolesMap(){
		$this->checkACL("viewSetForumRights");
		$mdl = Module::getInstance();
		$view = $this->getView();
		$fmodel = $this->getModel("sections");
		$rmodel = $this->getModel();
		$forums = $fmodel->getOrderedForums();
		$roles = $rmodel->getRoles();
		$rights = $rmodel->getAllRulesForAllRoles();
		if (!count($forums)) $this->setRedirect('index.php?module=forum',Text::_('Forum not exists'));
		else {
			$view->assign("forums",$forums);
			$view->assign("roles",$roles);
			$view->assign("rights",$rights);
			$view->assign("actions",$this->actions);
		}
	}
	
	public function viewUsersMap(){
		$this->checkACL("viewSetForumRights");
		$mdl = Module::getInstance();
		$view = $this->getView();
		$fmodel = $this->getModel("sections");
		$rmodel = $this->getModel();
		$forums = $fmodel->getOrderedForums();
		$users = $rmodel->getUsers();
		$rights = $rmodel->getAllRulesForAllUsers();
		if (!count($forums)) $this->setRedirect('index.php?module=forum',Text::_('Forum not exists'));
		else {
			$view->assign("forums",$forums);
			$view->assign("users",$users);
			$view->assign("rights",$rights);
			$view->assign("actions",$this->actions);
		}
	}
	public function showRights() {
		$this->checkACL("viewSetForumRights");
		$view = $this->getView();
		$model = $this->getModel("sections");
		switch($this->get('layout')) {
			case "rolesmap":
				$this->viewRolesMap();
				break;
			case "usersmap":
				$this->viewUsersMap();
				break;
			case "modify":
				$this->modifyRights();
				break;
			default:
				$rModel = $this->getModel();
				$return = Request::getSafe("return","");
				$psid         = Request::getSafe('psid', false); 					// ид строки
				$arr_psid     = Request::getSafe('cps_id', false);				// массив отмеченных галочкой элементов
				if(!$psid)  if($arr_psid&&is_array($arr_psid)&&count($arr_psid)>0) $psid = $arr_psid[0];
				$forum = $model->getForum($psid);
				if ($forum) {
					$roles = $rModel->getRoles();
					$view->assign('forum_id',$psid);
					$view->assign('forum_name',$forum->f_name);
					$view->assign('roles',$roles);
					$view->assign('return',$return);
				} else $this->setRedirect('index.php?module=forum',Text::_('Forum not exists'));
			break;
		}
	}
	
	public function modifyRights() {
		$this->checkACL("viewSetForumRights");
		$psid = Request::getInt('psid',0);
		$subject = Request::get('subject','user');
	
		$view = $this->getView();
		$view->assign("actions",$this->actions);
	
		$view->assign('subject',$subject);
		$subjectName = "";
	
		$rModel = $this->getModel();
		$model = $this->getModel("sections");
		$forum = $model->getForum($psid);
		if ($forum) {
			$view->assign('psid',$psid);
			$view->assign('forum_name',$forum->f_name);
	
			$rules = array();
			if ($subject == "role") {
				$roleId = Request::getInt('roleid',0);
				if ($roleId != 0) {
					$view->assign('roleId',$roleId);
					$subjectName = $rModel->getRoleTitle($roleId);
					$rules = $rModel->getRulesForRole($psid,$roleId);
				} else $this->warning(Text::_('Invalid role').": ".$roleId, __FUNCTION__);
			} else {
				$login = Request::get('userlogin','');
				if ($login) {
					$uid = User::getUserId($login);
					if ($uid) {
						$subjectName = $login;
						$view->assign('userId',$uid);
						$rules = $rModel->getRulesForUser($psid,$uid);
					} else $this->setRedirect('index.php?module=forum&view=rights&psid='.$psid,Text::_('User not found'));
				} else $this->setRedirect('index.php?module=forum&view=rights&psid='.$psid,Text::_('Empty username'));
			}
	
			$view->assign('rules',$rules);
			$view->assign('subjectName',$subjectName);
		}
		else $this->setRedirect('index.php?module=forum',Text::_('Forum not found'));
	}
	
	public function updateRights() {
		$psid = Request::getInt('psid',0);
		$roleId = Request::getInt('roleId',0);
		$userId = Request::getInt('userId',0);
		$subject = Request::get('subject','role');
		$access = Request::get('access',array());
		$oldAccess = Request::get('oldAccess',array());
		$userLogin = User::getLoginFor($userId);
		$is_apply	= Request::getSafe('apply');
		if ($is_apply) {
			if ($subject == 'user') {
				$redirectUrl = 'index.php?module=forum&view=rights&layout=modify&subject=user&psid='.$psid.'&userlogin='.$userLogin;
			} else {
				$redirectUrl = 'index.php?module=forum&view=rights&layout=modify&subject=role&psid='.$psid.'&roleid='.$roleId;
			}
		} else {
			$redirectUrl = "index.php?module=forum&view=rights&psid=".$psid;
		}
		$model = $this->getModel("rights");
		if ($subject == "user") $model->cleanRulesForUser($psid,$userId);
		foreach ($access as $action=>$state) {
			if ($state=="on") {
				if ($subject == "role") $model->setRuleForRole($psid,$roleId,$action,1);
				else $model->setRuleForUser($psid,$userId,$action,1);
			}
		}
		foreach ($oldAccess as $action=>$state) {
			if (!array_key_exists($action,$access) && $state=="1") {
				if ($subject == "role") $model->setRuleForRole($psid,$roleId,$action,0);
				//else $model->setRuleForUser($psid,$userId,$action,0);
			}
		}
	
		$this->setRedirect($redirectUrl,Text::_('Rights updated'));
	}
	
}
?>