<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class articleModeltree extends simpleTreeTable {
  public function __construct() {
  	parent::__construct();
  	$this->table="articles";
    $this->fld_id="a_id";
    $this->fld_parent_id="a_parent_id";
    $this->fld_title="a_title";
    $this->fld_deleted="a_deleted";
    $this->fld_enabled="a_published";
    $this->fld_alias="a_alias";
    $this->fld_orderby="a_title";
    $this->element_link="index.php?module=article&amp;view=read&amp;psid=";
  }
}
?>