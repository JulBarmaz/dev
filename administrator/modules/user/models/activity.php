<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class userModelactivity extends Model {
	
	public function getForumInfo($uid=0,$date_start='',$date_finish='') {
		if($date_start=='') $date_start=time()-86400;
		if($date_finish=='') $date_finish=time();
		$sql="SELECT a.* FROM (";
		$sql.="SELECT p.p_author_id,p.p_date,t.*";
		$sql.=" FROM #__forum_posts AS p";
		$sql.=" LEFT JOIN #__forum_themes AS t ON t.t_id=p.p_theme_id";
		$sql.=" WHERE p.p_author_id=".$uid." AND p.p_touch_date>'".$date_start."' 
		AND p.p_touch_date<'".$date_finish."'"; 
		$sql.=" UNION SELECT t.t_author_id as p_author_id,t.t_date as p_date,t.*";
		$sql.=" FROM #__forum_themes AS t";
		$sql.=" WHERE t.t_author_id<>".$uid." AND t.t_date>'".$date_start."' 
		AND t.t_date<'".$date_finish."'"; 		
		$sql.=") AS a";
		$sql.=" GROUP BY a.t_id";
		$sql.=" ORDER BY a.p_date ASC";
		$this->_db->setQuery($sql);
		//echo $this->_db->getQuery();
		return $this->_db->loadObjectList();				
	}
}
?>