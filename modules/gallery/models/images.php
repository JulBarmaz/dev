<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class galleryModelimages extends SpravModel {
	public function getGroup($psid=0) {
		$sql="SELECT * FROM #__gallery_groups WHERE gr_id=".(int)$psid." AND gr_deleted=0 AND gr_published=1 ORDER BY gr_ordering";
		$this->_db->setQuery($sql);
		$this->_db->loadObject($res);
		return $res;
	}
	public function getItem($psid=0) {
		$sql="select * from #__galleries where g_id=".(int)$psid." and g_deleted=0 and g_published=1 order by g_ordering";
		$this->_db->setQuery($sql);
		$this->_db->loadObject($res);
		return $res;
	}
	public function getImages($psid=0) {
		$sql="select * from #__gallery_images where gi_gallery_id=".(int)$psid." and gi_deleted=0 and gi_published=1 order by gi_ordering";
		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}
	public function getImage($psid=0) {
		$sql="select * from #__gallery_images where gi_id=".(int)$psid;
		$this->_db->setQuery($sql);
		$res=false;
		$this->_db->loadObject($res);
		return $res;
	}
	public function getImgArray($psid=0, $published=1) {
		$sql="select gi_id from #__gallery_images where gi_gallery_id=".(int)$psid." and gi_deleted=0";
		if($published) $sql.=" and gi_published=1 ";
		$sql.=" order by gi_ordering";
		$this->_db->setQuery($sql);
		return $this->_db->loadResultArray();
	}	
}

?>