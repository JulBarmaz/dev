<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class aclmgrControllerdefault extends SpravController {
	// работа с правами
	public function showRules() {
		$view = $this->getView();
		
		$roleId = Request::getInt('roleid',1);
		
		$message=array();
		if(Module::getHelper("acl", "aclmgr")->checkModulesAccess($message)){ // True if all rules exists and role has access for all modules.
			$message=array(); // reset messages if everything OK
			if(Module::getHelper("acl", "aclmgr")->checkModulesAcl($message)) $message=array();  // reset messages if everything OK
		} elseif(User::getInstance()->getRole() != $roleId) {
			 Util::redirect("index.php?module=aclmgr&view=roles", Text::_("Check your role module access rules first")); 
		}
		$view->assign('message',$message);
		
		$roleModel = $this->getModel('roles');
		$ruleModel = $this->getModel('rules');

		$tabId = Request::get('tabid','frontend');

		$objectsFE = $ruleModel->getObjects(0);
		$objectsBE = $ruleModel->getObjects(1);
		$rulesFE = $ruleModel->getRules($roleId,$objectsFE);
		$rulesBE = $ruleModel->getRules($roleId,$objectsBE);

		foreach ($rulesFE as $rf) { $objectsFE[$rf->acl_object_id]->canAccess = $rf->acl_access; }
		foreach ($rulesBE as $rb) { $objectsBE[$rb->acl_object_id]->canAccess = $rb->acl_access; }

		$view->assign('roleName',$roleModel->getRoleName($roleId));
		$view->assign('roleId',$roleId);
		$view->assign('tabId',$tabId);
		$view->assign('objectsFE',$objectsFE);
		$view->assign('objectsBE',$objectsBE);
	}
	
	public function update() {
		$roleId = Request::getInt('roleId',0);
		$access = Request::get('access',array());
		$oldAccess = Request::get('oldAccess',array());
		$tabId = Request::get('tabId','');

		$model = $this->getModel('rules');
		foreach ($access as $objectId=>$state) {
			if ($state=="on")
				$model->setRule($objectId,$roleId,1);
		}
		foreach ($oldAccess as $objectId=>$state) {
			if (!array_key_exists($objectId,$access) && $state=="1")
				$model->setRule($objectId,$roleId,0);
		}

		$this->setRedirect("index.php?module=aclmgr&view=rules&roleid=".$roleId."&tabid=".$tabId,Text::_('ACL updated'));
	}
	// работа с ролями (override справочника)
	public function showRoles() {
		$this->showData();
	}
	
	public function delete() {
		$mdl = Module::getInstance();
		$model = $this->getModel();
		$sr = $model->getSystemRoles();
		$dr = User::getDefaultRole();
		$arr_psid     = Request::get('cps_id', false);				// массив отмеченных галочкой элементов
		$psid         = Request::get('psid', false); 					// ид строки
		if (!$psid) if($arr_psid&&is_array($arr_psid)&&count($arr_psid)>0) $psid = $arr_psid[0];
		if ($psid) {
			if($dr==$psid) $this->setRedirect("index.php?module=aclmgr",Text::_('It is default role'));
			elseif(array_key_exists($psid, $sr)) $this->setRedirect("index.php?module=aclmgr",Text::_('It is system role'));
			else parent::delete();
		} else {
			$this->setRedirect("index.php?module=aclmgr",Text::_('Absent role'));
		}
	}
}
?>