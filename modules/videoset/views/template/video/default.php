<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$video=$this->video;
if ($video) {
	$title=addslashes($video->v_title);
	echo "<h1 class=\"video-title\">".$title."</h1>";
	echo "<div class=\"row\">";
	echo "<div class=\"single-video ".(count($this->videos) ? "col-md-9" : "col-md-12")."\">";
	echo $this->renderPlayer($video);
	echo "</div>";
	if(count($this->videos)) {
		echo "<div class=\"single-video-list col-md-3\">";
		echo "<h2 class=\"title\">".Text::_("Video list")." :</h2>";
		foreach($this->videos as $row) {
			if($row->v_id!=$video->v_id)  {
				if (is_file($poster = BARMAZ_UF_PATH."videoset".DS."i".DS.Files::getAppendix($row->v_image).DS.$row->v_image))
					$poster = BARMAZ_UF.'/videoset/i/'.Files::getAppendix($row->v_image)."/".$row->v_image;
				else $poster="";
				echo "<div class=\"video_elem\">";
				$href="index.php?module=videoset&view=video&psid=".$row->v_id;
				if(isset($row->v_alias)&&$row->v_alias) $href.="&alias=".$row->v_alias;
				$href=Router::_($href);
				echo "<a class=\"videos\" href=\"".$href."\"><img src=\"".$poster."\" alt=\"".$row->v_alt_img."\" tltle=\"".$row->v_title_img."\"/></a>";
				echo "<h3><a href=\"".$href."\">".$row->v_title."</a></h3>";
				echo "</div>";
			}
		}
		echo "</div>";
	}
	echo "</div>";
}
?>