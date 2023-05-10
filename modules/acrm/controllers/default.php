<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class acrmControllerdefault extends SpravController {
	public function clickACRM() {
		Portal::getInstance()->disableTemplate();
		$psid=Request::getInt("psid",0);
		echo ACRM::clickACRM($psid);
	}

	public function ajaxdisplayExecuted() {
		Portal::getInstance()->disableTemplate();
		$psid_string=Request::getSafe("psid","");
		if($psid_string){
			$arr_psid=ACRM::checkShown(explode(",",$psid_string));
			echo ACRM::displayExecuted($arr_psid);
		} else echo "FALSE";
	}
}
?>