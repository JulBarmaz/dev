<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class blogModelrights extends Model {
	public function getBlogsWithAction($_arr,$action) {
		$result=array();
		$query = "SELECT * FROM #__blogs_rights WHERE action='".$action."' AND b_id IN (".implode(",",array_values($_arr)).")";
		$this->_db->setQuery($query);
		$_datas = $this->_db->loadObjectList();
		if(count($_datas)) {
			foreach ($_datas as $key=>$r) {
				if ($r->r_id == User::getGuestRole() && intval($r->flag) == 1) {
					$result[$r->b_id]=true;
				}
			}
		}
		return $result;
	}
	public function getUsers() {
		$query = "SELECT DISTINCT r.u_id, u.u_login, u.u_nickname FROM #__blogs_rights AS r 
				LEFT JOIN #__users AS u ON u.u_id=r.u_id
				WHERE r.u_id>0";
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList("u_id");
	}
	public function getRoles() {
		$query = "SELECT * FROM #__acl_roles WHERE (ar_deleted=0) AND (ar_active=1)";
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList("ar_id");
	}
	public function getRoleName($roleId) {
		$query = "SELECT ar_name FROM #__acl_roles WHERE ar_id=".intval($roleId);
		$this->_db->setQuery($query);
		return strval($this->_db->loadResult());
	}
	public function getRoleTitle($roleId) {
		$query = "SELECT ar_title FROM #__acl_roles WHERE ar_id=".intval($roleId);
		$this->_db->setQuery($query);
		$res=strval($this->_db->loadResult());
		if ($res) return $res;
		else return $this->getRoleName($roleId);
	}
	public function getAllRulesForAllRoles() {
		$query = "SELECT * FROM #__blogs_rights WHERE r_id>0";
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
	public function getAllRulesForAllUsers() {
		$query = "SELECT * FROM #__blogs_rights WHERE u_id>0";
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
	public function getRulesForRole($blogId,$roleId) {
		$query = "SELECT * FROM #__blogs_rights WHERE (b_id=".intval($blogId).") AND (r_id=".intval($roleId).")";
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList("action");
	}
	public function getRulesForUser($blogId,$userId) {
		$query = "SELECT * FROM #__blogs_rights WHERE (b_id=".intval($blogId).") AND (u_id=".intval($userId).")";
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList("action");
	}
	public function setRuleForRole($blogId,$roleId,$action,$state) {
		$query = "SELECT COUNT(*) FROM #__blogs_rights WHERE (b_id=".intval($blogId).") AND (r_id=".intval($roleId).") AND (action='".$action."')";
		$this->_db->setQuery($query);
		$cnt = intval($this->_db->loadResult());
		if ($cnt > 0) { // Update
			$query = "UPDATE #__blogs_rights SET flag=".intval($state)." WHERE (b_id=".
			intval($blogId).") AND (r_id=".intval($roleId).") AND (action='".$action."')";
			$this->_db->setQuery($query);
			return $this->_db->query();
		} else {	// Insert
			$query = "INSERT INTO #__blogs_rights(b_id,r_id,u_id,action,flag)
				VALUES(".intval($blogId).",".intval($roleId).",0,'".$action."',".intval($state).")";
			$this->_db->setQuery($query);
			return $this->_db->query();
		}
	}
	public function setRuleForUser($blogId,$userId,$action,$state) {
		if($state==2){
			$query = "DELETE FROM #__blogs_rights WHERE (b_id=".intval($blogId).") AND (u_id=".intval($userId).") AND (action='".$action."')";
			$this->_db->setQuery($query);
			return $this->_db->query();
		} else {
			$query = "SELECT COUNT(*) FROM #__blogs_rights WHERE (b_id=".intval($blogId).") AND (u_id=".intval($userId).") AND (action='".$action."')";
			$this->_db->setQuery($query);
			$cnt = intval($this->_db->loadResult()); 
			if ($cnt > 0) {
				// Update
				$query = "UPDATE #__blogs_rights SET flag=".intval($state)." WHERE (b_id=".
				intval($blogId).") AND (u_id=".intval($userId).") AND (action='".$action."')";
				$this->_db->setQuery($query);
				return $this->_db->query();
			}
			else {
				// Insert
				$query = "INSERT INTO #__blogs_rights(b_id,r_id,u_id,action,flag)
					VALUES(".intval($blogId).",0,".$userId.",'".$action."',".intval($state).")";
				$this->_db->setQuery($query);
				return $this->_db->query();
			}
		}
	}
	public function cleanRulesForUser($blogId,$userId) {
		$query = "DELETE FROM FROM #__blogs_rights WHERE (b_id=".intval($blogId).") AND (u_id=".intval($userId).")";
		$this->_db->setQuery($query);
		return $this->_db->query();
	}
}
?>