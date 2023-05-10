<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class videosetModelitems extends SpravModel {
	
	public function checkTrashChilds(){
		// картинки принадлежащие удаляемым галлереям, в том числе и помеченные на удаление
		$sql="SELECT COUNT(v_id) FROM #__videoset_videos WHERE AND v_gallery_id IN (SELECT vg_id FROM #__video_galleries WHERE vg_deleted=1))";
		$this->_db->setQuery($sql);
		$images=$this->_db->loadResult();
		if ($images) return true;
		return false;
	}
	
	public function save(){
		$psid=parent::save(); 
		if ($psid) {
			$gal_thumbAutoResize = $this->getModule()->getParam("gal_thumbAutoResize");
			$gal_thumbWidth = $this->getModule()->getParam("gal_thumbWidth");
			$gal_thumbHeight = $this->getModule()->getParam("gal_thumbHeight");
			if ($gal_thumbAutoResize && $gal_thumbWidth && $gal_thumbHeight && $_FILES["vg_thumb"]["name"]) {
				$res=$this->getElement($psid);
				if (!$res) return 0;
				$filename=Files::splitAppendix($res->vg_thumb, true);
				if ($filename) {
					$_src=BARMAZ_UF_PATH."videoset".DS."items".DS."thumbs".DS.$res->vg_thumb;
					$_dest=$_src;
					if(Files::isImage($_src)){
						$res=Files::resizeImage($_src, $_dest,galleryConfig::$gal_thumbWidth,galleryConfig::$gal_thumbHeight);
						if (!$res) return 0;
					}
				}
			}
		} else return 0;
		$this->updateAlias($psid, Request::getSafe('vg_alias',""), Request::getSafe('vg_title',""));
		return $psid;
	}
}
?>