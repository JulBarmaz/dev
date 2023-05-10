<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class forumModule extends Module {
	public function prepare() {
		/* Not need here if is set in module settings */ $this->setDefaultView('sections');
	}
	public function getACLTemplate($is_admin=true){		
		$acl=array();$i=0;
		if($is_admin){
			$i++;$acl[$i]['ao_name']='forumModule'; $acl[$i]['ao_description']='Module access';
			$i++;$acl[$i]['ao_name']='viewForumSections'; $acl[$i]['ao_description']='View forum sections';
			$i++;$acl[$i]['ao_name']='viewForumPosts'; $acl[$i]['ao_description']='View forum posts';
			$i++;$acl[$i]['ao_name']='viewForumThemes'; $acl[$i]['ao_description']='View forum themes';
			$i++;$acl[$i]['ao_name']='deleteForumThemes'; $acl[$i]['ao_description']='Finally delete forum themes';
			$i++;$acl[$i]['ao_name']='viewSetForumRights'; $acl[$i]['ao_description']='View and modify rights';
			$i++;$acl[$i]['ao_name']='deleteForumSections'; $acl[$i]['ao_description']='Finally delete forum sections';
			$i++;$acl[$i]['ao_name']='deleteForumPosts'; $acl[$i]['ao_description']='Finally delete forum posts';
		} else {
			$i++;$acl[$i]['ao_name']='forumDisableCaptcha'; $acl[$i]['ao_description']='Disable captcha';
			$i++;$acl[$i]['ao_name']='forumModule'; $acl[$i]['ao_description']='Module access';
			$i++;$acl[$i]['ao_name']='forumDisableFloodControl'; $acl[$i]['ao_description']='Disable flood control';
		}
		return 	$acl;
	}
	public function getLinksArray(&$i,&$_arr) {
		$arrIdEx=explode(",", $this->getParam('exclude_forums_from_map'));
		
		$db=Database::getInstance();
		$module=$this->getName();
		// форумы
		$rights=Module::getInstance('forum')->getModel('rights');
		$res_id=array(0); $res_ex=array();
		$sql_sections="SELECT f_id FROM #__forum_sections WHERE f_deleted=0 AND f_enabled=1";
		$db->setQuery($sql_sections);
		$arrId=$db->LoadResultArray();
		
		if(count($arrId)) $res_id=array_keys($rights->getForumsWithAction($arrId,'read'));
		if(count($arrIdEx)) {
			foreach($arrIdEx as $eId) {
				$res_ex[]=intval($eId);
			}
		}
		$query='SELECT f_id, f_name, f_alias, f_meta_title FROM #__forum_sections WHERE f_enabled=1 AND f_deleted=0';
		if (count($res_id)) $query .= " AND (f_id IN (".implode(",",$res_id)."))";
		if (count($res_ex)) $query .= " AND (f_id NOT IN (".implode(",",$res_ex)."))";
		$db->setQuery($query);
		$res_blog=$db->loadObjectList();
		if (count($res_blog)) {
			foreach($res_blog as $val)  {
				$i++;
				$_arr[$module][$i]['link']=Router::_("index.php?module=forum&view=section&psid=".$val->f_id."&alias=".$val->f_alias, true);
				$_arr[$module][$i]['name']=$val->f_name;
				if ($val->f_meta_title) $_arr[$module][$i]['fullname']=$val->f_meta_title;
				else $_arr[$module][$i]['fullname']=$val->f_name;
				// идем темы этого форума
				$sql_post='SELECT t_id, t_theme, t_alias, t_touch_date FROM #__forum_themes WHERE t_forum_id='.$val->f_id.' AND t_enabled=1 AND t_deleted=0';
				if (count($res_id)) $query .= " AND (t_forum_id IN (".implode(",",$res_id)."))";
				if (count($res_ex)) $query .= " AND (t_forum_id NOT IN (".implode(",",$res_ex)."))";
				$db->setQuery($sql_post);
				$res_theme=$db->loadObjectList();
				foreach($res_theme as $theme)  {
					$i++;
					$_arr[$module][$i]['link']=Router::_("index.php?module=forum&view=theme&psid=".$theme->t_id."&alias=".$theme->t_alias, true);
					$_arr[$module][$i]['name']=$theme->t_theme;
					$_arr[$module][$i]['fullname']=$theme->t_theme;
					$_arr[$module][$i]['date_change']=$theme->t_touch_date;
				}
	
			}
		}
		return true;
	}
}
?>