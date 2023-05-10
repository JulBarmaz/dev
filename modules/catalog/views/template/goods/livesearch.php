<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

// Layout for catlogsearch widget 
$show_goods = $this->live_search_show_goods && count($this->goods);
$show_cats = $this->live_search_show_categories && count($this->categories);
if($show_cats){
	if($show_cats && $show_goods) echo "<h3 class=\"livesearch_title\">".Text::_("Categories")."</h3>";
	foreach ($this->categories as $cat){
		echo "<div class=\"livesearch_row float-fix\">";
		echo "<div class=\"livesearch_cats_title\">";
		$i = 0;
		$bc = array();
		foreach($cat as $key=>$val) {
			$i++;
			$bc[$i] = "<a class=\"livesearch_cat_link\" href=\"".Router::_("index.php?module=catalog&amp;view=goods&amp;psid=".(int)$key."&amp;alias=".$val["alias"]."&kwds=".urlencode($this->kwds).($this->controller ? "&controller=".$this->controller : ""))."\">".$val["title"]."</a>";
		}
		$bc=array_reverse($bc);
		echo implode(" / ", $bc);
		echo "</div>";
		echo "</div>";
	}
}

if($show_goods){
	if($show_cats && $show_goods) echo "<h3 class=\"livesearch_title\">".Text::_("Goods")."</h3>";
	foreach($this->goods as $g){
		$href = Router::_("index.php?module=catalog&view=goods&layout=info&psid=".$g->g_id."&amp;alias=".$g->g_alias.($this->controller ? "&controller=".$this->controller : ""));
		echo "<div class=\"livesearch_row float-fix\" onclick=\"location.href='".$href."'\">";
		echo "<div class=\"livesearch_thumb\">";
		$thumb=$this->getImage($g->g_thumb,1);
		if (!$thumb) $thumb = $this->getEmptyImage();
		echo HTMLControls::renderImage($thumb, false, catalogConfig::$thumb_width, 0, "", $g->g_id.")".$g->g_name);
		echo "</div>";
		echo "<div class=\"livesearch_goods_title\">";
		echo $g->g_name;
		echo "</div>";
		echo "</div>";
	}
	if($this->count_goods > count($this->goods) && $this->live_search_show_more_goods){
		echo "<div class=\"livesearch_row\">";
		echo "<div class=\"livesearch_show_more\">";
		echo "<a href=\"".Router::_("index.php?module=catalog&kwds=".urlencode($this->kwds).($this->controller ? "&controller=".$this->controller : ""))."\">";
		echo Text::_("Show other variants");
		echo "</a>";
		echo "</div>";
		echo "</div>";
	}
}
?>
