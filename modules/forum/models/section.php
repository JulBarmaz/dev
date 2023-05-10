<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class forumModelsection extends SpravModel {
	public function checkTreeEnabled($psid=0){ // получаем ВСЁ дерево включенных форумов и смотрим есть ли там наш
		if(!$psid) return true; // если это самый первый уровень то значит родительские смотреть нет смысла
		$tree=new simpleTreeTable();
		$tree->table="forum_sections";
		$tree->fld_id="f_id";
		$tree->fld_parent_id="f_parent_id";
		$tree->fld_title="f_name";
		$tree->fld_deleted="f_deleted";
		$tree->fld_enabled="f_enabled";
		$tree->buildTreeArrays("", 0 , 1, 1);
		foreach ($tree->getTreeArr(0) as $obj){
			if ((int)$obj->id==$psid) return true;
		}
		return false;
	}
	public function getSection($psid, $canModerate=false){
		$section=false;
		if ($psid && $this->checkTreeEnabled($psid)){
//		if ($psid && ($canModerate || $this->checkTreeEnabled($psid))){
			$sql="SELECT * FROM #__forum_sections WHERE f_enabled=1 AND f_deleted=0 AND f_id=".$psid;
			$this->_db->setQuery($sql);
			$this->_db->loadObject($section);
		}
		return $section;
	}
	public function getSections($psid, $allowed_ids, $canModerate=false){
		if (count($allowed_ids)) $ids=implode(",",$allowed_ids); else $ids=0;
		if($this->checkTreeEnabled($psid)){
//		if($canModerate || $this->checkTreeEnabled($psid)){
			$sql="SELECT a.*, ";
			$sql.="(SELECT COUNT(f_id) FROM #__forum_sections as b WHERE b.f_enabled=1 AND b.f_deleted=0 AND b.f_show_in_list=1 AND b.f_parent_id=a.f_id) as f_forums,";
			$sql.="(SELECT COUNT(t_id) FROM #__forum_themes as c WHERE c.t_enabled=1 AND c.t_deleted=0 AND c.t_forum_id=a.f_id) as f_themes";
			$sql.=" FROM #__forum_sections AS a WHERE a.f_enabled=1 AND a.f_deleted=0 AND a.f_show_in_list=1 AND a.f_parent_id=".$psid;
			$sql.=" AND a.f_id IN(".$ids.")";
			$sql.=" ORDER BY a.f_ordering";
			$this->_db->setQuery($sql);
			return $this->_db->loadObjectList();
		} else return null;
	}
	public function getTheme($psid, $forum_id=0, $published_only=true){
		$result=false;
		$sql="SELECT t.*,u.u_nickname,p.pf_img";
		$sql.=" FROM #__forum_themes AS t";
		$sql.=" LEFT JOIN #__users AS u ON u.u_id=t.t_author_id";
		$sql.=" LEFT JOIN #__profiles AS p ON p.pf_id=t.t_author_id";
		$sql.=" WHERE t.t_id=".$psid;
		if ($forum_id) $sql.=" AND t.t_forum_id=".$forum_id;
		if ($published_only) $sql.=" AND (t.t_enabled=1 OR t.t_author_id=".User::getInstance()->getID().") AND t.t_deleted=0";
		$this->_db->setQuery($sql);
		$this->_db->loadObject($result);
		return $result;
	}
	public function getThemesCount($psid, $published_only=true){
		$sql="SELECT count(t_id) FROM #__forum_themes WHERE t_forum_id=".$psid;
		if ($published_only) $sql.=" AND t_enabled=1 AND t_deleted=0";
		Database::getInstance()->setQuery($sql);
		return intval(Database::getInstance()->loadResult());
	}
	public function getThemes($psid, $published_only=true){
		$sql="SELECT t.*,u.u_nickname as theme_author, ";
		$sql.="(SELECT COUNT(p_id) FROM #__forum_posts as b WHERE b.p_enabled=1 AND b.p_deleted=0 AND b.p_theme_id=t.t_id) as t_posts";
		$sql.=" FROM #__forum_themes AS t";
		$sql.=" LEFT JOIN #__users AS u ON u.u_id=t.t_author_id";
		$sql.=" WHERE t.t_forum_id=".$psid;
		if ($published_only) $sql.=" AND (t.t_enabled=1 OR t.t_author_id=".User::getInstance()->getID().") AND t.t_deleted=0";
		$sql.=" ORDER BY t.t_fixed DESC, t.t_touch_date DESC";
		$sql.= $this->getAppendix();
		$this->_db->setQuery($sql);
		$themes=$this->_db->loadObjectList("t_id");
		if (count($themes)) {
			$tids=implode(",",array_keys($themes));
			$sql="SELECT p.*,u.u_nickname ";
			$sql.="	FROM #__forum_posts AS p";
			$sql.=" LEFT JOIN #__users AS u ON u.u_id=p.p_author_id";
			$sql.=" WHERE p.p_id IN (SELECT MAX(p_id)_id	FROM #__forum_posts WHERE p_theme_id IN (".$tids.") GROUP BY p_theme_id)";
			if ($published_only) $sql.=" AND p.p_enabled=1 AND p.p_deleted=0";
			$this->_db->setQuery($sql);
			$posts=$this->_db->loadObjectList("p_theme_id");
			foreach($themes as $theme){
				if (isset($posts[$theme->t_id])) {
					$theme->post_author_id=$posts[$theme->t_id]->p_author_id;
					$theme->post_author=$posts[$theme->t_id]->u_nickname;
					$theme->post_date=$posts[$theme->t_id]->p_date;
				} else {
					$theme->post_author_id=$theme->t_author_id;
					$theme->post_author=$theme->theme_author;
					$theme->post_date=$theme->t_date;
				}
			}
		}
		return $themes;
	}
	public function saveTheme($theme_id, $forum_id, $tTitle, $tText, $tTags, $tClosed, $tFixed, $premoderated) {
		if ($theme_id) {
			if ($premoderated) $enabled=0; else $enabled=1;
			$sql="UPDATE #__forum_themes SET t_forum_id = ".$forum_id." , 
									t_theme = '".$tTitle."' , 
									t_text = '".$tText."' , 
									t_touch_date = NOW() , 
									t_tags = '".$tTags."' , 
									t_enabled = ".$enabled." , 
									t_fixed = ".$tFixed." , 
									t_closed = ".$tClosed."
						WHERE t_id = ".$theme_id;
		} else {
			$senderIP = User::getInstance()->getIP();
			$uid=User::getInstance()->getId();
			if(!$uid) return false;
			if ($premoderated) $enabled=0; else $enabled=1;
			$sql="INSERT INTO #__forum_themes(t_author_id, t_forum_id, t_theme, t_text, t_date, t_touch_date, t_rating, t_tags, t_enabled, t_ip, t_views, t_deleted, t_fixed, t_closed	)
						VALUES(".$uid.", ".$forum_id.", '".$tTitle."', '".$tText."', NOW(), NOW(), 0, '".$tTags."', ".$enabled.", '".$senderIP."', 0, 0, ".$tFixed.", ".$tClosed."	)";
		}
		$this->_db->setQuery($sql);
		if (!$this->_db->query()) return false;
		else {
			if (!$theme_id) $theme_id = $this->_db->insertid();
			$this->updateAlias($theme_id, Request::getSafe('t_alias',""), $tTitle);
			return $theme_id;
		}
	}
	
	public function updateAlias($psid, $alias, $name) {
		if($alias) $alias = mb_substr ( Translit::_ ( $alias, DEF_CP, false ), 0, 255 );
		if(!$alias) $alias = mb_substr ( Translit::_ ( $name ), 0, 255 );
		if($alias=="forum") $alias=mb_substr($psid."-".Translit::_($name), 0, 255);
		$sql = "SELECT COUNT(*) FROM #__forum_themes WHERE t_alias='" . $alias . "' AND t_id<>" . $psid;
		$this->_db->setQuery ( $sql );
		if ($this->_db->loadResult () > 0) {
			$alias = mb_substr ( $psid . "-" . $alias, 0, 255 );
		}
		$sql = "UPDATE #__forum_themes SET t_alias='" . $alias . "' WHERE t_id=" . $psid;
		$this->_db->setQuery ( $sql );
		if ($this->_db->query ()) return $alias;
		else return false;
	}
	
}

?>