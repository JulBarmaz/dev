<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class galleryModelimages extends SpravModel {

	public function save(){
		$psid=parent::save(); 
		if ($psid) { 
			if (galleryConfig::$thumbAutoCreate && galleryConfig::$thumbWidth && galleryConfig::$thumbHeight && $_FILES["gi_image"]["name"] && !$_FILES["gi_thumb"]["name"]) {
					$res=$this->getElement($psid);
					if (!$res) return 0;
					$_src_thumb=BARMAZ_UF_PATH."gallery".DS."i".DS."thumbs".DS.$res->gi_thumb;
					if (!$res->gi_thumb || !is_file($_src_thumb)) {
						$filename=Files::splitAppendix($res->gi_image,true);
						if ($filename) {
							$_src=BARMAZ_UF_PATH."gallery".DS."i".DS.$filename;
							$_dest_path=BARMAZ_UF_PATH."gallery".DS."i".DS."thumbs".DS;
							Files::checkFolder($_dest_path.Files::getAppendix($res->gi_image), true); 
							$_dest=$_dest_path.$filename; 
							if(Files::isImage($_src)){
								if (!Files::resizeImage($_src, $_dest, galleryConfig::$thumbWidth, galleryConfig::$thumbHeight)) return 0;
								if (!$this->updateThumb($psid,$res->gi_image)) return 0;
							}
						} else return 0;
					}
			} elseif (galleryConfig::$thumbAutoResize && galleryConfig::$thumbWidth && galleryConfig::$thumbHeight && $_FILES["gi_thumb"]["name"]) {
				$res=$this->getElement($psid);
				if (!$res) return 0;
				$_src_thumb=BARMAZ_UF_PATH."gallery".DS."i".DS."thumbs".DS.Files::splitAppendix($res->gi_thumb, true);
				if ($res->gi_thumb && is_file($_src_thumb)) {
					$_dest=$_src_thumb;
					$_src=$_src_thumb;
					if(Files::isImage($_src))	{
						if (!Files::resizeImage($_src, $_dest, galleryConfig::$thumbWidth, galleryConfig::$thumbHeight)) return 0;
					}
				}
			}
		} else return 0;
		return $psid;
	}
	public function updateThumb($psid,$thumb){
		$sql="UPDATE #__gallery_images SET gi_thumb='".$thumb."' WHERE gi_id=".$psid;
		$this->_db->setQuery($sql);
		return $this->_db->query();
	}
}

?>