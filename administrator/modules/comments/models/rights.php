<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class commentsModelrights extends SpravModel {

	public function getRoles() {
		$query = "SELECT * FROM #__acl_roles WHERE (ar_deleted=0) AND (ar_active=1)";
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList("ar_id");
	}

	public function getGroups() {
		$query = "SELECT * FROM #__comms_grp ORDER BY cg_module,cg_view";
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	public function getAllRulesForAllRoles() {
		$query = "SELECT * FROM #__comms_acl";
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	public function setRuleForRole($grpId,$roleId,$action,$state) {
		$query = "SELECT COUNT(*) FROM #__comms_acl WHERE (ca_grp_id=".
				intval($grpId).") AND (ca_r_id=".intval($roleId).") AND (ca_action='".$action."')";
		$this->_db->setQuery($query);
		$cnt = intval($this->_db->loadResult());
		if ($cnt > 0) {
			// Update
			$query = "UPDATE #__comms_acl SET ca_flag=".intval($state)." WHERE (ca_grp_id=".
					intval($grpId).") AND (ca_r_id=".intval($roleId).") AND (ca_action='".$action."')";
			$this->_db->setQuery($query);
			return $this->_db->query();
		} else {  // Insert
			$query = "INSERT INTO #__comms_acl(ca_grp_id,ca_r_id,ca_action,ca_flag)
					VALUES(".intval($grpId).",".intval($roleId).",'".$action."',".intval($state).")";
			$this->_db->setQuery($query);
			return $this->_db->query();
		}
	}

}
?>