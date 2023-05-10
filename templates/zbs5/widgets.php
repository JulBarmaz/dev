<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_TEMPLATE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class widgetTMPL {
	public static function render_default($title, $body, &$widget){
		$widget_id = $widget->getParam('Widget_ID', false, "widget_".$widget->getParam("aw_id"));
		
		$title_tag = $widget->getParam("Title_tag");
		$title_link = $widget->getParam('title_link');
		
		$class = $widget->getParam('class');
		$class = "tmpl-default tmpl-".$widget->getParam("aw_name").($class ? " ".$class : "");
		
		$html = "<div id=\"".$widget_id."\" class=\"".$class."\"><div class=\"widget-inner-wrapper\">";
		if ($title && $title_link) $html.= "<".$title_tag." class=\"wTitle\"><a title=\"\" href=\"".Router::_($title_link)."\">".$title."</a></".$title_tag.">";
		else if ($title) $html.= "<".$title_tag." class=\"wTitle\">".$title."</".$title_tag.">";
		$html.= "<div class=\"w_content\">".$body."</div>";
		$html.= "</div></div>";
		
		return $html;
	}
	public static function render_rounded_4($title, $body, &$widget){
		$widget_id = $widget->getParam('Widget_ID', false, "widget_".$widget->getParam("aw_id"));
		
		$title_tag = $widget->getParam("Title_tag");
		$title_link = $widget->getParam('title_link');
		
		$class = $widget->getParam('class');
		$class = "tmpl-rounded_4 tmpl-".$widget->getParam("aw_name")." col-sm-6 col-md-12 ".($class ? " ".$class : "");
		
		$html = "<div id=\"".$widget_id."\" class=\"".$class."\">";
		$html.= "<div class=\"w_lt\"><div class=\"w_rt\"><div class=\"w_lb\"><div class=\"w_rb\">";
		if ($title && $title_link) $html.= "<".$title_tag." class=\"wTitle\"><a title=\"\" href=\"".Router::_($title_link)."\">".$title."</a></".$title_tag.">";
		else if ($title) $html.= "<".$title_tag." class=\"wTitle\">".$title."</".$title_tag.">";
		$html.= "<div class=\"w_content\">".$body."</div>";
		$html.= "</div></div></div></div>";
		$html.= "</div>";
		
		return $html; 
	}
	public static function render_rounded_6($title, $body, &$widget){
		$widget_id = $widget->getParam('Widget_ID', false, "widget_".$widget->getParam("aw_id"));
		
		$title_tag = $widget->getParam("Title_tag");
		$title_link = $widget->getParam('title_link');
		
		$class = $widget->getParam('class');
		$class = "tmpl-rounded_6 tmpl-".$widget->getParam("aw_name")." col-sm-6 col-md-12".($class ? " ".$class : "");
		
		$html = "<div id=\"".$widget_id."\" class=\"".$class."\">";
		$html.= "<div class=\"w_lc\"><div class=\"w_rc\"><div class=\"w_lt\"><div class=\"w_rt\"><div class=\"w_lb\"><div class=\"w_rb\">";
		if ($title && $title_link) $html.= "<".$title_tag." class=\"wTitle\"><a title=\"\" href=\"".Router::_($title_link)."\">".$title."</a></".$title_tag.">";
		else if ($title) $html.= "<".$title_tag." class=\"wTitle\">".$title."</".$title_tag.">";
		$html.= "<div class=\"w_content\">".$body."</div>";
		$html.= "</div></div></div></div></div></div>";
		$html.= "</div>";
		
		return $html; 
	}
	public static function render_col_2($title, $body, &$widget){
		$widget_id = $widget->getParam('Widget_ID', false, "widget_".$widget->getParam("aw_id"));
		
		$title_tag = $widget->getParam("Title_tag");
		$title_link = $widget->getParam('title_link');
		
		$class = $widget->getParam('class');
		$class = "tmpl-col_2 tmpl-".$widget->getParam("aw_name")." col-sm-6".($class ? " ".$class : "");
		$html = "<div id=\"".$widget_id."\" class=\"".$class."\"><div class=\"widget-inner-wrapper row-cell-wrapper\">";
		if ($title && $title_link) $html.= "<".$title_tag." class=\"wTitle\"><a title=\"\" href=\"".Router::_($title_link)."\">".$title."</a></".$title_tag.">";
		else if ($title) $html.= "<".$title_tag." class=\"wTitle\">".$title."</".$title_tag.">";
		$html.= "<div class=\"w_content\">".$body."</div>";
		$html.= "</div></div>";
		
		return $html;
	}
}
?>