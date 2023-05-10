<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

//BARMAZ_ELEMENT_INFO
defined('_BARMAZ_VALID') or die("Access denied");

class catalogModule extends Module {
	public function prepare() {
		if (catalogConfig::$catalogDisabled) $this->_disable(catalogConfig::$catalogDisabledMsg);
		else{
			$view = Request::getSafe("view", "");	
			$psid = Request::getInt("psid", 0);
			if(($view=="goods" && !$psid) || !$view){
				if(catalogConfig::$catalogTitle) Portal::getInstance()->setTitle(catalogConfig::$catalogTitle);
				if(catalogConfig::$catalogDescription) Portal::getInstance()->setMeta("description",catalogConfig::$catalogDescription);
				if(catalogConfig::$catalogKeywords) Portal::getInstance()->setMeta("keywords",catalogConfig::$catalogKeywords);
			}
		}
		/* Not need here if is set in module settings */ $this->setDefaultView('goods');
	}
	public function getSitemapHTML() { 
		$db=Database::getInstance();
		$module=$this->getName();
		$result = array("html"=>"","links"=>array());
		
		$tree=new simpleTreeTable();
		$tree->table="goods_group";
		$tree->fld_id="ggr_id";
		$tree->fld_parent_id="ggr_id_parent";
		$tree->fld_title="ggr_name";
		$tree->fld_deleted="ggr_deleted";
		$tree->fld_enabled="ggr_enabled";
		$tree->fld_alias="ggr_alias";
		$tree->fld_orderby="ggr_ordering";
		$tree->element_link="index.php?module=catalog&amp;view=goods&amp;psid=";
		$tree->buildTreeArrays("",0,1,1);
		$result["title_link"]=true;
		$result["html"] = $tree->getTreeHTML(0,'ul','catalog_tree');
		return $result;
	}	
}
?>