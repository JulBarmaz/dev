<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class acrmModule extends Module {
	public function prepare() {
		/* Not need here if is set in module settings */ $this->setDefaultView('items');
	}
	public function getACLTemplate($is_admin=true){		
		$acl=array();$i=0;
		if($is_admin){
			$i++;$acl[$i]['ao_name']='acrmModule'; $acl[$i]['ao_description']='Module access';
			$i++;$acl[$i]['ao_name']='viewAcrmCats'; $acl[$i]['ao_description']='Categories';
			$i++;$acl[$i]['ao_name']='viewAcrmClients'; $acl[$i]['ao_description']='Clients';
			$i++;$acl[$i]['ao_name']='viewAcrmItems'; $acl[$i]['ao_description']='Banners';
			$i++;$acl[$i]['ao_name']='deleteAcrmItems'; $acl[$i]['ao_description']='Finally delete banners';
			$i++;$acl[$i]['ao_name']='deleteAcrmCats'; $acl[$i]['ao_description']='Finally delete categories';
			$i++;$acl[$i]['ao_name']='deleteAcrmClients'; $acl[$i]['ao_description']='Finally delete clients';
				
		} else {
			$i++;$acl[$i]['ao_name']='acrmModule'; $acl[$i]['ao_description']='Module access';
		}
		return 	$acl;
	}
}
?>