<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class feedbackModule extends Module {
	public function prepare() {
		/* Not need here if is set in module settings */ $this->setDefaultView('messages');
	}
	public function getACLTemplate($is_admin=true){
		$acl=array();$i=0;
		if($is_admin){
			$i++;$acl[$i]['ao_name']='feedbackModule'; $acl[$i]['ao_description']='Module access';
			$i++;$acl[$i]['ao_name']='viewFeedbackMessages'; $acl[$i]['ao_description']='View feedback messages';
			$i++;$acl[$i]['ao_name']='modifyFeedbackMessages'; $acl[$i]['ao_description']='Modify feeback messages';
			$i++;$acl[$i]['ao_name']='viewFeedbackCategories'; $acl[$i]['ao_description']='View categories';
			$i++;$acl[$i]['ao_name']='deleteFeedbackMessages'; $acl[$i]['ao_description']='Finally delete feedbacks';
		} else {
			$i++;$acl[$i]['ao_name']='feedbackModule'; $acl[$i]['ao_description']='Module access';
			$i++;$acl[$i]['ao_name']='feedbackDisableFloodControl'; $acl[$i]['ao_description']='Disable flood control';
			$i++;$acl[$i]['ao_name']='feedbackDisableCaptcha'; $acl[$i]['ao_description']='Disable captcha';
		}
		return 	$acl;
	}
	public function getLinksArray(&$i,&$_arr) {
		$module=$this->getName();
		$i++;
		$_arr[$module][$i]['link']=Router::_("index.php?module=feedback&view=message", true);
		$_arr[$module][$i]['name']=Text::_("You may write us from here");
		$_arr[$module][$i]['fullname']=Text::_("You may write us from here");
		$_arr[$module][$i]['date_change']=Date::nowSQL();
		$i++;
		$_arr[$module][$i]['link']=Router::_("index.php?module=feedback&view=backcall", true);
		$_arr[$module][$i]['name']=Text::_("Order backcall");
		$_arr[$module][$i]['fullname']=Text::_("Order backcall");
		$_arr[$module][$i]['date_change']=Date::nowSQL();
		return true;
	}
}
?>