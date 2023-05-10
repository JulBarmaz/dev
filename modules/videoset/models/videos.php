<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class videosetModelvideos extends Model {
	public function getGroup($psid=0) {
	 	$sql="SELECT * FROM #__videoset_groups WHERE vgr_id=".(int)$psid." AND vgr_deleted=0 AND vgr_published=1 ORDER BY vgr_ordering";
	 	$this->_db->setQuery($sql);
		$this->_db->loadObject($res);
	 	return $res;
	 }
	public function getItem($psid=0) {
		$sql="select * from #__videoset_galleries where vg_id=".(int)$psid." and vg_deleted=0 and vg_published=1 order by vg_ordering";
		$this->_db->setQuery($sql);
		$this->_db->loadObject($res);
		return $res;
	}
	public function getVideos($psid=0) {
		$sql="select * from #__videoset_videos where v_gallery_id=".(int)$psid." and v_deleted=0 and v_published=1 order by v_ordering";
		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}
	public function getVideo($psid=0) {
		$sql="select * from #__videoset_videos where v_id=".(int)$psid;
		$this->_db->setQuery($sql);
		$res=false;
		$this->_db->loadObject($res);
		return $res;
	}
}

?>