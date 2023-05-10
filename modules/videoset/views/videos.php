<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class defaultViewvideos extends SpravView {
	public function renderPlayer($video, $width="100%", $height="auto"){
		if ($video->v_image && is_file(BARMAZ_UF_PATH."videoset".DS."i".DS.Files::getAppendix($video->v_image).DS.$video->v_image))
			$video->v_image = BARMAZ_UF.'/videoset/i/'.Files::getAppendix($video->v_image)."/".$video->v_image;
		else $video->v_image="";
		return Module::getHelper("player")->render($video, $width, $height);
	}
}

?>