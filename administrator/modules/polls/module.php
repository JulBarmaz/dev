<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class pollsModule extends Module {
	public function prepare() {
		/* Not need here if is set in module settings */ $this->setDefaultView('polls');
	}
	public function getACLTemplate($is_admin=true){		
		$acl=array();$i=0;
		if($is_admin){
			$i++;$acl[$i]['ao_name']='pollsModule'; $acl[$i]['ao_description']='Module access';
			$i++;$acl[$i]['ao_name']='viewPollsPolls'; $acl[$i]['ao_description']='Polls';
			$i++;$acl[$i]['ao_name']='viewPollsItems'; $acl[$i]['ao_description']='Poll items';
			$i++;$acl[$i]['ao_name']='deletePollsPolls'; $acl[$i]['ao_description']='Finally delete polls';
		} else {
			$i++;$acl[$i]['ao_name']='pollsModule'; $acl[$i]['ao_description']='Module access';
			$i++;$acl[$i]['ao_name']='pollsModuleVote'; $acl[$i]['ao_description']='Make votes';
			$i++;$acl[$i]['ao_name']='viewPollsAllResults'; $acl[$i]['ao_description']='View all results';
		}
		return 	$acl;
	}
	public function getLinksArray(&$i,&$_arr) {
		$db=Database::getInstance();
		$module=$this->getName();
		$result = array("html"=>"","links"=>array());
		$sql = "SELECT * FROM #__polls WHERE p_deleted=0 AND p_enabled=1";
		$db->setQuery($sql);
		$res=$db->loadObjectList();
		if(count($res)) {
			foreach($res as $val) {
				$i++;
				$_arr[$module][$i]['link']=Router::_("index.php?module=polls&view=poll&psid=".$val->p_id."&alias=".$val->p_alias, true);
				$_arr[$module][$i]['name']=$val->p_title;
				$_arr[$module][$i]['fullname']=$val->p_title;
				$_arr[$module][$i]['date_change']=Date::nowSQL();
			}
		}
		return true;
	}
}
?>