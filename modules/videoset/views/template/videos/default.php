<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

if (is_object($this->item)){
	echo "<h1 class=\"title\">".Text::_("Video gallery")." - ".$this->item->vg_title."</h1>";
	$href="index.php?module=videoset&amp;view=items&amp;psid=".$this->item->vg_group_id;
	if (is_object($this->group)) $href.="&amp;alias=".$this->group->vgr_alias;
	
	echo "<div class=\"video-gallery-toolbar\">";
	echo "	<div class=\"video-gallery-link\"><a class=\"btn btn-info\" href=\"".Router::_($href)."\">".Text::_("Back to video galleries")."</a></div>";
	echo "</div>";
	if (galleryConfig::$showParentDescr) {
		echo "<div class=\"video-group-comments\">";
		echo $this->item->vg_comment;
		echo "</div>";
	}
	echo "<div class=\"row\">";
	if (count($this->videos)) {
		foreach($this->videos as $video) {
			echo "<div class=\"video-gallery-video col-sm-6\">";
			//echo $this->renderPlayer($video, $this->medium_width, $this->medium_height);
			echo $this->renderPlayer($video, "100%", "auto");
			$href="index.php?module=videoset&view=video&psid=".$video->v_id;
			if(isset($video->v_alias) && $video->v_alias) $href.="&alias=".$video->v_alias;
			echo "<p class=\"video-title\"><a href=\"".Router::_($href)."\">".addslashes($video->v_title)."</a></p>";
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
	if (is_object($this->item)) $ref.="&alias=".$this->item->vg_alias;
	echo $this->appendLimitStringWF(false, $page, $ref, $records_count_wf, $str_sql_wf, $reestr);
}
?>