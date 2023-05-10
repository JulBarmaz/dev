<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class galleryModelgroups extends Model {
	public function getGroups() {
		$sql="SELECT * FROM #__gallery_groups WHERE gr_deleted=0 AND gr_show_in_list=1 AND gr_published=1 ORDER BY gr_ordering";
		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}
}
?>