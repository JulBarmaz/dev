<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class aclmgrModule extends Module {
	public function prepare() {
		/* Not need here if is set in module settings */ $this->setDefaultView('roles');
	}
	public function getACLTemplate($is_admin=true){		
		$acl=array();$i=0;
		if($is_admin){
			$i++;$acl[$i]['ao_name']='aclmgrModule'; $acl[$i]['ao_description']='Module access';
			$i++;$acl[$i]['ao_name']='viewAclmgrRoles'; $acl[$i]['ao_description']='View and modify roles';
			$i++;$acl[$i]['ao_name']='deleteAclmgrRoles'; $acl[$i]['ao_description']='Finally delete roles';
		}
		return 	$acl;
	}
}
?>