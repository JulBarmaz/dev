<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class articleModule extends Module {
	public function prepare() {
		/* Not need here if is set in module settings */ $this->setDefaultView('items');
	}
	public function getLinksArray(&$i,&$_arr) {
		$db=Database::getInstance();
		$module=$this->getName();
		$sql2='SELECT a_id, a_title, a_alias, a_date, a_deleted FROM #__articles WHERE a_deleted=0 and a_published=1';
		$db->setQuery($sql2);
		$res2=$db->loadObjectList();
		if (count($res2)) {
			foreach($res2 as $val2)  {
				$i++;
				$_arr[$module][$i]['link']=Router::_("index.php?module=article&view=read&psid=".$val2->a_id."&alias=".$val2->a_alias, true);
				$_arr[$module][$i]['name']=$val2->a_title;
				$_arr[$module][$i]['fullname']=$val2->a_title;
				$_arr[$module][$i]['date_change']=$val2->a_date;
			}
		}
		return true;
	}
	public function getACLTemplate($is_admin=true){		
		$acl=array();$i=0;
		if($is_admin){
			$i++;$acl[$i]['ao_name']='articleModule'; $acl[$i]['ao_description']='Module access';
			$i++;$acl[$i]['ao_name']='deleteArticleItems'; $acl[$i]['ao_description']='Finally delete articles';
			$i++;$acl[$i]['ao_name']='viewArticleItems'; $acl[$i]['ao_description']='View articles';
				 	
		} else {
			$i++;$acl[$i]['ao_name']='articleEditing';$acl[$i]['ao_description']='Article editing';
			$i++;$acl[$i]['ao_name']='articleModule'; $acl[$i]['ao_description']='Module access';
			$i++;$acl[$i]['ao_name']='articleVoting'; $acl[$i]['ao_description']='Voting';
		}
		return 	$acl;
	}
}
?>