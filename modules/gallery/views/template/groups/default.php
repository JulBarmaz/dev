<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

echo "<h1 class=\"title\">".Text::_("Gallery groups")."</h1>";
if (count($this->groups)) {
	if(galleryConfig::$ggr_thumbWidth) $autowidth=" style=\"width:".galleryConfig::$ggr_thumbWidth."px;\""; else $autowidth="";
	foreach($this->groups as $group) {
		$href="index.php?module=gallery&amp;view=items&amp;psid=".$group->gr_id;
		if ($group->gr_alias) $href.="&amp;alias=".$group->gr_alias;
		echo "<div class=\"gallerygroup row\">";
		echo "<div class=\"col-md-12\"><h3>";
		echo "<a class=\"gallery-title\" href=\"".Router::_($href)."\">";
		echo $group->gr_title."</a>";
		echo "</h3></div>";

		echo "<div class=\"thumb col-sm-3\">";
		if ($group->gr_thumb) $img=BARMAZ_UF_PATH.'gallery'.DS.'groups'.DS.'thumbs'.DS.Files::getAppendix($group->gr_thumb).DS.$group->gr_thumb;
		else $img="";
		if($img && is_file($img)) {
			$img_link=BARMAZ_UF.'/gallery/groups/thumbs/'.Files::getAppendix($group->gr_thumb)."/".$group->gr_thumb;
			echo "<a class=\"group-image\" href=\"".Router::_($href)."\">";
			echo "<img".$autowidth." title=\"".$group->gr_title_thm."\" alt=\"".$group->gr_alt_thm."\" class=\"group-image\" src=\"".$img_link."\" /></a>";
		}	else {
			echo "<a class=\"group-image\" href=\"".Router::_($href)."\">";
			echo "<img width=\"1\" height=\"1\"".$autowidth." alt=\"\" class=\"empty-img\" src=\"/images/blank.gif\" /></a>";
		}
		echo "</div><div class=\"comment col-sm-9\">".$group->gr_comment."</div>";
		echo "</div>";
	}
}
?>