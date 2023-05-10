<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_WIDGET_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class galleryWidget extends Widget {
	protected $_requiredModules = array("gallery");
	
	protected function setParamsMask(){
		parent::setParamsMask();
		$this->addParam("gallery_id", "table_select", 0, false, "SELECT g_id AS fld_id, CONCAT('(',g_id,') ',g_title) AS fld_name FROM #__galleries ORDER BY fld_name");
		$this->addParam("images_count", "integer", 6, true);
		$this->addParam("gw_quadro_by_row", "select", "4", true, SpravStatic::getCKArray("bs_elements_in_row"));
	}
	public function render() {
		$psid	= $this->getParam('gallery_id');
		$quantity = $this->getParam('images_count');
		$gw_quadro_by_row = $this->getParam("gw_quadro_by_row");
		$thumb_class="col-xss-12 col-xs-".$gw_quadro_by_row;;
		$art = false;
		$widgetHTML="";
		if ($psid && $quantity) {
			$alias=$this->getGalleryAlias($psid);
			$href=Router::_("index.php?module=gallery&view=images&psid=".$psid."&alias=".$alias);
			$images = $this->getImages($psid,$quantity);
			if (count($images)) {
				$widgetHTML .= "<div class=\"wgallery row mini-gutter\">";
				foreach($images as $image){
					$img=$this->getImage($image->gi_thumb);
					$widgetHTML.= "<div class=\"w_thumb ".$thumb_class."\"><a href=\"".$href."\"><img src=\"".$img."\" alt=\"\" /></a></div>";
				}		
				$widgetHTML.="</div>";
			} else $widgetHTML.= "";
		} else $widgetHTML.= "";
		return $widgetHTML;
	}
	public function getImages($psid,$quantity) {
		$imgs=array();
		$query = "SELECT gi_id FROM #__gallery_images
				WHERE gi_published=1 AND gi_deleted=0
				AND gi_gallery_id=".intval($psid);
		Database::getInstance()->setQuery($query);
		$ids_res=Database::getInstance()->loadObjectList();
		if (count($ids_res) && count($ids_res)>$quantity){
			$found_ids=array(0);
			for($i=0; $i<$quantity; $i++) {
				$current_image=$this->getRandImage($psid,$found_ids);
				if (is_object($current_image)){
					$imgs[]=$current_image;
					$found_ids[]=$current_image->gi_id;
				}	
			}
			
		} else {
			$query = "SELECT *  FROM #__gallery_images	WHERE gi_published=1 AND gi_deleted=0 AND gi_gallery_id=".intval($psid);
			Database::getInstance()->setQuery($query);
			$imgs = Database::getInstance()->loadObjectList();
		}
		return $imgs;
	}
	public function getRandImage($psid,$found_ids) {
		$img=false;
		$query = "SELECT gi_id FROM #__gallery_images
					WHERE gi_published=1 AND gi_deleted=0
					AND gi_gallery_id=".intval($psid)
				." AND gi_id NOT IN (".implode(",", $found_ids).")";
		Database::getInstance()->setQuery($query);
		$ids_res = Database::getInstance()->loadObjectList();
		if (count($ids_res)) {
			if (count($ids_res)){
				$rand=rand(0,count($ids_res)-1);
				$gi_id=$ids_res[$rand]->gi_id;
				$query = "SELECT *  FROM #__gallery_images	WHERE gi_published=1 AND gi_deleted=0 AND gi_id=".intval($gi_id);
				Database::getInstance()->setQuery($query);
				Database::getInstance()->loadObject($img);
			}
		}
		return $img;
	}
	public function getImage($img) {
		$imgpath=BARMAZ_UF_PATH."gallery".DS."i".DS."thumbs".DS.Files::splitAppendix($img,true);
		if((file_exists($imgpath))&&(is_file($imgpath))) {
			$imgurl=BARMAZ_UF."/gallery/i/thumbs/".Files::splitAppendix($img);
		} else $imgurl="";
		return $imgurl;
	}
	public function getGalleryAlias($psid) {
		$query="SELECT g_alias FROM #__galleries WHERE g_id=".$psid;
		Database::getInstance()->setQuery($query);
		return Database::getInstance()->loadResult();
	}
	
}
?>