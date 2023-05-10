<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class articleModule extends Module {
	public function prepare() {
		/* Not need here if is set in module settings */ $this->setDefaultView('list');
	}
	public function getSitemapHTML() { 
		$db=Database::getInstance();
		$module=$this->getName();
		$result = array("html"=>"","links"=>array());
		
		$tree=new simpleTreeTable();
		$tree->table="articles";
		$tree->fld_id="a_id";
		$tree->fld_parent_id="a_parent_id";
		$tree->fld_title="a_title";
		$tree->fld_deleted="a_deleted";
		$tree->fld_enabled="a_published";
		$tree->fld_alias="a_alias";
		$tree->fld_orderby="a_title";
		$tree->element_link="index.php?module=article&amp;view=read&amp;psid=";
		$tree->buildTreeArrays("",0,1,1);
		$result["title_link"]=false; 
		$result["html"] = $tree->getTreeHTML(0,'ul','article_tree');
		return $result;
	}	
}
?>