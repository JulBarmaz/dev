<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class galleryModelitems extends SpravModel {
	
	public function checkTrashChilds(){
		// картинки принадлежащие удаляемым галлереям, в том числе и помеченные на удаление
		$sql="SELECT COUNT(gi_id) FROM #__gallery_images WHERE AND gi_gallery_id IN (SELECT g_id FROM #__galleries WHERE g_deleted=1))";
		$this->_db->setQuery($sql);
		$images=$this->_db->loadResult();
		if ($images) return true;
		return false;
	}
	
	public function save(){
		$psid=parent::save(); 
		if ($psid) { 
			if (galleryConfig::$gal_thumbAutoResize && galleryConfig::$gal_thumbWidth && galleryConfig::$gal_thumbHeight && $_FILES["g_thumb"]["name"]) {
				$res=$this->getElement($psid);
				if (!$res) return 0;
				$filename=Files::splitAppendix($res->g_thumb, true);
				if ($filename) {
					$_src=BARMAZ_UF_PATH."gallery".DS."items".DS."thumbs".DS.$filename;
					$_dest=$_src;
					if(Files::isImage($_src)){
						$res=Files::resizeImage($_src, $_dest, galleryConfig::$gal_thumbWidth, galleryConfig::$gal_thumbHeight);
						if (!$res) return 0;
					}
				}
			}
		} else return 0;
		$this->updateAlias($psid, Request::getSafe('g_alias',""), Request::getSafe('g_title',""));
		return $psid;
	}
	
}
?>