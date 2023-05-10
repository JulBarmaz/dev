<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class installerModule extends Module {
	public function prepare() {
		/* Not need here if is set in module settings */ $this->setDefaultView("install");
	}
	public function getACLTemplate($is_admin=true){		
		$acl=array();$i=0;
		if($is_admin){
			$i++;$acl[$i]['ao_name']='installerModule'; $acl[$i]['ao_description']='Module access';
		} else {
			//
		}
		return 	$acl;
	}
}
?>