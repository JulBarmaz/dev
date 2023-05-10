<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class galleryModelgroups extends SpravModel {

	public function checkTrashChilds(){
		// проверяем галлереи внутри удаляемых групп, в том числе и помеченные на удаление
		$sql="SELECT COUNT(g_id) FROM #__galleries WHERE g_group_id IN (SELECT gr_id FROM #__gallery_groups WHERE gr_deleted=1)";
		$this->_db->setQuery($sql);
		$galleries=$this->_db->loadResult();
		if ($galleries) return true;
		return false;
	}
	
	public function save(){
		$psid=parent::save(); 
		if ($psid) { 
			if (galleryConfig::$ggr_thumbAutoResize && galleryConfig::$ggr_thumbWidth && galleryConfig::$ggr_thumbHeight && $_FILES["gr_thumb"]["name"]) {
				$res=$this->getElement($psid);
				if (!$res) return 0;
				$filename=Files::splitAppendix($res->gr_thumb, true);
				if ($filename) {
					$_src=BARMAZ_UF_PATH."gallery".DS."groups".DS."thumbs".DS.$filename;
					$_dest=$_src;
					if(Files::isImage($_src)){
					$res=Files::resizeImage($_src, $_dest, galleryConfig::$ggr_thumbWidth, galleryConfig::$ggr_thumbHeight);
					if (!$res) return 0;
					}
				} else return 0;
			}
		} else return 0;
		$this->updateAlias($psid, Request::getSafe('gr_alias',""), Request::getSafe('gr_title',""));
		return $psid;
	}
}

?>