<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class feedbackControllerdefault extends SpravController {
	public function showMessages() {
		$layout=$this->get('layout');
		if ($layout=='single') {
			Portal::getInstance()->disableTemplate();
			$psid=Request::getInt('psid',0);
			if ($psid){
				$model = $this->getModel();
				$model->setRead($psid);
				$msg = $model->getElement($psid);
				$view = $this->getView();
				$view->assign('msg',$msg);
			}
		} else $this->showData();
	}
	
	public function modify($ajaxModify=false){
		$mdl = Module::getInstance();
		$moduleName	= $mdl->getName();
		$reestr = $mdl->get('reestr');

		$model = $this->getModel();
		$viewname = $this->getView()->getName();
		$this->checkACL("modify".ucfirst($moduleName).ucfirst($viewname));
		$arr_psid     = Request::get('cps_id', false);				// массив отмеченных галочкой элементов
		$multy_code   = Request::get('multy_code', false); 		// ид верхней группы
		$psid         = Request::get('psid', false); 					// ид строки
		if(!$psid)  if($arr_psid&&is_array($arr_psid)&&count($arr_psid)>0) $psid = $arr_psid[0];
		if($psid) $model->setRead($psid);
		parent::modify($ajaxModify);
	}
}
?>