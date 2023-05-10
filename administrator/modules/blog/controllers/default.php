<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class blogControllerdefault extends SpravController {
	public $actions = array("read","write","moderate","postvote");

	public function ajaxupdateRoleRule(){
		$blog_id	= Request::getInt("psid");
		$role_id	= Request::getInt("role");
		$action 	= Request::getSafe("act");
		$state			= Request::getInt("flag");
		if (!$blog_id || !$role_id || !in_array($action, $this->actions)) echo "ERROR DATA";
		$rmodel 	= $this->getModel("rights");
		if ($rmodel->setRuleForRole($blog_id,$role_id,$action,$state)) {
			echo "OK";
		} else {
			echo "ERROR UPDATE";
		}
	}
	public function ajaxupdateUserRule(){
		$blog_id	= Request::getInt("psid");
		$user_id	= Request::getInt("user");
		$action 	= Request::getSafe("act");
		$state			= Request::getInt("flag");
		if (!$blog_id || !$user_id || !in_array($action, $this->actions)) echo "ERROR DATA";
		$rmodel 	= $this->getModel("rights");
		if ($rmodel->setRuleForUser($blog_id,$user_id,$action,$state)) {
			echo "OK";
		} else {
			echo "ERROR UPDATE";
		}
	}
	public function viewRolesMap(){
		$this->checkACL("viewSetRights");
		$mdl = Module::getInstance();
		$view = $this->getView();
		$bmodel = $this->getModel("list");
		$rmodel = $this->getModel();
		$blogs = $bmodel->getBlogs();
		$roles = $rmodel->getRoles();
		$rights = $rmodel->getAllRulesForAllRoles();
		if (!count($blogs)) $this->setRedirect('index.php?module=blog',Text::_('Blog not exists'));
		else {
			$view->assign("blogs",$blogs);
			$view->assign("roles",$roles);
			$view->assign("rights",$rights);
			$view->assign("actions",$this->actions);
		}
	}

	public function viewUsersMap(){
		$this->checkACL("viewSetRights");
		$mdl = Module::getInstance();
		$view = $this->getView();
		$bmodel = $this->getModel("list");
		$rmodel = $this->getModel();
		$blogs = $bmodel->getBlogs();
		$users = $rmodel->getUsers();
		$rights = $rmodel->getAllRulesForAllUsers();
		if (!count($blogs)) $this->setRedirect('index.php?module=blog',Text::_('Blog not exists'));
		else {
			$view->assign("blogs",$blogs);
			$view->assign("users",$users);
			$view->assign("rights",$rights);
			$view->assign("actions",$this->actions);
		}
	}

	public function showList() {
		$layout		= Request::get('layout');
		switch ($layout)  {
			case "all":
				$mdl = Module::getInstance();
				$reestr = $mdl->get('reestr');
				$reestr->set('consider_parents',false);
				$this->showData();
				break;
			default:
				$this->showData('bc_name','blogs_cats');
				break;
		}
	}
	public function showPost() {
		$this->showData('b_name','blogs');
	}

	public function showCategories() {
		$this->showData();
	}
	public function showRights() {
		$this->checkACL("viewSetRights");
		$view = $this->getView();
		$blogModel = $this->getModel("list");
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
				$blog = $blogModel->getBlog($psid);
				if ($blog) {
					$roles = $rModel->getRoles();
					$view->assign('blogId',$psid);
					$view->assign('blogName',$blog->b_name);
					$view->assign('roles',$roles);
					$view->assign('return',$return);
				} else $this->setRedirect('index.php?module=blog',Text::_('Blog not exists'));
				break;
		}
	}

	public function modifyRights() {
		$this->checkACL("viewSetRights");
		$blogId = Request::getInt('blogid',0);
		$subject = Request::get('subject','user');

		$view = $this->getView();
		$view->assign("actions",$this->actions);

		$view->assign('subject',$subject);
		$subjectName = "";

		$rModel = $this->getModel();
		$blogModel = $this->getModel("list");
		$blog = $blogModel->getBlog($blogId);
		if ($blog) {
			$view->assign('blogId',$blogId);
			$view->assign('blogName',$blog->b_name);

			$rules = array();
			if ($subject == "role") {
				$roleId = Request::getInt('roleid',0);
				if ($roleId != 0) {
					$view->assign('roleId',$roleId);
					$subjectName = $rModel->getRoleTitle($roleId);
					$rules = $rModel->getRulesForRole($blogId,$roleId);
				} else $this->warning(Text::_('Invalid role').": ".$roleId, __FUNCTION__);
			} else {
				$login = Request::get('userlogin','');
				if ($login) {
					$uid = User::getUserId($login);
					if ($uid) {
						$subjectName = $login;
						$view->assign('userId',$uid);
						$rules = $rModel->getRulesForUser($blogId,$uid);
					} else $this->setRedirect('index.php?module=blog&view=rights&blogid='.$blogId,Text::_('User not found'));
				} else $this->setRedirect('index.php?module=blog&view=rights&blogid='.$blogId,Text::_('Empty username'));
			}

			$view->assign('rules',$rules);
			$view->assign('subjectName',$subjectName);
		} else $this->setRedirect('index.php?module=blog',Text::_('Blog not found'));
	}

	public function updateRights() {
		$blogId = Request::getInt('blogId',0);
		$roleId = Request::getInt('roleId',0);
		$userId = Request::getInt('userId',0);
		$subject = Request::get('subject','role');
		$access = Request::get('access',array());
		$oldAccess = Request::get('oldAccess',array());
		$userLogin = User::getLoginFor($userId);
		$is_apply	= Request::getSafe('apply');
		if ($is_apply) {
			if ($subject == 'user') {
				$redirectUrl = 'index.php?module=blog&view=rights&layout=modify&subject=user&blogid='.$blogId.'&userlogin='.$userLogin;
			} else {
				$redirectUrl = 'index.php?module=blog&view=rights&layout=modify&subject=role&blogid='.$blogId.'&roleid='.$roleId;
			}
		} else {
			$redirectUrl = "index.php?module=blog&view=rights&blogid=".$blogId;
		}
		$model = $this->getModel("rights");
		if ($subject == "user") $model->cleanRulesForUser($psid,$userId);
		foreach ($access as $action=>$state) {
			if ($state=="on") {
				if ($subject == "role") $model->setRuleForRole($blogId,$roleId,$action,1);
				else $model->setRuleForUser($blogId,$userId,$action,1);
			}
		}
		foreach ($oldAccess as $action=>$state) {
			if (!array_key_exists($action,$access) && $state=="1") {
				if ($subject == "role") $model->setRuleForRole($blogId,$roleId,$action,0);
				// else $model->setRuleForUser($blogId,$userId,$action,0);
			}
		}

		$this->setRedirect($redirectUrl,Text::_('Rights updated'));
	}

}

?>