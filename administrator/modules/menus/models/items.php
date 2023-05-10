<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class menusModelitems extends SpravModel {
	public function save(){
		$psid=parent::save();
		if ($psid) {
			$thumbWidth=$this->getModule()->getParam("thumbWidth");
			$thumbHeight=$this->getModule()->getParam("thumbHeight");
			if (isset($_FILES["mi_image"]) && $_FILES["mi_image"]["name"] && (!isset($_FILES["mi_thumb"]) || !$_FILES["mi_thumb"]["name"])) {
				$res=$this->getElement($psid);
				if (!$res) return 0;
				$_src_thumb=BARMAZ_UF_PATH."menus".DS."thumbs".DS.$res->mi_thumb;
				if (!$res->mi_thumb || !is_file($_src_thumb)) {
					$filename=Files::splitAppendix($res->mi_image,true);
					if ($filename) {
						$_src=BARMAZ_UF_PATH."menus".DS."i".DS.$filename;
						$_dest_path=BARMAZ_UF_PATH."menus".DS."thumbs".DS;
						Files::checkFolder($_dest_path.Files::getAppendix($res->mi_image), true);
						$_dest=$_dest_path.$filename;
						if(Files::isImage($_src)){
							if (!Files::resizeImage($_src, $_dest, $thumbWidth, $thumbHeight)) return 0;
							if (!$this->updateThumb($psid,$res->mi_image)) return 0;
						}
					} else return 0;
				}
			} elseif (isset($_FILES["mi_thumb"]["name"]) && $_FILES["mi_thumb"]["name"]){
				$res=$this->getElement($psid);
				if (!$res) return 0;
				$_src_thumb=BARMAZ_UF_PATH."menus".DS."thumbs".DS.Files::splitAppendix($res->mi_thumb, true);
				if ($res->mi_thumb && is_file($_src_thumb)) {
					$_dest=$_src_thumb;
					$_src=$_src_thumb;
					if(Files::isImage($_src))	{
						if (!Files::resizeImage($_src, $_dest, $thumbWidth, $thumbHeight)) return 0;
					}
				}
			}
		} else return 0;
		return $psid;
	}
	public function updateThumb($psid,$thumb){
		$sql="UPDATE #__menus SET mi_thumb='".$thumb."' WHERE mi_id=".$psid;
		$this->_db->setQuery($sql);
		return $this->_db->query();
	}
}
?>