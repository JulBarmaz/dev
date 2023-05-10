<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class galleryModelitems extends Model {
	public function getGroup($psid=0) {
	 	$sql="SELECT * FROM #__gallery_groups WHERE gr_id=".(int)$psid." AND gr_deleted=0 AND gr_published=1 ORDER BY gr_ordering";
	 	$this->_db->setQuery($sql);
		$this->_db->loadObject($res);
	 	return $res;
	 }
	 public function getItems($psid=0) {
	 	$sql="SELECT * FROM #__galleries WHERE g_group_id=".(int)$psid." AND g_show_in_list=1 AND g_deleted=0 AND g_published=1 ORDER BY g_ordering";
	 	$this->_db->setQuery($sql);
	 	return $this->_db->loadObjectList();
	 }
}
?>