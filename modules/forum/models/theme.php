<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class forumModeltheme extends SpravModel {
	public function getTheme($psid, $published_only=true){
		$result=false;
		$sql="SELECT t.*,u.u_nickname,p.pf_img";
		$sql.=" FROM #__forum_themes AS t";
		$sql.=" LEFT JOIN #__users AS u ON u.u_id=t.t_author_id";
		$sql.=" LEFT JOIN #__profiles AS p ON p.pf_id=t.t_author_id";
		$sql.=" WHERE t.t_id=".$psid;
		if ($published_only) $sql.=" AND (t.t_enabled=1 OR t.t_author_id=".User::getInstance()->getID().") AND t.t_deleted=0";
		$this->_db->setQuery($sql);
		$this->_db->loadObject($result);
		return $result;
	}
	public function touchTheme($psid){
		$sql="UPDATE #__forum_themes set t_views=t_views+1 WHERE t_id=".$psid;
		$this->_db->setQuery($sql);
		return $this->_db->query();
	}
	public function getPost($psid, $theme_id=0, $published_only=true){
		$result=false;
		$sql="SELECT * FROM #__forum_posts WHERE p_id=".$psid;
		if ($theme_id) $sql.=" AND p_theme_id=".$theme_id;
		if ($published_only) $sql.=" AND p_enabled=1 AND p_deleted=0";
		$this->_db->setQuery($sql);
		$this->_db->loadObject($result);
		return $result;
	}
	public function getPostsCount($psid, $published_only=true){
		$sql="SELECT count(p_id) FROM #__forum_posts WHERE p_theme_id=".$psid;
		if ($published_only) $sql.=" AND p_enabled=1 AND p_deleted=0";
		$this->_db->setQuery($sql);
		return intval($this->_db->loadResult());
	}
	public function getPosts($psid, $published_only=true){
		$sql="SELECT p.*,u.u_nickname,pr.pf_img";
		$sql.=" FROM #__forum_posts AS p";
		$sql.=" LEFT JOIN #__users AS u ON u.u_id=p.p_author_id";
		$sql.=" LEFT JOIN #__profiles AS pr ON pr.pf_id=p.p_author_id";
		$sql.=" WHERE p.p_theme_id=".$psid;
		if ($published_only) $sql.=" AND p.p_enabled=1 AND p.p_deleted=0";
		$sql.=" ORDER BY p_date ASC";
		$sql.= $this->getAppendix();
		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}
	public function toggleThemeField($forum_id, $theme_id, $field){
		$sql="UPDATE #__forum_themes set ".$field."=ABS(".$field."-1) WHERE t_forum_id=".$forum_id." AND t_id=".$theme_id;
		$this->_db->setQuery($sql);
		return $this->_db->query();
	}
	public function togglePostField($theme_id, $post_id, $field){
		$sql="UPDATE #__forum_posts set ".$field."=ABS(".$field."-1) WHERE p_theme_id=".$theme_id." AND p_id=".$post_id;
		$this->_db->setQuery($sql);
		return $this->_db->query();
	}
	public function savePost($post_id, $theme_id, $postTitle, $postText, $premoderated=false){
		if ($post_id) {
			if ($premoderated) $enabled=0; else $enabled=1;
			$sql="UPDATE #__forum_posts SET	p_theme_id = ".$theme_id." , 
					p_theme = '".$postTitle."' , 
					p_text = '".$postText."' , 
					p_touch_date = NOW() , 
					p_enabled = ".$enabled." 
					WHERE	p_id = ".$post_id;
		} else {
			$senderIP = User::getInstance()->getIP();
			$uid=User::getInstance()->getId();
			if(!$uid) return false;
			if ($premoderated) $enabled=0; else $enabled=1;
			$sql="INSERT INTO #__forum_posts (p_author_id, p_theme_id, p_theme, p_text, p_date, p_touch_date, p_ip, p_rating, p_enabled, p_deleted)
					VALUES (".$uid.", ".$theme_id.", '".$postTitle."', '".$postText."', NOW(), NOW(), '".$senderIP."', 0, ".$enabled.", 0)";
		}
		$this->_db->setQuery($sql);
		if (!$this->_db->query()) return false;
		else {
			if ($post_id) return $post_id;
			else return $this->_db->insertid();
		}
	}
	
}
?>