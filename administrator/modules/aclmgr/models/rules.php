<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class aclmgrModelrules extends Model {

	public function getObjects($admin=0) {
		$query = "SELECT * FROM #__acl_objects"
						." WHERE ao_is_admin=".intval($admin)
						." AND (ao_module_name='system' OR ao_module_name='common_rules' OR ao_module_name IN ('".implode("','",Module::getInstalledModules())."'))"
						." ORDER BY ao_module_name, ao_ordering, ao_name";
		$this->_db->setQuery($query);
		$allObjects = $this->_db->loadObjectList("ao_id");
		$enObjects = array();
		foreach ($allObjects as $object) {
			if (!in_array($object->ao_module_name,Portal::getInstance()->getDisabledModules())) {
				$enObjects[$object->ao_id] = $object; 
			}
		}
		
		return $enObjects;
	}

	public function setRule($aclObjectId,$aclRoleId,$aclAccess) {
		$aclAccess = intval($aclAccess);
		$query = "INSERT INTO `#__acl_rules` VALUES(".$aclObjectId.",".$aclRoleId.",".$aclAccess.")";
		$query .= " ON DUPLICATE KEY UPDATE acl_access=".$aclAccess;
		$this->_db->setQuery($query);
		$this->_db->query();	
	}
	
	public function getRules($roleId,$objects) {
		$qin = "(";
		foreach ($objects as $objectId=>$object) {
			if ($qin != "(") $qin .= ",";
			$qin .= $objectId;
		}
		$qin .= ")";
		$query = "SELECT * FROM #__acl_rules AS ar LEFT JOIN #__acl_objects AS ao ON ar.acl_object_id=ao.ao_id"
						." WHERE (ar.acl_role_id=".intval($roleId).") AND (ar.acl_object_id IN ".$qin.")"
						." ORDER BY ao.ao_module_name";
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

}

?>