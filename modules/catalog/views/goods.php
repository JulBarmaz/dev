<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class defaultViewgoods extends SpravView {
	public function getEmptyImage() {
		$imgurl="";
		$imgpath_c=PATH_IMAGES.DS."nophoto.png";
		$imgpath_t=PATH_TEMPLATES.DS.Portal::getInstance()->getTemplate().DS.'images'.DS."nophoto.png";
		if((file_exists($imgpath_t))&&(is_file($imgpath_t))) {
			$imgurl=Portal::getURI()."templates/".Portal::getInstance()->getTemplate()."/images/nophoto.png";
		} elseif((file_exists($imgpath_c))&&(is_file($imgpath_c))) {
			$imgurl=Portal::getURI()."images/nophoto.png";
		} else $imgurl="";
		return  $imgurl;
	}
	public function getImage($img,$image_state=0) {
		$imgpath=BARMAZ_UF_PATH.'catalog'.DS.'i'.DS;
		$imgurl="";
		if($image_state==1) $imgpath.='thumbs'.DS.Files::splitAppendix($img,true);
		elseif($image_state==2) $imgpath.='medium'.DS.Files::splitAppendix($img,true);
		else $imgpath.='fullsize'.DS.Files::splitAppendix($img,true);
		if((file_exists($imgpath))&&(is_file($imgpath))) {
			$imgurl=BARMAZ_UF.'/catalog/i/';
			if($image_state==1) { $imgurl.='thumbs/'.Files::splitAppendix($img); }
			elseif($image_state==2) { $imgurl.='medium/'.Files::splitAppendix($img); }
			else { $imgurl.='fullsize/'.Files::splitAppendix($img); }
		} 
		return $imgurl;
	}
	public function renderPlayer($video, $width="100%", $height="auto"){
		if (is_file(BARMAZ_UF_PATH."catalog".DS."videothumbs".DS.Files::getAppendix($video->v_image).DS.$video->v_image))
			$video->v_image = BARMAZ_UF.'/catalog/videothumbs/'.Files::getAppendix($video->v_image)."/".$video->v_image;
		else $video->v_image="";
		return Module::getHelper("player","videoset")->render($video, $width, $height);
	}
	public function haveOptions($gids){
		return Module::getHelper("goods","catalog")->haveOptions($gids);
	}
	public function applyDiscounts($gid, $price, $discounts){
		return Module::getHelper("goods","catalog")->applyDiscounts($gid, $price, $discounts);
	}
	public function getExtendedPrices($ids){
		return Module::getHelper("goods","catalog")->getExtendedPrices($ids);
	}
	public function applyExtendedPrices($gid, $price, $quantity, $extPrices){
		return Module::getHelper("goods","catalog")->applyExtendedPrices($gid, $price, $quantity, $extPrices);
	}
}
?>