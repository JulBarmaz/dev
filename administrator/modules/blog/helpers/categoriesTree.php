<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class blogHelperCategoriesTree extends simpleTreeTable{
	public function __construct() {
		parent::__construct();
		$this->table="blogs_cats";
		$this->fld_id="bc_id";
		$this->fld_parent_id="bc_id_parent";
		$this->fld_title="bc_name";
		$this->fld_deleted="bc_deleted";
		$this->fld_orderby="bc_name";
		$this->element_link="index.php?module=blog&amp;view=categories&amp;psid=";
		$this->buildTreeArrays();
	}
}

?>