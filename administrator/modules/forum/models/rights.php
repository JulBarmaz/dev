<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined ( '_BARMAZ_VALID' ) or die ( "Access denied" );
class forumModelrights extends Model {
	public function getForumsWithAction($_arr, $action) {
		$result=array();
		$query = "SELECT * FROM #__forum_rights WHERE action='".$action."' AND f_id IN (".implode(",",array_values($_arr)).")";
		$this->_db->setQuery($query);
		$_datas = $this->_db->loadObjectList();
		if(count($_datas)) {
			foreach ($_datas as $key=>$r) {
				if ($r->r_id == User::getGuestRole() && intval($r->flag) == 1) {
					$result[$r->f_id]=true;
				}
			}
		}
		return $result;
	}
	public function getUsers() {
		$query = "SELECT DISTINCT r.u_id, u.u_login, u.u_nickname FROM #__forum_rights AS r 
					LEFT JOIN #__users AS u ON u.u_id=r.u_id
					WHERE r.u_id>0";
		$this->_db->setQuery ( $query );
		return $this->_db->loadObjectList ( "u_id" );
	}
	public function getRoles() {
		$query = "SELECT * FROM #__acl_roles WHERE (ar_deleted=0) AND (ar_active=1)";
		$this->_db->setQuery ( $query );
		return $this->_db->loadObjectList ( "ar_id" );
	}
	public function getRoleName($roleId) {
		$query = "SELECT ar_name FROM #__acl_roles WHERE ar_id=" . intval ( $roleId );
		$this->_db->setQuery ( $query );
		return strval ( $this->_db->loadResult () );
	}
	public function getRoleTitle($roleId) {
		$query = "SELECT ar_title FROM #__acl_roles WHERE ar_id=" . intval ( $roleId );
		$this->_db->setQuery ( $query );
		$res = strval ( $this->_db->loadResult () );
		if ($res) return $res;
		else return $this->getRoleName ( $roleId );
	}
	public function getAllRulesForAllRoles() {
		$query = "SELECT * FROM #__forum_rights WHERE r_id>0";
		$this->_db->setQuery ( $query );
		return $this->_db->loadObjectList ();
	}
	public function getAllRulesForAllUsers() {
		$query = "SELECT * FROM #__forum_rights WHERE u_id>0";
		$this->_db->setQuery ( $query );
		return $this->_db->loadObjectList ();
	}
	public function getRulesForRole($forum_id, $roleId) {
		$query = "SELECT * FROM #__forum_rights
					WHERE (f_id=" . intval ( $forum_id ) . ") AND (r_id=" . intval ( $roleId ) . ")";
		$this->_db->setQuery ( $query );
		return $this->_db->loadObjectList ( "action" );
	}
	public function getRulesForUser($forum_id, $userId) {
		$query = "SELECT * FROM #__forum_rights
					WHERE (f_id=" . intval ( $forum_id ) . ") AND (u_id=" . intval ( $userId ) . ")";
		$this->_db->setQuery ( $query );
		return $this->_db->loadObjectList ( "action" );
	}
	public function setRuleForRole($forum_id, $roleId, $action, $state) {
		$query = "SELECT COUNT(*) FROM #__forum_rights WHERE (f_id=" . intval ( $forum_id ) . ") AND (r_id=" . intval ( $roleId ) . ") AND (action='" . $action . "')";
		$this->_db->setQuery ( $query );
		$cnt = intval ( $this->_db->loadResult () );
		if ($cnt > 0) { // Update
			$query = "UPDATE #__forum_rights SET flag=" . intval ( $state ) . " WHERE (f_id=" . intval ( $forum_id ) . ") AND (r_id=" . intval ( $roleId ) . ") AND (action='" . $action . "')";
			$this->_db->setQuery ( $query );
			return $this->_db->query ();
		} else { // Insert
			$query = "INSERT INTO #__forum_rights(f_id,r_id,u_id,action,flag)
	    	VALUES(" . intval ( $forum_id ) . "," . intval ( $roleId ) . ",0,'" . $action . "'," . intval ( $state ) . ")";
			$this->_db->setQuery ( $query );
			return $this->_db->query ();
		}
	}
	public function setRuleForUser($forum_id, $userId, $action, $state) {
		if($state==2){
			$query = "DELETE FROM #__forum_rights WHERE (f_id=" . intval ( $forum_id ) . ") AND (u_id=" . intval ( $userId ) . ") AND (action='" . $action . "')";
			$this->_db->setQuery($query);
			return $this->_db->query();
		} else {
			$query = "SELECT COUNT(*) FROM #__forum_rights WHERE (f_id=" . intval ( $forum_id ) . ") AND (u_id=" . intval ( $userId ) . ") AND (action='" . $action . "')";
			$this->_db->setQuery ( $query );
			$cnt = intval ( $this->_db->loadResult () );
			if ($cnt > 0) {
				// Update
				$query = "UPDATE #__forum_rights SET flag=" . intval ( $state ) . " WHERE (f_id=" . intval ( $forum_id ) . ") AND (u_id=" . intval ( $userId ) . ") AND (action='" . $action . "')";
				$this->_db->setQuery ( $query );
				return $this->_db->query ();
			} else {
				// Insert
				$query = "INSERT INTO #__forum_rights(f_id,r_id,u_id,action,flag)
							VALUES(" . intval ( $forum_id ) . ",0," . $userId . ",'" . $action . "'," . intval ( $state ) . ")";
				$this->_db->setQuery ( $query );
				return $this->_db->query ();
			}
		}
	}
	public function cleanRulesForUser($forum_id, $userId) {
		$query = "DELETE FROM #__forum_rights WHERE (f_id=" . intval ( $forum_id ) . ") AND (u_id=" . intval ( $userId ) . ")";
		$this->_db->setQuery($query);
		return $this->_db->query();
	}
}

?>