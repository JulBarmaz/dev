<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class videosetModelgroups extends Model {
	public function getGroups() {
		$sql="SELECT * FROM #__videoset_groups WHERE vgr_deleted=0 AND vgr_show_in_list=1 AND vgr_published=1 ORDER BY vgr_ordering";
		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}
}
?>