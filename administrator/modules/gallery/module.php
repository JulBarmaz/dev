<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class galleryModule extends Module {
	public function prepare() {
		/* Not need here if is set in module settings */ $this->setDefaultView('groups');
	}
	public function getLinksArray(&$i,&$_arr) {
		$db=Database::getInstance();		
		$module=$this->getName();
		$sql='SELECT gr_id, gr_title, gr_alias FROM #__gallery_groups WHERE gr_deleted=0 AND gr_published=1';
		$db->setQuery($sql);
		$res=$db->loadObjectList();
		if (count($res)) {
			foreach($res as $val)  {
				$i++;
				$_arr[$module][$i]['link']=Router::_("index.php?module=gallery&view=items&psid=".$val->gr_id."&alias=".$val->gr_alias, true);
				$_arr[$module][$i]['name']=$val->gr_title;
				$_arr[$module][$i]['fullname']=$val->gr_title;
			}
		}
		$sql1='SELECT g_id, g_title, g_alias FROM #__galleries WHERE g_deleted=0 AND g_published=1';
		$db->setQuery($sql1);
		$res1=$db->loadObjectList();
		if (count($res1)) {
			foreach($res1 as $val1)  {
				$i++;
				$_arr[$module][$i]['link']=Router::_("index.php?module=gallery&view=images&psid=".$val1->g_id."&alias=".$val1->g_alias, true);
				$_arr[$module][$i]['name']=$val1->g_title;
				$_arr[$module][$i]['fullname']=$val1->g_title;
			}
		}
		return true;
	}
	public function getACLTemplate($is_admin=true){		
		$acl=array();$i=0;
		if($is_admin){
			$i++;$acl[$i]['ao_name']='galleryModule'; $acl[$i]['ao_description']='Module access';
			$i++;$acl[$i]['ao_name']='viewGalleryGroups'; $acl[$i]['ao_description']='Gallery groups';
			$i++;$acl[$i]['ao_name']='viewGalleryItems'; $acl[$i]['ao_description']='Gallery items';
			$i++;$acl[$i]['ao_name']='viewGalleryImages'; $acl[$i]['ao_description']='Gallery images';
			$i++;$acl[$i]['ao_name']='deleteGalleryGroups'; $acl[$i]['ao_description']='Finally delete groups';
			$i++;$acl[$i]['ao_name']='deleteGalleryItems'; $acl[$i]['ao_description']='Finally delete galleries';
			$i++;$acl[$i]['ao_name']='deleteGalleryImages'; $acl[$i]['ao_description']='Finally delete images';
		} else {
			$i++;$acl[$i]['ao_name']='galleryModule'; $acl[$i]['ao_description']='Module access';
			$i++;$acl[$i]['ao_name']='viewGalleryGroups'; $acl[$i]['ao_description']='Gallery groups';
			$i++;$acl[$i]['ao_name']='viewGalleryItems'; $acl[$i]['ao_description']='Gallery items';
			$i++;$acl[$i]['ao_name']='viewGalleryImages'; $acl[$i]['ao_description']='Gallery images';
		}
		return 	$acl;
	}
}
?>