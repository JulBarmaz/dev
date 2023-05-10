<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class articleControllerdefault extends SpravController {
	public function showitems() {
		$this->showData();
	}
	public function changeDateForm(){
		Portal::getInstance()->disableTemplate();
		// это статья
		$psid=$this->getPsid();
		$model=$this->getModel('items');
		$view=$this->getView();
		$data=$model->getDataArticle($psid);
		$view->renderChangeForm($psid,$data);
	}
	public function changeDate(){
		$model=$this->getModel('items');
		$psid=Request::getInt('psid');
		$new_date=Request::getInt('a_date');
		$res=$model->changeDateItems($psid,$new_date);
		if($res) $msg=Text::_("Data changed");
		else $msg=Text::_("Failed data change");
		$this->setredirect("index.php?module=article");
	}
	public function save() {
		$mdl				= Module::getInstance();
		$moduleName	= $mdl->getName();
		$reestr 		= $mdl->get('reestr');
		$model 			= $this->getModel();
		$viewname 	= $this->getView()->getName();
		if (defined("_ADMIN_MODE")) $this->checkACL("view".ucfirst($mdl->getName()).ucfirst($viewname));
		else $this->checkACL("modify".ucfirst($mdl->getName()).ucfirst($viewname));
		$psid       = Request::getSafe('psid', false); 					// ид строки
		$multy_code	= Request::getSafe('multy_code', 0);
		$layout		= Request::getSafe('layout');
		$page		= Request::getInt('page', 1);
		$sort		= Request::getSafe('sort');
		$orderby	= Request::getSafe('orderby');
		$is_apply	= Request::getSafe('apply');
		$is_add_new		= Request::getSafe('add_new');
		$task		= $reestr->get("task","");
		$reestr->set('multy_code',$multy_code);
		$reestr->set('view',$viewname);
		$reestr->set('psid',$psid);
		$new_psid=$model->save();
		if($new_psid) {
			$name	= Request::get('a_title',"");
			$alias	= Request::get('a_alias',"");
			$model->updateAlias($new_psid,$alias,$name);
			$model->garbageCollector($new_psid);
			$msg=Text::_("Save successfull"); $new_psid=urlencode($new_psid);
		}	else { $msg=Text::_("Save unsuccessfull"); $new_psid=urlencode($psid); }
		if ($is_apply) $url='index.php?module='.$moduleName.'&view='.$viewname.'&layout='.$layout.'&task=modify&psid='.$new_psid.'&sort='.$sort.'&page='.$page.'&orderby='.$orderby.'&multy_code='.$multy_code;
		elseif($is_add_new) $url='index.php?module='.$moduleName.'&view='.$viewname.'&layout='.$layout.'&task='.($task ? $task : "modify").'&sort='.$sort.'&page='.$page.'&orderby='.$orderby.'&multy_code='.$multy_code;
		else $url='index.php?module='.$moduleName.'&view='.$viewname.'&layout='.$layout.'&sort='.$sort.'&page='.$page.'&orderby='.$orderby.'&multy_code='.$multy_code;
		$this->setRedirect($url,$msg);
	}
	
}
?>