<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class serviceModule extends Module {
	public function prepare() {
		/* Not need here if is set in module settings */ $this->setDefaultView('updater');
	}
	public function getACLTemplate($is_admin=true){		
		$acl=array();$i=0;
		if($is_admin){
			$i++;$acl[$i]['ao_name']='serviceModule'; $acl[$i]['ao_description']='Module access';
			$i++;$acl[$i]['ao_name']='viewUpdater'; $acl[$i]['ao_description']='Site updates';
			$i++;$acl[$i]['ao_name']='viewMediamanager'; $acl[$i]['ao_description']='Media manager view';
			$i++;$acl[$i]['ao_name']='viewServiceMailerlog'; $acl[$i]['ao_description']='View mailer log';
			$i++;$acl[$i]['ao_name']='useMediamanager'; $acl[$i]['ao_description']='Media manager use';
			$i++;$acl[$i]['ao_name']='useImageProcessor'; $acl[$i]['ao_description']='Image processor use';
			$i++;$acl[$i]['ao_name']='viewInstaller'; $acl[$i]['ao_description']='Install components';
			$i++;$acl[$i]['ao_name']='viewDatabase'; $acl[$i]['ao_description']='DB service';
			$i++;$acl[$i]['ao_name']='viewCacheManager'; $acl[$i]['ao_description']='Cache manager';
		} else {
			//
		}
		return 	$acl;
	}
}
?>