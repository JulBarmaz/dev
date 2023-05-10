<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class galleryControllerdefault extends SpravController {
	public function showGroups() {
		$view=$this->getView();
		$viewname = $view->getName();
		$mdl=Module::getInstance();
		$this->checkACL("view".ucfirst($mdl->getName()).ucfirst($viewname));
		$model = $this->getModel();
		$psid	=	$this->getPsid();
		$groups=$model->getGroups();
		$view->assign('groups',$groups);
		$view->addBreadcrumb(Text::_("Gallery groups"), "#");
	}
	public function showItems() {
		$view=$this->getView();
		$viewname = $view->getName();
		$mdl=Module::getInstance("gallery");
		$this->checkACL("view".ucfirst($mdl->getName()).ucfirst($viewname));
		$model = $this->getModel();
		$psid         = Request::getSafe('psid', false); 					// ид строки
		$group=$model->getGroup($psid);
		if (is_object($group)) {
			if($group->gr_layout) $view->setlayout($group->gr_layout);
			if($group->gr_meta_keywords<>'')Portal::getInstance()->setMeta("keywords",$group->gr_meta_keywords);
			if($group->gr_meta_description<>'') Portal::getInstance()->setMeta("description",$group->gr_meta_description);
			if($group->gr_meta_title<>'') Portal::getInstance()->setTitle($group->gr_meta_title);
			else Portal::getInstance()->setTitle($group->gr_title);
				
			$items=$model->getItems($psid);
			$view->assign('group',$group);
			$view->assign('items',$items);

			$view->addBreadcrumb(Text::_("Main page"),Router::_("index.php"));
			$href="index.php?module=gallery&view=items&psid=".$group->gr_id;
			if($group->gr_alias) $href.="&alias=".$group->gr_alias;
			$view->addBreadcrumb(Text::_($group->gr_title),Router::_($href));
		} else $this->setRedirect("index.php?module=gallery", Text::_("Page not found"), 404);
	}
	public function showImages() {
		$view=$this->getView();
		$viewname = $view->getName();
		$mdl=Module::getInstance();
		$this->checkACL("view".ucfirst($mdl->getName()).ucfirst($viewname));
		$model = $this->getModel();
		$reestr = $mdl->get('reestr');
		$model = $this->getModel();
		$model->loadMeta();
		// мультикод может проставляться при вызове в контроллерах модулей.
		$multy_code   = Request::getSafe('multy_code', $reestr->get("multy_code",false)); 		// ид верхней группы
		$psid=$this->getPsid();
		if(!$multy_code) $multy_code=$psid;
		elseif(!$psid) $psid=$multy_code;
		$reestr->set('psid',$psid);
		$reestr->set('page', Request::getInt('page', 1));
		$reestr->set('sort',Request::getSafe("sort"));
		$reestr->set('orderby',Request::getSafe("orderby"));
		$reestr->set("multy_code",$multy_code);
		$item=$model->getItem($psid);
		$images=$model->getData($psid);
		if (is_object($item)) {	
			$group=$model->getGroup($item->g_group_id);
			if(is_object($group)){
			    if($item->g_layout) $view->setLayout($item->g_layout);
				if($item->g_meta_keywords<>'')Portal::getInstance()->setMeta("keywords",$item->g_meta_keywords);
				if($item->g_meta_description<>'') Portal::getInstance()->setMeta("description",$item->g_meta_description);		
				if($item->g_meta_title) Portal::getInstance()->setTitle($item->g_meta_title);
				else Portal::getInstance()->setTitle($item->g_title);
				$view->assign("images_by_row", intval($item->g_images_by_row)>0 ? $item->g_images_by_row : $this->getConfigVal("images_by_row"));
				$view->assign('group', $group);
				$view->assign('item', $item);
				$view->assign('images', $images);

				$view->addBreadcrumb(Text::_("Main page"),Router::_("index.php"));
				$href="index.php?module=gallery&view=items&psid=".$group->gr_id;
				if($group->gr_alias) $href.="&alias=".$group->gr_alias;
				$view->addBreadcrumb(Text::_($group->gr_title),Router::_($href));
				$href="index.php?module=gallery&view=images&psid=".$item->g_id;
				if($item->g_alias) $href.="&alias=".$item->g_alias;
				$view->addBreadcrumb(Text::_($item->g_title),Router::_($href));
			} else $this->setRedirect("index.php?module=gallery", Text::_("Page not found"), 404);
		} else $this->setRedirect("index.php?module=gallery", Text::_("Page not found"), 404);
	}
}

?>