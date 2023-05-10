<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class commentsControllerdefault extends SpravController {
	public function showgroups() {
		$this->showData();
	}
	public function ajaxupdateRoleRule(){
		$grp_id	= Request::getSafe("psid");
		$role_id	= Request::getSafe("role");
		$action 	= Request::getSafe("act");
		$state			= Request::getInt("flag");
		if (!$grp_id || !$role_id || !in_array($action, BaseComments::getInstance()->getActions())) echo "ERROR DATA";
		$model 	= $this->getModel("rights");
		if ($model->setRuleForRole($grp_id,$role_id,$action,$state)) {
			echo "OK";
		} else {
			echo "ERROR UPDATE";
		}
	}
	public function showRights() {
		$mdl = Module::getInstance();
		$view = $this->getView();
		$model = $this->getModel();
		$groups = $model->getGroups();
		$roles = $model->getRoles();
		$rights = $model->getAllRulesForAllRoles();
		if (!count($groups)) $this->setRedirect('index.php?module=comments',Text::_('Group not exists'));
		else {
			$view->assign("grps",$groups);
			$view->assign("roles",$roles);
			$view->assign("rights",$rights);
			$view->assign("actions",BaseComments::getInstance()->getActions());
		}
	}
	public function showcomments(){
		$this->showData("cg_title","comms_grp");
	}
	public function showcomcat(){
		$this->showData();
	}
	public function showcomtypes() {
		$this->showData();
	}	
	
}

?>