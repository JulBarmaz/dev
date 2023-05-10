<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class userModule extends Module {
	public function prepare() {
		/* Not need here if is set in module settings */ $this->setDefaultView('users');
	}
	public function getACLTemplate($is_admin=true){		
		$acl=array();$i=0;
		if($is_admin){
			$i++;$acl[$i]['ao_name']='userModule'; $acl[$i]['ao_description']='Module access';
			$i++;$acl[$i]['ao_name']='viewUserUsers'; $acl[$i]['ao_description']='View users list';
			$i++;$acl[$i]['ao_name']='modifyUserUsers'; $acl[$i]['ao_description']='Modify user';
			$i++;$acl[$i]['ao_name']='deleteUserUsers'; $acl[$i]['ao_description']='Finally delete users';
			$i++;$acl[$i]['ao_name']='viewUserAuth_providers'; $acl[$i]['ao_description']='View auth provders list';
			$i++;$acl[$i]['ao_name']='modifyUserAuth_providers'; $acl[$i]['ao_description']='Modify auth provders';
			$i++;$acl[$i]['ao_name']='deleteUserAuth_providers'; $acl[$i]['ao_description']='Finally delete auth provders';
			$i++;$acl[$i]['ao_name']='viewUserPanel'; $acl[$i]['ao_description']='View user panel';
			$i++;$acl[$i]['ao_name']='modifyUserPanel'; $acl[$i]['ao_description']='Modify user profile';
			$i++;$acl[$i]['ao_name']='viewUserBlacklist'; $acl[$i]['ao_description']='View and modify blacklist';
		} else {
			$i++;$acl[$i]['ao_name']='Profileuser'; $acl[$i]['ao_description']='User profile';
			$i++;$acl[$i]['ao_name']='userModule'; $acl[$i]['ao_description']='Module access';
		}
		return 	$acl;
	}
}
?>