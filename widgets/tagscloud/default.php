<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_WIDGET_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class tagscloudWidget extends Widget {
	protected function setParamsMask(){
		parent::setParamsMask();
		$this->addParam("min_font_size", "integer", 10);
		$this->addParam("max_font_size", "integer", 18);
		$this->addParam("max_tags_show", "integer", 20);
		$this->addParam("show_quantity", "boolean", 0);
		$this->addParam("links_nofollow", "boolean", 1);
		$this->addParam("tags_type", "select", "blog", false, array("blog"=>Text::_("Blogs posts"), "forum"=>Text::_("Forum themes")));
	}
	public function render() {
		$min_count=999999999; $max_count=0;
		$min_font_size = $this->getParam('min_font_size');
		$max_font_size = $this->getParam('max_font_size');
		$max_tags_show = $this->getParam('max_tags_show');
		$links_nofollow = $this->getParam('links_nofollow');
		$tags_type = $this->getParam('tags_type');
		$show_quantity = $this->getParam('show_quantity');
		$sql = "SELECT t_tag_name, COUNT(t_tag_name) AS tags_count FROM #__tags";
		if($tags_type=="blog"){
			$sql.= " WHERE t_module_name='blog'";
		} elseif($tags_type=="forum"){
			$sql.= " WHERE t_module_name='forum'";
		}
		$sql.= " GROUP BY t_tag_name ORDER BY tags_count DESC, t_tag_name ASC";
		if($max_tags_show) $sql.=" LIMIT ".(int)$max_tags_show;
		Database::getInstance()->setQuery($sql);
		$tags=Database::getInstance()->loadObjectList();
		if(Plugin::isLoaded("search.tags")) {
			$link=""; 
			$plugin_loaded=true;
		} else $plugin_loaded=false;
		$html= "<div class=\"w_tagscloud\">";
		if(count($tags)){
			foreach($tags as $tag){
				if($tag->tags_count>$max_count) $max_count=$tag->tags_count;
				if($tag->tags_count<$min_count) $min_count=$tag->tags_count;
			}
			$spread = $max_count - $min_count;
			if ( $spread <= 0 )	$spread = 1;
			$font_spread = $max_font_size - $min_font_size;
			if ( $font_spread < 0 )	$font_spread = 1;
			$font_step = $font_spread / $spread;
			if($plugin_loaded) {
				$html.= "<ul>";
				foreach($tags as $tag){
					$link=Router::_("index.php?task=search&searchtype=any&where_search=tags&kwds=".urlencode($tag->t_tag_name));
					$current_font_size=str_replace( ',', '.', ( $min_font_size + ( ( $tag->tags_count - $min_count ) * $font_step ) ) );
					$html.= "<li><a".($links_nofollow ? " rel=\"nofollow\"" : "")." style=\"font-size:".$current_font_size."px\" href=\"".$link."\">".$tag->t_tag_name.($show_quantity ? " (".$tag->tags_count.")" : "")."</a></li>";
				}
				$html.= "</ul>";
			}
		}	
		$html.= "</div>";
		return $html;
	}
}

?>