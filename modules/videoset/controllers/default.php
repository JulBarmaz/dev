<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class videosetControllerdefault extends Controller {
	public function showGroups() {
		$view=$this->getView();
		$viewname = $view->getName();
		$mdl=Module::getInstance();
		$this->checkACL("view".ucfirst($mdl->getName()).ucfirst($viewname));
		$model = $this->getModel();
		$psid	=	$this->getPsid();
		$groups=$model->getGroups();
		$view->assign('groups',$groups);
		$view->addBreadcrumb(Text::_("Videos"), "#");
	}
	public function showItems() {
		$view=$this->getView();
		$viewname = $view->getName();
		$mdl=Module::getInstance("gallery");
		$this->checkACL("view".ucfirst($mdl->getName()).ucfirst($viewname));
		$model = $this->getModel();
		$psid         = Request::getSafe('psid', false); 					// ид строки
		$group=$model->getGroup($psid);
		$items=$model->getItems($psid);
		if (is_object($group)){
			if($group->vgr_meta_keywords<>'')Portal::getInstance()->setMeta("keywords",$group->vgr_meta_keywords);
			if($group->vgr_meta_description<>'') Portal::getInstance()->setMeta("description",$group->vgr_meta_description);
			if($group->vgr_meta_title<>'') Portal::getInstance()->setTitle($group->vgr_meta_title);
			else Portal::getInstance()->setTitle($group->vgr_title);
			$view->addBreadcrumb(Text::_("Videos"), Router::_("index.php?module=videoset"));
			$href="index.php?module=videoset&view=items&psid=".$group->vgr_id;
			if($group->vgr_alias) $href.="&alias=".$group->vgr_alias;
			$view->addBreadcrumb(Text::_($group->vgr_title), "#");
			$view->assign('group',$group);
			$view->assign('items',$items);
		} else $this->setRedirect("index.php?module=videoset", Text::_("Page not found"), 404);
	}
	public function showVideos() {
		$view=$this->getView();
		$viewname = $view->getName();
		$mdl=Module::getInstance();
		$this->checkACL("view".ucfirst($mdl->getName()).ucfirst($viewname));
		$model = $this->getModel();
		$psid = Request::getSafe('psid', false); 	// ид строки
		$layout=$this->get('layout');
		$view->addBreadcrumb(Text::_('Videos'), Router::_("index.php?module=videoset"));
		$item=$model->getItem($psid);
		if (is_object($item)) {
			$group=$model->getGroup($item->vg_group_id);
			if(is_object($group)){						
				if($group->vgr_meta_keywords<>'')Portal::getInstance()->setMeta("keywords",$group->vgr_meta_keywords);
				if($group->vgr_meta_description<>'') Portal::getInstance()->setMeta("description",$group->vgr_meta_description);
				if($group->vgr_meta_title<>'') Portal::getInstance()->setTitle($group->vgr_meta_title);
				else Portal::getInstance()->setTitle($group->vgr_title);
				if(is_object($group)&&$group->vgr_show_in_list) {
					$href="index.php?module=videoset&view=items&psid=".$group->vgr_id;
					if($group->vgr_alias) $href.="&alias=".$group->vgr_alias;
					$view->addBreadcrumb(Text::_($group->vgr_title), Router::_($href));
				}
				$href="index.php?module=videoset&view=videos&psid=".$item->vg_id;
				if($item->vg_alias) $href.="&alias=".$item->vg_alias;
				$view->addBreadcrumb(Text::_($item->vg_title), "#");
				$view->assign('group',$group);
				$videos=$model->getVideos($psid);
				$view->assign('item',$item);
				$view->assign('videos',$videos);
			} else $this->setRedirect("index.php?module=videoset", Text::_("Page not found"), 404);
		} else $this->setRedirect("index.php?module=videoset", Text::_("Page not found"), 404);
	}
	public function showVideo() {
		$view=$this->getView();
		$viewname = $view->getName();
		$mdl=Module::getInstance();
		$this->checkACL("view".ucfirst($mdl->getName()).ucfirst($viewname)."s");
		$model = $this->getModel();
		$psid = Request::getSafe('psid', false); 	// ид строки
		$layout=$this->get('layout');
		$view->assign("big_width", $this->getConfigVal("big_width"));
		$view->assign("big_height", $this->getConfigVal("big_height"));
		$view->addBreadcrumb(Text::_('Videos'), Router::_("index.php?module=videoset"));
		$video=$model->getVideo($psid);
		if(is_object($video)){
			if($video->v_meta_keywords<>'')Portal::getInstance()->setMeta("keywords",$video->v_meta_keywords);
			if($video->v_meta_description<>'') Portal::getInstance()->setMeta("description",$video->v_meta_description);
			if($video->v_meta_title<>'') Portal::getInstance()->setTitle($video->v_meta_title);
			else Portal::getInstance()->setTitle($video->v_title);
			$videos=$model->getVideos($video->v_gallery_id);
			$gallery=$model->getItem($video->v_gallery_id);
			if(is_object($gallery)) {
				$group=$model->getGroup($gallery->vg_group_id);
				if(is_object($group)){
					if($group->vgr_show_in_list) {
						$href="index.php?module=videoset&view=items&psid=".$group->vgr_id;
						if($group->vgr_alias) $href.="&alias=".$group->vgr_alias;
						$view->addBreadcrumb(Text::_($group->vgr_title),Router::_($href));
					}
					$href="index.php?module=videoset&view=videos&psid=".$gallery->vg_id;
					if($gallery->vg_alias) $href.="&alias=".$gallery->vg_alias;
					$view->addBreadcrumb(Text::_($gallery->vg_title),Router::_($href));
					$view->addBreadcrumb($video->v_title, "#");
					$view->assign('video',$video);
					$view->assign('videos',$videos);
				} else $this->setRedirect("index.php?module=videoset", Text::_("Page not found"), 404);
			} else $this->setRedirect("index.php?module=videoset", Text::_("Page not found"), 404);
		} else $this->setRedirect("index.php?module=videoset", Text::_("Page not found"), 404);
	}
}

?>