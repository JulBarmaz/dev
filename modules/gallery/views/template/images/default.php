<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");
if (is_object($this->item)){
	echo "<h1 class=\"title\">".Text::_("Gallery")." - ".$this->item->g_title."</h1>";
	$href="index.php?module=gallery&amp;view=items&amp;psid=".$this->item->g_group_id;
	if (is_object($this->group)) $href.="&amp;alias=".$this->group->gr_alias;
	echo "<div class=\"gallery-toolbar row\">";
	echo "<div class=\"gallery-link col-xs-6\"><a class=\"btn btn-info\" href=\"".Router::_($href)."\">".Text::_("Back to galleries")."</a></div>";
	echo "<div class=\"toolbar-links col-xs-6\"><a class=\"btn btn-info\" onclick=\"slideshow();\">".Text::_("Start slideshow")."</a></div>";
	echo "</div>";
	if ($this->item->g_thumb) $img=BARMAZ_UF_PATH.'gallery'.DS.'items'.DS.'thumbs'.DS.Files::getAppendix($this->item->g_thumb).DS.$this->item->g_thumb;
	else $img="";
	if($img && is_file($img)) $img_link=BARMAZ_UF.'/gallery/items/thumbs/'.Files::getAppendix($this->item->g_thumb)."/".$this->item->g_thumb;
	else $img_link ="";
	if ($this->item->g_show_parent_descr  && ($this->item->g_comment || $img_link)) {
		echo "<div class=\"group-comments row\">";
		echo "<div class=\"thumb col-xss-12 col-xs-5 col-sm-3\">";
		echo "<img title=\"".$this->item->g_title_thm."\" alt=\"".$this->item->g_alt_thm."\" class=\"group-image\" src=\"".$img_link."\" />";
		echo "</div><div class=\"comment col-xss-12 col-xs-7 col-sm-9\">".$this->item->g_comment."</div>";
		echo "</div>";
	}
	echo "<div class=\"image-list row\">";
	if (count($this->images)) {
		switch($this->images_by_row){
			case "2":
				$item_class="col-xss-12 col-xs-6";
				break;
			case "3":
				$item_class="col-xss-12 col-xs-6 col-sm-4";
				break;
			case "4":
				$item_class="col-xss-12 col-xs-6 col-sm-3";
				break;
			case "6":
				$item_class="col-xss-12 col-xs-4 col-sm-3 col-md-2";
				break;
			default:
				$item_class="col-xss-12 col-xs-6 col-sm-3";
				break;
		}
		$first_image_id=" id=\"first_image\"";
		$automax=" style=\"max-width:100%;".(galleryConfig::$thumbHeight ? "max-height:".galleryConfig::$thumbHeight."px;" : "")."\"";
		$autowidth = " style=\"display:inline-block\"";
		foreach($this->images as $image) {
			$thumb_file=BARMAZ_UF_PATH.'gallery'.DS.'i'.DS.'thumbs'.DS.Files::getAppendix($image->gi_thumb).DS.$image->gi_thumb;
			$thumb=is_file($thumb_file);
			$img_file=BARMAZ_UF_PATH.'gallery'.DS.'i'.DS.Files::getAppendix($image->gi_image).DS.$image->gi_image;
			$img=is_file($img_file);
			$title=addslashes($image->gi_title);
			if (!$thumb && !$img ) {
				$thumb_link="";
				$image_link="";
			} elseif(!$thumb) { 
				$image_link=BARMAZ_UF.'/gallery/i/'.Files::getAppendix($image->gi_image)."/".$image->gi_image;
				$thumb_link=BARMAZ_UF.'/gallery/i/'.Files::getAppendix($image->gi_image)."/".$image->gi_image;
			} elseif(!$img) { 
				$image_link="";
				$thumb_link=BARMAZ_UF.'/gallery/i/thumbs/'.Files::getAppendix($image->gi_thumb)."/".$image->gi_thumb;
			} else {
				$image_link=BARMAZ_UF.'/gallery/i/'.Files::getAppendix($image->gi_image)."/".$image->gi_image;
				$thumb_link=BARMAZ_UF.'/gallery/i/thumbs/'.Files::getAppendix($image->gi_thumb)."/".$image->gi_thumb;
			}
			echo "<div class=\"".$item_class." gallery-image\">";
			echo "<div class=\"gallery-image-wrapper\">";
			if ($image_link) {
				echo "<a".$first_image_id.$autowidth." title=\"".$title."\" data-gg-attr=\"gallery\" class=\"gallery\" href=\"".$image_link."\">";
				if ($first_image_id) $first_image_id="";
			} else echo "<span".$first_image_id.$autowidth." class=\"gallery\">";
			echo "<img".$automax." title=\"".$image->gi_title_img."\" alt=\"".$image->gi_alt_img."\" class=\"single-image".($thumb_link ? "" : " empty-img")."\" src=\"".($thumb_link ? $thumb_link : "/images/blank.gif")."\" />";
			echo $image_link ? "</a>" : "</span>";
			echo "</div>";
			if((int)galleryConfig::$thumbTitleDelta && !$this->item->g_hide_image_titles) echo "<span class=\"image-title\">".$title."</span>";
			echo "</div>";
		}
	}
	echo "</div>";
	$mdl = Module::getInstance();
	$reestr = $mdl->get('reestr');
	$page = $reestr->get('page',0);
	$sort = $reestr->get('sort');
	$str_sql_wf=$reestr->get('str_sql_wf');
	$records_count_wf=$reestr->get('records_count_wf',0);
	$psid=$reestr->get('psid');
	$multy_code=$reestr->get('multy_code');

	$ref="index.php?module=gallery&view=images&multy_code=".$multy_code."&psid=".$psid;
	if (is_object($this->item)) $ref.="&alias=".$this->item->g_alias;
	echo $this->appendLimitStringWF(false, $page, $ref, $records_count_wf, $str_sql_wf, $reestr);
}
?>