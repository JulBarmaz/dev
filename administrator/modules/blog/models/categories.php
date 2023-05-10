<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class blogModelcategories extends SpravModel {
	public function save(){
		$psid=parent::save();
		if ($psid) {
			$this->updateAlias($psid, Request::getSafe('bc_alias',""), Request::getSafe('bc_name',""));
		} else return 0;
		return $psid;
	}
	
}
?>