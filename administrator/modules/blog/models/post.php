<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class blogModelpost extends SpravModel {
	public function cleanLinksFromBlogs(){
		// удаление ссылок
		$query = "DELETE FROM #__blogs_links WHERE parent_id NOT IN (SELECT bc_id FROM #__blogs_cats)".$this->_db->getDelimiter();
		$query.= "DELETE FROM #__tags WHERE t_module_name='".$this->getModule()->getName()."' AND t_object_id NOT IN (SELECT p_id FROM #__blogs_posts)".$this->_db->getDelimiter();
		$this->_db->setQuery($query); if(!$this->_db->query_batch(true,true)) return false;
		return true;
	}
	public function cleanRecords()	{
		if(parent::cleanRecords()) return $this->cleanLinksFromBlogs();	else return false;
	}
	public function save(){
		$tags=$this->buildTagsString(Request::getSafe("p_tags",""));
		Router::getInstance()->setVarsVal("p_tags",$tags);
		$new_psid=parent::save();
		if($new_psid) {
			$this->updateTags($new_psid, $tags);
			$this->updateAlias($new_psid, Request::getSafe('p_alias',""), Request::getSafe('p_theme',""));
		} else return 0;
		return $new_psid;		
	}	
}
?>