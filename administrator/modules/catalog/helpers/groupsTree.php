<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class catalogHelperGroupsTree extends simpleTreeTable{

	public function __construct() {
		parent::__construct();
		$this->table="goods_group";
		$this->fld_id="ggr_id";
		$this->fld_parent_id="ggr_id_parent";
		$this->fld_title="ggr_name";
		$this->fld_deleted="ggr_deleted";
		$this->fld_enabled="ggr_enabled";
		$this->fld_orderby="ggr_name";
		$this->element_link="";
		$this->buildTreeArrays();
	}
	public function getWholeTreeUp($links){
		$arr=array();
		if (is_array($links)&&count($links)) {
			foreach($links as $link){
				$arr[]=$link["id"];
			}
			return parent::getWholeTreeUp($arr);
		} else return $arr;
	}
}
?>