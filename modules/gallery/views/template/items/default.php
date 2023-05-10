<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$href="index.php?module=gallery";
echo "<h1 class=\"title\"><a class=\"gallery-groups\" href=\"".Router::_($href)."\">".Text::_("Galleries")."</a> - ".$this->group->gr_title."</h1>";

if ($this->group->gr_thumb) $img=BARMAZ_UF_PATH.'gallery'.DS.'groups'.DS.'thumbs'.DS.Files::getAppendix($this->group->gr_thumb).DS.$this->group->gr_thumb;
else $img="";
if($img && is_file($img)) $img_link=BARMAZ_UF.'/gallery/groups/thumbs/'.Files::getAppendix($this->group->gr_thumb)."/".$this->group->gr_thumb;
else $img_link="";
if (galleryConfig::$showParentDescr && ($this->group->gr_comment || $img_link)) {
	echo "<div class=\"group-comments row\">";
	echo "<div class=\"thumb col-xs-5 col-sm-3\">";
	echo "<img title=\"".$this->group->gr_title_thm."\" alt=\"".$this->group->gr_alt_thm."\" class=\"group-image\" src=\"".$img_link."\" />";
	echo "</div><div class=\"comment col-xs-7 col-sm-9\">".$this->group->gr_comment."</div>";
	echo "</div>";
}

if (count($this->items)) {
	if(galleryConfig::$thumbWidth) $autowidth=" style=\"width:".galleryConfig::$thumbWidth."px;\""; else $autowidth="";
	foreach($this->items as $item) {
		$href="index.php?module=gallery&amp;view=images&amp;psid=".$item->g_id;
		if ($item->g_alias) $href.="&amp;alias=".$item->g_alias;
		echo "<div class=\"gallerygroup row\">";
		echo "<div class=\"col-md-12\"><h3>";
		echo "<a class=\"gallery-title\" href=\"".Router::_($href)."\">";
		echo $item->g_title."</a>";
		echo "</h3></div>";
		
		echo "<div class=\"thumb col-sm-3\">";
		if ($item->g_thumb) $img=BARMAZ_UF_PATH.'gallery'.DS.'items'.DS.'thumbs'.DS.Files::getAppendix($item->g_thumb).DS.$item->g_thumb;
		else $img="";
		if($img && is_file($img)) {
			$img_link=BARMAZ_UF.'/gallery/items/thumbs/'.Files::getAppendix($item->g_thumb)."/".$item->g_thumb;
			echo "<a class=\"group-image\" href=\"".Router::_($href)."\">";
			echo "<img".$autowidth." title=\"".$item->g_title_thm."\" alt=\"".$item->g_alt_thm."\" class=\"group-image\" src=\"".$img_link."\" /></a>";
		} else {
			echo "<a class=\"group-image\" href=\"".Router::_($href)."\">";
			echo "<img width=\"1\" height=\"1\"".$autowidth." alt=\"\" class=\"empty-img\" src=\"/images/blank.gif\" /></a>";
		}
		echo "</div><div class=\"comment col-sm-9\">".$item->g_comment."</div>";
		echo "</div>";
	}
}
?>