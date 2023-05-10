<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class forumHelperPosts {

	public function countMessagesFromLastVisit($uid,$role,$published_only=true){
		if (!$uid) return false;
		$allowedIDSArr=$this->getForumIdsForUser($uid, $role);
		if (count($allowedIDSArr)){
			$allowedIDS=implode(",",$allowedIDSArr);
		} else $allowedIDS="0";
		$last_visit=User::getInstance()->getLastVisit();
		$sql="SELECT count(p.p_id)";
		$sql.=" FROM #__forum_posts AS p";
		$sql.=" LEFT JOIN #__forum_themes AS t ON t.t_id=p.p_theme_id";
//		$sql.=" LEFT JOIN #__users AS u ON u.u_id=p.p_author_id";
//		$sql.=" LEFT JOIN #__profiles AS pr ON pr.pf_id=p.p_author_id";
		$sql.=" WHERE p.p_author_id<>".$uid." AND p.p_date>'".$last_visit."' AND t.t_forum_id IN(".$allowedIDS.")";
		if ($published_only) $sql.=" AND p.p_enabled=1 AND p.p_deleted=0";
		Database::getInstance()->setQuery($sql);
//		Util::showArray(Database::getInstance()->getQuery());
		$total=Database::getInstance()->loadResult();
		$sql="SELECT count(t_id)";
		$sql.=" FROM #__forum_themes";
		$sql.=" WHERE t_author_id<>".$uid." AND t_date>'".$last_visit."' AND t_forum_id IN(".$allowedIDS.")";
		if ($published_only) $sql.=" AND t_enabled=1 AND t_deleted=0";
		Database::getInstance()->setQuery($sql);
		$total=$total+Database::getInstance()->loadResult();
		return $total;
	}
	
	public function getForumIdsForUser($uid,$role){
		// @TODO Отдает только разрешенные, принудительно запрещенные пользователю не учитывает
		$query = "SELECT DISTINCT f_id FROM #__forum_rights WHERE flag=1 AND action='read'";
		if ($uid) $query.= " AND (u_id=".intval($uid)." OR r_id=".intval($role).")";
		else $query.= " AND r_id=".intval($role);
		Database::getInstance()->setQuery($query);
		return Database::getInstance()->loadResultArray();
	}
	
	public function getIdByAlias($view,$alias){
		switch($view){
			case "section":
				$sql="SELECT f_id FROM #__forum_sections WHERE f_alias='".$alias."'";
				break;
			case "theme":
				$sql="SELECT t_id FROM #__forum_themes WHERE t_alias='".$alias."'";
				break;
			default:
				return 0;
				break;
		}
		Database::getInstance()->setQuery($sql);
		return intval(Database::getInstance()->loadResult());
	}
	
} 
?>
