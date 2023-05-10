<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class catalogModelimages extends SpravModel {
	
	public function updateThumb($psid,$thumb){
		$sql="UPDATE #__goods_img SET i_thumb='".$thumb."' WHERE i_id='".$psid."'";
		$this->_db->setQuery($sql);
		return $this->_db->query();
	}
	
	public function save()	{
		$mdl = Module::getInstance();
		$reestr = $mdl->get('reestr');
		$psid = $reestr->get('psid');
		$new_psid=parent::save();
		if ($new_psid) { 
			if (catalogConfig::$thumbAutoCreate && catalogConfig::$thumb_width && catalogConfig::$thumb_height && $_FILES["i_image"]["name"] && !$_FILES["i_thumb"]["name"]) {
				$res=$this->getElement($new_psid);
				if (!$res) return false;
				$_dest=BARMAZ_UF_PATH."catalog".DS."i".DS."thumbs".DS.$res->i_thumb;
				if ((!$res->i_thumb)||(!file_exists($_dest))) {
					$filename=Files::splitAppendix($res->i_image, true);
					if ($filename) {
						Files::checkFolder(BARMAZ_UF_PATH."catalog".DS."i".DS."thumbs".DS.Files::getAppendix($filename), true);
						$_src=BARMAZ_UF_PATH."catalog".DS."i".DS."fullsize".DS.$filename;
						$_dest=BARMAZ_UF_PATH."catalog".DS."i".DS."thumbs".DS.$filename;
						if(Files::isImage($_src))	{
							if (!Files::resizeImage($_src, $_dest, catalogConfig::$thumb_width, catalogConfig::$thumb_height)) return false;
							if (!$this->updateThumb($new_psid,$res->i_image)) return false;
						}							
					} else return false;
				}
			} elseif (catalogConfig::$thumbAutoResize && catalogConfig::$thumb_width && catalogConfig::$thumb_height && $_FILES["i_thumb"]["name"]) {
				$res=$this->getElement($new_psid);
				if (!$res) return false;
				$_dest=BARMAZ_UF_PATH."catalog".DS."i".DS."thumbs".DS.Files::splitAppendix($res->i_thumb, true);
				if ($res->i_thumb && is_file($_dest)) {
					$_src=$_dest;
					if(Files::isImage($_src))	{
						if (!Files::resizeImage($_src, $_dest, catalogConfig::$thumb_width, catalogConfig::$thumb_height)) return false;
					}
				}
			}
		}		
		return $new_psid;
	}
}
?>