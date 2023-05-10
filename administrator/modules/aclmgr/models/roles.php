<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class aclmgrModelroles extends SpravModel {

	public function cleanRules(){
		// подчищаем правила
		$query = "DELETE FROM #__acl_rules WHERE acl_role_id NOT IN (SELECT ar_id FROM #__acl_roles)";
		$this->_db->setQuery($query); if(!$this->_db->query()) return false; 
		
		return true;
	}
	
	public function cleanRecords(){
		if(parent::cleanRecords()) return $this->cleanRules();	else return false;
	}
	
	public function delete() {
		$mdl = Module::getInstance();
		$reestr = $mdl->get('reestr');
		$psid = $reestr->get('psid');
		$arr_psid = $reestr->get('arr_psid');
		$psids = '(';
		if($arr_psid &count($arr_psid)) {
			foreach($arr_psid as $tkey) { $psids .= "'".urldecode($tkey)."',"; }

			$psids=mb_substr($psids,0,mb_strrpos($psids,",",0,DEF_CP),DEF_CP);
			$psids.=')';

			$db = Database::getInstance();
			$query = "UPDATE #__users SET u_role=".User::getDefaultRole()." WHERE u_role in ".$psids;
			$db->setQuery($query);
			$db->query();

			return parent::delete();
		}
		return false;
	}

	public function getSystemRoles() {
		$query = "SELECT * FROM #__acl_roles WHERE ar_system=1";
		$this->_db->setQuery($query);
		$roles = $this->_db->loadObjectList("ar_id");
		return $roles;
	}
/*	
	public function getRoles(&$objects) {
		$query = "SELECT * FROM #__acl_roles WHERE (ar_active=1) AND (ar_deleted=0)";
		$this->_db->setQuery($query);
		$roles = $this->_db->loadObjectList();

		foreach ($objects as $object) {
			$object->roles = array();
			foreach ($roles as $role) {
				$object->roles[$role->ar_id]->id = $role->ar_id;
				$object->roles[$role->ar_id]->name = $role->ar_name;
				$object->roles[$role->ar_id]->access = 0; // NOT allowed by default!
			}
		}
		return $roles;
	}
*/
	public function getRoleName($roleId) {
		$db = Database::getInstance();
		$query = "SELECT ar_name FROM #__acl_roles WHERE ar_id=".intval($roleId);
		$db->setQuery($query);
		$role_name = $db->loadResult();
		return $role_name;
	}
}
?>