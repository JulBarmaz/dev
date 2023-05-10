<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class menusControllerdefault extends SpravController {

	public function ajaxGetViewsSelector(){
		$modname   = Request::getSafe('modname', false); 					// ид верхней группы
		$view = $this->getView();
		$view->assign("modname",$modname);
		$view->assign("vname","");
		echo $view->getViewsList();
	}

	public function ajaxGetControllersSelector(){
		$modname   = Request::getSafe('modname', false); 					// ид верхней группы
		$view = $this->getView();
		$view->assign("modname",$modname);
		$view->assign("vcontroller","");
		echo $view->getControllersList();
	}
	
	public function showItems(){
		$this->showData();
	}

	public function save(){
		$mi_access_all = Request::get("mi_access_all","off");
		if ($mi_access_all == "off") {
			$mi_access_arr = Request::get("mi_access",array());
			$mi_access = "";
			foreach ($mi_access_arr as $role=>$on) {
				$mi_access .= "$role;";
			}
			$mi_access = mb_substr($mi_access,0,mb_strlen($mi_access,DEF_CP) - 1,DEF_CP);
		}	else $mi_access = "all";
		$_REQUEST['mi_access']=$mi_access;
		parent::save();
	}

}
?>