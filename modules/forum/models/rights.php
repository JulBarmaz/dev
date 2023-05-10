<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class forumModelrights extends Model {

	private $_data = array();

	private function loadForumRights($psid) {
		if (!array_key_exists($psid,$this->_data)) {
		  $this->message("Loading rights for forum with ID=".$psid, __FUNCTION__);
			$query = "SELECT * FROM #__forum_rights WHERE f_id=".intval($psid);
			$this->_db->setQuery($query);
			$this->_data[$psid] = $this->_db->loadObjectList();
		}
	}
	private function loadForumsRights($arr,$action) {
		$this->message("Loading rights for forum with ID=".implode(",",array_values($arr)), __FUNCTION__);
		$query = "SELECT * FROM #__forum_rights WHERE action='".$action."' AND f_id IN (".implode(",",array_values($arr)).")";
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
	
	public function getForumsForAction($actions){
		$acts=explode(",", $actions);
		$actions="'".implode("','",$acts)."'";
		$uid = User::getInstance()->getID();
		$rid = User::getInstance()->getRole();
		$query = "SELECT DISTINCT f.f_id, f.f_name FROM #__forum_sections AS f, #__forum_rights AS r 
							WHERE f.f_id=r.f_id AND r.action IN(".$actions.")
							AND (r.u_id=".intval($uid)." OR r.r_id=".intval($rid).")";
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
	public function checkAction($psid,$action) {
		$this->loadForumRights($psid);
		$user = User::getInstance();

		$flag = 0;
		foreach ($this->_data[$psid] as $rrr) {
			// Разрешительная модель.
			// Если нашли разрешение равное 1, то возвращаем true независимо от того,
			// чьё разрешение нашли раньше пользователя или роли.
			if ($user->isLoggedIn()) {
				if ($rrr->u_id == $user->getId() && $rrr->action == $action && intval($rrr->flag) == 1) {
					return true;
				}
			}

			if ($rrr->r_id == $user->getRole() && $rrr->action == $action && intval($rrr->flag) == 1) {
				return true;
			}
		}
		
		return false;
	}
	
	public function getForumsForUser($uid,$role){
		$query = "SELECT DISTINCT * FROM #__forum_rights WHERE flag=1 AND action='read' AND (u_id=".intval($uid)." OR r_id=".intval($role).")";
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
	
	public function getForumIdsForUser($uid,$role){
		// @TODO Отдает только разрешенные, принудительно запрещенные пользователю не учитывает
		$query = "SELECT DISTINCT f_id FROM #__forum_rights WHERE flag=1 AND action='read'";
		if ($uid) $query.= " AND (u_id=".intval($uid)." OR r_id=".intval($role).")";
		else $query.= " AND r_id=".intval($role);
		$this->_db->setQuery($query);
		return $this->_db->loadResultArray();
	}
	public function getForumsWithAction($arr, $action) {
		$result=array();
		$_datas = $this->loadForumsRights($arr, $action);
		$user = User::getInstance();
		if(count($_datas)) {
			foreach ($_datas as $key=>$r) {
				if ($user->isLoggedIn()) {
					if ($r->u_id == $user->getId() && intval($r->flag) == 1) {
						$result[$r->f_id]=true;
					}
				}
				if ($r->r_id == $user->getRole() && intval($r->flag) == 1) {
					$result[$r->f_id]=true;
				}
			}
		}
		return $result;
	}
}

?>