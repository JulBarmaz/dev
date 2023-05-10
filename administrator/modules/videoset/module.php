<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class videosetModule extends Module {
	public function prepare() {
		/* Not need here if is set in module settings */ $this->setDefaultView('groups');
	}
	public function getLinksArray(&$i,&$_arr) {
		$db=Database::getInstance();		
		$module=$this->getName();
		$sql='SELECT vgr_id, vgr_title, vgr_alias FROM #__videoset_groups WHERE vgr_deleted=0 AND vgr_published=1';
		$db->setQuery($sql);
		$res=$db->loadObjectList();
		if (count($res)) {
			foreach($res as $val)  {
				$i++;
				$_arr[$module][$i]['link']=Router::_("index.php?module=videoset&view=items&psid=".$val->vgr_id."&alias=".$val->vgr_alias, true);
				$_arr[$module][$i]['name']=$val->vgr_title;
				$_arr[$module][$i]['fullname']=$val->vgr_title;
			}
		}
		$sql1='SELECT vg_id, vg_title, vg_alias FROM #__videoset_galleries WHERE vg_deleted=0 AND vg_published=1';
		$db->setQuery($sql1);
		$res1=$db->loadObjectList();
		if (count($res1)) {
			foreach($res1 as $val1)  {
				$i++;
				$_arr[$module][$i]['link']=Router::_("index.php?module=videoset&view=videos&psid=".$val1->vg_id."&alias=".$val1->vg_alias, true);
				$_arr[$module][$i]['name']=$val1->vg_title;
				$_arr[$module][$i]['fullname']=$val1->vg_title;
			}
		}
		return true;
	}
	public function getACLTemplate($is_admin=true){		
		$acl=array();$i=0;
		if($is_admin){
			$i++;$acl[$i]['ao_name']='videosetModule'; $acl[$i]['ao_description']='Module access';
			$i++;$acl[$i]['ao_name']='viewVideosetItems'; $acl[$i]['ao_description']='Gallery items';
			$i++;$acl[$i]['ao_name']='viewVideosetGroups'; $acl[$i]['ao_description']='Gallery groups';
			$i++;$acl[$i]['ao_name']='viewVideosetVideos'; $acl[$i]['ao_description']='Gallery videos';
			$i++;$acl[$i]['ao_name']='deleteVideosetGroups'; $acl[$i]['ao_description']='Finally delete groups';
			$i++;$acl[$i]['ao_name']='deleteVideosetItems'; $acl[$i]['ao_description']='Finally delete galleries';
			$i++;$acl[$i]['ao_name']='deleteVideosetVideos'; $acl[$i]['ao_description']='Finally delete videos';
		} else {
			$i++;$acl[$i]['ao_name']='viewVideosetItems'; $acl[$i]['ao_description']='Gallery items';
			$i++;$acl[$i]['ao_name']='videosetModule'; $acl[$i]['ao_description']='Module access';
			$i++;$acl[$i]['ao_name']='viewVideosetGroups'; $acl[$i]['ao_description']='Gallery groups';
			$i++;$acl[$i]['ao_name']='viewVideosetVideos'; $acl[$i]['ao_description']='Gallery videos';
		}
		return 	$acl;
	}
}
?>