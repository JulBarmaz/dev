<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class videosetModelitems extends Model {
	public function getGroup($psid=0) {
	 	$sql="SELECT * FROM #__videoset_groups WHERE vgr_id=".(int)$psid." AND vgr_deleted=0 AND vgr_published=1 ORDER BY vgr_ordering";
	 	$this->_db->setQuery($sql);
		$this->_db->loadObject($res);
	 	return $res;
	 }
	 public function getItems($psid=0) {
	 	$sql="SELECT * FROM #__videoset_galleries WHERE vg_group_id=".(int)$psid." AND vg_show_in_list=1 AND vg_deleted=0 AND vg_published=1 ORDER BY vg_ordering";
	 	$this->_db->setQuery($sql);
	 	return $this->_db->loadObjectList();
	 }
}
?>