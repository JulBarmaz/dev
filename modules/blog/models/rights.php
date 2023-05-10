<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class blogModelrights extends Model {

	private $_data = array();

	private function loadBlogRights($blogId) {
		if (!array_key_exists($blogId,$this->_data)) {
			$this->message("Loading rights for blog with ID=".$blogId, __FUNCTION__);
			$query = "SELECT * FROM #__blogs_rights WHERE b_id=".intval($blogId);
			$this->_db->setQuery($query);
			$this->_data[$blogId] = $this->_db->loadObjectList();
		}
	}
	private function loadBlogsRights($blogArr,$action) {
		$this->message("Loading rights for blog with ID=".implode(",",array_values($blogArr)), __FUNCTION__);
		$query = "SELECT * FROM #__blogs_rights WHERE action='".$action."' AND b_id IN (".implode(",",array_values($blogArr)).")";
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
	public function getBlogsForAction($actions,$cat_id=0){
		$acts=explode(",", $actions);
		$actions="'".implode("','",$acts)."'";
		$uid = User::getInstance()->getID();
		$rid = User::getInstance()->getRole();
		$query = "SELECT DISTINCT b.* FROM #__blogs AS b, #__blogs_rights AS r 
					WHERE b.b_id=r.b_id AND r.flag=1 AND r.action IN(".$actions.")";
		if($cat_id) $query.=" AND b.b_id IN (SELECT l.b_id FROM #__blogs_links AS l WHERE l.parent_id=".(int)$cat_id.") ";
		$query.= " AND (".(intval($uid) ? "u_id=".intval($uid)." OR ": "")."r.r_id=".intval($rid).")";
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
		
	public function getBlogsWithAction($blogArr,$action) {
		$result=array();
		$_datas = $this->loadBlogsRights($blogArr,$action);
		$user = User::getInstance();
		if(count($_datas)) {
			foreach ($_datas as $key=>$br) {
				if ($user->isLoggedIn()) {
					if ($br->u_id == $user->getId() && intval($br->flag) == 1) {
						$result[$br->b_id]=true;
					}
				}
				if ($br->r_id == $user->getRole() && intval($br->flag) == 1) {
					$result[$br->b_id]=true;
				}
			}
		}
		return $result;
	}
	public function checkAction($blogId,$action) {
		$this->loadBlogRights($blogId);
		$user = User::getInstance();

		$flag = 0;
		foreach ($this->_data[$blogId] as $br) {
			// Разрешительная модель.
			// Если нашли разрешение равное 1, то возвращаем true независимо от того,
			// чьё разрешение нашли раньше пользователя или роли.
			if ($user->isLoggedIn()) {
				if ($br->u_id == $user->getId() && $br->action == $action && intval($br->flag) == 1) {
					return true;
				}
			}

			if ($br->r_id == $user->getRole() && $br->action == $action && intval($br->flag) == 1) {
				return true;
			}
		}
		
		return false;
	}
/*	
	public function getBlogsForUser($uid,$role){
		$query = "SELECT DISTINCT * FROM #__blogs_rights WHERE u_id=".intval($uid)." OR r_id=".intval($role);
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
*/	
	public function getBlogIdsForUser($uid,$role){
		// @TODO Отдает только разрешенные, принудительно запрещенные пользователю не учитывает
		$query = "SELECT DISTINCT b_id FROM #__blogs_rights WHERE flag=1 AND action='read' AND (".(intval($uid) ? "u_id=".intval($uid)." OR ": "")."r_id=".intval($role).")";
		$this->_db->setQuery($query);
		return $this->_db->loadResultArray();
	}

}

?>