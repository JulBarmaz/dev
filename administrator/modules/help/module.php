<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class helpModule extends Module {
	public function getACLTemplate($is_admin=true){		
		$acl=array();$i=0;
		if($is_admin){
			$i++;$acl[$i]['ao_name']='helpModule'; $acl[$i]['ao_description']='Module access';
		} else {
			//
		}
		return 	$acl;
	}
}
?>