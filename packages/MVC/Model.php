<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class Model extends BaseObject {

	protected	$_db			= null;
	protected	$_module		= null;
	private		$_paginator		= null;

	public function __construct($module) {
		$this->initObj();

		$this->_module = $module;
		$this->_db = Database::getInstance();
	}

	protected function getAppendix() {
		return $this->_paginator->getAppendix();
	}

	protected function getModule() {
		return $this->_module;
	}
	public function createPaginator($view, $itemCount, $is_last_page=false) {
		$pageSize = defined("_ADMIN_MODE") ? $this->_module->getParam("Admin_page_size") : $pageSize = $this->_module->getParam("Page_size");
		$this->_paginator = new Paginator($view, $itemCount, $pageSize, $is_last_page);
		return $this->_paginator;
	}
	public function getParam($param_name, $subs_default_value=true, $custom_default_value=null){
		return $this->getModule()->getParam($param_name, $subs_default_value, $custom_default_value);
	}
	public function updateTags($object_id,$tag_string) { // повторно обрабатываются тэги
		$new_tags=array();
		$query = "DELETE FROM #__tags WHERE t_module_name='".$this->getModule()->getName()."' AND t_object_id=".(int)$object_id.$this->_db->getDelimiter();
		$tags=explode(",",$tag_string);
			
		if(is_array($tags) && count($tags)){
			foreach ($tags as $tag){
				$tag=trim($tag);
				if($tag) {
					$query.= "INSERT IGNORE INTO #__tags VALUES ('".$this->getModule()->getName()."',".intval($object_id).",'".$tag."')".$this->_db->getDelimiter();
					$new_tags[]=$tag;
				}
			}
		}
		$this->_db->setQuery($query);
		$this->_db->query_batch();
		if(count($new_tags)){
			$tag_string=",".implode(",", $new_tags).",";
			return $tag_string;
		} else return "";
	}
	public function buildTagsString($tag_string=""){
		$tags=explode(",",$tag_string);
		$new_tags=array();
		if(is_array($tags) && count($tags)){
			foreach ($tags as $tag){
				$tag=trim($tag);
				if($tag) $new_tags[]=$tag;
			}
		}
		if(count($new_tags)) $tag_string=",".implode(",", $new_tags).","; else $tag_string="";
		return $tag_string;
	}
	
}

?>