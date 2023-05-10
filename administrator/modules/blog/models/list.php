<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class blogModellist extends SpravModel {
	public function save(){
		$psid=parent::save();
		if ($psid) {
			$this->updateAlias($psid, Request::getSafe('b_alias',""), Request::getSafe('b_name',""));
		} else return 0;
		return $psid;
	}
	public function getBlog($blogId) {
		$blog=false;
		$query = "SELECT * FROM `#__blogs` WHERE `b_id`=".intval($blogId);
		$this->_db->setQuery($query);
		$this->_db->loadObject($blog);
		return $blog;
	}
	public function getBlogs() {
		$query = "SELECT * FROM #__blogs ORDER BY b_name";
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
	public function cleanBlogsLinks(){
		// удаленные посты
		$query = "DELETE FROM #__blogs_posts WHERE p_deleted=1";
		$this->_db->setQuery($query); if(!$this->_db->query()) return false; 
		// посты к удаленным блогам
		$query = "DELETE FROM #__blogs_posts WHERE p_blog_id NOT IN (SELECT b_id FROM #__blogs)";
		$this->_db->setQuery($query); if(!$this->_db->query()) return false; 
		// права на блоги
		$query = "DELETE FROM #__blogs_rights WHERE b_id NOT IN (SELECT b_id FROM #__blogs)";
		$this->_db->setQuery($query); if(!$this->_db->query()) return false; 
		// линки на блоги
		$query = "DELETE FROM #__blogs_links WHERE b_id NOT IN (SELECT b_id FROM #__blogs)";
		$this->_db->setQuery($query); if(!$this->_db->query()) return false;
		// тэги 
		$query = "DELETE FROM #__tags WHERE t_module_name='".$this->getModule()->getName()."' AND t_object_id NOT IN (SELECT p_id FROM #__blogs_posts)";
		$this->_db->setQuery($query); if(!$this->_db->query()) return false;
		// комментарии к постам удаленных блогов
		if(BaseComments::getInstance()->init("blog","post")){
			$query="SELECT p_id FROM #__blogs_posts";
			$this->_db->setQuery($query);
			$post_arr=$this->_db->loadResultArray();
			if(count($post_arr)){
				return BaseComments::getInstance()->cleanComments($post_arr);
			}
		}
		return true;
	}
	public function cleanRecords()	{
		if(parent::cleanRecords()) return $this->cleanBlogsLinks();	else return false;
	}
}