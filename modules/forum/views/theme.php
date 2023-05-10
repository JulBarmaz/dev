<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class defaultViewtheme extends SpravView {
	public function getImage($img) {
		$imgpath=BARMAZ_UF_PATH.'forum'.DS.'themes'.DS.Files::splitAppendix($img,true);
		$imgurl="";
		if((file_exists($imgpath))&&(is_file($imgpath))) {
			$imgurl=BARMAZ_UF.'/forum/themes/'.Files::splitAppendix($img);
		}
		return $imgurl;
	}
}

?>