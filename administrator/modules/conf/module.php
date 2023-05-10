<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class confModule extends Module {
	public function prepare() {
		/* Not need here if is set in module settings */ $this->setDefaultView('cpanel');
	}
	public function getACLTemplate($is_admin=true){		
		$acl=array();$i=0;
		if($is_admin){
			$i++;$acl[$i]['ao_name']='confModule'; $acl[$i]['ao_description']='Module access';
			$i++;$acl[$i]['ao_name']='viewConfWidgets'; $acl[$i]['ao_description']='Widgets';
			$i++;$acl[$i]['ao_name']='viewConfDopfields'; $acl[$i]['ao_description']='Additional fields';
			$i++;$acl[$i]['ao_name']='viewConfPlugins'; $acl[$i]['ao_description']='Plugins';
			$i++;$acl[$i]['ao_name']='deleteConfDopfields'; $acl[$i]['ao_description']='Finally delete additional fields';
			$i++;$acl[$i]['ao_name']='deleteConfCladr'; $acl[$i]['ao_description']='Finally delete сountries, regions, cities';
			$i++;$acl[$i]['ao_name']='viewConfModules'; $acl[$i]['ao_description']='Modules';
			$i++;$acl[$i]['ao_name']='viewConfCladr'; $acl[$i]['ao_description']='Countries, regions, cities';
			$i++;$acl[$i]['ao_name']='viewConfRedirectlinks'; $acl[$i]['ao_description']='Redirect links';
			$i++;$acl[$i]['ao_name']='deleteConfRedirectlinks'; $acl[$i]['ao_description']='Finally delete redirects';
			$i++;$acl[$i]['ao_name']='deleteConfWidgets'; $acl[$i]['ao_description']='Finally delete widgets';
			$i++;$acl[$i]['ao_name']='viewConfTmplzones'; $acl[$i]['ao_description']='Template zones';
			$i++;$acl[$i]['ao_name']='deleteConfTmplzones'; $acl[$i]['ao_description']='Finally delete template zones';
			$i++;$acl[$i]['ao_name']='viewConfMansitemap'; $acl[$i]['ao_description']='Show sitemap мanual editor';
			$i++;$acl[$i]['ao_name']='deleteConfMansitemap'; $acl[$i]['ao_description']='Finally delete element in site map manual editor';
		} else {
			//
		}
		return 	$acl;
	}
}
?>