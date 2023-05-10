<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class commentsModule extends Module {
	public function prepare() {
		/* Not need here if is set in module settings */ $this->setDefaultView('groups');
	}
	public function getACLTemplate($is_admin=true){		
		$acl=array();$i=0;
		if($is_admin){
			$i++;$acl[$i]['ao_name']='commentsModule'; $acl[$i]['ao_description']='Module access';
			$i++;$acl[$i]['ao_name']='viewCommentsGroups'; $acl[$i]['ao_description']='View comments groups';
			$i++;$acl[$i]['ao_name']='deleteCommentsGroups'; $acl[$i]['ao_description']='Finally delete comment groups';
			$i++;$acl[$i]['ao_name']='viewCommentsComments'; $acl[$i]['ao_description']='View comments';
			$i++;$acl[$i]['ao_name']='deleteCommentsComments'; $acl[$i]['ao_description']='Finally delete comments';
			$i++;$acl[$i]['ao_name']='viewCommentsComcat'; $acl[$i]['ao_description']='View comments categories';
			$i++;$acl[$i]['ao_name']='deleteCommentsComcat'; $acl[$i]['ao_description']='Finally delete comment categories';
			$i++;$acl[$i]['ao_name']='viewCommentsComtypes'; $acl[$i]['ao_description']='View comments types';
			$i++;$acl[$i]['ao_name']='deleteCommentsComtypes'; $acl[$i]['ao_description']='Finally delete comment types';
			$i++;$acl[$i]['ao_name']='viewCommentsRights'; $acl[$i]['ao_description']='View comments rights';
		} else {
			$i++;$acl[$i]['ao_name']='commentsDisableCaptcha'; $acl[$i]['ao_description']='Disable captcha';
		}
		return 	$acl;
	}
}
?>