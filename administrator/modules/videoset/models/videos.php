<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class videosetModelvideos extends SpravModel {
	public function save(){
		$psid=parent::save();
		if ($psid) {
			$this->updateAlias($psid, Request::getSafe('v_alias',""), Request::getSafe('v_title',""));
		} else return 0;
		return $psid;
	}
}

?>