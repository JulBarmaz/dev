<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_WIDGET_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class searchWidget extends Widget {
	protected function setParamsMask(){
		parent::setParamsMask();
		$this->addParam("search_type", "select", "blogs_posts", false, array("blogs_posts"=>Text::_("Blogs posts"), "article"=>Text::_("Articles"), "forum_posts"=>Text::_("Forum posts"), "comments"=>Text::_("Comments")));
		$this->addParam("show_label", "boolean", 0);
		$this->addParam("show_button", "boolean", 0);
		$this->addParam("text_in_field", "string", Text::_("Search entire site"));
		$this->addParam("minimum_search_length", "integer", 4);
	}
	public function prepare() {
		$minimum_search_length = $this->getParam('minimum_search_length');
		$script="
			$(document).ready(function() {
				$('#wFrmSearch').bind('submit', function() { if ($('#w_search_keywords').val().length<".intval($minimum_search_length).") return false;	});
			});";
		Portal::getInstance()->addScriptDeclaration($script);
	}
	public function render() {
		$search_type = $this->getParam('search_type');
		$show_label = $this->getParam('show_label');
		$show_button = $this->getParam('show_button');
		$text_in_field = $this->getParam('text_in_field');
		$html= "<form action=\"".Router::_("index.php")."\" method=\"post\" id=\"wFrmSearch\" name=\"wFrmSearch\">";
		$html.= "<div class=\"w_searchform".($show_button ? " search-with-button" : "")."\">";
		if ($show_label) $html.= HTMLControls::renderLabelField("keywords","Search phrase",1);
		$html.= HTMLControls::renderInputText("keywords","",30,"","w_search_keywords", "form-control", false, false, "", [], $text_in_field);
		$html.= "</div>";
		if($show_button) {
			$html.= "<div class=\"w_searchform_button\">";
			$html.= HTMLControls::renderButton("submit", "", "submit", "submit", "");
			$html.= "</div>";
		}
		$html.= HTMLControls::renderHiddenField("where_search", $search_type);
		$html.= HTMLControls::renderHiddenField("searchtype", "any");
		$html.= HTMLControls::renderHiddenField("module", "");
		$html.= HTMLControls::renderHiddenField("view", "");
		$html.= HTMLControls::renderHiddenField("task", "search");
		$html.= "</form>";
		return $html;
	}
}

?>