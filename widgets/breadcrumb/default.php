<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_WIDGET_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class breadcrumbWidget extends Widget {

	protected function setParamsMask(){
		parent::setParamsMask();
		$this->addParam("Show_first_separator", "boolean", 0);
	}
	
	public function render() {
		if (!Module::getInstance()->showBreadcrumb()) return "";
		$_breadCrumb = Module::getInstance()->getBreadCrumbArray();
		$show_first_separator = intval($this->getParam('Show_first_separator'));
		$first_shown=false;
		$html = "";
		if(count($_breadCrumb)){
			foreach ($_breadCrumb as $bc) {
				if (!$first_shown){
					if($show_first_separator){
						$html .= "<li class=\"separator\"><img class=\"separator\" src=\"/images/blank.gif\" width=\"1\" height=\"1\" alt=\"\" /></li>";
					}
					$first_shown=true;
				} else {
					$html .= "<li class=\"separator\"><img src=\"/images/blank.gif\" width=\"1\" height=\"1\" alt=\"\" /></li>";
				}
				if ($bc->link=="#")	$html .= "<li><a class=\"breadcrumb_link\">".$bc->text."</a></li>";
				else $html .= "<li><a class=\"breadcrumb_link\" href=\"".$bc->link."\">".$bc->text."</a></li>";
			}
			$html="<ol class=\"breadcrumb\">".$html."</ol>";
		}		
		return $html;
	}
}

?>