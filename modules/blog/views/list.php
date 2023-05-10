<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class defaultViewlist extends View {
	//
	
	public function getEmptyImage() {
		$imgurl="";
		$imgpath_c=PATH_IMAGES.DS."nophoto.png";
		$imgpath_t=PATH_TEMPLATES.DS.Portal::getInstance()->getTemplate().DS.'images'.DS."nophoto.png";
		if((file_exists($imgpath_t))&&(is_file($imgpath_t))) {
			$imgurl=Portal::getURI()."/templates/".Portal::getInstance()->getTemplate()."/images/nophoto.png";
		} elseif((file_exists($imgpath_c))&&(is_file($imgpath_c))) {
			$imgurl=Portal::getURI()."/images/nophoto.png";
		} else $imgurl="";
		return  $imgurl;
	}
	public function getImage($img,$image_state=0) {
		$imgpath=BARMAZ_UF_PATH.'blog/thumbs';
		$imgurl="";
		$imgpath.=DS.Files::splitAppendix($img,true);
		if((file_exists($imgpath))&&(is_file($imgpath))) {
			$imgurl=BARMAZ_UF.'/blog/thumbs/'.Files::splitAppendix($img);
		}
		return $imgurl;
	}
	
}

?>