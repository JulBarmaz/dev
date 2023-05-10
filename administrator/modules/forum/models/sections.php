<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class forumModelsections extends SpravModel {
	public static function compareForumsNames($a, $b){
		if ($a->f_name == $b->f_name) return 0;
		return ($a->f_name < $b->f_name) ? -1 : 1;
	}
	public function getForum($psid) {
		$forum=false;
		$query = "SELECT * FROM #__forum_sections WHERE f_id=".intval($psid);
		$this->_db->setQuery($query);
		$this->_db->loadObject($forum);
		return $forum;
	}
	public function getForums() {
		//$query = "SELECT a.*,(SELECT count(b.f_id) FROM #__forum_sections as b WHERE b.f_parent_id=a.f_id) as subforums FROM #__forum_sections as a ORDER BY a.f_name";
		$query = "SELECT a.* FROM #__forum_sections as a ORDER BY a.f_name";
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
	public function getOrderedForums() {
		$forums=$this->getForums();
		$fff=array(); 
		if(count($forums)){
			foreach($forums as $forum){
				$fff[$forum->f_id]=clone $forum;
				$fff[$forum->f_id]->f_name=$this->updateForumName($fff[$forum->f_id]->f_name,$fff[$forum->f_id]->f_parent_id,$forums);
			}
			$forums=false;
		}
		usort($fff,"forumModelsections::compareForumsNames");
		return $fff;
	}
	public function updateForumName($fname,$fparent,$list){
		if ($fparent>0){
			reset($list);
			foreach($list as $el){
				if ($el->f_id==$fparent)	{
					return $this->updateForumName($el->f_name." &rArr; ".$fname,$el->f_parent_id,$list);
				}
			}
		} else return $fname;
	}
	public function cleanRecords()	{
		if(parent::cleanRecords()) return $this->cleanForumsLinks();	else return false;
	}
	public function cleanForumsLinks(){
		// удаленные темы 
		$query = "DELETE FROM #__forum_themes WHERE t_deleted=1";
		$this->_db->setQuery($query); if(!$this->_db->query()) return false;
		// темы к удаленным форумам
		$query = "DELETE FROM #__forum_themes WHERE t_forum_id NOT IN (SELECT f_id FROM #__forum_sections)";
		$this->_db->setQuery($query); if(!$this->_db->query()) return false;
		// удаленные посты
		$query = "DELETE FROM #__forum_posts WHERE p_deleted=1";
		$this->_db->setQuery($query); if(!$this->_db->query()) return false;
		// посты к удаленным темам
		$query = "DELETE FROM #__forum_posts WHERE p_theme_id NOT IN (SELECT t_id FROM #__forum_themes)";
		$this->_db->setQuery($query); if(!$this->_db->query()) return false;
		// права на форумы
		$query = "DELETE FROM #__forum_rights WHERE f_id NOT IN (SELECT f_id FROM #__forum_sections)";
		$this->_db->setQuery($query); if(!$this->_db->query()) return false;
		// тэги
		$query = "DELETE FROM #__tags WHERE t_module_name='forum' AND t_object_id NOT IN (SELECT t_id FROM #__forum_themes)";
		$this->_db->setQuery($query); if(!$this->_db->query()) return false;
		return true;
	}
	public function save(){
		$psid=parent::save();
		if($psid) $this->updateAlias($psid, Request::getSafe('f_alias',""), Request::getSafe('f_name',""));
		return $psid;
	}
}
?>