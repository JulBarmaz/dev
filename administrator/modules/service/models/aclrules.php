<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class serviceModelaclrules extends Model {
	public function checkModulesAccess(&$message) {
		return Module::getHelper("acl", "aclmgr")->checkModulesAccess($message);
	}
	public function checkModulesAcl(&$message) {
		return Module::getHelper("acl", "aclmgr")->checkModulesAcl($message);
	}
}
?>