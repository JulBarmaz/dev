<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class forumModelnew extends SpravModel {

	public function getForumMessages($allowed_ids,$last_visit,$published_only=true){
		if (count($allowed_ids)) $ids=implode(",",$allowed_ids); else $ids=0;
		$sql="SELECT a.* FROM (";
		$sql.="SELECT p.p_author_id,p.p_date,t.*,u.u_nickname,pr.pf_img";
		$sql.=" FROM #__forum_posts AS p";
		$sql.=" LEFT JOIN #__forum_themes AS t ON t.t_id=p.p_theme_id";
		$sql.=" LEFT JOIN #__users AS u ON u.u_id=p.p_author_id";
		$sql.=" LEFT JOIN #__profiles AS pr ON pr.pf_id=p.p_author_id";
		$sql.=" WHERE p.p_author_id<>".User::getInstance()->getId()." AND p.p_touch_date>'".$last_visit."' AND t.t_forum_id IN(".$ids.")";
//		$sql.=" WHERE t.t_forum_id IN(".$ids.")";
		if ($published_only) $sql.=" AND p.p_enabled=1 AND p.p_deleted=0";
		$sql.=" UNION SELECT t.t_author_id as p_author_id,t.t_date as p_date,t.*,u.u_nickname,pr.pf_img";
		$sql.=" FROM #__forum_themes AS t";
		$sql.=" LEFT JOIN #__users AS u ON u.u_id=t.t_author_id";
		$sql.=" LEFT JOIN #__profiles AS pr ON pr.pf_id=t.t_author_id";
		$sql.=" WHERE t.t_author_id<>".User::getInstance()->getId()." AND t.t_date>'".$last_visit."' AND t.t_forum_id IN(".$ids.")";
		$sql.=") AS a";
		$sql.=" GROUP BY a.t_id";
		$sql.=" ORDER BY a.p_date DESC";
		$this->_db->setQuery($sql);
//		echo $this->_db->getQuery();
		return $this->_db->loadObjectList();
	}
	
}

?>