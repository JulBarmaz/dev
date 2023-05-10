<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class forumModelthemes extends SpravModel {
	public function cleanLinksFromPosts(){
		// удаление ссылок
		$query = "DELETE FROM #__tags WHERE t_module_name='".$this->getModule()->getName()."' AND t_object_id NOT IN (SELECT t_id FROM #__forum_themes)".$this->_db->getDelimiter();
		$this->_db->setQuery($query); if(!$this->_db->query_batch(true,true)) return false;
		return true;
	}
	public function cleanRecords()	{
		if(parent::cleanRecords()) return $this->cleanLinksFromPosts();	else return false;
	}
	public function save(){
		$tags=$this->buildTagsString(Request::getSafe("t_tags",""));
		Router::getInstance()->setVarsVal("t_tags",$tags);
		$psid=parent::save();
		if($psid) $this->updateTags($psid, $tags);
		if($psid) $this->updateAlias($psid, Request::getSafe('t_alias',""), Request::getSafe('t_theme',""));
		return $psid;
	}
}
?>