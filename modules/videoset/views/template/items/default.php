<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$href="index.php?module=videoset";
echo "<h1 class=\"title\"><a class=\"gallery-groups\" href=\"".Router::_($href)."\">".Text::_("Video galleries")."</a> - ".$this->group->vgr_title."</h1>";
if (galleryConfig::$showParentDescr) {
	echo "<div class=\"video-group-comments\">";
	echo $this->group->vgr_comment;
	echo "</div>";
}
if (count($this->items)) {
	$autowidth=" style=\"width:".galleryConfig::$thumbWidth."px;\"";
	foreach($this->items as $item) {
		$href="index.php?module=videoset&amp;view=videos&amp;psid=".$item->vg_id;
		if ($item->vg_alias) $href.="&amp;alias=".$item->vg_alias;
		echo "<div class=\"videogallerygroup row\">";
		echo "<div class=\"col-md-12\"><h3>";
		echo "<a class=\"gallery-title\" href=\"".Router::_($href)."\">";
		echo $item->vg_title."</a>";
		echo "</h3></div>";

		echo "<div class=\"thumb col-sm-3\">";
		if ($item->vg_thumb) $img=BARMAZ_UF_PATH.'videoset'.DS.'items'.DS.'thumbs'.DS.Files::getAppendix($item->vg_thumb).DS.$item->vg_thumb;
		else $img="";
		if($img && is_file($img)) 	{
			$img_link=BARMAZ_UF.'/videoset/items/thumbs/'.Files::getAppendix($item->vg_thumb)."/".$item->vg_thumb;
			echo "<a class=\"video-group-image\" href=\"".Router::_($href)."\">";
			echo "<img".$autowidth." alt=\"".$item->vg_title."\" class=\"video-group-image\" src=\"".$img_link."\" /></a>";
		} else {
			echo "<a class=\"video-group-image\" href=\"".Router::_($href)."\">";
			echo "<img width=\"1\" height=\"1\"".$autowidth." alt=\"\" class=\"empty-img\" src=\"/images/blank.gif\" /></a>";
		}
		echo "</div><div class=\"comment col-sm-9\">".$item->vg_comment."</div>";
		echo "</div>";
	}
}
?>