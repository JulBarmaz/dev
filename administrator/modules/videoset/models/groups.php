<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class videosetModelgroups extends SpravModel {

	public function checkTrashChilds(){
		// проверяем галлереи внутри удаляемых групп, в том числе и помеченные на удаление
		$sql="SELECT COUNT(vg_id) FROM #__videoset_galleries WHERE vg_group_id IN (SELECT vgr_id FROM #__videoset_groups WHERE vgr_deleted=1)";
		$this->_db->setQuery($sql);
		$galleries=$this->_db->loadResult();
		if ($galleries) return true;
		return false;
	}
	
	public function save(){
		$psid=parent::save();
		if ($psid) { 
			$ggr_thumbAutoResize = $this->getModule()->getParam("ggr_thumbAutoResize");
			$ggr_thumbWidth = $this->getModule()->getParam("ggr_thumbWidth");
			$ggr_thumbHeight = $this->getModule()->getParam("ggr_thumbHeight");
			if ($ggr_thumbAutoResize && $ggr_thumbWidth && $ggr_thumbHeight && $_FILES["vgr_thumb"]["name"]) {
				$res=$this->getElement($psid);
				if (!$res) return 0;
				$filename=Files::splitAppendix($res->vgr_thumb, true);
				if ($filename) {
					$_src=BARMAZ_UF_PATH."videoset".DS."groups".DS."thumbs".DS.$filename;
					$_dest=$_src;
					if(Files::isImage($_src)){
						$res=Files::resizeImage($_src, $_dest,galleryConfig::$$ggr_thumbWidth,galleryConfig::$$ggr_thumbHeight);
					if (!$res) return 0;
					}
				} else return 0;
			}
		} else return 0;
		$this->updateAlias($psid, Request::getSafe('vgr_alias',""), Request::getSafe('vgr_title',""));
		return $psid;
	}
}

?>