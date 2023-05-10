<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class blogModule extends Module {
	public function prepare() {
		/* Not need here if is set in module settings */ $this->setDefaultView('categories');
	}
	public function getLinksArray(&$i,&$_arr) {
		$arrCatsIdEx=explode(",", $this->getParam('exclude_cats_from_map'));
		$arrIdEx=explode(",", $this->getParam('exclude_blogs_from_map'));
		
		$db=Database::getInstance();
		$module=$this->getName();
		// категории блогов
		$sql='SELECT bc_id, bc_name, bc_alias, bc_meta_title FROM #__blogs_cats WHERE bc_enabled=1 AND bc_deleted=0';
		$db->setQuery($sql);
		$res=$db->loadObjectList();
		if (count($res)) {
			foreach($res as $val)  {
				$i++;
				$_arr[$module][$i]['link']=Router::_("index.php?module=blog&view=category&psid=".$val->bc_id."&alias=".$val->bc_alias, true);
				$_arr[$module][$i]['name']=$val->bc_name;
				if ($val->bc_meta_title) $_arr[$module][$i]['fullname']=$val->bc_meta_title;
				else $_arr[$module][$i]['fullname']=$val->bc_name;				
			}
		}
		// блоги и их посты
		$rights=Module::getInstance('blog')->getModel('rights');
		$res_id=array(0); $res_ex=array();
		$sql_blog="SELECT b_id FROM #__blogs WHERE b_deleted=0 AND b_enabled=1";
		$db->setQuery($sql_blog);
		$arrId=$db->LoadResultArray();
		if(count($arrId)) $res_id=array_keys($rights->getBlogsWithAction($arrId,'read'));
		if(count($arrIdEx)) {
			foreach($arrIdEx as $eId) {
				$res_ex[]=intval($eId);
			}
		}
		$query='SELECT b_id, b_name,b_alias,b_meta_title FROM #__blogs WHERE b_enabled=1 AND b_deleted=0';
		if (count($res_id)) $query .= " AND (b_id IN (".implode(",",$res_id)."))";
		if (count($res_ex)) $query .= " AND (b_id NOT IN (".implode(",",$res_ex)."))";
		$db->setQuery($query);
		$res_blog=$db->loadObjectList();
		if (count($res_blog)) {
			foreach($res_blog as $val)  {
				$i++;
				$_arr[$module][$i]['link']=Router::_("index.php?module=blog&view=blog&psid=".$val->b_id."&alias=".$val->b_alias, true);
				$_arr[$module][$i]['name']=$val->b_name;
				if ($val->b_meta_title) $_arr[$module][$i]['fullname']=$val->b_meta_title;
				else $_arr[$module][$i]['fullname']=$val->b_name;
				// идем в посты этого блога
				$sql_post='SELECT p_id, p_theme,p_alias,p_meta_title,p_touch_date FROM #__blogs_posts WHERE p_blog_id='.$val->b_id.' AND  p_enabled=1 AND p_deleted=0';
				if (count($res_id)) $query .= " AND (p_blog_id IN (".implode(",",$res_id)."))";
				if (count($res_ex)) $query .= " AND (p_blog_id NOT IN (".implode(",",$res_ex)."))";
				$db->setQuery($sql_post);
				$res_post=$db->loadObjectList();
				foreach($res_post as $post)  {
					$i++;
					$_arr[$module][$i]['link']=Router::_("index.php?module=blog&view=post&psid=".$post->p_id."&alias=".$post->p_alias, true);
					$_arr[$module][$i]['name']=$post->p_theme;
					if ($val->b_meta_title) $_arr[$module][$i]['fullname']=$post->p_meta_title;
					else $_arr[$module][$i]['fullname']=$post->p_theme;
					$_arr[$module][$i]['date_change']=$post->p_touch_date;
				}
				
			}
		}
		return true;
	}
	public function getACLTemplate($is_admin=true){
		$acl=array();$i=0;
		if($is_admin){
			$i++;$acl[$i]['ao_name']='blogModule'; $acl[$i]['ao_description']='Module access';
			$i++;$acl[$i]['ao_name']='deleteBlogList'; $acl[$i]['ao_description']='Finally delete blogs';
			$i++;$acl[$i]['ao_name']='viewBlogList'; $acl[$i]['ao_description']='View blogs list';
			$i++;$acl[$i]['ao_name']='viewBlogCategories'; $acl[$i]['ao_description']='View categories';
			$i++;$acl[$i]['ao_name']='modifyBlogCategories'; $acl[$i]['ao_description']='Modify categories';
			$i++;$acl[$i]['ao_name']='viewSetRights'; $acl[$i]['ao_description']='View and modify rights';
			$i++;$acl[$i]['ao_name']='deleteBlogCategories'; $acl[$i]['ao_description']='Finally delete categories';
			$i++;$acl[$i]['ao_name']='viewBlogPost'; $acl[$i]['ao_description']='View posts';
			$i++;$acl[$i]['ao_name']='deleteBlogPost'; $acl[$i]['ao_description']='Finally delete blog posts';
		} else {
			$i++;$acl[$i]['ao_name']='blogModule'; $acl[$i]['ao_description']='Module access';
			$i++;$acl[$i]['ao_name']='viewBlogList'; $acl[$i]['ao_description']='View blogs list';
			$i++;$acl[$i]['ao_name']='viewBlogCategories'; $acl[$i]['ao_description']='View categories';
		}
		return 	$acl;
	}
}
?>