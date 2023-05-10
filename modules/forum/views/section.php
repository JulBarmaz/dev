<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class defaultViewsection extends SpravView {
	public function getImage($img,$path='sections') {
		$imgpath=BARMAZ_UF_PATH.'forum'.DS.$path.DS.Files::splitAppendix($img,true);
		$imgurl="";
		if((file_exists($imgpath))&&(is_file($imgpath))) {
			$imgurl=BARMAZ_UF.'/forum/'.$path.'/'.Files::splitAppendix($img);
		}
		return $imgurl;
	}
}

?>