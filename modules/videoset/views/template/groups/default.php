<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

echo "<h1 class=\"title\">".Text::_("Video gallery groups")."</h1>";
if (count($this->groups)) {
	$autowidth=" style=\"width:".galleryConfig::$thumbWidth."px;\"";
	foreach($this->groups as $group) {
		$href="index.php?module=videoset&amp;view=items&amp;psid=".$group->vgr_id;
		if ($group->vgr_alias) $href.="&amp;alias=".$group->vgr_alias;
		echo "<div class=\"videogallerygroup row\">";
		echo "<div class=\"col-md-12\"><h3>";
		echo "<a class=\"gallery-title\" href=\"".Router::_($href)."\">";
		echo $group->vgr_title."</a>";
		echo "</h3></div>";

		echo "<div class=\"thumb col-sm-3\">";
		if ($group->vgr_thumb) $img=BARMAZ_UF_PATH.'videoset'.DS.'groups'.DS.'thumbs'.DS.Files::getAppendix($group->vgr_thumb).DS.$group->vgr_thumb;
		else $img="";
		if($img && is_file($img)) {
			$img_link=BARMAZ_UF.'/videoset/groups/thumbs/'.Files::getAppendix($group->vgr_thumb)."/".$group->vgr_thumb;
			echo "<a class=\"group-image\" href=\"".Router::_($href)."\">";
			echo "<img".$autowidth." alt=\"".$group->vgr_title."\" class=\"group-image\" src=\"".$img_link."\" /></a>";
		}	else {
			echo "<a class=\"group-image\" href=\"".Router::_($href)."\">";
			echo "<img width=\"1\" height=\"1\"".$autowidth." alt=\"\" class=\"empty-img\" src=\"/images/blank.gif\" /></a>";
		}
		echo "</div><div class=\"comment col-sm-9\">".$group->vgr_comment."</div>";
		echo "</div>";
	}
}
?>